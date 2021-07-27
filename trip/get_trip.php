<?php

require_once '../common/connect.php';

$data = get_data('Veuillez renseigner une identifiant de Trip.');

$query = $db->prepare('SELECT
    id,
    city,
    difficulty,
    time,
    distance,
    latitude,
    longitude,
    altitude,
    image,
    anecdote_1,
    anecdote_2,
    anecdote_3 FROM trips
        WHERE id = :id AND published = 1');
$query->execute([':id' => $data['id']]);
$trip = $query->fetch();

if (!$trip) {
    exit_with_message('Aucun Trip trouvé pour cet identifiant.');
}

$trip['id'] = (int) $trip['id'];
$trip['difficulty'] = (int) $trip['difficulty'];
$trip['time'] = (int) $trip['time'];
$trip['distance'] = (int) $trip['distance'];
$trip['latitude'] = (double) $trip['latitude'];
$trip['longitude'] = (double) $trip['longitude'];
$trip['altitude'] = (double) $trip['altitude'];

echo json_encode($trip);