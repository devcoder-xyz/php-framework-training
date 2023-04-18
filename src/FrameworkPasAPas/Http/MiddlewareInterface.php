<?php

namespace App\FrameworkPasAPas\Http;


interface MiddlewareInterface
{
    public function process(Request $request): ?Response;
}