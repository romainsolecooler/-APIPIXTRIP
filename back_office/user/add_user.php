<?php

require_once '../common.php';

$data = get_data_admin_type('admin', 'Administrateur', '', $_POST);
// $exceptions = ['image', 'age'];
$obligations = ['email', 'pseudo', 'password', 'type'];

if (!check_data_array_with_obligations($data, $obligations)) {
    exit_with_message('Veuillez renseigner tous les champs du formulaire.');
}

/* if (!check_data_array_with_exceptions($data, $exceptions)) {
    exit_with_message('Veuillez renseigner tous les champs nécessaires.');
} */

if (!check_email($data['email'])) {
    exit_with_message('Veuillez renseigner une adresse mail valide.');
}

$query = $db->prepare('SELECT u_id FROM users WHERE email = :email OR pseudo = :pseudo');
$query->execute([
    ':email' => $data['email'],
    ':pseudo' => $data['pseudo'],
]);
$user = $query->fetchAll();

if (count($user)) {
    exit_with_message('Cet utilisateur existe déjà.');
}

if (count($_FILES)) {
    $image = upload_image($_FILES['image'], 'users');
    if (isset($image['error'])) {
        exit_with_message('Erreur lors du téléchargement de l\'image. Veuillez contacter le service client. Code d\'erreur : ' . $image['error']);
    }
}

$u_id = unique_id();
$age = $data['age'] ?? 0;
$hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
$image_name = $image['name'] ?? '';

$query = $db->prepare('INSERT INTO users SET
    u_id = :u_id,
    email = :email,
    pseudo = :pseudo,
    age = :age,
    password = :password,
    image = :image,
    type = :type,
    register_date = :register_date');
$added = $query->execute([
    ':u_id' => $u_id,
    ':email' => $data['email'],
    ':pseudo' => $data['pseudo'],
    ':age' => $age,
    ':password' => $hashed_password,
    ':image' => $image_name,
    ':type' => $data['type'],
    ':register_date' => $time,
]);

if (!$added) {
    exit_with_message('Erreur lors de l\'ajout de l\'utilisateur en base de données. Veuillez contacter le service client.');
}

if ($data['type'] == 'merchant') {
    $query = $db->prepare('INSERT INTO merchant_infos SET merchant_id = :merchant_id');
    $query->execute([':merchant_id' => $u_id]);
}

exit_with_message('Utilisateur ajouté.', false);