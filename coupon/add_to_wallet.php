<?php

require_once '../common/connect.php';

$data = get_data('Informations manquantes pour ajouter le coupon au wallet.');

$query = $db->prepare('SELECT u_id FROM users WHERE u_id = :u_id');
$query->execute([':u_id' => $data['user_id']]);
$user = $query->fetch();

if (!$user) {
    exit_with_message('Veuillez vous connecter pour ajouter un trip à votre wallet.');
}

$query = $db->prepare('SELECT id FROM coupons WHERE id = :id');
$query->execute([':id' => $data['coupon_id']]);
$coupon = $query->fetch();

if (!$coupon) {
    exit_with_message('Ce coupon n\'existe pas.');
}

$query = $db->prepare('SELECT used FROM wallet WHERE user_id = :user_id AND coupon_id = :coupon_id');
$query->execute([
    ':user_id' => $data['user_id'],
    ':coupon_id' => $data['coupon_id'],
]);

$already_added_coupon = $query->fetch();

$sql = '';
$bindings = [
    ':user_id' => $data['user_id'],
    ':coupon_id' => $data['coupon_id'],
];

if ($already_added_coupon) {
    if (!$already_added_coupon['used']) {
        exit_with_message(null);
    } else {
        $sql .= 'UPDATE wallet SET used = 0 WHERE user_id = :user_id AND coupon_id = :coupon_id';
    }
} else {
    $sql .= 'INSERT INTO wallet SET used = 0, user_id = :user_id, coupon_id = :coupon_id, code = "' . unique_id(3) . '"';
}

$query = $db->prepare($sql);
$added_coupon = $query->execute($bindings);

if ($added_coupon) {
    exit_with_message(null, false);
}

exit_with_message('Erreur lors de l\'ajout du coupon dans votre wallet. Veuillez contacter le service client.');