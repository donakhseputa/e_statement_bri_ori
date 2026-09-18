<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/message.php"); ?>
<? require_once("../include/function.php"); ?>
<? require_once("../include/PHPMailer/class.phpmailer.php"); ?>
<?php
	error_reporting(E_STRICT);
	
	$act		= $_REQUEST['act'];
	$con		= pg_connect($connection) or die("Could not connect to database!");
	
	switch($act){
		case 'kirim_email': kirim_email($client); break;
	}
	
	function kirim_email($client){
		$list_antrian = $_REQUEST['list'];
		$list_antrian = rtrim($list_antrian,',');
		$arr_antrian_id = explode(',',$list_antrian);
		
		for($id=0;$id<count($arr_antrian_id);$id++){
			$tr_email_id = '';
			$sql_sel_antrian = "SELECT a.*,
										b.subject_email, b.isi_email, b.from_name, b.reply_to_email, b.template_email_id as template_id,
										c.email_from, c.email_host, c.email_bounce_back,
										d.nama, d.alamat1, d.alamat2, d.alamat3, d.blth, d.flagtrans, d.nama_file, d.password_pdf
								FROM antrian_email a
									LEFT JOIN template_email b
										ON a.template_email_id = b.template_email_id
									LEFT JOIN mail_server c
										ON b.mail_server_id = c.mail_server_id
									LEFT JOIN detail d
										ON a.loading_id = d.m_loading_id
											and a.nomor_rekening = d.nomor_rekening
								WHERE a.antrian_id = ".$arr_antrian_id[$id]."";
			$qry_sel_antrian	= pg_query($sql_sel_antrian) or die('ERROR select antrian: '.$sql_sel_antrian);
			$row_sel_antrian	= pg_fetch_assoc($qry_sel_antrian);
			$email_from			= $row_sel_antrian['email_from'];			/*ex: noreply@indointernal.com*/
			$from_name			= $row_sel_antrian['from_name'];			/*ex: appdev email*/
			$email_host			= $row_sel_antrian['email_host'];			/*ex: 192.165.1.1*/
			$email_reply		= $row_sel_antrian['reply_to_email'];		/*ex: app_dev@indointernal.com*/
			$email_bounce_back	= $row_sel_antrian['email_bounce_back'];	/*ex: app_dev@indocorp.com*/
			$email				= $row_sel_antrian['email'];				/*array explode email*/
			$detail['nama']		= $row_sel_antrian['nama'];					/*nama customer*/
			$detail['alamat1']	= $row_sel_antrian['alamat1'];				/*alamat1*/
			$detail['alamat2']	= $row_sel_antrian['alamat2'];				/*alamat2*/
			$detail['alamat3']	= $row_sel_antrian['alamat3'];				/*alamat3*/
			$pdf_location		= $row_sel_antrian['pdf_location'];			/*lokasi_pdf*/
			$pdf_name			= $row_sel_antrian['pdf_name'];				/*nama_pdf*/
			$blth				= $row_sel_antrian['blth'];					/*blth*/
				$periode = substr($blth,0,2).'-'.substr($blth,2,4);
				$periode = period($periode);
			$detail['periode']	= $periode;									/*ex: Oktober 2011*/
			$status_sample		= $row_sel_antrian['status_sample'];		/*status apakah email tersebut sample atau bukan. true untuk sample. false untuk bukan sample*/
			$password_pdf		= $row_sel_antrian['password_pdf'];			/*password dari file pdf*/
			$isi_email			= $row_sel_antrian['isi_email'];			/*isi email/content*/
				if($status_sample=='t' || $status_sample=='TRUE'){
					$ganti = "<hr>kalimat ini hanya muncul untuk email sample saja.<p>password untuk dokumen ini adalah ".$password_pdf;
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
			$status_sample		= $row_sel_antrian['status_sample'];		/*status_sample: true or false*/
			
				$subjek = replace_msg_email($flagtrans,$subjek,$detail);
			
			$mail = new PHPMailer();
			$mail->IsSMTP();
			$mail->From = $email_from;
			$mail->FromName = $from_name;
			$mail->Host = $email_host;
			$mail->Mailer   = "smtp";
			$mail->AddReplyTo($email_reply, 'No Reply');
			$mail->ConfirmReadingTo = $email_bounce_back;
			$mail->AddAddress($email, $detail['nama']);
			$mail->Subject = $subjek;
			$mail->AddAttachment($pdf_location.$pdf_name,$pdf_name);
			
			$sql_sel_attach = "SELECT name_file, location_file
								FROM antrian_email_attach_file a
									INNER JOIN m_attach_file b
										ON a.m_attach_file_id = b.m_attach_file_id
								WHERE a.antrian_id = ".$arr_antrian_id[$id];
			$qry_sel_attach = pg_query($sql_sel_attach) or die('ERROR select attach_file: '.$sql_sel_attach);
			while($row_sel_attach=pg_fetch_array($qry_sel_attach)){
				$attach_name = $row_sel_attach['name_file'];
				$attach_location = $row_sel_attach['location_file'];
				$mail->AddAttachment($attach_location.$attach_name,$attach_name);
			}
			
			
			$sql_ins_tr_email = "INSERT INTO tr_email(
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
			$qry_ins_tr_email = @pg_query($sql_ins_tr_email); //or die('ERROR insert tr_email: '.$sql_ins_tr_email);
			
			if(pg_affected_rows($qry_ins_tr_email)>0){
				$sql_sel_tr_email_id = "SELECT last_value as tr_email_id FROM tr_email_tr_email_id_seq";
				
				$qry_sel_tr_email_id = pg_query($sql_sel_tr_email_id) or die('ERROR select tr_email_id: '.$sql_sel_tr_email_id);
				$row_id = pg_fetch_assoc($qry_sel_tr_email_id);
				$tr_email_id = $row_id['tr_email_id'];
				
				if(isset($tr_email_id)){
					$hid_val = "<input type='hidden' name='tr_email_id' id='tr_email_id' value='|" . $client . "|" . $flagtrans . "|" . $tr_email_id . "|" . $email . "|'>";
					//$mail->MessageID = "|" . $client . "|" . $flagtrans . "|" . $tr_email_id . "|" . $email . "|";
					$mail->MessageID = $tr_email_id;
					$isi_email = replace_msg_email($flagtrans,$isi_email,$detail);
					//$img_mail = '<img src="http://apps.adflazz.com/images/e_statement/mandiri/emailConfirm.php?client=mandiri&flagtrans='.$flagtrans.'&tr_email_id='.$tr_email_id.'&email='.$email.'" border="0" height="1" width="1">';
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
													tgl_antrian, template_email_id, '".$_SESSION['userid']."',
													now(), email, loading_id,
													antrian_id, jadwal_id, status_sample
												FROM antrian_email
												WHERE antrian_id = ".$arr_antrian_id[$id]."";
						$qry_ins_antrian_his = pg_query($sql_ins_antrian_his) or die('ERROR insert antrian history: '.$sql_ins_antrian_his);
						
						$sql_del_antrian = "DELETE FROM antrian_email WHERE antrian_id = ".$arr_antrian_id[$id]."";
						$qry_del_antrian = pg_query($sql_del_antrian)or die("Error delete antrian: $sql");
					}else{
						$sql_ins_log_error = "
							INSERT INTO log_error_kirim(
								antrian_id, nomor_customer, nomor_rekening,
								email, loading_id,
								status_sample, error_info, tgl_kirim,
								email_host, email_account
							)VALUES(
								".$arr_antrian_id[$id].", '".$nomor_customer."', '".$nomor_rekening."',
								'".$email."', ".$loading_id.",
								'".$status_sample."', '".$mail->ErrorInfo."', now(),
								'".$email_host."', '".$email_from."'
							);
						";
						$qry_ins_log_error = @pg_query($sql_ins_log_error);
						
						$sql_del_tr_email = "DELETE FROM tr_email WHERE tr_email_id = $tr_email_id";
						$qry_del_tr_email = pg_query($sql_del_tr_email) or die('ERROR delete tr_email: '.$sql_del_tr_email);
						?>
                        <script>
							alert('<?=$nomor_rekening?> Gagal, <?=$tr_email_id.' '.$hid_val.replace_msg_email($flagtrans,$isi_email,$detail)?>');
                        </script>
                        <?php
					}
				}
			}
			
			$mail->ClearAddresses();
			$mail->ClearAttachments();
		}
	}
?>