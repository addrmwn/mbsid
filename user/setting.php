<?php
session_start();
require("../mainconfig.php");
$page_type = "user_settings";

if (isset($_SESSION['user'])) {
    $sess_username = $_SESSION['user']['username'];
    $check_user = mysqli_query($db, "SELECT * FROM users WHERE username = '$sess_username'");
    $data_user = mysqli_fetch_assoc($check_user);
    if (mysqli_num_rows($check_user) == 0) {
        header("Location: " . $cfg_baseurl . "logout.php");
    } else if ($data_user['status'] == "Suspended") {
        header("Location: " . $cfg_baseurl . "logout.php");
    }

    include("../lib/header.php");
    $msg_type = "nothing";

    if (isset($_POST['change_pswd'])) {
        $post_password = mysqli_real_escape_string($db, $_POST['password']);
        $post_npassword = mysqli_real_escape_string($db, $_POST['npassword']);
        $post_cnpassword = mysqli_real_escape_string($db, $_POST['cnpassword']);

        $verif_password = password_verify($post_password, $data_user['password']);


        if (empty($post_password) || empty($post_npassword) || empty($post_cnpassword)) {
            $msg_type = "error";
            $msg_content = "<b>Gagal:</b> Mohon mengisi semua input.";
        } else if ($verif_password == false) {
            $msg_type = "error";
            $msg_content = "<b>Gagal:</b> Password salah.";
        } else if (strlen($post_npassword) < 5) {
            $msg_type = "error";
            $msg_content = "<b>Gagal:</b> Password baru telalu pendek, minimal 5 karakter.";
        } else if ($post_cnpassword <> $post_npassword) {
            $msg_type = "error";
            $msg_content = "<b>Gagal:</b> Konfirmasi password baru tidak sesuai.";
        } else if ($data_user['username'] == "demoadmin") {
            $msg_type = "error";
            $msg_content = "<b>Gagal:</b> Password tidak bisa di ganti .";
        } else {
            $hashdata = password_hash($post_npassword, PASSWORD_DEFAULT);
            $update_user = mysqli_query($db, "UPDATE users SET password = '$hashdata' WHERE username = '$sess_username'");
            if ($update_user == TRUE) {
                $msg_type = "success";
                $msg_content = "<b>Success:</b> Password telah diubah.";
            } else {
                $msg_type = "error";
                $msg_content = "<b>Gagal:</b> Error system.";
            }
        }
    }

    $check_user = mysqli_query($db, "SELECT * FROM users WHERE username = '$sess_username'");
    $data_user = mysqli_fetch_assoc($check_user);
?>
    <!-- Main Content -->
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Settings</h1>
            </div>

            <div class="section-body">
                <div class="row">

                    <div class="col-md-12">
                        <form class="form-horizontal" role="form" method="POST">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="text-danger"><i class="fas fa-lock"></i> Change Password</h4>
                                </div>
                                <div class="card-body">
                                    <?php
                                    if ($msg_type == "success") {
                                    ?>
                                        <div class="alert alert-success">
                                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                                            <i class="fa fa-check-circle"></i>
                                            <?php echo $msg_content; ?>
                                        </div>
                                    <?php
                                    } else if ($msg_type == "error") {
                                    ?>
                                        <div class="alert alert-danger">
                                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                                            <i class="fa fa-times-circle"></i>
                                            <?php echo $msg_content; ?>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                    <div class="form-group">
                                        <label for="site-title" class="form-control-label col-sm-3 ">Password saat ini</label>
                                        <div class="col-md-12">
                                            <input type="password" name="password" class="form-control" placeholder="Password">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="site-description" class="form-control-label col-sm-3 ">Password Baru</label>
                                        <div class="col-md-12">
                                            <input type="password" name="npassword" class="form-control" placeholder="Password Baru">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-control-label col-sm-3 ">Konfirmasi Password Baru</label>
                                        <div class="col-md-12">
                                            <input type="password" name="cnpassword" class="form-control" placeholder="Konfirmasi Password Baru">
                                        </div>
                                    </div>



                                </div>
                                <div class="card-footer bg-whitesmoke ">
                                    <button class="btn btn-primary" name="change_pswd"><i class="fas fa-paper-plane"></i> Ganti Password</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>

<?php
    include("../lib/footer.php");
} else {
    header("Location: " . $cfg_baseurl);
}
?>