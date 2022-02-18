<?php
session_start();
require("../mainconfig.php");
$page_type = "sosmed";

if (isset($_SESSION['user'])) {
    $sess_username = $_SESSION['user']['username'];
    $check_user = mysqli_query($db, "SELECT * FROM users WHERE username = '$sess_username'");
    $data_user = mysqli_fetch_assoc($check_user);
    if (mysqli_num_rows($check_user) == 0) {
        header("Location: " . $cfg_baseurl . "auth/logout.php");
    } else if ($data_user['status'] == "Suspended") {
        header("Location: " . $cfg_baseurl . "auth/logout.php");
    }
}


$msg_type = "nothing";


if ($input_comment = $_GET['id']) {
    $check_cat = mysqli_query($db, "SELECT * FROM orders WHERE user = '$sess_username' AND comment = '$input_comment'");
} else {
    header("Location: " . $cfg_baseurl . "");
}



?>
<!DOCTYPE html>
<html>

<head>
    <title>Voucher-<?= $hotspotname . "-" . $getuprofile . "-" . $id; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="pragma" content="no-cache" />
    <link rel="icon" href="../img/favicon.png" />
    <script src="../js/qrious.min.js"></script>
    <style>
        body {
            color: #000000;
            background-color: #FFFFFF;
            font-size: 14px;
            font-family: 'Helvetica', arial, sans-serif;
            margin: 0px;
            -webkit-print-color-adjust: exact;
        }

        table.voucher {
            display: inline-block;
            border: 2px solid black;
            margin: 2px;
        }

        @page {
            size: auto;
            margin-left: 7mm;
            margin-right: 3mm;
            margin-top: 9mm;
            margin-bottom: 3mm;
        }

        @media print {
            table {
                page-break-after: auto
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto
            }

            td {
                page-break-inside: avoid;
                page-break-after: auto
            }

            thead {
                display: table-header-group
            }

            tfoot {
                display: table-footer-group
            }
        }

        #num {
            float: right;
            display: inline-block;
        }

        .qrc {
            width: 30px;
            height: 30px;
            margin-top: 1px;
        }
    </style>
</head>

<body onload="window.print()">

    <?php



    while ($data_cat = mysqli_fetch_assoc($check_cat)) {
        $check_service = $data_cat['service'];

        $check_service = mysqli_query($db, "SELECT * FROM services WHERE service = '$check_service'");
        $data_service = mysqli_fetch_assoc($check_service);

        $url = urlencode('http://' . $data_service['dns'] . '/login?username=' . $data_cat['voucher'] . '&password=' . $data_cat['voucher']);
    ?>

        <table class="voucher" style=" width: 160px;">
            <tbody>
                <tr>
                    <td style="text-align: left; font-size: 14px; font-weight:bold; border-bottom: 1px black solid;"><?= $data_cat['service'] ?> </span></td>
                </tr>
                <tr>
                    <td>
                        <table style=" text-align: center; width: 150px;">
                            <tbody>
                                <tr style="color: black; font-size: 11px;">
                                    <td>
                                        <table style="width:100%;">
                                            <!-- Username = Password    -->
                                            <tr>
                                                <td>Kode Voucher</td>
                                            </tr>
                                            <tr style="color: black; font-size: 14px;">
                                                <td style="width:100%; border: 1px solid black; font-weight:bold;"><?= $data_cat['voucher']; ?></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="border: 1px solid black; font-weight:bold;">Rp <?= number_format($data_cat['price']) ?></td>
                                            </tr>
                                            <!-- /  -->
                                            <!-- Username & Password  -->

                                            <!-- /  -->
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    <?php
    }

    $check_website = $db->query("SELECT * FROM website WHERE id ='1'");
    $data_website = $check_website->fetch_assoc();
    ?>