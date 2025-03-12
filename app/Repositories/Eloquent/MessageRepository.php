<?php

namespace App\Repositories\Eloquent;

use App\Models\Message;
use App\Models\User;
use App\Repositories\Contracts\MessageRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MessageRepository implements MessageRepositoryInterface
{
    public function sendMessage($text, $recipient,$iv, $expiry_minutes): Message
    {
        return Message::create([
            'encrypted_text' => $text,
            'recipient'      => $recipient,
            'iv'             => $iv, // store IV as hex string
            'expires_at'     => $expiry_minutes,
        ]);
    }
    
    public function readMessage($message_id): Message
    {
        return Message::find($message_id);
    }
    

}
