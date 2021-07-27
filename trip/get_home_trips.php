<?php

require_once '../common/connect.php';

header('Content-Type: application/json');

$query = $db->query('SELECT
    trips.id,
    city,
    difficulty,
    environment,
    category,
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
    FULL JOIN trips ON u_id = user_id
        WHERE home_trip = 1 AND published = 1
        ORDER BY id ASC');
$trips = $query->fetchAll();

foreach ($trips as &$trip) {
    $trip['id'] = (int) $trip['id'];
    $trip['difficulty'] = (int) $trip['difficulty'];
    $trip['latitude'] = (double) $trip['latitude'];
    $trip['longitude'] = (double) $trip['longitude'];
    $trip['from_admin'] = boolval($trip['from_admin']);
}

echo json_encode($trips);