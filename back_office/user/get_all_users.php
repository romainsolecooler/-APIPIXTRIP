<?php

require_once '../common.php';

$data = get_data_admin_type('admin', 'Administrateur', 'Veuillez renseigner un identifiant utilisateur.');

$query = $db->query('SELECT u_id, email, pseudo, age, image, type, register_date FROM users');
$users = $query->fetchAll();

echo json_encode($users);