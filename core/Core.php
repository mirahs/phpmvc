<?php
namespace core;


/** 制表符 **/
define('Tabs',                   "\t");
/** 换行符 **/
define('Newline',                "\r\n");


/**
 * 客户端IP
 * @return string
 */
function ip()
{
	if (isset($_SERVER['HTTP_CLIENT_IP']))
	{
		$hdr_ip = stripslashes($_SERVER['HTTP_CLIENT_IP']);
	}
	else
	{
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$hdr_ip = stripslashes($_SERVER['HTTP_X_FORWARDED_FOR']);
		}
		else
		{
			$hdr_ip = stripslashes($_SERVER['REMOTE_ADDR']);
		}
	}
	return $hdr_ip;
}

/**
 * 输出URL
 * @param string $url
 * @param array $data
 * @return string
 */
function url($url, $data)
{
	$rs = array();
	if (is_array($data))
	{
		foreach ($data as $key => $value)
        {
            $rs[] = $key . '=' . urlencode($value);
        }
	}
	return $url . (strstr('?', $url) ? '&' : '?') . implode('&', $rs);
}

/**
 * 得到原生URL(去问号后的QUERY_STRING)
 * @param string $uri
 * @return string
 */
function url_original($uri)
{
	$t = explode('?', $uri, 2);
	return $t[0];
}

/**
 * 通过uri得到mod
 * @param string $uri
 * @param string $root
 * @return array
 */
function url_to_mod($uri, $root='/')
{
	$uri 	= explode($root, $uri, 					2);	
	$uri 	= explode('.', 	 urldecode($uri[1]),	2);
	$uri	= explode('/', 	 $uri[0]);	
	
	$mod	= array();
	foreach ($uri as $v) 
	{
		$v !== '' && $mod[] = $v;
	}
	return $mod;
}

/**
 * 页面跳转
 * @param string|array $link
 * @param string|boolean $top
 * @param string $note
 */
function gouri($link = '', $top = '', $note = '')
{
    if (!$link || $top || $note)
    {
        $note = $note ? $note:(($top && strlen($top) > 6) ? $top : '');
        $top  = Tabs . (($top && ($top === true || $top == 'top' || $top == 1)) ? 'window.top.' : '');
        echo '<script type="text/javascript">' . Newline;
        if (is_array($link))
        {
            $link[0] = $top . "location.href='{$link[0]}';" . Newline;
            $link[1] = $top . "location.href='{$link[1]}';" . Newline;
            $note = $note?$note:'点击“确定”继续操作  点击“取消” 中止操作';
            echo 'if(window.confirm(' . json_encode($note) . ')){' . Newline . $link[0] . '}else{' . Newline . $link[1] . '}' . Newline;
        }
        else
        {
            $replace = $top . "location.href='{$link}';" . Newline;
            echo $link ? ($note ? 'if(window.confirm(' . json_encode($note) . ')){' . Newline . $replace . '};' : $replace) : 'window.history.go(-1);' . Newline;
        }
        echo '</script>' . Newline;
    }
    else
    {
    	echo '<script type="text/javascript">location.href="' . $link . '"</script>';
    }
    exit();
}

/**
 * 弹出对话框
 * @param string $msg
 * @param boolean $outer
 * @return string
 */
function msg($msg, $outer=true)
{
	$rs = Newline . 'alert(' . json_encode($msg) . ');' . Newline;
	if ($outer)
	{
		$rs = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . Newline . '<script type="text/javascript">' . Newline . $rs . Newline . '</script>' . Newline;
	}
	return $rs;
}


class Base
{
	public function __call($method, $arg)
	{
        if (DEFAULT_METHOD == $method)
        {
            header('HTTP/1.1 404 Not Found');
            trigger_error("ERROR! Can't find {$method} in " . get_class($this), E_USER_ERROR);
        }
        else
        {
            define('METHOD', DEFAULT_METHOD);
            $this->{DEFAULT_METHOD}();
        }
	}

	/**
	 * DB静态数组
	 */
	private static $_db = array();
	/**
	 * 返回数据库连接对像
	 * @param string $key
	 * @return \core\Mysql
	 */
	public static function db($key)
	{
		self::$_db[$key] || self::$_db[$key] = new Mysql($GLOBALS['scfg']['db'][$key]);
		self::$_db[$key]->active();

		return self::$_db[$key];
	}
	
	/**
     * 调试相关
     * @var $debug Debug
     */
    public $debug	= null;
    /**
     * 开启日志
     * @param string $filename
     */
    public function debug($filename)
    {
        $this->debug = new Debug($filename);
    }
	/**
	 * 记录日志
	 * @param string $k
	 * @param mixed $log
	 */
	public function log($k, $log)
	{
		if ($this->debug)
		{
			$this->debug->log($k, $log);
		}
	}
}

class Controller extends Base
{
	public function __construct($method)
	{
	    $method = $method ?: DEFAULT_METHOD;
        define('METHOD', $method);

		$this->{$method}();
	}
	
	/**
	 * Template
	 * @var \core\Tpl
	 */
	protected $_stpl = null;
	public function Template()
	{
        require_once 'Tpl.php';
		$this->_stpl = new Tpl(VIEW_PATH);
	}
	/**
	 * 赋值
	 * @param string $name
	 * @param mixed $value
	 */
	public function assign($name, $value = '')
	{
		$this->_stpl->assign($name, $value);
	}
	/**
	 * 输出
	 * @param string $filename
	 */
	public function import($filename)
	{
		$this->_stpl->import($filename);
	}
}


/**
 * 世界从这里开始
 * @param array $mod
 */
function start($mod)
{
	date_default_timezone_set('Asia/Shanghai');
	header('Server: Mochiweb');
	header('X-Powered-By: Mochiweb/Mirahs');

    $method = null;
	if (is_array($mod) && $mod[0])
	{
		if ($mod[1])
		{
			$app    = $mod[0];
			$controller = $mod[1];
            $filename = APP_PATH . $app . '/controller/' . $controller . 'Controller.php';
            if (file_exists($filename))
            {
                if (!empty($mod[2])) $method = $mod[2];
            }
            else
            {
                $controller = DEFAULT_CONTROLLER;
                $method = $mod[1];
            }
		}
		else
		{
			$app    = $mod[0];
            $controller = DEFAULT_CONTROLLER;
            $method = DEFAULT_METHOD;
		}
	}
	else
	{
        header('Location:' . url_original($_SERVER['REQUEST_URI']) . DEFAULT_APP . '/' . DEFAULT_CONTROLLER . '/' . DEFAULT_METHOD . '.html');
        exit;
	}

	define('APP', $app);
    define('CONTROLLER', $controller);
    define('METHOD', $method);

    // 自动加载应用文件
    $app_autoload_file = APP_PATH . $app . '/autoload.php';
    if (file_exists($app_autoload_file)) require_once $app_autoload_file;

    // 模板路径
	define('VIEW_PATH', APP_PATH . $app . '/view/');

	$Module = "app\\{$app}\\controller\\" . $controller . 'Controller';
    new $Module($method);
}
