<?php

/**
 *
 * @author zhao.binyan
 * @since 2014-01-11
 */
class Model_Base {

    public function __construct() {

    }

    public function getMysql($db_name) {
        if (isset($GLOBALS[$db_name])) {
            $db_config = $GLOBALS[$db_name];
            $handle = new Library_Mysqli($db_config['host'], $db_config['username'], $db_config['password'], $db_config['database'], $db_config['port']);
            if ($handle->connect_errno) {
                trigger_error('mysql_connect_error:'.$handle->connect_errno.$handle->connect_error);
            }

            $handle->set_charset('utf8');

            return $handle;
        } else {
            trigger_error('could not connect to database ' . $db_name);
        }
    }
}