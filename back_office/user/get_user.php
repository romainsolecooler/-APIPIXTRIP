<?php

require_once '../common.php';

$data = get_data('Veuillez remplir tous les champs.');

check_user_type($data['u_id'], 'admin', 'Administrateur');

$query = $db->prepare('SELECT u_id, email, pseudo, age, image, type, register_date FROM users WHERE u_id = :user_id');
$query->execute([':user_id' => $data['user_id']]);
$user = $query->fetch();

if (!$user) {
    exit_with_message('Aucun utilisateur trouvé.');
}

echo json_encode($user);