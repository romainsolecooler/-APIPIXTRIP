<?php

require_once '../common.php';

$data = json_decode(file_get_contents('php://input'), true);
$obligations = ['u_id'];

if (!check_data_array_with_obligations($data, $obligations)) {
    exit_with_message('Veuillez renseigner tous les champs nécessaires.');
}

$id = clean($data['u_id']);

check_user_type($id, 'merchant', 'Marchand');

$query = $db->prepare('SELECT name, address, phone, open_at, link FROM merchant_infos WHERE merchant_id = :merchant_id');
$query->execute([':merchant_id' => $id]);
$infos = $query->fetch();

$open_at = $infos['open_at'];

if ($open_at == null) {
    $open_at = [
        "monday" => [],
        "tuesday" => [],
        "wednesday" => [],
        "thursday" => [],
        "friday" => [],
        "saturday" => [],
        "sunday" => [],
    ];
    $infos['open_at'] = $open_at;
} else {
    $infos['open_at'] = json_decode($open_at);
}

echo json_encode($infos);
exit;