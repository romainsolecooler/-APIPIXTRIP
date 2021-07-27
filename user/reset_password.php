<?php

require_once '../common/connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$obligations = ['u_id', 'password', 'confirm_password'];

if (!check_data_array_with_obligations($data, $obligations)) {
    exit_with_message('Veuillez renseigner tous les champs nécessaires.');
}

$u_id = clean($data['u_id']);
$password = clean($data['password']);
$confirm_password = clean($data['confirm_password']);

if ($password == '' || $confirm_password == '') {
    exit_with_message('Merci de renseigner un nouveau mot de passe.');
}

if ($password != $confirm_password) {
    exit_with_message('Merci de confirmer votre mot de passe.');
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$query = $db->prepare('UPDATE users SET password = :password WHERE u_id = :u_id');
$update = $query->execute([
    ':password' => $hashed_password,
    ':u_id' => $u_id,
]);

if ($update) {
    exit_with_message('Mot de passe modifié !', false);
} else {
    exit_with_message("Erreur lors de la modification du mot de passe.\nMerci de contacter le service client.");
}