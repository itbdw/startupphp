<?php
/**
 * request 类
 *
 * @author zhao.binyan
 * @since  2014-04-27
 */
class Library_Request {
    public $get;
    public $post;
    public $request;
    public $timeout = 4;

    public function __construct() {
        $this->get     = $_GET;
        $this->post    = $_POST;
        $this->request = $_REQUEST;
    }

    public function getGet($name = '', $default = '') {
        return isset($this->get[$name]) ? $this->get[$name] : $default;
    }

    public function getPost($name = '', $default = '') {
        return isset($this->post[$name]) ? $this->post[$name] : $default;
    }

    public function getRequest($name = '', $default = '') {
        return isset($this->request[$name]) ? $this->request[$name] : $default;
    }

    /**
     * Send a POST requst using cURL
     *
     * @param string $url     to request
     * @param array  $post    values to send
     * @param array  $options for cURL
     * @return string
     */
    public function postData($url, $post = null, array $options = array()) {
        $defaults = array(
            CURLOPT_POST           => true,
            CURLOPT_HEADER         => false,
            CURLOPT_URL            => $url,
            CURLOPT_FRESH_CONNECT  => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FORBID_REUSE   => true,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_POSTFIELDS     => $post
        );

        $ch = curl_init();
        curl_setopt_array($ch, ($options + $defaults));
        if (!$result = curl_exec($ch)) {
            trigger_error(curl_error($ch));
            trigger_error("post data to $url failed");
        }
        curl_close($ch);
        return $result;
    }
}