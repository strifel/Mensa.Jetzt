<?php
require_once 'config.php';
if (!isset($_GET['code'])) {
    header("Location: ".$oauth_request_url, true, 303);
    exit();
}

$code = $_GET['code'];
$parameters = 'client_id='.$oauth_client_id.'&code='.$code.'&grant_type=authorization_code&redirect_uri='.$oauth_redirect_url.'&client_secret='.$oauth_client_secret;

$options = [
    'http' => [
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => $parameters,
    ],
];

$context = stream_context_create($options);
$result = file_get_contents($oauth_token_url, false, $context);
if ($result === false) {
    http_response_code(401);
    die("auth failed");
}
$access_token = json_decode($result)->{'access_token'};

$options = [
    'http' => [
        'header' => "Authorization: Bearer ".$access_token."\r\n",
        'method' => 'GET'
    ],
];

$context = stream_context_create($options);
$result = file_get_contents($oauth_userinfo_url, false, $context);
if ($result === false) {
    http_response_code(500);
    die("request failed");
};
$data = json_decode($result, true);
session_start();
$_SESSION['user_id'] = $data['sub'];
if (isset($data['name'])) {
    $_SESSION['name'] = $data['name'];
}
header("Location: /");
exit();