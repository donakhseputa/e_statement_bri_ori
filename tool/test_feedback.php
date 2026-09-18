<?php
	$email_inbox = "{mail.e-statement.net:110/pop3/tls/novalidate-cert}INBOX";
	$email_from = "Sun_Life_Financial_Indonesia@e-statement.net";
	$email_pass = "cim123";
	
	//$mbox = imap_open("{your.imap.host:143}", "username", "password");
	$mbox = imap_open($email_inbox, $email_from, $email_pass) or die("can't connect: " . imap_last_error());
	
	$email_bounce_message='';
	
	for($i = 1; $i <= imap_num_msg($mbox); $i++){
		//if($i != 3039){
			//echo $i;
			//die();
			//imap_delete($mbox, 116);
			//imap_expunge($mbox);
			//die();
			$header_info = imap_headerinfo($mbox, $i);
			$slice = preg_replace('/[\=\"\']/','',imap_body($mbox, $i));
			if (strtoupper(substr($header_info->senderaddress, 0, 13)) == "MAILER-DAEMON") {
				echo "<br><br>$i<br>mailer daemon - ".$header_info->subject." - ".$header_info->senderaddress." - ".$header_info->date;
				echo "<br>".$slice;
				//cekBounce($mbox, $i, $header_info->senderaddress);
				//imap_delete($mbox, $i);
			}else if(strtoupper(substr($header_info->subject, 0, 5)) == "READ:"){
				//echo "<br><br>$i<br>read email - ".$header_info->subject." - ".$header_info->date;
				//cekRead($mbox, $i, $header_info->in_reply_to);
			}else if(strtoupper(substr($header_info->subject, 0, 9)) == "IMG EMAIL"){
				//echo "<br><br>$i<br>img email - ".$header_info->subject;
				//cekReadImg($mbox, $i, $header_info->senderaddress);
			}else{
				$header = pg_escape_string($header_info->senderaddress);
				$body = pg_escape_string(imap_body($mbox, $i));
				//echo "<br><br>$i<br>else - ".$header_info->senderaddress;
				
				$sql = " insert into bounce_inbox(
								header_bounce, body_bounce, date_bounce
							)values(
								'$header'::varchar(100), '$body'::varchar(400), now()
							)";
				//@pg_query($sql);
				//imap_delete($mbox, $i);
			}
			echo " - ".$header_info->fromaddress ;
		//}
		//if($i == 1000) break;
	}
	//imap_expunge($mbox);
	imap_close($mbox);
?>