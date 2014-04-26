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
class Library_Mysqli extends Mysqli{
    protected $handle;
    protected $host;
    protected $username;
    protected $password;
    protected $port;
    protected $database;

    public function __construct($host, $username=null, $password=null, $port=null) {
        parent::__sonstruct($host, $username, $password, $port);
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->port = $port;
    }

    public function select_db($db) {
        $this->database = $db;
        parent::select_db($db);
    }

    /**
     * @param string $table
     * @param array  $array
     * @return bool
     */
    public function insert_array($table='', $array=array()) {
        $array = $this->safe_query($array);

        $fields = implode(',', array_keys($array));
        $values = implode(',', array_values($array));

        $sql = "INSERT INTO $table ($fields) VALUES ($values)";
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
    public function find_array($table='', $array=array(), $fields_array=array(), $sort=array(), $limit=20, $offset=0) {

        $where='';
        $fields = '*';
        $order = '';
        $array = $this->safe_query($array);
        if ($array) {
            $where .= 'WHERE';
            foreach ($array as $k=>$v) {
                $where .= "$k = $v";
            }
        }

        if ($fields_array) {
            $fields = implode(',', $fields_array);
        }

        if ($sort) {
            $order .= ' ORDER BY ';
            foreach ($sort as $k=>$v) {
                $order .= "$k $v,";
            }
            $order .= substr($order, 0, strlen($order) - 1);
        }

        $sql = "SELECT $fields FROM $table $where $order LIMIT $limit OFFSET $offset";
        return $this->find_sql($sql);
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
        echo $sql;
        return $tmp;
    }

    /**
     * @param $sql
     * @return bool
     */
    public function execute_sql($sql) {
        echo $sql;
        return parent::real_query($sql);
    }

    /**
     * prevent sql injection
     * @param $array
     * @return mixed
     */
    public function safe_query($array) {
        foreach ($array as $k=>&$v) {
            $v = $this->safe_word($v);
        }
        return $array;
    }

    public function safe_word($word) {
        if (!is_numeric($word)) {
            $word = "'".$this->real_escape_string($word)."'";
        }
        return $word;
    }
}