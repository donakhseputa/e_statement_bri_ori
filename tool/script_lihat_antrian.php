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
	}
	
	function blth(){
		$flagtrans	= $_REQUEST['flagtrans'];
		$sql_sel_blth = "SELECT b.blth
							FROM antrian_email a
								INNER JOIN m_loading b
									ON a.loading_id = b.m_loading_id
							WHERE b.flagtrans = '$flagtrans'
							GROUP BY b.blth
							ORDER BY substring(b.blth,3,4) desc, substring(b.blth,1,2) desc";
		$qry_sel_blth = pg_query($sql_sel_blth) or die('ERROR select blth: '.$sql_sel_blth);
		?>
        <select id="blth" name="blth" class="combobox" onchange="show_blth_rec();">
        	<option selected="selected" value=""></option>
        <?php
		while($row_sel_blth = pg_fetch_array($qry_sel_blth)){
			?>
            <option value="<?=$row_sel_blth['blth']?>"><?=$row_sel_blth['blth']?></option>
            <?php
		}
		?>
        </select>
        <?php
	}
	
	function nama_file(){
		$flagtrans	= $_REQUEST['flagtrans'];
		$blth	= $_REQUEST['blth'];
		$sql_sel_file = "SELECT b.loading_file
							FROM antrian_email a
								INNER JOIN m_loading b
									ON a.loading_id = b.m_loading_id
							WHERE b.blth = '$blth' and b.flagtrans = '$flagtrans'
							GROUP BY b.loading_file
							ORDER BY b.loading_file";
		$qry_sel_file = pg_query($sql_sel_file) or die('ERROR select blth: '.$sql_sel_file);
		?>
        <select id="nama_file" name="nama_file" class="combobox" onchange="show_nama_file_rec()">
        	<option selected="selected" value=""></option>
        <?php
		while($row_sel_file = pg_fetch_array($qry_sel_file)){
			?>
            <option value="<?=$row_sel_file['loading_file']?>"><?=$row_sel_file['loading_file']?></option>
            <?php
		}
		?>
        </select>
        <?php
	}
	
	function show_blth_rec(){
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['flagtrans'];
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
		$act = $_REQUEST['act'];
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['flagtrans'];
		$nama_file = $_REQUEST['nama_file'];
		$tipe_jadwal = $_REQUEST['tipe_jadwal'];
		$offset = $_REQUEST['offset'];
		$limit = $_REQUEST['jml_record'];
		//$limit = 50;
		$i = $offset;
		$c = 0;
		
		$sql_sel_antrian = "SELECT a.antrian_id, a.nama, a.email, a.nomor_rekening, a.pdf_name, a.pdf_location, a.jadwal_id, a.template_email_id, c.loading_file, 
									to_char(a.tgl_antrian,'dd Mon yyyy hh24:mi:ss') as tgl_antrian,
									to_char(d.tgl_jadwal,'dd Mon yyyy hh24:mi:ss') as tgl_jadwal,
									b.nama_template
								FROM antrian_email a
									INNER JOIN template_email b
										ON a.template_email_id = b.template_email_id
									INNER JOIN m_loading c
										ON a.loading_id = c.m_loading_id
									LEFT JOIN m_jadwal d
										ON a.jadwal_id = d.jadwal_id
								WHERE c.blth = '$blth' and c.flagtrans = '$flagtrans'";
		if($nama_file=='null' or $nama_file==''){
		}else{
			$sql_sel_antrian .= " and c.loading_file = '$nama_file'";
		}
		if($tipe_jadwal==2){
			$sql_sel_antrian .= " and a.jadwal_id is not null";
		}elseif($tipe_jadwal==3){
			$sql_sel_antrian .= " and a.jadwal_id is null";
		}
		$sql_sel_antrian .= "	ORDER BY a.jadwal_id, a.nomor_rekening";
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
        <table width="100%">
        	<tr>
            	<td colspan="2" align="center"><input type="button" id="button_back" name="button_back" class="button" value="BACK" onclick="show_filter()" /></td>
            </tr>
        	<tr>
            	<td colspan="2" align="center" style="color:RED">
                	<blink>
                    	Jika pengiriman menggunakan jadwal, pastikan Jadwal Kirim berada di antara pukul <?php echo $min_schedule?> - <?php echo $max_schedule?>
                        <br />
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
                    <td><input type="checkbox" id="check_all" name="check_all" onclick="checklist_all(this)" /></td>
                    <td>DEL</td>
                </tr>
            <?php
            while($row_show_antrian = pg_fetch_array($qry_show_antrian)){
                $i++;$c++;
                $antrian_id		= $row_show_antrian['antrian_id'];
                $nama			= $row_show_antrian['nama'];
				$email			= $row_show_antrian['email'];
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
                
                if($i%2==0){
                    $cls = 'table_row_odd';
                }else{
                    $cls = 'table_row_even';
                }
                ?>
                <tr class="<?=$cls?>" id="tr_<?=$c?>">
                    <td align="center"><?=$i?></td>
                    <td><?=$nama?></td>
                    <td><?=$email?></td>
                    <td><?=$nomor_rekening?></td>
                    <td>
                    
                    <? echo "<a href= '".$pdf_location. $pdf_name.  " ' >". $pdf_name ."</a>"; ?>
                    
                    </td>
                    <td><?=$nm_file?></td>
                    <td>
                    	<a onclick="view_template(<?=$antrian_id?>)"><?=$nama_template?></a>
						<?php
						$template_id
						?>
                    </td>
                    <td><?=$tgl_antrian?></td>
                    <?php
					if($tipe_jadwal==1 || $tipe_jadwal==2){
						echo '<td>'.$tgl_jadwal.'</td>';
					}
					?>
                    <td align="center"><input type="checkbox" id="cek_<?=$c?>" name="cek_[]" value="<?=$antrian_id?>" onclick="choose_me(<?=$c?>,<?=$antrian_id?>);" /></td>
                    <td align="center"><a onclick="delete_antrian(<?=$antrian_id?>)"><img src="../images/icons/del_data.png" /></a></td>
                </tr>
                <?php
            }
            ?>
                <tr>
                    <td align="center" colspan="11">
                        <?php
                            echo paging($offset,$jumlah_data,$limit,'div_show','script_lihat_antrian.php','act=show_antrian&blth='.$blth.'&flagtrans='.$flagtrans.'&nama_file='.$nama_file.'&jml_record='.$limit.'&tipe_jadwal='.$tipe_jadwal);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="11" align="center">
                        <input type="button" id="button_email" name="button_email" value="KIRIM EMAIL" class="button" onclick="kirim_email()" />&nbsp;
                        <input type="button" id="button_delete" name="button_delete" value="DELETE ANTRIAN" class="button" onclick="delete_all_antrian()" />
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
		?>
        <iframe id="frame_view" src="script_lihat_antrian.php?act=load_template&antrian_id=<?=$antrian_id?>" height="670" width="900" frameborder="0" style="background-color:#fff">
        </iframe>
        <?
	}
	
	function load_template(){
		$antrian_id = $_REQUEST['antrian_id'];
		
		$sql_attach = "SELECT a.m_attach_file_id, b.location_file, b.name_file, b.ukuran
						FROM antrian_email_attach_file a
							INNER JOIN m_attach_file b
								ON a.m_attach_file_id = b.m_attach_file_id
						WHERE a.antrian_id = ".$antrian_id;
		$qry_attach = pg_query($sql_attach) or die('ERROR select attach: '.$sql_attach);
		
		$sql_show = "SELECT a.nama, a.email, a.pdf_location, a.pdf_name, a.status_sample,
						b.from_name, b.reply_to_email, b.subject_email, b.isi_email, b.flagtrans,
						c.email_from,
						d.alamat1, d.alamat2, d.alamat3, d.blth, d.password_pdf
					FROM antrian_email a
						INNER JOIN template_email b
							ON a.template_email_id = b.template_email_id
						INNER JOIN mail_server c
							ON b.mail_server_id = c.mail_server_id
						INNER JOIN detail d
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
			if($status_sample=='t' || $status_sample=='TRUE'){
				$ganti = "<hr>kalimat ini hanya muncul untuk email sample saja.<p>password untuk dokumen ini adalah ".$password_pdf;
			}else{
				$ganti = "";
			}
			$isi_email = str_replace("@@#sample#@@",$ganti,$isi_email);
		$flagtrans = $row_show['flagtrans'];
		$pdf_location = $row_show['pdf_location'];
		$pdf_name = $row_show['pdf_name'];
		
		$detail['nama']		= $row_show['nama'];
		$detail['alamat1']	= $row_show['alamat1'];
		$detail['alamat2']	= $row_show['alamat2'];
		$detail['alamat3']	= $row_show['alamat3'];
		$blth				= $row_show['blth'];
			$periode = substr($blth,0,2).'-'.substr($blth,2,4);
			$periode = period($periode);
		$detail['periode']	= $periode;
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
                    	<a href="<?=$pdf_location.$pdf_name?>"><input type="button" class="button" value="<?=$pdf_name?>" /></a>
                    	<?php
						//echo "<a href='".$pdf_location.$pdf_name."'>".$pdf_name."</a>; ";
						?>
                    <!--<input type="text" id="text_pdf" name="text_pdf" class="textbox_4" value="<?=$pdf_name.';'?>" disabled="disabled" />-->
                    </td>
                </tr>
            	<tr>
                	<td>Attachment</td>
                    <td>
						<?php
                        while($row_attach = pg_fetch_array($qry_attach)){
                            $location_file = $row_attach['location_file'];
                            $name_file = $row_attach['name_file'];
                            $ukuran = $row_attach['ukuran'];
                            //echo "<a href='".$location_file.$name_file."'>".$name_file." ( ".$ukuran." Kb )</a>; ";
                        	echo '<a href="'.$location_file.$name_file.'"><input type="button" class="button" value="'.$name_file.' ( '.$ukuran.' Kb )" /></a>';
                        }
                        ?>
                    </td>
                    <!--<td><input type="text" id="text_attach" name="text_attach" class="textbox_4" value="<??>" disabled="disabled" /></td>-->
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
	
	pg_close($con);
?>