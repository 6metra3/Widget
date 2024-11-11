<?php

namespace App\Http\Controllers;

use App\Services\MessageService;
use App\Services\SmartSenderService;
use App\Services\ZohoService;
use Illuminate\Support\Facades\DB;

class DBController extends Controller
{
    protected $zohoService;
    protected $smartSenderService;
    protected $messageService;

    public function __construct(ZohoService $zohoService, SmartSenderService $smartSenderService, MessageService $messageService)
    {
        $this->zohoService = $zohoService;
        $this->smartSenderService = $smartSenderService;
        $this->messageService = $messageService;
    }


    public function getContactData($contactId)
    {
        $contactDb = DB::table('contacts')
            ->where('smart_sender_id', $contactId)
            ->first();

        if (!$contactDb) {
            $contactCollection = $this->smartSenderService->getContactCollection($contactId);
            $zohoContactId = $this->zohoService->createContactZoho($contactCollection);
            DB::table('contacts')->insert([
                'smart_sender_id' => $contactCollection['id'],
                'zoho_crm_id' => $zohoContactId,
                'name' => $contactCollection['fullName'] ?? null,
                'phone' => $contactCollection['phone'] ?? null,
                'email' => $contactCollection['email'] ?? null,
            ]);
        }
        $messages = $this->smartSenderService->getContactMessages($contactId);
        $this->messageService->saveMessages($contactId, $messages);
    }}


