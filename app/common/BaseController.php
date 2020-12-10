<?php
namespace app\common;

use core\Controller;


class BaseController extends Controller {
    /** @var Debug 调试工具 */
    private $_debug = null;
    /**
     * 开启调试
     * @param string $filename 调试文件
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
