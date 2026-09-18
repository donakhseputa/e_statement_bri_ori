<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/function.php"); ?>
<?
	$flagtrans	= $_REQUEST['flagtrans'];
	$act		= $_REQUEST['act'];
	$blth		= $_REQUEST['blth'];
	$file		= $_REQUEST['file'];
	$template_id= $_REQUEST['template_id'];
	$con		= pg_connect($connection) or die("Could not connect to database!");
	
	switch($act){
		case 'blth'				: blth($flagtrans); break;
		case 'nama_file'		: nama_file($blth,$flagtrans); break;
		case 'norek'			: norek($blth,$flagtrans,$file); break;
		case 'blth_record'		: blth_record($blth,$flagtrans); break;
		case 'filerecord'		: filerecord($blth,$flagtrans,$file); break;
		case 'kirim'			: kirim_ke_antrian(); break;
		case 'template_email'	: template_email($flagtrans); break;
		case 'attach_file'		: attach_file(); break;
		case 'email'			: email(); break;
		case 'sample'			: sample(); break;
		case 'jadwal'			: jadwal(); break;
	}
	
	function jadwal(){
	$flagtrans	= $_REQUEST['flagtrans'];
	?>
		<select name="jadwal_id" id="jadwal_id" class="combobox">
			<option selected="selected" value=""></option>
	<?
		$sql_jadwal = "SELECT jadwal_id, tgl_jadwal
						FROM m_jadwal
						WHERE flagtrans = '$flagtrans'
							and status = TRUE
							and tgl_jadwal > '".date('Y-m-d H:i:00')."'
						ORDER BY tgl_jadwal";
		$qry_jadwal = pg_query($sql_jadwal) or die('ERROR');
		while($row_jadwal = pg_fetch_array($qry_jadwal)){
		?>
			<option value="<?=$row_jadwal['jadwal_id']?>"><?=$row_jadwal['tgl_jadwal']?></option>
		<?
		}
	?>
		</select>
	<?
	}
	
	function blth($flagtrans){
	?>
		<select name="blth" id="blth" class="combobox" onchange="Loaddiv('blth_record','script_kirim_antrian.php','flagtrans=<?=$flagtrans?>'+'&act=blth_record&blth='+this.value);<? echo "$('#checkbox_nama_file').removeAttr('disabled');$('#check_attach').removeAttr('disabled');"?>cekFile();cekBlth();">
			<option selected="selected" value=""></option>
	<?
		$sql_blth = "SELECT a.blth
						FROM m_customer a
						INNER JOIN detail b
							ON a.nomor_rekening = b.nomor_rekening
						GROUP BY a.blth
						ORDER BY substring(a.blth,3,4) desc, substring(a.blth,1,2) desc";
		$qry_blth = pg_query($sql_blth) or die('ERROR');
		while($row_blth = pg_fetch_array($qry_blth)){
		?>
			<option value="<?=$row_blth['blth']?>"><?=$row_blth['blth']?></option>
		<?
		}
	?>
		</select>
	<?
	}
	
	function nama_file($blth,$flagtrans){
	?>
		<select name="nama_file" id="nama_file" class="combobox" onchange="Loaddiv('nama_file_record','script_kirim_antrian.php','flagtrans=<?=$flagtrans?>'+'&act=filerecord&blth=<?=$blth?>&file='+this.value);<? echo "$('#checkbox_norek').removeAttr('disabled');$('#checkbox_norek').removeAttr('checked');$('#norek').attr('disabled','disabled');$('#norek').val('');"?>">
			<option selected="selected" value=""></option>
	<?
		$sql_nama_file = "SELECT loading_file
							FROM m_loading
							WHERE blth = '$blth' and flagtrans = '$flagtrans'
							ORDER BY create_date desc";
		$qry_nama_file = pg_query($sql_nama_file) or die('ERROR');
		while($row_nama_file = pg_fetch_array($qry_nama_file)){
		?>
			<option value="<?=$row_nama_file['loading_file']?>"><?=$row_nama_file['loading_file']?></option>
		<?
		}
	?>
		</select>
	<?
	}
	
	function norek($blth,$flagtrans,$file){
	?>
		<select name="norek" id="norek" class="combobox" onchange="email()">
			<option selected="selected" value=""></option>
	<?
		$sql_norek = "SELECT b.nomor_rekening, b.nama
							FROM m_customer a
								INNER JOIN detail b
									ON a.nomor_rekening = b.nomor_rekening
							WHERE b.blth = '$blth' and b.flagtrans = '$flagtrans' and b.nama_file = '$file'";
		$qry_norek = pg_query($sql_norek) or die('ERROR');
		while($row_norek = pg_fetch_array($qry_norek)){
			$nama = $row_norek['nama'];
			if(strlen($nama)>13){
				$nama = str_replace(substr($nama,13),'...',$nama);
			}
		?>
			<option value="<?=$row_norek['nomor_rekening']?>"><?=$row_norek['nomor_rekening'].' - '.$nama?></option>
		<?
		}
	?>
		</select>
	<?
	}
	
	function blth_record($blth,$flagtrans){
		$sql_record = "SELECT sum(total_customer) as total_customer
							FROM m_loading
							WHERE blth = '$blth' and flagtrans = '$flagtrans'";
		$qry_record = pg_query($sql_record) or die('ERROR');
		$row_record = pg_fetch_assoc($qry_record);
		if($row_record['total_customer']!=0){
			$total_record = $row_record['total_customer'];
		}else{
			$total_record = 0;
		}
		?>
			<input type="text" name="jml_record" id="jml_record" class="textbox_1" disabled="disabled" value="<?=$total_record?>" />
        <?
	}
	
	function filerecord($blth,$flagtrans,$file){
		$sql_record = "SELECT sum(total_customer) as total_customer
							FROM m_loading
							WHERE blth = '$blth' and flagtrans = '$flagtrans' and loading_file = '$file'";
		$qry_record = pg_query($sql_record) or die('ERROR');
		$row_record = pg_fetch_assoc($qry_record);
		if($row_record['total_customer']!=0){
			$total_record = $row_record['total_customer'];
		}else{
			$total_record = 0;
		}
		?>
        	<input type="text" name="namafile_record" id="namafile_record" class="textbox_1" disabled="disabled" value="<?=$total_record?>" />
        <?
	}
	
	function attach_file(){
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['flagtrans'];
		?>
		<select name="attach_file1[]" id="attach_file1" class="combobox" onchange="attach_add()" >
        	<option value=""></option>
        <?php
		$sql_sel_attach = "SELECT m_attach_file_id, name_file
							FROM m_attach_file
							WHERE blth = '$blth'
								and flagtrans = '$flagtrans'
								and status = TRUE";
		$qry_sel_attach = pg_query($sql_sel_attach) or die('ERROR select m_attach_file: '.$sql_sel_attach);
		while($row_sel_attach=pg_fetch_array($qry_sel_attach)){
			?>
            <option value="<?=$row_sel_attach['m_attach_file_id']?>"><?=$row_sel_attach['name_file']?></option>
            <?php
		}
		?>
        </select>
        <select name="attach_file2[]" id="attach_file2" multiple="multiple" style="height:50px;width:180px;" class="combobox" >
        </select>
        <?php
	}
	
	function email(){
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['flagtrans'];
		$nama_file = $_REQUEST['nama_file'];
		$nomor_rekening = $_REQUEST['norek'];
		
		$sql_email = "SELECT email
							FROM vw_email
							WHERE blth = '$blth' and flagtrans = '$flagtrans' and nama_file = '$nama_file' and nomor_rekening = '$nomor_rekening'";
		$qry_email = pg_query($sql_email) or die('ERROR '.$sql_email);
		while($row_email = pg_fetch_array($qry_email)){
			$email[] = $row_email['email'];
		}
		$all_email = implode(';',$email);?>
        <input type="text" name="all_email" id="all_email" class="textbox_3" value="<?=$all_email?>" /><br /><br />
        <?php
	}
	
	function sample(){		
		$sql_sel_sample = "SELECT email_sample FROM sample_email";
		$qry_sel_sample = pg_query($sql_sel_sample) or die('ERROR '.$sql_sel_sample);
		$row_sel_sample = pg_fetch_assoc($qry_sel_sample);
		$sample_email = $row_sel_sample['email_sample'];
		?>
        <input type="text" name="text_sample_email" id="text_sample_email" class="textbox_3" value="<?=$sample_email?>" />
        <?php
	}
	
	function kirim_ke_antrian(){
		$pr = $_POST['pr'];
		$blth = $_POST['blth'];
		$file = $_POST['nama_file'];
		$norek = $_POST['norek'];
		$status = $_POST['status'];
		$template_id = $_POST['combo_template'];
		$arr_attach_file_id = $_POST['attach_file2'];
		$email_sample = $_POST['text_sample_email'];
		$jadwal_id = $_POST['jadwal_id'];
		$jml_record = 0;
		
		$sql_sel_status_jadwal = "SELECT flag_jadwal FROM m_jadwal WHERE jadwal_id = ".$jadwal_id."";
		$qry_sel_status_jadwal = pg_query($sql_sel_status_jadwal) or die('ERROR select flag jadwal: '.$sql_sel_status_jadwal);
		$row_stat_jadwal = pg_fetch_assoc($qry_sel_status_jadwal);
		$flag_jadwal = $row_stat_jadwal['flag_jadwal'];
		
		$sql_sel_cust_det = "SELECT a.nomor_customer, a.nomor_rekening, a.nomor_rekening as pdf_name, a.blth, a.flagtrans, a.email1, a.email2, a.email3,
									b.m_loading_id, b.nama, b.nama_file, c.tr_email_id, c.date_email_send, c.email_callback
								FROM m_customer a
									INNER JOIN detail b
										ON a.blth = b.blth
											and a.flagtrans = b.flagtrans
											and a.nomor_rekening = b.nomor_rekening
									LEFT JOIN vw_status_tr_email c
										ON b.m_loading_id = c.loading_id
											and (
												a.email1 = c.email
												or a.email2 = c.email
												or a.email3 = c.email
											)
								WHERE a.blth = '".$blth."'";
		if($file!=''){
			$sql_sel_cust_det .= " and b.nama_file = '$file'";
		}
		if($norek!=''){
			$sql_sel_cust_det .= " and b.nomor_rekening = '$norek'";
		}
		if($status==0){
			$sql_sel_cust_det .= " and c.tr_email_id is null";
		}elseif($status==1){
			$sql_sel_cust_det .= " and c.tr_email_id is not null and c.email_callback is null";
		}elseif($status==2){
			$sql_sel_cust_det .= " and c.tr_email_id is not null and c.email_callback is not null";
		}
		$sql_sel_cust_det .= " ORDER BY a.nomor_customer";
		$qry_sel_cust_det = pg_query($sql_sel_cust_det) or die('ERROR join customer dan detail: '.$sql_sel_cust_det);
		
		if(pg_num_rows($qry_sel_cust_det)>0){
			while($row_sel_cust_det = pg_fetch_array($qry_sel_cust_det)){
				$loading_id[]		= $row_sel_cust_det['m_loading_id'];
				$nomor_customer[]	= $row_sel_cust_det['nomor_customer'];
				$nomor_rekening[]	= $row_sel_cust_det['nomor_rekening'];
				$nama[]				= addslashes($row_sel_cust_det['nama']);
					$email1 = $row_sel_cust_det['email1'];
					$email2 = $row_sel_cust_det['email2'];
					$email3 = $row_sel_cust_det['email3'];
					$email_x = '';
					if($email1!=''){
						$email_x .= $email1.'##';
					}
					if($email2!=''){
						$email_x .= $email2.'##';
					}
					if($email3!=''){
						$email_x .= $email3.'##';
					}
				if($email_sample=='' || $email_sample=='null'){
					$email[] = rtrim($email_x,'##');
				}else{
					$email_sample = str_replace(';','##',trim($email_sample,';'));
					$email[] = $email_sample;
				}
				$pdf_name[]			= $row_sel_cust_det['pdf_name'].'.pdf';
				$pdf_location[]		= '../pdf/'.$row_sel_cust_det['blth'].'/'.str_replace(substr($row_sel_cust_det['nama_file'],-4),'',$row_sel_cust_det['nama_file']).'/';
			}
			
			$no_rek_exists = "";
			for($i=0;$i<count($nomor_rekening);$i++){
				$arr_email = explode('##',$email[$i]);
				for($k=0;$k<count($arr_email);$k++){
					$sql_sel_antrian = "SELECT nomor_rekening
										FROM antrian_email
										WHERE loading_id = ".$loading_id[$i]."
											and nomor_rekening = '".$nomor_rekening[$i]."'
											and email = '".$arr_email[$k]."'";
					$qry_sel_antrian = pg_query($sql_sel_antrian) or die('ERROR select antrian: '.$sql_sel_antrian);
					if(pg_num_rows($qry_sel_antrian)==0){
						$sql_ins_antrian = "INSERT INTO antrian_email(
													nomor_customer, nomor_rekening, nama,
													pdf_name, pdf_location, userid,
													tgl_antrian, template_email_id, email,
													loading_id, jadwal_id
												)VALUES(
													'".$nomor_customer[$i]."', '".$nomor_rekening[$i]."', '".$nama[$i]."',
													'".$pdf_name[$i]."', '".$pdf_location[$i]."', '".$_SESSION['userid']."',
													now(), ".$template_id.", '".$arr_email[$k]."',
													".$loading_id[$i].", ".$jadwal_id."
												);";
						$qry_ins_antrian = pg_query($sql_ins_antrian) or die('ERROR insert into antrian: '.$sql_ins_antrian);
						if(pg_affected_rows($qry_ins_antrian)){
							$jml_record++;
							//update m_jadwal
							if($flag_jadwal==''){
								$sql_upd_m_jadwal = "UPDATE m_jadwal SET flag_kirim = TRUE WHERE jadwal_id = ".$jadwal_id."";
								$qry_upd_m_jadwal = pg_query($sql_upd_m_jadwal) or die('ERROR update m_jadwal: '.$sql_upd_m_jadwal);
							}
							
							$sql_sel_antrian_id = "SELECT last_value as antrian_id FROM antrian_email_antrian_id_seq";
							$qry_sel_antrian_id = pg_query($sql_sel_antrian_id) or die('ERROR select antrian id: '.$sql_sel_antrian_id);
							$row_sel_antrian_id = pg_fetch_assoc($qry_sel_antrian_id);
							$antrian_id = $row_sel_antrian_id['antrian_id'];
							
							for($c=0;$c<count($arr_attach_file_id);$c++){
								$sql_ins_attach_id = "INSERT INTO antrian_email_attach_file(antrian_id, m_attach_file_id)
														VALUES($antrian_id,".$arr_attach_file_id[$c].")";
								$qry_ins_attach_id = pg_query($sql_ins_attach_id) or die('ERROR insert into antrian_email_attach_file: '.$sql_ins_attach_id);
							}
						}
					}else{
						if($no_rek_exists==""){
							$no_rek_exists = $nomor_rekening[$i];
						}else{
							$no_rek_exists .= ','.$nomor_rekening[$i];
						}
					}
				}
			}
		}
		
		echo $pr.'|'.$jml_record.'|'.$no_rek_exists;
	}
	
	function template_email($flagtrans){
		$sql_template = "SELECT template_email_id,nama_template FROM template_email WHERE flagtrans='$flagtrans'";
		$qry_template = pg_query($sql_template) or die('ERROR select template: '.$sql_template);
		?>
        <select id="combo_template" name="combo_template" class="combobox" onchange="cekFile()">
        	<option selected="selected" value=""></option>
			<?
            	while($row_template = pg_fetch_array($qry_template)){
					?>
                    <option value="<?=$row_template['template_email_id']?>"><?=$row_template['nama_template']?></option>
                    <?
            }
			?>
         </select>
         <?
	}
	
	pg_close($con);
?>