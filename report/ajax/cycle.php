<? require_once("../../include/config.php"); ?>
<? require_once("../../include/session.php"); ?>
<? require_once("../../include/function.php"); ?>
<?
	$con = pg_connect($connection) or die("Could not connect to database!");
	$pr = trim($_GET["pr"]);
	$arr_pr = explode("|", $pr);

	$flagtrans = trim($arr_pr[0]);
	$blth = trim($arr_pr[1]);
	$cycle = trim($arr_pr[2]);

	$blth_balik = substr($blth,-4).substr($blth,0,2);
		
			$BLTH_BARUS = BLTH_BARU;
			if($blth_balik>$BLTH_BARUS){
				$tabeldetail = "detail_" . $blth."_".$flagtrans;
				$sql_cek = "SELECT a.m_loading_id FROM m_loading a LEFT JOIN $tabeldetail b ON a.m_loading_id=b.m_loading_id 
				WHERE a.blth='$blth' AND a.flagtrans='$flagtrans'";
				$exe_cek = @pg_query($sql_cek);
				if($exe_cek){
					$tabeldetail = "detail_" . $blth."_".$flagtrans;
				}else{
					$tabeldetail = "detail";
				}
			}else{
				$tabeldetail = "detail";
			}
			
	$sql = "SELECT count(detail_id) AS jumlah";
	$sql .= " FROM $tabeldetail";
	$sql .= " WHERE flagtrans = '$flagtrans' AND blth = '$blth' AND nama_file = '$cycle'";
	$qry = pg_query($con, $sql) or die("Invalid query!");

	if ($row = pg_fetch_assoc($qry)) {
		$jumlah = $row["jumlah"];
	}

	echo(number_format($jumlah, 0, ",", "."));

	pg_close($con);
?>
