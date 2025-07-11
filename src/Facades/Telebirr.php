<?php

namespace Techive\Telebirr\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array sendPaymentRequest(float $amount, string $nonce, string $outTradeNo, string $subject)
 * @method static bool verifyNotification(array $data)
 *
 * @see \Techive\Telebirr\Telebirr
 */
class Telebirr extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'telebirr';
    }
}