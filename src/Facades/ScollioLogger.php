<?php

namespace Kz370\ScollioLogger\Facades;

use Illuminate\Support\Facades\Facade;

class ScollioLogger extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'scollio-logger';
    }
}
