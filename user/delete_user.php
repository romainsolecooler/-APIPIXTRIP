<?php

/* require_once '../common/connect.php';

$data = get_data('Veuillez sélectionner un utilisateur à supprimer.');

$query = $db->prepare('SELECT image FROM users WHERE u_id = :u_id AND email = :email');
$query->execute([
    ':u_id' => $data['u_id'],
    ':email' => $data['email'],
]);
$user = $query->fetch();

if (!$user) {
    exit_with_message('Utilisateur introuvable.');
}

$query = $db->prepare('DELETE FROM users WHERE u_id = :u_id AND email = :email');
$deleted = $query->execute([
    ':u_id' => $data['u_id'],
    ':email' => $data['email'],
]);

if ($deleted) {
    $user['image'] != '' && unlink($_SERVER['DOCUMENT_ROOT'] . '/images/users/' . $user['image']);
    exit_with_message('Utilisateur supprimé avec succès.', false);
}

exit_with_message('Erreur lors de la suppression de l\'utilisateur. Veuillez contacter le service client.'); */