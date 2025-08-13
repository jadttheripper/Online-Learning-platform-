<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Get POSTed text and target language
$text = $_POST['text'] ?? '';
$target = $_POST['target'] ?? 'en';  // default to English

if (empty($text)) {
    echo json_encode(['translated' => 'No text provided']);
    exit;
}

// Google Cloud Translate API key
$apiKey = 'AIzaSyCVGjCOjWfwblpmM9rCZcfKgNZ5wOPSzRc'; //

// Prepare the request payload
$requestData = [
    'q' => $text,
    'target' => $target,
    'format' => 'text'
];

// Prepare cURL
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://translation.googleapis.com/language/translate/v2?key=$apiKey");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Parse and handle the response
if ($httpCode == 200 && $response) {
    $data = json_decode($response, true);
    $translatedText = $data['data']['translations'][0]['translatedText'] ?? 'Translation failed.';
    echo json_encode(['translated' => $translatedText]);
} else {
    echo json_encode(['translated' => 'Translation error.']);
}
?>
