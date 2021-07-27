<?php

require_once '../common/connect.php';

$data = get_data('Veuillez remplir tous les champs du formulaire.', $_POST);

$query = $db->prepare('SELECT u_id FROM users WHERE u_id = :u_id AND (type = "merchant" OR type = "admin")');
$query->execute([':u_id' => $data['user_id']]);
$user = $query->fetchAll();

if (!count($user)) {
    exit_with_message('Veuillez vous connecter pour ajouter un coupon.');
}

$image = upload_image($_FILES['image'], 'coupons');

if (isset($image['error'])) {
    exit_with_message('Erreur lors du téléchargement de l\'image. Veuillez contacter le service client. Code d\'erreur : ' . $image['error']);
}

$query = $db->prepare('INSERT INTO coupons SET
    name = :name,
    image = :image,
    code = :code,
    user_id = :user_id');
$added_coupon = $query->execute([
    ':name' => $data['name'],
    ':image' => $image['name'],
    ':code' => unique_id(3),
    ':user_id' => $data['user_id'],
]);

if ($added_coupon) {
    exit_with_message('Coupon ajouté avec succès !', false);
}

exit_with_message('Erreur lors de l\'ajout du coupon en base de données. Veuillez contacter le service client.');