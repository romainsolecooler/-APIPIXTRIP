<?php

require_once '../common/connect.php';

$data = get_data('Veuillez remplir les champs du formulaire.');

$query = $db->prepare('SELECT coordinates FROM completed_trips WHERE
    user_id = :user_id AND
    trip_id = :trip_id');
$query->execute([
    ':user_id' => $data['user_id'],
    ':trip_id' => $data['trip_id'],
]);

$coordinates = json_decode($query->fetch()['coordinates'], true);

foreach ($coordinates as &$el) {
    $el['lat'] = (double) $el['lat'];
    $el['lon'] = (double) $el['lon'];
}

echo json_encode($coordinates);