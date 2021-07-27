<?php

exit;

require_once '../common/connect.php';

$data = get_data('Veuillez renseigner une photo à comparer.', $_POST);

$image = upload_image($_FILES['image'], 'temp');

if (isset($image['error'])) {
    exit_with_message('Erreur lors du téléchargement de l\'image. Veuillez contacter le service client. Code d\'erreur : ' . $image['error']);
}

$temp_image_name = $_SERVER['DOCUMENT_ROOT'] . '/images/temp/' . $image['name'];

$image1 = new Imagick($_SERVER['DOCUMENT_ROOT'] . '/images/trips/' . $data['trip_image']);
$image2 = new Imagick($temp_image_name);
$result = $image1->compareImages($image2, Imagick::METRIC_MEANSQUAREERROR);

$can_continue = $result[1] < 0.12 ? true : false;

//unlink($temp_image_name);

exit_with_message($can_continue, false);