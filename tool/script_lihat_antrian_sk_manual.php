<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/message.php"); ?>
<? require_once("../include/function.php"); ?>
<?php
	$act		= $_REQUEST['act'];
	$con		= pg_connect($connection) or die("Could not connect to database!");
	
	switch($act){
		case 'blth': blth(); break;
		case 'show_blth_rec': show_blth_rec(); break;
		case 'show_nama_file': nama_file(); break;
		case 'show_nama_file_rec': show_nama_file_rec(); break;
		case 'show_antrian': show_antrian($min_schedule, $max_schedule); break;
		case 'delete_antrian': del_antrian(); break;
		case 'view_template': view_template(); break;
		case 'load_template': load_template(); break;
		case 'delete_all_antrian': delete_all_antrian();break;
		case 'approve_antrian': approve_antrian(); break;
		case 'unapprove_antrian': unapprove_antrian(); break;
		case 'open_form_check': open_form_check(); break;
		case 'form_check_qua': form_check_qua(); break;
		case 'SimpanPerubahan': SimpanPerubahan(); break;
		case 'open_form_reschedule': open_form_reschedule(); break;
		case 'form_reschedule': form_reschedule(); break;
		case 'SaveNewSchedule': SaveNewSchedule(); break;
	}
	
	function blth(){
		$flagtrans	= $_REQUEST['flagtrans'];
		$blth_home	= $_REQUEST['blth_home'];
		if(strlen($blth_home)==5) $blth_home = "0".$blth_home;
		//echo "NIH==>>".$blth_home;
		$sql_sel_blth = "SELECT b.blth
							FROM antrian_email a
								INNER JOIN m_loading b
									ON a.loading_id = b.m_loading_id
							WHERE b.flagtrans = '$flagtrans'
							GROUP BY b.blth
							ORDER BY substring(b.blth,3,4) desc, substring(b.blth,1,2) desc LIMIT 6";
		$qry_sel_blth = pg_query($sql_sel_blth) or die('ERROR select blth: '.$sql_sel_blth);
		?>
        <select id="blth" name="blth" class="combobox" onChange="show_blth_rec();">
        	<option selected="selected" value=""></option>
        <?php
		while($row_sel_blth = pg_fetch_array($qry_sel_blth)){
		if(strlen($blth_home)>0){
			if($row_sel_blth['blth']==$blth_home) $sel = "SELECTED"; else $sel = "";
		}else{
			$sel = "";
		}
			?>
            <option value="<?php echo $row_sel_blth['blth'];?>" <?php echo $sel;?>><?php echo $row_sel_blth['blth'];?></option>
            <?php
		}
		?>
        </select>
        <?php
	}
	
	function nama_file(){
		$flagtrans	= $_REQUEST['flagtrans'];
		$blth	= $_REQUEST['blth'];
		if(strlen($blth)==5) $blth = "0".$blth;
		
		$mloading_home	= $_REQUEST['mloading_home'];
		
		$sql_sel_file = "SELECT b.loading_file, b.m_loading_id
							FROM antrian_email a
								INNER JOIN m_loading b
									ON a.loading_id = b.m_loading_id
							WHERE b.blth = '$blth' and b.flagtrans = '$flagtrans'
							GROUP BY b.loading_file, b.m_loading_id
							ORDER BY b.loading_file";
		$qry_sel_file = pg_query($sql_sel_file) or die('ERROR select blth: '.$sql_sel_file);
		?>
        <select id="nama_file" name="nama_file" class="combobox" onChange="show_nama_file_rec()">
        	<option selected="selected" value=""></option>
        <?php
		while($row_sel_file = pg_fetch_array($qry_sel_file)){
		if(strlen($mloading_home)>0){
			if($row_sel_file['m_loading_id']==$mloading_home) $sel = "SELECTED"; else $sel = "";
		}else{
			$sel = "";
		}
			?>
            <option value="<?php echo $row_sel_file['loading_file']?>" <?php echo $sel;?>><?php echo $row_sel_file['loading_file']?></option>
            <?php
		}
		?>
        </select>
        <?php
	}
	
	function show_blth_rec(){
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['flagtrans'];
		if(strlen($blth)==5) $blth = "0".$blth;
		$sql_sel_blth_rec = "SELECT count(a.nomor_rekening) as jumlah
								FROM antrian_email a
									INNER JOIN m_loading b
										ON a.loading_id = b.m_loading_id
								WHERE b.blth = '$blth' and b.flagtrans = '$flagtrans'";
		$qry_sel_blth_rec = pg_query($sql_sel_blth_rec) or die($sql_sel_blth_rec);
		$row_sel_blth_rec = pg_fetch_assoc($qry_sel_blth_rec);
		$jml_blth_rec = $row_sel_blth_rec['jumlah'];
		?>
		<input type="text" name="blth_record" id="blth_record" class="textbox_1" disabled="disabled" value="<?=$jml_blth_rec?>" />
        <?
	}
	
	function show_nama_file_rec(){
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['flagtrans'];
		$nama_file = $_REQUEST['nama_file'];
		if(strlen($blth)==5) $blth = "0".$blth;
		$sql_sel_nama_file_rec = "SELECT count(a.nomor_rekening) as jumlah
									FROM antrian_email a
										INNER JOIN m_loading b
											ON a.loading_id = b.m_loading_id
									WHERE b.blth = '$blth' and b.flagtrans = '$flagtrans' and b.loading_file = '$nama_file'";
		$qry_sel_nama_file_rec = pg_query($sql_sel_nama_file_rec) or die($sql_sel_nama_file_rec);
		$row_sel_nama_file_rec = pg_fetch_assoc($qry_sel_nama_file_rec);
		$jml_nama_file_rec = $row_sel_nama_file_rec['jumlah'];
		?>
		<input type="text" name="nama_file_record" id="nama_file_record" class="textbox_1" disabled="disabled" value="<?=$jml_nama_file_rec?>" />
        <?
	}
	
	function show_antrian($min_schedule, $max_schedule){
		$menu_id = $_REQUEST['menu_id'];
		$akses = getAkses($menu_id);
		$act = $_REQUEST['act'];
		$blth = $_REQUEST['blth'];
		if(strlen($blth)==5) $blth = "0".$blth;
		$flagtrans = $_REQUEST['flagtrans'];
		$nama_file = $_REQUEST['nama_file'];
		$tipe_jadwal = $_REQUEST['tipe_jadwal'];
		$offset = $_REQUEST['offset'];
		$limit = $_REQUEST['jml_record'];
		//$limit = 50;
		$i = $offset;
		$c = 0;
		
		$BLTH_BARUS = BLTH_BARU;
		
		$sql_sel_antrian = "SELECT a.antrian_id, a.nama, a.email, a.nomor_rekening, a.pdf_name, a.pdf_location, a.jadwal_id, a.template_email_id, c.loading_file, c.flagtrans, c.blth,
									to_char(a.tgl_antrian,'dd Mon yyyy hh24:mi') as tgl_antrian,
									to_char(d.tgl_jadwal,'dd Mon yyyy hh24:mi') as tgl_jadwal,
									b.nama_template,
									e.log_approval_id, e.is_approval, e.approval_user, e.is_check, e.check_userid, 
									to_char(e.approval_date,'dd Mon yyyy hh24:mi') as approval_date,
									to_char(e.check_date,'dd Mon yyyy hh24:mi') as check_date
									,a.error_info
								FROM antrian_email a
									INNER JOIN template_email b
										ON a.template_email_id = b.template_email_id
									INNER JOIN m_loading c
										ON a.loading_id = c.m_loading_id
									LEFT JOIN m_jadwal d
										ON a.jadwal_id = d.jadwal_id
									LEFT JOIN log_approval e
										ON a.log_approval_id = e.log_approval_id
								WHERE c.blth = '$blth' and c.flagtrans = '$flagtrans' 
								
							"; //AND lower(a.email) like '%@ymail%' 
							//AND lower(a.nomor_rekening) like '%5520020200539607%' 
		if($nama_file=='null' or $nama_file==''){
		}else{
			$sql_sel_antrian .= " and c.loading_file = '$nama_file'";
		}
		if($tipe_jadwal==2){
			$sql_sel_antrian .= " and a.jadwal_id is not null";
		}elseif($tipe_jadwal==3){
			$sql_sel_antrian .= " and a.jadwal_id is null";
		}
		//$sql_sel_antrian .= "	ORDER BY a.jadwal_id, a.nomor_rekening";
		//$sql_sel_antrian .= "	ORDER BY a.antrian_id"; 		/** DIRUBAH TGL 17072013, ADA PROSES SORTING **/
		
		
		#$sql_sel_antrian .= " AND a.nomor_rekening IN ('4687400200180408', '5520020130194705', '5188560131039303', '4687400200020901', '3565101100622007','4687400180019006','5188285280045709','5188285270000003','4687400100029309','3565101200001607','5188285230004103','5188561370008108', '4687400100024805','4687400180016002')	"; 		/**20190318 JIKA ADA NOREK KHUSUS **/ //CYCLE 15
		
		#$sql_sel_antrian .= " AND a.nomor_rekening IN ('5188560300129901')	"; 		/**20190318 JIKA ADA NOREK KHUSUS **/ //CYCLE 10
		
		$sql_sel_antrian .= "	ORDER BY a.antrian_id DESC"; 		/** DIRUBAH TGL 09102014, ADA PROSES SORTING **/
		
		
		
		
		
		$sql_show_antrian = $sql_sel_antrian." LIMIT $limit OFFSET $offset";
		$qry_show_antrian = pg_query($sql_show_antrian) or die('ERROR show antrian: '.$sql_show_antrian);
		$qry_sel_antrian = pg_query($sql_sel_antrian) or die('ERROR select antrian: '.$sql_sel_antrian);
		$jumlah_data = pg_num_rows($qry_sel_antrian);
		
		$sql_dist_antrian = "SELECT a.nama, a.nomor_rekening, c.loading_file, b.nama_template
								FROM antrian_email a
									INNER JOIN template_email b
										ON a.template_email_id = b.template_email_id
									INNER JOIN m_loading c
											ON a.loading_id = c.m_loading_id
								WHERE c.blth = '$blth' and c.flagtrans = '$flagtrans'";
		if($nama_file=='null' or $nama_file==''){
		}else{
			$sql_dist_antrian .= " and c.loading_file = '$nama_file'";
		}
		$sql_dist_antrian .= "	GROUP BY a.nama, a.nomor_rekening, c.loading_file, b.nama_template";
		$qry_dist_antrian = pg_query($sql_dist_antrian) or die('ERROR distinct no_rek: '.$sql_dist_antrian);
		$jumlah_no_rek = pg_num_rows($qry_dist_antrian);
		?>
		<style>
/* Tooltip container */
.tooltip {
  position: relative;
  display: inline-block;
  border-bottom: 1px dotted black; /* If you want dots under the hoverable text */
}

/* Tooltip text */
.tooltip .tooltiptext {
  visibility: hidden;
  width: 500px;
  background-color: black;
  color: #fff;
  text-align: center;
  padding: 5px 0;
  border-radius: 6px;
 
  /* Position the tooltip text - see examples below! */
  position: absolute;
  z-index: 1;
}

/* Show the tooltip text when you mouse over the tooltip container */
.tooltip:hover .tooltiptext {
  visibility: visible;
}
</style>
        <table width="100%">
        	<tr>
            	<td colspan="2" align="center"><input type="button" id="button_back" name="button_back" class="button" value="BACK" onClick="show_filter()" /></td>
            </tr>
        	<tr>
            	<td colspan="2" align="center" style="color:RED">
                	<blink>
                        Periksa koneksi mail server sebelum melakukan pengiriman
                    </blink>
                </td>
            </tr>
        	<tr>
        <?php
			if($nama_file=='null' or $nama_file==''){
				?>
                <td>Nama File: ALL</td>
                <?php
			}else{
				?>
                <td>Nama File: <?=$nama_file?></td>
                <?php
			}
		?>
        		<td align="right">Jumlah Antrian: <?=$jumlah_data?></td>
            </tr>
            <tr>
            	<td>Periode: <?=$blth?></td>
        		<td align="right">Jumlah Customer: <?=$jumlah_no_rek?></td>
            </tr>
        </table>
        <form name="form_antrian" id="form_antrian">
            <table width="100%" align="center" cellpadding="1" cellspacing="2" border="0">
                <tr class="table_header">
                    <td width="5%">NO</td>
                    <td width="14%">NAMA</td>
                    <td width="13%">EMAIL</td>
                    <td width="12%">NOMOR REKENING</td>
                    <td width="12%">PDF</td>
                    <td width="11%">NAMA FILE</td>
                    <td width="11%">TEMPLATE</td>
                    <td width="11%">TGL ANTRIAN</td>
                    <?php
					if($tipe_jadwal==1 || $tipe_jadwal==2){
						echo '<td width="11%">JADWAL KIRIM</td>';
					}
					?>
                    <td align="center">CHECK</td>
                    <td align="center">APPROVAL<a onClick="page_info('<?php echo $menu_id?>', '2')" title="information"></a></td>
                    <td><input type="checkbox" id="check_all" name="check_all" onClick="checklist_all(this)" /></td>
                    <td>DEL</td>
                </tr>
            <?php
            while($row_show_antrian = pg_fetch_array($qry_show_antrian)){
                $i++;$c++;
                $antrian_id		= $row_show_antrian['antrian_id'];
                $nama			= $row_show_antrian['nama'];
				$email			= trim( $row_show_antrian['email'] );
					$email = str_replace('##','<br>',$email);
                $nomor_rekening	= $row_show_antrian['nomor_rekening'];
                $pdf_name		= $row_show_antrian['pdf_name'];
                $pdf_location	= $row_show_antrian['pdf_location'];
                $nm_file		= $row_show_antrian['loading_file'];
                $nama_template	= $row_show_antrian['nama_template'];
                $tgl_antrian	= $row_show_antrian['tgl_antrian'];
                $jadwal_id		= $row_show_antrian['jadwal_id'];
                $tgl_jadwal		= $row_show_antrian['tgl_jadwal'];
                $template_id	= $row_show_antrian['template_email_id'];
                $log_approval_id= $row_show_antrian['log_approval_id'];
                $is_approval	= $row_show_antrian['is_approval'];
                $is_check	= $row_show_antrian['is_check'];
                $approval_user	= $row_show_antrian['approval_user'];
                $check_userid	= $row_show_antrian['check_userid'];
                $approval_date	= $row_show_antrian['approval_date'];
                $check_date	= $row_show_antrian['check_date'];
                $flagtrans = $row_show_antrian['flagtrans'];
		  $blth = $row_show_antrian['blth'];
		  $error_info = $row_show_antrian['error_info'];
		  
				if($tipe_jadwal <> '3'){
						if(strlen($jadwal_id) > 0){
								if(date("Y-m-d H:i", strtotime($tgl_jadwal)) < date("Y-m-d H:i")){
									$proses_ulang = "Y";
								}else{
									$proses_ulang = "N";
								}
						}else{
							$proses_ulang = "N";
					}
				}
				
				
				if($proses_ulang=="N"){
					#$tgl_jadwal = $tgl_jadwal;
					$tgl_jadwal = '<span style="font-size:9px;font-weight:bold;">'.$tgl_jadwal . '</span><br/><a onclick="reschedule(\'' . $log_approval_id . '\')" style="color:#009f3c;font-weight:bold;text-decoration:blink;">Reschedule?</a>';
				}else{
					$tgl_jadwal = '<span style="font-size:9px;font-weight:bold;">'.$tgl_jadwal . '</span><br/><a onclick="reschedule(\'' . $log_approval_id . '\')" style="color:#C60323;font-weight:bold;text-decoration:blink;">Terlewat! <br/>Reschedule?</a>';
					#$tgl_jadwal = '<span style="font-size:9px;font-weight:bold;">'.$tgl_jadwal . '</span><br/>Terlewat! <br/>Pengiriman Otomatis Akan dijalankan';
				}

				if($is_check==1){
					if(strtoupper($check_userid) == strtoupper($_SESSION['user_id'])){
						$cek_qua = '<a onclick="check_qua(\'' . $log_approval_id . '\')"><img src="../images/yes.png" title="Last checked by ' . $check_userid . ' on ' . $check_date . '"></a>';
					}else{
						$cek_qua = $check_userid . "<br>" . $check_date;
					}
				}else{
					if(substr($akses, 0, 1) == "1"){
						$cek_qua = '<a onclick="check_qua(\'' . $log_approval_id . '\')"><img src="../images/delete.gif" title="Not Approve"></a>';
					}else{
						$cek_qua = "Check First!";
					}
				}
				
				
				if($is_approval == "t"){
					if(strtoupper($approval_user) == strtoupper($_SESSION['user_id'])){
						$apr = '<a onclick="unapprove(\'' . $log_approval_id . '\')">' . $approval_user . '<br>' . $approval_date . '</a>';
					}else{
						$apr = $approval_user . "<br>" . $approval_date;
					}
					$apr = '<a onclick="unapprove(\'' . $log_approval_id . '\')">' . $approval_user . '<br>' . $approval_date . '</a>';
				}else{
				if($is_check==1){
					if(substr($akses, 0, 1) == "1"){
						$apr = '<a onclick="approve(\'' . $log_approval_id . '\')">Not Approve</a>';
					}else{
						$apr = "Not Approve";
					}
					} else {
						$apr = "Check First!";
					}
				}
                
                if($i%2==0){
                    $cls = 'table_row_odd';
                }else{
                    $cls = 'table_row_even';
                }
                ?>
                <tr class="<?php echo $cls?>" id="tr_<?=$c?>">
                    <td align="center"><?php echo $i?></td>
                    <td align="left"><?php echo $nama?></td>
                    <td align="left"><?php echo $email?> 
					<?php
					//style="background-color:<?=  ( check_email_address($email) ) ? '#9dfba6' : '#fb9d9d' ;
					?>
					<?php  
					$error_info = trim($error_info);
					if ( empty($error_info) )
					{
						echo '<img valig="bottom" title="'.(( check_email_address($email) ) ? 'Valid' : 'Not Valid' ).' Email" src="../images/'.(( check_email_address($email) ) ? 'valid2.png' : 'not-valid2.png' ).'">';
					}else{
						echo '<img valig="bottom" title="'.(( empty($error_info) ) ? 'Valid' : 'Not Valid' ).' Email" src="../images/'.(( empty($error_info) ) ? 'valid2.png' : 'not-valid2.png' ).'">';
					}
					?>
					<div class="tooltip">(status)
					  <span class="tooltiptext"><?php echo $error_info;?></span>
					</div> 
					</td>
                    <td align="center"><?php echo $nomor_rekening?></td>
                    <td align="center">
                    <?php
					if(file_exists($pdf_location.$pdf_name)){ ?>
					<a href="<?php echo $pdf_location.$pdf_name;?>" target="_blank"><?php echo $pdf_name ;?></a>
					<?php }else{ ?>
					<span style="color:red"><b>FILE NOT FOUND</b></span>
                    <?php } ?>
                    </td>
                    <td align="center"><?php echo $nm_file?></td>
                    <td align="center">
                    	<a onClick="view_template('<?php echo $antrian_id?>','<?php echo $flagtrans;?>', '<?php echo $blth;?>')"><?php echo $nama_template?></a>
						<?php
						$template_id
						?>
                    </td>
                    <td><?php echo $tgl_antrian?></td>
                    <?php
					if($tipe_jadwal==1 || $tipe_jadwal==2){
						echo '<td>'.$tgl_jadwal.'</td>';
					}
					?>
                    <td align="center"><?php echo $cek_qua?></td>
                    <td align="center"><?php echo $apr?></td>
                    <td align="center"><input type="checkbox" id="cek_<?=$c?>" name="cek_[]" value="<?=$antrian_id?>" onClick="choose_me(<?=$c?>,<?=$antrian_id?>);" /></td>
                    <td align="center">
                    	<?php
						if(substr($akses, 2, 1) == "1"){
							?>
                            <a onClick="delete_antrian(<?php echo $antrian_id?>)"><img src="../images/icons/del_data.png" /></a>
                            <?php
						}
						?>
                    </td>
                </tr>
                <?php
            }
            ?>
                <tr>
                    <td align="center" colspan="11">
                        <?php
                            echo paging($offset,$jumlah_data,$limit,'div_show','script_lihat_antrian_sk.php','act=show_antrian&menu_id='.$menu_id.'&blth='.$blth.'&flagtrans='.$flagtrans.'&nama_file='.$nama_file.'&jml_record='.$limit.'&tipe_jadwal='.$tipe_jadwal);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="11" align="center">
                    	<?php
						if(substr($akses, 0, 4) == "1111"){
							?>
							<input type="button" id="button_email" name="button_email" value="KIRIM EMAIL" class="button" onClick="kirim_email()" />
							<?php
						}
						echo "&nbsp;";
						if(substr($akses, 2, 1) == "1"){
							?>
							<input type="button" id="button_delete" name="button_delete" value="DELETE ANTRIAN" class="button" onClick="delete_all_antrian()" />
							<?php
						}
						?>
                    </td>
                </tr>
                <input type="hidden" id="list_antrian" name="list_antrian" />
                <input type="hidden" id="tipe_jadwal" name="tipe_jadwal" value="<?php echo $tipe_jadwal ?>" />
            </table>
        </form>
        <?php
	}
	
	function del_antrian(){
		$antrian_id = $_REQUEST['antrian_id'];
		
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
									WHERE antrian_id = ".$antrian_id."";
		$qry_ins_antrian_his = pg_query($sql_ins_antrian_his) or die('ERROR insert antrian history: '.$sql_ins_antrian_his);
		
		$sql_del_antrian = "DELETE FROM antrian_email WHERE antrian_id = ".$antrian_id."";
		$qry_del_antrian = pg_query($sql_del_antrian)or die("Error delete antrian: $sql");
	}
	
	function delete_all_antrian(){
		$list_antrian = $_REQUEST['list'];
		$list_antrian = rtrim($list_antrian,',');
		
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
									WHERE antrian_id in (".$list_antrian.")";
									echo $sql_ins_antrian_his;
		$qry_ins_antrian_his = pg_query($sql_ins_antrian_his) or die('ERROR insert antrian history: '.$sql_ins_antrian_his);
		
		$sql_del_antrian = "DELETE FROM antrian_email WHERE antrian_id in (".$list_antrian.")";
		$qry_del_antrian = pg_query($sql_del_antrian)or die("Error delete antrian: $sql");
	}
	
	function view_template(){
		$antrian_id = $_REQUEST['antrian_id'];
		$flagtrans = $_REQUEST['flagtrans'];
		$blth = $_REQUEST['blth'];
		?>
        <iframe id="frame_view" src="script_lihat_antrian_sk.php?act=load_template&antrian_id=<?php echo $antrian_id?>&flagtrans=<?php echo $flagtrans;?>&blth=<?php echo $blth;?>" height="670" width="900" frameborder="0" style="background-color:#fff">
        </iframe>
        <?
	}
	
	function load_template(){
		$antrian_id = $_REQUEST['antrian_id'];
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['flagtrans'];
		#echo $blth."_".$flagtrans;
		
		$sql_attach = "SELECT a.m_attach_file_id, b.location_file, b.name_file, b.ukuran, b.cid
						FROM antrian_email_attach_file a
							INNER JOIN m_attach_file b
								ON a.m_attach_file_id::integer = b.m_attach_file_id::integer
						WHERE a.antrian_id = ".$antrian_id;
		$qry_attach = pg_query($sql_attach) or die('ERROR select attach: '.$sql_attach);
		
		
		$blth_balik = substr($blth,-4).substr($blth,0,2);
		
		$BLTH_BARUS = BLTH_BARU;
		
		if($blth_balik>$BLTH_BARUS){
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
		
		
		
		if ($flagtrans == 'PK')
		{
			$add_detail = ', d.tanggal ';
		}
		else
		{
			$add_detail = '';
		}
		
		
		$sql_show = "SELECT a.nama, a.email, a.pdf_location, a.pdf_name, a.status_sample,
						b.from_name, b.reply_to_email, b.subject_email, b.isi_email, b.flagtrans,
						c.email_from,
						d.alamat1, d.alamat2, d.alamat3, d.city, d.blth, d.password_pdf, d.nama_file, d.tipe_kartu, d.email AS emailsebenarnya, d.ket_produk $add_detail
					FROM antrian_email a
						INNER JOIN template_email b
							ON a.template_email_id = b.template_email_id
						INNER JOIN mail_server c
							ON b.mail_server_id = c.mail_server_id
						INNER JOIN $tabeldetail d
							ON a.loading_id = d.m_loading_id
								and a.nomor_rekening = d.nomor_rekening
					WHERE a.antrian_id = ".$antrian_id;
		$qry_show = pg_query($sql_show) or die('ERROR show: '.$sql_show);
		$row_show = pg_fetch_assoc($qry_show);
		
		$from_name = $row_show['from_name'];
		$email_from = $row_show['email_from'];
		$reply_to = $row_show['reply_to_email'];
		$nama_customer = $row_show['nama'];
		$email_customer = $row_show['email'];
		$subjek = $row_show['subject_email'];
		$status_sample = $row_show['status_sample'];
		$password_pdf = $row_show['password_pdf'];
		$isi_email = $row_show['isi_email'];
		$nama_file = $row_show['nama_file'];
		$emailsebenarnya	= $row_show['emailsebenarnya'];			/*email sebenarnya*/
		$blth = $row_show['blth'];
		$flagtrans = $row_show['flagtrans'];
		
		/*AMBIL NAMA PRODUK / UTK KETERANGAN SAMPLE*/
			$sql_flag = "SELECT produk FROM mproduk WHERE flagtrans='$flagtrans'";
			$exe_flag = @pg_query($sql_flag);
			$row_flag = pg_fetch_array($exe_flag);
			$keterangan_sample = "<br/>PRODUK: <b><font color='red'>" . $row_flag['produk'] . "</font></b>";
			
				if($status_sample=='t' || $status_sample=='TRUE'){
					$ganti = "<hr>kalimat ini hanya muncul untuk email sample saja.<p>password untuk dokumen ini adalah ".$password_pdf . "<br> Periode: " .$blth	. "<br> Cycle : " . $nama_file . "<br> Email Sebenarnya : <b>" . $emailsebenarnya .'<b>'. $keterangan_sample;
				}else{
					$ganti = "";
				}
				
			$isi_email = str_replace("@@#sample#@@",$ganti,$isi_email);
		$pdf_location = $row_show['pdf_location'];
		$pdf_name = $row_show['pdf_name'];
		
		$detail['nama']		= $row_show['nama'];
		$detail['alamat1']	= $row_show['alamat1'];
		$detail['alamat2']	= $row_show['alamat2'];
		$detail['alamat3']	= $row_show['alamat3'];
		$detail['city']	= $row_show['city'];
		$detail['tipe_kartu']	= $row_show['tipe_kartu'];
		$detail['ket_produk']	= $row_show['ket_produk'];
		
			$periode = substr($blth,0,2).'-'.substr($blth,2,4);
			$periode = period($periode);
		$detail['periode']	= $periode;
		
		
		if ( $flagtrans == "PK")
		{
			$detail['tipe_kartu_adhoc']		= $row_show['tanggal'];
		}
		
		
		//EMBED IMAGE
		$isi_template = $isi_email;
		$cek_embed = substr_count($isi_template,'src="cid:1');
		if($cek_embed>0){
			$cidpos = strpos($isi_template,'src="cid:1');
			$kodecid = substr($isi_template, $cidpos, 13);
			$kodecid = substr($kodecid, -4);
			$sql_cid = "SELECT location_file,name_file FROM m_attach_file WHERE cid='$kodecid'";
			$exe_cid = pg_query($sql_cid);
			$row_cid = pg_fetch_array($exe_cid);
			$location_file = $row_cid['location_file'] . $row_cid['name_file'];
			$isi_email = str_replace('<img src="cid:'.$kodecid.'" />',"<img src='$location_file'>",$isi_email);
		} 
		?>        
		<script type="text/javascript" src="../script/ajax.js"></script>
		<script type="text/javascript" src="../script/jquery.js"></script>
        <script type="text/javascript" src="../script/function.js"></script>
        <style>
			table{
				background-color:#fff;
				color: #000000;
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 12px;
				margin: 20px 0px 20px 0px;
				padding: 0px 0px 0px 0px;
			}

			.button {
				background-color: #C60323;
				border: #000000 1px solid;
				color: #FFFFFF;
				cursor: pointer;
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 12px;
				font-weight: bold;
				height: 30px;
				border-radius: 10px;
			}
			
			.textbox_3 {
				background-color: #CCFFCC;
				border: #666666 1px solid;
				color: #0000CC;
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 12px;
				width: 200px;
				border-radius: 5px;
			}
			
			.textbox_4 {
				background-color: #CCFFCC;
				border: #666666 1px solid;
				color: #0000CC;
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 12px;
				width: 400px;
				border-radius: 5px;
			}
		</style>
		<div id="div_body">
		<form id="form_editor" name="form_editor" method="post">
            <table align="center" width="100%">
            	<tr>
                	<td>From</td>
                    <td><input type="text" id="text_from" name="text_from" class="textbox_4" value="<?=$from_name.' < '.$email_from.' >'?>" disabled="disabled" /></td>
                </tr>
            	<tr>
                	<td>Reply To</td>
                    <td><input type="text" id="text_reply_to" name="text_reply_to" class="textbox_4" value="<?=$from_name.' < '.$reply_to.' >'?>" disabled="disabled" /></td>
                </tr>
            	<tr>
                	<td>To</td>
                    <td><input type="text" id="text_to" name="text_to" class="textbox_4" value="<?=$nama_customer.' < '.$email_customer.' >'?>" disabled="disabled" /></td>
                </tr>
            	<tr>
                	<td>Subject</td>
                    <td><input type="text" id="text_subjek" name="text_subjek" class="textbox_4" value="<?=replace_msg_email($flagtrans,$subjek,$detail)?>" disabled="disabled" /></td>
                </tr>
            	<tr>
                	<td>PDF File</td>
                    <td>
                    	<a href="<?php echo $pdf_location.$pdf_name?>"><input type="button" class="button" value="<?php echo $pdf_name?>" /></a>
                    	<?php
						?>
                    </td>
                </tr>
            	<tr>
                	<td>Attachment</td>
                    <td>
						<?php
                        while($row_attach = pg_fetch_array($qry_attach)){
							if($row_attach['cid'] == ""){
								$location_file = $row_attach['location_file'];
								$name_file = $row_attach['name_file'];
								$ukuran = $row_attach['ukuran'];
								echo '<a href="'.$location_file.$name_file.'"><input type="button" class="button" value="'.$name_file.' ( '.$ukuran.' Kb )" /></a>';
							}
						}
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>Content</td>
                    <td style="border-color:#000; border-width:1px; border:solid"><?=replace_msg_email($flagtrans,$isi_email,$detail)?></td>
                </tr>
            </table>
        </form>
        </div>
        <?
	}
	
	function approve_antrian(){
		$log_approval_id = $_REQUEST['log_approval_id'];
		
		$sql_apr = "
			UPDATE log_approval
			SET
				is_approval = 't',
				approval_date = now(),
				approval_user = '".$_SESSION['user_id']."'
			WHERE log_approval_id = '" . $log_approval_id . "'
		";
		$qry_apr = pg_query($sql_apr);
	}
	
	function unapprove_antrian(){
		$log_approval_id = $_REQUEST['log_approval_id'];
		
		$sql_apr = "
			UPDATE log_approval
			SET
				is_approval = 'f',
				approval_date = now(),
				approval_user = '".$_SESSION['user_id']."'
			WHERE log_approval_id = '" . $log_approval_id . "'
		";
		$qry_apr = pg_query($sql_apr);
	}
	
	
	function open_form_check(){
		$log_approval_id = $_REQUEST['log_approval_id'];
		?>
        <iframe id="frame_view" src="script_lihat_antrian_sk.php?act=form_check_qua&log_approval_id=<?php echo $log_approval_id?>" height="350" width="450" frameborder="0">
        </iframe>
        <?
	}
	
	
	
function form_check_qua(){
$log_approval_id = $_REQUEST['log_approval_id'];
$sql = "SELECT note_check,is_check,check_userid,check_date FROM log_approval WHERE log_approval_id='$log_approval_id'";
$exe = pg_query($sql);
$row = pg_fetch_array($exe);
if(strlen($row['check_userid'])>0){
	$keterangan = "<i>Terakhir dicek oleh ".$row['check_userid']." pada ".date("d-m-Y H:i", strtotime($row['check_date']))." WIB </i>";
}else{
	$keterangan = "";
}
?>
<script>
		function SimpanPerubahan(val){
			$.post('script_lihat_antrian_sk.php?act=SimpanPerubahan&log_approval_id='+val,$("#form_edit").serialize(),function(respon_edit){
				if(respon_edit=='sukses'){
					alert('Berhasil di-update');
					parent.location.reload(true);
					parent.jQuery.fancybox.close();
				}else{
					alert(respon_edit);
				}
			});
		}
</script>
<body>
<?php require_once("../include/script.php"); ?>
<div align='center'>
<h1>KONFIRMASI PENGECEKAN</h1>
<form id='form_edit' name='form_edit' method='post'>
<input type="hidden" name="log_approval_id" value="<?php echo $log_approval_id;?>">
<table border='0' cellspacing='2' cellpadding='2' style='border-collapse:collapse;' width='95%'>
<tr>
<td width='30%' align='center' valign="top"><b>Catatan<br/></b><i>(optional)</i></td>
<td width='5%' align='left' valign="top"><b>:</b></td>
<td width='65%' align='left'>
<textarea name="catatan" id="catatan" class="textbox_4"  cols="35" rows="4" style="width:90%"><?php echo $row['note_check'];?></textarea>
</td>
</tr>
<tr>
<td></td>
<td></td>
<td style="font-size:11px;"><?php echo $keterangan;?></td>
</tr>
<tr>
<td colspan="2"></td>
<td>
<?php 
if($row['is_check']==1){
$name_button = "BLOCK APPROVAL";
$status_cek = 0;
} else {
$name_button = "SIAP APPROVE";
$status_cek = 1;
}
?>
<input type="hidden" name="status_cek" value="<?php echo $status_cek;?>">
<input type="button" name="button_update" id="button_update" class="button" value="<?php echo $name_button;?>" onClick="SimpanPerubahan('<?php echo $log_approval_id;?>')">
</td>
</tr>
</table>
</form>
<br/>
<br/>
</body>
<?php }
	
	
function SimpanPerubahan(){
	$status_cek = $_POST['status_cek'];
	if($status_cek==0) $status_approval = ",is_approval='f'"; else $status_approval = "";
	$log_approval_id = $_POST['log_approval_id'];
	$catatan = $_POST['catatan'];
	$sql = "UPDATE log_approval SET is_check='$status_cek', note_check='".addslashes($catatan)."', 
	check_date=NOW(), check_userid='".$_SESSION['userid']."' $status_approval
	WHERE log_approval_id='$log_approval_id'";
	$exe = pg_query($sql);
	if($exe){
		echo "sukses";
	}else{
		echo "GAGAL MENYIMPAN STATUS PENGECEKAN...";
	}
}	
	
	
	
	
	
	
/** START NEW SCHEDULE **/
	
	function open_form_reschedule(){
		$log_approval_id = $_REQUEST['log_approval_id'];
		$flagtrans = $_REQUEST['flagtrans'];
		?>
        <iframe id="frame_view" src="script_lihat_antrian_sk.php?act=form_reschedule&flagtrans=<?php echo $flagtrans;?>&log_approval_id=<?php echo $log_approval_id?>" height="380" width="500" frameborder="0">
        </iframe>
        <?
	}
	
	
	
	
		
function form_reschedule(){
$log_approval_id = $_REQUEST['log_approval_id'];
$flagtrans = $_REQUEST['flagtrans'];
$sql = "SELECT note_check,is_check,check_userid,check_date,rec_split,jeda FROM log_approval WHERE log_approval_id='$log_approval_id'";
$exe = pg_query($sql);
$row = pg_fetch_array($exe);
if(strlen($row['check_userid'])>0){
	$keterangan = "<i>Terakhir dicek oleh ".$row['check_userid']." pada ".date("d-m-Y H:i", strtotime($row['check_date']))." WIB </i>";
	$rec_split = $row['rec_split'];
	$jeda = $row['jeda'];
}else{
	$keterangan = "";
	$rec_split = $row['rec_split'];
	$jeda = $row['jeda'];
}


$tgl_skrg = date("Y-m-d H:i:s");
//$sql_total = "SELECT COUNT(antrian_id)AS total FROM antrian_email WHERE log_approval_id='$log_approval_id' AND jadwal_id IN (SELECT jadwal_id FROM m_jadwal WHERE tgl_jadwal < '$tgl_skrg')";
$sql_total = "SELECT COUNT(antrian_id)AS total FROM antrian_email WHERE log_approval_id='$log_approval_id' AND jadwal_id IN (SELECT jadwal_id FROM m_jadwal)";
$exe_total = @pg_query($sql_total) or die("ERROR: " . $sql_total);
$row_total = pg_fetch_array($exe_total);
?>
<!-- this is link to layout style -->
<link type="text/css" rel="stylesheet" media="all" href="../script/style.css">
<link type="text/css" rel="stylesheet" media="all" href="../include/fancybox/jquery.fancybox-1.3.4.css">
<link type="text/css" rel="stylesheet" media="all" href="../script/themes/base/jquery.ui.all.css">
<!-- this is link to get XML http object -->
<script type="text/javascript" src="../script/ajax.js"></script>
<script type="text/javascript" src="../script/jquery.js"></script>
<script type="text/javascript" src="../script/function.js"></script>
<script type="text/javascript" src="../script/ui/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../script/ui/jquery.ui.core.js"></script>
<script type="text/javascript" src="../include/fancybox/jquery.fancybox-1.3.4.js"></script>
<script type="text/javascript" src="../script/vtips/vtip.js"></script>
<script>
		function SaveNewSchedule(val){
		if(confirm('Simpan Perubahan untuk Jadwal Baru ini?')){
			$.post('script_lihat_antrian_sk.php?act=SaveNewSchedule&log_approval_id='+val,$("#form_edit").serialize(),function(respon_edit){
				if(respon_edit=='sukses'){
					alert('Berhasil di-update');
					parent.location.reload(true);
					parent.jQuery.fancybox.close();
				}else{
					alert(respon_edit);
				}
			});
		} else {
			return false;
		}
		}
		
		$(function() {
		var dateToday = new Date();
			$("#tgl_jadwal").datepicker({
				defaultDate: "+0d",
				minDate: dateToday,
				dateFormat: 'yy-mm-dd'
			});
		});

</script>
<body>

<div align='center'>
<h1>PERUBAHAN JADWAL</h1>
<form id='form_edit' name='form_edit' method='post'>
<input type="hidden" name="log_approval_id" value="<?php echo $log_approval_id;?>">
<input type="hidden" name="flagtrans" value="<?php echo $flagtrans;?>">
<table border='0' cellspacing='2' cellpadding='2' style='border-collapse:collapse;' width='95%'>
<tr>
<td colspan="2" align='left' valign="top"><b>Jumlah Email </b><i>(Terlewat) </i></td><td width="60%" >: <font color="red"><b><?php echo $row_total['total'];?></b></font> <i>Email(s)</i></td>
</tr>
<tr>
<td colspan="2" align='left' valign="top"><b>Jeda Antar Jadwal</b></td><td width="60%" >: <font color="red"><b><?php echo $jeda;?></b></font> <i>Menit</i></td>
</tr>
<tr>
<td colspan="2" align='left' valign="top"><b>Record Per Jadwal</b></td><td width="60%" >: <font color="red"><b><?php echo $rec_split;?></b></font> <i>Record(s)</i></td>
</tr>
<tr><td colspan="3" height="30"></td></tr>

<tr><td height="20" colspan="3" align="left"><b>Penjadwalan Ulang</b></td></tr>
<tr><td height="10" colspan="3" align="left" style="font-size:12px;font-family:helvetica;"><i>Pengaturan ini berlaku untuk jadwal yang telah terlewat saja</i></td></tr>
<tr>
<td colspan="3">
<table align="center" width="100%">
<tr>
<td>
<input type="text" id="tgl_jadwal" name="tgl_jadwal" value="<?php echo date('Y-m-d') ?>" class="textbox_2" readonly>&nbsp;
<select id="hour_jadwal" name="hour_jadwal" class="combobox">
<?php
for ($i = 0; $i <= 23; $i++){
if(strlen($i)==1) $i='0'.$i;
?>
<option value="<?= $i ?>"<? if ($i == date("H")) { echo(" selected=\"selected\""); } ?>><?= $i ?></option>
<? } ?>
</select>&nbsp;:&nbsp;
<select id="min_jadwal" name="min_jadwal" class="combobox">
<? for ($i = 0; $i <= 59; $i++) { 
if(strlen($i)==1) $i='0'.$i;
?>
<option value="<?= $i ?>"<? if ($i == date("i")) { echo(" selected=\"selected\""); } ?>><?= $i ?></option>
<? } ?>
</select>
</td>
</tr>
</table>
</td>
</tr>
													
<tr>
<td></td>
<td></td>
<td style="font-size:11px;"></td>
</tr>
<tr>
<td colspan="3">
<input type="hidden" name="jeda" value="<?php echo $jeda;?>">
<input type="hidden" name="rec_split" value="<?php echo $rec_split;?>">
<input type="button" name="button_update" id="button_update" class="button" value="SIMPAN PERUBAHAN" onClick="SaveNewSchedule('<?php echo $log_approval_id;?>')">
</td>
</tr>
</table>
</form>
<br/>
<br/>
</body>
<?php }


function SaveNewSchedule(){
	/** variables **/
	$log_approval_id = $_POST['log_approval_id'];
	$jeda = $_POST['jeda'];
	$rec_split = $_POST['rec_split'];
	$flagtrans = $_POST['flagtrans'];
	
	/** new date **/
	$tgl_jadwal = $_POST['tgl_jadwal'];
	$year_jadwal = date('Y',strtotime($tgl_jadwal));
	$month_jadwal = date('m',strtotime($tgl_jadwal));
	$date_jadwal = date('d',strtotime($tgl_jadwal));
	$hour_jadwal = $_POST['hour_jadwal'];
	$min_jadwal = $_POST['min_jadwal'];
	
	/** select overlapped schedule(s) **/
	$tgl_skrg = date("Y-m-d H:i:s");
	//$sql_sel = "SELECT DISTINCT(jadwal_id)AS jadwal_id FROM antrian_email WHERE log_approval_id='$log_approval_id' AND jadwal_id IN (SELECT jadwal_id FROM m_jadwal WHERE tgl_jadwal < '$tgl_skrg') ";
	$sql_sel = "SELECT DISTINCT(jadwal_id)AS jadwal_id FROM antrian_email WHERE log_approval_id='$log_approval_id' AND jadwal_id IN (SELECT jadwal_id FROM m_jadwal) ";
	$exe_sel = @pg_query($sql_sel) or die("ERROR : " . $sql_sel);
	$jadwalke = 0;
	$sip = 1;
	
	while($row_sel = pg_fetch_array($exe_sel)){
		/** update new schedule(s) **/
		$jadwal_id = $row_sel['jadwal_id'];
		/** check schedule's slot **/
		$new_schedule = cek_new_schedule($jadwal_id,$year_jadwal,$month_jadwal,$date_jadwal,$hour_jadwal,$min_jadwal,$jeda,$jadwalke,$flagtrans,$rec_split);		
		
		$jadwalke++;
		
	}
	
	
	if($sip==1){
		echo "sukses";
	}else{
		echo "GAGAL MENYIMPAN JADWAL BARU...";
	}
}	
	

	
	
	
	function cek_new_schedule($jadwal_id,$year_jadwal,$month_jadwal,$date_jadwal,$hour_jadwal,$min_jadwal,$jeda,$jadwalke,$flagtrans,$rec_split){
		$sched = date('Y-m-d H:i:00',mktime($hour_jadwal,$min_jadwal+($jeda*$jadwalke),0,$month_jadwal,$date_jadwal,$year_jadwal));
		$sql_cek = "
			SELECT * FROM m_jadwal WHERE tgl_jadwal = '$sched'
		";
		$qry_cek = pg_query($sql_cek);
		$jum_cek = pg_num_rows($qry_cek);
		
		if($jum_cek > 0){
			cek_new_schedule($jadwal_id,$year_jadwal,$month_jadwal,$date_jadwal,$hour_jadwal,$min_jadwal,$jeda,$jadwalke+1,$flagtrans,$rec_split);
		}else{
			$sql_upd = "UPDATE m_jadwal SET flag_jadwal='1', status='1', tgl_jadwal = '$sched' WHERE jadwal_id='$jadwal_id'";
			$exe_upd = @pg_query($sql_upd) or die("ERROR UPDATE : " . $sql_upd);
			if($exe_upd) $sip = 1; else $sip = 0;
			return($sip);
		}
	}

	
	@pg_close($con);
?>