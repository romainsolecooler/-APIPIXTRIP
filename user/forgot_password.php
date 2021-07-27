<?php

require_once '../common/connect.php';

$data = get_data('Veuillez remplir le formulaire.');

$query = $db->prepare('SELECT u_id, email FROM users WHERE email = :email');
$query->execute([':email' => $data['email']]);
$user = $query->fetch();

if ($user) {
    $to = $user['email'];
    $subject = 'Réinitialisation du mot de passe';
    $message = 'Merci de suivre ce lien pour réinitialiser votre mot de passe: https://pixtrip.fr/reset-password.php?u_id=' . $user['u_id'];
    $headers =  'From: contact@pixtrip.fr' . "\r\n" .
                'Reply-To: contact@pixtrip.fr' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
    mail($to, $subject, $message, $headers);
    exit_with_message(null, false);
} else {
    exit_with_message(null);
}