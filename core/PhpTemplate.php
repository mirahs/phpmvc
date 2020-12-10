<?php
class PhpTemplate {
    /** 模板文件目录 */
    private $_template_dir;
    /** 数据缓存 */
    private $_data = [];


    /**
     * PhpTemplate constructor.
     * @param string $TemplateDir
     */
    public function __construct($TemplateDir = null) {
        $TemplateDir && $this->_template_dir = $TemplateDir;
    }


    public function assign($name, $value) {
        if (is_array($name)) {
            foreach ($name as $k => $v) {
                $this->_data[ $k ] = $v;
            }
        } else {
            $this->_data[ $name ] = $value;
        }
    }

    public function append($name, $value) {
        if (is_array($value)) {
            $this->_data[ $name ][] = $value;
        } elseif (!is_array($this->_data[ $name ])) {
            $this->_data[ $name ] .= $value;
        }
    }

    public function import($tpl) {
        extract($this->_data);

        $file_name  = $this->_template_dir . $tpl;
        if (file_exists($file_name)) {
            require_once $file_name;
        } else {
            echo '<strong style="color:#F30">Can\'t find Template:' . $tpl . '</strong>';
        }
    }

    public function output($tpl, $vars = []) {
        extract(array_merge_recursive($this->_data, $vars));

        $file_name  = $this->_template_dir . $tpl;
        if (file_exists($file_name)) {
            require_once $file_name;
        } else {
            echo '<strong style="color:#F30">Can\'t find Template:' . $tpl . '</strong>';
        }
    }
}
