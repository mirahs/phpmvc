<?php
namespace core;


/**
 * 模板类
 * @package core
 */
class Tpl
{
    /**
     * 模板驱动名
     */
    private $_drive_name = 'PhpTemplate';

    /**
     * 驱动实例
     */
    private static $_drive;

    /**
     * 创建一个模板对像
     * @param string $template_dir
     * @param string $drive
     */
    public function __construct($template_dir, $drive = '', $temp_dir='', $cache_lifetime='', $template_filename = '')
    {
        $drive || $drive = $this->_drive_name;
        $filename 		 = CORE_PATH . "{$drive}.php";
        if (file_exists($filename))
        {
            require_once $filename;
            self::$_drive = new $drive($template_dir, $temp_dir, $cache_lifetime, $template_filename);
        }
        else
        {
            trigger_error("出错:找不到{$drive}模板驱动", E_USER_ERROR);
            exit();
        }
    }
    

    public function __call($name, $arg)
    {
    	if (method_exists(self::$_drive, $name))
    	{
    		self::$_drive->$name($arg[0], $arg[1]);
    	}
    	else
    	{
    		echo 'Error: Drive not method "' . $name . '"';
    	}
    }
    /**
     * Assign Template Content
     *
     * Usage Example:
     * $page->assign( 'TITLE',     'My Document Title' );
     * $page->assign( 'userlist',  array(
     *                                 array( 'ID' => 123,  'NAME' => 'John Doe' ),
     *                                 array( 'ID' => 124,  'NAME' => 'Jack Doe' ),
     *                             );
     *
     * @access public
     * @param string $name Parameter Name
     * @param mixed $value Parameter Value
     * @desc Assign Template Content
     */
    public function assign($name, $value = '')
    {
        self::$_drive->assign($name, $value);
    }

    /**11 0     * Assign Template Content
     *
     * Usage Example:
     * $page->append( 'userlist',  array( 'ID' => 123,  'NAME' => 'John Doe' ) );
     * $page->append( 'userlist',  array( 'ID' => 124,  'NAME' => 'Jack Doe' ) );
     *
     * @access public
     * @param string $name Parameter Name
     * @param mixed $value Parameter Value
     * @desc Assign Template Content
     */
    public function append($name, $value)
    {
        self::$_drive->append($name, $value);
    }
    /**
     * Execute parsed Template
     * Prints Parsing Results to Standard Output
     *
     * @access public
     * @param array $_top Content Array
     * @desc Execute parsed Template
     */
    public function output($_top = '')
    {
        self::$_drive->output($_top);
    }
    
    public static function import($filename)
    {
        self::$_drive->import($filename);
    }    
}
