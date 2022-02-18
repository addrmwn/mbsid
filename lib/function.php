<?php

function random($length)
{
    $str = "";
    $characters = array_merge(range('A', 'Z'), range('a', 'z'), range('0', '9'));
    $max = count($characters) - 1;
    for ($i = 0; $i < $length; $i++) {
        $rand = mt_rand(0, $max);
        $str .= $characters[$rand];
    }
    return $str;
}



function random_number($length)
{
    $str = "";
    $characters = array_merge(range('0', '9'));
    $max = count($characters) - 1;
    for ($i = 0; $i < $length; $i++) {
        $rand = mt_rand(0, $max);
        $str .= $characters[$rand];
    }
    return $str;
}

function random_code($length)
{
    $str = "";
    $characters = array_merge(range('A', 'Z'), range('0', '9'));
    $max = count($characters) - 1;
    for ($i = 0; $i < $length; $i++) {
        $rand = mt_rand(0, $max);
        $str .= $characters[$rand];
    }
    return $str;
}
function acak_nomor($length)
{
    $str = "";
    $karakter = array_merge(range('1', '9'));
    $max_karakter = count($karakter) - 1;
    for ($i = 0; $i < $length; $i++) {
        $rand = mt_rand(0, $max_karakter);
        $str .= $karakter[$rand];
    }
    return $str;
}

function encrypt($string, $key = 128)
{
    $result = '';
    for ($i = 0, $k = strlen($string); $i < $k; $i++) {
        $char = substr($string, $i, 1);
        $keychar = substr($key, ($i % strlen($key)) - 1, 1);
        $char = chr(ord($char) + ord($keychar));
        $result .= $char;
    }
    return base64_encode($result);
}
function decrypt($string, $key = 128)
{
    $result = '';
    $string = base64_decode($string);
    for ($i = 0, $k = strlen($string); $i < $k; $i++) {
        $char = substr($string, $i, 1);
        $keychar = substr($key, ($i % strlen($key)) - 1, 1);
        $char = chr(ord($char) - ord($keychar));
        $result .= $char;
    }
    return $result;
}
function tanggal_indo($tanggal)
{
    $bulan = array(
        1 =>   'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    );
    $split = explode('-', $tanggal);
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}

function bulan_indo($ambil)
{
    $bulan = array(
        1 =>   'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    );
    $split = explode('-', $ambil);
    return  ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}

function time_elapsed_string($datetime, $full = false)
{
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'Tahun',
        'm' => 'Bulan',
        'w' => 'Minggu',
        'd' => 'Hari',
        'h' => 'Jam',
        'i' => 'Menit',
        's' => 'Detik',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? '' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' Yang Lalu' : 'Baru Saja';
}
function validate_date($date)
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') == $date;
}

function br2nl($string)
{
    return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
}

function relativeTime($time, $short = false)
{
    $SECOND = 1;
    $MINUTE = 60 * $SECOND;
    $HOUR = 60 * $MINUTE;
    $DAY = 24 * $HOUR;
    $MONTH = 30 * $DAY;
    $before = time() - $time;

    if ($before < 0) {
        return "not yet";
    }

    if ($short) {
        if ($before < 1 * $MINUTE) {
            return ($before < 5) ? "just now" : $before . " ago";
        }

        if ($before < 2 * $MINUTE) {
            return "1m ago";
        }

        if ($before < 45 * $MINUTE) {
            return floor($before / 60) . "m ago";
        }

        if ($before < 90 * $MINUTE) {
            return "1h ago";
        }

        if ($before < 24 * $HOUR) {

            return floor($before / 60 / 60) . "h ago";
        }

        if ($before < 48 * $HOUR) {
            return "1d ago";
        }

        if ($before < 30 * $DAY) {
            return floor($before / 60 / 60 / 24) . "d ago";
        }


        if ($before < 12 * $MONTH) {
            $months = floor($before / 60 / 60 / 24 / 30);
            return $months <= 1 ? "1mo ago" : $months . "mo ago";
        } else {
            $years = floor($before / 60 / 60 / 24 / 30 / 12);
            return $years <= 1 ? "1y ago" : $years . "y ago";
        }
    }

    if ($before < 1 * $MINUTE) {
        return ($before <= 1) ? "just now" : $before . " seconds ago";
    }

    if ($before < 2 * $MINUTE) {
        return "a minute ago";
    }

    if ($before < 45 * $MINUTE) {
        return floor($before / 60) . " minutes ago";
    }

    if ($before < 90 * $MINUTE) {
        return "an hour ago";
    }

    if ($before < 24 * $HOUR) {

        return (floor($before / 60 / 60) == 1 ? 'about an hour' : floor($before / 60 / 60) . ' hours') . " ago";
    }

    if ($before < 48 * $HOUR) {
        return "yesterday";
    }

    if ($before < 30 * $DAY) {
        return floor($before / 60 / 60 / 24) . " days ago";
    }

    if ($before < 12 * $MONTH) {

        $months = floor($before / 60 / 60 / 24 / 30);
        return $months <= 1 ? "one month ago" : $months . " months ago";
    } else {
        $years = floor($before / 60 / 60 / 24 / 30 / 12);
        return $years <= 1 ? "one year ago" : $years . " years ago";
    }

    return "$time";
}

function randN($length)
{
    $chars = "23456789";
    $charArray = str_split($chars);
    $charCount = strlen($chars);
    $result = "";
    for ($i = 1; $i <= $length; $i++) {
        $randChar = rand(0, $charCount - 1);
        $result .= $charArray[$randChar];
    }
    return $result;
}

function randUC($length)
{
    $chars = "ABCDEFGHJKLMNPRSTUVWXYZ";
    $charArray = str_split($chars);
    $charCount = strlen($chars);
    $result = "";
    for ($i = 1; $i <= $length; $i++) {
        $randChar = rand(0, $charCount - 1);
        $result .= $charArray[$randChar];
    }
    return $result;
}
function randLC($length)
{
    $chars = "abcdefghijkmnprstuvwxyz";
    $charArray = str_split($chars);
    $charCount = strlen($chars);
    $result = "";
    for ($i = 1; $i <= $length; $i++) {
        $randChar = rand(0, $charCount - 1);
        $result .= $charArray[$randChar];
    }
    return $result;
}

function randULC($length)
{
    $chars = "ABCDEFGHJKLMNPRSTUVWXYZabcdefghijkmnprstuvwxyz";
    $charArray = str_split($chars);
    $charCount = strlen($chars);
    $result = "";
    for ($i = 1; $i <= $length; $i++) {
        $randChar = rand(0, $charCount - 1);
        $result .= $charArray[$randChar];
    }
    return $result;
}

function randNLC($length)
{
    $chars = "23456789abcdefghijkmnprstuvwxyz";
    $charArray = str_split($chars);
    $charCount = strlen($chars);
    $result = "";
    for ($i = 1; $i <= $length; $i++) {
        $randChar = rand(0, $charCount - 1);
        $result .= $charArray[$randChar];
    }
    return $result;
}

function randNUC($length)
{
    $chars = "23456789ABCDEFGHJKLMNPRSTUVWXYZ";
    $charArray = str_split($chars);
    $charCount = strlen($chars);
    $result = "";
    for ($i = 1; $i <= $length; $i++) {
        $randChar = rand(0, $charCount - 1);
        $result .= $charArray[$randChar];
    }
    return $result;
}

function randNULC($length)
{
    $chars = "23456789ABCDEFGHJKLMNPRSTUVWXYZabcdefghijkmnprstuvwxyz";
    $charArray = str_split($chars);
    $charCount = strlen($chars);
    $result = "";
    for ($i = 1; $i <= $length; $i++) {
        $randChar = rand(0, $charCount - 1);
        $result .= $charArray[$randChar];
    }
    return $result;
}
