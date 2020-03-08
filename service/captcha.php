<?php

/**
 * 前台图片验证码生成类Ï
 */
class captcha {

    public static function createImage() {
        //获取随机字符
        $rndstring = '';
        for ($i = 0; $i < 4; $i++)
            $rndstring .= chr(mt_rand(65, 90));
        //如果支持GD，则绘图
        if (function_exists("imagecreate")) {
            //Firefox部份情况会多次请求的问题，5秒内刷新页面将不改变session
            $ntime = time();
            if (is_null(session::get('dd_ckstr_last')) || is_null(session::get('dd_ckstr')) || ($ntime - session::get('dd_ckstr_last') > 1)) {
                session::set('dd_ckstr', strtolower($rndstring));
                session::set('dd_ckstr_last', $ntime);
            }
            $rndstring = session::get('dd_ckstr');
            $rndcodelen = strlen($rndstring);

            //创建图片，并设置背景色
            $im = imagecreate(100, 40);
            ImageColorAllocate($im, 255, 255, 255);


            //输出扰乱文字
            $rltxt = '123456789asdfghjklzrtyuiop';
            for ($i = 0; $i < strlen($rltxt); $i++) {
                $fontColor = ImageColorAllocate($im, rand(0, 255), rand(0, 255), rand(0, 255));
                $rltxt[$i] = strtoupper($rltxt[$i]);
                imagestring($im, 2, mt_rand(0, 100), mt_rand(0, 40), $rltxt[$i], $fontColor);
            }

            //背景线
            $lineColor1 = ImageColorAllocate($im, 999, 666, 222);

            for ($j = 3; $j <= 40; $j = $j + 3) {
                //imageline($im,2,$j,98,$j,$lineColor1);
            }
            for ($j = 2; $j < 104; $j = $j + (mt_rand(3, 6))) {
                $lineColor2 = ImageColorAllocate($im, rand(0, 255), rand(0, 255), rand(0, 255));
                //imageline($im,$j,2,$j-6,38,$lineColor2);
            }

            //画边框
            $bordercolor = ImageColorAllocate($im, 223, 223, 223);
            imagerectangle($im, 0, 0, 99, 39, $bordercolor);


            //输出文字
            for ($i = 0; $i < $rndcodelen; $i++) {
                $fontColor = ImageColorAllocate($im, rand(0, 255), rand(0, 255), rand(0, 255));
                $bc = mt_rand(0, 1);
                $rndstring[$i] = strtoupper($rndstring[$i]);
                imagettftext($im, 20, mt_rand(0, 60), 15 + $i * 20, mt_rand(20, 40), $fontColor, '../../config/fonts/simfang.ttf', $rndstring[$i]);
            }

            header("Pragma:no-cache\r\n");
            header("Cache-Control:no-cache\r\n");
            header("Expires:0\r\n");

            //输出特定类型的图片格式，优先级为 gif -> jpg ->png
            if (function_exists("imagejpeg")) {
                header("content-type:image/jpeg\r\n");
                imagejpeg($im);
            } else {
                header("content-type:image/png\r\n");
                imagepng($im);
            }
            ImageDestroy($im);
            exit();
        } else {
            //不支持GD，只输出字母 ABCD
            session::set('dd_ckstr', "abcd");
            session::set('dd_ckstr_last', '');
            header("content-type:image/jpeg\r\n");
            header("Pragma:no-cache\r\n");
            header("Cache-Control:no-cache\r\n");
            header("Expires:0\r\n");
            $fp = fopen("data/vdcode.jpg", "r");
            echo fread($fp, filesize("data/vdcode.jpg"));
            fclose($fp);
            exit();
        }
    }

    public static function getWord() {
        return is_null(session::get('dd_ckstr')) ? '' : session::get('dd_ckstr');
    }

    public static function ResetVdValue() {
        session::del('dd_ckstr');
        session::del('dd_ckstr_last');
    }

}

?>