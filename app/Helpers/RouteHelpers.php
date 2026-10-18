<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

class RouteHelpers
{
    static function domainRemap($sub)
    {
        return $sub . config('app.subdomain_base');
    }

    static function externalRoute($sub, $name, $options = [])
    {

        $domain = request()->getHost();

        $host = substr($domain, strpos($domain, '.') + 1);


        if ($domain == config('app.subdomain_base')) {
            return route($name, $options);
        }

        $parts = parse_url(route($name, $options));



        return Request::getScheme() . "://" . $sub . '.' . $host . ($parts['path'] ?? '/');
    }
}
