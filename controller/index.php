<?php
/**
 * 
 * @author zhao.binyan
 * @since 2014-01-11
 */
class Controller_Index extends Controller_Base {

    public function index() {
        $tmp['name'] = '项目1';
        $tmp['percent'] = '10%';
        $tmp['flag'] = '不错';

        $list[] = $tmp;
        $list[] = $tmp;

        $this->output['list'] = $list;
        $this->output['hello'] = 'this is hello world!';
        $this->display('welcome.php');
    }
}