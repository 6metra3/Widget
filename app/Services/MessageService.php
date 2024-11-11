<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class MessageService
{
    public function saveMessages($contactId, $messages)
    {
        $sortedMessages = $messages->sortBy('deliveredAt');

        $contactDbId = DB::table('contacts')->where('smart_sender_id', $contactId)->value('id');
        foreach ($sortedMessages as $message) {
            if (!DB::table('messages')->where('message_id', $message['id'])->exists()) {
                DB::table('messages')->insert([
                    'contact_id' => $contactDbId,
                    'message_id' => $message['id'],
                    'message' => $message['content']['resource']['parameters']['content'] ?? null,
                    'content_type' => $message['content']['type'] ?? null,
                    'image' => $message['sender']['image'] ?? null,
                    'sender_type' => $message['sender']['type'] ?? null,
                    'delivered_at' => $message['deliveredAt'] ?? null,
                ]);
            }
        }
    }
}

