<?php

require_once '../common.php';

$data = json_decode(file_get_contents('php://input'), true);
$obligations = ['u_id', 'id'];

if (!check_data_array_with_obligations($data, $obligations)) {
    exit_with_message('Veuillez renseigner tous les champs nécessaires.');
}

$u_id = clean($data['u_id']);
$id = clean($data['id']);

check_user_type(clean($data['u_id']), 'admin', 'Administrateur');

$sql = 'SELECT * FROM coupons WHERE id = :id';
$bindings = [':id' => $data['id']];

$query = $db->prepare($sql);
$query->execute($bindings);
$coupon = $query->fetch();

if (!$coupon) {
    exit_with_message('Aucun coupon trouvé.');
}

echo json_encode($coupon);