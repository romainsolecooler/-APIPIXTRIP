<?php

require_once '../common.php';

$data = get_data_admin_type('admin', 'Administrateur', 'Veuillez renseigner un identifiant utilisateur.');

$query = $db->prepare('DELETE FROM users WHERE u_id = :user_id');
$deleted = $query->execute([':user_id' => $data['user_id']]);

if (!$deleted) {
    exit_with_message('Erreur lors de la suppression de l\'utilisateur. Veuillez contacter le service client.');
}

exit_with_message('Utilisateur supprimé.', false);