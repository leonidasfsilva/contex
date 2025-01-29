<?php

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require 'vendor/autoload.php';

class PhpMailerLoader
{
    protected PHPMailer $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
    }

    public function sendEmail(string $subject, string $message, string $to, string $from, string $toName = null, string $fromName = null)
    {
        try {
            //Server settings
            $this->mailer->SMTPDebug = SMTP::DEBUG_SERVER;            //Enable verbose debug output
            $this->mailer->isSMTP();                                  //Send using SMTP
            $this->mailer->Host       = 'mail.mxcode.net';            //Set the SMTP server to send through
            $this->mailer->SMTPAuth = true;                           //Enable SMTP authentication
            $this->mailer->Username = $_ENV['SMTP_USERNAME'];         //SMTP username
            $this->mailer->Password = $_ENV['SMTP_PASSWORD'];         //SMTP password
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;  //Enable implicit TLS encryption
            $this->mailer->Port       = 465;                          //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $this->mailer->setFrom($from, $fromName ?? $from);
            $this->mailer->addAddress($to, $toName ?? $to);               //Add a recipient, Name is optional
            $this->mailer->addReplyTo('mikrotik@mxcode.net', 'Mikrotik Report Generator');
            // $this->mailer->addCC('cc@example.com');
            // $this->mailer->addBCC('bcc@example.com');

            //Attachments
            // $this->mailer->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            // $this->mailer->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            //Content
            $this->mailer->isHTML(true);                                  //Set email format to HTML
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $message;
            $this->mailer->AltBody = $message;

            if ($this->mailer->send()) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            return "Message could not be sent. Mailer Error: {$this->mailer->ErrorInfo}";
        }

    }

}