<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/function.php"); ?>
<?
	require_once('../include/tcpdf/config/lang/eng.php');
	require_once('../include/tcpdf/tcpdf.php');
	require_once('../include/fpdi/fpdi.php');
	
	$pr = $_REQUEST['pr'];
	$arr_pr = explode('|',$pr);
	
	###die (print_r($arr_pr));
	
	$flagtrans = $arr_pr[0];
	$menu_id = $arr_pr[1];
	$act = $arr_pr[2];
	$blth = $arr_pr[3];
	$file = $arr_pr[4];
	$file_mcustomer = $arr_pr[5];
	$GLOBALS['file_mcustomer'] = $file_mcustomer;
	
	$con = pg_connect($connection) or die("Could not connect to database!");
	$msg = "";
	$msg_not_exists = "";
	
	class PDF extends FPDI {
		function Header() {}
		function Footer() {}
	}
	
	class MYPDF extends TCPDF {
		function Header() {
			//$bMargin = 26;//$this->getBreakMargin();
			$bMargin = 32;//$this->getBreakMargin();
			$auto_page_break = $this->AutoPageBreak;
			$this->SetAutoPageBreak(false, 0);
			$this->SetAutoPageBreak($auto_page_break, $bMargin);
		}
		
		function Footer() {
			$this->SetFont('helvetica', '', 10);
			$this->SetXY(0, -15);
			$this->Cell(200, 10, 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
		}
	}
	
	switch($act){
		case 'dat_to_pdf':
				$waktu_awal = strtotime(date("Y-m-d H:i:s"));
				
				$sql_cek_dat = "SELECT * FROM m_loading WHERE flagtrans = '$flagtrans' and blth = '$blth' and loading_file = '$file'";
				$qry_cek_dat = pg_query($sql_cek_dat) or die('ERROR cek m_loading: '.$sql_cek_dat);
				$jml = pg_num_rows($qry_cek_dat);
				if($jml == 0){
					$msg = '|'.dat_to_pdf($flagtrans,$menu_id,$blth,$file,$url_directory,$pdftk_loc,$file_mcustomer);
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
	
	function dat_to_pdf($flagtrans,$menu_id,$blth,$file,$url_directory,$pdftk_loc,$file_mcustomer){
	
			buat_tabel_split();
			/******************************************
			CEK TABEL DETAIL<blth><flagtrans>
			******************************************/
			$blth_balik = substr($blth,-4).substr($blth,0,2);
		
			$BLTH_BARUS = BLTH_BARU;
			if($blth_balik>$BLTH_BARUS){
			$tabeldetail = strtolower("detail_".$blth."_".$flagtrans);
			$tabeldetail_pk = $tabeldetail."_p_k";
			$tabeldetail_un = $tabeldetail."_unique";
			
			$cek_tabel = "SELECT detail_id FROM $tabeldetail";
			$exe_tabel = @pg_query($cek_tabel);
			
			if(!$exe_tabel){
				$buat_tabel = "CREATE TABLE $tabeldetail(						
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
								  kurir character varying(15),
								  CONSTRAINT $tabeldetail_pk PRIMARY KEY (detail_id),
								  CONSTRAINT $tabeldetail_un UNIQUE (nomor_rekening, nama_file, blth)
								)WITHOUT OIDS;
								ALTER TABLE $tabeldetail OWNER TO postgres;";
				$exe_tabel = pg_query($buat_tabel)or die("ERROR: " . $buat_tabel);
			}
			}else{
				$tabeldetail = "detail";
			}
			
			
		$x = 10;
		$y = 24;
		$customer = 0;
		$customer_cetak = 0;
		$total_hlm = 0;
		$total_hlm_cetak = 0;
		$flag_case = 'N';
		$loop = 1;
		$msg_not_exists = array ();
		$prevcardnb = "";
		$daterecieve = date('m/d/Y H:i:s');
		
		$dirfile = '../tmp/'.$file;
		$open_dat = fopen($dirfile,'r');
		
		$m_loading_id = m_loading($flagtrans,$blth,$file);
		$nocetak_p = 1;
		while(!feof($open_dat)){
			$row_dat = fgets($open_dat);
			$row_dat = utf8_encode($row_dat);
			if(trim($row_dat)!=''){
				$row_dat = str_replace('ï»¿', '', $row_dat);
				$rectype = substr($row_dat,0,2);
				$no_card_dat = substr($row_dat,5,16);
				
				
				
				//////////////////////////////////////////////////////////////////////////////////// kz
				//PENGECEKAN APAKAH CUSTOMER ADA ATAU TIDAK
				$no_card_dat = cek_nomor_rekening_ada($no_card_dat,$tabeldetail,$m_loading_id);
				//////////////////////////////////////////////////////////////////////////////////// kz
						
						
						
				//CO0117201610000
				$nocetak_p2	 = str_pad($nocetak_p, 6, '0', STR_PAD_LEFT);
				$barcode 	 = $flagtrans.date('mdY').$nocetak_p2;
				
				if($loop != 1){
					if(($prevcardnb<>$no_card_dat)&&($flag_case=='Y')){
						
						
						
						
						$strpdf=doPDF($cdata,$header,$password_pdf,$blth,$file,$detail,$flag_case,$flagtrans,$barcode, $dt_kurir1);
						$arrpdf=explode('|',$strpdf); $pdf_name=$arrpdf[0]; $jml_hlm=$arrpdf[1];
						insert_detail($m_loading_id,$cdata,
							$flagtrans,$blth,$file,
							$pdf_name,$password_pdf,$jml_hlm,$tabeldetail,$email,$barcode, $dt_kurir1);
						insert_detail_cetak($m_loading_id,$cdata,
							$flagtrans,$blth,$file,
							$pdf_name,$password_pdf,$jml_hlm,$barcode, $dt_kurir1);
						$total_hlm += $jml_hlm;
						$total_hlm_cetak += $jml_hlm;
						
						
						
			
					}
					else if(($prevcardnb<>$no_card_dat)&&($flag_case=='N')){
						$strpdf=doPDF($cdata,$header,$password_pdf,$blth,$file,$detail,$flag_case,$flagtrans,$barcode, $dt_kurir1);
						$arrpdf=explode('|',$strpdf); $pdf_name=$arrpdf[0]; $jml_hlm=$arrpdf[1];
						insert_detail_cetak($m_loading_id,$cdata,
							$flagtrans,$blth,$file,
							$pdf_name,$password_pdf,$jml_hlm,$barcode, $dt_kurir1);
						$total_hlm_cetak += $jml_hlm;
						
						
			
			
					}
				}
				
				$email = "";
				$sql_cek_m_customer = "SELECT nomor_rekening, password_pdf, email1
										FROM m_customer
										WHERE blth = '$blth'
											AND flagtrans = '$flagtrans'";
					if($file_mcustomer != ''){
						$sql_cek_m_customer .= " AND log_customer_id = ".$file_mcustomer;
					}
				#####$sql_cek_m_customer .= " AND nomor_rekening = '$no_card_dat'"; ###die($sql_cek_m_customer);
				$sql_cek_m_customer .= " AND nomor_rekening like  '%".str_replace('X','',$no_card_dat)."%'"; ###die($sql_cek_m_customer);
				$qry_cek_m_customer = pg_query($sql_cek_m_customer) or die('ERROR cek m_customer: '.$sql_cek_m_customer);
				$jml_customer = pg_num_rows($qry_cek_m_customer);
				//$jml_customer = 0;
				while($row_email = pg_fetch_array($qry_cek_m_customer)){
							$email .= $row_email['email1'].";";
							$password_pdf = $row_email['password_pdf'];
							$password_pdf = trim($password_pdf);
						}
				#$row_cek_m_customer = pg_fetch_assoc($qry_cek_m_customer);
					
				$panjang_pass = strlen($password_pdf);
				if( $panjang_pass != 6 )
				{
					die("Password $no_card_dat bukan 6 karakter, di data master customer ($panjang_pass karakter)");
				}
				
				if($jml_customer==0){
					$flag_case = 'N';
					if($rectype == '01'){
						$msg_not_exists[] = $no_card_dat;
					}
				}else{
					$flag_case = 'Y';
				}
				if($prevcardnb<>$no_card_dat){
					if($rectype=='01'){ //hanya akan masuk saat nosebelum tidak sama dengan nocurrent, dan rectype 01
						$hlm = 1;
						$record = 0;
						if($flag_case == "Y"){
							$customer++;
						}
						#####$customer_cetak++;
						
						/////////////////////////////////////////////////////////////////////////////////////// kz
						if ( preg_match("/X/i",  $no_card_dat ) || preg_match("/x/i", $no_card_dat ) )
						{
							
						}else{
							$customer_cetak++;
						}
						/////////////////////////////////////////////////////////////////////////////////////// kz
						
						
						
						
						$cdata['no_card_dat']	= $no_card_dat;
						$cdata['custname1']	= trim(substr($row_dat,43,30));
						$cdata['custname2']	= trim(substr($row_dat,73,30));
						$cdata['addr1']		= trim(substr($row_dat,103,30));
						$cdata['addr2']		= trim(substr($row_dat,133,30));
						$cdata['addr3']		= trim(substr($row_dat,163,30));
						$cdata['city']		= trim(substr($row_dat,193,30));
						$cdata['zipcd']		= trim(substr($row_dat,223,9));
						$cdata['dtcetak']	= substr($row_dat,238,2).'-'.substr($row_dat,236,2).'-'.substr($row_dat,232,4);
						$cdata['dttempo']	= substr($row_dat,246,2).'-'.substr($row_dat,244,2).'-'.substr($row_dat,240,4);
						$cdata['custname3']	= trim(substr($row_dat,979,30));
						//$cdata['bunga_belanja']	= number_format(((int)substr($row_dat,458,5)/100),2,'.',',').'%';  //old dari data
						//$cdata['bunga_belanja']	= '2.95'.'%'; // 20170605 perubahan juni 2017
						//$cdata['bunga_belanja']	= '2.25'.'%'; // 20170704 perubahan juli 2017
						$cdata['bunga_belanja']	= '1.75'.'%'; // 20210731 perubahan Juli 2021 
						//$cdata['bunga_tunai']	= number_format(((int)substr($row_dat,464,5)/100),2,'.',',').'%'; //old dari data
						//$cdata['bunga_tunai']	= '2.95'.'%'; // 20170605 perubahan juni 2017
						//$cdata['bunga_tunai']	= '2.25'.'%'; // 20170704 perubahan juli 2017
						$cdata['bunga_tunai']	= '1.75'.'%'; // 20210731 perubahan Juli 2021 
						$kolektibilitas		= substr($row_dat,1060,1);
						
						/*KOLEKTIBILITAS*/
						switch($kolektibilitas)
						{
							case '1':		
								$teks_kolektibilitas = "LANCAR";
								break;		
							case '2':		
								$teks_kolektibilitas = "Dalam Perhatian Khusus";
								break;
							case '3':		
								$teks_kolektibilitas = "Kurang Lancar";
								break;
							case '4':		
								$teks_kolektibilitas = "Diragukan";
								break;
							case '5':		
								$teks_kolektibilitas = "MACET";
								break;
							default:
								$teks_kolektibilitas = "LANCAR";
								break;
						}
						$cdata['kolektibilitas']	= $teks_kolektibilitas;
						
						$header['tagihan_prev']		= array();//reset array header
						$header['tagihan_next']		= array();
						$header['pembayaran']		= array();
						$header['pembelanjaan']		= array();
						$header['fin_charge']		= array();
						$header['ambil_tunai']		= array();
						$header['kredit_limit']		= array();
						$header['kredit_sisa']		= array();
						$header['cdnumb']			= array();
						$header['cdname']			= array();
						$im=0;
						
						$cdata['total_tagihan_next']	= 0;
						$cdata['total_kredit_limit']	= 0;
						$cdata['total_kredit_sisa']		= 0;
						$cdata['total_tagihan_prev']	= 0;
						$cdata['total_pembayaran']		= 0;
						$cdata['total_pembelanjaan']	= 0;
						$cdata['total_fin_charge']		= 0;
						$cdata['total_ambil_tunai']		= 0;
						$cdata['total_batas_tunai']		= 0;
						
						$total_tagihan_next	= 0;
						$total_kredit_limit	= 0;
						$total_kredit_sisa	= 0;
						$total_tagihan_prev	= 0;
						$total_pembayaran	= 0;
						$total_pembelanjaan	= 0;
						$total_fin_charge	= 0;
						$total_ambil_tunai	= 0;
						$total_batas_tunai	= 0;
						
						$detail['dttrans']	= array();
						$detail['dtbuku']	= array();
						$detail['trans']	= array();
						$detail['jmltrans']	= array();
						$detail['cr']		= array();
						
						///UPDATE 11/29/2016 ADA PENAMBAHAN UNTUK PLOTING KURIR
						///SEMENTARA ADA 3 JENIS, FICC, NCS, BRI PADA TABEL MASTER_KURIR DI FIELD KURIR, CARDNO, KODEPOS
						//UPDATE 27.09.2018 // sesuai permintaan BRI
						$str_cek_kurir1	= "SELECT trim(kurir) as kurir FROM 
											master_kurir 
											WHERE cardno = '".$cdata['no_card_dat']."'
											ORDER BY kurir ASC 
											LIMIT 1";
						$pg_cek_kurir1	= pg_query($str_cek_kurir1);
						$dt_cek_kurir1	= pg_fetch_object($pg_cek_kurir1);
						$dt_kurir1		= $dt_cek_kurir1->kurir;
						if(empty($dt_kurir1) or $dt_kurir1 == '')
						{
							$str_cek_kurir2	= "SELECT trim(kurir) as kurir FROM 
												master_kurir 
												WHERE kodepos = '".$cdata['zipcd']."' 
												ORDER BY kurir ASC 
												LIMIT 1";
							$pg_cek_kurir2	= pg_query($str_cek_kurir2);
							$dt_cek_kurir2	= pg_fetch_object($pg_cek_kurir2);
							$dt_kurir2		= $dt_cek_kurir2->kurir;
							
							if(empty($dt_kurir2) or $dt_kurir2 == '')
							{
								$dt_kurir1 = 'NCS';
							}
							else
							{
								$dt_kurir1 = $dt_kurir2;
							}
							
						}
						
						$nocetak_p++;
					}
				}
				/*if(($rectype=='02')&&($prevrectype=='01')){
					$oricdnum	= substr($row_dat,27,16);
					$header['cdnumb'][($im-1)]	= substr($oricdnum,0,4).'-'.substr($oricdnum,4,4).'-'.substr($oricdnum,8,4).'-'.substr($oricdnum,12,4);
					$header['cdname'][($im-1)]	= substr($row_dat,43,30);
				}*/
				if($rectype=='01'){
					$tagihan_next	= ltrim(substr($row_dat,413,14),'0');
						(substr($row_dat,427,1) == "-")? $sign_tagihan_next = -1: $sign_tagihan_next = 1;
						$tagihan_next	= ($sign_tagihan_next * $tagihan_next);
					$kredit_limit	= ltrim(substr($row_dat,278,14),'0');
					$kredit_sisa	= ltrim(substr($row_dat,293,14),'0');
					$tagihan_prev	= ltrim(substr($row_dat,323,14),'0');
						(substr($row_dat,337,1) == "-")? $sign_tagihan_prev = -1: $sign_tagihan_prev = 1;
						$tagihan_prev	= ($sign_tagihan_prev * $tagihan_prev);
					$pembayaran		= ltrim(substr($row_dat,353,14),'0');
					$finance_charge	= ltrim(substr($row_dat,398,14),'0');
					$pembelanjaan	= ltrim(substr($row_dat,428,14),'0');
					$ambil_tunai	= ltrim(substr($row_dat,443,14),'0');
					$batas_tunai	= ltrim(substr($row_dat,308,14),'0');
					//20180628//bGZ
					$ttl_autodebet	= substr($row_dat,1087,20);					
					
					$total_tagihan_next	+= $tagihan_next;
					$total_kredit_limit	+= $kredit_limit;
					$total_kredit_sisa	+= $kredit_sisa;
					$total_tagihan_prev	+= $tagihan_prev;
					$total_pembayaran	+= $pembayaran;
					$total_pembelanjaan	+= $pembelanjaan;
					$total_fin_charge	+= $finance_charge;
					$total_ambil_tunai	+= $ambil_tunai;
					$total_batas_tunai	+= $batas_tunai;
					
					$cdata['total_tagihan_next']	= number_format($total_tagihan_next,0,'.',',');
					$cdata['total_kredit_limit']	= number_format($total_kredit_limit,0,'.',',');
					$cdata['total_kredit_sisa']		= number_format($total_kredit_sisa,0,'.',',');
					$cdata['total_tagihan_prev']	= number_format($total_tagihan_prev,0,'.',',');
					$cdata['total_pembayaran']		= number_format($total_pembayaran,0,'.',',');
					$cdata['total_pembelanjaan']	= number_format($total_pembelanjaan,0,'.',',');
					$cdata['total_fin_charge']		= number_format($total_fin_charge,0,'.',',');
					$cdata['total_ambil_tunai']		= number_format($total_ambil_tunai,0,'.',',');  
					$cdata['total_batas_tunai']		= number_format($total_batas_tunai,0,'.',',');
					$cdata['ttl_autodebet']			= number_format($ttl_autodebet,0,'.',',');
					
					/*$cdata['total_tagihan_next']	= "???";
					$cdata['total_kredit_limit']	= "???";
					$cdata['total_kredit_sisa']		= "???";
					$cdata['total_tagihan_prev']	= "???";
					$cdata['total_pembayaran']		= "???";
					$cdata['total_pembelanjaan']	= "???";
					$cdata['total_fin_charge']		= "???";
					$cdata['total_ambil_tunai']		= "???";  
					$cdata['total_batas_tunai']		= "???";*/
					
					if (empty($tagihan_next)) $tagihan_next = 0;
					if (empty($kredit_limit)) $kredit_limit = 0;
					if (empty($batas_tunai)) $batas_tunai = 0;
					if (empty($kredit_sisa)) $kredit_sisa = 0;
					if (empty($tagihan_prev)) $tagihan_prev = 0;
					if (empty($pembayaran)) $pembayaran = 0;
					if (empty($pembelanjaan)) $pembelanjaan = 0;
					if (empty($finance_charge)) $finance_charge = 0;
					if (empty($ambil_tunai)) $ambil_tunai = 0;
					
					$header['tagihan_next'][$im] 	= number_format($tagihan_next,0,'.',',');
					$header['kredit_limit'][$im] 	= number_format($kredit_limit,0,'.',',');
					$header['batas_tunai'][$im] 	= number_format($batas_tunai,0,'.',',');
					$header['kredit_sisa'][$im]		= number_format($kredit_sisa,0,'.',',');
					$header['tagihan_prev'][$im]	= number_format($tagihan_prev,0,'.',',');
					$header['pembayaran'][$im]		= number_format($pembayaran,0,'.',',');
					$header['pembelanjaan'][$im]	= number_format($pembelanjaan,0,'.',',');
					$header['fin_charge'][$im]		= number_format($finance_charge,0,'.',',');
					$header['ambil_tunai'][$im]		= number_format($ambil_tunai,0,'.',',');
					
					//////////////////////////////////////////////////////////////////////
					//case di bawah ini digunakan apabila ada rectype 01 secara berurutan
					/*if($prevrectype == '01'){
						$header['cdname'][($im-1)]	= $cdname_temp;
						$header['cdnumb'][($im-1)]	= $cdnumb_temp;
					}
					$oricdnum_temp					= substr($row_dat,27,16);
					$cdname_temp					= substr($row_dat,1061,30);
					$cdnumb_temp					= substr($oricdnum_temp,0,4).'-'.substr($oricdnum_temp,4,4).'-'.substr($oricdnum_temp,8,4).'-'.substr($oricdnum_temp,12,4);*/
					//////////////////////////////////////////////////////////////////////
					
					$oricdnum_temp					= substr($row_dat,27,16);
					$cdname_temp					= substr($row_dat,1061,25);
					$cdnumb_temp					= substr($oricdnum_temp,0,4).'-'.substr($oricdnum_temp,4,4).'-'.substr($oricdnum_temp,8,4).'-'.substr($oricdnum_temp,12,4);
					$header['cdname'][$im]			= $cdname_temp;
					$header['cdnumb'][$im]			= $cdnumb_temp;
					
					$im++;
					$ih=0;
				}
				if($rectype=='03'){
					//$jmltrans		= ltrim(substr($row_dat,147,14),'0');
					$jmltrans = ltrim(substr($row_dat,147,14), '0') ?: '0';
					
					$total_jmltrans	+= $jmltrans;
					
					$detail['dttrans'][($im-1)][$ih]		= substr($row_dat,79,2).'-'.substr($row_dat,77,2).'-'.substr($row_dat,73,4);
					$detail['dtbuku'][($im-1)][$ih]			= substr($row_dat,87,2).'-'.substr($row_dat,85,2).'-'.substr($row_dat,81,4);
					$detail['trans'][($im-1)][$ih]			= substr($row_dat,89,40);
					$detail['forexcurr'][($im-1)][$ih]		= substr($row_dat,129,3);
						$forex_nominal						= substr($row_dat,132,14);
					$detail['forexnominal'][($im-1)][$ih]	= number_format(($forex_nominal/100),2,".",",");
						$kurs_nominal						= substr($row_dat,164,10);
						if(trim($kurs_nominal) == "") $kurs_nominal = "0";
					$detail['kursnominal'][($im-1)][$ih]	= number_format(($kurs_nominal/100),2,".",",");
					$detail['jmltrans'][($im-1)][$ih]		= number_format($jmltrans,0,'.',',');
					$detail['cr'][($im-1)][$ih]				= substr($row_dat,162,2);
					
					$ih++;
				}
				$prevcardnb=$no_card_dat;
				$prevrectype=$rectype;
			}
			$loop++;
			
		}
		if($flag_case == 'Y' && feof($open_dat))
		{
			
			
			
			$strpdf=doPDF($cdata,$header,$password_pdf,$blth,$file,$detail,$flag_case,$flagtrans,$barcode, $dt_kurir1);
			$arrpdf=explode('|',$strpdf); $pdf_name=$arrpdf[0]; $jml_hlm=$arrpdf[1];
			insert_detail($m_loading_id,$cdata,
				$flagtrans,$blth,$file,
				$pdf_name,$password_pdf,$jml_hlm,$tabeldetail,$email,$barcode, $dt_kurir1);
			insert_detail_cetak($m_loading_id,$cdata,
				$flagtrans,$blth,$file,
				$pdf_name,$password_pdf,$jml_hlm,$barcode, $dt_kurir1);
			$total_hlm += $jml_hlm;
			$total_hlm_cetak += $jml_hlm;
			
		}
		else if($flag_case == 'N' && feof($open_dat)){
			$strpdf=doPDF($cdata,$header,$password_pdf,$blth,$file,$detail,$flag_case,$flagtrans,$barcode, $dt_kurir1);
			$arrpdf=explode('|',$strpdf); $pdf_name=$arrpdf[0]; $jml_hlm=$arrpdf[1];
			insert_detail_cetak($m_loading_id,$cdata,
				$flagtrans,$blth,$file,
				$pdf_name,$password_pdf,$jml_hlm,$barcode, $dt_kurir1);
			$total_hlm_cetak += $jml_hlm;
			
		}
		
		$file_merge = str_replace(substr($file,-4),'',$file);
		//untuk file cetak di merger berdasarkan kurir
		$str_kurir_c	= "SELECT kurir FROM detail_cetak GROUP BY kurir ORDER BY kurir";
		$pg_kurir_c		= pg_query($str_kurir_c);
		while($dt_kurir_c	= pg_fetch_object($pg_kurir_c))
		{
				$kurir_c	= $dt_kurir_c->kurir;
				//UNTUK FILE REGULER
				#$input_folder_merge = $url_directory.'\\pdf\\'.$flagtrans.'\\'.$blth.'\\'.$file_merge.'\\cetak\\'.$kurir_c.'\\*';
				$input_folder_merge = $url_directory.'\\pdf\\'.$flagtrans.'\\'.$blth.'\\'.$file_merge.'\\cetak_clean\\'.$kurir_c.'\\REG\\*';
				$output_folder_merge = $url_directory.'\\pdf\\'.$flagtrans.'\\'.$blth.'\\'.$file_merge.'\\produksi\\'.$file_merge.'_'.$kurir_c.'REG_cetak.pdf';
				merge_pdf_folder($input_folder_merge, $output_folder_merge, $pdftk_loc);
				
				//UNTUK FILE KKI
				$input_folder_merge2 = $url_directory.'\\pdf\\'.$flagtrans.'\\'.$blth.'\\'.$file_merge.'\\cetak_clean\\'.$kurir_c.'\\KKI\\*';
				$output_folder_merge2 = $url_directory.'\\pdf\\'.$flagtrans.'\\'.$blth.'\\'.$file_merge.'\\produksi\\'.$file_merge.'_'.$kurir_c.'KKI_cetak.pdf';
				merge_pdf_folder($input_folder_merge2, $output_folder_merge2, $pdftk_loc);
		}
		
		$total_splitnya= insert_detail_pdf_split(); ###20181005 INSERT KE DETAIL
		$customer = $customer + $total_splitnya;
		
		
		
		$total_hlm_cetak  = total_hlm_cetak($m_loading_id,$blth,$file,$tabeldetail,$flagtrans);
		
		create_p01($blth,$file,$total_hlm_cetak,$customer_cetak,$flagtrans);
		create_p02($blth,$file,$total_hlm_cetak,$customer_cetak,$flagtrans);
		create_q01($blth,$file,$total_hlm_cetak,$customer_cetak,$flagtrans);
		create_rec($blth,$file,$total_hlm_cetak,$customer_cetak,$flagtrans);
		create_sip($blth,$file,$total_hlm_cetak,$customer_cetak,$daterecieve,$flagtrans);
		
		update_m_loading($m_loading_id,$total_hlm,$customer,$total_hlm_cetak,$customer_cetak);
		create_csv($m_loading_id,$blth,$file,$tabeldetail,$flagtrans);
		create_csv_cetak($m_loading_id,$blth,$file,$tabeldetail,$flagtrans);
		
		set_email_null_duplikat($m_loading_id,$blth,$file,$tabeldetail,$flagtrans);
		
		
		
		//setelah terbentuk semua baru di zip
		create_zipfile($blth,$file,$flagtrans);
		$not_exists = implode(', ',$msg_not_exists);
		return $customer.'|'.$not_exists;
	}
	
	function doPDF($cdata,$header,$password_pdf,$blth,$file,$detail,$flag_case,$flagtrans,$barcode, $dt_kurir1){
		//$pdf = new MYPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		
		$arr_summary = array();
		$arr_hal = 0;
		$temp_norek ='';
		###$arr_summary[$cdata['no_card_dat']][norek]
		
		$pdf = new MYPDF('P', PDF_UNIT, array(210, 297), true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor(PDF_AUTHOR);
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		//$pdf->SetMargins(0, 20);
		$pdf->SetMargins(0, 35);
		$pdf->setPrintHeader(TRUE);
		$pdf->setPrintFooter(FALSE);
		//$pdf->SetAutoPageBreak(TRUE, 26);
		$pdf->AddFont("Sandy","","sandy.php");
		$pdf->AddFont("Sandy2","","sandy2.php");
		
		$card_numb = '';
		$card_bin = '';
		$nheader=count($header['cdnumb']);
		for($i=0;$i<$nheader;$i++){
			
			$card_numb = trim($header['cdnumb'][$i]);
			//str_replace(find,replace,string,count)
			$card_numb = str_replace('-','',$card_numb);
			$card_bin = substr($card_numb,0,6);
			//echo $card_numb;die();
			//echo $card_bin;die();
			//BIN KKI 618950
			
		}
		
		$x_teks_master = 10;
		$y_teks_master = 35;
		$pdf->AddPage();
		//$pdf->Image('../images/bricorp_mst_20130715.jpg',$x_teks_master,$y_teks_master,217,302);
		//$pdf->SetFont('helvetica','BI',13);
		$pdf->SetFont('helvetica','B',13);
		$pdf->SetXY($x_teks_master, $y_teks_master - 5);
		if($card_bin !== '618950')
		{
			$pdf->MultiCell(190,5,'Corporate Card Statement',0,'C');
		}
		else
		{
			$pdf->MultiCell(190,5,'Lembar Tagihan KKI Segmen Pemerintah',0,'C');
		}
		
		
		
		//cdnumb
		
		$x_biodata = $x_teks_master;
		//$y_biodata = $y_teks_master;
		$y_biodata = $y_teks_master + 3;
		$pdf->SetFont('Sandy','',9);
		$pdf->SetXY($x_biodata, $y_biodata);
		$pdf->SetX($x_biodata);$pdf->MultiCell(50,5,$cdata['custname2'],0,'L',0,1);
		$pdf->SetX($x_biodata);$pdf->MultiCell(50,5,$cdata['addr1'],0,'L',0,1);
		$pdf->SetX($x_biodata);$pdf->MultiCell(50,5,$cdata['addr2'],0,'L',0,1);
		$pdf->SetX($x_biodata);$pdf->MultiCell(50,5,$cdata['addr3'],0,'L',0,1);
		$pdf->SetX($x_biodata);$pdf->MultiCell(50,5,$cdata['city'].' '.$cdata['zipcd'],0,'L',0,1);
		
		
		$x_kotak2 = $x_teks_master + 50;
		//$y_kotak2 = $y_teks_master;
		$y_kotak2 = $y_teks_master + 3;
		rectfill($pdf, 200, 0, $x_kotak2, $y_kotak2, 140, 8, "FD");
		$pdf->SetFont('Sandy','',8);
		$pdf->SetXY($x_kotak2, $y_kotak2);
		$pdf->Cell(35, 4, "Tagihan Sebelumnya", 0, 0, 'L');
		$pdf->Cell(35, 4, "Pembayaran / Kredit", 0, 0, 'L');
		$pdf->Cell(35, 4, "Pembelanjaan / Debit", 0, 0, 'L');
		$pdf->Cell(35, 4, "Pengambilan Tunai", 0, 1, 'L');
		$pdf->SetFont('Sandy2','');
		$pdf->SetX($x_kotak2);
		$pdf->Cell(35, 4, "Previous Balance", 0, 0, 'L');
		$pdf->Cell(35, 4, "Payment / Credit", 0, 0, 'L');
		$pdf->Cell(35, 4, "Purchase / Debit", 0, 0, 'L');
		$pdf->Cell(35, 4, "Cash Advance", 0, 1, 'L');
		
		rectfill($pdf, 0, 0, $x_kotak2, $y_kotak2 + 8, 140, 4, "D");
		$pdf->SetFont('Sandy','');
		$pdf->SetX($x_kotak2);
		$pdf->Cell(35, 4, $cdata['total_tagihan_prev'], 0, 0, 'L');
		$pdf->Cell(35, 4, $cdata['total_pembayaran'], 0, 0, 'L');
		$pdf->Cell(35, 4, $cdata['total_pembelanjaan'], 0, 0, 'L');
		$pdf->Cell(35, 4, $cdata['total_ambil_tunai'], 0, 1, 'L');
		
		
		$x_kotak3 = $x_kotak2;
		$y_kotak3 = $y_kotak2 + 16;
		rectfill($pdf, 200, 0, $x_kotak3, $y_kotak3, 35, 8, "FD");
		rectfill($pdf, 0, 0, $x_kotak3 + 35, $y_kotak3, 35, 8, "D");
		rectfill($pdf, 200, 0, $x_kotak3 + 70, $y_kotak3, 35, 8, "FD");
		rectfill($pdf, 0, 0, $x_kotak3 + 105, $y_kotak3, 35, 8, "D");
		
		$pdf->SetFont('Sandy','',8);
		$pdf->SetXY($x_kotak3, $y_kotak3);
		$pdf->Cell(35, 4, "Tagihan Baru", 0, 1, 'L');
		$pdf->SetFont('Sandy2','',8);
		$pdf->SetX($x_kotak3);
		$pdf->Cell(35, 4, "New Balance", 0, 0, 'L');
		
		$pdf->SetFont('Sandy','',8);
		$pdf->SetXY($x_kotak3 + 35, $y_kotak3);
		$pdf->Cell(35, 8, $cdata['total_tagihan_next'], 0, 0, 'C');
		
		$pdf->SetFont('Sandy','',8);
		$pdf->SetXY($x_kotak3 + 70, $y_kotak3);
		$pdf->Cell(35, 4, "Sisa Kredit", 0, 1, 'L');
		$pdf->SetFont('Sandy2','',8);
		$pdf->SetX($x_kotak3 + 70);
		$pdf->Cell(35, 4, "Available Credit Limit", 0, 0, 'L');
		
		$pdf->SetFont('Sandy','',8);
		$pdf->SetXY($x_kotak3 + 105, $y_kotak3);
		$pdf->Cell(35, 8, $cdata['total_kredit_sisa'], 0, 0, 'C');
		
		
		$x_kotak4 = $x_kotak2;
		$y_kotak4 = $y_kotak2 + 28;
		rectfill($pdf, 200, 0, $x_kotak4, $y_kotak4, 35, 8, "FD");
		rectfill($pdf, 0, 0, $x_kotak4 + 35, $y_kotak4, 35, 8, "D");
		rectfill($pdf, 200, 0, $x_kotak4 + 70, $y_kotak4, 35, 8, "FD");
		rectfill($pdf, 0, 0, $x_kotak4 + 105, $y_kotak4, 35, 8, "D");
		
		$pdf->SetFont('Sandy','',8);
		$pdf->SetXY($x_kotak4, $y_kotak4);
		$pdf->Cell(35, 4, "Bunga Pembelanjaan", 0, 1, 'L');
		$pdf->SetFont('Sandy2','',8);
		$pdf->SetX($x_kotak4);
		$pdf->Cell(35, 4, "Retail Interest", 0, 0, 'L');
		
		$pdf->SetFont('Sandy','',8);
		$pdf->SetXY($x_kotak4 + 35, $y_kotak4);
		$pdf->Cell(35, 8, $cdata['bunga_belanja'], 0, 0, 'C');
		
		$pdf->SetFont('Sandy','',8);
		$pdf->SetXY($x_kotak4 + 70, $y_kotak4);
		$pdf->Cell(35, 4, "Bunga Pengambilan Tunai", 0, 1, 'L');
		$pdf->SetFont('Sandy2','',8);
		$pdf->SetX($x_kotak4 + 70);
		$pdf->Cell(35, 4, "Cash Advance Interest", 0, 0, 'L');
		
		$pdf->SetFont('Sandy','',8);
		$pdf->SetXY($x_kotak4 + 105, $y_kotak4);
		$pdf->Cell(35, 8, $cdata['bunga_tunai'], 0, 0, 'C');
		
		
		
		$x_kotak = $x_teks_master;
		$y_kotak = $y_teks_master + 43;
		rectfill($pdf, 200, 0, $x_kotak, $y_kotak, 190, 10, "FD");
		$pdf->SetFont('Sandy','',9);
		$pdf->SetXY($x_kotak, $y_kotak);
		$pdf->Cell(40, 5, "Tanggal Cetak", 0, 0, 'L');
		$pdf->Cell(55, 5, "Jatuh Tempo Pembayaran", 0, 0, 'L');
		$pdf->Cell(40, 5, "Batas Kredit", 0, 0, 'L');
		$pdf->Cell(55, 5, "Batas Pengambilan Tunai", 0, 1, 'L');
		$pdf->SetFont('Sandy2','');
		$pdf->SetX($x_kotak);
		$pdf->Cell(40, 5, "Statement Date", 0, 0, 'L');
		$pdf->Cell(55, 5, "Payment Due Date", 0, 0, 'L');
		$pdf->Cell(40, 5, "Credit Limit", 0, 0, 'L');
		$pdf->Cell(55, 5, "Cash Advance Limit", 0, 1, 'L');
		
		rectfill($pdf, 0, 0, $x_kotak, $y_kotak + 10, 190, 5, "D");
		$pdf->SetFont('Sandy','');
		$pdf->SetX($x_kotak);
		$pdf->Cell(40, 5, $cdata['dtcetak'], 0, 0, 'L');
		$pdf->Cell(55, 5, $cdata['dttempo'], 0, 0, 'L');
		$pdf->Cell(40, 5, $cdata['total_kredit_limit'], 0, 0, 'L');
		$pdf->Cell(55, 5, $cdata['total_batas_tunai'], 0, 1, 'L');
		
		
		
		$x_rincian_master = $x_teks_master;
		$y_rincian_master = $y_teks_master + 74;
		$pdf->SetFont('helvetica','',8);
		$pdf->SetXY($x_rincian_master, $y_rincian_master - 10);
		$pdf->MultiCell(190,5,'Halaman: '.$pdf->PageNo(),0,'R');
		$pdf->SetFont('helvetica','B',10);
		$pdf->SetXY($x_rincian_master, $y_rincian_master - 5);
		$pdf->MultiCell(190,5,'Rincian Transaksi',0,'C');
		
		rectfill($pdf, 200, 0, $x_rincian_master, $y_rincian_master, 190, 8, "FD");
		$pdf->SetFont('Sandy','',7);
		$pdf->SetXY($x_rincian_master, $y_rincian_master);
		$pdf->Cell(30, 8, "Cardholder Number", 0, 0, 'C');
		$pdf->Cell(34, 8, "Cardholder Name", 0, 0, 'C');
		$pdf->Cell(21, 4, "Tagihan", 0, 0, 'C');
		$pdf->Cell(21, 4, "Pembayaran", 0, 0, 'C');
		$pdf->Cell(21, 4, "Pembelanjaan", 0, 0, 'C');
		$pdf->Cell(21, 4, "Pengambilan", 0, 0, 'C');
		$pdf->Cell(21, 4, "Finance", 0, 0, 'C');
		$pdf->Cell(21, 4, "Tagihan", 0, 1, 'C');
		$pdf->SetX($x_rincian_master + 64);
		$pdf->Cell(21, 4, "Sebelumnya", 0, 0, 'C');
		$pdf->Cell(21, 4, "Kredit (-)", 0, 0, 'C');
		$pdf->Cell(21, 4, "Debit (+)", 0, 0, 'C');
		$pdf->Cell(21, 4, "Tunai (+)", 0, 0, 'C');
		$pdf->Cell(21, 4, "Charge", 0, 0, 'C');
		$pdf->Cell(21, 4, "Baru (IDR)", 0, 1, 'C');
		
		
		
		$tbldetmst='
			<table width="70%">
				';
		$nheader=count($header['cdnumb']);
		for($i=0;$i<$nheader;$i++){
			$cdname=trim($header['cdname'][$i]);
			if(strlen($cdname)>18)
				$cdname=substr($cdname,0,18)."..";
			$tbldetmst.='
				<tr>
					<td width="21%" height="12">'.$header['cdnumb'][$i].'</td>
					<td width="24%">'.$cdname.'</td>
					<td width="15%" align="right">'.$header['tagihan_prev'][$i].'</td>
					<td width="15%" align="right">'.$header['pembayaran'][$i].'</td>
					<td width="15%" align="right">'.$header['pembelanjaan'][$i].'</td>
					<td width="15%" align="right">'.$header['ambil_tunai'][$i].'</td>
					<td width="15%" align="right">'.$header['fin_charge'][$i].'</td>
					<td width="15%" align="right">'.$header['tagihan_next'][$i].'</td>
				</tr>';
		}
		$tbldetmst.='
				<tr>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>TOTAL</td>
					<td align="right">'.$cdata['total_tagihan_prev'].'</td>
					<td align="right">'.$cdata['total_pembayaran'].'</td>
					<td align="right">'.$cdata['total_pembelanjaan'].'</td>
					<td align="right">'.$cdata['total_ambil_tunai'].'</td>
					<td align="right">'.$cdata['total_fin_charge'].'</td>
					<td align="right">'.$cdata['total_tagihan_next'].'</td>
				</tr>';
		$tbldetmst.="
			</table>";
		
		$pdf->SetFont('Sandy','',8);
		$pdf->SetY($pdf->GetY() + 5);
		$pdf->SetX($x_teks_master);$pdf->writeHTML($tbldetmst, true, false, true, false, '');
		
		//$y_ktk = $pdf->GetY() + 10);
		
		if($pdf->GetY() > 240)
		{
			$pdf->AddPage();
			$x_ktk = 10;
			$y_ktk = $pdf->GetY() + 5;
			rectfill($pdf, 50, 0, $x_ktk, $y_ktk, 60, 10, "");
			$pdf->SetFont('Sandy','',9);
			$pdf->SetXY($x_ktk+1, $y_ktk+1);
			$pdf->Cell(25, 4, "TOTAL PEMBAYARAN", 0, 0, 'L');
			$pdf->SetXY($x_ktk+1, $y_ktk+6);
			$pdf->Cell(25, 4, "DENGAN AUTODEBET", 0, 0, 'L');
			$pdf->SetX($x_ktk+35);				
			$pdf->Cell(25, 4, $cdata['ttl_autodebet'], 0, 0, 'L');
			
			$pdf->SetY($pdf->GetY() + 15);
			$pdf->SetFont('Sandy','',8);
			$pdf->SetX($x_teks_master + 45);
			$pdf->MultiCell(100,5,'Kami mengucapkan terima kasih atas kepercayaan dan kesetiaan Perusahaan Anda menggunakan Corporate Card kami. Pergunakan fasilitas auto debet untuk pembayaran Corporate Card Anda. Untuk informasi lebih lanjut hubungi Corporate Card Relationship Officer kami di (021) 575 1234.',0,'C');
			
			
			$pdf->SetY(255);
			$pdf->SetFont('Sandy','',9);
			$pdf->SetX($x_teks_master);
			$pdf->MultiCell(100,5,'KUALITAS KREDIT: ' . $cdata['kolektibilitas'],0,'L');
		}
		else
		{
			$x_ktk = 10;
			$y_ktk = $pdf->GetY() + 10;
			rectfill($pdf, 50, 0, $x_ktk, $y_ktk, 60, 10, "");
			$pdf->SetFont('Sandy','',9);
			$pdf->SetXY($x_ktk+1, $y_ktk+1);
			$pdf->Cell(25, 4, "TOTAL PEMBAYARAN", 0, 0, 'L');
			$pdf->SetXY($x_ktk+1, $y_ktk+6);
			$pdf->Cell(25, 4, "DENGAN AUTODEBET", 0, 0, 'L');
			$pdf->SetX($x_ktk+35);				
			$pdf->Cell(25, 4, $cdata['ttl_autodebet'], 0, 0, 'L');
			
			$pdf->SetY($pdf->GetY() + 15);
			$pdf->SetFont('Sandy','',8);
			$pdf->SetX($x_teks_master + 45);
			$pdf->MultiCell(100,5,'Kami mengucapkan terima kasih atas kepercayaan dan kesetiaan Perusahaan Anda menggunakan Corporate Card kami. Pergunakan fasilitas auto debet untuk pembayaran Corporate Card Anda. Untuk informasi lebih lanjut hubungi Corporate Card Relationship Officer kami di (021) 575 1234.',0,'C');
			
			
			$pdf->SetY(255);
			$pdf->SetFont('Sandy','',9);
			$pdf->SetX($x_teks_master);
			$pdf->MultiCell(100,5,'KUALITAS KREDIT: ' . $cdata['kolektibilitas'],0,'L');
		}
		
		
		//////////////////MULAI PEMBUATAN DETAIL//////////////////
		for($i=0;$i<$nheader;$i++){
			$x_teks_detail = 10;
			$y_teks_detail = 35;
			$pdf->AddPage();
			
			//$pdf->SetFont('helvetica','BI',13);
			$pdf->SetFont('helvetica','B',13);
			$pdf->SetXY($x_teks_detail, $y_teks_detail - 5);
			
			if($card_bin !== '618950')
			{
				$pdf->MultiCell(190,5,'Transaction Details',0,'C');
			}
			else
			{
				$pdf->MultiCell(190,5,'Riwayat Transaksi',0,'C');
			}
			
			$x_biodata = $x_teks_detail;
			$y_biodata = $y_teks_detail + 3;
			$pdf->SetFont('Sandy','',9);
			$pdf->SetXY($x_biodata, $y_biodata);
			$pdf->SetX($x_biodata);$pdf->MultiCell(50, 5, $cdata['custname2'], 0, 'L', 0, 1);
			$pdf->SetX($x_biodata);$pdf->MultiCell(50, 5, trim($header['cdname'][$i]), 0, 'L', 0, 1);
			
			
			$x_kotak2 = $x_teks_detail + 50;
			$y_kotak2 = $y_teks_detail + 3;
			rectfill($pdf, 200, 0, $x_kotak2, $y_kotak2, 140, 8, "FD");
			$pdf->SetFont('Sandy','',8);
			$pdf->SetXY($x_kotak2, $y_kotak2);
			$pdf->Cell(35, 4, "Pembelanjaan / Debit", 0, 0, 'L');
			$pdf->Cell(35, 4, "Pengambilan Tunai", 0, 0, 'L');
			$pdf->Cell(35, 4, "Tagihan Baru", 0, 0, 'L');
			$pdf->Cell(35, 4, "Sisa Kredit", 0, 1, 'L');
			$pdf->SetFont('Sandy2','');
			$pdf->SetX($x_kotak2);
			$pdf->Cell(35, 4, "Purchase / Debit", 0, 0, 'L');
			$pdf->Cell(35, 4, "Cash Advance", 0, 0, 'L');
			$pdf->Cell(35, 4, "New Balance", 0, 0, 'L');
			$pdf->Cell(35, 4, "Available Credit Limit", 0, 1, 'L');
			
			rectfill($pdf, 0, 0, $x_kotak2, $y_kotak2 + 8, 140, 4, "D");
			$pdf->SetFont('Sandy','');
			$pdf->SetX($x_kotak2);
			$pdf->Cell(35, 4, $header['pembelanjaan'][$i], 0, 0, 'L');
			$pdf->Cell(35, 4, $header['ambil_tunai'][$i], 0, 0, 'L');
			$pdf->Cell(35, 4, $header['tagihan_next'][$i], 0, 0, 'L');
			$pdf->Cell(35, 4, $header['kredit_sisa'][$i], 0, 1, 'L');
			
			
			
			$x_kotak = $x_teks_detail;
			$y_kotak = $y_teks_detail + 25;
			rectfill($pdf, 200, 0, $x_kotak, $y_kotak, 190, 10, "FD");
			$pdf->SetFont('Sandy','',9);
			$pdf->SetXY($x_kotak, $y_kotak);
			$pdf->Cell(35, 5, "Nomor Kartu", 0, 0, 'L');
			$pdf->Cell(30, 5, "Tanggal Cetak", 0, 0, 'L');
			$pdf->Cell(45, 5, "Jatuh Tempo Pembayaran", 0, 0, 'L');
			$pdf->Cell(35, 5, "Batas Kredit", 0, 0, 'L');
			$pdf->Cell(45, 5, "Batas Pengambilan Tunai", 0, 1, 'L');
			$pdf->SetFont('Sandy2','');
			$pdf->SetX($x_kotak);
			$pdf->Cell(35, 5, "Card Number", 0, 0, 'L');
			$pdf->Cell(30, 5, "Statement Date", 0, 0, 'L');
			$pdf->Cell(45, 5, "Payment Due Date", 0, 0, 'L');
			$pdf->Cell(35, 5, "Credit Limit", 0, 0, 'L');
			$pdf->Cell(45, 5, "Cash Advance Limit", 0, 1, 'L');
			
			rectfill($pdf, 0, 0, $x_kotak, $y_kotak + 10, 190, 5, "D");
			$pdf->SetFont('Sandy','');
			$pdf->SetX($x_kotak);
			$pdf->Cell(35, 5, $header['cdnumb'][$i], 0, 0, 'L');
			$pdf->Cell(30, 5, $cdata['dtcetak'], 0, 0, 'L');
			$pdf->Cell(45, 5, $cdata['dttempo'], 0, 0, 'L');
			$pdf->Cell(35, 5, $header['kredit_limit'][$i], 0, 0, 'L');
			$pdf->Cell(45, 5, $header['batas_tunai'][$i], 0, 1, 'L');
			
			
			
			$x_rincian_detail = $x_teks_detail;
			$y_rincian_detail = $y_teks_detail + 55;
			$pdf->SetFont('helvetica','',8);
			$pdf->SetXY($x_rincian_detail, $y_rincian_detail - 10);
			$pdf->MultiCell(190,5,'Halaman: '.$pdf->PageNo(),0,'R');
			$pdf->SetFont('helvetica','B',10);
			$pdf->SetXY($x_rincian_detail, $y_rincian_detail - 5);
			$pdf->MultiCell(190,5,'Rincian Transaksi',0,'C');
			
			rectfill($pdf, 200, 0, $x_rincian_detail, $y_rincian_detail, 190, 8, "FD");
			$pdf->SetFont('Sandy','',7);
			$pdf->SetXY($x_rincian_detail, $y_rincian_detail);
			$pdf->Cell(25, 4, "Tgl Transaksi", 0, 0, 'C');
			$pdf->Cell(25, 4, "Tgl Pembukuan", 0, 0, 'C');
			$pdf->Cell(60, 4, "Keterangan", 0, 0, 'C');
			$pdf->Cell(30, 4, "Transaksi Valas", 0, 0, 'C');
			$pdf->Cell(25, 4, "Nilai Tukar", 0, 0, 'C');
			$pdf->Cell(25, 4, "Jumlah (Rp)", 0, 1, 'C');
			$pdf->SetFont('Sandy2','');
			$pdf->SetX($x_rincian_detail);
			$pdf->Cell(25, 4, "Transaction Date", 0, 0, 'C');
			$pdf->Cell(25, 4, "Posting Date", 0, 0, 'C');
			$pdf->Cell(60, 4, "Description", 0, 0, 'C');
			$pdf->Cell(30, 4, "Forex Transaction", 0, 0, 'C');
			$pdf->Cell(25, 4, "Kurs", 0, 0, 'C');
			$pdf->Cell(25, 4, "Amount (IDR)", 0, 1, 'C');
			
			
			
			$pdf->SetY($pdf->GetY() + 5);
			$pdf->SetFont('Sandy','',9);
			$pdf->SetX($x_rincian_detail);
			//$pdf->SetXY($x_rincian_detail, $y_rincian_detail);
			$pdf->Cell(25, 5, "", 0, 0, 'L');
			$pdf->Cell(25, 5, "", 0, 0, 'L');
			$pdf->Cell(60, 5, "Tagihan Bulan Lalu", 0, 0, 'L');$arr_summary[$cdata['no_card_dat']][$header['cdnumb'][$i]]['start'] = $pdf->PageNo();###
			$pdf->Cell(30, 5, "", 0, 0, 'L');$arr_summary[$cdata['no_card_dat']][$header['cdnumb'][$i]]['nama1'] = $cdata['custname2'];###
			$pdf->Cell(25, 5, "", 0, 0, 'L');$arr_summary[$cdata['no_card_dat']][$header['cdnumb'][$i]]['nama2'] = trim($header['cdname'][$i]);###
			$pdf->Cell(25, 5, $header['tagihan_prev'][$i], 0, 1, 'R');
			
			$pdf->Ln(5);
			
			$pdf->SetX($x_rincian_detail);
			$pdf->Cell(35, 5, $header['cdnumb'][$i], 0, 0, 'L');
			$pdf->Cell(35, 5, trim($header['cdname'][$i]), 0, 1, 'L');
			
			$ndetail=count($detail['dttrans'][$i]);
			for($x=0;$x<$ndetail;$x++){
				if($pdf->GetY() > 260){
					$pdf->AddPage(); 
					$pdf->SetX($x_rincian_detail);
					$pdf->Cell(190, 5, 'Halaman: '.$pdf->PageNo(), 0, 1, 'R');
					
					$pdf->Ln(3);
					
					$pdf->SetFont('helvetica','B',10);
					$pdf->SetX($x_rincian_detail);
					$pdf->MultiCell(190,5,'Rincian Transaksi',0,'C');
					
					rectfill($pdf, 200, 0, $x_rincian_detail, $pdf->GetY(), 190, 8, "FD");
					$pdf->SetFont('Sandy','',7);
					$pdf->SetXY($x_rincian_detail, $pdf->GetY());
					$pdf->Cell(25, 4, "Tgl Transaksi", 0, 0, 'C');
					$pdf->Cell(25, 4, "Tgl Pembukuan", 0, 0, 'C');
					$pdf->Cell(60, 4, "Keterangan", 0, 0, 'C');
					$pdf->Cell(30, 4, "Transaksi Valas", 0, 0, 'C');
					$pdf->Cell(25, 4, "Nilai Tukar", 0, 0, 'C');
					$pdf->Cell(25, 4, "Jumlah (Rp)", 0, 1, 'C');
					$pdf->SetFont('Sandy2','');
					$pdf->SetX($x_rincian_detail);
					$pdf->Cell(25, 4, "Transaction Date", 0, 0, 'C');
					$pdf->Cell(25, 4, "Posting Date", 0, 0, 'C');
					$pdf->Cell(60, 4, "Description", 0, 0, 'C');
					$pdf->Cell(30, 4, "Forex Transaction", 0, 0, 'C');
					$pdf->Cell(25, 4, "Kurs", 0, 0, 'C');
					$pdf->Cell(25, 4, "Amount (IDR)", 0, 1, 'C');
					
					$pdf->Ln(5);
				}
				
				$pdf->SetFont('Sandy','',9);
				if(strtoupper($detail['cr'][$i][$x])=='CR') $cr='CR'; else $cr='';
				$pdf->SetX($x_rincian_detail);
				//$pdf->SetXY($x_rincian_detail, $y_rincian_detail);
				//$pdf->Cell(10, 5, $pdf->GetY(), 0, 0, 'L');
				$pdf->Cell(25, 5, $detail['dttrans'][$i][$x], 0, 0, 'L');
				$pdf->Cell(25, 5, $detail['dtbuku'][$i][$x], 0, 0, 'L');
				$pdf->Cell(65, 5, $detail['trans'][$i][$x], 0, 0, 'L');
				$pdf->Cell(7, 5, $detail['forexcurr'][$i][$x], 0, 0, 'L');
				$pdf->Cell(19, 5, $detail['forexnominal'][$i][$x], 0, 0, 'R');
				$pdf->Cell(25, 5, $detail['kursnominal'][$i][$x], 0, 0, 'R');
				$pdf->Cell(23, 5, $detail['jmltrans'][$i][$x], 0, 0, 'R');
				$pdf->SetX($pdf->GetX()-2);$pdf->Cell(5, 5, $cr, 0, 1, 'L');
			}
			
			$pdf->Ln(5);
			
			if($pdf->GetY() > 260){
				$pdf->AddPage();
				$pdf->SetX($x_rincian_detail);
				$pdf->Cell(190, 5, 'Halaman: '.$pdf->PageNo(), 0, 1, 'R');
				
				$pdf->Ln(3);
				
				$pdf->SetFont('helvetica','B',10);
				$pdf->SetX($x_rincian_detail);
				$pdf->MultiCell(190,5,'Rincian Transaksi',0,'C');
				
				rectfill($pdf, 200, 0, $x_rincian_detail, $pdf->GetY(), 190, 8, "FD");
				$pdf->SetFont('Sandy','',7);
				$pdf->SetXY($x_rincian_detail, $pdf->GetY());
				$pdf->Cell(25, 4, "Tgl Transaksi", 0, 0, 'C');
				$pdf->Cell(25, 4, "Tgl Pembukuan", 0, 0, 'C');
				$pdf->Cell(60, 4, "Keterangan", 0, 0, 'C');
				$pdf->Cell(30, 4, "Transaksi Valas", 0, 0, 'C');
				$pdf->Cell(25, 4, "Nilai Tukar", 0, 0, 'C');
				$pdf->Cell(25, 4, "Jumlah (Rp)", 0, 1, 'C');
				$pdf->SetFont('Sandy2','');
				$pdf->SetX($x_rincian_detail);
				$pdf->Cell(25, 4, "Transaction Date", 0, 0, 'C');
				$pdf->Cell(25, 4, "Posting Date", 0, 0, 'C');
				$pdf->Cell(60, 4, "Description", 0, 0, 'C');
				$pdf->Cell(30, 4, "Forex Transaction", 0, 0, 'C');
				$pdf->Cell(25, 4, "Kurs", 0, 0, 'C');
				$pdf->Cell(25, 4, "Amount (IDR)", 0, 1, 'C');
				
				$pdf->Ln(5);
			}
			
			$pdf->SetFont('Sandy','',9);
			$pdf->SetX($x_rincian_detail + 50);
			$pdf->Cell(115, 5, "Total Interest & Service Charge", 0, 0, 'L');
			$pdf->Cell(23, 5, $header['fin_charge'][$i], 0, 1, 'R');
			
			$pdf->Ln(5);
			
			if($pdf->GetY() > 260){
				$pdf->AddPage();
				$pdf->SetX($x_rincian_detail);
				$pdf->Cell(190, 5, 'Halaman: '.$pdf->PageNo(), 0, 1, 'R');
				
				$pdf->Ln(3);
				
				$pdf->SetFont('helvetica','B',10);
				$pdf->SetX($x_rincian_detail);
				$pdf->MultiCell(190,5,'Rincian Transaksi',0,'C');
				
				rectfill($pdf, 200, 0, $x_rincian_detail, $pdf->GetY(), 190, 8, "FD");
				$pdf->SetFont('Sandy','',7);
				$pdf->SetXY($x_rincian_detail, $pdf->GetY());
				$pdf->Cell(25, 4, "Tgl Transaksi", 0, 0, 'C');
				$pdf->Cell(25, 4, "Tgl Pembukuan", 0, 0, 'C');
				$pdf->Cell(60, 4, "Keterangan", 0, 0, 'C');
				$pdf->Cell(30, 4, "Transaksi Valas", 0, 0, 'C');
				$pdf->Cell(25, 4, "Nilai Tukar", 0, 0, 'C');
				$pdf->Cell(25, 4, "Jumlah (Rp)", 0, 1, 'C');
				$pdf->SetFont('Sandy2','');
				$pdf->SetX($x_rincian_detail);
				$pdf->Cell(25, 4, "Transaction Date", 0, 0, 'C');
				$pdf->Cell(25, 4, "Posting Date", 0, 0, 'C');
				$pdf->Cell(60, 4, "Description", 0, 0, 'C');
				$pdf->Cell(30, 4, "Forex Transaction", 0, 0, 'C');
				$pdf->Cell(25, 4, "Kurs", 0, 0, 'C');
				$pdf->Cell(25, 4, "Amount (IDR)", 0, 1, 'C');
				
				$pdf->Ln(5);
			}
			
			$pdf->SetFont('Sandy','',9);
			$pdf->SetX($x_rincian_detail + 50);
			$pdf->Cell(115, 5, "Tagihan Bulan Ini", 0, 0, 'L');$arr_summary[$cdata['no_card_dat']][$header['cdnumb'][$i]]['end'] = $pdf->PageNo();###
			$pdf->Cell(23, 5, $header['tagihan_next'][$i], 0, 1, 'R');
			
			$pdf->SetY(255);
			$pdf->SetFont('Sandy','',8);
			$pdf->SetX($x_teks_detail);
			$pdf->MultiCell(190,5,'Nilai tukar Transaksi Valas sudah memperhitungkan komponen Biaya Penggunaan Kartu Kredit di Luar Negeri',0,'L');
		}
		//////////////////SELESAI PEMBUATAN DETAIL//////////////////
		
		
		
		$pdf_name = $cdata['no_card_dat'];
		$pass_admin_pdf = "app123456";
		$tot_hlm = $pdf->PageNo();
		
		
		
		$file = str_replace(substr($file,-4),'',$file);
		$flag_pdf = '../pdf/'.$flagtrans.'/';
		$blth_pdf = $flag_pdf.$blth.'/';
		$blth_file_pdf = $blth_pdf.$file.'/';
		
		//folder yang diambil oleh estatement nanti untuk keperluan di inject password dan gambar
		$blth_file_cetak = $blth_pdf.$file.'/cetak/';
		$blth_file_cetak_1 = $blth_pdf.$file.'/cetak_clean/';		
		
		//folder tujuan untuk keperluan data cetak
		$blth_file_cetak2 = $blth_pdf.$file.'/cetak/'.$dt_kurir1.'/';
		$blth_file_cetak2_1 = $blth_pdf.$file.'/cetak_clean/'.$dt_kurir1.'/';
		
		//folder pemisahan desain reguler dan kki
		$blth_file_cetak_r = $blth_pdf.$file.'/cetak/'.$dt_kurir1.'/REG/';
		$blth_file_cetak_r2 = $blth_pdf.$file.'/cetak_clean/'.$dt_kurir1.'/REG/';
		$blth_file_cetak_k = $blth_pdf.$file.'/cetak/'.$dt_kurir1.'/KKI/';
		$blth_file_cetak_k2 = $blth_pdf.$file.'/cetak_clean/'.$dt_kurir1.'/KKI/';
		
		//folder tujuan untuk keperluan data waybill
		$blth_file_cetak3 = $blth_pdf.$file.'/produksi/';
		
		
		if(file_exists($flag_pdf)==false) mkdir($flag_pdf);
		if(file_exists($blth_pdf)==false) mkdir($blth_pdf);
		if(file_exists($blth_file_pdf)==false) mkdir($blth_file_pdf);
		if(file_exists($blth_file_cetak)==false) mkdir($blth_file_cetak);
		if(file_exists($blth_file_cetak_1)==false) mkdir($blth_file_cetak_1);
		if(file_exists($blth_file_cetak2)==false) mkdir($blth_file_cetak2);
		if(file_exists($blth_file_cetak2_1)==false) mkdir($blth_file_cetak2_1);
		if(file_exists($blth_file_cetak_k)==false) mkdir($blth_file_cetak_k);
		if(file_exists($blth_file_cetak_r)==false) mkdir($blth_file_cetak_r);
		if(file_exists($blth_file_cetak_k2)==false) mkdir($blth_file_cetak_k2);
		if(file_exists($blth_file_cetak_r2)==false) mkdir($blth_file_cetak_r2);
		if(file_exists($blth_file_cetak3)==false) mkdir($blth_file_cetak3);
		$file_copy = $pdf_name;
		
		
		if($card_bin !== '618950')
		{	//bukan KKI
			$blth_file_cetak2 = $blth_file_cetak_r;
			$blth_file_cetak2_1 = $blth_file_cetak_r2;
		}
		else
		{	//KKI
			$blth_file_cetak2 = $blth_file_cetak_r;
			$blth_file_cetak2_1 = $blth_file_cetak_k2;
		}
		
		$cetak_output = $blth_file_cetak2.$pdf_name;
		if(file_exists($cetak_output)){
			//unlink($cetak_output);
		}
		$pdf->Output($cetak_output,'F');
		
		if ( preg_match("/X/i",  $pdf_name ) || preg_match("/x/i", $pdf_name ) )
		{
			
		}else{
			copy($cetak_output,  $blth_file_cetak2_1.$pdf_name);	
		}
		
		
		
		
		
		
		
		
		
		
		/// MEMBUAT TEMPORARY NOMOR REKENING YANG DI CASE SPEDIAL ------------------------------------------------------------------------
		###die(print_r($arr_summary));
		
		
		$split_pdf = 'off';
		$n_split = 0;
		$cdata_split = array();
		foreach ($arr_summary as $key => $value) 
		{
			foreach ($value as $key2 => $value2) 
			{
				###echo "$key2###"; //nomor rekening
				$n_split++;
				$nomor_induk = $key;
				$nomor_kartu_cc = $key2;
				$nomor_kartu_cc = str_replace('-', '', $nomor_kartu_cc);
				
				$pdf_name_split = $nomor_induk."_".$n_split;
				$cdata_split['induk'] =$nomor_induk;
				$cdata_split['anak'] = $nomor_kartu_cc;
				$cdata_split['nama1'] = $value2['nama1'];
				$cdata_split['nama2'] = $value2['nama2'];
				
				$sql_cek_m_customer = "SELECT nomor_rekening, password_pdf, email1
										FROM m_customer
										WHERE blth = '$blth'
											AND flagtrans = '$flagtrans'";
				if( $GLOBALS['file_mcustomer'] != ''){
						$sql_cek_m_customer .= " AND log_customer_id = ".$GLOBALS['file_mcustomer']." ";
					}
				$sql_cek_m_customer .= " AND  ( nomor_rekening = '$nomor_kartu_cc' OR nomor_rekening = '$key2' )";
				###die($sql_cek_m_customer);
				
				$qry_cek_m_customer = pg_query($sql_cek_m_customer) or die('ERROR cek m_customer: '.$sql_cek_m_customer);
				$jml_customer = pg_num_rows($qry_cek_m_customer);
				//$jml_customer = 0;
				if ( $jml_customer > 0)
				{
					$split_pdf = 'on';
					
					$email_x = '';
					while($row_email = pg_fetch_array($qry_cek_m_customer)){
						$email_x .= $row_email['email1'].";";
						$password_pdf_x = $row_email['password_pdf'];
					}
					
					$jml_hlm_split = ($value2['end'] - $value2['start']) +1;
					###insert_detail_split($m_loading_id,$cdata,$flagtrans,$blth,$file,$pdf_name,$password_pdf,$jml_hlm,$tabeldetail,$email,$barcode, $dt_kurir1);
							
					insert_detail_split(0,$cdata_split,
							$flagtrans,$blth,$file.'.DAT',
							$pdf_name_split,$password_pdf_x,$jml_hlm_split,'tmp_split',$email_x,'', '', $value2['start'], $value2['end']);
				}
			}
		}
		
		//MEMBUAT SPLIT PDF ESTATEMENT
		if($split_pdf == "on"){
			copy_insert_password_split_no_pass($blth_file_cetak2, $blth_file_pdf, $file_copy, $password_pdf, $pass_admin_pdf, $card_bin);
		}
		//MEMBUAT SPLIT PDF ESTATEMENT
		/// MEMBUAT TEMPORARY NOMOR REKENING YANG DI CASE SPEDIAL ------------------------------------------------------------------------
		
		
		
		
		
		
		
		
		
		
		//bila sudah terbentuk di folder cetak, maka diinject untuk keperluan estatement
		if($flag_case == "Y"){
			copy_insert_password($blth_file_cetak2, $blth_file_pdf, $file_copy, $password_pdf, $pass_admin_pdf, $card_bin);
		}
		
		//pembuatan untuk waybill FICC
		if($dt_kurir1 == 'FICC')
		{
			#gen_waybill_ficc($blth_file_cetak3, $blth, $header, $cdata, $detail, $barcode);
		}
		
		return $pdf_name.'|'.$tot_hlm;
	}
	
	function create_p01($blth,$file,$total_hlm_cetak,$customer_cetak,$flagtrans){
		$pdf = new MYPDF('L', PDF_UNIT, array(210, 297), true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor(PDF_AUTHOR);
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->SetMargins(0, 35);
		$pdf->setPrintHeader(TRUE);
		$pdf->setPrintFooter(FALSE);
		
		$pdf->AddPage();
		
		$x_teks = 10;
		$y_teks = 25;
		
		$pdf->SetFont('helvetica','BIU',32);
		$pdf->SetXY($x_teks, $y_teks);
		$pdf->Cell(100, 5, "BRI CORPORATE", 0, 0, "L");
		
		$pdf->SetFont('','B',12);
		$pdf->SetXY($x_teks + 150, $y_teks + 5);
		$pdf->Cell(100, 5, date('l, F j, Y, H:i:s'), 0, 0, "L");
		
		$pdf->SetFont('','B',30);
		$pdf->SetXY($x_teks + 250, $y_teks);
		$pdf->Cell(25, 5, "P-01", 1, 0, "L");
		
		$x_kotak = 10;
		$y_kotak = 50;
		
		rectfill($pdf, 200, 0, $x_kotak, $y_kotak, 40, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 40, $y_kotak, 20, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 60, $y_kotak, 20, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 80, $y_kotak, 20, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 100, $y_kotak, 40, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 140, $y_kotak, 20, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 160, $y_kotak, 40, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 200, $y_kotak, 20, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 220, $y_kotak, 20, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 240, $y_kotak, 20, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 260, $y_kotak, 10, 12, "FD");
		
		$pdf->SetFont('','',12);
		$pdf->SetXY($x_kotak, $y_kotak);
		$pdf->Cell(40, 12, "NAMA CABANG", 0, 0, "C");
		
		$pdf->SetXY($x_kotak + 40, $y_kotak);
		$pdf->Cell(20, 6, "JML", 0, 1, "C");
		$pdf->SetX($x_kotak + 40);
		$pdf->Cell(20, 6, "CUST", 0, 0, "C");
		
		$pdf->SetXY($x_kotak + 60, $y_kotak);
		$pdf->Cell(20, 6, "JML", 0, 1, "C");
		$pdf->SetX($x_kotak + 60);
		$pdf->Cell(20, 6, "HAL", 0, 0, "C");
		
		$pdf->SetXY($x_kotak + 80, $y_kotak);
		$pdf->Cell(20, 6, "JML", 0, 1, "C");
		$pdf->SetX($x_kotak + 80);
		$pdf->Cell(20, 6, "AMPLOP", 0, 0, "C");
		
		$pdf->SetXY($x_kotak + 100, $y_kotak);
		$pdf->Cell(40, 6, "NO URUT", 0, 1, "C");
		$pdf->SetX($x_kotak + 100);
		$pdf->Cell(20, 6, "AWAL", 1, 0, "C");
		$pdf->Cell(20, 6, "AKHIR", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 140, $y_kotak);
		$pdf->Cell(20, 6, "TGL", 0, 1, "C");
		$pdf->SetX($x_kotak + 140);
		$pdf->Cell(20, 6, "PROSES", 0, 0, "C");
		
		$pdf->SetXY($x_kotak + 160, $y_kotak);
		$pdf->Cell(40, 6, "JAM PROSES", 0, 1, "C");
		$pdf->SetX($x_kotak + 160);
		$pdf->Cell(20, 6, "MULAI", 1, 0, "C");
		$pdf->Cell(20, 6, "SELESAI", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 200, $y_kotak);
		$pdf->Cell(20, 6, "YANG", 0, 1, "C");
		$pdf->SetX($x_kotak + 200);
		$pdf->Cell(20, 6, "PROSES", 0, 0, "C");
		
		$pdf->SetXY($x_kotak + 220, $y_kotak);
		$pdf->Cell(20, 6, "YANG", 0, 1, "C");
		$pdf->SetX($x_kotak + 220);
		$pdf->Cell(20, 6, "TERIMA", 0, 0, "C");
		
		$pdf->SetXY($x_kotak + 240, $y_kotak);
		$pdf->Cell(20, 12, "STATUS", 0, 0, "C");
		
		$pdf->SetXY($x_kotak + 260, $y_kotak);
		$pdf->Cell(10, 12, "TT", 0, 1, "C");
		
		
		
		$x_kotak = 10;
		$y_kotak = 62;
		
		$pdf->SetFont('','',12);
		$pdf->SetXY($x_kotak, $y_kotak);
		$pdf->Cell(40, 12, str_replace(substr($file,-4),'',$file), 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 40, $y_kotak);
		$pdf->Cell(20, 12, $customer_cetak, 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 60, $y_kotak);
		$pdf->Cell(20, 12, $total_hlm_cetak, 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 80, $y_kotak);
		$pdf->Cell(20, 12, $customer_cetak, 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 100, $y_kotak);
		$pdf->Cell(20, 12, "", 1, 0, "C");
		$pdf->Cell(20, 12, "", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 140, $y_kotak);
		$pdf->Cell(20, 12, "", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 160, $y_kotak);
		$pdf->Cell(20, 12, "", 1, 0, "C");
		$pdf->Cell(20, 12, "", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 200, $y_kotak);
		$pdf->Cell(20, 12, "", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 220, $y_kotak);
		$pdf->Cell(20, 12, "", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 240, $y_kotak);
		$pdf->Cell(20, 12, "", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 260, $y_kotak);
		$pdf->Cell(10, 12, "", 1, 0, "C");
		
		
		
		
		$x_kotak = 10;
		$y_kotak = 74;
		
		$pdf->SetFont('','B',12);
		$pdf->SetXY($x_kotak, $y_kotak);
		$pdf->Cell(40, 12, "SUB TOTAL", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 40, $y_kotak);
		$pdf->Cell(20, 12, $customer_cetak, 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 60, $y_kotak);
		$pdf->Cell(20, 12, $total_hlm_cetak, 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 80, $y_kotak);
		$pdf->Cell(20, 12, $customer_cetak, 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 100, $y_kotak);
		$pdf->Cell(170, 12, "", 1, 0, "C");
		
		$file = str_replace(substr($file,-4),'',$file);	
		$flag_pdf = '../pdf/'.$flagtrans.'/';
		$blth_pdf = $flag_pdf.$blth.'/';
		$blth_file_pdf = $blth_pdf.$file.'/';
		$blth_file_pdf_cetak = $blth_file_pdf.'produksi/';
		if(file_exists($flag_pdf)==false) mkdir($flag_pdf);
		if(file_exists($blth_pdf)==false) mkdir($blth_pdf);
		if(file_exists($blth_file_pdf)==false) mkdir($blth_file_pdf);
		if(file_exists($blth_file_pdf_cetak)==false) mkdir($blth_file_pdf_cetak);
		$pdf_name = $file . "_p01.pdf";
		$cetak_p01 = $blth_file_pdf.$pdf_name;
		
		$pdf->Output($cetak_p01,'F');
		
		//menambahkan langkah untuk copy file p01 ke folder produksi
		if(file_exists($cetak_p01) == true)
		{
			//mkdir(dirname($dstfile), 0777, true);
			copy($cetak_p01, $blth_file_pdf_cetak.$pdf_name);		
		}
	}
	
	function create_p02($blth,$file,$total_hlm_cetak,$customer_cetak,$flagtrans){
		$pdf = new MYPDF('L', PDF_UNIT, array(210, 297), true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor(PDF_AUTHOR);
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->SetMargins(0, 35);
		$pdf->setPrintHeader(TRUE);
		$pdf->setPrintFooter(FALSE);
		
		$pdf->AddPage();
		
		$x_teks = 10;
		$y_teks = 25;
		
		$pdf->SetFont('helvetica','BIU',32);
		$pdf->SetXY($x_teks, $y_teks);
		$pdf->Cell(100, 5, "BRI CORPORATE", 0, 0, "L");
		
		$pdf->SetFont('','B',12);
		$pdf->SetXY($x_teks + 150, $y_teks + 5);
		$pdf->Cell(100, 5, date('l, F j, Y, H:i:s'), 0, 0, "L");
		
		$pdf->SetFont('','B',30);
		$pdf->SetXY($x_teks + 250, $y_teks);
		$pdf->Cell(25, 5, "P-02", 1, 0, "L");
		
		$x_kotak = 10;
		$y_kotak = 50;
		
		rectfill($pdf, 200, 0, $x_kotak, $y_kotak, 40, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 40, $y_kotak, 20, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 60, $y_kotak, 20, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 80, $y_kotak, 20, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 100, $y_kotak, 40, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 140, $y_kotak, 20, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 160, $y_kotak, 40, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 200, $y_kotak, 20, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 220, $y_kotak, 20, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 240, $y_kotak, 20, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 260, $y_kotak, 10, 12, "FD");
		
		$pdf->SetFont('','',12);
		$pdf->SetXY($x_kotak, $y_kotak);
		$pdf->Cell(40, 12, "NAMA CABANG", 0, 0, "C");
		
		$pdf->SetXY($x_kotak + 40, $y_kotak);
		$pdf->Cell(20, 6, "JML", 0, 1, "C");
		$pdf->SetX($x_kotak + 40);
		$pdf->Cell(20, 6, "CUST", 0, 0, "C");
		
		$pdf->SetXY($x_kotak + 60, $y_kotak);
		$pdf->Cell(20, 6, "JML", 0, 1, "C");
		$pdf->SetX($x_kotak + 60);
		$pdf->Cell(20, 6, "HAL", 0, 0, "C");
		
		$pdf->SetXY($x_kotak + 80, $y_kotak);
		$pdf->Cell(20, 6, "JML", 0, 1, "C");
		$pdf->SetX($x_kotak + 80);
		$pdf->Cell(20, 6, "AMPLOP", 0, 0, "C");
		
		$pdf->SetXY($x_kotak + 100, $y_kotak);
		$pdf->Cell(40, 6, "NO URUT", 0, 1, "C");
		$pdf->SetX($x_kotak + 100);
		$pdf->Cell(20, 6, "AWAL", 1, 0, "C");
		$pdf->Cell(20, 6, "AKHIR", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 140, $y_kotak);
		$pdf->Cell(20, 6, "TGL", 0, 1, "C");
		$pdf->SetX($x_kotak + 140);
		$pdf->Cell(20, 6, "PROSES", 0, 0, "C");
		
		$pdf->SetXY($x_kotak + 160, $y_kotak);
		$pdf->Cell(40, 6, "JAM PROSES", 0, 1, "C");
		$pdf->SetX($x_kotak + 160);
		$pdf->Cell(20, 6, "MULAI", 1, 0, "C");
		$pdf->Cell(20, 6, "SELESAI", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 200, $y_kotak);
		$pdf->Cell(20, 6, "YANG", 0, 1, "C");
		$pdf->SetX($x_kotak + 200);
		$pdf->Cell(20, 6, "PROSES", 0, 0, "C");
		
		$pdf->SetXY($x_kotak + 220, $y_kotak);
		$pdf->Cell(20, 6, "YANG", 0, 1, "C");
		$pdf->SetX($x_kotak + 220);
		$pdf->Cell(20, 6, "TERIMA", 0, 0, "C");
		
		$pdf->SetXY($x_kotak + 240, $y_kotak);
		$pdf->Cell(20, 12, "STATUS", 0, 0, "C");
		
		$pdf->SetXY($x_kotak + 260, $y_kotak);
		$pdf->Cell(10, 12, "TT", 0, 1, "C");
		
		
		
		$x_kotak = 10;
		$y_kotak = 62;
		
		$pdf->SetFont('','',12);
		$pdf->SetXY($x_kotak, $y_kotak);
		$pdf->Cell(40, 12, str_replace(substr($file,-4),'',$file), 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 40, $y_kotak);
		$pdf->Cell(20, 12, $customer_cetak, 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 60, $y_kotak);
		$pdf->Cell(20, 12, $total_hlm_cetak, 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 80, $y_kotak);
		$pdf->Cell(20, 12, $customer_cetak, 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 100, $y_kotak);
		$pdf->Cell(20, 12, "", 1, 0, "C");
		$pdf->Cell(20, 12, "", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 140, $y_kotak);
		$pdf->Cell(20, 12, "", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 160, $y_kotak);
		$pdf->Cell(20, 12, "", 1, 0, "C");
		$pdf->Cell(20, 12, "", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 200, $y_kotak);
		$pdf->Cell(20, 12, "", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 220, $y_kotak);
		$pdf->Cell(20, 12, "", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 240, $y_kotak);
		$pdf->Cell(20, 12, "", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 260, $y_kotak);
		$pdf->Cell(10, 12, "", 1, 0, "C");
		
		
		
		
		$x_kotak = 10;
		$y_kotak = 74;
		
		$pdf->SetFont('','B',12);
		$pdf->SetXY($x_kotak, $y_kotak);
		$pdf->Cell(40, 12, "SUB TOTAL", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 40, $y_kotak);
		$pdf->Cell(20, 12, $customer_cetak, 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 60, $y_kotak);
		$pdf->Cell(20, 12, $total_hlm_cetak, 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 80, $y_kotak);
		$pdf->Cell(20, 12, $customer_cetak, 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 100, $y_kotak);
		$pdf->Cell(170, 12, "", 1, 0, "C");
		
		$file = str_replace(substr($file,-4),'',$file);
		$flag_pdf = '../pdf/'.$flagtrans.'/';
		$blth_pdf = $flag_pdf.$blth.'/';
		$blth_file_pdf = $blth_pdf.$file.'/';
		$blth_file_pdf_cetak = $blth_file_pdf.'produksi/';
		if(file_exists($flag_pdf)==false) mkdir($flag_pdf);
		if(file_exists($blth_pdf)==false) mkdir($blth_pdf);
		if(file_exists($blth_file_pdf)==false) mkdir($blth_file_pdf);
		if(file_exists($blth_file_pdf_cetak)==false) mkdir($blth_file_pdf_cetak);
		$pdf_name = $file . "_p02.pdf";
		$cetak_p02 = $blth_file_pdf.$pdf_name;
		
		$pdf->Output($cetak_p02,'F');
		
		//menambahkan langkah untuk copy file p01 ke folder produksi
		if(file_exists($cetak_p02) == true)
		{
			//mkdir(dirname($dstfile), 0777, true);
			copy($cetak_p02, $blth_file_pdf_cetak.$pdf_name);		
		}

	}
	
	function create_q01($blth,$file,$total_hlm_cetak,$customer_cetak,$flagtrans){
		$pdf = new MYPDF('L', PDF_UNIT, array(210, 297), true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor(PDF_AUTHOR);
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->SetMargins(0, 35);
		$pdf->setPrintHeader(TRUE);
		$pdf->setPrintFooter(FALSE);
		
		$pdf->AddPage();
		
		$x_teks = 10;
		$y_teks = 25;
		
		$pdf->SetFont('helvetica','BIU',32);
		$pdf->SetXY($x_teks, $y_teks);
		$pdf->Cell(100, 5, "BRI CORPORATE", 0, 0, "L");
		
		$pdf->SetFont('','B',12);
		$pdf->SetXY($x_teks + 150, $y_teks + 5);
		$pdf->Cell(100, 5, date('l, F j, Y, H:i:s'), 0, 0, "L");
		
		$pdf->SetFont('','B',30);
		$pdf->SetXY($x_teks + 250, $y_teks);
		$pdf->Cell(25, 5, "Q-01", 1, 0, "L");
		
		$x_kotak = 10;
		$y_kotak = 50;
		
		rectfill($pdf, 200, 0, $x_kotak, $y_kotak, 40, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 40, $y_kotak, 20, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 60, $y_kotak, 20, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 80, $y_kotak, 20, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 100, $y_kotak, 40, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 140, $y_kotak, 20, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 160, $y_kotak, 40, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 200, $y_kotak, 20, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 220, $y_kotak, 20, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 240, $y_kotak, 20, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 260, $y_kotak, 10, 12, "FD");
		
		$pdf->SetFont('','',12);
		$pdf->SetXY($x_kotak, $y_kotak);
		$pdf->Cell(40, 12, "NAMA CABANG", 0, 0, "C");
		
		$pdf->SetXY($x_kotak + 40, $y_kotak);
		$pdf->Cell(20, 6, "JML", 0, 1, "C");
		$pdf->SetX($x_kotak + 40);
		$pdf->Cell(20, 6, "CUST", 0, 0, "C");
		
		$pdf->SetXY($x_kotak + 60, $y_kotak);
		$pdf->Cell(20, 6, "JML", 0, 1, "C");
		$pdf->SetX($x_kotak + 60);
		$pdf->Cell(20, 6, "HAL", 0, 0, "C");
		
		$pdf->SetXY($x_kotak + 80, $y_kotak);
		$pdf->Cell(20, 6, "JML", 0, 1, "C");
		$pdf->SetX($x_kotak + 80);
		$pdf->Cell(20, 6, "AMPLOP", 0, 0, "C");
		
		$pdf->SetXY($x_kotak + 100, $y_kotak);
		$pdf->Cell(40, 6, "NO URUT", 0, 1, "C");
		$pdf->SetX($x_kotak + 100);
		$pdf->Cell(20, 6, "AWAL", 1, 0, "C");
		$pdf->Cell(20, 6, "AKHIR", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 140, $y_kotak);
		$pdf->Cell(20, 6, "TGL", 0, 1, "C");
		$pdf->SetX($x_kotak + 140);
		$pdf->Cell(20, 6, "PROSES", 0, 0, "C");
		
		$pdf->SetXY($x_kotak + 160, $y_kotak);
		$pdf->Cell(40, 6, "JAM PROSES", 0, 1, "C");
		$pdf->SetX($x_kotak + 160);
		$pdf->Cell(20, 6, "MULAI", 1, 0, "C");
		$pdf->Cell(20, 6, "SELESAI", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 200, $y_kotak);
		$pdf->Cell(20, 6, "YANG", 0, 1, "C");
		$pdf->SetX($x_kotak + 200);
		$pdf->Cell(20, 6, "PROSES", 0, 0, "C");
		
		$pdf->SetXY($x_kotak + 220, $y_kotak);
		$pdf->Cell(20, 6, "YANG", 0, 1, "C");
		$pdf->SetX($x_kotak + 220);
		$pdf->Cell(20, 6, "TERIMA", 0, 0, "C");
		
		$pdf->SetXY($x_kotak + 240, $y_kotak);
		$pdf->Cell(20, 12, "STATUS", 0, 0, "C");
		
		$pdf->SetXY($x_kotak + 260, $y_kotak);
		$pdf->Cell(10, 12, "TT", 0, 1, "C");
		
		
		
		$x_kotak = 10;
		$y_kotak = 62;
		
		$pdf->SetFont('','',12);
		$pdf->SetXY($x_kotak, $y_kotak);
		$pdf->Cell(40, 12, str_replace(substr($file,-4),'',$file), 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 40, $y_kotak);
		$pdf->Cell(20, 12, "", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 60, $y_kotak);
		$pdf->Cell(20, 12, "", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 80, $y_kotak);
		$pdf->Cell(20, 12, "", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 100, $y_kotak);
		$pdf->Cell(20, 12, "", 1, 0, "C");
		$pdf->Cell(20, 12, "", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 140, $y_kotak);
		$pdf->Cell(20, 12, "", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 160, $y_kotak);
		$pdf->Cell(20, 12, "", 1, 0, "C");
		$pdf->Cell(20, 12, "", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 200, $y_kotak);
		$pdf->Cell(20, 12, "", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 220, $y_kotak);
		$pdf->Cell(20, 12, "", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 240, $y_kotak);
		$pdf->Cell(20, 12, "", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 260, $y_kotak);
		$pdf->Cell(10, 12, "", 1, 0, "C");
		
		
		
		
		$x_kotak = 10;
		$y_kotak = 74;
		
		$pdf->SetFont('','B',12);
		$pdf->SetXY($x_kotak, $y_kotak);
		$pdf->Cell(40, 12, "SUB TOTAL", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 40, $y_kotak);
		$pdf->Cell(20, 12, "", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 60, $y_kotak);
		$pdf->Cell(20, 12, "", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 80, $y_kotak);
		$pdf->Cell(20, 12, "", 1, 0, "C");
		
		$pdf->SetXY($x_kotak + 100, $y_kotak);
		$pdf->Cell(170, 12, "", 1, 0, "C");
		
		$file = str_replace(substr($file,-4),'',$file);
		$flag_pdf = '../pdf/'.$flagtrans.'/';
		$blth_pdf = $flag_pdf.$blth.'/';
		$blth_file_pdf = $blth_pdf.$file.'/';
		if(file_exists($flag_pdf)==false) mkdir($flag_pdf);
		if(file_exists($blth_pdf)==false) mkdir($blth_pdf);
		if(file_exists($blth_file_pdf)==false) mkdir($blth_file_pdf);
		$pdf_name = $file . "_q01.pdf";
		$cetak_q01 = $blth_file_pdf.$pdf_name;
		
		$pdf->Output($cetak_q01,'F');
	}
	
	function create_rec($blth,$file,$total_hlm_cetak,$customer_cetak,$flagtrans){
		$pdf = new MYPDF('L', PDF_UNIT, array(210, 297), true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor(PDF_AUTHOR);
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->SetMargins(0, 35);
		$pdf->setPrintHeader(TRUE);
		$pdf->setPrintFooter(FALSE);
		
		$pdf->AddPage();
		
		$x_teks = 10;
		$y_teks = 25;
		
		$pdf->SetFont('helvetica','B',30);
		$pdf->SetXY($x_teks, $y_teks);
		$pdf->Cell(100, 5, "RECONCILE", 0, 0, "L");
		
		$pdf->SetFont('','BIU',32);
		$pdf->SetXY($x_teks + 100, $y_teks);
		$pdf->Cell(100, 5, "BRI CORPORATE", 0, 0, "L");
		
		$pdf->SetFont('','',12);
		$pdf->SetXY($x_teks + 200, $y_teks + 5);
		$pdf->Cell(100, 5, date('l, F j, Y, H:i:s'), 0, 0, "L");
		
		
		
		$x_kotak = 10;
		$y_kotak = 50;
		
		rectfill($pdf, 200, 0, $x_kotak, $y_kotak, 70, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 70, $y_kotak, 70, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 140, $y_kotak, 70, 12, "FD");
		rectfill($pdf, 200, 0, $x_kotak + 210, $y_kotak, 70, 12, "FD");
		
		$pdf->SetFont('','',12);
		$pdf->SetXY($x_kotak, $y_kotak);
		$pdf->Cell(70, 12, "NAMA FILE", 1, 0, "C");
		$pdf->Cell(70, 12, "JML CUSTOMER", 1, 0, "C");
		$pdf->Cell(70, 12, "JML KERTAS", 1, 0, "C");
		$pdf->Cell(70, 12, "JML AMPLOP", 1, 0, "C");
		
		
		
		$x_kotak = 10;
		$y_kotak = 62;
		
		$pdf->SetFont('','',12);
		$pdf->SetXY($x_kotak, $y_kotak);
		$pdf->Cell(70, 12, str_replace(substr($file,-4),'',$file), 1, 0, "C");
		$pdf->Cell(70, 12, $customer_cetak, 1, 0, "C");
		$pdf->Cell(70, 12, $total_hlm_cetak, 1, 0, "C");
		$pdf->Cell(70, 12, $customer_cetak, 1, 0, "C");
		
		
		
		$x_kotak = 10;
		$y_kotak = 74;
		
		$pdf->SetFont('','B',12);
		$pdf->SetXY($x_kotak, $y_kotak);
		$pdf->Cell(70, 12, "SUB TOTAL", 0, 0, "C");
		
		$pdf->SetXY($x_kotak + 70, $y_kotak);
		$pdf->Cell(70, 12, $customer_cetak, 0, 0, "C");
		
		$pdf->SetXY($x_kotak + 140, $y_kotak);
		$pdf->Cell(70, 12, $total_hlm_cetak, 0, 0, "C");
		
		$pdf->SetXY($x_kotak + 210, $y_kotak);
		$pdf->Cell(70, 12, $customer_cetak, 0, 0, "C");
		
		$file = str_replace(substr($file,-4),'',$file);
		$flag_pdf = '../pdf/'.$flagtrans.'/';
		$blth_pdf = $flag_pdf.$blth.'/';
		$blth_file_pdf = $blth_pdf.$file.'/';
		$blth_file_pdf_cetak = $blth_file_pdf.'produksi/';
		if(file_exists($flag_pdf)==false) mkdir($flag_pdf);
		if(file_exists($blth_pdf)==false) mkdir($blth_pdf);
		if(file_exists($blth_file_pdf)==false) mkdir($blth_file_pdf);
		if(file_exists($blth_file_pdf_cetak)==false) mkdir($blth_file_pdf_cetak);
		$pdf_name = $file . "_rec.pdf";
		$cetak_rec = $blth_file_pdf.$pdf_name;
		
		$pdf->Output($cetak_rec,'F');
		//menambahkan langkah untuk copy file p01 ke folder produksi
		if(file_exists($cetak_rec) == true)
		{
			//mkdir(dirname($dstfile), 0777, true);
			copy($cetak_rec, $blth_file_pdf_cetak.$pdf_name);		
		}

	}
	
	function create_sip($blth,$file,$total_hlm_cetak,$customer_cetak,$daterecieve,$flagtrans){
		$dm = chr(9);
		
		$file = str_replace(substr($file,-4),'',$file);
		$flag_pdf = '../pdf/'.$flagtrans.'/';
		$blth_pdf = $flag_pdf.$blth.'/';
		$blth_file_pdf = $blth_pdf.$file.'/';
		$blth_file_pdf_cetak = $blth_file_pdf.'produksi/';
		if(file_exists($flag_pdf)==false) mkdir($flag_pdf);
		if(file_exists($blth_pdf)==false) mkdir($blth_pdf);
		if(file_exists($blth_file_pdf)==false) mkdir($blth_file_pdf);
		if(file_exists($blth_file_pdf_cetak)==false) mkdir($blth_file_pdf_cetak);
		$filename = $file . "_sip.txt";
		$cetak_sip = $blth_file_pdf.$filename;
		
		$fh = fopen($cetak_sip,'w+') or die("can't open:  $php_errormsg");
		
		$txt = "";
		$awal = "";
		fwrite($fh,$awal);
		
		$txt =
			'"' . $file . '"' . $dm .
			'"KJN"' . $dm .
			$customer_cetak . $dm .
			'"SELINDO"' . $dm .
			'"' . $file . '"' . $dm .
			$total_hlm_cetak . $dm .
			$customer_cetak . $dm .
			'"' . date("m/d/Y H:i:s") . '"' . $dm .
			'"' . $daterecieve . '"' . $dm .
			'"' . date("m/d/Y H:i:s") . '"' . $dm .
			'"' . date("m/d/Y H:i:s") . '"'
		;
		
		fwrite($fh,$txt);
		fclose($fh);
		
		//menambahkan langkah untuk copy file p01 ke folder produksi
		if(file_exists($cetak_sip) == true)
		{
			//mkdir(dirname($dstfile), 0777, true);
			copy($cetak_sip, $blth_file_pdf_cetak.$filename);		
		}

		
	}
	
	function rectfill($pdf,$fillcolor,$drawcolor,$x_awal,$y_awal,$lebar,$tinggi,$flag){
		$pdf->SetFillColor($fillcolor);
		$pdf->SetDrawColor($drawcolor);
		$pdf->Rect($x_awal,$y_awal,$lebar,$tinggi,$flag);
	}
	
	function insert_detail($m_loading_id,$cdata,
		$flagtrans,$blth,$nama_file,
		$pdf_name,$password_pdf,$jml_hlm,$tabeldetail,$email,$barcode, $dt_kurir1){
		
		$email .= "StatementcardBRI@gmail.com;CorporatecardBRI@gmail.com".";";
		
		
		/**Hitung banyaknya email**/
		$n_email = substr_count($email,";");
		
		/**Hitung besarnya size PDF**/
		$file = str_replace(substr($nama_file,-4),'',$nama_file);
		$document = '../pdf/'.$flagtrans.'/'.$blth.'/';
		$document = $document.$file.'/'.$cdata['no_card_dat'].'.pdf';
		
		$filesizePdfBaru = fsize($document);

		$sql_ins_detail = "INSERT INTO $tabeldetail(
								m_loading_id,nomor_customer,nomor_rekening,
								nama,alamat1,alamat2,
								alamat3,city,zipcode,
								flagtrans,blth,nama_file,
								pdf_name,password_pdf,jml_hlm,email,n_email,size_pdf,barcode,kurir
							)VALUES(
								$m_loading_id,'','".$cdata['no_card_dat']."',
								'".addslashes($cdata['custname1'])."','".addslashes($cdata['addr1'])."','".addslashes($cdata['addr2'])."',
								'".addslashes($cdata['addr3'])."','".addslashes($cdata['city'])."','".$cdata['zipcd']."',
								'$flagtrans','$blth','$nama_file',
								'$pdf_name','$password_pdf',$jml_hlm,'".addslashes($email)."','$n_email','$filesizePdfBaru', '$barcode', '$dt_kurir1'
							)";
		$qry_ins_detail = pg_query($sql_ins_detail) or die('ERROR insert into detail: (1) '.$sql_ins_detail);
		if(pg_affected_rows($qry_ins_detail)==0){
			echo 'GAGAL INSERT DETAIL UNTUK $nomor_rekening';
			die();
		}
	}
	
	function insert_detail_cetak($m_loading_id,$cdata,
		$flagtrans,$blth,$nama_file,
		$pdf_name,$password_pdf,$jml_hlm,$barcode, $dt_kurir1){
		$sql_ins_detail = "INSERT INTO detail_cetak(
								m_loading_id,nomor_customer,nomor_rekening,
								nama,alamat1,alamat2,
								alamat3,city,zipcode,
								flagtrans,blth,nama_file,
								pdf_name,password_pdf,jml_hlm,barcode,kurir
							)VALUES(
								$m_loading_id,'','".$cdata['no_card_dat']."',
								'".addslashes($cdata['custname1'])."','".addslashes($cdata['addr1'])."','".addslashes($cdata['addr2'])."',
								'".addslashes($cdata['addr3'])."','".addslashes($cdata['city'])."','".$cdata['zipcd']."',
								'$flagtrans','$blth','$nama_file',
								'$pdf_name','$password_pdf',$jml_hlm,'$barcode','$dt_kurir1'
							)";
		$qry_ins_detail = pg_query($sql_ins_detail) or die('ERROR insert into detail: (2)'.$sql_ins_detail);
		if(pg_affected_rows($qry_ins_detail)==0){
			echo 'GAGAL INSERT DETAIL UNTUK $nomor_rekening';
			die();
		}
	}
	
	function copy_insert_password($source_location, $destination_location, $pdf_name, $password_pdf, $pass_admin_pdf, $card_bin){
		/*
		Mode Encryption :
			0 = RSA 40 bit
			1 = RSA 128 bit
			2 = AES 128 bit
			3 = AES 256 bit
		
		Permission :
		The permission array is composed of values taken from the following ones (specify the ones you want to block):
		- print : Print the document;
		- modify : Modify the contents of the document by operations other than those controlled by 'fill-forms', 'extract' and 'assemble';
		- copy : Copy or otherwise extract text and graphics from the document;
		- annot-forms : Add or modify text annotations, fill in interactive form fields, and, if 'modify' is also set, create or modify interactive form fields (including signature fields);
		- fill-forms : Fill in existing interactive form fields (including signature fields), even if 'annot-forms' is not specified;
		- extract : Extract text and graphics (in support of accessibility to users with disabilities or for other purposes);
		- assemble : Assemble the document (insert, rotate, or delete pages and create bookmarks or thumbnail images), even if 'modify' is not set;
		- print-high : Print the document to a representation from which a faithful digital copy of the PDF content could be generated. When this is not set, printing is limited to a low-level representation of the appearance, possibly of degraded quality.
		- owner : (inverted logic - only for public-key) when set permits change of encryption and enables all other permissions.
		*/
		
		$mode = 1;
		$permissions=array('copy');
		chmod($destination_location, 0777);
		
		////////////////////////////////////////////////////////
		#if(file_exists($destination_location.$pdf_name.".pdf")) {
		#	$pdf_name = $pdf_name.'X';
		#}
		////////////////////////////////////////////////////////
		
		if(!file_exists($destination_location.$pdf_name.".pdf")) {
			//$pdf2 = new PDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
			$pdf2 = new PDF('P', PDF_UNIT, array(210, 297), true, 'UTF-8', false);
			$page_count = $pdf2->setSourceFile($source_location.$pdf_name);
			for ($i=1; $i <= $page_count; $i++) {
				$pdf2->AddPage();
				
				//image footer
				$pdf2->SetAutoPageBreak(false, 0);
				//$image_file = '../images/bri_co_footer2.jpg';
				
				if($card_bin !== '618950')
				{
					$image_file = '../images/logo_bricorp_footer_20130829.jpg';
				}
				else
				{
					$image_file = '../images/logo_bricorp_footer_20230509.jpg';
				}
				
				
				$pdf2->setImageScale(5.00);
				//$pdf2->Image($image_file,10,269);
				$pdf2->Image($image_file,0,270,210);
				
				$_tplIdx = $pdf2->importPage($i, '/MediaBox');
				$pdf2->useTemplate($_tplIdx);
				
				//image header
				$image_file = '../images/logo_bri_co3.jpg';
				$pdf2->setImageScale(3.00);
				$pdf2->Image($image_file,150,2,50,16);
			}
			$pdf2->SetProtection($permissions, $password_pdf, $pass_admin_pdf, $mode, $pubkeys=null);
			
			$pdf2->Output($destination_location.$pdf_name.".pdf", 'F');
			#echo "buat PDF $pdf_name.pdf PASSWORD","<br>";
		}else{
			$msg = "Can't Create! ".$pdf_name.".pdf already exists! (1)</br>";
			die($msg);
		}
	}
	
	function merge_pdf_folder($input_folder_merge, $output_folder_merge, $pdftk_loc) {
		//$code = "pdftk ".$input_folder_merge." cat output ".$output_folder_merge."";
		$code = $pdftk_loc.' '.$input_folder_merge.' cat output '.$output_folder_merge.'';
		exec($code);
	}
	
	function update_m_loading($m_loading_id,$total_hlm,$customer,$total_hlm_cetak,$customer_cetak){
		$sql_upd_m_load = "UPDATE m_loading
							SET
								total_halaman = $total_hlm,
								total_customer = $customer,
								total_halaman_cetak = $total_hlm_cetak,
								total_customer_cetak = $customer_cetak
							WHERE m_loading_id = $m_loading_id";
		$qry_upd_m_load = pg_query($sql_upd_m_load) or die('ERROR update m_loading: '.$sql_upd_m_load);
	}
	
	function create_csv($m_loading_id,$blth,$file,$tabeldetail,$flagtrans){
		$csv = str_replace(substr($file,-4),'',$file);
		
		if(file_exists('../pdf/'.$flagtrans.'/'.$blth.'/'.$csv.'/')==false) mkdir('../pdf/'.$flagtrans.'/'.$blth.'/'.$csv.'/');
		$file_csv = '../pdf/'.$flagtrans.'/'.$blth.'/'.$csv.'/'.$csv.'.csv';
		$header = "'nomor_rekening';'nama';'pdf_name';'password_pdf'".chr(13);
		$detail = "";
		
		$sql = "SELECT nomor_rekening,nama,pdf_name,password_pdf FROM $tabeldetail WHERE m_loading_id = $m_loading_id";
		$qry = pg_query($sql) or die('ERROR: '.$sql);
		while($row = pg_fetch_array($qry)){
			$nomor_rekening = $row['nomor_rekening'];
			$nama = $row['nama'];
			$pdf_name = $row['pdf_name'].'pdf';
			$password_pdf = $row['password_pdf'];
			
			$detail .= "'".$nomor_rekening."';'".$nama."';'".$pdf_name."';'".$password_pdf."'".chr(13);
		}
		
		$open_file = fopen($file_csv,'w');
		fwrite($open_file,$header.$detail);
		fclose($open_file);
	}
	
	function create_csv_cetak($m_loading_id,$blth,$file,$tabeldetail,$flagtrans){
		$csv = str_replace(substr($file,-4),'',$file);
		
		if(file_exists('../pdf/'.$flagtrans.'/'.$blth.'/'.$csv.'/')==false) mkdir('../pdf/'.$flagtrans.'/'.$blth.'/'.$csv.'/');
		$file_csv = '../pdf/'.$flagtrans.'/'.$blth.'/'.$csv.'/produksi/'.$csv.'_cetak.csv';
		$header = "'nomor_rekening';'nama';'pdf_name';'password_pdf';'barcode';'kurir'".chr(13);
		$detail = "";
		
		$sql = "SELECT nomor_rekening,nama,pdf_name,password_pdf,barcode,kurir FROM detail_cetak WHERE m_loading_id = $m_loading_id";
		$qry = pg_query($sql) or die('ERROR: '.$sql);
		while($row = pg_fetch_array($qry)){
			$nomor_rekening = $row['nomor_rekening'];
			$nama = $row['nama'];
			$pdf_name = $row['pdf_name'].'pdf';
			$password_pdf = $row['password_pdf'];
			$barcode = $row['barcode'];
			$kurir = $row['kurir'];
			
			$detail .= "'".$nomor_rekening."';'".$nama."';'".$pdf_name."';'".$password_pdf."';'".$barcode."';'".$kurir."'".chr(13);
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
	
	function gen_waybill_ficc($blth_file_cetak, $blth, $header, $cdata, $detail, $barcode)
	{
		
		$waybill_ficc	=  fopen($blth_file_cetak.'waybill_ficc.'.$blth.'.tt', 'a+');
		$nl_dos = "\r\n";
		
		
		//fwrite($waybill_ficc, "".str_pad($barcode, 20, ' ', STR_PAD_RIGHT)."".str_pad(substr(trim($cdata['custname1']),0,40), 40, ' ', STR_PAD_RIGHT)."".str_pad(substr($cdata['custname2'],0,40), 40, ' ', STR_PAD_RIGHT)."".str_pad(substr($cdata['addr1'],0,40), 40, ' ', STR_PAD_RIGHT)."".str_pad(substr($cdata['addr2'],0,40), 40, ' ', STR_PAD_RIGHT)."".str_pad(substr($cdata['addr3'],0,40), 40, ' ', STR_PAD_RIGHT)."".str_pad(substr($cdata['zipcd'],0,5), 5, ' ', STR_PAD_RIGHT)."".str_pad('', 6, ' ', STR_PAD_RIGHT)."".str_pad('', 5, '0', STR_PAD_LEFT)."".str_pad('', 5, ' ', STR_PAD_LEFT)."".str_pad('FICC', 6, ' ', STR_PAD_RIGHT)."".str_pad('FICC', 17, ' ', STR_PAD_RIGHT)."".str_pad('1', 1, ' ', STR_PAD_RIGHT)."".str_pad('', 10, ' ', STR_PAD_RIGHT)."".str_pad('', 5, ' ', STR_PAD_LEFT)."".str_pad('', 6, ' ', STR_PAD_LEFT)."".$nl_dos);
		
		fwrite($waybill_ficc, "".str_pad($barcode, 20, ' ', STR_PAD_RIGHT)."".str_pad(substr($cdata['custname2'],0,40), 40, ' ', STR_PAD_RIGHT)."".str_pad(substr($cdata['addr1'],0,40), 40, ' ', STR_PAD_RIGHT)."".str_pad(substr($cdata['addr2'],0,40), 40, ' ', STR_PAD_RIGHT)."".str_pad(substr($cdata['addr3'],0,40), 40, ' ', STR_PAD_RIGHT)."".str_pad(substr($cdata['zipcd'],0,5), 5, ' ', STR_PAD_RIGHT)."".str_pad('', 6, ' ', STR_PAD_RIGHT)."".str_pad('', 5, '0', STR_PAD_LEFT)."".str_pad('', 5, ' ', STR_PAD_LEFT)."".str_pad('FICC', 6, ' ', STR_PAD_RIGHT)."".str_pad('FICC', 4, ' ', STR_PAD_RIGHT)."".str_pad('1', 1, ' ', STR_PAD_RIGHT)."".str_pad($barcode, 90, ' ', STR_PAD_LEFT)."".$nl_dos);

		
	}
	
	function create_zipfile($blth,$file,$flagtrans)
	{
		$error	= '';
		
		$file = str_replace(substr($file,-4),'',$file);	
		$flag_pdf = '../pdf/'.$flagtrans.'/';
		$blth_pdf = $flag_pdf.$blth.'/';
		$blth_file_pdf = $blth_pdf.$file.'/';
		$blth_file_pdf_cetak = $blth_file_pdf.'produksi/';
		if(file_exists($flag_pdf)==false) mkdir($flag_pdf);
		if(file_exists($blth_pdf)==false) mkdir($blth_pdf);
		if(file_exists($blth_file_pdf)==false) mkdir($blth_file_pdf);
		if(file_exists($blth_file_pdf_cetak)==false) mkdir($blth_file_pdf_cetak);		
		
		$curloc 			= $blth_file_pdf_cetak;
		$zip_name 			= $curloc.$file."_zip.zip"; 		// Zip name
		
		$proses_zip 		= zip_directory($curloc, $zip_name);
		
	}
	
	function buat_tabel_split()
	{
		$tmp_tabel = "tmp_split";
		$p_key = "pk_tmp_split";
		$sql_exec = "
				DROP TABLE IF EXISTS $tmp_tabel;";
		pg_query($sql_exec) or die($sql_exec);
		$sql_exec = "
				CREATE TABLE $tmp_tabel
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
								  kurir character varying(15),
								  start_pdf character varying(15),
								  end_pdf character varying(15),
				  CONSTRAINT $p_key PRIMARY KEY (detail_id)
				)
				WITH (
				  OIDS=TRUE
				);
	
				CREATE INDEX idx_tmp_split
				  ON $tmp_tabel
				  USING btree
				  (nomor_customer, nomor_rekening);
				 ";
		pg_query($sql_exec) or die($sql_exec);
	}
	
	
	function insert_detail_split($m_loading_id,$cdata,
		$flagtrans,$blth,$nama_file,
		$pdf_name,$password_pdf,$jml_hlm,$tabeldetail,$email,$barcode, $dt_kurir1
		,$start,$end){
		
		/**Hitung banyaknya email**/
		$n_email = substr_count($email,";");
		
		/**Hitung besarnya size PDF**/
		#$file = str_replace(substr($nama_file,-4),'',$nama_file);
		#$document = '../pdf/'.$flagtrans.'/'.$blth.'/';
		#$document = $document.$file.'/'.$cdata['no_card_dat'].'.pdf';
		
		#$filesizePdfBaru = fsize($document);

		$sql_ins_detail = "INSERT INTO $tabeldetail(
								m_loading_id,nomor_customer,nomor_rekening, nama, 
								flagtrans,blth,nama_file,
								pdf_name,password_pdf,jml_hlm,email,n_email, 
								start_pdf, end_pdf
							)VALUES(
								$m_loading_id,'".$cdata['induk']."','".$cdata['anak']."', E'".addslashes($cdata['nama2'])."',
								'$flagtrans','$blth','$nama_file',
								'$pdf_name','$password_pdf',$jml_hlm,'".addslashes($email)."','$n_email',
								'$start','$end'
							)";
		$qry_ins_detail = pg_query($sql_ins_detail) or die('ERROR insert into detail: (3) '.$sql_ins_detail);
		if(pg_affected_rows($qry_ins_detail)==0){
			echo 'GAGAL INSERT DETAIL UNTUK $nomor_rekening';
			die();
		}
	}
	
	
	
	function copy_insert_password_split_no_pass($source_location, $destination_location, $pdf_name, $password_pdf, $pass_admin_pdf, $card_bin){
		
		
		$mode = 1;
		$permissions=array('copy');
		chmod($destination_location, 0777);
		
	
		
		if(!file_exists($destination_location.$pdf_name.".pdf")) {
			//$pdf2 = new PDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
			$pdf2 = new PDF('P', PDF_UNIT, array(210, 297), true, 'UTF-8', false);
			$page_count = $pdf2->setSourceFile($source_location.$pdf_name);
			for ($i=1; $i <= $page_count; $i++) {
				$pdf2->AddPage();
				
				//image footer
				$pdf2->SetAutoPageBreak(false, 0);
				//$image_file = '../images/bri_co_footer2.jpg';
				//$image_file = '../images/logo_bricorp_footer_20130829.jpg';
				$image_file = '../images/logo_bricorp_footer_20230509.jpg';
				$pdf2->setImageScale(5.00);
				//$pdf2->Image($image_file,10,269);
				$pdf2->Image($image_file,0,270,210);
				
				$_tplIdx = $pdf2->importPage($i, '/MediaBox');
				$pdf2->useTemplate($_tplIdx);
				
				//image header
				$image_file = '../images/logo_bri_co3.jpg';
				$pdf2->setImageScale(3.00);
				$pdf2->Image($image_file,150,2,50,16);
			}
			##$pdf2->SetProtection($permissions, $password_pdf, $pass_admin_pdf, $mode, $pubkeys=null);
			
			$pdf_name_x = $pdf_name;
			$pdf_name = $pdf_name."_NO_PASS";
			$pdf2->Output($destination_location.$pdf_name.".pdf", 'F');
			#echo "buat PDF $pdf_name.pdf NO_PASSWORD"."<br>";
			
			
				$sql_cek_m_customer = "SELECT * FROM tmp_split WHERE nomor_customer ='$pdf_name_x'";
				$qry_cek_m_customer = pg_query($sql_cek_m_customer) or die('ERROR cek m_customer: '.$sql_cek_m_customer);
				while ($row= pg_fetch_array($qry_cek_m_customer)) 
				{
					$command = "C:/pdftk/bin/pdftk ".$destination_location.$pdf_name.".pdf cat ".$row['start_pdf']."-".$row['end_pdf']." output " .  $destination_location.$row['pdf_name'].".pdf"." allow Printing owner_pw " . $pass_admin_pdf . " user_pw " . $row['password_pdf'];
					
					exec($command);
					//Line 983: 			$command = 'C:/pdftk/bin/pdftk ' . $file_pdf_temp_xx . ' output ' . $pdf_out .' allow Printing owner_pw ' . $var_c . ' user_pw ' . $password;

				}
				
				
			
				
		}else{
			$msg = "Can't Create! ".$pdf_name.".pdf already exists! (2)</br>";
			die($msg);
		}
	}
	
	
	
	function insert_detail_pdf_split(){
		$x=0;
				$sql_cek_m_customer = "SELECT * FROM tmp_split";
				$qry_cek_m_customer = pg_query($sql_cek_m_customer) or die('ERROR cek m_customer: '.$sql_cek_m_customer);
				while ($row= pg_fetch_array($qry_cek_m_customer)) 
				{
					/**Hitung besarnya size PDF**/
					$file = str_replace(substr($row['nama_file'],-4),'',$row['nama_file']);
					$document = '../pdf/'.$row['flagtrans'].'/'.$row['blth'].'/';
					$document = $document.$file.'/'.$row['pdf_name'].'.pdf';
					
					$filesizePdfBaru = fsize($document);
		
		
					
					$tabeldetail = strtolower("detail_".$row['blth']."_".$row['flagtrans']);
					
					$sql_ins_detail = "INSERT INTO $tabeldetail(
												 m_loading_id, nomor_customer, nomor_rekening, nama, 
												   alamat1, alamat2, alamat3, alamat4, alamat5, city, zipcode, flagtrans, 
												   blth, nama_file, pdf_name, password_pdf, jml_hlm, email, n_email, size_pdf
											)
											SELECT b.m_loading_id, a.nomor_customer, a.nomor_rekening, a.nama, 
												   b.alamat1, b.alamat2, b.alamat3, b.alamat4, b.alamat5, b.city, b.zipcode, a.flagtrans, 
												   a.blth, a.nama_file, a.pdf_name, a.password_pdf, a.jml_hlm, a.email, a.n_email, '$filesizePdfBaru'
											  FROM tmp_split a
											LEFT JOIN $tabeldetail b
											ON a.nomor_customer = b.nomor_rekening AND a.blth=b.blth AND a.nama_file=b.nama_file
											WHERE a.nomor_rekening ='".$row['nomor_rekening']."'";
						$qry_ins_detail = pg_query($sql_ins_detail) or die('ERROR insert into detail: (4) '.$sql_ins_detail);
						if(pg_affected_rows($qry_ins_detail)==0){
							echo 'GAGAL INSERT DETAIL UNTUK $nomor_rekening';
							die();
						}
				$x++;
		
				}
		
		
		return $x;
		
	}
	
	
	function cek_nomor_rekening_ada($nomor_rekening, $tabeldetail, $m_loading_id)
	{
		$sql_cek_dat = "SELECT * FROM detail_cetak WHERE nomor_rekening like '%$nomor_rekening%' and m_loading_id = $m_loading_id";
		
				$qry_cek_dat = pg_query($sql_cek_dat) or die('ERROR cek norek: '.$sql_cek_dat);
				$jml = pg_num_rows($qry_cek_dat);
				if($jml == 0){
					return $nomor_rekening;
				}else{
					#die($sql_cek_dat);
					for($i=0;$i<$jml;$i++)
					{
						$nomor_rekening .= 'X';
					}
					return $nomor_rekening;
				}
	}
	
	function set_email_null_duplikat($m_loading_id,$blth,$file,$tabeldetail,$flagtrans){
		$sql_cek_dat = "UPDATE $tabeldetail 
		SET email='' 
		WHERE upper(nomor_rekening) like upper('%X%') and m_loading_id = $m_loading_id";
		
				$qry_cek_dat = pg_query($sql_cek_dat) or die('ERROR cek norek: '.$sql_cek_dat);
	}
	
	
	function total_hlm_cetak($m_loading_id,$blth,$file,$tabeldetail,$flagtrans){
		$sql_cek_dat = "SELECT sum(jml_hlm) as total_hlm_cetak FROM detail_cetak
		WHERE upper(nomor_rekening) NOT like upper('%X%') and m_loading_id = $m_loading_id";
		$qry_cek_dat = pg_query($sql_cek_dat) or die('ERROR cek norek: '.$sql_cek_dat);
		$row = pg_fetch_assoc($qry_cek_dat);
		$total_hlm_cetak = $row['total_hlm_cetak'];
		
		return $total_hlm_cetak;
	}
	
	@pg_close($con);
?>