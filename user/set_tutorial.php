<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once '../common/connect.php';

$data = get_data('Veuillez remplir le formulaire.');

$query = $db->prepare('UPDATE users SET tutorial = 1 WHERE u_id = :u_id');
$query->execute([':u_id' => $data['u_id']]);