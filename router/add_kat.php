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
        include("../lib/header.php");

        if (isset($_POST['add'])) {
            $post_name = mysqli_real_escape_string($db, $_POST['name']);
            $post_category = mysqli_real_escape_string($db, $_POST['category']);

            $checkdb_service = mysqli_query($db, "SELECT * FROM data_router_cat WHERE name = '$post_name'");
            $datadb_service = mysqli_fetch_assoc($checkdb_service);
            if (empty($post_name) || empty($post_category)) {
                $msg_type = "error";
                $msg_content = $add_category_content_error;
            } else if (mysqli_num_rows($checkdb_service) > 0) {
                $msg_type = "error";
                $msg_content = $add_category_content_already;
            } else {
                $insert_provider = mysqli_query($db, "INSERT INTO data_router_cat (name, code, category) VALUES ('$post_name', 'Router', '$post_category')");
                if ($insert_provider == TRUE) {
                    $msg_type = "success";
                    $msg_content = $add_category_content_success;
                } else {
                    $msg_type = "error";
                    $msg_content = "<b>Failed:</b> System Error.";
                }
            }
        }

?>
        <!-- Main Content -->
        <div class="main-content">
            <section class="section">
                <div class="section-header">
                    <h1><?= $add_category; ?></h1>
                </div>

                <div class="section-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="text-danger"><i class="fas fa-list"></i> <?= $add_category; ?></h4>
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
                                    <form class="form-horizontal" role="form" method="POST">
                                        <div class="form-group">
                                            <label class="col-md-2 control-label"><?= $add_category_name; ?></label>
                                            <div class="col-md-12">
                                                <input type="text" name="name" class="form-control" placeholder="Example : Home Mikrotik">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label"><?= $code; ?></label>
                                            <div class="col-md-12">
                                                <input type="text" name="category" class="form-control" placeholder="Example : MR">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-offset-2 col-md-10">
                                                <a href="<?php $cfg_baseurl; ?>kat_router" class="btn btn-danger"><i class="fas fa-arrow-alt-circle-left"></i> <?= $back; ?></a>
                                                <button type="submit" class="btn btn-primary" name="add"><i class="fa fa-paper-plane"></i> Submit </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        </div>
<?php
        include("../lib/footer.php");
    }
} else {
    header("Location: " . $cfg_baseurl);
}
?>