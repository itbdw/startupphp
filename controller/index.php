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

    public function testDB() {
        $db = new Model_Base();
        $handle = $db->getMysql('mysql_db');
        $ret = $handle->insert_array('seemimi_post', array('title'=>time(), 'content'=>'ss'));
        var_dump($ret);
        $ret = $handle->find_array('seemimi_post');
        var_dump($ret);
    }
}