<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/function.php"); ?>
<?php
	$pr = $_POST['pr'];
	$arr_pr = explode('|',$pr);
	$product = $arr_pr[0];
	$menu_id = $arr_pr[1];
	$act = $arr_pr[2];
	$blth = $arr_pr[3];
	$file = $arr_pr[4];
	$con = pg_connect($connection) or die('Could not connect to database!');
	$waktu_awal = strtotime(date("Y-m-d H:i:s"));
	$msg = "";
	$jml = 0;
	$psn="";
	
	if($act=='upload'){
		$open_txt = fopen($file,'r');
		$header_txt = trim(fgets($open_txt));
		$header = "NO	NoCard	Password_pdf	Email";
		$sql_ins_cust = "";
		
		if(strtoupper($header)==strtoupper($header_txt)){
			$log_customer_id = log_customer($product,$blth,$file);
			while(!feof($open_txt)){
				$row_txt = fgets($open_txt);
				if(trim($row_txt)!=""){
					$arr_row_txt = explode(chr(9),$row_txt);
					$no_card = trim($arr_row_txt[1]);
					$password_pdf = $arr_row_txt[2];
					if(strlen($password_pdf)!=6){
						$delcust="DELETE FROM log_customer WHERE log_customer_id=".$log_customer_id;
						$qrydelcust = pg_query($delcust)or die('Err: $delcust');
						$delcust="DELETE FROM m_customer WHERE log_customer_id=".$log_customer_id;
						$qrydelcust = pg_query($delcust)or die('Err: $delcust');
						$psn = "ERROR: Data $no_card no baris ke ".($jml+1)." punya password tidak 6 karakter";
						$jml = 0;
						$no_card_exists = 0;
						//$msg = "ERROR: There some data have a password more than 6 characters";
						break;
					}
					$email = addslashes(trim($arr_row_txt[3]));
					
					$sql_sel_cust = "SELECT * FROM m_customer WHERE log_customer_id = $log_customer_id and nomor_rekening = '$no_card'";
					$qry_sel_cust = pg_query($sql_sel_cust);
					
					if(pg_num_rows($qry_sel_cust)==0){
						$sql_ins_cust .= "INSERT INTO m_customer(
												nomor_rekening, email1,
												password_pdf, blth, flagtrans,
												log_customer_id)
											VALUES(
												'$no_card', '$email',
												'$password_pdf', '$blth', '$product',
												$log_customer_id);";
						$jml++;
					}else{
						if($no_card_exists==""){
							$no_card_exists = $no_card;
						}else{
							$no_card_exists .= ", ".$no_card;
						}
					}
				}
			}
			$msg = $jml."|".$no_card_exists;
			if($sql_ins_cust!=""){
				$qry_ins_cust = pg_query($sql_ins_cust) or die("<br>GAGAL INSERT INTO m_customer<br>".$sql_ins_cust);
				update_log_customer($log_customer_id,$jml);
			}
		}else{
			$psn = "ERROR: header file txt salah. Seharusnya <br>".$header."<br>Header File txt:<br>".$header_txt;
			$msg = "0|0";
		}
		$waktu_akhir = strtotime(date("Y-m-d H:i:s"));
		$waktu = $waktu_akhir-$waktu_awal;
		
		echo $msg.'|'.$waktu.'|'.$product.'|'.$psn;
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
	
	function update_log_customer($log_customer_id,$customer){
		$sql_upd_log_customer = "UPDATE log_customer
								SET
									total_customer = $customer
								WHERE log_customer_id = $log_customer_id";
		$qry_upd_log_customer = pg_query($sql_upd_log_customer) or die('ERROR update m_loading: '.$sql_upd_log_customer);
	}
	
	pg_close($con);
?>