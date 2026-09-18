<?php require_once("../include/config.php"); ?>
<?php require_once("../include/session.php"); ?>
<?php require_once("../include/function.php"); ?>
<?php require_once("../tool/include_url.php"); ?>
<?php define('FPDF_FONTPATH','../include/fpdf/font/'); ?>
<?php require_once("../include/fpdf/fpdf.php"); ?>
<?php require_once("../include/fpdi/FPDI_Protection.php"); ?>
<?php
	
	/*require_once('../include/tcpdf/config/lang/eng.php');
	require_once('../include/tcpdf/tcpdf.php');*/
	require_once ('../include/tcpdf/tcpdf.php');
	require_once("../include/fpdi/fpdi.php");
	
	ini_set('memory_limit', '-1');
?>



	
	
    
<?php

/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../include/Classes/');
/** PHPExcel_IOFactory */
include 'PHPExcel/IOFactory.php';



/** LOADING FILE DENGAN MENGGUNAKAN NOMOR REKENING (DISAMAKAN DGN TABEL m_customer) **/
	$GLOBALS['dir_root'] = $www_location;
	#$GLOBALS['dir_root'] = 'D:/XAMP/htdocs/e_statement_cimb_niaga/';
	
	
	$pr = $_REQUEST['pr'];
	$arr_pr = explode('|',$pr);
	$flagtrans = $arr_pr[0];
	$menu_id = $arr_pr[1];
	$act = $arr_pr[2];
	$blth = $arr_pr[3];
	$file = $arr_pr[4];
	$filecust = $arr_pr[6];
	$par = $arr_pr[4];
	//die($pr);
	//echo($act);
	$con = pg_connect($connection) or die("Could not connect to database!");
	//$scon_group_nisp = "host=192.168.101.17 dbname=grouping_nispcard user=postgres password=edp321";
	$scon_group_nisp = "";
	$msg = "";
	$msg_not_exists = "";
	
	switch($act){
		case 'folder_loading':
				folder_loading($blth,$flagtrans,$par);
			break;
		case 'show_record':
				cycle_loading($blth,$flagtrans,$par);
			break;	
		case 'cycle_loading':
				//die($blth);
				cycle_loading($blth,$flagtrans,$par);
			break;	
		case 'excel_to_pdf':
				$waktu_awal = strtotime(date("Y-m-d H:i:s"));
				//die ( 'konversi mulai' );
				$sql_cek_dat = "SELECT * FROM m_loading WHERE flagtrans = '$flagtrans' and blth = '$blth' and loading_file = '$file'";
				#die($sql_cek_dat );
				$qry_cek_dat = pg_query($sql_cek_dat) or die('ERROR cek m_loading: '.$sql_cek_dat);
				$jml = pg_num_rows($qry_cek_dat);
				if($jml == 0){
					if ( strlen($file) > 50 )
					{
						$msg = 'ERROR: Nama file terlalu panjang, maksimal 50 Karakter';
					}else{
						$msg = excel_to_pdf($flagtrans,$menu_id,$blth,$file,$filecust,$con,$connection,$scon_group_nisp);
					}
					
				}else{
					//$msg = 'ERROR: file sudah pernah di-upload';
					die("FILE PERNAH DI PROSES SEBELUMNYA");
				}
				
				$waktu_akhir = strtotime(date("Y-m-d H:i:s"));
				$waktu = $waktu_akhir-$waktu_awal;
				
				echo $msg.'|'.$waktu.'|'.$flagtrans;
			break;
		case 'show_record':
				show_record($blth,$flagtrans);
			break;
	}
	
	function m_loading($flagtrans,$blth,$file){
		$sql_ins_m_loading = "INSERT INTO m_loading(
									userid,
									create_date,
									flagtrans,
									blth,
									loading_file
								)
								VALUES(
									'".$_SESSION['userid']."',
									now(),
									'$flagtrans',
									'$blth',
									'$file'
								)";
		$qry_ins_m_loading = pg_query($sql_ins_m_loading) or die('ERROR insert m_loading: '.$sql_ins_m_loading);
		
		$sql_sel_m_loading = "SELECT last_value FROM m_loading_loading_id_seq";
		$qry_sel_m_loading = pg_query($sql_sel_m_loading) or die('ERROR select m_loading: '.$sql_sel_m_loading);
		$row_sel_m_loading = pg_fetch_assoc($qry_sel_m_loading);
		$m_loading_id = $row_sel_m_loading['last_value'];
		
		
		
		
		#$sql2 = "TRUNCATE table tmp_excel_hana_bank";
		#$qry2 = @pg_query($sql2);
		
		
		return $m_loading_id;
	}
	
	
	function cycle_loading($blth,$flagtrans,$par)
	{
	
		$html='<select id="cycle_loading" name="cycle_loading" class="combobox" style="width: 500px;" onchange="cekFile()">';
          $html .='<option value="'.''.'" disabled selected>'.'--- Pilih File DAT Sumber ---'.'</option>';
		$tabeldetail = strtolower("detail_".$blth."_"."mt");
		$sql_baca = "SELECT * FROM m_loading
						WHERE blth='$blth' and flagtrans='MT' 
						AND ( lower(loading_file) like lower('%_CE_%')
						OR lower(loading_file) like lower('%_E_%')
						OR lower(loading_file) like lower('%_EST_%') )
						ORDER BY m_loading_id DESC";
						
		$qry = pg_query($sql_baca) or die('ERROR: '.$sql_baca);
		$x = 0;
		while($row = pg_fetch_array($qry)){
			$x++;
			$html .='<option value="'.strtoupper(strtolower($row['loading_file'])).'">'.$row['blth'].' | '.$row['loading_file'].' | '.$row['total_customer'].' cust</option>';
		}
		$html .='</select>'; 
		echo $html;
	}
	
	
	
	
	function folder_loading($blth,$flagtrans,$par){
		
		
		$row['folder'] = $GLOBALS['dir_root'].'EMBOSS_EMATERAI/_ALL_PDF/';
		$nfile=0;
		if($row){
			$folder=$row['folder'];
			if(is_dir($folder)){
				if ($dh = opendir($folder)) {
					echo "<table><tr valign='top' align='left'><td style='font-family:century gothic;font-size:10pt;'>";
					echo "<tr valign='top' align='left'><td style='font-family:century gothic;font-size:10pt;'>";
					echo "Alamat Direktori</td><td>:</td><td style='font-family:century gothic;font-size:10pt;font-weight:bold;'><i>".$folder."</i></td><tr valign='top'><td align='left' style='font-family:century gothic;font-size:10pt;'>File(s)**</td><td>:</td> ";
					$filesnm="";
					while (($file = readdir($dh)) !== false) {
						if(trim($filesnm)!='') $filesnm.="\n";
						if($file != "." && $file != ".." && $file != "..."){
							$filesnm.=($nfile+1). ' '.$file;$nfile++;
						} 
						
					}
					echo "<td><textarea id='isi_file' name='isi_file' cols='50' rows='30' readonly class='textarea_loading' style='height:60px;'>".$filesnm."</textarea><br/>
					TOTAL FILE : ($nfile)<br>
					<b><i><font color='brown'>**File yang tertera harus file berbentuk (<b>*.pdf</b>) <br><b>DENGAN PENAMAAN YANG SAMA MASING MASING NOMOR CIF</b>  yaitu:
					<br>- 3565101101477203_16042022.pdf
					<br>- 3565101207595809_16042022.pdf
					<br>- 5188560268673601_16042022.pdf
					<br>- dst.......
					
					</font></i></b></td></tr></table>";
				}else{
					echo $folder." tidak bisa dibuka (suggest: periksa hak akses folder pada administrator)";
				}
			}else{
				echo $folder." tidak ditemukan (suggest: periksa setting default folder)";
			}
		}else{
			echo "Direktori Default belum di set (suggest: setting default folder)";
		}
		echo "<input type='hidden' id='nfile' id='nfile' value='".$nfile."'>";
		
		
		if ($nfile == 0 ){
			?>
			<script>
			$("#a1").hide();
			$("#a2").hide();
			</script>
			<?php
		}
	}
	
	
	
	
	function insert_folder_to_db(){
		
		$sql		= "TRUNCATE TABLE master_proses_after_ematerai_file;";
		$query 		= pg_query($sql);
	
		$row['folder'] = $GLOBALS['dir_root'].'EMBOSS_EMATERAI/_ALL_PDF/';
		$nfile=0;
		if($row)
		{
			$folder=$row['folder'];
			if(is_dir($folder)){
				if ($dh = opendir($folder)) 
				{
					while (($file = readdir($dh)) !== false) 
					{
						if($file != "." && $file != ".." && $file != "...")
						{
							if(strtoupper(substr($file,-4))== ".PDF")
							{
								
								$sql		= "
								INSERT INTO master_proses_after_ematerai_file(
										 nama_file, direktori)
								VALUES ('".$file."', '".$row['folder']."');
								";
								$query 		= pg_query($sql);
							} 
						} 
					}
				}else{
					//echo $folder." tidak bisa dibuka (suggest: periksa hak akses folder pada administrator)";
				}
			}else{
				//echo $folder." tidak ditemukan (suggest: periksa setting default folder)";
			}
		}
	}
	
	function excel_to_pdf($flagtrans,$menu_id,$blth,$file,$filecust,$con,$connection,$scon_group_nisp)
	{
		#$azz = '_';
		$file_replace = str_replace('.DAT','',$file);
		$sql_cek_dat_azz = "SELECT * FROM m_loading WHERE lower(loading_file) like lower('%$file_replace%')
		and flagtrans='BC'";
		$qry_cek_dat_azz = pg_query($sql_cek_dat_azz) or die('ERROR cek m_loading: '.$sql_cek_dat_azz);
		$jml_azz = pg_num_rows($qry_cek_dat_azz);
		if($jml_azz > 0){
			$file = str_replace('.DAT','_P'.$jml_azz.'.DAT',$file);
		}else{
			$file = str_replace('.DAT','_P0.DAT',$file);
		}
		
		
		if (empty($file)) die("FILEE KOSONG TIDAK TERBACA");
		$m_loading_id = m_loading($flagtrans,$blth,$file);
		insert_folder_to_db();
		#die("AAA");
		$tabeldetail = strtolower("detail_".$blth."_"."mt");
		$file_replace = str_replace(substr( $file,-7),'',$file);
		$sql_del = "UPDATE master_proses_after_ematerai set keterangan='ready', waktu=now();
		
		UPDATE $tabeldetail 
		set no_rek_asli = NULL 
	WHERE lower(nama_file) like lower('%$file_replace%')
						AND no_rek_asli ='tidak ada'
						";
		$qry_del = pg_query($sql_del) or die('ERROR: '.$qry_del);
		
		
		update_urutan_no_pdf_ematerai(0);
		
		return 'Convert Berhasil|'.$n_total_cust.'|0';
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/*
	
	function convDate($tgl,$pemisah){
		//format $tgl harus dd-Mmm-yy agar menjadi ddMonyyyy
		if (substr($tgl,0,3) != '0000' and substr($tgl,0,3) !='' )
		{
	//	$arrMon=array('01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember');
		
		$arrTgl=explode($pemisah,$tgl);
		$arrMon=array('Jan'=>'Jan','Feb'=>'Feb','Mar'=>'Mar','Apr'=>'Apr','May'=>'Mei','Jun'=>'Jun','Jul'=>'Jul','Aug'=>'Agu','Sep'=>'Sep','Oct'=>'Okt','Nov'=>'Nov','Dec'=>'Des');
		
		$monNm=$arrMon[$arrTgl[1]];
		return str_replace(',','',$arrTgl[0]).''.$monNm.'19'.$arrTgl[2];
		
		}
		elseif (strlen($monNm) != 3)
		{
			return ('-');
		}
		
		else
		{
			return ('-');
		}
		
		
	}
	
	
	
	function ubah_format_tanggal($tgl)
	{
		$tgl=str_replace('-','/',$tgl);
		$tmp_tgl=explode('/',$tgl);
		
		//tambahan 20151002
		if (strlen( $tmp_tgl[1] ) == 1) {
			$tmp_tgl[1]='0'.$tmp_tgl[1];
		}
		if (strlen( $tmp_tgl[0] ) == 1) {
			$tmp_tgl[0]='0'.$tmp_tgl[0];
		}
		//tambahan 20151002
		
		$arr=array('01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Aug','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec');
		
		$dd		= $tmp_tgl[1];
		//$MM		= $arr[ $tmp_tgl[0] ];
		$MM		= $tmp_tgl[0];
		//$yy		= ( strlen($tmp_tgl[2]) == 4 ) ? substr($tmp_tgl[2],2,2) : '20'.$tmp_tgl[2]; //1234
		$yy		= ( strlen($tmp_tgl[2]) == 4 ) ? $tmp_tgl[2] : '20'.$tmp_tgl[2]; //1234
		
		return $dd.'/'.$MM.'/'.$yy;
	}
	
	function m_loading($flagtrans,$blth,$file){
		$file = str_replace(".ccstmncbs",".CCSTMNCBS", $file);
		$sql_ins_m_loading = "INSERT INTO m_loading(
									userid,
									create_date,
									flagtrans,
									blth,
									loading_file
								)
								VALUES(
									'".$_SESSION['userid']."',
									now(),
									'$flagtrans',
									'$blth',
									'$file'
								)";
		$qry_ins_m_loading = pg_query($sql_ins_m_loading) or die('ERROR insert m_loading: '.$sql_ins_m_loading);
		
		$sql_sel_m_loading = "SELECT last_value FROM m_loading_m_loading_id_seq";
		$qry_sel_m_loading = pg_query($sql_sel_m_loading) or die('ERROR select m_loading: '.$sql_sel_m_loading);
		$row_sel_m_loading = pg_fetch_assoc($qry_sel_m_loading);
		$m_loading_id = $row_sel_m_loading['last_value'];
		
		return $m_loading_id;
	}
	
	function log_customer($flagtrans,$blth,$file){
		$sql_sel_log_customer = "SELECT log_customer_id FROM log_customer WHERE blth = '$blth' AND flagtrans = '$flagtrans' ORDER BY log_customer_id DESC LIMIT 1";
		$qry_sel_log_customer = pg_query($sql_sel_log_customer) or die('ERROR select log_customer: '.$sql_sel_log_customer);
		$row_sel_log_customer = pg_fetch_array($qry_sel_log_customer);
		$log_customer_id = $row_sel_log_customer['log_customer_id'];
		
		return $log_customer_id;
	}
	
	
	function excel_to_pdf($flagtrans,$menu_id,$blth,$file,$filecust,$con,$connection,$scon_group_nisp)
	{
		
		//create tabel
		
		$m_loading_id = m_loading($flagtrans,$blth,$file);
		
		
				
				
				$blth_balik = substr($blth,-4).substr($blth,0,2);
			
				$BLTH_BARUS = BLTH_BARU;
				if($blth_balik>$BLTH_BARUS){
				$tabeldetail = strtolower("detail_".$blth."_".$flagtrans);
				$tabeldetail_a1 = $tabeldetail."_p_k";
				$tabeldetail_un = $tabeldetail."_unique";
				$tabelindex = $tabeldetail."_index";
				
				$cek_tabel = "SELECT detail_id FROM $tabeldetail";
				$exe_tabel = @pg_query($cek_tabel);
				if(!$exe_tabel){
					
				$buat_tabel = "CREATE TABLE $tabeldetail
								(						
								 detail_id serial NOT NULL,
								  m_loading_id integer,
								  nomor_customer character varying(20),
								  nomor_rekening character varying(20),
								  nama character varying(60),
								  alamat1 character varying(200),
								  alamat2 character varying(200),
								  alamat3 character varying(200),
								  alamat4 character varying(200),
								  alamat5 character varying(200),
								  city character varying(200),
								  zipcode character varying(9),
								  flagtrans character varying(2),
								  blth character varying(6),
								  nama_file character varying(50),
								  pdf_name character varying(100),
								  password_pdf character varying(25),
								  jml_hlm integer,
								  flag_attach character varying(3),
								  tipe_kartu character varying(40),
								  no_rek_asli character varying(20),
								  ket_produk character varying(60),
								  email text,
								  n_email integer DEFAULT 1,
								  size_pdf numeric(7,2),
								  barcode text,
								  nama_produk text,
								  tanggal text,
								  total_produk text,
								  kode_cab text,
								  cabang text,
								  CONSTRAINT $tabeldetail_a1 PRIMARY KEY (detail_id),
								  CONSTRAINT $tabeldetail_un UNIQUE (nomor_rekening, nama_file, blth)
								) 
								WITHOUT OIDS;
								ALTER TABLE $tabeldetail OWNER TO postgres;";
				$exe_tabel = pg_query($buat_tabel)or die("ERROR: " . $buat_tabel);
					//indexing
					//$sql_ind = "CREATE INDEX $tabelindex ON $tabeldetail  USING btree (blth, flagtrans, nama_file,nomor_rekening);";
					$sql_ind = "CREATE INDEX $tabelindex ON $tabeldetail  USING btree (nomor_rekening, m_loading_id);";
					$exe_ind = pg_query($sql_ind)or die("ERROR: " . $sql_ind);
				}
				}else{
					$tabeldetail = "detail";
				}
		
		
		//selesai create tabel
		

		
		//$master_header = "CYCLE_DATE;POLICY_NUMBER;ISSUE_DATE;OWNER_NAME;DIST_CHNL_CODE;BRANCH_CODE;BRANCH_NAME;HNW;POL_RMRK;EMAIL;EPOL_STAT;DOB";
		$master_header = "POLICY_NO*TTPD_NUM*SEND_DATE*TYPE*TRXN_TYPE*OWNER_NAME*ADDRESS_1*ADDRESS_2*ADDRESS_3*ADDRESS_4*ZIP_CODE*CODE_RESI*TGL_PICK_UP*TGL_KIRIM*TGL_STATUS*STATUS*PENERIMA*KETERANGAN*SENT_VIA*SLA*DIST_CHANNEL_DESC*ASSIGN_USER_ID*EMAIL_ADDRESS*FLAG_ESTATEMENT*FLAG_ESTAT_EFF_DT*ESTATEMENT_IND*BIRTH_DT*SEX_CODE*";
		
		//$master_header = "CYCLE_DATE;POL_NUM;ISSUE_DATE;OWNER_NAME;DIST_CHNL_CODE;BRANCH_CODE;BRANCH_NAME;HNW;POL_REMARK;EMAIL;EPOL_STAT;DOB";
		
		
		
		
		#die("TEST");
		
	
	
	
	
	
		$dirfile = '../temp_file/billing/'.$flagtrans.'/'.$blth.'/';
		$inputFileName = $dirfile.'/'.$file;  
			$no_urut = 0;
			try {
				$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
			} catch(Exception $e) {
				die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
			}
			
			
			
			$n_cust=0;
			$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
			//var_dump($sheetData);
			
			//$no_urut = 0;
			$kolom='';
			foreach($sheetData as $row => $columns) 
			{
				$no_urut++;  
				
				if($row != 1) 
				{
					$value = '';
					
					foreach($columns as $cell => $val) 
					{
						
						if ($cell == 'AC' || $cell == 'AD' || $cell == 'AE') continue;
						
						
						$value[] = addslashes(str_replace( array('(',')'), '' , $val) ) ;
						
						
					}
			
					$values = "('" . implode("',E'",$value) . "')";
					$values = str_replace(")",", '".$blth."', '".$flagtrans."', '".$m_loading_id."', '".$file."')",$values);
					$query = ("INSERT INTO $temp (
						$kolom_insert 
						blth, flagtrans, m_loading_id, nama_file
					) VALUES " . $values) or die(pg_last_error());
				
					$qry_ins_cust = pg_query($query) or die("<br>GAGAL INSERT INTO $temp<br>".$query);
				//	echo $query .'<br>';
					
					$n_cust++;
				}else{
					//nama kolom
					foreach($columns as $cell => $val) 
					{
						if ($cell == 'AC' || $cell == 'AD' || $cell == 'AE') continue;
						$kolom .= $val.'*';
						
					}
					
					//CEK PERBEDAAN HEADER
					$kolom=str_replace('-','',$kolom);
					if(strtoupper($kolom) != strtoupper($master_header) )
					{
						$sql_del = "DELETE FROM m_loading WHERE loading_file = '$file' ";
						$qry_del = pg_query($sql_del) or die("<br>GAGAL DELETE m_loading<br>".$sql_del);
						die("HEADER EXCEL SALAH<br><br>didata<br>$kolom<br><br>seharusnya<br>$master_header<br>");
					}else{
							$kolom_insert = '';
							
											$temp = 'temp_csv_'.$flagtrans;
											$drop = 'DROP TABLE IF EXISTS '.$temp ;
											pg_query ($drop);
											$buat_tabel_temp = 
												" CREATE TABLE $temp (
													id SERIAL,
												";
												
											$nama_tabel_x = explode('*',$kolom);
											
											for ($i = 0; $i < count($nama_tabel_x); $i++) {
												if (empty($nama_tabel_x[$i])) continue;
												$nama_tabel_x[$i] = str_replace( array(' ','-'), '' , $nama_tabel_x[$i]);
												
												$buat_tabel_temp .= $nama_tabel_x[$i]." text," ;
												$kolom_insert .= $nama_tabel_x[$i]."," ;
											}
											
											
												$buat_tabel_temp .= " status_a1 text, blth text, flagtrans text,
													m_loading_id text,
													nama_file text
												)
												";
											//die("$buat_tabel_temp");
											$exe_tabel = pg_query($buat_tabel_temp)or die("ERROR: " . $buat_tabel_temp);
					
					}
				}
				
			}
	//die("INSERT EXCEL ");
	
	$temp = 'temp_csv_'.$flagtrans.'_status';
	$drop = 'DROP TABLE IF EXISTS '.$temp ;
	pg_query ($drop);
	$buat_tabel_temp = " CREATE TABLE $temp (
					id SERIAL, nomor_polis text, ";
	$buat_tabel_temp .= " status_cover text, status_polis_page3 text, status_ketentuan text, status_spaj text, status_combine text, 
	m_loading_id text)";
											//die("$buat_tabel_temp");
	$exe_tabel = pg_query($buat_tabel_temp)or die("ERROR: " . $buat_tabel_temp);
												
	remove_duplikat_polis();									
	insert_folder_to_db();
	
	$sql_del = "UPDATE master_proses_after_ematerai set keterangan='ready', waktu=now();";
	$qry_del = pg_query($sql_del) or die('ERROR: '.$qry_del);
		
		return 'Convert Berhasil|'.$n_total_cust.'|0';
	}
	
	function fungsi_nama_file($tanggal_nama_file)
	{
		//20170208
		
		$x= substr($tanggal_nama_file,6,2).substr($tanggal_nama_file,4,2).substr($tanggal_nama_file,2,2);
		return $x;
	}
	
	
	

	
	
	//FUNGSI KONVERSI BULAN
		function konversi_bulan($bulan){
		switch($bulan){
		case "01":
		$namabulan = "Januari";
		break;
		case "02":
		$namabulan = "Februari";
		break;
		case "03":
		$namabulan = "Maret";
		break;
		case "04":
		$namabulan = "April";
		break;
		case "05":
		$namabulan = "Mei";
		break;
		case "06":
		$namabulan = "Juni";
		break;
		case "07":
		$namabulan = "Juli";
		break;
		case "08":
		$namabulan = "Agustus";
		break;
		case "09":
		$namabulan = "September";
		break;
		case "10":
		$namabulan = "Oktober";
		break;
		case "11":
		$namabulan = "November";
		break;
		case "12":
		$namabulan = "Desember";
		break;
		}
		return($namabulan);
		}
		
	function output_pdf_2($pdf,$nama_file_pdf,$blth,$file,$flagtrans){
		$pdfLoc='../pdf/'.$flagtrans.'/';
		if(!file_exists($pdfLoc)) mkdir($pdfLoc)or die('Error mkdir '.$pdfLoc);
		$pdfLoc.=$blth.'/';
		if(!file_exists($pdfLoc)) mkdir($pdfLoc)or die('Error mkdir '.$pdfLoc);
		//$file = str_replace(substr($file,-4),'',$file);
		$data = explode('.',$file);
		$file = $data[0];
		$pdfLoc.=$file.'/';
		if(!file_exists($pdfLoc)) mkdir($pdfLoc)or die('Error mkdir '.$pdfLoc);
		$pdf_output = $pdfLoc.$nama_file_pdf.'.pdf';
		if(file_exists($pdf_output)){
			unlink($pdf_output);
		}
		$pdf->Output($pdf_output,'F');
		$filesizePdf = fsize($pdf_output);
		return $filesizePdf;
	}
	
	
	      
	

	
	function insert_detail($m_loading_id,$nomor_customer,$nomor_rekening,
								$nama,$address1,$address2,
								$address3,$city,$zipcode,
								$flagtrans,$blth,$nama_file,
								$pdf_name,$password_pdf,$jml_hlm,$email,$filesizePdf,$totalemailcustomernya,$tabeldetail){
		//KHUSUS BULAN MARET 2014, TIDAK DIBERIKAN TAMBAHAN KETERANGAN LIQUID ATAU SOLID
		//if(substr($nomor_rekening,0,4)=="4645") $tipe_kartunya = "PLATINUM"; else $tipe_kartunya = "TITANIUM";
		$tipe_kartunya='';		
		$n_email = substr_count($email,";");
		$n_email = $totalemailcustomernya;
		$nama_file = str_replace(".ccstmncbs",".CCSTMNCBS", $nama_file);
		
		
				
		$sql_ins_detail = "INSERT INTO $tabeldetail(
								m_loading_id,nomor_customer,nomor_rekening,
								nama,alamat1,alamat2,
								alamat3,city,zipcode,
								flagtrans,blth,nama_file,
								pdf_name,password_pdf,jml_hlm,tipe_kartu, email, n_email, size_pdf
							)VALUES(
								$m_loading_id,'$nomor_customer','$nomor_rekening',
								E'".addslashes($nama)."',E'".addslashes($address1)."',E'".addslashes($address2)."',
								E'".addslashes($address3)."','$city','$zipcode',
								'$flagtrans','$blth','$nama_file',
								'$pdf_name','$password_pdf',$jml_hlm, '$tipe_kartunya',E'".addslashes($email)."','$n_email','$filesizePdf'
							)";
		$qry_ins_detail = pg_query($sql_ins_detail) or die('ERROR insert into detail: '.$sql_ins_detail);
		if(pg_affected_rows($qry_ins_detail)==0){
			echo 'GAGAL INSERT DETAIL UNTUK $nomor_rekening';
			die();
		}
	}
	
	
	function update_m_loading($m_loading_id,$total_hlm,$customer){
		$sql_upd_m_load = "UPDATE m_loading
							SET
								total_halaman = $total_hlm,
								total_customer = $customer
							WHERE m_loading_id = $m_loading_id";
		$qry_upd_m_load = pg_query($sql_upd_m_load) or die('ERROR update m_loading: '.$sql_upd_m_load);
	}
	
	function update_log_customer($log_customer_id,$customer){
		$sql_upd_log_customer = "UPDATE log_customer
								SET
									total_customer = '$customer'
								WHERE log_customer_id = '$log_customer_id'";
		$qry_upd_log_customer = pg_query($sql_upd_log_customer) or die('ERROR update log_customer: '.$sql_upd_log_customer);
	}
	
	function rectfill($pdf,$fillcolor,$drawcolor,$x_awal,$y_awal,$lebar,$tinggi,$flag){
	
		$pdf->SetFillColor($fillcolor);
		$pdf->SetDrawColor($drawcolor);
		$pdf->Rect($x_awal,$y_awal,$lebar,$tinggi,$flag);
	}
	
	
	function image_header($pdf,$card_numb,$x_image,$y_image){
		$card_subs = '';
		$card_show = '';
		$url_img = '';
		
		$url_img = '../images/Logo-ocbc-nisp.jpg';
		
		$pdf->Image($url_img, 8, 2, 50, 10, 'JPG', '', '', false, 300, '', false, false, 0, false, false, false);						
	}	
	

	
	function create_csv($m_loading_id,$blth,$file,$flagtrans,$sql_cek,$tabeldetail){
		//$csv = str_replace(substr($file,-10),'',$file);
		$data = explode('.',$file);
		$file = $data[0];
		$csv = $file;
		
		$file_csv = '../pdf/'.$flagtrans.'/'.$blth.'/'.$csv.'/'.$csv.'.csv';
		$header = "'no_ref';'nama';'nama_pdf';'password_pdf';".chr(13);
		$detail = "";
		
		$sql = "SELECT nomor_rekening,nama,pdf_name,password_pdf FROM $tabeldetail WHERE m_loading_id = $m_loading_id";
		$qry = pg_query($sql) or die('ERROR: '.$sql);
		while($row = pg_fetch_array($qry)){
			$nomor_rekening = $row['nomor_rekening'];
			$nama = $row['nama'];
			$pdf_name = $row['pdf_name'].'.pdf';
			#$password_pdf = $row['password_pdf']; //Pada 03062013 diganti menggunakan 6 digit terakhir nomor kartu kredit
			$password_pdf = $row['password_pdf'];
			
			$detail .= "'".$nomor_rekening."';'".$nama."';'".$pdf_name."';'".$password_pdf."';'".chr(13);
		}
		
		$open_file = fopen($file_csv,'w');
		fwrite($open_file,$header.$detail);
		fclose($open_file);
	}
	
	function show_record($blth,$flagtrans){
		$sql_jmlrecord = "SELECT count(*) as jumlah
							FROM m_customer
							WHERE blth = '$blth' and flagtrans = '$flagtrans'";
		$qry_jmlrecord = pg_query($sql_jmlrecord) or die('ERROR');
		$row_jmlrecord = pg_fetch_assoc($qry_jmlrecord);
		echo number_format($row_jmlrecord['jumlah'],0,'','.');
	}
	
	
	function copy_temp_to_all()
	{
		$sql = "
		INSERT INTO temp_excel_all_saved(
			no ,
					no_ticket_2 ,
					card_number ,
					nama ,
					tgl_close_card ,
					tgl_lunas_bayar ,
					no_surat ,
					tgl_surat ,
					alamat_1 ,
					alamat_2 ,
					alamat_3 ,
					alamat_4 ,
					alamat_5 ,
					cr_addr_email ,
			m_loading_id, nama_file) 
		SELECT no ,
					no_ticket_2 ,
					card_number ,
					nama ,
					tgl_close_card ,
					tgl_lunas_bayar ,
					no_surat ,
					tgl_surat ,
					alamat_1 ,
					alamat_2 ,
					alamat_3 ,
					alamat_4 ,
					alamat_5 ,
					cr_addr_email ,
			   m_loading_id, nama_file
		  FROM temp_excel_a1;
		";
		$qry = pg_query($sql) or die('ERROR: '.$sql);
	}
	
	function buat_csv_noexist($nama_file,$isi, $f)
	{
			$folder_dest="../tmp/noexist/";
			if(!file_exists($folder_dest)) mkdir($folder_dest)or die('Error mkdir '.$folder_dest);
		
			$csv = $f."_" . $nama_file;
			$file_csv = $folder_dest.$csv.'.txt';
			$header = "ACCT_NO_".$f.";NAMA_".$f.";".chr(13);
			
			$open_file = fopen($file_csv,'w');
			fwrite($open_file,$header.$isi);
			fclose($open_file);
			
	}
	
	function update_error($m_loading_id, $field,$n){
		$sql_upd_m_load = "UPDATE m_loading
							SET
								$field = $n
							WHERE m_loading_id = $m_loading_id";
		$qry_upd_m_load = pg_query($sql_upd_m_load);
		
		if(@pg_affected_rows($qry_upd_m_load) == 0){
			echo "ERROR update m_loading error $field: " . $sql_upd_m_load;
		}
	}
	
	function format_aneh($x)
	{
		$arr_pr = explode('/',$x);
		if ( count($arr_pr ) >2 )
		{
			return $arr_pr[1].'-'.$arr_pr[0];
		}else{
			return $x;
		}
	}
	
	
	//copy_temp_to_log
	function copy_temp_to_log(){
		$sql_upd_m_load = "

INSERT INTO log_temp_excel_a1(
            no_ticket_2 ,
					card_number ,
					nama ,
					tgl_close_card ,
					tgl_lunas_bayar ,
					no_surat ,
					tgl_surat ,
					alamat_1 ,
					alamat_2 ,
					alamat_3 ,
					alamat_4 ,
					alamat_5 ,
					cr_addr_email ,
			m_loading_id, nama_file)
           select
          no_ticket_2 ,
					card_number ,
					nama ,
					tgl_close_card ,
					tgl_lunas_bayar ,
					no_surat ,
					tgl_surat ,
					alamat_1 ,
					alamat_2 ,
					alamat_3 ,
					alamat_4 ,
					alamat_5 ,
					cr_addr_email ,
					m_loading_id, nama_file
            FROM temp_excel_a1";
		$qry_upd_m_load = pg_query($sql_upd_m_load);
		
		if(@pg_affected_rows($qry_upd_m_load) == 0){
			echo "ERROR COPY temporary to LOG TEMPORARY ";
		}
	}
	
	function add_page1($pdf, $url_pdf='')
	{
		if ( !empty($url_pdf) )
		{
			//Brosur_CIMB.pdf
			$pdf->AddPage();
			$master_template = "../tmp/master_template_pdf/$url_pdf";
		
			$pdf->setSourceFile($master_template);
			$import_template = $pdf->importPage(1);
			$pdf->useTemplate($import_template, 0,0, null, false);
			
			return 'ada';
		}
		
		return 'no';
		

	}
	
	
	
	function membaca_txt_listing_a1()
	{
		$file1 = $GLOBALS['dir_root'].'PERSONAL_LOAN/INPUT_EDP/Welkit_home.pdf';
		$file2 = $GLOBALS['dir_root'].'PERSONAL_LOAN/INPUT_EDP/Welkit_office.pdf';
		
		$file1a = $GLOBALS['dir_root'].'PERSONAL_LOAN/INPUT_EDP/Welkit_home_convent.pdf';
		$file1b = $GLOBALS['dir_root'].'PERSONAL_LOAN/INPUT_EDP/Welkit_home_syariah.pdf';
		$file2a = $GLOBALS['dir_root'].'PERSONAL_LOAN/INPUT_EDP/Welkit_office_convent.pdf';
		$file2b = $GLOBALS['dir_root'].'PERSONAL_LOAN/INPUT_EDP/Welkit_office_syariah.pdf';
		
		$file3 = $GLOBALS['dir_root'].'PERSONAL_LOAN/INPUT_EDP/LISTING_ALL_CUSTOMER_PERSONAL_LOAN.TXT';
		$dirfile_wl_1 = $GLOBALS['dir_root'].'PERSONAL_LOAN/output_txt/Welkit_home.txt';
		$dirfile_wl_2 = $GLOBALS['dir_root'].'PERSONAL_LOAN/output_txt/Welkit_office.txt';
		
		
		$dirfile = $file3;
	
		if(!file_exists($file1a)){
			#die("PROSES GAGAL <br> file Welkit_home_convent.pdf belum di copy!");
			$file1a = $GLOBALS['dir_root'].'PERSONAL_LOAN/master/NO_PRODUK.pdf';
		}
		
		if(!file_exists($file1b)){
			#die("PROSES GAGAL <br> file Welkit_home_syariah.pdf belum di copy!");
			$file1b = $GLOBALS['dir_root'].'PERSONAL_LOAN/master/NO_PRODUK.pdf';
		}
		
		if(!file_exists($file2a)){
			#die("PROSES GAGAL <br> file Welkit_office_convent.pdf belum di copy!");
			$file2a = $GLOBALS['dir_root'].'PERSONAL_LOAN/master/NO_PRODUK.pdf';
		}
		
		if(!file_exists($file2b)){
			#die("PROSES GAGAL <br> file Welkit_office_syariah.pdf belum di copy!");
			$file2b = $GLOBALS['dir_root'].'PERSONAL_LOAN/master/NO_PRODUK.pdf';
		}
		
		
		
		if(!file_exists($file3)){
			die("PROSES GAGAL <br> file LISTING_ALL_CUSTOMER_PERSONAL_LOAN.TXT belum di copy!");
		}
		
		
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		//menggabungkan welkit home syariah dan konven menjadi 1
		if(file_exists($file1)){
			unlink($file1);
		}
		$command = 'C:/pdftk/bin/pdftk ' . $file1a .' '. $file1b. ' cat output ' . $file1; 
		exec($command);
		sleep(10);
		
		
		if(!file_exists($file1)){
			die("FILE $file1 tidak bisa terbentuk");
		}
		
		if(file_exists($file2)){
			unlink($file2);
		}
		//menggabungkan welkit office syariah dan konven menjadi 1
		$command = 'C:/pdftk/bin/pdftk ' . $file2a .' '. $file2b. ' cat output ' . $file2; 
		exec($command);
		sleep(10);
		
		if(!file_exists($file2)){
			die("FILE $file2 tidak bisa terbentuk");
		}
		
		
		
		
		
		$dir_source_wl_1 = $file1;
		$dir_source_wl_2 = $file2;
		
		
		$gabungkan = 'C:/xpdf/bin32/pdftotext -layout ' . $dir_source_wl_1 . ' '.$dirfile_wl_1;
		exec($gabungkan);
		sleep(10);
		
		if(!file_exists($dirfile_wl_1)){
			die("FILE $dirfile_wl_1 tidak bisa terbentuk");
		}
		
		
		$gabungkan = 'C:/xpdf/bin32/pdftotext -layout ' . $dir_source_wl_2 . ' '.$dirfile_wl_2;
		exec($gabungkan);
		sleep(10);
		
		if(!file_exists($dirfile_wl_2)){
			die("FILE $dirfile_wl_2 tidak bisa terbentuk");
		}
		

		//////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$data=array();
		$data_x=array();
		
								if(file_exists($dirfile)){
								$customer = 0;
								$open_dat = fopen($dirfile,'r') or die('Err: Error Open File!');
								$x=0;
								$customer_n=0;
								$lembar_n=0;
								while(!feof($open_dat)){
									$row_dat = fgets($open_dat);
									$regtype = strtoupper(substr($row_dat,0,1));
									
									$mm = explode('|', $row_dat);
									$kurir = NULL;
									$kurir = trim($mm[41],'"');
									$acct_no = trim($mm[2],'"');
									
									
									if (empty($acct_no)) continue;
									
									
									###echo "$sql_upd_m_load<br>";
									$sql_upd_m_load = "UPDATE temp_excel_a1
														SET
															kurir = '$kurir' 
														WHERE c = '$acct_no'";
														
									$qry_upd_m_load = pg_query($sql_upd_m_load) ;
									
									$x++;
								}
								###die ("jumlah datanya: $x");
						}
		return 'done';
	}
	
	
	
	function membaca_txt_pdf_a1()
	{
		//$dir_source = "D:/htdocs/e_statement_cimb_niaga/tmp/adhoc/2CIF/2CIF.pdf";
		$document = $GLOBALS['dir_root'].'PERSONAL_LOAN/INPUT_PDF/pdf_a1.pdf';
		
		
		$dirfile = $GLOBALS['dir_root'].'PERSONAL_LOAN/output_txt/pdf_a1.txt';
		$dirfile_wl_1 = $GLOBALS['dir_root'].'PERSONAL_LOAN/output_txt/Welkit_home.txt';
		$dirfile_wl_2 = $GLOBALS['dir_root'].'PERSONAL_LOAN/output_txt/Welkit_office.txt';
		
		$file = $dirfile;
		if(file_exists($dirfile)){
			unlink($dirfile);
		}
		
		if(file_exists($dirfile_wl_1)){
			unlink($dirfile_wl_1);
		}
		
		if(file_exists($dirfile_wl_2)){
			unlink($dirfile_wl_2);
		}
		
		
		if(!file_exists($document)){
			die("PROSES GAGAL <br> file pdf tabel amortisasi untuk di split belum di copy. penamaan file harus PDF_a1.PDF");
		}
		$gabungkan = 'C:/xpdf/bin32/pdftotext -layout ' . $document . ' '.$dirfile;
		exec($gabungkan);
		sleep(10);
		
		#die("XXXXXX");
		membuat_tabel_txt();
		
		$data=array();
		$data_x=array();
		
								if(file_exists($dirfile)){
								$customer = 0;
								$open_dat = fopen($dirfile,'r') or die('Err: Error Open File!');
								$header_dat = fgets($open_dat);
								$x=1;
								$customer_n=0;
								$lembar_n=0;
								while(!feof($open_dat)){
									$row_dat = fgets($open_dat);
									$regtype = strtoupper(substr($row_dat,0,1));
									if ( preg_match("/Kepada/i", $row_dat) )
									{
										$customer_n++;
										$customer = "Customer $customer_n";
										$start_x = $x;
									}
									
									$mm = NULL;
									$mm = explode('-', $row_dat);
									if ( count($mm) == 4 && strlen($mm[1]) > 2 )
									{
										$acct_no = trim($row_dat);
										$acct_no = str_replace('-','',$acct_no);
										
									}
									
									
									if(ord($regtype)==12){
										###echo "halaman $x <br>";
										$halaman = $x;
										$x++;
										$xyz = ($halaman-$start_x)+1;
										$lembar_n++;
										$data[$file][$customer]=$start_x ." - ".$halaman ." ## total halaman = ".$xyz." ## $acct_no ##$lembar_n";
										
										$data_x[$customer] = "$customer---$acct_no---$start_x---$halaman---$xyz---$file";
									}
									
								}
								///Jumlah Cust###Lembar###amplop
								$recon[$file] = "TOTAL CUST: $customer_n###TOTAL LEMBAR: $lembar_n###$customer_n";
								fclose($open_dat);
						}
						
		
		foreach($data_x as $cell => $val)
		{
			$w3r1_array = explode('---', $val);
			list($nama_cust, $acct_no, $start, $end, $total_halaman, $file) = $w3r1_array;
			membuat_tabel_txt_insert($nama_cust, $acct_no, $start, $end, $total_halaman, $file);
		}
						
		$www=array();
		$www['data'] = $data;
		$www['summary'] = $data_x;
		$www['recon'] =$recon;
		
		return $www;
	}
	
	
	function membuat_tabel_txt()
	{
		
				$temp2 = 'temp_excel_a1_summary';
					$buat_tabel_temp = 
						" 
						DROP TABLE IF EXISTS " . $temp2 . ";
						CREATE TABLE $temp2 (
						id SERIAL,
						nama_cust text,
						acct_no text,
						mulai integer,
						selesai integer,
						total_halaman integer,
						file text
						)
						";
				
					$exe_tabel = pg_query($buat_tabel_temp)or die("ERROR: " . $buat_tabel_temp);
	}
	
	
	function membuat_tabel_txt_insert($nama_cust, $acct_no, $start, $end, $total_halaman, $file)
	{
		
				$temp2 = 'temp_excel_a1_summary';
					$buat_tabel_temp = 
						" 
						
						INSERT INTO $temp2(
						nama_cust, acct_no, mulai, selesai, total_halaman, file
						)
						VALUES (
						'$nama_cust', '$acct_no', $start, $end, $total_halaman, '$file'
						);
						";
				
					$exe_tabel = pg_query($buat_tabel_temp)or die("ERROR: " . $buat_tabel_temp);
	}
	
	
	function bulan_eng_to_idn($bln)
	{
		
		$arrMon = array(
		'Januari' => '01', 
		'Februari' => '02',
		'Maret' => '03', 
		'April' => '04', 
		'Mei' => '05', 
		'Juni' => '06', 
		'Juli' => '07', 
		'Agustus' => '08', 
		'September' => '09', 
		'Oktober' => '10', 
		'November' => '11', 
		'Desember' => '12');
		
		
		$monNm = $arrMon[$bln];
		if ( empty( $monNm ) )
		{
			return $bln;
		}else{
			return $monNm;
		}
		
	}
	
	
	function convDate2($tgl)
	{
		$arrMon = array(
		'01' => 'Januari', '1' => 'Januari', 
		'02' => 'Februari', '2' => 'Februari', 
		'03' => 'Maret', '3' => 'Maret', 
		'04' => 'April', '4' => 'April', 
		'05' => 'Mei', '5' => 'Mei', 
		'06' => 'Juni', '6' => 'Juni', 
		'07' => 'Juli', '7' => 'Juli', 
		'08' => 'Agustus', '8' => 'Agustus', 
		'09' => 'September', '9' => 'September',
		'10' => 'Oktober', 
		'11' => 'November', 
		'12' => 'Desember');
		$arrTgl = explode('/', $tgl);
		$monNm = $arrMon[$arrTgl[1]];
		return $arrTgl[2] . ' ' . $monNm . ' ' . $arrTgl[0];
	}
	

	
	function get_html($tgl_1, $nama, $address1 , $address2 , $address3,  $city,  $email)
	{
		return '
			
			
			
			
			<tr>
				<td height="26" valign="top">
				<p class="hurufmargin">
						&nbsp;</p>
				</td>
				<td>
				<p class="hurufmargin" style="text-align:right;">
						'.$tgl_1.'</p>
				</td>
			</tr>
			<tr>
				<td height="26" valign="top" colspan="2">
				
					<p class="hurufmargin">
						Kepada YTH Bapak/Ibu <b>'.$nama.'</b><br>
						<br>
						<br>
						<b>'.$email.'</b><br>
						<br>
						 </p>
					
				</td>
			</tr>
			<tr>
				<td height="45" valign="top" colspan="2">
					<p class="hurufmargin">
						<br />
						Nasabah X-Tra Dana CIMB Niaga yang terhormat,
						<br />
						</p>
						
					<p class="hurufmargin">
						Terima kasih atas kepercayaan dan pilihan Anda untuk menjadi nasabah X-Tra Dana CIMB Niaga. 
						<br />
						</p>
						
						
					<p class="hurufmargin">
						Sebagai nasabah kami, Anda layak mendapatkan fasilitas terbaik yang kami persembahkan melalui fasilitas X-Tra Dana. X-Tra Dana hadir untuk memberikan solusi keuangan guna memenuhi apapun kebutuhan Anda, seperti biaya renovasi rumah, biaya pendidikan, perlengkapan rumah tangga, barang elektronik, biaya pernikahan, liburan dan lainnya.
						<br />
						</p>
						
					<p class="hurufmargin">
						Untuk memudahkan Anda mengetahui jumlah cicilan pinjaman per bulan, perkenankan kami menyampaikan rincian cicilan X-tra Dana Anda yang berisi:
						<br />
						</p>
						
						
					<table cellpadding="0" cellspacing="0" border="0">
						<tbody>
							<tr>
								<td valign="top" width="20" class="hurufmargin">
									- &nbsp;</td>
								<td height="16" valign="top" class="hurufmargin">
									Jangka waktu tertera di kolom <b>"Number of Payment/Jml Angsuran"</b> </td>
							</tr>
							<tr>
								<td valign="top" width="20" class="hurufmargin">
									- &nbsp;</td>
								<td height="16" valign="top" class="hurufmargin">
									Cicilan yang harus dibayarkan tiap bulan tertera di kolom <b>"Reguler Payment/ Angsuran"</b></td>
							</tr>
							<tr>
								<td valign="top" width="20" class="hurufmargin">
									- &nbsp;</td>
								<td height="16" valign="top" class="hurufmargin">
									Cicilan dibayarkan sebelum tanggal jatuh tempo yang tertera di kolom <b>"Date/Tanggal"</b></td>
							</tr>
							<tr>
								<td valign="top" width="20" class="hurufmargin">
									- &nbsp;</td>
								<td height="16" valign="top" class="hurufmargin">
									Cicilan tiap bulannya dapat dibayarkan ke nomor kartu X-Tra Dana Anda yang tersedia di kolom  <b>"PL Number No. Personal Loan"</b>.</b></td>
							</tr>
							
							
							
							
						</tbody>
					</table>
					<br>
					
				</td>
				
			</tr>
			
			<tr>
				<td height="18" valign="top" colspan="2">
				
				
				<p class="hurufmargin">
						
Berbagai informasi mengenai X-Tra Dana dapat Anda ketahui dengan membaca petunjuk penggunaan yang kami sertakan. Bacalah dengan teliti dan simpanlah untuk referensi pada masa yang akan datang. Untuk informasi lebih lanjut, silakan menghubungi <b>Call Center CIMB Niaga 14041</b> atau kunjungi website kami di <b> <a href="www.cimbniaga.com">www.cimbniaga.com</a></b>

						<br />
						</p>
						
						
						
						
						
						
						
						
						<p class="hurufmargin">
						Silahkan gunakan password ddmmyy untuk membuka E-Statement Anda. Password terdiri dari:
						</p>
						<table border="0" width="600">
	<tbody>
		<tr>
			<td height="10" width="9" class="hurufmargin">
				&nbsp;</td>
			<td height="10" width="59" class="hurufmargin">
				- dd</td>
			<td height="10" width="17" class="hurufmargin">
				:</td>
			<td height="10" class="hurufmargin">
				dua digit tanggal lahir</td>
		</tr>
		<tr>
			<td height="10" class="hurufmargin">
				&nbsp;</td>
			<td height="10" class="hurufmargin">
				- mm</td>
			<td height="10" class="hurufmargin">
				:</td>
			<td height="10" class="hurufmargin">
				dua digit bulan lahir</td>
		</tr>
		<tr>
			<td height="10" class="hurufmargin">
				&nbsp;</td>
			<td height="10" class="hurufmargin">
				- yy</td>
			<td height="10" class="hurufmargin">
				:</td>
			<td height="10" class="hurufmargin">
				dua digit tahun lahir</td>
		</tr>
		<tr>
			<td height="10" class="hurufmargin">
				&nbsp;</td>
			<td height="10" class="hurufmargin">
				&nbsp;</td>
			<td height="10" class="hurufmargin">
				&nbsp;</td>
			<td height="10" class="hurufmargin">
				&nbsp;</td>
		</tr>
		<tr>
			<td height="10" class="hurufmargin">
				&nbsp;</td>
			<td height="10" class="hurufmargin">
				contoh</td>
			<td height="10" class="hurufmargin">
				:</td>
			<td height="10" class="hurufmargin">
				Password untuk tanggal lahir 20 Maret 1971 adalah 200371</td>
		</tr>
	</tbody>
</table>








						<p class="hurufmargin">
Untuk dapat menerima laporan cicilan per bulan melalui <i>e-mail</i>, pastikan Anda memiliki program Adobe Acrobat Reader versi 6.0 ke atas pada komputer atau <i>handphone</i> Anda.
						<br />
						</p>
					<p class="hurufmargin">
						Selamat mewujudkan mimpi Anda dan keluarga bersama X-Tra Dana!</p>
				
				</td>
			</tr>
			
			';
	}
	
	function cek_jarak_potong_wl($temp, $nomor_rekening, $kode_kirim, $dirfile_wl_x)
	{
			$dirfile = $dirfile_wl_x;
			$data= array();
			
						if(file_exists($dirfile)){
								$customer = 0;
								$open_dat = fopen($dirfile,'r') or die('Err: Error Open File!');
								$header_dat = fgets($open_dat);
								$x=1;
								$customer_n=0;
								$lembar_n=0;
								
								while(!feof($open_dat)){
									$row_dat = fgets($open_dat);
									$regtype = strtoupper(substr($row_dat,0,1));
									
									
									
									if(ord($regtype)==12){
										###echo "halaman $x <br>";
										if ( $acct_no == $nomor_rekening)
										{
											fclose($open_dat);
											
											$data['jarak_potong'] = $x.'-'.$x;
											$data['kalimat_no_urut'] = $kalimat_no_urut;
											#return $x.'-'.$x;
											return $data;
										}
										$halaman = $x;
										$x++;
									}
									
									
									if ( preg_match("/reg:/i", $row_dat) || preg_match("/REG:/i", $row_dat) || preg_match("/reg :/i", $row_dat) || preg_match("/REG :/i", $row_dat))
									{
										$mm = NULL;
										$mm = explode(':', $row_dat);
										$mm[1] = trim($mm[1]);
										$mmm = explode(' ', $mm[1]);
										$acct_no = $mmm[0];
									}
									
									$mm = NULL;
									$mm = explode('-', $row_dat);
									if (count($mm) == 4)
									{
										$kalimat_no_urut = trim($row_dat);
									}
									
									
									
								}
								fclose($open_dat);
						}else{
							die("DATA $dirfile WELCOME LETTER TIDAK DITEMUKAN");
						}
						
						die("DATA $nomor_rekening WELCOME LETTER TIDAK DITEMUKAN PADA $dirfile");
	}
	
	function cek_jarak_potong_wl_x($temp, $nomor_rekening, $kode_kirim, $dirfile_wl_x)
	{
		$sql_baca = "SELECT * FROM $temp 
						WHERE c='$nomor_rekening' and aj='$kode_kirim' 
						ORDER BY c ASC" ;
		$qry = pg_query($sql_baca) or die('ERROR: '.$sql_baca);
		$x = 0;
		while($row = pg_fetch_array($qry)){
			$x++;
			
			if ( $row['c'] == $nomor_rekening )
			{
				return $x.'-'.$x;
			}
		}
	}
	
	
	function buat_tmp_p01_cetak_x($dir_logx, $recon, $nama_cycle)
	{
		$tmp_html='';
		$n_cust=0;
		$n_lembar=0;
		$n_amplop=0;
		
		$str_Exsip = "";
		$nl="\r\n";
		$handle 		= fopen($dir_logx."SIP_$nama_cycle.txt", "w");
		
		foreach( $recon as $key => $val ) 
		{
			$row = explode('###',$val);
			 $tmp_html .='
			 <tr style="font-size:11px;">
			 <td style="text-align:left;padding:4px;background:#ffffff;">'.$key.' </td>
									<td style="text-align:right;padding:4px;background:#ffffff;"> '.$row[0].' &nbsp;</td>
									<td style="text-align:right;padding:4px;background:#ffffff;"> '.$row[1].' &nbsp;</td>
									<td style="text-align:right;padding:4px;background:#ffffff;"> '.$row[2].' &nbsp;</td><td style="text-align:center;padding:4px;background:#ffffff;"></td>
									<td style="text-align:center;padding:4px;background:#ffffff;"></td>
									<td style="text-align:center;padding:4px;background:#ffffff;"></td>
									<td style="text-align:center;padding:4px;background:#ffffff;"></td>
									<td style="text-align:center;padding:4px;background:#ffffff;"></td>
									<td style="text-align:center;padding:4px;background:#ffffff;"></td></tr>
									';
			$lokasi_folder = $row[3];
			$n_cust += $row[0];
			$n_lembar += $row[1];
			$n_amplop += $row[2];
			
			
			$file_gabung = $row[3].'*.pdf';
			$gabungkan = 'C:/pdftk/bin/pdftk ' . $file_gabung . ' cat output ' . $dir_logx."$key.pdf";
			exec($gabungkan);
			
			
			
			$str_Exsip = "x|x".$key."x|x".chr(9)."x|x".$row[4]."x|x".chr(9)."".$row[0]."".chr(9)."x|xSELINDOx|x".chr(9)."x|x".'PL'."x|x".chr(9)."".$row[1]."".chr(9)."".$row[2]."".chr(9)."".date("m/d/Y H:i:s", time())."".
			chr(9)."".date("m/d/Y H:i:s", time())."".
			chr(9)."".date("m/d/Y H:i:s", time())."".chr(9).
			#"".$daterecive_ex1[1]."/".$daterecive_ex1[2]."/".$daterecive_ex1[0]." ".$daterecive_ex[1]."".chr(9).
			#"".$datebeginpro_ex1[1]."/".$datebeginpro_ex1[2]."/".$datebeginpro_ex1[0]." ".$datebeginpro_ex[1]."".chr(9).
			"".date("m/d/Y H:i:s", time())."";
			$str_Exsip = str_replace('x|x','"', $str_Exsip);
			fwrite ($handle , $str_Exsip.$nl);
		}
		
		
		
	
		
		 $tmp_html .='<tr style="font-size:11px;">
    	<td style="text-align:center;padding:4px;background:#ffffff;">Grand Total</td>
        <td style="text-align:right;padding:4px;background:#ffffff;"> '.$n_cust.' &nbsp;</td>
        <td style="text-align:right;padding:4px;background:#ffffff;"> '.$n_lembar.' &nbsp;</td>
        <td style="text-align:right;padding:4px;background:#ffffff;"> '.$n_amplop.' &nbsp;</td>
        <td colspan="6" style="background:#ffffff;"></td>
    </tr>';
		
		include_once("design_p01_a1.php");
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function buat_nomor_urut($pdfLoc_cetakan, $temp_no_urut, $kalimat_no_urut)
	{
	
	
			$pdf = new TCPDF('P', 'mm', 'A4');
                $pdf->SetAutoPageBreak(true, 1);
                $pdf->SetDisplayMode("real");
                $pdf->SetLeftMargin(0);
                $pdf->SetRightMargin(0);
                $pdf->SetTopMargin(0);
                $pdf->setPrintHeader(false);
                $pdf->setPrintFooter(false);
			$pdf->AddPage();
			
			$pdf->SetFont('helvetica', 'I', 6, '', false);
            $pdf->SetXY('20', -45); //kz
            $pdf->Cell(50, 6, $kalimat_no_urut, 0, 1);
			
			$pdf_output = $pdfLoc_cetakan.$temp_no_urut;
			#die("$pdf_output");
			
			 
			if(file_exists($pdf_output))
			{
				unlink($pdf_output);
			}
			$pdf->Output($pdf_output, 'F');
			 ####die($pdf_output.' - '.$kalimat_no_urut);
			if(file_exists($pdf_output))
			{
				return true;
			}else{
				die("PDF TEMP $kalimat_no_urut TIDAK DITEMUKAN");
			}
	}
	
	
	
	function get_html_sya($tgl_1, $nama, $address1 , $address2 , $address3,  $city,  $email, $k1, $k2, $k3, $k4, $k5)
	{
		return '
			
			
			
			
			<tr>
				<td height="26" valign="top">
				<p class="hurufmargin">
						&nbsp;</p>
				</td>
				<td>
				<p class="hurufmargin" style="text-align:right;">
						'.$tgl_1.'</p>
				</td>
			</tr>
			<tr>
				<td height="26" valign="top" colspan="2">
				
					<p class="hurufmargin">
						Kepada YTH Bapak/Ibu <b>'.$nama.'</b><br>
						<br>
						<br>
						<b>'.$email.'</b><br>
						<br>
						 </p>
					
				</td>
			</tr>
			
			
			<tr>
				<td height="45" valign="top" colspan="2">
					<p class="hurufmargin">
						<br />
						Nasabah X-tra Dana iB Niaga yang terhormat, 
						<br />
						</p>
						
					<p class="hurufmargin">
						Terima kasih atas kepercayaan dan pilihan Anda untuk menjadi nasabah X-Tra Dana iB Cimb Niaga. 
						<br />
						</p>
						
						
					<p class="hurufmargin">
						Sebagai nasabah kami, Anda layak mendapatkan fasilitas terbaik yang kami persembahkan melalui fasilitas X-Tra Dana iB. X-Tra Dana iB hadir untuk memberikan solusi keuangan guna memenuhi apapun kebutuhan Anda, seperti biaya pendidikan, biaya ibadah umrah/wisata religi, biaya pernikahan, biaya kesehatan, liburan, perlengkapan rumah tangga, barang elektronik, dan lainnya. 
						<br />
						</p>
						
					<p class="hurufmargin">
						Bersama ini, kami sampaikan persetujuan fasilitas X-Tra Dana iB  untuk Anda sebagai berikut: 
						<br />
						</p>
						
					
					
					
					
		<table border="0" width="600">
	<tbody>
		<tr>
			<td height="10" width="9" class="hurufmargin">
				&nbsp;</td>
			<td height="10" width="300" class="hurufmargin">
				<b>Mitra Penyedia Jasa/Barang</b></td>
			<td height="10" width="17" class="hurufmargin">
				:</td>
			<td height="10" class="hurufmargin">
				<b>'.$k1.'</b></td>
		</tr>
		<tr>
			<td height="10" class="hurufmargin">
				&nbsp;</td>
			<td height="10" class="hurufmargin">
				<b>Harga Perolehan Paket Jasa/Barang</b></td>
			<td height="10" class="hurufmargin">
				:</td>
			<td height="10" class="hurufmargin">
				<b>'.$k2.'</b></td>
		</tr>
		<tr>
			<td height="10" class="hurufmargin">
				&nbsp;</td>
			<td height="10" class="hurufmargin">
				<b>Jumlah Pembiayaan Bank </b></td>
			<td height="10" class="hurufmargin">
				:</td>
			<td height="10" class="hurufmargin">
				<b>'.$k3.'</b></td>
		</tr>
		<tr>
			<td height="10" class="hurufmargin">
				&nbsp;</td>
			<td height="10" class="hurufmargin">
				<b>Jangka Waktu  </b></td>
			<td height="10" class="hurufmargin">
				:</td>
			<td height="10" class="hurufmargin">
				<b>'.$k4.'</b></td>
		</tr>
		<tr>
			<td height="10" class="hurufmargin">
				&nbsp;</td>
			<td height="10" class="hurufmargin">
				<b>Angsuran/bln</b></td>
			<td height="10" class="hurufmargin">
				:</td>
			<td height="10" class="hurufmargin">
				<b>'.$k5.'</b></td>
		</tr>
	</tbody>
</table>



					<br>
					
				</td>
				
			</tr>
			
			
			<tr>
				<td height="18" valign="top" colspan="2">
				
					<p class="hurufmargin">
						Jumlah pembiayaan tersebut telah dikreditkan ke rekening Anda dan telah kami transfer ke <b>'.$k1.'</b> selaku mitra penyedia jasa/barang di Bank CIMB Niaga. Terlampir kami sampaikan pula rincian fasilitas dan jadual pembayaran angsuran fasilitas X-Tra Dana iB Anda. 
						<br />
						</p>
						
					<p class="hurufmargin">
						Kami informasikan juga bahwa sesuai ketentuan yang berlaku, setiap fasilitas pinjaman/pembiayaan Bank akan terdaftar pada Sistem Informasi BI dan/atau OJK. Apabila nasabah memiliki lebih dari 1 (satu) fasilitas pinjaman/pembiayaan Bank, maka status kolektibilitas yang dilaporkan akan mengacu pada kualitas kredit terendah.
						<br />
						</p>
				
				<p class="hurufmargin">
Berbagai informasi mengenai X-Tra Dana iB dapat Anda ketahui dengan membaca petunjuk penggunaan yang kami sertakan. Bacalah dengan teliti dan simpanlah untuk referensi pada masa yang akan datang. Untuk informasi lebih lanjut, silakan menghubungi <b>Call Center CIMB Niaga 14041</b> atau kunjungi website kami di <b> <a href="www.cimbniaga.com">www.cimbniaga.com</a></b>

						<br />
						</p>
						
						
						
						
						
						
						
						
						<p class="hurufmargin">
						Silahkan gunakan password ddmmyy untuk membuka E-Statement Anda. Password terdiri dari:
						</p>
						<table border="0" width="600">
	<tbody>
		<tr>
			<td height="10" width="9" class="hurufmargin">
				&nbsp;</td>
			<td height="10" width="59" class="hurufmargin">
				- dd</td>
			<td height="10" width="17" class="hurufmargin">
				:</td>
			<td height="10" class="hurufmargin">
				dua digit tanggal lahir</td>
		</tr>
		<tr>
			<td height="10" class="hurufmargin">
				&nbsp;</td>
			<td height="10" class="hurufmargin">
				- mm</td>
			<td height="10" class="hurufmargin">
				:</td>
			<td height="10" class="hurufmargin">
				dua digit bulan lahir</td>
		</tr>
		<tr>
			<td height="10" class="hurufmargin">
				&nbsp;</td>
			<td height="10" class="hurufmargin">
				- yy</td>
			<td height="10" class="hurufmargin">
				:</td>
			<td height="10" class="hurufmargin">
				dua digit tahun lahir</td>
		</tr>
		<tr>
			<td height="10" class="hurufmargin">
				&nbsp;</td>
			<td height="10" class="hurufmargin">
				&nbsp;</td>
			<td height="10" class="hurufmargin">
				&nbsp;</td>
			<td height="10" class="hurufmargin">
				&nbsp;</td>
		</tr>
		<tr>
			<td height="10" class="hurufmargin">
				&nbsp;</td>
			<td height="10" class="hurufmargin">
				contoh</td>
			<td height="10" class="hurufmargin">
				:</td>
			<td height="10" class="hurufmargin">
				Password untuk tanggal lahir 20 Maret 1971 adalah 200371</td>
		</tr>
	</tbody>
</table>








						<p class="hurufmargin">
Untuk dapat menerima laporan cicilan per bulan melalui <i>e-mail</i>, pastikan Anda memiliki program Adobe Acrobat Reader versi 6.0 ke atas pada komputer atau <i>handphone</i> Anda.
						<br />
						</p>
					<p class="hurufmargin">
						Selamat mewujudkan mimpi Anda dan keluarga bersama X-Tra Dana iB!</p>
				
				</td>
			</tr>
			';
	}
	
	
	
	
	
	function remove_duplikat_polis(){
		
		$sql		= "UPDATE temp_csv_a1
   SET 
   status_a1='duplikat'
   where policy_no IN ( 
   select policy_no  FROM temp_csv_a1 where estatement_ind='Y'  group by policy_no having count(*) > 1 
 );
";
		$query 		= pg_query($sql);
		
		return 'x';
	}
	pg_close($con);
	
	*/
?>