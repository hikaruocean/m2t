<?php 

try {
    define('CONPOZ_PATH', realpath(__DIR__ . '/..'));
    define('APP_PATH', realpath(__DIR__ . '/../app'));
    define('CORE_PATH', realpath(__DIR__ . '/../core'));

    $config = require(APP_PATH . '/conf/config.php');
    require(APP_PATH . '/conf/envInit.php');
    require(CORE_PATH . '/include/autoLoader.php');
    require(CONPOZ_PATH . '/vendor/autoload.php');
    $bag = require(APP_PATH . '/conf/scriptService.php');
    $app = new \Conpoz\Core\Lib\Script\App($bag);
    $app->run($config, $argv);
} catch (\Exception $e) {
    die($e->getMessage());
}