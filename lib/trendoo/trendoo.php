<?php
define("TRENDOO_BASEURL", "https://api.trendoo.net/API/v1.0/REST/");

define("MESSAGE_HIGH_QUALITY", "GP");
define("MESSAGE_MEDIUM_QUALITY", "GS");
define("MESSAGE_LOW_QUALITY", "SI");

/**
 * Authenticates the user given it's username and password.
 * Returns the pair user_key, Session_key
 */
function smsLogin($username, $password) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, TRENDOO_BASEURL .
                'login?username=' . $username .
                '&password=' . $password);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);

    if ($info['http_code'] != 200) {
        return null;
    }

    return explode(";", $response);
}

/**
 * Sends an SMS message
 */
function sendSMS($auth, $sendSMS) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, TRENDOO_BASEURL . 'sms');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-type: application/json',
        'user_key: ' . $auth[0],
        'Session_key: ' . $auth[1]
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($sendSMS));
    $response = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);

    if ($info['http_code'] != 201) {
        return null;
    }

    return json_decode($response);
}

/**
 * Get SMS history
 */
function smsHistory($auth, $dateFrom = false) {
    $dateFrom = ($dateFrom != false) ? $dateFrom : date("YmdHis", strtotime("-3 days"));
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, TRENDOO_BASEURL . 'smshistory?from=' . $dateFrom);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-type: application/json',
        'user_key: ' . $auth[0],
        'Session_key: ' . $auth[1]
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);

    if ($info['http_code'] != 200) {
        return null;
    }
    else {
        return json_decode($response);
    }
}

/**
 * Get SMS message state
 */
function smsState($auth, $order_id = false) {
    if ($order_id != false) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, TRENDOO_BASEURL . 'sms/' . $order_id);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type: application/json',
            'user_key: ' . $auth[0],
            'Session_key: ' . $auth[1]
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        if ($info['http_code'] != 200) {
            return null;
        }
        else {
            return json_decode($response);
        }
    }
    else {
        return null;
    }
}

/**
 * Get status description
 */
function smsStatus($status) {

    switch ($status) {
        case 'SCHEDULED'    : return 'Envío programado'; break;
        case 'SENT'         : return 'Enviado, sin reporte de entrega'; break;
        case 'DLVRD'        : return 'Recibido'; break;
        case 'INVALIDDST'   : return 'Destinatario no válido'; break;
        case 'ERROR'        : return 'Error de entrega'; break;
        case 'TIMEOUT'      : return 'Sin información del operador'; break;
        case 'TOOM4NUM'     : return 'Demasiados SMS para el mismo destinatario'; break;
        case 'TOOM4USER'    : return 'Demasiados SMS enviados por el usuario'; break;
        case 'UNKNPFX'      : return 'Prefijo SMS no válido'; break;
        case 'UNKNRCPT'     : return 'Número de teléfono no válido'; break;
        case 'WAIT4DLVR'    : return 'Mensaje enviado, en espera del reporte'; break;
        case 'WAITING'      : return 'Pendiente, no enviado todavía'; break;
        case 'UNKNOWN'      : return 'Estado desconocido'; break;
        case 'BLACKLISTED'  : return 'Número en lista negra'; break;
        case 'KO'           : return 'Envío rechazado'; break;
        case 'INVALIDCONTENTS'  : return 'Contenido no válido'; break;
    }
    
}


/**
 * SMS Credits service status
 */
function smsCredit($auth) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, TRENDOO_BASEURL . 'status');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-type: application/json',
        'user_key: ' . $auth[0],
        'Session_key: ' . $auth[1]
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);

    if ($info['http_code'] != 200) {
        return null;
    }
    else {
        return json_decode($response);
    }
}