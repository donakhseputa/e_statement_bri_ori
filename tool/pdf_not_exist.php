<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/function.php"); ?>
<?php

$con = pg_connect($connection) or die('Could not connect to database!');

$x=array();
$tabeldetail = 'detail_022017_BC';
$sql_show = "SELECT * FROM $tabeldetail WHERE m_loading_id = '1098' order by detail_id desc";
$qry_show = pg_query($sql_show) or die('ERROR show: '.$sql_show);
			
while($row_show=pg_fetch_array($qry_show))
{
			$nomor_rekening = $row_show['nomor_rekening'];
			$nama = $row_show['nama'];
			$alamat1 = $row_show['alamat1'];
			$pdf_name = $row_show['pdf_name'].'.pdf';
			$password_pdf = $row_show['password_pdf'];
			$jml_hlm = $row_show['jml_hlm'];
			$zipcode = $row_show['zipcode'];
			$file_induk = $row_show['nama_file'];
			$lokasi_file = "../pdf/BC/".$row_show['blth'] ."/". substr($row_show['nama_file'],0,strlen($row_show['nama_file'])-4);
			//die($lokasi_file);
			
	if(!file_exists($lokasi_file ."/". $pdf_name))
	{
		
		$x[$nomor_rekening]=$lokasi_file ."/". $pdf_name;
	}
	
}
echo "JUMLAH PDF YANG BELUM ADA: ". count($x);
echo "<br>";
echo "<pre>";

die(print_r($x));

echo "</pre>";

pg_close($con);
?>