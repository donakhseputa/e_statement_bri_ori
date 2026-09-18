<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php");  ?>

<?
	$pr = trim($_POST["pr"]);
	$arr_pr = explode("|", $pr);

	$flagtrans = trim($arr_pr[0]);
	$blth = trim($arr_pr[2]);
	
	$con = pg_connect($connection) or die("Could not connect to database!");
	
	
	$sql = "SELECT DISTINCT nama_file";
	$sql .= " FROM m_customer";
	$sql .= " WHERE flagtrans = '$flagtrans' AND blth = '$blth'";
	$sql .= " ORDER BY nama_file ASC";
	//die($sql);
	$qry = pg_query($con, $sql) or die("Invalid query!");
	
?>
<select name="cycle" class="combobox" onchange="ChangeCycle(this.value);">
	<option value="Pilih" selected="selected">- Pilih -</option>
	<? while ($row = pg_fetch_assoc($qry)) { ?>
	<option value="<?= $row["nama_file"] ?>"><?= $row["nama_file"] ?></option>
	<? } ?>
</select>
|	
<?
  if($blth == "Pilih"){ die(""); }
	
	$sql = "SELECT DISTINCT nama_file,jumlah_records,\"user\",waktu,demanifest,kemsip";
	$sql .= " FROM log_import_dc";
	$sql .= " WHERE flagtrans = '$flagtrans' AND blth = '$blth'";
	$sql .= " ORDER BY nama_file ASC";
	//die($sql);
	$qry = pg_query($con, $sql) or die("Invalid query!");
	$jumlah = pg_num_rows($qry);
	
?>
<table width="99%" cellpadding="2" cellspacing="2">
  <tr class="table_header">
    <td width="42%" height="25" align="center" valign="middle">Nama File (imported file)    </td>
    <td width="17%" align="center" valign="middle">Jumlah Record </td>
    <td width="14%" align="center" valign="middle">Demanifest </td>
    <td width="9%" align="center" valign="middle">Kemsip </td>
    <td width="8%" align="center" valign="middle">User </td>
    <td width="10%" align="center" valign="middle">Waktu</td>
  </tr>
<?	if($jumlah > 0){
 		while ($row = pg_fetch_assoc($qry)) { ?>
  
  <tr>
    <td align="left" valign="middle" bgcolor="#FFFFFF">&nbsp;<?= $row["nama_file"] ?></td>
    <td align="center" valign="middle" bgcolor="#FFFFFF"><?= $row["jumlah_records"] ?></td>
    <td align="center" valign="middle" bgcolor="#FFFFFF"><?= $row["demanifest"] ?></td>
    <td align="center" valign="middle" bgcolor="#FFFFFF"><?= $row["kemsip"] ?></td>
    <td align="center" valign="middle" bgcolor="#FFFFFF"><?= $row["user"] ?></td>
    <td align="center" valign="middle" bgcolor="#FFFFFF"><?= $row["waktu"] ?></td>
  </tr>
<? 		}
	}else{
?>
  <tr>
    <td height="25" align="center" valign="middle" colspan="6" bgcolor="#FFFFFF"><?= $blth ?> - No Data</td>
  </tr>
<?
	}	
?>
</table>
<?
	pg_close($con);
	
?>
