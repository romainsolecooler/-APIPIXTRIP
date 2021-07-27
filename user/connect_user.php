<?php

require_once '../common/connect.php';

$data = get_data('Veuillez renseigner un email / pseudo et un mot de passe.');

$query = $db->prepare('SELECT u_id, email, pseudo, age, password, image, tutorial FROM users WHERE email = :email OR pseudo = :pseudo');
$query->execute([
    ':email' => $data['email_pseudo'],
    ':pseudo' => $data['email_pseudo']
]);

$user = $query->fetch();

if (!$user) {
    exit_with_message('Aucun utilisateur trouvé pour cette adresse email / ce pseudo.');
}

$verify = password_verify($data['password'], $user['password']);

if (!$verify) {
    exit_with_message('Mauvaise combinaison email / pseudo et mot de passe.');
}

$res = [
    'u_id' => $user['u_id'],
    'email' => $user['email'],
    'pseudo' => $user['pseudo'],
    'age' => (int) $user['age'],
    'image' => $user['image'],
    'tutorial' => (int) $user['tutorial']
];

echo json_encode($res);