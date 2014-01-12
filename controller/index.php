<?php
/**
 * 
 * @author zhao.binyan
 * @since 2014-01-11
 */
class Controller_Index extends Controller_Base {

    public function index() {
        $ip = $_SERVER['REMOTE_ADDR'];
        $ipope = new Library_Ip();
        $location = $ipope->getAddr($ip);
        var_dump($location);

        $city['province'] = $_SERVER['$CONF_PROVINCE'];
        $city['city']     = $_SERVER['$CONF_CITY'];
        $city['county']   = $_SERVER['$CONF_COUNTY'];

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
}