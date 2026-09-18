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
		case 'norek'			: norek($ord,$blth,$flagtrans,$file); break;
		case 'blth_record'		: blth_record($blth,$flagtrans); break;
		case 'filerecord'		: filerecord($blth,$flagtrans,$file); break;
		case 'kirim'			: kirim_ke_antrian(); break;
		case 'template_email'	: template_email($flagtrans); break;
		case 'template_name'	: template_name(); break;
		case 'attach_file'		: attach_file(); break;
		case 'email'			: email(); break;
		case 'sample'			: sample(); break;
		case 'file_customer'	: file_customer($blth,$flagtrans);break;
		case 'kinerja_file'		: kinerja_file(); break;
	}
	
	function blth($flagtrans){
	?>
		<select name="blth" id="blth" class="combobox" onchange="
        		Loaddiv('blth_record','script_pilihan_kirim_antrian_sk.php','flagtrans=<?=$flagtrans?>'+'&act=blth_record&blth='+this.value);
                $('#checkbox_nama_file').removeAttr('disabled');
                $('#checkbox_nama_file').removeAttr('checked');
                $('#checkbox_jadwal').removeAttr('disabled');
                $('#checkbox_jadwal').removeAttr('checked');
                $('#check_attach').removeAttr('disabled');
                $('#check_attach').removeAttr('checked');
                cek_attach();
                oncheck_file();
                cekFile();
                cekBlth();
                ">
			<option selected="selected" value=""></option>
	<?php
	$tabeldetail = "detail_" . $blth . "_" . $flagtrans;
		$sql_blth = "SELECT blth
						FROM m_loading WHERE flagtrans='$flagtrans'
						GROUP BY blth
						ORDER BY substring(blth,3,4) desc, substring(blth,1,2) desc";
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
		<select name="nama_file" id="nama_file" class="combobox" onchange="
        	Loaddiv('nama_file_record','script_pilihan_kirim_antrian_sk.php','flagtrans=<?=$flagtrans?>'+'&act=filerecord&blth=<?=$blth?>&file='+this.value);
			$('#checkbox_inputnorek').removeAttr('checked');$('#checkbox_inputnorek').removeAttr('disabled');
			<? echo "$('#checkbox_norek').removeAttr('disabled');$('#checkbox_norek').removeAttr('checked');$('#norek').attr('disabled','disabled');$('#norek').val('');"?>
            ">
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
	
	function file_customer($blth,$flagtrans){
	?>
		<select name="file_cust" id="file_cust" class="combobox">
	<?
		$sql_file_cust = "SELECT log_customer_id, nama_file
							FROM log_customer
							WHERE blth = '$blth' and flagtrans = '$flagtrans'
							ORDER BY create_date desc";
		$qry_file_cust = pg_query($sql_file_cust) or die('ERROR');
		while($row_file_cust = pg_fetch_array($qry_file_cust)){
		?>
			<option value="<?=$row_file_cust['log_customer_id']?>"><?=$row_file_cust['nama_file']?></option>
		<?
		}
	?>
		</select>
	<?
	}
	
	function norek($ord,$blth,$flagtrans,$file){
		$ord = $_REQUEST['ord'];
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['flagtrans'];
		$file = $_REQUEST['file'];
		
		$blth_balik = substr($blth,-4).substr($blth,0,2);
		
		$BLTH_BARU = "201408";
		if($blth_balik>$BLTH_BARU){
			$tabeldetail = "detail_" . $blth."_".$flagtrans;
			$sql_cek = "SELECT a.m_loading_id FROM m_loading a LEFT JOIN $tabeldetail b ON a.m_loading_id=b.m_loading_id 
			WHERE a.blth='$blth' AND a.flagtrans='$flagtrans'";
			$exe_cek = @pg_query($sql_cek);
			if($exe_cek){
				$tabeldetail = "detail_" . $blth."_".$flagtrans;
			}else{
				$tabeldetail = "detail";
			}
		}else{
			$tabeldetail = "detail";
		}
		
		
		switch($ord){
		case 1:
		$urut = "ORDER BY nomor_rekening ASC";
		break;
		case 2:
		$urut = "ORDER BY nama ASC";
		break;
		case 3:
		$urut = "ORDER BY nama DESC";
		break;
		case 4:
		$urut = "ORDER BY jml_hlm ASC";
		break;
		case 5:
		$urut = "ORDER BY jml_hlm DESC";
		break;
		}
		?>
			<select name="norek" id="norek" class="combobox" onchange="email()" style="font-size:8pt;">
				<option selected="selected" value=""></option>
		<?
			$sql_norek = "SELECT DISTINCT nomor_rekening, nama, jml_hlm
								FROM $tabeldetail 
								WHERE blth = '$blth' and flagtrans = '$flagtrans' and nama_file = '$file' AND email!=''
								$urut";
			$qry_norek = pg_query($sql_norek) or die('ERROR');
			while($row_norek = pg_fetch_array($qry_norek)){
				$nama = $row_norek['nama'];
				if(strlen($nama)>13){
					$nama = substr($nama,0,13).'...';
				}
			?>
				<option value="<?=$row_norek['nomor_rekening']?>"><?=$row_norek['nomor_rekening'].' - '. $row_norek['jml_hlm'] . ' hlm - '. $nama?></option>
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
		$nama_file = $_REQUEST['nama_file'];
		$flagtrans = $_REQUEST['flagtrans'];
		
		$sql_cek_selektif = "
			SELECT *
			FROM attach_selektif a
				INNER JOIN m_loading b
					ON a.m_loading_id = b.m_loading_id
			WHERE b.blth = '$blth'
				and b.flagtrans = '$flagtrans'";
		if($nama_file != ""){
			$sql_cek_selektif .= " and b.loading_file = '$nama_file'";
		}
		$qry_cek_selektif = pg_query($sql_cek_selektif);
		
		if(pg_num_rows($qry_cek_selektif) > 0){
			$sql_sel_selektif = "
				SELECT a.urutan_attach_file, c.m_attach_file_id, c.name_file
				FROM attach_selektif a
					INNER JOIN m_loading b
						ON a.m_loading_id = b.m_loading_id
					INNER JOIN m_attach_file c
						ON a.m_attach_file_id = c.m_attach_file_id
				WHERE b.blth = '$blth'
					and b.flagtrans = '$flagtrans'";
			if($nama_file != ""){
				$sql_sel_selektif .= " and b.loading_file = '$nama_file'";
			}
			$sql_sel_selektif .= " and c.status = TRUE
				ORDER BY a.urutan_attach_file ASC, a.m_attach_file_id ASC";
			$qry_sel_selektif = pg_query($sql_sel_selektif);
			?>
            <select name="attach_file2[]" id="attach_file2" multiple="multiple" style="height:50px;width:180px;" class="combobox" >
				<?php
                while($row_sel_selektif = pg_fetch_array($qry_sel_selektif)){
					?>
                    <option value="<?php echo $row_sel_selektif['m_attach_file_id']?>" selected="selected"><?php echo $row_sel_selektif['urutan_attach_file'].". ".$row_sel_selektif['name_file']?></option>
                    <?php
                }
				?>
            </select>
            <script language="javascript">
				$("#tipe_attach").val('2');
            </script>
            <?php
		}else{
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
            <script language="javascript">
				$("#tipe_attach").val('1');
            </script>
        	<?php
		}
	}
	
	function email(){
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['flagtrans'];
		$nama_file = $_REQUEST['nama_file'];
		$nomor_rekening = $_REQUEST['norek'];
		
		$blth_balik = substr($blth,-4).substr($blth,0,2);
		
		$BLTH_BARU = "201408";
		if($blth_balik>$BLTH_BARU){
			$tabeldetail = "detail_" . $blth."_".$flagtrans;
			$sql_cek = "SELECT a.m_loading_id FROM m_loading a LEFT JOIN $tabeldetail b ON a.m_loading_id=b.m_loading_id 
			WHERE a.blth='$blth' AND a.flagtrans='$flagtrans'";
			$exe_cek = @pg_query($sql_cek);
			if($exe_cek){
				$tabeldetail = "detail_" . $blth."_".$flagtrans;
			}else{
				$tabeldetail = "detail";
			}
		}else{
			$tabeldetail = "detail";
		}
		
		$sql_email = "SELECT email
							FROM $tabeldetail 
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
		$tanda_file = $_GET['tanda'];
		$flagtrans = $_POST['flagtrans'];
		$pr = $_POST['pr'];
		$blth = $_POST['blth'];
		$file = $_POST['nama_file'];
		$file_cust_id = $_POST['file_cust'];
		$norek = $_POST['norek'];
		$status = $_POST['status'];
		$template_id = $_POST['combo_template'];
		$tipe_attach = $_POST['tipe_attach'];
		$arr_attach_file_id = $_POST['attach_file2'];
		$email_sample = $_POST['text_sample_email'];
		$ord = $_POST['pilihan_order'];
		$jeda = $_POST['jeda'];
		$rec_split = $_POST['rec_split'];
		if(isset($_POST['tipe_kinerja'])) $blth_kinerja = $_POST['tipe_kinerja']; else $blth_kinerja = "";
		$jml_record = 0;
		
		$checkbox_inputnorek = $_POST['checkbox_inputnorek'];
		$cardno_manual = $_POST['cardno_manual'];
		$checkbox_norek = $_POST['checkbox_norek'];
		$checkbox_jadwal = $_POST['checkbox_jadwal'];
		
		$norek2 = '';
		if($checkbox_inputnorek=='on'){
			if($tanda_file=='file'){
				$file_data = $_POST["loading_cardno"];
				$folder_dest = '../tmp/file_upload/';
				$norek2 = get_file_cardno($folder_dest,$file_data);
			}else{
				$norek2 = get_cardno_txt($cardno_manual);
			}
		}
		
		$tanda_jadwal_antrian='';
		if($checkbox_jadwal=='on'){
			$tanda_jadwal_antrian= 'ok';
		}
		$blth_balik = substr($blth,-4).substr($blth,0,2);
		
		$BLTH_BARU = "201408";
		if($blth_balik>$BLTH_BARU){
			$tabeldetail = "detail_" . $blth."_".$flagtrans;
			$sql_cek = "SELECT a.m_loading_id FROM m_loading a LEFT JOIN $tabeldetail b ON a.m_loading_id=b.m_loading_id 
			WHERE a.blth='$blth' AND a.flagtrans='$flagtrans'";
			$exe_cek = @pg_query($sql_cek);
			if($exe_cek){
				$tabeldetail = "detail_" . $blth."_".$flagtrans;
			}else{
				$tabeldetail = "detail";
			}
		}else{
			$tabeldetail = "detail";
		}
		
		switch($ord){
		case 1:
		$urut = "ORDER BY b.nomor_rekening ASC";
		break;
		case 2:
		$urut = "ORDER BY b.nama ASC";
		break;
		case 3:
		$urut = "ORDER BY b.nama DESC";
		break;
		case 4:
		$urut = "ORDER BY b.jml_hlm ASC";
		break;
		case 5:
		$urut = "ORDER BY b.jml_hlm DESC";
		break;
		}
		
		//CEK EMBED CODE
		$sql_cek = "SELECT isi_email FROM template_email WHERE template_email_id='$template_id'";
		$exe_cek = pg_query($sql_cek);
		$row_cek = pg_fetch_array($exe_cek);
		$isi_template = $row_cek['isi_email'];
		$cek_embed = substr_count($isi_template,'src="cid:1');
		if($cek_embed>0){
			$cidpos = strpos($isi_template,'src="cid:1');
			$kodecid = substr($isi_template, $cidpos, 13);
			$kodecid = substr($kodecid, -4);
			$sql_cid = "SELECT m_attach_file_id FROM m_attach_file WHERE cid='$kodecid'";
			$exe_cid = pg_query($sql_cid);
			$row_cid = pg_fetch_array($exe_cid);
			$cid = $row_cid['m_attach_file_id'];
		} else {
			$cid = "";
		}
		//END CEK EMBED CODE
		
		
		// ************  20150306 Revisi ************ //
		//-- DROP VIEW $nama_view;
		$nama_view='vw_'.date('dmyhis');
		$sql_view = "
			CREATE OR REPLACE VIEW $nama_view AS 
					select 
					a.status_sample, a.tr_email_id, a.nomor_rekening, a.email, a.loading_id, a.date_email_send, a.email_callback, b.flagtrans, b.loading_file, b.blth
					from tr_email a
					LEFT JOIN m_loading b ON b.m_loading_id=a.loading_id
					where b.loading_file='$file'
					AND status_sample = 'f';
			";
		//ALTER TABLE $nama_view OWNER TO postgres;
		//die ( $sql_view );
		$qry_view = @pg_query($sql_view);
		// ************  20150306 Revisi ************ //
		
		
		$tgl_jadwal = $_POST['tgl_jadwal'];
		$year_jadwal = date('Y',strtotime($tgl_jadwal));
		$month_jadwal = date('m',strtotime($tgl_jadwal));
		$date_jadwal = date('d',strtotime($tgl_jadwal));
		$hour_jadwal = $_POST['hour_jadwal'];
		$min_jadwal = $_POST['min_jadwal'];
		
		$sql_sel_cust_det = "SELECT b.nomor_customer, b.nomor_rekening, b.blth, b.flagtrans, b.email,
									b.m_loading_id, b.nama, b.nama_file, b.jml_hlm, c.tr_email_id, c.date_email_send, c.email_callback, b.pdf_name as pdf_name
								FROM $tabeldetail b 
									LEFT JOIN $nama_view c
										ON b.m_loading_id = c.loading_id
											and b.nomor_rekening = c.nomor_rekening
								WHERE b.blth = '".$blth."' AND b.flagtrans='$flagtrans' AND b.email!=''";
		if($file!=''){
			$sql_sel_cust_det .= " and b.nama_file = '$file'";
		}
		if($norek!=''){
			$sql_sel_cust_det .= " and b.nomor_rekening = '$norek'";
		}
		if($norek2!=''){
			$sql_sel_cust_det .= " and b.nomor_rekening in $norek2";
		}
		if($status==1){
			$sql_sel_cust_det .= " and c.tr_email_id is null";
		}elseif($status==2){
			$sql_sel_cust_det .= " and c.tr_email_id is not null and c.email_callback is null";
		}elseif($status==3){
			$sql_sel_cust_det .= " and c.tr_email_id is not null and c.email_callback is not null";
		}
		$sql_sel_cust_det .= " $urut";
		
		//die ( $sql_sel_cust_det );
		$qry_sel_cust_det = pg_query($sql_sel_cust_det) or die('ERROR join customer dan detail: '.$sql_sel_cust_det);
		
		$bnyk_antrian = pg_num_rows($qry_sel_cust_det);
		$bnyk_jadwal = ceil($bnyk_antrian/$rec_split);
		
		if($bnyk_antrian>0){
			while($row_sel_cust_det = pg_fetch_array($qry_sel_cust_det)){
				$loading_id[]		= $row_sel_cust_det['m_loading_id'];
				$nomor_customer[]	= $row_sel_cust_det['nomor_customer'];
				$nomor_rekening[]	= $row_sel_cust_det['nomor_rekening'];
				$nama[]				= addslashes($row_sel_cust_det['nama']);
				$blthnya[]			= $row_sel_cust_det['blth'];
				$email_x = str_replace(";","##",$row_sel_cust_det['email']);
				$m_loading_id		= $row_sel_cust_det['m_loading_id'];

				if($email_sample=='' || $email_sample=='null'){
					$email[] = rtrim($email_x,'##');
					$status_sample[] = 'f';
					$bypass_apr = "N";
				}else{
					$email_sample = str_replace(';','##',trim($email_sample,';'));
					$email[] = $email_sample;
					$status_sample[] = 't';
					$bypass_apr = "Y";
				}
				$pdf_name[]			= $row_sel_cust_det['pdf_name'].".pdf";
				$folder_file		= str_replace(substr($row_sel_cust_det['nama_file'],-4),'',$row_sel_cust_det['nama_file']);
				/*$ex 				= explode('.',$row_sel_cust_det['nama_file']);
				$ext				= '';
				if(count($ex)>1) $ext = '.'.$ex[count($ex)-1];
				$folder_file		= str_replace($ext,'',$row_sel_cust_det['nama_file']);*/
				$pdf_location[]		= '../pdf/'.$row_sel_cust_det['flagtrans'].'/'.$row_sel_cust_det['blth'].'/'.$folder_file.'/';
				$flag_attach[]		= $row_sel_cust_det['flag_attach'];
			}
			
			$log_approval_id = log_approval();
			if($bypass_apr == "Y"){
				bypass_approval($log_approval_id);
			}
			
			$no_rek_exists = "";
			$antrianke = 0;
			$jadwalke = 0;
			for($i=0;$i<count($nomor_rekening);$i++){
				$arr_email = explode('##',$email[$i]);
				for($k=0;$k<count($arr_email);$k++){
					$sql_sel_antrian = "SELECT nomor_rekening
										FROM antrian_email
										WHERE loading_id = ".$loading_id[$i]."
											and nomor_rekening = '".$nomor_rekening[$i]."'
											and email = '".addslashes($arr_email[$k])."'";
					$qry_sel_antrian = @pg_query($sql_sel_antrian);
					if(@pg_num_rows($qry_sel_antrian)==0){
						$antrianke++;
						if($tanda_jadwal_antrian=='ok'){
							if($antrianke==$jadwalke*$rec_split+1){
								//$sched = date('Y-m-d H:i:00',mktime($hour_jadwal,$min_jadwal+($jeda*$jadwalke),0,$month_jadwal,$date_jadwal,$year_jadwal));
								cek_m_jadwal($year_jadwal,$month_jadwal,$date_jadwal,$hour_jadwal,$min_jadwal,$jeda,$jadwalke,$flagtrans,$rec_split);
								
								$sql_sel_m_jadwal = "SELECT last_value FROM m_jadwal_jadwal_id_seq";
								$qry_sel_m_jadwal = pg_query($sql_sel_m_jadwal) or die('ERROR select m_jadwal: '.$sql_sel_m_jadwal);
								$row_sel_m_jadwal = pg_fetch_assoc($qry_sel_m_jadwal);
								$jadwal_id = $row_sel_m_jadwal['last_value'];
								$jadwalke++;
							}
							$header_jadwal_id = 'jadwal_id,';
							$val_jadwal_id = $jadwal_id.",";
						}else{
							$header_jadwal_id = '';
							$val_jadwal_id = '';
						}
						$sql_ins_antrian = "INSERT INTO antrian_email(
													nomor_customer, nomor_rekening, nama,
													pdf_name, pdf_location, userid,
													tgl_antrian, template_email_id, email,
													loading_id, ".$header_jadwal_id." status_sample, blth, flagtrans,
													log_approval_id
												)VALUES(
													'".$nomor_customer[$i]."', '".$nomor_rekening[$i]."', '".$nama[$i]."',
													'".$pdf_name[$i]."', '".$pdf_location[$i]."', '".$_SESSION['userid']."',
													now(), ".$template_id.", '".addslashes($arr_email[$k])."',
													".$loading_id[$i].", ".$val_jadwal_id." '".$status_sample[$i]."', '".$blthnya[$i]."', '$flagtrans',
													'$log_approval_id'
												);";
						$qry_ins_antrian = @pg_query($sql_ins_antrian);
						if(@pg_affected_rows($qry_ins_antrian)){
							$jml_record++;
							//update m_jadwal
							if($tanda_jadwal_antrian=='ok'){
								if($flag_jadwal==''){
									$sql_upd_m_jadwal = "UPDATE m_jadwal SET flag_jadwal = TRUE WHERE jadwal_id = ".$jadwal_id."";
									$qry_upd_m_jadwal = pg_query($sql_upd_m_jadwal) or die('ERROR update m_jadwal: '.$sql_upd_m_jadwal);
								}
							}
							
							$sql_sel_antrian_id = "SELECT last_value as antrian_id FROM antrian_email_antrian_id_seq";
							$qry_sel_antrian_id = pg_query($sql_sel_antrian_id) or die('ERROR select antrian id: '.$sql_sel_antrian_id);
							$row_sel_antrian_id = pg_fetch_assoc($qry_sel_antrian_id);
							$antrian_id = $row_sel_antrian_id['antrian_id'];
							
							if($cid<>''){
								$sql_ins_attach_id = "INSERT INTO antrian_email_attach_file(antrian_id, m_attach_file_id)
														VALUES($antrian_id,".$cid.")";
								$qry_ins_attach_id = pg_query($sql_ins_attach_id) or die('ERROR insert into antrian_email_attach_file_embed: '.$sql_ins_attach_id);
							}
							
							
							/** INPUT FILE KINERJA **/
							/*
						if($blth_kinerja <> '' and  ($flagtrans =='D' )){
							$sql_k = "SELECT nama_produk FROM produk_kinerja WHERE blth='$blth' AND nomor_rekening='".$nomor_rekening[$i]."'";
							$exe_k = pg_query($sql_k);
							while($row_k = pg_fetch_array($exe_k)){
							$produk_kinerja = $row_k['nama_produk'];
								$sql_kinerja = "SELECT m_kinerja_id FROM m_kinerja WHERE blth='$blth' AND flagtrans = '$flagtrans' AND nama_produk='$produk_kinerja'";
								$exe_kinerja = pg_query($sql_kinerja);
								$n_kinerja = pg_num_rows($exe_kinerja);
								if($n_kinerja>0){
								$row_kinerja = pg_fetch_array($exe_kinerja);
								$m_kinerja_id = $row_kinerja['m_kinerja_id']; //m_kinerja_id
								$sql_ins_kinerja_id = "INSERT INTO antrian_email_kinerja_file(antrian_id, m_kinerja_id,ket_produk)
															VALUES('$antrian_id','$m_kinerja_id','$list_kinerja')";
								$qry_ins_kinerja_id = pg_query($sql_ins_kinerja_id) or die('ERROR insert into antrian_email_kinerja_file: '.$sql_ins_kinerja_id);
								}
							}
							}
							*/
							/** END INPUT FILE KINERJA **/
							
							
							
							if($tipe_attach == 1){
								for($c=0;$c<count($arr_attach_file_id);$c++){
									$sql_ins_attach_id = "INSERT INTO antrian_email_attach_file(antrian_id, m_attach_file_id)
															VALUES($antrian_id,".$arr_attach_file_id[$c].")";
									$qry_ins_attach_id = pg_query($sql_ins_attach_id) or die('ERROR insert into antrian_email_attach_file: '.$sql_ins_attach_id);
								}
							}elseif($tipe_attach == 2){
								for($c=0;$c<count($arr_attach_file_id);$c++){
									if($flag_attach[$i][$c] == '1'){
										$sql_ins_attach_id = "INSERT INTO antrian_email_attach_file(antrian_id, m_attach_file_id)
																VALUES($antrian_id,".$arr_attach_file_id[$c].")";
										$qry_ins_attach_id = pg_query($sql_ins_attach_id) or die('ERROR insert into antrian_email_attach_file: '.$sql_ins_attach_id);
									}
								}
							}
						}
					}else{
						if($no_rek_exists==""){
							$no_rek_exists = $nomor_rekening[$i];
						}else{
							$no_rek_exists .= ', '.$nomor_rekening[$i];
						}
					}
				}
			}
			update_log_approval_sk($log_approval_id, $jml_record, $m_loading_id, $rec_split, $jeda); 
		}
		
		// ************  20150306 Revisi ************ //
		$sql_view = "
		DROP VIEW $nama_view;";
		//ALTER TABLE $nama_view OWNER TO postgres;
		//die ( $sql_view );
		$qry_view = @pg_query($sql_view);
		// ************  20150306 Revisi ************ //
		
		echo '|'.$pr.'|'.$jml_record.'|'.$no_rek_exists;
	}
	
	function cek_m_jadwal($year_jadwal,$month_jadwal,$date_jadwal,$hour_jadwal,$min_jadwal,$jeda,$jadwalke,$flagtrans,$rec_split){
		$sched = date('Y-m-d H:i:00',mktime($hour_jadwal,$min_jadwal+($jeda*$jadwalke),0,$month_jadwal,$date_jadwal,$year_jadwal));
		$sql_cek = "
			SELECT * FROM m_jadwal WHERE tgl_jadwal = '$sched'
		";
		$qry_cek = pg_query($sql_cek);
		$jum_cek = pg_num_rows($qry_cek);
		
		if($jum_cek > 0){
			cek_m_jadwal($year_jadwal,$month_jadwal,$date_jadwal,$hour_jadwal,$min_jadwal,$jeda,$jadwalke+1,$flagtrans,$rec_split);
		}else{
			$sql_ins_jadwal = "
				INSERT INTO m_jadwal(
					tgl_jadwal,
					flagtrans,
					status,
					user_create,
					date_create,
					total
				)VALUES(
					'$sched',
					'$flagtrans',
					TRUE,
					'".$_SESSION['userid']."',
					now(),
					'$rec_split'
				)";
			$qry_ins_jadwal = @pg_query($sql_ins_jadwal) or die('ERROR insert m_jadwal: '.$sql_ins_jadwal);
		}
	}
	
	function template_email($flagtrans){
		$sql_template = "SELECT template_email_id,nama_template FROM template_email WHERE flagtrans='$flagtrans' and status ='t'";
		$qry_template = pg_query($sql_template) or die('ERROR select template: '.$sql_template);
		?>
        <select id="combo_template" name="combo_template" class="combobox" onchange="cekFile();template_name();">
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
	
	function template_name(){
		$template_id = $_REQUEST['template_id'];
		
		$sql_template = "SELECT template_email_id, nama_template FROM template_email WHERE template_email_id='$template_id'";
		$qry_template = @pg_query($sql_template);# or die('ERROR select template: '.$sql_template);
		if($qry_template){
			$row_template = pg_fetch_array($qry_template)
			?>
        <input type="hidden" id="combo_template_name" name="combo_template_name" value="<?=$row_template['nama_template']?>" />
			<?
		}
	}
	
	
	
	
	
	function kinerja_file(){
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['flagtrans'];
		
		$sql_cek_kinerja = "SELECT DISTINCT(blth)AS blth FROM m_kinerja WHERE flagtrans='$flagtrans'";
		$qry_cek_kinerja = @pg_query($sql_cek_kinerja);
		if($qry_cek_kinerja){
		?>
			<select name="kinerja_file1" id="kinerja_file1" class="combobox">
			<?php
			while($row_sel_attach=pg_fetch_array($qry_cek_kinerja)){
				?>
				<option value="<?=$row_sel_attach['blth']?>"><?=$row_sel_attach['blth']?></option>
				<?php
			}
			?>
			</select>
			<?php
		}else{
			echo "No DB";
		}
	}
	
	function get_cardno_search($checkbox_norek){
		$checkbox_norek = trim($checkbox_norek);
		$expl = explode(',',$checkbox_norek);
		$cardno = '';
		if(count($expl>1)){
			for($i=0;$i<count($expl);$i++){
				$cardno .= "'".trim($expl[$i])."', ";
			}
		}else{
			$cardno .= "'".$checkbox_norek."'";
		}
		
		return $cardno;
	}
	
	function get_file_cardno($path,$file){
		$cardno = '';
		//echo $path.$file."<br>";
		if(file_exists($path.$file)){
			$open_txt = fopen( $path.$file ,'r');
			$n=0;
			while(!feof($open_txt)){
				$row_txt = fgets($open_txt);
				$row_txt = trim($row_txt);
				if($row_txt!='') $cardno .= "'".$row_txt."', ";
			}
			fclose($open_txt);
		}else{
			die("UPLOAD GAGAL!!!!");
		}
		$cardno = rtrim($cardno,', ');
		if(trim($cardno)=='') $cardno = "''";
		$cardno = '('.$cardno.')';
		return $cardno;
	}
	
	function get_cardno_txt($cardno_manual){
		$cardno_tmp='';
		if(trim($cardno_manual)!=''){
			$cardno_manual = str_replace(';',',',trim($cardno_manual));
			$ex = explode(',',$cardno_manual);
			if($ex>1){
				for($i=0;$i<count($ex);$i++){
					$cardno_tmp .= "'".trim($ex[$i])."', ";
				}
			}else{
				$cardno_tmp .= "'".trim($cardno_manual)."'";
			}
			$cardno_tmp = rtrim($cardno_tmp,', ');
		}else{
			$cardno_tmp = "''";
		}
		$norek2 = '('.$cardno_tmp.')';
		
		return $norek2;
	}
	
	@pg_close($con);
?>