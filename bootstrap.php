<?php
// 根目录
define('ROOT_PATH',     __DIR__ . '/');
// 框架核心目录
define('CORE_PATH',     __DIR__ . '/core/');


// composer插件加载
require_once ROOT_PATH . 'vendor/autoload.php';


if (APP_DEBUG)
{
    $whoops = new Whoops\Run();
    $whoops->pushHandler(new Whoops\Handler\PrettyPageHandler());
    $whoops->register();
}

// 加载框架引导文件
require_once __DIR__ . '/core/start.php';
