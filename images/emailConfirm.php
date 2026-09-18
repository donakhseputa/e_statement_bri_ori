<?php
	header('Content-type: image/jpg');
	require_once("../include/config.php"); 
	require_once("../include/PHPMailer/class.phpmailer.php");
	
	//echo "http://202.51.252.148/e_statement_bri/images/logo_bri.jpg";
	$email=$_REQUEST['email'];
	$mail = new PHPMailer();
	$mail->IsSMTP();
	$mail->From = 'noreply@indocorp.com';//$email_from;
	$mail->FromName = 'TEST';
	$mail->Host = '202.51.252.22';
	$mail->Mailer = "smtp";
	//if(trim($email_reply)!='') 
	$mail->AddReplyTo('', 'No Reply');//$from_name);
	//$mail->ConfirmReadingTo = 'app_dev@indointernal.com';
	//$mail->AddAddress('indomedia.appdev@gmail.com', 'YOPIE');
	$mail->AddAddress('app_dev@indocorp.com', 'YOPIE');
	$mail->Subject = 'Email dari IMG';
	$mail->MsgHTML("<html><body>".$email."</body></html>");
	$mail->IsHTML(true);
	//if (!$mail->Send()) echo $mail->ErrorInfo; else echo "success!!";
	$mail->Send();
	$mail->ClearAddresses();
	$mail->ClearAttachments();
	header("Location: http://202.51.252.148/e_statement_bri/images/logo_bri.jpg");
	//$mail->AddAttachment($pdf_location.$pdf_name,$pdf_name);
?>