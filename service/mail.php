<?php

/**
 * 发邮件管理
 */
class mail {

    protected static $_mail = null;

    public static function init() {
        if (self::$_mail === null) {
            self::$_mail = Load::lib('sendmail');
        }
    }

    public static function send($mailto = '', $mailtitle = '', $mailbody = '') {
        if (!$mailto || !$mailtitle || !$mailbody) {
            return false;
        }
        self::init();
        self::$_mail->IsMail();
        self::$_mail->SetFrom('noreply@tuweike.com', '兔微客');
        self::$_mail->AddAddress($mailto, '');
        //self::$_mail->AddCC('59933367@qq.com', 'Smokash');
        self::$_mail->Subject = $mailtitle;
        self::$_mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
        self::$_mail->MsgHTML($mailbody);
        if (self::$_mail->Send()) {
            return true;
        }
        return false;
    }

}

?>