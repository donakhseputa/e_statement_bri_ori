<style type="text/css">
.tebal {
	font-weight: bold;
	text-align: left;
}
.besar {
	font-size: 18px;
	font-family: Arial, Helvetica, sans-serif;
	color: #F00;
	text-decoration: blink;
	text-align: center;
}
.blink2 {
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
}
</style>
<?php
	$sql_approval_antrian = "
		SELECT
		la.log_approval_id, ml.m_loading_id,ml.loading_file,ml.flagtrans,ml.blth,pr.produk,
		la.userid || ' - ' || to_char(la.create_date, 'dd/mm/yyyy hh24:mi') as proses,
		case
			when is_approval is true then approval_user || ' - ' || to_char(approval_date, 'dd/mm/yyyy hh24:mi')
			else '-'
			end as apr,
			case
				when is_check = '1' then check_userid || ' - ' || to_char(check_date, 'dd/mm/yyyy hh24:mi')
				else '-'
				end as cek,
		min(tgl_jadwal) as m_jadwal, max(tgl_jadwal)as max_jadwal, count(*) as jml_antrian
		FROM log_approval la
		INNER JOIN antrian_email ae ON la.log_approval_id = ae.log_approval_id
		LEFT JOIN m_loading ml ON ae.loading_id=ml.m_loading_id
		LEFT JOIN mproduk pr ON pr.flagtrans=ml.flagtrans
		LEFT JOIN m_jadwal mj ON ae.jadwal_id = mj.jadwal_id
		WHERE la.create_date between now()-interval'20 days' and now()
			and status_sample = 'f' 
			and ml.m_loading_id is not null
			GROUP BY la.log_approval_id, is_approval, approval_date,
			la.userid, la.create_date, approval_user, check_date, 
			note_check, check_userid, is_check,ml.m_loading_id,ml.loading_file,ml.flagtrans,ml.blth,pr.produk
				ORDER BY la.create_date DESC
	";
	$qry_approval_antrian = pg_query($con, $sql_approval_antrian) or die("Invalid query! " . $sql_approval_antrian);
	$no_urut = 1;
	if(pg_num_rows($qry_approval_antrian) > 0){
		?>
		<div align="center" class="besar">CEK DAN APPROVAL DI ANTRIAN</div>
		<table width="90%" border="1" style="font-family:helvetica;font-size:12px;border-collapse:collapse;" cellpadding="2" cellspacing="2">
			<tr bgcolor="#cdcdcd">
				<td class="tebal" width="5%">NO</td>
				<td width="25%" class="tebal">PRODUK</td>
				<td width="15%" align="center"><b>BLTH</b></td>
				<td width="20%" class="tebal">FILE</td>
				<td width="20%" align="center"><b>CEK / APPROVE</b></td>
				<td width="10%" align="right"><b>JUMLAH</b></td>
			</tr>
			<?php
			while ($row_antrian = pg_fetch_assoc($qry_approval_antrian)) 
			{
				if($no_urut % 2=='1'){$class='background-color:#ffffff;';}else{ $class='background-color:#ccddcc;';};
				
				$sql_menu = "SELECT menuid,lokasi FROM menu1 WHERE lower(lokasi) LIKE '%index_lihat_antrian%' AND flagtrans='".$row_antrian['flagtrans']."'";
				$exe_menu = @pg_query($sql_menu);
				$row_menu = pg_fetch_array($exe_menu);
				$menunya = $row_menu['lokasi']."?pr=".$row_antrian['flagtrans']."|".$row_menu['menuid']."|".$row_antrian ["blth"] . "|". $row_antrian['m_loading_id'];
				
				
				?>
				<tr style="<?php echo $class;?>" valign="top">
					<td width="6" valign="top"><b><?php echo $no_urut ?></b></td>
					<td valign="top" style="color:#34578B;font-family:helvetica;font-size:12px;"><a href="<?php echo $menunya;?>"><?php echo $row_antrian["produk"]."</a><br/><font style='font-family:arial;font-size:12px;color:black;font-weight:bold;'>".$row_antrian["proses"]."</font>"; ?></a></td>
					<td valign="top" align="center"><b><?php echo $row_antrian["blth"] ?></b></td>
					<td valign="top" style="color:#000000;font-family:helvetica;font-size:12px;font-weight:bold;">
					<a href="<?php echo $menunya;?>" style="color:black;"><?php echo  $row_antrian['loading_file'];?></a><br/><font style="font-family:arial;font-size:12px;color:black;font-weight:bold;">
					<?php echo "<font color='#960014'>Awal Kirim:</font> " . date("d/m/Y H:i", strtotime($row_antrian['m_jadwal']));?><br/>
					<?php echo "<font color='green'>Akhir Kirim: </font>" . date("d/m/Y H:i", strtotime($row_antrian['max_jadwal']));?>
					</font>
					</td>
					<td valign="top" align="center" style="color:#000000;font-family:helvetica;font-size:12px;font-weight:bold;">
					<a href="<?php echo $menunya;?>" style="color:black;">
					<?php echo "<font style='color:#960014'>cek: </font>" . $row_antrian["cek"] . "<br/><font style='color:green'>apr: </font>" . $row_antrian["apr"] ?></a></td>
					<td valign="top" style="color:#ee0026;font-weight:bold;" align="right"><a href="<?php echo $menunya;?>" style="color:#ee0026;"><?php echo number_format($row_antrian["jml_antrian"]) ?></a></td>
				</tr>
				<?php
				$no_urut++;	
			}
			?>
		</table>
		<br/>
        <?php
	}
	#$sql_belum_dikirim = "
	#	SELECT a.flagtrans, a.blth, a.total_customer, loading_file, a.create_date, b.produk, a.userid
	#	FROM m_loading a
	#	INNER JOIN mproduk b on a.flagtrans = b.flagtrans
	#	WHERE
	#		m_loading_id not in (select distinct (loading_id) from tr_email where status_sample = 'f')
	#		and a.create_date between now()-interval'90 days' and now()
	#		and total_customer > 0
	#	ORDER BY m_loading_id DESC, produk DESC;
	#";
	$sql_belum_dikirim = "
		SELECT a.flagtrans, a.blth, a.total_customer, loading_file, a.create_date, b.produk, a.userid
		FROM m_loading a
		INNER JOIN mproduk b on a.flagtrans = b.flagtrans
		WHERE
			m_loading_id not in (select loading_id from tr_email where status_sample = 'f' group by loading_id)
			and a.create_date between now()-interval'90 days' and now()
			and total_customer > 0
		ORDER BY m_loading_id DESC, produk DESC LIMIT 10;
	";
	#$qry_belum_dikirim = pg_query($con, $sql_belum_dikirim) or die("Invalid query! " . $sql_belum_dikirim);
	$no_urut = 1;
	$xyz = 0;
	#if(pg_num_rows($qry_belum_dikirim) > 0){
	if($xyz > 0){
		?>
		<div align="center" class="besar">DAFTAR FILE YANG BELUM DIKIRIM</div>
		<table width="90%" border="1" style="font-family:helvetica;font-size:12px;border-collapse:collapse;" cellpadding="2" cellspacing="2">
			<tr bgcolor="#cdcdcd">
				<td class="tebal" height="20" width="5%">NO</td>
				<td width="25%" class="tebal">PRODUK</td>
				<td width="15%" align="center"><b>BLTH</b></td>
				<td width="20%" class="tebal">FILE</td>
				<td width="20%" align="center"><b>USER / TGL LOADING</b></td>
				<td width="10%" align="right"><b>JUMLAH</b></td>
			</tr>
			<?php
			while ($row_belum_dikirim = pg_fetch_assoc($qry_belum_dikirim)) 
			{
			if($no_urut % 2=='1'){$class='background-color:#ffffff;';}else{ $class='background-color:#ccddcc;';};

				?>
				<tr style="<?php echo $class;?>">
					<td valign="top"><b><?php echo $no_urut ?></b></td>
					<td valign="top" style="color:#34578B;font-family:helvetica;font-size:12px;"><a href="?pr=<?php echo $row_belum_dikirim['flagtrans'];?>"><?php echo $row_belum_dikirim["produk"] ?></a></td>
					<td valign="top" align="center"><b><?php echo $row_belum_dikirim ["blth"] ?></b></td>
					<td valign="top" style="color:#34578B;font-weight:bold;font-size:12px;"><a href="?pr=<?php echo $row_belum_dikirim['flagtrans'];?>"><?php echo $row_belum_dikirim ["loading_file"] ?></a></td>
					<td valign="top" align="center"><b><?php echo $row_belum_dikirim['userid'];?> - <?php echo date("d/m/Y H:i",strtotime(substr($row_belum_dikirim ["create_date"],0,16))); ?></b></td>
					<td valign="top" align="right" style="color:#ee0026;font-weight:bold;"><?php echo number_format($row_belum_dikirim["total_customer"]) ?></td>
				</tr>
				<?php
				$no_urut++;	
			}
			?>
		</table>
		<br/>
        <?php
	}
	$sql_antrian = "
		SELECT a.loading_id,d.produk, c.blth, c.loading_file, b.tgl_jadwal, count(*) as total_antrian, d.flagtrans, a.userid, to_char(a.tgl_antrian, 'yyyy-mm-dd hh24:mi') as tgl_antrian,  c.userid, c.create_date,
		case
			when la.is_approval is true then la.approval_user || ' - ' || to_char(la.approval_date, 'dd/mm/yyyy hh24:mi')
			else '-'
			end as apr,
			case
				when la.is_check = '1' then la.check_userid || ' - ' || to_char(la.check_date, 'dd/mm/yyyy hh24:mi')
				else '-'
				end as cek
		FROM antrian_email a
		LEFT JOIN m_jadwal b ON a.jadwal_id = b.jadwal_id
		INNER JOIN m_loading c ON a.loading_id = c.m_loading_id
		INNER JOIN mproduk d ON c.flagtrans = d.flagtrans
		INNER JOIN log_approval la ON la.log_approval_id=a.log_approval_id 
		WHERE
			c.create_date between now()-interval'30 days' and now()
			and status_sample = 'f'
			and tgl_jadwal < now()
		GROUP BY a.loading_id,d.produk, c.blth, c.loading_file, b.tgl_jadwal, d.flagtrans,a.userid, to_char(a.tgl_antrian, 'yyyy-mm-dd hh24:mi'),
		la.is_approval,la.approval_user,la.approval_date,la.is_check,la.check_userid,la.check_date,c.userid,c.create_date
		ORDER BY total_antrian DESC, b.tgl_jadwal ASC;
	";
	#$qry_antrian = pg_query($con, $sql_antrian) or die("Invalid query! " . $sql_antrian);
	$no_urut = 1;
	$xy= @pg_num_rows($qry_antrian);
	if( $xy > 0){
		?>
		<div align="center" class="besar">DAFTAR YANG PERLU DICEK DI ANTRIAN</div>
		<table width="90%" border="1" style="font-family:helvetica;font-size:12px;border-collapse:collapse;" cellpadding="2" cellspacing="2">
			
			<tr bgcolor="#cdcdcd">
				<td class="tebal" width="5%">NO</td>
				<td width="25%" class="tebal">PRODUK</td>
				<td width="15%" align="center"><b>BLTH</b></td>
				<td width="20%" class="tebal">FILE</td>
				<td width="20%" align="center"><b>DETAIL KIRIM</b></td>
				<td width="10%" align="right"><b>JUMLAH</b></td>
			</tr>
			<?php
			while ($row_antrian = pg_fetch_assoc($qry_antrian)) 
			{
				if($no_urut % 2=='1'){$class='background-color:#ffffff;';}else{ $class='background-color:#ccddcc;';};
				$sql_menu = "SELECT menuid,lokasi FROM menu1 WHERE lower(lokasi) LIKE '%index_lihat_antrian%' AND flagtrans='".$row_antrian['flagtrans']."'";
				$exe_menu = @pg_query($sql_menu);
				$row_menu = pg_fetch_array($exe_menu);
				$menunya = $row_menu['lokasi']."?pr=".$row_antrian['flagtrans']."|".$row_menu['menuid']."|".$row_antrian ["blth"] . "|". $row_antrian['loading_id'];

				?>
				<tr style="<?php echo $class;?>">
					<td valign="top"><b><?php echo $no_urut ?></b></td>
					<td valign="top" style="font-family:helvetica;font-size:12px;"><a href="<?php echo $menunya;?>"><?php echo $row_antrian["produk"] ?></a></td>
					<td valign="top" align="center"><b><?php echo $row_antrian ["blth"] ?></b></td>
					<td valign="top" style="font-size:12px;">
					<b><a href="<?php echo $menunya;?>"><?php echo $row_antrian ["loading_file"] ?></a></b>
					<br/><u>Diproses oleh:</u> <b><br/><?php echo $row_antrian['userid'] . " - " . date("d/m/Y H:i", strtotime($row_antrian['create_date']));?></b>
					<br/><u>Dimasukkan ke antrian oleh:</u><br/><b><?php echo $row_antrian['userid'] . " - " . date("d/m/Y H:i", strtotime($row_antrian['tgl_antrian']));?></b>
					</td>
					<td align="center" valign="top" >
					<u>Dicek oleh:</u> <b><br/><?php echo $row_antrian['cek'];?></b>
					<br/><u>Approval oleh:</u> <b><br/><?php echo $row_antrian['apr'];?></b>
					<br/><u><font style="color:#960014;">Jadwal Kirim: <b><br/><?php echo date("d/m/Y H:i",strtotime(substr($row_antrian ["tgl_jadwal"],0,16))); ?></b></font></u></td>					
					<td align="right" valign="top" style="color:#ee0026;font-weight:bold;"><a href="<?php echo $menunya;?>" style="color:#ee0026;"><?php echo number_format($row_antrian["total_antrian"]) ?></a></td>
				</tr>
				<?php
				$no_urut++;	
			}
			?>
		</table>
		<br/>
        <?php
	}
?>