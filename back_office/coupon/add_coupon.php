<?php

require_once '../common.php';

$data = get_data_admin_type('admin', 'Administrateur', 'Veuillez remplir tous les champs du formulaire.', $_POST);

$obligations = ['name', 'merchant_id'];

if (!check_data_array_with_obligations($data, $obligations)) {
    exit_with_message('Veuillez renseigner tous les champs du formulaire.');
}

if (!count($_FILES)) {
    exit_with_message('Veuillez ajouter une image.');
}

$image = upload_image($_FILES['image'], 'coupons');

if (isset($image['error'])) {
    exit_with_message('Erreur lors du téléchargement de l\'image. Veuillez contacter le service client. Code d\'erreur : ' . $image['error']);
}

$query = $db->prepare('SELECT id FROM merchant_infos WHERE merchant_id = :merchant_id');
$query->execute([':merchant_id' => $data['merchant_id']]);
$infos = $query->fetch();

$query = $db->prepare('INSERT INTO coupons SET
    name = :name,
    image = :image,
    user_id = :user_id,
    merchant_id = :merchant_id,
    infos_id = :infos_id');
$added_coupon = $query->execute([
    ':name' => $data['name'],
    ':image' => $image['name'],
    ':user_id' => $data['u_id'],
    ':merchant_id' => $data['merchant_id'],
    ':infos_id' => $infos['id'],
]);

if ($added_coupon) {
    exit_with_message('Coupon ajouté avec succés.', false);
}

exit_with_message('Erreur lors de l\'ajout du coupon en base de données. Veuillez contacter le service client.');