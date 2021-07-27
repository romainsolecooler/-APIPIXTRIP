<?php

require_once '../common.php';

$data = json_decode(file_get_contents('php://input'), true);
$obligations = ['u_id', 'id'];

if (!check_data_array_with_obligations($data, $obligations)) {
    exit_with_message('Veuillez renseigner tous les champs nécessaires.');
}

$u_id = clean($data['u_id']);
$id = clean($data['id']);

check_user_type(clean($data['u_id']), 'admin', 'Administrateur');

$query = $db->prepare('SELECT id, city, difficulty, environment, latitude, longitude, image, environment, category, anecdote_1, anecdote_2, anecdote_3, published, home_trip FROM trips WHERE id = :id');
$query->execute([':id' => $id]);
$trip = $query->fetch();

if (!$trip) {
    exit_with_message('Aucun trip trouvé.');
}

$query = $db->prepare('SELECT coupon_id AS id, name, image FROM trip_coupons FULL JOIN coupons ON coupon_id = id WHERE trip_id = :trip_id');
$query->execute([':trip_id' => $id]);
$coupons = $query->fetchAll();
$trip['coupons'] = $coupons;

echo json_encode($trip);