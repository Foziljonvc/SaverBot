<?php

$curl = curl_init();

// Set up the cURL request
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://indownloader.app/request',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array(
        'link' => 'https://www.instagram.com/p/DBEgbIuuIdT/'
    ),
    CURLOPT_HTTPHEADER => array(
        'Cookie: PHPSESSID=1275pvns30q469ddktol7f9vth'
    ),
));

$response = curl_exec($curl);

curl_close($curl);

$data = json_decode($response, true);

// Check if there's an error
if ($data['error'] === false && isset($data['html'])) {
    // Load HTML content
    $html = $data['html'];

    // Create a new DOMDocument
    $dom = new DOMDocument();
    // Suppress warnings due to malformed HTML
    @$dom->loadHTML($html);

    // Extract all anchor tags
    $links = $dom->getElementsByTagName('a');

    // Loop through anchor tags to find the download link
    $downloadLink = null;
    foreach ($links as $link) {
        $href = $link->getAttribute('href');
        if (strpos($href, 'dl=1') !== false) {
            $downloadLink = $href;
            break;
        }
    }
    if ($downloadLink) {
        echo "Download link: " . $downloadLink;
    } else {
        echo "No valid download link found in the response.";
    }
} else {
    echo "Error in response or no HTML content.";
}

?>
