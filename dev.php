<?php
session_start();

require 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$menu = "dev";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Development Team</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/hover.css/2.3.1/css/hover-min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/dev.css"> <!-- Include dev.css file -->
</head>
<body>
    <?php include("includes/header.php"); ?>

    <section class="content">
        <div class="container-fluid">
            <h1 class="text-left animate__animated animate__fadeInDown mb-4">
                <i class="fas fa-users"></i> Development Team
            </h1>

            <!-- Navigation Tabs -->
            <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active hvr-underline-from-center" id="executive-tab" data-bs-toggle="tab" data-bs-target="#executive" type="button" role="tab" aria-controls="executive" aria-selected="true">
                        <i class="fas fa-user-tie"></i> Executives
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link hvr-underline-from-center" id="advisor-tab" data-bs-toggle="tab" data-bs-target="#advisor" type="button" role="tab" aria-controls="advisor" aria-selected="false">
                        <i class="fas fa-users-cog"></i> Advisory Team
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link hvr-underline-from-center" id="developer-tab" data-bs-toggle="tab" data-bs-target="#developer" type="button" role="tab" aria-controls="developer" aria-selected="false">
                        <i class="fas fa-laptop-code"></i> Developers
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="myTabContent">
                <!-- Executives Tab -->
                <div class="tab-pane fade show active" id="executive" role="tabpanel" aria-labelledby="executive-tab">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-primary text-white">
                                    <h3><i class="fas fa-user-tie"></i> Executives</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <div class="card h-100 hvr-glow">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <img src="assets/dist/img/sorachai-01 (1).jpg" class="img-fluid rounded-circle me-3" alt="Asst. Prof. Dr. Sorachai Kamonlimskul">
                                                        <div>
                                                            <h5 class="card-title"><i class="fas fa-user-tie"></i> Asst. Prof. Dr. Sorachai Kamonlimskul</h5>
                                                            <p class="card-text"><i class="fas fa-briefcase"></i> Director of the Center for Educational Innovation and Technology</p>
                                                            <p class="card-text"><i class="fas fa-envelope"></i> Email: sorachai@sut.ac.th</p>
                                                            <p class="card-text"><i class="fas fa-phone"></i> Phone: 0-4422-5779</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <div class="card h-100 hvr-glow">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <img src="assets/dist/img/thawatphong-01.jpg" class="img-fluid rounded-circle me-3" alt="Asst. Prof. Dr. Thawatphong Pitak">
                                                        <div>
                                                            <h5 class="card-title"><i class="fas fa-user-tie"></i> Asst. Prof. Dr. Thawatphong Pitak</h5>
                                                            <p class="card-text"><i class="fas fa-briefcase"></i> Deputy Director of the Center for Educational Innovation and Technology</p>
                                                            <p class="card-text"><i class="fas fa-envelope"></i> Email: thawatphong@sut.ac.th</p>
                                                            <p class="card-text"><i class="fas fa-phone"></i> Phone: 0-4422-5778</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advisory Team Tab -->
                <div class="tab-pane fade" id="advisor" role="tabpanel" aria-labelledby="advisor-tab">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-success text-white">
                                    <h3><i class="fas fa-users-cog"></i> Advisory Team (Innovation Development Division)</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-4">
                                            <div class="card h-100 hvr-glow">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <img src="assets/dist/img/metee-01.jpg" class="img-fluid rounded-circle me-3" alt="Mr. Metee Prasomsap">
                                                        <div>
                                                            <h5 class="card-title"><i class="fas fa-user"></i> Mr. Metee Prasomsap</h5>
                                                            <p class="card-text"><i class="fas fa-briefcase"></i> Head of Innovation Development Division</p>
                                                            <p class="card-text"><i class="fas fa-envelope"></i> Email: metee.ceit@gmail.com</p>
                                                            <p class="card-text"><i class="fas fa-phone"></i> Phone: 0-4422-5769</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-4">
                                            <div class="card h-100 hvr-glow">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <img src="assets/dist/img/thanakorn-1.jpg" class="img-fluid rounded-circle me-3" alt="Mr. Thanakorn Wichitking">
                                                        <div>
                                                            <h5 class="card-title"><i class="fas fa-user"></i> Mr. Thanakorn Wichitking</h5>
                                                            <p class="card-text"><i class="fas fa-briefcase"></i> Innovation Development Officer</p>
                                                            <p class="card-text"><i class="fas fa-envelope"></i> Email: tanakorn.ceit@gmail.com</p>
                                                            <p class="card-text"><i class="fas fa-phone"></i> Phone: 0-4422-5769</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-4">
                                            <div class="card h-100 hvr-glow">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <img src="assets/dist/img/pongsakor-01.jpg" class="img-fluid rounded-circle me-3" alt="Mr. Pongsakorn Prommak">
                                                        <div>
                                                            <h5 class="card-title"><i class="fas fa-user"></i> Mr. Pongsakorn Prommak</h5>
                                                            <p class="card-text"><i class="fas fa-briefcase"></i> Innovation Development Officer</p>
                                                            <p class="card-text"><i class="fas fa-envelope"></i> Email: beztmartyn@gmail.com</p>
                                                            <p class="card-text"><i class="fas fa-phone"></i> Phone: 087-654-3210</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Developers Tab -->
                <div class="tab-pane fade" id="developer" role="tabpanel" aria-labelledby="developer-tab">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-warning text-white">
                                    <h3><i class="fas fa-laptop-code"></i> Developers</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-4">
                                            <div class="card h-100 hvr-glow">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <img src="assets/dist/img/thanakorn-1.jpg" class="img-fluid rounded-circle me-3" alt="Mr. Thanakorn Wichitking">
                                                        <div>
                                                            <h5 class="card-title"><i class="fas fa-user"></i> Mr. Thanakorn Wichitking</h5>
                                                            <p class="card-text"><i class="fas fa-briefcase"></i> Innovation Development Officer</p>
                                                            <p class="card-text"><i class="fas fa-envelope"></i> Email: tanakorn.ceit@gmail.com</p>
                                                            <p class="card-text"><i class="fas fa-phone"></i> Phone: 0-4422-5769</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-4">
                                            <div class="card h-100 hvr-glow">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <img src="assets/dist/img/adithep2.jpg" class="img-fluid rounded-circle me-3" alt="Mr. Adithep Srimonta">
                                                        <div>
                                                            <h5 class="card-title"><i class="fas fa-user"></i> Mr. Adithep Srimonta</h5>
                                                            <p class="card-text"><i class="fas fa-briefcase"></i> Internship Student, Academic Year 2024 (ETC MSU)</p>
                                                            <p class="card-text"><i class="fas fa-envelope"></i> Email: adithepsrimonta2546@gmail.com</p>
                                                            <p class="card-text"><i class="fas fa-phone"></i> Phone: 095-468-9268</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-4">
                                            <div class="card h-100 hvr-glow">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <img src="assets/dist/img/6150.jpg" class="img-fluid rounded-circle me-3" alt="Mr. Winai Changlek">
                                                        <div>
                                                            <h5 class="card-title"><i class="fas fa-user"></i> Mr. Winai Changlek</h5>
                                                            <p class="card-text"><i class="fas fa-briefcase"></i> Internship Student, Academic Year 2024 (ETC MSU)</p>
                                                            <p class="card-text"><i class="fas fa-envelope"></i> Email: wc0705900@gmail.com</p>
                                                            <p class="card-text"><i class="fas fa-phone"></i> Phone: 064-118-5435</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <?php include("footer.php"); ?>
</body>
</html>