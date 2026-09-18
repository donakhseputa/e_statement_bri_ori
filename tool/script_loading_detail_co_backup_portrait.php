<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/function.php"); ?>
<?
	require_once('../include/tcpdf/config/lang/eng.php');
	require_once('../include/tcpdf/tcpdf.php');
	require_once('../include/fpdi/fpdi.php');
	
	$pr = $_REQUEST['pr'];
	$arr_pr = explode('|',$pr);
	$flagtrans = $arr_pr[0];
	$menu_id = $arr_pr[1];
	$act = $arr_pr[2];
	$blth = $arr_pr[3];
	$file = $arr_pr[4];
	$file_mcustomer = $arr_pr[5];
	$con = pg_connect($connection) or die("Could not connect to database!");
	$msg = "";
	$msg_not_exists = "";
	
	class PDF extends FPDI {
		function Header() {}
		function Footer() {}
	}
	
	class MYPDF extends TCPDF {
		function Header() {
			$bMargin = 26;//$this->getBreakMargin();
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
		
		$dirfile = '../tmp/'.$file;
		$open_dat = fopen($dirfile,'r');

		$m_loading_id = m_loading($flagtrans,$blth,$file);
		while(!feof($open_dat)){
			$row_dat = fgets($open_dat);
			$row_dat = utf8_encode($row_dat);
			if(trim($row_dat)!=''){
				$rectype = substr($row_dat,0,2);
				$no_card_dat = substr($row_dat,5,16);
				
				if($loop != 1){
					if(($prevcardnb<>$no_card_dat)&&($flag_case=='Y')){
						$strpdf=doPDF($cdata,$header,$password_pdf,$blth,$file,$detail,$flag_case);
						$arrpdf=explode('|',$strpdf); $pdf_name=$arrpdf[0]; $jml_hlm=$arrpdf[1];
						insert_detail($m_loading_id,$cdata,
							$flagtrans,$blth,$file,
							$pdf_name,$password_pdf,$jml_hlm);
						insert_detail_cetak($m_loading_id,$cdata,
							$flagtrans,$blth,$file,
							$pdf_name,$password_pdf,$jml_hlm);
						$total_hlm += $jml_hlm;
						$total_hlm_cetak += $jml_hlm;
					}
					else if(($prevcardnb<>$no_card_dat)&&($flag_case=='N')){
						$strpdf=doPDF($cdata,$header,$password_pdf,$blth,$file,$detail,$flag_case);
						$arrpdf=explode('|',$strpdf); $pdf_name=$arrpdf[0]; $jml_hlm=$arrpdf[1];
						insert_detail_cetak($m_loading_id,$cdata,
							$flagtrans,$blth,$file,
							$pdf_name,$password_pdf,$jml_hlm);
						$total_hlm_cetak += $jml_hlm;
					}
				}
				
				$sql_cek_m_customer = "SELECT nomor_rekening, password_pdf
										FROM m_customer
										WHERE blth = '$blth'
											AND flagtrans = '$flagtrans'";
					if($file_mcustomer != ''){
						$sql_cek_m_customer .= " AND log_customer_id = ".$file_mcustomer;
					}
				$sql_cek_m_customer .= " AND nomor_rekening = '$no_card_dat'";
				$qry_cek_m_customer = pg_query($sql_cek_m_customer) or die('ERROR cek m_customer: '.$sql_cek_m_customer);
				//$jml_customer = pg_num_rows($qry_cek_m_customer);
				$jml_customer = 0;
				$row_cek_m_customer = pg_fetch_assoc($qry_cek_m_customer);
				$password_pdf = $row_cek_m_customer['password_pdf'];
				
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
						$customer_cetak++;
						
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
						$cdata['bunga_belanja']	= number_format((substr($row_dat,460,3)/100),2,'.',',').'%';
						$cdata['bunga_tunai']	= number_format((substr($row_dat,466,3)/100),2,'.',',').'%';
						
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
					}
				}
				if(($rectype=='02')&&($prevrectype=='01')){
					$oricdnum	= substr($row_dat,27,16);
					$header['cdnumb'][($im-1)]	= substr($oricdnum,0,4).'-'.substr($oricdnum,4,4).'-'.substr($oricdnum,8,4).'-'.substr($oricdnum,12,4);
					$header['cdname'][($im-1)]	= substr($row_dat,43,30);
				}
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
					if($prevrectype == '01'){
						$header['cdname'][($im-1)]	= $cdname_temp;
						$header['cdnumb'][($im-1)]	= $cdnumb_temp;
					}
					$oricdnum_temp					= substr($row_dat,27,16);
					$cdname_temp					= substr($row_dat,1061,30);
					$cdnumb_temp					= substr($oricdnum_temp,0,4).'-'.substr($oricdnum_temp,4,4).'-'.substr($oricdnum_temp,8,4).'-'.substr($oricdnum_temp,12,4);
					//////////////////////////////////////////////////////////////////////
					
					$im++;
					$ih=0;
				}
				if($rectype=='02'){
					$jmltrans		= ltrim(substr($row_dat,147,14),'0');
					
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
			$strpdf=doPDF($cdata,$header,$password_pdf,$blth,$file,$detail,$flag_case);
			$arrpdf=explode('|',$strpdf); $pdf_name=$arrpdf[0]; $jml_hlm=$arrpdf[1];
			insert_detail($m_loading_id,$cdata,
				$flagtrans,$blth,$file,
				$pdf_name,$password_pdf,$jml_hlm);
			insert_detail_cetak($m_loading_id,$cdata,
				$flagtrans,$blth,$file,
				$pdf_name,$password_pdf,$jml_hlm);
			$total_hlm += $jml_hlm;
			$total_hlm_cetak += $jml_hlm;
		}
		else if($flag_case == 'N' && feof($open_dat)){
			$strpdf=doPDF($cdata,$header,$password_pdf,$blth,$file,$detail,$flag_case);
			$arrpdf=explode('|',$strpdf); $pdf_name=$arrpdf[0]; $jml_hlm=$arrpdf[1];
			insert_detail_cetak($m_loading_id,$cdata,
				$flagtrans,$blth,$file,
				$pdf_name,$password_pdf,$jml_hlm);
			$total_hlm_cetak += $jml_hlm;
		}
		
		$file_merge = str_replace(substr($file,-4),'',$file);
		$input_folder_merge = $url_directory.'\\pdf\\'.$blth.'\\'.$file_merge.'\\cetak\\*';
		$output_folder_merge = $url_directory.'\\pdf\\'.$blth.'\\'.$file_merge.'\\'.$file_merge.'_cetak.pdf';
		merge_pdf_folder($input_folder_merge, $output_folder_merge, $pdftk_loc);
		
		update_m_loading($m_loading_id,$total_hlm,$customer,$total_hlm_cetak,$customer_cetak);
		create_csv($m_loading_id,$blth,$file);
		$not_exists = implode(', ',$msg_not_exists);
		return $customer.'|'.$not_exists;
	}
	
	function doPDF($cdata,$header,$password_pdf,$blth,$file,$detail,$flag_case){
		$pdf = new MYPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor(PDF_AUTHOR);
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->SetMargins(0, 20);
		$pdf->setPrintHeader(TRUE);
		$pdf->setPrintFooter(FALSE);
		$pdf->SetAutoPageBreak(TRUE, 26);
		$pdf->AddFont("Sandy","","sandy.php");
		$pdf->AddFont("Sandy2","","sandy2.php");
		
		$x_teks_master = 10;
		$y_teks_master = 30;
		$pdf->AddPage();
		//$pdf->Image('../images/bricorp_mst_20130715.jpg',$x_teks_master,$y_teks_master,217,302);
		$pdf->SetFont('helvetica','BI',13);
		$pdf->SetXY($x_teks_master, $y_teks_master - 5);
		$pdf->MultiCell(190,5,'Corporate Card Statement',0,'C');
		
		
		
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
		
		
		
		$x_kotak = $x_teks_master;
		$y_kotak = $y_teks_master + 35;
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
		$y_rincian_master = $y_teks_master + 70;
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
		$tbldetmst.='
				<tr>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>BATAS KREDIT LIMIT</td>
					<td>&nbsp;</td>
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
		
		$pdf->SetY($pdf->GetY() + 10);
		$pdf->SetFont('Sandy','',8);
		$pdf->SetX($x_teks_master + 45);
		$pdf->MultiCell(100,5,'Kami mengucapkan terima kasih atas kepercayaan dan kesetiaan Perusahaan Anda menggunakan Corporate Card kami. Pergunakan auto debet untuk pembayaran Corporate Card Anda. Hubungi Corporate Card Relationship Specialist kami untuk informasi lebih lanjut',0,'C');
		
		//////////////////MULAI PEMBUATAN DETAIL//////////////////
		for($i=0;$i<$nheader;$i++){
			$x_teks_detail = 10;
			$y_teks_detail = 30;
			$pdf->AddPage();
			
			$pdf->SetFont('helvetica','BI',13);
			$pdf->SetXY($x_teks_detail, $y_teks_detail - 5);
			$pdf->MultiCell(190,5,'Transaction Details',0,'C');
			
			
			
			$x_biodata = $x_teks_detail;
			$y_biodata = $y_teks_detail + 3;
			$pdf->SetFont('Sandy','',9);
			$pdf->SetXY($x_biodata, $y_biodata);
			$pdf->SetX($x_biodata);$pdf->MultiCell(50, 5, $cdata['custname2'], 0, 'L', 0, 1);
			$pdf->SetX($x_biodata);$pdf->MultiCell(50, 5, $header['cdname'][$i], 0, 'L', 0, 1);
			
			
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
			$pdf->Cell(35, 4, $cdata['total_pembelanjaan'], 0, 0, 'L');
			$pdf->Cell(35, 4, $cdata['total_ambil_tunai'], 0, 0, 'L');
			$pdf->Cell(35, 4, $cdata['total_tagihan_next'], 0, 0, 'L');
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
			
			
			//$pdf->AddPage();
			//$pdf->Image("../images/bricorp_det_20130711.jpg", $x_detail, 10, 217, 302);
			$tbldetdet="";
			$tbldetdet.='
				<table width="70%">
					<tr>
						<td width="17%" height="5"></td>
						<td width="18%"></td>
						<td width="43%">Tagihan Bulan Lalu</td>
						<td width="5%"></td>
						<td width="17%"></td>
						<td width="17%"></td>
						<td width="16%" align="right">'.$header['tagihan_prev'][$i].'</td>
						<td></td>
					</tr>';
			$ndetail=count($detail['dttrans'][$i]);
			$tbldetdet.='
					<tr>
						<td colspan="8">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="8">'.$header['cdnumb'][$i].'&nbsp;&nbsp;&nbsp;&nbsp;'.$header['cdname'][$i].'</td>
					</tr>';
			for($x=0;$x<$ndetail;$x++){
				if(strtoupper($detail['cr'][$i][$x])=='CR') $cr='CR'; else $cr='';
				$tbldetdet.='
					<tr>
						<td width="17%" height="12">'.$detail['dttrans'][$i][$x].'</td>
						<td width="18%">'.$detail['dtbuku'][$i][$x].'</td>
						<td width="43%">'.$detail['trans'][$i][$x].'</td>
						<td width="5%">'.$detail['forexcurr'][$i][$x].'</td>
						<td width="17%" align="right">'.$detail['forexnominal'][$i][$x].'</td>
						<td width="17%" align="right">'.$detail['kursnominal'][$i][$x].'</td>
						<td width="16%" align="right">'.$detail['jmltrans'][$i][$x].'</td>
						<td>'.$cr.'</td>
					</tr>';
			}
			$tbldetdet.='
					<tr>
						<td colspan="8">&nbsp;</td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td>Total Interest & Service Charge</td>
						<td></td>
						<td></td>
						<td></td>
						<td align="right">'.$header['fin_charge'][$i].'</td>
						<td></td>
					</tr>
					<tr>
						<td colspan="8">&nbsp;</td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td>Tagihan Bulan Ini</td>
						<td></td>
						<td></td>
						<td></td>
						<td align="right">'.$header['tagihan_next'][$i].'</td>
						<td></td>
					</tr>
				</table>';
			$pdf->SetY($pdf->GetY() + 5);
			$pdf->SetFont('Sandy','',9);
			$pdf->SetX($x_rincian_detail);$pdf->writeHTML($tbldetdet, true, false, true, false, '');
		}
		//////////////////SELESAI PEMBUATAN DETAIL//////////////////
		
		$pdf_name = $cdata['no_card_dat'];
		$pass_admin_pdf = "app123456";
		$tot_hlm = $pdf->PageNo();
		
		$file = str_replace(substr($file,-4),'',$file);
		$blth_pdf = '../pdf/'.$blth.'/';
		$blth_file_pdf = $blth_pdf.$file.'/';
		$blth_file_cetak = $blth_pdf.$file.'/cetak/';
		if(file_exists($blth_pdf)==false) mkdir($blth_pdf);
		if(file_exists($blth_file_pdf)==false) mkdir($blth_file_pdf);
		if(file_exists($blth_file_cetak)==false) mkdir($blth_file_cetak);
		$file_copy = $pdf_name;
		$cetak_output = $blth_file_cetak.$pdf_name;
		if(file_exists($cetak_output)){
			unlink($cetak_output);
		}
		$pdf->Output($cetak_output,'F');
		
		if($flag_case == "Y"){
			copy_insert_password($blth_file_cetak, $blth_file_pdf, $file_copy, $password_pdf, $pass_admin_pdf);
		}
		return $pdf_name.'|'.$tot_hlm;
	}
	
	function rectfill($pdf,$fillcolor,$drawcolor,$x_awal,$y_awal,$lebar,$tinggi,$flag){
		$pdf->SetFillColor($fillcolor);
		$pdf->SetDrawColor($drawcolor);
		$pdf->Rect($x_awal,$y_awal,$lebar,$tinggi,$flag);
	}
	
	function insert_detail($m_loading_id,$cdata,
		$flagtrans,$blth,$nama_file,
		$pdf_name,$password_pdf,$jml_hlm){
		$sql_ins_detail = "INSERT INTO detail(
								m_loading_id,nomor_customer,nomor_rekening,
								nama,alamat1,alamat2,
								alamat3,city,zipcode,
								flagtrans,blth,nama_file,
								pdf_name,password_pdf,jml_hlm
							)VALUES(
								$m_loading_id,'','".$cdata['no_card_dat']."',
								'".addslashes($cdata['custname1'])."','".addslashes($cdata['addr1'])."','".addslashes($cdata['addr2'])."',
								'".addslashes($cdata['addr3'])."','".addslashes($cdata['city'])."','".$cdata['zipcd']."',
								'$flagtrans','$blth','$nama_file',
								'$pdf_name','$password_pdf',$jml_hlm
							)";
		$qry_ins_detail = pg_query($sql_ins_detail) or die('ERROR insert into detail: '.$sql_ins_detail);
		if(pg_affected_rows($qry_ins_detail)==0){
			echo 'GAGAL INSERT DETAIL UNTUK $nomor_rekening';
			die();
		}
	}
	
	function insert_detail_cetak($m_loading_id,$cdata,
		$flagtrans,$blth,$nama_file,
		$pdf_name,$password_pdf,$jml_hlm){
		$sql_ins_detail = "INSERT INTO detail_cetak(
								m_loading_id,nomor_customer,nomor_rekening,
								nama,alamat1,alamat2,
								alamat3,city,zipcode,
								flagtrans,blth,nama_file,
								pdf_name,password_pdf,jml_hlm
							)VALUES(
								$m_loading_id,'','".$cdata['no_card_dat']."',
								'".addslashes($cdata['custname1'])."','".addslashes($cdata['addr1'])."','".addslashes($cdata['addr2'])."',
								'".addslashes($cdata['addr3'])."','".addslashes($cdata['city'])."','".$cdata['zipcd']."',
								'$flagtrans','$blth','$nama_file',
								'$pdf_name','$password_pdf',$jml_hlm
							)";
		$qry_ins_detail = pg_query($sql_ins_detail) or die('ERROR insert into detail: '.$sql_ins_detail);
		if(pg_affected_rows($qry_ins_detail)==0){
			echo 'GAGAL INSERT DETAIL UNTUK $nomor_rekening';
			die();
		}
	}
	
	function copy_insert_password($source_location, $destination_location, $pdf_name, $password_pdf, $pass_admin_pdf){
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
		
		if(!file_exists($destination_location.$pdf_name.".pdf")) {
			$pdf2 = new PDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
			$page_count = $pdf2->setSourceFile($source_location.$pdf_name);
			for ($i=1; $i <= $page_count; $i++) {
				$pdf2->AddPage();
				
				//image footer
				$pdf2->SetAutoPageBreak(false, 0);
				$image_file = '../images/bri_co_footer2.jpg';
				$pdf2->setImageScale(5.00);
				$pdf2->Image($image_file,10,269);
				
				$_tplIdx = $pdf2->importPage($i, '/MediaBox');
				$pdf2->useTemplate($_tplIdx);
				
				//image header
				$image_file = '../images/logo_bri_co3.jpg';
				$pdf2->setImageScale(3.00);
				$pdf2->Image($image_file,150,2,50,16);
			}
			$pdf2->SetProtection($permissions, $password_pdf, $pass_admin_pdf, $mode, $pubkeys=null);
			
			$pdf2->Output($destination_location.$pdf_name.".pdf", 'F');
		}else{
			$msg = "Can't Create! ".$pdf_name.".pdf already exists! </br>";
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
	
	function create_csv($m_loading_id,$blth,$file){
		$csv = str_replace(substr($file,-4),'',$file);
		
		if(file_exists('../pdf/'.$blth.'/'.$csv.'/')==false) mkdir('../pdf/'.$blth.'/'.$csv.'/');
		$file_csv = '../pdf/'.$blth.'/'.$csv.'/'.$csv.'.csv';
		$header = "'nomor_rekening';'nama';'pdf_name';'password_pdf'".chr(13);
		$detail = "";
		
		$sql = "SELECT nomor_rekening,nama,pdf_name,password_pdf FROM detail WHERE m_loading_id = $m_loading_id";
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