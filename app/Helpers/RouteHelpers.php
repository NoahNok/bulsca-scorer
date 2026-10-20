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

    /**
     * A main site url that works from a subdomain. route() builds urls for routes without a domain
     * on the current host, so from live. they'd land on the subdomain's own catch-all routes.
     */
    static function mainRoute($name, $options = [])
    {
        $parts = parse_url(route($name, $options));

        return rtrim(config('app.url'), '/') . ($parts['path'] ?? '/') . (isset($parts['query']) ? '?' . $parts['query'] : '');
    }
}
