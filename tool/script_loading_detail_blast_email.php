<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/function.php"); ?>
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
		$dirfile = '../tmp/'.$file;
		//$dirfile = '../temp_file/billing/'.$flagtrans.'/'.$blth.'/'.$file;
		$open_dat = fopen($dirfile,'r');
		$header_dat = fgets($open_dat);
		$header = "NO	NoCard	Nama	Email";
		if(trim(strtoupper($header))==trim(strtoupper($header_dat))){
			$m_loading_id = m_loading($flagtrans,$blth,$file);
			$log_customer_id = log_customer($flagtrans,$blth,$file);
			while(!feof($open_dat)){
				$row_txt = fgets($open_dat);
				if(trim($row_txt)!=""){
					$arr_row_txt = explode(chr(9),$row_txt);
					$no_card = trim($arr_row_txt[1]);
					$nama = $arr_row_txt[2];
					$email = trim($arr_row_txt[3]);
					
					$total_hlm = $total_hlm + $hlm;
					$customer++;
					//$pdf_name = substr($nomor_kunci,-4).'_'.$customer;
					//output_pdf($pdf,$pdf_name,$blth,$file,$flagtrans);
					insert_mcustomer($no_card, $email, '',
										$blth, $flagtrans, $log_customer_id);
					insert_detail($m_loading_id,$nomor_customer,$no_card,
									$nama,$alamat1,$alamat2,
									$alamat3,$city,$zipcode,
									$flagtrans,$blth,$file,
									'',$password_pdf,0,
									$email,'0',$flag_attach);
				}
			}
			update_log_customer($log_customer_id,$customer);
			update_m_loading($m_loading_id,$total_hlm,$customer);
			//if($customer > 0) create_csv($flagtrans,$m_loading_id,$blth,$file);
		}else{
			$msg_error = 'File TXT tidak standar<br>';
			echo $header."<br>";
			echo $header_dat."<br>";
		}
		return $msg_error.'|'.$customer.'|'.$msg_not_exists;
	}
	
	function insert_mcustomer($no_card, $email, $password_pdf,
								$blth, $product, $log_customer_id){
		$sql_ins_cust = "INSERT INTO m_customer(
								nomor_rekening, email1,
								password_pdf, blth, flagtrans,
								log_customer_id)
							VALUES(
								'$no_card', '$email',
								'$password_pdf', '$blth', '$product',
								$log_customer_id)";
		$qry_ins_cust = @pg_query($sql_ins_cust);
		if(pg_affected_rows($qry_ins_cust)==0){
			echo 'GAGAL INSERT CUSTOMER UNTUK $nomor_rekening<br>';
			echo $sql_ins_cust;
			die();
		}
	}
	
	function insert_detail($m_loading_id,$nomor_customer,$nomor_rekening,
								$nama,$address1,$address2,
								$address3,$city,$zipcode,
								$flagtrans,$blth,$nama_file,
								$pdf_name,$password_pdf,$jml_hlm,
								$email,$filesize,$flag_attach){
		//$n_email = substr_count($email,";");
		$email = rtrim($email,";");
			$arr_email = explode(";", $email);
		$n_email = count($arr_email);
		
		$sql_ins_detail = "INSERT INTO detail(
								m_loading_id,nomor_customer,nomor_rekening,
								nama,alamat1,alamat2,
								alamat3,city,zipcode,
								flagtrans,blth,nama_file,
								pdf_name,password_pdf,jml_hlm,
								email,n_email,size_pdf,
								flag_attach
							)VALUES(
								$m_loading_id,'$nomor_customer','$nomor_rekening',
								'".addslashes($nama)."','".addslashes($address1)."','".addslashes($address2)."',
								'".addslashes($address3)."','$city','$zipcode',
								'$flagtrans','$blth','$nama_file',
								'$pdf_name','$password_pdf',$jml_hlm,
								'$email',$n_email,'$filesize',
								null
							)";
		$qry_ins_detail = @pg_query($sql_ins_detail);
		if(pg_affected_rows($qry_ins_detail)==0){
			echo 'GAGAL INSERT DETAIL UNTUK $nomor_rekening<br>';
			echo $sql_ins_detail;
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
	
	function create_csv($flagtrans,$m_loading_id,$blth,$file){
		$csv = str_replace(substr($file,-4),'',$file);
		
		$file_csv = '../pdf/'.$flagtrans.'/'.$blth.'/'.$csv.'/'.$csv.'.csv';
		$header = "'nomor_rekening';'nama';'pdf_name';'password_pdf'".chr(13);
		$detail = "";
		
		$sql = "SELECT nomor_rekening,nama,pdf_name,password_pdf FROM detail WHERE m_loading_id = $m_loading_id";
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