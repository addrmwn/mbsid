<?php
session_start();
require("../mainconfig.php");
$page_type = "sosmed";

if (isset($_SESSION['user'])) {
    $sess_username = $_SESSION['user']['username'];
    $check_user = mysqli_query($db, "SELECT * FROM users WHERE username = '$sess_username'");
    $data_user = mysqli_fetch_assoc($check_user);
    if (mysqli_num_rows($check_user) == 0) {
        header("Location: " . $cfg_baseurl . "user/logout.php");
    } else if ($data_user['status'] == "Suspended") {
        header("Location: " . $cfg_baseurl . "user/logout.php");
    }
}


$msg_type = "nothing";


if (isset($_GET['id'])) {
    $post_comment = mysqli_real_escape_string($db, $_GET['id']);
    $check_cat = mysqli_query($db, "SELECT * FROM orders WHERE comment = '$post_comment'");




    if (isset($_POST['delbycomm'])) {

        include "../lib/api.class.php";


        $all_data = mysqli_num_rows($check_cat);

        $voucher_check = mysqli_fetch_assoc($check_cat);

        $voucherin = $voucher_check['voucher'];


        $check_voucher = mysqli_query($db, "SELECT * FROM orders WHERE comment = '$post_comment'");
        $data_voucher = mysqli_fetch_assoc($check_voucher);

        $service_voucher = $data_voucher['service'];

        $check_service = mysqli_query($db, "SELECT * FROM services WHERE service = '$service_voucher' ");
        $data_service = mysqli_fetch_assoc($check_service);

        $ip = $data_service['ip'];
        $username_router = $data_service['username_mikrotik'];
        $password_router = decrypt($data_service['password_mikrotik']);

        $API = new RouterosAPI();
        $API->debug = false;
        $iphost_mikrotik    = $ip;
        $username_mikrotik  = $username_router;
        $password_mikrotik  = $password_router;
        if ($API->connect($iphost_mikrotik, $username_mikrotik, $password_mikrotik)) {

            $list_voucher = $voucherin;

            $getuser = $API->comm("/ip/hotspot/user/print", array(
                "?comment" => "$post_comment",
                "?uptime" => "00:00:00",
            ));

            for ($i = 0; $i < $all_data; $i++) {
                $usersdetails = $getuser[$i];
                $uid = $usersdetails['.id'];

                $API->comm("/ip/hotspot/user/remove", array(
                    ".id" => "$uid",
                ));
                $delete_data_db = mysqli_query($db, "DELETE FROM orders WHERE comment = '$post_comment'");

                $msg_type = "success";
                $msg_content = "Delete Voucher by comment berhasil";
            }
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
                <h1>List Voucher</h1>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="text-danger"><i class="fas fa-history"></i> List Voucher</h4>
                            </div>
                            <div class="card-body">
                                <center>
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

                                        <button type="submit" class="btn btn-danger btn-xs dim" name="delbycomm"><i class="fa fa-trash"></i> Delete semua voucher untuk comment tersebut</button>
                                    </form>
                                </center>
                                <div class="table-responsive">
                                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Voucher</th>
                                                <th>Paket</th>
                                                <th>Comment</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 1;

                                            while ($data_cat = mysqli_fetch_assoc($check_cat)) {

                                                $check_service = $data_cat['service'];

                                            ?>
                                                <tr>
                                                    <td><?php echo $no; ?></td>
                                                    <td><?php echo $data_cat['voucher']; ?></td>
                                                    <td><?php echo $data_cat['service']; ?></td>
                                                    <td><?php echo $data_cat['comment']; ?></td>
                                                </tr>

                                            <?php
                                                $no++;
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

<?php
} else {
    header("Location: " . $cfg_baseurl . "");
}
include("../lib/footer.php");

?>