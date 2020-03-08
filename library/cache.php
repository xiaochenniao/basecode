<?php

class cache {

    public static function set($id, $data, $tags = array()) {
        $data = "<?php\nreturn " . var_export($data, true) . "\n;?>";
        $path = self::_path($id, true);
        file_put_contents($path, $data);
        @chmod($path, 0777);
        $tags = (array) $tags;
        if (!empty($tags)) {
            foreach ($tags as $tag) {
                $tid = '__tag__' . $tag;
                if (($data = self::get($tid)) === false) {
                    $data = array();
                }
                $data[] = $id;
                $data = array_unique($data);
                self::set($tid, $data);
            }
        }
    }

    /**
     * ��ȡ���棬ʧ�ܻ򻺴���ʧЧʱ���� false
     *
     * @param string $id
     *
     * @return mixed
     */
    public static function get($id) {
        $path = self::_path($id, false);
        if (is_readable($path)) {
            return require ($path);
        }
        return null;
    }

    /**
     * ɾ��ָ���Ļ���
     *
     * @param string $id
     */
    public static function del($id = null) {
        if (is_null($id)) {
            @exec('rm -rf ' . DATA_DIR . '/system/*');
        } else {
            $path = self::_path($id, false);
            @unlink($path);
        }
    }

    public static function del_taq($tag) {
        $tid = '__tag__' . $tag;
        if (($ids = self::get($tid)) === false) {
            return;
        }
        foreach ($ids as $id) {
            $id && self::del($id);
        }
        self::del($tid);
    }

    /**
     * ȷ�������ļ�������������Ҫ�Ĵμ�����Ŀ¼
     *
     * @param string $id
     * @param boolean $mkdirs
     *
     * @return string
     */
    protected static function _path($id, $mkdirs = true) {
        $filename = md5($id) . '.php';
        $root_dir = DATA_DIR . '/system/';
        if ($mkdirs && !is_dir($root_dir)) {
            $umask = @umask(0);
            @mkdir($root_dir, 0777, true);
            @umask($umask);
        }
        return $root_dir . $filename;
    }

}
