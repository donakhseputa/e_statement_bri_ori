<?php

//die ( date('m/d/Y', strtotime('8 Aug 2015')) );
//die ( date('m/d/Y') );
// Report all errors except E_NOTICE
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);



$timezone="Asia/Jakarta";
date_default_timezone_set($timezone);
include $www_location."include/config.php";
include $www_location."include/rumus_tipe_gagal_rev1.php";


$con = pg_connect($connection) or die("Could not connect to database!");

$tabelnya = 'antrian_email';
$pesan=array();
		$sql="
		select * from $tabelnya
where error_info IS NOT NULL
";
			#die ($sql);
			$query = pg_query($sql) or die("Invalid query! auto SOF" . $sql) ;
			$rows = pg_num_rows($query);
			while($row = pg_fetch_array($query))
			{				
				$x = explode('@', $row['ket_error']);
				$xx = substr($x[1],0,20);
				$y = explode(' ', $xx);
				$pesan[ $row['email'] ] = $y[0];
				
				$id_tabel=  $row['antrian_id'];
				$tipe_gagal=  get_tipe_gagal_antrian ( $row['error_info'],  $row['email']) ;
				
				
				$sql2 ="
				UPDATE $tabelnya 
				SET tipe_gagal_rev1 ='$tipe_gagal'
				WHERE antrian_id=$id_tabel";
				@pg_query($sql2);
			}
			
	echo "<pre>";
	die(print_r($pesan));
	echo "</pre>";		

pg_close($con);
?>