<?php
namespace core;


/**
 * 得到原生URL(去问号后的QUERY_STRING)
 * @param string $uri
 * @return string
 */
function url_original($uri) {
    $t = explode('?', $uri, 2);
    return $t[0];
}

/**
 * 通过uri得到mod
 * @param string $uri
 * @param string $root
 * @return array
 */
function url_to_mod($uri, $root = '/') {
    $uri 	= explode($root, $uri, 					2);
    $uri 	= explode('.', 	 urldecode($uri[1]),	2);
    $uri	= explode('/', 	 $uri[0]);

    $mod	= [];
    foreach ($uri as $v) $v !== '' && $mod[] = $v;
    return $mod;
}


class Base {
    public function __call($method, $arg) {
        if (DEFAULT_METHOD == $method) {
            header('HTTP/1.1 404 Not Found');
            trigger_error("ERROR! Can't find {$method} in " . get_class($this), E_USER_ERROR);
        } else {
            define('METHOD', DEFAULT_METHOD);
            $this->{DEFAULT_METHOD}();
        }
    }


    /**
     * 调试相关
     * @var $debug Debug
     */
    private $_debug 	= null;
    /**
     * 开启日志
     * @param string $filename
     */
    private function debug($filename = '') {
        $this->_debug = new Debug($filename);
    }
    /**
     * 记录日志
     * @param string $key
     * @param mixed $val
     */
    public function log($key, $val) {
        $this->_debug || $this->debug();
        $this->_debug->log($key, $val);
    }
}

class Controller extends Base {
    public function __construct($method) {
        $method = $method ?: DEFAULT_METHOD;
        define('METHOD', $method);

        $this->{$method}();
    }


    /**
     * Template
     * @var \Tpl
     */
    protected $_tpl = null;
    public function Template() {
        require_once 'Tpl.php';
        $this->_tpl = new \Tpl(VIEW_PATH);
    }
    /**
     * 赋值
     * @param string $name
     * @param mixed $value
     */
    public function assign($name, $value = '') {
        $this->_tpl->assign($name, $value);
    }
    /**
     * 输出
     * @param string $filename
     */
    public function import($filename = '') {
        $this->_tpl->import($filename);
    }
}


/**
 * 世界从这里开始
 * @param array $mod
 */
function start($mod) {
    date_default_timezone_set('Asia/Shanghai');
    header('Server: mochiweb');
    header('X-Powered-By: mochiweb/mirahs');

    $method = null;
    if (is_array($mod) && $mod[0]) {
        if ($mod[1]) {
            $app = $mod[0];
            $controller = $mod[1];
            $filename = APP_PATH . $app . '/controller/' . $controller . '.php';
            if (file_exists($filename)) {
                if (!empty($mod[2])) $method = $mod[2];
            } else {
                $controller = DEFAULT_CONTROLLER;
                $method = $mod[1];
            }
        } else {
            $app = $mod[0];
            $controller = DEFAULT_CONTROLLER;
            $method = DEFAULT_METHOD;
        }
    } else {
        header('Location:' . url_original($_SERVER['REQUEST_URI']) . DEFAULT_APP . '/' . DEFAULT_CONTROLLER . '/' . DEFAULT_METHOD . '.html');
        exit;
    }

    define('APP', $app);
    define('CONTROLLER', $controller);
    define('METHOD', $method);

    // 自动加载全局文件
    $autoload_file = ROOT_PATH . 'autoload.php';
    if (file_exists($autoload_file)) require_once $autoload_file;
    // 自动加载应用文件
    $app_autoload_file = APP_PATH . $app . '/autoload.php';
    if (file_exists($app_autoload_file)) require_once $app_autoload_file;

    // 模板路径
    define('VIEW_PATH', APP_PATH . $app . '/view/');

    $Module = "app\\{$app}\\controller\\" . $controller;
    new $Module($method);
}
