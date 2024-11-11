<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class ZohoService
{
    private $clientId;
    private $clientSecret;
    private $refreshToken;

    public function __construct()
    {
        $config = Config::get('configs.zoho');
        $clientId = $config['client_id'];
        $this->clientId = $clientId;
        $clientSecret = $config['client_secret'];
        $this->clientSecret = $clientSecret;
        $refreshToken = $config['refresh_token'];
        $this->refreshToken = $refreshToken;
    }

    public function getAccessToken()
    {
        $response = Http::asForm()->post('https://accounts.zoho.eu/oauth/v2/token', [
            'grant_type' => 'refresh_token',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $this->refreshToken,
        ]);
        if ($response->successful()) {
            $responseData = $response->json();
            return $responseData['access_token'];
        }
    }

    public function createContactZoho($contactCollection)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $this->getAccessToken(),
            'Content-Type' => 'application/json',
        ])->post('https://www.zohoapis.eu/crm/v6/Contacts', [
            "data" => [
                [
                    "Last_Name" => $contactCollection['fullName'] ?? 'Unknown',
                    "Phone" => $contactCollection['phone'] ?? '',
                    "Email" => $contactCollection['email'] ?? '',
                    "SM_User_ID" => $contactCollection['id'] ?? '',
                ]
            ]
        ]);
        if ($response->successful()) {
            return $response['data'][0]['details']['id'];
        }
    }
}
