<?php

/*
 * 图片操作
 */

class pic {

    protected static $_curl = null;

    public static function init() {
        if (self::$_curl === null) {
            self::$_curl = Load::lib('client');
            self::$_curl->setTimeout(100);
            self::$_curl->setProxy('proxy1.local.com:33311');
        }
    }

    /**
     * 图片添加
     * @param array or string $pics 图片地址，可多个
     * @param string $name 分组名字（30个字符以内）
     */
    public static function set($pics, $mtype = 1, $type = 2, $groupid = 0) {
        if (!$pics) {
            return false;
        }
        $data = array();
        $data['uid'] = F::logininfo("id");
        $data['groupid'] = $groupid;
        $data['mtype'] = $mtype;
        $data['theday'] = date("Ymd");
        $data['type'] = $type;
        if (is_array($pics)) {
            foreach ($pics as $pic) {
                $data['imgurl'] = $pic;
                db::set('s_pic', $data);
            }
        } else {
            $data['imgurl'] = $pics;
            return db::set('s_pic', $data);
        }

        return true;
    }

    public static function get($imgurl) {
        if (!$imgurl) {
            return false;
        }
        if (!$dt = db::getOne('s_pic', 'imgurl LIKE "%' . $imgurl . '%"', array())) {
            return self::set($imgurl);
        }
        return $dt['id'];
    }

    // 图片上传方法 $ftype 上传格式  $st 是否生成小图 $path 上传路径
    // author = duzf ; date = 2014-04-01 14:56
    public static function uploadImg($ftype = 'img', $st = false, $path = 'picture/', $filedata = 'file', $mtype = 1, $type = 2) {
        $path = trim($path, '/') . '/' . date('Y') . '/' . date('m') . '/' . date('d') . '/';
        $uploadconfigs = array(
            'img' => array(
                'allowed_types' => "gif|jpg|jpeg|png|bmp|GIF|JPG|JPEG|PNG|BMP",
                'max_size' => 1000000,
                'upload_path' => $path
            ),
            'audio' => array(
                'allowed_types' => "midi|mp3|wma|wav|ra|rm",
                'max_size' => 5000000,
                'upload_path' => $path
            ),
            'gimg' => array(
                'allowed_types' => "gif|jpg|jpeg|png|bmp|GIF|JPG|JPEG|PNG|BMP",
                'max_size' => 500000,
                'upload_path' => $path,
                'small_imags' => array('s' => array(100, 100), 'm' => array(190, 190, 'max'))
            ),
            'doc' => array(
                'allowed_types' => "csv|doc|docx|word|CSV|DOC|DOCX|WORD",
                'max_size' => 500000,
                'upload_path' => $path
            ),
        );
        if (!$uploadconfigs[$ftype]) {
            return false;
        }
        $upload_lib = Load::lib('upload', $uploadconfigs[$ftype]);
        if ($upload_lib->run($filedata, $st)) {
            $filedata = $upload_lib->data();
            if (!empty($filedata)) {
                if ($filedata['is_image']) {
                    $returnfileurl = $filedata['file_small']['s'] ? $filedata['file_small']['s'] : $filedata['file_url'];
                } else {
                    $returnfileurl = $filedata['file_url'];
                }
                $imgurl = '/' . trim($filedata['file_url'], '/');
                if ($mtype && self::set($imgurl, $mtype, $type)) {
                    return $imgurl;
                }
                return $imgurl;
            }
        }
        return false;
    }

    //组织多图上传数据
    public static function upload_imgs($data, $path = "hlt_order") {
        $imgs = $data['img'];
        $len = count($imgs['name']);
        $datas = array();
        for ($i = 0; $i < $len; $i++) {
            if ($i >= 9) {
                break;
            }
            $datas[] = array(
                'name' => $imgs['name'][$i],
                'type' => $imgs['type'][$i],
                'tmp_name' => $imgs['tmp_name'][$i],
                'error' => $imgs['error'][$i],
                'size' => $imgs['size'][$i]
            );
        }
        $picNames = '';
        if ($datas) {
            // 多图
            $targetPath = self::makeDir(UPLOAD_DIR . "/" . trim($path, '/') . "/" . date('Y') . '/' . date('m') . '/' . date('d') . '/');
            $files = $datas;
            $fileTypes = array('jpg', 'jpeg', 'gif', 'png', 'JPG', 'JPEG', 'GIF', 'PNG'); // File extensions
            $imgs = array();
            foreach ($files as $k => $v) {
                $tempFile = $v['tmp_name'];
                $fileParts = pathinfo($v['name']);
                $fileName = substr(md5($v['name'] . time()), 0, 16) . "." . $fileParts['extension'];
                $targetFile = rtrim($targetPath, '/') . '/' . $fileName;
                if (in_array($fileParts['extension'], $fileTypes)) {
                    move_uploaded_file($tempFile, $targetFile);
                    $imgs[] = str_replace(UPLOAD_DIR . '/', '', $targetPath) . $fileName;
                }
            }
            $picNames = implode(',', $imgs);
        }
        return $picNames;
    }

    //检查是否有目录或文件，创建之
    public static function makeDir($dir) {
        if (!file_exists($dir)) {
            try {
                $path = UPLOAD_DIR . '/';
                $newDir = str_replace($path, '', $dir);
                $xd = explode('/', trim($newDir, '/'));
                foreach ($xd as $v) {
                    $path .= $v . '/';
                    if (!file_exists($path)) {
                        if (!mkdir($path, 0777)) {
                            return false;
                            break;
                        }
                    }
                }
                return $path;
            } catch (Exception $e) {
                return false;
            }
        }
        return $dir;
    }

    public static function getImgByUrl($id, $url) {
        self::init();
        $ext = strtolower(substr($url, strrpos($url, '.') + 1));
        $retry = 5;
        while (--$retry) {
            try {
                $imgdata = self::$_curl->get($url);
                if ($imgdata) {
                    break;
                }
            } catch (except $e) {
                continue;
            }
        }
        if (!$imgdata) {
            return false;
        } else {
            return self::saveImg($id, $imgdata, $ext);
        }
    }

    public static function saveImg($id, $imgdata, $ext) {
        $idstr = str_pad(func::tableHash($id, 10000) - 1, 4, "0", STR_PAD_LEFT);
        $dir = UPLOAD_DIR . "/";
        $return_url = 'ad_order';
        $paths = array();
        $paths[] = 'wby';
        $paths[] = substr($idstr, 0, 2);
        $paths[] = substr($idstr, 2, 2);
        $fdir = '';
        foreach ($paths as $path) {
            $return_url .= '/' . $path;
            if (!file_exists($dir . $return_url)) {
                mkdir($dir . $return_url, 0777);
            }
        }
        $needmove = false;
        if (in_array($ext, array('jpg', 'jpeg', 'bmp', 'gif', 'png'))) {
            $file = substr(md5($id), 0, 26) . '.' . $ext;
        } else {
            $file = substr(md5($id), 0, 26);
            $needmove = true;
        }
        $filename = $dir . $return_url . '/' . $file;
        file_put_contents($filename, $imgdata);
        if ($needmove) {
            $file = self::imgMove($dir . $return_url, $file);
        }
        //self::imgThumb($dir,$file);
        return $return_url . '/' . $file;
    }

    public static function imgMove($dir, $oldfile) {
        $file = $oldfile;
        $file = $dir . "/" . $file;
        $mine = exec('file -ib ' . escapeshellarg($file));
        $tmp = explode(';', $mine);
        $mine = $tmp[0];
        $mimes = array(
            'bmp' => 'image/x-ms-bmp',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'png' => 'image/png',
            'tif' => 'image/tiff',
            'tga' => 'image/x-targa',
            'psd' => 'image/vnd.adobe.photoshop'
        );
        $mimes = array_flip($mimes);
        if ($mimes[$mine]) {
            $newfile = trim($oldfile, '.') . "." . $mimes[$mine];
            if (copy($file, $dir . "/" . $newfile)) {
                unlink($file);
            }
        } else {
            $newfile = '';
            unlink($file);
        }
        return $newfile;
    }

    //缩略图
    public static function imgThumb($dir, $file) {
        $source_f = $dir . "/" . $file;
        if (!file_exists($source_f)) {
            return false;
        }
        $thumbdir = $dir . "/s";
        if (!file_exists($thumbdir)) {
            mkdir($thumbdir, 0777);
        }
        $maxwidth = 150;
        $maxheight = 100;
        $source = $thumbdir . "/" . $file;
        if (file_exists($source)) {
            return true;
        }
        @copy($source_f, $source);
        $size = @getimagesize($source);
        switch ($size[2]) {
            case 1:
                $im = @imagecreatefromgif($source);
                break;
            case 2:
                $im = @imagecreatefromjpeg($source);
                break;
            case 3:
                $im = @imagecreatefrompng($source);
                break;
        }
        $width = @imagesx($im);
        $height = @imagesy($im);
        if (($maxwidth && $width > $maxwidth) && ($maxheight && $height > $maxheight)) {
            $ratio = $width / $height;
            $sratio = $maxwidth / $maxheight;
            if ($ratio >= $sratio) {
                $newheight = $maxheight;
                $newwidth = $width * ($maxheight / $height);
            } else {
                $newwidth = $maxwidth;
                $newheight = $height * ($maxwidth / $width);
            }
            if (function_exists("imagecopyresampled")) {
                $newim = @imagecreatetruecolor($newwidth, $newheight);
                @imagecopyresampled($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
            } else {
                $newim = @imagecreate($newwidth, $newheight);
                @imagecopyresized($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
            }
        } else {
            $newim = $im;
            $newwidth = $width;
            $newheight = $height;
        }
        switch ($size['mime']) {
            case 'image/jpeg':
                @imageJPEG($newim, $source);
                break;
            case 'image/gif':
                @imageGIF($newim, $source);
                break;
            case 'image/png':
                @imagePNG($newim, $source);
                break;
        }
        @ImageDestroy($newim);
        return true;
    }

    public static function imgDel($path = '') {
        if (!$path) {
            return false;
        }

        $imgpath = UPLOAD_DIR . '/' . trim($path, '/');
        if (!file_exists($imgpath)) {
            return false;
        }
        return unlink($imgpath);
    }

}

?>