<?php

class mysqli_conn {

    protected $_db = null;
    protected $_timeout = null;
    protected $_dsn = null;

    public function __construct($dsn, $timeout = null) {
        $this->_dsn = $dsn;
        if ($timeout !== null) {
            $this->_timeout = $timeout;
        } elseif (isset($this->_dsn['timeout'])) {
            $this->_timeout = $this->_dsn['timeout'];
        } elseif ($timeout = config::get('db.timeout')) {
            $this->_timeout = $timeout;
        }
    }

    protected function _connect() {
        if (!$this->_db) {
            $this->_db = mysqli_init();
            $this->_db->options(MYSQLI_OPT_CONNECT_TIMEOUT, 8);
            if ($this->_timeout) {
                $this->_db->options(MYSQL_OPT_READ_TIMEOUT, $this->_timeout);
                $this->_db->options(MYSQL_OPT_WRITE_TIMEOUT, $this->_timeout);
            }
            @$this->_db->real_connect($this->_dsn['host'], $this->_dsn['user'], $this->_dsn['pass'], $this->_dsn['database'], $this->_dsn['port']);
            if ($this->_db->connect_error) {
                $errno = $this->_db->connect_errno;
                $error = $this->_db->connect_error;
                $this->close();
                throw new except('Connect Error (' . $errno . ') ' . $error, $errno);
            }
            if (isset($this->_dsn['charset'])) {
                $this->_db->set_charset($this->_dsn['charset']);
            } elseif ($charset = config::get('db.charset')) {
                $this->_db->set_charset($charset);
            }
        }
    }

    public function queryAll($sql) {
        if ($result = $this->_query($sql)) {
            $rows = array();
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            $result->close();
            @mysqli_free_result($result);
        } else {
            $rows = false;
            $this->check();
        }
        return $rows;
    }

    public function queryOne($sql) {
        if ($result = $this->_query($sql)) {
            $row = $result->fetch_assoc();
            $result->close();
            @mysqli_free_result($result);
        } else {
            $row = false;
            $this->check();
        }
        return $row;
    }

    protected function _query($sql) {
        $this->_connect();
        $res = @$this->_db->query($sql);
        $this->check();
        return $res;
    }

    public function execute($sql) {
        $this->_connect();
        $res = @$this->_db->query($sql);
        $this->check();
        return $res;
    }

    public function insertId() {
        $res = @$this->_db->insert_id;
        $this->check();
        return $res;
    }

    public function close() {
        if ($this->_db) {
            @$this->_db->close();
            $this->_db = null;
        }
    }

    public function error() {
        if ($this->_db->errno) {
            return 'Mysql Error (' . $this->_db->errno . ') ' . $this->_db->error;
        }
        return null;
    }

    public function check() {
        if ($this->_db->errno) {
            $errno = $this->_db->errno;
            $error = $this->_db->error;
            $this->close();
            throw new except('Mysql Error (' . $errno . ') ' . $error, $errno);
        }
    }

}
