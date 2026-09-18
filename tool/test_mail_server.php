<? require_once("../include/PHPMailer/class.phpmailer.php"); ?>
<?php
    /*$mail_server_id = $_REQUEST['mail_server_id'];
    
    $sql_sel_mail_server = "SELECT * FROM mail_server WHERE mail_server_id = $mail_server_id";
    $qry_sel_mail_server = pg_query($sql_sel_mail_server) or die('ERROR select mail_server: '.$sql_sel_mail_server);
    $rowmail = pg_fetch_assoc($qry_sel_mail_server);*/
    
    $email_from	="noreply@indocorp.com";
    $email_host	="202.51.252.22";
	$email_address = "app_dev@indointernal.com";
    
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->From = $email_from;
    $mail->FromName = 'E-statement';
    $mail->Host = $email_host;
    $mail->Mailer   = "smtp";
    $mail->Subject  = 'Test E-statement BRI';
    $mail->AddAddress($email_address, 'APPDEV');
    //$mail->AddAddress($email_from, 'APPDEV');
            
    if($mail->Send()){	
        echo 'Success send to : <br>'.$email_address;
    }else{
        echo 'Failed send to : <br>'.$email_address;
    }
    
    $mail->ClearAddresses();
?>