<?php
session_start();
require("../mainconfig.php");
$msg_type = "nothing";

if (isset($_SESSION['user'])) {
    $sess_username = $_SESSION['user']['username'];
    $check_user = mysqli_query($db, "SELECT * FROM users WHERE username = '$sess_username'");
    $data_user = mysqli_fetch_assoc($check_user);
    if (mysqli_num_rows($check_user) == 0) {
        header("Location: " . $cfg_baseurl . "logout.php");
    } else if ($data_user['status'] == "Suspended") {
        header("Location: " . $cfg_baseurl . "logout.php");
    } else if ($data_user['level'] != "Developers") {
        header("Location: " . $cfg_baseurl);
    } else {
        if (isset($_POST['setting'])) {

            $post_title = $_POST['title'];
            $post_logotext = $_POST['logo_text'];
            $post_text_mini = $_POST['text_mini'];
            $post_langcode = $_POST['langcode'];

            if (empty($post_title) || empty($post_logotext)) {
                $msg_type = "error";
                $msg_content = "<b>Gagal:</b> Mohon mengisi semua input.";
            } else {
                $update_status = mysqli_query($db, "UPDATE website SET title = '$post_title', logo_text = '$post_logotext', text_mini = '$post_text_mini', lang = '$post_langcode' WHERE id = '1'");
                if ($update_status == TRUE) {
                    $msg_type = "success";
                    $msg_content = "<b>Berhasil:</b> Website berhasil di konfigurasi.";
                } else {
                    $msg_type = "error";
                    $msg_content = "<b>Gagal:</b> Error system.";
                }
            }
        }
        $checkdb_service = mysqli_query($db, "SELECT * FROM website WHERE id = '1'");
        $datadb_service = mysqli_fetch_assoc($checkdb_service);
        include("../lib/header.php");
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
                                        <h4 class="text-danger"><i class="fas fa-globe"></i> Pengaturan Website</h4>
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
                                            <label>Nama Website</label>
                                            <input type="text" class="form-control" name="title" value="<?php echo $datadb_service['title']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Logo Text</label>
                                            <input type="text" class="form-control" name="logo_text" value="<?php echo $datadb_service['logo_text']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Logo Mini Text</label>
                                            <input type="text" class="form-control" name="text_mini" value="<?php echo $datadb_service['text_mini']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Bahasa</label>
                                            <select class="form-control" name="langcode" required>
                                                <option value="<?php echo $datadb_service['lang']; ?>"><?php echo $datadb_service['lang']; ?></option>
                                                <option value="id">Indonesia</option>
                                                <option value="en">English</option>

                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-block waves-effect w-md waves-light" name="setting">Ubah</button>
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
    }
} else {
    header("Location: " . $cfg_baseurl);
}
?>