<?php
session_start();
require("../mainconfig.php");

if (isset($_SESSION['user'])) {
    $sess_username = $_SESSION['user']['username'];
    $check_user = mysqli_query($db, "SELECT * FROM users WHERE username = '$sess_username'");
    $data_user = mysqli_fetch_assoc($check_user);
    if (mysqli_num_rows($check_user) == 0) {
        header("Location: " . $cfg_baseurl . "user/logout.php");
    } else if ($data_user['status'] == "Suspended") {
        header("Location: " . $cfg_baseurl . "user/logout.php");
    } else {
        if (isset($_GET['oid'])) {
            $post_oid = mysqli_real_escape_string($db, $_GET['oid']);
            $checkdb_order = mysqli_query($db, "SELECT * FROM orders WHERE oid = '$post_oid'");
            $datadb_order = mysqli_fetch_assoc($checkdb_order);

            if ($datadb_order['status_voucher'] == "Sudah Digunakan") {
                $label = "success";
            } else if ($datadb_order['status_voucher'] == "Belum Digunakan") {
                $label = "warning";
            }

            if (mysqli_num_rows($checkdb_order) == 0) {
                header("Location: " . $cfg_baseurl . "voucher/list");
            } else {
                include("../lib/header.php");

?>

                <!-- Main Content -->
                <div class="main-content">
                    <section class="section">
                        <div class="section-header">
                            <h1>Detail Voucher</h1>
                        </div>
                        <div class="section-body">
                            <div class="row">
                                <div class="col-xl-12 col-lg-7">
                                    <div class="alert alert-primary">
                                        <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                                        <i class="fa fa-info-circle"></i>
                                        Klik Kode Voucher, otomatis kode akan ter-copy
                                    </div>
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class="text-danger"><i class="fas fa-list"></i> Detail Voucher</h4>
                                            </div>



                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-bordered" data-filter=#filter>

                                                        <tr>
                                                            <td><b>ID Pesanan</b></td>
                                                            <td><code><?php echo $datadb_order['oid']; ?></code></td>
                                                        </tr>
                                                        <tr>
                                                            <td><b>Paket</b></td>
                                                            <td><?php echo $datadb_order['service']; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><b>Kode Voucher</b></td>
                                                            <td><button class="btn btn-primary" name="voucher" id="voucher" onclick="copy_to_clipboard('voucher')"><?php echo $datadb_order['voucher']; ?> <i class="fa fa-copy"></i></button></td>
                                                            <input type="text" style="display:none" name="voucher" id="voucher" value="<?php echo $datadb_order['voucher']; ?>">
                                                        </tr>
                                                        <tr>
                                                            <td><b>Harga</b></td>
                                                            <td>Rp <?php echo number_format($datadb_order['price'], 0, ',', '.'); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><b>Status Voucher</b></td>
                                                            <td><label class="btn btn-<?php echo $label; ?>"><?php echo $datadb_order['status_voucher']; ?></label></td>
                                                        </tr>
                                                        <tr>
                                                            <td><b>Tanggal Pembelian</b></td>
                                                            <td><?php echo $datadb_order['date']; ?> <?php echo $datadb_order['time']; ?></td>
                                                        </tr>
                                                    </table>
                                                    <a href="<?php echo $cfg_baseurl; ?>voucher/list" class="btn btn-success m-b-10"><i class="fa fa-arrow-left"></i> Kembali</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </section>
                </div>

                <script type="text/javascript">
                    function copy_to_clipboard(data) {
                        document.getElementById(data).style = "display:block";
                        var copyText = document.getElementById(data);
                        copyText.select();
                        copyText.setSelectionRange(0, 99999);
                        document.execCommand("copy");
                        document.getElementById(data).style = "display:none";
                        alert('Kode voucher berhasil di salin.');
                    }
                </script>
<?php
                include("../lib/footer.php");
            }
        } else {
            header("Location: " . $cfg_baseurl . "voucher/list");
        }
    }
} else {
    header("Location: " . $cfg_baseurl);
}
?>