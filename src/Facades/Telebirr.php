<?php

namespace Techive\Telebirr\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array createPayment(array $data)
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