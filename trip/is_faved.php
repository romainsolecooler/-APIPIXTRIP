<?php

require_once '../common/connect.php';

$data = get_data('Veuillez remplir le formulaire');

$query = $db->prepare('SELECT COUNT(trip_id) AS faved FROM faved_trips WHERE user_id = :user_id AND trip_id = :trip_id');
$query->execute([
    ':user_id' => $data['user_id'],
    ':trip_id' => $data['trip_id'],
]);

$faved = $query->fetch();
$return = $faved['faved'] == 1 ? true : false;

echo json_encode($return);