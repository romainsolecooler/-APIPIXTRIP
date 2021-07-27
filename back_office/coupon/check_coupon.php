<?php

require_once '../common.php';

$data = json_decode(file_get_contents('php://input'), true);
$obligations = ['u_id', 'code'];

if (!check_data_array_with_obligations($data, $obligations)) {
    exit_with_message('Veuillez renseigner tous les champs nécessaires.');
}

$id = clean($data['u_id']);
$code = clean($data['code']);

check_user_type($id, 'merchant', 'Marchand');

$query = $db->prepare('SELECT used FROM wallet FULL JOIN coupons ON coupon_id = id WHERE code = :code AND merchant_id = :merchant_id');
$query->execute([
    ':code' => $code,
    ':merchant_id' => $id
]);
$coupon = $query->fetch();

if (!$coupon) {
    exit_with_message('Ce coupon n\'existe pas ou appartient à un autre commercant.');
}

if ($coupon['used'] == 1) {
    exit_with_message('Ce coupon a déjà été utilisé.');
}

$query = $db->prepare('UPDATE wallet SET used = 1 WHERE code = :code');
$added_to_wallet = $query->execute([':code' => $code]);

if (!$added_to_wallet) {
    exit_with_message("Erreur lors de la vérification du coupon.\nVeuillez contacter le service client.");
}

exit_with_message('Coupon valide !', false);