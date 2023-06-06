<?php

use App\FrameworkPasAPas\DB\Query\Select;
use App\FrameworkPasAPas\DotEnv;
use App\FrameworkPasAPas\Http\Request;
use App\FrameworkPasAPas\Kernel;

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

(new DotEnv(dirname(__DIR__) . '/.env'))->load();


$kernel = new Kernel(getenv('APP_ENV'));
if (php_sapi_name() != 'cli') {
    $response = $kernel->handle(Request::fromGlobals());
//    $response = $kernel->handle(new Request($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES));
    $response->send();
}
