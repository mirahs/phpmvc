<?php
/**
 * 获取和设置配置参数 支持批量定义
 * @param string|array $name 配置变量
 * @param mixed $value 配置值
 * @param mixed $default 默认值
 * @return mixed
 */
function C($name=null, $value=null,$default=null) {
    static $_config = array();
    // 无参数时获取所有
    if (empty($name)) {
        return $_config;
    }
    // 优先执行设置获取或赋值
    if (is_string($name)) {
        if (!strpos($name, '.')) {
            $name = strtoupper($name);
            if (is_null($value))
                return isset($_config[$name]) ? $_config[$name] : $default;
            $_config[$name] = $value;
            return null;
        }
        // 二维数组设置和获取支持
        $name = explode('.', $name);
        $name[0]   =  strtoupper($name[0]);
        if (is_null($value))
            return isset($_config[$name[0]][$name[1]]) ? $_config[$name[0]][$name[1]] : $default;
        $_config[$name[0]][$name[1]] = $value;
        return null;
    }
    // 批量设置
    if (is_array($name)){
        //$_config = array_merge($_config, array_change_key_case($name,CASE_UPPER));
        $_config = array_merge_recursive($_config, array_change_key_case($name,CASE_UPPER));
        return null;
    }
    return null; // 避免非法参数
}


/**
 * 数据库连接对像
 * @param mixed $key
 * @return \core\Mysql|null
 */
function DB($key=null)
{
    // DB静态数组
    static $_db = [];
    if (is_null($key)) return null;
    if (empty($_db[$key])) $_db[$key] = new core\Mysql(C('db.' . $key));
    $_db[$key]->active();
    return $_db[$key];
}

/**
 * 确保目录存在
 * @param string $path
 */
function PathSure($path)
{
    //$dir = iconv("UTF-8", "GBK", "Public/bookcover");
    if (!file_exists($path)) mkdir($path,0777,true);
}

/**
 * 日志记录
 * @param string $k
 * @param string $v
 * @param string $filename
 */
function LogMi($k = "", $v = "", $filename = '')
{
    $pathDir = APP_PUBLIC . 'logs/';
    PathSure($pathDir);
    $filename = $filename ?: 'center.log';
    $filename = $pathDir . $filename;
    $file = fopen($filename,'a');

    $content = '';
    $content .= $k." --- " . date("Y-m-d H:i:s") . " \n";
    $content .= json_encode($v) . " \n\n";

    fwrite($file,$content);
    fclose($file);
}

/**
 * 获取uri
 * @return string
 */
function GetUri()
{
    $pageURL = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
    if ($_SERVER["SERVER_PORT"] != "80")
    {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"];
    }
    else
    {
        $pageURL .= $_SERVER["SERVER_NAME"];
    }
    return $pageURL;
}
