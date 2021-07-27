<?php

require_once '../common.php';

$data = get_data('Veuillez renseigner un identifiant utilisateur.');

check_user_type($data['u_id'], 'admin', 'Administrateur');

$query = $db->query('SELECT * FROM trips');
$trips = $query->fetchAll();

echo json_encode($trips);