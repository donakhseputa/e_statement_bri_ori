<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/function.php"); ?>
<?
	$pr = trim($_GET["pr"]);
	$arr_pr = explode("|", $pr);

	$flagtrans = trim($arr_pr[0]);
	$menu_id = (int)trim($arr_pr[1]);
	
	$con = pg_connect($connection) or die("Could not connect to database!");

	//$akses = GetAccess($con_client, $menuid);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?= $title ?></title>

<? require_once("../include/script.php"); ?>
<? require_once("../include/message.php"); ?>

<script type="text/javascript">

function delete_data(val)
{
	//alert(val);
	$("#listdata").load("../ecash/proses_m_vendor_edc.php?act=listdata&start=0&page=1&no=1&stat=1");
}



function ChangeBLTH(val){
		var obj = document.form_data;
		var xmlHttp = GetXmlHttpObject();
		var url = "ajax_blth.php";
		var par = "pr=<?= $pr ?>|" + val + "&mr=" + Math.random();
		//alert(val);
		if (!xmlHttp) {
			return;
		}
		xmlHttp.onreadystatechange = function() {
			var arrResponseText;			
			if (xmlHttp.readyState == 4) {
				arrXmlHttp = xmlHttp.responseText.split("|");
				document.getElementById("div_cycle").innerHTML = arrXmlHttp[0];
				document.getElementById("div_keterangan").innerHTML = arrXmlHttp[1];
			} else {
				document.getElementById("div_cycle").innerHTML = "<img src=\"../../general/images/ajax.gif\" alt=\"loading...\" border=\"0\" />";
				document.getElementById("div_keterangan").innerHTML = "<img src=\"../../general/images/ajax.gif\" alt=\"loading...\" border=\"0\" />";
			}
		}
		xmlHttp.open("POST", url, true);
		xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlHttp.setRequestHeader("Content-length", par.length);
		xmlHttp.setRequestHeader("Connection", "close");
		xmlHttp.send(par);
	}
	function CheckForm() {
		var obj = document.form_data;

		if(obj.blth.value == "Pilih"){
			alert("Masukkan BLTH");
			obj.blth.focus();
			return false;				
		}		
		
          
        else if(obj.dbf_file.value == ""){		
			alert("Masukkan File");
			obj.dbf_file.focus();
			return false;					
		}
		else {
			return true;
		}
	}
</script>
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
                    	<? require_once("../include/page_title.php"); ?>
					</td>
				</tr>
				<tr>
					<td align="left" valign="top">
						<form action="proses_loading_attach_file.php" method="post" enctype="multipart/form-data" name="form_data" onsubmit="return CheckForm();">
						<input name="pr" type="hidden" value="<?= $pr ?>" />
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td width="100%" align="center" valign="top">
									<table width="100%" border="0" cellspacing="2" cellpadding="2">
										<tr>
										  <td width="20%" height="30" align="left" valign="middle">Periode (BLTH)</td>
										  <td width="80%" align="left" valign="middle">
                                          
                                          <select name="blth" class="combobox" onchange="ChangeBLTH(this.value);">
                                                                                        
                                            <option value="Pilih">- Pilih -</option>
                                            <?
	$sql = "SELECT DISTINCT blth, SUBSTRING(blth, 3, 4) || SUBSTRING(blth, 1, 2) AS blth2";
	$sql .= " FROM m_customer";
	$sql .= " WHERE flagtrans = '$flagtrans'";
	$sql .= " ORDER BY blth2 ASC";
//echo $sql;
	$qry = pg_query($con, $sql) or die("Invalid query! 1");

	while ($row = pg_fetch_assoc($qry)) {
?>
                                            <option value="<?= $row["blth"] ?>"<? if ($row["blth"] == $blth) { echo(" selected=\"selected\""); } ?>>
                                              <?= $row["blth"] ?>
                                            </option>
                                            <?
	}
?>
<option value="ALL">- ALL -</option>
                                          </select> 
										  <!-- UNTUK EMBED, TANDAI PILIHAN INI -->
										  <input type="checkbox" id="cek_embed" name="cek_embed" value="1" style="">
											<label style="font-size:12px;font-family:verdana;" for="cek_embed">Embed Image</label>
										  <!-- END UNTUK EMBED, TANDAI PILIHAN INI -->
										  </td>
									  </tr>
										<tr>
                                          <td height="30" align="left" valign="middle">Keterangan</td>
										  <td align="left" valign="middle"><textarea name="ket" id="ket" cols="100" rows="5"></textarea></td>
									  </tr>
										<tr>
											<td height="30" colspan="2" align="center" valign="middle">
												Silahkan memasukkan file  di bawah ini atau klik &quot;Browse&quot; untuk mencari file :											</td>
										</tr>
										<tr>
											<td height="30" colspan="2" align="center" valign="middle">
												<input name="dbf_file" type="file" class="textbox_4" size="80" />											</td>
										</tr>
                                        <tr>
											<td height="30" colspan="2" align="center" valign="middle"><label style="font-size:11px;">* untuk nama file, tidak boleh lebih dari 100 karakter dan jangan menggunakan tanda  ' atau &quot;</label></td>
										</tr>
										<tr>
											<td height="40" colspan="2" align="center" valign="middle">
												<input name="upload" type="submit" class="button" value="UPLOAD" />											</td>
										</tr>
										<tr>
											<td height="30" colspan="2" align="center" valign="middle">&nbsp;</td>
										</tr>
									</table>
							  </td>
							</tr>
						</table>
						</form>
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td width="100%" align="center" valign="top">
									<table width="100%" border="0" cellspacing="2" cellpadding="2" bgcolor="#FFFFFF">
										<tr class="table_header">
											<td width="3%" height="30" align="center" valign="middle">
												No
											</td>
											<td width="4%" align="center" valign="middle">BLTH</td>
											<td width="17%" align="center" valign="middle">Nama File </td>
											<td width="32%" align="center" valign="middle">Keterangan</td>
											<td width="9%" align="center" valign="middle">Size (Kb)</td>
											<td width="9%" align="center" valign="middle">Embed Code</td>
											<td width="15%" align="center" valign="middle">User</td>
											<td width="20%" align="center" valign="middle">Waktu</td>
										</tr>
<?


$dataPerPage = 3;

	$arr_pg = explode("|", $pr);
	$flag = trim($arr_pg[0]);
	$menu = trim($arr_pg[1]);
	$page = trim($arr_pg[2]);


		if($page <> "")
		
		{
		
			$noPage = $page;
			$flag = $flag;
			$menu = $menu;
			
		} 
		else 
		{
			$noPage = 1;
			$flag = $flagtrans;
			$menu = $menuid;
			$i = 1;
		}

		if ($noPage == 1)
		{ 
			$i = 1;
		}
		else	
		{
			$i = ($dataPerPage * $noPage)- ($dataPerPage-1);
		}


    $sql= "select count(*) as jumlah from m_attach_file";
   $sql .= " WHERE flagtrans = '$flag'";
	//echo $sql; 
	$qry = pg_query($con, $sql) or die("Invalid query! 2");
	while ($rowjum = pg_fetch_assoc($qry)) 
		{
			$jumData = $rowjum["jumlah"] ;
		}
echo "Total " .$jumData;
	$offset = ($noPage - 1) * $dataPerPage;

	
	$sql = "SELECT *";
	$sql .= " FROM m_attach_file";
	$sql .= " WHERE flagtrans = '$flag'";
	$sql .= " ORDER BY m_attach_file_id::integer DESC";
	$sql .= " LIMIT $dataPerPage";
	$sql .= " OFFSET $offset";
	$qry = pg_query($con, $sql) or die("Invalid query! 3 =" . $sql);
//echo $sql;
	while ($row = pg_fetch_assoc($qry)) 
	{
	if($row['cid']<>''){
	$embedcode = "cid:" . $row['cid'];
	} else $embedcode = "";
?>
										<tr class="table_row_odd">
											<td height="30" align="center" valign="middle">
												<?php echo $i ?>
											</td>
											<td height="30" align="center" valign="middle"><?php echo $row["blth"] ?></td>
											<td align="left" valign="middle">
										 <?php echo "<a href= '".$row['location_file'] . $row["name_file"].  " ' >". $row['name_file'] ."</a>"; ?>

                                        </td>
											<td align="left" valign="middle">
												<?php echo $row["keterangan"] ?>
											</td>
											<td align="right" valign="middle"><?php echo number_format($row["ukuran"] /1024) . "&nbsp" ?> </td>
											<td align="center" valign="middle"><?php echo $embedcode; ?> </td>
											<td align="left" valign="middle"><?php echo $row["userid"] ?></td>
											<td align="left" valign="middle"><?php echo date('Y-m-d H:i',strtotime($row['modify_date']))?></td>
										</tr>
<?php
	$i++;
	}
?>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td align="center">
                    <? 
				
					$jumPage = ceil($jumData/$dataPerPage);
				//	echo $jumPage;
			
	    			if ($noPage > 1) 
					{
					echo  "<a href='".$_SERVER['PHP_SELF']."?pr=".$flag."|".$menuid."|".($noPage-1)."'>&lt;&lt; Prev</a>";
					
					}
			
					for($page = 1; $page <= $jumPage; $page++)
					{
						 if ((($page >= $noPage - 3) && ($page <= $noPage + 3)) || ($page == 1) || ($page == $jumPage)) 
						 {   
							if (($showPage == 1) && ($page != 2))  echo "..."; 
							if (($showPage != ($jumPage - 1)) && ($page == $jumPage))  echo "...";
							if ($page == $noPage) echo " <b>".$page."</b> ";
		
							else echo " <a href='".$_SERVER['PHP_SELF']."?pr=".$flag."|".$menuid."|".$page."'>".$page."</a> ";							
							$showPage = $page;          
						 }
					}

// menampilkan link next


			if ($noPage < $jumPage) echo "<a href='".$_SERVER['PHP_SELF']."?pr=".$flag."|".$menuid."|".($noPage+1)."'>Next &gt;&gt;</a>";
		
					
					
					
					echo $jumlah ?>
                    </td>
				</tr>
			</table>
			<form id="form1" name="form1" width = "900" method="post" action="">
	    </form></td>
	</tr>
	<tr>
		<td align="center" valign="top">
<? require_once("../include/footer.php"); ?>
		</td>
	</tr>
</table>
<script type="text/javascript">
	var obj = document.form_data;
	obj.blth.focus();
</script>
</body>
</html>
<?
function hapus_data(){
	    $data_id = $_REQUEST['m_attach_file_id'];
		
		$sql_del = "DELETE FROM m_attach_file WHERE m_attach_file_id = ".$data_id."";
		$qry_del = pg_query($sql_del)or die("Error delete data : $sql");
		?>
        <script>
		//	show_antrian();
		</script>
        <?php
	}
?>
<?
	pg_close($con);
?>