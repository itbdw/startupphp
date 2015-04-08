<?php
/**
 * 智能MySQLi类，继承原生 MySQLi
 *
 * $mysql = Library_Mysqli::getInstance($host, $username, $password, $dbname, $port);
 *
 * 没做复杂封装，方便简单使用，同时继承原生 MySQLi 全部功能
 *
 * 根据个人喜好使用即可
 *
 * 示例
 * $mysql = Library_Mysqli::getInstance($host, $username, $password, $dbname, $port, true);
 * $mysql->maxTolerateTime = '0.0001';
 * $ret   = $mysql->find('deploy_log', array('uid >='=>'17', 'time_start>='=>1397554060));
 *
 * @property Library_Mysqli[] $instances
 * @author zhao.binyan
 * @since  2015-04-08
 */
class Library_Mysqli extends Mysqli {
    private static $instances = array();
    private $host;
    private $username;
    private $password;
    private $dbname;
    private $port;

    public $maxTolerateTime = 1.5;

    final public function __construct($host, $username, $password, $dbname, $port) {
        parent::__construct($host, $username, $password, $dbname, $port);
        $this->host     = $host;
        $this->username = $username;
        $this->password = $password;
        $this->dbname   = $dbname;
        $this->port     = $port;
//        trigger_error('init resourse ' . $this->host . ':' . $this->port . ':' . $this->dbname);
    }

    /**
     * @param      $host
     * @param      $username
     * @param      $password
     * @param      $dbname
     * @param      $port
     * @param bool $persistent
     * @return Library_Mysqli
     */
    public function getInstance($host, $username, $password, $dbname, $port, $persistent = false) {
        if ($persistent) {
            $host = 'p:' . $host;
        }
        $key = $host . $dbname . $port;
        if (empty(self::$instances[$key])
            || !(self::$instances[$key] instanceof self)
            || !self::$instances[$key]->ping()
        ) {
            self::$instances[$key] = new Base_Helper_Mysqli($host, $username, $password, $dbname, $port);

            if (self::$instances[$key]->connect_errno) {
                $logerror = sprintf("Mysqli Connect Error, Host:%s, Error:%s", $host . ':' . $port . ':' . $dbname, self::$instances[$key]->connect_errno . ' ' . self::$instances[$key]->connect_error);
                trigger_error($logerror, E_USER_WARNING);
            }

            self::$instances[$key]->real_query("SET NAMES UTF8");
        }
        return self::$instances[$key];
    }

    /**
     * @param string $sql
     * @return bool|mysqli_result
     */
    public function query($sql) {
        $time   = microtime(true);
        $result = parent::query($sql);
        $this->logError($sql, microtime(true) - $time);
        return $result;
    }

    /**
     *
     * @param $sql
     * @return bool
     */
    public function real_query($sql) {
        $time   = microtime(true);
        $result = parent::real_query($sql);
        $this->logError($sql, microtime(true) - $time);
        return $result;
    }

    /**
     * @param string $table
     * @param array  $data
     * @return bool
     */
    public function insert($table = '', $data = array()) {
        $array  = $this->safeQuery($data);
        $fields = implode(',', array_keys($array));
        $values = implode(',', array_values($array));
        $sql    = "INSERT INTO $table ($fields) VALUES ($values)";
        return $this->real_query($sql);
    }

    /**
     * @param string $table
     * @param array  $update
     * @param array  $where
     * @return bool
     */
    public function update($table = '', $update = array(), $where = array()) {
        $update = $this->safeQuery($update);
        $where  = $this->safeQuery($where);
        $update = $this->parseCondition($update);
        $where  = $this->parseCondition($where);
        if ($update == '') {
            return false;
        }
        //REPLACE 'WHERE' TO 'SET'
        $update = 'SET ' . mb_substr($update, 6);
        $sql    = "UPDATE $table $update $where";
        return $this->real_query($sql);
    }

    /**
     * 依赖MySQL的索引、约束，需要强烈配合MySQL，依靠PHP是在高并发环境下是不可信的
     *
     * @param string $table
     * @param array  $data
     * @param array  $preseve_columns 这些字段在冲突时不会更新
     * @return bool
     */
    public function upsert($table = '', $data = array(), $preseve_columns = array()) {
        $array  = $this->safeQuery($data);
        $fields = implode(',', array_keys($array));
        $values = implode(',', array_values($array));

        foreach ($preseve_columns as $val) {
            unset($array[$val]);
        }

        $update = $this->parseCondition($array);
        $update = mb_substr($update, 6);
        $sql    = "INSERT INTO $table ($fields) VALUES ($values) ON DUPLICATE KEY UPDATE $update";
        return $this->real_query($sql);
    }

    /**
     * @param string $table
     * @param array  $where
     * @return bool
     */
    public function delete($table = '', $where = array()) {
        $array = $this->safeQuery($where);
        $where = $this->parseCondition($array);
        if ($where == '') {
            return false;
        }
        $sql = "DELETE FROM $table $where ";
        return $this->real_query($sql);
    }

    /**
     * @param string $table
     * @param array  $where
     * @param array  $fields_array
     * @param array  $sort
     * @param int    $limit
     * @param int    $offset
     * @return array
     */
    public function find($table = '', $where = array(), $fields_array = array(), $sort = array(), $limit = 20, $offset = 0) {
        $array  = $this->safeQuery($where);
        $fields = '*';
        $order  = '';
        $where  = $this->parseCondition($array);
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
        return $this->findBySql($sql);
    }

    /**
     * @param string $table
     * @param array  $where
     * @return array
     */
    public function findCount($table = '', $where = array()) {
        $array = $this->safeQuery($where);
        $where = $this->parseCondition($array);
        $sql   = "SELECT count(*) as count FROM $table $where";
        $tmp   = $this->findBySql($sql);
        return $tmp[0]['count'];
    }

    /**
     * @param $sql
     * @return array
     */
    public function findBySql($sql) {
        $tmp = array();
        if ($result = $this->query($sql)) {
            while ($cache = $result->fetch_assoc()) {
                $tmp[] = $cache;
            }
            $result->free();
        }
        return $tmp;
    }

    /**
     * 过滤危险数组value
     *
     * @param $array
     * @return mixed
     */
    public function safeQuery($array) {
        foreach ($array as &$v) {
            $v = $this->safeWord($v);
        }
        return $array;
    }

    /**
     * wrap everything as string except number
     *
     * @param $word
     * @return string
     */
    public function safeWord($word) {
        if (!is_numeric($word)) {
            $word = "'" . $this->real_escape_string($word) . "'";
        }
        return $word;
    }

    /**
     * need escape first
     *
     * @param array $array
     * @return string
     */
    public function parseCondition($array = array()) {
        $where = '';
        if ($array) {
            $where .= 'WHERE ';
            foreach ($array as $k => $v) {
                $op = preg_replace('/^\w+/', '', $k);
                $k  = str_replace($op, '', $k);
                $op = trim($op);
                $op = $op ? $op : '=';
                $where .= "`$k` $op $v";
                $where .= ' AND ';
            }
            $where = substr($where, 0, -5);
        }
        return $where;
    }

    /**
     * @param string $db
     * @return bool|void
     */
    public function select_db($db) {
        $this->dbname = $db;
        parent::select_db($db);
    }

    /**
     * @param $sql
     * @param $timespend
     */
    public function logError($sql, $timespend) {

        if ($this->errno) {
            $exeuse_error = sprintf("Mysqli Execuse Error, Host:%s, Error:%s, Sql:%s", $this->host . ':' . $this->port . ':' . $this->dbname, $this->errno . ' ' . $this->error . ' ' . $this->sqlstate, $sql);

            trigger_error($exeuse_error, E_USER_WARNING);
        }

        if ($timespend > $this->maxTolerateTime) {
            $timespend_error = sprintf("Mysqli Execuse Time Is Too Long, Host:%s, Realtime:%s, Toterate Time:%s, Sql:%s", $this->host . ':' . $this->port . ':' . $this->dbname, $timespend, $this->maxTolerateTime, $sql);
            trigger_error($timespend_error, E_USER_WARNING);
        }
    }

    /**
     *
     */
    public function __destruct() {
        $this->close();
//        trigger_error('clear resourse ' . $this->host . ':' . $this->port . ':' . $this->dbname);
    }
}
