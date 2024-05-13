<?php

require_once 'config.php';

function create_token($user_id, $username, $name): string {
    global $jwt_signing_key;

    $payload = json_encode([
        "user_id"=> $user_id,
        "username"=> $username,
        "name" => $name,
        "iat" => time(),
        "exp" => time() + 60*60*24*31,
    ]);
    $token = "eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.".str_replace("=", "", base64_encode($payload));
    $hash = hash_hmac("sha512", $token, $jwt_signing_key);
    $token = $token.".".$hash;
    return $token;
}

function get_token_value($token) {
    global $jwt_signing_key;

    if (substr_count($token,".") != 2) return FALSE;
    $token_mid_part = explode(".", $token)[1];
    if (
        strcmp(hash_hmac("sha256", "eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.".$token_mid_part, $jwt_signing_key),
        explode(".", $token)[2]) == 0
    ) return FALSE;
    return json_decode(base64_decode($token_mid_part), true);
}
