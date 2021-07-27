<?php

require_once '../common.php';

$data = get_data('Veuillez remseigner une email / pseudo et un mot de passe.');

$query = $db->prepare('SELECT u_id, email, pseudo, age, password, image, type FROM users WHERE email = :email OR pseudo = :pseudo');
$query->execute([
    ':email' => $data['email_pseudo'],
    ':pseudo' => $data['email_pseudo']
]);

$user = $query->fetch();

if (!$user) {
    exit_with_message('Aucun utilisateur trouvé pour cette adresse email / pseudo.');
}

if ($user['type'] == 'user') {
    exit_with_message('Seuls les administrateurs et les marchands peuvent se connecter au back office de Pixtrip.');
}

$verify = password_verify($data['password'], $user['password']);

if (!$verify) {
    exit_with_message('Mauvaise combinaison email / pseudo et mot de passe.');
}

$res = [
    'u_id' => $user['u_id'],
    'email' => $user['email'],
    'pseudo' => $user['pseudo'],
    'age' => $user['age'],
    'image' => $user['image'],
    'type' => $user['type']
];

echo json_encode($res);