<?php
/* CONFIG DATABASE */
//die("maintenance");
#die("maintenance");

$www_location = "C:/app/htdocs/e_statement_bri/";					/*untuk live*/ 
ini_set('memory_limit', '-1');
require_once($www_location."include/config.php"); 
require_once($www_location."include/message.php"); 
require_once($www_location."include/function.php"); 
require_once($www_location."include/parseUserAgentString.php"); 
//rumus_tipe_gagal.php
require_once($www_location."include/rumus_tipe_gagal.php"); 

$con = pg_connect($connection) or die("Could not connect to database!");
	
	
	function update_tbl_bounce_inbox()
	{
		
		$sql="select a.body_bounce, a.bounce_inbox_id, b.date_email_callback , a.date_bounce, b.tr_email_id, b.email as email2
		from bounce_inbox a
		INNER JOIN tr_email b
		ON b.date_email_callback = a.date_bounce 
		WHERE (a.email ~ '^([0-9]+[.]?[0-9]*|[.][0-9]+)$') OR a.email IS NULL OR a.email=''
		order by a.bounce_inbox_id ASC";
		
		//WHERE (a.email ~ '^([0-9]+[.]?[0-9]*|[.][0-9]+)$') OR a.email IS NULL OR a.email=''
		
		/*
		$sql ="select body_bounce, bounce_inbox_id
			from bounce_inbox 
			WHERE (email ~ '^([0-9]+[.]?[0-9]*|[.][0-9]+)$') OR email IS NULL OR email=''
			order by bounce_inbox_id ASC";
						//Where date_bounce between '2014-12-21 17:41:09' AND '2014-12-23 18:27:33'	
		*/
		
		$qry = @pg_query($sql);
		
		while($row = pg_fetch_array($qry))
		{
			$email='';
			$email=extract_emails_from($row['body_bounce']);
			$www= (!empty($email[0]))?  $email[0] : $row['email2'] ;
			
			$sql2 = "UPDATE bounce_inbox
			SET
			email=upper('".$www."'),
			tr_email_id='".$row['tr_email_id']."'
			Where bounce_inbox_id=".$row['bounce_inbox_id']."";
			@pg_query($sql2);
		}
	
	
	}
	
	
	function split_ket_error_gagal($ket_error)
	{
	
			$x = trim(preg_replace('/\s\s+/', ' ', $ket_error));
			$string = trim(preg_replace('/\s+/', ' ', $x));
			//echo $string;
			
			//preg_match('~:(.*?)-~', $string, $output);
			//echo "<br>".$output[1]; // 256
			
			$n=explode("Below", $string);
			//echo "<br>".$n[0]; // 256
			
			return $n[0];
	
	}
	
	function split_ket_error_delay($ket_error)
	{
	
			$x = trim(preg_replace('/\s\s+/', ' ', $ket_error));
			$string = trim(preg_replace('/\s+/', ' ', $x));
			//echo $string;
			
			//preg_match('~:(.*?)-~', $string, $output);
			//echo "<br>".$output[1]; // 256
			
			$n=explode("Delay reason", $string);
			$yy=explode("This message was created automatically by mail delivery software", $n[0]);
			//echo "<br>".$n[0]; // 256
			
			return $yy[1]." - "."Delay Reason: ". substr($n[1], 0, 550);
	
	}
	
	
	function split_ket_error_gagal_full($ket_error)
	{
	
			$x = trim(preg_replace('/\s\s+/', ' ', $ket_error));
			$string = trim(preg_replace('/\s+/', ' ', $x));
			//echo $string;
			return $string;

	
	}
	
	function update_gagal( $con, $date_email_callback, $ket_error, $email)
	{
		if ( empty($date_email_callback) && empty($ket_error)  && empty($email)  )
		{
			return false;
		}
		
		//$tipe_gagal = get_tipe_gagal ( $ket_error );
		$tipe_gagal = '';
		
		$rev_ket_error= split_ket_error_gagal($ket_error);
		$rev_mm= substr($rev_ket_error, 0, 450);
		
		$sql = "UPDATE tr_email
		SET
		--tipe_gagal = '$tipe_gagal', 
		date_email_callback='$date_email_callback' ,
		ket_error=E'".addslashes (str_replace ('--','', str_replace("'","",$rev_mm)))."',
		email_callback='f'
		
		Where email=upper('$email')  AND email_callback IS NULL 
		AND 
		(cast( '$date_email_callback' as timestamp))-(cast( date_email_send as timestamp)) between  '- 5 hours' and '1 days'
		";
		
		$cek = @pg_query($sql);
		if(@pg_affected_rows($cek) == 0){
			return 'db_gagal';
		}else{
			return 'db_berhasil';
		}
	
	}
	
	
	function update_gagal_last( $con, $date_email_callback, $ket_error, $email, $tr_email_id )
	{
		
		$tipe_gagal = get_tipe_gagal ( $ket_error );
		
		$rev_ket_error= split_ket_error_gagal($ket_error);
		$rev_mm= substr($rev_ket_error, 0, 450);
		
		
	
		$sql = "SELECT * FROM tr_email WHERE tr_email_id=$tr_email_id AND lower(email)=lower('".trim($email)."') AND date_email_callback IS NULL";
		$qry_cek_dat = @pg_query($sql);
		$jml = @pg_num_rows($qry_cek_dat);
		//die($sql . $jml);
        if ($jml > 0) {
			//INSERT BERDASARKAN TR_EMAIL_ID
			//die('with tr_email_id');
			$sql = "UPDATE tr_email
				SET
				tipe_gagal = '$tipe_gagal', 
				nama_customer = '$tipe_gagal', 
				date_email_callback='$date_email_callback' ,
				ket_error=E'".addslashes (str_replace ('--','', str_replace("'","",$rev_mm)))." (with tr_email_id)',
				email_callback='f'
				Where tr_email_id=$tr_email_id  AND email_callback IS NULL 
				";
				
				$cek = @pg_query($sql);
			
			
		}else{
			//INSERT BERDASARKAN EMAIL
			//die('with email');
			$sql = "UPDATE tr_email
			SET
			nama_customer = '$tipe_gagal', 
			date_email_callback='$date_email_callback' ,
			ket_error=E'".addslashes (str_replace ('--','', str_replace("'","",$rev_mm)))." (with email)',
			email_callback='f'
			
			Where lower(email)=lower('$email')  AND email_callback IS NULL 
			AND 
			(cast( '$date_email_callback' as timestamp))-(cast( date_email_send as timestamp)) between  '- 5 hours' and '1 days'
			";
			
			$cek = @pg_query($sql);
			
		}
	
	}
	
	function update_respon($con, $date_email_callback, $ket_error, $email, $message_id1, $pengirim, $tipe_baca, $informasi)
	{
		
		
		
		
		
		$sql='';
		$sql = "UPDATE tr_email
				SET
				respon = respon || '$informasi //' ";
		$y1=(int) $message_id1;
		$sql.=" WHERE tr_email_id=".trim($y1);
		
		
		
		
		$sql_sel_nama_file_rec = "SELECT respon FROM tr_email"." WHERE tr_email_id=".trim($y1);
		$qry_sel_nama_file_rec = @pg_query($sql_sel_nama_file_rec);
		$row_sel_nama_file_rec = pg_fetch_assoc($qry_sel_nama_file_rec);
		$jml_nama_file_rec = $row_sel_nama_file_rec['respon'];
		
				
		//$cek = @pg_query($sql) or die('ERROR1 func update_read: ' . $sql);
		if (empty($jml_nama_file_rec))
		{
			$sql='';
			$sql = "UPDATE tr_email
					SET
					respon = '$informasi //' ";
			$y1=(int) $message_id1;
			$sql.=" WHERE tr_email_id=".trim($y1);
			$cek = @pg_query($sql);
		}else{
			$cek = @pg_query($sql);
		}
		
		if(@pg_affected_rows($cek) == 0){
			return 'db_gagal';
		}else{
			return 'db_berhasil';
		}
	
	}

	function update_read($con, $date_email_callback, $ket_error, $email, $message_id1, $pengirim, $tipe_baca, $info_device='')
	{
		$sql='';
		$sql = "UPDATE tr_email
				SET
				tgl_read='$date_email_callback' ,
				info_device='$info_device' ,
				read_method='$tipe_baca',
				body_email_read=E'".addslashes (str_replace ('--','', str_replace("'","",$ket_error)))."' " ;
				
		if ( !empty( $message_id1) )
		{
			$y1=(int) $message_id1;
			$sql.=" WHERE tr_email_id=".trim($y1)."  AND email_callback IS NULL AND tgl_read IS NULL";
		}else if ( !empty( $email) ){
			$sql.=" WHERE email=upper('$email')  AND email_callback IS NULL AND tgl_read IS NULL
					AND 
					(cast('$date_email_callback' as timestamp))-(cast( date_email_send as timestamp)) between  '- 5 hours' and '14 days' 
					";
		}else if ( !empty( $pengirim) ){
			$sql.=" WHERE email=upper('$pengirim')  AND email_callback IS NULL AND tgl_read IS NULL
					AND  
					(cast('$date_email_callback' as timestamp))-(cast( date_email_send as timestamp)) between  '- 5 hours' and '14 days' 
					";
		}else{
			return 'db_gagal';
		}

		//echo $sql;
		//$cek = @pg_query($sql) or die('ERROR1 func update_read: ' . $sql);
		$cek = @pg_query($sql);
		if(@pg_affected_rows($cek) == 0){
			
			$sql='';
			$sql = "UPDATE tr_email
				SET
				tgl_read='$date_email_callback' ,
				info_device='$info_device' ,
				read_method='$tipe_baca',
				body_email_read=E'".addslashes (str_replace ('--','', str_replace("'","",$ket_error)))."' " ;
			$sql.=" WHERE email=upper('$email')  AND email_callback IS NULL AND tgl_read IS NULL
					AND 
					(cast('$date_email_callback' as timestamp))-(cast( date_email_send as timestamp)) between  '- 5 hours' and '14 days' 
					
					";
			//$cek2 = @pg_query($sql) or die('ERROR2 func update_read: ' . $sql);
			$cek2 = @pg_query($sql);
			if(@pg_affected_rows($cek2) == 0){
				return 'db_gagal';
			}else{
				return 'db_berhasil';
			}
			
			return 'db_gagal';
		}else{
			return 'db_berhasil';
		}
	
	}
	
	function extract_emails_from($string){
		preg_match_all("/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i", $string, $matches);
		//return $matches;
		$tmp=array();
		foreach ($matches[0] as $key => $value) {
			//if ( trim(strtolower($value)) != 'sun_life_financial_indonesia@e-statement.net' && trim(strtolower($value)) != 'sli_care@sunlife.com' && trim(strtolower($value)) != 'sun_life_financial_indonesia@e-statement.net.' && trim(strtolower($value)) != 'sli_care@sunlife.com.')
			//{
				$tmp[]=strtolower($value);
			//}
		}
		$mm= array_unique($tmp);
		return $mm;
	}

	//$email_inbox = "{mail.e-statement.net:110/pop3/tls/novalidate-cert}INBOX";
	//$email_from = "Sun_Life_Financial_Indonesia@e-statement.net";
	//$email_pass = "cim123";
		$cari_slfi='Sun_Life';
		$n_gagal=0;
		$n_read=0;
		$n_other=0;
		$n_img=0;
		$n_respon=0;
		
		$sql_sel_mail_server = "SELECT c.mail_server_id, c.email_from, c.email_pass, c.email_inbox, c.email_bounce_back
							FROM mail_server c
							WHERE c.status ='t' 
							AND mail_server_id=4";
							
							//echo $sql_sel_mail_server;
		
		$qry_sel_mail_server = @pg_query($sql_sel_mail_server);
		
		while($row_mail_server = pg_fetch_array($qry_sel_mail_server))
		{
			$email_inbox = $row_mail_server['email_inbox'];
			$email_from = $row_mail_server['email_bounce_back'];
			$email_pass = $row_mail_server['email_pass'];
			get_from_mailserver($con, $email_inbox,$email_from,$email_pass);
			//update_tbl_bounce_inbox();
				
		}
	
function get_from_mailserver($con,$email_inbox,$email_from,$email_pass)
{
	//$mbox = imap_open("{your.imap.host:143}", "username", "password");
	$n_gagal=0;
	$n_read=0;
	$n_other=0;
	$n_img=0;
	$n_respon=0;
	$mbox = imap_open($email_inbox, $email_from, $email_pass) or die("can't connect: " . imap_last_error());
	
	$email_bounce_message='';
	
	//die('jum = ' .imap_num_msg($mbox));
	
	ini_set('memory_limit', '-1');
	//for($i = 20000; $i <= imap_num_msg($mbox); $i++){ 15040
	//1 januari dari 10691
	#for($i = 1 ; $i <= imap_num_msg($mbox) ; $i++)
	//if ( imap_num_msg($mbox) > 1000 ) $xx=1000;

	//if ( imap_num_msg($mbox) > 20 ) $xx=20;
	//else $xx = imap_num_msg($mbox);
	
	$z11 = 	imap_num_msg($mbox);
	$z22 = $z11 -1000;

	
	//for($i = 1 ; $i <= $xx ; $i++)
	for($i = $z22 ; $i <= $z11 ; $i++)
	{
			$tr_email_id = '';
			$loading_id = '';
				
			$header_info = imap_headerinfo($mbox, $i);
			$slice = preg_replace('/[\=\"\']/','',imap_body($mbox, $i));
			
			
			if ( 
			strtoupper(substr($header_info->senderaddress, 0, 13)) == "MAILER-DAEMON"
			&&  preg_match("/amazon/i", $header_info->senderaddress)
			)
			{
				//continue;
				
				$abcd=extract_emails_from(imap_body($mbox, $i));
				$pengirim = trim($header_info->senderaddress);
				
				$status='error';
				$rev_ket_error_full= split_ket_error_gagal_full( substr(imap_body($mbox, $i), 0, 1000)  );
				$rev_ket_error= split_ket_error_gagal( substr(imap_body($mbox, $i), 203, 797) );
				//die($rev_ket_error_full);
				$w=array('(WIB)','(WIT)','(WITA)','(UTC)','(PST)','(GMT)','(CST)','(PDT)','(EDT)','(ICT)','(CIT)','(SGT)', '(CDT)');
				$xyz= str_replace($w, '' ,$header_info->date );
				
				$imil = strtoupper($abcd[0]);
				//echo "$imil <br>";
				
				$slice = preg_replace('/[\=\"\']/','',imap_body($mbox, $i));
					$arr_message = explode("|",$slice);
					
				//AMBIL blth dari hidden input
				$email_bounce_message = preg_replace('/[\'\/]/','',$email_bounce_message);
				$client_feedback = $arr_message[1];
				$flagtrans = $arr_message[2];
				$tr_email_id = $arr_message[3];
				
				if (empty($loading_id)) $loading_id = 0;
				if (empty($tr_email_id)) $tr_email_id = 0;
				
				
				insert_tabel_gagal($con, $header_info->subject, $rev_ket_error_full, $xyz, '', $tr_email_id, $loading_id, '', $imil, 'f', $status);
				update_gagal_last_sk_tr_amazon( $con, $xyz, $rev_ket_error, $imil, $tr_email_id );
				
				$n_gagal++;
				
				imap_delete($mbox, $i);
				
				//die ("DARI AMAZON:". strtoupper($abcd[0]).", $message_id1, $pengirim , $xyz");
				
			}else 
			if ( strtoupper(substr($header_info->senderaddress, 0, 13)) == "MAILER-DAEMON" 
				|| strtoupper(substr($header_info->subject, 0, 7)) == "UNDELIV" 
				|| strtoupper(substr($header_info->subject, 0, 7)) == "WARNING" 
				|| strtoupper(substr($header_info->senderaddress, 0, 10)) == "POSTMASTER"
				|| strtolower(substr($header_info->subject, 0, 14)) == "returned mail:"
				|| strtoupper(substr( trim( $header_info->senderaddress ), 0, 20)) == "MAIL DELIVERY SYSTEM" 
				|| strtoupper(substr( trim( $header_info->subject ), 0, 20)) == "MAIL DELIVERY FAILED" 
				|| strtolower(substr( trim( $header_info->subject ), -9)) == "(failure)" 
				|| strtoupper(substr( trim( $header_info->subject ), 0, 7)) == "FAILURE" 
				) 
			{
				//continue;
				$status='error';
				$arr_1=array();
				

				$abcd=extract_emails_from(imap_body($mbox, $i));
				 

				$pengirim = trim($header_info->senderaddress);
				
				 
				$w=array('(WIB)','(WIT)','(WITA)','(UTC)','(PST)','(GMT)','(CST)','(PDT)','(EDT)','(ICT)','(CIT)','(SGT)', '(CDT)');
				$xyz= str_replace($w, '' ,$header_info->date );
				 



				   




				
				$arr_1 = get_tr_email_id_gagal($con,strtoupper($abcd[0]), $message_id1, strtoupper($pengirim) , $xyz);
				$rev_ket_error= split_ket_error_gagal( substr(imap_body($mbox, $i), 203, 797) );
				$rev_ket_error_delay= split_ket_error_delay( imap_body($mbox, $i) );
				//$rev_ket_error_full= split_ket_error_gagal_full( substr(imap_body($mbox, $i), 203, 797) );
				$rev_ket_error_full= split_ket_error_gagal_full( imap_body($mbox, $i) );
				
							   


				//die (print_r($arr_1));
				$tr_email_id_x = $arr_1[0];
				$loading_id = $arr_1[1];
				$flagtrans='';
										
				
				/*
				echo "<br><br>$i GAGAL<br>".
				"##date: ".$header_info->date.
				"<br>## message id1: ".$header_info->in_reply_to.
				"<br>## message id2: ".$header_info->references.
				"<br>## pengirim: ".$header_info->senderaddress.
				"<br>## judul: ".$header_info->subject.
				"<br>## EMAIL: ".strtoupper($abcd[0]).
				"<br>## TR_EMAIL_ID: ".$tr_email_id.
				"<br>## BODY: ".substr(imap_body($mbox, $i), 203, 797);
				*/
				
			   
								  


				 
				 if ( !empty( $abcd[0] ) )
				{
					$imil=trim(strtoupper($abcd[0]));
				}else{
					$imil=$header_info->in_reply_to;
				}
				
				
				$start = 'Message-ID:';
				$end = 'X-Priority:';
				$tr_email_id= trim(get_string_between($rev_ket_error_full, $start, $end));
				
				#####if ($tr_email_id > 400000  && $tr_email_id != 3204313 ) continue;#####
				
				if ( !is_numeric($tr_email_id) || empty ($tr_email_id) ) $tr_email_id = $tr_email_id_x;
				
				insert_tabel_gagal($con, $header_info->subject, $rev_ket_error_full, $xyz, $blth, $tr_email_id, $loading_id, $flagtrans, $imil, 'f', $status);
				
				//Warning: message 1fEVRa-000231-Ax delayed 24 hours
				if ( preg_match("/delayed/i", $header_info->subject)  )
				{
					update_delay_last( $con, $xyz, $rev_ket_error_delay, $imil, $tr_email_id );
					imap_delete($mbox, $i);
					continue;
				}
				//Warning: message 1fEVRa-000231-Ax delayed 24 hours
				
				if ($tr_email_id > 1000000000000 ) //1.000.000.000.000 (1 triliun)
				{
					update_gagal_last_sk_tr( $con, $xyz, $rev_ket_error, $imil, $tr_email_id );
				}else{
					update_gagal_last( $con, $xyz, $rev_ket_error, $imil, $tr_email_id );
				
				}
				$n_gagal++;
				
				imap_delete($mbox, $i);
				
				
			}else if( strtoupper(substr($header_info->subject, 0, 5)) == "READ:" 
					|| strtoupper(substr($header_info->subject, 0, 5)) == "[READ"
					|| strtoupper(substr($header_info->subject, 0, 12)) == "LAPORAN BACA" 
					|| strtoupper(substr($header_info->subject, 0, 2)) == "RE" 
					|| strtoupper(substr($header_info->subject, 0, 3)) == "BLS"
					|| strtoupper(substr($header_info->subject, 0, 3)) == "HAL"
					|| strtolower(substr($header_info->subject, 0, 6)) == "return" 
					|| strtolower(substr($header_info->subject, 0, 7)) == "checked"  
					|| strtolower(substr($header_info->subject, 0, 8)) == "checked "  
					|| strtolower(substr($header_info->subject, 0, 8)) == "dpriksa:"  
					|| strtolower(substr($header_info->subject, 0, 8)) == "dpriksa "  
					|| strtolower(substr($header_info->subject, 0, 6)) == "dibaca"  
					|| strtolower(substr($header_info->subject, 0, 7)) == "dibaca "  
					|| strtolower(substr($header_info->subject, 0, 13)) == "laporan baca " 
					|| strtolower(substr($header_info->subject, 0, 6)) == "sudah "  
					|| strtolower(substr($header_info->subject, 0, 13)) == "sudah dibaca:"  
					|| strtolower(substr($header_info->subject, 0, 26)) == "return receipt (displayed)"  
					|| strtolower(substr($header_info->subject, 0, 14)) == "return receipt"  
					|| strtolower(substr($header_info->subject, 0, 14)) == "returned mail:"  
					|| strtolower(substr(trim($header_info->subject), 0, 6)) == "return"  
					|| strtolower(substr(trim($header_info->subject), 0, 9)) == "automatic" 
					|| strtolower(substr(trim($header_info->subject), 0, 4)) == "auto" 
					|| strtoupper(substr($header_info->subject, 0, 2)) == "E-" 
					 
					)
			{
				
				//continue;
				$status='read';
				
				$abcd=extract_emails_from(imap_body($mbox, $i));
				
				
				/*
				echo "<br><br>$i READs<br>".
				"##date: ".$header_info->date.
				"<br>## message id1: ".$header_info->in_reply_to.
				"<br>## message id2: ".$header_info->references.
				"<br>## pengirim: ".$header_info->senderaddress.
				"<br>## judul: ".$header_info->subject.
				"<br>## EMAIL: ".strtoupper($abcd[0]).
				"<br>## TR_EMAIL_ID: ".$tr_email_id.
				"<br>## BODY: ".substr(imap_body($mbox, $i), 0, 1000);
				*/
				
				$message_id1 = $header_info->in_reply_to;
				
				$pengirim = trim($header_info->senderaddress);
				
				
				$w=array('(WIB)','(WIT)','(WITA)','(UTC)','(PST)','(GMT)','(CST)','(PDT)','(EDT)','(ICT)','(CIT)','(SGT)', '(CDT)');
				$xyz= str_replace($w, '' ,$header_info->date );
				
				if ($message_id1 > 1000000000000 ) //1.000.000.000.000 (1 triliun)
				{
					$cek_delete2 = update_read_sk_tr( $con, $xyz, substr(imap_body($mbox, $i), 0, 1000), strtoupper($abcd[0]), $message_id1, strtoupper($pengirim) ,'outlook');
				}else{
					$cek_delete2 = update_read( $con, $xyz, substr(imap_body($mbox, $i), 0, 1000), strtoupper($abcd[0]), $message_id1, strtoupper($pengirim) , 'outlook' );
				}
				#$cek_delete2 = update_read( $con, $xyz, substr(imap_body($mbox, $i), 0, 1000), strtoupper($abcd[0]), $message_id1, strtoupper($pengirim) , 'outlook' );
				if ( $cek_delete2 == 'db_berhasil' )
				{
					imap_delete($mbox, $i);
					$n_read++;
					continue;
				}
				
				$tr_email_id = get_tr_email_id_read($con,strtoupper($abcd[0]), $message_id1, strtoupper($pengirim) , $xyz);
				#####if ($tr_email_id > 400000  && $tr_email_id != 3204313 ) continue;#####
				
				if ( empty($tr_email_id) || !intval($tr_email_id) ) {
					insert_tabel_read($con, $tr_email_id, $xyz, $slice, 'outlook'); 
					imap_delete($mbox, $i);
					continue;
				}
						   
				insert_tabel_read($con, $tr_email_id, $xyz, $slice, 'outlook');
				imap_delete($mbox, $i);
				
			}else if(strtoupper(substr($header_info->subject, 0, 9)) == "IMG EMAIL"){
				
				//continue;
				$status='img';
				
				//tambahan
				$arr_bounce = explode(chr(13) . chr(10), imap_body($mbox, $i));
				$email_bounce_message = addslashes($arr_bounce[5]);
				$email_bounce_message .= " " . addslashes($arr_bounce[6]);
				
				$slice = preg_replace('/[\=\"\']/','',imap_body($mbox, $i));
				$arr_message = explode("|",$slice);
				
				//ambil tanggal kirimnya email balasan
				$headerinfo = imap_headerinfo($mbox, $i);
				$tgl_kirim = $headerinfo -> date;
				
				//AMBIL blth dari hidden input
				$email_bounce_message = preg_replace('/[\'\/]/','',$email_bounce_message);
				$client_feedback = $arr_message[1];
				$flagtrans = $arr_message[2];
				$tr_email_id = $arr_message[3];
				$email = $arr_message[4];
							
				$info_device = $arr_message[5];
				
				$info_device = user_agent_parser($info_device);
				//tambahan
				
				#####if ($tr_email_id > 400000  && $tr_email_id != 3204313 ) continue;#####
				
				$abcd=extract_emails_from(imap_body($mbox, $i));
				
				/*
				echo "<br><br>$i IMG EMAIL<br>".
				"##date: ".$header_info->date.
				"<br>## message id1: ".$header_info->in_reply_to.
				"<br>## message id2: ".$header_info->references.
				"<br>## pengirim: ".$header_info->senderaddress.
				"<br>## judul: ".$header_info->subject.
				"<br>## EMAIL: ".strtoupper($abcd[0]).
				"<br>## TR_EMAIL_ID: ".$tr_email_id.
				"<br>## BODY: ".substr(imap_body($mbox, $i), 0, 1000);
				*/
				
				$message_id1 = $tr_email_id;
				//die( $message_id1 );
				$pengirim = trim($header_info->senderaddress);
				
				$w=array('(WIB)','(WIT)','(WITA)','(UTC)','(PST)','(GMT)','(CST)','(PDT)','(EDT)','(ICT)','(CIT)','(SGT)', '(CDT)');
				$xyz= str_replace($w, '' ,$header_info->date );
				
							
				if ($tr_email_id > 1000000000000 ) //1.000.000.000.000 (1 triliun)
				{
					$cek_delete2 = update_read_sk_tr( $con, $xyz, substr(imap_body($mbox, $i), 0, 1000), strtoupper($abcd[0]), $message_id1, strtoupper($pengirim) ,'img_email', $info_device);
				}else{
					$cek_delete2 = update_read( $con, $xyz, substr(imap_body($mbox, $i), 0, 1000), strtoupper($abcd[0]), $message_id1, strtoupper($pengirim) ,'img_email', $info_device);
				}

					 
				if ( $cek_delete2 == 'db_berhasil' )
				{
					imap_delete($mbox, $i);
					$n_img++;
					continue;
				}
				
				
				#####$tr_email_id = get_tr_email_id_read($con,strtoupper($abcd[0]), $message_id1, strtoupper($pengirim) , $xyz);
				

				

				if ( empty($tr_email_id) || !intval($tr_email_id) ) {
					imap_delete($mbox, $i);
					continue;
				}
				
				insert_tabel_read($con, $tr_email_id, $xyz, $slice, 'img');
				imap_delete($mbox, $i);
				
			}else{
				/*
				$n_other++;
				imap_delete($mbox, $i);
				
				continue;
				*/
				$header = imap_header($mbox, $i); 
				$header_info = imap_headerinfo($mbox, $i);
				$blth = date("mY");

				$header = pg_escape_string($header_info->senderaddress);
				$body = pg_escape_string(imap_body($mbox, $i));
				$tgl1 = strtotime($header_info->date);
				$tgl_read = date("Y-m-d H:i",$tgl1);
				cekOthers($mbox, $i, $header, $body, $tgl_read, $blth, $flagtrans);
				
				/*
				echo "<br><br>$i OTHERS<br>".
				"##date: ".$header_info->date.
				"<br>## message id1: ".$header_info->in_reply_to.
				"<br>## message id2: ".$header_info->references.
				"<br>## pengirim: ".$header_info->senderaddress.
				"<br>## judul: ".$header_info->subject.
				"<br>## EMAIL: ".strtoupper($abcd[0]).
				"<br>## TR_EMAIL_ID: ".$tr_email_id.
				"<br>## BODY: ".substr(imap_body($mbox, $i), 0, 1000);
				*/
				
				
				
				
				$n_other++;
				imap_delete($mbox, $i);
			}

	}
			//SIMPAN HISTORY AUTO-FEEDBACK
			$sql_history = "INSERT INTO autofeedback_history(
			autofeedback_date, autofeedback_gagal, autofeedback_sukses_img, autofeedback_sukses, autofeedback_others
			) 
			VALUES(NOW(), '$n_gagal', '$n_img', '$n_read', '$n_other')";
			@pg_query($sql_history);
			//SIMPAN HISTORY AUTO-FEEDBACK

	echo "<br>Penarikan Berhasil $email_from<br>GAGAL :$n_gagal
	<br>SUKSES IMG :$n_img
	<br>SUKSES RESPON :$n_respon
	<br>SUKSES READ :$n_read
	<br>OTHERS :$n_other
	<br>"."dari total ".imap_num_msg($mbox)."<hr>";
	imap_expunge($mbox);
	imap_close($mbox);
}
	
	pg_close($con);

	
	
	
	function insert_tabel_read($con, $tr_email_id, $tgl_kirim, $body_email, $ket)
	{
		$sql_ins = "INSERT INTO read_email (
						tr_email_id, waktu_read_email, body_email, keterangan
					)VALUES(
						'".$tr_email_id."', '".$tgl_kirim."', '".addslashes($body_email)."'::varchar(600), '$ket'
					)";
		//$qry_ins = @pg_query($sql_ins);
		$cek = @pg_query($sql_ins);
		if(@pg_affected_rows($cek) == 0){
			return 'db_gagal';
		}else{
			return 'db_berhasil';
		}
	}
	
	
	function insert_tabel_gagal($con, $header, $body, $tgl_read, $blth, $tr_email_id, $loading_id, $flagtrans, $email_bouncenya, $sample, $keterangan)
	{
		//if ( empty( $header ) && empty( $body ) ) return 'db_berhasil';
		//if ( empty( $tgl_read ) ) return 'db_gagal';
		if ( empty( $loading_id ) ) $loading_id=0;
		$sql_insert = " insert into bounce_inbox(header_bounce, body_bounce, date_bounce, status, blth, tr_email_id, m_loading_id, flagtrans, email,sample_status) 
							values(E'".addslashes ($header)."'::varchar(100), E'".addslashes ($body)."'::text, '$tgl_read', '$keterangan', '$blth', '$tr_email_id','$loading_id','$flagtrans', '".substr($email_bouncenya, 0, 39)."','$sample')";
		
		//@pg_query($sql_insert);
		#$cek = @pg_query($sql_insert) or die('ERROR: ' . $sql_insert);
		$cek = @pg_query($sql_insert);
		if(@pg_affected_rows($cek) == 0){
			//die($sql_insert);
			return 'db_gagal';
		}else{
			return 'db_berhasil';
		}
	}
	
	
	
	function get_tr_email_id_read($con, $email, $message_id1, $pengirim, $tgl_baca)
	{
		
		
		if ( !empty( $message_id1) && intval($tr_email_id) )
		{
			return $message_id1;
		}else if ( !empty( $email) ){
			$sql="SELECT tr_email_id FROM tr_email
					WHERE upper(email)=upper('$email')  AND email_callback IS NULL 
					AND
					(cast('$tgl_baca' as timestamp))-(cast( date_email_send as timestamp)) between  '- 5 hours' and '7 days' 
					LIMIT 1";
		}else if ( !empty( $pengirim) ){
			$sql="SELECT tr_email_id FROM tr_email
					WHERE upper(email)=upper('$pengirim')  AND email_callback IS NULL 
					AND 
					(cast('$tgl_baca' as timestamp))-(cast( date_email_send as timestamp)) between  '- 5 hours' and '7 days' 
					LIMIT 1";
		}else{
			return '';
		}
		
		$qry_sel_mail_server = @pg_query($sql) ;
		if(@pg_affected_rows($qry_sel_mail_server) == 0){
			return 0;
		}
		
		while($row_mail_server = pg_fetch_array($qry_sel_mail_server))
		{
			$tr_email_id = $row_mail_server['tr_email_id'];
				
		}
				
		return $tr_email_id;
	
	}
	
	
	function get_tr_email_id_gagal($con, $email, $message_id1, $pengirim, $tgl_baca)
	{

		
		if ( !empty( $message_id1 ) && intval($message_id1))
		{
			$sql=" 
					SELECT tr_email_id, loading_id FROM tr_email
					WHERE tr_email_id = $message_id1 ORDER BY tr_email_id DESC  LIMIT 1";
			
		}else if ( !empty( $email) ){
			$sql=" 
					SELECT tr_email_id, loading_id FROM tr_email
					WHERE upper(email)=upper('$email')  
					AND 
					(cast('$tgl_baca' as timestamp))-(cast( date_email_send as timestamp)) between  '- 5 hours' and '1 days' 
					ORDER BY tr_email_id DESC LIMIT 1";
		}else if ( !empty( $pengirim) ){
			if ( empty ( $tgl_baca ) ) return '';
			$sql=" 
					SELECT tr_email_id, loading_id FROM tr_email
					WHERE upper(email)=upper('$pengirim')  
					AND 
					(cast('$tgl_baca' as timestamp))-(cast( date_email_send as timestamp)) between  '- 5 hours' and '12 days' 
					ORDER BY tr_email_id DESC  LIMIT 1";
		}else{
			return '';
		}
		//return $sql;
		$qry_sel_mail_server = @pg_query($sql);
		
		while($row_mail_server = pg_fetch_array($qry_sel_mail_server))
		{
			$array[0]=$row_mail_server['tr_email_id'];
			$array[1]=$row_mail_server['loading_id'];
			/*
			if ( !empty( $message_id1) )
			{
				$array[0]=$message_id1;
			}
			*/
		}
				
		return $array;
	
	}
	
		
	function cekOthers($mbox, $i, $header, $body, $tgl_read, $blth, $flagtrans)
	{
		$user_others = htmlentities($header);
		$start_others = strpos($user_others,htmlentities("<"));
		$ambil_email_others = substr($user_others,$start_others,60);
		$email_bouncenya = str_replace(array(htmlentities("<"),htmlentities(">")),"",$ambil_email_others);

		$sql_data = "select loading_id,tr_email_id,status_sample FROM tr_email where upper(trim(email)) = upper('$email_bouncenya') and loading_id in (select m_loading_id from m_loading where blth = '$blth')";
		#$exe_data = pg_query($sql_data) or die("Invalid query!".$sql_data);
		$exe_data = @pg_query($sql_data);
		$n_data = @pg_num_rows($exe_data);
		
		if($n_data <> 0){
			$row_data = pg_fetch_array($exe_data);
			$loading_id = $row_data['loading_id'];
			$tr_email_id = $row_data['tr_email_id'];
			$sample = $row_data['status_sample'];
				//masukkan tabel bounce_inbox
				$sql_insert = " insert into bounce_inbox(header_bounce, body_bounce, date_bounce, status, blth, tr_email_id, m_loading_id, flagtrans, email,sample_status) 
								values('$header'::varchar(100), '$body'::varchar(400), '$tgl_read', 'others', '$blth', '$tr_email_id','$loading_id','$flagtrans', '$email_bouncenya','$sample')";
				@pg_query($sql_insert);
				imap_delete($mbox, $i);
		}
		
	}
	
	function get_string_between($string, $start, $end)
	{
		$string = ' ' . $string;
		$ini = strpos($string, $start);
		if ($ini == 0) return '';
		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		return substr($string, $ini, $len);
	}
										  

	
	 function update_gagal_last_sk_tr( $con, $date_email_callback, $ket_error, $email, $tr_email_id )
	{
		$kode1 = $rev_nn= substr($tr_email_id, 0, 1);
		
		$kode1_tr_email_id = substr($tr_email_id, 1, 7);
		$kode1_tr_email_id = (int)$kode1_tr_email_id;
		
		$kode1_m_loading_id = substr($tr_email_id, 8, 5);
		$kode1_m_loading_id = (int)$kode1_m_loading_id;
		
		$tipe_gagal = get_tipe_gagal ( $ket_error );
		
		
		$tr_email_id = $kode1_tr_email_id;
		
		$tabel_tr_email = cek_tabel_tr_email($kode1_m_loading_id);
		
		$rev_ket_error= split_ket_error_gagal($ket_error);
		$rev_mm= substr($rev_ket_error, 0, 450);
		$rev_mm= str_replace("= ","",$rev_mm);
		
		
	
		$sql = "SELECT * FROM $tabel_tr_email WHERE tr_email_id=$tr_email_id AND lower(email)=lower('".trim($email)."') AND date_email_callback IS NULL";
		$qry_cek_dat = @pg_query($sql);
		$jml = @pg_num_rows($qry_cek_dat);
		//die($sql . $jml);
        if ($jml > 0) {
			//INSERT BERDASARKAN TR_EMAIL_ID
			//die('with tr_email_id');
			$sql = "UPDATE $tabel_tr_email
				SET 
				tipe_gagal = '$tipe_gagal', 
				nama_customer = '$tipe_gagal', 
				date_email_callback='$date_email_callback' ,
				ket_error=E'".addslashes (str_replace ('--','', str_replace("'","",$rev_mm)))." (with tr_email_id)',
				email_callback='f'
				Where tr_email_id=$tr_email_id  AND email_callback IS NULL 
				";
				
				$cek = @pg_query($sql);
			
			
		}else{
			//INSERT BERDASARKAN EMAIL
			//die('with email');
			
			$sql = "UPDATE $tabel_tr_email
			SET
			tipe_gagal = '$tipe_gagal', 
			date_email_callback='$date_email_callback' ,
			ket_error=E'".addslashes (str_replace ('--','', str_replace("'","",$rev_mm)))." (with email)',
			email_callback='f'
			
			Where lower(email)=lower('$email')  AND email_callback IS NULL 
			AND 
			(cast( '$date_email_callback' as timestamp))-(cast( date_email_send as timestamp)) between  '- 5 hours' and '1 days'
			";
			
			$cek = @pg_query($sql);
			
		}
		
	} 


function cek_tabel_tr_email($kode1_m_loading_id)
	{
	
			$sql_cek_dat = "SELECT * FROM m_loading WHERE m_loading_id::integer = $kode1_m_loading_id AND m_loading_id > 1995 LIMIT 1";
				//die($sql_cek_dat );
				$qry_cek_dat = pg_query($sql_cek_dat) or die('ERROR cek m_loading: '.$sql_cek_dat);
				$jml = @pg_num_rows($qry_cek_dat);
				$row= @pg_fetch_assoc($qry_cek_dat);
				
				if($jml == 0){
					#die("TABEL TR_EMAIL SK TR TIDAK DITEMUKAN $sql_cek_dat");
					return "tr_email";
				}else{
					return "tr_email_".trim($row['blth']).'_'.trim($row['flagtrans']);
				}
	}
	
	
	
	function update_read_sk_tr($con, $date_email_callback, $ket_error, $email, $tr_email_id, $pengirim, $tipe_baca, $info_device='')
	{
		//$tr_email_id = '1'. str_pad($tr_email_id, 7, 0, STR_PAD_LEFT). str_pad($loading_id, 5, 0, STR_PAD_LEFT);
		$kode1 = substr($tr_email_id, 0, 1);
		
		$kode1_tr_email_id = substr($tr_email_id, 1, 7);
		$kode1_tr_email_id = (int)$kode1_tr_email_id;
		
		$kode1_m_loading_id = substr($tr_email_id, 8, 5);
		$kode1_m_loading_id = (int)$kode1_m_loading_id;
		
		$message_id1 = $kode1_tr_email_id;
		$tabel_tr_email = cek_tabel_tr_email($kode1_m_loading_id);
		#die($tabel_tr_email);
		
		$sql='';
		$sql = "UPDATE $tabel_tr_email
				SET
				tgl_read='$date_email_callback' ,
				read_method='$tipe_baca',
				info_device='$info_device' ,
				body_email_read=E'".addslashes (str_replace ('--','', str_replace("'","",$ket_error)))."' " ;
				
		if ( !empty( $message_id1) )
		{
			$y1=(int) $message_id1;
			$sql.=" WHERE tr_email_id=".trim($y1)."  AND email_callback IS NULL AND tgl_read IS NULL";
		}else if ( !empty( $email) ){
			$sql.=" WHERE email=upper('$email')  AND email_callback IS NULL AND tgl_read IS NULL
					AND 
					(cast('$date_email_callback' as timestamp))-(cast( date_email_send as timestamp)) between  '- 5 hours' and '14 days' 
					";
		}else if ( !empty( $pengirim) ){
			$sql.=" WHERE email=upper('$pengirim')  AND email_callback IS NULL AND tgl_read IS NULL
					AND  
					(cast('$date_email_callback' as timestamp))-(cast( date_email_send as timestamp)) between  '- 5 hours' and '14 days' 
					";
		}else{
			return 'db_gagal';
		}

		#echo $sql;
		//$cek = @pg_query($sql) or die('ERROR1 func update_read: ' . $sql);
		$cek = @pg_query($sql);
		if(@pg_affected_rows($cek) == 0){
			
			$sql='';
			$sql = "UPDATE $tabel_tr_email
				SET
				tgl_read='$date_email_callback' ,
				read_method='$tipe_baca',
				body_email_read=E'".addslashes (str_replace ('--','', str_replace("'","",$ket_error)))."' " ;
			$sql.=" WHERE email=upper('$email')  AND email_callback IS NULL AND tgl_read IS NULL
					AND 
					(cast('$date_email_callback' as timestamp))-(cast( date_email_send as timestamp)) between  '- 5 hours' and '14 days' 
					
					";
			//$cek2 = @pg_query($sql) or die('ERROR2 func update_read: ' . $sql);
			$cek2 = @pg_query($sql);
			if(@pg_affected_rows($cek2) == 0){
				return 'db_gagal';
			}else{
				return 'db_berhasil';
			}
			
			return 'db_gagal';
		}else{
			return 'db_berhasil';
		}
										
	
	
	}
	
	
	
	
	function update_delay_last( $con, $date_email_callback, $ket_error, $email, $tr_email_id )
	{
		////////////////////////////////////////////////////////////////////////////////
		//$tr_email_id = '1'. str_pad($tr_email_id, 7, 0, STR_PAD_LEFT). str_pad($loading_id, 5, 0, STR_PAD_LEFT);
		$kode1 = substr($tr_email_id, 0, 1);
		
		$kode1_tr_email_id = substr($tr_email_id, 1, 7);
		$kode1_tr_email_id = (int)$kode1_tr_email_id;
		
		$kode1_m_loading_id = substr($tr_email_id, 8, 5);
		$kode1_m_loading_id = (int)$kode1_m_loading_id;
		
		$message_id1 = $kode1_tr_email_id;
		$tabel_tr_email = cek_tabel_tr_email($kode1_m_loading_id);
		
		$tr_email_id = $kode1_tr_email_id;
		////////////////////////////////////////////////////////////////////////////////
		
		$tipe_gagal = 'delay';
		
		$rev_ket_error= split_ket_error_gagal($ket_error);
		$rev_mm= substr($rev_ket_error, 0, 600);
		
		#552 exceeded storage allocation
		if ( trim($tipe_gagal)=='552 exceeded storage allocation')
		{
			$kode_uob='15';
		}else{
			$kode_uob='16';
		}
	
		$sql = "SELECT * FROM $tabel_tr_email WHERE tr_email_id=$tr_email_id AND lower(email)=lower('".trim($email)."') AND date_email_callback IS NULL";
		$qry_cek_dat = @pg_query($sql);
		$jml = @pg_num_rows($qry_cek_dat);
		//die($sql . $jml);
        if ($jml > 0) {
			//INSERT BERDASARKAN TR_EMAIL_ID
			//die('with tr_email_id');
			$sql = "UPDATE $tabel_tr_email
				SET
					delay_info = (case when delay_info is null then '' else delay_info end) || E' $date_email_callback - ".addslashes (str_replace ('--','', str_replace("'","",$rev_mm)))." (with tr_email_id) //'
				Where tr_email_id=$tr_email_id  AND email_callback IS NULL 
				";
				
				$cek = pg_query($sql);
			
			
		}else{
			//INSERT BERDASARKAN EMAIL
			//die('with email');
			$sql = "UPDATE $tabel_tr_email
			SET
				delay_info =  (case when delay_info is null then '' else delay_info end) || E' $date_email_callback - ".addslashes (str_replace ('--','', str_replace("'","",$rev_mm)))." (with email) //'
			Where lower(email)=lower('$email')  AND email_callback IS NULL 
			AND 
			(cast( '$date_email_callback' as timestamp))-(cast( date_email_send as timestamp)) between  '- 5 hours' and '1 days'
			";
			
			$cek = pg_query($sql);
			
		}
		
		//die($sql);
	
	}
	
	
	function user_agent_parser( $info_device )
	{
		$arr_message = explode("#####",$info_device);
		$tmp = $arr_message[2];
		$tmp = str_replace('reports:','',$tmp);
		$tmp = trim($tmp);
		
		$info = '';
		$parser = new parseUserAgentStringClass(); // This creates a new instance of this class object.
		$parser->includeAndroidName = true;
		$parser->includeWindowsName = true;
		$parser->includeMacOSName = true;
		$parser->parseUserAgentString($tmp); // This calls the parser function in the class object.

		$info .= "Full: ".$parser->fullname.' // ';
		$info .= "OS Name: ".$parser->osname.' // ';
		$info .= "Browser Name: ".$parser->browsername.' // ';
		$info .= "Browser Version: ".$parser->browserversion.' // ';
		$info .= "Device Type: ".$parser->type.' // ';
		
		if ( $tmp == 'Mozilla/5.0 (Windows NT 5.1; rv:11.0) Gecko Firefox/11.0 (via ggpht.com GoogleImageProxy)' )
		{
			return 'Google Mail App [Name: Google Mail // Developer: Google Inc // Type: Email Client] - ['.$tmp.']';
		}else if  ( $tmp == 'YahooMailProxy; https://help.yahoo.com/kb/yahoo-mail-proxy-SLN28749.html' ){
			return 'Yahoo Mail App [Name: Yahoo MailProxy // Developer: Yahoo! Inc // Type: Bot/Crawler] - ['.$tmp.']';
		}else{
			return $info .' - ['.$tmp.']';
		}
		
		
		
	}
	
	
	
	 function update_gagal_last_sk_tr_amazon( $con, $date_email_callback, $ket_error, $email, $tr_email_id )
	{
		$kode1 = $rev_nn= substr($tr_email_id, 0, 1);
		
		$kode1_tr_email_id = substr($tr_email_id, 1, 7);
		$kode1_tr_email_id = (int)$kode1_tr_email_id;
		
		$kode1_m_loading_id = substr($tr_email_id, 8, 5);
		$kode1_m_loading_id = (int)$kode1_m_loading_id;
		
		$tipe_gagal = get_tipe_gagal ( $ket_error );
		
		
		$tr_email_id = $kode1_tr_email_id;
		
		$tabel_tr_email = cek_tabel_tr_email($kode1_m_loading_id);
		
		$rev_ket_error= split_ket_error_gagal($ket_error);
		$rev_mm= substr($rev_ket_error, 0, 450);
		$rev_mm= str_replace("= ","",$rev_mm);
		
		
	
		$sql = "SELECT * FROM $tabel_tr_email WHERE tr_email_id=$tr_email_id AND lower(email)=lower('".trim($email)."') AND date_email_callback IS NULL";
		$qry_cek_dat = @pg_query($sql);
		$jml = @pg_num_rows($qry_cek_dat);
		//die($sql . $jml);
        if ($jml > 0) {
			//INSERT BERDASARKAN TR_EMAIL_ID
			//die('with tr_email_id');
			$sql = "UPDATE $tabel_tr_email
				SET 
				tipe_gagal = '$tipe_gagal', 
				nama_customer = '$tipe_gagal', 
				date_email_callback='$date_email_callback' ,
				ket_error=E'".addslashes (str_replace ('--','', str_replace("'","",$rev_mm)))." (with tr_email_id)',
				email_callback='f'
				Where tr_email_id=$tr_email_id  AND email_callback IS NULL 
				";
				
				$cek = @pg_query($sql);
			
			
		}else{
			//INSERT BERDASARKAN EMAIL
			//die('with email');
			$tabel_tr_email = 'tr_email_'.date('mY').'_bc';
			$sql = "UPDATE $tabel_tr_email 
			SET
			tipe_gagal = '$tipe_gagal', 
			nama_customer = '$tipe_gagal', 
			date_email_callback='$date_email_callback' ,
			ket_error=E'".addslashes (str_replace ('--','', str_replace("'","",$rev_mm)))." (with email)',
			email_callback='f'
			
			Where lower(email)=lower('$email')  AND email_callback IS NULL 
			AND 
			(cast( '$date_email_callback' as timestamp))-(cast( date_email_send as timestamp)) between  '- 24 hours' and '1 days'
			";
			//die($sql);
			$cek = @pg_query($sql);
			
			$jml2 = @pg_num_rows($cek);
			if ($jml == 0) 
			{
					$tabel_tr_email = 'tr_email_'.GetBLTH(date('mY'),-1).'_bc';


					$sql2 = "UPDATE $tabel_tr_email 
					SET
					tipe_gagal = '$tipe_gagal', 
					nama_customer = '$tipe_gagal', 
					date_email_callback='$date_email_callback' ,
					ket_error=E'".addslashes (str_replace ('--','', str_replace("'","",$rev_mm)))." (with email)',
					email_callback='f'
					
					Where lower(email)=lower('$email')  AND email_callback IS NULL 
					AND 
					(cast( '$date_email_callback' as timestamp))-(cast( date_email_send as timestamp)) between  '- 24 hours' and '1 days'
					";
					//die($sql2);
					$cek2 = @pg_query($sql2);
			
			}
		}
		
	}
	

?>