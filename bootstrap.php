<?php
// composer 自动加载
require_once __DIR__ . '/vendor/autoload.php';


error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_STRICT ^ E_DEPRECATED);

// 调试模式
if (APP_DEBUG) {
    ini_set('display_errors','On');

    $whoops = new Whoops\Run();
    // 命令行模式
    if ('cli' === PHP_SAPI) {
        $whoops->pushHandler(new Whoops\Handler\PlainTextHandler());
    } else {
        $whoops->pushHandler(new Whoops\Handler\PrettyPageHandler());
    }
    $whoops->register();
} else {
    ini_set('display_errors','Off');
    ini_set('log_errors', 'On');
    ini_set('error_log', PATH_LOG . 'php_errors.log');
}
ini_set('date.timezone','Asia/Shanghai');

// 加载框架引导文件
require_once __DIR__ . '/core/start.php';
