<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Platform\InstagramController;
use Illuminate\Http\Request;

class IncomingData extends Controller
{
    public function RedirectByUrl(Request $request): void
    {
        $update = $request->input();

        if (isset($update["message"])) {
            $chatId = $update["message"]["chat"]["id"];
            $text = $update["message"]["text"];

            $url = $this->extractUrl($text);

            $platform = $this->getPlatformFromUrl($url);

            match ($platform) {
                'YouTube' => $this->sendMessage($chatId, "Sizning linkingiz YouTube platformasiga tegishli: $url"),
                'Instagram' => InstagramController::index($chatId, $url),
                'Facebook' => $this->sendMessage($chatId, "Sizning linkingiz Facebook platformasiga tegishli: $url"),
                'LinkedIn' => $this->sendMessage($chatId, "Sizning linkingiz LinkedIn platformasiga tegishli: $url"),
                'Threads' => $this->sendMessage($chatId, "Sizning linkingiz Threads platformasiga tegishli: $url"),
                default => $this->sendMessage($chatId, "Sizning kiritgan linkingiz tanilmaydi: $url"),
            };
        }
    }
    public function extractUrl($text): ?string
    {
        preg_match_all('/\bhttps?:\/\/[^\s]+/i', $text, $matches);
        return $matches[0] ? $matches[0][0] : null;
    }
    private function getPlatformFromUrl($url): string
    {
        if (str_contains($url, 'instagram.com')) {
            return 'Instagram';
        } elseif (str_contains($url, 'facebook.com')) {
            return 'Facebook';
        } elseif (str_contains($url, 'linkedin.com')) {
            return 'LinkedIn';
        } elseif (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
            return 'YouTube';
        } elseif (str_contains($url, 'threads.net')) {
            return 'Threads';
        } else {
            return 'Not';
        }
    }
    public function sendMessage($chatId, $text): void
    {
        $token = '7758856749:AAFHUR5Nc0VF1H0t4LB7-NcbHDmG086WogM';
        $url = "https://api.telegram.org/bot$token/sendMessage";

        $data = [
            'chat_id' => $chatId,
            'text' => $text,
        ];

        file_get_contents($url . '?' . http_build_query($data));
    }
}
