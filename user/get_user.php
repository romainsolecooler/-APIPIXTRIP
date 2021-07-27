<?php

require_once '../common/connect.php';

$data = get_data('Veuillez renseigner un identifiants d\'utilisateur');

$query = $db->prepare('SELECT email, pseudo, age, image, tutorial FROM users WHERE u_id = :u_id');
$query->execute([':u_id' => $data['u_id']]);
$user = $query->fetch();

if (!$user) {
    exit_with_message('Aucun utilisateur trouvé pour cet identifiant.');
}

$user['age'] = (int) $user['age'];
$user['tutorial'] = (int) $user['tutorial'];

echo json_encode($user);