<?php
namespace app\common;


class Debug {
    /** 日志数据 */
    private $_logs = [];
    /** 调试文件 */
    private $_filename;


    /**
     * Debug constructor.
     * @param string $filename 调试文件
     */
    public function __construct($filename) {
        $this->_filename = $filename ?: APP . '_debug.log';

        ob_start();
        register_shutdown_function([$this, 'callback']);
    }


    /**
     * 添加日志
     * @param $key
     * @param $val
     */
    public function log($key, $val) {
        if ($key) $this->_logs[ $key ] = $val;
    }


    /**
     * 内部回调
     */
    public function callback() {
        $buffer = ob_get_contents();
        ob_clean();
        ob_implicit_flush(true);
        //if ('api' === APP) $this->log('buffer', $buffer);
        $this->write();
        exit($buffer);
    }


    /**
     * 日志写入文件
     */
    private function write() {
        $filename = PATH_LOG . $this->_filename;

        $logs = [
            'DATE'=> date("Y-m-d H:i:s"),
            'URL' => \core\url_original($_SERVER['REQUEST_URI']),
        ];

        if ($_GET) $logs['GET'] = $_GET;
        if ($_POST) $logs['POST'] = $_POST;
        if ($this->_logs) $logs['LOGS'] = $this->_logs;

        $this->_logs = null;

        $content = "------------------" . APP . '/' . CONTROLLER . '/' . METHOD . "------------------\n" . var_export($logs, true) . "\n\n";

        $file = fopen($filename, 'a');
        fwrite($file, $content);
        fclose($file);
    }
}
