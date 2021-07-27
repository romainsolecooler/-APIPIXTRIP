<?php

require_once '../common/connect.php';

$data = get_data('Veuillez remplir tous les champs du formulaire.', $_POST);

$image = upload_image($_FILES['image'], 'trips');

if (isset($image['error'])) {
    exit_with_message('Erreur lors du téléchargement de l\'image. Veuillez contacter le service client. Code d\'erreur : ' . $image['error']);
}

$query = $db->prepare('UPDATE trips SET
    city = :city,
    difficulty = :difficulty,
    time = :time,
    latitude = :latitude,
    longitude = :longitude,
    altitude = :altitude,
    distance = :distance,
    image = :image,
    anecdote_1 = :anecdote_1,
    anecdote_2 = :anecdote_2,
    anecdote_3 = :anecdote_3,
    published = :published
        WHERE id = :id');
$modified_trip = $query->execute([
    ':city' => $data['city'],
    ':difficulty' => $data['difficulty'],
    ':time' => $data['time'],
    ':latitude' => $data['latitude'],
    ':longitude' => $data['longitude'],
    ':altitude' => $data['altitude'],
    ':distance' => $data['distance'],
    ':image' => $image['name'],
    ':anecdote_1' => $data['anecdote_1'],
    ':anecdote_2' => $data['anecdote_2'],
    ':anecdote_3' => $data['anecdote_3'],
    ':published' => $data['published'],
    ':id' => $data['id']
]);

if ($modified_trip) {
    exit_with_message('Trip modifié avec succès.', false);
}

exit_with_message('Erreur lors de la modifcation du Trip en base de données. Veuillez contacter le service client.');