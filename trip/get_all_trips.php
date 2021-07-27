<?php

require_once '../common/connect.php';

$data = get_data('');

$conditionnal = '';
$bindings = [];
if (!empty($data)) {
    $data = clean_array($data);
    foreach ($data as $key => $el) {
        $binding_string = ':' . $key;
        $conditionnal .= ' AND ' . $key . ' = ' . $binding_string;
        $bindings[$binding_string] = $el;
    }
}

$sql = 'SELECT
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
        WHERE published = 1 ORDER BY id ASC';

$query = $db->prepare($sql);
$query->execute($bindings);
$trips = $query->fetchAll();

if (!$trips) {
    exit_with_message('Aucun Trip trouvé pour ces critères.');
}

foreach ($trips as &$trip) {
    $trip['id'] = (int) $trip['id'];
    $trip['difficulty'] = (int) $trip['difficulty'];
    $trip['latitude'] = (double) $trip['latitude'];
    $trip['longitude'] = (double) $trip['longitude'];
    $trip['from_admin'] = boolval($trip['from_admin']);
}

echo json_encode($trips);