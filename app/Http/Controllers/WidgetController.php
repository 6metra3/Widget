<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class WidgetController extends Controller
{
    public function getMessages($contactId)
    {
        $contactDb = DB::table('contacts')
            ->where('smart_sender_id', $contactId)
            ->first();

        if($contactDb)
        {
            $messages = DB::table('messages')
                ->where('contact_id', $contactDb->id)
                ->orderBy('delivered_at', 'desc')
                ->get();
            return $messages;
        }
        return response()->json(['message' => 'Contact not found'], 404);
    }

}
