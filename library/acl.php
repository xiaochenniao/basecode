<?php

class acl {

    protected $_rules = array();

    function setRules($rules) {
        $this->_rules = $rules;
    }

    function getRules() {
        return $this->_rules;
    }

    function allow($role, $res = null, $action = null) {
        $this->_add('allow', $role, $res, $action);
    }

    function deny($role, $res = null, $action = null) {
        $this->_add('deny', $role, $res, $action);
    }

    protected function _add($type, $role, $res, $action) {
        if (empty($res))
            $res = '#';
        if (empty($action))
            $action = '#';
        foreach ((array) $res as $r) {
            foreach ((array) $action as $a) {
                $this->_rules[$role][$type][$r][$a] = $type;
            }
        }
    }

    function _is($type, $role, $res, $action) {
        if (!isset($this->_rules[$role][$type]))
            return false;
        if (isset($this->_rules[$role][$type]['#'])) {
            if (isset($this->_rules[$role][$type]['#'][$action]) || isset($this->_rules[$role][$type]['#']['#'])) {
                return true;
            }
        }
        if (!isset($this->_rules[$role][$type][$res])) {
            return false;
        }
        if (isset($this->_rules[$role][$type][$res][$action]) || isset($this->_rules[$role][$type][$res]['#'])) {
            return true;
        }
        return false;
    }

    function is($role, $res, $action) {
        if (!isset($this->_rules[$role]))
            return false;
        if ($this->_is('deny', $role, $res, $action) === true) {
            return false;
        }
        if ($this->_is('allow', $role, $res, $action) === true) {
            return true;
        }
        return false;
    }

}
