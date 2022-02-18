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
            $post_cat = mysqli_real_escape_string($db, $_POST['category']);
            $post_price = mysqli_real_escape_string($db, $_POST['price']);
            $post_price_reseller = mysqli_real_escape_string($db, $_POST['price_reseller']);
            $post_price_admin = mysqli_real_escape_string($db, $_POST['price_admin']);
            $post_uptime = mysqli_real_escape_string($db, $_POST['uptime']);
            $post_input_router = mysqli_real_escape_string($db, $_POST['input_router']);
            $post_mac = mysqli_real_escape_string($db, $_POST['mac']);

            if ($post_mac == 'Ya') {
                $lock = '; [:local mac $"mac-address"; /ip hotspot user set mac-address=$mac [find where name=$user]]';
            } else {
                $lock = '';
            }

            $scheduler_add = '{:local usernya $user;:if ([/system schedule find name=$usernya]="") do={/system schedule add name=$usernya interval=' . $post_uptime . ' on-event="/ip hotspot user remove [find name=$usernya]\r\n/ip hotspot active remove [find user=$usernya]\r\n/system schedule remove [find name=$usernya]"}}' . $lock;
            $ratelimit = ($_POST['ratelimit']);
            $sharedusers = ($_POST['sharedusers']);

            $checkdb_router = mysqli_query($db, "SELECT * FROM data_router WHERE id = '$post_input_router' AND status = 'Active'");
            $datadb_router = mysqli_fetch_assoc($checkdb_router);

            $post_iphost = $datadb_router['ip_server'];
            $post_username = $datadb_router['username_mikrotik'];
            $post_password = $datadb_router['password_mikrotik'];
            $post_dns = $datadb_router['dns'];

            $password_decrypt = decrypt($datadb_router['password_mikrotik']);

            $checkdb_service = mysqli_query($db, "SELECT * FROM services WHERE service = '$post_service'");
            $datadb_service = mysqli_fetch_assoc($checkdb_service);
            if (empty($post_service) || empty($post_price)) {
                $msg_type = "error";
                $msg_content = "<b>Gagal:</b> Mohon mengisi semua input.";
            } else if (mysqli_num_rows($checkdb_service) > 0) {
                $msg_type = "error";
                $msg_content = "<b>Gagal:</b> Nama $post_service sudah terdaftar di database.";
            } else {
                $insert_service = mysqli_query($db, "INSERT INTO services (category, service, uptime, dns,ip, username_mikrotik, password_mikrotik, price, status ) VALUES ('$post_cat', '$post_service', '$post_uptime', '$post_dns', '$post_iphost', '$post_username', '$post_password','$post_price','Active')");
                if ($insert_service == TRUE) {
                    $msg_type = "success";

                    $msg_content = "<b>Berhasil:</b> Layanan berhasil ditambah.<br /><b>Service Name:</b> $post_service<br /><b>Category:</b> $post_cat <br /><b>Harga:</b> Rp " . number_format($post_price, 0, ',', '.') . "<br /><br /><b>Status:</b> Active";
                    $API = new RouterosAPI();
                    $API->debug = false;
                    if ($API->connect($post_iphost, $post_username, $password_decrypt)) {
                        $API->comm("/ip/hotspot/user/profile/add", array(
                            /*"add-mac-cookie" => "yes",*/
                            "name" => "$post_service",
                            "rate-limit" => "$ratelimit",
                            "shared-users" => "$sharedusers",
                            "on-login" => "$scheduler_add",
                            "transparent-proxy" => "yes",
                            "status-autorefresh" => "1m",
                        ));
                    } else {
                        echo " Router Not Connected";
                    }
                } else {
                    $msg_type = "error";
                    $msg_content = "<b>Gagal:</b> Error system.";
                }
            }
        }

        include("../lib/header.php");

?>
        <!-- Main Content -->
        <div class="main-content">
            <section class="section">
                <div class="section-header">
                    <h1>Tambah Data Profile</h1>
                </div>


                <div class="section-body">
                    <div class="row">

                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="text-danger"><i class="fas fa-list"></i> Tambah user profile</h4>
                                </div>

                                <div class="card-body">

                                    <?php
                                    if ($msg_type == "success") {
                                    ?>
                                        <div class="alert alert-primary">
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
                                            <label class="col-md-5 control-label">Kategori Data Router</label>
                                            <div class="col-md-12">
                                                <select class="form-control" id="category_router" name="category_router">
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
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Nama Server</label>
                                            <div class="col-md-12">
                                                <select class="form-control" name="input_router" id="input_router">
                                                    <option value="0">Pilih kategori Data Router..</option>
                                                </select>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Category Voucher</label>
                                            <div class="col-md-12">
                                                <select class="form-control" name="category">
                                                    <?php
                                                    $check_cat = mysqli_query($db, "SELECT * FROM service_cat WHERE code = 'Router' ORDER BY id DESC");
                                                    while ($data_cat = mysqli_fetch_assoc($check_cat)) {
                                                    ?>
                                                        <option value="<?php echo $data_cat['category']; ?>"><?php echo $data_cat['name']; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Nama Profile</label>
                                            <div class="col-md-12">
                                                <input type="text" name="service" class="form-control" placeholder="Example : Paket-4-Jam">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Shared User</label>
                                            <div class="col-md-12">
                                                <input type="text" name="sharedusers" class="form-control" value="1" placeholder="1">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Rate Limit [up/down]</label>
                                            <div class="col-md-12">
                                                <input type="text" name="ratelimit" class="form-control" placeholder="Example : 512k/1M">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Masa Aktif</label>
                                            <div class="col-md-12">
                                                <input type="text" name="uptime" class="form-control" placeholder="Example : 1h/4h/7h/30d">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Kunci Mac Address</label>
                                            <div class="col-md-12">
                                                <select class="form-control" name="mac">
                                                    <option value="">Pilih salah satu</option>
                                                    <option value="Ya">Ya</option>
                                                    <option value="Tidak">Tidak</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Harga</label>
                                            <div class="col-md-12">
                                                <input type="text" name="price" class="form-control" placeholder="Example : 5000">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-offset-2 col-md-10">
                                                <button type="submit" class="btn btn-primary" name="submit"><i class="fa fa-paper-plane"></i> Submit </button>
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
                $("#category_router").change(function() {
                    var category = $("#category_router").val();
                    $.ajax({
                        url: '<?php echo $cfg_baseurl; ?>inc/router_service.php',
                        data: 'category=' + category,
                        type: 'POST',
                        dataType: 'html',
                        success: function(msg) {
                            $("#input_router").html(msg);
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