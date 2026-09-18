<?php require_once("../include/config.php"); ?>
<?php require_once("../include/session.php"); ?>
<?php require_once("../include/function.php"); ?>
<?php

	$pr = $_POST['pr'];
	$arr_pr = explode('|',$pr);
	$product = $arr_pr[0];
	$menu_id = $arr_pr[1];
	$act = $arr_pr[2];
	$blth = $arr_pr[3];
	$file = $arr_pr[4];
	
	$blth_report= $arr_pr[5];
	
	$con = pg_connect($connection) or die('Could not connect to database!');
	$waktu_awal = strtotime(date("Y-m-d H:i:s"));
	$msg = "";
	$jml = 0;
	$jml_noexist = 0;
	
	
	
	
	
	if($act=='upload'){
	
	//CEK BLTH
	/**	Digunakan untuk menentukan tabel detail yang mana yang akan dipakai	**/
	$blth_balik = substr($blth,-4).substr($blth,0,2);
	$flagtrans = $product;
	
			$BLTH_BARUNYA = BLTH_BARU;
			if($blth_balik>$BLTH_BARUNYA){
				$tabeldetail = "detail_" . $blth."_".$flagtrans;
				$sql_cek = "SELECT a.m_loading_id FROM m_loading a LEFT JOIN $tabeldetail b ON a.m_loading_id=b.m_loading_id 
				WHERE a.blth='$blth' AND a.flagtrans='$flagtrans'";
				$exe_cek = @pg_query($sql_cek);
				if($exe_cek){
					$tabeldetail = "detail_" . $blth."_".$flagtrans;
				}else{
					$tabeldetail = "detail";
				}
			}else{
				$tabeldetail = "detail";
			}

	#echo "tabeldetail:" . $tabeldetail . " , config blth: " . $BLTH_BARUNYA . " , blth:" . $blth_balik . ", flag: " . $flagtrans;
	#die;
	
	/** JIKA MENGGUNAKAN TIPE LOADING NOMOR REKENING (AH) **/
		$xyz= explode('/', $file);
		$nnn= substr($xyz[2], 0, 3);
		if ($nnn != 'KU_')
		{
			$msg = "ERROR: Nama File harus berawalan KU_";
			echo $msg.'|'.$waktu.'|'.$product;
		
		}else{
		
		
		$open_txt = fopen($file,'r');
		$header_txt = trim(fgets($open_txt));
		$header = "cardno	email";
		$sql_ins_cust = "";
		
		if(strtoupper($header)==strtoupper($header_txt)){
			$log_customer_id = log_customer($product,$blth,$file);
			while(!feof($open_txt)){
				$row_txt = fgets($open_txt);
				if(trim($row_txt)!=""){
					$arr_row_txt = explode(chr(9),$row_txt);
					$no_card = trim($arr_row_txt[0]);
					$email = trim($arr_row_txt[1]);
					

												
						
						$sql_cek_cust = "SELECT nomor_rekening,email FROM $tabeldetail WHERE blth='$blth' AND nomor_rekening='$no_card' AND flagtrans='$product'  AND substring(nama_file from 1 for 3) != 'KU_'";
						$exe_cek_cust = @pg_query($sql_cek_cust);
						$n_cek_cust = pg_num_rows($exe_cek_cust);
						$row_cek_cust = pg_fetch_array($exe_cek_cust);
						
						if($n_cek_cust>0){
						
						
						if(strlen($email)>0){
						
						$sql_ins_cust = "INSERT INTO m_customer_ku(nomor_rekening, email1,blth, flagtrans,log_customer_ku_id,existed_mcust)
											VALUES('$no_card', '$email', '$blth', '$product', $log_customer_id,'1');";
						@pg_query($sql_ins_cust);
						
						} else{
							$old_email = $row_cek_cust['email'];
							$sql_ins_cust = "INSERT INTO m_customer_ku(nomor_rekening, email1,blth, flagtrans,log_customer_ku_id,existed_mcust)
											VALUES('$no_card', '$old_email', '$blth', '$product', $log_customer_id,'1');";
							@pg_query($sql_ins_cust);
						}
						#$sql_edit_cust = "UPDATE m_customer SET email1='$email' WHERE blth='$blth' AND nomor_rekening='$no_card'";
						#@pg_query($sql_edit_cust);
						
						#$sql_upd_detail = "UPDATE detail SET email='$email' WHERE blth='$blth' AND nomor_rekening='$no_card'";
						#@pg_query($sql_upd_detail);
						
						$jml++;
						
						} else {
							
							$no_card_exists .= $no_card . ", ";
							$sql_ins_cust = "INSERT INTO m_customer_ku(nomor_rekening, email1,blth, flagtrans,log_customer_ku_id, existed_mcust)
											VALUES('$no_card', '$email', '$blth', '$product', $log_customer_id,'0');";
							@pg_query($sql_ins_cust);
							$jml_noexist++;
						}
						
						

					
				}
			}
			
			
			
			$folder_dest="../temp_file/ku/".$product."/".$blth_report."/";
			/* MEMBENTUK FILE CSV BERISI DATA NOMOR REKENING YANG TIDAK EXIST DI MCUSTOMER SEBELUMNYA */
			$csv = "no_exist_" . sprintf("%03d",$log_customer_id)."_".date("Ymd");
			$file_csv = $folder_dest.$csv.'.csv';
			$sql_csv = "SELECT nomor_rekening,email1 FROM m_customer_ku WHERE log_customer_ku_id='$log_customer_id' AND existed_mcust is false";
			$exe_csv = pg_query($sql_csv);
			$header = "nomor_rekening;email".chr(13);
			$detail = "";
			while($row_csv = pg_fetch_array($exe_csv)){
			$nomor_rekening = $row_csv['nomor_rekening'];
			$email = $row_csv['email1'];
			$detail .= "".$nomor_rekening.";".$email.";".chr(13);
			}
			$open_file = fopen($file_csv,'w');
			fwrite($open_file,$header.$detail);
			fclose($open_file);
		
			update_log_customer_ku($log_customer_id,$jml,$jml_noexist,$file_csv);
		$msg = $jml."|<b>". rtrim($no_card_exists, ", ") . "</b><br/>Rekapitulasinya dapat dilihat di: <a href='$file_csv' target='_blank'><u>[$csv]</u></a>";
			
		}else{
			$msg = "ERROR: header file txt salah. Seharusnya <br>".$header."<br>Header File txt:<br>".$header_txt;
		}
		$waktu_akhir = strtotime(date("Y-m-d H:i:s"));
		$waktu = $waktu_akhir-$waktu_awal;
		
		echo $msg.'|'.$waktu.'|'.$product;
		
		}
	}
	
	function log_customer($flagtrans,$blth,$file){
		$sql_ins_log_customer = "INSERT INTO log_customer_ku(
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
		
		$sql_sel_log_customer = "SELECT last_value FROM log_customer_ku_log_customer_ku_id_seq";
		$qry_sel_log_customer = pg_query($sql_sel_log_customer) or die('ERROR select log_customer: '.$sql_sel_log_customer);
		$row_sel_log_customer = pg_fetch_assoc($qry_sel_log_customer);
		$log_customer_id = $row_sel_log_customer['last_value'];
		
		return $log_customer_id;
	}
	
	function update_log_customer_ku($log_customer_id,$customer,$jml_noexist, $file_csv){
		$sql_upd_log_customer = "UPDATE log_customer_ku
								SET
									total_customer = $customer, location_noexist='$file_csv', noexist='$jml_noexist' 
								WHERE log_customer_ku_id = $log_customer_id";
		$qry_upd_log_customer = pg_query($sql_upd_log_customer) or die('ERROR update m_loading: '.$sql_upd_log_customer);
	}
	
	pg_close($con);
?>