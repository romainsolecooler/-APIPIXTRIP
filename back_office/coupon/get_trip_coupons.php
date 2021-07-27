<?php

require_once '../common.php';

$data = get_data_admin_type('admin', 'Administrateur', 'Veuillez remplir les champs du formulaire.');

$query = $db->prepare('SELECT id, name, image, code, user_id, merchant_id FROM coupons FULL JOIN trip_coupons ON id = coupon_id WHERE trip_id = :trip_id');
$query->execute([':trip_id' => $data['trip_id']]);
$coupons = $query->fetchAll();

if (!count($coupons)) {
    exit_with_message('Aucun coupon trouvé pour ce trip.');
}

echo json_encode($coupons);