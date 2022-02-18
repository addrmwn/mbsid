<?php
date_default_timezone_set('Asia/Jakarta');
error_reporting(0);


// config website
$cfg_baseurl =  "http://localhost/mbsid/";


// database
$db_server = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "mbsid";

// date & time
$date = date("Y-m-d");
$time = date("H:i:s");

// require
require("lib/database.php");
require("lib/function.php");
