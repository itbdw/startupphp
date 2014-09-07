<?php
/**
 * StartupPhp
 *
 * 规范：文件名全部小写，类名从顶级目录开始，驼峰式大写如 Model_Base
 *
 * 入口文件
 *
 * 自动加载规则示例 Model_Admin_User => model/admin/user.php => model/admin_user.php => model_admin_user.php
 * 控制器文件先行判断文件是否存在，即只会 Controller_test_demo => controller/test/demo.php
 *
 * @author zhao.binyan
 * @since  2014-01-11
 */

ini_set('display_errors', 'On');
error_reporting(E_ALL); //线上注释掉此行

define('ROOT', __DIR__);
define('DS', DIRECTORY_SEPARATOR);
include ROOT . DS . 'config' . DS . 'main.config.php';
include ROOT . DS . 'config' . DS . 'city.config.php';
include ROOT . DS . 'config' . DS . 'db.config.php';

function autoload_class($className) {
    $flow       = explode('_', $className);
    $flow_count = count($flow);
    $file       = '';
    //规则示例 Model_Admin_User => model/admin/user.php => model/admin_user.php => model_admin_user.php
    for ($i = 1; $i <= $flow_count; $i++) {
        $flow_dir  = array_slice($flow, 0, $flow_count - $i);
        $flow_file = array_slice($flow, $flow_count - $i);

        $dir = implode(DS, $flow_dir);
        if ($dir != '') {
            $dir .= DS;
        }

        $file = strtolower(ROOT . DS . $dir . implode('_', $flow_file) . '.php');

        if (file_exists($file)) {
            include $file;
            return;
        }
    }

    //执行到这儿说明没找到
    trigger_error(404 . ' | ' . $file . ' not found!');
    exit('unable to load ' . $className);
}

spl_autoload_register('autoload_class');

/**
 * Class Core
 */
class  Core {
    public function __construct() {
    }

    public function index() {
    }

    public function dispatch() {
        $controller = !empty($_REQUEST[DEFAULT_CONTROLLER_NAME]) ? trim($_REQUEST[DEFAULT_CONTROLLER_NAME]) :
            DEFAULT_CONTROLLER;
        $action     = !empty($_REQUEST[DEFAULT_ACTION_NAME]) ? trim($_REQUEST[DEFAULT_ACTION_NAME]) : DEFAULT_ACTION;

        $controller = 'controller_' . $controller;
        $controller = str_replace('_', ' ', $controller);
        $controller = ucwords($controller);
        $controller = str_replace(' ', '_', $controller);
        $controller = str_replace('..', '', $controller);
        $file       = strtolower(ROOT . DS . str_replace('_', DS, $controller) . '.php');

        if (!file_exists($file) || !is_readable($file)) {
            $controller = 'controller_base';
            $action     = 'errorPage';
            trigger_error($file . ' not found at init');
        }

        $controller = new $controller();
        if (!method_exists($controller, $action)) {
            $action = 'errorPage';
        }

        $controller->$action();
    }
}

$core = new Core();
$core->dispatch();
$time_spend = sprintf('%.2f', time() - $_SERVER['REQUEST_TIME']);
if ($time_spend > 3) {
    echo "<br />\n 执行 $time_spend 秒了";
}
