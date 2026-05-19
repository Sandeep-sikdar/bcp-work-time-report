<?php
session_start();
require_once(__DIR__ . '/crestcurrent.php');

// Call user.current to force CRest to check, refresh, and save tokens if expired
CRest::call('user.current');

// Load settings
$settings = json_decode(file_get_contents(__DIR__ . '/settings.json'), true);

header('Content-Type: application/json');
echo json_encode([
    'access_token' => $settings['access_token'] ?? '',
    'client_endpoint' => $settings['client_endpoint'] ?? ''
]);
