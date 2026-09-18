<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/message.php"); ?>
<? require_once("../include/function.php"); ?>
<? require_once("../include/PHPMailer/class.phpmailer.php"); ?>
<?php
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
										c.email_from, c.email_host,
										d.nama, d.alamat1, d.alamat2, d.alamat3, d.blth, d.flagtrans, d.nama_file
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
			echo $sql_sel_antrian.'::4\n';
			//die();
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
			//$pdf_name			= $row_sel_antrian['pdf_name'];				/*nama_pdf*/
			$blth				= $row_sel_antrian['blth'];					/*blth*/
				$periode = substr($blth,0,2).'-'.substr($blth,2,4);
				$periode = period($periode);
			$detail['periode']	= $periode;									/*ex: Oktober 2011*/
			$isi_email			= $row_sel_antrian['isi_email'];			/*isi email/content*/
			$subjek				= $row_sel_antrian['subject_email'];		/*subjek*/
			$nama_file			= $row_sel_antrian['nama_file'];			/*nama_file*/
			$nomor_rekening		= $row_sel_antrian['nomor_rekening'];		/*nomor_rekening*/
			$nomor_customer		= $row_sel_antrian['nomor_customer'];		/*nomor_customer*/
			$flagtrans			= $row_sel_antrian['flagtrans'];			/*flagtrans*/
			$loading_id			= $row_sel_antrian['loading_id'];			/*flagtrans*/
			$template_id		= $row_sel_antrian['template_id'];			/*flagtrans*/
			
				$subjek = replace_msg_email($flagtrans,$subjek,$detail);
			
			$mail = new PHPMailer();
			$mail->IsSMTP();
			$mail->From = $email_from;
			$mail->FromName = $from_name;
			$mail->Host = $email_host;
			$mail->Mailer   = "smtp";
			$mail->AddReplyTo($email_reply, $from_name);
			$mail->AddAddress($email, $detail['nama']);
			$mail->Subject = $subjek;
			//$mail->AddAttachment($pdf_location.$pdf_name,$pdf_name);
			
			$sql_sel_attach_ = "SELECT b.pdf_name,b.nomor_rekening,b.nama_file,b.blth,b.nama
								FROM antrian_email_attach_pdf a
									INNER JOIN detail b 
										ON a.detail_id=b.detail_id
								WHERE a.antrian_id = ".$arr_antrian_id[$id];
			$qry_sel_attach_ = pg_query($sql_sel_attach_) or die('ERROR select attach_file: '.$sql_sel_attach_);
			$listacc="";
			$listpdf="";
			$listnme="";
			while($row_sel_attach_=pg_fetch_array($qry_sel_attach_)){
				$listnme.=$row_sel_attach_['nama']."#";
				$listacc.=$row_sel_attach_['nomor_rekening']."#";
				$listpdf.=$row_sel_attach_['pdf_name'].".pdf#";
				$attach_name = $row_sel_attach_['pdf_name'].".pdf";
				$attach_location = "../pdf/".$row_sel_attach_['blth'].'/'.str_replace(substr($row_sel_attach_['nama_file'],-4),'',$row_sel_attach_['nama_file']).'/';
				$mail->AddAttachment($attach_location.$attach_name,$attach_name);
			}
			
			$sql_sel_attach = "SELECT name_file, location_file
								FROM antrian_email_attach_file a
									INNER JOIN m_attach_file b
										ON a.m_attach_file_id = b.m_attach_file_id
								WHERE a.antrian_id = ".$arr_antrian_id[$id];
			echo $sql_sel_attach."::3\n";
			$qry_sel_attach = pg_query($sql_sel_attach) or die('ERROR select attach_file: '.$sql_sel_attach);
			while($row_sel_attach=pg_fetch_array($qry_sel_attach)){
				$attach_name = $row_sel_attach['name_file'];
				$attach_location = $row_sel_attach['location_file'];
				$mail->AddAttachment($attach_location.$attach_name,$attach_name);
			}
			
			
			$sql_ins_tr_email = "INSERT INTO tr_email(
									nomor_customer, nomor_rekening, email,
									email_sukses, date_email_send, count_sent,
									loading_id, antrian_id, template_id, nama_customer
								)VALUES(
									'$nomor_customer', '".trim($listacc,'#')."', '$email',
									TRUE, now(), 1,
									$loading_id, ".$arr_antrian_id[$id].", $template_id, '".trim($listnme,'#')."'
								)";
			echo $sql_ins_tr_email."::2<br>";
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
													nomor_customer, '".trim($listacc,'#')."', nama,
													'".trim($listpdf,'#')."', '$attach_location', userid,
													tgl_antrian, template_email_id, '".$_SESSION['userid']."',
													now(), email, loading_id, antrian_id
												FROM antrian_email
												WHERE antrian_id = ".$arr_antrian_id[$id]."";
						echo $sql_ins_antrian_his."::1<br>";
						$qry_ins_antrian_his = pg_query($sql_ins_antrian_his) or die('ERROR insert antrian history: '.$sql_ins_antrian_his);
						
						$sql_del_antrian = "DELETE FROM antrian_email WHERE antrian_id = ".$arr_antrian_id[$id]."";
						$qry_del_antrian = pg_query($sql_del_antrian)or die("Error delete antrian: $sql");
						
						$sql_del_antrian = "DELETE FROM antrian_email_attach_file WHERE antrian_id = ".$arr_antrian_id[$id]."";
						$qry_del_antrian = pg_query($sql_del_antrian)or die("Error delete antrian: $sql");
						
						$sql_del_antrian = "DELETE FROM antrian_email_attach_pdf WHERE antrian_id = ".$arr_antrian_id[$id]."";
						$qry_del_antrian = pg_query($sql_del_antrian)or die("Error delete antrian: $sql");
					}else{
						$sql_del_tr_email = "DELETE FROM tr_email WHERE tr_email_id = $tr_email_id";
						$qry_del_tr_email = pg_query($sql_del_tr_email) or die('ERROR delete tr_email: '.$sql_del_tr_email);
						?>
                        <script>
							alert('<?=$nomor_rekening?> Gagal, <?=$mail->ErrorInfo.'xx'.$tr_email_id.' '.$hid_val.replace_msg_email($flagtrans,$isi_email,$detail)?>');
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