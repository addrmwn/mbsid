<?php
$check_website = $db->query("SELECT * FROM website WHERE id ='1'");
$data_website = $check_website->fetch_assoc();
$lang = $data_website['lang'];

include('./lang/' . $lang . '.php');

$langcode = $data_website['lang'];
include("../lang/" . $lang . ".php");

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <link rel="shortcut icon" href="<?php echo $cfg_baseurl; ?>assets/img/favicon.ico">

    <title><?php echo $data_website['logo_text']; ?></title>

    <!-- General CSS Files -->
    <link rel="stylesheet" href="<?php echo $cfg_baseurl; ?>assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $cfg_baseurl; ?>/assets/modules/fontawesome/css/all.min.css">

    <link rel="stylesheet" href="<?php echo $cfg_baseurl; ?>assets/modules/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="<?php echo $cfg_baseurl; ?>assets/modules/jquery-selectric/selectric.css">

    <!-- DataTables -->
    <link href="<?php echo $cfg_baseurl; ?>assets/css/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $cfg_baseurl; ?>assets/css/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="<?php echo $cfg_baseurl; ?>assets/css/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />

    <!-- Template CSS -->
    <link rel="stylesheet" href="<?php echo $cfg_baseurl; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo $cfg_baseurl; ?>assets/css/components.css">
</head>

<body>
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg"></div>
            <nav class="navbar navbar-expand-lg main-navbar">
                <form class="form-inline mr-auto">
                    <ul class="navbar-nav mr-3">
                        <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
                    </ul>

                </form>
                <ul class="navbar-nav navbar-right">


                    <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                            <img alt="image" src="<?php echo $cfg_baseurl; ?>assets/img/avatar/avatar-1.png" class="rounded-circle mr-1">
                            <div class="d-sm-none d-lg-inline-block">Hi, <?php echo $sess_username; ?></div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="<?php echo $cfg_baseurl; ?>user/setting" class="dropdown-item has-icon">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="<?php echo $cfg_baseurl; ?>logout" class="dropdown-item has-icon text-danger">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>
            <div class="main-sidebar sidebar-style-2">
                <aside id="sidebar-wrapper">
                    <div class="sidebar-brand">
                        <a href="<?php echo $cfg_baseurl; ?>"><?php echo $data_website['logo_text'] ?></a>
                    </div>
                    <div class="sidebar-brand sidebar-brand-sm">
                        <a href="<?php echo $cfg_baseurl; ?>"><?php echo $data_website['text_mini'] ?></a>
                    </div>
                    <ul class="sidebar-menu">
                        <li class="menu-header">Dashboard</li>
                        <li><a class="nav-link" href="<?php echo $cfg_baseurl; ?>"><i class="fas fa-home"></i> <span><?= $dahboard; ?></span></a></li>
                        <?php
                        if (isset($_SESSION['user'])) {
                        ?>
                            <li class="menu-header">Services</li>
                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-server"></i> <span>Menu Router</span></a>
                                <ul class="dropdown-menu">

                                    <li><a class="nav-link" href="<?php echo $cfg_baseurl; ?>router/kat_router"><?php echo $category_router; ?></a></li>
                                    <li><a class="nav-link" href="<?php echo $cfg_baseurl; ?>router/data_router">Data Router</a></li>
                                    <li><a class="nav-link" href="<?php echo $cfg_baseurl; ?>router/test_login">Test Login</a></li>
                                    <li><a class="nav-link" href="<?php echo $cfg_baseurl; ?>router/add_profile">Tambah User Profile</a></li>
                                    <li><a class="nav-link" href="<?php echo $cfg_baseurl; ?>router/list_profile">List User Profile</a></li>

                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link has-dropdown"><i class="fas fa-credit-card"></i> <span>Voucher</span></a>
                                <ul class="dropdown-menu">
                                    <li><a class="nav-link" href="<?php echo $cfg_baseurl; ?>voucher/generate">Generate Voucher</a></li>
                                    <li><a class="nav-link" href="<?php echo $cfg_baseurl; ?>voucher/list">List Voucher</a></li>
                                </ul>
                            </li>
                            <li><a class="nav-link" href="<?php echo $cfg_baseurl; ?>pages/report"><i class="fas fa-money-check"></i> <span>Report</span></a></li>

                            <li class="nav-item dropdown">
                                <a href="#" class="nav-link has-dropdown"><i class="fas fa-cog"></i> <span><?= $settings; ?></span></a>
                                <ul class="dropdown-menu">
                                    <li><a class="nav-link" href="<?php echo $cfg_baseurl; ?>user/setting"><?= $account_settings; ?></a></li>
                                    <li><a class="nav-link" href="<?php echo $cfg_baseurl; ?>user/setweb"><?= $website_settings; ?></a></li>

                                </ul>
                            </li>


                    </ul>

                    <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
                        <a href="<?php echo $cfg_baseurl; ?>changelogs" class="btn btn-primary btn-lg btn-block btn-icon-split">
                            <i class="fas fa-info-circle"></i> Changelogs
                        </a>
                    </div>
                </aside>
            </div>
        <?php
                        }
        ?>