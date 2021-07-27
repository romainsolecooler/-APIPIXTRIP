<?php

require_once '../common.php';

$data = json_decode(file_get_contents('php://input'), true);
$obligations = ['u_id'];

if (!check_data_array_with_obligations($data, $obligations)) {
    exit_with_message('Veuillez renseigner tous les champs nécessaires.');
}

$id = clean($data['u_id']);

check_user_type($id, 'admin', 'Administrateur');

$query = $db->query('SELECT u_id AS id, email, pseudo, image FROM users WHERE type = "merchant"');
$merchants = $query->fetchAll();

echo json_encode($merchants);
exit;