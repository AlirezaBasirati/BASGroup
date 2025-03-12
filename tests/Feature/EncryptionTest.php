<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Repositories\Eloquent\MessageRepository;
use App\Services\EncryptionService;
use Tests\TestCase;
use Carbon\Carbon;


class EncryptionTest extends TestCase
{

    /**
     * A test that check total functionality of sending secrect messsage.
     */
    public function testUserCanSendMessage()
    {
        $response = $this->Json('POST', '/api/message', [
            "text" => "This Is Text For BAS-WORLD",
            "recipient" => "Alireza",
            "expiry_minutes" => 1
        ], []);

        $response->assertStatus(201);
    }


    /**
     * A test that check total functionality of reading secrect messsage.
     */

    public function testUserCanReadMessage()
    {
        $text = "This Message Sent For Test";
        $recipient = "Some One";
        $expiryMinutes = 1;

        $encryption = new EncryptionService();
        $encryptionData = $encryption->encrypting($text);


        $expiresAt = Carbon::now()->addMinutes($expiryMinutes);


        $messageRepository = new MessageRepository();
        $message = $messageRepository->sendMessage(
            $encryptionData['encryptedMessage'],
            $recipient,
            $encryptionData['iv'],
            $expiresAt
        );
        $response = $this->Json('POST', '/api/message/read', [
            "message_id" => $message->id,
            "decryption_key" => $encryptionData['encryptionKey']
        ], []);

        $response->assertStatus(200);
    }
}
