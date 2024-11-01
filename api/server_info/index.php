<?php
include_once '../../config.php';
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

echo(json_encode(array(
    "status" => "Running",
    "page_title" => $pageTitle,
    "canteens" => $canteen_types,
    "times" => $times,
    "default_time" => $default_time
)));