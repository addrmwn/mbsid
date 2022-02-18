<?php
session_start();
require("../mainconfig.php");

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
    <title><?php echo $data_website['title']; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="{$_theme}/images/favicon.ico">
    <style>
        .v_wrapper {
            width: 241px;
            padding: 0 5px 5px 0;
            display: inline-block;
            border-right: 1px dashed #999;
            border-bottom: 1px dashed #999;
        }

        .table_printed {
            border: 1px solid #ccc;
            font-family: arial;
            font-size: 12px;
        }

        .td_header {
            border-bottom: 1px solid #ccc;
            background: #ddd !important;
            text-align: center;
        }

        .td_body_title {
            text-align: left;
        }

        .td_body_content {
            text-align: right;
        }

        .td_footer {
            border-bottom: 1px solid #ccc;
            background: #eee !important;
        }
    </style>

    <?php



    while ($data_cat = mysqli_fetch_assoc($check_cat)) {
        $check_service = $data_cat['service'];

        $check_service = mysqli_query($db, "SELECT * FROM services WHERE service = '$check_service'");
        $data_service = mysqli_fetch_assoc($check_service);

        $url = urlencode('http://' . $data_service['dns'] . '/login?username=' . $data_cat['voucher'] . '&password=' . $data_cat['voucher']);
    ?>

        <div class="v_wrapper">
            <table class="table_printed" width="100%">
                <tr>
                    <th colspan="3" class="td_header"><span style="padding:10px;font-size:15px;">** Voucher <?= $data_cat['service'] ?> **</span></th>
                </tr>
                <tr>
                    <td rowspan="6">
                        <img style="-webkit-user-select: none;margin: auto;background-color: hsl(0, 0%, 90%);transition: background-color 300ms;" src="https://api.qrserver.com/v1/create-qr-code/?size=60x60&amp;data='<?php echo $url; ?>'">
                    </td>
                </tr>
                <tr>
                    <td class="td_body_title"><span style="padding:5px;">Username</span></td>
                    <td class="td_body_content"><span style="padding:5px;"><b><?= $data_cat['voucher'] ?></b></span></td>
                </tr>
                <tr>
                    <td class="td_body_title"><span style="padding:5px;">Password</span></td>
                    <td class="td_body_content"><span style="padding:5px;"><b><?= $data_cat['voucher'] ?></b></span></td>
                </tr>
                <tr>
                    <td class="td_body_title"><span style="padding:5px;">Price</span></td>
                    <td class="td_body_content"><span style="padding:5px;"><b>Rp <?= number_format($data_cat['price']) ?></b></span></td>
                </tr>
                <tr>
                    <td style="text-align: center" colspan="3"><span style="color:#008899;">Login: <span style="color:#ff7777">http://<?php echo $data_service['dns']; ?></span></span></td>
                </tr>
            </table>
        </div>

    <?php
    }

    $check_website = $db->query("SELECT * FROM website WHERE id ='1'");
    $data_website = $check_website->fetch_assoc();
    ?>
</head>

<body>
    <page size="A4">
        <form method="post" action="print.php" class="no-print">
            <table width="100%" border="0" cellspacing="0" cellpadding="1" class="btn btn-default btn-sm">
                <script>
                    window.onload = function() {
                        window.print();
                    }
                </script>