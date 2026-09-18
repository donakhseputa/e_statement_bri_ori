<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<?
	$con = pg_connect($connection) or die("Could not connect to database!");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?= $title ?></title>
<? require_once("../include/script.php"); ?>
<? require_once("../include/message.php"); ?>
</head>

<body>
<table id="container" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td align="center" valign="top">
<? require_once("../include/header.php"); ?>
<? require_once("../include/menu.php"); ?>
<? require_once("../include/greeting.php"); 
$pr=trim($_GET["pr"]);
$arr_pr=explode("|", $pr);
$product_id=trim($arr_pr[0]);
?>
		</td>
	</tr>
	<tr>
		<td height="227" align="center" valign="top">
			<table width="900" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="2" align="left" valign="top">
<?
	$page_title = "HOME" .  $title;
?>
<? require_once("../include/page_title.php"); ?>
<?php require_once("../../cek_free_space/cek_space.php"); ?>
					</td>
				</tr>
         <? if($product_id!=""){?>       
				<tr>
					<td height="25" colspan="2" align="left" valign="middle" class="subtitle">
						Silahkan pilih salah satu menu untuk melanjutkan proses :
					</td>
				</tr>
				<tr>
					<td height="25" colspan="2" align="left" valign="middle" class="subtitle">&nbsp;
                    	
					</td>
				</tr>
                <?php
				$sql_menugroup = "SELECT DISTINCT mg.menugroupid, mg.menugroup, mg.urutan
									FROM menugroup1 mg
										INNER JOIN (select distinct m.menugroupid, um.userid, m.flagtrans
													from menu1 m
														inner join usermenu1 um
															on m.menuid = um.menuid where m.status='t') mn_g
											ON mg.menugroupid = mn_g.menugroupid
									WHERE upper(mn_g.userid) = upper('".$_SESSION["user_id"]."') and mn_g.flagtrans = '$product_id'
									ORDER BY mg.urutan";
				$qry_menugroup = pg_query($sql_menugroup) or die('ERROR menugroup1: '.$sql_menugroup);
				while($row_menugroup=pg_fetch_array($qry_menugroup)){
					?>
                    <tr>
                        <td height="25" colspan="2" align="left" valign="middle" class="subtitle">
                            <?=$row_menugroup['menugroup']?> :
                        </td>
                    </tr>
                    <?php
					$sql_menu = "SELECT DISTINCT a.menuid, a.menu, a.flagtrans, a.lokasi, a.status, a.urutan
								FROM menu1 a
									INNER JOIN usermenu1 b
										ON a.menuid = b.menuid
								WHERE upper(b.userid) = upper('".$_SESSION["user_id"]."') and a.status='t' and a.flagtrans = '$product_id' and a.menugroupid = ".$row_menugroup['menugroupid']."
								ORDER BY a.urutan";
					$qry_menu = pg_query($sql_menu) or die('ERROR menu: '.$sql_menu);
					$no_urut = 1;
					while($row_menu=pg_fetch_array($qry_menu)){
					?>
                    <tr>
                        <td colspan="2" align="left" valign="top">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="30" height="20" align="center" valign="middle"> <? echo $no_urut ."." ?> </td>
                                    <td width="870" align="left" valign="middle">
                                    	<?php
										if($row_menu['status']=='t'){
											?>
											<a href="<?=$row_menu['lokasi'].'?pr='.$row_menu['flagtrans'].'|'.$row_menu['menuid']?>"><?=$row_menu["menu"]?></a>
                                            <?php
										}else{
											?>
											<a href="../underconstruction/index.php"><?=$row_menu["menu"]?></a>
                                            <?php
										}
										$no_urut++;
											?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <?php
					}
				}
			} else {?>
                <tr>
					<td width="525" height="25" align="left" valign="middle" class="subtitle">
					  Silahkan pilih salah satu produk untuk melanjutkan proses :			        </td>
					<td width="375" align="left" valign="middle" class="subtitle">
					
                    
                    
				<tr>
					<td height="25" colspan="2" align="left" valign="middle" class="subtitle">
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
<?
	$sql = "SELECT distinct a.flagtrans,a.produk
	FROM mproduk a,usermenu1 b,menu1 c
  WHERE a.status ='t' and upper(b.userid) = upper('" . $_SESSION["userid"] . "') AND b.menuid =c.menuid   AND c.flagtrans=a.flagtrans AND SUBSTR(akses, 1, 1) = '1' order by produk desc";
	$qry = pg_query($con, $sql) or die("Invalid query!");
$no_urut = 1;
	while ($row = pg_fetch_assoc($qry)) {
		
?>
							<tr>
								<td width="30" height="20" align="center" valign="middle"><? echo $no_urut ."." ?></td>
								<td width="870" align="left" valign="middle">
									<a href="?pr=<?= $row["flagtrans"] ?>"><?= $row["produk"] ?></a>
								</td>
							</tr>
<?
	$no_urut ++;	
	}
?>
						</table>
					</td>
				</tr>
                
				<? 
				
				}
				
				?>
			</table>
		</td>
	</tr>
	<tr>
		<td align="center" valign="top">
<? require_once("../include/list_belum_terkirim.php"); ?>
<? require_once("../include/footer.php"); ?>
	  </td>
  </tr>
</table>
</body>
</html>
<?
	pg_close($con);
?>
