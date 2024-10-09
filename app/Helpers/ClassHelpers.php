<?php

namespace App\Helpers;

use App\Models\Competitor;

class ClassHelpers
{

    public static function castToClass($object, $final_class)
    {
        return unserialize(sprintf(
            'O:%d:"%s"%s',
            strlen($final_class),
            $final_class,
            strstr(strstr(serialize($object), '"'), ':')
        ));
    }
}
