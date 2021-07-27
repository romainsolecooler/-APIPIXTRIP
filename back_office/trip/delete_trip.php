<?php 

require_once '../common.php';

$data = get_data_admin_type('admin', 'Administrateur', 'Veuillez renseigner un identifiant de trip.');

$obligations = ['id'];

if (!check_data_array_with_obligations($data, $obligations)) {
    exit_with_message('Veuillez renseigner un identifiant de trip.');
}

$query = $db->prepare('DELETE FROM trips WHERE id = :id');
$deleted = $query->execute([':id' => $data['id']]);

if (!$deleted) {
    exit_with_message('Erreur lors de la suppression du trip. Veuillez contacter le service client.');
}

$query = $db->prepare('DELETE FROM completed_trips WHERE trip_id = :trip_id');
$deleted = $query->execute([':trip_id' => $data['id']]);

if (!$deleted) {
    exit_with_message('Erreur lors de la suppression des données du trip. Veuillez contacter le service client.');
}

$query = $db->prepare('DELETE FROM faved_trips WHERE trip_id = :trip_id');
$query->execute([':trip_id' => $data['id']]);

exit_with_message('Trip supprimé', false);