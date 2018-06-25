<?php
// 启动session
session_start();

require_once 'Core.php';


define('DEFAULT_APP',           'home');
define('DEFAULT_CONTROLLER',    'Index');
define('DEFAULT_METHOD',        'Index');


// 命令行模式
if ('cli' === PHP_SAPI)
{
    // php index.php app,controller,method
    $mod = $argv[1];
    $mod = explode(',', $mod);
}
else
{
    $uri = core\url_original($_SERVER['REQUEST_URI']);
    $mod = core\url_to_mod($uri);
}

core\start($mod);
