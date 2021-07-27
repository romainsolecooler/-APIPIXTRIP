<?php

require_once '../common/connect.php';

$data = get_data('');

$email = clean($data['email']);
$pseudo = clean($data['pseudo']);
$id = clean($data['id']);
$image = clean($data['image'] ?? '');
$apple = clean($data['apple'] ?? false);

if ($apple) {
    $query = $db->query('SELECT u_id, email, pseudo, age, password, image, tutorial FROM users WHERE email LIKE "%@privaterelay.appleid.com"');
    $users = $query->fetchAll();
    foreach ($users as $user) {
        if (password_verify($data['id'], $user['password'])) {
            echo json_encode([
                'u_id' => $user['u_id'],
                'email' => $user['email'],
                'pseudo' => $user['pseudo'],
                'age' => (int) $user['age'],
                'image' => $user['image'],
                'tutorial' => (int) $user['tutorial'],
            ]);
            exit;
        }
    }
}

$query = $db->prepare('SELECT u_id, email, pseudo, age, password, image, tutorial FROM users WHERE
    email = :email OR
    pseudo = :pseudo');
$query->execute([
    ':email' => $email,
    ':pseudo' => $pseudo,
]);
$user = $query->fetch();

if ($user) {
    $verify = password_verify($data['id'], $user['password']);
    if ($verify) {
        echo json_encode([
            'u_id' => $user['u_id'],
            'email' => $user['email'],
            'pseudo' => $user['pseudo'],
            'age' => (int) $user['age'],
            'image' => $user['image'],
            'tutorial' => (int) $user['tutorial'],
        ]);
        exit;
    }
    exit_with_message('Utilisateur déjà existant ou créé via le formulaire.');
} else {
    $u_id = unique_id();
    $hashed_password = password_hash($id, PASSWORD_DEFAULT);
    $type = 'user';

    $query = $db->prepare('INSERT INTO users SET
    u_id = :u_id,
    email = :email,
    pseudo = :pseudo,
    password = :password,
    image = :image,
    type = :type,
    register_date = :register_date');
    $added = $query->execute([
        ':u_id' => $u_id,
        ':email' => $email,
        ':pseudo' => $pseudo,
        ':password' => $hashed_password,
        ':image' => $image,
        ':type' => $type,
        ':register_date' => $time,
    ]);
    if ($added) {
        echo json_encode([
            'u_id' => $u_id,
            'email' => $email,
            'pseudo' => $pseudo,
            'age' => 0,
            'image' => $image,
            'tutorial' => 0,
        ]);
        exit;
    }
    exit_with_message('Erreur lors de l\'ajout en base de données. Veuillez contacter le service client.');
}