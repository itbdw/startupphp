<?php

/**
 *
 * @author zhao.binyan
 * @since 2014-04-27
 */
class Library_Request {
    public $get;
    public $post;
    public $request;

    public function __construct() {
        $this->get     = $_GET;
        $this->post    = $_POST;
        $this->request = $_REQUEST;
    }

    public function get_data($name = '', $default = '') {
        return isset($this->get[$name]) ? $this->get[$name] : $default;
    }

    public function post_data($name = '', $default = '') {
        return isset($this->post[$name]) ? $this->post[$name] : $default;
    }

    public function request_data($name = '', $default = '') {
        return isset($this->request[$name]) ? $this->request[$name] : $default;
    }


}