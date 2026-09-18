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
			$exe_tabel = @pg_query($buat_tabel)or die("ERROR CREATE TABLE: " . $sql_tabel);
		}
		/*END CEK TABEL DETAIL*/
		
		
		/************************
		START
		************************/
		
		
		
		/***** INSERT m_loading *****/
		$m_loading_id = m_loading($flagtrans,$blth,$file);
		

		$open_txt = fopen($dirfile,'r');
		$header_txt = trim(fgets($open_txt));
		$header_txt = substr($header_txt, 0, 1);
		
		$pdf =  new TCPDF('P', 'mm', 'A4');
		$pdf->SetFont('times', '', 10);
		$pdf->SetAutoPageBreak(true, 1);
		$pdf->SetDisplayMode("real");
		$pdf->SetLeftMargin(0);
		$pdf->SetRightMargin(0);
		$pdf->SetTopMargin(0);
		$pdf->setPrintHeader(false);		
		$pdf->setPrintFooter(false);
		$pdf->SetProtection($permissions=array('modify','copy','assemble'), $user_pass='', $ownerpass, $mode=1, $pubkeys=null);
		
		$pdf->AddPage();	
        $header = '!';
		if(strtoupper($header_txt) !=  strtoupper($header))
		{ 
			$msg = "ERROR: header file txt salah. Seharusnya <br>".$header."<br>Sementara Header File " .$filetoname . ":<br>".$header_txt;
			die($msg);
		}
		
		$x = 18;
		$y_next = 40;
					
		$f = fopen($dirfile,'r');
		$ln= 0;
		$detail='';
		$no_urut = 1;
		
		while ($row_txt= trim(fgets ($f))) {
			++$ln;
			
			//echo $row_txt . "<br>";
			if ($ln == 1)
			{
				$pdf->SetFont("times", "B", 10);
				$pdf->SetXY($x, $y_next);
				$prod = str_replace('!', '', $row_txt);
				$pdf->Cell(10, 10,($prod), 0, 0, "L");
				$y_next = $y_next + 4;
				
				if ($prod == 'PG INDEKS BISNIS-27')
				{
					$x_produk = 18;
					$y_produk = 16;
					$url_img_produk = '../images/logo_PG_asset_management.jpg';
					$pdf->Image($url_img_produk,$x_produk,$y_produk, '40', '', 'JPG', '', '', true, 600, '', false, false, 0, false, false, false);
				}
				
				if ($prod == 'MNC DANA LANCAR')
				{
					$x_produk = 18;
					$y_produk = 16;
					$url_img_produk = '../images/logo_PG_MNC_asset_management_new.jpg';
					$pdf->Image($url_img_produk,$x_produk,$y_produk, '60', '', 'JPG', '', '', true, 600, '', false, false, 0, false, false, false);
				}
			
				if ($prod == 'PANIN DANA PRIMA')
				{
					$x_produk = 18;
					$y_produk = 21;
					$url_img_produk = '../images/logo_panin asset management.jpg';
					$pdf->Image($url_img_produk,$x_produk,$y_produk, '84', '', 'JPG', '', '', true, 300, '', false, false, 0, false, false, false);
				}

				$pdf->SetFont("times", "B", 10);
				$pdf->SetXY($x, $y_next);
				$teks1 = 'Kepada';
				$pdf->Cell(10, 10,($teks1), 0, 0, "L");
				
				$pdf->SetFont("times", "I", 10);
				$pdf->SetXY($x + 13, $y_next);
				$teks1a = '/To';
				$pdf->Cell(10, 10,($teks1a), 0, 0, "L");

				$y_next = $y_next + 4;

				$x_image = 146.5;
				$y_image = 14;
			
				$url_img = '../images/reksadana3.jpg';
//				$url_img = '../images/reksa_dana_big.jpg';
				//$pdf->Image($url_img,$x_image,$y_image);
				$pdf->Image($url_img,$x_image,$y_image, '40', '', 'JPG', '', '', true, 300, '', false, false, 0, false, false, false);
			}
			
			if ($ln == 2)
			{
				$pdf->SetFont("times", "", 10);
				$pdf->SetXY($x, $y_next);
				$nama = str_replace('!', '', $row_txt);
				$pdf->Cell(10, 10,($nama), 0, 0, "L");
				$y_next = $y_next + 3;
			}
			
			if ($ln == 3)
			{
				$alm1 = str_replace('!', '', $row_txt);
			}
			
			if ($ln == 4)
			{
				$y_next = 55;
				$alm2 = str_replace('!', '', $row_txt);
				$alm12 = $alm1."***". $alm2;
				$alm12 = str_replace('***', "\n", $alm12);

			}
			
			if ($ln == 5)
			{
				$emai = str_replace('!', '', $row_txt);
				$y_next = 55;
				
				
				$alm1_all =  $alm12."\n".'Email : '.$emai;
				$pdf->SetFont("times", "", 10);
				$pdf->Ln(0.5);
				$pdf->SetXY($x, $y_next);
				$pdf->setCellHeightRatio(1.1);
				$pdf->MultiCell(75, 0.1, $alm1_all, 0, 'L', 0, '');
				
				
				$y_next = $y_next + 10;
				$pdf->SetFont("helvetica", "B", 12);
				$pdf->SetXY($x + 78, $y_next);
				$teks3 = 'Surat Konfirmasi';
				$pdf->Cell(10, 20,($teks3), 0, 0, "C");
				$y_next = $y_next + 3; 
				
				$pdf->SetFont("helvetica", "IB", 12);
				$pdf->SetXY($x + 62, $y_next+1);
				$teks4 = 'Confirmation Letter';
				$pdf->Cell(10, 21,($teks4), 0, 0);
				$y_next = $y_next + 3;
				
				$pdf->SetFont("times", "B", 10);
				$pdf->SetXY($x, $y_next);
				$teks5 = 'Manager Investasi';
				$pdf->Cell(10, 35,($teks5), 0, 0);
				
				$pdf->SetFont("times", "I", 10);
				$pdf->SetXY($x + 29, $y_next);
				$teks5a = '/Fund Manager';
				$pdf->Cell(10, 35,($teks5a), 0, 0);
				
				$pdf->SetFont("times", "B", 10);
				$pdf->SetXY($x + 100, $y_next);
				$teks6 = 'Agen Penjual';
				$pdf->Cell(10, 35,($teks6), 0, 0);
				
				$pdf->SetFont("times", "I", 10);
				$pdf->SetXY($x + 121, $y_next);
				$teks6a = '/Selling Agent';
				$pdf->Cell(10, 35,($teks6a), 0, 0);
				
				$y_next = $y_next + 4;
			}
			
			if ($ln == 6)
			{
				$pdf->SetFont("times", "", 10);
				$pdf->SetXY($x, $y_next);
				$minv = explode("!", $row_txt);
				$pdf->Cell(10, 35,($minv[0]), 0, 0);
				
				$pdf->SetXY($x + 100, $y_next);
				$minv = explode("!", $row_txt);
				$pdf->Cell(10, 35,($minv[3]), 0, 0); 
				$y_next = $y_next + 3;
			}
			
			if ($ln == 7)
			{
				$ali1 = explode("!", $row_txt);
				$almat_mi_all = $ali1[0] . "\n";
				$almat_agen_all = $ali1[3] . "\n";
			}
			
			if ($ln == 8)
			{
				$ali2 = explode("!", $row_txt);
				$alm_mi_2 = $ali2[0];
				$alm_agen_2 = $ali2[3];
				if(strlen($alm_mi_2)>0){
					$almat_mi_all = $almat_mi_all.$alm_mi_2."\n";
				}
				if(strlen($alm_agen_2)>0){
					$almat_agen_all = $almat_agen_all.$alm_agen_2."\n";
				}
			}
			
			if ($ln == 9)
			{
				$ali3 = explode("!", $row_txt);
				$alm_mi_3 = $ali3[0];
				$alm_agen_3 = $ali3[3];
				if(strlen($alm_mi_3)>0){
					$almat_mi_all = $almat_mi_all .$alm_mi_3."\n";
				}
				if(strlen($alm_agen_3)>0){
					$almat_agen_all = $almat_agen_all .$alm_agen_3."\n";
				}
			}
			
			if ($ln == 10)
			{
				
				$telp = explode("!", $row_txt);
				$telpon = 'Telp : ' .' '. $telp[1] . '  Fax : '. $telp[2] ;
				$telpon_agen = 'Telp : ' .' '. $telp[3] . '  Fax : '. $telp[4] ;
				$almat_mi_all = $almat_mi_all .$telpon ;
				$almat_agen_all = $almat_agen_all .$telpon_agen ;
				
				$y_next = $y_next + 17;
				$pdf->SetXY($x + 100, $y_next);
				$pdf->setCellHeightRatio(1);
				$pdf->MultiCell(85, 0.1, $almat_agen_all, 0, 'L', 0, '');
				
				$pdf->SetXY($x, $y_next);
				$pdf->setCellHeightRatio(1);
				$pdf->MultiCell(85, 0.1, $almat_mi_all, 0, 'L', 0, '');

				
			}
			/*
			if ($ln == 11)
			{
				$y_next = $y_next - 6;
			    $y_agen = 110;
				$style1 = array('width' => 0.2);
				$pdf->Line(10, $y_agen+8, 200, $y_agen+8,$style1);

				$pdf->SetFont("times", "", 10);
				$pdf->SetXY($x, $y_next);
				$teks10 = 'CIF';
				$pdf->Cell(10, 75,($teks10), 0, 0);

				$pdf->SetXY($x + 50, $y_next);
				$teks11 = ':';
				$pdf->Cell(10, 75,($teks11), 0, 0);
				
				$pdf->SetXY($x + 54, $y_next);
				$cif = str_replace('!', '', $row_txt);
				$pdf->Cell(10, 75,($cif), 0, 0);
				
				$y_next = $y_next + 10;
			}
			*/
			if ($ln == 12)
			{
			$y_next = $y_next - 6;
			    $y_agen = 110;
				$style1 = array('width' => 0.2);
				$pdf->Line(10, $y_agen+8, 200, $y_agen+8,$style1);
				
				$pdf->SetFont("times", "", 10);
				$pdf->SetXY($x, $y_next);
				$teks10 = 'Nomor Rekening Reksa Dana';
				$pdf->Cell(10, 75,($teks10), 0, 0);
				
				
				$pdf->SetXY($x + 50, $y_next);
				$pdf->Cell(10, 75,($teks11), 0, 0);
				
				$pdf->SetXY($x + 54, $y_next);
				$det2 = str_replace('!', '', $row_txt);
				$pdf->Cell(10, 75,($det2), 0, 0);
				$noRek = $det2;
				
				$y_next = $y_next + 4;
				
				$pdf->SetFont("times", "I", 10);
				$pdf->SetXY($x, $y_next);
				$teks12 = 'Account No.';
				$pdf->Cell(10, 75,($teks12), 0, 0);
				
				$y_next = $y_next + 6;
			}

			if ($ln == 13)
			{
				$pdf->SetFont("times", "", 10);
				$pdf->SetXY($x, $y_next);
				$teks13 = 'Nomor Rekening MI';
				$pdf->Cell(10, 75,($teks13), 0, 0);

				$pdf->SetXY($x + 50, $y_next);
				$pdf->Cell(10, 75,($teks11), 0, 0);
				
				$pdf->SetXY($x + 54, $y_next);
				$det3 = str_replace('!', '', $row_txt);
				$pdf->Cell(10, 75,($det3), 0, 0);
				
				$y_next = $y_next + 4;
				
				$pdf->SetFont("times", "I", 10);
				$pdf->SetXY($x, $y_next);
				$teks15 = 'Fund Manager Account No.';
				$pdf->Cell(10, 75,($teks15), 0, 0);
				
				$y_next = $y_next + 6;
			}

			if ($ln == 14)
			{
				$pdf->SetFont("times", "", 10);
				$pdf->SetXY($x, $y_next);
				$teks16 = 'Tanggal Transaksi';
				$pdf->Cell(10, 75,($teks16), 0, 0);

						
				$pdf->SetXY($x + 50, $y_next);
				$pdf->Cell(10, 75,($teks11), 0, 0);
				
				$pdf->SetXY($x + 54, $y_next);
				$det4 = str_replace('!', '', $row_txt);
				$pdf->Cell(10, 75,($det4), 0, 0);
				
		$tgl_bulan_tahun = str_replace('/','',$det4);
		$nama_file_pdf = 'UCN-'.$tgl_bulan_tahun.'-'.$noRek;
	
				$y_next = $y_next + 4;
				
				$pdf->SetFont("times", "I", 10);
				$pdf->SetXY($x, $y_next);
				$teks18 = 'Trade Date';
				$pdf->Cell(10, 75,($teks18), 0, 0);
				
				$y_next = $y_next + 6;
			}

			if ($ln == 15)
			{
				$pdf->SetFont("times", "", 10);
				$pdf->SetXY($x, $y_next);
				$teks19 = 'Total Nominal Pembelian';
				$pdf->Cell(10, 75,($teks19), 0, 0);

				$pdf->SetXY($x + 50, $y_next);
				$pdf->Cell(10, 75,($teks11), 0, 0);
				
				$pdf->SetXY($x + 54, $y_next);
				$det5 = str_replace('!', '', $row_txt);
				$pdf->Cell(10, 75,number_format($det5, 2, '.', ','), 0, 0);
				
				$y_next = $y_next + 4;
				
				$pdf->SetFont("times", "I", 10);
				$pdf->SetXY($x, $y_next);
				$teks20 = 'Subscription Amount';
				$pdf->Cell(10, 75,($teks20), 0, 0);
				
				$y_next = $y_next + 6;
			}

			if ($ln == 16)
			{
				$pdf->SetFont("times", "", 10);
				$pdf->SetXY($x, $y_next);
				$teks21 = 'Total Nominal Penjualan';
				$pdf->Cell(10, 75,($teks21), 0, 0);

				$pdf->SetXY($x + 50, $y_next);
				$pdf->Cell(10, 75,($teks11), 0, 0);
				
				$pdf->SetXY($x + 54, $y_next);
				$det6 = str_replace('!', '', $row_txt);
				$pdf->Cell(10, 75,number_format($det6, 2, '.', ','), 0, 0);
				
				$y_next = $y_next + 4;
				
				$pdf->SetFont("times", "I", 10);
				$pdf->SetXY($x, $y_next);
				$teks22 = 'Redemption Amount';
				$pdf->Cell(10, 75,($teks22), 0, 0);
				
				$y_next = $y_next + 6;
			}

			if ($ln == 17)
			{
				$pdf->SetFont("times", "", 10);
				$pdf->SetXY($x, $y_next);
				$teks23 = 'NAB Per Unit';
				$pdf->Cell(10, 75,($teks23), 0, 0);

				$pdf->SetXY($x + 50, $y_next);
				$pdf->Cell(10, 75,($teks11), 0, 0);
				
				$pdf->SetXY($x + 54, $y_next);
				$det7 = str_replace('!', '', $row_txt);
				$pdf->Cell(10, 75,number_format($det7, 4, '.', ','), 0, 0);
				
				$y_next = $y_next + 4;
				
				$pdf->SetFont("times", "I", 10);
				$pdf->SetXY($x, $y_next);
				$teks24 = 'NAV Per Unit';
				$pdf->Cell(10, 75,($teks24), 0, 0);
				
				$y_next = $y_next + 6;
			}

			if ($ln == 18)
			{
				$pdf->SetFont("times", "", 10);
				$pdf->SetXY($x, $y_next);
				$teks25 = 'Jumlah Unit Pembelian';
				$pdf->Cell(10, 75,($teks25), 0, 0);

				$pdf->SetXY($x + 50, $y_next);
				$pdf->Cell(10, 75,($teks11), 0, 0);
				
				$pdf->SetXY($x + 54, $y_next);
				$det8 = str_replace('!', '', $row_txt);
				$pdf->Cell(10, 75,number_format($det8, 4, '.', ','), 0, 0);
				
				$y_next = $y_next + 4;
				
				$pdf->SetFont("times", "I", 10);
				$pdf->SetXY($x, $y_next);
				$teks26 = 'Subscription (Unit)';
				$pdf->Cell(10, 75,($teks26), 0, 0);
				
				$y_next = $y_next + 6;
			}

			if ($ln == 19)
			{
				$pdf->SetFont("times", "", 10);
				$pdf->SetXY($x, $y_next);
				$teks27 = 'Jumlah Unit Penjualan';
				$pdf->Cell(10, 75,($teks27), 0, 0);

				$pdf->SetXY($x + 50, $y_next);
				$pdf->Cell(10, 75,($teks11), 0, 0);
				
				$pdf->SetXY($x + 54, $y_next);
				$det9 = str_replace('!', '', $row_txt);
				$pdf->Cell(10, 75,number_format($det9, 4, '.', ','), 0, 0);
				
				$y_next = $y_next + 4;
				
				$pdf->SetFont("times", "I", 10);
				$pdf->SetXY($x, $y_next);
				$teks28 = 'Redemption (Unit)';
				$pdf->Cell(10, 75,($teks28), 0, 0);
				
				$y_next = $y_next + 6;
			}

			if ($ln == 20)
			{
				$pdf->SetFont("times", "", 10);
				$pdf->SetXY($x, $y_next);
				$teks29 = 'Saldo Awal (Unit)';
				$pdf->Cell(10, 75,($teks29), 0, 0);

				$pdf->SetXY($x + 50, $y_next);
				$pdf->Cell(10, 75,($teks11), 0, 0);
				
				$pdf->SetXY($x + 54, $y_next);
				$det10 = str_replace('!', '', $row_txt);
				$pdf->Cell(10, 75,number_format($det10, 4, '.', ','), 0, 0);
				
				$y_next = $y_next + 4;
				
				$pdf->SetFont("times", "I", 10);
				$pdf->SetXY($x, $y_next);
				$teks30 = 'Opening Balance (Unit)';
				$pdf->Cell(10, 75,($teks30), 0, 0);
				
				$y_next = $y_next + 6;
			}

			if ($ln == 21)
			{
				$pdf->SetFont("times", "", 10);
				$pdf->SetXY($x, $y_next);
				$teks31 = 'Saldo Akhir (Unit)';
				$pdf->Cell(10, 75,($teks31), 0, 0);

				$pdf->SetXY($x + 50, $y_next);
				$pdf->Cell(10, 75,($teks11), 0, 0);
				
				$pdf->SetXY($x + 54, $y_next);
				$det11 = str_replace('!', '', $row_txt);
				$pdf->Cell(10, 75,number_format($det11, 4, '.', ','), 0, 0);
				
				$y_next = $y_next + 4;
				
				$pdf->SetFont("times", "I", 10);
				$pdf->SetXY($x, $y_next);
				$teks31 = 'Ending Balance (Unit)';
				$pdf->Cell(10, 75,($teks31), 0, 0);
				
				$y_next = $y_next + 6;

			    $y_agen = 217;
				$style1 = array('width' => 0.2);
				$pdf->Line(10, $y_agen+8, 200, $y_agen+8,$style1);

				$pdf->SetFont("times", "", 10);
				$pdf->SetXY($x -9, $y_next);
				$teks32 = 'Laporan ini dikeluarkan secara elektronik oleh Biro Kustodian - PT. Bank Central Asia Tbk. dan sah tanpa tanda tangan';
				$pdf->Cell(10, 85,($teks32), 0, 0);

				$y_next = $y_next + 4;

				$pdf->SetFont("times", "I", 10);
				$pdf->SetXY($x -9, $y_next);
				$teks32 = 'This Confirmation is electronically generated by PT. Bank Central Asia Tbk. - Custodial Services Department, no signature required';
				$pdf->Cell(10, 85,($teks32), 0, 0);
			}
			
			if ($ln == 22)
			{
				$filetonames = $no_urut;
//				echo $no_urut . '.  '. $nama;
				$detail = '';
				$filesizePdf = output_pdf($pdf,$nama_file_pdf,$blth,$file,$flagtrans);
		/**INSERT DETAIL**/
		insert_detail($m_loading_id,$noCard,$noRek,
				$nama,substr($alm1,0,39),substr($alm2,0,39),'','','00000',
				$flagtrans,$blth,$file,
				$nama_file_pdf,'','1',$emai,$filesizePdf,'1');
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
				$pdf->SetProtection($permissions=array('modify','copy','assemble'), $user_pass='', $ownerpass, $mode=1, $pubkeys=null);
				$pdf->AddPage();
				$x = 18;
				$y_next = 40;
			}
			
		}
	
  
		
		fclose ($f);
		
		update_m_loading($m_loading_id,$no_urut-1,$no_urut-1);
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
			// Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)
			
			
			$x_image = 146.5;
			$y_image = 4;
			
			$url_img = '../images/reksa_dana_big.jpg';
			//$pdf->Image($url_img,$x_image,$y_image);
			$pdf->Image($url_img,$x_image,$y_image, '40', '', 'JPG', '', '', true, 300, '', false, false, 0, false, false, false);
				
			//$x = 10;
			//$y_next = 18;
			
				$x_bca = 147;
				$y_bca = 18;
				$pdf->SetFont("arialn", "B", 9);	
				$pdf->SetXY($x_bca,$y_bca);
				$pdf->Cell(10, 10,'PT. Bank Central Asia Tbk.', 0, 0, "L");			    
				$y_bca = $y_bca + 3;
				
				$pdf->SetFont("arialn", "", 9);	
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
				$pdf->Line(10, $y_agen+8, 200, $y_agen+8,$style1);
				$pdf->Line(43, $y_agen+11, 200, $y_agen+11,$style1);
				$pdf->Line(10, $y_agen+17, 200, $y_agen+17,$style1);
				
				$x_judul_transaksi = 84;
				$y_judul_transaksi = 81.5 ;
				$pdf->SetFont("arialn", "", 8);	
				$pdf->SetXY($x_judul_transaksi,$y_judul_transaksi);
				$pdf->Cell(10, 10,'TRANSACTION', 0, 0, "C");
				
				$x_judul_saldo = 165;
				$y_judul_saldo = 81.5 ;
				$pdf->SetFont("arialn", "", 8);	
				$pdf->SetXY($x_judul_saldo,$y_judul_saldo);
				$pdf->Cell(10, 10,'SALDO', 0, 0, "C");
				
				$x_post = 10;
				$y_post = 85 ;
				$pdf->SetFont("arialn", "", 7);	
				$pdf->SetXY($x_post,$y_post);
				$pdf->Cell(10, 10,'Post', 0, 0, "L");
				$pdf->SetXY($x_post,$y_post+2);
				$pdf->Cell(10, 10,'Date', 0, 0, "L");
				
				$x_post = 22;
				$y_post = 85 ;
				$pdf->SetFont("arialn", "", 7);	
				$pdf->SetXY($x_post,$y_post);
				$pdf->Cell(10, 10,'NAB', 0, 0, "L");
				$pdf->SetXY($x_post,$y_post+2);
				$pdf->Cell(10, 10,'Date', 0, 0, "L");
				
				$x_post = 34;
				$y_post = 85 ;
				$pdf->SetFont("arialn", "", 7);	
				$pdf->SetXY($x_post,$y_post);
				$pdf->Cell(10, 10,'Desc', 0, 0, "L");
				
				$x_post = 47;
				$y_post = 85 ;
				$pdf->SetFont("arialn", "", 7);	
				$pdf->SetXY($x_post,$y_post);
				$pdf->Cell(10, 10,'Gross', 0, 0, "L");
				$x_post = 43;
				$pdf->SetXY($x_post,$y_post+2.5);
				$pdf->Cell(10, 10,'Transaction Value', 0, 0, "L");
				
				$x_post = 65;
				$y_post = 85 ;
				$pdf->SetFont("arialn", "", 7);	
				$pdf->SetXY($x_post,$y_post);
				$pdf->Cell(10, 10,'Fee/Charges', 0, 0, "L");
				
				$x_post = 87;
				$y_post = 85 ;
				$pdf->SetFont("arialn", "", 7);	
				$pdf->SetXY($x_post,$y_post);
				$pdf->Cell(10, 10,'Net', 0, 0, "L");
				$x_post = 80;
				$pdf->SetXY($x_post,$y_post+2.5);
				$pdf->Cell(10, 10,'Transaction Value', 0, 0, "L");
				
				$x_post = 106;
				$y_post = 85 ;
				$pdf->SetFont("arialn", "", 7);	
				$pdf->SetXY($x_post,$y_post);
				$pdf->Cell(10, 10,'Unit Price', 0, 0, "L");
				$x_post = 109;
				$pdf->SetXY($x_post,$y_post+2.5);
				$pdf->Cell(10, 10,'Rp.', 0, 0, "L");
				
				$x_post = 126;
				$y_post = 85 ;
				$pdf->SetFont("arialn", "", 7);	
				$pdf->SetXY($x_post,$y_post);
				$pdf->Cell(10, 10,'Unit', 0, 0, "L");
				$x_post = 124;
				$pdf->SetXY($x_post,$y_post+2.5);
				$pdf->Cell(10, 10,'Quantity', 0, 0, "L");
				
				$x_post = 144;
				$y_post = 85 ;
				$pdf->SetFont("arialn", "B", 7);	
				$pdf->SetXY($x_post,$y_post);
				$pdf->Cell(10, 10,'Unit Balance', 0, 0, "L");
				$x_post = 146;
				$pdf->SetXY($x_post,$y_post+2.5);
				$pdf->Cell(10, 10,'Quantity', 0, 0, "L");
				
				$x_post = 164;
				$y_post = 85 ;
				$pdf->SetFont("arialn", "", 7);	
				$pdf->SetXY($x_post,$y_post);
				$pdf->Cell(10, 10,'Avg. Cost/Unit', 0, 0, "L");
				$x_post = 169;
				$pdf->SetXY($x_post,$y_post+2.5);
				$pdf->Cell(10, 10,'Rp.', 0, 0, "L");
				
				$x_post = 184;
				$y_post = 85 ;
				$pdf->SetFont("arialn", "", 7);	
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