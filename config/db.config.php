<?php
/**
 * database sample
 * @author zhao.binyan
 * @since 2014-04-27
 */
$GLOBALS['mysql_db']['host']     = 'localhost';
$GLOBALS['mysql_db']['username'] = 'root';
$GLOBALS['mysql_db']['password'] = '123456';
$GLOBALS['mysql_db']['port']     = '3306';
$GLOBALS['mysql_db']['database'] = 'app_seemimi';


if (isset($_SERVER['HTTP_APPNAME'])) {
    $GLOBALS['mysql_db']['host']     = SAE_MYSQL_HOST_M;
    $GLOBALS['mysql_db']['username'] = SAE_MYSQL_USER;
    $GLOBALS['mysql_db']['password'] = SAE_MYSQL_PASS;
    $GLOBALS['mysql_db']['port']     = SAE_MYSQL_PORT;
    $GLOBALS['mysql_db']['database'] = 'app_seemimi';
}

