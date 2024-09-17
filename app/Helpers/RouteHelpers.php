<?php

namespace App\Helpers;

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

    static function isCustomHost(): bool
    {
        $domain = request()->getHost();

        $hostOnly = parse_url($domain, PHP_URL_HOST);

        $ADDITIONAL_DOMAINS = explode(",", env('APP_ADDITIONAL_DOMAINS'));

        return in_array($hostOnly, $ADDITIONAL_DOMAINS);
    }
}
