<?php

require_once '../common/connect.php';

$data = get_data('Veuillez remplir le formulaire.');

$query = $db->prepare('SELECT name, address, phone, open_at, link FROM merchant_infos WHERE id = :id');
$query->execute([':id' => $data['id']]);
$infos = $query->fetch();

echo json_encode($infos);
exit;