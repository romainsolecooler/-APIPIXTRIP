<?php

require_once '../common.php';

$data = get_data_admin_type('admin', 'Administrateur', 'Veuillez remplir les champs du formulaire.');

$obligations = ['trip_id', 'coupon_id'];

if (!check_data_array_with_obligations($data, $obligations)) {
    exit_with_message('Veuillez renseigner un identifiant de trip et de coupon.');
}

$query = $db->prepare('SELECT trip_id FROM trip_coupons WHERE trip_id = :trip_id AND coupon_id = :coupon_id');
$query->execute([
    ':trip_id' => $data['trip_id'],
    ':coupon_id' => $data['coupon_id'],
]);

$already_exists = $query->fetch();

if ($already_exists) {
    exit_with_message('Ce coupon est déjà associé à ce trip.');
}

$query = $db->prepare('INSERT INTO trip_coupons SET trip_id = :trip_id, coupon_id = :coupon_id');
$added = $query->execute([
    ':trip_id' => $data['trip_id'],
    ':coupon_id' => $data['coupon_id'],
]);

if (!$added) {
    exit_with_message('Erreur lors de l\'ajout en base de données. Veuillez contacter le service client.');
}

exit_with_message('Coupon ajouté au trip avec succès !', false);