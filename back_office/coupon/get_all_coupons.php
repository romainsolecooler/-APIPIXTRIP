<?php

require_once '../common.php';

$data = get_data_with_user_type('Veuillez remplir tous les champs du formulaire.');

$sql = 'SELECT * FROM coupons';
$bindings = [];

if ($data['user_type'] == 'merchant') {
    $sql .= ' WHERE merchant_id = :merchant_id';
    $bindings[':merchant_id'] = $data['u_id'];
}

$query = $db->prepare($sql);
$query->execute($bindings);
$coupons = $query->fetchAll();

echo json_encode($coupons);