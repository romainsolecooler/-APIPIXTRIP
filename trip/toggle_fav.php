<?php

require_once '../common/connect.php';

$data = get_data('Veuillez remplir le formulaire');

$bindings = [
    ':user_id' => $data['user_id'],
    ':trip_id' => $data['trip_id'],
];
$faved = false;

$query = $db->prepare('SELECT trip_id FROM faved_trips WHERE user_id = :user_id AND trip_id = :trip_id');
$query->execute($bindings);
$faved_trip = $query->fetch();

if ($faved_trip) {
    $query = $db->prepare('DELETE FROM faved_trips WHERE user_id = :user_id AND trip_id = :trip_id');
    $faved = false;
} else {
    $query = $db->prepare('INSERT INTO faved_trips SET user_id = :user_id, trip_id = :trip_id');
    $faved = true;
}
$query->execute($bindings);

echo json_encode(['faved' => $faved]);