<?php
session_start();
require("../mainconfig.php");
$page_type = "user_login";
$msg_type = "nothing";
$check_website = $db->query("SELECT * FROM website WHERE id ='1'");
$data_website = $check_website->fetch_assoc();

if (isset($_SESSION['user'])) {
    $sess_username = $_SESSION['user']['username'];
    $check_user = mysqli_query($db, "SELECT * FROM users WHERE username = '$sess_username'");
    $cek_user_ulang = mysqli_num_rows($check_user);
    $data_pengguna = mysqli_fetch_assoc($check_user);
    header("Location: " . $cfg_baseurl);
    $verif_password = password_verify($password, $data_pengguna['password']);

    if (mysqli_num_rows($check_user) == 0) {
        header("Location: " . $cfg_baseurl . "logout.php");
    }
}
if (isset($_POST['login'])) {
    $post_username = mysqli_real_escape_string($db, stripslashes(strip_tags(htmlspecialchars(htmlentities($_POST['username'], ENT_QUOTES)))));
    $post_password = mysqli_real_escape_string($db, stripslashes(strip_tags(htmlspecialchars(htmlentities($_POST['password'], ENT_QUOTES)))));
    $cek_pengguna = mysqli_query($db, "SELECT * FROM users WHERE username = '$post_username'");
    $cek_pengguna_ulang = mysqli_num_rows($cek_pengguna);
    $data_pengguna = mysqli_fetch_assoc($cek_pengguna);

    $verif_password = password_verify($post_password, $data_pengguna['password']);


    if (empty($post_username)) {
        $msg_type = "error";
        $msg_content = '<b>Gagal:</b> Mohon mengisi semua input..<script>swal("Error!", "Mohon mengisi semua input..", "error");</script>';
    } else if ($cek_pengguna_ulang == 0) {
        $msg_type = "error";
        $msg_content = '<b>Gagal:</b> Username Tidak Terdaftar.<script>swal("Error!", "Username Tidak Terdaftar.", "error");</script>';
    }
    if (empty($post_password)) {
        $msg_type = "error";
        $msg_content = '<b>Gagal:</b> Mohon mengisi semua input.<script>swal("Error!", "Mohon mengisi semua input.", "error");</script>';
    } else if ($verif_password <> $data_pengguna['password']) {
        $msg_type = "error";
        $msg_content = '<b>Gagal:</b> Password salah.<script>swal("Error!", "Password salah.", "error");</script>';
    } else {

        if ($cek_pengguna_ulang == 1) {
            if ($verif_password == true) {
                $_SESSION['user'] = $data_pengguna;
                $username = $data_pengguna['username'];
                exit(header("Location: " . $cfg_baseurl));
            } else {
                $msg_type = "error";
                $msg_content = "<b>Gagal:</b> Error system (1).";
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <link rel="shortcut icon" href="<?php echo $cfg_baseurl; ?>assets/img/favicon.ico">

    <title>Login</title>

    <!-- General CSS Files -->
    <link rel="stylesheet" href="<?php echo $cfg_baseurl; ?>assets/modules/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $cfg_baseurl; ?>assets/modules/fontawesome/css/all.min.css">

    <link rel="stylesheet" href="<?php echo $cfg_baseurl; ?>assets/modules/bootstrap-social/bootstrap-social.css">


    <!-- Template CSS -->
    <link rel="stylesheet" href="<?php echo $cfg_baseurl; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo $cfg_baseurl; ?>assets/css/components.css">
</head>

<body>
    <div id="app">
        <section class="section">
            <div class="container mt-5">
                <div class="row">
                    <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                        <div class="login-brand">
                            <img src="<?php echo $cfg_baseurl; ?>assets/img/header.png" alt="logo" width="100">
                        </div>

                        <div class=" card card-primary">
                            <div class="card-header">
                                <h4>Login</h4>
                            </div>

                            <div class="card-body">
                                <?php
                                if ($msg_type == "error") {
                                ?>
                                    <div class="alert alert-danger">
                                        <i class="fa fa-times-circle"></i>
                                        <?php echo $msg_content; ?>
                                    </div>
                                <?php
                                }
                                ?>
                                <form method="POST" class="needs-validation" novalidate="">
                                    <div class="form-group">
                                        <label for="username">Username</label>
                                        <input class="form-control" type="text" name="username" autocomplete="off">

                                    </div>

                                    <div class="form-group">
                                        <div class="d-block">
                                            <label for="password" class="control-label">Password</label>
                                            <div class="float-right">
                                            </div>
                                        </div>
                                        <input class="form-control" type="password" name="password">

                                    </div>


                                    <div class="form-group">
                                        <button class="btn btn-primary btn-lg btn-block" name="login" type="submit"><i class="fas fa-sign-in-alt"></i>
                                            Login
                                        </button>
                                    </div>
                                </form>


                            </div>
                        </div>

                        <div class="simple-footer">
                            Copyright &copy; Mikbill <?php echo date("Y"); ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- General JS Scripts -->
    <script src="<?php echo $cfg_baseurl; ?>assets/modules/jquery.min.js"></script>
    <script src="<?php echo $cfg_baseurl; ?>assets/modules/popper.js"></script>
    <script src="<?php echo $cfg_baseurl; ?>assets/modules/tooltip.js"></script>
    <script src="<?php echo $cfg_baseurl; ?>assets/modules/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?php echo $cfg_baseurl; ?>assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
    <script src="<?php echo $cfg_baseurl; ?>assets/modules/moment.min.js"></script>
    <script src="<?php echo $cfg_baseurl; ?>assets/js/stisla.js"></script>

    <!-- JS Libraies -->
    <script src="<?php echo $cfg_baseurl; ?>assets/modules/sweetalert/sweetalert.min.js"></script>


    <!-- Template JS File -->
    <script src="<?php echo $cfg_baseurl; ?>assets/js/scripts.js"></script>
    <script src="<?php echo $cfg_baseurl; ?>assets/js/custom.js"></script>

    <!-- Page Specific JS File -->
</body>

</html>