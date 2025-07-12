<?php

namespace Techive\Telebirr;

use Illuminate\Support\Facades\Http;

class Telebirr
{
    protected string $apiUrl;
    protected string $appId;
    protected string $appKey;
    protected string $publicKey;

    public function __construct()
    {
        $this->apiUrl = config('telebirr.api_url');
        $this->appId = config('telebirr.app_id');
        $this->appKey = config('telebirr.app_key');

        // Get the raw public key string from the config
        $rawPublicKey = config('telebirr.public_key');

        // ✅ Format the raw key into a valid PEM format that openssl can read
        $this->publicKey = "-----BEGIN PUBLIC KEY-----\n" . wordwrap($rawPublicKey, 64, "\n", true) . "\n-----END PUBLIC KEY-----";
    }
    /**
     * ✅ RENAMED
     * Prepares and sends the initial payment request to Telebirr.
     */
    public function sendRequest(array $data): array
    {
        // 1. Prepare the full payload, including the appKey needed for signing
        $payloadWithKey = $this->preparePayload($data);

        // 2. Create the signature from the payload that includes the appKey
        $signature = $this->sign($payloadWithKey);

        // 3. Remove the appKey before encrypting the payload for the 'ussd' field
        $payloadForEncryption = $payloadWithKey;
        unset($payloadForEncryption['appKey']);
        $encryptedUssd = $this->encrypt($payloadForEncryption);

        // 4. Build the final request data
        $requestData = [
            'appid' => $this->appId,
            'sign' => $signature,
            'ussd' => $encryptedUssd,
        ];

        $response = Http::post($this->apiUrl, $requestData);

        return $response->json();
    }

    /**
     * ✅ RENAMED & CORRECTED
     * Verifies the signature of a decrypted notification from Telebirr.
     */
    public function verify(array $decryptedData): bool
    {
        $receivedSign = $decryptedData['sign'] ?? '';
        if (empty($receivedSign)) {
            return false;
        }

        unset($decryptedData['sign']);

        // Add the appKey back to verify the signature, as Telebirr includes it in their calculation
        $decryptedData['appKey'] = $this->appKey;

        // Re-sign the data we received
        $expectedSign = $this->sign($decryptedData);

        return $receivedSign === $expectedSign;
    }

    /**
     * Decrypts incoming webhook data from Telebirr.
     */
    public function decrypt(string $encryptedData): ?array
    {
        $decrypted = '';
        openssl_public_decrypt(
            base64_decode($encryptedData),
            $decrypted,
            $this->publicKey,
            OPENSSL_PKCS1_PADDING
        );

        return json_decode($decrypted, true);
    }

    private function preparePayload(array $data): array
    {
        return array_merge([
            'appId' => $this->appId,
            'appKey' => $this->appKey, // The appKey is needed for signing
            'nonce' => \Illuminate\Support\Str::random(16),
            'timestamp' => round(microtime(true) * 1000),
            'shortCode' => config('telebirr.short_code'),
            'timeoutExpress' => "30",
            'receiveName' => config('app.name', 'My Application'),
        ], $data);
    }

    /**
     * ✅ CORRECTED SIGNING METHOD
     * Creates a signature by encrypting the payload string with the appKey.
     */
    private function sign(array $data): string
    {
        ksort($data);
        $stringToSign = "";
        foreach ($data as $key => $value) {
            if ($value !== "" && !is_null($value)) {
                $stringToSign .= $key . "=" . $value . "&";
            }
        }
        $stringToSign = rtrim($stringToSign, '&');

        $encrypted = openssl_encrypt(
            $stringToSign,
            'aes-128-ecb',
            $this->appKey,
            OPENSSL_RAW_DATA
        );

        return base64_encode($encrypted);
    }

    /**
     * Encrypts the payload for the 'ussd' field using Telebirr's public key.
     */
    private function encrypt(array $data): string
    {
        $jsonPayload = json_encode($data);
        $encrypted = '';
        openssl_public_encrypt($jsonPayload, $encrypted, $this->publicKey, OPENSSL_PKCS1_PADDING);

        return base64_encode($encrypted);
    }
}
