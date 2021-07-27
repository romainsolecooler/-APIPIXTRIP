<?php

require_once '../common/connect.php';

$data = get_data('Veuillez remplir tous les champs du formulaire.');

if (!check_email($data['email'])) {
    exit_with_message('Veuillez renseigner une adresse mail.');
}

$query = $db->prepare('SELECT email, pseudo FROM users WHERE (email = :email OR pseudo = :pseudo) AND u_id != :u_id');
$query->execute([
    ':email' => $data['email'],
    ':pseudo' => $data['pseudo'],
    ':u_id' => $data['u_id']
]);

if (count($query->fetchAll())) {
    exit_with_message('Combinaison email / pseudo déjà utilisé.');
}

$query = $db->prepare('UPDATE users SET
    email = :email,
    pseudo = :pseudo,
    age = :age
        WHERE u_id = :u_id');
$query->execute([
    ':email' => $data['email'],
    ':pseudo' => $data['pseudo'],
    ':age' => $data['age'],
    ':u_id' => $data['u_id'],
]);

if ($query) {
    exit_with_message(null, false);
}

exit_with_message('Erreur lors de la modification en base de donnée. Veuillez contacter le service client.');