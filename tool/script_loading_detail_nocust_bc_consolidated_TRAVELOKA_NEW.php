<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/function.php"); ?>
<? define('FPDF_FONTPATH','../include/fpdf/font/'); ?>
<? require_once("../include/fpdf/fpdf.php"); ?>
<? require_once("../include/fpdi/FPDI_Protection.php"); ?>
<?
	
	
	
	
	ini_set('memory_limit', '-1');
	
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
		
		
		
		
		$sql2 = "TRUNCATE table tmp_excel_hana_bank";
		$qry2 = @pg_query($sql2);
		
		
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
								  CONSTRAINT $tabeldetail_pk PRIMARY KEY (detail_id)
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
		$x = $x + 6.2;
		$y = 24;
		$y_detail = $y+102 - 10;
		$customer = 0;
		$customer_temp = 0;
		$total_hlm = 0;
		$total_hlm_x = 0;
		$flag_cust = 'Y';
		$flag_case = 'N';
		$no_card_cek = '';
		$msg_not_exists = array ();
		$row_message = 2;				/*VARIABEL INI YANG DIUBAH-UBAH*/
		$max_record = 28-$row_message;
		$row_total_cek = $max_record-5;
		
		$dirfile = '../tmp/'.$file;
		$open_dat = fopen($dirfile,'r');
		
		$log_customer_id = log_customer($flagtrans,$blth,$file);
		$m_loading_id = m_loading($flagtrans,$blth,$file);
		
		
		$arr_summary = array();
		$key_CIF = '';
		$flag_summary = false;
		$halaman_cetak_kertas = 1;
		$hlm=0;
		$tagihan_baru_summary_int = 0;
		$pembayaran_minimum_summary_int = 0;
		$flag_a = FALSE;
		
		
		$nama_pdf_1_cif = NULL;
		$nama_pdf_2_cif = NULL;
		//CREATE TABLE TEMP CUSTOMER DI SID BRI
		/*
		$buat_tabel = "DROP TABLE IF EXISTS customer_online;
						CREATE TABLE customer_online
						(
						  customerid integer NOT NULL,
						  kodeproduk character varying(12),
						  flagtrans character varying(2),
						  blth character varying(6),
						  nama_cycle character varying(50),
						  cus_name character varying(100),
						  barcode character varying(25),
						  cardno character varying(25),
						  zip_code character varying(6),
						  flag_status character varying(1),
						  insert_date timestamp without time zone,
						  email character varying(100),
						  cardno2 character varying(25),
						  kode_gabungan character varying(25),
						  kolektibilitas character varying(1),
						  userid character varying(30),
						  flag_update boolean DEFAULT false,
						  flag_update_date timestamp(6) without time zone,
						  flag_approve_date timestamp(6) without time zone,
						  flag_denied_date timestamp(6) without time zone
						)
						WITH (
						  OIDS=TRUE
						);
						ALTER TABLE customer_online
						  OWNER TO postgres;";
						  */
						  
						  
						  
		/* ---------------------------------- CUSTOMER ONLINE  ---------------------------------- */
		/*
			$buat_tabel = "TRUNCATE TABLE customer_online;";
		$exe_tabel = pg_query($buat_tabel)or die("ERROR CREATE TEMP BLOK HOLD NISP 17: " . $buat_tabel);
		//CREATE TABLE TEMP BLOK DARI DB MAS BAGUS 17
		
		//CEK DATA BLOK DARI SERVER GROUPING EDP
		$connection2 = "host=192.168.2.10 port=5432 dbname=bri user=app password=app123";
		$sql_blok = "
		INSERT INTO customer_online
		SELECT customerid, kodeproduk, flagtrans, blth, nama_cycle, cus_name, 
       barcode, cardno, zip_code, flag_status, insert_date, email, cardno2, 
       kode_gabungan, kolektibilitas, userid, flag_update, flag_update_date, 
       flag_approve_date, flag_denied_date 
		FROM dblink('".$connection2."', 'SELECT customerid, kodeproduk, flagtrans, blth, nama_cycle, cus_name, 
       barcode, cardno, zip_code, flag_status, insert_date, email, cardno2, 
       kode_gabungan, kolektibilitas, userid, flag_update, flag_update_date, 
       flag_approve_date, flag_denied_date FROM customer
	   WHERE flag_update=''t'' ' )
		AS t(
		 customerid integer,
						  kodeproduk varchar ,
						  flagtrans varchar ,
						  blth varchar,
						  nama_cycle varchar,
						  cus_name varchar,
						  barcode varchar,
						  cardno varchar,
						  zip_code varchar,
						  flag_status varchar,
						  insert_date timestamp without time zone,
						  email varchar,
						  cardno2 varchar,
						  kode_gabungan varchar,
						  kolektibilitas varchar,
						  userid varchar,
						  flag_update boolean,
						  flag_update_date timestamp without time zone,
						  flag_approve_date timestamp without time zone,
						  flag_denied_date timestamp without time zone
		);";
		//pg_query($sql_blok) or die("<br>GAGAL INSERT DATA BLOK<br>$sql_blok");
		@pg_query($sql_blok);
		@pg_close(pg_connect($connection2));
		$connection = "host=localhost port=5432 dbname=e_statement_bri user=postgres password=Indomedia123"; //LIVE
		#$connection = "host=localhost port=5432 dbname=e_statement_bri user=app password=app123"; //TESTING
		$con = pg_connect($connection) or die("Could not connect to database bri estat!");
		//END OF CEK DATA TEMP CUSTOMER DI SID BRI
		*/
		/* ---------------------------------- CUSTOMER ONLINE  ---------------------------------- */
		
		//die("temporary");
		$baris=0;
		
		
		while(!feof($open_dat)){
			$baris++;
			
					
					
			$row_dat = fgets($open_dat);
			if(trim($row_dat)!=''){
				$regtype=null;
				$regtype = substr($row_dat,0,2);
				$key_CIF_x = substr($row_dat,5,16);
				
				////////////////////////////////////////////////// TAMBAHAN STRUKTUR BARU SUMMARY //////////////////////////////////////////////////////////////////
				if ($regtype == '01' )
				{
					
					if ( $halaman_cetak_kertas != 0 && $flag_a == TRUE )
					{
						#$n_halaman_cetak_kertas = $halaman_cetak_kertas;
						$flag_a = FALSE;
						
						
					}
					
					
					
					$url_pdf_jpg_summary = "../images/Layout_C1_Summary.jpg";
					
					
					
					$OLS_CURR_BAL_SIGN 				=  NULL;
					$CR_LIMIT_SIGN 					=  NULL;
					$OLS_TOT_AMOUNT_DUE_SIGN 		=  NULL;
					$CR_CASH_LINE_SIGN 				=  NULL;
					$CR_AVAIL_CASH_SIGN 			=  NULL;
					$CR_AVAIL_CREDIT_SIGN 			=  NULL;
					
					
					$PT_ST_RECORD_TYPE 			= NULL;
					$OLS_CUST_NBR 				=  NULL;
					$OLS_PYMT_DUE_DATE 			=  NULL;
					$OLS_CURR_BAL 				=  NULL;
					$CR_LIMIT 					=  NULL;
					$OLS_TOT_AMOUNT_DUE 		=  NULL;
					$CR_CASH_LINE 				=  NULL;
					$CR_AVAIL_CASH 				=  NULL;
					$CR_AVAIL_CREDIT 			=  NULL;
					$CR_DTE_BIRTH 				=  NULL;
					$statement_date_summary		=  NULL;
					$tagihan_baru_summary		=  NULL;
					$tagihan_baru_summary_int		=  NULL;
					$pembayaran_minimum_summary	=  NULL;
					$pembayaran_minimum_summary_int	=  NULL;
					
					$nama_cover_summary			=  NULL;
					$address1_cover_summary		=  NULL;
					$address2_cover_summary		=  NULL;
					$address3_cover_summary		=  NULL;
					$city_cover_summary			=  NULL;
					$zipcode_cover_summary		=  NULL;
					$text1_cover_summary		=  NULL;
					
					$no_card_dat_summary		=  NULL;
					$no_card_masking_summary	=  NULL;
					
					$credit_limit_summary		=  NULL;
					$credit_limit_sign_summary		=  NULL;
					$avail_credit_limit_summary	=  NULL;
					$avail_credit_limit_sign_summary	=  NULL;
					$new_balance_summary		=  NULL;
					$new_balance_sign_summary		=  NULL;
					$min_payment_summary		=  NULL;
					$min_payment_sign_summary		=  NULL;
					
					
					$var_cover= 520;
					$email_01=null;
					$email_01 = trim(substr($row_dat,(580+$var_cover),49)); //diganti per 26 juli 2013
					
					if(trim($email_01) == "")
					{
						$flag_email_kosong = TRUE;
						#$flag_email_kosong2 = 'true';
						$OLS_CUST_NBR 				= substr($row_dat,2,16); //NOMOR CIF
						$msg_not_exists[] = $OLS_CUST_NBR ;
						continue;
					}else{
						$flag_email_kosong = FALSE;
						#$flag_email_kosong2 = 'false';
						#die($email);
					}

					$PT_ST_RECORD_TYPE 			= substr($row_dat,0,2);
					$OLS_CUST_NBR 				= substr($row_dat,2,16); //NOMOR CIF
					$OLS_PYMT_DUE_DATE 			= substr($row_dat,18,8); //TANGGAL JATUH TEMPO PEMBAYARAN
					
					
					$OLS_CURR_BAL 				= substr($row_dat,26,14); //TOTAL TAGIHAN BARU (UNTUK SELURUH KARTU)
					$OLS_CURR_BAL 				= number_format((int)$OLS_CURR_BAL,0,'','.');
					$OLS_CURR_BAL_SIGN 			= substr($row_dat,40,1);
					$OLS_CURR_BAL_SIGN 			= ($OLS_CURR_BAL_SIGN == '-' ? '-' : '');
					$OLS_CURR_BAL 				= $OLS_CURR_BAL_SIGN.$OLS_CURR_BAL; //TOTAL TAGIHAN BARU (UNTUK SELURUH KARTU)
					
					
					$CR_LIMIT 					= substr($row_dat,41,14); #CREDIT LIMIT GABUNGAN
					$CR_LIMIT 					= number_format((int)$CR_LIMIT,0,'','.');
					$CR_LIMIT_SIGN 				= substr($row_dat,55,1); #CREDIT LIMIT GABUNGAN SIGN
					$CR_LIMIT_SIGN 				= ($CR_LIMIT_SIGN == '-' ? '-' : '');
					$CR_LIMIT 					= $CR_LIMIT_SIGN.$CR_LIMIT; #CREDIT LIMIT GABUNGAN
					
					
					$OLS_TOT_AMOUNT_DUE 		= substr($row_dat,56,14); //TOTAL MINUM PAYMENT (UNTUK SELURUH KARTU)
					$OLS_TOT_AMOUNT_DUE 		= number_format((int)$OLS_TOT_AMOUNT_DUE,0,'','.');
					$OLS_TOT_AMOUNT_DUE_SIGN 	= substr($row_dat,70,1);
					$OLS_TOT_AMOUNT_DUE_SIGN 	= ($OLS_TOT_AMOUNT_DUE_SIGN == '-' ? '-' : '');
					$OLS_TOT_AMOUNT_DUE 		= $OLS_TOT_AMOUNT_DUE_SIGN.$OLS_TOT_AMOUNT_DUE; //TOTAL MINUM PAYMENT (UNTUK SELURUH KARTU)
					
					
					$CR_CASH_LINE 				= substr($row_dat,71,14); //BATAS PENGAMBILAN TUNAI
					$CR_CASH_LINE 				= number_format((int)$CR_CASH_LINE,0,'','.');
					$CR_CASH_LINE_SIGN 			= substr($row_dat,85,1);
					$CR_CASH_LINE_SIGN 			= ($CR_CASH_LINE_SIGN == '-' ? '-' : '');
					$CR_CASH_LINE 				= $CR_CASH_LINE_SIGN.$CR_CASH_LINE;
					
					
					$CR_AVAIL_CASH 				= substr($row_dat,86,14); //SISA / BATAS PENGAMBILAN TUNAI
					$CR_AVAIL_CASH 				= number_format((int)$CR_AVAIL_CASH,0,'','.');
					$CR_AVAIL_CASH_SIGN 		= substr($row_dat,100,1);
					$CR_AVAIL_CASH_SIGN 		= ($CR_AVAIL_CASH_SIGN == '-' ? '-' : '');
					$CR_AVAIL_CASH 				= $CR_AVAIL_CASH_SIGN.$CR_AVAIL_CASH; //SISA / BATAS PENGAMBILAN TUNAI
					
					$CR_AVAIL_CASH = $CR_CASH_LINE;//20201023 PERUBAHAN BATAS PENGAMBILAN TUNAI
					
					
					$CR_AVAIL_CREDIT 			= substr($row_dat,101,14); #SISA KREDIT LIMIT GABUNGAN
					$CR_AVAIL_CREDIT 			= number_format((int)$CR_AVAIL_CREDIT,0,'','.');
					$CR_AVAIL_CREDIT_SIGN		= substr($row_dat,115,1); 
					$CR_AVAIL_CREDIT_SIGN 		= ($CR_AVAIL_CREDIT_SIGN == '-' ? '-' : '');
					$CR_AVAIL_CREDIT 			= $CR_AVAIL_CREDIT_SIGN.$CR_AVAIL_CREDIT; #SISA KREDIT LIMIT GABUNGAN
					
					$CR_DTE_BIRTH 				= substr($row_dat,116,8); #die($CR_DTE_BIRTH);
					$var_cover_summary = 520;
					$nama_pdf_1_cif = $OLS_CUST_NBR;
					
					$nama_cover_summary 						= rtrim(substr($row_dat,43+$var_cover_summary,60));
					$address1_cover_summary 					= rtrim(substr($row_dat,103+$var_cover_summary,30));
					$address2_cover_summary 					= rtrim(substr($row_dat,133+$var_cover_summary,30));
					$address3_cover_summary						= rtrim(substr($row_dat,163+$var_cover_summary,30));
					$city_cover_summary 						= rtrim(substr($row_dat,193+$var_cover_summary,30));
					$zipcode_cover_summary 						= rtrim(substr($row_dat,223+$var_cover_summary,5));
					
					
					
					#$text1_cover_summary =   "Yth. Nasabah KK BRI. Demi meningkatkan layanan, kami akan mengenakan biaya sms notifikasi transaksi sebesar Rp 5.000 per kartu mulai tanggal 1 September 2019. Informasi lebih lanjut hubungi Contact BRI 14017/1500017" ; 
					#$text1_cover_summary =   "Yth. Nasabah KK BRI. Sesuai dgn Surat Edaran Bank Indonesia No. 16/25/DSKP tgl 31-12-2014, mulai tgl 01-07-2020 pemegang kartu kredit WAJIB menggunakan PIN 6 digit sebagai sarana verifikasi & autentikasi transaksi. Info Contact BRI 14017/1500017" ; //20191015
					
					#$text1_cover_summary =   "Yth Nasabah KK BRI, untuk kenyamanan Anda per 3 Oktober 2020 seluruh Lembar Tagihan KK BRI akan disampaikan dalam satu file/amplop yang sama. Tingkatkan transaksi dan nikmati berbagai penawaran istimewa Kartu Kredit BRI. Info Contact BRI 14017/1500017" ; //20201005
					
					/*
					$text1_cover_summary =   "Yth Nasabah Kartu Kredit BRI, sesuai dengan UU RI No. 10 tahun 2020 tanggal 26 Oktober 2020 tentang bea materai, maka per tanggal 1 Januari 2021 Biaya Materai menjadi sebesar Rp.10.000,- (sepuluh ribu rupiah) untuk total Transaksi Kartu Kredit BRI di atas Rp.5.000.000,- (lima juta rupiah). "."\n"."Info Contact BRI 14017/1500017" ; //20201223
					*/
					
					$text1_cover_summary = "Yth Nasabah Kartu Kredit BRI, untuk kelancaran anda dalam bertransaksi pastikan alamat pengiriman Kartu Kredit BRI anda sudah benar dan lengkap. Apabila ada perubahan alamat mohon dapat dilakukan pengkinian data melalui : "."\n"."1. BRICC Mobile (dapat didownload melalui Appstore / Playstore)"."\n"."2. Info Contact BRI 14017 /1500017" ; //20210319
					
					
					$statement_date_summary = substr($row_dat,232+$var_cover_summary,8);
					$statement_date_summary = substr($statement_date_summary,6,2).'-'.substr($statement_date_summary,4,2).'-'.substr($statement_date_summary,0,4);
					$OLS_PYMT_DUE_DATE_x = substr($OLS_PYMT_DUE_DATE,6,2).'-'.substr($OLS_PYMT_DUE_DATE,4,2).'-'.substr($OLS_PYMT_DUE_DATE,0,4);
					
					$tagihan_baru_summary = substr($row_dat,413+$var_cover_summary,14);
					#$tagihan_baru_summary_int += (int)$tagihan_baru_summary;
					$tagihan_baru_summary_int = $OLS_CURR_BAL;
					
					$pembayaran_minimum_summary = substr($row_dat,263+$var_cover_summary,14);
					#$pembayaran_minimum_summary_int += (int)$pembayaran_minimum_summary;
					$pembayaran_minimum_summary_int = $OLS_TOT_AMOUNT_DUE;
					
					
					$no_card_dat_summary 		= substr($row_dat,27+$var_cover_summary,16);
					$no_card_masking_summary 	= substr($no_card_dat_summary,0,4).'-'.substr($no_card_dat_summary,4,2).'XX-XXXX'.'-'.substr($no_card_dat_summary,12,4);
					
					
					$credit_limit_summary 		= substr($row_dat,278+$var_cover_summary,14);
					$credit_limit_summary 		= number_format((int)$credit_limit_summary,0,'','.');//perubahan pemisah desimal titik
					$credit_limit_sign_summary 	= substr($row_dat,292+$var_cover_summary,1);
					$credit_limit_sign_summary 	= ($credit_limit_sign_summary == '-' ? '-' : '');
					$credit_limit_summary = $credit_limit_sign_summary.$credit_limit_summary ;
					
					$avail_credit_limit_summary 		= substr($row_dat,293+$var_cover_summary,14);
					$avail_credit_limit_summary 		= number_format((int)$avail_credit_limit_summary,0,'','.');//perubahan pemisah desimal titik
					$avail_credit_limit_sign_summary 	= substr($row_dat,307+$var_cover_summary,1);
					$avail_credit_limit_sign_summary 	= ($avail_credit_limit_sign_summary == '-' ? '-' : '');
					$avail_credit_limit_summary = $avail_credit_limit_sign_summary.$avail_credit_limit_summary ;
					
					#$new_balance_summary 		= substr($row_dat,413+$var_cover_summary,14);
					$min_payment_summary 		= substr($row_dat,263+$var_cover_summary,14);
					$min_payment_summary 		= number_format((int)$min_payment_summary,0,'','.');//perubahan pemisah desimal titik
					
					
					$new_balance_summary = number_format((int)substr($row_dat,413+$var_cover_summary,14),0,'','.');//perubahan pemisah desimal titik
					$new_balance_sign_summary = substr($row_dat,427+$var_cover_summary,1);
					#$new_balance_summary = $new_balance_summary . $new_balance_sign_summary. ' YY';
					$new_balance_sign_summary 		= ($new_balance_sign_summary == '-' ? '-' : '');
					$new_balance_summary = $new_balance_sign_summary.$new_balance_summary ;
										
										
					
					$flag_summary = true;
					
					
					
					$barcode = substr($row_dat,(917+$var_cover_summary),10);
					$posko = substr($row_dat,(885+$var_cover_summary),5);
					$kurir = substr($row_dat,(891+$var_cover_summary),5);
					$nourut = substr($row_dat,(928+$var_cover_summary),5);
					$kolektibilitas = substr($row_dat,(630+$var_cover_summary),1);
					
					
					$cif_masking 	= substr($OLS_CUST_NBR,0,4).'-'.substr($OLS_CUST_NBR,4,2).'XX-XXXX'.'-'.substr($OLS_CUST_NBR,12,4);
									
					#$arr_summary[$OLS_CUST_NBR]['header1'] = "$OLS_CUST_NBR##$OLS_PYMT_DUE_DATE_x##$CR_LIMIT##$CR_AVAIL_CASH";
					$arr_summary[$OLS_CUST_NBR]['header1'] = "$cif_masking##$OLS_PYMT_DUE_DATE_x##$CR_LIMIT##$CR_AVAIL_CASH";
					$arr_summary[$OLS_CUST_NBR]['header2'] = "$statement_date_summary##$tagihan_baru_summary_int##$pembayaran_minimum_summary_int##$CR_AVAIL_CREDIT";
					
					$arr_summary[$OLS_CUST_NBR]['header3'] = "Yth. Bapak/Ibu##$nama_cover_summary##$address1_cover_summary##$address2_cover_summary##$address3_cover_summary##$city_cover_summary $zipcode_cover_summary## ##$text1_cover_summary";
					
					$arr_summary[$OLS_CUST_NBR][$no_card_dat_summary] = "$nama_cover_summary##$no_card_masking_summary##$credit_limit_summary##$new_balance_summary##$min_payment_summary";
					
					$arr_summary[$OLS_CUST_NBR]['footer1'] = "$CR_LIMIT";
					$arr_summary[$OLS_CUST_NBR]['footer2'] = "$CR_AVAIL_CREDIT";
					
					$arr_summary[$OLS_CUST_NBR]['footer5'] = "$tagihan_baru_summary_int";
					$arr_summary[$OLS_CUST_NBR]['footer6'] = "$pembayaran_minimum_summary_int";
					
					
					
					
					
					
					
				}
				#die ($flag_email_kosong2);
				
				
				
if ( $flag_email_kosong == FALSE){
				
				if ($regtype == '04' )
				{
					$key_CIF = $key_CIF_x;
					$tagihan_baru_summary_int = 0;
					$pembayaran_minimum_summary_int = 0;
					
					$flag_a = TRUE;
					
					
					
				}
				
				////////////////////////////////////////////////// TAMBAHAN STRUKTUR BARU SUMMARY //////////////////////////////////////////////////////////////////
				
				#if ($key_CIF != '' ) die("$key_CIF != $key_CIF_x");
				if ($flag_case == 'Y' && $regtype == '02'  )
				###if ($flag_case == 'Y' && $regtype == '02' && ( $GLOBALS['flag_hana_bank']  === true || $GLOBALS['flag_hana_bank_2']  === true ) )
				{
										
					#if( ($record >= ($hlm*$max_record)-5 )  && !empty($hlm) ){
					if( ($record >= ($hlm*$max_record)-5 )  ){
						
						$hlm++;
						#echo "halaman $hlm , halaman kertas $halaman_cetak_kertas <br>";
						
						
						
						$pdf->SetX($x+90);
						#$pdf->Cell(45,4,'1 bersambung ke halaman '.$hlm.'...',0,1,'L');
						$pdf->Cell(45,4,'halaman '.$halaman_cetak_kertas.' bersambung ke halaman '.($halaman_cetak_kertas+1).'...',0,1,'L');
						
						$halaman_cetak_kertas = add_page($pdf,
									$hlm,$jml_hlm,
									$url_img,$ximage,$yimage,
									$x,$y,$row_message,
									$no_card,$statement_date,$payment_due_date,$credit_limit,$cash_advan_limit,
									$purchase_debit,$cash_advance,$new_balance,$min_payment,$avail_credit_limit,
									$nama,$address1,$address2,$address3,$city,$zipcode,
									$prev_bonus,$earned_bonus,$point_bonus,$redeemed_bonus,$adjusment_bonus,$point_avail,
									$max_retail_interest,$retail_interest,$max_cash_advan_interest,$cash_advan_interest,
									$prev_balance,$payment_credit,
									$barcode,$posko,$kurir,$nourut,$file,$kolektibilitas,$forex_curr,$forex_nominal,$kurs_nominal
									,$url_pdf_jpg
									,$halaman_cetak_kertas
									,$y_keb_hana_tambahan1,$y_keb_hana_tambahan2,$y_keb_hana_tambahan3,$y_keb_hana_tambahan4,$y_keb_hana_tambahan5);
						$pdf->SetXY($x+68,$y_detail-4);
						$pdf->SetFont('BRICARD02','',7.12);
					}
					
					$pdf_name=null;
					#$pdf_name = $card_name.'_'.substr($no_card_dat,-4).'_'.$customer;
					#$pdf_name = 'Billing_Statement'.'_'.substr($nama_pdf_2_cif,-4).'_'.str_pad($customer, 6, "0", STR_PAD_LEFT);
					$pdf_name = str_pad($customer, 6, "0", STR_PAD_LEFT).'_Billing_Statement'.'_'.substr($nama_pdf_2_cif,-4);
					#die("bb");
					#die("$email - $baris");
					$halaman_cetak_kertas = end_detail($pdf,$x,$y,$new_balance,$interest_service_charge,$dr_cr_indicator,$statement_date ,$halaman_cetak_kertas);
					
					if ( ($key_CIF != $key_CIF_x && $key_CIF != '') )
					{
						#$hlm_tabel = $n_halaman_cetak_kertas;
						$hlm_tabel = $halaman_cetak_kertas;
						output_pdf($pdf,$pdf_name,$flagtrans,$blth,$file);
						#$yyyyyy = "AAAAA ";
					}
					#output_pdf($pdf,$pdf_name,$flagtrans,$blth,$file);
					insert_mcustomer($no_card_dat, $email, $password_pdf,
										$blth, $flagtrans, $log_customer_id);
					
					///////////////////////////////////////////////////////////////////////////////////
					
					
					///////////////////////////////////////////////////////////////////////////////////
					if ( ($key_CIF != $key_CIF_x && $key_CIF != '') )
					{
					
					$total_hlm_x += $hlm_tabel;
					#echo "$yyyyyy ## $hlm --- $halaman_cetak_kertas x1/"."($key_CIF != $key_CIF_x && $key_CIF != '')"."<br>";
					#insert_detail($m_loading_id,$nomor_customer,$no_card_dat,
					insert_detail($m_loading_id,$nomor_customer,$nama_pdf_2_cif,
									$nama,$address1,$address2,
									$address3,$city,$zipcode,
									$flagtrans,$blth,$file,
									$pdf_name,$password_pdf,$hlm_tabel, $email, $tabeldetail, $ket_produk);
					
					}
					$flag_cust=null;
					$flag_cust = 'Y';
					
					
					
				}
				
				
			
				
				
				
				if($regtype == '02'  ){
					
					$flag_case=null;
					$flag_case = 'Y';
					$flag_cust = 'Y';
				
					//////////////////////////////// STRUKTUR BARU
					#$halaman_cetak_kertas++;
					#$hlm++;
					$var_cover= 520;
					$nama_pdf_2_cif = $nama_pdf_1_cif;
					#echo "halaman $hlm , halaman kertas $halaman_cetak_kertas <br>";
					//////////////////////////////// STRUKTUR BARU
					
					
					
					$card_name='';
					$no_card_dat=null;
					$no_card_dat = substr($row_dat,27,16);
					
					$GLOBALS['nomor_rekening'] = $no_card_dat;
					$GLOBALS['geser'] = 0;
					$GLOBALS['flag_hana_bank']  = false;
					$GLOBALS['flag_hana_bank_2']  = false;
					$GLOBALS['flag_bri_btn']  = false;
					
						$no_card=null;
						$no_card = substr($no_card_dat,0,4).'-'.substr($no_card_dat,4,4).'-'.substr($no_card_dat,8,4).'-'.substr($no_card_dat,12,4);
						$no_card_masking = substr($no_card_dat,0,4).'-'.substr($no_card_dat,4,2).'XX-XXXX'.'-'.substr($no_card_dat,12,4);
					#$email=null;
					$email = trim(substr($row_dat,(580+$var_cover),49)); //diganti per 26 juli 2013
					#$email = 'AAAAAAAAAAAAAAAAAA';
					
					/* -------------------------------*/
					//cek email online 
					//@pg_close();
					//$email = cek_email_status_online($no_card_dat, $email);
					//@pg_close();
					//$connection = "host=localhost port=5432 dbname=e_statement_bri user=postgres password=Indomedia123";
					//$con = pg_connect($connection) or die("Could not connect to database bri estat!");
					/* -------------------------------*/
					//$email = trim(substr($row_dat,580,50)); 
					$password_pdf=null;
					#$password_pdf = substr($no_card_dat,-6); //PASSWORD LAMA
					
					
					
					
					$x_thn =null;
					$x_bln =null;
					$x_tgl =null;
					
					$x_thn = substr($CR_DTE_BIRTH,0,4);
					$x_bln = substr($CR_DTE_BIRTH,4,2);
					$x_tgl = substr($CR_DTE_BIRTH,6,2);
					
					$password_pdf = $x_tgl.get_3_huruf_bulan($x_bln).$x_thn;
					
					
					
					
					
					$url_img= NULL;
					$card_name= NULL;
					$ket_produk= NULL;
					$yimage= NULL;
					$y= NULL;
					
					$url_pdf = '';
					$url_pdf_jpg = '';
					$y_traveloka_tambahan = 0;
					
					$y_keb_hana_tambahan1 = 0;
					$y_keb_hana_tambahan2 = 0;
					$y_keb_hana_tambahan3 = 0;
					$y_keb_hana_tambahan4 = 0;
					$y_keb_hana_tambahan5 = 0;
					
					if (substr($no_card_dat,0,1) == "4") //VISA
					{
						
							
						if ( substr($no_card_dat,0,8) == "46874001" || substr($no_card_dat,0,8) == "46874002") //Infinite - 20160419 Bowo
						{
							//$url_pdf = 'Promo_Premium.pdf';
							$url_pdf_jpg = 'Promo_Infinite.jpg';
							
							#$url_img = "../images/layout_infinite.jpg"; //kz
							$url_img = "../images/Layout_C1_VISA.jpg"; //OK
							$card_name="visa";
							$ket_produk="VISA";
							#$yimage = 3.5;//kz
							#$y = 24;//kz
							
							$yimage = 0;
							$y = 24;
							//die('Infinite');
							
							#$GLOBALS['geser'] = 1.4; //kz
							$GLOBALS['geser'] = 0;
							###$GLOBALS['flag_hana_bank_2']  = true;
							###$customer_temp++;		
                           if ( substr($no_card_dat,0,8) == "46874001" ) //Infinite - 20160419 Bowo
							{
								#$url_pdf_jpg = 'Promo_Infinite_Pekerja.jpg';
							}
						} 
						else if ( substr($no_card_dat,0,8) == "43650203" ) //traveloka - 20160419 Bowo
						{
							//$url_pdf = 'Promo_Premium.pdf';
							$url_pdf_jpg = '';
							
							$url_img = "../images/layout_traveloka3_r1.jpg";
							$card_name="visa";
							$ket_produk="VISA";
							#$yimage = 3.5;
							#$y = 24;
							$yimage = 0;
							$y = 24;
							//die('traveloka');
							
							#$GLOBALS['geser'] = 1.4; //traveloka old
							$GLOBALS['geser'] = 0;
							
							$y_traveloka_tambahan = 2;
						}
						else if ( substr($no_card_dat,0,8) == "43650204" ) //BRI Pegadaian - 20201123
						{
							//$url_pdf = 'Promo_Premium.pdf';
							$url_pdf_jpg = '';
							
							$url_img = "../images/layout_pegadaian.jpg";
							$card_name="visa";
							$ket_produk="VISA";
							#$yimage = 3.5;
							#$y = 24;
							$yimage = 0;
							$y = 24;
							//die('pegadaian');
							
							#$GLOBALS['geser'] = 1.4; //traveloka old
							$GLOBALS['geser'] = 0;
							
							$y_traveloka_tambahan = 2;
						}
						elseif ( substr($no_card_dat,0,8) == "43597201" || substr($no_card_dat,0,8) == "43597202") //Hana bank Platinum - 20160727 Bowo
						{
							//$url_pdf = '';
							$url_pdf_jpg = 'Promo_Hana_Bank.jpg';
							
							$url_img = "../images/layout_hana_bank.jpg";//kz
							$card_name="visa";
							$ket_produk="VISA";
							#$yimage = 0; //kz
							$y = 21.5; //kz
							
							$yimage = 0;
							#$y = 24;
							/*$y_detail = $y+102;*/
							//die('Infinite');
							
							#$GLOBALS['geser'] = 1.4; //kz
							$GLOBALS['geser'] = -10;
							$GLOBALS['flag_hana_bank']  = true;
							$y_keb_hana_tambahan1 = 5;
							$y_keb_hana_tambahan2 = 5;
							$y_keb_hana_tambahan3 = 7;
							$y_keb_hana_tambahan4 = 8;
							$y_keb_hana_tambahan5 = 4;
							###$customer_temp++;
						}
						elseif ( substr($no_card_dat,0,8) == "43596501" || substr($no_card_dat,0,8) == "43596502" ) //Hana bank - 20160727 Bowo
						{
						
							if (
							substr($no_card_dat,0,16) == "4359650100032409" || substr($no_card_dat,0,16) == "4359650100028605" ||
							substr($no_card_dat,0,16) == "4359650100027201" || substr($no_card_dat,0,16) == "4359650100033704" ||
							substr($no_card_dat,0,16) == "4359650100005009" || substr($no_card_dat,0,16) == "4359650200026400" ||
							substr($no_card_dat,0,16) == "4359650100011007" || substr($no_card_dat,0,16) == "4359650100033209" ||
							substr($no_card_dat,0,16) == "4359650280004608" || substr($no_card_dat,0,16) == "4359650200014000" ||
							substr($no_card_dat,0,16) == "4359650100005108" || substr($no_card_dat,0,16) == "4359650180007107" ||
							substr($no_card_dat,0,16) == "4359650100029306" || substr($no_card_dat,0,16) == "4359650100031500" ||
							substr($no_card_dat,0,16) == "4359650180003403" || substr($no_card_dat,0,16) == "4359650100023200"
							)
							{
								//KEB HANA BANK GOLD LAMA
								//$url_pdf = '';
								$url_pdf_jpg = 'Promo_Hana_Bank.jpg';
								
								$url_img = "../images/layout_hana_bank.jpg";
								$card_name="visa";
								$ket_produk="VISA";
								#$yimage = 0;//kz
								$y = 21.5;//kz
								
								$yimage = 0;
								#$y = 24;
								/*$y_detail = $y+102;*/
								
								$GLOBALS['geser'] = -10; //kz
								#$GLOBALS['geser'] = 0;
								$GLOBALS['flag_hana_bank']  = true;
								
								$y_keb_hana_tambahan1 = 5;
								$y_keb_hana_tambahan2 = 5;
								$y_keb_hana_tambahan3 = 7;
								$y_keb_hana_tambahan4 = 8;
								$y_keb_hana_tambahan5 = 4;
								
								###$customer_temp++;
							}else{
								//BRI BTN
								$url_pdf_jpg = '';
								
								$url_img = "../images/layout_bri_btn_r2.jpg";
								$card_name="btn";
								$ket_produk="BTN";
								#$yimage = 3.5;
								#$y = 24;
								$yimage = 0;
								$y = 24;
								//die('traveloka');
								
								#$GLOBALS['geser'] = 1.4; //traveloka old
								$GLOBALS['geser'] = 0;
								
								$y_traveloka_tambahan = 2;
								
								$GLOBALS['flag_bri_btn']  = true;
							
							}
						}
						else //VISA
						{
							if ( substr($no_card_dat,0,8) == "43650201" || substr($no_card_dat,0,8) == "43650202" ) //BIN  //BRI TOUCH
							{
								//$url_pdf = 'Promo_BRI_Touch.pdf';
								$url_pdf_jpg = 'Promo_BRI_Touch.jpg';
								if ( substr($no_card_dat,0,8) == "43650201" ) //BIN  //BRI TOUCH
								{
									#$url_pdf_jpg = 'Promo_BRI_Touch_Pekerja.jpg';
								}
							}else{
								//$url_pdf = '';
								$url_pdf_jpg = '';
							}
							
							#$url_img = "../images/layout_visa_20130328.jpg";
							$url_img = "../images/Layout_C1_VISA.jpg";
							$card_name="visa";
							$ket_produk="VISA";
							#$yimage = 3.5; //kz
							#$y = 24; //kz
							
							$yimage = 0;
							$y = 24;
							
							#$GLOBALS['geser'] = 1.4;//kz
							$GLOBALS['geser'] = 0;
						}
					}
					elseif (substr($no_card_dat,0,1) == "5") //MASTER
					{
						if ( substr($no_card_dat,0,6) == "518828" || substr($no_card_dat,0,6) == "518856" )  // gold dan silver
						{
							if (  substr($no_card_dat,0,8) == "51885633" ) //BNP GOLD - 20180109 kz //AGRO
							{
								$url_pdf_jpg = 'Promo_BNP_Gold.jpg';
								#$url_pdf_jpg = '';
							}
							elseif ( substr($no_card_dat,0,8) == "51885603" || substr($no_card_dat,0,8) == "51885613")  //World Access
							{
								$url_pdf_jpg = 'Promo_World_Access.jpg';
								if (  substr($no_card_dat,0,8) == "51885613" ) 
								{
									#$url_pdf_jpg = 'Promo_World_Access_Pekerja.jpg';
								}
							}
							elseif ( substr($no_card_dat,0,8) == "51882852" ) //Wonderful Indonesia
							{
								$url_pdf_jpg = 'Promo_Wonderful_Indonesia.jpg';
							}
							elseif ( substr($no_card_dat,0,8) == "51882892" ) //PROMOT3R - 20180109 kz
							{
								$url_pdf_jpg = 'Promo_Promoter.jpg';
							}else{
								$url_pdf_jpg = 'Promo_Easy_Card.jpg';
								 if ( substr($no_card_dat,0,8) == "51882801" || substr($no_card_dat,0,8) == "51882821" || substr($no_card_dat,0,8) == "51882841" 
								|| substr($no_card_dat,0,8) == "51885601" || substr($no_card_dat,0,8) == "51885621" )
								{
									#$url_pdf_jpg = 'Promo_Easy_Card_Pekerja.jpg';
								}else if ( substr($no_card_dat,0,8) == "51882802" || substr($no_card_dat,0,8) == "51882822" || substr($no_card_dat,0,8) == "51882842" 
								|| substr($no_card_dat,0,8) == "51885602" || substr($no_card_dat,0,8) == "51885622" || substr($no_card_dat,0,8) == "51885652" )
								{
									
									$url_pdf_jpg = 'Promo_Easy_Card.jpg';
								}else{
									$url_pdf_jpg = 'Promo_Easy_Card.jpg';
								}
								
							}
							
							
							#$GLOBALS['geser'] = -1.4;//kz
							$GLOBALS['geser'] = 0;
							
						}elseif ( substr($no_card_dat,0,8) == "55200233" ) {  //BNP Platinum
							$url_pdf_jpg = 'Promo_BNP_Platinum.jpg';
							#$GLOBALS['geser'] = -1.4;
							$GLOBALS['geser'] = 0;
							
						}else if ( substr($no_card_dat,0,8) == "54758201" || substr($no_card_dat,0,8) == "54758202" || substr($no_card_dat,0,8) == "54758203" ) { //Business Card
							$url_pdf_jpg = 'Promo_Business_Card.jpg';
							#$GLOBALS['geser'] = -1.4;//kz
							$GLOBALS['geser'] = 0;
							
						}else if ( substr($no_card_dat,0,8) == "55200201" || substr($no_card_dat,0,8) == "55200202" ) { //Platinum
							$url_pdf_jpg = 'Promo_Platinum.jpg';
							#$GLOBALS['geser'] = -1.4;//kz
							$GLOBALS['geser'] = 0;
							if ( substr($no_card_dat,0,8) == "55200201" )
							{
								#$url_pdf_jpg = 'Promo_Platinum_Pekerja.jpg';
							}
							
						}else if ( substr($no_card_dat,0,8) == "55347901" || substr($no_card_dat,0,8) == "55347902" || substr($no_card_dat,0,8) == "55347903" ) { //Platinum
							$url_pdf_jpg = 'Promo_Corporate_Card.jpg';
							#$GLOBALS['geser'] = -1.4;//kz
							$GLOBALS['geser'] = 0;
						}else{
							//$url_pdf = '';
							$url_pdf_jpg = '';
						}
						
						#$url_img = "../images/layout_master_20130326.jpg";//kz
						
						if (  substr($no_card_dat,0,8) == "51885633" ) //BNP GOLD - 20180109 kz //AGRO
						{
							$url_img = "../images/Layout_C1_MasterCard.jpg";
						}else{
							$url_img = "../images/Layout_C1_MasterCard.jpg";
						}
							
						
						$card_name="master";
						$ket_produk="MASTER";
						#$yimage = 3.5; //kz
						#$y = 24; kz
						
						$yimage = 0;
						$y = 24;
						
					}
					elseif (substr($no_card_dat,0,1) == "3") //JCB
					{
						
							if ( substr($no_card_dat,0,8) == "35651011" || substr($no_card_dat,0,8) == "35651012" ) //JCB 
							{
								//$url_pdf = 'Promo_JCB.pdf';
								$url_pdf_jpg = 'Promo_JCB.jpg';
								if ( substr($no_card_dat,0,8) == "35651011" )
								{
									#$url_pdf_jpg = 'Promo_JCB_Platinum.jpg'; //pekerja
								}
							}else{
								//$url_pdf = '';
								$url_pdf_jpg = '';
							}
							
							
						//penambahan 20170321
						//$url_img = "../images/layout_3.jpg";
						#$url_img = "../images/layout_4_jcb_rev1.jpg";
						$url_img = "../images/Layout_C1_JCB.jpg";
						$card_name="jcb";
						$ket_produk="JCB";
						#$yimage = 3.5;
						$yimage = 0;
						$y = 24;
						
						
						$GLOBALS['geser'] = 0;
					}
					
					
					$url_pdf_jpg = '';
					//$url_pdf_jpg = 'Promo_dummy.jpg';
					$url_pdf_jpg = 'Promo_Generic.jpg';
				}
				
				if($flag_cust=='Y'){
					
					
					if(trim($email) == ""){$jml_customer = 0;}else{$jml_customer = 1;}
					
					if($jml_customer==0){
						$flag_cust = 'N';
						$flag_case = 'N';
						if($regtype == '02'){
							$msg_not_exists[] = $no_card_dat;
						}
						
						#$arr_summary
					}else{
						switch($regtype){
							case '02':		
							
									
					
									$hlm = 1;
									
									
									$jml_hlm = substr($row_dat,(884+$var_cover),1);
									$record = 0;
									#####$customer++;
									$total_hlm = $total_hlm + $jml_hlm;
									
									
									
								if ( $key_CIF != $key_CIF_x || $key_CIF == '' ){
									
									$customer++;
									#$pdf = new FPDI_Protection('P','mm',array(216,304.79999999));
									$pdf = new FPDI_Protection('P','mm',array(216,302));
									//$pdf = new FPDI_Protection('P','mm',array(216,290));
									$pdf->SetAutoPageBreak(true, 7);
									$pdf->SetDisplayMode("real");
									$pdf->SetLeftMargin(1);
									$pdf->SetRightMargin(1);
									$pdf->SetTopMargin(12);
									$pdf->AddFont('BRICARD02','','BRICARD02.php');
									$pdf->AddFont('C39P24DhTt','','C39P24DhTt.php');
									$pdf->AddFont('Helvetica-Condensed-Light-Li','','Helvetica-Condensed-Light-Li.php');
									
								
									
									$pdf->SetProtection(array("print"), $password_pdf, "appdev123456");
									
								}
									
$org_type = NULL;
$nama = NULL;
$address1 = NULL;
$address2 = NULL;
$address3 = NULL;
$city = NULL;
$zipcode = NULL;
$statement_date = NULL;
$statement_date = NULL;
$payment_due_date = NULL;
$payment_due_date = NULL;
$min_payment = NULL;
$credit_limit = NULL;
$avail_credit_limit = NULL;
$cash_advan_limit = NULL;
$prev_balance = NULL;
$prev_balance_sign = NULL;
$prev_balance = NULL;
$payment_credit = NULL;
$interest_service_charge = NULL;
$new_balance = NULL;
$new_balance_sign = NULL;
$new_balance = NULL;
$purchase_debit = NULL;
$cash_advance = NULL;
$retail_interest = NULL;
$max_retail_interest = NULL;
$retail_interest = NULL;
$max_cash_advan_interest = NULL;
$cash_advan_interest = NULL;
$prev_bonus = NULL;
$earned_bonus = NULL;
$point_bonus = NULL;
$redeemed_bonus = NULL;
$adjusment_bonus = NULL;
$point_avail = NULL;
$barcode = NULL;
$posko = NULL;
$kurir = NULL;
$nourut = NULL;
$kolektibilitas = NULL;




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
									$min_payment = number_format((int)substr($row_dat,263,14),0,'','.');//perubahan pemisah desimal titik
									$credit_limit = number_format((int)substr($row_dat,278,14),0,'','.');//perubahan pemisah desimal titik
									$avail_credit_limit = number_format((int)substr($row_dat,293,14),0,'','.');//perubahan pemisah desimal titik
									$cash_advan_limit = number_format((int)substr($row_dat,308,14),0,'','.');//perubahan pemisah desimal titik
									$prev_balance = number_format((int)substr($row_dat,323,14),0,'','.');//perubahan pemisah desimal titik
										$prev_balance_sign = substr($row_dat,337,1);
										#$prev_balance = $prev_balance . $prev_balance_sign;
										$prev_balance_sign = ($prev_balance_sign == '-' ? '-' : '');
										$prev_balance = $prev_balance_sign.$prev_balance;
									$payment_credit = number_format((int)substr($row_dat,353,14),0,'','.');//perubahan pemisah desimal titik
									$interest_service_charge = number_format((int)substr($row_dat,398,14),0,'','.');//perubahan pemisah desimal titik
									$new_balance = number_format((int)substr($row_dat,413,14),0,'','.');//perubahan pemisah desimal titik
										$new_balance_sign = substr($row_dat,427,1);
										#$new_balance = $new_balance . $new_balance_sign;
										$new_balance_sign = ($new_balance_sign == '-' ? '-' : '');
										$new_balance = $new_balance_sign.$new_balance ;
									$purchase_debit = number_format((int)substr($row_dat,428,14),0,'','.');//perubahan pemisah desimal titik
									$cash_advance = number_format((int)substr($row_dat,443,14),0,'','.');//perubahan pemisah desimal titik
									
									
											//20200506 KZ
											#$retail_interest = (int)substr($row_dat,458,5);
											$retail_interest = (int)substr($row_dat,458,5);
											$max_retail_interest = $retail_interest*12;
											$max_retail_interest = str_replace(substr($max_retail_interest,-2),'',$max_retail_interest).'.'.substr($max_retail_interest,-2).'%';
											$retail_interest = str_replace(substr($retail_interest,-2),'',$retail_interest).'.'.substr($retail_interest,-2).'%';
											
											//$max_retail_interest = '35.40%'; //REVISI SESUAI EMAIL 20170531 start juni 2017
											//$retail_interest = '2.95%'; //REVISI SESUAI EMAIL 20170531
											
											//$max_retail_interest = '26.95%'; //REVISI SESUAI EMAIL 20170531 start juni 2017
											//$retail_interest = '2.25%'; //REVISI SESUAI EMAIL 20170531
											
											#$max_retail_interest = '24.00%'; //REVISI SESUAI EMAIL 20200506 6 Mei 2020
											#$retail_interest = '2.00%'; //REVISI SESUAI EMAIL 20200506 6 Mei 2020
											//20170531 KZ
											
											
										
									
											
											//20200506 KZ
											#$cash_advan_interest = (int)substr($row_dat,464,5);
											$cash_advan_interest = (int)substr($row_dat,464,5);
											$max_cash_advan_interest = $cash_advan_interest*12;
											$max_cash_advan_interest = str_replace(substr($max_cash_advan_interest,-2),'',$max_cash_advan_interest).'.'.substr($max_cash_advan_interest,-2).'%';
											$cash_advan_interest = str_replace(substr($cash_advan_interest,-2),'',$cash_advan_interest).'.'.substr($cash_advan_interest,-2).'%';
											
											//$max_cash_advan_interest = '35.40%'; //REVISI SESUAI EMAIL 20170531 start juni 2017
											//$cash_advan_interest = '2.95%'; //REVISI SESUAI EMAIL 20170531
											
											//$max_cash_advan_interest = '26.95%'; //REVISI SESUAI EMAIL 20170531 start juni 2017
											//$cash_advan_interest = '2.25%'; //REVISI SESUAI EMAIL 20170531
											
											//$max_cash_advan_interest = '24.00%'; //REVISI SESUAI EMAIL 20200506 6 Mei 2020
											//$cash_advan_interest = '2.00%'; //REVISI SESUAI EMAIL 20200506 6 Mei 2020
											//20170531 KZ
											
										
										
									$prev_bonus = (int)substr($row_dat,(524+$var_cover),6);
									$earned_bonus = (int)substr($row_dat,(534+$var_cover),6);
									$point_bonus = (int)substr($row_dat,(544+$var_cover),6);
									$redeemed_bonus = (int)substr($row_dat,(554+$var_cover),6);
									$adjusment_bonus = (int)substr($row_dat,(564+$var_cover),6);
									$point_avail = (int)substr($row_dat,(574+$var_cover),6);
									
									
									/* ------------------------------------------ REVISI POINT DENGA PLUS MINUS 20180329 ------------------------------------------ */
									
									$plus_min_prev_bonus 			= substr($row_dat,(520+$var_cover),1);
									$plus_min_earned_bonus			= substr($row_dat,(530+$var_cover),1);
									$plus_min_point_bonus			= substr($row_dat,(540+$var_cover),1);
									$plus_min_redeemed_bonus		= substr($row_dat,(550+$var_cover),1);
									$plus_min_adjusment_bonus		= substr($row_dat,(560+$var_cover),1);
									$plus_min_point_avail			= substr($row_dat,(570+$var_cover),1);
									
									$prev_bonus 			= ($plus_min_prev_bonus == '-' ? $plus_min_prev_bonus.$prev_bonus : $prev_bonus); 
									$earned_bonus 			= ($plus_min_earned_bonus == '-' ? $plus_min_earned_bonus.$earned_bonus : $earned_bonus); 
									$point_bonus 			= ($plus_min_point_bonus == '-' ? $plus_min_point_bonus.$point_bonus : $point_bonus); 
									$redeemed_bonus 		= ($plus_min_redeemed_bonus == '-' ? $plus_min_redeemed_bonus.$redeemed_bonus : $redeemed_bonus); 
									$adjusment_bonus		= ($plus_min_adjusment_bonus == '-' ? $plus_min_adjusment_bonus.$adjusment_bonus : $adjusment_bonus); 
									$point_avail 			= ($plus_min_point_avail == '-' ? $plus_min_point_avail.$point_avail : $point_avail); 
									
									/* ------------------------------------------ REVISI POINT DENGA PLUS MINUS 20180329 ------------------------------------------ */
									
									$barcode = substr($row_dat,(917+$var_cover),10);
									$posko = substr($row_dat,(885+$var_cover),5);
									$kurir = substr($row_dat,(891+$var_cover),5);
									$nourut = substr($row_dat,(928+$var_cover),5);
									$kolektibilitas = substr($row_dat,(630+$var_cover),1);
									
									
									//if ( $regtype == '02' ) add_page1($pdf); //tambahan gambar 1 kz mulai April 2017
									if ( $regtype == '02' )
									{
										
										#$ket1 = add_page1_jpg($pdf, $url_pdf_jpg); //tambahan gambar 1 kz mulai April 2017 dibuka jika promonya masing2 tipe kartu tetap ada
										
										if ( $flag_summary )
										{
											#$url_pdf_jpg = '';
											$ket1 = add_page1_jpg($pdf, $url_pdf_jpg); //tambahan gambar 1 kz mulai April 2017
											$halaman_cetak_kertas = add_page2_sumamry_jpg($pdf, $url_pdf_jpg_summary, $arr_summary, $jml_hlm , $barcode, $posko, $kuri, $nourut, $kolektibilitas, $file); 
											
											$arr_summary = array();
											$flag_summary = false;
										}else{
											$url_pdf_jpg_summary = '';
										}
										
										
									}
									
									$halaman_cetak_kertas = add_page($pdf,
												$hlm,$jml_hlm,
												$url_img,$ximage,$yimage,
												$x,$y,$row_message,
												$no_card,$statement_date,$payment_due_date,$credit_limit,$cash_advan_limit,
												$purchase_debit,$cash_advance,$new_balance,$min_payment,$avail_credit_limit,
												$nama,$address1,$address2,$address3,$city,$zipcode,
												$prev_bonus,$earned_bonus,$point_bonus,$redeemed_bonus,$adjusment_bonus,$point_avail,
												$max_retail_interest,$retail_interest,$max_cash_advan_interest,$cash_advan_interest,
												$prev_balance,$payment_credit,
												$barcode,$posko,$kurir,$nourut,$file,$kolektibilitas,$forex_curr,$forex_nominal,$kurs_nominal
												,$url_pdf_jpg
												,$halaman_cetak_kertas
												,$y_keb_hana_tambahan1,$y_keb_hana_tambahan2,$y_keb_hana_tambahan3,$y_keb_hana_tambahan4,$y_keb_hana_tambahan5);
									
									
									
		
		
									$pdf->SetXY($x+68-22,$y_detail+$y_traveloka_tambahan+$y_keb_hana_tambahan1);
									$pdf->SetFont('BRICARD02','',7.12);
									$pdf->Cell(67,4,'Tagihan Bulan Lalu',0,0,'L');
									
									$pdf->Cell(18-9+22,4,$prev_balance,0,1,'R');
								break;
							
							case '03':
									$record++;
									
$embossing_name = NULL;
$transaction_date = NULL;
$transaction_date = NULL;
$posting_date = NULL;
$posting_date = NULL;
$description = NULL;
$forex_curr = NULL;
$forex_nominal = NULL;
$forex_nominal = NULL;
$kurs_nominal = NULL;

$kurs_nominal = NULL;
$amount = NULL;
$dr_cr_indicator = NULL;

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
									$amount = number_format((int)substr($row_dat,147,14),0,'','.');//perubahan pemisah desimal titik
									$dr_cr_indicator = substr($row_dat,162,2);
									
									if($record!=1 && $record==$hlm*$max_record+1){
										
										$hlm++;
										#echo "halaman $hlm , halaman kertas $halaman_cetak_kertas <br>";
										
										$pdf->SetX($x+90);
										#$pdf->Cell(45,4,'2 bersambung ke halaman '.$hlm.'...',0,1,'L');
										$pdf->Cell(45,4,'halaman '.$halaman_cetak_kertas.' bersambung ke halaman '.($halaman_cetak_kertas+1).'...',0,1,'L');
										
										$halaman_cetak_kertas = add_page($pdf,
													$hlm,$jml_hlm,
													$url_img,$ximage,$yimage,
													$x,$y,$row_message,
													$no_card,$statement_date,$payment_due_date,$credit_limit,$cash_advan_limit,
													$purchase_debit,$cash_advance,$new_balance,$min_payment,$avail_credit_limit,
													$nama,$address1,$address2,$address3,$city,$zipcode,
													$prev_bonus,$earned_bonus,$point_bonus,$redeemed_bonus,$adjusment_bonus,$point_avail,
													$max_retail_interest,$retail_interest,$max_cash_advan_interest,$cash_advan_interest,
													$prev_balance,$payment_credit,
													$barcode,$posko,$kurir,$nourut,$file,$kolektibilitas,$forex_curr,$forex_nominal,$kurs_nominal
													,$url_pdf_jpg
													,$halaman_cetak_kertas
													,$y_keb_hana_tambahan1,$y_keb_hana_tambahan2,$y_keb_hana_tambahan3,$y_keb_hana_tambahan4,$y_keb_hana_tambahan5);
									}
									
									if($record==1){
										$pdf->SetXY($x+68,$y_detail+4);
									}elseif($record==($hlm-1)*$max_record+1){
										$pdf->SetXY($x+68,$y_detail);
									}
									
									$no_card_det = substr($row_dat,27,16);
										$no_card_det = substr($no_card_det,0,4).'-'.substr($no_card_det,4,4).'-'.substr($no_card_det,8,4).'-'.substr($no_card_det,12,4);
										
										$no_card_det_masking2 = str_replace('-','',$no_card_det);
										$no_card_det_masking2 = substr($no_card_det_masking2,0,4).'-'.substr($no_card_det_masking2,4,2).'XX-XXXX'.'-'.substr($no_card_det_masking2,12,4);
										
										#$no_card_det_masking = substr($no_card_dat,0,4).'-'.substr($no_card_dat,4,2).'XX-XXXX'.'-'.substr($no_card_dat,12,4);
									
									#####if($tmp_no_card_det != $no_card_det && !empty($tmp_no_card_det) ){
									if( $tmp_no_card_det != $no_card_det ){
										
										
										#if( ($record >= ($hlm*$max_record)-1 ) && !empty($hlm) ){
										if( ($record >= ($hlm*$max_record)-1 )  ){
											
											$hlm++;
											#echo "halaman $hlm , halaman kertas $halaman_cetak_kertas <br>";
											
											$pdf->SetX($x+90);
											#$pdf->Cell(45,4,'3 bersambung ke halaman '.$hlm.'...',0,1,'L');
											$pdf->Cell(45,4,'halaman '.$halaman_cetak_kertas.' bersambung ke halaman '.($halaman_cetak_kertas+1).'...',0,1,'L');
											
											$halaman_cetak_kertas = add_page($pdf,
														$hlm,$jml_hlm,
														$url_img,$ximage,$yimage,
														$x,$y,$row_message,
														$no_card,$statement_date,$payment_due_date,$credit_limit,$cash_advan_limit,
														$purchase_debit,$cash_advance,$new_balance,$min_payment,$avail_credit_limit,
														$nama,$address1,$address2,$address3,$city,$zipcode,
														$prev_bonus,$earned_bonus,$point_bonus,$redeemed_bonus,$adjusment_bonus,$point_avail,
														$max_retail_interest,$retail_interest,$max_cash_advan_interest,$cash_advan_interest,
														$prev_balance,$payment_credit,
														$barcode,$posko,$kurir,$nourut,$file,$kolektibilitas,$forex_curr,$forex_nominal,$kurs_nominal
														,$url_pdf_jpg
														,$halaman_cetak_kertas
														,$y_keb_hana_tambahan1,$y_keb_hana_tambahan2,$y_keb_hana_tambahan3,$y_keb_hana_tambahan4,$y_keb_hana_tambahan5);
											$pdf->SetXY($x+68,$y_detail-4);
										}
										$pdf->Ln();
										$record++;
										
										$pdf->SetFont('BRICARD02','',7.12);
										
										$pdf->SetX($x+4);
										#$pdf->Cell(35,4,$no_card_det.'-masking',0,0,'L');
										$pdf->Cell(35,4,$no_card_det_masking2,0,0,'L');
										
										$pdf->Cell(29,4,$embossing_name,0,1,'L');
										$record++;
										
										$tmp_no_card_det = $no_card_det;
									}
									
									$pdf->SetFont('BRICARD02','',7);
									
									$pdf->SetX($x+4);
									$pdf->Cell(22,4,$transaction_date,0,0,'L');
									$pdf->Cell(20,4,$posting_date,0,0,'L');
									$pdf->Cell(58-11,4,$description,0,0,'L');
									$pdf->Cell(6-2,4,$forex_curr,0,0,'L');
									$pdf->Cell(13,4,$forex_nominal,0,0,'R');
									$pdf->Cell(16,4,$kurs_nominal,0,0,'R');
									$pdf->Cell(17,4,$amount,0,0,'R');
									
									if($dr_cr_indicator=='CR'){
										$pdf->SetX($pdf->GetX()-2);
										$pdf->Cell(2,4,$dr_cr_indicator,0,1,'L');
									}else{
										$pdf->Cell(2,4,'',0,1,'L'); //kz
										
									}
								break;
						}
					}
				}
			}
			
}
		}
		if($flag_case == 'Y' && feof($open_dat) )
		###if($flag_case == 'Y' && feof($open_dat) && ( $GLOBALS['flag_hana_bank']  === true || $GLOBALS['flag_hana_bank_2']  === true ) )
		{
			
			
			
			#if( ($record >= ($hlm*$max_record)-5)  && !empty($hlm) ){
			if( ($record >= ($hlm*$max_record)-5)   ){
				
				$hlm++;
				#echo "halaman $hlm , halaman kertas $halaman_cetak_kertas <br>";
				
				$pdf->SetX($x+90);
				#$pdf->Cell(45,4,'4 bersambung ke halaman '.$hlm.'...',0,1,'L');
				$pdf->Cell(45,4,'halaman '.$halaman_cetak_kertas.' bersambung ke halaman '.($halaman_cetak_kertas+1).'...',0,1,'L');
				$halaman_cetak_kertas = add_page($pdf,
							$hlm,$jml_hlm,
							$url_img,$ximage,$yimage,
							$x,$y,$row_message,
							$no_card,$statement_date,$payment_due_date,$credit_limit,$cash_advan_limit,
							$purchase_debit,$cash_advance,$new_balance,$min_payment,$avail_credit_limit,
							$nama,$address1,$address2,$address3,$city,$zipcode,
							$prev_bonus,$earned_bonus,$point_bonus,$redeemed_bonus,$adjusment_bonus,$point_avail,
							$max_retail_interest,$retail_interest,$max_cash_advan_interest,$cash_advan_interest,
							$prev_balance,$payment_credit,
							$barcode,$posko,$kurir,$nourut,$file,$kolektibilitas,$forex_curr,$forex_nominal,$kurs_nominal
							,$url_pdf_jpg
							,$halaman_cetak_kertas
							,$y_keb_hana_tambahan1,$y_keb_hana_tambahan2,$y_keb_hana_tambahan3,$y_keb_hana_tambahan4,$y_keb_hana_tambahan5);
				$pdf->SetXY($x+68,$y_detail-4);
				$pdf->SetFont('BRICARD02','',7.12);
			}
			#$pdf_name = $card_name.'_'.substr($no_card_dat,-4).'_'.$customer;
			$pdf_name = str_pad($customer, 6, "0", STR_PAD_LEFT).'_Billing_Statement'.'_'.substr($nama_pdf_2_cif,-4);
			#die("aa");
			$halaman_cetak_kertas = end_detail($pdf,$x,$y,$new_balance,$interest_service_charge,$dr_cr_indicator,$statement_date ,$halaman_cetak_kertas);
			
			$hlm_tabel = $halaman_cetak_kertas;
			output_pdf($pdf,$pdf_name,$flagtrans,$blth,$file);
			insert_mcustomer($no_card_dat, $email, $password_pdf,
								$blth, $flagtrans, $log_customer_id);
								
			///////////////////////////////////////////////////////////////////////////////////
				
					///////////////////////////////////////////////////////////////////////////////////
			#echo "$hlm --- $halaman_cetak_kertas x2/"."<br>";
			$total_hlm_x += $hlm_tabel;
			#insert_detail($m_loading_id,$nomor_customer,$no_card_dat,
			insert_detail($m_loading_id,$nomor_customer,$nama_pdf_2_cif,
							$nama,$address1,$address2,
							$address3,$city,$zipcode,
							$flagtrans,$blth,$file,
							$pdf_name,$password_pdf,$hlm_tabel, $email, $tabeldetail, $ket_produk);
			$flag_case = 'N';
			$flag_cust = 'Y';
			
		}
		
		###$customer = $customer_temp;
		update_log_customer($log_customer_id,$customer);
		#update_m_loading($m_loading_id,$total_hlm,$customer);
		update_m_loading($m_loading_id,$total_hlm_x,$customer);
		create_csv($m_loading_id,$flagtrans,$blth,$file,$tabeldetail);
		$not_exists = implode(', ',$msg_not_exists);
		
		
		export_excel_hana_bank($blth.'_'.$file);
		
		
		$base_file = str_replace(substr($file,-4),'',$file);
		$php_path_file = '../pdf/'.$flagtrans.'/'.$blth.'/'.$base_file.'/';
		$file_name_exist = "no_email_".$base_file.".TXT";
		if(trim($not_exists) != ''){
			$create_file_exist = create_file_not_exist($php_path_file, $file_name_exist, str_replace(', ', chr(13), $not_exists));
			if($create_file_exist) {
				$not_exists = "<a href='".$php_path_file.$file_name_exist."' target='_blank'>".$file_name_exist."</a>";
			}
		}
		
		//ubah_email_customer_online($tabeldetail, $m_loading_id); //DIMATIKAN UNTUK PENGECEKAN CUSTOMER ONLINE
		
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
						$barcode,$posko,$kurir,$nourut,$file,$kolektibilitas,$forex_curr,$forex_nominal,$kurs_nominal
						,$url_pdf_jpg, $halaman_cetak_kertas
						,$y_keb_hana_tambahan1,$y_keb_hana_tambahan2,$y_keb_hana_tambahan3,$y_keb_hana_tambahan4,$y_keb_hana_tambahan5){
		$pdf->AddPage();
		$halaman_cetak_kertas++;
		if ( !empty($url_img) )
		{
			$pdf->Image($url_img,$ximage,$yimage,217,302);
		}
		
		//20170815 PERNAMBAHAN FLAG
		if ( empty($url_pdf_jpg) )
		{
			$var_hal = 1;
		}else{
			$var_hal = 2;
		}
		//20170815 PERNAMBAHAN FLAG
		
		//teks utk MASTER
		/*
		//kasino kz
		if (substr($no_card,0,1) == "5")
		{
			$x_teks_master = 175;
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
		*/
		//selesai utk teks MASTER
			
		$pdf->SetFont('Arial','B',6);
		$y_kotak_atas = 23.5;
		
		
		$no_card_masking = str_replace('-','',$no_card);
		$no_card_masking = substr($no_card_masking,0,4).'-'.substr($no_card_masking,4,2).'XX-XXXX'.'-'.substr($no_card_masking,12,4);
		
		$pdf->SetXY($x+2,$y+$y_kotak_atas);
		#$pdf->Cell(31,5,$no_card,0,0,'C'); //20180724
		$pdf->Cell(31 + $GLOBALS['geser'],5,$no_card_masking,0,0,'C');
				
		$pdf->SetXY($x+34-2,$y+$y_kotak_atas);//kz
		$pdf->Cell(24,5,$statement_date,0,0,'C');
		$GLOBALS['billing_cycle'] = $statement_date;
		
		
		
		$pdf->SetXY($x+60-7,$y+$y_kotak_atas);
		$pdf->Cell(40,5,$payment_due_date,0,0,'C');
		$GLOBALS['due_date'] = $payment_due_date;
		
		
		
		$pdf->SetXY($x+101-10,$y+$y_kotak_atas);
		$pdf->Cell(22,5,$credit_limit,0,0,'C');
		
		$pdf->SetXY($x+126-12,$y+$y_kotak_atas);
		$pdf->Cell(34,5,$cash_advan_limit,0,0,'C');
		
		$y_2 = $pdf->GetY()+17.2 -3;
		$pdf->SetXY($x+3,$y_2);
		$pdf->Cell(30,5,$purchase_debit,0,0,'C');
		
		$pdf->SetXY($x+34-4,$y_2);
		$pdf->Cell(28,5,$cash_advance,0,0,'C');
		
		$pdf->SetXY($x+66-8,$y_2);
		$pdf->Cell(22,5,$new_balance,0,0,'C');
		
		$pdf->SetXY($x+94-10,$y_2);
		$pdf->Cell(33,5,$min_payment,0,0,'C');
		
		$pdf->SetXY($x+131-12,$y_2);
		$pdf->Cell(28,5,$avail_credit_limit,0,0,'C');
		
		
		
		$x_barcode = $x+1+4;
		$y_barcode = $y+76-9+2;
		$pdf->SetXY($x_barcode,$y_barcode-5.5);
		#$pdf->SetFont('C39P24DhTt','',45);
		$pdf->SetFont('C39P24DhTt','',45);
		$pdf->Cell(70,4,$barcode,0,1,'L');
		
		
		$pdf->SetFillColor(255,255,255);
		$pdf->Rect($x_barcode,$y_barcode-17-3-2,53,5.5+11+2,'F');
		
		##$pdf->SetFillColor(255,25,255);
		##$pdf->Rect($x_barcode,$y_barcode-7,53,5.5,'F');
		#$pdf->SetFillColor(255,255,255);
		#$pdf->Rect($x_barcode,$y_barcode+5,53,5.5,'F');
		
		$pdf->SetXY($x_barcode+52,$y_barcode-1);
		#$pdf->SetFont('Arial','',8);
		$pdf->SetFont('Arial','',5);
		$pdf->Cell(10,3,$posko,0,1,'L');
		
		$pdf->SetX($x_barcode+52);
		$pdf->Cell(10,3,$kurir,0,1,'L');
		
		$pdf->SetXY($x_barcode,$y_barcode+5);
		$pdf->Cell(70,3,$barcode." - ".$nourut." / ".$file,0,0,'L');
		
		
		
		$x_nama = $x+1+4;
		$y_nama = $y+49-5;
		$pdf->SetFont('BRICARD02','',7.12);
		
		$pdf->SetXY($x_nama,$y_nama);
		$pdf->Cell(70,3.5,'Yth. Bapak/Ibu',0,1,'L');
		
		$pdf->SetX($x_nama);
		$pdf->Cell(70,3.5,$nama,0,1,'L');
		
		//if($hlm==1){ //KEY POINT DINYALAKAN JIKA ADA BROSUR HLM = 2 , JIKAT TIDAK ADA HLM 1
		//if($hlm==$var_hal){ //KEY POINT DINYALAKAN JIKA ADA BROSUR HLM = 2 , JIKAT TIDAK ADA HLM 1
		if($hlm > 0){ //KEY POINT DINYALAKAN JIKA ADA BROSUR HLM = 2 , JIKAT TIDAK ADA HLM 1  (memunculkan di semua halaman)
			$pdf->SetX($x_nama);
			$pdf->Cell(70,3.5,$address1,0,1,'L');
			
			$pdf->SetX($x_nama);
			$pdf->Cell(70,3.5,$address2,0,1,'L');
			
			$pdf->SetX($x_nama);
			$pdf->Cell(70,3.5,$address3,0,1,'L');
			
			$pdf->SetX($x_nama);
			$pdf->Cell(70,3.5,$city.' '.$zipcode,0,1,'L');
		}
		
		$text1 = NULL;
		$text2 = NULL;
		$text3 = NULL;
		$text4 = NULL;
		
		$x_attention = $x+84-6;
		$y_attention = $y+50-4;
		//$pdf->SetXY($x_attention,$y_attention); //20170811 default
		$pdf->SetXY($x_attention - 3,$y_attention); //20170811
		
		$pdf->SetFont('Arial','',7.5);
		$kodekartu = substr(str_replace("-","",str_replace(" ","",$no_card)),0,8);
		
		/*
		$text1_cover_summary =   "Yth Nasabah Kartu Kredit BRI, sesuai dengan UU RI No. 10 tahun 2020 tanggal 26 Oktober 2020 tentang bea materai, maka per tanggal 1 Januari 2021 Biaya Materai menjadi sebesar Rp.10.000,- (sepuluh ribu rupiah) untuk total Transaksi Kartu Kredit BRI di atas Rp.5.000.000,- (lima juta rupiah). "."\n"."Info Contact BRI 14017/1500017" ; //20201223
		*/
		
		$text1_cover_summary = "Yth Nasabah Kartu Kredit BRI, untuk kelancaran anda dalam bertransaksi pastikan alamat pengiriman Kartu Kredit BRI anda sudah benar dan lengkap. Apabila ada perubahan alamat mohon dapat dilakukan pengkinian data melalui : "."\n"."1. BRICC Mobile (dapat didownload melalui Appstore / Playstore)"."\n"."2. Info Contact BRI 14017 /1500017" ; //20210319
		
		/*
		$text1 =   "Yth Nasabah KK BRI, untuk kenyamanan Anda per 3 Oktober 2020 seluruh Lembar Tagihan KK BRI akan disampaikan dalam satu file/amplop yang sama. Tingkatkan transaksi dan nikmati berbagai penawaran istimewa Kartu Kredit BRI. Info Contact BRI 14017/1500017" ; //20201007
		
		$text2 =   "Yth Nasabah KK BRI, untuk kenyamanan Anda per 3 Oktober 2020 seluruh Lembar Tagihan KK BRI akan disampaikan dalam satu file/amplop yang sama. Tingkatkan transaksi dan nikmati berbagai penawaran istimewa Kartu Kredit BRI. Info Contact BRI 14017/1500017" ; //20201007
		
		$text3 =   "Yth Nasabah KK BRI, untuk kenyamanan Anda per 3 Oktober 2020 seluruh Lembar Tagihan KK BRI akan disampaikan dalam satu file/amplop yang sama. Tingkatkan transaksi dan nikmati berbagai penawaran istimewa Kartu Kredit BRI. Info Contact BRI 14017/1500017" ; //20201007
		
		$text4 =   "Yth Nasabah KK BRI, untuk kenyamanan Anda per 3 Oktober 2020 seluruh Lembar Tagihan KK BRI akan disampaikan dalam satu file/amplop yang sama. Tingkatkan transaksi dan nikmati berbagai penawaran istimewa Kartu Kredit BRI. Info Contact BRI 14017/1500017" ; //20201007
		
		
		$text_hana =   "Yth Nasabah KK BRI, untuk kenyamanan Anda per 3 Oktober 2020 seluruh Lembar Tagihan KK BRI akan disampaikan dalam satu file/amplop yang sama. Tingkatkan transaksi dan nikmati berbagai penawaran istimewa Kartu Kredit BRI. Info Contact BRI 14017/1500017" ; //20201007
		
		$text_agro =   "Yth Nasabah KK BRI, untuk kenyamanan Anda per 3 Oktober 2020 seluruh Lembar Tagihan KK BRI akan disampaikan dalam satu file/amplop yang sama. Tingkatkan transaksi dan nikmati berbagai penawaran istimewa Kartu Kredit BRI. Info Contact BRI 14017/1500017" ; //20201007
		
		
		$text_traveloka =   "Reward Point adalah jumlah BRI Poin Kartu Kredit. Nasabah akan memperoleh 5 BRI Poin tiap kelipatan transaksi ritel Rp 100.000,00. Akumulasi saldo BRI poin dalam 1 bulan siklus tagihan secara otomatis akan menjadi Traveloka point setiap tanggal 15 di bulan berikutnya. Penambahan saldo traveloka Point dapat dilihat pada Aplikasi Traveloka" ; //20191015
		*/
		
		$text1 = $text1_cover_summary;
		$text2 = $text1_cover_summary;
		$text3 = $text1_cover_summary;
		$text4 = $text1_cover_summary;
		$text_hana = $text1_cover_summary;
		$text_agro = $text1_cover_summary;
		$text_traveloka = $text1_cover_summary;
		
		$message_atas = NULL;
		$text_default = "Call BRI 14017";
		switch($kodekartu){
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
			
			case "43650202"	: $message_atas = $text3; break; //umum - Touch
			case "46874002"	: $message_atas = $text3; break; //umum - Infinite
			
			case "43650203"	: $message_atas = $text_traveloka; break; //umum - traveloka
	
			default				: $message_atas = $text1; break;
		}
		
		$kodekartu_2 = substr(str_replace("-","",str_replace(" ","",$no_card)),0,6);
		if ( substr($kodekartu_2,0,6) == "435972" || substr($kodekartu_2,0,8) == "43597201" || substr($kodekartu_2,0,8) == "43597202") //HANA BANK
		{
			//$message_atas ='';
			$message_atas =$text_hana; //20170531
		}
		else if (substr($kodekartu_2,0,6) == "435965" || substr($kodekartu_2,0,8) == "43596501" || substr($kodekartu_2,0,8) == "43596502" ) //HANA BANK
		{
			//$message_atas =''; 
			$message_atas =$text_hana; //20170531
		}
		else if (substr($kodekartu_2,0,1) == "3")
		{
			$message_atas =$text4;
		}
		//$pdf->MultiCell(75,4,$message_atas,0,'C');
		#$pdf->MultiCell(68,3.5,$message_atas,0,'C');
		$pdf->MultiCell(68,3.5,$message_atas,0,'J');//20201223
		
		
		
		$x_hlm = $x+133-10;
		$y_hlm = $y+82-7;
		
		
		$y_hlm = $y_hlm + $y_keb_hana_tambahan2;
		
		
		$pdf->SetXY($x_hlm,$y_hlm);
		$pdf->SetFont('Arial','',7.5);
		#####$pdf->Cell(20,5,'Halaman: '.$hlm.' / '.$jml_hlm);
		########$pdf->Cell(20,5,'Halaman: '.$halaman_cetak_kertas.' / '.$jml_hlm); //20200120 dimatikan karena error halaman KZ
		#$pdf->Cell(20,5,'Halaman: '.$pdf->getAliasNumpage().' / '.$pdf->getAliasNbPages()); //20200120 dimatikan karena error halaman KZ
		$pdf->Cell(20,5,'Halaman: '.$halaman_cetak_kertas.' / '.$jml_hlm); //20200120 dimatikan karena error halaman KZ
		
		
		
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
					$teks_kolektibilitas = "MACET";
		}
		
		
		
		/*JUMLAH BARIS MESSAGE HARUS DI-SET DI ATAS*/
		$y_message = $y+219-(3.5*$row_message)-4;
		$x_message = $x+4;
		/************DISEDIAKAN ($row_message) BARIS UNTUK MESSAGE************/
		
		
		
		$y_message = $y_message + $y_keb_hana_tambahan3;
		
		$pdf->SetFont('Arial','B',6);
		$pdf->SetXY($x_message ,$y_message -6 );
		$pdf->Cell(15,5,'Kolektibilitas : ' . $teks_kolektibilitas,0,1,'L');
		$pdf->SetFont('Arial','',7);
		$pdf->SetXY($x_message-2,$y_message + 3-4);
		#####$pdf->Cell(150,3.5,'"Nilai Tukar Transaksi Valas sudah memperhitungkan Komponen Biaya Penggunaan Kartu Kredit di Luar Negeri"',0,1,'C');
		#$pdf->Cell(150,3.5,'Yth. Nasabah Kartu Kredit BRI, informasi BRIPoin dan penukaran poin dapat diakses melalui website bripoin.bri.co.id',0,1,'C');
		
		
		$flag_poinx = 'true';
		if ( substr($kodekartu,0,8) == "43650203" ) //traveloka - 20160419 Bowo
		{
			$flag_poinx = 'false';
		}
		else if ( substr($kodekartu,0,8) == "51882852" ) //Wonderful Indonesia
		{
			$flag_poinx = 'false';
		}else{
			if ( substr($kodekartu,0,8) == "51882801" || substr($kodekartu,0,8) == "51882821" || substr($kodekartu,0,8) == "51882841" || substr($kodekartu,0,8) == "51885601" || substr($kodekartu,0,8) == "51885621" )
			{
				$flag_poinx = 'false';
			}
			else if ( substr($kodekartu,0,8) == "51882802" || substr($kodekartu,0,8) == "51882822" || substr($kodekartu,0,8) == "51882842" || substr($kodekartu,0,8) == "51885602" || substr($kodekartu,0,8) == "51885622" || substr($kodekartu,0,8) == "51885652" )
			{
				$flag_poinx = 'false';
			}
		
		}
		
		if ($flag_poinx == 'true')
		{
			#$paragraf1x = "Yth. Nasabah Kartu Kredit BRI, informasi BRIPoin dan penukaran poin dapat diakses melalui website bripoin.bri.co.id";
			$paragraf1x = "Yth. Nasabah Kartu Kredit BRI, informasi BRIPoin dan penukaran poin dapat diakses melalui website bripoin.bri.co.id";
			$pdf->Cell(150,3.5,$paragraf1x,0,1,'C');
			
		}else{
			$paragraf1x = "Nilai Tukar Transaksi Valas sudah memperhitungkan Komponen Biaya Penggunaan Kartu Kredit di Luar Negeri";
			$pdf->Cell(150,3.5,$paragraf1x,0,1,'C');
		}
		
		
		$pdf->Ln();
		
		
		
		//if($hlm==1){ //KEY POINT DINYALAKAN JIKA ADA BROSUR HLM = 2 , JIKAT TIDAK ADA HLM 1
		//if($hlm==$var_hal){ //KEY POINT DINYALAKAN JIKA ADA BROSUR HLM = 2 , JIKAT TIDAK ADA HLM 1
		if($hlm > 0){ //KEY POINT DINYALAKAN JIKA ADA BROSUR HLM = 2 , JIKAT TIDAK ADA HLM 1 (memunculkan di semua halaman)
			
			if ( $kodekartu == '43650203' )
			{
				////////////////////////////////////////////////// PENAMBAHAN TRAVELOKA
				/*
				$pdf->SetFont('Arial','B',14);
				$pdf->SetX($x+31-3);
				$pdf->Cell(21,5+8,$earned_bonus,0,0,'C');
			
				$pdf->SetX($x+109-10);
				$pdf->Cell(21,5+8,'+/- '.$adjusment_bonus,0,0,'C');
				*/
				////////////////////////////////////////////////// PENAMBAHAN TRAVELOKA
				
			}else if ( $GLOBALS['flag_bri_btn']  === true ){
				////////////////////////////////////////////////// PENAMBAHAN BRI BTN
				$pdf->SetFont('Arial','B',14);
				$pdf->SetX($x+31-3);
				#$pdf->Cell(21,5+8,'ZZZZ',0,0,'C');
				$pdf->Cell(21,5+8,'',0,0,'C');
			
				$pdf->SetX($x+109-10);
				#$pdf->Cell(21,5+8,'+/- '.'ZZZZ',0,0,'C');
				$pdf->Cell(21,5+8,'',0,0,'C');
				
				////////////////////////////////////////////////// PENAMBAHAN BRI BTN
		
	
			}else{
			
				
			$y = $y + $y_keb_hana_tambahan4;
			
			$y_point = $y+219;
			$pdf->SetFont('Arial','B',6);
			$pdf->SetXY($x+5,$y_point);
			$pdf->Cell(21,5,$prev_bonus,0,0,'C');
			
			$pdf->SetX($x+31-3);
			$pdf->Cell(21,5,$earned_bonus,0,0,'C');
			
			$pdf->SetX($x+57-5);
			$pdf->Cell(21,5,$point_bonus,0,0,'C');
			
			$pdf->SetX($x+83-7);
			$pdf->Cell(21,5,$redeemed_bonus,0,0,'C');
			
			$pdf->SetX($x+109-10);
			$pdf->Cell(21,5,$adjusment_bonus,0,0,'C');
			
			$pdf->SetX($x+135-10);
			$pdf->Cell(21,5,$point_avail,0,0,'C');
			
			
			}
		}
		
		
		
		$y = $y + $y_keb_hana_tambahan5;
		
		$y_info = $y+240;
		$pdf->SetFont('Arial','B',6);
		$pdf->SetXY($x+6-3,$y_info);
		$pdf->Cell(38,5,$max_retail_interest.'    '.$retail_interest,0,0,'C');
		
		$pdf->SetX($x+47-5);
		$pdf->Cell(41,5,$max_cash_advan_interest.'    '.$cash_advan_interest,0,0,'C');
		
		$pdf->SetX($x+91-8);
		$pdf->Cell(33,5,$prev_balance,0,0,'C');
		
		$pdf->SetX($x+126-10);
		$pdf->Cell(31,5,$payment_credit,0,0,'C');
		
		$record = 0;
		
		
		return $halaman_cetak_kertas;
	}
	
	function end_detail($pdf,$x,$y,$new_balance,$interest_service_charge,$dr_cr_indicator,$statement_date, $halaman_cetak_kertas){
		if($interest_service_charge!=0){
			$pdf->Ln();
			$pdf->SetX($x+4);
			$pdf->Cell(35-13,4,$statement_date,0,0,'L');
			
			$pdf->Cell(29+13-22,4,$statement_date,0,0,'L');
			
			$pdf->Cell(67-10+22,4,'TOTAL INTEREST & SERVICE CHARGE',0,0,'L');
			
			$pdf->Cell(18,4,$interest_service_charge,0,0,'R');
		}
		
		$pdf->Ln();
		$pdf->Ln();
		$pdf->Ln();
		$pdf->Ln();
		
		$pdf->SetX($x+67-21);
		$pdf->Cell(67-9+23,4,'Tagihan Bulan ini',0,0,'L');
		
		$pdf->Cell(18,4,$new_balance,0,1,'R');
		
		
		
		
		if ( $GLOBALS['flag_hana_bank']  === true )
		{
			insert_tmp_hana_bank_all_cycle( $GLOBALS['nomor_rekening'] , $GLOBALS['billing_cycle'], $new_balance, $GLOBALS['due_date'] );
		}
		
		return $halaman_cetak_kertas;
	}
	
	function output_pdf($pdf,$nama_file_pdf,$flagtrans,$blth,$file){
		
		
		
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
	
	function insert_mcustomer($no_card, $email, $password_pdf,
								$blth, $product, $log_customer_id){
		$sql_ins_cust = "INSERT INTO m_customer(
								nomor_rekening, email1,
								password_pdf, blth, flagtrans,
								log_customer_id)
							VALUES(
								'$no_card', E'".addslashes($email)."',
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
								$pdf_name,$password_pdf,$jml_hlm, $email, $tabeldetail, $ket_produk){
								
		/**Hitung banyaknya email**/
		#$n_email = substr_count($email,";");
		$n_email = 1;
		
		/**Hitung besarnya size PDF**/
		$file = str_replace(substr($nama_file,-4),'',$nama_file);
		$document = '../pdf/'.$flagtrans.'/'.$blth.'/';
		$document = $document.$file.'/'.$pdf_name.'.pdf';
		
		$filesizePdfBaru = fsize($document);
		
		//$cek =cek_norek_utk_kasus_kirim_ulang_cycle15Agustus2019($nomor_rekening);
		//if ($cek == 'ada')
		{
				$sql_ins_detail = "INSERT INTO $tabeldetail(
										m_loading_id,nomor_customer,nomor_rekening,
										nama,alamat1,alamat2,
										alamat3,city,zipcode,
										flagtrans,blth,nama_file,
										pdf_name,password_pdf,jml_hlm,email,n_email,size_pdf,ket_produk
									)VALUES(
										$m_loading_id,'$nomor_customer','$nomor_rekening',
										E'".addslashes($nama)."',E'".addslashes($address1)."',E'".addslashes($address2)."',
										E'".addslashes($address3)."','".addslashes($city)."','$zipcode',
										'$flagtrans','$blth','$nama_file',
										'$pdf_name','$password_pdf',$jml_hlm,E'".addslashes($email)."','$n_email','$filesizePdfBaru','$ket_produk'
									)";
				$qry_ins_detail = pg_query($sql_ins_detail) or die('ERROR insert into detail: '.$sql_ins_detail);
				if(pg_affected_rows($qry_ins_detail)==0)
				{
					echo 'GAGAL INSERT DETAIL UNTUK $nomor_rekening';
					die();
				}
		}
	}
	
	function cek_norek_utk_kasus_kirim_ulang_cycle15Agustus2019($nomor_rekening)
	{
		$sql = "SELECT nomor_rekening FROM tmp_kirim_ulang_cycle15Agustus2019 WHERE nomor_rekening = '$nomor_rekening' ";
		$qry = pg_query($sql) or die('ERROR: '.$sql);
		$jml = pg_num_rows($qry);
		if($jml > 0)
		{return 'ada' ;}
		else{return '';}
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
			$pdf_name = $row['pdf_name'].'.pdf';
			$password_pdf = $row['password_pdf'];
			
			$detail .= "'".$nomor_rekening."';'".$nama."';'".$pdf_name."';'".$password_pdf."'".chr(13);
		}
		
		$open_file = fopen($file_csv,'w');
		fwrite($open_file,$header.$detail);
		fclose($open_file);
	}
	
	
	function add_page1($pdf, $url_pdf='')
	{
		if ( !empty($url_pdf) )
		{
			$pdf->AddPage();
			$master_template = "../tmp/master/$url_pdf";
		
			$pdf->setSourceFile($master_template);
			$import_template = $pdf->importPage(1);
			$pdf->useTemplate($import_template, 0,0, null, false);
			
			return 'ada';
		}
		
		return 'no';
		

	}
	
	function add_page1_jpg($pdf, $url_pdf='')
	{
		if ( !empty($url_pdf) )
		{
			$pdf->AddPage();
			$url_img = "../tmp/master/$url_pdf";
		
			#$pdf->Image($url_img,0,0,216.00,304.80);
			$pdf->Image($url_img,0,0,217,302);
			
			return 'ada';
		}
		
		return 'no';
		

	}
	
	
	function add_page2_sumamry_jpg($pdf, $url_pdf='', $arr_summary, $jml_hlm , $barcode, $posko, $kurir, $nourut, $kolektibilitas, $file )
	{
		#die(print_r($arr_summary));
		#if(empty($arr_summary)) return 'no';
		
		if ( !empty($url_pdf) )
		{
			$pdf->AddPage();
			$halaman_cetak_kertas = 1;
			
			
			
			
			$url_img = "$url_pdf";
		
			#$pdf->Image($url_img,0,0,216.00,304.80);
			$pdf->Image($url_img,0,0,217,302);
			
			
			
			
			///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			
		
			///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			
			$x_hlm = $x+133+20+18;
			$y_hlm = $y+82+38-20;
			$pdf->SetXY($x_hlm,$y_hlm);
			$ukuran_font_xx = 7.5;
			
			$pdf->SetFont('BRICARD02','',$ukuran_font_xx);
			#####$pdf->Cell(20,5,'Halaman: '.$hlm.' / '.$jml_hlm);
			$pdf->SetFont('Arial','',$ukuran_font_xx);
			########$pdf->Cell(20,5,'Halaman: 1 / '.$jml_hlm);//20200120 dimatikan karena error halaman KZ
			#$pdf->Cell(20,5,'Halaman: '.$pdf->getAliasNumpage().' / '.$pdf->getAliasNbPages()); //20200120 dimatikan karena error halaman KZ
			$pdf->Cell(20,5,'Halaman: 1'.' / '.$jml_hlm); //20200120 dimatikan karena error halaman KZ
			$pdf->SetFont('BRICARD02','',$ukuran_font_xx);
		
			$var_summary_jeda = 22;
			$var_geser_x1 = 13;
			$x_kanan_ujung=100+$var_geser_x1+20 ;
			#$x_kolom['atas']				= array(24, 75, 125, 173);//jjika LEFT
			$x_kolom['atas']				= array(1, 50, 98, 148);
			$x_kolom['atas_x']				= array('n', 'n', 'y', 'y');
			$x_kolom['bawah']				= array(1, 50, 98, 148);
			$x_kolom['bawah_x']				= array('n', 'n', 'y', 'y');
			
			$x_kolom['footer']				= array(23, 63+$var_geser_x1 , $x_kanan_ujung-(2*$var_summary_jeda)-30, $x_kanan_ujung-(1*$var_summary_jeda)-30, $x_kanan_ujung-30);
			$x_kolom['footer_x']			= array('nn', 'n', 'y', 'y', 'y');
			
			$f = 0;
			$total_tagihan_1 = 0;
			$total_tagihan_2 = 0;
			$var_y_summary = 15+25;
			
			foreach ($arr_summary as $key => $value) 
			{
				
				foreach ($value as $key2 => $value2) 
				{
					
					$arr_isi = explode('##',$value2);
					if ( $key2 == 'header1' || $key2 == 'header2' )
					{
						if ( $key2 == 'header1' )
						{
							$xx=50-5;
							$flag_xx = 'atas_x';
							$flag_x = 'atas';
						}else{
							$xx=70 -10;
							$flag_xx = 'bawah_x';
							$flag_x = 'bawah';
						}
						#die($key2.'----'.$value2);# -- header1----4365020131157009##20190901##00000060000000##00000038290361--
						for($x_tambahan=0;$x_tambahan<4;$x_tambahan++)
						{
							#die ("aaa ".$x_kolom['atas'][$x_tambahan]);
							$pdf->SetXY($x_kolom[$flag_x][$x_tambahan],$xx);
							#$pdf->SetFont('BRICARD02','',$ukuran_font_xx);
							$pdf->SetFont('Arial','B','6');
							
							
							//UNTUK MENGECEK APAKAH MERUBAH FORMAT NUMBER DAN TEXT
							if ( $x_kolom[$flag_xx][$x_tambahan]=='y')
							{
								#$pdf->Cell(67,4, number_format((int)$arr_isi[$x_tambahan],0,'',',') ,0,0,'L');
								#$pdf->Cell(67,4, number_format((int)$arr_isi[$x_tambahan],0,'','.') ,0,0,'C');
								
								$pdf->Cell(67,4,$arr_isi[$x_tambahan],0,0,'C');
							}else{
								$pdf->Cell(67,4,$arr_isi[$x_tambahan],0,0,'C');
								
								
							}
							
							$pdf->SetFont('BRICARD02','',$ukuran_font_xx);
						}
					}else if (  $key2 == 'header3' ){
						
						/////////////////////////////////////////////////////////////////////////////////////
								#$xxxx = $pdf->GetX()-67;
								#$yyyyy = $pdf->GetY()-2;
								#die( "x = $xxxx, y=$yyyyy");
								$x_barcode = 23;
								$y_barcode =95;
								$pdf->SetXY($x_barcode,$y_barcode-5.5);
								$pdf->SetFont('C39P24DhTt','',45);
								$pdf->Cell(70,4,$barcode,0,1,'L');
								
								$pdf->SetFillColor(255,255,255);
								$pdf->Rect($x_barcode,$y_barcode-17-3-2,53,5.5+11+2,'F');
								
								$pdf->SetXY($x_barcode+52,$y_barcode-1);
								$pdf->SetFont('Arial','',8);
								$pdf->Cell(10,3,$posko,0,1,'L');
								
								$pdf->SetX($x_barcode+52);
								$pdf->Cell(10,3,$kurir,0,1,'L');
								
								$pdf->SetXY($x_barcode,$y_barcode+5);
								$pdf->Cell(70,3,$barcode." - ".$nourut." / ".$file,0,0,'L');
								
						/////////////////////////////////////////////////////////////////////////////////////
						
						
						for($x_tambahan=0;$x_tambahan<=count($arr_isi);$x_tambahan++)
						{
							
							if ($x_tambahan+1 == count($arr_isi) )
							{
								$pdf->SetXY(123,85-14-6+5+2-2);
								$pdf->SetFont('Arial','',$ukuran_font_xx);
								#$pdf->MultiCell(70,3.5,$arr_isi[$x_tambahan],0,'C');
								$pdf->MultiCell(70,3.5,$arr_isi[$x_tambahan],0,'J'); //20201223
								$pdf->SetFont('BRICARD02','',$ukuran_font_xx);
							}else{
								$pdf->SetXY(20+3,87+(3.5*$x_tambahan)-18);
								$pdf->SetFont('BRICARD02','',$ukuran_font_xx);
								$pdf->Cell(67,4,$arr_isi[$x_tambahan],0,0,'L');
							}
							
							
							
						}
						
						
						
		
					}else if (  $key2 == 'footer1' ){
						for($x_tambahan=0;$x_tambahan<=count($arr_isi);$x_tambahan++)
						{
							$pdf->SetXY($x_kolom['footer'][1],170-$var_y_summary);
							$pdf->SetFont('BRICARD02','',$ukuran_font_xx);
							$pdf->Cell(67,4,'Kredit Limit Gabungan',0,0,'L');
							
							
							$pdf->SetXY($x_kolom['footer'][2],170-$var_y_summary);
							$pdf->SetFont('BRICARD02','',$ukuran_font_xx);
							#$pdf->Cell(67,4, number_format((int)$arr_isi[$x_tambahan],0,'',',') ,0,0,'R');
							$pdf->Cell(67,4, $arr_isi[$x_tambahan] ,0,0,'R');
						}
					}else if (  $key2 == 'footer2' ){
						#$summary_footer = number_format((int)$arr_isi[0],0,'','.');//perubahan pemisah desimal titik
						$summary_footer = $arr_isi[0];//perubahan pemisah desimal titik
					}else if (  $key2 == 'footer5' ){
						#$summary_footer_x1 = number_format((int)$arr_isi[0],0,'','.');//perubahan pemisah desimal titik
						$summary_footer_x1 = $arr_isi[0];
					}else if (  $key2 == 'footer6' ){
						#$summary_footer_x2 = number_format((int)$arr_isi[0],0,'','.');//perubahan pemisah desimal titik
						$summary_footer_x2 = $arr_isi[0];//perubahan pemisah desimal titik
					}else{
						for($x_tambahan=0;$x_tambahan<=count($arr_isi);$x_tambahan++)
						{
							
								if ($x_tambahan == 3)
								{
									$total_tagihan_1 += (int)$arr_isi[$x_tambahan];
								}else if($x_tambahan == 4){
									$total_tagihan_2 += (int)$arr_isi[$x_tambahan];
								}
							
								$pdf->SetXY($x_kolom['footer'][$x_tambahan],180+(10*$f)-$var_y_summary);
								$pdf->SetFont('BRICARD02','',$ukuran_font_xx);
									
								if ($x_tambahan == 3)
								{
									$pdf->Cell(67+1.3,4,$arr_isi[$x_tambahan],0,0,'R');
									
								}else{
									
								
										if ( $x_kolom['footer_x'][$x_tambahan]=='y' )
										{
											
											#$pdf->Cell(67,4, number_format((int)$arr_isi[$x_tambahan],0,'',',') ,0,0,'R');
											$pdf->Cell(67,4, $arr_isi[$x_tambahan] ,0,0,'R');
											
										}else{
											if ( $x_kolom['footer_x'][$x_tambahan]=='nn')
											{
												#$pdf->SetXY(20+71,85-14);
												$pdf->MultiCell(50,3,$arr_isi[$x_tambahan],0,'L');
											}else{
												$pdf->Cell(67,4,$arr_isi[$x_tambahan],0,0,'L');
											}
											
										}
										
								}
						}
						$f++;
					}
				}
			}
			
							#$f++;
							$pdf->SetXY($x_kolom['footer'][4]+35,180+(10*$f)-$var_y_summary);
							$pdf->SetFont('BRICARD02','',$ukuran_font_xx);
							$pdf->Cell(67,4,'Sisa Limit Gabungan',0,0,'L');
							
							
							$pdf->SetXY($x_kolom['footer'][4]+$var_summary_jeda,180+(10*$f)-$var_y_summary);
							$pdf->SetFont('BRICARD02','',$ukuran_font_xx);
							$pdf->Cell(67,4, $summary_footer ,0,0,'R');
			
			
			
							#$f = $f+3;
							$f++;
							$pdf->SetXY($x_kolom['footer'][0],180+(10*$f)-$var_y_summary);
							$pdf->SetFont('BRICARD02','',$ukuran_font_xx);
							$pdf->Cell(67,4,'Total Tagihan Bulan ini',0,0,'L');
							
							
							///////////////////////////////////////////////// tambahan footer summary 20200924
							$pdf->SetXY($x_kolom['footer'][0]+50,30+180+(10*$f)-$var_y_summary);
							$pdf->SetFont('helvetica','I',$ukuran_font_xx+1);
							$pdf->Cell(67,4,'Pembayaran atas tagihan Kartu Kredit BRI Anda, agar tetap dibayarkan ke masing-masing kartu',0,0,'C');
							///////////////////////////////////////////////// tambahan footer summary 20200924
							
							
							$pdf->SetXY($x_kolom['footer'][3],180+(10*$f)-$var_y_summary);
							$pdf->SetFont('BRICARD02','',$ukuran_font_xx);
							#####$pdf->Cell(67,4, number_format($total_tagihan_1,0,'',','),0,0,'R');//kz 20191107
							$pdf->Cell(67+1.3,4, $summary_footer_x1,0,0,'R');
							
							
							$pdf->SetXY($x_kolom['footer'][4],180+(10*$f)-$var_y_summary);
							$pdf->SetFont('BRICARD02','',$ukuran_font_xx);
							#####$pdf->Cell(67,4, number_format($total_tagihan_2,0,'',','),0,0,'R');//kz 20191107
							$pdf->Cell(67,4, $summary_footer_x2,0,0,'R');
			///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			
			#return 'ada';
			return $halaman_cetak_kertas;
		}
		
		
		
		return 'no';
		

	}
	
	
	function cek_email_status_online($nomor_rekening, $email_lama)
	{
		
		/*
		select barcode, cardno, email, flag_status, flag_update
		from customer 
		where 
		cardno = '5520020170666604' and flag_update='t' 
		and
		(flag_status='1' OR flag_status='2')
		*/
		
		$sql = "select barcode, cardno, email, flag_status, flag_update
		from customer_online
		where 
		cardno = '$nomor_rekening' and flag_update='t' 
		and
		(flag_status='1' OR flag_status='2')";
		$qry = pg_query($sql) or die('ERROR cek email di SID: '.$sql);
		$row = pg_fetch_array($qry);
		$jml = pg_num_rows($qry);
		if($jml > 0){
			return $row['email'];
		}else{
			return $email_lama;
		}
	}
	
	
	function ubah_email_customer_online($tabeldetail, $m_loading_id)
	{
		
		$sql = "SELECT b.cardno,b.email as email_baru, a.nama, a.nomor_rekening, a.email FROM
(
select * from $tabeldetail
where m_loading_id=$m_loading_id ) a

LEFT JOIN customer_online b ON b.cardno = a.nomor_rekening
WHERE b.cardno IS NOT NULL
AND b.flag_update='t' 
AND (b.flag_status='1' OR b.flag_status='2')";
		$qry = pg_query($sql) or die('ERROR cek email di SID: '.$sql);
		$jml = pg_num_rows($qry);
		if($jml > 0){
			
			while($row = pg_fetch_array($qry)){
				$nomor_rekening = $row['nomor_rekening'];
				$email = $row['email_baru'];
				
				$sql2 = "UPDATE $tabeldetail 
						SET 
							email='$email'
						WHERE 
							nomor_rekening='$nomor_rekening' AND m_loading_id = $m_loading_id";
				$qry2 = pg_query($sql2) or die('ERROR: '.$sql2);
				
			}
			
		}else{
			return false;
		}
	}
	
	
	function insert_tmp_hana_bank( $nomor_rekening , $billing_cycle, $new_balance, $due_date )
	{
		$nomor_rekening = trim($nomor_rekening);
		
		$sql = "select * from master_hana_bank 
		where 
		nomor_rekening = '$nomor_rekening'";
		$qry = @pg_query($sql);
		$jml = @pg_num_rows($qry);
		if($jml > 0){
				$sql2 = "INSERT INTO tmp_excel_hana_bank(
				 credit_card_no, billing_cycle, min_payment, 
				full_payment, due_date)
		VALUES ('$nomor_rekening', '$billing_cycle', '', '$new_balance', 
				'$due_date');";
			$qry2 = @pg_query($sql2);
			return true;
		}else{
			return false;
		}
	}
	
	function insert_tmp_hana_bank_all_cycle( $nomor_rekening , $billing_cycle, $new_balance, $due_date )
	{
		$nomor_rekening = trim($nomor_rekening);
		
		
				$sql2 = "INSERT INTO tmp_excel_hana_bank(
				 credit_card_no, billing_cycle, min_payment, 
				full_payment, due_date)
		VALUES ('$nomor_rekening', '$billing_cycle', '', '$new_balance', 
				'$due_date');";
			$qry2 = @pg_query($sql2);
			return true;
		
	}
	
	
	function export_excel_hana_bank($namafile)
	{
		
		
		include '../include/PHPExcel/PHPExcel.php';
		/** PHPExcel_IOFactory */
		include '../include/phpexcel/PHPExcel/IOFactory.php';
		
			$sql_datas = "SELECT  * FROM tmp_excel_hana_bank";
		$qry_datas = pg_query($sql_datas) or die('ERROR select data: '.$sql_datas);
		$sum_sql_datas = pg_num_rows($qry_datas);
		
		if($sum_sql_datas == 0 ) return false;
		
		
		$objPHPExcel = new PHPExcel();
		
		
		#$objPHPExcel->getDefaultStyle()->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
	
	
		$F=$objPHPExcel->getActiveSheet();
		
		$styleArray = array(
			'font' => array(
				'name'         => 'Arial',
				'bold'         => true,
				'italic'    => false,
				'size'        => 12
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap'       => true
			)
		);
		
	
		$Line=1;
		
		$F->getColumnDimension('A')->setWidth(30);
		$F->getColumnDimension('B')->setWidth(30);
		$F->getColumnDimension('C')->setWidth(30);
		$F->getColumnDimension('D')->setWidth(30);
		$F->getColumnDimension('E')->setWidth(30);
		
		
		$styleArray = array(
			'font' => array(
				'name'         => 'Arial',
				'bold'         => true,
				'italic'    => false,
				'size'        => 12
			),
			'borders' => array(
			'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
			'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
			'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
			'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
		),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap'       => true
			)
		);
		
	
		$objPHPExcel->getActiveSheet()->getStyle('A'.$Line.':E'.$Line.'')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('ffdfdfdf');
		
		#$objPHPExcel->getActiveSheet()->getStyle('A'.$Line.':E'.$Line.'')->setQuotePrefix(true);
		
		
		
		
		$F->setCellValueExplicit('A'.$Line, "credit_card_no", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('A'.$Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('B'.$Line, "billing_cycle", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('B'.$Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('C'.$Line, "min_payment", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('C'.$Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('D'.$Line, "full_payment", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('D'.$Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('E'.$Line, "due_date", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('E'.$Line)->applyFromArray($styleArray);
		
		$Line=2;
		$nomor = 1;
		while($row_data = pg_fetch_array($qry_datas)){//extract each record
		$credit_card_no = $row_data['credit_card_no'];
		
		$billing_cycle_x = $row_data['billing_cycle'];
		$ax = explode('-',$billing_cycle_x );
		$billing_cycle = $ax[0];
		
		$min_payment = $row_data['min_payment'];
		$full_payment = $row_data['full_payment'];
		
		$due_date_x = $row_data['due_date'];
		$axy = explode('-',$due_date_x );
		$due_date = $axy[2].'/'.$axy[1].'/'.$axy[0];
		
		$styleArray = array(
		'borders' => array(
			'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
			'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
			'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
			'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
		)
		);
		
		$F->getStyle('A'.$Line.'')->applyFromArray($styleArray);
		$F->getStyle('B'.$Line.'')->applyFromArray($styleArray);
		$F->getStyle('C'.$Line.'')->applyFromArray($styleArray);
		$F->getStyle('D'.$Line.'')->applyFromArray($styleArray);
		$F->getStyle('E'.$Line.'')->applyFromArray($styleArray);
		
		
		$F->getStyle('A'.$Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
	
		$F->setCellValue('A'.$Line, $credit_card_no);
		$F->getStyle('A'.$Line)->setQuotePrefix(true);
	
		
		#$F->setCellValue('A'.$Line, ' ' . $credit_card_no . ' ');
			#$F->setCellValueExplicit('A'.$Line, $credit_card_no, PHPExcel_Cell_DataType::TYPE_STRING );
				$F->setCellValueExplicit('B'.$Line, $billing_cycle, PHPExcel_Cell_DataType::TYPE_STRING);
				$F->setCellValueExplicit('C'.$Line, $min_payment, PHPExcel_Cell_DataType::TYPE_STRING);
				$F->setCellValueExplicit('D'.$Line, $full_payment, PHPExcel_Cell_DataType::TYPE_STRING);
				$F->setCellValueExplicit('E'.$Line, $due_date, PHPExcel_Cell_DataType::TYPE_STRING);
			++$Line;
			$nomor+=1;
			
			

			
		}
		
		
		
		
		#$dir = '../tmp/hana_bank/'.'hana_bank_'.$namafile.'.xls';
		$dir = '../tmp/hana_bank/'.'hana_bank_'.$namafile.'.xlsx';
		if(file_exists($dir)){ // check if file already
				unlink($dir);
			}
		//$objPHPExcel->save($dir);
		
		#$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		#$objWriter->save($dir);
		
#$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
#$objWriter->save($dir);
		
		
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 
$objWriter->setOffice2003Compatibility(true);
$objWriter->save($dir);


	}
	
	
	
	function get_3_huruf_bulan( $bln )
	{
		$date_mon = array(
		'01'=>'JAN',
		'02'=>'FEB',
		'03'=>'MAR',
		'04'=>'APR',
		'05'=>'MEI',
		'06'=>'JUN',
		'07'=>'JUL',
		'08'=>'AGU',
		'09'=>'SEP',
		'10'=>'OKT',
		'11'=>'NOV',
		'12'=>'DES');
		
		if (empty($date_mon[$bln])) return '00';

		return ucfirst( strtolower($date_mon[$bln]) );
		
	}
	
	
	@pg_close($con);
?>