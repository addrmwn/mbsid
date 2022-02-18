<?php
session_start();
require("../mainconfig.php");
require("../lib/api.class.php");

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
            $post_sid = mysqli_real_escape_string($db, $_POST['sid']);
            $post_type = mysqli_real_escape_string($db, $_POST['type']);
            $post_cat = mysqli_real_escape_string($db, $_POST['category']);
            $post_service = mysqli_real_escape_string($db, $_POST['service']);
            $post_iphost = mysqli_real_escape_string($db, $_POST['ip']);
            $post_username = mysqli_real_escape_string($db, $_POST['username_mikrotik']);
            $post_password = mysqli_real_escape_string($db, encrypt($_POST['password_mikrotik']));
            $post_price = mysqli_real_escape_string($db, $_POST['price']);
            $post_pid = mysqli_real_escape_string($db, $_POST['pid']);
            $post_provider = mysqli_real_escape_string($db, $_POST['provider']);


            $checkdb_service = mysqli_query($db, "SELECT * FROM services WHERE sid = '$post_sid'");
            $datadb_service = mysqli_fetch_assoc($checkdb_service);
            if (empty($post_sid) || empty($post_service) || empty($post_iphost) || empty($post_username) || empty($post_password) || empty($post_price) || empty($post_pid) || empty($post_provider)) {
                $msg_type = "error";
                $msg_content = "<b>Gagal:</b> Mohon mengisi semua input.";
            } else if (mysqli_num_rows($checkdb_service) > 0) {
                $msg_type = "error";
                $msg_content = "<b>Gagal:</b> Service ID $post_sid sudah terdaftar di database.";
            } else {
                $insert_service = mysqli_query($db, "INSERT INTO services (sid, type, category, service, ip, username_mikrotik, password_mikrotik, price, status, pid, provider) VALUES ('$post_sid', '$post_type', '$post_cat', '$post_service', '$post_iphost', '$post_username', '$post_password','$post_price', 'Active', '$post_pid', '$post_provider')");
                if ($insert_service == TRUE) {
                    $msg_type = "success";
                    $msg_content = "<b>Berhasil:</b> Layanan berhasil ditambah.<br /><b>Service ID:</b> $post_sid<br /><b>Service Name:</b> $post_service<br /><b>Category:</b> $post_cat <br /><b>Harga:</b> Rp " . number_format($post_price, 0, ',', '.') . "<br /><b>Provider ID:</b> $post_pid<br /><b>Provider Code:</b> $post_provider<br /><b>Status:</b> Active";
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
                $update_service = mysqli_query($db, "UPDATE services SET category = '$post_cat', service = '$post_service', ip = '$post_iphost', price = '$post_price', status = '$post_status', pid = '$post_pid', provider = '$post_provider' WHERE sid = '$post_sid'");
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
            $checkdb_service = mysqli_query($db, "SELECT * FROM services WHERE id = '$post_sid'");
            $datadb_service = mysqli_fetch_assoc($checkdb_service);

            $post_iphost = $datadb_service['ip'];
            $post_username = $datadb_service['username_mikrotik'];
            $post_password = $datadb_service['password_mikrotik'];
            $password_decrypt = decrypt($datadb_service['password_mikrotik']);
            $post_service = $datadb_service['service'];


            if (mysqli_num_rows($checkdb_service) == 0) {
                $msg_type = "error";
                $msg_content = "<b>Gagal:</b> Layanan tidak ditemukan.";
            } else {
                $delete_user = mysqli_query($db, "DELETE FROM services WHERE id = '$post_sid'");
                if ($delete_user == TRUE) {
                    $msg_type = "success";
                    $msg_content = "<b>Berhasil:</b> Paket <b>$post_service</b> dihapus.";
                    $API = new RouterosAPI();
                    $API->debug = false;
                    if ($API->connect($post_iphost, $post_username, $password_decrypt)) {
                        $API->comm("/ip/hotspot/user/profile/remove", array(
                            ".id" => "$post_service",
                        ));
                    }
                } else {
                    echo "Router tidak konek";
                }
            }
        }

        include("../lib/header.php");
?>
        <!-- Main Content -->
        <div class="main-content">
            <section class="section">
                <div class="section-header">
                    <h1>Data Profile</h1>
                </div>

                <div class="section-body">
                    <div class="row">

                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="text-danger"><i class="fas fa-list"></i> Daftar Profile</h4>
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
                                    <div class="table-responsive">
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Layanan</th>
                                                    <th>Harga</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // start pagin// start paging config
                                                if (isset($_GET['search']) and isset($_GET['category'])) {
                                                    $search = $_GET['search'];
                                                    $category = $_GET['category'];
                                                    if (!empty($_GET['search']) and !empty($_GET['category'])) {
                                                        $query_list = "SELECT * FROM services WHERE service LIKE '%$search%' OR sid LIKE '%$search%' AND category LIKE '%$category%' ORDER BY id DESC"; // edit
                                                    } else if (empty($_GET['search'])) {
                                                        $query_list = "SELECT * FROM services WHERE category LIKE '%$category%' ORDER BY id DESC"; // edit
                                                    } else if (empty($_GET['category'])) {
                                                        $query_list = "SELECT * FROM services WHERE service LIKE '%$search%' OR sid LIKE '%$search%' ORDER BY id DESC"; // edit
                                                    } else {
                                                        $query_list = "SELECT * FROM services ORDER BY id DESC"; // edit
                                                    }
                                                } else {
                                                    $query_list = "SELECT * FROM services ORDER BY id DESC"; // edit
                                                }
                                                $records_per_page = 3000000000000; // edit

                                                $starting_position = 0;
                                                if (isset($_GET["page_no"])) {
                                                    $starting_position = ($_GET["page_no"] - 1) * $records_per_page;
                                                }
                                                $new_query = $query_list . " LIMIT $starting_position, $records_per_page";
                                                $new_query = mysqli_query($db, $new_query);
                                                $no = 1;
                                                // end paging config
                                                while ($data_show = mysqli_fetch_assoc($new_query)) {
                                                    if ($data_show['status'] == "Full") {
                                                        $label = "danger";
                                                    } else if ($data_show['status'] == "Active") {
                                                        $label = "success";
                                                    } else if ($data_show['status'] == "Not Active") {
                                                        $label = "danger";
                                                    }
                                                ?>
                                                    <tr>
                                                        <td><?php echo $no; ?></td>
                                                        <td><?php echo $data_show['service']; ?></td>
                                                        <td>Rp <?php echo number_format($data_show['price']); ?></td>
                                                        <td><label class="btn btn-<?php echo $label; ?>"><?php echo $data_show['status']; ?></label></td>
                                                        <td align="center">
                                                            <form action="<?php echo $_SERVER['PHP_SELF']; ?>?service_id=<?php echo $data_show['id']; ?>" class="form-inline" role="form" method="POST">
                                                                <button type="submit" name="delete" class="btn btn-sm btn-danger"><i class="fa fa-trash" title="Hapus"></i></button>
                                                        </td>
                                                        </form>
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
        include("../lib/footer.php");
    }
} else {
    header("Location: " . $cfg_baseurl);
}
?>