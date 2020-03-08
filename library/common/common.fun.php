<?php

function r_gets() {
    return request::gets();
}

function r_get($key, $default_value = null) {
    return request::get($key, $default_value);
}

function r_has($key) {
    return isset(request::$_params[$key]);
}

function r_int($key, $default_value = 0) {
    return isset(request::$_params[$key]) && is_numeric(request::$_params[$key]) ? (int) request::$_params[$key] : $default_value;
}

function r_str($key, $default_value = '') {
    return isset(request::$_params[$key]) && is_string(request::$_params[$key]) ? trim(request::$_params[$key]) : $default_value;
}

function r_arr($key, array $default_value = array()) {
    return isset(request::$_params[$key]) && is_array(request::$_params[$key]) ? request::$_params[$key] : $default_value;
}

function v_set($key, $value = true, $is_escape = true) {
    view::set($key, $value, $is_escape);
}

function v_json($var = null) {
    if ($var === null) {
        $var = view::getParams();
    }
    view::set_template(true);
    echo json_encode($var);
    response::send();
}

function v_jsonp($callback = null, $var = null) {
    if ($var === null) {
        $var = view::getParams();
    }
    view::set_template(true);
    echo $callback . '(' . json_encode($var) . ')';
    response::send();
}

function v_ajax($msg = '') {
    @header("Expires: -1");
    @header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
    @header("Pragma: no-cache");
    @header('Content-Type:text/html;charset=utf-8');
    view::set_template(true);
    echo $msg;
    response::send();
}

function v_display($template = null) {
    $controller = request::getControllerUName(); //控制器名称
    $module = request::get_module_name();
    $pageurl = request::getBasePath() . ($module ? '/' . $module . '/' : '/' ) . $controller;
    v_set(array(
        'adminurl' => ADMIN_URL,
        'pageurl' => $pageurl,
        'adverurl' => ADVER_URL,
        'mediaurl' => MEDIA_URL,
        'wwwurl' => WWW_URL,
        'fileurl' => FILE_URL,
    ));
    if (r_get('inajax')) {
        v_set('inajax', r_get('inajax'));
    }
    v_set('version', 20160520);
    v_set('imgurl', FILE_URL);
    v_set('logininfo', F::logininfo());
    view::display($template);
}

function v_callback($notice, $to = 0, $callback = 'frameback', $type = 'html', $domain = '') {
    view::set_template(true);
    if ($type == 'json') {
        echo "<script>" . $domain . "self.parent." . $callback . "(" . $notice . ", '" . $to . "');</script>";
    } else {
        echo "<script>" . $domain . "self.parent." . $callback . "('" . $notice . "', '" . $to . "');</script>";
    }
    response::send();
}

function v_notice($notice, $type = null) {
    if ($type === null) {
        view::set('jumpurl', request::get_referer(), false);
        $type = 0;
    } elseif (is_array($type)) {
        view::set('jumpurl', view::url($type), false);
        $type = 0;
    } elseif (substr($type, 0, 1) === '/') {
        view::set('jumpurl', view::url($type), false);
        $type = 0;
    } elseif (is_string($type)) {
        view::set('jumpurl', view::url(array('action' => $type)), false);
        $type = 0;
    }
    view::set('notice', (array) $notice, false);
    view::set('type', (int) $type);

    //session::set('notice', view::getParams());
    //response::redirect('default/notice');

    view::display('global/notice.tpl');
    response::send();
}

function v_repeat() {
    if (!F::checkformhash(r_get('formhash'))) {
        v_notice('请不要重复提交表单');
    }
}
