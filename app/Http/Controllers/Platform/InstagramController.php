<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InstagramController extends Controller
{
    public static function index($chatId, $url): void
    {
        $curl = curl_init();

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
                'link' => $url
            ),
            CURLOPT_HTTPHEADER => array(
                'Cookie: PHPSESSID=1275pvns30q469ddktol7f9vth'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $data = json_decode($response, true);

        if ($data['error'] === false && isset($data['html'])) {
            // Load HTML content
            $html = $data['html'];

            // Create a new DOMDocument
            $dom = new \DOMDocument();
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
            self::sendDocument($chatId, $downloadLink);
        } else {
            self::sendDocument($chatId, "This Url Not Valid!");
        }
    }

    public static function sendDocument($chatId, $document): void
    {
        $token = '7758856749:AAFHUR5Nc0VF1H0t4LB7-NcbHDmG086WogM';
        $url = "https://api.telegram.org/bot" . $token . "/sendMessage";

        $data = [
            'chat_id' => $chatId,
            'text' => "Downloading ... "
        ];

        $response = file_get_contents($url . '?' . http_build_query($data));

        $decode = json_decode($response);
        $message_id = $decode->result->message_id;

        $url = "https://api.telegram.org/bot$token/sendDocument";

        $data = [
            'chat_id' => $chatId,
            'document' => $document,
        ];

        file_get_contents($url . '?' . http_build_query($data));

        $url = "https://api.telegram.org/bot$token/deleteMessage";

        $data = [
            'chat_id' => $chatId,
            'message_id' => $message_id,
        ];

        file_get_contents($url . '?' . http_build_query($data));
    }
}
