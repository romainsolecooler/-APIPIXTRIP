<?php

require_once '../common.php';

$data = $_POST;
$obligations = ['u_id', 'id', 'name'];

if (!check_data_array_with_obligations($data, $obligations)) {
    exit_with_message('Veuillez renseigner tous les champs du formulaire.');
}

$user_id = clean($data['u_id']);
$name = clean($data['name']);
$id = clean($data['id']);

check_user_type($user_id, 'admin', 'Administrateur');

$sql = 'UPDATE coupons SET name = :name';

$bindings = [
    ':name' => $name,
    ':id' => $id,
];

if (count($_FILES)) {
    $image = upload_image($_FILES['image'], 'coupons');
    if (isset($image['error'])) {
        exit_with_message('Erreur lors du téléchargement de l\'image. Veuillez contacter le service client. Code d\'erreur : ' . $image['error']);
    }
    $sql .= ', image = :image';
    $bindings['image'] = $image['name'];
}

$sql .= ' WHERE id = :id';

$query = $db->prepare($sql);
$updated_coupon = $query->execute($bindings);

if ($updated_coupon) {
    exit_with_message('Coupon modifié avec succés.', false);
}

exit_with_message('Erreur lors de la modification du coupon en base de données. Veuillez contacter le service client.');