<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/function.php"); ?>
<? define('FPDF_FONTPATH','../include/fpdf/font/'); ?>
<? require_once("../include/fpdf/fpdf.php"); ?>
<? require_once("../include/fpdi/FPDI_Protection.php"); ?>
<?
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
		//$url_img = "../images/logo_bri.jpg";
		$url_img = "../images/bri_dplk.jpg";
		$ximage = 0;
		$yimage = 0;
		$x = 15;
		$y = 24;
		$customer = 0;
		$total_hlm = 0;
		$flag_cust = 'N';
		$flag_cetak = 'N';
		$flag_out = 'N';
		$no_card_cek = '';
		$msg_not_exists = array ();
		
		$dirfile = '../tmp/'.$file;
		$open_dat = fopen($dirfile,'r');

		$m_loading_id = m_loading($flagtrans,$blth,$file);
		while(!feof($open_dat)){
			
			$row_dat = fgets($open_dat);
			if(trim($row_dat)!=''){
				$regtype = substr($row_dat,0,3);
				switch($regtype){
					case '111':
							if(trim(substr($row_dat,3,3)) == "@"){
								$xtl = 0;
								$dtl = 0;
								
								if($flag_cetak == 'Y'){
									/*cek m_customer*/
									$sql_cek_m_customer = "SELECT nomor_rekening, password_pdf
															FROM m_customer
															WHERE blth = '$blth'
																AND flagtrans = '$flagtrans'";
									/*if($file_mcustomer != ''){
										$sql_cek_m_customer .= " AND log_customer_id = ".$file_mcustomer;
									}*/
									//$sql_cek_m_customer .= " AND trim(nomor_rekening) = '".trim($rek_code[0])."'";
									$sql_cek_m_customer .= " AND trim(nomor_rekening) = '".trim($no_cif)."'";
									$qry_cek_m_customer = pg_query($sql_cek_m_customer) or die('ERROR cek m_customer: '.$sql_cek_m_customer);
									$jml_customer = pg_num_rows($qry_cek_m_customer);
									//$jml_customer = 1;
									/*end of cek m_customer*/
									
									include("design_pdf_bd.php");
									
									if($jml_customer==0){
										if($msg_not_exists==""){
											$msg_not_exists = $rek_code[0];
										}else{
											$msg_not_exists .= ', '.$rek_code[0];
										}
									}else{
										$total_hlm = $total_hlm + $jml_hlm;
										$customer++;
										$pdf_name = $rek_code[0].'_'.$customer;
										output_pdf($pdf,$pdf_name,$blth,$file);
										//insert_detail($m_loading_id,$no_cif,$rek_code[0],
										insert_detail($m_loading_id,'',$no_cif,
														$cus_name,substr($cus_adrs,0,30),'',
														'',$cus_city,$zip_code,
														$flagtrans,$blth,$file,
														$pdf_name,$password_pdf,$jml_hlm);
										
										unset($rek_code,$prd_name,$sub_name_mo1,$sld_awal_mo1,$sld_akhr_mo1,$trx_desc,$trx_amn1,$trx_amn2,$trx_amn3,$trx_amn4,$trx_amn5,$trx_amn6,$trx_amn7,$dtl_date,$dtl_desc,$dtl_amnt1,$dtl_amnt2,$dtl_amnt3,$dtl_amnt4,$dtl_amnt5,$dtl_hlm,$xtl_name);
									}
								}
								$flag_cetak = 'Y';
								
								$bartt = trim(substr($row_dat,287,15));
								$dat_file = substr($row_dat,328,12);
								$hlm = trim(substr($row_dat,343,2));
								$jml_hlm = trim(substr($row_dat,345,3));
								$nourut = substr($row_dat,348,6);
							}
						break;
					
					case 'CUS':
							$cus_name = trim(substr($row_dat,3,50));
							$rek_code[] = trim(substr($row_dat,102,25));
						break;
					
					case 'ADR':
							$cus_adrs = substr($row_dat,3,99);
							$cif_code = trim(substr($row_dat,102,35));
								$arr_cif = explode("/",$cif_code);
								$no_cif = trim($arr_cif[0]);
						break;
					
					case 'CTY':
							$cus_city = substr($row_dat,3,40);
							$tgl_lahr = substr($row_dat,102,20);
						break;
					
					case 'AGE':
							$zip_code = substr($row_dat,3,5);
							$end_umur = substr($row_dat,102,2);
						break;
					
					case 'TG1':
							$end_date = substr($row_dat,102,10);
						break;
					
					case 'TG2':
							$tgl_awal = substr($row_dat,3,10);
							$tgl_akhr = substr($row_dat,102,10);
						break;
					
					case 'POR':
							$prd_name[] = substr($row_dat,3,40);
						break;
					
					case 'M01':
							$sub_name_mo1[] = trim(substr($row_dat,3,42));
							$sld_awal_mo1[] = trim(substr($row_dat,53,20));
							$sld_akhr_mo1[] = trim(substr($row_dat,73,20));
						break;
					
					case 'TTL':
							$sld_awal_ttl = trim(substr($row_dat,53,20));
							$sld_akhr_ttl = trim(substr($row_dat,73,20));
						break;
					
					case 'PRD':
							$trx_desc[] = trim(substr($row_dat,3,50));
							$trx_amn1[] = trim(substr($row_dat,53,20));
							$trx_amn2[] = trim(substr($row_dat,73,20));
							$trx_amn3[] = trim(substr($row_dat,93,20));
							$trx_amn4[] = trim(substr($row_dat,113,20));
							$trx_amn5[] = trim(substr($row_dat,133,20));
							$trx_amn6[] = trim(substr($row_dat,153,20));
							$trx_amn7[] = trim(substr($row_dat,173,20));
						break;
					
					case 'GRP':
							$acc_name = substr($row_dat,3,50);
						break;
					
					case 'XTL':
							$xtl++;
							$xtl_name[$xtl] = trim(substr($row_dat,3,75));
							$temp_xtl = $xtl;
							$dtl = 0;
						break;
					
					case 'DTL':
							$dtl++;
							$dtl_date[$xtl][$dtl] = trim(substr($row_dat,3,10));
							$dtl_desc[$xtl][$dtl] = trim(substr($row_dat,18,45));
							$dtl_unit[$xtl][$dtl] = trim(substr($row_dat,63,20));
							$dtl_amnt1[$xtl][$dtl] = trim(substr($row_dat,83,20));
							$dtl_amnt2[$xtl][$dtl] = trim(substr($row_dat,103,20));
							$dtl_amnt3[$xtl][$dtl] = trim(substr($row_dat,123,20));
							$dtl_amnt4[$xtl][$dtl] = trim(substr($row_dat,143,20));
							$dtl_amnt5[$xtl][$dtl] = trim(substr($row_dat,163,20));
							$dtl_hlm[$xtl][$dtl] = trim(substr($row_dat,343,2));
						break;
				}
			}
		}
		
		if($flag_cetak == 'Y'){
			/*cek m_customer*/
			$sql_cek_m_customer = "SELECT nomor_rekening, password_pdf
									FROM m_customer
									WHERE blth = '$blth'
										AND flagtrans = '$flagtrans'";
			/*if($file_mcustomer != ''){
				$sql_cek_m_customer .= " AND log_customer_id = ".$file_mcustomer;
			}*/
			//$sql_cek_m_customer .= " AND trim(nomor_rekening) = '".trim($rek_code[0])."'";
			$sql_cek_m_customer .= " AND trim(nomor_rekening) = '".trim($no_cif)."'";
			$qry_cek_m_customer = pg_query($sql_cek_m_customer) or die('ERROR cek m_customer: '.$sql_cek_m_customer);
			$jml_customer = pg_num_rows($qry_cek_m_customer);
			//$jml_customer = 1;
			/*end of cek m_customer*/
			
			include("design_pdf_bd.php");
			
			if($jml_customer==0){
				if($msg_not_exists==""){
					$msg_not_exists = $rek_code[0];
				}else{
					$msg_not_exists .= ', '.$rek_code[0];
				}
			}else{
				$total_hlm = $total_hlm + $jml_hlm;
				$customer++;
				$pdf_name = $rek_code[0].'_'.$customer;
				output_pdf($pdf,$pdf_name,$blth,$file);
				//insert_detail($m_loading_id,$no_cif,$rek_code[0],
				insert_detail($m_loading_id,'',$no_cif,
								$cus_name,substr($cus_adrs,0,30),'',
								'',$cus_city,$zip_code,
								$flagtrans,$blth,$file,
								$pdf_name,$password_pdf,$jml_hlm);
				
				unset($rek_code,$prd_name,$sub_name_mo1,$sld_awal_mo1,$sld_akhr_mo1,$trx_desc,$trx_amn1,$trx_amn2,$trx_amn3,$trx_amn4,$trx_amn5,$trx_amn6,$trx_amn7,$dtl_date,$dtl_desc,$dtl_amnt1,$dtl_amnt2,$dtl_amnt3,$dtl_amnt4,$dtl_amnt5,$dtl_hlm,$xtl_name);
			}
		}
		update_m_loading($m_loading_id,$total_hlm,$customer);
		create_csv($m_loading_id,$blth,$file);
		$not_exists = implode(',',$msg_not_exists);
		return $customer.'|'.$not_exists;
	}
	
	function addpage($pdf,$x,$y,$ximage,$yimage,$url_img,$cus_name,$cus_adrs,$cus_city,$zip_code,$bartt,$dat_file,$nourut,$hlm,$jml_hlm){
		$pdf->AddPage();
		
		/*$pdf->Image($url_img,$ximage,$yimage,51);
		$pdf->SetXY($ximage-1,$yimage+13);
		$pdf->SetFont('Arial','B',13);
		$pdf->SetTextColor(255,136,31);
		$pdf->Cell(51,5,'CUSTODIAN SERVICES',0,0,'L');
				
		$pdf->SetTextColor(0,0,0);*/
		
		$pdf->Image($url_img,$ximage,$yimage,216,280);
		
		$x_kotaknama = $x;
		$y_kotaknama = $y+3;
		
		$x_barcode = $x_kotaknama+1;
		$y_barcode = $y_kotaknama+18;
		
		$pdf->SetXY($x_barcode,$y_barcode);
		$pdf->SetFont('C39P24DhTt','',45);
		$pdf->Cell(70,4,$bartt,0,1,'L');
		
		$pdf->SetFillColor(255,255,255);
		$pdf->Rect($x_barcode,$y_barcode-7,53,6.5,'F');
		$pdf->SetFillColor(255,255,255);
		$pdf->Rect($x_barcode,$y_barcode+5,53,5.5,'F');
		
		$pdf->SetFont('BRICARD02','',7);
		$pdf->SetXY($x_barcode+1,$y_barcode+6);
		$pdf->Cell(70,3,$bartt,0,0,'L');
		$pdf->SetX($x_barcode+25);
		$pdf->Cell(70,3,$dat_file." - ".$nourut."/".$hlm." of ".$jml_hlm,0,0,'L');
		
		rectfill($pdf,0,0,$x_kotaknama,$y_kotaknama,76,28,'D');
		$pdf->SetFont('BRICARD02','',9);
		
		$pdf->SetXY($x_kotaknama,$y_kotaknama+1);
		$pdf->Cell(76,4,$cus_name,0,1,'L');
		
		$pdf->SetX($x_kotaknama);
		$pdf->Cell(76,4,$cus_adrs,0,1,'L');
		
		$pdf->SetX($x_kotaknama);
		$pdf->Cell(76,4,$cus_city,0,1,'L');
		
		$pdf->SetX($x_kotaknama);
		$pdf->Cell(76,4,$zip_code,0,1,'L');
	}
	
	function rectfill($pdf,$fillcolor,$drawcolor,$x_awal,$y_awal,$lebar,$tinggi,$flag){
		$pdf->SetFillColor($fillcolor);
		$pdf->SetDrawColor($drawcolor);
		$pdf->Rect($x_awal,$y_awal,$lebar,$tinggi,$flag);
	}
	
	function header_rincian($pdf,$x_rincian,$y_rincian,$lebar_kotak){
		$pdf->SetFont('BRICARD02','',9);
		
		$pdf->SetXY($x_rincian,$y_rincian);
		$pdf->Cell(200,4,'Rincian Transaksi',0,1,'L');
		
		rectfill($pdf,0,0,$x_rincian,$y_rincian+4,184,$lebar_kotak,'D');
		
		$pdf->SetXY($x_rincian,$y_rincian+$lebar_kotak+4);
		$pdf->SetFont('BRICARD02','',7);
		$pdf->Cell(185,4,'Apabila dalam waktu 1 (satu) minggu setelah diterimanya laporan tersebut tidak terdapat keberatan, kami anggap isi laporan sudah benar.',0,1,'L');
		
		$pdf->SetFont('BRICARD02','',6);
		
		rectfill($pdf,200,0,$x_rincian+1,$y_rincian+5,15,6,'FD');
		$pdf->SetXY($x_rincian+1,$y_rincian+5);
		$pdf->Cell(15,6,'Tanggal',0,0,'C');
		
		rectfill($pdf,200,0,$x_rincian+17,$y_rincian+5,57,6,'FD');
		$pdf->SetXY($x_rincian+17,$y_rincian+5);
		$pdf->Cell(57,6,'Keterangan',0,0,'C');
		
		rectfill($pdf,200,0,$x_rincian+75,$y_rincian+5,20,6,'FD');
		$pdf->SetXY($x_rincian+75,$y_rincian+5);
		$pdf->Cell(20,3,'Volume',0,0,'C');
		$pdf->SetXY($x_rincian+75,$y_rincian+8);
		$pdf->Cell(20,3,'(Unit, Lembar) (1)',0,0,'C');
		
		rectfill($pdf,200,0,$x_rincian+96,$y_rincian+5,20,6,'FD');
		$pdf->SetXY($x_rincian+96,$y_rincian+5);
		$pdf->Cell(20,3,'NAB/Unit atau',0,0,'C');
		$pdf->SetXY($x_rincian+96,$y_rincian+8);
		$pdf->Cell(20,3,'Harga (Rp) (2)',0,0,'C');
		
		rectfill($pdf,200,0,$x_rincian+117,$y_rincian+5,23,6,'FD');
		$pdf->SetXY($x_rincian+117,$y_rincian+5);
		$pdf->Cell(23,3,'Nilai Investasi (Rp)',0,0,'C');
		$pdf->SetXY($x_rincian+117,$y_rincian+8);
		$pdf->Cell(23,3,'(3=1x2)',0,0,'C');
		
		rectfill($pdf,200,0,$x_rincian+141,$y_rincian+5,19,6,'FD');
		$pdf->SetXY($x_rincian+141,$y_rincian+5);
		$pdf->Cell(19,3,'Biaya (Rp)',0,0,'C');
		$pdf->SetXY($x_rincian+141,$y_rincian+8);
		$pdf->Cell(19,3,'(4)',0,0,'C');
		
		rectfill($pdf,200,0,$x_rincian+161,$y_rincian+5,22,6,'FD');
		$pdf->SetXY($x_rincian+161,$y_rincian+5);
		$pdf->Cell(22,3,'Jumlah Bayar (Rp)',0,0,'C');
		$pdf->SetXY($x_rincian+161,$y_rincian+8);
		$pdf->Cell(22,3,'(5=3+4)',0,0,'C');
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
	
	function insert_detail($m_loading_id,$nomor_customer,$nomor_rekening,
								$nama,$address1,$address2,
								$address3,$city,$zipcode,
								$flagtrans,$blth,$nama_file,
								$pdf_name,$password_pdf,$jml_hlm){
		$sql_ins_detail = "INSERT INTO detail(
								m_loading_id,nomor_customer,nomor_rekening,
								nama,alamat1,alamat2,
								alamat3,city,zipcode,
								flagtrans,blth,nama_file,
								pdf_name,password_pdf,jml_hlm
							)VALUES(
								$m_loading_id,'$nomor_customer','$nomor_rekening',
								'".addslashes($nama)."','".addslashes($address1)."','".addslashes($address2)."',
								'".addslashes($address3)."','".addslashes($city)."','$zipcode',
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