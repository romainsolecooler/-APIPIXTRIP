<?php

require_once 'utils.php';

$db_user = 'zjkv8823_pixtrip';
$db_password = '@Pixtrip_App66?';

try {
    $db = new PDO('mysql:host=localhost;dbname=zjkv8823_pixtrip;charset=utf8', $db_user, $db_password);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    // $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Connection failed : ' . $e->getMessage();
}
