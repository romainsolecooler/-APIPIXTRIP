<?php

require_once '../common.php';

$data = get_data_admin_type('admin', 'Administrateur', 'Veuillez remplir les champs du formulaire.');

$obligations = ['trip_id', 'coupon_id'];

if (!check_data_array_with_obligations($data, $obligations)) {
    exit_with_message('Veuillez renseigner un identifiant de Trip et de coupon.');
}

$query = $db->prepare('DELETE FROM trip_coupons WHERE trip_id = :trip_id AND coupon_id = :coupon_id');
$deleted = $query->execute([
    ':trip_id' => $data['trip_id'],
    ':coupon_id' => $data['coupon_id'],
]);

if (!$deleted) {
    exit_with_message('Erreur lors de la suppression en base de données. Veuillez contacter le service client.');
}

exit_with_message('Suppression effectuée.', false);