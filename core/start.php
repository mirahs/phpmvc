<?php
// 启动session
session_start();

require_once 'Core.php';


define('DEFAULT_APP',           'home');
define('DEFAULT_CONTROLLER',    'Index');
define('DEFAULT_METHOD',        'Index');


$uri = core\url_original($_SERVER['REQUEST_URI']);
$mod = core\url_to_mod($uri);

core\start($mod);
