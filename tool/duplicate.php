<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/message.php"); ?>
<? require_once("../include/function.php"); ?>

<?php
	
	die("jangan digunakan");
	$con		= pg_connect($connection) or die("Could not connect to database!");
	
	
	$sql = "
select nomor_rekening, email, MIN(tr_email_id) as tr_email_id, count(*) from tr_email_062020_bc
where loading_id = 2141 
--AND email='01IRFANM@GMAIL.COM' AND nomor_rekening='5188560259976203' 
group by nomor_rekening, email
having count(*) > 1
order by count(*) DESC";
	$query = pg_query($sql) or die('ERROR select attach_file: '.$sql);
	while($row=pg_fetch_array($query))
	{
		$nomor_rekening 		= $row['nomor_rekening'];
		$email 					= $row['email'];
		$tr_email_id 			= $row['tr_email_id'];
		
		
		
		$sql2 = "UPDATE tr_email_062020_bc
				   SET loading_id = 99998
				 WHERE loading_id = 2141 
				AND email='".$email."' AND nomor_rekening='".$nomor_rekening."' AND tr_email_id NOT IN (".$tr_email_id.");
				";
		
		pg_query($sql2);
		
	}
				
				
	
?>