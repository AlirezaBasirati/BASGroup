<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReadMessageRequest;
use App\Http\Requests\StoreMessageRequest;
use App\Models\Message;
use Carbon\Carbon;

class MessageController extends Controller
{
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
        
        // Generate a random 32-byte key (for AES-256) and represent it as hex.
        $encryptionKey = bin2hex(random_bytes(32));
        
        // Generate a random IV (initialization vector) for AES-256-CBC
        $ivLength = openssl_cipher_iv_length('AES-256-CBC');
        $iv = random_bytes($ivLength);
        
        // Encrypt the message text using the generated key and IV.
        // Note: We convert the key back from hex to binary.
        $encryptedText = openssl_encrypt($text, 'AES-256-CBC', hex2bin($encryptionKey), 0, $iv);
        
        // Calculate expiration timestamp
        $expiresAt = Carbon::now()->addMinutes($expiryMinutes);
        
        // Save the encrypted message in the database.
        $message = Message::create([
            'encrypted_text' => $encryptedText,
            'recipient'      => $recipient,
            'iv'             => bin2hex($iv), // store IV as hex string
            'expires_at'     => $expiresAt,
        ]);
        
        return response()->json([
            'message_id'     => $message->id,
            'decryption_key' => $encryptionKey,
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
        $message = Message::find($request['message_id']);
        
        // Check if the message has expired.
        if (Carbon::now()->greaterThan($message->expires_at)) {
            // delete the expired message.
            $message->delete();
            return response()->json(['error' => 'Message has expired.'], 410);
        }
        
        // Convert stored IV (hex) back to binary.
        $iv = hex2bin($message->iv);
        // Convert provided decryption key from hex to binary.
        $decryptionKey = hex2bin($request['decryption_key']);
        
        // Attempt to decrypt the message.
        $decryptedText = openssl_decrypt($message->encrypted_text, 'AES-256-CBC', $decryptionKey, 0, $iv);
        
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
