<?php

namespace App\Repositories\Contracts;

use App\Models\Message;
use Illuminate\Database\Eloquent\Collection;

interface MessageRepositoryInterface
{
    public function sendMessage($text, $recipient,$iv, $expiry_minutes): Message;
    public function readMessage($message_id): Message;
}
