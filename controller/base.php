<?php
/**
 *
 * @author zhao.binyan
 * @since  2014-01-11
 */
class Controller_Base {
    /**
     * 输出数据共享容器
     *
     * @var
     */
    public $output;

    public function __construct() {
        $this->preventDirectInstance();
        $base                            = $_SERVER['SCRIPT_NAME'];
        $this->output['static_base_url'] = rtrim(dirname($base), '/');
    }

    /**
     * prevent base instance
     */
    public function preventDirectInstance() {
        $ret = is_subclass_of($this, __CLASS__);
        if (!$ret) {
            //die('This page is not allowed to display!');
        }
    }

    /**
     * 调用模板页面
     */
    public function display($path) {
        $path = ROOT . DS . 'view' . DS . ltrim($path, '/');
        if (file_exists($path)) {
            extract($this->output); //覆盖现有变量
            include $path;
        } else {
            trigger_error(404 . '|' . $path . ' not found!');
        }
    }

    /**
     * 输出 json 数据
     */
    public function displayJson($exit = true, $option = 0) {
        if (defined('JSON_UNESCAPED_UNICODE')) {
            $option = JSON_UNESCAPED_UNICODE;
        }
        echo json_encode($this->output, $option);
        if ($exit) {
            exit();
        }
    }

    public function errorPage($code = 404, $msg = 'Page is not found', $content = 'This page is not found! 您可以选择截图给开发人员。或者要不您回退一下浏览器吧，这真是一个悲伤的故事……') {

        $this->output['code']    = $code;
        $this->output['msg']     = $msg;
        $this->output['content'] = $content;
        $this->display('404.php');
    }
}
