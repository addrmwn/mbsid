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
        if (isset($_POST['add'])) {
            $post_cat = mysqli_real_escape_string($db, $_POST['category_router']);
            $post_nama_server = mysqli_real_escape_string($db, $_POST['nama_server']);
            $post_iphost = mysqli_real_escape_string($db, $_POST['ip_server']);
            $post_username = mysqli_real_escape_string($db, $_POST['username_mikrotik']);
            $post_password = mysqli_real_escape_string($db, encrypt($_POST['password_mikrotik']));
            $post_dns = mysqli_real_escape_string($db, $_POST['dns']);

            $checkdb_service = mysqli_query($db, "SELECT * FROM data_router WHERE sid = '$post_sid'");
            $datadb_service = mysqli_fetch_assoc($checkdb_service);
            if (empty($post_nama_server) || empty($post_iphost) || empty($post_username) || empty($post_password)) {
                $msg_type = "error";
                $msg_content = "<b>Gagal:</b> Mohon mengisi semua input.";
            } else if (mysqli_num_rows($checkdb_service) > 0) {
                $msg_type = "error";
                $msg_content = "<b>Gagal:</b> Service ID $post_sid sudah terdaftar di database.";
            } else {
                $insert_service = mysqli_query($db, "INSERT INTO data_router (category, nama_server, dns, ip_server, username_mikrotik, password_mikrotik, status) VALUES ('$post_cat','$post_nama_server', '$post_dns','$post_iphost', '$post_username', '$post_password', 'Active' )");
                if ($insert_service == TRUE) {
                    $msg_type = "success";
                    $msg_content = "<b>Berhasil:</b> Data Router Berhasil Di Tambah";
                } else {
                    $msg_type = "error";
                    $msg_content = "<b>Gagal:</b> Error system.";
                }
            }
        } else if (isset($_POST['edit'])) {
            $post_sid = mysqli_real_escape_string($db, $_GET['service_id']);
            $post_cat = mysqli_real_escape_string($db, $_POST['category']);
            $post_service = mysqli_real_escape_string($db, $_POST['service']);
            $post_iphost = mysqli_real_escape_string($db, $_POST['ip']);
            $post_price = mysqli_real_escape_string($db, $_POST['price']);
            $post_pid = mysqli_real_escape_string($db, $_POST['pid']);
            $post_provider = mysqli_real_escape_string($db, $_POST['provider']);
            $post_status = mysqli_real_escape_string($db, $_POST['status']);
            if (empty($post_service) || empty($post_price) || empty($post_pid) || empty($post_provider)) {
                $msg_type = "error";
                $msg_content = "<b>Gagal:</b> Mohon mengisi input.";
            } else if ($post_status != "Active" and $post_status != "Not active") {
                $msg_type = "error";
                $msg_content = "<b>Gagal:</b> Input tidak sesuai.";
            } else {
                $update_service = mysqli_query($db, "UPDATE data_router SET category = '$post_cat', service = '$post_service', ip = '$post_iphost', price = '$post_price', status = '$post_status', pid = '$post_pid', provider = '$post_provider' WHERE sid = '$post_sid'");
                if ($update_service == TRUE) {
                    $msg_type = "success";
                    $msg_content = "<b>Berhasil:</b> Layanan berhasil diubah.<br /><b>Service ID:</b> $post_sid<br /><b>Service Name:</b> $post_service<br /><b>Category:</b> $post_cat <br /><b>Harga:</b> Rp " . number_format($post_price, 0, ',', '.') . "<br /><b>Provider ID:</b> $post_pid<br /><b>Provider Code:</b> $post_provider<br /><b>Status:</b> $post_status";
                } else {
                    $msg_type = "error";
                    $msg_content = "<b>Gagal:</b> Error system.";
                }
            }
        } else if (isset($_POST['delete'])) {
            $post_sid = mysqli_real_escape_string($db, $_GET['service_id']);
            $checkdb_service = mysqli_query($db, "SELECT * FROM data_router WHERE sid = '$post_sid'");
            $nama_server = $checkdb_service['name_server'];
            if (mysqli_num_rows($checkdb_service) == 0) {
                $msg_type = "error";
                $msg_content = "<b>Gagal:</b> Router tidak ditemukan.";
            } else {
                $delete_user = mysqli_query($db, "DELETE FROM data_router WHERE sid = '$post_sid'");
                if ($delete_user == TRUE) {
                    $msg_type = "success";
                    $msg_content = "<b>Berhasil:</b> Router <b>$nama_server</b> dihapus.";
                }
            }
        }

        include("../lib/header.php");
?>
        <!-- Main Content -->
        <div class="main-content">
            <section class="section">
                <div class="section-header">
                    <h1>Tambah Data router</h1>
                </div>

                <div class="section-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="text-danger"><i class="fas fa-server"></i> Tambah Router</h4>
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
                                            <label class="col-md-5 control-label">Data Router</label>
                                            <div class="col-md-12">
                                                <select class="form-control" id="category_router" name="category_router">
                                                    <option value="0">Pilih salah satu...</option>
                                                    <?php
                                                    $check_cat = mysqli_query($db, "SELECT * FROM data_router_cat WHERE code = 'Router' ORDER BY name ASC");
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
                                            <label class="col-md-5 control-label">Nama Mikrotik</label>
                                            <div class="col-md-12">
                                                <input type="text" name="nama_server" class="form-control" placeholder="Example : RB750GR3">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-5 control-label">DNS Name</label>
                                            <div class="col-md-12">
                                                <input type="text" name="dns" class="form-control" placeholder="Example : Mikrobill.net">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-5 control-label">IP Server</label>
                                            <div class="col-md-12">
                                                <input type="text" name="ip_server" class="form-control" placeholder="Example : 10.10.10.1">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Username Mikrotik</label>
                                            <div class="col-md-12">
                                                <input type="text" name="username_mikrotik" class="form-control" placeholder="Username Mikrotik">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Password Mikrotik</label>
                                            <div class="col-md-12">
                                                <input type="password" name="password_mikrotik" class="form-control" placeholder="Password Mikrotik">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-offset-2 col-md-10">
                                                <a href="<?php $cfg_baseurl; ?>data_router" class="btn btn-danger"><i class="fas fa-arrow-alt-circle-left"></i> Kembali</a>
                                                <button type="submit" class="btn btn-primary" name="add"><i class="fa fa-paper-plane"></i> Submit </button>
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