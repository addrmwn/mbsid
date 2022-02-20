<?php
session_start();
require("mainconfig.php");

$msg_type = "nothing";

if (isset($_SESSION['user'])) {
    $sess_username = $_SESSION['user']['username'];
    $check_user = mysqli_query($db, "SELECT * FROM users WHERE username = '$sess_username'");
    $data_user = mysqli_fetch_assoc($check_user);
    if (mysqli_num_rows($check_user) == 0) {
        header("Location: " . $cfg_baseurl . "logout.php");
    }
} else {
    header("Location: " . $cfg_baseurl . "auth/login");
}


$queryto = mysqli_query($db, "SELECT * FROM data_router");
$tampilto = mysqli_num_rows($queryto);

// total voucher 

$check_voucher = mysqli_query($db, "SELECT * FROM orders WHERE status_voucher = 'Belum Digunakan'");
$hitung_voucher = mysqli_num_rows($check_voucher);

// report harian 

$report_today = mysqli_query($db, "SELECT SUM(price) AS total FROM report WHERE date = '$date'");
$profit_today = mysqli_fetch_assoc($report_today);


// report bulanan

$report_month = mysqli_query($db, "SELECT SUM(price) AS total FROM report WHERE MONTH(report.date) = '" . date('m') . "' AND YEAR(report.date) = '" . date('Y') . "'");
$profit_month = mysqli_fetch_assoc($report_month);


include("lib/header.php");
?>
<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Dashboard</h1>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="far fa-credit-card"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4><?= $total_voucher; ?></h4>
                        </div>
                        <div class="card-body">
                            <?php echo $hitung_voucher; ?> Voucher
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="fas fa-money-check-alt"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4><?= $purchase_today; ?></h4>
                        </div>
                        <div class="card-body">
                            <?php if ($currency == "Rp") {
                                $income = number_format($profit_today['total'], 0, ',', ',');
                            } else {
                                $income = number_format($profit_today['total'], 0, ',', ',');
                            }
                            ?>
                            <?= $currency; ?> <?= $income; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-money-check-alt"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4><?= $purchase_month; ?></h4>
                        </div>
                        <div class="card-body">
                            <?php if ($currency == "Rp") {
                                $income = number_format($profit_month['total'], 0, ',', ',');
                            } else {
                                $income = number_format($profit_month['total'], 2, '.', ',');
                            }
                            ?>
                            <?= $currency; ?> <?= $income; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="fas fa-server"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Data router</h4>
                        </div>
                        <div class="card-body">
                            <?php echo $tampilto; ?> Router
                        </div>
                    </div>
                </div>
            </div>
        </div>


</div>
</div>
</section>
</div>
<?php
include("lib/footer.php");
?>