<?php

require_once '../common/connect.php';

$data = get_data('', null, false);

$user_id = clean($data->user_id);
$trip_id = clean($data->trip_id);
$coordinates = [];

foreach ($data->coordinates as $el) {
    $coordinates[] = [
        'lat' => clean($el->lat),
        'lon' => clean($el->lon)
    ];
}

$coordinates = json_encode($coordinates);

$query = $db->prepare('SELECT trip_id FROM completed_trips WHERE user_id = :user_id AND trip_id = :trip_id');
$query->execute([
    ':user_id' => $user_id,
    ':trip_id' => $trip_id,
]);
$exist = $query->fetch();

$formated = '';
$sql = 'completed_trips SET user_id = :user_id, trip_id = :trip_id, coordinates = :coordinates';
$bindings = [
    ':user_id' => $user_id,
    ':trip_id' => $trip_id,
    ':coordinates' => $coordinates,
];
if ($exist) {
    $formated = 'UPDATE ' . $sql . ' WHERE user_id = :exist_user AND trip_id = :exist_trip';
    $bindings[':exist_user'] = $user_id;
    $bindings[':exist_trip'] = $trip_id;
} else {
    $formated = 'INSERT INTO ' . $sql;
}

$query = $db->prepare($formated);
$query->execute($bindings);