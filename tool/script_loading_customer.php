<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/function.php"); ?>
<?php
	$act = $_REQUEST['act'];
	$con = pg_connect($connection) or die('Could not connect to database!');
	
	switch($act){
		case 'log_customer'			: log_customer(); break;
		case 'show_customer'		: show_customer(); break;
		case 'editcust'				: editcust(); break;
	}
	
	function editcust(){
		$m_customer_id = $_REQUEST['m_customer_id'];
		$txt_norek = $_REQUEST['txt_norek'];
		$txt_email = $_REQUEST['txt_email'];
		
		$sqlupdcust = " INSERT INTO m_customer_history 
						SELECT *,now() AS date_created FROM m_customer WHERE m_customer_id=".$m_customer_id;
			
		$qryupdcust = pg_query($sqlupdcust) or die('ERROR SQL Upd Cust: '.$sqlupdcust);
		
		$sqlupdcust = " UPDATE m_customer SET email1='".$txt_email."' WHERE m_customer_id=".$m_customer_id;
		
		$qryupdcust = pg_query($sqlupdcust) or die('ERROR SQL Upd Cust: '.$sqlupdcust);
		
		if($qryupdcust){
			echo "Edit Customer dengan nomer rekening ".$txt_norek." berhasil";
		}
	}
	
	function log_customer(){
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['flagtrans'];
		$offset = $_REQUEST['offset'];
		$limit = 5;
		$i = $offset;
		$c = 0;
		
		$sql_sel = "SELECT log_customer_id, userid, to_char(create_date,'yyyy-mm-dd HH24:mi:ss') as create_date, nama_file, total_customer FROM log_customer WHERE blth = '$blth' and flagtrans = '$flagtrans' ORDER BY create_date DESC";
		$sql_show = $sql_sel." LIMIT $limit OFFSET $offset";
		$qry_sel = pg_query($sql_sel) or die('ERROR select: '.$sql_sel);
		$qry_show = pg_query($sql_show) or die('ERROR show: '.$sql_show);
		$jumlah_data = pg_num_rows($qry_sel);
		?>
        <table width="100%" align="center" cellpadding="1" cellspacing="2" border="0">
        	<tr class="table_header">
            	<td width="10%">NO</td>
            	<td width="20%">User</td>
            	<td width="20%">Tanggal Loading</td>
            	<td width="20%">Nama File</td>
            	<td width="20%">Total <i>Customer</i></td>
            	<td width="10%">ACT</td>
            </tr>
        <?php
		while($row_show=pg_fetch_array($qry_show)){
			$i++;
			$log_customer_id = $row_show['log_customer_id'];
			$userid = $row_show['userid'];
			$create_date = $row_show['create_date'];
			$nama_file = $row_show['nama_file'];
			$total_customer = $row_show['total_customer'];
			
			if($i%2==0){
				$cls = 'table_row_odd';
			}else{
				$cls = 'table_row_even';
			}
			?>
            <tr class="<?=$cls?>">
            	<td align="center" height="30"><?=$i?></td>
            	<td><?=$userid?></td>
            	<td align="center"><?=$create_date?></td>
            	<td><a href="../tmp/<?=$nama_file?>" target="_blank"><?=$nama_file?></a></td>
            	<td align="right"><? echo number_format($total_customer,0,' ','')?></td>
            	<td align="center"><a onclick="show_cust(<?=$log_customer_id?>)">VIEW</a></td>
            </tr>
            <?php
		}
		?>
            <tr>
                <td align="center" colspan="11">
                    <?php
                        echo paging($offset,$jumlah_data,$limit,'div_customer','script_loading_customer.php','act=log_customer&blth='.$blth.'&flagtrans='.$flagtrans);
                    ?>
                </td>
            </tr>
        </table>
        <?php
	}
	
	function show_customer(){
		$log_customer_id = $_REQUEST['log_customer_id'];
		$offset = $_REQUEST['offset'];
		$limit = 25;
		$i = $offset;
		$c = 0;
		
		$sql_sel = "SELECT nomor_rekening, email1, m_customer_id, blth FROM m_customer WHERE log_customer_id = '$log_customer_id' ORDER BY nomor_rekening";
		$sql_show = $sql_sel." LIMIT $limit OFFSET $offset";
		$qry_sel = pg_query($sql_sel) or die('ERROR select: '.$sql_sel);
		$qry_show = pg_query($sql_show) or die('ERROR show: '.$sql_show);
		$jumlah_data = pg_num_rows($qry_sel);
		?>
        <table width="100%" align="center" cellpadding="1" cellspacing="2" border="0">
        	<tr class="table_header">
            	<td width="10%">NO</td>
            	<td width="45%">Nomor Rekening</td>
            	<td>e-Mail</td>
				<td>[EDIT]</td>
            </tr>
        <?php
		while($row_show=pg_fetch_array($qry_show)){
			$i++;
			$nomor_rekening = $row_show['nomor_rekening'];
			$email = $row_show['email1'];
			$custid = $row_show['m_customer_id'];
			$blth = $row_show['blth'];
			
			if($i%2==0){
				$cls = 'table_row_odd';
			}else{
				$cls = 'table_row_even';
			}
			?>
            <tr class="<?=$cls?>">
            	<td align="center"><?=$i?></td>
            	<td><?=$nomor_rekening?></td>
            	<td><?=$email?></td>
				<td align="center">
					<a href='#' 
						onClick='
							EditCust("<?=$custid?>","<?=$nomor_rekening?>","<?=$email?>",<?=$log_customer_id?>,"<?=$blth?>");'>[E]</a>
				</td>
            </tr>
            <?php
		}
		?>
            <tr>
                <td align="center" colspan="11">
                    <?php
                        echo paging($offset,$jumlah_data,$limit,'div_customer','script_loading_customer.php','act=show_customer&blth='.$blth.'&flagtrans='.$flagtrans.'&log_customer_id='.$log_customer_id);
                    ?>
                </td>
            </tr>
        </table>
        <?php
	}