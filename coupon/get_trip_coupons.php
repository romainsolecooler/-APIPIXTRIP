<?php

require_once '../common/connect.php';

$data = get_data('Veuillez remplir le formulaire.');

$query = $db->prepare('SELECT 
    id,
    name,
    image 
    FROM coupons FULL JOIN trip_coupons ON id = coupon_id
    WHERE trip_id = :trip_id');
$query->execute([':trip_id' => $data['trip_id']]);
$coupons = $query->fetchAll();

if (!count($coupons)) {
    exit_with_message(null);
}

foreach ($coupons as &$coupon) {
    $coupon['id'] = (int) $coupon['id'];
}

echo json_encode($coupons);
exit;