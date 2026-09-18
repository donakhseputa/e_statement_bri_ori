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
	
	function log_customer($flagtrans,$blth,$file){
		$sql_ins_log_customer = "INSERT INTO log_customer(
									userid,
									create_date,
									flagtrans,
									blth,
									nama_file
								)
								VALUES(
									'".$_SESSION['userid']."',
									now(),
									'$flagtrans',
									'$blth',
									'".basename($file)."'
								)";
		$qry_ins_log_customer = pg_query($sql_ins_log_customer) or die('ERROR insert log_customer: '.$sql_ins_log_customer);
		
		$sql_sel_log_customer = "SELECT last_value FROM log_customer_log_customer_id_seq";
		$qry_sel_log_customer = pg_query($sql_sel_log_customer) or die('ERROR select log_customer: '.$sql_sel_log_customer);
		$row_sel_log_customer = pg_fetch_assoc($qry_sel_log_customer);
		$log_customer_id = $row_sel_log_customer['last_value'];
		
		return $log_customer_id;
	}
	
	function dat_to_pdf($flagtrans,$menu_id,$blth,$file){
		//$url_img = "../images/2. Low Res - g0tch4.jpg";.
		//$url_img = "../images/3. Med Res - Without Text - g0tch4.jpg"; //20120301
		//$url_img = "../images/1. Med Res - g0tch4.jpg";
		//$url_img = "../images/BRI_BillingStatement_Rev2-1.png";
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
		$flag_cust = 'Y';
		$flag_case = 'N';
		$no_card_cek = '';
		$msg_not_exists = array ();
		$row_message = 4;				/*VARIABEL INI YANG DIUBAH-UBAH*/
		$max_record = 28-$row_message;
		$row_total_cek = $max_record-5;
		
		$dirfile = '../tmp/'.$file;
		$open_dat = fopen($dirfile,'r');
		
		$log_customer_id = log_customer($flagtrans,$blth,$file);
		$m_loading_id = m_loading($flagtrans,$blth,$file);
		while(!feof($open_dat)){
			
			$row_dat = fgets($open_dat);
			if(trim($row_dat)!=''){
				$regtype = substr($row_dat,0,2);
				if ($flag_case == 'Y' && $regtype == '01')
				{
					if($record >= $hlm*$row_total_cek){
						$hlm++;
						$pdf->SetX($x+90);
						$pdf->Cell(45,4,'bersambung ke halaman '.$hlm.'...',0,1,'L');
						
						add_page($pdf,
									$hlm,$jml_hlm,
									$url_img,$ximage,$yimage,
									$x,$y,$row_message,
									$no_card,$statement_date,$payment_due_date,$credit_limit,$cash_advan_limit,
									$purchase_debit,$cash_advance,$new_balance,$min_payment,$avail_credit_limit,
									$nama,$address1,$address2,$address3,$city,$zipcode,
									$prev_bonus,$earned_bonus,$point_bonus,$redeemed_bonus,$adjusment_bonus,$point_avail,
									$max_retail_interest,$retail_interest,$max_cash_advan_interest,$cash_advan_interest,
									$prev_balance,$payment_credit,
									$barcode,$posko,$kurir,$nourut,$file);
						$pdf->SetXY($x+68,$y_detail-4);
						$pdf->SetFont('BRICARD02','',9);
					}
					$pdf_name = $card_name.'_'.substr($no_card_dat,-4).'_'.$customer;
					end_detail($pdf,$x,$y,$new_balance,$interest_service_charge,$dr_cr_indicator,$statement_date);
					output_pdf($pdf,$pdf_name,$blth,$file);
					insert_mcustomer($no_card_dat, $email, $password_pdf,
										$blth, $flagtrans, $log_customer_id);
					insert_detail($m_loading_id,$nomor_customer,$no_card_dat,
									$nama,$address1,$address2,
									$address3,$city,$zipcode,
									$flagtrans,$blth,$file,
									$pdf_name,$password_pdf,$hlm);
					$flag_cust = 'Y';
				}
				$flag_case = 'Y';
				$flag_cust = 'Y';
				if($regtype == '01'){
					$card_name='';
					$no_card_dat = substr($row_dat,27,16);
						$no_card = substr($no_card_dat,0,4).'-'.substr($no_card_dat,4,4).'-'.substr($no_card_dat,8,4).'-'.substr($no_card_dat,12,4);
					$email = trim(substr($row_dat,580,50));
					$password_pdf = substr($no_card_dat,-6);
				
					if (substr($no_card_dat,0,1) == "4") //VISA
						{
						$url_img = "../images/BRI_BillingStatement_Rev2-1.png";
						$card_name="visa";
					//	$url_img = "../images/BRI_BillingStatement_Rev2-14.gif"; //testing only
						}
						elseif (substr($no_card_dat,0,1) == "5") //MASTER
						{
						$url_img = "../images/layout_master.jpg";
						$card_name="master";
						}
						
				}
				
				if($flag_cust=='Y'){
					/*$sql_cek_m_customer = "SELECT nomor_rekening, password_pdf
											FROM m_customer
											WHERE blth = '$blth'
												AND flagtrans = '$flagtrans'
												AND nomor_rekening = '$no_card_dat'";
					$qry_cek_m_customer = pg_query($sql_cek_m_customer) or die('ERROR cek m_customer: '.$sql_cek_m_customer);
					$jml_customer = pg_num_rows($qry_cek_m_customer);
					$row_cek_m_customer = pg_fetch_assoc($qry_cek_m_customer);
					$password_pdf = $row_cek_m_customer['password_pdf'];*/
					
					if(trim($email) == ""){$jml_customer = 0;}else{$jml_customer = 1;}
					
					if($jml_customer==0){
						$flag_cust = 'N';
						$flag_case = 'N';
						if($regtype == '01'){
							$msg_not_exists[] = $no_card_dat;
						}
					}else{
						switch($regtype){
							case '01':									
									$hlm = 1;
									$jml_hlm = substr($row_dat,884,1);
									$record = 0;
									$customer++;
									$total_hlm = $total_hlm + $jml_hlm;
									$pdf = new FPDI_Protection('P','mm',array(216,304.79999999));
									$pdf->SetAutoPageBreak(true, 7);
									$pdf->SetDisplayMode("real");
									$pdf->SetLeftMargin(1);
									$pdf->SetRightMargin(1);
									$pdf->SetTopMargin(12);
									$pdf->AddFont('BRICARD02','','BRICARD02.php');
									$pdf->AddFont('C39P24DhTt','','C39P24DhTt.php');
									$pdf->AddFont('Helvetica-Condensed-Light-Li','','Helvetica-Condensed-Light-Li.php');
									$pdf->SetProtection(array("print"), $password_pdf, "appdev123456");
									
									$org_type = substr($row_dat,2,3);
									$nama = rtrim(substr($row_dat,43,60));
									$address1 = rtrim(substr($row_dat,103,30));
									$address2 = rtrim(substr($row_dat,133,30));
									$address3 = rtrim(substr($row_dat,163,30));
									$city = rtrim(substr($row_dat,193,30));
									$zipcode = rtrim(substr($row_dat,223,5));
									$statement_date = substr($row_dat,232,8);
										$statement_date = substr($statement_date,6,2).'-'.substr($statement_date,4,2).'-'.substr($statement_date,0,4);
									$payment_due_date = substr($row_dat,240,8);
										$payment_due_date = substr($payment_due_date,6,2).'-'.substr($payment_due_date,4,2).'-'.substr($payment_due_date,0,4);
									$new_balance = number_format((int)substr($row_dat,248,14),0,'',',');
									$min_payment = number_format((int)substr($row_dat,263,14),0,'',',');
									$credit_limit = number_format((int)substr($row_dat,278,14),0,'',',');
									$avail_credit_limit = number_format((int)substr($row_dat,293,14),0,'',',');
									$cash_advan_limit = number_format((int)substr($row_dat,308,14),0,'',',');
									$prev_balance = number_format((int)substr($row_dat,323,14),0,'',',');
									$payment_credit = number_format((int)substr($row_dat,353,14),0,'',',');
									$interest_service_charge = number_format((int)substr($row_dat,398,14),0,'',',');
									$purchase_debit = number_format((int)substr($row_dat,428,14),0,'',',');
									$cash_advance = number_format((int)substr($row_dat,443,14),0,'',',');
									$retail_interest = (int)substr($row_dat,458,5);
										$max_retail_interest = $retail_interest*12;
											$max_retail_interest = str_replace(substr($max_retail_interest,-2),'',$max_retail_interest).'.'.substr($max_retail_interest,-2).'%';
										$retail_interest = str_replace(substr($retail_interest,-2),'',$retail_interest).'.'.substr($retail_interest,-2).'%';
									$cash_advan_interest = (int)substr($row_dat,464,5);
										$max_cash_advan_interest = $cash_advan_interest*12;
											$max_cash_advan_interest = str_replace(substr($max_cash_advan_interest,-2),'',$max_cash_advan_interest).'.'.substr($max_cash_advan_interest,-2).'%';
										$cash_advan_interest = str_replace(substr($cash_advan_interest,-2),'',$cash_advan_interest).'.'.substr($cash_advan_interest,-2).'%';
									$prev_bonus = (int)substr($row_dat,524,6);
									$earned_bonus = (int)substr($row_dat,534,6);
									$point_bonus = (int)substr($row_dat,544,6);
									$redeemed_bonus = (int)substr($row_dat,554,6);
									$adjusment_bonus = (int)substr($row_dat,564,6);
									$point_avail = (int)substr($row_dat,574,6);
									$barcode = substr($row_dat,917,10);
									$posko = substr($row_dat,885,5);
									$kurir = substr($row_dat,891,5);
									$nourut = substr($row_dat,928,5);
									
									add_page($pdf,
												$hlm,$jml_hlm,
												$url_img,$ximage,$yimage,
												$x,$y,$row_message,
												$no_card,$statement_date,$payment_due_date,$credit_limit,$cash_advan_limit,
												$purchase_debit,$cash_advance,$new_balance,$min_payment,$avail_credit_limit,
												$nama,$address1,$address2,$address3,$city,$zipcode,
												$prev_bonus,$earned_bonus,$point_bonus,$redeemed_bonus,$adjusment_bonus,$point_avail,
												$max_retail_interest,$retail_interest,$max_cash_advan_interest,$cash_advan_interest,
												$prev_balance,$payment_credit,
												$barcode,$posko,$kurir,$nourut,$file);
									
									$pdf->SetXY($x+68,$y_detail);
									$pdf->SetFont('BRICARD02','',9);
									$pdf->Cell(67,4,'Tagihan Bulan Lalu',0,0,'L');
									
									$pdf->Cell(18,4,$prev_balance,0,1,'R');
								break;
							
							case '02':
									$record++;
									$embossing_name = rtrim(substr($row_dat,43,30));
									$transaction_date = substr($row_dat,73,8);
										$transaction_date = substr($transaction_date,6,2).'-'.substr($transaction_date,4,2).'-'.substr($transaction_date,0,4);
									$posting_date = substr($row_dat,81,8);
										$posting_date = substr($posting_date,6,2).'-'.substr($posting_date,4,2).'-'.substr($posting_date,0,4);
									$description = substr($row_dat,89,40);
									$amount = number_format((int)substr($row_dat,147,14),0,'',',');
									$dr_cr_indicator = substr($row_dat,162,2);
									
									if($record!=1 && $record==$hlm*$max_record+1){
										$hlm++;
										$pdf->SetX($x+90);
										$pdf->Cell(45,4,'bersambung ke halaman '.$hlm.'...',0,1,'L');
										
										add_page($pdf,
													$hlm,$jml_hlm,
													$url_img,$ximage,$yimage,
													$x,$y,$row_message,
													$no_card,$statement_date,$payment_due_date,$credit_limit,$cash_advan_limit,
													$purchase_debit,$cash_advance,$new_balance,$min_payment,$avail_credit_limit,
													$nama,$address1,$address2,$address3,$city,$zipcode,
													$prev_bonus,$earned_bonus,$point_bonus,$redeemed_bonus,$adjusment_bonus,$point_avail,
													$max_retail_interest,$retail_interest,$max_cash_advan_interest,$cash_advan_interest,
													$prev_balance,$payment_credit,
													$barcode,$posko,$kurir,$nourut,$file);
									}
									
									if($record==1){
										$pdf->SetXY($x+68,$y_detail+4);
									}elseif($record==($hlm-1)*$max_record+1){
										$pdf->SetXY($x+68,$y_detail);
									}
									
									$no_card_det = substr($row_dat,27,16);
										$no_card_det = substr($no_card_det,0,4).'-'.substr($no_card_det,4,4).'-'.substr($no_card_det,8,4).'-'.substr($no_card_det,12,4);
									
									if($tmp_no_card_det != $no_card_det){
										if($record >= $hlm*$max_record-1){
											$hlm++;
											$pdf->SetX($x+90);
											$pdf->Cell(45,4,'bersambung ke halaman '.$hlm.'...',0,1,'L');
											
											add_page($pdf,
														$hlm,$jml_hlm,
														$url_img,$ximage,$yimage,
														$x,$y,$row_message,
														$no_card,$statement_date,$payment_due_date,$credit_limit,$cash_advan_limit,
														$purchase_debit,$cash_advance,$new_balance,$min_payment,$avail_credit_limit,
														$nama,$address1,$address2,$address3,$city,$zipcode,
														$prev_bonus,$earned_bonus,$point_bonus,$redeemed_bonus,$adjusment_bonus,$point_avail,
														$max_retail_interest,$retail_interest,$max_cash_advan_interest,$cash_advan_interest,
														$prev_balance,$payment_credit,
														$barcode,$posko,$kurir,$nourut,$file);
											$pdf->SetXY($x+68,$y_detail-4);
										}
										$pdf->Ln();
										$record++;
										
										$pdf->SetFont('BRICARD02','',9);
										
										$pdf->SetX($x+4);
										$pdf->Cell(35,4,$no_card_det,0,0,'L');
										
										$pdf->Cell(29,4,$embossing_name,0,1,'L');
										$record++;
										
										$tmp_no_card_det = $no_card_det;
									}
									
									$pdf->SetFont('BRICARD02','',9);
									
									$pdf->SetX($x+4);
									$pdf->Cell(35,4,$transaction_date,0,0,'L');
									
									$pdf->Cell(29,4,$posting_date,0,0,'L');
									
									$pdf->Cell(67,4,$description,0,0,'L');
									
									$pdf->Cell(18,4,$amount,0,0,'R');
									
									if($dr_cr_indicator=='CR'){
										$pdf->Cell(2,4,$dr_cr_indicator,0,1,'L');
									}else{
										$pdf->Cell(2,4,'',0,1,'L');
									}
								break;
						}
					}
				}
			}
		}
		if($flag_case == 'Y' && feof($open_dat))
		{
			if($record >= $hlm*$row_total_cek){
				$hlm++;
				$pdf->SetX($x+90);
				$pdf->Cell(45,4,'bersambung ke halaman '.$hlm.'...',0,1,'L');
				
				add_page($pdf,
							$hlm,$jml_hlm,
							$url_img,$ximage,$yimage,
							$x,$y,$row_message,
							$no_card,$statement_date,$payment_due_date,$credit_limit,$cash_advan_limit,
							$purchase_debit,$cash_advance,$new_balance,$min_payment,$avail_credit_limit,
							$nama,$address1,$address2,$address3,$city,$zipcode,
							$prev_bonus,$earned_bonus,$point_bonus,$redeemed_bonus,$adjusment_bonus,$point_avail,
							$max_retail_interest,$retail_interest,$max_cash_advan_interest,$cash_advan_interest,
							$prev_balance,$payment_credit,
							$barcode,$posko,$kurir,$nourut,$file);
				$pdf->SetXY($x+68,$y_detail-4);
				$pdf->SetFont('BRICARD02','',9);
			}
			$pdf_name = $card_name.'_'.substr($no_card_dat,-4).'_'.$customer;
			end_detail($pdf,$x,$y,$new_balance,$interest_service_charge,$dr_cr_indicator,$statement_date);
			output_pdf($pdf,$pdf_name,$blth,$file);
			insert_mcustomer($no_card_dat, $email, $password_pdf,
								$blth, $flagtrans, $log_customer_id);
			insert_detail($m_loading_id,$nomor_customer,$no_card_dat,
							$nama,$address1,$address2,
							$address3,$city,$zipcode,
							$flagtrans,$blth,$file,
							$pdf_name,$password_pdf,$hlm);
			$flag_case = 'N';
			$flag_cust = 'Y';
		}
		update_log_customer($log_customer_id,$customer);
		update_m_loading($m_loading_id,$total_hlm,$customer);
		create_csv($m_loading_id,$blth,$file);
		$not_exists = implode(', ',$msg_not_exists);
		return $customer.'|'.$not_exists;
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
		/*$pdf->SetFont('Arial','B',8);
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
		$pdf->Cell(70,3,'atau 14017.',0,1,'C');*/
		
		$pdf->SetFont('Arial','',9);
		if(substr($no_card,0,4) == '4365'){
			$pdf->MultiCell(70,4,'Untuk meningkatkan pelayanan kami Kepada Pemegang Kartu Kredit BRI Touch, Terhitung Mulai Oktober 2012, Reward Rupiah Kartu Kredit BRI Touch berubah menjadi BRI Point. Info Lebih Lanjut Call BRI 14017',0,'C');
		}else{
			$pdf->MultiCell(70,4,'Kartu Kredit BRI dapat dibayarkan melalu Jaringan Luas ATM BRI, dapat pula menggunakan Kartu ATM Bank apapun melalui Jaringan ATM Bersama, Link, dan Prima. Info Lebih Lanjut Hubungi Call BRI 14017',0,'C');
		}
		
		
		
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
		//$pdf->Cell(150,3.5,'Nasabah Kartu Kredit BRI yang terhormat, terima kasih atas kesetiaan Anda menggunakan Kartu Kredit BRI. Untuk lebih',0,1,'C');
		$pdf->Cell(150,3.5,'Nikmati Layanan E-Statement BRI, tagihan dapat anda terima dimana saja anda berada,',0,1,'C');
		
		$pdf->SetX($x_message);
		//$pdf->Cell(150,3.5,'meningkatkan pelayanan kami kepada Anda maka mulai tgl. 1 Januari 2012 pembayaran tagihan listrik (PT. PLN) dan',0,1,'C');
		$pdf->Cell(150,3.5,'Ketik BRI(spasi)ES(spasi)Nama Pada Kartu#16 Digit No Kartu Kredit#Alamat Email KIRIM KE 9333.',0,1,'C');
		
		$pdf->SetX($x_message);
		//$pdf->Cell(150,3.5,'telepon (PT. Telkom) melalui BRI akan dikenakan biaya administrasi sebesar Rp 2.500,-',0,1,'C');
		$pdf->Cell(150,3.5,'E-Statement BRI Praktis, Cepat, dan Aman',0,1,'C');
		
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
	
	function insert_mcustomer($no_card, $email, $password_pdf,
								$blth, $product, $log_customer_id){
		$sql_ins_cust = "INSERT INTO m_customer(
								nomor_rekening, email1,
								password_pdf, blth, flagtrans,
								log_customer_id)
							VALUES(
								'$no_card', '".addslashes($email)."',
								'$password_pdf', '$blth', '$product',
								$log_customer_id)";
		$qry_ins_cust = pg_query($sql_ins_cust) or die('ERROR insert into detail: '.$sql_ins_cust);
		if(pg_affected_rows($qry_ins_cust)==0){
			echo 'GAGAL INSERT CUSTOMER UNTUK $nomor_rekening';
			die();
		}
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
	
	function update_log_customer($log_customer_id,$customer){
		$sql_upd_log_customer = "UPDATE log_customer
								SET
									total_customer = $customer
								WHERE log_customer_id = $log_customer_id";
		$qry_upd_log_customer = pg_query($sql_upd_log_customer) or die('ERROR update log_customer: '.$sql_upd_log_customer);
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
	
	pg_close($con);
?>