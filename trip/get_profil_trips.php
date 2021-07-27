<?php

require_once '../common/connect.php';

$data = get_data('Veuillez remplir le formulaire.');

$sql = 'SELECT trips.id,
city,
difficulty,
time,
distance,
latitude,
longitude,
trips.image,
anecdote_1,
anecdote_2,
anecdote_3,
CASE 
    WHEN type = "admin"
    THEN TRUE
    ELSE FALSE
END
AS from_admin
FROM users 
FULL JOIN completed_trips ON u_id = completed_trips.user_id
LEFT JOIN trips ON completed_trips.trip_id = trips.id
WHERE completed_trips.user_id = :user_id';

$old_sql = 'SELECT id,
city,
difficulty,
time,
distance,
latitude,
longitude,
image,
anecdote_1,
anecdote_2,
anecdote_3 FROM completed_trips LEFT JOIN trips ON trip_id = id
WHERE completed_trips.user_id = :user_id';

$query = $db->prepare('SELECT id,
    city,
    difficulty,
    environment,
    category,
    latitude,
    longitude,
    image,
    anecdote_1,
    anecdote_2,
    anecdote_3 FROM completed_trips LEFT JOIN trips ON trip_id = id
    WHERE completed_trips.user_id = :user_id');
$query->execute([':user_id' => $data['user_id']]);
$trips = $query->fetchAll();

foreach ($trips as &$trip) {
    $trip['id'] = (int) $trip['id'];
    $trip['difficulty'] = (int) $trip['difficulty'];
    $trip['latitude'] = (double) $trip['latitude'];
    $trip['longitude'] = (double) $trip['longitude'];
}

echo json_encode($trips);