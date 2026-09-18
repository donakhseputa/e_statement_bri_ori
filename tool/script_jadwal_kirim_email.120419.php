<? require_once("F:/AppDev/Public/www/e_statement_bri/include/config.php"); ?>
<? require_once("F:/AppDev/Public/www/e_statement_bri/include/session.php"); ?>
<? require_once("F:/AppDev/Public/www/e_statement_bri/include/message.php"); ?>
<? require_once("F:/AppDev/Public/www/e_statement_bri/include/function.php"); ?>
<? require_once("F:/AppDev/Public/www/e_statement_bri/include/PHPMailer/class.phpmailer.php"); ?>
<?php
	$sql_sel_jadwal_antrian = "SELECT a.antrian_id, a.jadwal_id
								FROM antrian_email a
									INNER JOIN m_jadwal b
										ON a.jadwal_id = b.jadwal_id
								WHERE b.jadwal_id <= ".date('Y-m-d H:i:00')."
									and b.status = TRUE";
	$qry_sel_jadwal_antrian = pg_query($sql_sel_jadwal_antrian) or die('ERROR select antrian jadwal: '.$sql_sel_jadwal_antrian);
	while($row_sel_jadwal_antrian = pg_fetch_array($qry_sel_jadwal_antrian)){
		$arr_antrian_id[] = $row_sel_jadwal_antrian['antrian_id'];
		$arr_jadwal_id[] = $row_sel_jadwal_antrian['jadwal_id'];
	}
	
	for($id=0;$id<count($arr_antrian_id);$id++){
		$tr_email_id = '';
		$sql_sel_antrian = "SELECT a.*,
									b.subject_email, b.isi_email, b.from_name, b.reply_to_email, b.template_email_id as template_id,
									c.email_from, c.email_host,
									d.nama, d.alamat1, d.alamat2, d.alamat3, d.blth, d.flagtrans, d.nama_file, d.pdf_name AS pdf_name_, d.nomor_rekening AS nomor_rekening
							FROM antrian_email a
								LEFT JOIN template_email b
									ON a.template_email_id = b.template_email_id
								LEFT JOIN mail_server c
									ON b.mail_server_id = c.mail_server_id
								LEFT JOIN antrian_email_attach_pdf e
									ON e.antrian_id=a.antrian_id
								LEFT JOIN detail d
									ON d.detail_id=e.detail_id
							WHERE a.antrian_id = ".$arr_antrian_id[$id]."";
		$qry_sel_antrian	= pg_query($sql_sel_antrian) or die('ERROR select antrian: '.$sql_sel_antrian);
		$row_sel_antrian	= pg_fetch_assoc($qry_sel_antrian);
		
		$email_from			= $row_sel_antrian['email_from'];			/*ex: noreply@indointernal.com*/
		$from_name			= $row_sel_antrian['from_name'];			/*ex: appdev email*/
		$email_host			= $row_sel_antrian['email_host'];			/*ex: 192.165.1.1*/
		$email_reply		= $row_sel_antrian['reply_to_email'];		/*ex: app_dev@indointernal.com*/
		$email				= $row_sel_antrian['email'];				/*array explode email*/
		$detail['nama']		= $row_sel_antrian['nama'];					/*nama customer*/
		$detail['alamat1']	= $row_sel_antrian['alamat1'];				/*alamat1*/
		$detail['alamat2']	= $row_sel_antrian['alamat2'];				/*alamat2*/
		$detail['alamat3']	= $row_sel_antrian['alamat3'];				/*alamat3*/
		//$pdf_location		= $row_sel_antrian['pdf_location'];			/*lokasi_pdf*/
		$pdf_location		= "../pdf/".$row_sel_antrian['blth'].'/'.str_replace(substr($row_sel_antrian['nama_file'],-4),'',$row_sel_antrian['nama_file']).'/';
		$blth				= $row_sel_antrian['blth'];					/*blth*/
			$periode = substr($blth,0,2).'-'.substr($blth,2,4);
			$periode = period($periode);
		$detail['periode']	= $periode;									/*ex: Oktober 2011*/
		$isi_email			= $row_sel_antrian['isi_email'];			/*isi email/content*/
		$subjek				= $row_sel_antrian['subject_email'];		/*subjek*/
		$nama_file			= $row_sel_antrian['nama_file'];			/*nama_file*/
		$nomor_rekening		= $row_sel_antrian['nomor_rekening_'];		/*nomor_rekening*/
		$nomor_customer		= $row_sel_antrian['nomor_customer'];		/*nomor_customer*/
		$flagtrans			= $row_sel_antrian['flagtrans'];			/*flagtrans*/
		$loading_id			= $row_sel_antrian['loading_id'];			/*flagtrans*/
		$template_id		= $row_sel_antrian['template_id'];			/*flagtrans*/
		$jadwal_id			= $row_sel_antrian['jadwal_id'];			/*jadwal_id*/
		$pdf_name[]			= $row_sel_antrian['pdf_name_'];			/*nama_pdf*/
		$list_pdf			= $row_sel_antrian['pdf_name_'].'.pdf';
		
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->From = $email_from;
		$mail->FromName = $from_name;
		$mail->Host = $email_host;
		$mail->Mailer   = "smtp";
		$mail->AddReplyTo($email_reply, $from_name);
		$mail->AddAddress($email, $detail['nama']);
			$subjek = replace_msg_email($flagtrans,$subjek,$detail);
		$mail->Subject = $subjek;
		//$mail->AddAttachment($pdf_location.$pdf_name,$pdf_name);
		
		while($row_sel_antrian	= pg_fetch_assoc($qry_sel_antrian)){
			$pdf_name[]			= $row_sel_antrian['pdf_name_'];
			$nomor_rekening		.= '#'.$row_sel_antrian['nomor_rekening_'];
			$list_pdf			.= '#'.$row_sel_antrian['pdf_name_'].'.pdf';
		}
		
		for($i=0;$i<count($pdf_name);$i++){
			$mail->AddAttachment($pdf_location.$pdf_name[$i].'.pdf',$pdf_name[$i].'.pdf');
		}
		
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
								loading_id, antrian_id, template_id
							)VALUES(
								'$nomor_customer', '$nomor_rekening', '$email',
								TRUE, now(), 1,
								$loading_id, ".$arr_antrian_id[$id].", $template_id
							)";
		$qry_ins_tr_email = pg_query($sql_ins_tr_email) or die('ERROR insert tr_email: '.$sql_ins_tr_email);
		
		if(pg_affected_rows($qry_ins_tr_email)>0){
			$sql_sel_tr_email_id = "SELECT last_value as tr_email_id FROM tr_email_tr_email_id_seq";
			
			$qry_sel_tr_email_id = pg_query($sql_sel_tr_email_id) or die('ERROR select tr_email_id: '.$sql_sel_tr_email_id);
			$row_id = pg_fetch_assoc($qry_sel_tr_email_id);
			$tr_email_id = $row_id['tr_email_id'];
			
			if(isset($tr_email_id)){
				$hid_val = "<input type='hidden' name='tr_email_id' id='tr_email_id' value='|" . $client ."|" . $flagtrans . "|" . $tr_email_id . "|" . $email . "|'>";
				$isi_email = replace_msg_email($flagtrans,$isi_email,$detail);
				$mail->MsgHTML("<html><body>".$hid_val.$isi_email."</body></html>");
				$mail->IsHTML(true);
				
				if($mail->Send()){
					$sql_ins_antrian_his = "INSERT INTO antrian_email_history(
												nomor_customer, nomor_rekening, nama,
												pdf_name, pdf_location, userid,
												tgl_antrian, template_email_id, user_modify,
												tgl_modify, email, loading_id, antrian_id
											)SELECT
												nomor_customer, '$nomor_rekening', nama,
												'$list_pdf', '$pdf_location', userid,
												tgl_antrian, template_email_id, '".$_SESSION['userid']."',
												now(), email, loading_id, antrian_id
											FROM antrian_email
											WHERE antrian_id = ".$arr_antrian_id[$id]."";
					$qry_ins_antrian_his = pg_query($sql_ins_antrian_his) or die('ERROR insert antrian history: '.$sql_ins_antrian_his);
					
					$sql_del_antrian = "DELETE FROM antrian_email WHERE antrian_id = ".$arr_antrian_id[$id]."";
					$qry_del_antrian = pg_query($sql_del_antrian)or die("Error delete antrian: $sql");
					
					$sql_upd_jadwal = "UPDATE m_jadwal SET flag_jadwal = FALSE, status = FALSE, date_sent = now() WHERE jadwal_id = $jadwal_id";
					$qry_upd_jadwal = pg_query($sql_upd_jadwal) or die('ERROR update jadwal: '.$sql_upd_jadwal);
				}else{
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
?>