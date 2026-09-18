<?php

//die ( date('m/d/Y', strtotime('8 Aug 2015')) );
//die ( date('m/d/Y') );
// Report all errors except E_NOTICE
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);



$timezone="Asia/Jakarta";
date_default_timezone_set($timezone);
include $www_location."include/config.php";
include $www_location."include/rumus_tipe_gagal_rev1.php";
include $www_location."include/function.php";


$con = pg_connect($connection) or die("Could not connect to database!");

//$tabelnya = 'tr_email_122022_bc';
$tabel_tr_email = 'tr_email_'.GetBLTH(date('mY'),-1).'_bc';

$pesan=array();
			$sql="
					DELETE FROM _suppression_list_aws
			 WHERE lower(email) IN 
			 (
			 Select lower(email) as x FROM $tabel_tr_email  WHERE tipe_gagal like '%Suppression%' 
			 );
			";
			//die ($sql);
			$query = pg_query($sql) or die("ERROR DELETE" . $sql) ;
			
				

				$sql2 ="
				INSERT INTO _suppression_list_aws(
							 email, keterangan, waktu)
							Select lower(email) as a, tipe_gagal as b, now() FROM tr_email_022023_bc  WHERE tipe_gagal like '%Suppression%' 
				";
				$query = pg_query($sql2) or die("ERROR INSERT" . $sql2) ;
	
		if ($query) echo "berhasil";
		else echo "gagal";

pg_close($con);
?>