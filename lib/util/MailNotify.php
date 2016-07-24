<?php
namespace lib\util;
use lib\db\User;

require_once( dirname( __FILE__ ) . '/../Constants.php' );
require_once(dirname(__FILE__) . '/../../vendor/autoload.php');

class MailNotify{
    /**
     * @param $subject
     * @param $body
     * @throws \Exception
     * @throws \phpmailerException
     */
    public static function sentMailToAllUser($subject, $body) {
        $users = User::getEmailUserList();
        if(!empty($users)){
            try {
                $mail = new \PHPMailer();
                $mail->isSendmail();
                $mail->setLanguage('ja');
                $mail->isHTML(false);
                $mail->Encoding = "7bit";
                $mail->CharSet = 'ISO-2022-JP';

                $mail->From = CONFIG_MAIL_FROM;
                $mail->FromName = mb_encode_mimeheader(CONFIG_SITE_NAME);

                $mail->addAddress(CONFIG_MAIL_FROM);
                foreach ($users as $user) {
                    $mail->addBcc($user->email);
                }

                $mail->Subject = mb_encode_mimeheader($subject);
                $mail->Body = mb_convert_encoding($body, "JIS", "UTF-8");

                if (!$mail->send()) {
                    error_log('Message could not be sent.');
                    error_log('Mailer Error: ' . $mail->ErrorInfo);
                }
            } catch (Exception $e){
                error_log('Mailer Error: ' . $e->getMessage());
                error_log('Mailer Error: ' . $e->getTraceAsString());
            }
        }

    }
}
