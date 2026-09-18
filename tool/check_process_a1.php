<?php



	require_once("../include/config.php");
	require_once("../include/session.php");
	require_once("../include/function.php");
	#if(is_file("../include/chr_symbol.php"))	require_once("../include/chr_symbol.php");
	date_default_timezone_set("Asia/Jakarta");
	//error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED | E_STRICT);
	
	ini_set('memory_limit', '-1');
	
	
	$con = pg_connect($connection) or die("Could not connect to database!");
	
	$act		= $_REQUEST['act'];
	
	switch($act){

		case 'check_process'			: check_process(); break;
		case 'check_sftp'				: check_sftp(); break;
		case 'info_ip_staging'			: info_ip_staging(); break;
		
	
	}
	
	
	
	function check_process()
	{
		
		$sql_cek_dat = "SELECT * FROM master_proses_after_ematerai where keterangan='off'";
		$qry_cek_dat = pg_query($sql_cek_dat) or die('ERROR cek m_loading: '.$sql_cek_dat);
		$jml = @pg_num_rows($qry_cek_dat);
		if($jml == 0){
			echo "1";
		}else{
			echo "0";
		}
		
	}
	
	
	function info_ip_staging()
	{
		
		$sql= "select ip from m_sftp_server_sumber where status='t' LIMIT 1";
		$qry = @pg_query($sql);;
		$row = @pg_fetch_assoc($qry);
		$ip_m_sftp_server_sumber = $row['ip'];
		
		
		$sql= "select ip from m_sftp_server_tujuan where status='t' LIMIT 1";
		$qry = @pg_query($sql);;
		$row = @pg_fetch_assoc($qry);
		$ip_m_sftp_server_tujuan = $row['ip'];
		
		
		if ( !empty($ip_m_sftp_server_sumber) && !empty($ip_m_sftp_server_tujuan) )
		{
			echo '<div class="alert alert-info">
				IP SFTP Source Server : <b>'.$ip_m_sftp_server_sumber.'</b><br>
				IP SFTP Destination Server : <b>'.$ip_m_sftp_server_tujuan.'</b>
				</div>';
		}else{
			echo '';
		}
	}
	
	
	
	function check_sftp()
	{
		
		$config=config('tujuan');

		
		// Connect to FTP Server
			$conn_id = @ftp_connect($config['ip'], $config['port'] );
			if($conn_id){
			// Login to FTP Server
				$login_result = @ftp_login($conn_id, $config['key_user'], $config['key_pass']);
				if($login_result) @ftp_pasv( $conn_id,true);
			}
			$ket = '';$ket_key = 'TUJUAN';
			if($tipe=='tujuan2'){
				$ket=' 2';$ket_key = 'TUJUAN2';
			}
			// Verify Log In Status
			if ((!$conn_id) || (!$login_result)) {
				echo 'LOGIN FTP FAILED';
			} else 
			{
				echo 'LOGIN FTP SUCCESS';
			}
			
			if ($conn_id) ftp_close($conn_id);
	}
	
	@pg_close($con);
?>