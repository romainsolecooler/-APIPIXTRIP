<?php

require_once '../common.php';

$data = json_decode(file_get_contents('php://input'), true);
$obligations = ['u_id', 'id'];

if (!check_data_array_with_obligations($data, $obligations)) {
    exit_with_message('Veuillez renseigner tous les champs du formulaire.');
}

$user_id = clean($data['u_id']);
$id = clean($data['id']);

check_user_type($user_id, 'admin', 'Administrateur');

$bindings = [':id' => $id];

$query = $db->prepare('DELETE FROM coupons WHERE id = :id');
$deleted = $query->execute($bindings);
$query = $db->prepare('DELETE FROM trip_coupons WHERE coupon_id = :id');
$deleted = $query->execute($bindings);
$query = $db->prepare('DELETE FROM wallet WHERE coupon_id = :id;');
$deleted = $query->execute($bindings);

if ($deleted) {
    exit_with_message('Coupon supprimé.', false);
}

exit_with_message('Erreur lors de la suppression du coupon.');