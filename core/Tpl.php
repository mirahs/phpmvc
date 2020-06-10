<?php
class Tpl {
    /** 模板驱动名 */
    private $_drive_name = 'PhpTemplate';
    /** 驱动实例 */
    private static $_drive;


    /**
     * 创建一个模板对像
     * @param string $template_dir
     * @param string $drive
     */
    public function __construct($template_dir, $drive = '', $temp_dir = '', $cache_lifetime = '', $template_filename = '') {
        $drive || $drive = $this->_drive_name;
        $filename = __DIR__ . '/' . "{$drive}.php";
        if (file_exists($filename)) {
            require_once $filename;
            self::$_drive = new $drive($template_dir, $temp_dir, $cache_lifetime, $template_filename);
        } else {
            trigger_error("出错:找不到{$drive}模板驱动", E_USER_ERROR);
            exit();
        }
    }

    public function __call($method, $arg) {
        if (method_exists(self::$_drive, $method)) {
            self::$_drive->$method($arg[0], $arg[1]);
        } else {
            echo 'Error: Drive not method "' . $method . '"';
        }
    }


    /**
     * Assign Template Content
     *
     * Usage Example:
     * $page->assign('TITLE',     'My Document Title');
     * $page->assign('userlist',  [
     *                                 ['ID' => 123,  'NAME' => 'John Doe'],
     *                                 ['ID' => 124,  'NAME' => 'Jack Doe'],
     *                             ];
     *
     * @access public
     * @param string $name Parameter Name
     * @param mixed $value Parameter Value
     * @desc Assign Template Content
     */
    public function assign($name, $value = '') {
        self::$_drive->assign($name, $value);
    }
    /**11 0     * Assign Template Content
     *
     * Usage Example:
     * $page->append('userlist', ['ID' => 123,  'NAME' => 'John Doe']);
     * $page->append('userlist',  ['ID' => 124,  'NAME' => 'Jack Doe']);
     *
     * @access public
     * @param string $name Parameter Name
     * @param mixed $value Parameter Value
     * @desc Assign Template Content
     */
    public function append($name, $value) {
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
    public function output($_top = []) {
        self::$_drive->output($_top);
    }

    public static function import($filename) {
        $filename = $filename ?: CONTROLLER . '/' . METHOD . '.php';
        self::$_drive->import($filename);
    }
}
