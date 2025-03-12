<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReadMessageRequest;
use App\Http\Requests\StoreMessageRequest;
use App\Repositories\Contracts\MessageRepositoryInterface;
use App\Services\EncryptionService;
use Carbon\Carbon;

class MessageController extends Controller
{
    protected $messageRepository;
    protected $encryption;
    public function __construct(MessageRepositoryInterface $messageRepository)
    {
        $this->messageRepository = $messageRepository;
        $this->encryption = new EncryptionService;
    }
    /**
     * Store a new encrypted message.
     *
     * Expected JSON payload:
     * {
     *    "text": "Your secret message",
     *    "recipient": "colleague@example.com",
     *    "expiry_minutes": 60
     * }
     */
    public function store(StoreMessageRequest $request)
    {   
        $text = $request['text'];
        $recipient = $request['recipient'];
        $expiryMinutes = $request['expiry_minutes'];

        // Encrypting Message
        $encryptionData = $this->encryption->encrypting($text);
        
        // Calculate expiration timestamp
        $expiresAt = Carbon::now()->addMinutes($expiryMinutes);
        
        // Save the encrypted message in the database.
        $message = $this->messageRepository->sendMessage(
            $encryptionData['encryptedMessage'],
            $recipient,
            $encryptionData['iv'],
            $expiresAt
        );

        return response()->json([
            'message_id'     => $message->id,
            'decryption_key' => $encryptionData['encryptionKey'],
            'expires_at'     => $expiresAt->toDateTimeString()
        ], 201);
    }

    /**
     * Read a message (read once).
     *
     * Expected JSON payload:
     * {
     *    "message_id": 1,
     *    "decryption_key": "the key provided earlier"
     * }
     */
    public function read(ReadMessageRequest $request)
    {
        $message = $this->messageRepository->readMessage($request['message_id']);

        // Check if the message has expired.
        if (Carbon::now()->greaterThan($message->expires_at)) {
            // delete the expired message.
            $message->delete();
            return response()->json(['error' => 'Message has expired.'], 410);
        }

        // Decrypting Message
        $decryptedText = $this->encryption->decrypting(
            $message->iv, 
            $request['decryption_key'], 
            $message->encrypted_text
        );


        if ($decryptedText === false) {
            return response()->json(['error' => 'Invalid decryption key.'], 403);
        }

        // Delete the message after it has been read.
        $message->delete();

        return response()->json([
            'text'      => $decryptedText,
            'recipient' => $message->recipient,
            'read_at'   => Carbon::now()->toDateTimeString()
        ], 200);
    }
}
