<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/function.php"); ?>
<?
	$pr = trim($_GET["pr"]);
	$arr_pr = explode("|", $pr);
	$flagtrans = trim($arr_pr[0]);
	$menu_id = (int)trim($arr_pr[1]);
	
	$act = $_REQUEST['act'];
	
	$con = pg_connect($connection) or die("Could not connect to database!");
	$akses = userAcces($connection,$menu_id);
	
	switch($act){
		case 'blth'			: blth(); break;
		case 'rec_blth'		: rec_blth(); break;
		case 'nama_file'	: nama_file(); break;
		case 'show_report'	: show_report(); break;
	}
	
	function blth(){
		$flagtrans = $_REQUEST['flagtrans'];
		?>
        <select id="blth" name="blth" class="combobox" onChange="fg_check_nama_file();button_export();">
        	<option value=""></option>
			<?php
            $sql_sel_blth = "SELECT b.blth
                                FROM tr_email a
									INNER JOIN m_loading b
										ON a.loading_id = b.m_loading_id
                                WHERE b.flagtrans = '$flagtrans'
								GROUP BY b.blth
                                ORDER BY substring(b.blth, 3, 4) desc, substring(b.blth, 1, 2) desc";
            $qry_sel_blth = pg_query($sql_sel_blth) or die('ERROR select blth: '.$sql_sel_blth);
			while($row_sel_blth=pg_fetch_array($qry_sel_blth)){
				?>
                <option value="<?=$row_sel_blth['blth']?>"><?=$row_sel_blth['blth']?></option>
                <?php
			}
		?>
        </select>
        <?php
	}
	
	function rec_blth(){
		$flagtrans = $_REQUEST['flagtrans'];
		$blth = $_REQUEST['blth'];
		$sql_rec_blth = "SELECT count(*) as jml_blth
                                FROM tr_email a
									INNER JOIN m_loading b
										ON a.loading_id = b.m_loading_id
                                WHERE b.blth = '$blth' and b.flagtrans = '$flagtrans'";
        $qry_rec_blth = pg_query($sql_rec_blth) or die('ERROR select blth: '.$sql_rec_blth);
		$row_rec_blth = pg_fetch_assoc($qry_rec_blth);
		$jml_rec_blth = $row_rec_blth['jml_blth'];
		?>
        <input type="text" class="textbox_1" disabled="disabled" value="<?=$jml_rec_blth?>" />
        <?php
	}
	
	function nama_file(){
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['flagtrans'];
		
		$sql_sel_nama_file = "SELECT loading_file FROM m_loading WHERE blth = '$blth' and flagtrans = '$flagtrans'";
		$qry_sel_nama_file = pg_query($sql_sel_nama_file) or die('ERROR select nama_file: '.$sql_sel_nama_file);
		?>
        <select id="nama_file" name="nama_file" class="combobox" onChange="button_export()">
        	<option value=""></option>
        <?php
			while($row_nama_file = pg_fetch_array($qry_sel_nama_file)){
				?>
                <option value="<?=$row_nama_file['loading_file']?>"><?=$row_nama_file['loading_file']?></option>
                <?php
			}
		?>
        </select>
        <?php
	}
	
	function get_from_mailserver($blth, $flagtrans, $nama_file){
		$sql_sel_mail_server = "SELECT c.mail_server_id, c.email_from, c.email_pass, c.email_inbox, c.email_bounce_back
								FROM tr_email a
									INNER JOIN template_email b
										ON a.template_id = b.template_email_id
									INNER JOIN mail_server c
										ON b.mail_server_id = c.mail_server_id
									INNER JOIN m_loading d
										ON a.loading_id = d.m_loading_id
								WHERE d.blth = '$blth' and d.flagtrans = '$flagtrans'";
		if($nama_file!='' && $nama_file!='null'){
			$sql_sel_mail_server .= " and d.loading_file = '$nama_file'";
		}
		$sql_sel_mail_server .= " GROUP BY c.mail_server_id, c.email_from, c.email_pass, c.email_inbox, c.email_bounce_back";
		$qry_sel_mail_server = pg_query($sql_sel_mail_server) or die('ERROR: select mail server: '.$sql_sel_mail_server);
		
		while($row_mail_server = pg_fetch_array($qry_sel_mail_server)){
			$email_inbox = $row_mail_server['email_inbox'];
			$email_from = $row_mail_server['email_bounce_back'];
			$email_pass = $row_mail_server['email_pass'];
			
			//$mbox = imap_open("{your.imap.host:143}", "username", "password");
			$mbox = imap_open($email_inbox, $email_from, $email_pass) or die("can't connect: " . imap_last_error());
			
			$email_bounce_message='';
			for($i = 1; $i <= imap_num_msg($mbox); $i++){
				$header_info = imap_headerinfo($mbox, $i);
				if (strtoupper(substr($header_info->senderaddress, 0, 13)) == "MAILER-DAEMON") {
					cekBounce($mbox, $i, $header_info->senderaddress);
					imap_delete($mbox, $i);
				}else if(strtoupper(substr($header_info->subject, 0, 5)) == "READ:"){
					cekRead($mbox, $i, $header_info->in_reply_to);
				}else if(strtoupper(substr($header_info->subject, 0, 9)) == "IMG EMAIL"){
					cekReadImg($mbox, $i, $header_info->senderaddress);
				}else{
					$header = pg_escape_string($header_info->senderaddress);
					$body = pg_escape_string(imap_body($mbox, $i));
					
					$sql = " insert into bounce_inbox(
									header_bounce, body_bounce, date_bounce
								)values(
									'$header'::varchar(100), '$body'::varchar(400), now()
								)";
					@pg_query($sql);
					imap_delete($mbox, $i);
				}
			}
			imap_expunge($mbox);
			imap_close($mbox);
		}
		return 'connect';
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
		$tr_email_id = $arr_message[3];
		$email = $arr_message[4];
		
		if($email==''){
			// Ambil Alamat Email Dari Error Msg
			preg_match('/\(([0-9a-zA-Z\.\-\_\@]+)\)/',addslashes($arr_bounce[6]),$matches); 
			$email = $matches[1];
		}elseif($email==''){
			preg_match('/<([0-9a-zA-Z\.\-\_\@]+)>/',addslashes($arr_bounce[6]),$matches); 
			$email=$matches[1];
		} 
		
		if(trim($email)<>'' && (int)$tr_email_id){ 
			$sql = "UPDATE tr_email";
			$sql .= " SET email_callback = FALSE, date_email_callback = NOW(), ket_error='$email_bounce_message'";
			$sql .= " WHERE tr_email_id = $tr_email_id and trim(email) = '".trim($email)."'";
			$query = pg_query($sql) or die("Invalid query!".$sql);
			
			if(!pg_affected_rows($query)){
				$sql = "	INSERT INTO bounce_inbox(";
				$sql .= " 			header_bounce, body_bounce, date_bounce";
				$sql .= " 		)VALUES(";
				$sql .= "			'$header'::varchar(100), '$slice'::varchar(400), now())";
				
				@pg_query($sql);
			}
		}else{
			$sql = "	INSERT INTO bounce_inbox(";
			$sql .= " 			header_bounce, body_bounce, date_bounce";
			$sql .= " 		)VALUES(";
			$sql .= "			'$header'::varchar(100), '$slice'::varchar(400), now())";
			
			@pg_query($sql);
		}
	}
	
	function cekRead($mbox, $i, $in_reply_to){
		//ambil tanggal kirimnya email balasan
		$headerinfo = imap_headerinfo($mbox, $i);
		$tgl_read = $headerinfo -> date;
		
		//ambil tanggal dibaca
		$slice = preg_replace('/[\=\"\']/','',imap_body($mbox, $i));
		/*$arr_message = explode("was read on",$slice);
		
		//ubah format tanggal
		$tgl_read = substr($arr_message[1],1,16);
			$tgl_read = substr($tgl_read,6,4)."-".substr($tgl_read,3,2)."-".substr($tgl_read,0,2)." ".substr($tgl_read,11,5);*/
		
		if($tgl_read != ''){
			if((int)$in_reply_to){
				$sql_ins = "INSERT INTO read_email (
							tr_email_id, waktu_read_email, body_email, keterangan
						)VALUES(
							".$in_reply_to.", '".$tgl_read."', '".$slice."'::varchar(600), 'outlook'
						)";
				$qry_ins = @pg_query($sql_ins);
				
				if(pg_affected_rows($qry_ins) > 0){
					$sql = "UPDATE tr_email";
					$sql .= " SET tgl_read = '".$tgl_read."'";
					$sql .= " WHERE tr_email_id = ".$in_reply_to;
					$sql .= " AND tgl_read is null";
					$query = @pg_query($sql);
					
					imap_delete($mbox, $i);
				}
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
						".$tr_email_id.", '".$tgl_kirim."', '".$slice."'::varchar(600), 'img_email'
					)";
			$qry_ins = @pg_query($sql_ins);
			
			if(pg_affected_rows($qry_ins)){
				$sql = "UPDATE tr_email";
				$sql .= " SET tgl_read = '".$tgl_kirim."'";
				$sql .= " WHERE tr_email_id = ".$tr_email_id;
				$sql .= " AND tgl_read is null";
				$query = @pg_query($sql);
				
				imap_delete($mbox, $i);
			}
		}
	}
	
	function show_report(){
		$no = 0;
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['flagtrans'];
		$nama_file = $_REQUEST['nama_file'];
		$report_menu = $_REQUEST['report_menu'];
		$tipe_report = $_REQUEST['tipe_report'];
		
		if($report_menu==1){
			$get_respon = get_from_mailserver($blth, $flagtrans, $nama_file);
			//$get_respon='connect';
			if($get_respon=='connect'){
				$sql_sel_tr_email = "SELECT distinct a.email, b.nama,
											substring(b.nomor_rekening,1,4) || ' ' || substring(b.nomor_rekening,5,4) || ' ' || substring(b.nomor_rekening,9,4) || ' ' || substring(b.nomor_rekening,13,4) as nomor_rekening_,
											b.nomor_rekening,
											to_char(a.date_email_send,'DD-Mon-YYYY HH24:MI:SS') as date_email_send
										FROM tr_email a, vw_email b
										WHERE a.loading_id = b.m_loading_id
											and a.email = b.email
											and a.nomor_rekening = b.nomor_rekening
											and b.blth = '$blth'
											and b.flagtrans = '$flagtrans'
											and a.email_sukses = TRUE
											and a.email_callback is null";
				if($nama_file!='' && $nama_file!='null'){
					$sql_sel_tr_email .= " and b.nama_file='$nama_file'";
				}
				$sql_sel_tr_email .= " ORDER BY b.nomor_rekening ASC";
				$qry_sel_tr_email = pg_query($sql_sel_tr_email) or die('ERROR select tr_email: '.$sql_sel_tr_email);
				
				if($tipe_report==2){
					header("Content-type: application/vnd.ms-excel");
					header("Content-Disposition: attachment; filename=".$blth."_emailSukses");
					header("Expires: 0");
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header("Pragma: public");
				}
				?>
				<table align="center">
					<tr>
						<td>
							<table width="1000" align="center" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td align="center" valign="middle" class="title" colspan="6"><strong>REPORT - LAPORAN EMAIL SUKSES KIRIM</strong></td>
								</tr>
								<tr>
									<td height="20" colspan="6">PERIODE: <?=$blth?></td>
								</tr>
							</table>
							<table width="1000" align="center" border="1" cellpadding="2" cellspacing="0">
								<tr bgcolor="#FF0000" style="color:#FFF">
									<td height="30" align="center" valign="middle"><strong>No</strong></td>
									<td align="center" valign="middle"><strong>Nomor Rekening</strong></td>
									<td align="center" valign="middle"><strong>Nama Customer</strong></td>
									<td align="center" valign="middle"><strong>Alamat e-mail</strong></td>
									<td align="center" valign="middle"><strong>Tanggal Kirim</strong></td>
								</tr>
								<?php
								while($row_sel_tr_email=pg_fetch_array($qry_sel_tr_email)){
									$no++;
									?>
									<tr>
										<td><?=$no?></td>
										<td><?=$row_sel_tr_email['nomor_rekening_']?></td>
										<td><?=$row_sel_tr_email['nama']?></td>
										<td><?=$row_sel_tr_email['email']?></td>
										<td><?=$row_sel_tr_email['date_email_send']?></td>
									</tr>
									<?php
								}
								?>
							</table>
						</td>
					</tr>
				</table>
				<?php
			}else{
				echo $get_respon;
			}
		}elseif($report_menu==2){
			$get_respon = get_from_mailserver($blth, $flagtrans, $nama_file);
			//$get_respon='connect';
			if($get_respon=='connect'){
				$sql_sel_tr_email = "SELECT a.email, to_char(a.date_email_send,'DD-Mon-YYYY HH24:MI:SS') as date_email_send, a.ket_error,
											b.nama,
											(substring(b.nomor_rekening,1,4) || ' ' || substring(b.nomor_rekening,5,4) || ' ' || substring(b.nomor_rekening,9,4) || ' ' || substring(b.nomor_rekening,13,4)) as nomor_rekening_,
											b.nomor_rekening,
											b.nama_file
									FROM tr_email a, detail b
									WHERE a.loading_id = b.m_loading_id
										and b.blth = '$blth'
										and b.flagtrans = '$flagtrans'
										and a.email_sukses = TRUE
										and a.email_callback is not null
										and a.nomor_rekening = b.nomor_rekening";
				if($nama_file!='' && $nama_file!='null'){
					$sql_sel_tr_email .= " and b.nama_file='$nama_file'";
				}
				$sql_sel_tr_email .= " ORDER BY b.nomor_rekening ASC";
				$qry_sel_tr_email = pg_query($sql_sel_tr_email) or die('ERROR select tr_email: '.$sql_sel_tr_email);
				
				if($tipe_report==2){
					header("Content-type: application/vnd.ms-excel");
					header("Content-Disposition: attachment; filename=".$blth."_emailGagal");
					header("Expires: 0");
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header("Pragma: public");
				}
				?>
				<table align="center">
					<tr>
						<td>
							<table width="1000" align="center" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td align="center" valign="middle" class="title" colspan="6"><strong>REPORT - LAPORAN EMAIL GAGAL KIRIM</strong></td>
								</tr>
								<tr>
									<td height="20" colspan="6">PERIODE: <?=$blth?></td>
								</tr>
							</table>
							<table width="1000" align="center" border="1" cellpadding="2" cellspacing="0">
								<tr bgcolor="#FF0000" style="color:#FFF">
									<td height="30" align="center" valign="middle"><strong>No</strong></td>
									<td align="center" valign="middle"><strong>Nomor Rekening</strong></td>
									<td align="center" valign="middle"><strong>Nama Customer</strong></td>
									<td align="center" valign="middle"><strong>Alamat e-mail</strong></td>
									<td align="center" valign="middle"><strong>Tanggal Kirim</strong></td>
									<td align="center" valign="middle"><strong>Pesan Error</strong></td>
								</tr>
								<?php
								while($row_sel_tr_email=pg_fetch_array($qry_sel_tr_email)){
									$no++;
									?>
									<tr>
										<td><?=$no?></td>
										<td><?=$row_sel_tr_email['nomor_rekening_']?></td>
										<td><?=$row_sel_tr_email['nama']?></td>
										<td><?=$row_sel_tr_email['email']?></td>
										<td><?=$row_sel_tr_email['date_email_send']?></td>
										<td><?=$row_sel_tr_email['ket_error']?></td>
									</tr>
									<?php
								}
								?>
							</table>
						</td>
					</tr>
				</table>
				<?php
			}else{
				echo $get_respon;
			}
		}elseif($report_menu==3){
			$get_respon = get_from_mailserver($blth, $flagtrans, $nama_file);
			//$get_respon='connect';
			if($get_respon=='connect'){
				$periode = preg_replace('/([0-9]{2})([0-9]{4})/','$2-$1',$blth);
				$sql_sel_tr_email = "SELECT body_bounce, to_char(date_bounce,'DD-Mon-YYYY HH24:MI:SS') as date_bounce, header_bounce
										FROM bounce_inbox
										WHERE date_bounce like '$periode%'
										ORDER BY date_bounce ASC";
				$qry_sel_tr_email = pg_query($sql_sel_tr_email) or die('ERROR select tr_email: '.$sql_sel_tr_email);
				
				if($tipe_report==2){
					header("Content-type: application/vnd.ms-excel");
					header("Content-Disposition: attachment; filename=".$blth."_emailLainLain");
					header("Expires: 0");
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header("Pragma: public");
				}
				?>
				<table align="center">
					<tr>
						<td>
							<table width="1000" align="center" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td align="center" valign="middle" class="title" colspan="6"><strong>REPORT - LAPORAN EMAIL SUKSES KIRIM</strong></td>
								</tr>
								<tr>
									<td height="20" colspan="6">PERIODE: <?=$blth?></td>
								</tr>
							</table>
							<table width="1000" align="center" border="1" cellpadding="2" cellspacing="0">
								<tr bgcolor="#FF0000" style="color:#FFF">
									<td height="30" align="center" valign="middle"><strong>No</strong></td>
									<td align="center" valign="middle"><strong>Tanggal</strong></td>
									<td align="center" valign="middle"><strong>Header</strong></td>
									<td align="center" valign="middle"><strong>Body</strong></td>
								</tr>
								<?php
								while($row_sel_tr_email=pg_fetch_array($qry_sel_tr_email)){
									$no++;
									?>
									<tr>
										<td><?=$no?></td>
										<td><?=$row_sel_tr_email['date_bounce']?></td>
										<td><?=$row_sel_tr_email['header_bounce']?></td>
										<td><?=$row_sel_tr_email['body_bounce']?></td>
									</tr>
									<?php
								}
								?>
							</table>
						</td>
					</tr>
				</table>
				<?php
			}else{
				echo $get_respon;
			}
		
		}
	}
	
	pg_close($con);
?>