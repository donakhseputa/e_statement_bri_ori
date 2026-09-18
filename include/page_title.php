<?php
	(isset($menu_id))?$menu_id=$menu_id:$menu_id=0;
	
	if($product_id!='' && $menu_id!=0){
		$sql = "SELECT a.menugroupid,a.menu,a.flagtrans,b.produk,c.menugroup ";
		$sql .= " FROM menu1 a,mproduk b,menugroup1 c";
		$sql .= " WHERE a.menugroupid=c.menugroupid and a.flagtrans = b.flagtrans and a.menuid = '$menu_id'";
	}elseif($product_id=='' && $menu_id!=0){
		$sql = "SELECT a.menugroupid,a.menu,a.flagtrans,c.menugroup ";
		$sql .= " FROM menu1 a,menugroup1 c";
		$sql .= " WHERE a.menugroupid=c.menugroupid and a.menuid = '$menu_id'";
	}elseif($product_id=='' && $menu_id==0){
		$sql = "SELECT a.menugroupid,a.menu,a.flagtrans,c.menugroup ";
		$sql .= " FROM menu1 a,menugroup1 c";
		$sql .= " WHERE a.menugroupid=c.menugroupid and a.menuid = '$menu_id'";
	}elseif($product_id!='' && $menu_id==0){
		$sql = "SELECT b.produk ";
		$sql .= " FROM mproduk b";
		$sql .= " WHERE b.flagtrans = '$product_id'";
	}
	$qry = pg_query($con, $sql) or die("Invalid query!");
	
	if($row = pg_fetch_assoc($qry)){
		if(trim($row["flagtrans"])=='' && $menu_id!=0){
			$page_title = strtoupper($client). " &gt;&gt; ";
			$page_title .=strtoupper($row["menugroup"]) . " &gt;&gt; ";
			$page_title .=strtoupper($row["menu"]);
		}elseif(trim($row["flagtrans"])=='' && $menu_id==0){
			$page_title =strtoupper($row["produk"]);
		}else{
			$page_title = strtoupper($row["produk"]). " &gt;&gt; ";
			$page_title .=strtoupper($row["menugroup"]) . " &gt;&gt; ";
			$page_title .=strtoupper($row["menu"]);
		}
	}
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="10%" height="60" align="left" valign="middle">
			<a onclick="history.go(-1)"><img src="../images/back.gif" border="0" /></a>
		</td>
		<td width="90%" align="left" valign="middle" class="title">
			<?= $page_title ?>
		</td>
	</tr>
</table>
