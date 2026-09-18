<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/function.php"); ?>
<? //define('FPDF_FONTPATH','../include/fpdf/font/'); ?>
<? //require_once("../include/fpdf/fpdf.php"); ?>
<? //require_once("../include/fpdi/FPDI_Protection.php"); ?>
<?
	require_once('../include/tcpdf/config/lang/eng.php');
	require_once('../include/tcpdf/tcpdf.php');
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
	
	class MYPDF extends TCPDF {

		//Page header
		public function Header() {
			$bMargin = 26;//$this->getBreakMargin();
			$auto_page_break = $this->AutoPageBreak;
			$this->SetAutoPageBreak(false, 0);
			$image_file = '../images/bri_co_footer2.jpg';
			$this->setImageScale(5.00);
			$this->Image($image_file,10,185);
			$this->SetAutoPageBreak($auto_page_break, $bMargin);
			$image_file = '../images/logo_bri_co3.jpg';
			$this->setImageScale(3.00);
			$this->Image($image_file,240,2,50,16);
		}

		/* Page footer
		/*public function Footer() {
			// Position at 15 mm from bottom
			//$this->SetY(-15);
			$bMargin = $this->getBreakMargin();
			$auto_page_break = $this->AutoPageBreak;
			$this->SetAutoPageBreak(false, 0);
			$image_file = '../images/bri_co_footer.jpg';
			$this->Image($image_file,10,175,100,40, '', '', '', false, 300, '', false, false, 0);
			$this->SetAutoPageBreak($auto_page_break, $bMargin);
		}*/
	}

	
	switch($act){
		case 'dat_to_pdf':
				$waktu_awal = strtotime(date("Y-m-d H:i:s"));
				
				$sql_cek_dat = "SELECT * FROM m_loading WHERE flagtrans = '$flagtrans' and blth = '$blth' and loading_file = '$file'";
				$qry_cek_dat = pg_query($sql_cek_dat) or die('ERROR cek m_loading: '.$sql_cek_dat);
				$jml = pg_num_rows($qry_cek_dat);
				if($jml == 0){
					$msg = '|'.dat_to_pdf($flagtrans,$menu_id,$blth,$file);
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
	
	function dat_to_pdf($flagtrans,$menu_id,$blth,$file){
		$ximage = 0;
		$yimage = 3.5;
		/*$x = 7;
		$y = 13;*/
		$x = 10;
		$y = 24;
		//$y_detail = $y+100;
		$y_detail = $y+102;
		$customer = 0;
		$total_hlm = 0;
		$flag_case = 'N';
		$no_card_cek = '';
		$msg_not_exists = array ();
		$row_message = 4;				/*VARIABEL INI YANG DIUBAH-UBAH*/
		$max_record = 28-$row_message;
		$row_total_cek = $max_record-5;
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
				
				if(($prevcardnb<>$no_card_dat)&&($flag_case=='Y')){
					$strpdf=doPDF($cdata,$header,$password_pdf,$blth,$file,$detail);
					$arrpdf=explode('|',$strpdf); $pdf_name=$arrpdf[0]; $jml_hlm=$arrpdf[1];
					insert_detail($m_loading_id,$cdata,
						$flagtrans,$blth,$file,
						$pdf_name,$password_pdf,$jml_hlm);
					$total_hlm += $jml_hlm;
				}
				
				$flag_case = 'Y';
				
				$sql_cek_m_customer = "SELECT nomor_rekening, password_pdf
										FROM m_customer
										WHERE blth = '$blth'
											AND flagtrans = '$flagtrans'
											AND nomor_rekening = '$no_card_dat'";
				$qry_cek_m_customer = pg_query($sql_cek_m_customer) or die('ERROR cek m_customer: '.$sql_cek_m_customer);
				$jml_customer = pg_num_rows($qry_cek_m_customer);
				$row_cek_m_customer = pg_fetch_assoc($qry_cek_m_customer);
				$password_pdf = $row_cek_m_customer['password_pdf'];
				
				if($jml_customer==0){
					$flag_case = 'N';
					if($rectype == '01'){
						$msg_not_exists[] = $no_card_dat;
					}
				}else{
					if($prevcardnb<>$no_card_dat){
						if($rectype=='01'){ //hanya akan masuk saat nosebelum tidak sama dengan nocurrent, dan rectype 01
							$hlm = 1;
							$record = 0;
							$customer++;
							
							$cdata['no_card_dat']	= $no_card_dat;
							$cdata['custname1']	= substr($row_dat,43,30);
							if(trim(strtoupper($cdata['custname1']))=='PERTAMINA') $cdata['spcust']=1; else $cdata['spcust']=0;
							$cdata['custname2']	= substr($row_dat,73,30);
							$cdata['addr1']		= substr($row_dat,103,30);
							$cdata['addr2']		= substr($row_dat,133,30);
							$cdata['addr3']		= substr($row_dat,163,30);
							$cdata['city']		= substr($row_dat,193,30);
							$cdata['zipcd']		= substr($row_dat,223,9);
							$cdata['dtcetak']	= substr($row_dat,238,2).'-'.substr($row_dat,236,2).'-'.substr($row_dat,232,4);
							$cdata['dttempo']	= substr($row_dat,246,2).'-'.substr($row_dat,244,2).'-'.substr($row_dat,240,4);
							$cdata['custname3']	= substr($row_dat,979,30);
							$cdata['bunga_belanja']	= number_format((substr($row_dat,460,3)/100),2,'.',',').'%';
							$cdata['bunga_tunai']	= number_format((substr($row_dat,466,3)/100),2,'.',',').'%';
							
							$header['tagihan_prev']		= array();//reset array header
							$header['tagihan_next']		= array();
							$header['pembayaran']		= array();
							$header['pembelanjaan']		= array();
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
							$cdata['total_ambil_tunai']		= 0; 
							$cdata['total_batas_tunai']		= 0; 
							
							$total_tagihan_next	= 0; 
							$total_kredit_limit	= 0; 
							$total_kredit_sisa	= 0; 
							$total_tagihan_prev	= 0; 
							$total_pembayaran	= 0; 
							$total_pembelanjaan	= 0; 
							$total_ambil_tunai	= 0; 
							$total_batas_tunai	= 0; 
							
							$detail['dttrans']	= array();
							$detail['dtbuku']	= array();
							$detail['trans']	= array();
							$detail['jmltrans']	= array();
							$detail['cr']		= array();
							
							//print_r($cdata);
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
						$pembayaran		= ltrim(substr($row_dat,353,14),'0');
						$bunga_debit	= ltrim(substr($row_dat,398,14),'0');
						$belanja_debit	= ltrim(substr($row_dat,428,14),'0');
							$pembelanjaan	= $belanja_debit + $bunga_debit;
						$ambil_tunai	= ltrim(substr($row_dat,443,14),'0');
						$batas_tunai	= ltrim(substr($row_dat,308,14),'0');
						
						$total_tagihan_next	+= $tagihan_next;
						$total_kredit_limit	+= $kredit_limit; 
						$total_kredit_sisa	+= $kredit_sisa; 
						$total_tagihan_prev	+= $tagihan_prev; 
						$total_pembayaran	+= $pembayaran; 
						$total_pembelanjaan	+= $pembelanjaan; 
						$total_ambil_tunai	+= $ambil_tunai; 
						$total_batas_tunai	+= $batas_tunai; 
						
						$cdata['total_tagihan_next']	= number_format($total_tagihan_next,0,'.',',');
						$cdata['total_kredit_limit']	= number_format($total_kredit_limit,0,'.',',');
						$cdata['total_kredit_sisa']		= number_format($total_kredit_sisa,0,'.',',');
						$cdata['total_tagihan_prev']	= number_format($total_tagihan_prev,0,'.',',');
						$cdata['total_pembayaran']		= number_format($total_pembayaran,0,'.',',');
						$cdata['total_pembelanjaan']	= number_format($total_pembelanjaan,0,'.',',');
						$cdata['total_ambil_tunai']		= number_format($total_ambil_tunai,0,'.',',');  
						$cdata['total_batas_tunai']		= number_format($total_batas_tunai,0,'.',',');
						
						$header['tagihan_next'][$im] 	= number_format($tagihan_next,0,'.',',');
						$header['kredit_limit'][$im] 	= number_format($kredit_limit,0,'.',',');
						$header['kredit_sisa'][$im]		= number_format($kredit_sisa,0,'.',',');
						$header['tagihan_prev'][$im]	= number_format($tagihan_prev,0,'.',',');
						$header['pembayaran'][$im]		= number_format($pembayaran,0,'.',',');
						$header['pembelanjaan'][$im]	= number_format($pembelanjaan,0,'.',',');
						$header['ambil_tunai'][$im]		= number_format($ambil_tunai,0,'.',',');
						
						//////////////////////////////////////////////////////////////////////
						//case di bawah ini digunakan apabila ada rectype 01 secara berurutan
						if($prevrectype == '01'){
							$header['cdname'][($im-1)]	= "";
							$header['cdnumb'][($im-1)]	= $cdnumb_temp;
						}
						$oricdnum_temp					= substr($row_dat,27,16);
						$cdnumb_temp					= substr($oricdnum_temp,0,4).'-'.substr($oricdnum_temp,4,4).'-'.substr($oricdnum_temp,8,4).'-'.substr($oricdnum_temp,12,4);
						//////////////////////////////////////////////////////////////////////
						
						$im++;
						$ih=0;
					}
					if($rectype=='02'){
						$jmltrans		= ltrim(substr($row_dat,147,14),'0');
						
						$total_jmltrans	+= $jmltrans;
						
						$detail['dttrans'][($im-1)][$ih]	= substr($row_dat,79,2).'-'.substr($row_dat,77,2).'-'.substr($row_dat,73,4);
						$detail['dtbuku'][($im-1)][$ih]		= substr($row_dat,87,2).'-'.substr($row_dat,85,2).'-'.substr($row_dat,81,4);
						$detail['trans'][($im-1)][$ih]		= substr($row_dat,89,40);
						$detail['jmltrans'][($im-1)][$ih]	= number_format($jmltrans,0,'.',',');
						$detail['cr'][($im-1)][$ih]			= substr($row_dat,162,2);
						
						$ih++;
					}
					
				}
				$prevcardnb=$no_card_dat;
				$prevrectype=$rectype;
			}
		}
		if($flag_case == 'Y' && feof($open_dat))
		{
			$strpdf=doPDF($cdata,$header,$password_pdf,$blth,$file,$detail);
			//echo "xxx::".$strpdf;
			$arrpdf=explode('|',$strpdf); $pdf_name=$arrpdf[0]; $jml_hlm=$arrpdf[1];
			insert_detail($m_loading_id,$cdata,
				$flagtrans,$blth,$file,
				$pdf_name,$password_pdf,$jml_hlm);
			$total_hlm += $jml_hlm;
		}	
		update_m_loading($m_loading_id,$total_hlm,$customer);
		create_csv($m_loading_id,$blth,$file);
		$not_exists = implode(', ',$msg_not_exists);
		return $customer.'|'.$not_exists;
	}
	
	function card_name($card_number){
		if($card_number=='4211-6701'){
			$cc_name = 'visa_classic';
		}elseif($card_number=='4211-6801'){
			$cc_name = 'visa_gold';
		}elseif($card_number=='5526-9500'){
			$cc_name = 'master_bussiness';
		}elseif($card_number=='4897-8101'){
			$cc_name = 'visa_platinum';
		}elseif($card_number=='5268-5301'){
			$cc_name = 'master_gold';
		}elseif($card_number=='5239-4001'){
			$cc_name = 'master_platinum';
		}else{
			$cc_name = 'error_code';
		}
		return $cc_name;
	}
	
	function doPDF($cdata,$header,$password_pdf,$blth,$file,$detail){
	
		$pdf = new MYPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		//$pdf->SetProtection($permissions=array('copy'), $user_pass=$password_pdf, $owner_pass="appdev123456", $mode=1, $pubkeys=null);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor(PDF_AUTHOR);
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->SetMargins(0, 20);
		//$pdf->setTopMargin(20);
		//$pdf->setPageOrientation('L','',25);
		//$pdf->setHeaderMargin(140);
		$pdf->setPrintHeader(true);
		$pdf->setPrintFooter(false);
		//$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		//$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		//$pdf->SetHeaderMargin(0);
		//$pdf->SetFooterMargin(0);
		$pdf->SetAutoPageBreak(TRUE, 26);
			
		
		$x_teks_master = 40;
		$y_teks_master = 55;
		$pdf->AddPage();
		//$pdf->Image('../images/logo_bri.jpg',200,0);
		$pdf->Image('../images/bricorp.jpg',35,20,226,56);
		$pdf->SetFont('helvetica','',13);
		$pdf->MultiCell(0,5,'CORPORATE CARD STATEMENT',0,'C',0,1,0,14);
		$pdf->SetFont('','',9);
		$y_kotak=30;
		$pdf->SetY($y_kotak);
		//$pdf->MultiCell(20,5,$cdata['dtcetak'],0,'C',0,0,38,$y_kotak);
		//$pdf->MultiCell(20,5,$cdata['dttempo'],0,'C',0,0,68,$y_kotak);
		$pdf->SetX(38);$pdf->Write(5,$cdata['dtcetak']);
		$pdf->SetX(71);$pdf->Write(5,$cdata['dttempo']);
		$pdf->SetX(109);$pdf->Write(5,$cdata['total_kredit_limit']);
		$pdf->SetX(148);$pdf->Write(5,$cdata['total_batas_tunai']);
		$pdf->SetX(193);$pdf->Write(5,$cdata['bunga_belanja']);
		$pdf->SetX(235);$pdf->Write(5,$cdata['bunga_tunai']);
		$y_kotak=47;
		$pdf->SetY($y_kotak);
		$pdf->SetX(45);$pdf->Write(5,$cdata['total_tagihan_prev']);
		$pdf->SetX(81);$pdf->Write(5,$cdata['total_pembayaran']);
		$pdf->SetX(118);$pdf->Write(5,$cdata['total_pembelanjaan']);
		$pdf->SetX(165);$pdf->Write(5,$cdata['total_ambil_tunai']);
		$pdf->SetX(193);$pdf->Write(5,$cdata['total_tagihan_next']);
		$pdf->SetX(232);$pdf->Write(5,$cdata['total_kredit_sisa']);//111111
		
		$pdf->SetFont('','B',11);
		$pdf->SetXY($x_teks_master,$y_teks_master);
		$pdf->MultiCell(100,5,$cdata['custname1'],0,'L',0,1);
		$pdf->SetFont('','',11);
		$pdf->SetX($x_teks_master);$pdf->MultiCell(100,5,$cdata['custname2'],0,'L',0,1);
		$pdf->SetX($x_teks_master);$pdf->MultiCell(100,5,$cdata['addr1'],0,'L',0,1);
		$pdf->SetX($x_teks_master);$pdf->MultiCell(100,5,$cdata['addr2'],0,'L',0,1);
		$pdf->SetX($x_teks_master);$pdf->MultiCell(100,5,$cdata['addr3'],0,'L',0,1);
		$pdf->MultiCell(100,1,'',0,'L',0,1);
		$pdf->SetX($x_teks_master);$pdf->MultiCell(100,5,$cdata['city'].' '.$cdata['zipcd'],0,'L',0,1);
		$pdf->Ln();
		$pdf->Ln();
		$x_detail_master = 25;
		$pdf->SetFont('','',9);
		$pdf->SetX($x_detail_master-1);$pdf->MultiCell(0,5,'DEPARTMENT/DIVISION NAME :'.$cdata['custname1'].'			'.$cdata['custname3'],0,'L',0,1);
		
		$tbldetmst='
			<table>
				<tr>
					<td width="14%" nowrap>CARDHOLDER NUMBER</td>
					<td width="16%" nowrap>CARDHOLDER NAME</td>
					<td width="12%" align="right">TAGIHAN<br>SEBELUMNYA</td>
					<td width="12%" align="right">PEMBAYARAN/<br>KREDIT (-)</td>
					<td width="12%" align="right">PEMBELANJAAN<br>DEBIT/BUNGA (+)</td>
					<td width="12%" align="right">PENGAMBILAN<br>TUNAI (+)</td>
					<td width="12%" align="right">TAGIHAN<br>BARU</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
				</tr>
				';
		$nheader=count($header['cdnumb']);
		for($i=0;$i<$nheader;$i++){
			$cdname=trim($header['cdname'][$i]);
			if(strlen($cdname)>18)
				$cdname=substr($cdname,0,18)."..";
			$tbldetmst.='
				<tr>
					<td>'.$header['cdnumb'][$i].'</td>
					<td>'.$cdname.'</td>
					<td align="right">'.$header['tagihan_prev'][$i].'&nbsp;&nbsp;&nbsp;&nbsp;</td>
					<td align="right">'.$header['pembayaran'][$i].'&nbsp;&nbsp;&nbsp;&nbsp;</td>
					<td align="right">'.$header['pembelanjaan'][$i].'&nbsp;&nbsp;&nbsp;&nbsp;</td>
					<td align="right">'.$header['ambil_tunai'][$i].'&nbsp;&nbsp;&nbsp;&nbsp;</td>
					<td align="right">'.$header['tagihan_next'][$i].'&nbsp;&nbsp;&nbsp;&nbsp;</td>
				</tr>';
		}
		$tbldetmst.='
				<tr>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>TOTAL</td>
					<td align="right">'.$cdata['total_tagihan_prev'].'&nbsp;&nbsp;&nbsp;&nbsp;</td>
					<td align="right">'.$cdata['total_pembayaran'].'&nbsp;&nbsp;&nbsp;&nbsp;</td>
					<td align="right">'.$cdata['total_pembelanjaan'].'&nbsp;&nbsp;&nbsp;&nbsp;</td>
					<td align="right">'.$cdata['total_ambil_tunai'].'&nbsp;&nbsp;&nbsp;&nbsp;</td>
					<td align="right">'.$cdata['total_tagihan_next'].'&nbsp;&nbsp;&nbsp;&nbsp;</td>
				</tr>';
		$tbldetmst.='
				<tr>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>BATAS KREDIT LIMIT</td>
					<td>&nbsp;</td>
					<td align="right">'.$cdata['total_tagihan_prev'].'&nbsp;&nbsp;&nbsp;&nbsp;</td>
					<td align="right">'.$cdata['total_pembayaran'].'&nbsp;&nbsp;&nbsp;&nbsp;</td>
					<td align="right">'.$cdata['total_pembelanjaan'].'&nbsp;&nbsp;&nbsp;&nbsp;</td>
					<td align="right">'.$cdata['total_ambil_tunai'].'&nbsp;&nbsp;&nbsp;&nbsp;</td>
					<td align="right">'.$cdata['total_tagihan_next'].'&nbsp;&nbsp;&nbsp;&nbsp;</td>
				</tr>';
		$tbldetmst.="
			</table>";
			
		$pdf->SetX($x_detail_master);$pdf->writeHTML($tbldetmst, true, false, true, false, '');
		
		$pdf->AddPage();
		$pdf->SetFont('','',11);
		$pdf->MultiCell(0,5,'CORPORATE CARD DETAILS',0,'C',0,1);
		$pdf->SetFont('','',10);
		$pdf->SetX($x_detail_master-1);$pdf->MultiCell(0,5,'Department/Division Name :'.$cdata['custname1'].'			'.$cdata['custname3'],0,'L',0,1);
		for($i=0;$i<$nheader;$i++){
			if(($cdata['spcust']==1)&&($i>0)){
				$pdf->AddPage();
				$pdf->SetFont('','',11);
				$pdf->MultiCell(0,5,'CORPORATE CARD DETAILS',0,'C',0,1);
				$pdf->SetFont('','',10);
				$pdf->SetX($x_detail_master-1);$pdf->MultiCell(0,5,'Department/Division Name :'.$cdata['custname1'].'			'.$cdata['custname3'],0,'L',0,1);
			}
			//$tblhdrdet='';
			//$pdf->SetX($x_detail_master);$pdf->writeHTML($tblhdrdet, true, 0, true, 0);
			$tbldetdet="";
			if($i>0){
				$tbldetdet.='
					<table>
						<tr><td>&nbsp;</td></tr>
						<tr><td>&nbsp;</td></tr>
						<tr><td>&nbsp;</td></tr>
					</table>';
			}
			$tbldetdet.='
				<table width="95%">
					<tr>
						<td colspan="5">
							<table>
								<tr>
									<td width="77%" colspan="2"></td>
									<td width="10%" align="left">Batas Kredit :</td>
									<td width="8%" align="right">'.$header['kredit_limit'][$i].'</td>
									<td width="5%"></td>
								</tr>
								<tr>
									<td width="17%">'.$header['cdnumb'][$i].'</td>
									<td width="60%">'.$header['cdname'][$i].'</td>
									<td width="10%" align="left">Sisa Kredit :</td>
									<td width="8%" align="right">'.$header['kredit_sisa'][$i].'</td>
									<td width="5%"></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td width="12%">Tgl. Transaksi</td>
						<td width="12%">Tgl. Pembukuan</td>
						<td width="59%">Rincian Transaksi</td>
						<td width="12%">Jumlah (Rp.)&nbsp;</td>
						<td width="5%"></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td>Tagihan Sebelumnya</td>
						<td align="right">'.$header['tagihan_prev'][$i].'</td>
						<td></td>
					</tr>';
			$ndetail=count($detail['dttrans'][$i]);
			for($x=0;$x<$ndetail;$x++){
				if(strtoupper($detail['cr'][$i][$x])=='CR') $cr='CR'; else $cr='';
				$tbldetdet.='
					<tr>
						<td>'.$detail['dttrans'][$i][$x].'</td>
						<td>'.$detail['dtbuku'][$i][$x].'</td>
						<td>'.$detail['trans'][$i][$x].'</td>
						<td align="right">'.$detail['jmltrans'][$i][$x].'</td>
						<td>'.$cr.'</td>
					</tr>';
			}
			$tbldetdet.='
					<tr>
						<td></td>
						<td></td>
						<td align="center">Tagihan Baru</td>
						<td align="right">'.$header['tagihan_next'][$i].'</td>
						<td></td>
					</tr>
				</table>';
			$tot_hlm=$pdf->PageNo();
			$pdf->SetX($x_detail_master);$pdf->writeHTML($tbldetdet, true, false, true, false, '');
			//$pdf->Ln(5);
		}
		
		$pdf_name = substr($cdata['no_card_dat'],-4);
		if($tot_hlm == '') $tot_hlm = $pdf->PageNo();
		output_pdf($pdf,$pdf_name,$blth,$file);		
		return $pdf_name.'|'.$tot_hlm;
	}
	
	function add_page($pdf,
						$hlm,$jml_hlm,
						$url_img,$ximage,$yimage,
						$x,$y,$row_message,
						$no_card,$statement_date,$payment_due_date,$credit_limit,$cash_advan_limit,
						$purchase_debit,$cash_advance,$new_balance,$min_payment,$avail_credit_limit,
						$nama,$address1,$address2,$address3,$city,$zipcode,
						$prev_bonus,$earned_bonus,$point_bonus,$redeemed_bonus,$adjusment_bonus,$point_avail,
						$max_retail_interest,$retail_interest,$max_cash_advan_interest,$cash_advan_interest,
						$prev_balance,$payment_credit,
						$barcode,$posko,$kurir,$nourut,$file){
		$pdf->AddPage();
		
						
						
						
					
					
		$pdf->Image($url_img,$ximage,$yimage,217,302);
		
		
		if (substr($no_card,0,1) == "5") //teks utk MASTER
			{
				$x_teks_master = 175; //$x+168
				$y_teks_master = 13;

					$pdf->SetFont('helvetica','B',5);
					$pdf->SetXY($x_teks_master,$y_teks_master+26.5);
					$pdf->Cell(34,3,"Diterima di seluruh dunia",0,1,'L');
					$pdf->SetFont('Helvetica-Condensed-Light-Li','',5.5);		
					$pdf->SetX($x_teks_master);
					$pdf->MultiCell(35.75,2,"Kartu Kredit BRI dapat digunakan untuk berbelanja di seluruh outlet yang berlogo MasterCard diseluruh dunia.",0,'J');
					
					$pdf->SetFont('helvetica','B',5);
					$pdf->SetXY($x_teks_master,$y_teks_master+56.5);
					$pdf->Cell(34,3,"BRI Protection Plus",0,1,'L');
					$pdf->SetFont('Helvetica-Condensed-Light-Li','',5.5);		
					$pdf->SetX($x_teks_master);
					$pdf->MultiCell(35.75,2,"Perlindungan asuransi jiwa bagi Pemegang Utama Kartu Kredit BRI yang akan melunasi tagihan kartu kredit dan memberikan kelebihan dana* yang bisa dipergunakan oleh keluarga terkasih apabila musibah yang tidak diharapkan terjadi dengan tiba-tiba.",0,'J');
					$pdf->SetXY($x_teks_master-0.5,$y_teks_master+71);
					$pdf->Cell(34,3,"*  Jika  masih terdapat  sisa  manfaat  asuransi",0,1,'L');
					$pdf->SetXY($x_teks_master,$y_teks_master+73.5);
					$pdf->Cell(34,3,"**",0,1,'L');
					$pdf->SetXY($x_teks_master+2,$y_teks_master+74);
					$pdf->MultiCell(33.5,2,"Gratis pembayaran premi bulan pertama apabila terjadi musibah meninggal dunia akibat kecelakaan.",0,'J');
					
					$pdf->SetFont('helvetica','B',5);
					$pdf->SetXY($x_teks_master,$y_teks_master+100.5);
					$pdf->Cell(34,3,"Gratis Akses Executive Lounge",0,1,'L');
					$pdf->SetFont('Helvetica-Condensed-Light-Li','',5.5);		
					$pdf->SetX($x_teks_master);
					$pdf->MultiCell(35.75,2,"Bagi Anda pemegang Kartu Kredit BRI jenis Gold dan Platinum yang bepergian dengan pesawat udara dapat menikmati fasilitas Executive Airport Lounge selama menunggu  waktu  keberangkatan di lebih dari 30 Executive Lounge di bandara seluruh Indonesia.",0,'J');
			
					
					$pdf->SetFont('helvetica','B',5);
					$pdf->SetXY($x_teks_master,$y_teks_master+135);
					$pdf->Cell(34,3,"Program BRING (Belanja RINGan)",0,1,'L');
					$pdf->SetFont('Helvetica-Condensed-Light-Li','',5.5);		
					$pdf->SetX($x_teks_master);
					$pdf->MultiCell(35.99,2,"Anda dapat memiliki produk pilihan melalui program cicilan BRING yang kami tawarkan melalui katalog belanja setiap bulannya.",0,'J');
					
					$pdf->SetFont('helvetica','B',5);
					$pdf->SetXY($x_teks_master,$y_teks_master+164);
					$pdf->MultiCell(35.75,2,"Kemudahan  Pembayaran  Tagihan Rutin di ATM BRI",0,'J');
					$pdf->SetFont('Helvetica-Condensed-Light-Li','',5.5);		
					$pdf->SetXY($x_teks_master,$y_teks_master+169);
					$pdf->MultiCell(35.75,2,"Anda dapat melakukan pembayaran rutin seperti listrik (PLN), telepon (Telkom), telepon selular (Telkomsel dan Matrix), dan biaya pendidikan, Universitas Terbuka melalui ATM BRI.",0,'J');
					
					$pdf->SetFont('helvetica','B',5);
					$pdf->SetXY($x_teks_master,$y_teks_master+198);
					$pdf->Cell(34,3,"Kemudahan  Pembelian  Pulsa Isi Ulang",0,1,'L');
					$pdf->SetFont('Helvetica-Condensed-Light-Li','',5.5);		
					$pdf->SetX($x_teks_master);
					$pdf->MultiCell(35.75,2,"Anda dapat melakukan pembelian pulsa isi ulang di seluruh Indonesia untuk berbagai  operator GSM (Simpati, XL Bebas, IM3 Smart, Mentari, Star One, dan Kartu As)",0,'J');
					
					$pdf->SetFont('helvetica','B',5);
					$pdf->SetXY($x_teks_master,$y_teks_master+230);
					$pdf->Cell(34,3,"Auto Payment",0,1,'L');
					$pdf->SetFont('Helvetica-Condensed-Light-Li','',5.5);		
					$pdf->SetX($x_teks_master);
					$pdf->MultiCell(35.75,2,"Anda dapat melakukan pembayaran listrik (PLN) dan Telepon (Telkom) secara otomatis dengan mendaftar terlebih dahulu melalui ATM BRI atau menghubungi Call BRI di 14017 atau (021) 57 987 400.",0,'J');
					
					$pdf->SetFont('helvetica','B',5);
					$pdf->SetXY($x_teks_master,$y_teks_master+262);
					$pdf->Cell(34,3,"Layanan 24 Jam",0,1,'L');
					$pdf->SetFont('Helvetica-Condensed-Light-Li','',5.5);		
					$pdf->SetX($x_teks_master);
					$pdf->MultiCell(35.75,2,"Customer Service kami siap membantu Anda selama 4 jam sehari dan 7 hari seminggu, termasuk hari Minggu dan hari libur. Hubungi kami di Call BRI di 14017 atau (021)57 987 400.",0,'J');
			
	
			} //selesai utk teks MASTER
			
		$pdf->SetFont('Arial','B',8);
		$y_kotak_atas = 23.5;
		/*$pdf->SetXY($x+2,$y+21.5);
		$pdf->Cell(31,5,$no_card,0,0,'C');
				
		$pdf->SetXY($x+34,$y+21.5);
		$pdf->Cell(24,5,$statement_date,0,0,'C');
		
		$pdf->SetXY($x+60,$y+21.5);
		$pdf->Cell(40,5,$payment_due_date,0,0,'C');
		
		$pdf->SetXY($x+101,$y+21.5);
		$pdf->Cell(22,5,$credit_limit,0,0,'C');
		
		$pdf->SetXY($x+126,$y+21.5);
		$pdf->Cell(34,5,$cash_advan_limit,0,0,'C');*/
		
		$pdf->SetXY($x+2,$y+$y_kotak_atas);
		$pdf->Cell(31,5,$no_card,0,0,'C');
				
		$pdf->SetXY($x+34,$y+$y_kotak_atas);
		$pdf->Cell(24,5,$statement_date,0,0,'C');
		
		$pdf->SetXY($x+60,$y+$y_kotak_atas);
		$pdf->Cell(40,5,$payment_due_date,0,0,'C');
		
		$pdf->SetXY($x+101,$y+$y_kotak_atas);
		$pdf->Cell(22,5,$credit_limit,0,0,'C');
		
		$pdf->SetXY($x+126,$y+$y_kotak_atas);
		$pdf->Cell(34,5,$cash_advan_limit,0,0,'C');
		
		//$y_2 = $pdf->GetY()+17;
		$y_2 = $pdf->GetY()+17.2;
		$pdf->SetXY($x+3,$y_2);
		$pdf->Cell(30,5,$purchase_debit,0,0,'C');
		
		$pdf->SetXY($x+34,$y_2);
		$pdf->Cell(28,5,$cash_advance,0,0,'C');
		
		$pdf->SetXY($x+66,$y_2);
		$pdf->Cell(22,5,$new_balance,0,0,'C');
		
		$pdf->SetXY($x+94,$y_2);
		$pdf->Cell(33,5,$min_payment,0,0,'C');
		
		$pdf->SetXY($x+131,$y_2);
		$pdf->Cell(28,5,$avail_credit_limit,0,0,'C');
		
		
		
		$x_barcode = $x+1;
		$y_barcode = $y+76;
		$pdf->SetXY($x_barcode,$y_barcode);
		$pdf->SetFont('C39P24DhTt','',45);
		$pdf->Cell(70,4,$barcode,0,1,'L');
		
		$pdf->SetFillColor(255,255,255);
		$pdf->Rect($x_barcode,$y_barcode-7,53,5.5,'F');
		$pdf->SetFillColor(255,255,255);
		$pdf->Rect($x_barcode,$y_barcode+5,53,5.5,'F');
		
		$pdf->SetXY($x_barcode+52,$y_barcode-1);
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(10,3,$posko,0,1,'L');
		
		$pdf->SetX($x_barcode+52);
		$pdf->Cell(10,3,$kurir,0,1,'L');
		
		$pdf->SetXY($x_barcode,$y_barcode+5);
		$pdf->Cell(70,3,$barcode." - ".$nourut." / ".$file,0,0,'L');
		
		
		
		$x_nama = $x+1;
		$y_nama = $y+49;
		$pdf->SetFont('BRICARD02','',9);
		
		$pdf->SetXY($x_nama,$y_nama);
		$pdf->Cell(70,4,'Yth. Bapak/Ibu',0,1,'L');
		
		$pdf->SetX($x_nama);
		$pdf->Cell(70,4,$nama,0,1,'L');
		
		if($hlm==1){
			$pdf->SetX($x_nama);
			$pdf->Cell(70,4,$address1,0,1,'L');
			
			$pdf->SetX($x_nama);
			$pdf->Cell(70,4,$address2,0,1,'L');
			
			$pdf->SetX($x_nama);
			$pdf->Cell(70,4,$address3,0,1,'L');
			
			$pdf->SetX($x_nama);
			$pdf->Cell(70,4,$city.' '.$zipcode,0,1,'L');
		}
		
		
		
		$x_attention = $x+84;
		$y_attention = $y+48;
		$pdf->SetXY($x_attention,$y_attention);
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(70,3,'PERHATIAN:',0,1,'C');
		
		$pdf->SetX($x_attention);
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(70,3,'BANK BRI TIDAK PERNAH MEMBERI KUASA KEPADA',0,1,'C');
		
		$pdf->SetX($x_attention);
		$pdf->Cell(70,3,'SIAPAPUN UNTUK MENGAMBIL KEMBALI KARTU',0,1,'C');
		
		$pdf->SetX($x_attention+3);
		$pdf->Cell(21,3,'KREDIT ANDA.',0,0,'L');
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(50,3,'HATI2 JIKA ADA PIHAK YANG',0,1,'L');
		
		$pdf->SetX($x_attention);
		$pdf->Cell(70,3,'MENGATAS NAMAKAN BANK BRI YANG INGIN',0,1,'C');
		
		$pdf->SetX($x_attention);
		$pdf->Cell(70,3,'MENGAMBIL KARTU KREDIT ANDA. JIKA',0,1,'C');
		
		$pdf->SetX($x_attention);
		$pdf->Cell(70,3,'MENEMUKAN KECURIGAAN HARAP MENGHUBUNGI',0,1,'C');
		
		$pdf->SetX($x_attention);
		$pdf->Cell(70,3,'CALL CENTER 24 JAM PADA NO.TLP. 021.579-87400',0,1,'C');
		
		$pdf->SetX($x_attention);
		$pdf->Cell(70,3,'atau 14017.',0,1,'C');
		
		
		
		$x_hlm = $x+133;
		$y_hlm = $y+82;
		$pdf->SetXY($x_hlm,$y_hlm);
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(20,5,'Halaman: '.$hlm.' / '.$jml_hlm);
		
		
		
		/*JUMLAH BARIS MESSAGE HARUS DI-SET DI ATAS*/
		$y_message = $y+219-(3.5*$row_message);
		$x_message = $x+4;
		//$y_message = $y+184;
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($x_message,$y_message);
		$pdf->Cell(150,3.5,'Nasabah Kartu Kredit BRI yang terhormat, terima kasih atas kesetiaan Anda menggunakan Kartu Kredit BRI. Untuk lebih',0,1,'C');
		
		$pdf->SetX($x_message);
		$pdf->Cell(150,3.5,'meningkatkan pelayanan kami kepada Anda maka mulai tgl. 1 Januari 2012 pembayaran tagihan listrik (PT. PLN) dan',0,1,'C');
		
		$pdf->SetX($x_message);
		$pdf->Cell(150,3.5,'telepon (PT. Telkom) melalui BRI akan dikenakan biaya administrasi sebesar Rp 2.500,-',0,1,'C');
		
		$pdf->Ln();
		/************TERSEDIA ($row_message) BARIS UNTUK MESSAGE************/
		
		
		
		if($hlm==1){
			$y_point = $y+227;
			$pdf->SetFont('Arial','B',8);
			$pdf->SetXY($x+5,$y_point);
			$pdf->Cell(21,5,$prev_bonus,0,0,'C');
			
			$pdf->SetX($x+31);
			$pdf->Cell(21,5,$earned_bonus,0,0,'C');
			
			$pdf->SetX($x+57);
			$pdf->Cell(21,5,$point_bonus,0,0,'C');
			
			$pdf->SetX($x+83);
			$pdf->Cell(21,5,$redeemed_bonus,0,0,'C');
			
			$pdf->SetX($x+109);
			$pdf->Cell(21,5,$adjusment_bonus,0,0,'C');
			
			$pdf->SetX($x+135);
			$pdf->Cell(21,5,$point_avail,0,0,'C');
		}
		
		
		
		$y_info = $y+252;
		$pdf->SetFont('Arial','B',8);
		$pdf->SetXY($x+6,$y_info);
		$pdf->Cell(38,5,$max_retail_interest.'    '.$retail_interest,0,0,'C');
		
		$pdf->SetX($x+47);
		$pdf->Cell(41,5,$max_cash_advan_interest.'    '.$cash_advan_interest,0,0,'C');
		
		$pdf->SetX($x+91);
		$pdf->Cell(33,5,$prev_balance,0,0,'C');
		
		$pdf->SetX($x+126);
		$pdf->Cell(31,5,$payment_credit,0,0,'C');
		
		$record = 0;
	}
	
	function end_detail($pdf,$x,$y,$new_balance,$interest_service_charge,$dr_cr_indicator,$statement_date){
		if($interest_service_charge!=0){
			$pdf->Ln();
			$pdf->SetX($x+4);
			$pdf->Cell(35,4,$statement_date,0,0,'L');
			
			$pdf->Cell(29,4,$statement_date,0,0,'L');
			
			$pdf->Cell(67,4,'TOTAL INTEREST & SERVICE CHARGE',0,0,'L');
			
			$pdf->Cell(18,4,$interest_service_charge,0,0,'R');
			
			if($dr_cr_indicator=='CR'){
				$pdf->Cell(2,4,$dr_cr_indicator,0,1,'L');
			}else{
				$pdf->Cell(2,4,'',0,1,'L');
			}
		}
		
		$pdf->Ln();
		$pdf->Ln();
		$pdf->Ln();
		$pdf->Ln();
		
		$pdf->SetX($x+68);
		$pdf->Cell(67,4,'Tagihan Bulan ini',0,0,'L');
		
		$pdf->Cell(18,4,$new_balance,0,1,'R');
	}
	
	function output_pdf($pdf,$nama_file_pdf,$blth,$file){
		$file = str_replace(substr($file,-4),'',$file);
		$blth_pdf = '../pdf/'.$blth.'/';
		$blth_file_pdf = $blth_pdf.$file.'/';
		if(file_exists($blth_pdf)==false) mkdir($blth_pdf);
		if(file_exists($blth_file_pdf)==false) mkdir($blth_file_pdf);
		$pdf_output = $blth_file_pdf.$nama_file_pdf.'.pdf';
		if(file_exists($pdf_output)){
			unlink($pdf_output);
		}
		$pdf->Output($pdf_output,'F');
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
	
	function update_m_loading($m_loading_id,$total_hlm,$customer){
		$sql_upd_m_load = "UPDATE m_loading
							SET
								total_halaman = $total_hlm,
								total_customer = $customer
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