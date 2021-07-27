<?php

require_once '../common/connect.php';

$data = get_data('Veuillez renseigner votre ancien mot de passe ainsi que le nouveau.');

if ($data['old_password'] == $data['new_password']) {
    exit_with_message('L\'ancien et le nouveau mot de passe ne peuvent pas être identiques.');
}

$query = $db->prepare('SELECT password FROM users WHERE u_id = :u_id');
$query->execute([':u_id' => $data['u_id']]);
$current_password = $query->fetch();

if (!$current_password) {
    exit_with_message('Aucun utilisateur trouvé pour cet identifiant');
}

$verify = password_verify($data['old_password'], $current_password['password']);

if (!$verify) {
    exit_with_message('Ancien mot de passe incorrect.');
}

$new_password = password_hash($data['new_password'], PASSWORD_DEFAULT);

$query = $db->prepare('UPDATE users SET password = :password WHERE u_id = :u_id');
$changed_password = $query->execute([
    ':password' => $new_password,
    ':u_id' => $data['u_id'],
]);

if ($changed_password) {
    exit_with_message(null, false);
}

exit_with_message('Erreur lors du changement du mot de passe. Veuillez contacter le service client.');