<?php
$menu = "manage_urls";
include("includes/header.php");
include 'db_connection.php'; // Connect to the database from db_connection.php
require 'vendor/autoload.php'; // Assuming you are using a QR code library like Endroid QR Code

use Endroid\QrCode\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel;

// Check if the user is logged in
if (session_status() == PHP_SESSION_NONE) {
    session_start();  // Start session if it hasn't started yet
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user_id from the user's session
$user_id = $_SESSION['user_id'];

// Add a column to track views
try {
    $pdo->exec("ALTER TABLE urls ADD COLUMN views INT DEFAULT 0");
} catch (PDOException $e) {
    // Ignore if the column already exists
}

// Fetch URL data from the database for this user
try {
    $stmt = $pdo->prepare("SELECT * FROM urls WHERE user_id = :user_id ORDER BY created_at DESC");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Function to truncate URL text
function truncate_url($url, $max_length = 50) {
    if (strlen($url) > $max_length) {
        return htmlspecialchars(substr($url, 0, $max_length)) . "...";
    }
    return htmlspecialchars($url);
}

// Function to convert month to Thai
function thai_month($month) {
    $thai_months = [
        'Jan' => 'Jan',
        'Feb' => 'Feb',
        'Mar' => 'Mar',
        'Apr' => 'Apr',
        'May' => 'May',
        'Jun' => 'Jun',
        'Jul' => 'Jul',
        'Aug' => 'Aug',
        'Sep' => 'Sep',
        'Oct' => 'Oct',
        'Nov' => 'Nov',
        'Dec' => 'Dec'
    ];
    return $thai_months[$month];
}

// Function to convert date to Thai format
function thai_date($date) {
    $day = date('j', strtotime($date)); // Day (without leading zero)
    $month = thai_month(date('M', strtotime($date))); // Month (short format)
    $year = date('Y', strtotime($date)); // Year
    return "$day $month $year";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $original_url = $_POST['original_url'];
    $short_code = generateShortCode();
    $short_url = "https://short.sut.ac.th/$short_code";

    // Create QR Code
    $qrCode = new QrCode($short_url);
    $qrCode->setSize(300);
    $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH);

    // Save QR Code to the qrcodes folder
    $qrCodePath = "qrcodes/$short_code.png";
    file_put_contents($qrCodePath, $qrCode->writeString());

    // Save data to the database
    $stmt = $pdo->prepare("INSERT INTO urls (user_id, original_url, short_url, qr_code_path) VALUES (:user_id, :original_url, :short_url, :qr_code_path)");
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindParam(':original_url', $original_url, PDO::PARAM_STR);
    $stmt->bindParam(':short_url', $short_url, PDO::PARAM_STR);
    $stmt->bindParam(':qr_code_path', $qrCodePath, PDO::PARAM_STR);
    $stmt->execute();

    echo "Short URL and QR Code have been created and saved successfully!";
}

function generateShortCode() {
    return substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 6);
}

// Increment view count when a short URL is accessed
if (isset($_GET['code'])) {
    $short_code = $_GET['code'];
    $stmt = $pdo->prepare("UPDATE urls SET views = views + 1 WHERE short_url = :short_url");
    $stmt->bindParam(':short_url', $short_code);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Your URLs</title>
    <!-- Connect to Noto Sans Thai font from Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- QR Code Library -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
    <style>
        /* Use Noto Sans Thai font */
        body {
            font-family: 'Noto Sans Thai', sans-serif;
        }

        /* Remove underline from links */
        a {
            text-decoration: none;
        }

        /* Style the table */
        .table-responsive {
            overflow-x: auto;
        }

        table td span {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: inline-block;
            max-width: 250px;
        }

        @media (max-width: 768px) {
            .card-title {
                font-size: 1rem;
            }

            table td {
                font-size: 0.875rem;
            }

            button {
                font-size: 0.875rem;
            }
        }

        /* Style the download button */
        .btn-download {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        /* Style the card */
        .card {
            border-radius: 10px;
            overflow: hidden;
        }

        /* Style the button */
        .btn {
            border-radius: 5px;
            text-decoration: none; /* Remove underline */
        }

        /* Style the muted text */
        .text-muted {
            font-size: 0.9rem;
        }

        /* Center */
        #qrcodeContainer {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Add animation to the button */
        .btn:hover {
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
    <section class="content-header">
        <div class="container-fluid">
            <h1><i class="nav-icon fas fa-cogs"></i> Manage Your URLs</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Created URLs</h3>
                    <button class="btn btn-danger btn-sm ml-auto" onclick="deleteAllEntries()">Delete All</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="urlTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Date</th>
                                    <th>Original URL</th>
                                    <th>Short-URL</th>
                                    <th>QR Code</th>
                                    <th>Views</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Display data from the database for the specific user
                                foreach ($data as $index => $entry) {
                                    // Convert date to Thai format
                                    $formatted_date = thai_date($entry['created_at']);
                                    $qr_code_path = $entry['qr_code_path'];
                                    $qr_code_exists = file_exists($qr_code_path); // Check if the QR Code file exists

                                    echo "<tr data-id='" . $entry['id'] . "'>
                                            <td>" . ($index + 1) . "</td>
                                            <td>" . htmlspecialchars($formatted_date) . "</td>
                                            <td><span title='" . htmlspecialchars($entry['original_url']) . "'>" . truncate_url($entry['original_url'], 50) . "</span></td>
                                            <td>" . htmlspecialchars($entry['short_url']) . "</td>
                                            <td>";
                                    
                                    if ($qr_code_exists) {
                                        echo "<a href='" . htmlspecialchars($qr_code_path) . "' download class='btn btn-primary btn-sm btn-download'><i class='fas fa-download'></i> Download QR</a>";
                                    } else {
                                        echo "<span class='text-muted'>No QR Code file</span>";
                                    }

                                    echo "</td>
                                            <td>" . htmlspecialchars($entry['views']) . "</td>
                                            <td>
                                                <a href='" . htmlspecialchars($entry['short_url']) . "' target='_blank' class='btn btn-info btn-sm'>View</a>
                                                <button class='btn btn-secondary btn-sm' onclick='copyToClipboard(\"" . htmlspecialchars($entry['short_url']) . "\")'>
                                                    <i class='fas fa-copy'></i> Copy
                                                </button>
                                                <button class='btn btn-danger btn-sm' onclick='deleteEntry({$entry['id']})'>Delete</button>
                                            </td>
                                          </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Page: Manage URLs -->
    <div class="tab-pane fade" id="manage-urls" role="tabpanel" aria-labelledby="manage-urls-tab">
      <div class="card">
        <div class="card-header bg-info text-white">
          <h3><i class="fas fa-link"></i> Manage URLs</h3>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table id="manageUrlsTable" class="table table-bordered animate-table">
              <thead>
                <tr>
                  <th>No.</th>
                  <th>Creator</th>
                  <th>Original URL</th>
                  <th>Short URL</th>
                  <th>Created Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php
                foreach ($urls as $index => $url):
                  $fullname = htmlspecialchars($url['fullname']);
                  $original_url = htmlspecialchars($url['original_url']);
                  $short_url = htmlspecialchars($url['short_url']);
                  $qr_code_path = htmlspecialchars($url['qr_code_path']);
                  $date = new DateTime($url['created_at']);
                  $formatted_date = htmlspecialchars($date->format('d/m/Y'));
                ?>
                <tr>
                  <td><?= $index + 1 ?></td>
                  <td><?= $fullname ?></td>
                  <td class="text-truncate" style="max-width: 125px;"><?= $original_url ?></td>
                  <td><a href="redirect.php?code=<?= basename($short_url) ?>" target="_blank"><?= $short_url ?></a></td>
                  <td><?= $formatted_date ?></td>
                  <td>
                    <form method="POST" action="manage_urls.php" onsubmit="return confirm('Are you sure you want to delete this URL?');">
                      <input type="hidden" name="delete_url" value="<?= $short_url ?>">
                      <input type="hidden" name="qr_code_path" value="<?= $qr_code_path ?>">
                      <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <script>
    // Function to delete a URL entry from the database
    function deleteEntry(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to delete this entry?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('delete_url.php', { id: id }, function(response) {
                    const res = JSON.parse(response);
                    if (res.status === 'success') {
                        // Remove the row from the HTML table using data-id
                        $(`tr[data-id="${id}"]`).remove();
                        
                        Swal.fire('Success', res.message, 'success');
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                });
            }
        });
    }

    // Function to delete all URL entries from the database
    function deleteAllEntries() {
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to delete all entries?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Delete All',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('delete_url.php', { deleteAll: true }, function(response) {
                    const res = JSON.parse(response);
                    if (res.status === 'success') {
                        // Clear the HTML table
                        $('#urlTable tbody').empty();
                        
                        Swal.fire('Success', res.message, 'success');
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                });
            }
        });
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Copied!',
                text: 'Short-URL has been copied to the clipboard',
            });
        }).catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Unable to copy Short-URL',
            });
        });
    }
    </script>

    <?php include('footer.php'); ?>
</body>
</html>