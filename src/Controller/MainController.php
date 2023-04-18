<?php

namespace App\Controller;

use App\FrameworkPasAPas\Controller\AbstractController;
use App\FrameworkPasAPas\Http\Response;

final class MainController extends AbstractController
{
    public function __invoke(): Response
    {
        return new Response('toto');
    }
}