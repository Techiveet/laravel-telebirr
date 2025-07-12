<?php

namespace Techive\Telebirr\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array sendRequest(array $data)
 * @method static array|null decrypt(string $encryptedData)
 * @method static bool verify(array $decryptedData)
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