<?php 


$www_location = "D:/app/htdocs/e_statement_bri/";					/*untuk live*/ 
ini_set('memory_limit', '-1');
require_once($www_location."include/config.php"); 
require_once($www_location."include/message.php"); 
require_once($www_location."include/function.php"); 
//rumus_tipe_gagal.php
require_once($www_location."include/rumus_tipe_gagal.php"); 
$con = pg_connect($connection) or die("Could not connect to database!");


	
		$sql = "
		
select test.body_bounce, test.date_bounce, test.tr_email_id, test.email from 
dblink('host=192.168.108.11 dbname=e_statement_bri user=postgres password=Indomedia123'::text,
'select body_bounce,date_bounce, tr_email_id, email from 
bounce_inbox where tr_email_id = ''3204313'' OR tr_email_id::integer < 1000000'::text) 
   test
   (body_bounce text,date_bounce text, tr_email_id text, email text)

   ";
		$qry_cek_dat = @pg_query($sql);
		$jml = @pg_num_rows($qry_cek_dat);
		//die($sql . $jml);
        if ($jml > 0) {
		
			
			while($row = pg_fetch_array($qry_cek_dat))
			{
				
				update_gagal_last( $con, $row['date_bounce'], $row['body_bounce'], $row['email'], 1 );
			
			}
		
		
			
			
		}
		die("selesai");

	function update_gagal_last( $con, $date_email_callback, $ket_error, $email, $tr_email_id )
	{
		
		$tipe_gagal = get_tipe_gagal ( $ket_error );
		
		$rev_ket_error= split_ket_error_gagal($ket_error);
		$rev_mm= substr($rev_ket_error, 0, 450);
		
		
	
		$sql = "SELECT * FROM tr_email WHERE tr_email_id=$tr_email_id AND lower(email)=lower('".trim($email)."') AND date_email_callback IS NULL";
		$qry_cek_dat = @pg_query($sql);
		$jml = @pg_num_rows($qry_cek_dat);
		//die($sql . $jml);
        if ($jml > 0) {
			//INSERT BERDASARKAN TR_EMAIL_ID
			//die('with tr_email_id');
			$sql = "UPDATE tr_email
				SET
				nama_customer = '$tipe_gagal', 
				date_email_callback='$date_email_callback' ,
				ket_error=E'".addslashes (str_replace ('--','', str_replace("'","",$rev_mm)))." (with tr_email_id)',
				email_callback='f'
				Where tr_email_id=$tr_email_id  AND email_callback IS NULL 
				";
				
				$cek = @pg_query($sql);
			
			
		}else{
			//INSERT BERDASARKAN EMAIL
			//die('with email');
			$sql = "UPDATE tr_email
			SET
			nama_customer = '$tipe_gagal', 
			date_email_callback='$date_email_callback' ,
			ket_error=E'".addslashes (str_replace ('--','', str_replace("'","",$rev_mm)))." (with email)',
			email_callback='f'
			
			Where lower(email)=lower('$email')  AND email_callback IS NULL 
			AND 
			(cast( '$date_email_callback' as timestamp))-(cast( date_email_send as timestamp)) between  '- 5 hours' and '1 days'
			";
			
			$cek = @pg_query($sql);
			
		}
	
	}
	
	
	
	function split_ket_error_gagal($ket_error)
	{
	
			$x = trim(preg_replace('/\s\s+/', ' ', $ket_error));
			$string = trim(preg_replace('/\s+/', ' ', $x));
			//echo $string;
			
			//preg_match('~:(.*?)-~', $string, $output);
			//echo "<br>".$output[1]; // 256
			
			$n=explode("Below", $string);
			//echo "<br>".$n[0]; // 256
			
			return $n[0];
	
	}
pg_close($con);
?>