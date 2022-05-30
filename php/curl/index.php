#!/usr/bin/env php

<?php
// Example of using API4AI personal protective equipment entrance detection.

// Use 'demo' mode just to try api4ai for free. Free demo is rate limited.
// For more details visit:
//   https://api4.ai
$MODE = 'demo';

// Your RapidAPI key. Fill this variable with the proper value if you want
// to try api4ai via RapidAPI marketplace.
$RAPIDAPI_KEY = null;

$OPTIONS = [
    'demo' => [
        'url' => 'https://demo.api4ai.cloud/ppe-entrance/v1/results',
        'headers' => ['A4A-CLIENT-APP-ID: sample']
    ]
];

// Initialize request session.
$request = curl_init();

// Check if path to local image provided.
$data = ['url' => 'https://storage.googleapis.com/api4ai-static/samples/ppe-3.jpg'];
if (array_key_exists(1, $argv)) {
    if (strpos($argv[1], '://')) {
        $data = ['url' => $argv[1]];
    } else {
        $filename = pathinfo($argv[1])['filename'];
        $data = ['image' => new CURLFile($argv[1], null, $filename)];
    }
}

// Set request options.
curl_setopt($request, CURLOPT_URL, $OPTIONS[$MODE]['url']);
curl_setopt($request, CURLOPT_HTTPHEADER, $OPTIONS[$MODE]['headers']);
curl_setopt($request, CURLOPT_POST, true);
curl_setopt($request, CURLOPT_POSTFIELDS, $data);
curl_setopt($request, CURLOPT_RETURNTRANSFER, true);

// Execute request.
$result = curl_exec($request);

// Decode response.
$raw_response = json_decode($result, true);

// Print raw response.
echo join('',
          ["💬 Raw response:\n",
           json_encode($raw_response),
           "\n"]);

// Parse response and get people count.
$objects_count = count($raw_response['results'][0]['entities'][0]['objects']);

// Close request session.
curl_close($request);

// Print people count and detected equipment.
$index = 1;
echo "\n💬 Recognized persons: ${objects_count} \n";
foreach ($raw_response['results'][0]['entities'][0]['objects'] as $obj) {
    echo "💬 Person {$index}:\n", json_encode($obj['entities'][1]['classes'], JSON_PRETTY_PRINT), "\n";
    $index += 1;
}
?>
