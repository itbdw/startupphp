<?php

/**
 * A Mysqli Class Extends From Mysqli
 *
 * You can use the origin mysqli method as you wish
 * DO mind the sql injection if you write sql by yourself
 *
 * @author zhao.binyan
 * @since 2014-04-26
 */
class Library_Mysqli extends Mysqli {
    protected $database;

    /**
     * @param string $host
     * @param null   $username
     * @param null   $password
     * @param null   $dbname
     * @param null   $port
     * @param null   $socket
     */
    public function __construct($host, $username = null, $password = null, $dbname=null, $port = null, $socket = null) {
        parent::__construct($host, $username, $password, $dbname, $port, $socket);
    }

    public function select_db($db) {
        parent::select_db($db);
    }

    /**
     * @param string $table
     * @param array  $array
     * @return bool
     */
    public function insert_array($table = '', $array = array()) {
        $array = $this->safe_query($array);

        $fields = implode(',', array_keys($array));
        $values = implode(',', array_values($array));

        $sql = "INSERT INTO $table ($fields) VALUES ($values)";
        return $this->execute_sql($sql);
    }

    /**
     * @param string $table
     * @param array  $update
     * @param array  $where
     * @return bool
     */
    public function update_array($table = '', $update = array(), $where = array()) {

        $update = $this->safe_query($update);
        $where  = $this->safe_query($where);

        $update = $this->parse_condition($update);
        $where  = $this->parse_condition($where);
        if ($update == '') {
            return false;
        }

        //REPLACE 'WHERE' TO 'SET'
        $update = ' SET ' . mb_substr($update, 6);
        $sql    = "UPDATE $table $update $where";
        return $this->execute_sql($sql);
    }

    /**
     * @param string $table
     * @param array  $array
     * @return bool
     */
    public function delete_array($table = '', $array = array()) {
        $array = $this->safe_query($array);
        $where = $this->parse_condition($array);
        if ($where == '') {
            return false;
        }
        $sql = "DELETE FROM $table $where ";
        return $this->execute_sql($sql);
    }

    /**
     * @param string $table
     * @param array  $array
     * @param array  $fields_array
     * @param array  $sort
     * @param int    $limit
     * @param int    $offset
     * @return array
     */
    public function find_array($table = '', $array = array(), $fields_array = array(), $sort = array(), $limit = 20, $offset = 0) {
        $array = $this->safe_query($array);

        $fields = '*';
        $order  = '';
        $where  = $this->parse_condition($array);

        if ($fields_array) {
            $fields = implode(',', $fields_array);
        }

        if ($sort) {
            $order .= ' ORDER BY ';
            foreach ($sort as $k => $v) {
                $order .= "$k $v,";
            }
            $order = substr($order, 0, strlen($order) - 1);
        }

        $sql = "SELECT $fields FROM $table $where $order LIMIT $limit OFFSET $offset";
        return $this->find_sql($sql);
    }

    /**
     * @param string $table
     * @param array  $array
     * @return array
     */
    public function find_count($table = '', $array = array()) {
        $array = $this->safe_query($array);
        $where = $this->parse_condition($array);

        $sql = "SELECT count(*) as count FROM $table $where";
        $tmp = $this->find_sql($sql);
        return $tmp[0]['count'];
    }

    /**
     * @param $sql
     * @return array
     */
    public function find_sql($sql) {
        $tmp = array();
        if ($result = self::query($sql)) {
            while ($cache = $result->fetch_assoc()) {
                $tmp[] = $cache;
            }
            $result->free();
        }
        return $tmp;
    }

    /**
     * @param $sql
     * @return bool
     */
    public function execute_sql($sql) {
        return parent::real_query($sql);
    }

    /**
     * @param $array
     * @return mixed
     */
    public function safe_query($array) {
        foreach ($array as $k => &$v) {
            $v = $this->safe_word($v);
        }
        return $array;
    }

    /**
     * wrap a everything as a string except number
     *
     * @param $word
     * @return string
     */
    public function safe_word($word) {
        if (!is_numeric($word)) {
            $word = "'" . $this->real_escape_string($word) . "'";
        }
        return $word;
    }

    /**
     * need escape first
     * @param array $array
     * @return stringb
     */
    public function parse_condition($array = array()) {
        $where = '';
        if ($array) {
            $where .= ' WHERE ';
            foreach ($array as $k => $v) {
                $where .= "$k = $v";
            }
        }
        return $where;
    }
}