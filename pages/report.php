<?php
session_start();
require("../mainconfig.php");

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
        include("../lib/header.php");

        $report_month = mysqli_query($db, "SELECT SUM(price) AS total FROM report WHERE MONTH(report.date) = '" . date('m') . "' AND YEAR(report.date) = '" . date('Y') . "'");
        $profit_month = mysqli_fetch_assoc($report_month);

        $check_data = mysqli_query($db, "SELECT * FROM report");
        $data_report = mysqli_fetch_assoc($check_data);

        $qry = mysqli_query($db, "SELECT SUM(price) AS total FROM report WHERE MONTH(report.date) = '" . date('m') . "' AND YEAR(report.date) = '" . date('Y') . "'");
        $row2 = mysqli_fetch_assoc($qry);

        $total = $row2['total'];


?>

        <!-- Main Content -->
        <div class="main-content">
            <section class="section">
                <div class="section-header">
                    <h1>Report</h1>
                </div>
                <div class="section-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="text-danger"><i class="fas fa-credit-card"></i> Report</h4>
                                </div>
                                <?php
                                if (isset($_POST['submit'])) {
                                    $bln = date($_POST['bulan']);
                                    $thn = date($_POST['tahun']);
                                    if (!empty($bln)) {
                                        // perintah tampil data berdasarkan periode bulan
                                        $q = mysqli_query($db, "SELECT * FROM report WHERE MONTH(report.date) ='$bln' AND YEAR(report.date) = '$thn'");
                                    } else {
                                        // perintah tampil semua data
                                        $q = mysqli_query($db, "SELECT * FROM report Order By id DESC");
                                    }
                                } else {
                                    // perintah tampil semua data
                                    $q = mysqli_query($db, "SELECT * FROM report Order By id DESC");
                                }



                                // hitung jumlah baris data
                                $s = $q->num_rows;
                                ?>
                                <div class="card-body">
                                    <form method="POST" action="" class="form-inline">
                                        <label class="col-md-2 control-label">Tampilkan Data</label>
                                        <select class="form-control mr-2" name="bulan">
                                            <option value="1">Januari</option>
                                            <option value="2">Februari</option>
                                            <option value="3">Maret</option>
                                            <option value="4">April</option>
                                            <option value="5">Mei</option>
                                            <option value="6">Juni</option>
                                            <option value="7">Juli</option>
                                            <option value="8">Agustus</option>
                                            <option value="9">September</option>
                                            <option value="10">Oktober</option>
                                            <option value="11">November</option>
                                            <option value="12">Desember</option>
                                        </select>
                                        <select class="form-control mr-2" name="tahun">
                                            <?php

                                            $qry = mysqli_query($db, "SELECT * FROM report GROUP BY year(date)");
                                            while ($t = mysqli_Fetch_array($qry)) {
                                                $data = explode('-', $t['date']);
                                                $tahun = $data[0];
                                                echo "<option value='$tahun'>$tahun</option>";
                                            }
                                            echo "";
                                            ?>
                                        </select>
                                        <button type="submit" name="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tampilkan</button>
                                    </form>
                                    <br>
                                    <br>

                                    <div class="table-responsive">
                                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Voucher</th>
                                                    <th>Paket</th>
                                                    <th>Harga</th>
                                                    <th>Tanggal</th>
                                                </tr>
                                            <tbody>

                                                <?php

                                                $no = 1;
                                                while ($r = $q->fetch_assoc()) {

                                                    $tanggal = date('Y-m-d', strtotime($r['date']));
                                                    $ambil_bulan = bulan_indo($tanggal);
                                                    $profit_ambil = mysqli_query($db, "SELECT SUM(price) AS total FROM report WHERE MONTH(report.date) = '" . date('m') . "' AND YEAR(report.date) = '" . date('Y') . "'");
                                                    $data_profit = mysqli_fetch_assoc($profit_ambil);
                                                    $ambil_profit = $data_profit['total'];


                                                    $all_profit = mysqli_query($db, "SELECT SUM(price) AS total FROM report");
                                                    $data_all = mysqli_fetch_assoc($all_profit);
                                                    $ambil_all = $data_all['total'];

                                                    $prof = mysqli_query($db, "SELECT SUM(price) AS total FROM report WHERE MONTH(report.date) ='$bln' AND YEAR(report.date) = '$thn'");
                                                    $prof_data = mysqli_fetch_assoc($prof);
                                                    $take_profit = $prof_data['total'];
                                                ?>

                                                    <tr>
                                                        <td><?= $no++ ?></td>
                                                        <td><?= $r['voucher'] ?></td>
                                                        <td><?= $r['service'] ?></td>
                                                        <td>Rp <?= number_format($r['price']); ?></td>
                                                        <td><?= tanggal_indo($r['date']) ?></td>
                                                    </tr>

                                                <?php
                                                }
                                                ?>

                                        </table>
                                    </div>
                                    <center>
                                        <?php
                                        if (!empty($bln)) {
                                            $text = number_format($take_profit);
                                            $h5 = "Total Pendapatan Bulan $ambil_bulan";
                                        } else {
                                            $text =  number_format($ambil_all);
                                            $h5 = "Total Semua Pendapatan";
                                        }
                                        ?>
                                        <h5><?= $h5; ?><br>
                                            Rp <?php echo $text; ?>
                                    </center>
                                    </h5>
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
