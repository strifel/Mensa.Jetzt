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
        !hash_equals(explode(".", $token)[2], hash_hmac("sha512", "eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.".$token_mid_part, $jwt_signing_key))
    ) return FALSE;
    return json_decode(base64_decode($token_mid_part), true);
}
