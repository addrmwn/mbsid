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
            $post_id = mysqli_real_escape_string($db, $_POST['id']);
            $post_name = mysqli_real_escape_string($db, $_POST['name']);
            $post_category = mysqli_real_escape_string($db, $_POST['category']);

            $checkdb_service = mysqli_query($db, "SELECT * FROM data_router_cat WHERE id = '$post_id'");
            $datadb_service = mysqli_fetch_assoc($checkdb_service);
            if (empty($post_id) || empty($post_name) || empty($post_category)) {
                $msg_type = "error";
                $msg_content = "<b>Failed:</b> Please fill all inputs.";
            } else if (mysqli_num_rows($checkdb_service) > 0) {
                $msg_type = "error";
                $msg_content = "<b>Failed:</b> Category ID $post_id already exist in database.";
            } else {
                $insert_provider = mysqli_query($db, "INSERT INTO data_router_cat (id, name, code, category) VALUES ('$post_id', '$post_name', 'Router', '$post_category')");
                if ($insert_provider == TRUE) {
                    $msg_type = "success";
                    $msg_content = "<b>Success:</b> Service category successfully added.<br /><b>Provider ID:</b> $post_id<br /><b>Name:</b>$post_name";
                } else {
                    $msg_type = "error";
                    $msg_content = "<b>Failed:</b> System Error.";
                }
            }
        } else if (isset($_POST['delete'])) {
            $post_id = mysqli_real_escape_string($db, $_GET['servicecat_id']);
            $checkdb_news = mysqli_query($db, "SELECT * FROM data_router_cat WHERE id = '$post_id'");
            if (mysqli_num_rows($checkdb_news) == 0) {
                $msg_type = "error";
                $msg_content = "<b>Gagal:</b> Kategori tidak ditemukan.";
            } else {
                $delete_news = mysqli_query($db, "DELETE FROM data_router_cat WHERE id = '$post_id'");
                if ($delete_news == TRUE) {
                    $msg_type = "success";
                    $msg_content = "<b>Berhasil:</b> Kategori dihapus.";
                }
            }
        }

        include("../lib/header.php");

?>
        <!-- Main Content -->
        <div class="main-content">
            <section class="section">
                <div class="section-header">
                    <h1><?= $category; ?>
                    </h1>
                </div>

                <div class="section-body">
                    <div class="row">

                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="text-danger"><i class="fas fa-list"></i> <?= $category_list; ?></h4>
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
                                                    <a href="<?php $cfg_baseurl; ?>add_kat" class="btn btn-lg btn-primary mb-3 font-weight-bold"><i class="fas fa-plus-circle"></i> <?= $add_category; ?></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th><?= $name_server; ?></th>
                                                    <th><?= $code; ?></th>
                                                    <th><?= $action; ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // start paging config
                                                $query_list = "SELECT * FROM data_router_cat ORDER BY id ASC"; // edit
                                                $records_per_page = 1000; // edit

                                                $starting_position = 0;
                                                if (isset($_GET["page_no"])) {
                                                    $starting_position = ($_GET["page_no"] - 1) * $records_per_page;
                                                }
                                                $new_query = $query_list . " LIMIT $starting_position, $records_per_page";
                                                $new_query = mysqli_query($db, $new_query);
                                                // end paging config
                                                $no = 1;
                                                while ($data_show = mysqli_fetch_assoc($new_query)) {
                                                ?>
                                                    <tr>
                                                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>?servicecat_id=<?php echo $data_show['id']; ?>" class="form-inline" role="form" method="POST">
                                                            <td><?php echo $no; ?></td>
                                                            <td><?php echo $data_show['name']; ?></td>
                                                            <td><?php echo $data_show['category']; ?></td>
                                                            <td>
                                                                <button type="submit" name="delete" class="btn btn-xs btn-danger"><i class="fa fa-trash" title="Delete"></i> <?= $delete; ?></button>
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