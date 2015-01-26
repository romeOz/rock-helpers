<?php
use rock\base\Alias;

$composerAutoload = dirname(__DIR__) . '/vendor/autoload.php';
if (is_file($composerAutoload)) {
    /** @var \Composer\Autoload\ClassLoader $loader */
    $loader = require($composerAutoload);
}

$loader->addPsr4('rockunit\\', __DIR__);

Alias::setAlias('tests', __DIR__);
Alias::setAlias('rockunit', __DIR__);
Alias::setAlias('runtime', '@tests/runtime');

require(dirname(__DIR__) . '/src/polyfills.php');
$_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'] = 'site.com';
$_SERVER['REQUEST_URI'] = '/';
date_default_timezone_set('UTC');

define('ROCKUNIT_RUNTIME', __DIR__ . '/runtime/');