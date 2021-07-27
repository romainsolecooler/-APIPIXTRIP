<?php

require_once '../common/connect.php';

$data = get_data('Veuillez replir tous les champs du formulaire.');

$query = $db->prepare('SELECT id FROM coupon_state WHERE user_id');