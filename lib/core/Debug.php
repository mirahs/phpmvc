<?php
namespace core;


class Debug {
    /** 日志数据 */
    private $_logs = [];
    /** 输出文件名 */
    private $_filename;


    /**
     * Debug constructor.
     * @param string $filename
     */
    public function __construct($filename) {
        ob_start();
        register_shutdown_function([$this, 'callback']);

        $this->_filename = $filename ?: APP . '_debug.log';
    }


    /**
     * 添加日志
     * @param $key
     * @param $val
     */
    public function log($key, $val) {
        if ($key) {
            $this->_logs[ $key ] = $val;
        }
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
        $pathDir = ROOT_PATH . 'log/';
        path_sure($pathDir);
        $filename = $pathDir . $this->_filename;

        $logs = [
            'DATE'=> date("Y-m-d H:i:s"),
            'URL' => url_original($_SERVER['REQUEST_URI']),
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
