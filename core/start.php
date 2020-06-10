<?php
require_once 'functions.php';
require_once 'Core.php';


// 默认应用
defined('DEFAULT_APP') or define('DEFAULT_APP', 'home');
// 默认控制器
defined('DEFAULT_CONTROLLER') or define('DEFAULT_CONTROLLER', 'Index');
// 默认方法
defined('DEFAULT_METHOD') or define('DEFAULT_METHOD', 'Index');


// 命令行模式
if ('cli' === PHP_SAPI) {
    // php index.php app/controller/method
    $mod = $argv[1];
    $mod = explode('/', $mod);
} else {
    $uri = \core\url_original($_SERVER['REQUEST_URI']);
    $mod = \core\url_to_mod($uri);
}

\core\start($mod);
