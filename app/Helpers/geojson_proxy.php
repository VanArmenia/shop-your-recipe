<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$geojson_url = "https://www.geoboundaries.org/gbRequest.html?ISO=CAN&ADM=0";
$response = file_get_contents($geojson_url);

echo $response;
?>

