<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php");  ?>
<?


	$pr = trim($_POST["pr"]);
	$arr_pr = explode("|", $pr);

	$flagtrans = trim($arr_pr[0]);
	$menuid = trim($arr_pr[1]);
	$blth = trim($_POST["blth"]);
	$ket = trim($_POST["ket"]);

	$con = pg_connect($connection) or die("Could not connect to database!");


	$sql = "SELECT COUNT(*) AS jumlah";
	$sql .= " FROM m_attach_file";
	$sql .= " WHERE blth = '$blth' and flagtrans = '$flagtrans' and name_file = '" . $_FILES["dbf_file"]["name"] . "'";
	$qry = pg_query($con, $sql) or die("Invalid query!");

     $lokasi_blth = '../attach_file/' . $blth.'/' ;
	 $lokasi = '../attach_file/' . $blth .'/'. $flagtrans .'/' ;

	if ($row = pg_fetch_assoc($qry)) {
		if ($row["jumlah"] == 0) {
		
			if (!file_exists($lokasi_blth)) 
			{
				mkdir($lokasi_blth, 0777);
			}
			
			if (!file_exists($lokasi)) 
			{
				mkdir($lokasi, 0777);
			}
			
			if (move_uploaded_file($_FILES["dbf_file"]["tmp_name"], $lokasi.$_FILES["dbf_file"]["name"])) 
				  {
					chmod("$lokasi" .$_FILES["dbf_file"]["name"], 0777);
							$error = 0;
						} else {
							$error = 2;
						}
			if($error==0)
			{

		$ukuran = ($_FILES["dbf_file"]['size']);
			$con = pg_connect($connection);

			$q =" INSERT INTO m_attach_file "; 
			$q .=" (blth,flagtrans,status,ukuran, location_file,keterangan,userid,modify_date,name_file) values ";
			$q .=" ('$blth','$flagtrans', 't', $ukuran,'$lokasi', '$ket','".$_SESSION['userid']."',now(), '".$_FILES["dbf_file"]["name"]."')"; 
			$p=pg_query($con,$q);
			header("Location: index_loading_attach_file.php?pr=$flagtrans|$menuid|sukses");
			
			}
				
		}
		
			else {
					$error = 1;
				}
		
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?= $title ?></title>

</head>

<body>
<table id="container" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td align="center" valign="top">
<? require_once("../include/header.php"); ?>
<? require_once("../include/menu.php"); ?>
<? require_once("../include/greeting.php"); ?>
		</td>
	</tr>
	<tr>
		<td height="500" align="center" valign="top">
			<table width="900" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td align="left" valign="top">
<?
	$back = "index_loading_attach_file.php?pr=$flagtrans|$menuid|'back'|$blth";
	$title_level = 3;
?>
<? require_once("../include/page_title.php"); ?></td>
				</tr>
				<tr>
					<td align="left" valign="top">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<? if ($error == 0) { ?>
							<tr>
								<td height="30" align="center" valign="middle">
									<div id="div_image">
										<img src="../images/ajax.gif" alt="loading..." border="0" />
										<img src="../images/ajax.gif" alt="loading..." border="0" />
										<img src="../images/ajax.gif" alt="loading..." border="0" />
										<img src="../images/ajax.gif" alt="loading..." border="0" />
										<img src="../images/ajax.gif" alt="loading..." border="0" />
									</div>
								</td>
							</tr>
							<? } ?>
							<tr>
								<td width="100%" height="30" align="center" valign="middle">
									<div id="div_message">
<?
	switch ($error) {
		case 0:
				echo("-- File telah berhasil diupload dengan sukses --<br />");
						break;
		case 1:
			echo("-- PERHATIAN !!! --<br /><br />");
			echo("-- File ini sudah pernah diupload --");
			break;
		case 2:
			echo("-- PERHATIAN !!! --<br /><br />");
			echo("-- File ini gagal diupload ke server --");
			break;
		case 3:
			echo("-- PERHATIAN !!! --<br /><br />");
			echo("-- File yang diupload bukan berekstensi *.DBF --");
			break;
	}
?>
									</div>
								</td>
							</tr>
							<tr>
								<td height="20">&nbsp;</td>
							</tr>
							<tr>
								<td height="40" align="center" valign="middle">
									<div id="div_button"></div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="center" valign="top">
<? require_once("../include/footer.php"); ?>
		</td>
	</tr>
</table>
<? if ($error == 0) { ?>
<script type="text/javascript">
	Import();
</script>
<? } ?>
</body>
</html>
<?
	pg_close($con);
	

?>
