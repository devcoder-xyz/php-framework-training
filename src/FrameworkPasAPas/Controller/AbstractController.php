<?php

namespace App\FrameworkPasAPas\Controller;

use App\FrameworkPasAPas\DependencyInjection\Container;

abstract class AbstractController
{
    private Container $container;

    public function setContainer(Container $container): void
    {
        $this->container = $container;
    }

    protected function get(string $id)
    {
        return $this->container->get($id);
    }
}