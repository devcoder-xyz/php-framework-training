<?php

use App\FrameworkPasAPas\DotEnv;
use App\FrameworkPasAPas\Http\Request;
use App\FrameworkPasAPas\Kernel;

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

(new DotEnv(dirname(__DIR__) . '/.env'))->load();

$kernel = new Kernel(getenv('APP_ENV'));
$response = $kernel->handle(Request::fromGlobals());
$response->send();