<?php

namespace App\Services;

class EncryptionService
{
    public function encrypting($message): array
    {
        // Generate a random 32-byte key (for AES-256) and represent it as hex.
        $encryptionKey = bin2hex(random_bytes(32));

        // Generate a random IV (initialization vector) for AES-256-CBC
        $ivLength = openssl_cipher_iv_length('AES-256-CBC');
        $iv = random_bytes($ivLength);

        // Encrypt the message text using the generated key and IV.
        // Note: We convert the key back from hex to binary.
        $encryptedText = openssl_encrypt($message, 'AES-256-CBC', hex2bin($encryptionKey), 0, $iv);

        return [
            "iv" => bin2hex($iv),
            "encryptedMessage" => $encryptedText,
            "encryptionKey" => $encryptionKey
        ];
    }

    public function decrypting($iv, $decryption_key, $encrypted_text){
         // Convert stored IV (hex) back to binary.
         $iv = hex2bin($iv);
         // Convert provided decryption key from hex to binary.
         $decryptionKey = hex2bin($decryption_key);
 
         // Attempt to decrypt the message.
         $decryptedText = openssl_decrypt($encrypted_text, 'AES-256-CBC', $decryptionKey, 0, $iv);

         return $decryptedText;
    }
}
