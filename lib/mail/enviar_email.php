<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    require_once 'PHPMailerAutoload.php';
    
    $HOST_DEFAULT     = "smtp.drhtransparencia.com.br";         // "smtp-mail.outlook.com";
    $USERNAME_DEFAULT = "notificacao@drhtransparencia.com.br";  // "gerasys.suporte@outlook.com";
    $PASSWORD_DEFAULT = "notifica2017Transp";                   // "gsti2014";
    $FROMNAME_DEFAULT = "Notificação DRH Transparência";        // "GeraSys TI - DRH Transparência";
    
    $MAIL_SENDER_DEFALUT = "suporte@drhtransparencia.com.br";
    
    function enviarEmail($mensagem, $assunto, $nome_servidor, $email_destinatario, $usar_locaweb) {
        $ret = false;
        
        if ( $usar_locaweb === true ) {
            try {
                $break_line  = "\n";
                $mail_sender = $USERNAME_DEFAULT; // $MAIL_SENDER_DEFALUT; //"suporte@drhtransparencia.com.br";
                $mail_name   = $FROMNAME_DEFAULT;

                $headers  = "MIME-Version: 1.0" . $break_line;
                $headers .= "Content-type: text/html; charset=iso-8859-1" . $break_line;
                $headers .= "From: {$mail_name} <{$mail_sender}> " . $break_line;
                $headers .= "Reply-to: "    . $mail_sender . $break_line;  // Responder para..
                $headers .= "Return-Path: " . $mail_sender . $break_line;  // Responder para..
                $headers .= "Message-ID: <" . guid() . ">" . $break_line; 
                //$headers .= "Cc: "          . $mail_sender . $break_line; 
                $headers .= "Bcc: "         . $mail_sender . $break_line; 
                //$headers .= "Bcc: isaque.ribeiro@agilsoftwares.com.br" . $break_line;
                //$headers .= "Bcc: gerson.farias.2007@gmail.com" . $break_line;

                $ret = mail($email_destinatario, $assunto, $mensagem, $headers);
            } catch (Exception $ex) {
                echo 'Erro ao enviar e-mail:<br>' . print($ex->getMessage());
            }
        } else {
            try {
                /*
                $mail = new PHPMailer();
                $mail->setLanguage('pt');

                // Outlook e Hotmail
                $host     = $HOST_DEFAULT; //"smtp-mail.outlook.com";
                $port     = 587;
                $username = $USERNAME_DEFAULT; //"gerasys.suporte@outlook.com";
                $password = $PASSWORD_DEFAULT; //"gsti2014";

                $from     = $username;
                $fromName = $FROMNAME_DEFAULT; //'GeraSys TI - DRH Transparência';

                //$mail->SMTPSecure = 'tls'; // Para Hotmail 
                $mail->Host     = $host;
                $mail->Port     = $port;
                $mail->Username = $username;
                $mail->Password = $password;

                // Remetente
                $mail->From       = $from;
                $mail->FromName   = $fromName;
                //$mail->ReturnPath = $from;
                $mail->addReplyTo($from, $fromName); // Responder para...

                // Destinatarios
                $mail->addAddress($email_destinatario, $nome_servidor);
                $mail->AddBCC($username, $fromName); // Com cópia oculta
                $mail->AddBCC('isaque.ribeiro@outlook.com', 'Isaque M. Ribeiro (Analista de Sistemas)'); // Com cópia oculta

                $mail->isHTML(true);
                $mail->CharSet  = 'utf-8'; 
                $mail->WordWrap = 70;

                $mail->Subject  = $assunto;
                $mail->Priority = 1;
                $mail->Body     = $mensagem;
                $mail->AltBody  = strip_tags($mensagem);

                $mail->isSMTP();
                $mail->SMTPAuth = true;
                
                $ret = $mail->Send();
                */
                
                $mail = new PHPMailer();
                $mail->setLanguage('pt');

                $host     = "smtp.drhtransparencia.com.br";
                $port     = 587;
                $username = "notificacao@drhtransparencia.com.br";
                $password = "notifica2017Transp"; 

                $from     = $username;
                $fromName = 'Notificação DRH Transparência';

                $mail->IsSMTP();
                $mail->SMTPAuth   = true;
                $mail->SMTPSecure = 'tls';
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );

                $mail->Host     = $host;
                $mail->Port     = $port;
                $mail->Username = $username;
                $mail->Password = $password;

                // Remetente
                $mail->From       = $from;              // Remetente
                $mail->FromName   = $fromName;
                $mail->ReturnPath = $from;              // Mesmo e-mail do remetente
                $mail->addReplyTo($from, $fromName);    // Responder para...

                // Destinatarios
                $mail->addAddress($email_destinatario, $nome_servidor);
    //            $mail->addCC($from, $fromName);  // Com cópia 
                $mail->addBCC($from, $fromName); // Com cópia oculta
    //            $mail->addBCC('isaque.ribeiro@agilsoftwares.com.br', 'Isaque M. Ribeiro (Analista de Sistemas)'); // Com cópia oculta
    //            $mail->addBCC('isaque.ribeiro@outlook.com', 'Isaque M. Ribeiro (Analista de Sistemas)'); // Com cópia oculta

                $mail->isHTML(true);
                $mail->CharSet  = 'utf-8'; 
                $mail->WordWrap = 70;

                $mail->Subject  = $assunto;
                $mail->Priority = 1;
                $mail->Body     = $mensagem;
                $mail->AltBody  = strip_tags($mensagem);

                $ret = $mail->Send();

                if ( !$ret ){
                    echo '<br>Erro ao tentar enviar e-mail:<br>' . print($mail->ErrorInfo);
                } else {
                    $mail->ClearAllRecipients();
                    $mail->ClearAttachments();  
                }                
            } catch (Exception $ex) {
                echo $ex->getMessage();
            }
        }
        
        return $ret;
    }

    function enviarEmailTeste($mensagem, $assunto, $nome_servidor, $email_destinatario) {
        $ret = false;

        try {
            $mail = new PHPMailer();
            $mail->setLanguage('pt');

            $host     = "smtp.drhtransparencia.com.br";
            $port     = 587;
            $username = "notificacao@drhtransparencia.com.br";
            $password = "notifica2017Transp"; 

            $from     = $username;
            $fromName = 'Notificação DRH Transparência';

            $mail->IsSMTP();
            $mail->SMTPAuth   = true;
            $mail->SMTPSecure = 'tls';
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            $mail->Host     = $host;
            $mail->Port     = $port;
            $mail->Username = $username;
            $mail->Password = $password;

            // Remetente
            $mail->From       = $from;              // Remetente
            $mail->FromName   = $fromName;
            $mail->ReturnPath = $from;              // Mesmo e-mail do remetente
            $mail->addReplyTo($from, $fromName);    // Responder para...

            // Destinatarios
            $mail->addAddress($email_destinatario, $nome_servidor);
//            $mail->addCC($from, $fromName);  // Com cópia 
            $mail->addBCC($from, $fromName); // Com cópia oculta
//            $mail->addBCC('isaque.ribeiro@agilsoftwares.com.br', 'Isaque M. Ribeiro (Analista de Sistemas)'); // Com cópia oculta
//            $mail->addBCC('isaque.ribeiro@outlook.com', 'Isaque M. Ribeiro (Analista de Sistemas)'); // Com cópia oculta

            $mail->isHTML(true);
            $mail->CharSet  = 'utf-8'; 
            $mail->WordWrap = 70;

            $mail->Subject  = $assunto;
            $mail->Priority = 1;
            $mail->Body     = $mensagem;
            $mail->AltBody  = strip_tags($mensagem);

            $ret = $mail->Send();

            if ( !$ret ){
                echo '<br>Erro ao tentar enviar e-mail:<br>' . print($mail->ErrorInfo);
            } else {
                $mail->ClearAllRecipients();
                $mail->ClearAttachments();  
            }                
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        
        return $ret;
    }