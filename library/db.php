<?php

if (!defined('MYSQL_OPT_READ_TIMEOUT')) {
    define('MYSQL_OPT_READ_TIMEOUT', 11);
}
if (!defined('MYSQL_OPT_WRITE_TIMEOUT')) {
    define('MYSQL_OPT_WRITE_TIMEOUT', 12);
}

require_once 'mysqli_conn.php';

interface db_api {

    static function getByPk($table_name, $id);

    static function getByPks($table_name, array $ids, $order = array(), $page_size = 0, $page = 0);

    static function getWhere($table_name, $where, array $args = array(), $order = array(), $page_size = 0, $page = 0, $groupby = '');

    static function getOne($table_name, $where = null, array $args = array(), $order = array());

    static function getAll($table_name, $order = array(), $page_size = 0, $page = 0, $groupby = '');

    //static function get_in($table_name, $field_name, array $values, $order = array(), $page_size = 0,  $page = 0);

    static function getByPkForFields($table_name, $field_names, $id);

    static function getByPksForFields($table_name, $field_names, array $ids, $order = array(), $page_size = 0, $page = 0);

    static function getOneForFields($table_name, $field_names, $where = null, array $args = array(), $order = array());

    static function getAllForFields($table_name, $field_names, $order = array(), $page_size = 0, $page = 0, $groupby = '');

    static function getWhereForFields($table_name, $field_names, $where, array $args = array(), $order = array(), $page_size = 0, $page = 0, $groupby = '');

    static function getUnion($table_names, $field_names, $where, array $args = array(), $order = array(), $page_size = 0, $page = 0, $groupby = '');

    //static function get_in_for_fields(array $field_names, $table_name, $field_name, array $values, $order = array(), $page_size = 0,  $page = 0);

    static function countWhere($table_name, $where, array $args = array(), $groupby = '');

    static function countAll($table_name, $groupby = '');

    static function countUnion($table_names, $where, array $args = array(), $groupby = '');

    //static function count_in($table_name, $field_name, array $values);

    static function pageWhere($table_name, $where, array $args = array(), $order = array(), $page_size = 0, $page = 0, $groupby = '');

    static function pageAll($table_name, $order = array(), $page_size = 0, $page = 0, $groupby = '');

    static function pageWhereForFields($table_name, $field_name, $where, array $args = array(), $order = array(), $page_size = 0, $page = 0, $groupby = '');

    static function pageUnion($table_names, $field_name, $where, array $args = array(), $order = array(), $page_size = 0, $page = 0, $groupby = '');

    /*
      static function page_by_ids_with_count($record_count, $table_name, array $ids, $order = array(), $page_size = 0,  $page = 0);
      static function page_with_count($record_count, $table_name, array $keyvalues, $order = array(), $page_size = 0,  $page = 0);
      static function page_in_with_count($record_count, $table_name, $field_name, array $values, $order = array(), $page_size = 0,  $page = 0);
      static function pageAll_with_count($record_count, $table_name, $order = array(), $page_size = 0,  $page = 0);
      static function pageWhere_with_count($record_count, $table_name, $where, array $args = array(), $order = array(), $page_size = 0,  $page = 0);
     */

    static function setByPk($table_name, array $keyvalues, $id);

    static function setByPks($table_name, array $keyvalues, array $ids);

    static function setWhere($table_name, array $keyvalues, $where, array $args = array());

    static function setAll($table_name, array $keyvalues);

    static function set($table_name, array $keyvalues);

    static function add($table_name, array $keyvalues);

    static function addMany($table_name, array $keyvalues_list);

    static function delByPk($table_name, $id);

    static function delByPks($table_name, array $ids);

    static function delWhere($table_name, $where, array $args = array());

    //static function rep($table_name, array $keyvalues);
    //static function rep_many($table_name, array $keyvalues_list);

    /*
      static function inc_by_id($table_name, array $keyvalues, $id);
      static function inc_by_ids($table_name, array $keyvalues, array $ids);
      static function inc($table_name, array $keyvalues, array $conditions);
     */

    /*
      static function dec_by_id($table_name, array $keyvalues, $id);
      static function dec_by_ids($table_name, array $keyvalues, array $ids);
      static function dec($table_name, array $keyvalues, array $conditions);
     */

    /*
      static function fetch_all($table_name, $sql, array $args = array());
      static function fetch_one($table_name, $sql, array $args = array());
      static function modify($table_name, $sql, array $args = array());
      static function create($table_name, $sql, array $args = array());
      static function remove($table_name, $sql, array $args = array());
      static function change($table_name, $sql, array $args = array());
     */

    //static function update(/* ... */);
    //static function insert(/* ... */);
    //static function delete(/* ... */);
    //static function replace(/* ... */);

    static function queryAll($sql, $table_name = '*');

    static function queryOne($sql, $table_name = '*');

    static function execute($sql, $table_name = '*');

    static function closeAll();
}

class db implements db_api {

    protected static $_db = null;
    protected static $_timeout = null;
    protected static $_table_name = null;
    protected static $_primary = array();
    protected static $_identity = null;
    protected static $_tables = array();
    protected static $_auto_close = false;

    protected static function _steupTable($table_name, $master = true) {
        $_table_name = $table_name;
        if (strpos($table_name, '.')) {
            list($_schema, $_table_name) = explode('.', $table_name, 2);
        }
        self::$_table_name = '`' . $_table_name . '`';
        if (!isset(self::$_tables[$table_name])) {
            $tables = config::get('db.tables', array());
            // print_r($tables);exit;
            $table_config = null;
            foreach ($tables as $table => $val) {
                if (preg_match("/^" . $table . "$/i", $table_name)) {
                    $table_config = $val;
                    break;
                }
            }
            if ($table_config === null) {
                throw new except('config db.tables [' . $table_name . '] not found.');
            }
            $dsn = config::get('db.sources.' . $table_config['source']);
            // print_r($dsn);exit;
            if (!$dsn) {
                throw new except('config db.source [' . $table_name . '] not found.');
            }
            //$dsn['pass'] = keyt::mydecrypt($dsn['pass']);
            //主从判断
            $is_slave = config::get('slave.is_slave');
            if($is_slave && !$master){
                $slave_hosts = config::get('slave.host');
                if(is_string($slave_hosts)){
                    $slave_host_arr = explode(',', $slave_hosts);
                    $slave_host = $slave_host_arr[array_rand($slave_host_arr, 1)];
                    $dsn['host'] = $slave_host;
                }
            }
            
            self::$_tables[$table_name] = $table_config;
            self::$_tables[$table_name]['_db_'] = new mysqli_conn($dsn, self::$_timeout);
            
        }
        self::$_primary = self::$_tables[$table_name]['pk'];
    }

    protected static function _setupDb($table_name, $master = true) {
        if (empty(self::$_tables[$table_name]['_db_'])) {
            self::_steupTable($table_name, $master = true);
        }
        self::$_db = self::$_tables[$table_name]['_db_'];
    }

    public static function setAutoClose($type = true) {
        self::$_auto_close = $type;
    }

    public static function close($table_name, $force = 0) {
        if (self::$_auto_close || $force) {
            if (isset(self::$_tables[$table_name])) {
                self::$_tables[$table_name]['_db_']->close();
                unset(self::$_tables[$table_name]);
            }
            self::$_db = null;
        }
    }

    public static function closeAll() {
        foreach (self::$_tables as $resource) {
            $resource['_db_']->close();
        }
        self::$_tables = array();
        self::$_db = null;
    }

    public static function error() {
        return self::$_db->error();
    }

    public static function insertId() {
        return self::$_db->insertId();
    }

    public static function queryAll($sql, $table_name = '*') {
        self::_setupDb($table_name, false);
        $result = self::$_db->queryAll($sql);
        self::close($table_name);
        return $result;
    }

    public static function queryOne($sql, $table_name = '*') {
        self::_setupDb($table_name, false);
        $result = self::$_db->queryOne($sql);
        self::close($table_name);
        return $result;
    }

    public static function execute($sql, $table_name = '*', $close = true) {
        // logger::sql_log($sql,$table_name);
        self::_setupDb($table_name);
        try {
            $result = self::$_db->execute($sql);
        } catch (except $e) {
            // logger::log("db_execute_error",$sql." : ".$e->getCode());
            throw $e;
        }
        if ($close) {
            self::close($table_name);
        }
        return $result;
    }

    public static function getAll($table_name, $order = array(), $page_size = 0, $page = 0, $groupby = '') {
        return self::getAllForFields($table_name, '*', $order, $page_size, $page, $groupby);
    }

    public static function getOne($table_name, $where = null, array $args = array(), $order = array()) {
        return self::getOneForFields($table_name, '*', $where, $args, $order);
    }

    public static function getOneForFields($table_name, $field_names, $where = null, array $args = array(), $order = array()) {
        self::_steupTable($table_name, false);
        $sql = 'SELECT ' . $field_names . ' FROM ' . self::$_table_name;
        $sql .= self::_where($where, $args);
        if ($order == 'rand()') {
            $max = max((self::countWhere($table_name, $where, $args) - 1), 0);
            $rand = rand(0, $max);
            $sql .= ' LIMIT ' . $rand . ', 1';
        } else {
            $sql .= self::_order($order);
            $sql .= ' LIMIT 1';
        }
        return self::queryOne($sql, $table_name);
    }

    public static function getWhere($table_name, $where, array $args = array(), $order = array(), $page_size = 0, $page = 0, $groupby = '') {
        return self::getWhereForFields($table_name, '*', $where, $args, $order, $page_size, $page, $groupby);
    }

    public static function getWhereForFields($table_name, $field_names, $where, array $args = array(), $order = array(), $page_size = 0, $page = 0, $groupby = '') {
        self::_steupTable($table_name, false);
        $sql = 'SELECT ' . $field_names . ' FROM ' . self::$_table_name;
        $sql .= self::_where($where, $args);
        $sql .= $groupby ? " GROUP BY " . $groupby : '';
        if ($order == 'rand()') {
            $max = max((self::countWhere($table_name, $where, $args) - $page_size), 0);
            $rand = rand(0, $max);
            $sql .= ' LIMIT ' . $rand . ', ' . $page_size;
            $result = self::queryAll($sql, $table_name);
            shuffle($result);
            return $result;
        } else {
            $sql .= self::_order($order);
            $sql .= self::_limit($page_size, $page);
            return self::queryAll($sql, $table_name);
        }
    }

    public static function getUnion($table_names, $field_names, $where, array $args = array(), $order = array(), $page_size = 0, $page = 0, $groupby = '') {
        $table_name = $table_names[0];
        self::_steupTable($table_name, false);
        $sql = 'SELECT ' . $field_names . ' FROM ' . self::$_table_name . ' as a LEFT JOIN ' . $table_names[1] . ' as b ON ' . $table_names[2];
        $sql .= self::_where($where, $args);
        $sql .= $groupby ? " GROUP BY " . $groupby : '';
        if ($order == 'rand()') {
            $max = max((self::countUnion($table_names, $where, $args) - $page_size), 0);
            $rand = rand(0, $max);
            $sql .= ' LIMIT ' . $rand . ', ' . $page_size;
            $result = self::queryAll($sql, $table_name);
            shuffle($result);
            return $result;
        } else {
            $sql .= self::_order($order);
            $sql .= self::_limit($page_size, $page);
            return self::queryAll($sql, $table_name);
        }
    }

    public static function getAllForFields($table_name, $field_names, $order = array(), $page_size = 0, $page = 0, $groupby = '') {
        self::_steupTable($table_name, false);
        $sql = 'SELECT ' . $field_names . ' FROM ' . self::$_table_name;
        $sql .= $groupby ? " GROUP BY " . $groupby : '';
        $sql .= self::_order($order);
        $sql .= self::_limit($page_size, $page);
        return self::queryAll($sql, $table_name);
    }

    public static function getByPk($table_name, $id) {
        self::_steupTable($table_name, false);
        return self::getOne($table_name, self::_whereByPk(array($id)));
    }

    public static function getByPkForFields($table_name, $field_names, $id) {
        self::_steupTable($table_name, false);
        return self::getOneForFields($table_name, $field_names, self::_whereByPk(array($id)));
    }

    public static function getByPksForFields($table_name, $field_names, array $ids, $order = array(), $page_size = 0, $page = 0) {
        self::_steupTable($table_name, false);
        return self::getWhereForFields($table_name, $field_names, self::_whereByPk($ids), $args = array(), $order, $page_size, $page);
    }

    public static function getByPks($table_name, array $ids, $order = array(), $page_size = 0, $page = 0) {
        self::_steupTable($table_name, false);
        return self::getWhere($table_name, self::_whereByPk($ids), $args = array(), $order, $page_size, $page);
    }

    public static function countAll($table_name, $groupby = '') {
        self::_steupTable($table_name, false);
        $sql = 'SELECT COUNT(' . ($groupby ? "DISTINCT " . $groupby : "*") . ') AS count FROM ' . self::$_table_name;
        $record = self::queryOne($sql, $table_name);
        return (int) $record['count'];
    }

    public static function countWhere($table_name, $where, array $args = array(), $groupby = '') {
        self::_steupTable($table_name, false);
        $sql = 'SELECT COUNT(' . ($groupby ? "DISTINCT " . $groupby : "*") . ') AS count FROM ' . self::$_table_name;
        $sql .= self::_where($where, $args);
        $record = self::queryOne($sql, $table_name);
        return (int) $record['count'];
    }

    public static function countUnion($table_names, $where, array $args = array(), $groupby = '') {
        $table_name = $table_names[0];
        self::_steupTable($table_name, false);
        $sql = 'SELECT COUNT(' . ($groupby ? "DISTINCT " . $groupby : "*") . ') AS count FROM ' . self::$_table_name . ' as a LEFT JOIN ' . $table_names[1] . ' as b ON ' . $table_names[2];
        ;
        $sql .= self::_where($where, $args);
        $record = self::queryOne($sql, $table_name);
        return (int) $record['count'];
    }

    public static function pageAll($table_name, $order = array(), $page_size = 0, $page = 0, $groupby = '') {
        return array(self::_buildPageData(self::countAll($table_name, $groupby), $page_size, $page), self::getAll($table_name, $order, $page_size, $page, $groupby));
    }

    public static function pageWhere($table_name, $where, array $args = array(), $order = array(), $page_size = 0, $page = 0, $groupby = '') {
        return array(self::_buildPageData(self::countWhere($table_name, $where, $args, $groupby), $page_size, $page), self::getWhere($table_name, $where, $args, $order, $page_size, $page, $groupby));
    }

    public static function pageWhereForFields($table_name, $field_names = '', $where, array $args = array(), $order = array(), $page_size = 0, $page = 0, $groupby = '') {
        $field_names = !$field_names ? '*' : $field_names;
        return array(self::_buildPageData(self::countWhere($table_name, $where, $args, $groupby), $page_size, $page), self::getWhereForFields($table_name, $field_names, $where, $args, $order, $page_size, $page, $groupby));
    }

    public static function pageUnion($table_names, $field_names = '', $where, array $args = array(), $order = array(), $page_size = 0, $page = 0, $groupby = '') {
        $table_name = $table_names[0];
        $field_names = !$field_names ? '*' : $field_names;
        return array(self::_buildPageData(self::countUnion($table_names, $where, $args, $groupby), $page_size, $page), self::getUnion($table_names, $field_names, $where, $args, $order, $page_size, $page, $groupby));
    }

    public static function setByPk($table_name, array $keyvalues, $id) {
        self::_steupTable($table_name);
        return self::setWhere($table_name, $keyvalues, self::_whereByPk(array($id)));
    }

    public static function setByPks($table_name, array $keyvalues, array $ids) {
        self::_steupTable($table_name);
        return self::setWhere($table_name, $keyvalues, self::_whereByPk($ids));
    }

    public static function setWhere($table_name, array $keyvalues, $where, array $args = array()) {
        self::_steupTable($table_name);
        $set = array();
        foreach ($keyvalues as $col => $val) {
            if (substr($val, 0, 1) == '@') {
                if (preg_match("/^@([\-+]{1,2}[0-9\.]+)$/", $val, $match)) {
                    $set[] = "`$col` = `$col` " . $match[1];
                    continue;
                }
            }
            $set[] = "`$col` = '" . addslashes((string) $val) . "'";
        }
        $sql = 'UPDATE ' . self::$_table_name . ' SET ' . implode(', ', $set) . self::_where($where, $args);
        $result = self::execute($sql, $table_name);
        return $result;
    }

    public static function setAll($table_name, array $keyvalues) {
        return self::setWhere($table_name, $keyvalues, '');
    }

    public static function set($table_name, array $keyvalues) {
        self::_steupTable($table_name);
        if (self::_isExistData($table_name, $keyvalues) === false) {
            return self::add($table_name, $keyvalues);
        } else {
            $pk_data = array_intersect_key($keyvalues, array_flip(self::$_primary));
            return self::setWhere($table_name, $keyvalues, self::_whereByPk($pk_data));
        }
    }

    public static function add($table_name, array $keyvalues) {
        self::_steupTable($table_name);
        $cols = array();
        $vals = array();
        foreach ($keyvalues as $col => $val) {
            $cols[] = "`$col`";
            $vals[] = "'" . addslashes((string) $val) . "'";
        }
        $sql = 'INSERT INTO ' . self::$_table_name . ' (' . implode(', ', $cols) . ') ' . 'VALUES (' . implode(', ', $vals) . ')';

        $result = self::execute($sql, $table_name, false);
        $insertid = self::insertId();
        self::close($table_name);
        return $insertid > 0 ? $insertid : $result;
    }

    public static function addMany($table_name, array $keyvalues_list) {
        self::_steupTable($table_name);
        $cols = array();
        foreach ($keyvalues_list[0] as $col => $value) {
            $cols[] = "`$col`";
        }
        $values_list = array();
        foreach ($keyvalues_list as $keyvalues) {
            $values = array();
            foreach ($keyvalues as $key => $value) {
                $values[] = "'" . addslashes((string) $value) . "'";
            }
            $values_list[] = '(' . implode(', ', $values) . ')';
        }
        $sql = 'INSERT INTO ' . self::$_table_name . ' (' . implode(', ', $cols) . ') ' . 'VALUES ' . implode(', ', $values_list);
        self::execute($sql, $table_name, false);
        $result = self::insertId();
        self::close($table_name);
        return $result;
    }

    public static function delByPk($table_name, $id) {
        self::_steupTable($table_name);
        return self::delWhere($table_name, self::_whereByPk(array($id)));
    }

    public static function delByPks($table_name, array $ids) {
        if (empty($ids)) {
            return false;
        }
        self::_steupTable($table_name);
        return self::delWhere($table_name, self::_whereByPk($ids));
    }

    public static function delWhere($table_name, $where, array $args = array()) {
        self::_steupTable($table_name);
        $sql = 'DELETE FROM ' . self::$_table_name . self::_where($where, $args);
        $result = self::execute($sql, $table_name);
        return $result;
    }

    protected static function _isExistData($table_name, array $keyvalues) {
        $pk_data = array_intersect_key($keyvalues, array_flip(self::$_primary));
        if (empty($pk_data)) {
            return false;
        }
        if (!self::getOne($table_name, self::_whereByPk($pk_data))) {
            return false;
        }
        return true;
    }

    protected static function _buildPageData($record_count, $page_size, $page) {
        $page_size = (int) $page_size;
        $page = (int) $page;
        if ($record_count <= 0) {
            $page_count = 1;
            $page = 1;
        } else {
            $page_count = @ceil($record_count / $page_size);
        }
        if ($page > $page_count) {
            $page = $page_count;
        } elseif ($page < 1) {
            $page = 1;
        }
        return array('record_count' => $record_count, 'page_count' => $page_count, 'page' => $page, 'page_size' => $page_size);
    }

    protected static function _where($where = null, array $args) {
        $_where_sql = '';
        if (!empty($where) && is_string($where)) {
            $_where_sql = ' WHERE ' . self::_replaceSqlArgs($where, $args);
        }
        return $_where_sql;
    }

    protected static function _whereByPk($where) {
        if (is_array($where)) {
            $whereOrTerms = array();
            foreach ($where as $val) {
                $val = array($val);
                if (count($val) != count(self::$_primary)) {
                    throw new except("cols not eq the num of primary key");
                }
                $val = array_combine(self::$_primary, $val);
                $whereAndTerms = array();
                foreach ($val as $col => $v) {
                    $whereAndTerms[] = self::_replaceSqlArgs('`' . $col . '` = ?', array($v));
                }
                $whereOrTerms[] = '(' . implode(' AND ', $whereAndTerms) . ')';
            }
            $where = implode(' OR ', $whereOrTerms);
        }
        return $where;
    }

    protected static function _order($_order) {
        $order_by_sql = '';
        if (!empty($_order)) {
            if (!is_array($_order)) {
                return ' ORDER BY ' . $_order;
            }
            $orders = array();
            foreach ($_order as $field_name => $order) {
                $orders[] = $field_name . ' ' . strtoupper($order);
            }
            $order_by_sql = ' ORDER BY ' . implode(', ', $orders);
        }
        return $order_by_sql;
    }

    protected static function _limit($page_size, $page) {
        $page_size = (int) $page_size;
        $page = (int) $page;
        $limit_sql = '';
        if ($page_size !== 0) {
            $page_size = abs($page_size);
            if ($page < 1) {
                $page = 1;
            }
            $begin_offset = ($page - 1) * $page_size;
            $limit_sql = ' LIMIT ' . $begin_offset . ', ' . $page_size;
        }
        return $limit_sql;
    }

    protected static function _replaceSqlArgs($sql, array $args) {
        if (!empty($args)) {
            $begin_position = 0;
            foreach ($args as $arg) {
                if (is_null($arg)) {
                    $replace_string = 'NULL';
                } else if (is_string($arg)) {
                    $replace_string = '\'' . addslashes($arg) . '\'';
                } else if (is_numeric($arg)) {
                    $replace_string = '\'' . $arg . '\'';
                } else if (is_bool($arg)) {
                    $replace_string = $arg;
                } else {
                    throw new except("stop: {$sql}");
                }
                $position_step = strlen($replace_string);
                $replace_position = strpos($sql, '?', $begin_position);
                if ($replace_position === false) {
                    throw new except("the number of args is not equal to the number of '?' in sql: {$sql}");
                }
                $sql = substr_replace($sql, $replace_string, $replace_position, 1);
                $begin_position = $replace_position + $position_step;
            }
        }
        return $sql;
    }

    public static function setTimeout($second) {
        self::$_timeout = $second;
    }

    public static function getTimeout($second) {
        return self::$_timeout;
    }

}
