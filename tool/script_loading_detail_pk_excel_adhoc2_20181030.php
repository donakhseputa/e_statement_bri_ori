<?php require_once("../include/config.php"); ?>
<?php require_once("../include/session.php"); ?>
<?php require_once("../include/function.php"); ?>
<?php define('FPDF_FONTPATH','../include/fpdf/font/'); ?>
<?php require_once("../include/fpdf/fpdf.php"); ?>
<?php require_once("../include/fpdi/FPDI_Protection.php"); ?>
<?php
	/*require_once('../include/tcpdf/config/lang/eng.php');
	require_once('../include/tcpdf/tcpdf.php');*/
	require_once ('../include/tcpdf/tcpdf.php');
	require_once("../include/fpdi/fpdi.php");
	
	
	#die("Excel Adhoc");
?>

	
	
	
    
<?php
/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../include/Classes/');
/** PHPExcel_IOFactory */
include 'PHPExcel/IOFactory.php';

/** LOADING FILE DENGAN MENGGUNAKAN NOMOR REKENING (DISAMAKAN DGN TABEL m_customer) **/

	$pr = $_REQUEST['pr'];
	$arr_pr = explode('|',$pr);
	$flagtrans = $arr_pr[0];
	$menu_id = $arr_pr[1];
	$act = $arr_pr[2];
	$blth = $arr_pr[3];
	$file = $arr_pr[4];
	$filecust = $arr_pr[6];
	
	//die($pr);
	//echo($act);
	$con = pg_connect($connection) or die("Could not connect to database!");
	//$scon_group_nisp = "host=192.168.101.17 dbname=grouping_nispcard user=postgres password=edp321";
	$scon_group_nisp = "";
	$msg = "";
	$msg_not_exists = "";
	
	switch($act){
		case 'excel_to_pdf':
				$waktu_awal = strtotime(date("Y-m-d H:i:s"));
				//die ( 'konversi mulai' );
				$sql_cek_dat = "SELECT * FROM m_loading WHERE flagtrans = '$flagtrans' and blth = '$blth' and loading_file = '$file'";
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
	
	
	function excel_to_pdf($flagtrans,$menu_id,$blth,$file,$filecust,$con,$connection,$scon_group_nisp)
	{
		
		//create tabel
		
		$m_loading_id = m_loading($flagtrans,$blth,$file);
				$temp = 'temp_excel_'.$flagtrans;
				$drop = 'DROP TABLE IF EXISTS '.$temp ;
				pg_query ($drop);
				$buat_tabel_temp = 
					" CREATE TABLE $temp (
						id SERIAL,
					
					no text,
					no_ticket_2 text,
					card_number text,
					nama text,
					tgl_close_card text,
					no_surat text,
					tgl_surat text,
					alamat_1 text,
					alamat_2 text,
					alamat_3 text,
					alamat_4 text,
					alamat_5 text,
					cr_addr_email text,
					nama_pdf text,
					tipe_kartu text,
					kagen text,
					agen text,
						m_loading_id text,
						nama_file text
					)
					";
				
				$exe_tabel = pg_query($buat_tabel_temp)or die("ERROR: " . $buat_tabel_temp);
				
				
				$temp2 = 'temp_excel_all_saved';

					$buat_tabel_temp = 
						" 
						DROP TABLE IF EXISTS " . $temp2 . ";
						CREATE TABLE $temp2 (
						id SERIAL,
					
					no text,
					no_ticket_2 text,
					card_number text,
					nama text,
					tgl_close_card text,
					no_surat text,
					tgl_surat text,
					alamat_1 text,
					alamat_2 text,
					alamat_3 text,
					alamat_4 text,
					alamat_5 text,
					cr_addr_email text,
						nama_pdf text,
					tipe_kartu text,
						kagen text,
					agen text,
						m_loading_id text,
						nama_file text
						)
						";
				
					$exe_tabel = pg_query($buat_tabel_temp)or die("ERROR: " . $buat_tabel_temp);
				
				
				//die('temp');
				/******************************************
				CEK TABEL DETAIL<blth><flagtrans>
				******************************************/
				$blth_balik = substr($blth,-4).substr($blth,0,2);
			
				$BLTH_BARUS = BLTH_BARU;
				if($blth_balik>$BLTH_BARUS){
				$tabeldetail = strtolower("detail_".$blth."_".$flagtrans);
				$tabeldetail_pk = $tabeldetail."_p_k";
				$tabeldetail_un = $tabeldetail."_unique";
				$tabelindex = $tabeldetail."_index";
				
				$cek_tabel = "SELECT detail_id FROM $tabeldetail";
				$exe_tabel = @pg_query($cek_tabel);
				if(!$exe_tabel){
					
				$buat_tabel = "CREATE TABLE $tabeldetail(						
								 detail_id serial NOT NULL,
								  m_loading_id integer,
								  nomor_customer character varying(20),
								  nomor_rekening character varying(20),
								  nama character varying(60),
								  alamat1 character varying(150),
								  alamat2 character varying(150),
								  alamat3 character varying(150),
								  alamat4 character varying(150),
								  alamat5 character varying(150),
								  city character varying(40),
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
								  size_pdf numeric(6,2),
								  barcode text,
								  nama_produk text,
								  tanggal text,
								  total_produk text,
								  kode_cab character varying(10),
								  cabang character varying(50),
								  CONSTRAINT $tabeldetail_pk PRIMARY KEY (detail_id),
								  CONSTRAINT $tabeldetail_un UNIQUE (nomor_rekening, nama_file, blth)
								)WITHOUT OIDS;
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
		

		//$master_header = "No*No Ticket 2*Card_number*Nama*Tgl Close Card*Tgl Lunas_Bayar*No Surat*Tgl Surat*ALAMAT_1*ALAMAT_2*ALAMAT_3*ALAMAT_4*ALAMAT_5*CR_ADDR_EMAIL*";
		#$master_header = "No*No Ticket2*Card_number*Nama*Tgl Close Card*Tgl Lunas_Bayar*No Surat*Tgl Surat*ALAMAT_1*ALAMAT_2*ALAMAT_3*ALAMAT_4*ALAMAT_5*CR_ADDR_EMAIL*";
		###$master_header = "No*No Ticket2*Card_number*Nama*Tgl Close Card*No Surat*Tgl Surat*ALAMAT_1*ALAMAT_2*ALAMAT_3*ALAMAT_4*ALAMAT_5*CR_ADDR_EMAIL*Nama File convert PDF (Save File)*kagen*agen*";
		$master_header = "No*No Ticket2*Card_number*Nama*Tgl Close Card*No Surat*Tgl Surat*ALAMAT_1*ALAMAT_2*ALAMAT_3*ALAMAT_4*ALAMAT_5*CR_ADDR_EMAIL*Nama File convert PDF (Save File)*Tipe Kartu*KAGEN*AGEN*";
		
		$dirfile = '../temp_file/billing/'.$flagtrans.'/'.$blth.'/';
		$inputFileName = $dirfile.'/'.$file;  
			$no_urut = 0;
			try {
				$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
			} catch(Exception $e) {
				die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
			}
			/*echo '<hr />';
			echo "<pre>";
			*/
			
			
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
						#if ($cell == 'O' || $cell == 'P' || $cell == 'Q') continue;
						if ($cell == 'R' || $cell == 'S' || $cell == 'T') continue;
						
						
						if ($cell == 'W'){
							//$val	 = ubah_format_tanggal($val); // merubah ketuker tanggal bulan dan tahun
							$value[] = addslashes(str_replace( array('(',')'), '' , $val) ) ;
						}else if ($cell == 'N'){
							$value[] = str_replace(' ','_',$val);
						}else{
							$value[] = addslashes(str_replace( array('(',')'), '' , $val) ) ;
						}
						
						
					}
			
			
					$values = "('" . implode("',E'",$value) . "')";
					$values = str_replace(")",", '".$m_loading_id."', '".$file."')",$values);
					//$query = ("INSERT INTO $temp (NO, NO_REF, TGL_SURAT1, BLOCK_DATE1, NAMA_BASIC_KOP, NO_CARD_BASIC_x, NAMA_BASIC, NO_CARD_SUPP1_x, NAMA_SUPP1, NO_CARD_SUPP2_x, NAMA_SUPP2, NO_CARD_SUPP3_x, NAMA_SUPP3, NO_CARD_SUPP4_x, NAMA_SUPP4, JENIS_KARTU, EMGIL_GDDYEHH, GDDY_1, GDDY_2, GDDY_3, CITY, POSTAL_CODE, BLOCK_DATE,Flag_kurir, Flag_waybill, Flag_Surat , m_loading_id , nama_file 
					$query = ("INSERT INTO $temp (
					
					no ,
					no_ticket_2 ,
					card_number ,
					nama ,
					tgl_close_card ,
					no_surat ,
					tgl_surat ,
					alamat_1 ,
					alamat_2 ,
					alamat_3 ,
					alamat_4 ,
					alamat_5 ,
					cr_addr_email ,
					nama_pdf,
					tipe_kartu,
					kagen,
					agen
					,m_loading_id , nama_file 
					) VALUES " . $values) or die(pg_last_error());
				
					$qry_ins_cust = pg_query($query) or die("<br>GAGAL INSERT INTO $temp<br>".$query);
				//	echo $query .'<br>';
					
					$n_cust++;
				}else{
					//nama kolom
					foreach($columns as $cell => $val) 
					{
						if ($cell == 'R' || $cell == 'S' || $cell == 'T') continue;
						$kolom .= $val.'*';
						
					}
					
					//CEK PERBEDAAN HEADER
					$kolom=str_replace('-','',$kolom);
					if(strtoupper($kolom) != strtoupper($master_header) )
					{
						$sql_del = "DELETE FROM m_loading WHERE loading_file = '$file' ";
						$qry_del = pg_query($sql_del) or die("<br>GAGAL DELETE m_loading<br>".$sql_del);
						die("HEADER EXCEL SALAH<br><br>didata<br>$kolom<br><br>seharusnya<br>$master_header<br>");
					}
				}
				
			}
		
		//die($kolom);
		
				
				/*
				//DIMATIKAN CEK NO CIF
				//CEK DOUBLE NOMO CIF
				$sql_cek = "SELECT NO_REF, count(*) from $temp GROUP BY NO_REF HAVING COUNT(*) > 1";
				$qry_cek = pg_query($sql_cek) or die('ERROR cek Nomor REF Double: '.$sql_cek);
				$jml_double = pg_num_rows($qry_cek);
				if($jml_double > 0)
				{
					$sql_del = "DELETE FROM m_loading WHERE loading_file = '$file' ";
					$qry_del = pg_query($sql_del) or die("<br>GAGAL DELETE m_loading<br>".$sql_del);
					die('Nomor REF ada yg Double');
				}
				*/
	
	
	//die('insert detail oke');
	
	$jenis_pdf = 0;  //0 = untuk pecah per customer, 1 = untuk gabung per cycle
	copy_temp_to_all();
	###die('insert temporary oke');
	
	
	$n_total_cust = to_pdf($flagtrans,$blth,$file,$con,$temp,$jenis_pdf,$tabeldetail,$m_loading_id, $tanggal_nama_file);

	/* --------------------------------------------------------------------------- */
	/*
		//gabungkan temp1.PDF menjadi satu file
		$dirfile_all = '../pdf/'.$flagtrans.'/'.$blth.'/'.str_replace(array('.XLS','.xls','.xlx','.XLSX'),'', $file);
		$file_gabung = $dirfile_all.'/*.pdf';
		$file_gabung2 = $dirfile_all.'/';
		//$nama_file_gabung = str_replace('.XLS','', $file) .'_ALL'. '.pdf';
		$nama_file_gabung = str_replace(array('.XLS','.xls','.xlx','.XLSX'),'', $file) .'_ALL'. '.pdf';
        $gabungkan = 'C:/pdftk/bin/pdftk ' . $file_gabung . ' cat output ' . $dirfile_all .'/'. $nama_file_gabung;
		//die($gabungkan);
        exec($gabungkan);
	*/
	/* --------------------------------------------------------------------------- */
		
		//20150904 membuat csv dan zipnya
		create_csv($m_loading_id,$blth,$file,$flagtrans,$sql_cek,$tabeldetail);
		
		//BUAT FILE ZIP
		$path_zip = $blth_file_pdf;
		$loc_file_zip = $blth_file_pdf;
		$zip_pdf = $path_zip . str_replace(array(".xlsx",".XLSX",".TXT",".txt"),".zip", $file);
		#Zip($loc_file_zip,'./'.$zip_pdf.'.zip');
		//20150904 membuat csv dan zipnya
		
		
		return 'Convert Berhasil|'.$n_total_cust.'|0';
	}
	
	function fungsi_nama_file($tanggal_nama_file)
	{
		//20170208
		
		$x= substr($tanggal_nama_file,6,2).substr($tanggal_nama_file,4,2).substr($tanggal_nama_file,2,2);
		return $x;
	}
	function to_pdf($flagtrans,$blth,$file,$con,$temp,$jenis_pdf,$tabeldetail,$m_loading_id, $tanggal_nama_file='')
	{
		$sql_baca = "SELECT * FROM $temp ORDER BY id ASC" ;
		
		//die($sql_baca);
		$n_no_email = 0;
		$n_no_hardcopy = 0;
		$nx='';
		$record_not_exists_hardcoypy = '';
		$record_not_exists = '';
		
		
		$n=1;
		$nn=0;
		$qry = pg_query($sql_baca) or die('ERROR: '.$sql_baca);
		while($row = pg_fetch_array($qry)){
				$var_naik = 6;
			$x = 20;
			//$y = 42;
			$y = 60 -15 - 6 - $var_naik;
			$jeda_baris=3.5;
			//$password_pdf = convDate($tgl_lahir,'-');
			$nomor_customer			=  trim($row['no_ticket_2']);
			$nomor_rekening			=  trim($row['card_number']);
			
			$nama					=  trim($row['nama']);
			$tgl_close_card			=  trim($row['tgl_close_card']);
			$tgl_lunas_bayar		=  ''; //KEY NYA
			$no_surat				=  trim($row['no_surat']); 
			$tgl_surat				=  trim($row['tgl_surat']); 
			
			
			$address1				=  trim($row['alamat_1']);
			$address2				=  trim($row['alamat_2']);
			$address3				=  trim($row['alamat_3']);
			$city					=  trim($row['alamat_4']);
			$zipcode				=  trim($row['alamat_5']);
			
			
			$tipe_kartu					=  trim($row['tipe_kartu']);
			$posko					=  trim($row['kagen']);
			$kurir					=  trim($row['agen']);

			$nama_produk			=  (empty($tgl_lunas_bayar)) ? 'TUTUP KARTU' : 'TUTUP KARTU DAN SURAT LUNAS';
			$nama_produk = "$tipe_kartu $nama_produk";
			
			$password_pdf			=  '';
			
			
			
			$row['nama_pdf'] = trim($row['nama_pdf']);
			
			if ( empty(  $row['nama_pdf'] ) )
			{
				$nama_file_pdf			=  str_pad($n, 4, 0, STR_PAD_LEFT).'_'.$nomor_rekening;
			}else{
				$nama_file_pdf			=  trim($row['nama_pdf']);
			}
			
			$email					=  trim($row['cr_addr_email']);
			

			
			
						
						
			if ($jenis_pdf == 0) 
			{ 
				
				$pdf = new TCPDF('P', 'mm', 'A4');
                $pdf->SetAutoPageBreak(true, 7);
                $pdf->SetDisplayMode("real");
                $pdf->SetLeftMargin(0);
                $pdf->SetRightMargin(0);
                $pdf->SetTopMargin(0);
                $pdf->setPrintHeader(false);
                $pdf->setPrintFooter(false);		
				$pdf->AddPage();
				//$pdf->SetProtection($permissions=array('copy','modify','annot-forms','extract','assemble','fill-forms'), $user_pass=$password_pdf, $owner_pass='app123', $mode=1, $pubkeys=null);
				//$pdf->SetProtection($permissions=array('copy','modify','annot-forms','extract','assemble','fill-forms'), $user_pass=$password_pdf, $owner_pass='app123', $mode=1, $pubkeys=null);
				$fontname = "helvetica";
				
				$pdf->SetFont($fontname, '', 9.5);
				
				
				
				
				 $style = array(
                    
                    'text' => false
                );
				
				
				$fontsize1 = 9;
				$y = $y - 11;
				//-------------------------------------------------------------------------
				$pdf->SetFont('helvetica', '', $fontsize1);
				$pdf->SetXY($x, $y);
				$pdf->Cell(10, 10, 'No', 0, 0, "L");
				
				$pdf->SetXY($x+15, $y);
				$pdf->Cell(10, 10, ':', 0, 0, "L");
				
				$pdf->SetXY($x+20, $y);
				$pdf->Cell(10, 10, $no_surat, 0, 0, "L");
				
				
				//-------------------------------------------------------------------------
				$var_start_kanan2 = 105+52;
				$var_start_kanan = 0;
				
				$pdf->SetFont('helvetica', '', $fontsize1);
				$pdf->SetXY($x+$var_start_kanan2, $y);
				$pdf->Cell(10, 10, 'Jakarta, '.$tgl_surat, 0, 0, "R");
				
				//-------------------------------------------------------------------------
				
				$pdf->SetXY($x, $y+(1*$jeda_baris));
				$pdf->Cell(10, 10, 'Perihal', 0, 0, "L");
				
				$pdf->SetXY($x+15, $y+(1*$jeda_baris));
				$pdf->Cell(10, 10, ':', 0, 0, "L");
				
				$pdf->SetFont('helvetica', 'U', $fontsize1);
				$pdf->SetXY($x+20, $y+(1*$jeda_baris));
				$pdf->Cell(10, 10, 'Surat Keterangan Penutupan Kartu Kredit BRI', 0, 0, "L");
				
				#$pdf->SetFont('helvetica', 'U', $fontsize1);
				#$pdf->SetXY($x+20, $y+(2*$jeda_baris));
				#$pdf->Cell(10, 10, 'Kartu Kredit BRI', 0, 0, "L");
				
				//-------------------------------------------------------------------------
				$y = $y - 2;
				$pdf->SetFont('helvetica', '', $fontsize1);
				$pdf->SetXY($x+$var_start_kanan, $y+(3*$jeda_baris));
				$pdf->Cell(10, 10, 'Kepada Yth, Bapak/ Ibu ', 0, 0, "L");
				
				//-------------------------------------------------------------------------
				
				$pdf->SetFont('helvetica', 'B', $fontsize1);
				$pdf->SetXY($x+$var_start_kanan, $y+(4*$jeda_baris));
				$pdf->Cell(10, 10, strtoupper($nama), 0, 0, "L");
				
				$pdf->SetXY($x+$var_start_kanan, $y+(5*$jeda_baris));
				$pdf->Cell(10, 10, strtoupper($address1), 0, 0, "L");
				
				$pdf->SetXY($x+$var_start_kanan, $y+(6*$jeda_baris));
				$pdf->Cell(10, 10, strtoupper($address2), 0, 0, "L");
				
				$pdf->SetXY($x+$var_start_kanan, $y+(7*$jeda_baris));
				$pdf->Cell(10, 10, strtoupper($address3), 0, 0, "L");
				
				$pdf->SetXY($x+$var_start_kanan, $y+(8*$jeda_baris));
				$pdf->Cell(10, 10, strtoupper($city), 0, 0, "L");
				
				$pdf->SetXY($x+$var_start_kanan, $y+(9*$jeda_baris));
				$pdf->Cell(10, 10, strtoupper($zipcode), 0, 0, "L");
				
				$pdf->SetFont('helvetica', '', $fontsize1);
				//-------------------------------------------------------------------------
				$y = $y + 20;
				
				$pdf->SetFont('helvetica', '', $fontsize1);
				$pdf->SetXY($x, $y+(11*$jeda_baris) - 3);
				$pdf->Cell(10, 10, 'Dengan Hormat,', 0, 0, "L");
				
				//-------------------------------------------------------------------------
				
				/*
				$pdf->SetLeftMargin(20);
				$pdf->SetRightMargin(20);
				$pdf->SetXY($x, $y+(13*$jeda_baris) -3);
				$html = '<span style="text-align:left;">Sehubungan dengan adanya Program Penutupan Kartu Kredit BRI secara otomatis oleh Pihak Bank, bersama ini kami sampaikan, bahwa Kartu Kredit BRI atas nama : </span>';
				// output the HTML content
				$pdf->writeHTML($html, true, 0, true, true);
				*/
				
				$pdf->SetLeftMargin(20);
				$pdf->SetRightMargin(20);
				$pdf->SetXY($x, $y+(14*$jeda_baris) -3);
				$html = '<span style="text-align:justify;">Dengan ini kami informasikan bahwa Kartu Kredit BRI Bapak/Ibu belum diaktifkan selama lebih dari 12 bulan. Demi memberikan keamanan dan kenyamanan kepada Bapak/Ibu, bersama ini kami sampaikan bahwa Kartu Kredit BRI Bapak/Ibu dengan rincian:</span>';
				// output the HTML content
				$pdf->writeHTML($html, true, 0, true, true);
				
				//-------------------------------------------------------------------------
				
				$pdf->SetFont('helvetica', 'B', $fontsize1);
				$pdf->SetXY($x, $y+(16.5*$jeda_baris));
				$pdf->Cell(10, 10, 'Nama', 0, 0, "L");
				
				$pdf->SetXY($x+35, $y+(16.5*$jeda_baris));
				$pdf->Cell(10, 10, ':', 0, 0, "L");
				
				$pdf->SetFont('helvetica', 'B', $fontsize1);
				$pdf->SetXY($x+40, $y+(16.5*$jeda_baris));
				$pdf->Cell(10, 10, $nama, 0, 0, "L");
				
				
				$pdf->SetFont('helvetica', 'B', $fontsize1);
				$pdf->SetXY($x, $y+(17.5*$jeda_baris));
				$pdf->Cell(10, 10, 'No Kartu Kredit BRI', 0, 0, "L");
				
				$pdf->SetXY($x+35, $y+(17.5*$jeda_baris));
				$pdf->Cell(10, 10, ':', 0, 0, "L");
				
				$pdf->SetFont('helvetica', 'B', $fontsize1);
				$pdf->SetXY($x+40, $y+(17.5*$jeda_baris));
				$pdf->Cell(10, 10, $nomor_rekening, 0, 0, "L");
				
				
				$pdf->SetFont('helvetica', 'B', $fontsize1);
				$pdf->SetXY($x, $y+(18.5*$jeda_baris));
				$pdf->Cell(10, 10, 'Tipe Kartu', 0, 0, "L");
				
				$pdf->SetXY($x+35, $y+(18.5*$jeda_baris));
				$pdf->Cell(10, 10, ':', 0, 0, "L");
				
				$pdf->SetFont('helvetica', 'B', $fontsize1);
				$pdf->SetXY($x+40, $y+(18.5*$jeda_baris));
				$pdf->Cell(10, 10, $tipe_kartu, 0, 0, "L");
				
				$pdf->SetFont('helvetica', '', $fontsize1);
				
					
					$pdf->SetLeftMargin(20);
					$pdf->SetRightMargin(20);
					$pdf->SetXY($x, $y+(22*$jeda_baris) -1);
					$html = '<span style="text-align:justify;">Telah kami lakukan penutupan secara otomatis per tanggal <b>'.$tgl_close_card.'</b>. Adapun penutupan otomatis ini kami lakukan untuk memberikan rasa aman dan nyaman agar Bapak/Ibu terhindar dari risiko penyalahgunaan kartu kredit yang sudah terlalu lama tidak aktif  karena kartu hilang/ditemukan orang lain/digunakan oleh pihak lain tanpa sepengetahuan Bapak/Ibu.</span>';
				// output the HTML content
					$pdf->writeHTML($html, true, 0, true, true);
					
					
					
					$pdf->SetLeftMargin(20);
					$pdf->SetRightMargin(20);
					$pdf->SetXY($x, $y+(27*$jeda_baris));
					$html = '<span style="text-align:justify;">Namun, apabila setelah tanggal penutupan kartu masih terdapat tagihan transaksi, maka akan dilakukan klarifikasi dan penyelesaian sesuai dengan ketentuan Bank BRI. Sehubungan dengan hal tersebut, apabila masih terdapat fisik kartu sebagaimana dimaksud, mohon dapat segera digunting/dihancurkan/dimusnahkan.
					</span>';
					// output the HTML content
					$pdf->writeHTML($html, true, 0, true, true);
					
					
					$pdf->SetLeftMargin(20);
					$pdf->SetRightMargin(20);
					$pdf->SetXY($x, $y+(31*$jeda_baris));
					$html = '<span style="text-align:justify;">Apabila Bapak/Ibu masih berminat untuk menggunakan kembali Kartu Kredit BRI, Bapak/Ibu dapat melakukan pengajuan ulang sebagaimana pengajuan aplikasi Kartu Kredit BRI bagi nasabah baru.</span>';
				// output the HTML content
					$pdf->writeHTML($html, true, 0, true, true);
					
				
				//-------------------------------------------------------------------------
				
				
				$pdf->SetLeftMargin(20);
				$pdf->SetRightMargin(20);
				$pdf->SetXY($x, $y+(34*$jeda_baris));
				$html = '<span style="text-align:justify;">Demikian informasi yang dapat kami sampaikan, mohon agar surat ini dapat dipergunakan dengan semestinya. Terima kasih atas kepercayaan yang selama ini diberikan kepada Bank BRI.</span>';
				// output the HTML content
				$pdf->writeHTML($html, true, 0, true, true);
				
				//-------------------------------------------------------------------------
				
				
				$pdf->SetLeftMargin(20);
				$pdf->SetRightMargin(20);
				$pdf->SetXY($x, $y+(37*$jeda_baris));
				$html = '<span style="text-align:left;">Untuk informasi lebih lanjut hubungi Contact BRI 14017/1500017</span>';
				// output the HTML content
				$pdf->writeHTML($html, true, 0, true, true);
			
				
				//-------------------------------------------------------------------------
				//FOOOTER
				$v_turun = 3;
				
				$pdf->SetFont('helvetica', 'B', $fontsize1);
				$pdf->SetXY($x+120, $y+((36+$v_turun)*$jeda_baris));
				$pdf->Cell(10, 10, 'Jakarta, '.$tgl_surat, 0, 0, "C");

				$pdf->SetXY($x+120, $y+((37+$v_turun)*$jeda_baris));
				$pdf->Cell(10, 10, 'PT. Bank Rakyat  Indonesia (Persero) Tbk.', 0, 0, "C");
				
				$pdf->SetXY($x+120, $y+((38+$v_turun)*$jeda_baris));
				$pdf->Cell(10, 10, 'Card Center Division', 0, 0, "C");
				
				$pdf->SetXY($x+120, $y+((51.5+$v_turun)*$jeda_baris));
				$pdf->Cell(10, 10, 'General Manager', 0, 0, "C");
				
				
				$url_img_produk = '../images/barcode_n2.jpg';
				// Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)
				$pdf->Image($url_img_produk,$x+105,$y+((40+$v_turun)*$jeda_baris), '40', '', '', '', '', true, 300, '', false, false, 0, false, false, false);
				
				//FOOTER
				//-------------------------------------------------------------------------
				
				$y = $y + 20;
				
				$pdf->SetFont('helvetica', '', $fontsize1);
				$pdf->SetXY($x, $y+(52*$jeda_baris));
				$pdf->Cell(10, 10, '------------------------------------------------------------------------------------------------------------------------------------------------------------', 0, 0, "L");
				
				$pdf->SetFont('helvetica', 'I', $fontsize1);
				$pdf->SetXY($x+30, $y+(53*$jeda_baris));
				$pdf->Cell(10, 10, 'Surat ini tercetak secara otomatis dan tidak memerlukan tanda tangan', 0, 0, "L");
				
				
				
				//-------------------------------------------------------------------------------
				//HEADER
				$url_img_produk = '../images/logo_bri_n2.jpg';
				$pdf->Image($url_img_produk,17,10 - $var_naik, '20', '', '', '', '', true, 300, '', false, false, 0, false, false, false);
				
				
				$jeda_baris = 2;
				$font_atas = 5;
				$y = 6 - $var_naik ;
				$pdf->SetFont('helvetica', 'B', 14);
				$pdf->SetXY($x+80, $y+(1*$jeda_baris)-1);
				$pdf->Cell(10, 10, 'PT. BANK RAKYAT INDONESIA (PERSERO)', 0, 0, "C");

				$y = $y + 1;
				$pdf->SetFont('helvetica', 'B', 10);
				$pdf->SetXY($x+80, $y+(2*$jeda_baris));
				$pdf->Cell(10, 10, 'KANTOR PUSAT', 0, 0, "C");
				
				$y = $y + 0.5;
				
				$pdf->SetFont('helvetica', '', $font_atas);
				$pdf->SetXY($x+80, $y+(3*$jeda_baris));
				$pdf->Cell(10, 10, 'Jalan Jenderal Sudirman No. 44-46 Tromol Pos 1094 / 1000 Jakarta 10210', 0, 0, "C");
				
				$pdf->SetXY($x+80, $y+(4*$jeda_baris));
				$pdf->Cell(10, 10, 'Telepon : 2510244, 25100254, 2510264, 2510269, 2510279', 0, 0, "C");
				
				$pdf->SetXY($x+80, $y+(5*$jeda_baris));
				$pdf->Cell(10, 10, 'Facsimile : 2500065, 2500077  Kawat : KANPUSBRI', 0, 0, "C");
				
				$pdf->SetXY($x+80, $y+(6*$jeda_baris));
				$pdf->Cell(10, 10, 'Telex : 65293, 65301, 65456, 65459, 65461', 0, 0, "C");
				
				$pdf->SetXY($x+80, $y+(7*$jeda_baris));
				$pdf->Cell(10, 10, 'Website : www.bri.co.id', 0, 0, "C");
				
				$pdf->SetXY($x+80, $y+(8*$jeda_baris));
				$pdf->Cell(10, 10, 'Email : User_Id@bri.co.id', 0, 0, "C");
				
				$pdf->SetFont('helvetica', '', $fontsize1);
				//HEADER
				//-------------------------------------------------------------------------------
				
				
				
				
				
				
				$jml_hlm = 1;
				
				$size_pdf=output_pdf_2($pdf,$nama_file_pdf,$blth,$file,$flagtrans,$password_pdf);
				
				
				
				if ( empty($email) )
				{
					if ( empty($nomor_rekening) ) 
					{
						$n++;
						continue;
					}
					$record_not_exists .= $nomor_rekening .";". $nama .";".chr(13);
					$n_no_email++;
					$n++;
					###continue;
				}
			
			
			
				
				$n_email = empty($email) ? 0 : 1 ;
				$sql_ins_detail = "INSERT INTO $tabeldetail(
								m_loading_id,nomor_customer,nomor_rekening,
								nama,alamat1,alamat2,
								alamat3,city,zipcode,
								flagtrans,blth,nama_file,
								pdf_name,password_pdf,jml_hlm,
								nama_produk,tanggal,email,n_email,size_pdf
							)VALUES(
								$m_loading_id,'$nomor_customer','$nomor_rekening',
								'".addslashes($nama)."','".addslashes($address1)."','".addslashes($address2)."',
								'".addslashes($address3)."','$city','$zipcode',
								'$flagtrans','$blth','$file',
								'$nama_file_pdf','$password_pdf',$jml_hlm,
								'".addslashes($nama_produk)."','$tanggal_template','$email',$n_email,$size_pdf
							)";
					$qry_ins_detail = pg_query($sql_ins_detail) or die('ERROR insert into detail: '.$sql_ins_detail);
			
			} 
			$sql_update = "select count(*) as jumlah_customer,sum(jml_hlm) as total_hlm from $tabeldetail 
					WHERE m_loading_id = $m_loading_id ";

			$qry_update = pg_query($sql_update) or die('ERROR: '.$sql_update);
				while($row_update = pg_fetch_array($qry_update))
				{
					$jumlah_customer = $row_update['jumlah_customer'];
					$total_hlm = $row_update['total_hlm'];
				}
			
			update_m_loading($m_loading_id,$total_hlm,$jumlah_customer);
			
			$n++;
			$nn++;
		}
		
		
		if($n_no_email != 0) 
		{
			$folder_file		= trim( str_replace( array('.XLSX','.XLS' ) , '', $file )); 
			buat_csv_noexist($folder_file,$record_not_exists,'no_exist_email');
			//update_error($m_loading_id, 'no_email',$n_no_email);
			
			$loc_file_not_exist = '../tmp/noexist/';
			$nx.=  "<br> Data Email Not Exist: <a href='".$loc_file_not_exist."no_exist_email_".$folder_file.".txt"."' target='_blank'>"."no_exist_email_".$folder_file.".txt"."</a>";
			
		}
		
		if($n_no_hardcopy != 0) 
		{
			$folder_file		= trim( str_replace( array('.XLSX','.XLS' ) , '', $file )); 
			buat_csv_noexist($folder_file,$record_not_exists_hardcoypy,'no_exist_hardcopy');
			
			$loc_file_not_exist = '../tmp/noexist/';
			$nx.=  "<br> Data HARDCOPY: <a href='".$loc_file_not_exist."no_exist_hardcopy_".$folder_file.".txt"."' target='_blank'>"."no_exist_hardcopy_".$folder_file.".txt"."</a>";
			
		}
		
		copy_temp_to_log();
		
		return $nn.'|'.$nx;
		
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
	         
	function output_pdf($pdf,$nama_file_pdf,$blth,$file,$flagtrans,$password_pdf){
		//$file = str_replace(substr($file,-10),'',$file);
		$data = explode('.',$file);
		$file = str_replace(' ','',$data[0]);
		$blth_pdf = '../pdf/'.$flagtrans.'/';
		if(file_exists($blth_pdf)==false) mkdir($blth_pdf);
		$blth_pdf = $blth_pdf.$blth.'/';
		if(file_exists($blth_pdf)==false) mkdir($blth_pdf);
		$blth_file_pdf = $blth_pdf.$file.'/';
		if(file_exists($blth_file_pdf)==false) mkdir($blth_file_pdf);
		$pdf_output = $blth_file_pdf.$nama_file_pdf.'.pdf';
		$pdf_temp = $blth_file_pdf.'temp_'.$nama_file_pdf.'.pdf';
		$pdf_temp1 = $blth_file_pdf.'temp_1'.$nama_file_pdf.'.pdf';
		$pdf_tempx = $blth_file_pdf.'temp_x'.$nama_file_pdf.'.pdf';
		
		if(file_exists($pdf_output)){
			unlink($pdf_output);
		}
		$pdf->Output($pdf_temp,'F');
		
		$admin_pass ='123x';
		//$file_pdf_master = '../pdf/KT/master_kta.pdf';
		$file_pdf_master = '../pdf/KT/loan_mailer_20160406.pdf';
		$file_pdf_tambahan = '../pdf/KT/tambahan.pdf';
		
		$command = 'C:/pdftk/bin/pdftk ' . $file_pdf_master . ' multistamp '. $pdf_temp .' output ' . $pdf_temp1 ; 
		exec($command);

		$command_x = 'C:/pdftk/bin/pdftk ' . $pdf_temp1 . ' ' .$file_pdf_tambahan .' cat  output ' . $pdf_tempx ;
		exec($command_x);

		$command2 = 'C:/pdftk/bin/pdftk ' . $pdf_tempx . ' output ' . $pdf_output .' allow Printing owner_pw ' . $admin_pass . ' user_pw ' . $password_pdf; 
		exec($command2);
		
		if(file_exists($pdf_temp)){
			unlink($pdf_temp);
		}
		if(file_exists($pdf_temp1)){
			unlink($pdf_temp1);
		}
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
								'".addslashes($nama)."','".addslashes($address1)."','".addslashes($address2)."',
								'".addslashes($address3)."','$city','$zipcode',
								'$flagtrans','$blth','$nama_file',
								'$pdf_name','$password_pdf',$jml_hlm, '$tipe_kartunya','".addslashes($email)."','$n_email','$filesizePdf'
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
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	

	
	function Zip($source, $destination)
	{
		if (extension_loaded('zip') === true)
		{
			if (file_exists($source) === true)
			{
					$zip = new ZipArchive();
	
					if ($zip->open($destination, ZIPARCHIVE::CREATE) === true)
					{
							$source = realpath($source);							
							if (is_dir($source) === true)
							{									
									$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
	
									foreach ($files as $file)
									{
											$file = realpath($file);
											if($file2 == ""){$file1 = '';}else{$file1 = $file2. '';}
	
											if (is_dir($file) === true)
											{
													$file2 = basename($file);													
													$zip->addEmptyDir(str_replace($source . '/', '', $file2 . '/'));
											}
	
											if (is_file($file) === true)
											{			
													$file3 = basename($file);									
													$zip->addFromString($file1 . $file3, file_get_contents($file));
											}
									}
							}
	
							else if (is_file($source) === true)
							{
									$zip->addFromString(basename($source), file_get_contents($source));
							}
					}
	
					return $zip->close();
			}
		}
	
		return false;
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
					no_surat ,
					tgl_surat ,
					alamat_1 ,
					alamat_2 ,
					alamat_3 ,
					alamat_4 ,
					alamat_5 ,
					cr_addr_email ,
			   m_loading_id, nama_file
		  FROM temp_excel_pk;
		";
		$qry = pg_query($sql) or die('ERROR: '.$sql);
	}
	
	function buat_csv_noexist($nama_file,$isi, $f)
	{
			$folder_dest="../tmp/noexist/";
			$csv = $f."_" . $nama_file;
			$file_csv = $folder_dest.$csv.'.txt';
			$header = "NO_REF_".$f.";NAMA_".$f.";".chr(13);
			
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

INSERT INTO log_temp_excel_pk(
            no_ticket_2 ,
					card_number ,
					nama ,
					tgl_close_card ,
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
					no_surat ,
					tgl_surat ,
					alamat_1 ,
					alamat_2 ,
					alamat_3 ,
					alamat_4 ,
					alamat_5 ,
					cr_addr_email ,
					m_loading_id, nama_file
            FROM temp_excel_pk";
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
	
	pg_close($con);
?>