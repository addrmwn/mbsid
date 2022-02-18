<?php
session_start();
require("../mainconfig.php");
$page_type = "sosmed";

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
    require("../lib/api.class.php");
    $msg_type = "nothing";

    if (isset($_POST['generate'])) {

        $post_service = mysqli_real_escape_string($db, $_POST['service']);
        $post_category = mysqli_real_escape_string($db, $_POST['category']);
        $post_quantity = mysqli_real_escape_string($db, $_POST['quantity']);
        $post_addcomment = mysqli_real_escape_string($db, $_POST['comment']);
        $post_lenght = mysqli_real_escape_string($db, $_POST['lenght']);
        $post_character = mysqli_real_escape_string($db, $_POST['character']);


        //$char_length = '6';
        $code = random_number(15);
        $commnt = rand(100, 999) . "-" . date("m.d.y") . "-" . $post_addcomment;

        $char_length = mt_rand(8, 15);
        for ($n = 1; $n <= $post_quantity; $n++) {
            if ($post_character == "lower1") {
                $kodvoc[$n] = randLC($post_lenght);
            } elseif ($post_character == "upper1") {
                $kodvoc[$n] = randUC($post_lenght);
            } elseif ($post_character == "upplow1") {
                $kodvoc[$n] = randULC($post_lenght);
            } elseif ($post_character == "mix") {
                $kodvoc[$n] = randNLC($post_lenght);
            } elseif ($post_character == "mix1") {
                $kodvoc[$n] = randNUC($post_lenght);
            } elseif ($post_character == "mix2") {
                $kodvoc[$n] = randNULC($post_lenght);
            }

            $check_service = mysqli_query($db, "SELECT * FROM services WHERE id = '$post_service' AND status = 'Active'");
            $data_service = mysqli_fetch_assoc($check_service);
            $check_orders = mysqli_query($db, "SELECT * FROM orders WHERE username = '$post_username'");
            $data_orders = mysqli_fetch_assoc($check_orders);

            $price_ori = $data_service['price'];

            $oid = random_number(3) . random_number(4);
            $service = $data_service['service'];
            $iphost = $data_service['ip'];
            $username_router = $data_service['username_mikrotik'];
            $pass_router = $data_service['password_mikrotik'];
            $password_router = decrypt($pass_router);
            $provider = $data_service['provider'];
            $pid = $data_service['pid'];
            $uptime = $data_service['uptime'];

            $masa = $data_service['masa'];
            date_default_timezone_set('Asia/Jakarta');
            $tanggalnya = date('Y-m-d H:i:s');
            $datepx = date_create($tanggalnya);
            date_add($datepx, date_interval_create_from_date_string('' . $masa . ' hours'));

            if (empty($post_service)) {
                $msg_type = "error";
                $msg_content = "<b>Gagal:</b> Mohon mengisi input.";
            } else if (mysqli_num_rows($check_service) == 0) {
                $msg_type = "error";
                $msg_content = "<b>Gagal:</b> Layanan tidak ditemukan.";
            } else {
                $poid = $oid;

                if (empty($poid)) {
                    $msg_type = "error";
                    $msg_content = "<b>Gagal:</b> Server Rusak.";
                } else {
                    $API = new RouterosAPI();
                    $API->debug = false;
                    $iphost_mikrotik    = $iphost;
                    $username_mikrotik  = $username_router;
                    $password_mikrotik  = $password_router;

                    if ($API->connect($iphost_mikrotik, $username_mikrotik, $password_mikrotik)) {

                        $gen_user = '';
                        $gen_pwd = '';
                        //for($i = 0; $i < $char_length; $i++){
                        $gen_user .= $kodvoc[rand(0, strlen($char_lenght) - 1)];
                        $gen_pwd = $gen_user;
                        // }
                        $user = $gen_user;
                        $pass = $gen_pwd;
                        $voucher = $pass;
                        $API->comm("/ip/hotspot/user/add", array(
                            "server"    => "hotspot1",
                            "name"         => $kodvoc[$n],
                            "password"    =>  $kodvoc[$n],
                            "profile"     => $service,
                            "limit-uptime" => $uptime,
                            "comment"        => $commnt
                        ));
                    } else {
                        $msg_type = "error";
                        $msg_content = "<b>Error:</b> Router not connected.";
                    }
                    //}


                    // for($n = 1; $n < $post_quantity; $n++ ){

                    $insert_order = mysqli_query($db, "INSERT INTO orders (code, oid, poid, user, service, voucher, price, date, time, comment) VALUES ('$code','$oid', '$poid', '$sess_username', '$service', '$kodvoc[$n]','$price_ori', '$date', '$time','$commnt')");
                    if ($insert_order == TRUE) {
                        $msg_type = "success";
                        $msg_content = "<b>Berhasil generate voucher.</b><br /><b>Layanan:</b> $service<br />";
                    } else {
                        $msg_type = "error";
                        $msg_content = "<b>Gagal:</b> Error system (2).";
                    }

                    //}
                }
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
                <h1>Generate Voucher</h1>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="text-danger"><i class="fas fa-random"></i> Generate Voucher</h4>
                            </div>
                            <div class="card-body">
                                <?php
                                if ($msg_type == "success") {
                                ?>
                                    <div class="alert alert-primary">
                                        <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                                        <i class="fa fa-check-circle"></i>
                                        <?php echo $msg_content; ?>
                                        <br>
                                        <a href="<?php echo $cfg_baseurl; ?>voucher/list"><label class="btn btn-success btn-xs dim" ; ?> Cek Detail Voucher</label></a>
                                    </div>
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
                                <label class="col-md-2 control-label">Layanan</label>
                                <div class="col-md-12">
                                    <select class="form-control" id="category" name="category">
                                        <option value="0">Pilih salah satu...</option>
                                        <?php
                                        $check_cat = mysqli_query($db, "SELECT * FROM service_cat WHERE code = 'Router' ORDER BY name ASC");
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
                                <label class="col-md-2 control-label">Paket</label>
                                <div class="col-md-12">

                                    <select class="form-control" name="service" id="service">
                                        <option value="0">Pilih Layanan...</option>
                                    </select>

                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Generate Voucher</label>
                                <div class="col-md-12">
                                    <input type="number" name="quantity" class="form-control" placeholder="Masukan Jumlah yang ingin di buat">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Panjang tiap voucher</label>
                                <div class="col-md-12">
                                    <select name="lenght" class="form-control" required>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>

                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Karakter</label>
                                <div class="col-md-12">
                                    <select name="character" class="form-control" required>
                                        <option value="lower1">Mix abcd2345</option>
                                        <option value="upper1">Mix ABCD2345</option>
                                        <option value="upplow1">Mix aBcD2345</option>
                                        <option value="mix">Mix 5ab2c34d</option>
                                        <option value="mix1">Mix 5AB2C34D</option>
                                        <option value="mix2">Mix 5aB2c34D</option>


                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Comment</label>
                                <div class="col-md-12">
                                    <input type="text" name="comment" class="form-control" placeholder="Masukan Comment">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-offset-2 col-md-10">
                                    <button type="submit" class="btn btn-primary" name="generate"><i class="fa fa-paper-plane"></i> Generate Voucher </button>
                                </div>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </div>
    <script type="text/javascript" src="../assets/js/jquery-1.10.2.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $("#category").change(function() {
                var category = $("#category").val();
                $.ajax({
                    url: '<?php echo $cfg_baseurl; ?>inc/generate_service.php',
                    data: 'category=' + category,
                    type: 'POST',
                    dataType: 'html',
                    success: function(msg) {
                        $("#service").html(msg);
                    }
                });
            });
            $("#service").change(function() {
                var service = $("#service").val();
                $.ajax({
                    url: '<?php echo $cfg_baseurl; ?>admin/inc/order_voucher_note.php',
                    data: 'service=' + service,
                    type: 'POST',
                    dataType: 'html',
                    success: function(msg) {
                        $("#note").html(msg);
                    }
                });
            });
        });
    </script>

<?php
    include("../lib/footer.php");
} else {
    header("Location: " . $cfg_baseurl);
}
?>