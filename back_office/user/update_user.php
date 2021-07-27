<?php

require_once '../common.php';

$data = get_data_admin_type('admin', 'Administrateur', '', $_POST);
// $exceptions = ['image', 'age'];
$obligations = ['user_id', 'email', 'pseudo', 'type'];

if (!check_data_array_with_obligations($data, $obligations)) {
    exit_with_message('Veuillez renseigner tous les champs du formulaire.');
}

/* if (!check_data_array_with_exceptions($data, $exceptions)) {
    exit_with_message('Veuillez renseigner tous les champs nécessaires.');
} */

if (!check_email($data['email'])) {
    exit_with_message('Veuillez renseigner une adresse mail valide.');
}

$query = $db->prepare('SELECT email, pseudo, image FROM users WHERE (email = :email OR pseudo = :pseudo) AND u_id != :u_id');
$query->execute([
    ':email' => $data['email'],
    ':pseudo' => $data['pseudo'],
    ':u_id' => $data['user_id'],
]);

$users = $query->fetchAll();

if (count($users)) {
    exit_with_message('Combinaison email / pseudo déjà utilisé.');
}

$age = $data['age'] ?? 0;
$sql = 'UPDATE users SET
email = :email,
pseudo = :pseudo,
age = :age,
type = :type';
$bindings = [
    ':email' => $data['email'],
    ':pseudo' => $data['pseudo'],
    ':age' => $age,
    ':type' => $data['type'],
    ':user_id' => $data['user_id'],
];

if (count($_FILES) || isset($_FILES['image'])) {
    $image = upload_image($_FILES['image'], 'users');
    if (isset($image['error'])) {
        exit_with_message('Erreur lors du téléchargement de l\'image. Veuillez contacter le service client. Code d\'erreur : ' . $image['error']);
    }
    $sql .= ', image = :image';
    $bindings['image'] = $image['name'];
}

$sql .= ' WHERE u_id = :user_id';

$query = $db->prepare($sql);
$modified = $query->execute($bindings);

if (!$modified) {
    exit_with_message('Erreur lors de la modification en base de données. Veuillez contacter le service client.');
}

exit_with_message('Utilisateur modifié.', false);