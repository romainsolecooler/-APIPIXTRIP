<?php

require_once '../common.php';

$data = json_decode(file_get_contents('php://input'), true);
$obligations = ['u_id'];

if (!check_data_array_with_obligations($data, $obligations)) {
    exit_with_message('Veuillez renseigner tous les champs nécessaires.');
}

$id = clean($data['u_id']);

check_user_type($id, 'merchant', 'Marchand');

$query = $db->prepare('SELECT name, used FROM wallet FULL JOIN coupons ON id = coupon_id WHERE merchant_id = :id');
$query->execute([':id' => $id]);
$coupons = $query->fetchAll();
$temp = [];
foreach ($coupons as $coupon) {
    $name = $coupon['name'];
    $used = $coupon['used'];
    if (!array_key_exists($name, $temp)) {
        $temp[$name] = [
            'used' => 0,
            'unused' => 0,
        ];
    }
    $used_unused_index = $used == 0 ? 'unused' : 'used';
    $temp[$name][$used_unused_index]++;
}

$res = [];
foreach ($temp as $key => $el) {
    $res[] = [
        'name' => $key,
        'used' => $el['used'],
        'unused' => $el['unused'],
    ];
}

echo json_encode($res);
exit;