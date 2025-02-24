<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connection.php'; // Include database connection
require 'vendor/autoload.php'; // Load autoload for PHPExcel

use PhpOffice\PhpSpreadsheet\IOFactory;

// Function to generate username from full name
function generateUsername($fullname, $pdo) {
    $username = strtolower(str_replace(' ', '.', $fullname));
    $check_stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
    $check_stmt->bindParam(':username', $username);
    $check_stmt->execute();
    $counter = 1;
    $original_username = $username;
    while ($check_stmt->rowCount() > 0) {
        $username = $original_username . $counter;
        $check_stmt->bindParam(':username', $username);
        $check_stmt->execute();
        $counter++;
    }
    return $username;
}

// Fetch user role from database
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check role
if ($user['role'] !== 'admin') {
    header("Location: index.php"); // Redirect to index if not admin
    exit();
}

// Function to create a new user
if (isset($_POST['create_user'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $fullname = $_POST['fullname'];
    $role = $_POST['role'];

    // Check if username already exists
    $check_stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
    $check_stmt->bindParam(':username', $username);
    $check_stmt->execute();

    if ($check_stmt->rowCount() == 0) {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, fullname, role) VALUES (:username, :password, :fullname, :role)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':role', $role);
        $stmt->execute();

        $_SESSION['alert'] = [
            'icon' => 'success',
            'title' => 'Success!',
            'text' => 'New user created successfully',
        ];
    } else {
        $_SESSION['alert'] = [
            'icon' => 'error',
            'title' => 'Error!',
            'text' => 'This username already exists',
        ];
    }

    header("Location: admin.php");
    exit();
}

// Function to handle file upload (CSV/Excel)
function handleFileUpload($file, $pdo) {
    // Check if file is uploaded and no errors
    if ($file['error'] == UPLOAD_ERR_OK) {
        $file_tmp_path = $file['tmp_name'];
        $file_name = $file['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Check file extension
        if (in_array($file_ext, ['xlsx', 'xls', 'csv'])) {
            // Read Excel or CSV file
            if ($file_ext == 'csv') {
                $data = array_map('str_getcsv', file($file_tmp_path));
            } else {
                require 'vendor/autoload.php'; // Use PhpSpreadsheet to read Excel file
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_tmp_path);
                $data = $spreadsheet->getActiveSheet()->toArray();
            }

            // Import data into database
            $success_count = 0;
            $error_count = 0;

            foreach ($data as $row) {
                $username = $row[0];
                $password = password_hash($row[1], PASSWORD_DEFAULT);
                $fullname = $row[2];
                $role = $row[3];

                // Check if username already exists
                $check_stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
                $check_stmt->bindParam(':username', $username);
                $check_stmt->execute();

                if ($check_stmt->rowCount() == 0) {
                    $stmt = $pdo->prepare("INSERT INTO users (username, password, fullname, role) VALUES (:username, :password, :fullname, :role)");
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':password', $password);
                    $stmt->bindParam(':fullname', $fullname);
                    $stmt->bindParam(':role', $role);
                    $stmt->execute();
                    $success_count++;
                } else {
                    $error_count++;
                }
            }

            $_SESSION['alert'] = [
                'icon' => 'success',
                'title' => 'Success!',
                'text' => "Data imported successfully: $success_count items, Failed: $error_count items",
            ];
        } else {
            $_SESSION['alert'] = [
                'icon' => 'error',
                'title' => 'Error!',
                'text' => 'Invalid file format (only .csv, .xlsx, .xls allowed)',
            ];
        }
    } else {
        $_SESSION['alert'] = [
            'icon' => 'error',
            'title' => 'Error!',
            'text' => 'File upload error',
        ];
    }
}

// Function to edit user data
if (isset($_POST['edit_user'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $fullname = $_POST['fullname'];
    $role = $_POST['role'];

    $stmt = $pdo->prepare("UPDATE users SET username = :username, fullname = :fullname, role = :role WHERE id = :id");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':fullname', $fullname);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    $_SESSION['alert'] = [
        'icon' => 'success',
        'title' => 'Success!',
        'text' => 'User data updated successfully',
    ];
}

// Function to change password
if (isset($_POST['change_password'])) {
    $id = $_POST['id'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
    $stmt->bindParam(':password', $new_password);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    $_SESSION['alert'] = [
        'icon' => 'success',
        'title' => 'Success!',
        'text' => 'Password changed successfully',
    ];
}

// Function to delete user
if (isset($_GET['delete_user'])) {
    $id = $_GET['delete_user'];

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    $_SESSION['alert'] = [
        'icon' => 'success',
        'title' => 'Success!',
        'text' => 'User deleted successfully',
    ];

    header("Location: admin.php");
    exit;
}

// Function to upload user data
if (isset($_POST['upload_users'])) {
    $file = $_FILES['user_file'];
    handleFileUpload($file, $pdo);
    header("Location: admin.php");
    exit;
}

// Fetch all users
$stmt = $pdo->prepare("SELECT * FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all URLs created by users
$url_stmt = $pdo->prepare("SELECT urls.*, users.fullname FROM urls JOIN users ON urls.user_id = users.id ORDER BY urls.created_at DESC");
$url_stmt->execute();
$urls = $url_stmt->fetchAll(PDO::FETCH_ASSOC);

// Set menu value
$menu = "admin";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Management</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <!-- DataTables CSS -->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
  <!-- SweetAlert2 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/admin.css">
</head>
<body>
  <?php include("includes/header.php"); ?>

  <section class="content">
    <div class="container-fluid">
      <h1 class="text-left">
        <i class="fas fa-cogs"></i> Admin Management
      </h1>

      <!-- Navigation Tabs -->
      <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="user-tab" data-bs-toggle="tab" data-bs-target="#user" type="button" role="tab" aria-controls="user" aria-selected="true">
            <i class="fas fa-users"></i> Manage Users
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload" type="button" role="tab" aria-controls="upload" aria-selected="false">
            <i class="fas fa-upload"></i> Upload Data
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="url-tab" data-bs-toggle="tab" data-bs-target="#url" type="button" role="tab" aria-controls="url" aria-selected="false">
            <i class="fas fa-link"></i> URL Management
          </button>
        </li>
      </ul>

      <!-- Tab Content -->
      <div class="tab-content" id="myTabContent">
        <!-- Tab: Manage Users -->
        <div class="tab-pane fade show active" id="user" role="tabpanel" aria-labelledby="user-tab">
          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                  <h3><i class="fas fa-users"></i> Manage Users</h3>
                  <button type="button" class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="fas fa-plus"></i> Add
                  </button>
                </div>
                <div class="card-body">
                  <!-- User Table -->
                  <table id="userTable" class="table table-bordered animate-table">
                    <thead>
                      <tr>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Role</th>
                        <th>Last Login IP</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($users as $user): ?>
                        <tr>
                          <td><?php echo $user['username']; ?></td>
                          <td><?php echo $user['fullname']; ?></td>
                          <td><?php echo $user['role']; ?></td>
                          <td><?php echo $user['last_login_ip']; ?></td>
                          <td>
                            <button type="button" class="btn btn-warning btn-action" data-bs-toggle="modal" data-bs-target="#editUserModal<?php echo $user['id']; ?>">
                              <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-action" onclick="confirmDelete(<?php echo $user['id']; ?>)">
                              <i class="fas fa-trash"></i>
                            </button>
                            <button type="button" class="btn btn-info btn-action" data-bs-toggle="modal" data-bs-target="#changePasswordModal<?php echo $user['id']; ?>">
                              <i class="fas fa-key"></i>
                            </button>
                          </td>
                        </tr>

                        <!-- Modal: Edit User -->
                        <div class="modal fade" id="editUserModal<?php echo $user['id']; ?>" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="editUserModalLabel"><i class="fas fa-user-edit"></i> Edit User</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                <form method="post" id="editUserForm<?php echo $user['id']; ?>">
                                  <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                  <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" name="username" value="<?php echo $user['username']; ?>" required>
                                  </div>
                                  <div class="mb-3">
                                    <label for="fullname" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" name="fullname" value="<?php echo $user['fullname']; ?>" required>
                                  </div>
                                  <div class="mb-3">
                                    <label for="role" class="form-label">Role</label>
                                    <select class="form-control" name="role" required>
                                      <option value="user" <?php echo ($user['role'] == 'user') ? 'selected' : ''; ?>>User</option>
                                      <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                    </select>
                                  </div>
                                  <button type="submit" name="edit_user" class="btn btn-primary w-100">
                                    <i class="fas fa-save"></i> Save
                                  </button>
                                </form>
                              </div>
                            </div>
                          </div>
                        </div>

                        <!-- Modal: Change Password -->
                        <div class="modal fade" id="changePasswordModal<?php echo $user['id']; ?>" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="changePasswordModalLabel"><i class="fas fa-key"></i> Change Password</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                <form method="post" id="changePasswordForm<?php echo $user['id']; ?>">
                                  <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                  <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" name="new_password" required>
                                  </div>
                                  <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" name="confirm_password" required>
                                  </div>
                                  <button type="submit" name="change_password" class="btn btn-primary w-100">
                                    <i class="fas fa-save"></i> Save
                                  </button>
                                </form>
                              </div>
                            </div>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tab: Upload Data -->
        <div class="tab-pane fade" id="upload" role="tabpanel" aria-labelledby="upload-tab">
          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header bg-success text-white">
                  <h3><i class="fas fa-upload"></i> Upload User Data</h3>
                </div>
                <div class="card-body">
                  <form method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                      <label for="user_file" class="form-label">Select Excel/CSV File</label>
                      <input type="file" class="form-control" id="user_file" name="user_file" accept=".xlsx, .xls, .csv" required>
                      <small class="form-text text-muted">File format: Full Name, Password, Role</small>
                    </div>
                    <button type="submit" name="upload_users" class="btn btn-primary">Upload</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tab: URL Management -->
        <div class="tab-pane fade" id="url" role="tabpanel" aria-labelledby="url-tab">
          <div class="card">
            <div class="card-header bg-info text-white">
              <h3><i class="fas fa-link"></i> URL Management</h3>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table id="urlTable" class="table table-bordered animate-table">
                  <thead>
                    <tr>
                      <th>No.</th>
                      <th>Creator</th>
                      <th>Original URL</th>
                      <th>Short URL</th>
                      <th>Created Date</th>
                      <th>Views</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    foreach ($urls as $index => $url):
                      $fullname = htmlspecialchars($url['fullname']);
                      $original_url = htmlspecialchars($url['original_url']);
                      $short_url = htmlspecialchars($url['short_url']);
                      $views = htmlspecialchars($url['views']);
                      $date = new DateTime($url['created_at']);
                      $date->modify('+543 years');
                      $formatted_date = htmlspecialchars($date->format('d/m/Y'));
                    ?>
                    <tr>
                      <td><?= $index + 1 ?></td>
                      <td><?= $fullname ?></td>
                      <td class="text-truncate" style="max-width: 125px;"><?= $original_url ?></td>
                      <td><a href="<?= $short_url ?>" target="_blank"><?= $short_url ?></a></td>
                      <td><?= $formatted_date ?></td>
                      <td><?= $views ?></td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Modal: Add New User -->
  <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addUserModalLabel"><i class="fas fa-user-plus"></i> Add New User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form method="post" id="addUserForm">
            <div class="mb-3">
              <label for="username" class="form-label">Username</label>
              <input type="text" class="form-control" name="username" required>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" name="password" required>
            </div>
            <div class="mb-3">
              <label for="confirm_password" class="form-label">Confirm Password</label>
              <input type="password" class="form-control" name="confirm_password" required>
            </div>
            <div class="mb-3">
              <label for="fullname" class="form-label">Full Name</label>
              <input type="text" class="form-control" name="fullname" required>
            </div>
            <div class="mb-3">
              <label for="role" class="form-label">Role</label>
              <select class="form-control" name="role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
              </select>
            </div>
            <button type="submit" name="create_user" class="btn btn-primary w-100">
              <i class="fas fa-save"></i> Create User
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- DataTables JS -->
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
  <!-- SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Custom JS -->
  <script>
    $(document).ready(function() {
      // Initialize DataTables for user table
      $('#userTable').DataTable({
        "pageLength": 10, // Number of rows per page
        "language": {
          "search": "Search:", // Change search text to English
          "lengthMenu": "Show _MENU_ entries per page",
          "info": "Showing _START_ to _END_ of _TOTAL_ entries",
          "paginate": {
            "first": "First",
            "last": "Last",
            "next": "Next",
            "previous": "Previous"
          }
        }
      });

      // Initialize DataTables for URL table
      $('#urlTable').DataTable({
        "pageLength": 10,
        "language": {
          "search": "Search:",
          "lengthMenu": "Show _MENU_ entries per page",
          "info": "Showing _START_ to _END_ of _TOTAL_ entries",
          "paginate": {
            "first": "First",
            "last": "Last",
            "next": "Next",
            "previous": "Previous"
          }
        }
      });
    });

    // Function to show SweetAlert2 when deleting a user
    function confirmDelete(userId) {
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = `admin.php?delete_user=${userId}`;
        }
      });
    }

    // Show SweetAlert2 when an action is successful or an error occurs
    <?php if (isset($_SESSION['alert'])): ?>
      Swal.fire({
        icon: '<?php echo $_SESSION['alert']['icon']; ?>',
        title: '<?php echo $_SESSION['alert']['title']; ?>',
        text: '<?php echo $_SESSION['alert']['text']; ?>',
      });
      <?php unset($_SESSION['alert']); ?>
    <?php endif; ?>
  </script>

  <?php include("footer.php"); ?>
</body>
</html>