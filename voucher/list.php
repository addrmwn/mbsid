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
                                <div class="form-group">
                                    <select class="form-control" id="comment" name="comment">

                                        <option value="">Pilih Voucher</option>
                                        <?php

                                        $check_voucher = mysqli_query($db, "SELECT * FROM orders GROUP By 'comment'");

                                        while ($data_voucher = mysqli_fetch_assoc($check_voucher)) {

                                        ?>
                                            <option value="<?php echo $data_voucher['comment']; ?>"><?php echo $data_voucher['comment']; ?></option>

                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <script>
                                    function printV(a, b) {
                                        var comm = document.getElementById('comment').value;
                                        var url = "print.php?id=" + comm + "&" + a + "=" + b + "";
                                        if (comm === "") {
                                            alert('Silakan pilih salah satu Comment terlebih dulu!');
                                        } else {
                                            var win = window.open(url, '_blank');
                                            win.focus();
                                        }
                                    }
                                </script>
                                <script>
                                    function printsmall(a, b) {
                                        var comm = document.getElementById('comment').value;
                                        var url = "print_small.php?id=" + comm + "&" + a + "=" + b + "";
                                        if (comm === "") {
                                            alert('Silakan pilih salah satu Comment terlebih dulu!');
                                        } else {
                                            var win = window.open(url, '_blank');
                                            win.focus();
                                        }
                                    }
                                </script>
                                <script>
                                    function lihat_data() {
                                        var comm = document.getElementById('comment').value;
                                        var url = "cek_data.php?id=" + comm + "";
                                        if (comm === "") {
                                            alert('Silakan pilih salah satu Comment terlebih dulu!');
                                        } else {
                                            var win = window.open(url, '_blank');
                                            win.focus();
                                        }
                                    }
                                </script>
                                <button class="btn btn-primary btn-xs dim" title='Print' onclick="printV('qr','yes');"><i class="fa fa-print"></i> Default</button>
                                <button class="btn btn-primary btn-xs dim" title='Print' onclick="printsmall('small','yes');"><i class="fa fa-print"></i> Small</button>
                                <button class="btn btn-primary btn-xs dim" title='lihat data' onclick="lihat_data();"><i class="fa fa-list"></i> Cek data by comment</button>
                                <br>
                                <br>
                                <hr>
                                <div class="table-responsive">
                                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>ID Pesanan</th>
                                                <th>Paket</th>
                                                <th>Harga</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php

                                            $new_query = $db->query("SELECT * FROM orders WHERE user = '$sess_username' ORDER BY id DESC"); // edit
                                            // end paging config
                                            while ($data_order = mysqli_fetch_assoc($new_query)) {

                                            ?>
                                                <tr class="odd gradeX">
                                                    <td><a href="<?php echo $cfg_baseurl; ?>voucher/detail.php?oid=<?php echo $data_order['oid']; ?>" class="btn btn-primary btn-xs dim" title="Detail"><i class="fa fa-eye"></i> Detail</a></td>
                                                    <td><?php echo $data_order['oid']; ?></td>
                                                    <td><?php echo $data_order['service']; ?></td>
                                                    <td>Rp <?php echo number_format($data_order['price'], 0, ',', '.'); ?></td>
                                                </tr>
                                            <?php
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
} else {
    header("Location: " . $cfg_baseurl);
}
?>