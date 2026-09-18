<?php 


$timezone="Asia/Jakarta";
date_default_timezone_set($timezone);
include "include/config.php";
$_SESSION['userid']='otomatis';

$con = pg_connect($connection) or die("Could not connect to database!");
	
	
			$m_loding_id = 2727;
			$tr_email = "tr_email_032021_bc";
			
			//$sql = "select * from $tr_email where loading_id = $m_loding_id and status_sample ='f' order by tr_email_id ASC";
			
				$sql ="
select a.nomor_rekening, a.tr_email_id from ( select * from $tr_email where loading_id = $m_loding_id
and status_sample ='f' order by tr_email_id ASC ) a
LEFT join ( select * from antrian_email where loading_id = $m_loding_id and status_sample ='f') b
ON a.nomor_rekening=b.nomor_rekening
WHERE b.antrian_id IS NOT NULL";

			die($sql);
			$query = pg_query($sql) or die("Invalid query!" . $sql) ;
			$rows = pg_num_rows($query);
			if ($rows > 0)
			{
				while($x_hasil = pg_fetch_array($query))
				{
					$nomor_rekening = $x_hasil['nomor_rekening'];
					$tr_email_id = $x_hasil['tr_email_id'];
					
					$sql0 = "select * from $tr_email where loading_id = $m_loding_id  AND nomor_rekening='$nomor_rekening'
						and status_sample ='f' order by tr_email_id ASC";
					$query0 = pg_query($sql0) or die("Invalid query!" . $sql0) ;
					$rows0 = pg_num_rows($query0);
					
					$sql2 = "select * from antrian_email where loading_id = $m_loding_id and nomor_rekening = '$nomor_rekening' and status_sample ='f' ";
					//die($sql2);
					$query2 = pg_query($sql2) or die("Invalid query!" . $sql2) ;
					$rows2 = pg_num_rows($query2);
					if ($rows2 > 0)
					{
						//die("tr_email_id : $tr_email_id, dengan norek $nomor_rekening ada di tr_email sebanyak $rows0 dan di antrian sebanyak $rows2");
						
						$sqlx = "UPDATE $tr_email
							   SET loading_id=9901
							 WHERE loading_id = $m_loding_id AND nomor_rekening = '$nomor_rekening';
							";
						//die($sqlx);
						pg_query($sqlx);
					}
					
					
				}
			}
				
		
		
?>