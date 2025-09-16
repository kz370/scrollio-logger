<?php

namespace Scollio\Facades;

use Illuminate\Support\Facades\Facade;

class Logger extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'scollio-logger';
    }
}
