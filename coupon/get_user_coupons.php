<?php

require_once '../common/connect.php';

$data = get_data('Veuillez renseigner un identifiant utilisateur.');

$sql = 'SELECT id, name, image, code, used, infos_id FROM coupons FULL JOIN wallet ON id = coupon_id
WHERE wallet.user_id = :user_id';
$bindings = [
    ':user_id' => $data['user_id'],
];

if (isset($data['used'])) {
    $sql .= ' AND used = :used';
    $bindings[':used'] = $data['used'];
}

$query = $db->prepare($sql);
$query->execute($bindings);

$coupons = $query->fetchAll();

foreach ($coupons as &$coupon) {
    $coupon['id'] = (int) $coupon['id'];
    $coupon['used'] = (int) $coupon['used'];
    $coupon['infos_id'] = (int) $coupon['infos_id'];
}

echo json_encode($coupons);