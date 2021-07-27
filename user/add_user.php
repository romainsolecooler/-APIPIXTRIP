<?php

header('Access-Control-Allow-Origin: *');

require_once '../common/connect.php';

$data = get_data('Veuillez remplir tous les champs du formulaire.');

if (!check_email($data['email'])) {
    exit_with_message('Veuillez renseigner une adresse mail.');
}

$query = $db->prepare('SELECT u_id FROM users WHERE
    email = :email OR 
    pseudo = :pseudo');
$query->execute([
    ':email' => $data['email'],
    ':pseudo' => $data['pseudo']
]);
$user = $query->fetchAll();

if (count($user)) {
    exit_with_message('Cet utilisateur existe déjà.');
}

// $image = upload_image($_FILES['image'], 'users')['name'] ?? '';

$u_id = unique_id();
$hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
$type = $data['type'] ?? 'user';

$query = $db->prepare('INSERT INTO users SET 
    u_id = :u_id,
    email = :email,
    pseudo = :pseudo,
    password = :password,
    type = :type,
    register_date = :register_date,
    tutorial = 0');
$added = $query->execute([
    ':u_id' => $u_id,
    ':email' => $data['email'],
    ':pseudo' => $data['pseudo'],
    ':password' => $hashed_password,
    ':type' => $type,
    ':register_date' => $time,
]);

if ($added) {
    echo json_encode([
        'u_id' => $u_id
    ]);
    exit;
}

exit_with_message('Erreur lors de l\'ajout en base de donnée. Veuillez contacter le service client.');