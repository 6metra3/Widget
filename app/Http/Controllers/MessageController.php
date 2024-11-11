<?php

namespace App\Http\Controllers;

use App\Services\SmartSenderService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    protected $smartSenderService;

    public function __construct(SmartSenderService $smartSenderService)
    {
        $this->smartSenderService = $smartSenderService;
    }

    public function sendMessage(Request $request)
    {
        return $this->smartSenderService->sendMessage($request);
    }
}
