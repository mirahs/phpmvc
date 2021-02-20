<?php
namespace core;


/** 制表符 **/
define('Tabs',                   "\t");
/** 换行符 **/
define('Newline',                "\r\n");


/**
 * 获取和设置配置参数 支持批量定义
 * @param string|array $name 配置变量
 * @param mixed $value 配置值
 * @param mixed $default 默认值
 * @return mixed
 */
function conf($name = null, $value = null, $default = null) {
    static $_config = [];
    // 无参数时获取所有
    if (empty($name)) return $_config;
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
 * 确保目录存在
 * @param string $path
 */
function path_sure($path) {
    if (!file_exists($path)) mkdir($path,0777,true);
}

/**
 * 获取完整uri http://xxx.com|https://xxx.com|http://xxx.com:88|https://xxx.com:88
 * @return string
 */
function uri_full() {
    $pageURL = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
    $pageURL .= $_SERVER['HTTP_HOST'];
    return $pageURL;
}

/**
 * 客户端IP
 * @return string
 */
function ip() {
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $hdr_ip = stripslashes($_SERVER['HTTP_CLIENT_IP']);
    } else {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $hdr_ip = stripslashes($_SERVER['HTTP_X_FORWARDED_FOR']);
        } else {
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
function url($url, $data) {
    $rs = [];
    if (is_array($data)) {
        foreach ($data as $key => $value) $rs[] = $key . '=' . urlencode($value);
    }
    return $url . (strstr('?', $url) ? '&' : '?') . implode('&', $rs);
}

/**
 * 页面跳转
 * @param string|array $link
 * @param string|boolean $top
 * @param string $note
 */
function gouri($link = '', $top = '', $note = '') {
    if (!$link || $top || $note) {
        $note = $note ? $note:(($top && strlen($top) > 6) ? $top : '');
        $top  = Tabs . (($top && ($top === true || $top == 'top' || $top == 1)) ? 'window.top.' : '');
        echo '<script type="text/javascript">' . Newline;
        if (is_array($link)) {
            $link[0] = $top . "location.href='{$link[0]}';" . Newline;
            $link[1] = $top . "location.href='{$link[1]}';" . Newline;
            $note = $note?$note:'点击“确定”继续操作  点击“取消” 中止操作';
            echo 'if(window.confirm(' . json_encode($note) . ')){' . Newline . $link[0] . '}else{' . Newline . $link[1] . '}' . Newline;
        } else {
            $replace = $top . "location.href='{$link}';" . Newline;
            echo $link ? ($note ? 'if(window.confirm(' . json_encode($note) . ')){' . Newline . $replace . '};' : $replace) : 'window.history.go(-1);' . Newline;
        }
        echo '</script>' . Newline;
    } else {
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
function msg($msg, $outer = true) {
    $rs = Newline . 'alert(' . json_encode($msg) . ');' . Newline;
    if ($outer) {
        $rs = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . Newline . '<script type="text/javascript">' . Newline . $rs . Newline . '</script>' . Newline;
    }
    return $rs;
}
