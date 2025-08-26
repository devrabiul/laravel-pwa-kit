<?php

namespace Devrabiul\PwaKit\Facades;

use Illuminate\Support\Facades\Facade;

class PwaKit extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'PwaKit';
    }
}
