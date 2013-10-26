<?php

namespace Tool\Mail;

use Tool\Mail\PHPMailer\PHPMailer;
use Tool\Mail\Exception\InvalidReceiversException;
use Tool\Mail\Exception\EmptyReceiverException;

class MailHandler
{
    private $username = "";
    private $password = "";
    private $senderName = "";
    
    public function __construct($username, $password, $senderName)
    {
        // 加入php mailer library
        // if (!class_exists("PHPMailer")) {
        //     require_once realpath(__DIR__) . "/PHPMailer/PHPMailer.php";
        // }
        
        // username = gmail帳號，也就是系統設定發信人
        $this->username = $username;
        $this->password = $password;
        $this->senderName = $senderName;
    }
    
    // 副本可以自行選定要不要發送
    public function sendMail($subject, $body, $receivers, $ccReceivers = array())
    {
        if (!is_array($receivers) || !is_array($ccReceivers)) {
            throw new InvalidReceiversException();
        } else if (count($receivers) < 1) {
            throw new EmptyReceiverException();
        }
        
        $mail = new PHPMailer(true);
        
        // 啟用SMTP發信
        $mail->IsSMTP();
        
        // phpMailer例外交給外面來處理
        
        // 打開debug選項
        // $mail->SMTPDebug = 2;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "tls";
        $mail->CharSet = "utf-8";
        
        // 設定mail server為gmail
        $mail->Host = "smtp.gmail.com";
        $mail->Port = "587";
        
        // 設定寄件者
        $mail->SetFrom($this->username, $this->senderName);
        
        // ex: ts013xxxxx@gmail.com
        $mail->Username = $this->username;
        $mail->Password = $this->password;
        
        // 加入收信者
        for ($i = 0; $i < count($receivers); $i++) {
            $mail->AddAddress($receivers[$i]["email"], $receivers[$i]["name"]);
        }
        
        
        // 加入副本收信者
        for ($i = 0; $i < count($ccReceivers); $i++) {
            $mail->AddCC($ccReceivers[$i]["email"], $ccReceivers[$i]["name"]);
        }
        
        $mail->Subject = $subject;
        $mail->MsgHTML($body);
        $mail->Send();
    }
    
    public function __destruct()
    {
    }
}
?>