<?php
	

$timezone="Asia/Jakarta";
date_default_timezone_set($timezone);
include $www_location."include/config.php";
$_SESSION['userid']='otomatis';


$con = pg_connect($connection) or die("Could not connect to database!");	
	
$x = array();
	
$sql="
select * from tr_email_072020_bc where nomor_rekening IN(
select nomor_rekening from tr_email_072020_bc 
where loading_id = 2247
group by nomor_rekening, email
having count(*) > 1
)
and loading_id = 2247
order by nomor_rekening DESC";

$query = pg_query($sql) or die("Invalid query! auto SOF" . $sql) ;
			$rows = pg_num_rows($query);
			while($row = pg_fetch_array($query))
			{				
				if (empty($x[$row['nomor_rekening']]))
				{
					$x[$row['nomor_rekening']]=$row['nomor_rekening'];
				}else{
					$sql2="
					UPDATE tr_email_072020_bc
					   SET loading_id=9123
					 WHERE loading_id = 2247 AND tr_email_id = ".$row['tr_email_id'].";

					";
					pg_query($sql2);
				}
			}

pg_close($con);