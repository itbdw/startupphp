<?php

/**
 *
 * @author zhao.binyan
 * @since 2014-01-11
 */
class Controller_Index extends Controller_Base {

    public function index() {

//        var_dump($this->output);
        $ip       = $_SERVER['REMOTE_ADDR'];
        $ipope    = new Library_Ip();
        $location = $ipope->getAddr($ip);
//        var_dump($location);
        $this->output['location'] = $location;

        $city['province'] = $GLOBALS['CONF_PROVINCE'];
        $city['city']     = $GLOBALS['CONF_CITY'];
        $city['county']   = $GLOBALS['CONF_COUNTY'];

        $this->output['city'] = json_encode($city);

        $tmp['name']    = '项目1';
        $tmp['percent'] = '10%';
        $tmp['flag']    = '不错';

        $list[] = $tmp;
        $list[] = $tmp;

        $this->output['list']  = $list;
        $this->output['hello'] = 'this is hello world!';
        $this->display('welcome.php');
    }

    /**
     * 测试mysqli类，线上请删除
     */
    public function testMysqli() {
        $db = new Library_Mysqli('localhost', 'root', '123456');
        $db->select_db('startupphp');

        $where = array('name' => 'sds');

        $ret = $db->update_array('test', array('hello' => 2434), $where);
        var_dump($ret);

        $ret = $db->delete_array('test', array('id' => '2434'));
        var_dump($ret);

        $ret = $db->find_array('test');
        var_dump($ret);

        $count = $db->find_count('test', $where);
        var_dump($count);

    }
}