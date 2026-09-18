<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php");//require_once("../include/script.php"); ?>
<? require_once("../include/function.php"); ?>
<!--script type="text/javascript">
	function view_template(val){
		//alert('view template! '+val);
		var antrian_id = val;
		$.post('../report/script_feedback_email.php?act=view_template&antrian_id='+antrian_id,$("#form_template").serialize(),function(respon){
			$.fancybox(respon);
		});
	}
</script-->
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
		//case 'view_template': view_template(); break;
	}
	
	/*function view_template(){
		$antrian_id = $_REQUEST['antrian_id'];
		?>
			<iframe id="frame_view" src="../tool/script_lihat_antrian.php?act=load_template&antrian_id=<?=$antrian_id?>&typev=1" height="670" width="900" frameborder="0" style="background-color:#fff">
        		</iframe>
		<?
	}*/
	
	
	function blth(){
		$flagtrans = $_REQUEST['flagtrans'];
		?>
        <select id="blth" name="blth" class="combobox" onChange="check_nama_file();button_export();">
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
		//echo "$sql_sel_mail_server<br>";
		$qry_sel_mail_server = pg_query($sql_sel_mail_server) or die('ERROR: select mail server: '.$sql_sel_mail_server);
		
		while($row_mail_server = pg_fetch_array($qry_sel_mail_server)){
			$email_inbox = $row_mail_server['email_inbox'];
			$email_from = $row_mail_server['email_bounce_back'];
			$email_pass = $row_mail_server['email_pass'];
			
			//$mbox = imap_open("{your.imap.host:143}", "username", "password");
			//$mbox = imap_open($email_inbox, $email_from, $email_pass) or die("can't connect: " . imap_last_error());
			error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
			if($mbox = imap_open($email_inbox, $email_from, $email_pass)){
				$valid=1;
				$email_bounce_message='';
				for($i = 1; $i <= imap_num_msg($mbox); $i++){
					//$body_info = imap_body($mbox, $i);
					$slice = preg_replace('/[\=\"\']/','',imap_body($mbox, $i));
					$arr_message = explode("|",$slice);
					$client = trim(strtoupper($arr_message[1]));
					$flagtrans = $arr_message[2];
					$tr_email_id = $arr_message[3];
					$email = $arr_message[4];
					//imap_close($mbox);
					//return $client
					if($client=='BRI'){
						$header_info = imap_headerinfo($mbox, $i);
						if (strtoupper(substr($header_info->senderaddress, 0, 13)) == "MAILER-DAEMON") {
							cekBounce($mbox, $i, $header_info->senderaddress);
						}else{
							$header = pg_escape_string($header_info->senderaddress);
							$body = pg_escape_string(imap_body($mbox, $i));
							
							$sql = " insert into bounce_inbox(
											header_bounce, body_bounce, date_bounce
										)values(
											'$header'::varchar(100), '$body'::varchar(400), now()
										)";
							@pg_query($sql);
						}
						//imap_delete($mbox, $i);
					}					
				}				
				//imap_expunge($mbox);
				imap_close($mbox);
			}else{
				$valid=0;
			}				
		}
		return 'connect|'.$valid;
	}
	
	function cekBounce($mbox, $i, $header){
		$arr_bounce = explode(chr(13) . chr(10), imap_body($mbox, $i));
		$email_bounce_message = addslashes($arr_bounce[5]);
		$email_bounce_message .= " " . addslashes($arr_bounce[6]);
		
		$slice = preg_replace('/[\=\"\']/','',imap_body($mbox, $i));
		$arr_message = explode("|",$slice);
		
		//AMBIL blth dari hidden input
		$email_bounce_message = preg_replace('/[\'\/]/','',$email_bounce_message);
		$client = $arr_message[1];
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
		
		if($email<>''){ 
			$sql = "UPDATE tr_email";
			$sql .= " SET email_callback = FALSE, date_email_callback = NOW(), ket_error='$email_bounce_message'";
			$sql .= " WHERE tr_email_id = '$tr_email_id' and trim(email) = '".trim($email)."'";
		}
		$query = pg_query($sql) or die("Invalid query!");
		
		if(!pg_affected_rows($query)){
			$sql = "	INSERT INTO bounce_inbox(";
			$sql .= " 			header_bounce, body_bounce, date_bounce";
			$sql .= " 		)VALUES(";
			$sql .= "			'$header'::varchar(100), '$slice'::varchar(400), now())";
			
			@pg_query($sql);
		}
	}
	
	function show_report(){
		$no = 0;
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['flagtrans'];
		$nama_file = $_REQUEST['nama_file'];
		$nama_file_ = basename($nama_file,'.DAT');
		$report_menu = $_REQUEST['report_menu'];
		$tipe_report = $_REQUEST['tipe_report'];
		
		if($report_menu==1){
			$get_respon = get_from_mailserver($blth, $flagtrans, $nama_file);
			//print_r($get_respon);
			//die();
			$arr_respon = explode("|",$get_respon);
			//$get_respon='connect';
			if($arr_respon[0]=='connect'){
				/*$sql_sel_tr_email = "SELECT distinct a.email, b.nama, b.nomor_rekening, to_char(a.date_email_send,'DD-Mon-YYYY HH24:MI:SS') as date_email_send
										FROM tr_email a, vw_email b
										WHERE a.loading_id = b.m_loading_id
											and a.email = b.email
											and a.nomor_rekening = b.nomor_rekening
											and b.blth = '$blth'
											and b.flagtrans = '$flagtrans'
											and a.email_sukses = TRUE
											and a.email_callback is null";*/
				$sql_sel_tr_email = "SELECT distinct a.email, a.nama_customer AS nama, a.nomor_rekening, to_char(a.date_email_send,'DD-Mon-YYYY HH24:MI:SS') as date_email_send, nama_file, a.antrian_id
										FROM tr_email a, vw_email b
										WHERE a.loading_id = b.m_loading_id
											and a.email = b.email
											and b.blth = '$blth'
											and b.flagtrans = '$flagtrans'
											and a.email_sukses = TRUE
											and a.email_callback is null";							
				if($nama_file!='' && $nama_file!='null'){
					$sql_sel_tr_email .= " and b.nama_file='$nama_file'";
				}
				$sql_sel_tr_email .= " ORDER BY nama_file, date_email_send, a.nomor_rekening ASC";
				//echo "x: ".$sql_sel_tr_email."<br><br>";
				$qry_sel_tr_email = pg_query($sql_sel_tr_email) or die('ERROR select tr_email: '.$sql_sel_tr_email);
				
				if($tipe_report==2){
					header("Content-type: application/vnd.ms-excel");
					header("Content-Disposition: attachment; filename=".$blth."_emailSukses_".$nama_file_);
					header("Expires: 0");
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header("Pragma: public");
					$dlxls="";
				}else{
					$dlxls='<a href="#" onClick="window.open(\'script_feedback_email.php?act=show_report&flagtrans=BC&blth='.$blth.'&nama_file='.$nama_file.'&report_menu=1&tipe_report=2\');">DOWNLOAD</a> (XLS)';//echo "x: ".$sql_sel_tr_email."<br><br>";
				}

				if($arr_respon[1]==1){
					echo "<b>Connect Successfull to Server Mail</b>";
				}else{
					echo "<b>Connect Failed to Server Mail: Server may busy or some configuration not valid</b>";
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
									<td height="20" colspan="4">PERIODE: <?=$blth?></td>
									<td height="20" colspan="2" align="right"><?=$dlxls?></td>
								</tr>
							</table>
							<table width="1000" align="center" border="1" cellpadding="2" cellspacing="0">
								<tr bgcolor="#FF0000" style="color:#FFF">
									<td height="30" align="center" valign="middle"><strong>No</strong></td>
									<td align="center" valign="middle"><strong>Cycle</strong></td>
									<td align="center" valign="middle"><strong>Nomor Rekening</strong></td>
									<td align="center" valign="middle"><strong>Nama Customer</strong></td>
									<td align="center" valign="middle"><strong>Alamat e-mail</strong></td>
									<td align="center" valign="middle"><strong>Tanggal Kirim</strong></td>
									<?php 
										if($tipe_report==1)
											echo "<td align='center' valign='middle'><strong>Template</strong></td>";
									?>
								</tr>
								<?php
								while($row_sel_tr_email=pg_fetch_array($qry_sel_tr_email)){
									$no++;
									?>
									<tr>
										<td valign='top'><?=$no?></td>
										<td valign='top'><?=$row_sel_tr_email['nama_file']?></td>
										<td><?=str_replace('#','<br>\'','\''.$row_sel_tr_email['nomor_rekening'])?></td>
										<td><?=str_replace('#','<br>',$row_sel_tr_email['nama'])?></td>
										<td valign='top'><?=$row_sel_tr_email['email']?></td>
										<td valign='top'><?=$row_sel_tr_email['date_email_send']?></td>
										<?php 
											if($tipe_report==1)
												echo "<td align='center'><a href='#' onClick='view_template(".$row_sel_tr_email['antrian_id'].")'>[V]</a></td>";
										?>
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
				echo $arr_respon[0];
			}
		}elseif($report_menu==2){
			$get_respon = get_from_mailserver($blth, $flagtrans, $nama_file);
			$arr_respon = explode("|",$get_respon);
			//$get_respon='connect';
			if($arr_respon[0]=='connect'){
				/*$sql_sel_tr_email = "SELECT a.email, to_char(a.date_email_send,'DD-Mon-YYYY HH24:MI:SS') as date_email_send, a.ket_error,
											b.nama, b.nomor_rekening, b.nama_file
									FROM tr_email a, detail b
									WHERE a.loading_id = b.m_loading_id
										and b.blth = '$blth'
										and b.flagtrans = '$flagtrans'
										and a.email_sukses = TRUE
										and a.email_callback is not null
										and a.nomor_rekening = b.nomor_rekening";*/
				$sql_sel_tr_email = "SELECT a.email, to_char(a.date_email_send,'DD-Mon-YYYY HH24:MI:SS') as date_email_send, a.ket_error,
											a.nama_customer AS nama, a.nomor_rekening, b.nama_file
									FROM tr_email a, detail b
									WHERE a.loading_id = b.m_loading_id
										and b.blth = '$blth'
										and b.flagtrans = '$flagtrans'
										and a.email_sukses = TRUE
										and a.email_callback is not null AND a.email IN (SELECT DISTINCT(email1) FROM m_customer WHERE blth='$blth')";						
				if($nama_file!='' && $nama_file!='null'){
					$sql_sel_tr_email .= " and b.nama_file='$nama_file'";
				}
				$sql_sel_tr_email .= " GROUP BY  
					a.email, date_email_send, a.ket_error, a.nama_customer, a.nomor_rekening, b.nama_file 
				ORDER BY b.nama_file, date_email_send, a.nomor_rekening ASC";
				$qry_sel_tr_email = pg_query($sql_sel_tr_email) or die('ERROR select tr_email: '.$sql_sel_tr_email);
				
				if($tipe_report==2){
					header("Content-type: application/vnd.ms-excel");
					header("Content-Disposition: attachment; filename=".$blth."_emailGagal_".$nama_file_);
					header("Expires: 0");
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header("Pragma: public");
					$dlxls="";
				}else{
					$dlxls='<a href="#" onClick="window.open(\'script_feedback_email.php?act=show_report&flagtrans=BC&blth='.$blth.'&nama_file='.$nama_file.'&report_menu=2&tipe_report=2\');">DOWNLOAD</a> (XLS)';
				}

				if($arr_respon[1]==1){
					echo "<b>Connect Successfull to Server Mail</b>";
				}else{
					echo "<b>Connect Failed to Server Mail: Server may busy or some configuration not valid</b>";
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
									<td height="20" colspan="4">PERIODE: <?=$blth?></td>
									<td height="20" colspan="2" align="right"><?=$dlxls?></td>
								</tr>
							</table>
							<table width="1000" align="center" border="1" cellpadding="2" cellspacing="0">
								<tr bgcolor="#FF0000" style="color:#FFF">
									<td height="30" align="center" valign="middle"><strong>No</strong></td>
									<td align="center" valign="middle"><strong>Cycle</strong></td>
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
										<td valign='top'><?=$no?></td>
										<td nowrap><?=$row_sel_tr_email['nama_file']?></td>
										<td><?=str_replace('#','<br>\'','\''.$row_sel_tr_email['nomor_rekening'])?></td>
										<td><?=str_replace('#','<br>',$row_sel_tr_email['nama'])?></td>
										<td><?=$row_sel_tr_email['email']?></td>
										<td nowrap><?=$row_sel_tr_email['date_email_send']?></td>
										<td valign='top'><?=$row_sel_tr_email['ket_error']?></td>
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
				echo $arr_respon[0];
			}
		}elseif($report_menu==4){//all send status
			$get_respon = get_from_mailserver($blth, $flagtrans, $nama_file);
			$arr_respon = explode("|",$get_respon);
			if($arr_respon[0]=='connect'){
				$sql_sel_tr_email = "SELECT a.email, to_char(a.date_email_send,'DD-Mon-YYYY HH24:MI:SS') as date_email_send,
											a.nama_customer AS nama, a.nomor_rekening, b.nama_file, a.email_callback
									FROM tr_email a, detail b
									WHERE a.loading_id = b.m_loading_id
										and b.blth = '$blth'
										and b.flagtrans = '$flagtrans'
										and a.email_sukses = TRUE
										AND a.email IN (SELECT DISTINCT(email1) FROM m_customer WHERE blth='$blth')";						
				if($nama_file!='' && $nama_file!='null'){
					$sql_sel_tr_email .= " and b.nama_file='$nama_file'";
				}
				$sql_sel_tr_email .= " GROUP BY  
					a.email, date_email_send, a.nama_customer, a.nomor_rekening, b.nama_file, a.email_callback 
				ORDER BY b.nama_file, date_email_send, a.nomor_rekening ASC";
				$qry_sel_tr_email = pg_query($sql_sel_tr_email) or die('ERROR select tr_email: '.$sql_sel_tr_email);
				
				if($tipe_report==2){
					header("Content-type: application/vnd.ms-excel");
					header("Content-Disposition: attachment; filename=".$blth."_emailReport_".$nama_file_);
					header("Expires: 0");
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header("Pragma: public");
					$dlxls="";
				}else{
					$dlxls='<a href="#" onClick="window.open(\'script_feedback_email.php?act=show_report&flagtrans=BC&blth='.$blth.'&nama_file='.$nama_file.'&report_menu=4&tipe_report=2\');">DOWNLOAD</a> (XLS)';
				}

				if($arr_respon[1]==1){
					echo "<b>Connect Successfull to Server Mail</b>";
				}else{
					echo "<b>Connect Failed to Server Mail: Server may busy or some configuration not valid</b>";
				}
				?>
				<table align="center">
					<tr>
						<td>
							<table width="1000" align="center" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td align="center" valign="middle" class="title" colspan="6"><strong>REPORT - LAPORAN EMAIL TERKIRIM</strong></td>
								</tr>
								<tr>
									<td height="20" colspan="4">PERIODE: <?=$blth?></td>
									<td height="20" colspan="2" align="right"><?=$dlxls?></td>
								</tr>
							</table>
							<table width="1000" align="center" border="1" cellpadding="2" cellspacing="0">
								<tr bgcolor="#FF0000" style="color:#FFF">
									<td height="30" align="center" valign="middle"><strong>No</strong></td>
									<td align="center" valign="middle"><strong>Cycle</strong></td>
									<td align="center" valign="middle"><strong>Nomor Rekening</strong></td>
									<td align="center" valign="middle"><strong>Nama Customer</strong></td>
									<td align="center" valign="middle"><strong>Alamat e-mail</strong></td>
									<td align="center" valign="middle"><strong>Tanggal Kirim</strong></td>
									<td align="center" valign="middle"><strong>Status</strong></td>
								</tr>
								<?php
								while($row_sel_tr_email=pg_fetch_array($qry_sel_tr_email)){
									$no++;
									$cb=trim($row_sel_tr_email['email_callback']);
									($cb=='f')?($st='GAGAL'):($st='SUKSES');
									?>
									<tr>
										<td valign='top'><?=$no?></td>
										<td nowrap><?=$row_sel_tr_email['nama_file']?></td>
										<td><?=str_replace('#','<br>\'','\''.$row_sel_tr_email['nomor_rekening'])?></td>
										<td><?=str_replace('#','<br>',$row_sel_tr_email['nama'])?></td>
										<td><?=$row_sel_tr_email['email']?></td>
										<td nowrap><?=$row_sel_tr_email['date_email_send']?></td>
										<td valign='top'><?=$st?></td>
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
				echo $arr_respon[0];
			}
		}elseif($report_menu==5){//pdf list
			$tval = $_REQUEST['tval'];
			$get_respon = get_from_mailserver($blth, $flagtrans, $nama_file);
			$arr_respon = explode("|",$get_respon);
			if($arr_respon[0]=='connect'){
				if($tval==0){
					$tblnm='vw_email';
					$ldvar='m_loading_id';
					$jmlkr='0';
				}else{
					$tblnm='tr_email';
					$ldvar='loading_id';
					$jmlkr='COUNT(a.nomor_rekening)';
				}
				$sql_sel_tr_email = "SELECT b.nama, b.nomor_rekening, b.nama_file, b.pdf_name, $jmlkr AS jml_kirim
							FROM $tblnm a JOIN detail b ON a.$ldvar=b.m_loading_id AND a.nomor_rekening=b.nomor_rekening
							WHERE b.blth = '$blth' and b.flagtrans = '$flagtrans' 
								AND a.email IN (SELECT DISTINCT(email1) FROM m_customer WHERE blth='032012')";						
				if($nama_file!='' && $nama_file!='null'){
					$sql_sel_tr_email .= " and b.nama_file='$nama_file'";
				}
				$sql_sel_tr_email .= " GROUP BY  
					b.nama, b.nomor_rekening, b.nama_file, b.pdf_name
				ORDER BY b.nama_file, b.nomor_rekening ASC";
				$qry_sel_tr_email = pg_query($sql_sel_tr_email) or die('ERROR select tr_email: '.$sql_sel_tr_email);
				
				if($tipe_report==2){
					header("Content-type: application/vnd.ms-excel");
					header("Content-Disposition: attachment; filename=".$blth."_pdfList_".$nama_file_);
					header("Expires: 0");
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header("Pragma: public");
					$dlxls="";
				}else{	
					$dlxls='<a href="#" onClick="window.open(\'script_feedback_email.php?act=show_report&flagtrans=BC&blth='.$blth.'&nama_file='.$nama_file.'&report_menu=5&tipe_report=2\');">DOWNLOAD</a> (XLS)';//echo $sql_sel_tr_email.'<br>';
				}

				if($arr_respon[1]==1){
					echo "<b>Connect Successfull to Server Mail</b>";
				}else{
					echo "<b>Connect Failed to Server Mail: Server may busy or some configuration not valid</b>";
				}
				?>
				<table align="center">
					<tr>
						<td>
							<table width="1000" align="center" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td align="center" valign="middle" class="title" colspan="6"><strong>REPORT - LAPORAN DAFTAR PDF</strong></td>
								</tr>
								<tr>
									<td height="20" colspan="3">PERIODE: <?=$blth?></td>
									<td height="20" colspan="2" align="right"><?=$dlxls?></td>
								</tr>

							</table>
							<table width="1000" align="center" border="1" cellpadding="2" cellspacing="0">
								<tr bgcolor="#FF0000" style="color:#FFF">
									<td height="30" align="center" valign="middle"><strong>No</strong></td>
									<td align="center" valign="middle"><strong>Cycle</strong></td>
									<td align="center" valign="middle"><strong>Nomor Rekening</strong></td>
									<td align="center" valign="middle"><strong>Nama Customer</strong></td>
									<!--td align="center" valign="middle"><strong>Alamat e-mail</strong></td-->
									<td align="center" valign="middle"><strong>Nama PDF</strong></td>
									<td align="center" valign="middle"><strong>Jumlah Terkirim</strong></td>
								</tr>
								<?php
								$totkirim=0;
								while($row_sel_tr_email=pg_fetch_array($qry_sel_tr_email)){
									$no++;$totkirim=$totkirim+$row_sel_tr_email['jml_kirim'];
									if($tipe_report==2) $pdflink=$row_sel_tr_email['pdf_name'].'.PDF'; else $pdflink='<a target="_blank" href="../pdf/'.$blth.'/'.basename($row_sel_tr_email['nama_file'],'.DAT').'/'.$row_sel_tr_email['pdf_name'].'.pdf">'.$row_sel_tr_email['pdf_name'].'.PDF</a>';
									?>
									<tr>
										<td valign='top'><?=$no?></td>
										<td nowrap><?=$row_sel_tr_email['nama_file']?></td>
										<td><?=str_replace('#','<br>\'','\''.$row_sel_tr_email['nomor_rekening'])?></td>
										<td><?=str_replace('#','<br>',$row_sel_tr_email['nama'])?></td>
										<!--td><?=$row_sel_tr_email['email']?></td-->
										<td align='right' nowrap><?=$pdflink?></td>
										<td align='right' nowrap><?=$row_sel_tr_email['jml_kirim']?></td>
									</tr>
									<?php
								}
								echo "<tr><td></td><td colspan=4><b>TOTAL</b></td><td align=right>$totkirim</td></tr>";
								?>
							</table>
						</td>
					</tr>
				</table>
				<?php
			}else{
				echo $arr_respon[0];
			}
		}elseif($report_menu==3){
			$get_respon = get_from_mailserver($blth, $flagtrans, $nama_file);
			$arr_respon = explode("|",$get_respon);
			//$get_respon='connect';
			if($arr_respon[0]=='connect'){
				$periode = preg_replace('/([0-9]{2})([0-9]{4})/','$2-$1',$blth);
				$sql_sel_tr_email = "SELECT body_bounce, to_char(date_bounce,'DD-Mon-YYYY HH24:MI:SS') as date_bounce, header_bounce
										FROM bounce_inbox
										WHERE date_bounce like '$periode%'
										ORDER BY date_bounce ASC";
				$qry_sel_tr_email = pg_query($sql_sel_tr_email) or die('ERROR select tr_email: '.$sql_sel_tr_email);
				
				if($tipe_report==2){
					header("Content-type: application/vnd.ms-excel");
					header("Content-Disposition: attachment; filename=".$blth."_emailBounce");
					header("Expires: 0");
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header("Pragma: public");
				}
				if($arr_respon[1]==1){
					echo "<b>Connect Successfull to Server Mail</b>";
				}else{
					echo "<b>Connect Failed to Server Mail: Server may busy or some configuration not valid</b>";
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
				echo $arr_respon[0];
			}
			//echo "x: ".$sql_sel_tr_email."<br><br>";		
		}
	}
	
	pg_close($con);
?>