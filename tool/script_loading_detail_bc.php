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
	$file_mcustomer = $arr_pr[5];
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
					$msg = '|'.dat_to_pdf($flagtrans,$menu_id,$blth,$file,$file_mcustomer);
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
		case 'show_file_mcustomer':
				show_file_mcustomer($blth,$flagtrans);
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
	
	function dat_to_pdf($flagtrans,$menu_id,$blth,$file,$file_mcustomer){
	
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
			}
			}else{
				$tabeldetail = "detail";
			}
			
			
		$ximage = 0;
		$yimage = 3.5;
		$x = 10;
		$y = 24;
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

		$m_loading_id = m_loading($flagtrans,$blth,$file);
		while(!feof($open_dat)){
			
			$row_dat = fgets($open_dat);
			if(trim($row_dat)!=''){
				$regtype = substr($row_dat,0,2);
				if ($flag_case == 'Y' && $regtype == '01')
				{
					if($record >= ($hlm*$max_record)-5){
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
									$barcode,$posko,$kurir,$nourut,$file,$kolektibilitas,$forex_curr,$forex_nominal,$kurs_nominal);
						$pdf->SetXY($x+68,$y_detail-4);
						$pdf->SetFont('BRICARD02','',9);
					}
					$pdf_name = $card_name.'_'.substr($no_card_dat,-4).'_'.$customer;
					end_detail($pdf,$x,$y,$new_balance,$interest_service_charge,$dr_cr_indicator,$statement_date);
					output_pdf($pdf,$pdf_name,$flagtrans,$blth,$file);
					insert_detail($m_loading_id,$nomor_customer,$no_card_dat,
									$nama,$address1,$address2,
									$address3,$city,$zipcode,
									$flagtrans,$blth,$file,
									$pdf_name,$password_pdf,$hlm,$tabeldetail,$email, $ket_produk);
					$flag_cust = 'Y';
				}
				$flag_case = 'Y';
				$flag_cust = 'Y';
				if($regtype == '01'){
					$card_name='';
					$no_card_dat = substr($row_dat,27,16);
					$no_card = substr($no_card_dat,0,4).'-'.substr($no_card_dat,4,4).'-'.substr($no_card_dat,8,4).'-'.substr($no_card_dat,12,4);
				
					if (substr($no_card_dat,0,1) == "4") //VISA
					{
						$url_img = "../images/layout_visa_20130328.jpg";
						$card_name="visa";
						$ket_produk="VISA";
					}
					elseif (substr($no_card_dat,0,1) == "5") //MASTER
					{
						$url_img = "../images/layout_master_20130326.jpg";
						$card_name="master";
						$ket_produk="MASTER";
					}
				}
				
				if($flag_cust=='Y'){
				$email = "";
					$sql_cek_m_customer = "SELECT nomor_rekening, password_pdf, email1
											FROM m_customer
											WHERE blth = '$blth'
												AND flagtrans = '$flagtrans'";
					if($file_mcustomer != ''){
						$sql_cek_m_customer .= " AND log_customer_id = ".$file_mcustomer;
					}
					$sql_cek_m_customer .= " AND nomor_rekening = '$no_card_dat'";
					$qry_cek_m_customer = pg_query($sql_cek_m_customer) or die('ERROR cek m_customer: '.$sql_cek_m_customer);
					$jml_customer = pg_num_rows($qry_cek_m_customer);
					#$row_cek_m_customer = pg_fetch_assoc($qry_cek_m_customer);
					
					while($row_email = pg_fetch_array($qry_cek_m_customer)){
						$password_pdf = $row_email['password_pdf'];
						$email .= $row_email['email1'].";";
					}
					
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
									$min_payment = number_format((int)substr($row_dat,263,14),0,'',',');
									$credit_limit = number_format((int)substr($row_dat,278,14),0,'',',');
									$avail_credit_limit = number_format((int)substr($row_dat,293,14),0,'',',');
									$cash_advan_limit = number_format((int)substr($row_dat,308,14),0,'',',');
									$prev_balance = number_format((int)substr($row_dat,323,14),0,'',',');
										$prev_balance_sign = substr($row_dat,337,1);
										$prev_balance = $prev_balance . $prev_balance_sign;
									$payment_credit = number_format((int)substr($row_dat,353,14),0,'',',');
									$interest_service_charge = number_format((int)substr($row_dat,398,14),0,'',',');
									$new_balance = number_format((int)substr($row_dat,413,14),0,'',',');
										$new_balance_sign = substr($row_dat,427,1);
										$new_balance = $new_balance . $new_balance_sign;
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
									$kolektibilitas = substr($row_dat,630,1);
									
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
												$barcode,$posko,$kurir,$nourut,$file,$kolektibilitas,$forex_curr,$forex_nominal,$kurs_nominal);
									
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
									$forex_curr = substr($row_dat,129,3);
									$forex_nominal = substr($row_dat,132,14);
										$forex_nominal = number_format(($forex_nominal/100),2,".",",");
									$kurs_nominal = substr($row_dat,164,10);
										if(trim($kurs_nominal) == "") $kurs_nominal = "0";
										$kurs_nominal = number_format(($kurs_nominal/100),2,".",",");
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
													$barcode,$posko,$kurir,$nourut,$file,$kolektibilitas,$forex_curr,$forex_nominal,$kurs_nominal);
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
														$barcode,$posko,$kurir,$nourut,$file,$kolektibilitas,$forex_curr,$forex_nominal,$kurs_nominal);
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
									
									$pdf->SetFont('BRICARD02','',8);
									
									$pdf->SetX($x+4);
									$pdf->Cell(22,4,$transaction_date,0,0,'L');
									$pdf->Cell(20,4,$posting_date,0,0,'L');
									$pdf->Cell(58,4,$description,0,0,'L');
									$pdf->Cell(6,4,$forex_curr,0,0,'L');
									$pdf->Cell(13,4,$forex_nominal,0,0,'R');
									$pdf->Cell(16,4,$kurs_nominal,0,0,'R');
									$pdf->Cell(17,4,$amount,0,0,'R');
									
									if($dr_cr_indicator=='CR'){
										$pdf->SetX($pdf->GetX()-2);
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
			if($record >= ($hlm*$max_record)-5){
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
							$barcode,$posko,$kurir,$nourut,$file,$kolektibilitas,$forex_curr,$forex_nominal,$kurs_nominal);
				$pdf->SetXY($x+68,$y_detail-4);
				$pdf->SetFont('BRICARD02','',9);
			}
			$pdf_name = $card_name.'_'.substr($no_card_dat,-4).'_'.$customer;
			end_detail($pdf,$x,$y,$new_balance,$interest_service_charge,$dr_cr_indicator,$statement_date);
			output_pdf($pdf,$pdf_name,$flagtrans,$blth,$file);
			insert_detail($m_loading_id,$nomor_customer,$no_card_dat,
							$nama,$address1,$address2,
							$address3,$city,$zipcode,
							$flagtrans,$blth,$file,
							$pdf_name,$password_pdf,$hlm,$tabeldetail, $email, $ket_produk);
			$flag_case = 'N';
			$flag_cust = 'Y';
		}		
		update_m_loading($m_loading_id,$total_hlm,$customer);
		create_csv($m_loading_id,$flagtrans,$blth,$file,$tabeldetail);
		$not_exists = implode(', ',$msg_not_exists);
		
		$base_file = str_replace(substr($file,-4),'',$file);
		$php_path_file = '../pdf/'.$flagtrans.'/'.$blth.'/'.$base_file.'/';
		$file_name_exist = "no_email_".$base_file.".TXT";
		if(trim($not_exists) != ''){
			$create_file_exist = create_file_not_exist($php_path_file, $file_name_exist, str_replace(', ', chr(13), $not_exists));
			if($create_file_exist) {
				$not_exists = "<a href='".$php_path_file.$file_name_exist."' target='_blank'>".$file_name_exist."</a>";
			}
		}
		
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
						$barcode,$posko,$kurir,$nourut,$file,$kolektibilitas,$forex_curr,$forex_nominal,$kurs_nominal){
		$pdf->AddPage();
		
		$pdf->Image($url_img,$ximage,$yimage,217,302);
		
		//teks utk MASTER
		if (substr($no_card,0,1) == "5")
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
		}
		//selesai utk teks MASTER
			
		$pdf->SetFont('Arial','B',8);
		$y_kotak_atas = 23.5;
		
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
		
		$pdf->SetFont('Arial','',9);
		$kodekartu = substr(str_replace("-","",str_replace(" ","",$no_card)),0,8);
		//$text1 = "Nasabah Yth, Selama libur Hari Raya Idul Fitri 2014, pembayaran tagihan tetap dapat dilakukan melalui jaringan ATM BRI, Bersama, Prima dan e-channel BRI lainnya. Untuk info lebih lanjut hubungi Call BRI 14017.";
		//$text1 = "Nasabah Yth. Sehubungan makin maraknya penipuan dengan mengatasnamakan BRI, kami informasikan bahwa pengumuman pemenang undian berhadiah BRI hanya disampaikan melalui media massa Nasional & Website resmi BRI www.bri.co.id serta akun twitter @promo_BRI. Untuk info lebih lanjut hubungi Call BRI 14017.";
		//$text1 = "Nasabah Yth. BRI tidak pernah memberi kuasa kepada pihak lain untuk meminta data atau Kartu Kredit BRI Anda. Mohon untuk menjaga kerahasiaan data Kartu Kredit Anda. Untuk info lebih lanjut hubungi kami di 14017.";
		$text2 = "Nasabah Yth, dalam rangka meningkatkan layanan mulai 1 Februari 2016 Bunga Kartu Kredit BRI disesuaikan menjadi 2.50% per bulan. Terus gunakan dan nikmati berbagai penawaran istimewa dengan Kartu Kredit BRI Info Call BRI 14017";
		$text3 = "Nasabah Yth, dalam rangka meningkatkan layanan mulai 1 Februari 2016 Bunga Kartu Kredit BRI disesuaikan menjadi 2.95% per bulan. Terus gunakan dan nikmati berbagai penawaran istimewa dengan Kartu Kredit BRI Info Call BRI 14017";
		$text_default = "Call BRI 14017";
		switch($kodekartu){
			//case "51882801"	: break; //reguler
			//case "51882821"	: break; //reguler
			//case "51882841"	: break; //reguler
			case "51882801"	: $message_atas = $text2; break; //pekerja - silver
			case "51882821"	: $message_atas = $text2; break; //pekerja - silver
			case "51882841"	: $message_atas = $text2; break; //pekerja - silver
			
			case "51885601"	: $message_atas = $text2; break; //pekerja - gold
			case "51885621"	: $message_atas = $text2; break; //pekerja - gold
			
			case "55200201"	: $message_atas = $text2; break; //pekerja - platinum
			case "43650201"	: $message_atas = $text2; break; //pekerja - Touch
			case "46874001"	: $message_atas = $text2; break; //pekerja - Infinite

			case "51882802"	: $message_atas = $text3; break; //umum - silver
			case "51882822"	: $message_atas = $text3; break; //umum - silver
			case "51882842"	: $message_atas = $text3; break; //umum - silver
			
			case "51885602"	: $message_atas = $text3; break; //umum - gold
			case "51885622"	: $message_atas = $text3; break; //umum - gold
			case "51885652"	: $message_atas = $text3; break; //umum - gold
			case "51885633"	: $message_atas = $text3; break; //umum - gold
	
			case "55200202"	: $message_atas = $text3; break; //umum - platinum
			case "55200252"	: $message_atas = $text3; break; //umum - platinum
			case "55200233"	: $message_atas = $text3; break; //umum - platinum

			case "54758201"	: $message_atas = $text3; break; //umum - bisnis
			case "55347901"	: $message_atas = $text3; break; //umum - corporate
			
			case "43650202"	: $message_atas = $text2; break; //umum - Touch
			case "46874002"	: $message_atas = $text2; break; //umum - Infinite
	
			//case "55347901"	: $message_atas = ""; break; //corporated card
			default				: $message_atas = $text_default; break;
		}
		$pdf->MultiCell(70,4,$message_atas,0,'C');
		
		
		
		$x_hlm = $x+133;
		$y_hlm = $y+82;
		$pdf->SetXY($x_hlm,$y_hlm);
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(20,5,'Halaman: '.$hlm.' / '.$jml_hlm);
		
		
		
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
					$teks_kolektibilitas = "MACET";
		}
		
		
		
		/*JUMLAH BARIS MESSAGE HARUS DI-SET DI ATAS*/
		$y_message = $y+219-(3.5*$row_message);
		$x_message = $x+4;
		/************TERSEDIA ($row_message) BARIS UNTUK MESSAGE************/
		
		$pdf->SetFont('Arial','B',8);
		$pdf->SetXY($x_message - 1,$y_message -3);
		$pdf->Cell(15,5,'Kolektibilitas : ' . $teks_kolektibilitas,0,1,'L');
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($x_message,$y_message + 3);
		$pdf->Cell(150,3.5,'"Nilai Tukar Transaksi Valas XXX sudah memperhitungkan Komponen Biaya Penggunaan Kartu Kredit di Luar Negeri"',0,1,'C');
		
		$pdf->Ln();
		
		
		
		if($hlm==1){
			$y_point = $y+229;
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
		}
		
		$pdf->Ln();
		$pdf->Ln();
		$pdf->Ln();
		$pdf->Ln();
		
		$pdf->SetX($x+68);
		$pdf->Cell(67,4,'Tagihan Bulan ini',0,0,'L');
		
		$pdf->Cell(18,4,$new_balance,0,1,'R');
	}
	
	function output_pdf($pdf,$nama_file_pdf,$flagtrans, $blth,$file){
		$file = str_replace(substr($file,-4),'',$file);
		$blth_flag = '../pdf/'.$flagtrans.'/';
		$blth_pdf = $blth_flag.$blth.'/';
		$blth_file_pdf = $blth_pdf.$file.'/';
		if(file_exists($blth_flag)==false) mkdir($blth_flag);
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
								$pdf_name,$password_pdf,$jml_hlm, $tabeldetail, $email, $ket_produk){
		/**Hitung banyaknya email**/
		$n_email = substr_count($email,";");
		
		/**Hitung besarnya size PDF**/
		$file = str_replace(substr($nama_file,-4),'',$nama_file);
		$document = '../pdf/'.$flagtrans.'/'.$blth.'/';
		$document = $document.$file.'/'.$pdf_name.'.pdf';
		
		$filesizePdfBaru = fsize($document);
		
		
		$sql_ins_detail = "INSERT INTO $tabeldetail(
								m_loading_id,nomor_customer,nomor_rekening,
								nama,alamat1,alamat2,
								alamat3,city,zipcode,
								flagtrans,blth,nama_file,
								pdf_name,password_pdf,jml_hlm,email,n_email,size_pdf,ket_produk
							)VALUES(
								$m_loading_id,'$nomor_customer','$nomor_rekening',
								E'".addslashes($nama)."','".addslashes($address1)."','".addslashes($address2)."',
								'".addslashes($address3)."','".addslashes($city)."','$zipcode',
								'$flagtrans','$blth','$nama_file',
								'$pdf_name','$password_pdf',$jml_hlm,'".addslashes($email)."','$n_email','$filesizePdfBaru','$ket_produk'
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
	
	function create_csv($m_loading_id,$flagtrans, $blth,$file,$tabeldetail){
		$csv = str_replace(substr($file,-4),'',$file);
		
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
	
	function show_record($blth,$flagtrans){
		$sql_jmlrecord = "SELECT count(*) as jumlah
							FROM m_customer
							WHERE blth = '$blth' and flagtrans = '$flagtrans'";
		$qry_jmlrecord = pg_query($sql_jmlrecord) or die('ERROR');
		$row_jmlrecord = pg_fetch_assoc($qry_jmlrecord);
		echo number_format($row_jmlrecord['jumlah'],0,'','.');
	}
	
	function show_file_mcustomer($blth,$flagtrans){
		$sql_filecustomer = "SELECT log_customer_id, nama_file
							FROM log_customer
							WHERE blth = '$blth' and flagtrans = '$flagtrans'
							ORDER BY log_customer_id DESC";
		$qry_filecustomer = pg_query($sql_filecustomer) or die('ERROR');
		?>
        <select id="file_mcustomer" name="file_mcustomer" class="combobox">
        	<option value="">ALL <?php echo $blth?></option>
			<?php
            while($row_filecustomer = pg_fetch_array($qry_filecustomer)){
				?>
            	<option value="<?php echo $row_filecustomer['log_customer_id'] ?>"><?php echo $row_filecustomer['nama_file'] ?></option>
                <?php
			}
            ?>
        </select>
        <?php
	}
	
	@pg_close($con);
?>