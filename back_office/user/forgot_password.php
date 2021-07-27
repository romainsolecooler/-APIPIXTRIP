<?php

require_once '../common.php';

$data = json_decode(file_get_contents('php://input'), true);
$obligations = ['email'];

if (!check_data_array_with_obligations($data, $obligations)) {
    exit_with_message('Veuillez renseigner tous les champs nécessaires.');
}

$email = clean($data['email']);

$query = $db->prepare('SELECT u_id, email FROM users WHERE email = :email');
$query->execute([':email' => $email]);
$user = $query->fetch();

if ($user) {
    $to = $user['email'];
    $subject = 'Réinitialisation du mot de passe';
    $message = 'Merci de suivre ce lien pour réinitialiser votre mot de passe: https://pixtrip.fr/reset-password.php?u_id=' . $user['u_id'];
    $headers =  'From: contact@pixtrip.fr' . "\r\n" .
                'Reply-To: contact@pixtrip.fr' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
    mail($to, $subject, $message, $headers);
    exit_with_message('Un mail vient d\'être envoyé à votre adresse mail. Merci de suivre les instructions.', false);
} else {
    exit_with_message('Aucun utilisateur trouvé.');
}