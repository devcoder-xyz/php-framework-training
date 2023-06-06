<?php

namespace App\Controller;

use App\FrameworkPasAPas\Controller\AbstractController;
use App\FrameworkPasAPas\Http\Response;
use App\FrameworkPasAPas\Renderer\PHPRenderer;

final class MainController extends AbstractController
{

    private PHPRenderer $PHPRenderer;

    public function __construct(PHPRenderer $PHPRenderer)
    {
        $this->PHPRenderer = $PHPRenderer;
    }

    public function __invoke(): Response
    {
        if (!isset($_SESSION['user'])) {
            return new Response('', 403);
        }
        $content = $this->PHPRenderer->render('main.php');
        return new Response($content);
    }
}