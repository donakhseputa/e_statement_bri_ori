<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/function.php"); ?>
<? define('FPDF_FONTPATH','../include/fpdf/font/'); ?>
<? require_once("../include/fpdf/fpdf.php"); ?>
<? require_once("../include/fpdi/FPDI_Protection.php"); ?>
<?php
	require_once('../include/tcpdf/config/lang/eng.php');
	require_once('../include/tcpdf/tcpdf.php');
	?>
<?
/** LOADING FILE DENGAN MENGGUNAKAN NOMOR REKENING (DISAMAKAN DGN TABEL m_customer) **/

	$pr = $_REQUEST['pr'];
	$arr_pr = explode('|',$pr);
	$flagtrans = $arr_pr[0];
	$menu_id = $arr_pr[1];
	$act = $arr_pr[2];
	$blth = $arr_pr[3];
	$file = $arr_pr[4];
	
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
					$msg = dat_to_pdf($flagtrans,$menu_id,$blth,$file);
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
		
		return $m_loading_id;
	}
	
	function log_customer($flagtrans,$blth,$file){
		$sql_sel_log_customer = "SELECT log_customer_id FROM log_customer WHERE blth = '$blth' AND flagtrans = '$flagtrans' ORDER BY log_customer_id DESC LIMIT 1";
		$qry_sel_log_customer = pg_query($sql_sel_log_customer) or die('ERROR select log_customer: '.$sql_sel_log_customer);
		$row_sel_log_customer = pg_fetch_array($qry_sel_log_customer);
		$log_customer_id = $row_sel_log_customer['log_customer_id'];
		
		return $log_customer_id;
	}
	
	function dat_to_pdf($flagtrans,$menu_id,$blth,$file){
	
		// Message
		$sql_message = "SELECT * FROM m_message ORDER BY m_message_id DESC LIMIT 1";
		$exe_message = pg_query($sql_message);
		$row_message = pg_fetch_array($exe_message);
		$message1 = $row_message['message1'];
		$message2 = $row_message['message2'];
		$message3 = $row_message['message3'];
		$message4 = $row_message['message4'];
								
		$x = 5;
		$y = 15;
		$id_bar = 0;
		$y_detail = $y+100;
		$customer = 0;
		$total_hlm = 0;
		$max_baris = 45;
		
		$strOwnPass = "SELECT pass_admin_pdf FROM m_folder WHERE flagtrans='".$flagtrans."' and status=TRUE";
		$qryOwnPass	= pg_query($strOwnPass)or die('Err: '.$strOwnPass);
		$rowOwnPass	= pg_fetch_array($qryOwnPass);
		$nOwnPass	= pg_num_rows($qryOwnPass);
		if($nOwnPass>0){
						$ownerpass=$rowOwnPass['pass_admin_pdf'];
		}
					
		$dirfile = '../temp_file/billing/'.$flagtrans.'/'.$blth.'/'.$file;
		
		
		
		/******************************************
		CEK TABEL DETAIL<blth><flagtrans>
		******************************************/
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
							) 
							WITHOUT OIDS;
							ALTER TABLE $tabeldetail OWNER TO postgres;";
			$exe_tabel = @pg_query($buat_tabel);
		}
		/*END CEK TABEL DETAIL*/
		
		
		
		
		
		$open_txt = fopen($dirfile,'r');
		$header_txt = trim(fgets($open_txt));
		$header_txt = substr($header_txt, -4, 4);
		
		$pdf =  new TCPDF('P', 'mm', 'A4');
		$pdf->SetFont('helvetica', '', 10);
		$pdf->SetAutoPageBreak(true, 1);
		$pdf->SetDisplayMode("real");
		$pdf->SetLeftMargin(0);
		$pdf->SetRightMargin(0);
		$pdf->SetTopMargin(0);
		$pdf->setPrintHeader(false);		
		$pdf->setPrintFooter(false);
		//$pdf->AddFont('helvetica','','helvetica.php');
		$password_pdf ='';
		//$pdf->SetProtection(array("print","change"), $password_pdf, "appdev123456");
		$pdf->SetProtection($permissions=array('modify','copy','assemble'), $user_pass='', $ownerpass, $mode=1, $pubkeys=null);
		
		$pdf->AddPage();
		$header = '!!!!';
		
		if(strtoupper($header_txt) ==  strtoupper($header)){
		
		/************************
		START
		************************/
		
		
		
		/***** INSERT m_loading *****/
		$m_loading_id = m_loading($flagtrans,$blth,$file);
		
		
		$c=0;
					
		$f = fopen($dirfile,'r');
		$ln= 0;
		$detail='';
		$no_urut = 1;
		$x_post = 32;
		
		
		while ($row_txt= trim(fgets ($f))) {
			++$ln;
			
			$x = 10;
			$y_next = 18;
			
			if ($ln == 1) 
			{
				$produk = str_replace('!!!!','',$row_txt);
				$pdf->SetFont("helvetica", "B", 9);	
				$pdf->SetXY($x, 18);
				$pdf->Cell(10, 10,($produk), 0, 0, "L");
				$y_next = $y_next + 3;
				cetak_kotak($pdf, $produk);
			}
			
			if ($ln == 2) 
			{
				$y_next = 21;
				$alamat_bank_1 = str_replace('!','',$row_txt);
				$pdf->SetFont("helvetica", "", 8);	
				$pdf->SetXY($x,$y_next);
				$pdf->Cell(10, 10,($alamat_bank_1), 0, 0, "L");
				$y_next = $y_next + 3;
				
			}
			if ($ln == 3) 
			{
				$y_next = 24;  
				$alamat_bank_2 = str_replace('!','',$row_txt);
				$pdf->SetXY($x,$y_next);
				$pdf->Cell(10, 10,($alamat_bank_2), 0, 0, "L");
				$y_next = $y_next + 3;
			}
			
			if ($ln == 4) 
			{
				$y_next = 27;
				$alamat_bank_3 = str_replace('!','',$row_txt);
				$pdf->SetXY($x,$y_next);
				$pdf->Cell(10, 10,($alamat_bank_3), 0, 0, "L");
				$y_next = $y_next + 3;
				
			}
			if ($ln == 5) 
			{
				$y_next = 30;
				$a = explode("!", $row_txt)	;
				$a1 = trim($a[1]);
				$a2 = trim($a[2]);
				$a3 = trim($a[3]);
				$phone_bank = 'Tel. '. $a1 .  ' Fax. '. $a2 ;
				$pdf->SetXY($x,$y_next);
				$pdf->Cell(10, 10,($phone_bank), 0, 0, "L");
				$y_next = $y_next + 7;
			
			}
		
		
			if ($ln == 6) 
			{
				$produk = strtoupper(str_replace('!','',$row_txt));
				$y_judul = 69;
				$x_judul = 105;
				$pdf->SetFont("helvetica", "B", 10);	
				$pdf->SetXY($x_judul,$y_judul);
				$pdf->Cell(10, 10,'UNIT TRUST REPORT ' .$produk, 0, 0, "C");				
			}
			if ($ln == 7) 
			{
				$posting = str_replace('!!!','',$row_txt);
				$posting = str_replace('!',' s/d ',$posting);
				$y_judul = $y_judul + 4;
				$x_judul = 105;
				$pdf->SetFont("helvetica", "", 8);	
				$pdf->SetXY($x_judul,$y_judul);
				$pdf->Cell(10, 10,'Posting Period : ' .$posting, 0, 0, "C");
				
			}
			if ($ln == 8) 
			{
				$agen = str_replace('!','',$row_txt);
				$y_agen = 77;
				$pdf->SetFont("helvetica", "", 8);	
				$pdf->SetXY($x,$y_agen);
				$pdf->Cell(10, 10,'Agen ID    :  ' .$agen, 0, 0, "L");
				
				
				
			}
			if ($ln == 9) 
			{
				$y_nama = 38;
				$nama = str_replace('!','',$row_txt);
				$pdf->SetXY($x + 5, $y_nama);
				$pdf->Cell(10, 10,"Kepada / To ", 0, 0, "L");
				$y_nama = $y_nama + 3;
				
				$pdf->SetFont("helvetica", "", 9);	
				$pdf->SetXY($x + 5,$y_nama);
				$pdf->Cell(10, 10,($nama), 0, 0, "L");
				$y_nama = $y_nama + 3;
			}
		
			if ($ln == 10) 
			{
				$alamat_1 = str_replace('!','',$row_txt);
				$a = explode(" ", $alamat_1);
				
			}
			if ($ln == 11) 
			{
				$y_nama = 47.5;
				$alamat_2 = str_replace('!','',$row_txt);
				$alamat_12 = $alamat_1."***". $alamat_2;
				$alamat_12 = str_replace('***',"\n",$alamat_12);
				
				$pdf->SetFont("helvetica", "", 9);	
				$pdf->SetXY($x + 5,$y_nama);
				$pdf->setCellHeightRatio(1.0);
				$pdf->MultiCell(75, 1, $alamat_12, 0, 'L',0,'');
				
			}
			if ($ln == 12) 
			{
				$y_nama = 58;
				$email = str_replace('!','',$row_txt);
				$pdf->SetFont("helvetica", "", 9);	
				$pdf->SetXY($x + 5,$y_nama);
				$pdf->Cell(10, 10,'E - mail : ' .$email, 0, 0, "L");
				$y_nama = $y_nama + 3;
				
			}
			
			
				
			if ($ln >= 13 and  $detail != 'akhir_proses') 
				
			{
				$b = explode("!", $row_txt);
				$b0 = trim($b[0]);
				$b1 = trim($b[1]);
				$b2 = trim($b[2]);
				$b3 = trim($b[3]);
				$b4 = trim($b[4]);
				$b5 = trim($b[5]);
				$b6 = trim($b[6]);
				$b7 = trim($b[7]);
				if (substr($b7,0,1) == '-')
					{
						$b7 = "(". str_replace('-','',$b7) . ")";
						
					}
				$b8 = trim($b[8]);
				$b9 = trim($b[9]);
				$b10 = trim($b[10]);
				
				if ($ln == 13 and $b2 == 'Saldo Awal') 
					{
						$detail = 'mulai_proses';
						$y_post = 92 ;
					}
					
				
				if ($b2 == 'Saldo Awal' or $b2 == 'Saldo Akhir' )
				{	
					$b3='';
					$b4='';
					$b5='';
					$b6='';
					$b7='';
					$b9='';
					$b10='';
				}
				
				//saldo awal
				$x0 = 10;
				$pdf->SetFont("helvetica", "", 6.2);	
				$pdf->SetXY($x0,$y_post);
				$pdf->Cell(10, 10,$b0, 0, 0, "L");
				
				$x1 = 22;
				
				$pdf->SetXY($x1,$y_post);
				$pdf->Cell(10, 10,$b1, 0, 0, "L");
				
				$x2 = 35;
				
				$pdf->SetXY($x2,$y_post);
				$pdf->Cell(10, 10,$b2, 0, 0, "L");
				
				$x3 = 53;
				
				$pdf->SetXY($x3,$y_post);
				$pdf->Cell(10, 10,$b3, 0, 0, "R");
				
				$x4 = 71;
				
				$pdf->SetXY($x4,$y_post);
				$pdf->Cell(10, 10,$b4, 0, 0, "R");
				
				$x5 = 92;
				
				$pdf->SetXY($x5,$y_post);
				$pdf->Cell(10, 10,$b5, 0, 0, "R");
				
				$x6 = 110;
				
				$pdf->SetXY($x6,$y_post);
				$pdf->Cell(10, 10,$b6, 0, 0, "R");
				
				$x7 = 131;
				
				$pdf->SetXY($x7,$y_post);
				$pdf->Cell(10, 10,$b7, 0, 0, "R");
				
				$x_post = 152;
				$pdf->SetX($x_post);
				$pdf->Cell(10, 10,$b8, 0, 0, "R");
				
				$x9 = 170;
				$pdf->SetX($x9);
				$pdf->Cell(10, 10,$b9, 0, 0, "R");
				
				$x10 = 190;
				$pdf->SetX($x10);
				$pdf->Cell(10, 10,$b10, 0, 0, "R");
				
				$y_post = $y_post + 2.5;
				
				if ($b2 == 'Saldo Akhir') 
					{
						
						$detail = 'akhir_proses';
						$line_akhir_detail = $ln;
						$style1 = array('width' => 0.2);
						$y_post = $y_post + 5;
						$y_akhir =  $y_post - 2 ;
						$pdf->Line(10, $y_post, 200, $y_post ,$style1);
					}
				
				
				if ($detail == 'akhir_proses')
				{
					
				}
			}
			
			else
			{
				if ($detail == 'akhir_proses')
				{
					
				    $b = explode("!", $row_txt);
					$b0 = trim($b[0]);
					$b1 = trim($b[1]);
					$b2 = trim($b[2]);
					$b3 = trim($b[3]);
					$b4 = trim($b[4]);
					$b5 = trim($b[5]);
					$b6 = trim($b[6]);
					$b7 = trim($b[7]);
					
					$b8 = trim($b[8]);
					$b9 = trim($b[9]);
					$b10 = trim($b[10]);
					
					
					$y_akhir = $y_akhir;
					$pdf->SetFont("helvetica", "", 7);	
					if ($ln == $line_akhir_detail + 1)
					{
						$x2 = 10;
						$y2 = $y_akhir;
						$pdf->SetXY($x2, $y2);
						$pdf->Cell(10, 10,"Unit Holder's Code", 0, 0, "L");
						
						$x3 = 67;
						$pdf->SetX($x3);
						$pdf->Cell(10, 10,"Total Aktiva Bersih / UP (" . $b1 .")", 0, 0, "L");
						
						
						$x4 = 148;
						$y2 = $y_post;
						$pdf->SetX($x4);
						$pdf->Cell(10, 10,"Unrealized P/(L )", 0, 0, "L");
						
						$x2a = 53;
						$pdf->SetX($x2a);
						$pdf->Cell(10, 10,$b0, 0, 0, "R");
						$noCard = $b0;
						//31122012
						$bulan_tahun = str_replace('/','',$b1);
						$bulan_tahun = substr($bulan_tahun,2,6);
						$nama_file_pdf = 'UTR-'.$bulan_tahun.'-'.$b0;
						
						$x6 = 125;
						$pdf->SetX($x6);
						$pdf->Cell(10, 10,$b2, 0, 0, "R");
						
						$x7 = 190;
						$pdf->SetX($x7);
						$pdf->Cell(10, 10,number_format($b3,2), 0, 0, "R");
						
						$y_akhir = $y_akhir + 2.5;
					}
					if ($ln == $line_akhir_detail + 2)
					{
						$x2 = 10;
						$y2 = $y_akhir;
						$pdf->SetXY($x2, $y2);
						$pdf->Cell(10, 10,"End Balance", 0, 0, "L");
						
						$x3 = 67;
						$pdf->SetX($x3);
						$pdf->Cell(10, 10,"Avg. Cost/Unit ", 0, 0, "L");
						
						
						$x4 = 148;
						$y2 = $y_post;
						$pdf->SetX($x4);
						$pdf->Cell(10, 10,"Realized P/(L )", 0, 0, "L");
						
						$x2a = 53;
						$pdf->SetX($x2a);
						$pdf->Cell(10, 10,$b0, 0, 0, "R");
						
						$x6 = 125;
						$pdf->SetX($x6);
						$pdf->Cell(10, 10,$b2, 0, 0, "R");
						
						$x7 = 190;
						$pdf->SetX($x7);
						$pdf->Cell(10, 10,($b3), 0, 0, "R");
						
						$y_akhir = $y_akhir + 5;
						$pdf->SetFont("helvetica", "I", 7);	
						$x2 = 10;
						$y2 = $y_akhir;
						$pdf->SetXY($x2, $y2);
					$pdf->Cell(10, 10,"1. We assume that the information provided above are correct if within one week we don't have any respons from you", 0, 0, "L");
						$y_akhir = $y_akhir + 3;
					 	
						$x2 = 10;
						$y2 = $y_akhir;
						$pdf->SetXY($x2, $y2);	
						$pdf->Cell(10, 10,"2. This is computer generated print out and no signature required", 0, 0, "L");

						
					}
				}
					
			}
			if ($row_txt == '@')
			{
				//$filetonames = $nama_file;

				$detail='';
				$filesizePdf = output_pdf($pdf,$nama_file_pdf,$blth,$file,$flagtrans);
				
				/**INSERT DETAIL**/
				insert_detail($m_loading_id,$noCard,$noCard,
				$nama,substr($alamat_1,0,39),substr($alamat_2,0,39),'','','00000',
				$flagtrans,$blth,$file,
				$nama_file_pdf,'','1',$email,$filesizePdf,'1');
				/**END INSERT DETAIL**/
				
				$ln = 0;
				$no_urut++;
				$pdf =  new TCPDF('P', 'mm', 'A4');
				$pdf->SetFont('helvetica', '', 10);
				$pdf->SetAutoPageBreak(true, 1);
				$pdf->SetDisplayMode("real");
				$pdf->SetLeftMargin(0);
				$pdf->SetRightMargin(0);
				$pdf->SetTopMargin(0);
				$pdf->setPrintHeader(false);		
				$pdf->setPrintFooter(false);
				$pdf->AddFont('helvetica','','helvetica.php');
				$pdf->AddPage();
				$password_pdf ='';
				$pdf->SetProtection($permissions=array('modify','copy','assemble'), $user_pass='', "appdev123456", $mode=1, $pubkeys=null);
			}
			
		}
		
		/************************
		END
		************************/
		
		
		update_m_loading($m_loading_id,$no_urut-1,$no_urut-1);
		
		}else{
			$msg_error = "ERROR: header file txt salah. Seharusnya <br>".$header."<br>Sementara Header File " .$filetoname . ":<br>".$header_txt;
		}
		return $msg_error.'|'.($no_urut-1).'|'.$msg_not_exists;
	}
	
	
	
	
	
	
	/*******************************
	FUNGSI TAMBAHAN
	*******************************/
	
	function cetak_kotak($pdf,$produk)
		{
			
			
			if ($produk == 'PG ASSET MANAGEMENT')
			{
				$x_produk = 10;
				$y_produk = 5;
				$url_img_produk = '../images/logo_PG_asset_management.jpg';
				$pdf->Image($url_img_produk,$x_produk,$y_produk, '30', '', 'JPG', '', '', true, 600, '', false, false, 0, false, false, false);
			}
			
			if ($produk == 'PT PANIN ASSET MANAGEMENT')
			{
				$x_produk = 10;
				$y_produk = 11;
				$url_img_produk = '../images/logo_panin asset management.jpg';
				$pdf->Image($url_img_produk,$x_produk,$y_produk, '84', '', 'JPG', '', '', true, 600, '', false, false, 0, false, false, false);
			}
			
			if ($produk == 'PT MNC ASSET MANAGEMENT')
			{
				$x_produk = 10;
				$y_produk = 5;
				$url_img_produk = '../images/logo_PG_MNC_asset_management_new.jpg';
				$pdf->Image($url_img_produk,$x_produk,$y_produk, '65', '', 'JPG', '', '', true, 600, '', false, false, 0, false, false, false);
			}
			// Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)
			
			
			$x_image = 146.5;
			$y_image = 4;
			
			$url_img = '../images/reksadana3.jpg';
			//$pdf->Image($url_img,$x_image,$y_image);
			$pdf->Image($url_img,$x_image,$y_image, '40', '', 'JPG', '', '', true, 300, '', false, false, 0, false, false, false);
				
			//$x = 10;
			//$y_next = 18;
			
				$x_bca = 147;
				$y_bca = 18;
				$pdf->SetFont("helvetica", "B", 9);	
				$pdf->SetXY($x_bca,$y_bca);
				$pdf->Cell(10, 10,'PT. Bank Central Asia Tbk.', 0, 0, "L");			    
				$y_bca = $y_bca + 3;
				
				$pdf->SetFont("helvetica", "", 8);	
				$pdf->SetXY($x_bca,$y_bca);
				$pdf->Cell(10, 10,'CUSTODIAN SERVICES', 0, 0, "L");			    
				$y_bca = $y_bca + 3;
				
				$pdf->SetXY($x_bca,$y_bca);
				$pdf->Cell(10, 10,'Menara BCA, Grand Indonesia Lt. 28', 0, 0, "L");			    
				$y_bca = $y_bca + 3;
				
				$pdf->SetXY($x_bca,$y_bca);
				$pdf->Cell(10, 10,'Jl. M.H. Thamrin No. 1', 0, 0, "L");			    
				$y_bca = $y_bca + 3;
				
				$pdf->SetXY($x_bca,$y_bca);
				$pdf->Cell(10, 10,'Jakarta 10310', 0, 0, "L");			    
				$y_bca = $y_bca + 3;

				$pdf->SetXY($x_bca,$y_bca);
				$pdf->Cell(10, 10,'Tel. 62 21 235 88 665 Fax. 62 21 235 88 374', 0, 0, "L");			    
				$y_bca = $y_bca + 3;
			
				//Garis
			    $y_agen = 77;
				$style1 = array('width' => 0.2);
				$pdf->Line(10, $y_agen+7.5, 200, $y_agen+7.5,$style1);
				$pdf->Line(43, $y_agen+11, 200, $y_agen+11,$style1);
				$pdf->Line(10, $y_agen+17, 200, $y_agen+17,$style1);
				
				$x_judul_transaksi = 84;
				$y_judul_transaksi = 81.5 ;
				$pdf->SetFont("helvetica", "", 8);	
				$pdf->SetXY($x_judul_transaksi,$y_judul_transaksi);
				$pdf->Cell(10, 10,'TRANSACTION', 0, 0, "C");
				
				$x_judul_saldo = 165;
				$y_judul_saldo = 81.5 ;
				$pdf->SetFont("helvetica", "", 8);	
				$pdf->SetXY($x_judul_saldo,$y_judul_saldo);
				$pdf->Cell(10, 10,'SALDO', 0, 0, "C");
				
				$x_post = 10;
				$y_post = 85 ;
				$pdf->SetFont("helvetica", "", 7);	
				$pdf->SetXY($x_post,$y_post);
				$pdf->Cell(10, 10,'Post', 0, 0, "L");
				$pdf->SetXY($x_post,$y_post+2);
				$pdf->Cell(10, 10,'Date', 0, 0, "L");
				
				$x_post = 22;
				$y_post = 85 ;
				$pdf->SetFont("helvetica", "", 7);	
				$pdf->SetXY($x_post,$y_post);
				$pdf->Cell(10, 10,'NAB', 0, 0, "L");
				$pdf->SetXY($x_post,$y_post+2);
				$pdf->Cell(10, 10,'Date', 0, 0, "L");
				
				$x_post = 34;
				$y_post = 85 ;
				$pdf->SetFont("helvetica", "", 7);	
				$pdf->SetXY($x_post,$y_post);
				$pdf->Cell(10, 10,'Desc', 0, 0, "L");
				
				$x_post = 47;
				$y_post = 85 ;
				$pdf->SetFont("helvetica", "", 7);	
				$pdf->SetXY($x_post,$y_post);
				$pdf->Cell(10, 10,'Gross', 0, 0, "L");
				$x_post = 43;
				$pdf->SetXY($x_post,$y_post+2.5);
				$pdf->Cell(10, 10,'Transaction Value', 0, 0, "L");
				
				$x_post = 65;
				$y_post = 85 ;
				$pdf->SetFont("helvetica", "", 7);	
				$pdf->SetXY($x_post,$y_post);
				$pdf->Cell(10, 10,'Fee/Charges', 0, 0, "L");
				
				$x_post = 87;
				$y_post = 85 ;
				$pdf->SetFont("helvetica", "", 7);	
				$pdf->SetXY($x_post,$y_post);
				$pdf->Cell(10, 10,'Net', 0, 0, "L");
				$x_post = 80;
				$pdf->SetXY($x_post,$y_post+2.5);
				$pdf->Cell(10, 10,'Transaction Value', 0, 0, "L");
				
				$x_post = 106;
				$y_post = 85 ;
				$pdf->SetFont("helvetica", "", 7);	
				$pdf->SetXY($x_post,$y_post);
				$pdf->Cell(10, 10,'Unit Price', 0, 0, "L");
				$x_post = 109;
				$pdf->SetXY($x_post,$y_post+2.5);
				//$pdf->Cell(10, 10,'Rp.', 0, 0, "L");
				
				$x_post = 128;
				$y_post = 85 ;
				$pdf->SetFont("helvetica", "", 7);	
				$pdf->SetXY($x_post,$y_post);
				$pdf->Cell(10, 10,'Unit', 0, 0, "L");
				$x_post = 126;
				$pdf->SetXY($x_post,$y_post+2.5);
				$pdf->Cell(10, 10,'Quantity', 0, 0, "L");
				
				$x_post = 144;
				$y_post = 85 ;
				$pdf->SetFont("helvetica", "B", 7);	
				$pdf->SetXY($x_post,$y_post);
				$pdf->Cell(10, 10,'Unit Balance', 0, 0, "L");
				$x_post = 146;
				$pdf->SetXY($x_post,$y_post+2.5);
				$pdf->Cell(10, 10,'Quantity', 0, 0, "L");
				
				$x_post = 162;
				$y_post = 85 ;
				$pdf->SetFont("helvetica", "", 7);	
				$pdf->SetXY($x_post,$y_post);
				$pdf->Cell(10, 10,'Avg. Cost/Unit', 0, 0, "L");
				$x_post = 169;
				$pdf->SetXY($x_post,$y_post+2.5);
				//$pdf->Cell(10, 10,'Rp.', 0, 0, "L");
				
				$x_post = 184;
				$y_post = 85 ;
				$pdf->SetFont("helvetica", "", 7);	
				$pdf->SetXY($x_post,$y_post);
				$pdf->Cell(10, 10,'Realized P/L', 0, 0, "L");
				
		}
	
	/*******************************
	FUNGSI TAMBAHAN
	*******************************/
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function output_pdf($pdf,$nama_file_pdf,$blth,$file,$flagtrans){
		$file = str_replace(substr($file,-4),'',$file);
		$blth_pdf = '../pdf/'.$flagtrans.'/';
		if(file_exists($blth_pdf)==false) mkdir($blth_pdf);
		$blth_pdf = $blth_pdf.$blth.'/';
		if(file_exists($blth_pdf)==false) mkdir($blth_pdf);
		$blth_file_pdf = $blth_pdf.$file.'/';
		if(file_exists($blth_file_pdf)==false) mkdir($blth_file_pdf);
		$pdf_output = $blth_file_pdf.$nama_file_pdf.'.pdf';
		if(file_exists($pdf_output)){
			unlink($pdf_output);
		}
		$pdf->Output($pdf_output,'F');
		$filesizePdf = fsize($pdf_output);
		return $filesizePdf;
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
								$nama,$address1,$address2,
								$address3,$city,$zipcode,
								$flagtrans,$blth,$nama_file,
								$pdf_name,$password_pdf,$jml_hlm,$email,$filesizePdf,$totalemailcustomernya){

		$n_email = substr_count($email,";");
		$n_email = $totalemailcustomernya;
		$n_email = 1;
		$tabeldetail = "detail_" . $blth . "_" . $flagtrans;
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
								'$pdf_name','$password_pdf','$jml_hlm', '$tipe_kartunya','$email','$n_email','$filesizePdf'
							)";
		$qry_ins_detail = pg_query($sql_ins_detail) or die('ERROR insert into detail: '.$sql_ins_detail);
		if(pg_affected_rows($qry_ins_detail)==0){
			echo 'GAGAL INSERT DETAIL UNTUK $nomor_rekening';
			die();
		}
	}
	
	function update_m_loading($m_loading_id,$total_hlm,$customer){
	// Message
		$sql_message = "SELECT m_message_id FROM m_message ORDER BY m_message_id DESC LIMIT 1";
		$exe_message = pg_query($sql_message);
		$row_message = pg_fetch_array($exe_message);
		$m_message_id = $row_message['m_message_id'];
		
		$sql_upd_m_load = "UPDATE m_loading
							SET
								status = 'true',
								total_halaman = '$total_hlm',
								total_customer = '$customer'
							WHERE m_loading_id = '$m_loading_id'";
		$qry_upd_m_load = pg_query($sql_upd_m_load) or die('ERROR update m_loading: '.$sql_upd_m_load);
	}
	
	function update_log_customer($log_customer_id,$customer){
		$sql_upd_log_customer = "UPDATE log_customer
								SET
									total_customer = '$customer'
								WHERE log_customer_id = '$log_customer_id'";
		$qry_upd_log_customer = pg_query($sql_upd_log_customer) or die('ERROR update log_customer: '.$sql_upd_log_customer);
	}
	
	
	
	
	
	
	function create_csv($m_loading_id,$blth,$file,$flagtrans,$sql_cek){
		$csv = str_replace(substr($file,-10),'',$file);
		
		$file_csv = '../pdf/'.$flagtrans.'/'.$blth.'/'.$csv.'/'.$csv.'.csv';
		$header = "'nomor_rekening';'nama';'pdf_name';'password_pdf';".chr(13);
		$detail = "";
		
		$sql = "SELECT nomor_rekening,nama,pdf_name,password_pdf FROM detail WHERE m_loading_id = $m_loading_id";
		$qry = pg_query($sql) or die('ERROR: '.$sql);
		while($row = pg_fetch_array($qry)){
			$nomor_rekening = $row['nomor_rekening'];
			$nama = $row['nama'];
			$pdf_name = $row['pdf_name'].'.pdf';
			#$password_pdf = $row['password_pdf']; //Pada 03062013 diganti menggunakan 6 digit terakhir nomor kartu kredit
			$password_pdf = substr($nomor_rekening,-6);
			
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
	
	pg_close($con);
?>