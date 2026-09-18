<?php
	

$timezone="Asia/Jakarta";
date_default_timezone_set($timezone);
include $www_location."include/config.php";
$_SESSION['userid']='otomatis';


$con = pg_connect($connection) or die("Could not connect to database!");	
	
$x = array();
	
$sql="
SELECT table_name
  FROM information_schema.tables
 WHERE table_schema='public'
   AND table_type='BASE TABLE';";

$query = pg_query($sql) or die("Invalid query! auto SOF" . $sql) ;
			$rows = pg_num_rows($query);
			while($row = pg_fetch_array($query))
			{				
				if (empty($x[$row['nomor_rekening']]))
				{
					$x[$row['nomor_rekening']]=$row['nomor_rekening'];
				}else{
					
				}
				
				
				$sql2="
					SELECT * FROM ".$row['table_name']."
					";
					$query2 =  pg_query($sql2);
					$rows2 = pg_num_rows($query2);
					
					$x[$row['table_name']]= $rows2;
			}

pg_close($con);

//echo "<pre>";
//die(print_r($x));

arsort($x);
$html = '<table border="1">';
$html .= "<tr>
		<td><b>NAMA TABLE</b></td>
		<td><b>COUNT</b></td>
		</tr>
		";

foreach ($x as $key => $value) {
    // $arr[3] will be updated with each value from $arr...
   $html .= "<tr>
		<td>$key</td>
		<td>".number_format($value, 0, ',', '.')."</td>
		</tr>
		";
}

$html .= '</table>';

echo $html;

