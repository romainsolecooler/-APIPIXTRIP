<?php

require_once '../common/connect.php';

$data = get_data('Veuillez renseigner un identifiant de coupon.');

$query = $db->prepare('SELECT name, image FROM coupons WHERE id = :id');
$query->execute([':id' => $data['id']]);
$coupon = $query->fetch();

if (!$coupon) {
    exit_with_message('Aucun coupon trouvé pour cet identifiant.');
}

echo json_encode($coupon);