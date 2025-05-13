<?php
header('Content-Type: application/json');

$boxes = [
    'brunei' => '5e41889919585f001bae8015',
    'london' => '626030a418aca4001ca27240',
    // Add more locations as needed
];

$location = $_GET['location'] ?? 'brunei';
$boxId = $boxes[$location] ?? $boxes['brunei'];

$url = "https://api.opensensemap.org/boxes/$boxId";

$response = @file_get_contents($url);
if (!$response) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch data from OpenSenseMap']);
    exit;
}
echo $response;
?>
