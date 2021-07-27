<?php

require_once '../common/connect.php';

$data = get_data('Veuillez renseigner un identifiant d\'utilisateur.', $_POST);

$image = upload_image($_FILES['image'], 'users');

if (isset($image['error'])) {
    exit_with_message('Erreur lors du téléchargementde l\'image. Veuillez contacter le service client. Code d\'erreur : ' . $image['error']);
}

$query = $db->prepare('UPDATE users SET image = :image WHERE u_id = :u_id');
$query->execute([
    ':image' => $image['name'],
    ':u_id' => $data['u_id'],
]);

if ($query) {
    echo json_encode(['image' => $image['name']]);
    exit;
}

exit_with_message('Erreur lors de la modification en base de données. Veuillez contacter le service client.');