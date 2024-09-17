<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;

class RouteHelpers
{
    static function domainRemap($sub)
    {
        $defaultDomain = env('APP_SUBDOMAIN_BASE');

        $domain = request()->getHost();


        $ADDITIONAL_DOMAINS = explode(",", env('APP_ADDITIONAL_DOMAINS'));

        $ADDITIONAL_DOMAINS = array_map(function ($val) use ($sub) {
            return $sub . $val;
        }, $ADDITIONAL_DOMAINS);



        if (in_array($domain, $ADDITIONAL_DOMAINS)) {
            return $domain;
        }

        return $sub . $defaultDomain;
    }

    static function externalRoute($sub, $name, $options = [])
    {

        $domain = request()->getHost();

        $host = substr($domain, strpos($domain, '.') + 1);

        $parts = parse_url(route($name, $options));



        return 'https://' . $sub . '.' . $host . ($parts['path'] ?? '');
    }
}
