<?php
    require 'PHPMailer/PHPMailer.php';
    require 'PHPMailer/SMTP.php';
    require 'PHPMailer/Exception.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;

    define ('GUSER','h3i53nb3rg.discord.j4j@gmail.com');
    define ('GPWD','Agron_292');

    function smtpmailer($to, $from, $from_name, $subject, $body) { 
        $mail = new PHPMailer();

        $mail->SMTPKeepAlive = true;  
        $mail->Mailer = "smtp"; 
        $mail->IsSMTP();
        $mail->CharSet = "UTF-8";
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAutoTLS = false;
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 465;

        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        $mail->Username = GUSER;  
        $mail->Password = GPWD;           
        $mail->SetFrom($from, $from_name);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AddAddress($to);

        if(!$mail->Send()) {
            return false;
        } else {
            return true;
        }
    }
?>