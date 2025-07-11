<?php

namespace Techive\Telebirr;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Telebirr
{
    protected array $config;
    protected Client $client;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = new Client(['base_uri' => $this->config['api_url']]);
    }

    /**
     * Create a payment request and get the redirect URL.
     *
     * @param float $amount
     * @param string $nonce
     * @param string $outTradeNo
     * @param string $subject
     * @return array
     * @throws GuzzleException
     */
    public function sendPaymentRequest(float $amount, string $nonce, string $outTradeNo, string $subject): array
    {
        $payload = $this->preparePayload($amount, $nonce, $outTradeNo, $subject);
        $encryptedPayload = $this->encrypt($payload);
        $signature = $this->sign($payload);

        $requestData = [
            'appid' => $this->config['app_id'],
            'sign' => $signature,
            'ussd' => $encryptedPayload,
        ];

        $response = $this->client->post('', ['json' => $requestData]);
        
        return json_decode($response->getBody()->getContents(), true);
    }
    
    /**
     * Verify the signature of a notification from Telebirr.
     *
     * @param array $data The notification data.
     * @return bool
     */
    public function verifyNotification(array $data): bool
    {
        if (!isset($data['sign'])) {
            return false;
        }

        $signature = $data['sign'];
        unset($data['sign']);
        
        // Per Telebirr docs, sort keys alphabetically to create the string to sign
        ksort($data);

        $stringToSign = '';
        foreach ($data as $key => $value) {
            if ($value !== '' && !is_null($value)) {
                $stringToSign .= $key . '=' . $value . '&';
            }
        }
        $stringToSign = rtrim($stringToSign, '&');

        $publicKey = "-----BEGIN PUBLIC KEY-----\n" . wordwrap($this->config['public_key'], 64, "\n", true) . "\n-----END PUBLIC KEY-----";
        $key = openssl_get_publickey($publicKey);
        
        return openssl_verify($stringToSign, base64_decode($signature), $key, OPENSSL_ALGO_SHA256) === 1;
    }


    private function preparePayload(float $amount, string $nonce, string $outTradeNo, string $subject): array
    {
        return [
            'appId' => $this->config['app_id'],
            'appKey' => $this->config['app_key'],
            'nonce' => $nonce,
            'notifyUrl' => $this->config['notify_url'],
            'outTradeNo' => $outTradeNo,
            'receiveName' => $this->config['receive_name'],
            'returnUrl' => $this->config['return_url'],
            'shortCode' => $this->config['short_code'],
            'subject' => $subject,
            'timeoutExpress' => $this->config['timeout_express'],
            'timestamp' => round(microtime(true) * 1000),
            'totalAmount' => number_format($amount, 2, '.', ''),
        ];
    }

    private function sign(array $data): string
    {
        ksort($data);
        $stringToSign = '';
        foreach ($data as $key => $value) {
             if ($value !== '' && !is_null($value)) {
                $stringToSign .= $key . '=' . $value . '&';
            }
        }
        $stringToSign = rtrim($stringToSign, '&');

        return hash('sha256', $stringToSign);
    }

    private function encrypt(array $data): string
    {
        $jsonPayload = json_encode($data);
        $publicKey = "-----BEGIN PUBLIC KEY-----\n" . wordwrap($this->config['public_key'], 64, "\n", true) . "\n-----END PUBLIC KEY-----";
        $encrypted = '';
        openssl_public_encrypt($jsonPayload, $encrypted, $publicKey, OPENSSL_PKCS1_PADDING);

        return base64_encode($encrypted);
    }
}