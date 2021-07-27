<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$time = time();

function debug($el, $die = true) {
    echo json_encode($el);
    $die && die;
    return;
}

function get_data($error_message = '', $source = null, $clean = true, $return_json = true) {
    $return_json && header('Content-Type: application/json');
    $raw_data = $source ?? json_decode(file_get_contents('php://input'));
    $data = $clean ? clean_array($raw_data) : $raw_data;
    if ($error_message != '') {
        if (!check_data_array($data)) {
            exit_with_message($error_message);
        }
    }
    return $data;
}

function check_user_type($u_id, $type, $label) {
    if (!isset($u_id)) {
        exit_with_message('Veuillez renseigner un identifiant administrateur.');
    }
    global $db;
    $user_id = clean($u_id);
    $query = $db->prepare('SELECT type FROM users WHERE u_id = :u_id');
    $query->execute([':u_id' => $user_id]);
    $user = $query->fetch();
    if ($user['type'] != $type) {
        exit_with_message('Statut requis pour continuer : ' . $label);
    }
    return true;
}

function get_data_admin_type($type, $label, $error_message = '', $source = null, $clean = true, $return_json = true) {
    $data = get_data($error_message, $source, $clean, $return_json);
    check_user_type($data['u_id'], $type, $label);
    return $data;
}

function get_data_with_user_type($error_message = '', $source = null, $clean = true, $return_json = true) {
    $data = get_data($error_message, $source, $clean, $return_json);
    if (!isset($data['u_id'])) {
        exit_with_message('Veuillez renseigner un identifiant utilisateur.');
    }
    global $db;
    $u_id = clean($data['u_id']);
    $query = $db->prepare('SELECT type FROM users WHERE u_id = :u_id');
    $query->execute([':u_id' => $u_id]);
    $user = $query->fetch();
    if (!$user || $user['type'] == 'user') {
        exit_with_message('Vous ne possédez pas les autorisations nécessaires.');
    }
    $data['user_type'] = $user['type'];
    return $data;
}

function unique_id($length = 20) {
    return bin2hex(random_bytes($length));
}

function clean($el) {
    return trim(strip_tags($el));
}

function clean_array($array) {
    $res = [];
    if (!empty($array)) {
        foreach ($array as $key => $el) {
            $res[$key] = clean($el);
        }
    }
    return $res;
}

function br($number = 1) {
    for ($i = 0; $i < $number; $i++) {
        echo '<br>';
    }
    return;
}

function check_email($email) {
    if (trim($email) == '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    return true;
}

function check_data_array($array) {
    if (empty($array)) {
        return false;
    }
    foreach ($array as $el) {
        if (is_string($el)) {
            if (trim($el) == '') {
                return false;
            }
        }
    }
    return true;
}

function check_data_array_with_exceptions($array, $exceptions) {
    if (empty($array)) {
        return false;
    }
    foreach($array as $key => $el) {
        if (!in_array($key, $exceptions)) {
            if (is_string($el)) {
                if (trim($el) == '') {
                    return false;
                }
            }
        }
    }
    return true;
}

function check_data_array_with_obligations($array, $obligations) {
    if (empty($array)) {
        return false;
    }
    foreach ($obligations as $ob) {
        if (isset($array[$ob])) {
            $el = $array[$ob];
            if (is_string($el)) {
                if (trim($el) == '') {
                    return false;
                }
            }
        } else {
            return false;
        }
    }
    return true;
}

function check_array_params($array, $needed) {
    foreach ($needed as $need) {
        if (empty($array[$need])) {
            return false;
        }
    }
    return true;
}

function exit_with_message($message, $error = true) {
    $res = [
        'error' => $error,
        'message' => $message,
    ];
    echo json_encode($res);
    exit;
    return;
}

function upload_image($file, $directory) {
    $res = [];
    try {
   
        // Undefined | Multiple Files | $_FILES Corruption Attack
        // If this request falls under any of them, treat it invalid.
        if (
            !isset($file['error']) ||
            is_array($file['error'])
        ) {
            $res['error'] = 'Invalid parameters.';
            return $res;
        }
    
        // Check $files['error'] value.
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                $res['error'] = 'No file sent.';
                return $res;
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $res['error'] = 'Exceeded filesize limit';
                return $res;
                break;
            default:
                $res['error'] = 'Unknown errors.';
                return $res;
                break;
        }
    
        // You should also check filesize here.
        if ($file['size'] > 10000000) {
            $res['error'] = 'Exceeded filesize limit.';
            return $res;
        }
    
        // DO NOT TRUST $files['mime'] VALUE !!
        // Check MIME Type by yourself.
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        if (false === $ext = array_search(
            $finfo->file($file['tmp_name']),
            array(
                'jpg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
            ),
            true
        )) {
            $res['error'] = 'Invalid file format.';
            return $res;
        }
    
        // You should name it uniquely.
        // DO NOT USE $files['name'] WITHOUT ANY VALIDATION !!
        // On this example, obtain safe unique name from its binary data.
        $name = sha1_file($file['tmp_name']) . '.' . $ext;
        if (!move_uploaded_file(
            $file['tmp_name'],
            sprintf($_SERVER['DOCUMENT_ROOT'] . '/images/' . $directory . '/%s',
                $name,
            )
        )) {
            $res['error'] = 'Failed to move uploaded file.';
            return $res;
        }
        // everything is fine
        $res['name'] = $name;
        return $res;
    
    } catch (RuntimeException $e) {
    
        return $e->getMessage();
    
    }
}