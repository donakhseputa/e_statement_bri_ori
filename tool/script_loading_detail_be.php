<? #require_once("../include/config.php"); ?>
<? #require_once("../include/session.php"); ?>
<? #require_once("../include/function.php"); ?>
<? define('FPDF_FONTPATH','../include/fpdf/font/'); ?>
<? #require_once("../include/fpdf/fpdf.php"); ?>
<? #require_once("../include/fpdi/FPDI_Protection.php"); ?>
<?php
	require_once('path_url_link.php');
	require_once("../include/config.php");
	require_once("../include/session.php");
	require_once("../include/function.php");
	define('FPDF_FONTPATH','../include/fpdf/font/');
	ini_set('memory_limit', '-1');
	
/** LOADING FILE DENGAN MENGGUNAKAN NOMOR REKENING (DISAMAKAN DGN TABEL m_customer) **/

	$pr = $_REQUEST['pr'];
	$arr_pr = explode('|',$pr);
	$flagtrans = $arr_pr[0];
	$menu_id = $arr_pr[1];
	$act = $arr_pr[2];
	$blth = $arr_pr[3];
	$file = $arr_pr[4];
	$filecust = $arr_pr[6];
	
	$con = pg_connect($connection) or die("Could not connect to database!");
	$msg = "";
	$msg_not_exists = "";
	
	switch($act){
		case 'dat_to_pdf':
				$waktu_awal = strtotime(date("Y-m-d H:i:s"));
				$sql_cek_dat = "SELECT * FROM m_loading WHERE flagtrans = '$flagtrans' and blth = '$blth' and loading_file = '$file'";
				$qry_cek_dat = pg_query($sql_cek_dat) or die('ERROR cek m_loading: '.$sql_cek_dat);
				$jml = pg_num_rows($qry_cek_dat);
				if($jml == 0){
					$msg = dat_to_cust($flagtrans,$menu_id,$blth,$file,$filecust);
				}else{
					$msg = 'ERROR: file sudah pernah di-upload';
				}
				
				$waktu_akhir = strtotime(date("Y-m-d H:i:s"));
				$waktu = $waktu_akhir-$waktu_awal;
				
				echo $msg.'|'.$waktu.'|'.$flagtrans;
			break;
		case 'show_record':
				show_record($blth,$flagtrans);
			break;
	}
	
	function dat_to_cust($flagtrans,$menu_id,$blth,$file,$filecust)
	{
		$sourcefile="../temp_file/billing/".$flagtrans."/".$blth."/";
		$ket = '';
		if(!file_exists($sourcefile.$file)) $ket = 'No file upload found!!!';
		
		if($ket==""){
			$expl = explode('_',$file);
			$tanda = $expl[0];
			#bila ada produk baru tambahkan
			$msg = data_to_pdf($flagtrans,$menu_id,$blth,$file,$sourcefile);
			/*if(strtolower($tanda)=='samira'){
				$msg = data_to_pdf($flagtrans,$menu_id,$blth,$file,$sourcefile);
			}else{
				echo "File produk $tanda tidak ada dalam daftar pada file $file";die();
			}*/
		}else{
			$msg = $ket;
		}
		
		if(!file_exists($sourcefile.$file)) unlink($sourcefile.$file);
		
		return $msg;
	}
	
	function m_loading($flagtrans,$blth,$file,$filecust){
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
		
		return $m_loading_id;
	}
	
	function log_customer($flagtrans,$blth,$file){
		$sql_sel_log_customer = "SELECT log_customer_id FROM log_customer WHERE blth = '$blth' AND flagtrans = '$flagtrans' ORDER BY log_customer_id DESC LIMIT 1";
		$qry_sel_log_customer = pg_query($sql_sel_log_customer) or die('ERROR select log_customer: '.$sql_sel_log_customer);
		$row_sel_log_customer = pg_fetch_array($qry_sel_log_customer);
		$log_customer_id = $row_sel_log_customer['log_customer_id'];
		
		return $log_customer_id;
	}
	
	function data_to_pdf($flagtrans,$menu_id,$blth,$file,$sourcefile){
		
		
		
		$msg_error	= "";
		
		$total_hlm = 0;				$no_urut = 0;			$counter_isi = 0;
		$tot_no_email = 0;			$tot_no_txt = 0;
		$msg_not_exists = "";		$no_txt_convert="";		$no_email="";
		if(file_exists($sourcefile.$file)){
			$tabeldetail = "detail_".$blth."_".$flagtrans;
			$tabeldetail_pk = $tabeldetail."_p_k";
			$tabeldetail_un = $tabeldetail."_unique";
			
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
								  city character varying(40),
								  zipcode character varying(9),
								  flagtrans character varying(2),
								  blth character varying(6),
								  nama_file character varying(50),
								  nama_txt character varying(50),
								  pdf_name character varying(100),
								  password_pdf character varying(25),
								  jml_hlm integer,
								  flag_attach character varying(3),
								  tipe_kartu character varying(40),
								  no_rek_asli character varying(20),
								  ket_produk character varying(60),
								  email text,
								  n_email integer DEFAULT 1,
								  size_pdf numeric(6,2),
								  barcode text,
								  nama_produk text,
								  tanggal text,
								  tgl_jatuh_tempo text,
								  tgl_tagihan text,
								  periode text,
								  nama_corr text,
								  kelurahan text,
								  kecamatan text,
								  tipe text,
								  kode_cab character varying(10),
								  cabang character varying(50),
								  CONSTRAINT $tabeldetail_pk PRIMARY KEY (detail_id),
								  CONSTRAINT $tabeldetail_un UNIQUE (nomor_rekening, nama_file, blth)
								) 
								WITHOUT OIDS;
								ALTER TABLE $tabeldetail OWNER TO postgres;";
				$exe_tabel = @pg_query($buat_tabel);


				$tabelindex = $tabeldetail."_index";			
				$sql_ind = "CREATE INDEX $tabelindex ON $tabeldetail  USING btree (blth, flagtrans, nama_file,nomor_rekening);";
				$exe_ind = pg_query($sql_ind)or die("ERROR: " . $sql_ind);				
			}
			
			#$base_file = str_replace(substr($file,-4),'',$file);
			$base_file = get_nfile($file);
			$m_loading_id = m_loading($flagtrans,$blth,$file,$filecust);
			#log_file_upload($m_loading_id,$file,$blth,$flagtrans);
			
			$open_dat 		= fopen($sourcefile.$file,'r');
			#$header_dat 	= fgets($open_dat);
			#while($dt = pg_fetch_assoc($qry_data)){
			$ln = '
';
			while(!feof($open_dat)){
				$row_dat = fgets($open_dat);
				$exp_row = explode('	',rtrim($row_dat,$ln));
				#$ket_produk				= $dt['a'];#unit
				$acc_num				= trim($exp_row[0]);#unit
				$nama_cust				= trim($exp_row[1]);#unit
				$email_xls				= trim($exp_row[2]);#email
				$pass = '';
				#ganti / dan & dengan ; dan ambil data paling depan
				$email_xls = str_replace('/',';',$email_xls);
				$email_xls = str_replace('& ',';',$email_xls);
				$email_xls = str_replace(' dan ',';',$email_xls);
				$email_xls = str_replace(' DAN ',';',$email_xls);
				$email_xls = str_replace(', ',';',$email_xls);
				$ex_email = explode(';',$email_xls);
				$email = trim($email_xls);
				if(count($ex_email)>1) $email = $ex_email[0];
				$hal = 1;
						
				if($email!=""){
					$no_urut++;
					#$nama_file_pdf = $no_urut.'_'.str_replace(array('.pdf','.PDF'),'',$nfile);
					#$pdf_out	= $loc_bill.$nama_file_pdf.".pdf";
					#$hal_baru = cetak_pdf_password( $pdf_in, $pdf_out, $pass);
					#$hal_baru = copy_pdf($pdf, $pdf_in, $pdf_out, $pass);
					if($hal_baru>1) $hal = $hal_baru;
					//$filesizePdf = output_pdf($pdf,$nama_file_pdf,$blth,$file,$flagtrans); //matikan jika tanpa convert pdf
					#$filesizePdf = fsize($pdf_out);
					$filesizePdf = '0';
					insert_detail($m_loading_id,$cif_num,$acc_num,
					$nama_cust,$alamat1,$alamat2,$alamat3,$alamat4,$kodepos,
					$flagtrans,$blth,$file,
					$nama_file_pdf,$pass,$hal,$email,$filesizePdf,'1',$prod,$ket_produk, $agen,
					$tgl_tagihan,$periode,$nama_file,$nama_corr,$kelurahan,$kecamatan,$kota,$tipe,
					$td_rate,$format,$tgl_jatuh_tempo);
					$total_hlm += $hal;
				}else{
					$tot_no_email++;
					$no_email .= $acc_num.", ";
				}
			}
			
			if($counter_isi>0){
				#csv data
				create_csv($tabeldetail,$m_loading_id,$blth,$file,$flagtrans,$sql_cek);
				#listing file no email
				#$base_file = str_replace(substr($file,-4),'',$file);
				$base_file = get_nfile($file);
				$php_path_file = '../tmp_file/log/'.$flagtrans.'/'.$blth.'/'.$base_file.'/';
				create_folder($php_path_file);
				$file_name_exist = "no_email_".$base_file.".TXT";
				if(trim($no_email) != ''){
					$no_email = "NOMOR REKENING".chr(13).$no_email;
					$create_file_exist = create_file_exist($php_path_file, $file_name_exist, str_replace(', ', chr(13), $no_email));
					if($create_file_exist) {
						$msg_not_exists .= "No Email : <a href='".$php_path_file.$file_name_exist."' target='_blank'>".$file_name_exist."</a> jumlah : $tot_no_email <br>";
					}
				}
				#listing file no txt
				$file_name_exist_2 = "no_txt_".$base_file.".TXT";
				if(trim($no_txt_convert) != ''){
					$no_txt_convert = "NOMOR REKENING".chr(13).$no_txt_convert;
					$create_file_exist = create_file_exist($php_path_file, $file_name_exist_2, str_replace(', ', chr(13), $no_txt_convert));
					if($create_file_exist) {
						$msg_not_exists .= "No File : <a href='".$php_path_file.$file_name_exist_2."' target='_blank'>".$file_name_exist_2."</a> jumlah : $tot_no_txt <br>";
					}
				}
			}
			update_m_loading($m_loading_id,$total_hlm,$no_urut,$tot_no_email,$tot_no_txt);
			#update_log_file_upload($m_loading_id,$file,$blth,$flagtrans);
		}else{
			$msg_error = "Err : File txt data ".$file;
		}
		
		return $msg_error.'|'.$no_urut.'|'.$msg_not_exists;
	}
	
	function insert_mcustomer($no_card, $email, $password_pdf,
								$blth, $product, $log_customer_id){
		if(substr($no_card,0,4)=="4645") $tipe_kartu = "P"; else $tipe_kartu = "T";	
		$sql_ins_cust = "INSERT INTO m_customer(
								nomor_rekening, email1,
								password_pdf, blth, flagtrans,
								log_customer_id, tipe_kartu)
							VALUES(
								'$no_card', '$email',
								'$password_pdf', '$blth', '$product',
								$log_customer_id, '$tipe_kartu')";
		$qry_ins_cust = pg_query($sql_ins_cust) or die('ERROR insert into m_customer: '.$sql_ins_cust);
		if(pg_affected_rows($qry_ins_cust)==0){
			echo 'GAGAL INSERT CUSTOMER UNTUK $nomor_rekening';
			die();
		}
	}
	
	function insert_detail($m_loading_id,$nomor_customer,$nomor_rekening,
								$nama,$address1,$address2,$address3,$address4,$zipcode,
								$flagtrans,$blth,$nama_file_zip,
								$pdf_name,$password_pdf,$jml_hlm,$email,$filesizePdf,$totalemailcustomernya,$prod,$ket_produk,$agen,
								$tgl_tagihan,$periode,$nama_file,$nama_corr,$kelurahan,$kecamatan,$kota,$tipe,
								$td_rate,$format,$tgl_jatuh_tempo){

		$n_email = substr_count($email,";");
		$n_email = $totalemailcustomernya;
		$n_email = 1;
		$ex = explode(';',$email);
		$ct = 0;
		for($i=0;$i<count($ex);$i++){
			if($ex[$i]!="") $ct++;
		}
		if($ct>1) $n_email = $ct;
		$tabeldetail = "detail_" . $blth . "_" . $flagtrans;
		$sql_ins_detail = "INSERT INTO $tabeldetail(
								m_loading_id,nomor_customer,nomor_rekening,
								nama,alamat1,alamat2,alamat3,alamat4,city,zipcode,
								flagtrans,blth,nama_file,nama_txt,
								pdf_name,password_pdf,jml_hlm,tipe_kartu, email, 
								n_email, size_pdf, cabang, ket_produk, 
								tgl_tagihan,periode,nama_corr,kelurahan,kecamatan,tipe,
								nama_produk,tanggal,tgl_jatuh_tempo
							)VALUES(
								$m_loading_id,'$nomor_customer','$nomor_rekening',
								E'".addslashes($nama)."',E'".addslashes($address1)."',E'".addslashes($address2)."',E'".addslashes($address3)."',E'".addslashes($address4)."',E'".addslashes($kota)."','$zipcode',
								'$flagtrans','$blth','$nama_file_zip','$nama_file',
								'$pdf_name','$password_pdf','$jml_hlm', '$tipe_kartunya',E'".addslashes(trim($email))."',
								'$n_email','$filesizePdf','$prod',E'".addslashes($ket_produk)."',
								E'".addslashes($tgl_tagihan)."',E'".addslashes($periode)."',E'".addslashes($nama_corr)."',E'".addslashes($kelurahan)."',E'".addslashes($kecamatan)."',E'".addslashes($tipe)."',
								E'".addslashes($td_rate)."',E'".addslashes($format)."',E'".addslashes($tgl_jatuh_tempo)."'
							)";
								  
		$qry_ins_detail = pg_query($sql_ins_detail) or die('ERROR insert into detail: '.$sql_ins_detail);
		if(pg_affected_rows($qry_ins_detail)==0){
			echo 'GAGAL INSERT DETAIL UNTUK $nomor_rekening';
			die();
		}
	}
	
	function update_m_loading($m_loading_id,$total_hlm,$customer,$tot_no_email,$tot_no_txt){
		
		$sql_upd_m_load = "UPDATE m_loading
							SET
								total_halaman = '$total_hlm',
								total_customer = '$customer' 
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
	
	function create_csv($tabeldetail,$m_loading_id,$blth,$file,$flagtrans,$sql_cek){
		#$csv = str_replace(substr($file,-4),'',$file);
		$csv = get_nfile($file);
		
		$file_csv = '../pdf/'.$flagtrans.'/'.$blth.'/'.$csv.'/'.$csv.'.csv';
		$header = "'cif';'nomor_rekening';'nama';'pdf_name';'password_pdf';".chr(13);
		$detail = "";
		
		$sql = "SELECT nomor_customer,nomor_rekening,nama,pdf_name,password_pdf FROM $tabeldetail WHERE m_loading_id = $m_loading_id";
		$qry = pg_query($sql) or die('ERROR: '.$sql);
		while($row = pg_fetch_array($qry)){
			$nomor_customer = $row['nomor_customer'];
			$nomor_rekening = $row['nomor_rekening'];
			$nama = $row['nama'];
			$pdf_name = $row['pdf_name'];
			$password_pdf = $row['password_pdf'];
			
			$detail .= "'".$nomor_customer."';'".$nomor_rekening."';'".$nama."';'".$pdf_name."';'".$password_pdf."';'".chr(13);
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
	
	function log_file_upload($m_loading_id,$file,$blth,$flagtrans){
		$folder_dest="../temp_file/billing/".$flagtrans."/".$blth."/";
		#if(is_folder($folder_dest)){
			if(is_file($folder_dest.$file)){
				$str = "INSERT INTO log_file_upload(
								nama_file, path,  
							   flagtrans, blth, waktu, status, m_loading)
						VALUES (E'".addslashes($file)."', E'".addslashes($folder_dest)."', 
								'$flagtrans', '$blth', now(), 'available', $m_loading_id);";
				$sql = pg_query($str) or die($str);
			}else{
				die('Err : File '.$folder_dest.$file.' tidak ditemukan');
			}
		#}else{
		#	die('Err : Folder '.$folder_dest.' tidak ditemukan');
		#}
	}
	
	function update_log_file_upload($m_loading_id,$file,$blth,$flagtrans){
		$folder_dest="../temp_file/billing/".$flagtrans."/".$blth."/";
		#if(is_folder($folder_dest)){
			if(is_file($folder_dest.$file)){
				unlink($folder_dest.$file);
				$str = "UPDATE log_file_upload SET status = 'deleted' WHERE m_loading = $m_loading_id;";
				$sql = pg_query($str) or die($str);
			}else{
				die('Err : File '.$folder_dest.$file.' atau '.$folder_dest.$filecust.' tidak ditemukan');
			}
		#}else{
		#	die('Err : Folder '.$folder_dest.' tidak ditemukan');
		#}
	}
	
	function zip_password_unrar($source, $destination, $nama_file)
	{
		$folder_sumber = $source;
		$file_hasil =  $destination;
		$code = "x" ;
		#$command = "C:\Winrar\WinRAR.exe " . $code ." -p".$password . " ". $folder_sumber . " ". $file_hasil;
		$command = "C:\Winrar\WinRAR.exe " . $code ." ". $folder_sumber . " ". $file_hasil;
		
		#die($command);
		exec($command);
		return true;
	}
	
	
	function get_nama_folder_extract_zip($direktori)
	{
		$folder='';
		$data=array();
		
		$data['daftar_file']  = opendir( $direktori );
		
		while (false !== ( $data['filename'] = readdir( $data['daftar_file'] ))) {
			$ext_file = get_nfile_ext($data['filename']);
			#if ( substr( $data['filename'], -4) == '.xls' || substr( $data['filename'], -4) == '.XLS' || substr( $data['filename'], -5) == '.XLSX' || substr( $data['filename'], -5) == '.xlsx' ) 
			if(strtolower($ext_file)=='.xls' || strtolower($ext_file)=='.xlsx')
			{
				return $direktori;
			}
			
			if ( is_dir( $direktori.'/'.$data['filename'] )  && ( $data['filename']  != '.' && $data['filename'] != '..') ) 
			{
				$folder .= $data['filename'].'/';
				return $direktori.$folder;
			}
			
		}
			
		return 'failed';
	}
	
	function cek_file_source_to_tmp_db($dir,$tmp_tabel,$file_zip){
		$dir = rtrim($dir,"/");
        $mydir = opendir($dir);
        while(false !== ($file = readdir($mydir)))
		{
            if($file != "." && $file != "..")
			{
                @chmod($dir."/".$file, 0777);
                if(is_dir($dir."/".$file))
				{
                    chdir('.');
                    cek_file_source_to_tmp_db($dir."/".$file,$tmp_tabel,$file_zip);
                }
				else
				{
					$ex = explode('.',$file);
					$ext = '.'.$ex[(count($ex)-1)];
					$str = "INSERT INTO $tmp_tabel (nfile, ext, path, file_zip) VALUES 
							(E'".addslashes($file)."',E'".addslashes($ext)."',E'".addslashes($dir)."',E'".addslashes($file_zip)."')";
					pg_query($str);
                }
            }
        }
        closedir($mydir);
	}
	
	function insert_tmp_file_xls($tmp_tabel,$temp_services,$nama_zip){
		$return = "";
		$str_xls = "SELECT nfile, ext, path FROM $tmp_tabel WHERE lower(ext) in ('.xls', '.xlsx')";
		$qry_xls = pg_query($str_xls);
		$row = pg_num_rows($qry_xls);
		if($row>0){
			$temp_tabel1 = $temp_services;

			$sql_cre_temp = "
						DROP TABLE IF EXISTS " . $temp_tabel1 . ";
						CREATE TABLE " . $temp_tabel1 . "
						(
							nama_file text,
							nama_zip text,
							tot_data text,
							counter text,
							a	text,
							b	text,
							c	text,
							d	text,
							e	text,
							f	text,
							g	text,
							h	text,
							i	text,
							j	text
						)
						WITHOUT OIDS;
						";

			$qry_cre_temp = @pg_query($sql_cre_temp);
			while($dt = pg_fetch_array($qry_xls)){
				$file = $dt[0];
				$path = $dt[2];
				if(is_file(rtrim($path,'/')."/".$file)){
					insert_tmp_services($path,$file,$nama_zip,$temp_tabel1);
				}else{
					$return .= "File $file tidak ditemukan";
				}
			}
		}else{
			$return = "File .xls atau .xls tidak dapat ditemukan!!!";
		}
		
		return $return;
	}
	
	function insert_tmp_services($update_file_path,$file,$nama_zip,$temp_tabel1){
		$update_file_path = rtrim($update_file_path,'/')."/".$file;
		
		$error = '';
		ini_set('memory_limit', '-1');
		include_once '../include/phpexcel/PHPExcel/IOFactory.php';
		$objReader = PHPExcel_IOFactory::createReaderForFile($update_file_path); 
		$objPHPExcel = $objReader->load($update_file_path);
		$worksheetNames = $objPHPExcel->getSheetNames($update_file_path);
		
		$colinmust = array('A'=>'Unit', 'B'=>'File pdf', 'C'=>'Email', 'D'=>'Tgl STO', 'E'=>'Undangan 1', 
							'F'=>'Undangan 2', 'G'=>'Matauang', 'H'=>'Tagihan', 'I'=>'Periode pembayaran', 'J'=>'No VA');
		
		$str_qry = '';
		$seq = 0;
		$counter = 0;
		$counter_true = 0;
		$counter_false= 0;
		$banyak_file  = 0;
		$flag1		= '';
		$flag2		= '';
		$cardno_arr = array('0'=>'');
		$header_sql = "INSERT INTO $temp_tabel1 (a, b, c, d, e, f, g, h, i, j, nama_file, counter, nama_zip) VALUES";

		foreach($worksheetNames as $sheetName){
			$objPHPExcel->setActiveSheetIndexByName($sheetName);
			$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
			foreach($sheetData as $colData)
			{
				$seq++;
				$counter++;
				if($seq==1){
					$ss = '';
					foreach($colData as $keyCol => $valCol){
						if(!empty($colinmust[$keyCol])){
							$checkfield = strpos(strtoupper(trim($valCol)), strtoupper($colinmust[$keyCol]));
							if ($checkfield !== false){
								#unset($colinmust[$keyCol]);
							}else{
								$ss .= $keyCol.' => '.strtoupper($colinmust[$keyCol]).' => '.strtoupper($valCol).', ';
							}
						}
						$counter = 0;
					}
					if($ss!=''){
						echo "error header xls : ".rtrim($ss,', ');die();
					}
				}else{
					$str_inp = $header_sql."(E'".addslashes(trim_con($colData['A']))."', E'".addslashes(trim_con($colData['B']))."', 
					E'".addslashes(trim_con($colData['C']))."', E'".addslashes(trim_con($colData['D']))."', E'".addslashes(trim_con($colData['E']))."', 
					E'".addslashes(trim_con($colData['F']))."', E'".addslashes(trim_con($colData['G']))."', E'".addslashes(trim_con($colData['H']))."',
					E'".addslashes(trim_con($colData['I']))."', E'".addslashes(trim_con($colData['J']))."', '$file', '$counter', '$nama_zip')";
					#cek bila cif atau account number kosong maka tidak akan diinput
					if(trim_con($colData['A'])!='')$qry_inp = pg_query($str_inp) or die("ERROR Tmp lampiran : ".$str_inp);
				}
			}
		}
		
		return true;
	}
	
	function trim_con($data){
		#mengganti spasi value null dengan spasi biasa
		$data = str_replace(' ',' ',$data);
		#trim spasi kosong kiri kanan
		$data = trim($data);
		
		return $data;
	}
	
	function create_folder($php_path_file){
		$path = '';
		rtrim($php_path_file,'/');
		$ex = explode("/",$php_path_file);
		for($i=0;$i<count($ex);$i++){
			if(!empty($ex[$i])){
				if($i==0){
					#drive awal ../ ata C:/
					$path .= $ex[$i].'/';
				}else{
					$path .= $ex[$i].'/';
					if(file_exists($path)==false) mkdir($path);
				}
			}
		}
	}
	
	function cetak_pdf_password( $pdf_in, $pdf_out, $password='')
	{
		require_once('../include/tcpdf/config/lang/eng.php');
		require_once('../include/tcpdf/tcpdf.php');
		require_once('../include/FPDI/fpdi.php');

				
		$var_c='@ppD3v#';
		$var_c= random_numbers(8);#echo $var_c."<br>";
		$var_c=$password; #tidak ada owner pass hanya ada password yang diminta user saja
		
		$pdf = new FPDI('P', 'mm', 'A4'); //FPDI extends TCPDF

		$pages = $pdf->setSourceFile( $pdf_in );

		for ($i = 1; $i <= $pages; $i++) 
		{
			$pdf->AddPage();
			$page = $pdf->ImportPage($i);
			$pdf->useTemplate($page, 0, 0);
		}

		$pdf->SetProtection($permissions=array('modify','copy','assemble'), $password, $var_c, $mode=3, $pubkeys=null);
		$pdf->Output( $pdf_out ,'F');
		
		return $pages;
	}
	
	function copy_pdf($pdf, $pdf_in, $pdf_out, $password='')
	{

				
		#$var_c='@ppD3v#';
		#$var_c= random_numbers(8);#echo $var_c."<br>";
		#$var_c=$password; #tidak ada owner pass hanya ada password yang diminta user saja
		
		#$pdf = new FPDI('P', 'mm', 'A4'); //FPDI extends TCPDF
		$pages = $pdf->setSourceFile($pdf_in);

		#$pages = $pdf->setSourceFile( $pdf_in );

		/*for ($i = 1; $i <= $pages; $i++) 
		{
			$pdf->AddPage();
			$page = $pdf->ImportPage($i);
			$pdf->useTemplate($page, 0, 0);
		}

		$pdf->SetProtection($permissions=array('modify','copy','assemble'), $password, $var_c, $mode=3, $pubkeys=null);
		unset($pdf);*/
		#$pdf->Output( $pdf_out ,'F');
		if(!copy($pdf_in,$pdf_out)){
			echo "Gagal copy file : ".$pdf_in;die();
		}
		
		return $pages;
	}
	
	function random_numbers($digits)
	{
		$min=pow(10,$digits-1);
		$max=pow(10,$digits)-1;
		return mt_rand($min,$max);
	}
	
	pg_close($con);
?>