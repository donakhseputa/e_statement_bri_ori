<? //$www_location = "C:/App/htdocs/e_statement_nisp/";			/*untuk live*/ ?>
<? require_once("include_url.php"); ?>
<? require_once($www_location."include/config.php"); ?>
<? require_once($www_location."include/message.php"); ?>
<? require_once($www_location."include/function.php"); ?>
<? require_once($www_location."include/PHPMailer/class.phpmailer.php"); ?>
<? require_once($www_location."include/PHPMailer/class.pop3.php"); /**DITAMBAHKAN UNTUK MENCEGAH MAIL SERVER DIBLOK SEBAGAI SPAM**/?> 
<?php
	
	
	
	$status_aws = cek_status_aws();
	if ( $status_aws == 'offline' ) die("AWS WEB DOWN!!");
	
	error_reporting(E_STRICT);
	ini_set('memory_limit', '-1');
	
	//die("maintenance");
	
	$con		= pg_connect($connection) or die("Could not connect to database!");
	date_default_timezone_set('Asia/Jakarta');
	$BLTH_BARUS = BLTH_BARU;
	
	
	
	$hour = date('H');
	$hour = (int)$hour;

	#echo " Jam saat ini : $hour";
/*
	if ( $hour >= 0 && $hour <=9)
	{
		#echo "jam cek tidak valid";
		//yahoo
		$t_sql = '';
		$t_sql2 = "AND ( lower(a.email) like lower('%@yahoo%') OR  lower(a.email) like  lower('%@ymail%') OR  lower(a.email)  like lower('%@ROCKETMAIL%') )";
		$t_limit = '5';
		//$t_sql2 = "AND ( lower(a.email) not like lower('%@yahoo%')  AND  lower(a.email) not like lower('%@ymail%') AND  lower(a.email) not like lower('%@ROCKETMAIL%') )";
	}else{
		#echo "jam cek valid";
		$t_sql = "and a.email ~* '^[A-Za-z0-9._%\-+!#$&/=?^|~]+@[A-Za-z0-9.-]+[.][A-Za-z]+$' = 't'";
		$t_sql2 = "AND ( lower(a.email) not like lower('%@yahoo%')  AND  lower(a.email) not like lower('%@ymail%') AND  lower(a.email) not like lower('%@ROCKETMAIL%') )";
		$t_limit = '500';
	}
	
	
	//--ORDER BY a.antrian_id ASC 
	//substring(email from '@(.*)$') as domain
	
	$sql_sel_jadwal_antrian = "SELECT a.antrian_id, a.jadwal_id, a.flagtrans, a.blth
								FROM antrian_email a
									INNER JOIN m_jadwal b
										ON a.jadwal_id = b.jadwal_id
									LEFT JOIN log_approval e
										ON a.log_approval_id = e.log_approval_id
								WHERE b.tgl_jadwal 
								between ( (now() - interval '7 days')::date || ' 00:00:00')::timestamp without time zone  
								AND now()
									and b.status = TRUE and e.is_approval = 't' 
									$t_sql 
									$t_sql2
									ORDER BY a.kode_kirim ASC,  a.antrian_id ASC
									limit $t_limit";
	*/
	/*
	$sql_sel_1 ="select * from(
SELECT a.antrian_id, a.jadwal_id, a.flagtrans, a.blth, a.kode_kirim, a.email 
								FROM antrian_email a
									INNER JOIN m_jadwal b
										ON a.jadwal_id = b.jadwal_id
									LEFT JOIN log_approval e
										ON a.log_approval_id = e.log_approval_id
								WHERE b.tgl_jadwal 
								between ( (now() - interval '7 days')::date || ' 00:00:00')::timestamp without time zone  
								AND now()
									and b.status = TRUE and e.is_approval = 't' 
									and a.email ~* '^[A-Za-z0-9._%\-+!#$&/=?^|~]+@[A-Za-z0-9.-]+[.][A-Za-z]+$' = 't'
									and a.kode_kirim = 1
									ORDER BY a.kode_kirim ASC,  a.antrian_id ASC
									limit 10) xx";
	$sql_sel_2 = "select * from(
									SELECT a.antrian_id, a.jadwal_id, a.flagtrans, a.blth, a.kode_kirim, a.email 
								FROM antrian_email a
									INNER JOIN m_jadwal b
										ON a.jadwal_id = b.jadwal_id
									LEFT JOIN log_approval e
										ON a.log_approval_id = e.log_approval_id
								WHERE b.tgl_jadwal 
								between ( (now() - interval '7 days')::date || ' 00:00:00')::timestamp without time zone  
								AND now()
									and b.status = TRUE and e.is_approval = 't' 
									and a.email ~* '^[A-Za-z0-9._%\-+!#$&/=?^|~]+@[A-Za-z0-9.-]+[.][A-Za-z]+$' = 't'
									and a.kode_kirim = 2
									ORDER BY a.kode_kirim ASC,  a.antrian_id ASC
									limit 30) yy";
	$sql_sel_3 = "UNION select * from(
									SELECT a.antrian_id, a.jadwal_id, a.flagtrans, a.blth, a.kode_kirim, a.email 
								FROM antrian_email a
									INNER JOIN m_jadwal b
										ON a.jadwal_id = b.jadwal_id
									LEFT JOIN log_approval e
										ON a.log_approval_id = e.log_approval_id
								WHERE b.tgl_jadwal 
								between ( (now() - interval '7 days')::date || ' 00:00:00')::timestamp without time zone  
								AND now()
									and b.status = TRUE and e.is_approval = 't' 
									and a.email ~* '^[A-Za-z0-9._%\-+!#$&/=?^|~]+@[A-Za-z0-9.-]+[.][A-Za-z]+$' = 't'
									and a.kode_kirim = 3
									ORDER BY a.kode_kirim ASC,  a.antrian_id ASC
									limit 5) zz";
	//$sql_sel_3='';
	$sql_sel_jadwal_antrian = "$sql_sel_1
								UNION
								$sql_sel_2
								
								$sql_sel_3
								";
	*/
	
	$arr_kode_kirim = array();
	$arr_kode_kirim[] = 4;
	
	$arr_kode_kirim_limit = array();
	$arr_kode_kirim_limit[] = 100;
	
	$sql_sel_1 = '';
	for($nnnn=0;$nnnn<count($arr_kode_kirim);$nnnn++)
	{
		$flag_OO = '';
		//if ($nnnn==2) $flag_OO .= " and a.flagtrans = 'BC' ";
		if ($nnnn==2) $flag_OO .= " and (a.flagtrans = 'BC' OR a.flagtrans = 'BE')";
		if ($nnnn>0) $sql_sel_1 .= ' UNION ';
		//$flag_OO = " and a.flagtrans = 'BC' ";
		$flag_OO = " and (a.flagtrans = 'BC' OR a.flagtrans = 'BE') ";
		
		$sql_sel_1 .="select * from(
								SELECT a.antrian_id, a.jadwal_id, a.flagtrans, a.blth, a.kode_kirim, a.email 
								FROM antrian_email a
									INNER JOIN m_jadwal b
										ON a.jadwal_id = b.jadwal_id
									LEFT JOIN log_approval e
										ON a.log_approval_id = e.log_approval_id
								WHERE b.tgl_jadwal 
								between ( (now() - interval '7 days')::date || ' 00:00:00')::timestamp without time zone  
								AND now()
									and b.status = TRUE and e.is_approval = 't' 
									and a.email ~* '^[A-Za-z0-9._%\-+!#$&/=?^|~]+@[A-Za-z0-9.-]+[.][A-Za-z]+$' = 't'
									and a.kode_kirim = ".$arr_kode_kirim[$nnnn]." 
									$flag_OO 
									and (a.antrian_id%2 != 0)
									and (a.jadwal_id%2 != 0)
									AND a.error_info IS NULL
									ORDER BY a.kode_kirim ASC,  a.antrian_id ASC
									limit ".$arr_kode_kirim_limit[$nnnn].") x".$arr_kode_kirim[$nnnn].' ';
	}
	
	$sql_sel_jadwal_antrian = $sql_sel_1;
	
			#echo $sql_sel_jadwal_antrian;die();
									
	$qry_sel_jadwal_antrian = pg_query($sql_sel_jadwal_antrian) or die('ERROR select antrian jadwal: '.$sql_sel_jadwal_antrian);
	while($row_sel_jadwal_antrian = pg_fetch_array($qry_sel_jadwal_antrian)){
		$arr_antrian_id[] = $row_sel_jadwal_antrian['antrian_id'];
		$arr_jadwal_id[] = $row_sel_jadwal_antrian['jadwal_id'];
		$arr_blth[] = $row_sel_jadwal_antrian['blth'];
		$arr_flagtrans[] = $row_sel_jadwal_antrian['flagtrans'];
	}
	
	for($id=0;$id<count($arr_antrian_id);$id++){
	
	// if((strlen($arr_blth[$id])>0)&&(strlen($arr_flagtrans[$id])>0)){
		// $tabeldetail = "detail_" . $arr_blth[$id] . "_" . $arr_flagtrans[$id];
	// }else{
		// $tabeldetail = "detail";
	// }
	
	$blth_balik = substr($arr_blth[$id],-4).substr($arr_blth[$id],0,2);
		
		if($blth_balik>$BLTH_BARUS){
			$tabeldetail = "detail_" . $arr_blth[$id]."_".$arr_flagtrans[$id];
			$sql_cek = "SELECT a.m_loading_id FROM m_loading a LEFT JOIN $tabeldetail b ON a.m_loading_id=b.m_loading_id 
			WHERE a.blth='$arr_blth[$id]' AND a.flagtrans='$arr_flagtrans[$id]'";
			$exe_cek = @pg_query($sql_cek);
			if($exe_cek){
				$tabeldetail = "detail_" . $arr_blth[$id]."_".$arr_flagtrans[$id];
			}else{
				$tabeldetail = "detail";
			}
		}else{
			$tabeldetail = "detail";
		}
		
		
		
		if ( $arr_flagtrans[$id] == 'PK')
		{
			$add_detail = ', d.tanggal ';
		}
		else
		{
			$add_detail = '';
		}
	
		$tr_email_id = '';
		$sql_sel_antrian = "SELECT a.*,
									b.subject_email, b.isi_email, b.from_name, b.reply_to_email, b.template_email_id as template_id,
									c.email_from, c.email_host, c.email_bounce_back,c.email_pass,
									d.nama, d.alamat1, d.alamat2, d.alamat3, d.blth, d.flagtrans, d.nama_file, d.password_pdf, d.tipe_kartu, d.ket_produk, d.email AS emailsebenarnya, d.nomor_rekening $add_detail
							FROM antrian_email a
								LEFT JOIN template_email b
									ON a.template_email_id = b.template_email_id
								LEFT JOIN mail_server c
									ON b.mail_server_id = c.mail_server_id
								LEFT JOIN $tabeldetail d
									ON a.loading_id = d.m_loading_id
										and a.nomor_rekening = d.nomor_rekening 
								LEFT JOIN log_approval e
									ON a.log_approval_id = e.log_approval_id
							WHERE a.antrian_id = '".$arr_antrian_id[$id]."' and e.is_approval = 't'";
							
		$qry_sel_antrian	= pg_query($sql_sel_antrian) or die('ERROR select antrian: '.$sql_sel_antrian);
		$row_sel_antrian	= pg_fetch_assoc($qry_sel_antrian);
		$email_from			= $row_sel_antrian['email_from'];			/*ex: noreply@indointernal.com*/
		$email_pass			= $row_sel_antrian['email_pass'];	/*password*/
		$from_name			= $row_sel_antrian['from_name'];			/*ex: appdev email*/
		$email_host			= $row_sel_antrian['email_host'];			/*ex: 192.165.1.1*/
		$email_reply		= $row_sel_antrian['reply_to_email'];		/*ex: app_dev@indointernal.com*/
		$email_bounce_back	= $row_sel_antrian['email_bounce_back'];	/*ex: app_dev@indocorp.com*/
		$email				= $row_sel_antrian['email'];				/*array explode email*/
		$detail['nama']		= $row_sel_antrian['nama'];					/*nama customer*/
		$detail['alamat1']	= $row_sel_antrian['alamat1'];				/*alamat1*/
		$detail['alamat2']	= $row_sel_antrian['alamat2'];				/*alamat2*/
		$detail['alamat3']	= $row_sel_antrian['alamat3'];				/*alamat3*/
		$detail['tipe_kartu']	= $row_sel_antrian['tipe_kartu'];				/*tipe_kartu*/
		$detail['ket_produk']	= $row_sel_antrian['ket_produk'];				/*ket_produk*/
		$pdf_location		= $row_sel_antrian['pdf_location'];			/*lokasi_pdf*/
		$pdf_name			= $row_sel_antrian['pdf_name'];				/*nama_pdf*/
		$blth				= $row_sel_antrian['blth'];					/*blth*/
		$nama_file			= $row_sel_antrian['nama_file'];			/*nama_file*/
		$emailsebenarnya	= $row_sel_antrian['emailsebenarnya'];			/*email_sebenarnya*/

			$periode = substr($blth,0,2).'-'.substr($blth,2,4);
			$periode = period($periode);
		$detail['periode']	= $periode;									/*ex: Oktober 2011*/
		$status_sample		= $row_sel_antrian['status_sample'];		/*status apakah email tersebut sample atau bukan. true untuk sample. false untuk bukan sample*/
		$password_pdf		= $row_sel_antrian['password_pdf'];			/*password dari file pdf*/
		$isi_email			= $row_sel_antrian['isi_email'];			/*isi email/content*/
			if($status_sample=='t' || $status_sample=='TRUE'){
				if ( empty($password_pdf) ) $password_pdf ='tidak ada';
				$ganti = "<hr>kalimat ini hanya muncul untuk email sample saja.<p>password untuk dokumen ini adalah  ".$password_pdf. "<br> Periode: " .$blth	. "<br> Cycle : " . $nama_file . "<br> Email Sebenarnya : <b>" . $emailsebenarnya . "</b>";
				}else{
				$ganti = "";
			}
			$isi_email = str_replace("@@#sample#@@",$ganti,$isi_email);	/*ganti kode sample*/
		$subjek				= $row_sel_antrian['subject_email'];		/*subjek*/
		$nama_file			= $row_sel_antrian['nama_file'];			/*nama_file*/
		$nomor_rekening		= $row_sel_antrian['nomor_rekening'];		/*nomor_rekening*/
		$nomor_customer		= $row_sel_antrian['nomor_customer'];		/*nomor_customer*/
		$flagtrans			= $row_sel_antrian['flagtrans'];			/*flagtrans*/
		$loading_id			= $row_sel_antrian['loading_id'];			/*loading_id*/
		$template_id		= $row_sel_antrian['template_id'];			/*template_id*/
		$jadwal_id			= $row_sel_antrian['jadwal_id'];			/*jadwal_id*/
		$status_sample		= $row_sel_antrian['status_sample'];		/*status_sample: true or false*/
		
		
		if ( $flagtrans == "PK")
		{
			$detail['tipe_kartu_adhoc']			= $row_sel_antrian['tanggal'];
		}
		
		//tambahan 20151119 cek email valid atau tidak, jika valid kirim , jika tidak gagal
		$cek_valid_email=check_email_address($email);
		//if ( !$cek_valid_email ) continue;
			if ( !$cek_valid_email ) 
			{
				$sql_error_info = "UPDATE antrian_email 
							SET tgl_error_info=now(),
							error_info='Invalid Email Address format ($email)".' / '."'  
							WHERE antrian_id =".$arr_antrian_id[$id]."
							AND nomor_rekening='".$nomor_rekening."'";
							#die($sql_error_info);
							@pg_query($sql_error_info);
				continue;
			}
		
		
		/**
		Ditambahkan untuk mencegah agar tidak dianggap sebagai spam
		**/
		$pop = new POP3();
		$pop->Authorise($email_host, 110, 60, $email_from, $email_pass, 1);
		
		
		$subjek = replace_msg_email($flagtrans,$subjek,$detail);
		$mail = new PHPMailer();
		$mail->IsSMTP();
		
		//tambahan autentifikasi 20150220
		$mail->SMTPDebug = 2;  // untuk memunculkan pesan error /debug di layar
		$mail->SMTPAuth = true;  // authentifikasi smtp enable atau disable
		//$mail->Username = $email_from; //username email
		//$mail->Password =  $email_pass; //password email
		//tambahan autentifikasi 20150220
		
		
		
		
		////////////////////////////////////////////// TAMBAHAN UNTUK AWS ///////////////////////////
			//$mail->Username = 'AKIAZ3UZB3BUCKYJPTNH'; //username email
			//$mail->Password =  'BOxeMuMuk1/D9aahsd5QpNflbTQW3OJFw89mM3t3ZXjD'; //password email
			//$email_host	= 'email-smtp.ap-south-1.amazonaws.com';
			
			
			$mail->Username = 'AKIA54LF2PUGPZAKDSNA'; //username email
			$mail->Password =  'BAb/R7IhocFuwv74iVHkLUYUKX1eEs852NPudN7mJ+3h'; //password email
			$email_host	= 'email-smtp.ap-southeast-1.amazonaws.com';
			$mail->SMTPSecure = 'tls';
			$mail->Port       = 587;
			////////////////////////////////////////////// TAMBAHAN UNTUK AWS ///////////////////////////
		
		$mail->From = $email_from;
		$mail->FromName = $from_name;
		$mail->Host = $email_host;
		$mail->Mailer   = "smtp";
		//$mail->AddReplyTo($email_reply, $from_name);
		//$mail->ConfirmReadingTo = $email_bounce_back;
		$mail->AddAddress($email, $detail['nama']);
		$subjek = replace_msg_email($flagtrans,$subjek,$detail);
		$mail->Subject = $subjek;
		$pdf_location = $www_location.substr($pdf_location,3);
		
		if(file_exists($pdf_location.$pdf_name)){
			$status_pdf = "Y";
		}else{
			$status_pdf = "N";
		}
		
		if($mail->AddAttachment($pdf_location.$pdf_name,$pdf_name)){
			$status_pdf = "Y";
		}else{
			$status_pdf = "N";
		}
		
		$sql_sel_attach = "SELECT name_file, location_file, b.cid 
								FROM antrian_email_attach_file a
									INNER JOIN m_attach_file b
										ON a.m_attach_file_id::integer = b.m_attach_file_id::integer
								WHERE a.antrian_id = '".$arr_antrian_id[$id]."'";
			$qry_sel_attach = pg_query($sql_sel_attach) or die('ERROR select attach_file: '.$sql_sel_attach);
			while($row_sel_attach=pg_fetch_array($qry_sel_attach)){
				$attach_name = $row_sel_attach['name_file'];
				$attach_location = $row_sel_attach['location_file'];
				$attach_location = $www_location.substr($attach_location,3);
				if($row_sel_attach['cid']<>''){
					if($mail->AddEmbeddedImage($attach_location.$attach_name, $row_sel_attach['cid'])){
					}else{
						$sql_ins_log_error = "
							INSERT INTO log_error_kirim(
								antrian_id, nomor_customer, nomor_rekening,
								email, loading_id, jadwal_id,
								status_sample, error_info, tgl_kirim,
								email_host, email_account
							)VALUES(
								".$arr_antrian_id[$id].", '".$nomor_customer."', '".$nomor_rekening."',
								'".$email."', ".$loading_id.", ".$jadwal_id.",
								'".$status_sample."', '".$mail->ErrorInfo."', now(),
								'".$email_host."', '".$email_from."'
							);
						";
						$qry_ins_log_error = @pg_query($sql_ins_log_error);
					}
				}else{
					if($mail->AddAttachment($attach_location.$attach_name,$attach_name)){
					}else{
						$sql_ins_log_error = "
							INSERT INTO log_error_kirim(
								antrian_id, nomor_customer, nomor_rekening,
								email, loading_id, jadwal_id,
								status_sample, error_info, tgl_kirim,
								email_host, email_account
							)VALUES(
								".$arr_antrian_id[$id].", '".$nomor_customer."', '".$nomor_rekening."',
								'".$email."', ".$loading_id.", ".$jadwal_id.",
								'".$status_sample."', '".$mail->ErrorInfo."', now(),
								'".$email_host."', '".$email_from."'
							);
						";
						$qry_ins_log_error = @pg_query($sql_ins_log_error);
					}
				}
			}
		
		
		if($flagtrans == "BE") $status_pdf = "Y";
		###if($flagtrans == "PK") $status_pdf = "Y";###
		
		
		/* TAMBAHAN 20170722 Mengecek jika di antrian masih ada, (mengecek tidak ada double kirim) kz */
		$sql_cek_jx = "select * FROM antrian_email WHERE antrian_id = ".$arr_antrian_id[$id]."";
		$qry_cek_jx = pg_query($sql_cek_jx) or die('ERROR cek antrian masih ada atau tidak: ' . $sql_cek_jx);
		$jml_cek_jx = pg_num_rows($qry_cek_jx);
		if ($jml_cek_jx == 0) continue;
		/* TAMBAHAN 20170722 Mengecek jika di antrian masih ada, (mengecek tidak ada double kirim) kz */
		
		$rev_tr_email = cek_sk_tr( $arr_blth[$id] ,  $arr_flagtrans[$id] );///1
		
		if($status_pdf == "Y"){
			$sql_ins_tr_email = "INSERT INTO $rev_tr_email(
									nomor_customer, nomor_rekening, email,
									email_sukses, date_email_send, count_sent,
									loading_id, antrian_id, template_id,
									status_sample
								)VALUES(
									'$nomor_customer', '$nomor_rekening', '$email',
									TRUE, now(), 1,
									$loading_id, ".$arr_antrian_id[$id].", $template_id,
									'".$status_sample."'
								)";
			$qry_ins_tr_email = pg_query($sql_ins_tr_email) or die('ERROR insert tr_email: '.$sql_ins_tr_email);
			
			if(pg_affected_rows($qry_ins_tr_email)>0){
				if ($rev_tr_email == 'tr_email')///2
				{
					$sql_sel_tr_email_id = "SELECT last_value as tr_email_id FROM tr_email_tr_email_id_seq1";
				}else{
					$sql_sel_tr_email_id = "SELECT last_value as tr_email_id FROM ".$rev_tr_email."_tr_email_id_seq";
				}
				
				
				$qry_sel_tr_email_id = pg_query($sql_sel_tr_email_id) or die('ERROR select tr_email_id: '.$sql_sel_tr_email_id);
				$row_id = pg_fetch_assoc($qry_sel_tr_email_id);
				$tr_email_id = $row_id['tr_email_id'];
				$tr_email_id_del = $row_id['tr_email_id'];
				
				if ($rev_tr_email != 'tr_email')///3
				{
					$tr_email_id = '1'. str_pad($tr_email_id, 7, 0, STR_PAD_LEFT). str_pad($loading_id, 5, 0, STR_PAD_LEFT);
				}
				if(isset($tr_email_id)){
					//$hid_val = "<input type='hidden' name='tr_email_id' id='tr_email_id' value='|" . $client ."|" . $flagtrans . "|" . $tr_email_id . "|" . $email . "|'>";
					
					$hid_val = '';
					
					$mail->MessageID = $tr_email_id;
					$isi_email = replace_msg_email($flagtrans,$isi_email,$detail);
					$img_mail = '<img src="http://www.e-statement.net/e_statement/bri/emailConfirm.php?client=BRI&flagtrans='.$flagtrans.'&tr_email_id='.$tr_email_id.'&email='.$email.'" border="0" height="1" width="1" alt="">';
					$mail->MsgHTML("<html><body>".$hid_val.$isi_email.$img_mail."</body></html>");
					$mail->IsHTML(true);
					
					if($mail->Send()){
						$sql_ins_antrian_his = "INSERT INTO antrian_email_history(
													nomor_customer, nomor_rekening, nama,
													pdf_name, pdf_location, userid,
													tgl_antrian, template_email_id, user_modify,
													tgl_modify, email, loading_id,
													antrian_id, jadwal_id, status_sample
												)SELECT
													nomor_customer, nomor_rekening, nama,
													pdf_name, pdf_location, userid,
													tgl_antrian, template_email_id, 'by_schedule',
													now(), email, loading_id,
													antrian_id, jadwal_id, status_sample
												FROM antrian_email
												WHERE antrian_id = ".$arr_antrian_id[$id]."";
						$qry_ins_antrian_his = pg_query($sql_ins_antrian_his) or die('ERROR insert antrian history: '.$sql_ins_antrian_his);
						
						$sql_del_antrian = "DELETE FROM antrian_email WHERE antrian_id = ".$arr_antrian_id[$id]."";
						$qry_del_antrian = pg_query($sql_del_antrian)or die("Error delete antrian: $sql");
						
						
						/* TAMBAHAN 20170722 Mengecek jika di jadwal_id masih ada diantrian tidak di false kan kz */
						$sql_cek_j = "select * from antrian_email where jadwal_id=$jadwal_id";
						$qry_cek_j = pg_query($sql_cek_j) or die('ERROR cek jadwal_id di antrian tersisa: ' . $sql_cek_j);
						$jml_cek_j = pg_num_rows($qry_cek_j);
						if ($jml_cek_j == 0) 
						{
							$sql_upd_jadwal = "UPDATE m_jadwal SET flag_jadwal = FALSE, status = FALSE, date_sent = now() WHERE jadwal_id = $jadwal_id";
							$qry_upd_jadwal = pg_query($sql_upd_jadwal) or die('ERROR update jadwal: '.$sql_upd_jadwal);
						}
						/* TAMBAHAN 20170722 Mengecek jika di jadwal_id masih ada diantrian tidak di false kan kz */
						
						
						
						///////////////////////////////////////////////////////////////// DOUBLE AMAZON TIDAK STABIL
						$sql_kz = "select nomor_rekening, count(*) as jumlah from $rev_tr_email where loading_id = $loading_id 
						AND nomor_rekening='$nomor_rekening' and status_sample ='f' 
						group by nomor_rekening having count(*) > 1";
						//die($sql_kz);
						$query_kz1 = pg_query($sql_kz) or die("Invalid query!" . $sql_kz) ;
						$rows_kz1 = pg_num_rows($query_kz1);
						if ($rows_kz1 > 0)
						{
							$sql2_kz = "select * from $rev_tr_email where loading_id = $loading_id  AND nomor_rekening='$nomor_rekening'
								and status_sample ='f' order by tr_email_id DESC LIMIT 1";
							//die($sql2);
							$query2_kz = pg_query($sql2_kz) or die("Invalid query!" . $sql2_kz) ;
							$rows2_kz = pg_num_rows($query2_kz);
							if ($rows2_kz > 0)
							{
								//die("tr_email_id : $tr_email_id, dengan norek $nomor_rekening ada di tr_email sebanyak $rows0 dan di antrian sebanyak $rows2");
								
								$sqlx_kz = "UPDATE $rev_tr_email
									   SET loading_id=9902
									 WHERE loading_id = $loading_id AND nomor_rekening = '$nomor_rekening' and status_sample ='f' 
									 AND tr_email_id not in ($tr_email_id_del);
									";
								//die($sqlx);
								pg_query($sqlx_kz);
							}
						}
						///////////////////////////////////////////////////////////////// DOUBLE AMAZON TIDAK STABIL
						
					}else{
						$sql_ins_log_error = "
							INSERT INTO log_error_kirim(
								antrian_id, nomor_customer, nomor_rekening,
								email, loading_id, jadwal_id,
								status_sample, error_info, tgl_kirim,
								email_host, email_account
							)VALUES(
								".$arr_antrian_id[$id].", '".$nomor_customer."', '".$nomor_rekening."',
								'".$email."', ".$loading_id.", ".$jadwal_id.",
								'".$status_sample."', '".$mail->ErrorInfo."', now(),
								'".$email_host."', '".$email_from."'
							);
						";
						$qry_ins_log_error = @pg_query($sql_ins_log_error);
						
						$sql_del_tr_email = "DELETE FROM $rev_tr_email WHERE tr_email_id = $tr_email_id_del";
						$qry_del_tr_email = pg_query($sql_del_tr_email) or die('ERROR delete tr_email: '.$sql_del_tr_email);
						?>
						<script>
							alert('<?=$nomor_rekening?> Gagal, <?=$tr_email_id.' '.$hid_val.replace_msg_email($flagtrans,$isi_email,$detail)?>');
						</script>
						<?php
					}
				}
			}
		}else{
			$sql_ins_log_error = "
				INSERT INTO log_error_kirim(
					antrian_id, nomor_customer, nomor_rekening,
					email, loading_id, jadwal_id,
					status_sample, error_info, tgl_kirim,
					email_host, email_account
				)VALUES(
					".$arr_antrian_id[$id].", '".$nomor_customer."', '".$nomor_rekening."',
					'".$email."', ".$loading_id.", ".$jadwal_id.",
					'".$status_sample."', '".$mail->ErrorInfo."', now(),
					'".$email_host."', '".$email_from."'
				);
			";
			$qry_ins_log_error = @pg_query($sql_ins_log_error);
		}
		
		$mail->ClearAddresses();
		$mail->ClearAttachments();
	}
?>