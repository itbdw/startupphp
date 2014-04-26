<?php
/**
 * 
 * @author zhao.binyan
 * @since 2014-01-11
 */
class Controller_Base {
    /**
     * 输出数据共享容器
     * @var
     */
    public $output;

    public function __construct() {
        $base = $_SERVER['SCRIPT_NAME'];
        $this->output['static_base_url'] = dirname($base);
    }

    /**
     * 调用模板页面
     */
    public function display($path) {
        $path = ROOT . DS . 'view' . DS . ltrim($path, '/');
        if (file_exists($path)) {
            extract($this->output);//覆盖现有变量
            include $path;
        } else {
            trigger_error(404 . '|' . $path . ' not found!');
        }
    }

    /**
     * 输出 json 数据
     */
    public function displayJson($exit=true, $option=0) {
        if (defined('JSON_UNESCAPED_UNICODE')) {
            $option = JSON_UNESCAPED_UNICODE;
        }
        echo json_encode($this->output, $option);
        if ($exit) {
            exit();
        }
    }

    public function errorPage($code=404,$msg='error found') {
        die("error_found: $code: $msg");
    }
}