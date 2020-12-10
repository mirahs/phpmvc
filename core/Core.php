<?php
namespace core;


/**
 * 得到原始 uri
 * @example /admin/Index/Index.html?a=111&b=222 => /admin/Index/Index.html
 * @param string $uri $_SERVER['REQUEST_URI']
 * @return string
 */
function url_original($uri) {
    $t = explode('?', $uri, 2);
    return $t[0];
}

/**
 * 通过 uri 得到 mod
 * @example /admin/Index/Index.html => [admin, Index, Index]
 * @param string $uri
 * @param string $root
 * @return array
 */
function url_to_mod($uri, $root = '/') {
    $uri 	= explode($root, $uri, 2);
    $uri 	= explode('.', urldecode($uri[1]), 2);
    $uri	= explode('/', $uri[0]);

    $mod	= [];
    foreach ($uri as $v) $v !== '' && $mod[] = $v;
    return $mod;
}


class Controller {
    public function __construct($method) {
        $method = $method ?: DEFAULT_METHOD;
        define('METHOD', $method);
        $this->{$method}();
    }

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
     * 模板赋值
     * @param string $name
     * @param mixed $value
     */
    public function assign($name, $value) {
        $this->_template();
        $this->_tpl->assign($name, $value);
    }
    /**
     * 模板输出
     * @param string $filename
     */
    public function import($filename = '') {
        $this->_template();
        $this->_tpl->import($filename);
    }
    /** @var \Tpl  */
    protected $_tpl = null;
    private function _template() {
        if ($this->_tpl) return;

        require_once 'Tpl.php';
        $this->_tpl = new \Tpl(PATH_VIEW);
    }
}


/**
 * 世界从这里开始
 * @param array $mod
 */
function start($mod) {
    if (empty($mod) || !is_array($mod)) {
        //header('Location:' . url_original($_SERVER['REQUEST_URI']) . DEFAULT_APP . '/' . DEFAULT_CONTROLLER . '/' . DEFAULT_METHOD . '.html');
        header('Location:' . uri_full() . '/' . DEFAULT_APP . '/' . DEFAULT_CONTROLLER . '/' . DEFAULT_METHOD . '.html');
        exit;
    }

    $method = null;
    if ($mod[1]) {
        $app        = $mod[0];
        $controller = $mod[1];
        $filename   = PATH_APP . $app . '/controller/' . $controller . '.php';
        if (file_exists($filename)) {
            if ($mod[2]) $method = $mod[2];
        } else {
            $controller = DEFAULT_CONTROLLER;
            $method     = $mod[1];
        }
    } else {
        $app        = $mod[0];
        $controller = DEFAULT_CONTROLLER;
        $method     = DEFAULT_METHOD;
    }

    define('APP',       $app);
    define('CONTROLLER',$controller);

    // 自动加载全局文件
    $autoload_file = PATH_APP . 'autoload.php';
    if (file_exists($autoload_file)) {
        /** @noinspection PhpIncludeInspection */
        require_once $autoload_file;
    }
    // 自动加载应用文件
    $app_autoload_file = PATH_APP . $app . '/autoload.php';
    if (file_exists($app_autoload_file)) {
        /** @noinspection PhpIncludeInspection */
        require_once $app_autoload_file;
    }

    // 模板路径
    define('PATH_VIEW', PATH_APP . $app . '/view/');

    $Module = "app\\{$app}\\controller\\{$controller}";
    new $Module($method);
}
