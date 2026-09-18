<?php $www_location = "D:/App/htdocs/e_statement_bri/";					/*untuk live*/ ?>
<?php require_once($www_location."include/config.php"); ?>
<?php require_once($www_location."include/message.php"); ?>
<?php require_once($www_location."include/function.php"); ?>


<?php
	$con = pg_connect($connection) or die("Could not connect to database!");
	date_default_timezone_set('Asia/Jakarta');
	
	?>

<?php
	
	$con = pg_connect($connection) or die("Could not connect to database!");
	
	//echo $connection;
	  $sql_sel_mail_server = "SELECT c.mail_server_id, c.email_from, c.email_pass, c.email_inbox, c.email_bounce_back
							FROM mail_server c
							WHERE c.status ='t'";
							//echo $sql_sel_mail_server;
		
		$qry_sel_mail_server = pg_query($sql_sel_mail_server) or die('ERROR: select mail server: '.$sql_sel_mail_server);
		
		while($row_mail_server = pg_fetch_array($qry_sel_mail_server))
			{
				$email_inbox = $row_mail_server['email_inbox'];
				$email_from = $row_mail_server['email_bounce_back'];
				$email_pass = $row_mail_server['email_pass'];
				
				get_from_mailserver($email_inbox,$email_from,$email_pass);
				
			}
		
			
			function get_from_mailserver($email_inbox,$email_from,$email_pass)
			{
			//die("FFF = ".$email_pass);
			$blth = date("mY");
			$jum_max_rec = 5000;
			
			//summary untuk history feedback
			$e_gagal = 0;
			$e_sukses_img = 0;
			$e_sukses = 0;
			$e_others = 0;
			
			$mbox = imap_open($email_inbox, $email_from, $email_pass) or die("can't connect: " . imap_last_error());
			$jum_mbox = (int)imap_num_msg($mbox);
			
			echo("jumlah email di inbox ".$email_from .  "  =  ". $jum_mbox) . "<br>";
			imap_close($mbox);
			
			$bnyk_loop = ((int)($jum_mbox / $jum_max_rec)) + 1;
			if($jum_mbox < $jum_max_rec) $jum_max_rec = $jum_mbox;
			 
			for($abc = 1; $abc <= $bnyk_loop; $abc++){
				$mbox = imap_open($email_inbox, $email_from, $email_pass) or die("can't connect: " . imap_last_error());
				$email_bounce_message='';
				for($i = 1; $i <= $jum_max_rec; $i++){
					$header = imap_header($mbox, $i); 
					$header_info = imap_headerinfo($mbox, $i);
					
					/** perubahan 24 Juli 2013 **/
					$subject_email = $header_info->subject;
					
					
					$subject_email = strtolower($subject_email);
					$balik_subject_email = strrev($subject_email);
					$posisi_b = strpos($balik_subject_email,"irb");
					$balik_subject_email = substr($balik_subject_email,0,$posisi_b+3);

					$subject_template_email =  $row_mail_server['subject_email'];
					$periode = substr($blth,0,2).'-'.substr($blth,2,4);
					$periode = period($periode);
					$subjek_template = str_replace("#periode(MonYYYY)",$periode,$subject_template_email);
					$subjek_template = strtolower($subjek_template);
					$balik_subjek_template = strrev($subjek_template);
					$posisi_t = strpos($balik_subjek_template,"irb");		//HARD CODE
					$balik_subjek_template = substr($balik_subjek_template,0,$posisi_t+3);
					/** end perubahan 24 Juli 2013 **/
					
					if 
					(
					
					   (strtoupper(substr($header_info->senderaddress, 0, 13)) == "MAILER-DAEMON") 
					or (strtoupper(substr($header_info->senderaddress, 0, 10)) == "POSTMASTER")
					or (strtoupper(substr($header_info->senderaddress, 0, 20)) == "MAIL DELIVERY SYSTEM")  
					
					)
					
					
					{
						cekBounce($mbox, $i, $header_info->senderaddress);
						imap_delete($mbox, $i);
						$e_gagal += 1;	//TOTAL EMAIL GAGAL
					}
					
				/** perubahan 24 juli 2013 **/
				
				//jika subject email sama persis dengan template subject, maka masukkan ke OTHERS
				else if($subjek_template == $subject_email){
					$header = pg_escape_string($header_info->senderaddress);
					$body = pg_escape_string(imap_body($mbox, $i));
					$tgl1 = strtotime($header_info->date);
					$tgl_read = date("Y-m-d H:i",$tgl1);
					cekOthers($mbox, $i, $header, $body, $tgl_read, $blth, $flagtrans);
				} else {
				//jika berbeda, maka:

					//1. jika IMG EMAIL, maka:
					if(strtoupper(substr($header_info->subject, 0, 9)) == "IMG EMAIL")
					{
						cekReadImg($mbox, $i, $header_info->senderaddress);
						$e_sukses_img += 1;	//TOTAL EMAIL SUKSES(IMG)
						} else if 
							(
								(($balik_subject_email==$balik_subjek_template)&&(strtolower(substr($header_info->subject, 0, 5)) == "read:"  ))
								or (($balik_subject_email==$balik_subjek_template)&&(strtolower(substr($header_info->subject, 0, 6)) == "return"  ))
								or (($balik_subject_email==$balik_subjek_template)&&(strtolower(substr($header_info->subject, 0, 7)) == "checked"  ))	
								or (($balik_subject_email==$balik_subjek_template)&&(strtolower(substr($header_info->subject, 0, 8)) == "checked "  ))	
								or (($balik_subject_email==$balik_subjek_template)&&(strtolower(substr($header_info->subject, 0, 8)) == "dpriksa:"  ))	
								or (($balik_subject_email==$balik_subjek_template)&&(strtolower(substr($header_info->subject, 0, 8)) == "dpriksa "  ))	
								or (($balik_subject_email==$balik_subjek_template)&&(strtolower(substr($header_info->subject, 0, 6)) == "dibaca"  ))	
								or (($balik_subject_email==$balik_subjek_template)&&(strtolower(substr($header_info->subject, 0, 7)) == "dibaca "  ))
								or (($balik_subject_email==$balik_subjek_template)&&(strtolower(substr($header_info->subject, 0, 6)) == "sudah "  ))	
								or (($balik_subject_email==$balik_subjek_template)&&(strtolower(substr($header_info->subject, 0, 26)) == "return receipt (displayed)"  ))
								or (($balik_subject_email==$balik_subjek_template)&&(strtolower(substr($header_info->subject, 0, 14)) == "return receipt"  ))
								or (($balik_subject_email==$balik_subjek_template)&&(strtolower(substr(trim($header_info->subject), 0, 6)) == "return"  ))
								or (($balik_subject_email==$balik_subjek_template)&&(strtolower(substr($header_info->subject, 0, 3)) == "re:"  ))	
								or (($balik_subject_email==$balik_subjek_template)&&(strtolower(substr($header_info->subject, 0, 4)) == "bls:"  ))	
							)
						{
					//2. jika merupakan email TERBACA, maka:
							//$reference = preg_replace($patterns, $replacements, $references);
									
										if((int)$header_info->in_reply_to)
											{
												$tr_email_id = $header_info->in_reply_to;
											}
										else if((int)$header_info->references)
											{
												$tr_email_id =  $header_info->references;
												
											}
											else{
											$headernya = pg_escape_string($header_info->senderaddress);
											$user_others = htmlentities($headernya);
											$start_others = strpos($user_others,htmlentities("<"));
											$ambil_email_others = substr($user_others,$start_others,60);
											$email_bouncenya = str_replace(array(htmlentities("<"),htmlentities(">")),"",$ambil_email_others);
												$tr_email_id = "xxxx|~~~|".$email_bouncenya;
											}
											
									cekRead($mbox, $i, $tr_email_id, $blth,strtolower($email_bounce));
									$e_sukses += 1;
						} else 
						{
					//3. sisanya dimasukkan sebagai OTHERS
							$header = pg_escape_string($header_info->senderaddress);
							$body = pg_escape_string(imap_body($mbox, $i));
							$tgl1 = strtotime($header_info->date);
							$tgl_read = date("Y-m-d H:i",$tgl1);
							cekOthers($mbox, $i, $header, $body, $tgl_read, $blth, $flagtrans);
							
							$e_others += 1;	//TOTAL EMAIL OTHERS
					}

				}
				/** end perubahan 24 juli 2013 **/
				
				}
				imap_expunge($mbox);
				imap_close($mbox);
			}
			
			//SIMPAN HISTORY AUTO-FEEDBACK
			$sql_history = "INSERT INTO autofeedback_history(
			autofeedback_date, autofeedback_gagal, autofeedback_sukses_img, autofeedback_sukses, autofeedback_others
			) 
			VALUES(NOW(), '$e_gagal', '$e_sukses_img', '$e_sukses', '$e_others')";
			@pg_query($sql_history) or die('ERROR: ' . $sql_history);
			
		}
		return 'connect';
	
	
	
function cekOthers($mbox, $i, $header, $body, $tgl_read, $blth, $flagtrans){
	$user_others = htmlentities($header);
	$start_others = strpos($user_others,htmlentities("<"));
	$ambil_email_others = substr($user_others,$start_others,60);
	$email_bouncenya = str_replace(array(htmlentities("<"),htmlentities(">")),"",$ambil_email_others);

	$sql_data = "select loading_id,tr_email_id,status_sample FROM tr_email where lower(trim(email)) = '$email_bouncenya' and loading_id in (select m_loading_id from m_loading where blth = '$blth')";
	$exe_data = pg_query($sql_data) or die("Invalid query!".$sql_data);
	$n_data = pg_num_rows($exe_data);
	
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
	
	
	
	
	function cekBounce($mbox, $i, $header){
		$arr_bounce = explode(chr(13) . chr(10), imap_body($mbox, $i));
		$email_bounce_message = addslashes($arr_bounce[5]);
		$email_bounce_message .= " " . addslashes($arr_bounce[6]);
		
		$slice = preg_replace('/[\=\"\']/','',imap_body($mbox, $i));
		$arr_message = explode("|",$slice);
		
		//AMBIL blth dari hidden input
		$email_bounce_message = preg_replace('/[\'\/]/','',$email_bounce_message);
		$client_feedback = $arr_message[1];
		$flagtrans = $arr_message[2];
		
		$reference = $header_info->references;

		$patterns = array();
		$patterns[0] = "'<'";
		$patterns[1] = "'>'";
		
		$replacements = array();
		$replacements[1] = '';
		$replacements[0] = '';

		$reference = preg_replace($patterns, $replacements, $reference);
		
		if((int)$header_info->in_reply_to)
				{
				$tr_email_id = $header_info->in_reply_to;	
			
				}
		elseif((int)$reference)
				{
				$tr_email_id = $reference;	
			
				}
		
		else
				{		
					$tr_email_id = $arr_message[3];
				}
		
		$email = $arr_message[4];
		
		if($email==''){
			// Ambil Alamat Email Dari Error Msg
			preg_match('/\(([0-9a-zA-Z\.\-\_\@]+)\)/',addslashes($arr_bounce[6]),$matches); 
			$email = $matches[1];
		}elseif($email==''){
			preg_match('/<([0-9a-zA-Z\.\-\_\@]+)>/',addslashes($arr_bounce[6]),$matches); 
			$email = $matches[1];
		} 
		
		if(trim($email)<>'' && (int)$tr_email_id){ 
			$sql = "UPDATE tr_email";
			$sql .= " SET email_callback = FALSE, date_email_callback = NOW(), ket_error='$email_bounce_message'";
			$sql .= " WHERE tr_email_id = $tr_email_id and  lower(trim(email)) = '".strtolower(trim($email))."'";
			$query = pg_query($sql) or die("Invalid query!".$sql);
			
			if(!pg_affected_rows($query)){
				$sql = "	INSERT INTO bounce_inbox(";
				$sql .= " 			header_bounce, body_bounce, date_bounce, status";
				$sql .= " 		)VALUES(";
				$sql .= "			'$header'::varchar(100), '$slice'::varchar(400), now(),'error')";
				
				 @pg_query($sql);
			}
		}else{
			$sql = "	INSERT INTO bounce_inbox(";
			$sql .= " 			header_bounce, body_bounce, date_bounce, status";
			$sql .= " 		)VALUES(";
			$sql .= "			'$header'::varchar(100), '$slice'::varchar(400), now(), 'error')";
			
			 @pg_query($sql);
		}
	}
	
	function cekRead($mbox, $i, $tr_email_id, $blth, $email_bounce){
		//ambil tanggal kirimnya email balasan
		$headerinfo = imap_headerinfo($mbox, $i);
		$tgl_read = $headerinfo -> date;
		
		$tgl1 = strtotime($headerinfo->date);
		$tgl_read = date("Y-m-d H:i",$tgl1);
		

		
		// *** baca dlm format html jika ada
					
					$dataTxt = get_part($mbox, $i , "TEXT/PLAIN");
               
						// GET HTML BODY
						$dataHtml = get_part($mbox, $i , "TEXT/HTML");
					
						if ($dataHtml != "") {
							$dataHtml_lines = explode("\n", $dataHtml);
							unset($dataHtml);
							foreach ($dataHtml_lines as $x => $line) {
								$dataHtml .= $line . "\n";
							}
							$dataHtml = eregi_replace("<body[^>.]*>","<body>",$dataHtml);
							$msgBody = $dataHtml;
						}
						else {
							$dataTxt_lines = explode("\n", $dataTxt);
							unset($dataTxt);
							foreach ($dataTxt_lines as $x => $line) {
								$dataTxt .= htmlspecialchars($line) . "<br />\n";
							}
							$msgBody = $dataTxt;
						}
							
					// ***
		
		$slice = preg_replace('/[\=\"\']/','',imap_body($mbox, $i));
		$slice = $msgBody ;
		
		if($tgl_read != '')
		{
			if(substr($tr_email_id,0,9)<>'xxxx|~~~|')
				{
					$sql_ins = 
					"INSERT INTO read_email (
							tr_email_id, waktu_read_email, body_email, keterangan
						)VALUES(
							".$tr_email_id.", '".$tgl_read."', '".addslashes($slice)."'::varchar(600), 'outlook'
						)";
					$qry_ins = @pg_query($sql_ins);
					$affect_row = @pg_affected_rows($qry_ins);
					if( $affect_row > 0)
					{
						$sql = "UPDATE tr_email";
						$sql .= " SET tgl_read = '".$tgl_read."', read_method='outlook', body_email_read = '".addslashes($slice)."'::varchar(600)";
						$sql .= " WHERE tr_email_id = ".$tr_email_id;
						$sql .= " AND tgl_read is null";
				  $query = @pg_query($sql);
						imap_delete($mbox, $i);
					}
				}
			 else  //jika in_reply_to kosong, pakai alamat email utk masukkan ke read_email 
			 {
			 

		
			 $email_bouncenya = strtolower(trim(substr($tr_email_id,9,50)));
			 $sql_insert = " select tr_email_id, '".$tgl_read."' AS tgl_read,'".addslashes($slice)."' AS body_email, 'outlook' FROM tr_email where lower(trim(email)) = '$email_bouncenya' and loading_id in (select m_loading_id from m_loading where blth = '$blth') AND tgl_read is null ";
			 $sql_inserto = " selet tr_email_id, '".$tgl_read."' AS tgl_read,'".addslashes($slice)."' AS body_email, 'outlook' pROM tr_email mhere lower(trim(email)) = '$email_bouncenya' ant loading_id in (selet m_loading_id prom m_loading mhere blth = '$blth') ANT tgl_read is null ";
			
			//die($sql_insert) . "<br>";
			$qry_sql_insert = pg_query($sql_insert) or die('ERROR: '.$sql_insert);
		    $jumlah_data = pg_num_rows($qry_sql_insert);
		
				if ($jumlah_data > 0)
				{
				
				$row = pg_fetch_row($qry_sql_insert);
				$tr_email_id = $row[0] ;
				$tgl_read = $row[1] ;
				$body_email_read = $row[2] ;
		
						$sql = "UPDATE tr_email SET tgl_read = '".$tgl_read."', body_email_read = '".addslashes($body_email_read)."'
								WHERE tr_email_id = '$tr_email_id'
								AND tgl_read is null";
						$query = @pg_query($sql);
						imap_delete($mbox, $i);
				}
				// else{
					// $sql_i = "INSERT INTO tampung(tgl_read,body_email,email,tr_email_id) VALUES('$tgl_read','".addslashes($sql_inserto)."','$email_bouncenya','$tr_email_id')";
					// $query = @pg_query($sql_i); 
				// }
				
			
			 }
				
		}
	}
	
	function cekReadImg($mbox, $i, $header){
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
		
		if(trim($email)<>''){
			$sql_ins = "INSERT INTO read_email (
						tr_email_id, waktu_read_email, body_email, keterangan
					)VALUES(
						".$tr_email_id.", '".$tgl_kirim."', '".addslashes($slice)."'::varchar(600), 'img_email'
					)";
			$qry_ins = @pg_query($sql_ins);
			
			if($qry_ins){
				$sql = "UPDATE tr_email";
				$sql .= " SET tgl_read = '".$tgl_kirim."', read_method='img_email', body_email_read = '".addslashes($slice)."'::varchar(600)";
				$sql .= " WHERE tr_email_id = ".$tr_email_id;
				$sql .= " AND tgl_read is null";
					$query = @pg_query($sql);
				
				imap_delete($mbox, $i);
			}
		}
	}
	
	
	//Start 20130211
	
	function get_mime_type(&$structure) 
	{
	   $primary_mime_type = array("TEXT", "MULTIPART","MESSAGE", "APPLICATION", "AUDIO","IMAGE", "VIDEO", "OTHER");
   		if($structure->subtype) 
			{
				return $primary_mime_type[(int) $structure->type] . '/' .$structure->subtype;
			}
			return "TEXT/PLAIN";
   }
	
	
	function get_part($stream, $msg_number, $mime_type, $structure = false, $part_number = false) 
	{
   // die("stream".$stream);
		if (!$structure) 
		{
			$structure = imap_fetchstructure($stream, $msg_number);
		}

		if ($structure) 
		{
	
			if($mime_type == get_mime_type($structure)) 
			{
				if(!$part_number) 
						{
							$part_number = "1";
				   		}
				   			$text = imap_fetchbody($stream, $msg_number, $part_number);
				   if($structure->encoding == 3) 
				   		{
					   		return imap_base64($text);
				   		} 
				   else if($structure->encoding == 4) 
				   		{
					   		return imap_qprint($text);
				   		} 
				   			else 
						{
					   return $text;
				   }
			   }
	   
			if($structure->type == 1) /* multipart */ 
					{
				   while(list($index, $sub_structure) = each($structure->parts)) 
				   		{
					   		if($part_number) 
							{
						   		$prefix = $part_number . '.';
					   		}
					   	$data = get_part($stream, $msg_number, $mime_type, $sub_structure, $prefix . ($index + 1));
					   if($data) 
					   		{
						   		return $data;
					   		}
					} // end while
			   } // end multipart
		   } // end structure
		   return false;
	}
	//END 20130211
	
	
	pg_close($con);
?>