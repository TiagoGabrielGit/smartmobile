<?php
session_start();

if (isset($_SESSION['id'])) {
    ?>
    <!DOCTYPE html>
    <html lang="pt-br">

    <head>
        <meta charset="utf-8">
        <meta content="width=device-width, initial-scale=1.0" name="viewport">

        <title>SmartMobile</title>
        <meta content="" name="description">
        <meta content="" name="keywords">

        <!-- Favicons -->
        <link href="/assets/img/favicon.png" rel="icon">
        <link href="/assets/img/apple-touch-icon.png" rel="apple-touch-icon">

        <!-- Google Fonts -->
        <link href="https://fonts.gstatic.com" rel="preconnect">
        <link
            href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
            rel="stylesheet">

        <!-- Vendor CSS Files -->
        <link href="/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
        <link href="/assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
        <link href="/assets/vendor/quill/quill.snow.css" rel="stylesheet">
        <link href="/assets/vendor/quill/quill.bubble.css" rel="stylesheet">
        <link href="/assets/vendor/remixicon/remixicon.css" rel="stylesheet">
        <link href="/assets/vendor/simple-datatables/style.css" rel="stylesheet">

        <!-- Template Main CSS File -->
        <link href="/assets/css/style.css" rel="stylesheet">

        <!-- =======================================================
      * Template Name: NiceAdmin
      * Updated: Jan 29 2024 with Bootstrap v5.3.2
      * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
      * Author: BootstrapMade.com
      * License: https://bootstrapmade.com/license/
      ======================================================== -->
    </head>

    <body>
        <header id="header" class="header fixed-top d-flex align-items-center">

            <div class="d-flex align-items-center justify-content-between">
                <a href="/index.php" class="logo d-flex align-items-center">
                    <img src="/assets/img/logo.png" alt="">
                    <span class="d-none d-lg-block">SmartMobile</span>
                </a>
            </div><!-- End Logo -->

            <nav class="header-nav ms-auto">
                <ul class="d-flex align-items-center">

                    <li class="nav-item dropdown pe-3">

                        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                            <img src="/assets/img/profile-img.png" alt="Profile" class="rounded-circle">
                            <span class="d-none d-md-block dropdown-toggle ps-2">
                                <?= $_SESSION['nome_usuario'] ?>
                            </span>
                        </a><!-- End Profile Iamge Icon -->

                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="/logout.php">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <span>Sign Out</span>
                                </a>
                            </li>

                        </ul><!-- End Profile Dropdown Items -->
                    </li><!-- End Profile Nav -->
                </ul>
            </nav><!-- End Icons Navigation -->

        </header><!-- End Header -->

        <?php
} else {
    // ID de sessão não está definido, redirecionar para login.php
    header("Location: /login.php");
    exit(); // É importante sair do script após o redirecionamento
}
?>