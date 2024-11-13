<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;


class SmartSenderService
{
    private $apiKey;

    public function __construct()
    {
        $config = Config::get('configs.smartSender');
        $apikey = $config['api_key'];
        $this->apiKey = $apikey;
    }

    private function sendRequest($url)
    {
        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
        ])->get($url);
        if ($response->successful()) {
            return $response->json();
        }
    }

    public function getContactCollection($contactId)
    {
        $response = $this->sendRequest("https://api.smartsender.com/v1/contacts/{$contactId}");

        if ($response) {
            return $response;
        }
    }

    public function getContactMessages($contactId, $page = 1, $limitation = 20)
    {
        $url = "https://api.smartsender.com/v1/contacts/{$contactId}/messages?page=" . $page . "&limitation=" . $limitation;
        $response = $this->sendRequest($url);
        if ($response) {
            return collect($response['collection']);
        }
    }

    public function sendMessage(Request $request, $contactId)
    {
        $data = $request->all();

        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
        ])->post("https://api.smartsender.com/v1/contacts/{$contactId}/send", $data);

        if ($response->successful()) {
            return $response->json();
        } else {
            return response()->json([
                'message' => 'Error sending message',
                'error' => $response->json(),
            ], 500);
        }
    }


}
