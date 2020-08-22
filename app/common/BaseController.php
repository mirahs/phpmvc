<?php
namespace app\common;

use core\Controller;
use core\Debug;


class BaseController extends Controller {
    /**
     * 调试工具
     * @var $_debug Debug
     */
    private $_debug = null;
    /**
     * 开启调试
     * @param string $filename
     */
    public function debug($filename = '') {
        $this->_debug = new Debug($filename);
    }
    /**
     * 记录日志
     * @param string $key
     * @param mixed $val
     */
    public function log($key, $val = '') {
        $this->_debug || $this->debug();
        $this->_debug->log($key, $val);
    }
}
