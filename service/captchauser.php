<?php

/**
 * 验证码生成类
 */
class captchauser {

    public static function createImage($x = 100, $y = 30, $num = 4) {
        //获取随机字符
        $rndstring = self::Code2($num);
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
            $im = imagecreatetruecolor($x, $y);
            $gray = imagecolorallocate($im, 248, 248, 248);

            // 填充颜色
            imagefill($im, 0, 0, $gray);

            //画边框
            //$bordercolor = ImageColorAllocate($im, 242,242,242);
            //imagerectangle($im, 0, 0,($x - 1),($y - 1), $bordercolor);


            $xbase = floor($x / 12);
            $xnum = floor($x / $num);

            //输出文字
            $fonttype = "/font/palai.ttf";
            for ($i = 0; $i < $rndcodelen; $i++) {
                $fontColor = imagecolorallocate($im, rand(0, 200), rand(0, 200), rand(0, 200));
                $size = rand(15, 20);
                imagefttext($im, $size, 0, $i * $xnum + $xbase, mt_rand($size, $y), $fontColor, $fonttype, strtoupper($rndstring[$i]));
            }

            //干扰点和线
            for ($i = 0; $i < 50; $i++) {
                $color = imagecolorallocate($im, rand(0, 200), rand(0, 200), rand(0, 200));
                $xx = rand(0, $x);
                $yy = rand(0, $y);
                imagesetpixel($im, $xx, $yy, $color);
            }
            for ($i = 0; $i < rand(1, 8); $i++) {
                $color = imagecolorallocate($im, rand(0, 200), rand(0, 200), rand(0, 200));
                $x1 = rand(0, $x);
                $y1 = rand(0, $y);
                $x2 = rand(0, $x);
                $y3 = rand(0, $y);
                imageline($im, $x1, $y1, $x2, $y3, $color);
            }

            header("Pragma:no-cache\r\n");
            header("Cache-Control:no-cache\r\n");
            header("Expires:0\r\n");
            //输出特定类型的图片格式，优先级为 gif -> jpg ->png
            if (function_exists("imagepng")) {
                header("content-type:image/png\r\n");
                imagepng($im);
            } else {
                header("content-type:image/jpeg\r\n");
                imagejpeg($im);
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

    public static function Code($num = 4) {
        $code = "";
        for ($i = 1; $i <= $num; $i++) {
            $r = rand(0, 1);
            switch ($r) {
                case 1:
                    $rand = mt_rand(49, 57);
                    break;
                case 0:

                    $rand = mt_rand(65, 90);
                    break;
            }
            $code .= chr($rand);
        }
        return $code;
    }

    public static function Code2() {
        $str = "2,3,4,5,6,7,8,9,a,b,c,d,f,g,h,k,m,n,p,q,r,s,t,u,v,w,x,y,z"; //要显示的字符，可自己进行增删
        $list = explode(",", $str);
        $cmax = count($list) - 1;
        $verifyCode = '';
        for ($i = 0; $i < 4; $i++) {
            $randnum = mt_rand(0, $cmax);
            $verifyCode .= $list[$randnum];
        }
        return $verifyCode;
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