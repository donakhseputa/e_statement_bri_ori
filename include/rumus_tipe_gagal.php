<?php
	/*
	require_once("../include/config.php");
	
	//kirim antrian otomatis
	//require_once("../include/proses_otomatis/script_loading_otomatis.php");
	//kirim antrian otomatis
	
	$con = pg_connect($connection) or die("Could not connect to database!");
	
	//$act		= $_REQUEST['act'];
	
	
	
	#$sql = "SELECT  * from tr_email where tr_email_id = 15375985";
	$sql = "SELECT  * from tr_email where loading_id = 3148";
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
	
	die("manual insert gagal");
	*/
	
	function get_tipe_gagal ( $string )
	{
		$tipe_error='';
		$arr_code_smtp_1=rumus_code_smtp_1('get');
		$arr_code_smtp_2=rumus_code_smtp_2('get');
		$array_preg_match=get_preg_match();
		
		/*
		echo '<pre>';
		print_r( $arr_code_smtp );
		echo '</pre>';
		die();
		*/
		
		
		
		//START KARENA DENGAN CODE TIDAK DITEMUKAN, MAKA MENCARI DENGAN STRING
		if ( empty($tipe_error) )
		{
			foreach ( $array_preg_match as $key => $value) 
			{
			# code...
				if( preg_match($key, $string ) ) 
				{
					//do something
					#echo "ketemu kode ke ".$value;
					#die();
					
					$tipe_error		=	rumus_code_smtp_2($value);
					break;
				}
			}
			
		}
		//END KARENA DENGAN CODE TIDAK DITEMUKAN, MAKA MENCARI DENGAN STRING
		
		
		//START MENCARI CODE BERDASARKAN ARRAY CODE SMTP 3 digit
		if ( empty($tipe_error) )
		{
			foreach ( $arr_code_smtp_1 as $key => $value) 
			{
			# code...
				if( preg_match("/$key/i", $string ) || preg_match("/ $key(.+) /i", $string ) || preg_match("/ $key(.+)-/i", $string ) ) 
				{
					//do something
					//echo "ketemu m_loading_id ke ".$row['tr_email_id'];
					//die();
					
					$tipe_error		=	rumus_code_smtp_1($key);
					break;
				}
			}
		}
		//END MENCARI CODE BERDASARKAN ARRAY CODE SMTP 3 digit
		
		//START MENCARI CODE BERDASARKAN ARRAY CODE SMTP
		if ( empty($tipe_error) )
		{
			foreach ( $arr_code_smtp_2 as $key => $value) 
			{
			# code...
				if( preg_match("/ $key(.+) /i", $string ) || preg_match("/ $key(.+)-/i", $string ) ) 
				{
					//do something
					//echo "ketemu m_loading_id ke ".$row['tr_email_id'];
					//die();
					
					$tipe_error		=	rumus_code_smtp_2($key);
					break;
				}
			}
		}
		//END MENCARI CODE BERDASARKAN ARRAY CODE SMTP
		
		
		
		if ( empty($tipe_error) ) $tipe_error='not defined';
		
		
		return $tipe_error;
	}
	
	
	function rumus_code_smtp_1($code='')
	{
		
		$code_smtp=array();
		#$code_smtp['200']='200 nonstandard success response';
		#$code_smtp['211']='211 System status, or system help reply';
		#$code_smtp['214']='214 Help message';
		#$code_smtp['220']='220 <domain> Service ready';
		#$code_smtp['221']='221 <domain> Service closing transmission channel';
		#$code_smtp['250']='250 Requested mail action okay, completed';
		#$code_smtp['251']='251 User not local; will forward to <forward-path>';
		#$code_smtp['252']='252 Cannot VRFY user, but will accept message and attempt delivery';
		#$code_smtp['354']='354 Start mail input; end with';
		#$code_smtp['421']='421 <domain> Service not available, closing transmission channel';
		
		$code_smtp['5.0.0']='5.0.0 Address does not exist';
$code_smtp['5.1.0']='5.1.0 Other address status';
$code_smtp['5.1.1']='5.1.1 Bad destination mailbox address';
$code_smtp['5.1.2']='5.1.2 Bad destination system address';
$code_smtp['5.1.3']='5.1.3 Bad destination mailbox address syntax';
$code_smtp['5.1.4']='5.1.4 Destination mailbox address ambiguous';
$code_smtp['5.1.5']='5.1.5 Destination mailbox address valid';
$code_smtp['5.1.6']='5.1.6 Mailbox has moved';
$code_smtp['5.1.7']='5.1.7 Bad senders mailbox address syntax';
$code_smtp['5.1.8']='5.1.8 Bad senders system address';
$code_smtp['5.2.0']='5.2.0 Other or undefined mailbox status';
$code_smtp['5.2.1']='5.2.1 Mailbox disabled, not accepting messages';
$code_smtp['5.2.2']='5.2.2 Mailbox full';
$code_smtp['5.2.3']='5.2.3 Message length exceeds administrative limit.';
$code_smtp['5.2.4']='5.2.4 Mailing list expansion problem';
$code_smtp['5.3.0']='5.3.0 Other or undefined mail system status';
$code_smtp['5.3.1']='5.3.1 Mail system full';
$code_smtp['5.3.2']='5.3.2 System not accepting network messages';
$code_smtp['5.3.3']='5.3.3 System not capable of selected features';
$code_smtp['5.3.4']='5.3.4 Message too big for system';
$code_smtp['5.4.0']='5.4.0 Other or undefined network or routing status';
$code_smtp['5.4.1']='5.4.1 No answer from host';
$code_smtp['5.4.2']='5.4.2 Bad connection';
$code_smtp['5.4.3']='5.4.3 Routing server failure';
$code_smtp['5.4.4']='5.4.4 Unable to route';
$code_smtp['5.4.5']='5.4.5 Network congestion';
$code_smtp['5.4.6']='5.4.6 Routing loop detected';
$code_smtp['5.4.7']='5.4.7 Delivery time expired';
$code_smtp['5.5.0']='5.5.0 Other or undefined protocol status';
$code_smtp['5.5.1']='5.5.1 Invalid command';
$code_smtp['5.5.2']='5.5.2 Syntax error';
$code_smtp['5.5.3']='5.5.3 Too many recipients';
$code_smtp['5.5.4']='5.5.4 Invalid command arguments';
$code_smtp['5.5.5']='5.5.5 Wrong protocol version';
$code_smtp['5.6.0']='5.6.0 Other or undefined media error';
$code_smtp['5.6.1']='5.6.1 Media not supported';
$code_smtp['5.6.2']='5.6.2 Conversion required and prohibited';
$code_smtp['5.6.3']='5.6.3 Conversion required but not supported';
$code_smtp['5.6.4']='5.6.4 Conversion with loss performed';
$code_smtp['5.6.5']='5.6.5 Conversion failed';
$code_smtp['5.7.0']='5.7.0 Other or undefined security status';
$code_smtp['5.7.1']='5.7.1 Delivery not authorized, message refused';
$code_smtp['5.7.2']='5.7.2 Mailing list expansion prohibited';
$code_smtp['5.7.3']='5.7.3 Security conversion required but not possible';
$code_smtp['5.7.4']='5.7.4 Security features not supported';
$code_smtp['5.7.5']='5.7.5 Cryptographic failure';
$code_smtp['5.7.6']='5.7.6 Cryptographic algorithm not supported';
$code_smtp['5.7.7']='5.7.7 Message integrity failure';

		
		if ($code=='get')
		{
			return $code_smtp;
		}else{
			return $code_smtp[$code];
		}
		
	}
	
	function rumus_code_smtp_2($code='')
	{
		
		$code_smtp=array();
		#$code_smtp['200']='200 nonstandard success response';
		#$code_smtp['211']='211 System status, or system help reply';
		#$code_smtp['214']='214 Help message';
		#$code_smtp['220']='220 <domain> Service ready';
		#$code_smtp['221']='221 <domain> Service closing transmission channel';
		#$code_smtp['250']='250 Requested mail action okay, completed';
		#$code_smtp['251']='251 User not local; will forward to <forward-path>';
		#$code_smtp['252']='252 Cannot VRFY user, but will accept message and attempt delivery';
		#$code_smtp['354']='354 Start mail input; end with';
		#$code_smtp['421']='421 <domain> Service not available, closing transmission channel';
		
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
		
		$code_smtp['541']='541 Your message has been detected and labeled as spam';
		
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
		
		$key_smtp['/554 (.+)delivery/i']				='554';  
		$key_smtp['/554(.+)delivery/i']					='554'; 
		
		$key_smtp['/not(.+)exist/i']					='550';  
		$key_smtp['/not (.+)exist/i']					='550'; 
		
		
		$key_smtp['/poor(.+)reputation/i']				='541';
		$key_smtp['/poor (.+)reputation/i']				='541';
		
		$key_smtp['/blocked(.+) /i']					='541';
		$key_smtp['/blocked/i']							='541';
		
		$key_smtp['/spam(.+) /i']					='541';
		$key_smtp['/spam/i']							='541';
		
		$key_smtp['/block(.+)list/i']				='541';
		$key_smtp['/block (.+)list/i']				='541';
		
		
		$key_smtp['/reach is(.+)disabled/i']				='554';
		$key_smtp['/reach is (.+)disabled/i']				='554';
		
		$key_smtp['/mailbox is(.+)full/i']				='552';
		$key_smtp['/is(.+)full/i']						='552';
		$key_smtp['/is (.+)full/i']						='552';	

		$key_smtp['/host(.+)name/i']					='101';
		$key_smtp['/host (.+)name/i']					='101';
		$key_smtp['/tidak dapat(.+)ditemukan/i']		='550';
		$key_smtp['/tidak dapat (.+)ditemukan/i']		='550';
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
		$key_smtp['/rejected(.+) /i']			='530';
		$key_smtp['/rejected/i']				='530';
		$key_smtp['/not(.+)found/i']					='550';
		$key_smtp['/not (.+)found/i']					='550';
		$key_smtp['/wasnt(.+)found/i']					='550';
		$key_smtp['/wasnt (.+)found/i']					='550';//couldnt be found
		$key_smtp['/couldnt be(.+)found/i']				='550';
		$key_smtp['/couldnt be (.+)found/i']			='550';
		$key_smtp['/couldnt(.+)be found/i']				='550';
		$key_smtp['/couldnt (.+)be found/i']			='550';
		$key_smtp['/may not (.+)exist/i']				='550';
		$key_smtp['/may not(.+)exist/i']				='550';
		$key_smtp['/couldnt(.+)find/i']					='104';
		$key_smtp['/couldnt (.+)find/i']				='104';
		$key_smtp['/couldn(.+)find/i']					='104';
		$key_smtp['/couldn (.+)find/i']					='104';
		$key_smtp['/not(.+)exist/i']					='550';  
		$key_smtp['/not (.+)exist/i']					='550';  
		$key_smtp['/confidential(.+)information/i']		='530';  
		$key_smtp['/confidential (.+)information/i']	='530';  
		$key_smtp['/quota(.+)exceeded/i']				='552';
		$key_smtp['/quota (.+)exceeded/i']				='552';
		#$key_smtp['/ (.+)failed/i']						='530'; 
		
		$key_smtp['/ (.+)disabled/i']					='554'; 
		
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