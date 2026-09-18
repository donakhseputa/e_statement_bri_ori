<?php
	
	require_once("../include/config.php");
	
	//kirim antrian otomatis
	//require_once("../include/proses_otomatis/script_loading_otomatis.php");
	//kirim antrian otomatis
	
	$con = pg_connect($connection) or die("Could not connect to database!");
	
	//$act		= $_REQUEST['act'];
	
	$sql = "SELECT tr_email_id, ket_error, date_email_send FROM tr_email 
	WHERE status_sample='f' 
	AND ket_error IS NOT NULL
	--AND nama_customer IS NULL 
	AND nama_customer = 'not defined'
	AND ( date_email_send between '2017-03-01 00:00:01' AND  '2018-01-01 00:00:01') 
	ORDER BY tr_email_id DESC 
	--LIMIT 10";
	$query = pg_query($sql);
	//date_bounce between '2014-12-21 17:41:09' AND '2014-12-23 18:27:33'	
			
	while($row = pg_fetch_array($query))
	{
		
		$tipe_error = get_tipe_gagal ( $row['ket_error'] );
		//if ($tipe_error != 'not defined' ) continue;
		echo $row['tr_email_id']."---  $tipe_error -------- ".$row['date_email_send'] ."<br>";
		
		$sql2 ="UPDATE tr_email
				SET nama_customer='".$tipe_error."'
				WHERE tr_email_id=".$row['tr_email_id'];
		pg_query($sql2);
	}
	
	
	
	function get_tipe_gagal ( $string )
	{
		$tipe_error='';
		$arr_code_smtp=rumus_code_smtp('get');
		$array_preg_match=get_preg_match();
		
		/*
		echo '<pre>';
		print_r( $arr_code_smtp );
		echo '</pre>';
		die();
		*/
		
		//START MENCARI CODE BERDASARKAN ARRAY CODE SMTP
		foreach ( $arr_code_smtp as $key => $value) 
		{
		# code...
			if( preg_match("/ $key(.+) /i", $string ) || preg_match("/ $key(.+)-/i", $string ) ) 
			{
				//do something
				//echo "ketemu m_loading_id ke ".$row['tr_email_id'];
				//die();
				
				$tipe_error		=	rumus_code_smtp($key);
				break;
			}
		}
		//END MENCARI CODE BERDASARKAN ARRAY CODE SMTP
		
		
		//START KARENA DENGAN CODE TIDAK DITEMUKAN, MAKA MENCARI DENGAN STRING
		if ( empty($tipe_error) )
		{
			foreach ( $array_preg_match as $key => $value) 
			{
			# code...
				if( preg_match($key, $string ) ) 
				{
					//do something
					//echo "ketemu m_loading_id ke ".$row['tr_email_id'];
					//die();
					
					$tipe_error		=	rumus_code_smtp($value);
					break;
				}
			}
			
		}
		//END KARENA DENGAN CODE TIDAK DITEMUKAN, MAKA MENCARI DENGAN STRING
		
		if ( empty($tipe_error) ) $tipe_error='not defined';
		
		
		return $tipe_error;
	}
	
	
	
	function rumus_code_smtp($code='')
	{
		
		$code_smtp=array();
		$code_smtp['200']='200 nonstandard success response';
		$code_smtp['211']='211 System status, or system help reply';
		$code_smtp['214']='214 Help message';
		$code_smtp['220']='220 <domain> Service ready';
		$code_smtp['221']='221 <domain> Service closing transmission channel';
		$code_smtp['250']='250 Requested mail action okay, completed';
		$code_smtp['251']='251 User not local; will forward to <forward-path>';
		$code_smtp['252']='252 Cannot VRFY user, but will accept message and attempt delivery';
		$code_smtp['354']='354 Start mail input; end with';
		$code_smtp['421']='421 <domain> Service not available, closing transmission channel';
		$code_smtp['450']='450 Requested mail action not taken: mailbox unavailable';
		$code_smtp['451']='451 Requested action aborted: local error in processing';
		$code_smtp['452']='452 Requested action not taken: insufficient system storage';
		$code_smtp['500']='500 Syntax error, command unrecognised';
		$code_smtp['501']='501 Syntax error in parameters or arguments';
		$code_smtp['502']='502 Command not implemented';
		$code_smtp['503']='503 Bad sequence of commands';
		$code_smtp['504']='504 Command parameter not implemented';
		$code_smtp['521']='521 <domain> does not accept mail';
		$code_smtp['530']='530 Access denied';
		$code_smtp['550']='550 mailbox unavailable (does not exist)';
		$code_smtp['551']='551 User not local; please try again';
		$code_smtp['552']='552 exceeded storage allocation';
		$code_smtp['553']='553 mailbox name not allowed';
		$code_smtp['554']='554 This account has been disabled or discontinued';
		$code_smtp['101']='101 Couldnt find any host named';
		$code_smtp['102']='102 Queue too long';
		$code_smtp['103']='103 Email Delayed';
		$code_smtp['104']='104 Error Domain';
		
		if ($code=='get')
		{
			return $code_smtp;
		}else{
			return $code_smtp[$code];
		}
		
	}
	
	function get_preg_match()
	{
		$key_smtp=array();
		$key_smtp['/host(.+)name/i']					='101';
		$key_smtp['/host (.+)name/i']					='101';
		$key_smtp['/tidak dapat(.+)ditemukan/i']		='101';
		$key_smtp['/tidak dapat (.+)ditemukan/i']		='101';
		$key_smtp['/ queue(.+)too/i']					='102';
		$key_smtp['/ queue (.+)too/i']					='102';
		$key_smtp['/delayed(.+) /i']					='103';
		$key_smtp['/delayed (.+) /i']					='103';
		$key_smtp['/over(.+)quota/i']					='552';
		$key_smtp['/over (.+)quota/i']					='552';
		$key_smtp['/no(.+)mailbox/i']					='550';
		$key_smtp['/no (.+)mailbox/i']					='550';
		$key_smtp['/tidak(.+)ditemukan/i']				='550';
		$key_smtp['/tidak (.+)ditemukan/i']				='550';
		$key_smtp['/couldnt be(.+)found/i']				='550';
		$key_smtp['/couldnt be (.+)found/i']			='550';
		$key_smtp['/doesnt(.+)have/i']					='550';
		$key_smtp['/doesnt (.+)have/i']					='550';
		$key_smtp['/failed(.+)status/i']				='530';
		$key_smtp['/failed (.+)status/i']				='530';
		$key_smtp['/couldnt(.+)find/i']					='104';
		$key_smtp['/couldnt (.+)find/i']				='104';
		$key_smtp['/couldn(.+)find/i']					='104';
		$key_smtp['/couldn (.+)find/i']					='104';
		$key_smtp['/confidential(.+)information/i']		='530';  
		$key_smtp['/confidential (.+)information/i']	='530';  
		$key_smtp['/quota(.+)exceeded/i']				='552';
		$key_smtp['/quota (.+)exceeded/i']				='552';
		$key_smtp['/ (.+)failed/i']						='530'; 
		$key_smtp['/ (.+)disabled/i']					='554'; 
		$key_smtp['/mailbox is(.+)full/i']				='552';
		$key_smtp['/mailbox is (.+)full/i']				='552';
		$key_smtp['/sudah(.+)penuh/i']					='552';
		$key_smtp['/sudah (.+)penuh/i']					='552';
		$key_smtp['/no such(.+)user/i']					='550';
		$key_smtp['/no such (.+)user/i']				='550';
		$key_smtp['/no such(.+)address/i']				='550';
		$key_smtp['/no such (.+)address/i']				='550';
		$key_smtp['/no longer(.+)accepts/i']			='521';
		$key_smtp['/no longer (.+)accepts/i']			='521';
		$key_smtp['/could not(.+)be delivered/i']		='521';
		$key_smtp['/could not (.+)be delivered/i']		='521';
		
		  
		
		
		return $key_smtp;
	}
					
	//@pg_close($con);
?>