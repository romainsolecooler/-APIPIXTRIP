<?php

header('Access-Control-Allow-Origin: *');

require_once '../common/connect.php';

$data = get_data('', $_POST);

$data = clean_array($data);

$query = $db->prepare('SELECT u_id FROM users WHERE u_id = :u_id');
$query->execute([':u_id' => $data['user_id']]);
$user = $query->fetchAll();

if (!count($user)) {
    exit_with_message('Veuillez vous connecter pour ajouter un Trip.');
}

$image = upload_image($_FILES['image'], 'trips');

if (isset($image['error'])) {
    exit_with_message('Erreur lors du téléchargement de l\'image. Veuillez contacter le service client. Code d\'erreur : ' . $image['error']);
}

$needed = [
    'city',
    /* 'distance',
    'time',
    'difficulty', */
    'latitude',
    'longitude',
    'anecdote_1',
    'anecdote_2',
    'user_id',
];

if (!check_array_params($data, $needed)) {
    exit_with_message('Merci de renseigner les champs nécessaires.');
}

$anecdote_3 = $data['anecdote_3'] ?? '';

$query = $db->prepare('INSERT INTO trips SET
    city = :city,
    difficulty = :difficulty,
    environment = :environment,
    category = :category,
    latitude = :latitude,
    longitude = :longitude,
    image = :image,
    anecdote_1 = :anecdote_1,
    anecdote_2 = :anecdote_2,
    anecdote_3 = :anecdote_3,
    add_date = :add_date,
    published = :published,
    user_id = :user_id');
$added_trip = $query->execute([
    ':city' => $data['city'],
    ':difficulty' => $data['difficulty'],
    ':environment' => $data['environment'],
    ':category' => $data['category'],
    ':latitude' => $data['latitude'],
    ':longitude' => $data['longitude'],
    ':image' => $image['name'],
    ':anecdote_1' => $data['anecdote_1'],
    ':anecdote_2' => $data['anecdote_2'],
    ':anecdote_3' => $anecdote_3,
    ':add_date' => $time,
    ':published' => 0,
    ':user_id' => $data['user_id'],
]);

if ($added_trip) {
    exit_with_message('Trip ajouté avec succès !', false);
}

exit_with_message('Erreur lors de l\'ajout du trip en base de données. Veuillez contacter le service client.');