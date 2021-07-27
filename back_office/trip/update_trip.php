<?php

require_once '../common.php';

$data = $_POST;
$exceptions = ['anecdote_3', 'coupons'];
$obligations = ['id', 'city', 'difficulty', 'environment', 'category', 'latitude', 'longitude', 'anecdote_1', 'anecdote_2', 'published', 'home_trip'];

$user_id = clean($data['u_id']);

check_user_type($user_id, 'admin', 'Administrateur');

if (!check_data_array_with_obligations($data, $obligations)) {
    exit_with_message('Veuillez renseigner tous les champs nécessaires.');
}

$id = clean($data['id']);
$city = clean($data['city']);
$difficulty = clean($data['difficulty']);
$environment = clean($data['environment']);
$category = clean($data['category']);
$latitude = clean($data['latitude']);
$longitude = clean($data['longitude']);
$anecdote_1 = clean($data['anecdote_1']);
$anecdote_2 = clean($data['anecdote_2']);
$published = clean($data['published']);
$home_trip = clean($data['home_trip']);
$anecdote_3 = '';
if (isset($data['anecdote_3']) && strlen(clean($data['anecdote_3']))) {
    $anecdote_3 = clean($data['anecdote_3']);
}
if (isset($data['coupons']) && count($data['coupons'])) {
    $coupons = [];
    foreach ($data['coupons'] as $coupon) {
        $coupons[] = clean($coupon);
    }
}

$sql = 'UPDATE trips SET
    city = :city,
    difficulty = :difficulty,
    latitude = :latitude,
    longitude = :longitude,
    environment = :environment,
    category = :category,
    anecdote_1 = :anecdote_1,
    anecdote_2 = :anecdote_2,
    anecdote_3 = :anecdote_3,
    published = :published,
    home_trip = :home_trip';

$bindings = [
    ':id' => $id,
    ':city' => $city,
    ':difficulty' => $difficulty,
    ':latitude' => $latitude,
    ':longitude' => $longitude,
    ':environment' => $environment,
    ':category' => $category,
    ':anecdote_1' => $anecdote_1,
    ':anecdote_2' => $anecdote_2,
    ':anecdote_3' => $anecdote_3,
    ':published' => $published,
    ':home_trip' => $home_trip,
];

if (count($_FILES)) {
    $image = upload_image($_FILES['image'], 'trips');
    if (isset($image['error'])) {
        exit_with_message('Erreur lors du téléchargement de l\'image. Veuillez contacter le service client. Code d\'erreur : ' . $image['error']);
    }
    $sql .= ', image = :image';
    $bindings['image'] = $image['name'];
}

$sql .= ' WHERE id = :id';

$query = $db->prepare($sql);
$modified_trip = $query->execute($bindings);

if (!$modified_trip) {
    exit_with_message('Erreur lors de la modifcation du Trip en base de données. Veuillez contacter le service client.');
}

if (isset($data['coupons']) && count($data['coupons'])) {
    $query = $db->prepare('DELETE FROM trip_coupons WHERE trip_id = :trip_id');
    $query->execute([':trip_id' => $id]);
    $sql = 'INSERT INTO trip_coupons (trip_id, coupon_id) VALUES ';
    $bindings = [];
    foreach ($coupons as $coupon) {
        $trip_binding = ':' . unique_id(2);
        $coupon_binding = ':' . unique_id(2);
        $sql .= '(' . $trip_binding . ', ' . $coupon_binding . '),';
        $bindings[$trip_binding] = $id;
        $bindings[$coupon_binding] = $coupon;
    }
    $sql = substr($sql, 0, -1);
    $query = $db->prepare($sql);
    $added_coupons = $query->execute($bindings);
    if (!$added_coupons) {
        exit_with_message('Erreur lors de l\'ajout des coupons au trip. Veuillez contacter le service client.');
    }
}

exit_with_message('Trip modifié avec succès !', false);