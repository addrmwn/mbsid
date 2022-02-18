<?php
session_start();
require("../mainconfig.php");

if (isset($_SESSION['user'])) {
    $sess_username = $_SESSION['user']['username'];
    $check_user = mysqli_query($db, "SELECT * FROM users WHERE username = '$sess_username'");
    $data_user = mysqli_fetch_assoc($check_user);
    if (mysqli_num_rows($check_user) == 0) {
        header("Location: " . $cfg_baseurl . "auth/logout.php");
    } else if ($data_user['status'] == "Suspended") {
        header("Location: " . $cfg_baseurl . "auth/logout.php");
    } else {
        if (isset($_GET['id'])) {
            $post_id = mysqli_real_escape_string($db, $_GET['id']);
            $check_router = mysqli_query($db, "SELECT * FROM data_router WHERE id = '$post_id'");
            $data_router = mysqli_fetch_assoc($check_router);

            if (mysqli_num_rows($check_router) == 0) {
                header("Location: " . $cfg_baseurl . "router/data_router");
            } else {
                include("../lib/header.php");


                if (isset($_POST['save'])) {
                    $post_order_id = mysqli_real_escape_string($db, $_GET['id']);
                    $post_name_server = mysqli_real_escape_string($db, $_POST['nama_server']);
                    $post_dns = mysqli_real_escape_string($db, $_POST['dns']);
                    $post_ip = mysqli_real_escape_string($db, $_POST['ip_server']);
                    $post_username_mikrotik = mysqli_real_escape_string($db, $_POST['username_mikrotik']);
                    $post_password_mikrotik = mysqli_real_escape_string($db, encrypt($_POST['password_mikrotik']));
                    $post_status = mysqli_real_escape_string($db, $_POST['status']);


                    $check_update_router = mysqli_query($db, "SELECT * FROM data_router WHERE id = '$post_order_id'");
                    $data_update_router = mysqli_fetch_array($check_update_router, MYSQLI_ASSOC);

                    if (mysqli_num_rows($check_update_router) == 0) {
                        $_SESSION['msg_type'] = $msg_type = "error";
                        $_SESSION['msg_content'] = $msg_content = "Data router tidak di temukan.";
                    } else {
                        $success_update = mysqli_query($db, "UPDATE data_router SET nama_server = '$post_name_server', dns = '$post_dns', ip_server = '$post_ip', username_mikrotik = '$post_username_mikrotik', password_mikrotik = '$post_password_mikrotik', status = '$post_status' WHERE id = '$post_order_id'");
                        if ($success_update == TRUE) {
                            $_SESSION['msg_type'] = $msg_type = "success";
                            $_SESSION['msg_content'] = $msg_content = $msg_content = "<b>Berhasil:</b> Data Router berhasil di edit";
                            header("Location: " . $cfg_baseurl . "router/data_router.php");
                        } else {
                            $_SESSION['msg_type'] = $msg_type = "error";
                            $_SESSION['msg_content'] = $msg_content = "Error database. (Update)";
                        }
                    }
                }

?>
                <!-- Main Content -->
                <div class="main-content">
                    <section class="section">
                        <div class="section-header">
                            <h1>Edit Data Router</h1>
                        </div>
                        <div class="section-body">
                            <div class="row">
                                <div class="col-xl-12 col-lg-7">

                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class="text-danger"><i class="fas fa-server"></i> Detail Router</h4>
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
                                                    <div class="table-responsive">
                                                        <table class="table table-striped table-bordered" data-filter=#filter>

                                                            <tr>
                                                                <td><b>Nama Server</b></td>

                                                                <td> <input type="text" class="form-control" name="nama_server" value="<?php echo $data_router['nama_server']; ?>">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><b>DNS</b></td>
                                                                <td> <input type="text" class="form-control" name="dns" value="<?php echo $data_router['dns']; ?>">
                                                            </tr>
                                                            <tr>
                                                                <td><b>IP Server</b></td>
                                                                <td> <input type="text" class="form-control" name="ip_server" value="<?php echo $data_router['ip_server']; ?>">
                                                            </tr>
                                                            <tr>
                                                                <td><b>Username</b></td>
                                                                <td> <input type="text" class="form-control" name="username_mikrotik" value="<?php echo $data_router['username_mikrotik']; ?>">
                                                            </tr>
                                                            <tr>
                                                                <td><b>Password</b></td>
                                                                <td> <input type="text" class="form-control" name="password_mikrotik" value="<?php echo decrypt($data_router['password_mikrotik']); ?>">
                                                            </tr>
                                                            <tr>
                                                                <td><b>Status</b></td>
                                                                <td>
                                                                    <select class="form-control" name="status">
                                                                        <option value="<?php echo $data_router['status']; ?>"><?php echo $data_router['status']; ?></option>
                                                                        <option value="Active">Active</option>
                                                                        <option value="Not active">Not active</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <a href="<?php echo $cfg_baseurl; ?>router/data_router" class="btn btn-success m-b-10"><i class="fa fa-arrow-left"></i> Kembali</a>
                                                        <button type="submit" class="btn btn-primary" name="save"><i class="fa fa-paper-plane"></i> Update </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </section>
                </div>
<?php
                include("../lib/footer.php");
            }
        } else {
            header("Location: " . $cfg_baseurl . "router/data_router");
        }
    }
} else {
    header("Location: " . $cfg_baseurl);
}
?>