<?php

require_once '../common.php';

$data = get_data('Veuillez renseigner un identifiant utilisateur.');

$query = $db->prepare('SELECT u_id, email, pseudo, age, image, type FROM users WHERE u_id = :u_id');
$query->execute([':u_id' => $data['u_id']]);
$user = $query->fetch();

if (!$user) {
    exit_with_message('Aucun utilisateur trouvé pour cette adresse email / pseudo.');
}

if ($user['type'] == 'user') {
    exit_with_message('Seuls les administrateurs et les marchands peuvent se connecter au back office de Pixtrip.');
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