<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/function.php"); ?>
<script type="text/javascript" src="../script/vtips/vtip.js"></script>
<style>
.vtip {
	cursor:help;
}

.vtip input {
	cursor:help;
}

p#vtip {
	display: none;
	position: absolute;
	padding: 2px;
	margin:auto;
	left: 5px;
	font-size: 12px;
	background-color: #CCFFCC;
	border: 1px solid #666666;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	z-index: 9999;
}

p#vtip #vtipArrow {
	position: absolute;
	top: -10px;
	left: 5px
}      
</style> 
<?php
	$act = $_REQUEST['act'];
	$con = pg_connect($connection) or die('Could not connect to database!');
	
	switch($act){
		case 'blth_show'			: blth_show(); break;
		case 'log_detail'			: log_detail(); break;
		case 'show_detail'			: show_detail(); break;
		case 'del_data'				: del_data(); break;
	}
	
	function blth_show(){
		?>
        <select id="blth_show" name="blth_show" class="combobox" onchange="show_blth_log()">
            <?php
            $sql_sel_blth = "SELECT blth
                            FROM m_loading
                            GROUP BY blth
                            ORDER BY substring(blth,3,4) DESC, substring(blth,1,2) DESC";
            $qry_sel_blth = pg_query($sql_sel_blth) or die('ERROR '.$sql_sel_blth);
            while($row_blth=pg_fetch_array($qry_sel_blth)){
                ?>
                <option value="<?=$row_blth['blth']?>"
                    <?php
                    if($blth==date('mY')){
                        echo 'selected';
                    }
                    ?>><?=$row_blth['blth']?></option>
                <?php
            }
            ?>
        </select>
        <?php
	}
	
	function log_detail(){
		?>
        <?php
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['flagtrans'];
		$offset = $_REQUEST['offset'];
		$input_search = $_REQUEST['input_search'];
		$input_search = str_replace('8764346466435364647768799667654537543756',' ',$input_search);
		$limit = 5;
		$i = $offset;
		$c = 0;
		
		/*$sql_sel = "SELECT m_loading_id, userid, to_char(create_date,'yyyy-mm-dd HH24:mi:ss') as create_date, loading_file, total_halaman, total_customer FROM m_loading WHERE blth = '$blth' and flagrans = '$flagtrans' and (upper(loading_file) like upper('%".$input_search."%') or upper(userid) like upper('%".$input_search."%')) ORDER BY create_date DESC";*/
		$sql_sel = "SELECT a.m_loading_id, a.userid, to_char(a.create_date,'yyyy-mm-dd HH24:mi:ss') as create_date, a.loading_file, a.total_halaman, a.total_customer FROM m_loading a JOIN detail b ON a.m_loading_id=b.m_loading_id WHERE a.blth = '$blth' and a.flagtrans = '$flagtrans' and (upper(a.loading_file) like upper('%".$input_search."%') or upper(a.userid) like upper('%".$input_search."%') or upper(b.nomor_rekening) like upper('%".$input_search."%')) 
		GROUP BY a.m_loading_id, a.userid, a.create_date, a.loading_file, a.total_halaman, a.total_customer ORDER BY a.create_date DESC";
		$sql_show = $sql_sel." LIMIT $limit OFFSET $offset";
		$qry_sel = pg_query($sql_sel) or die('ERROR select: '.$sql_sel);
		$qry_show = pg_query($sql_show) or die('ERROR show: '.$sql_show);
		//echo $sql_show;
		$jumlah_data = pg_num_rows($qry_sel);
		?>
        <table width="100%" align="center" cellpadding="1" cellspacing="2" border="0">
        	<tr class="table_header">
            	<td width="10%">NO</td>
            	<td width="16%">User</td>
            	<td width="16%">Waktu</td>
            	<td width="16%">Nama File</td>
            	<td width="16%">Total Halaman</td>
            	<td width="16%">Total <i>Customer</i></td>
            	<td width="10%">ACT</td>
            </tr>
        <?php
		while($row_show=pg_fetch_array($qry_show)){
			$i++;
			$m_loading_id = $row_show['m_loading_id'];
			$userid = $row_show['userid'];
			$create_date = $row_show['create_date'];
			$nama_file = $row_show['loading_file'];
			$total_halaman = $row_show['total_halaman'];
			$total_customer = $row_show['total_customer'];
			
			$sql_cek_tr_email = "SELECT count(*) as jml FROM tr_email WHERE loading_id = $m_loading_id and (status_sample is null or status_sample = FALSE)";
			$qry_cek_tr_email = pg_query($sql_cek_tr_email) or die('ERROR: '.$sql_cek_tr_email);
			$row_cek = pg_fetch_assoc($qry_cek_tr_email);
			if($row_cek['jml'] == 0){
				$del_data = '&nbsp;<a onclick="del_data('.$m_loading_id.')"><img class="vtip" title="delete" src="../images/icons/del_data.png" /></a>';
			}else{
				$del_data = "";
			}
			
			$csv = str_replace(substr($nama_file,-4),'',$nama_file);
			$file_csv = '../pdf/'.$blth.'/'.$csv.'/'.$csv.'.csv';
			if(file_exists($file_csv))
				$download_csv = '&nbsp;<a href="'.$file_csv.'" target="_blank"><img class="vtip" title="download" src="../images/icons/download_file.png" /></a>';
			else
				$download_csv = '';
			
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
            	<td align="right"><? echo number_format($total_halaman,0,' ','')?></td>
            	<td align="right"><? echo number_format($total_customer,0,' ','')?></td>
            	<td align="center">
                	<a onclick="show_det(<?=$m_loading_id?>)"><img class="vtip" title="view" src="../images/icons/icon_view.gif" /></a>
                    <?php echo $del_data; ?>
                    <?php echo $download_csv; ?>
                </td>
            </tr>
            <?php
		}
		?>
            <tr>
                <td align="center" colspan="11">
                    <?php
                        echo paging($offset,$jumlah_data,$limit,'div_detail','script_loading_detail.php','act=log_detail&blth='.$blth.'&flagtrans='.$flagtrans.'&input_search='.$input_search);
                    ?>
                </td>
            </tr>
        </table>
        <?php
	}
	
	function show_detail(){
		$m_loading_id = $_REQUEST['m_loading_id'];
		$input_search = $_REQUEST['input_search'];
		$input_search = str_replace('8764346466435364647768799667654537543756','%',$input_search);
		
		$offset = $_REQUEST['offset'];
		$limit = 25;
		$i = $offset;
		$c = 0;
		
		$sql_sel = "SELECT nomor_rekening, nama, alamat1, alamat2, alamat3, city, zipcode, blth, nama_file,
		pdf_name, password_pdf, jml_hlm, m_loading_id FROM detail WHERE m_loading_id = '$m_loading_id' and (upper(nomor_rekening) like upper('%".$input_search."%') or upper(nama) like upper('%".$input_search."%')) ORDER BY nomor_rekening";
		$sql_show = $sql_sel." LIMIT $limit OFFSET $offset";
		$qry_sel = pg_query($sql_sel) or die('ERROR select: '.$sql_sel);
		$qry_show = pg_query($sql_show) or die('ERROR show: '.$sql_show);
		$jumlah_data = pg_num_rows($qry_sel);
		//echo $sql_sel;
		?>
        <table width="100%" align="center" cellpadding="1" cellspacing="2" border="0">
        	<input type="hidden" id="m_loading_id" value="<?php echo $m_loading_id?>" />
        	<tr class="table_header">
            	<td width="8%">NO</td>
            	<td width="11%">Nomor rekening</td>
            	<td width="12%">Nama</td>
            	<td width="12%">Alamat1</td>
            	<td width="12%">Pdf_file</td>
            	<td width="12%">Password_pdf</td>
            	<td width="11%">Jumlah lembar</td>
            	<td width="11%">Kodepos</td>
            	<td width="11%">Lokasi File</td>
            </tr>
        <?php
		while($row_show=pg_fetch_array($qry_show)){
			$i++;
			$nomor_rekening = $row_show['nomor_rekening'];
			$nama = $row_show['nama'];
			$alamat1 = $row_show['alamat1'];
			$pdf_name = $row_show['pdf_name'].'.pdf';
			$password_pdf = $row_show['password_pdf'];
			$jml_hlm = $row_show['jml_hlm'];
			$zipcode = $row_show['zipcode'];
			$file_induk = $row_show['nama_file'];
			$lokasi_file = "../pdf/".$row_show['blth'] ."/". substr($row_show['nama_file'],0,strlen($row_show['nama_file'])-4);
			//$id = $row_show['m_loading_id'];		
			if($i%2==0){
				$cls = 'table_row_odd';
			}else{
				$cls = 'table_row_even';
			}
			?>
            <tr class="<?=$cls?>">
            	<td align="center"><?=$i?></td>
            	<td><?=$nomor_rekening?></td>
            	<td><?=$nama?></td>
            	<td><?=$alamat1?></td>
            	

                
                <td align="center">
                 <? echo "<a href= '".$lokasi_file ."/". $pdf_name.  " ' >". $pdf_name ."</a>"; ?>
                
				
                </td>
                
           	  <td align="center"><?=$password_pdf?></td>
           	  <td align="right"><?=number_format($jml_hlm,0)?> &nbsp </td>
           	  <td><?=$zipcode?></td>
            	<td><?=$file_induk?></td>
            </tr>
            <?php
		}
		?>
            <tr>
                <td align="center" colspan="11">
                    <?php
					echo paging($offset,$jumlah_data,$limit,'div_detail','script_loading_detail_group.php','act=show_detail&blth='.$blth.'&flagtrans='.$flagtrans.'&m_loading_id='.$m_loading_id.'&input_search='.$input_search);
                    ?>
                </td>
            </tr>
        </table>
        <?php
	}
	
	function del_data(){
		$m_loading_id = $_REQUEST['m_loading_id'];
		
		$sql = "SELECT blth, loading_file FROM m_loading WHERE m_loading_id = $m_loading_id";
		$qry = pg_query($sql) or die('ERROR: '.$sql);
		$row = pg_fetch_assoc($qry);
		$blth = $row['blth'];
		$loading_file = $row['loading_file'];
		
		
		$sql_del = "DELETE FROM m_loading WHERE m_loading_id = $m_loading_id;
					DELETE FROM detail WHERE m_loading_id = $m_loading_id;
					DELETE FROM antrian_email_history WHERE loading_id = $m_loading_id;
					DELETE FROM tr_email WHERE loading_id = $m_loading_id;
					DELETE FROM antrian_email WHERE loading_id = $m_loading_id;";
		$qry_del = pg_query($sql_del) or die('ERROR: '.$qry_del);
		
		
		$file = str_replace(substr($loading_file,-4),'',$loading_file);
		$dir_pdf = '../pdf/'.$blth.'/'.$file.'/';
		hapus_folder($dir_pdf);
		rmdir(rtrim($dir_pdf,'/'));
	}
?>