<?php

namespace Techive\Telebirr;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Telebirr
{
    protected array $config;

    public function __construct()
    {
        $this->config = config('telebirr');
    }

    /**
     * Creates a payment request based on the H5/SuperApp documentation.
     */
    public function createPayment(array $data): array
    {
        // 1. Prepare the business content payload
        $bizContent = $this->prepareBizContent($data);

        // 2. Encrypt the business content with Telebirr's Public Key
        $encryptedBizContent = $this->encrypt($bizContent);

        // 3. Prepare the main data array for signing
        $signContent = [
            "appid" => $this->config['app_id'],
            "method" => "telebirr.onlinePay",
            "nonce" => $data['nonce'],
            "notify_url" => $data['notifyUrl'],
            "out_trade_no" => $data['outTradeNo'],
            "timestamp" => now()->format('Y-m-d H:i:s'),
            "biz_content" => $encryptedBizContent,
        ];

        // 4. Create the RSA signature with your Private Key
        $signature = $this->sign($signContent);

        // 5. Build and send the final request
        $requestData = array_merge($signContent, [
            "sign_type" => "RSA2",
            "sign" => $signature,
            "version" => "1.0",
        ]);
        
        $response = Http::post($this->config['api_url'], $requestData);

        return $response->json();
    }

    protected function prepareBizContent(array $data): array
    {
        return [
            "out_trade_no" => $data['outTradeNo'],
            "subject" => "Payment for Modules",
            "total_amount" => $data['totalAmount'],
            "short_code" => $this->config['short_code'],
            "notify_url" => $data['notifyUrl'],
            "return_url" => $data['returnUrl'],
            "appid" => $this->config['merchant_id'], // Uses merchant_id here
            "timeout_express" => "30m",
        ];
    }

    /**
     * Creates a signature using your private key with SHA256withRSA.
     */
    private function sign(array $data): string
    {
        ksort($data);
        $stringToSign = collect($data)->map(fn($value, $key) => "{$key}={$value}")->implode('&');

        openssl_sign(
            $stringToSign,
            $signature,
            $this->config['private_key'],
            "sha256WithRSAEncryption"
        );

        return base64_encode($signature);
    }

    /**
     * Encrypts the payload for the 'biz_content' field using Telebirr's public key.
     */
    private function encrypt(array $data): string
    {
        $jsonPayload = json_encode($data);
        $formattedPublicKey = "-----BEGIN PUBLIC KEY-----\n" . wordwrap($this->config['public_key'], 64, "\n", true) . "\n-----END PUBLIC KEY-----";
        
        openssl_public_encrypt(
            $jsonPayload,
            $encrypted,
            $formattedPublicKey,
            OPENSSL_PKCS1_PADDING
        );

        return base64_encode($encrypted);
    }
}