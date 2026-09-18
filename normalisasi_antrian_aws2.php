<?php 


$timezone="Asia/Jakarta";
date_default_timezone_set($timezone);
include "include/config.php";
$_SESSION['userid']='otomatis';

$con = pg_connect($connection) or die("Could not connect to database!");
	
	
			$m_loding_id = 3681;
			$tr_email = "tr_email_032022_bc";
			
			$sql = "select nomor_rekening, count(*) as jumlah from $tr_email where loading_id = $m_loding_id and status_sample ='f'
group by nomor_rekening
having count(*) > 1";
			die($sql);
			$query = pg_query($sql) or die("Invalid query!" . $sql) ;
			$rows = pg_num_rows($query);
			if ($rows > 0)
			{
				while($x_hasil = pg_fetch_array($query))
				{
					$nomor_rekening = $x_hasil['nomor_rekening'];
					
					
					
					$sql2 = "select * from $tr_email where loading_id = $m_loding_id  AND nomor_rekening='$nomor_rekening'
						and status_sample ='f' order by tr_email_id DESC LIMIT 1";
					//die($sql2);
					$query2 = pg_query($sql2) or die("Invalid query!" . $sql2) ;
					$rows2 = pg_num_rows($query2);
					if ($rows2 > 0)
					{
						//die("tr_email_id : $tr_email_id, dengan norek $nomor_rekening ada di tr_email sebanyak $rows0 dan di antrian sebanyak $rows2");
						$x_hasil2 = pg_fetch_array($query2);
						$tr_email_id2 = $x_hasil2['tr_email_id'];
						
						$sqlx = "UPDATE $tr_email
							   SET loading_id=9903
							 WHERE loading_id = $m_loding_id AND nomor_rekening = '$nomor_rekening' 
							 AND status_sample ='f' AND tr_email_id not in ($tr_email_id2);
							";
						//die($sqlx);
						pg_query($sqlx);
					}
					
					
				}
			}
				
		
		
?>