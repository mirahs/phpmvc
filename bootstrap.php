<?php
error_reporting(E_ALL ^ E_DEPRECATED ^ E_STRICT ^ E_NOTICE ^ E_WARNING);

// 根目录
define('ROOT_PATH',     __DIR__ . '/');


// composer插件加载
require_once __DIR__ . '/vendor/autoload.php';


// 调试模式
if (APP_DEBUG) {
    $whoops = new Whoops\Run();
    // 命令行模式
    if ('cli' === PHP_SAPI) {
        $whoops->pushHandler(new Whoops\Handler\PlainTextHandler());
    } else {
        $whoops->pushHandler(new Whoops\Handler\PrettyPageHandler());
    }
    $whoops->register();
}

// 加载框架引导文件
require_once __DIR__ . '/core/start.php';
