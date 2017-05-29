<?php

/**
 *  This file is part of the Symfony Theatre application.
 *
 * (c) Christian Otter <phnixdev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use App\Env;
use App\AppCache;
use App\AppKernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require __DIR__.'/../app/autoload.php';

$request = Request::createFromGlobals();

$env = new Env($request);
$debug = $env->isDebug();

if ($debug) {
    Debug::enable();
}

$kernel = new AppKernel($env->getEnv(), $debug);
$kernel->loadClassCache();

if ($env->shouldCache()) {
    $kernel = new AppCache($kernel);
}

$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
