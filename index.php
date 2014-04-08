<?php
/**
 * StartupPhp
 *
 * 规范：文件名全部小写，类名从顶级目录开始，驼峰式大写如 Model_Base
 *
 * 入口文件
 * @author zhao.binyan
 * @since 2014-01-11
 */

ini_set('display_errors', 'On');
error_reporting(E_ALL);//线上注释掉此行

define('ROOT', __DIR__);
define('DS', DIRECTORY_SEPARATOR);
include ROOT . DS . 'config' . DS . 'main.config.php';
include ROOT . DS . 'config' . DS . 'city.config.php';

function autoload_class($className) {
    $dir = substr($className, 0, stripos('_', $className));
    $file = strtolower(ROOT . DS . str_replace('_', DS, $className) . '.php');

    if (file_exists($file)) {
        include $file;
    } else {
        trigger_error(404 . '|' .$dir . 'not found');
    }
}
spl_autoload_register('autoload_class');

/**
 * Class Core
 */
class  Core {
    public function __construct() {
        $this->filter();
    }

    public function index() {

    }

    public function dispatch() {
        $controller = !empty($_REQUEST[DEFAULT_CONTROLLER_NAME]) ? trim($_REQUEST[DEFAULT_CONTROLLER_NAME]) :
            DEFAULT_CONTROLLER;
        $action = !empty($_REQUEST[DEFAULT_ACTION_NAME]) ? trim($_REQUEST[DEFAULT_ACTION_NAME]) : DEFAULT_ACTION;

        $controller =  'controller_' . $controller;
        $controller = str_replace('_', ' ', $controller);
        $controller = ucwords($controller);
        $controller = str_replace(' ', '_', $controller);
        $controller = new $controller();
        if (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            trigger_error(404 . '|' . $action . 'not found');
        }
    }
}

$core = new Core();
$core->dispatch();
$time_spend = sprintf('%.2f', time() - $_SERVER['REQUEST_TIME']);
if ($time_spend > 3) {
    echo "<br />\n 执行 $time_spend 秒了";
}
