<?php

namespace App\DigitalJudge;

use Illuminate\Support\Facades\Facade;

class DigitalJudgeFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'digitaljudge';
    }
}
