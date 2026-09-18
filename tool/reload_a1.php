<script type="text/javascript">
$("#loading_summary").hide();
</script>



<?php 
	error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED | E_STRICT);
	require_once("../include/config.php");
	require_once("../include/session.php");
	require_once("../include/function.php");
	
	date_default_timezone_set("Asia/Jakarta");
	
	
	ini_set('memory_limit', '-1');
	
	
	$con = pg_connect($connection) or die("Could not connect to database!");
	
	$file_zip = '';
	$file_txt = '';
	$status_akhir = '';
	$lama_proses = '';
		
		
		$sql_cek_dat = "
						SELECT userid, m_loading_id, loading_file , blth, ( extract(epoch from (now() - create_date::timestamp)) /60  ) as lama_proses FROM 
						(select * from m_loading  order by m_loading_id DESC limit 3)a 
						WHERE flagtrans = 'BC'  and (total_customer is null ) ORDER BY m_loading_id DESC LIMIT 1
						";
		$qry_cek_dat = pg_query($sql_cek_dat) or die('ERROR cek m_loading 1: '.$sql_cek_dat);
		$jml = @pg_num_rows($qry_cek_dat);
		$row = pg_fetch_assoc($qry_cek_dat);
		if($jml == 0 ){
			
			echo '
			<div class="alert alert-success" >
			<strong>PROSES SELESAI!</strong>  Silakan refresh browser Anda dan cek log proses
			</div>
			
			';

			$status_akhir = "done";
			die("");
		}else{
			$m_loading_id = $row['m_loading_id'];
			$user_pemproses = $row['userid'];
			$file_zip = $row['loading_file'];
			$file_txt = $row['loading_file_txt'];
			$blth = $row['blth'];
			$lama_proses = ceil($row['lama_proses']);
		}
		
		
		$folder_pdf =  str_replace(substr($file_zip,-4),'',$file_zip);
		
		/* ---------------------------------------- MENGHITUNG PDF INJECT ---------------------------------------- */
		$pdf_inject_process_total =  cek_jumlah_temp($blth,'MT',$file_zip);
		$pdf_inject_process =  filesInDir("../pdf/BC/$blth/$folder_pdf/", ".pdf");
		//die("$blth,$m_loading_id");
		//die("$pdf_inject_process");
		$pdf_inject_process_last = check_pdf_inject($blth,$m_loading_id);
		$last_inject_pdf = $pdf_inject_process_last;
		$persen_inject = (($pdf_inject_process/$pdf_inject_process_total)*100);
		$persen_inject = number_format($persen_inject, 0); 
		$progress_inject_total = "$pdf_inject_process of $pdf_inject_process_total";
		/* ---------------------------------------- MENGHITUNG PDF INJECT ---------------------------------------- */
		$arr_polis =  cek_on_proses($blth,'MT',$file_zip);
		$polis_running =  cek_on_proses_polis($blth,'MT',$file_zip);
		$split_batch =  split_batch();
		$total_batch =  total_batch($blth,'MT',$file_zip);
		$total_batch_selesai =  total_batch_selesai($blth,'MT',$file_zip);
		
	
		if ($persen_inject == 0) 
		{
			$persen_inject = '100';
			$progress_inject_total = "DONE";
		}
		
		
		
		
		
		/* ---------------------------------------- CHECK LAMA WASKTU INJECT ---------------------------------------- */
		/*
		31 = 6000
		x=10000

		6000x=31*10000
		x=31*10000/6000
		*/
		$time_remaining = '';
		$sisa_waktu_proses = '';
		if( !empty($lama_proses)  && $persen_inject != 100 )
		{
			$sisa_waktu_proses = ($lama_proses*$pdf_inject_process_total/$pdf_inject_process) -  $lama_proses;
			$sisa_waktu_proses = ceil($sisa_waktu_proses);
			
			//$pdf_inject_process of $pdf_inject_process_total
			//$sisa_waktu_proses = "<br>running time : <b>$lama_proses</b> minutes <br>time remaining : <b>$sisa_waktu_proses</b> minutes  ";
			
			$time_remaining = "
			<tr>
				<td>Time Remaining</td>
				<td>:</td>
				<td><b>$sisa_waktu_proses</b> minutes (".floor($sisa_waktu_proses / 60).' hours '.($sisa_waktu_proses - floor($sisa_waktu_proses / 60) * 60)." minutes)</td>
			</tr>";
		}
		
		/* ---------------------------------------- CHECK LAMA WASKTU INJECT ---------------------------------------- */
		
		@pg_close($con);
?>

<!DOCTYPE html>
<html>

<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
* {box-sizing: border-box}

.container {
  width: 100%;
}

.skills {
  text-align: right;
  padding-right: 20px;
  line-height: 30px;
  color: white;
}

.html {width: <?php echo "$persen_inject";?>%; background-color: #4CAF50;}
.css {width: <?php echo "$persen_zip_part";?>%; background-color: #2196F3;}





.container {
  margin: 50px auto; 
}
.progressbar {
  margin: 0;
  padding: 0;
  counter-reset: step;
}
.progressbar li {
  list-style-type: none;
  width: 20%;
  float: left;
  font-size: 12px;
  position: relative;
  text-align: center;
  text-transform: uppercase;
  color: #7d7d7d;
}
.progressbar li:before {
  width: 30px;
  height: 30px;
  content: counter(step);
  counter-increment: step;
  line-height: 30px;
  border: 2px solid #7d7d7d;
  display: block;
  text-align: center;
  margin: 0 auto 10px auto;
  border-radius: 50%;
  background-color: white;
}
.progressbar li:after {
  width: 100%;
  height: 2px;
  content: '';
  position: absolute;
  background-color: #7d7d7d;
  top: 15px;
  left: -50%;
  z-index: -1;
}
.progressbar li:first-child:after {
  content: none;
}
.progressbar li.active {
  color: green;
}
.progressbar li.active:before {
  border-color: #55b776;
}
.progressbar li.active + li:after {
  background-color: #55b776;
}




.progressx {
  position: relative;
}
.progressx .progress-bar {
  position: absolute;
  overflow: hidden;
  line-height: 20px;
}


.progressx {
  overflow: hidden;
  height: 20px;
  margin-bottom: 20px;
  background-color: #B5C4D2;
  border-radius: 4px;
  -webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
}
.progress-bar {
  float: left;
  width: 0%;
  height: 100%;
  font-size: 12px;
  line-height: 20px;
  color: #ffffff;
  text-align: center;
  background-color: #337ab7;
  -webkit-box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.15);
  box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.15);
  -webkit-transition: width 0.6s ease;
  -o-transition: width 0.6s ease;
  transition: width 0.6s ease;
}
</style>



</head>
<body>
<div class="alert alert-info" id="finalisasi" style="display:none">
			<strong>FINALISASI PEMROSESAN!</strong>  mohon menunggu........ <img src="../images/ajax.gif" alt="loading..." border="0" />
			</div>

<p><b>PROGRESS INJECT</b> <br>


<table>


<tr>
	<td>Processed by</td>
	<td>:</td>
	<td><?php echo " $user_pemproses";?></td>
</tr>

<tr>
	<td>File Name</td>
	<td>:</td>
	<td><?php echo " $file_zip ";?></td>
</tr>

<tr>
	<td>Inject PDF</td>
	<td>:</td>
	<td><?php echo "<b>$progress_inject_total</b> ";?></td>
</tr>
<tr>
	<td>Last Inject PDF</td>
	<td>:</td>
	<td><?php echo "<b>$last_inject_pdf</b> ";?></td>
</tr>
<tr>
				<td>Running Time</td>
				<td>:</td>
				<td><?php 
				if ( $progress_inject_total == "DONE")
				{
					echo "<b>DONE</b>";
				}else{
					echo "<b>$lama_proses</b> minutes (".floor($lama_proses / 60).' hours '.($lama_proses -   floor($lama_proses / 60) * 60)." minutes)";
				}
				
				
				
				?> </td>
			</tr>
<?php echo $time_remaining ;?>
</table>
 <br>
 <br>



<div class="progressx">
  <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $persen_inject;?>"
  aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $persen_inject;?>%">
    <?php echo $persen_inject;?>%
  </div>
</div>
<?php 
if ($arr_polis != 'done'){
	?>

	
<br>
<br>
<br>
<br>

<?php 
$x = ceil($total_batch/$split_batch);

if ( $total_batch_selesai == $total_batch)
{
	$total_batch_selesai = ceil($total_batch_selesai/$split_batch);
}else{
	$total_batch_selesai = floor($total_batch_selesai/$split_batch);
	//echo "<br>$total_batch_selesai<br>";
}
?>
<div class="container">
        <ul class="progressbar">
		<?php
		for ($i = 0; $i < $x; $i++) {
		?>
            <li
			<?php 
			if ($i < $total_batch_selesai)
			{
				echo 'class="active"';
			}
			?>    
			> 
			<?php
			echo 'BATCH '.($i+1);
			if ($i < $total_batch_selesai)
			{
				echo ' <br><i>(selesai)</i>';
			}
			?></li>
		<?php 
		}
		?>
        </ul>
    </div>
	
	
	
<?php 
}
	?>
	
	
	<?php 
		if ($x == $total_batch_selesai)
		{
	?>
		<script type="text/javascript">
			$("#finalisasi").show();
		</script>
	<?php 
		}
	?>
</body>
</html>



<?php 


function filesInDir($tdir, $extention='', $hasil='')
{
		//die($tdir);
		if(file_exists($tdir)==false) return '0';
		
		$x=0;
        $dirs = scandir($tdir);
        foreach($dirs as $file)
        {
                if (($file == '.')||($file == '..'))
                {
                }
                elseif (is_dir($tdir.'/'.$file))
                {
						
                        filesInDir($tdir.'/'.$file,  $direktori);
                }
                else
                {
						$ext = substr($file,-4); 
						
						if ($extention == '.zip')
						{
							if($hasil == "")
							{
								$a = $file;
								if (strpos($a, 'part') !== false) {
									$x++;
								}
							}else{
								$a = $file;
								if (strpos($a, 'part') !== false) {
									
								}else{
									return $a;
								}
							}
							
						}else{
							if ( $ext == $extention )
							{
								$x++;
							}
						
						}
						
						
						
						
                }
        }
		
		return $x;
}


function check_pdf_inject($blth='', $m_loading_id='')
{
	
	if (empty($blth)) return '';
	
 	$tabel_detail="detail_".$blth."_bc";
	$sql_cek_dat = "SELECT * FROM 
						$tabel_detail
						WHERE m_loading_id::text = '$m_loading_id' ORDER BY detail_id DESC LIMIT 1";
						//die($sql_cek_dat);
		$qry_cek_dat = pg_query($sql_cek_dat) or die('ERROR cek m_loading 4: '.$sql_cek_dat);
		$jml = @pg_num_rows($qry_cek_dat);
		$row = pg_fetch_assoc($qry_cek_dat);
		if($jml == 0){
			
			$pdf_name = "DONE";
		}else{
			$pdf_name = $row['pdf_name'];
			
		}
		
		return $pdf_name;
}

function cek_jumlah_temp($blth,$flagtrans,$file)
{
	if ( empty($blth)) return 'done';
	$tabeldetail = strtolower("detail_".$blth."_"."mt");
	$file_replace = str_replace(substr($file,-7),'',$file);
	$sql_cek_dat = "SELECT count(*) as total_customer FROM 
						$tabeldetail WHERE lower(nama_file) like lower('%$file_replace%')
						";
		$qry_cek_dat = pg_query($sql_cek_dat) or die('ERROR cek m_loading 4: '.$sql_cek_dat);
		$jml = @pg_num_rows($qry_cek_dat);
		$row = pg_fetch_assoc($qry_cek_dat);
		if($jml == 0){
			
			$status_akhir = "done";
		}else{
			$status_akhir = $row['total_customer'];
			
		}
		
		return $status_akhir;
}

function cek_on_proses($blth,$flagtrans,$file)
{
	
	$tabeldetail = strtolower("detail_".$blth."_"."mt");
	$file_replace = str_replace(substr($file,-7),'',$file);
	$sql_cek_dat = "SELECT count(*) as total_customer FROM 
						$tabeldetail WHERE lower(nama_file) like lower('%$file_replace%')
						AND no_rek_asli = 'running'
						";
						
	#$sql_cek_dat = "SELECT * FROM temp_csv_a1_status ORDER BY id DESC LIMIT 1";
	
	
		$qry_cek_dat = pg_query($sql_cek_dat) or die('ERROR cek m_loading 4: '.$sql_cek_dat);
		$jml = @pg_num_rows($qry_cek_dat);
		$row = pg_fetch_assoc($qry_cek_dat);
		if($jml == 0){
			
			return "done";
		}else{
			if ( $row['status_combine'] == 'selesai' ||  $row['status_combine'] == 'TIDAK ADA' )
			{
				$status_akhir = 5;
			}else if ( $row['status_spaj'] == 'selesai' ||  $row['status_spaj'] == 'TIDAK ADA' ){
				$status_akhir = 4;
			}else if ( $row['status_ketentuan'] == 'selesai' ||  $row['status_ketentuan'] == 'TIDAK ADA' ){
				$status_akhir = 3;
			}else if ( $row['status_polis_page3'] == 'selesai' ||  $row['status_polis_page3'] == 'TIDAK ADA' ){
				$status_akhir = 2;
			}else if ( $row['status_cover'] == 'selesai' ||  $row['status_cover'] == 'TIDAK ADA' ){
				$status_akhir = 1;
			}
			
			
			
		}
		
		return $status_akhir;
}



function cek_on_proses_polis($blth,$flagtrans,$file)
{
	
	$tabeldetail = strtolower("detail_".$blth."_"."mt");
	$file_replace = str_replace(substr($file,-7),'',$file);
	$sql_cek_dat = "SELECT count(*) as total_customer FROM 
						$tabeldetail WHERE lower(nama_file) like lower('%$file_replace%')
						AND no_rek_asli = 'running'
						";
	#$sql_cek_dat = "SELECT * FROM temp_csv_a1_status ORDER BY id DESC LIMIT 1";
		$qry_cek_dat = pg_query($sql_cek_dat) or die('ERROR cek m_loading 4: '.$sql_cek_dat);
		$jml = @pg_num_rows($qry_cek_dat);
		$row = pg_fetch_assoc($qry_cek_dat);
		if($jml == 0){
			
			return "done";
		}else{
			$status_akhir = $row['nomor_polis'] .' data ke '.$row['id'];
			
		}
		
		return $status_akhir;
}



function split_batch()
{
	
	$sql_cek_dat = "SELECT * FROM master_proses_after_ematerai ORDER BY id DESC LIMIT 1";
		$qry_cek_dat = pg_query($sql_cek_dat) or die('ERROR cek m_loading 4: '.$sql_cek_dat);
		$jml = @pg_num_rows($qry_cek_dat);
		$row = pg_fetch_assoc($qry_cek_dat);
		if($jml == 0){
			
			return "0";
		}else{
			$status_akhir = $row['jumlah_proses'];
			
		}
		
		return $status_akhir;
}



function total_batch($blth,$flagtrans,$file)
{
	$tabeldetail = strtolower("detail_".$blth."_"."mt");
	$file_replace = str_replace(substr($file,-7),'',$file);
	$sql_cek_dat = "SELECT count(*) as total_batch  FROM 
						$tabeldetail WHERE lower(nama_file) like lower('%$file_replace%')
						AND no_rek_asli = ''
						";
	
	#$sql_cek_dat = "SELECT count(*) as total_batch FROM temp_csv_a1 WHERE estatement_ind='Y'  ";
		$qry_cek_dat = pg_query($sql_cek_dat) or die('ERROR cek m_loading 4: '.$sql_cek_dat);
		$jml = @pg_num_rows($qry_cek_dat);
		$row = pg_fetch_assoc($qry_cek_dat);
		if($jml == 0){
			
			return "0";
		}else{
			$status_akhir = $row['total_batch'] ;
			
		}
		
		return $status_akhir;
}





function total_batch_selesai($blth,$flagtrans,$file)
{
	$tabeldetail = strtolower("detail_".$blth."_"."mt");
	$file_replace = str_replace(substr($file,-7),'',$file);
	$sql_cek_dat = "SELECT count(*) as total_customer FROM 
						$tabeldetail WHERE lower(nama_file) like lower('%$file_replace%')
						AND no_rek_asli IS NOT NULL
						";
	
	#$sql_cek_dat = "SELECT count(*) as total_batch FROM temp_csv_a1 WHERE status_a1 IS NOT NULL";
		$qry_cek_dat = pg_query($sql_cek_dat) or die('ERROR cek m_loading 4: '.$sql_cek_dat);
		$jml = @pg_num_rows($qry_cek_dat);
		$row = pg_fetch_assoc($qry_cek_dat);
		if($jml == 0){
			
			return "0";
		}else{
			$status_akhir = $row['total_batch'] ;
			
		}
		
		return $status_akhir;
}
?>
