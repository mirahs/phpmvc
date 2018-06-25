<?php
namespace core;


/** 制表符 **/
define('Tabs',                   "\t");

/** 换行符 **/
define('Newline',                "\r\n");

/** 现在秒数 **/
define('Now_Time',               time());


/**
 * Convert special characters to HTML safe entities.
 *
 * @param string $string to encode
 * @return string
 */
function h($string)
{
	return htmlspecialchars($string, ENT_QUOTES, 'utf-8');
}

/**
 * 得到客户端IP
 *
 * @return string IP
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
 */
function url($url, $data)
{
	$rs = array();
	if (is_array($data))
	{
		foreach ($data as $key => $value)
        {
            $rs[] = $key.'='.urlencode($value);
        }
	}
	return $url.(strstr('?',$url)?'&':'?').implode('&',$rs);
}

/**
 * 得到 原生 URL(去问号后的 QUERY_STRING)
 */
function url_original($uri)
{
	$t = explode('?', $uri, 2);
	return $t[0];
}

/**
 * 通过uri得到mod
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
 * iE缓存控制
 *
 * @param int 		$expires		缓存时间 0:为不缓存 单位:s
 * @param string 	$etag			ETag
 * @param int 		$LastModified	最后更新时间
 */
function expires($expires=0, $etag='', $LastModified=0)
{
	if($expires)
	{
		header("Expires: " . gmdate("D, d M Y H:i:s", Now_Time + $expires) . " GMT");
		header("Cache-Control: max-age=" . $expires);
		$LastModified && header("Last-Modified: " . gmdate("D, d M Y H:i:s", $LastModified) . " GMT");
		if($etag)
		{
			if($etag == $_SERVER["HTTP_IF_NONE_MATCH"])
			{
				header("Etag: " . $etag, true, 304);
				exit();
			}
			else
			{
				header("Etag: " . $etag);
			}
		}
	}
	else
	{
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
	}
}

/**
 * 页面跳转
 *
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
 *
 * @param string $msg
 * @param boolean $outer
 * @return string
 */
function msg($msg, $outer = true)
{
	$rs = Newline . 'alert(' . Json_encode($msg) . ');' . Newline;
	if ($outer)
	{
		$rs = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . Newline . '<script type="text/javascript">' . Newline . $rs . Newline . '</script>' . Newline;
	}
	return $rs;
}
/**
 * Filter a valid UTF-8 string so that it contains only words, numbers,
 * dashes, underscores, periods, and spaces - all of which are safe
 * characters to use in file names, URI, XML, JSON, and (X)HTML.
 *
 * @param string $string to clean
 * @param bool $spaces TRUE to allow spaces
 * @return string
 */
function sanitize($string, $spaces = TRUE)
{
	$search = array(
			'/[^\w\-\. ]+/u',			// Remove non safe characters
			'/\s\s+/',					// Remove extra whitespace
			'/\.\.+/', '/--+/', '/__+/'	// Remove duplicate symbols
	);

	$string = preg_replace($search, array(' ', ' ', '.', '-', '_'), $string);

	if( ! $spaces)
	{
		$string = preg_replace('/--+/', '-', str_replace(' ', '-', $string));
	}

	return trim($string, '-._ ');
}


/**
 * Create a SEO friendly URL string from a valid UTF-8 string.
 *
 * @param string $string to filter
 * @return string
 */
function sanitize_url($string)
{
	return urlencode(mb_strtolower(sanitize($string, FALSE)));
}


/**
 * Filter a valid UTF-8 string to be file name safe.
 *
 * @param string $string to filter
 * @return string
 */
function sanitize_filename($string)
{
	return sanitize($string, FALSE);
}

/**
 * Encode a string so it is safe to pass through the URL
 *
 * @param string $string to encode
 * @return string
 */
function base64_url_encode($string = NULL)
{
	return strtr(base64_encode($string), '+/=', '-_~');
}


/**
 * Decode a string passed through the URL
 *
 * @param string $string to decode
 * @return string
 */
function base64_url_decode($string = NULL)
{
	return base64_decode(strtr($string, '-_~', '+/='));
}


/**
 * 基类的基类
 */
class Base
{
	/**
	 * 默认方法
	 */
	public $default_method = DEFAULT_METHOD;
	/**
	 * 没定的方法
	 * @param String $method
	 * @param String $arg
	 */
	public function __call($method, $arg)
	{
        if ($arg[1] && $method == $arg[1])
        {
            header('HTTP/1.1 404 Not Found');
            trigger_error("ERROR! Can't find {$method} in " . get_class($this), E_USER_ERROR);
        }
        else
        {
            $this->{$this->default_method}($arg[0], $method);
        }
	}
	
	/**
	 * DB静态数组
	 */
	private static $_db = array();
	/**
	 * 返回数据库连接对像
	 *
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
     * 调试 相关
     * @return \core\Debug
     */
    public $debug	= null;
	/**
	 * 调试日志
	 * @param string $k
	 * @param mixed $log
	 */
	public function logs($k, $log)
	{
		if ($this->debug)
		{
			$this->debug->logs($k, $log);
		}
	}
}

/**
 * 构造模块基类 *
 */
class Controller extends Base
{
	public function __construct($mod)
	{
		if (!$mod)
		{
			$mod = array($this->default_method);
		}

		$this->{$mod[0]}($mod);
	}
	
	/**
	 * Template句柄容器
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
 *
 * @param array $mod
 */
function start($mod)
{
	date_default_timezone_set('Asia/Shanghai');
	header('Server: Mochiweb');
	header('X-Powered-By: Mochiweb/Mirahs');

	if (is_array($mod) && $mod[0])
	{// 有app
		if ($mod[1])
		{// 有controller
			$app    = $mod[0];
			$Module = $mod[1];
            $filename = APP_PATH . $app . '/controller/' . $Module . '.php';
            array_shift($mod);
            if (file_exists($filename))
            {
                array_shift($mod);
            }
            else
            {// 默认controller
                $Module =  DEFAULT_CONTROLLER;
            }
		}
		else
		{// 默认controlle method
			$app    = $mod[0];
            $Module = DEFAULT_CONTROLLER;
			$mod    = [DEFAULT_METHOD];
		}
	}
	else
	{// 默认app controlle method
		$app    = DEFAULT_APP;
        $Module = DEFAULT_CONTROLLER;
		$mod    = [DEFAULT_METHOD];
	}

    // 自动加载应用文件
    $app_autoload_file = APP_PATH . $app . '/autoload.php';
    if (file_exists($app_autoload_file))
    {
        require_once $app_autoload_file;
    }
	define('VIEW_PATH', APP_PATH . $app . '/view/');
	// 初始化类
	$Module = "app\\{$app}\\controller\\" . $Module;
    new $Module($mod);
}
