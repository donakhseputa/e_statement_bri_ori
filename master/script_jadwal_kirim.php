<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<?
	$act = $_REQUEST['act'];
	$con = pg_connect($connection) or die("Could not connect to database!");
	
	switch($act){
		case 'show_table': show_table(); break;
		case 'add_jadwal': add_jadwal(); break;
		case 'save': save(); break;
		case 'delete_jadwal': delete_jadwal(); break;
	}
	
	function show_table(){
		?>
        <table width="100%" cellpadding="0" cellspacing="0" border="1" style="border:1px;border-style:solid">
            <tr class="table_header">
                <td width="25%">Jadwal</td>
                <td width="20%">Produk</td>
                <td width="15%">User</td>
                <td width="20%">Create</td>
                <td width="10%">Status</td>
                <td width="10%">&nbsp;</td>
            </tr>
        <?php
		$sql_sel_jadwal = "SELECT a.*, b.produk
							FROM m_jadwal a
								INNER JOIN mproduk b
									ON a.flagtrans = b.flagtrans
							ORDER BY produk, tgl_jadwal";
		$qry_sel_jadwal = pg_query($sql_sel_jadwal) or die('ERROR select mail_server: '.$sql_sel_jadwal);
		while($row_sel_jadwal=pg_fetch_array($qry_sel_jadwal)){
			$i++;
			$jadwal_id = $row_sel_jadwal['jadwal_id'];
			$tgl_jadwal = $row_sel_jadwal['tgl_jadwal'];
			$produk = $row_sel_jadwal['produk'];
			$user_create = $row_sel_jadwal['user_create'];
			$date_create = $row_sel_jadwal['date_create'];
			$flag_jadwal = $row_sel_jadwal['flag_jadwal'];
			$status = $row_sel_jadwal['status'];
			$date_sent = $row_sel_jadwal['date_sent'];
			
			if(strtoupper($flag_jadwal)==''){
				$flag = 'AVAILABLE';
			}elseif(strtoupper($flag_jadwal)=='T'){
				$flag = 'QUEUE';
			}elseif(strtoupper($flag_jadwal)=='F'){
				$flag = 'DONE';
			}
			
			if(strtoupper($status)=='T' && $date_sent=='' && $tgl_jadwal < date('Y-m-d H:i:00')){
				$flag = 'NOT AVAILABLE';
			}
			
			if($i%2==0){
				$cls = 'table_row_odd';
			}else{
				$cls = 'table_row_even';
			}
			
			?>
			<tr class="<?=$cls?>" height="30">
				<td align="center"><?=$tgl_jadwal?></td>
				<td><?=$produk?></td>
                <td><?=$user_create?></td>
				<td align="center"><?=$date_create?></td>
				<td align="center"><?=$flag?></td>
				<td align="center">
                	<?php
					if(strtoupper($status)=='T' and strtoupper($flag_jadwal)==''){
						?>
                        <a onclick="delete_conf(<?=$jadwal_id?>)"><img src="../images/icons/del_data.png" border="0" /></a>
                        <?php
					}elseif(strtoupper($status)=='F'){
						echo $date_sent;
					}
					?>
				</td>
			</tr>
			<?php
		}
		?>
        </table>
        <?php
	}
	
	function add_jadwal(){
		?>
        <form name="form_add" id="form_add">
            <table width="525">
                <tr>
                    <td align="center" colspan="2" style="font-size:16px"><b>ADD SCHEDULE</b></td>
                </tr>
                <tr>
                    <td height="20">&nbsp;</td>
                </tr>
                <tr>
                    <td width="30%">Produk</td>
                    <td height="30" valign="bottom">
                    	<select id="flagtrans" name="flagtrans" class="combobox">
                    	<?php
						$sql_produk = "SELECT * FROM mproduk ORDER BY produk";
						$qry_produk = pg_query($sql_produk) or die('ERROR: '.$sql_produk);
						while($row_produk = pg_fetch_array($qry_produk)){
							?>
                            <option value="<?=$row_produk['flagtrans']?>"><?=$row_produk['produk']?></option>
                            <?php
						}
						?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Jadwal</td>
                    <td height="20" valign="bottom">
                    	<input type="text" id="tgl_jadwal" name="tgl_jadwal" class="textbox_2" readonly>
                        <select id="hour_jadwal" name="hour_jadwal" class="combobox">
                            <?php
							for ($i = 0; $i <= 23; $i++){
								if(strlen($i)==1) $i='0'.$i;
                            ?>
                            <option value="<?= $i ?>"<? if ($i == date("H")) { echo(" selected=\"selected\""); } ?>><?= $i ?></option>
                            <? } ?>
                        </select>&nbsp;:&nbsp;
                        <select id="min_jadwal" name="min_jadwal" class="combobox">
                            <? for ($i = 0; $i <= 59; $i++) { 
                                if(strlen($i)==1) $i='0'.$i;
                            ?>
                            <option value="<?= $i ?>"<? if ($i == date("i")) { echo(" selected=\"selected\""); } ?>><?= $i ?></option>
                            <? } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td height="20">Status</td>
                    <td>AVAILABLE</td>
                </tr>
                <tr>
                    <td height="20" colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2" align="center"><input type="button" name="button_save" id="button_save" class="button" value="SAVE" onClick="save()"></td>
                </tr>
            </table>
</form>
        <?php
	}
	
	function save(){
		$flagtrans = $_POST['flagtrans'];
		$tgl_jadwal = $_POST['tgl_jadwal'];
		$hour_jadwal = $_POST['hour_jadwal'];
		$min_jadwal = $_POST['min_jadwal'];
		$sched = $tgl_jadwal.' '.$hour_jadwal.':'.$min_jadwal;
		
		$sql_ins_jadwal = "INSERT INTO m_jadwal(
									tgl_jadwal,
									flagtrans,
									status,
									user_create,
									date_create
									)VALUES(
										'$sched',
										'$flagtrans',
										TRUE,
										'".$_SESSION['userid']."',
										now()
									)";
		$qry_ins_jadwal = pg_query($sql_ins_jadwal) or die('ERROR insert m_jadwal: '.$sql_ins_jadwal);
		if($qry_ins_jadwal){
			echo 'sukses';
		}
	}
	
	function delete_jadwal(){
		$jadwal_id = $_REQUEST['jadwal_id'];
		
		$sql_del_jadwal = "DELETE FROM m_jadwal WHERE jadwal_id = $jadwal_id";
		$qry_del_jadwal = pg_query($sql_del_jadwal) or die('ERROR delete m_jadwal: '.$sql_del_jadwal);
		echo 'sukses';
	}
	
	pg_close($con);
?>