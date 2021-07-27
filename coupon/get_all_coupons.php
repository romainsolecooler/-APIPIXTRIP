<?php

require_once '../common/connect.php';

$query = $db->query('SELECT * FROM coupons');
$coupons = $query->fetchAll();

echo json_encode($coupons);