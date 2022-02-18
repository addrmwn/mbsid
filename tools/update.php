<?php

// Untuk melakukan update pada status voucher yang sedang digunakan.

date_default_timezone_set('Asia/Jakarta');

require("../mainconfig.php");
require("../lib/api.class.php");

$checkdb_voucher = mysqli_query($db, "SELECT * FROM orders");
while ($datadb_voucher = mysqli_fetch_assoc($checkdb_voucher)) {

    $voucher = $datadb_voucher['voucher'];
    $layanan = $datadb_voucher['service'];
    $harga = $datadb_voucher['price'];

    if ($datadb_voucher['status_voucher'] == 'Sudah Digunakan') {
        echo "<font color='green'><b>Voucher : $voucher | Status : Sudah digunakan  </font><br><br>";
    } else {
        // cek data router 

        $checkdb_router = mysqli_query($db, "SELECT * FROM data_router");
        $datadb_router = mysqli_fetch_assoc($checkdb_router);

        // info router
        $ip_server = $datadb_router['ip_server'];
        $username = $datadb_router['username_mikrotik'];
        $password = decrypt($datadb_router['password_mikrotik']);
        // api router
        $API = new RouterosAPI();
        if ($API->connect($ip_server, $username, $password)) {
            $gethotspot = $API->comm("/ip/hotspot/active/print");

            $totalhotspotuser = count($gethotspot);
            $hotspotactive = $gethotspot;

            foreach ($hotspotactive as $active_voucher) {
                $enabled = $active_voucher['user'];


                $voucher_actived = $enabled;
                $voucherdb = $datadb_voucher['voucher'];

                if ($voucherdb == $voucher_actived) {
                    $update = mysqli_query($db, "UPDATE `orders` SET `status_voucher`='Sudah Digunakan' WHERE voucher='$voucher_actived'");
                    $update = mysqli_query($db, "INSERT INTO report (service, voucher, price, date ) VALUES ('$layanan','$voucherdb','$harga','$date') ");
                    if ($update == TRUE) {
                        echo "<font color='green'><b>Voucher : $voucher | Status : Berhasil Di Update  </font><br><br>";
                    } else {
                        echo "System Error";
                    }
                }
            }
            echo "<font color='red'><b>Voucher : $voucher | Status : Belum aktif pada mikrotik hotspot </font><br><br>";
        }
    }
}
