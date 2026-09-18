<?php
//die("Maintenance Sementara");
	session_cache_expire(60);
	session_name("PHPSESSID");
	session_start();
ini_set( 'memory_limit', '128M' );
	$title = "..:: E-STATEMENT BRI ::..";
		$connection = "host=127.0.0.1 port=5433 dbname=estatement_bri user=postgres password=postgres";
		// $connection = "host=127.0.0.1 port=5432 dbname=e_statement_bri_old user=postgres password=Indomedia123";
			#$connection = "host=192.168.101.14 port=5432 dbname=e_statement_bri_i_am_sorry user=postgres password=123456";$connection = "host=127.0.0.1 port=5433 dbname=estatement_bri user=postgres password=postgres";


	date_default_timezone_set('Asia/Jakarta');
	
	
	//$connection_sid = "host=192.168.2.10 port=5432 dbname=bri user=app password=app123"; //tambahan 20170510
	
	
	$GLOBALS['sk_tr'] 						= 202004;
	
	
	$send_email = false;
	$row_user = 20;
	$row_customer = 20;
	$client = 'BRI';
	$pesan = 'bri_msg';
	define('BLTH_BARU',"201409");	//live: 201409, testing: 201408
	
	$url_directory = "C:\\app\\htdocs\\e_statement_bri";
	//$url_directory = "D:\\htdocs\\e_statement_bri";
	$pdftk_loc = "C:\\pdftk\\bin\\pdftk.exe";
	
	$min_schedule = "10.00";
	$max_schedule = "20.00";
	
	function userAcces($con,$val){
		$connect=pg_connect($con);
		$sql="SELECT akses FROM usermenu1 WHERE menuid='$val'";
		$query=pg_query($connect,$sql);
		list($akses)=pg_fetch_array($query);
		return $akses;
	}
?>
