<?php
// Get the API URL from the request
$apiUrl = $_GET['url'];

// Create a cURL handle
$curl = curl_init();

// Set the cURL options
curl_setopt($curl, CURLOPT_URL, $apiUrl);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

// Make the request to the API
$response = curl_exec($curl);

// Check for any errors
if (curl_errno($curl)) {
    header("HTTP/1.1 500 Internal Server Error");
    echo 'Error: ' . curl_error($curl);
    exit;
}

// Close the cURL handle
curl_close($curl);

// Forward the API response to the client
header('Content-Type: application/json');
echo $response;
?>
