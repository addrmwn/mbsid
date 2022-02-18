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
        if (isset($_POST['delete'])) {
            $post_sid = mysqli_real_escape_string($db, $_GET['service_id']);
            $checkdb_service = mysqli_query($db, "SELECT * FROM data_router WHERE id = '$post_sid'");
            $datadb_service = mysqli_fetch_assoc($checkdb_service);
            $name_server = $datadb_service['nama_server'];
            if (mysqli_num_rows($checkdb_service) == 0) {
                $msg_type = "error";
                $msg_content = "<b>Gagal:</b> Router tidak ditemukan.";
            } else {
                $delete_user = mysqli_query($db, "DELETE FROM data_router WHERE id = '$post_sid'");
                if ($delete_user == TRUE) {
                    $msg_type = "success";
                    $msg_content = "<b>Berhasil:</b> Router <b>$name_server</b> dihapus.";
                }
            }
        }

        include("../lib/header.php");
?>
        <!-- Main Content -->
        <div class="main-content">
            <section class="section">
                <div class="section-header">
                    <h1>Data Router</h1>
                </div>

                <div class="section-body">
                    <div class="row">

                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="text-danger"><i class="fas fa-list"></i> Daftar Router</h4>
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
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <a href="<?php $cfg_baseurl; ?>add_router" class="btn btn-lg btn-primary mb-3 font-weight-bold"><i class="fas fa-server"></i> Tambah data router</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Server</th>
                                                    <th>IP Server</th>
                                                    <th>Username Mikrotik</th>
                                                    <th>Password Mikrotik</th>
                                                    <th>Dns Name </th>
                                                    <th>Status</th>
                                                    <th>Aksi Edit</th>
                                                    <th>Aksi Delete</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // start paging config
                                                if (isset($_GET['search']) and isset($_GET['category'])) {
                                                    $search = $_GET['search'];
                                                    $category = $_GET['category'];
                                                    if (!empty($_GET['search']) and !empty($_GET['category'])) {
                                                        $query_list = "SELECT * FROM data_router WHERE service LIKE '%$search%' OR sid LIKE '%$search%' AND category LIKE '%$category%' ORDER BY id DESC"; // edit
                                                    } else if (empty($_GET['search'])) {
                                                        $query_list = "SELECT * FROM data_router WHERE category LIKE '%$category%' ORDER BY id DESC"; // edit
                                                    } else if (empty($_GET['category'])) {
                                                        $query_list = "SELECT * FROM data_router WHERE service LIKE '%$search%' OR sid LIKE '%$search%' ORDER BY id DESC"; // edit
                                                    } else {
                                                        $query_list = "SELECT * FROM data_router ORDER BY id DESC"; // edit
                                                    }
                                                } else {
                                                    $query_list = "SELECT * FROM data_router ORDER BY id DESC"; // edit
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
                                                        <td><?php echo $data_show['nama_server']; ?></td>
                                                        <td><?php echo $data_show['ip_server']; ?></td>
                                                        <td><?php echo $data_show['username_mikrotik']; ?></td>
                                                        <td><?php echo decrypt($data_show['password_mikrotik']); ?></td>
                                                        <td><?php echo $data_show['dns']; ?></td>

                                                        <td><label class="btn btn-<?php echo $label; ?>"><?php echo $data_show['status']; ?></label></td>

                                                        <td align="center">
                                                            <button class="btn btn-sm btn-warning" data-toggle='modal' data-target='#show' data-id="<?= $data_show['sid'] ?>"><i class="fa fa-edit" title="Edit"></i></button>
                                                        </td>
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