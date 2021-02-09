<?php
// 是否调试
define('APP_DEBUG',     true);

// 根目录
define('PATH_ROOT',     __DIR__ . '/../');
// 应用目录
define('PATH_APP',      PATH_ROOT . 'app/');
// 运行时目录
define('PATH_RUNTIME',  PATH_ROOT . 'runtime/');
// 日志目录
define('PATH_LOG',      PATH_RUNTIME . 'log/');

// 默认应用
define('DEFAULT_APP',           'home');
// 默认控制器
define('DEFAULT_CONTROLLER',    'Index');
// 默认方法
define('DEFAULT_METHOD',        'Index');


// 加载启动文件
require_once __DIR__ . '/../bootstrap.php';
