<?php

session_start();
require("../mainconfig.php");
require("../lib/api.class.php");
$msg_type = "nothing";

if (isset($_SESSION['user'])) {
    $sess_username = $_SESSION['user']['username'];
    $check_user = mysqli_query($db, "SELECT * FROM users WHERE username = '$sess_username'");
    $data_user = mysqli_fetch_assoc($check_user);
    if (mysqli_num_rows($check_user) == 0) {
        header("Location: " . $cfg_baseurl . "account/logout");
    } else if ($data_user['status'] == "Suspended") {
        header("Location: " . $cfg_baseurl . "account/logout");
    } else if ($data_user['level'] != "Developers") {
        header("Location: " . $cfg_baseurl);
    } else {
        if (isset($_POST['submit'])) {
            $post_service = mysqli_real_escape_string($db, $_POST['service']);
            $check_service = mysqli_query($db, "SELECT * FROM data_router WHERE id = '$post_service' AND status = 'Active'");
            $data_service = mysqli_fetch_assoc($check_service);

            $post_ip = $data_service['ip_server'];
            $post_username = $data_service['username_mikrotik'];
            $post_password = decrypt($data_service['password_mikrotik']);

            $API = new RouterosAPI();
            $API->debug = false;
            if ($API->connect($post_ip, $post_username, $post_password)) {
                $msg_type = "success";
                $msg_content = "Router Berhasil Konek";
            } else {
                $msg_type = "error";
                $msg_content = "Router Tidak Konek";
            }
        }

        include("../lib/header.php");

?>
        <!-- Main Content -->
        <div class="main-content">
            <section class="section">
                <div class="section-header">
                    <h1>Test Login</h1>
                </div>
                <div class="section-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="text-danger"><i class="fas fa-sign-in-alt"></i> Test Login</h4>
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
                                            <label class="col-md-5 control-label">Kategori</label>
                                            <div class="col-md-12">
                                                <select class="form-control" id="category" name="category">
                                                    <option value="0">Pilih salah satu...</option>
                                                    <?php
                                                    $check_cat = mysqli_query($db, "SELECT * FROM data_router_cat WHERE code = 'Router' ORDER BY name ASC");
                                                    while ($data_cat = mysqli_fetch_assoc($check_cat)) {
                                                    ?>
                                                        <option value="<?php echo $data_cat['category']; ?>"><?php echo $data_cat['category']; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-2 control-label">Nama Server</label>
                                                <div class="col-md-12">
                                                    <select class="form-control" name="service" id="service">
                                                        <option value="0">Pilih kategori...</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-offset-2 col-md-10">
                                                <button type="submit" class="btn btn-primary" name="submit"><i class="fas fa-sign-in-alt"></i> Test Login </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        </section>
        </div>
        <script type="text/javascript" src="../assets/js/jquery-1.10.2.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $("#category").change(function() {
                    var category = $("#category").val();
                    $.ajax({
                        url: '<?php echo $cfg_baseurl; ?>inc/router_service.php',
                        data: 'category=' + category,
                        type: 'POST',
                        dataType: 'html',
                        success: function(msg) {
                            $("#service").html(msg);
                        }
                    });
                });
            });
        </script>

<?php
        include("../lib/footer.php");
    }
} else {
    header("Location: " . $cfg_baseurl);
}
?>