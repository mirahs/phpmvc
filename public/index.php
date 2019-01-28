<?php
// public目录
define('APP_PUBLIC',  __DIR__ . '/');
// 应用目录
define('APP_PATH',  __DIR__ . '/../app/');
// 是否调试
define('APP_DEBUG', true);

// 默认应用
define('DEFAULT_APP',           'home');
// 默认控制器
define('DEFAULT_CONTROLLER',    'Index');
// 默认方法
define('DEFAULT_METHOD',        'Index');


// 加载启动文件
require_once __DIR__ . '/../bootstrap.php';
