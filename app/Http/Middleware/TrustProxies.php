<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * Local dev sau Traefik/Docker: trust tất cả proxies.
     * Production: thay bằng mảng IP/CIDR cụ thể, vd: ['10.0.0.0/8', '172.16.0.0/12'].
     */
    protected $proxies = '*';

    /**
     * Header mask khuyến nghị cho Laravel 10/11 (Symfony 6+).
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
