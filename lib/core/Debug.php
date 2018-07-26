<?php 
namespace core;


class Debug 
{	
	/** 日志数组 */
	private $_logs 	   = array();
	/** 输出文件名 */
	private $_filename;

    /**
     * Debug constructor.
     * @param string $filename
     */
	public function __construct($filename)
	{
		ob_start();
		register_shutdown_function(array($this, 'callback'));
		
		$this->_filename = $filename ?: APP . '_debug.log';
	}
	/**
	 * 调试日志
	 */
	public function log($k, $log)
	{
		if ($k && $log)
		{
			$this->_logs[$k] = $log;
		}
	}
	
	/**
	 * 内部内调
	 */
	public function callback()
	{
		$buffer     = ob_get_contents();
		ob_clean();
		ob_implicit_flush(true);
        if ('api' === APP) $this->log('buffer', $buffer);
        $this->write();
		exit($buffer);
	}


	/**
	 * 日志写入文件
	 */
	private function write()
	{
		$filename = ROOT_PATH . $this->_filename;
        $logs = array(
            'DATE'=> date("Y-m-d H:i:s"),
            'URL' => url_original($_SERVER['REQUEST_URI']),
        );

		if ($_GET) $logs['GET'] = $_GET;
		if ($_POST) $logs['POST'] = $_POST;
		if ($this->_logs) $logs['LOGS'] = $this->_logs;
        $this->_logs = null;

        $content = "------------------" . MODULE . '/' . MOD . "------------------\n" . var_export($logs, true) . "\n\n";

        $file = fopen($filename, 'a');
        fwrite($file, $content);
        fclose($file);
	}
}
