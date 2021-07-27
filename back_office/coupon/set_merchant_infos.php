<?php

require_once '../common.php';

$data = json_decode(file_get_contents('php://input'), true);
//$data = get_data_admin_type('merchant', 'Marchand', 'Veuillez remplir les champs du formulaire.');
$obligations = ['u_id'];

if (!check_data_array_with_obligations($data, $obligations)) {
    exit_with_message('Veuillez renseigner tous les champs nécessaires.');
}

function clean_if_not_null($el) {
    global $data;
    return isset($data[$el]) ? clean($data[$el]) : null;
}

$id = clean($data['u_id']);
$name = clean_if_not_null('name');
$address = clean_if_not_null('address');
$phone = clean_if_not_null('phone');
$link = clean_if_not_null('link');
$open_at = isset($data['open_at']) ? clean(json_encode($data['open_at'])) : null;

$query = $db->prepare('SELECT id FROM merchant_infos WHERE merchant_id = :merchant_id');
$query->execute([':merchant_id' => $data['u_id']]);
$exists = $query->fetch();

$sql = ' merchant_infos SET 
    name = :name,
    address = :address,
    phone = :phone,
    open_at = :open_at,
    link = :link';
$bindings = [
    ':merchant_id' => $id,
    ':name' => $name,
    ':address' => $address,
    ':phone' => $phone,
    ':open_at' => $open_at,
    ':link' => $link,
];
$merchant_string = 'merchant_id = :merchant_id';

if ($exists) {
    $sql = 'UPDATE' . $sql . ' WHERE ' . $merchant_string;
} else {
    $sql = 'INSERT INTO' . $sql . ', ' . $merchant_string;
}

$query = $db->prepare($sql);
$infos = $query->execute($bindings);

if ($infos) {
    exit_with_message('Les informations ont bien été modifiées.', false);
}

exit_with_message('Erreur en base de données. Veuillez contacter le service client.');