<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/function.php"); ?>
<?php
	$act = $_REQUEST['act'];
	$con = pg_connect($connection) or die('Could not connect to database!');
	
	switch($act){
		case 'blth_show'			: blth_show(); break;
		case 'log_detail'			: log_detail(); break;
		case 'show_detail'			: show_detail(); break;
		case 'del_data'				: del_data(); break;
		case 'download_sip'			: download_sip(); break;
		case 'list_file_local'		: list_file_local(); break;
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
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['flagtrans'];
		$offset = $_REQUEST['offset'];
		$input_search = $_REQUEST['input_search'];
		$input_search = str_replace('8764346466435364647768799667654537543756',' ',$input_search);
		$limit = 5;
		$i = $offset;
		$c = 0;
		
		$blth_balik = substr($blth,-4).substr($blth,0,2);
		
		$BLTH_BARUS = BLTH_BARU;
		
		if($blth_balik>$BLTH_BARUS){
			$tabeldetail = "detail_" . $blth."_".$flagtrans;
			$sql_cek = "SELECT a.m_loading_id FROM m_loading a LEFT JOIN $tabeldetail b ON a.m_loading_id=b.m_loading_id 
			WHERE a.blth='$blth' AND a.flagtrans='$flagtrans'";
			$exe_cek = @pg_query($sql_cek);
			if($exe_cek){
				$tabeldetail = "detail_" . $blth."_".$flagtrans;
				$tambahlokasipdf = $flagtrans."/";
			}else{
				$tabeldetail = "detail";
				$tambahlokasipdf = "";
			}
		}else{
			$tabeldetail = "detail";
			$tambahlokasipdf = "";
		}

		$GLOBALS['tr_email_sk_br']= cek_sk_tr($blth, $flagtrans);
		
		
		/*$sql_sel = "SELECT m_loading_id, userid, to_char(create_date,'yyyy-mm-dd HH24:mi:ss') as create_date, loading_file, total_halaman, total_customer FROM m_loading WHERE blth = '$blth' and flagrans = '$flagtrans' and (upper(loading_file) like upper('%".$input_search."%') or upper(userid) like upper('%".$input_search."%')) ORDER BY create_date DESC";*/
		$sql_sel = "
			SELECT
				a.m_loading_id, a.userid, to_char(a.create_date,'yyyy-mm-dd HH24:mi:ss') as create_date,
				a.loading_file, a.total_halaman, a.total_customer,
				a.total_halaman_cetak, a.total_customer_cetak
			FROM
				m_loading a
				LEFT JOIN $tabeldetail b
					ON a.m_loading_id = b.m_loading_id
			WHERE
				a.blth = '$blth'
				and a.flagtrans = '$flagtrans'
				and (
					upper(a.loading_file) like upper('%".$input_search."%')
					or upper(a.userid) like upper('%".$input_search."%')
					or upper(b.nomor_rekening) like upper('%".$input_search."%')
				)
			GROUP BY
				a.m_loading_id, a.userid, a.create_date,
				a.loading_file, a.total_halaman, a.total_customer,
				a.total_halaman_cetak, a.total_customer_cetak
			ORDER BY
				a.create_date DESC";
		$sql_show = $sql_sel." LIMIT $limit OFFSET $offset";
		$qry_sel = pg_query($sql_sel) or die('ERROR select: '.$sql_sel);
		$qry_show = pg_query($sql_show) or die('ERROR show: '.$sql_show);
		//echo $sql_show;
		$jumlah_data = pg_num_rows($qry_sel);
		?>
        <table width="100%" align="center" cellpadding="1" cellspacing="2" border="0">
        	<tr class="table_header">
            	<td>NO</td>
            	<td>User</td>
            	<td>Waktu</td>
            	<td>Nama File</td>
            	<td>Total Halaman</td>
            	<td>Total <i>Customer</i></td>
            	<td>Total <i>no_email</i></td>
            	<td>Total Halaman Cetak</td>
            	<td>Total <i>Customer</i> Cetak</td>
            	<td>ACT</td>
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
			$total_halaman_cetak = $row_show['total_halaman_cetak'];
			$total_customer_cetak = $row_show['total_customer_cetak'];
			
			
			$sql_cek_tr_email = "SELECT count(*) as jml FROM ".$GLOBALS['tr_email_sk_br']." WHERE loading_id = $m_loading_id and (status_sample is null or status_sample = FALSE)";
			$qry_cek_tr_email = pg_query($sql_cek_tr_email) or die('ERROR: '.$sql_cek_tr_email);
			$row_cek = pg_fetch_assoc($qry_cek_tr_email);
			if($row_cek['jml'] == 0){
				$del_data = '&nbsp;<a onclick="del_data('.$m_loading_id.')"><img class="vtip" title="delete" src="../images/icons/del_data.png" /></a>';
			}else{
				$del_data = "";
			}
			
			
			$csv = str_replace(substr($nama_file,-4),'',$nama_file);
			$file_csv = '../pdf/'.$tambahlokasipdf.$blth.'/'.$csv.'/'.$csv.'.csv';
			if(file_exists($file_csv))
				$download_csv = '&nbsp;<a href="'.$file_csv.'" target="_blank"><img class="vtip" title="password pdf" src="../images/icons/download_file.png" /></a>';
			else
				$download_csv = '';
			
			/*
			$csv2 = str_replace(substr($nama_file,-4),'',$nama_file);
			$file_csv2 = '../pdf/'.$tambahlokasipdf.$blth.'/'.$csv2.'/'.$csv2.'_cetak.csv';
			if(file_exists($file_csv2))
				$download_csv2 = '&nbsp;<a href="'.$file_csv2.'" target="_blank"><img class="vtip" title="listing cetak" src="../images/icons/download_file.png" /></a>';
			else
				$download_csv2 = '';
			*/
			
			$file_txt = '../pdf/'.$tambahlokasipdf.$blth.'/'.$csv.'/no_email_'.$csv.'.TXT';
			if(file_exists($file_txt)){
				$download_txt = '&nbsp;<a href="'.$file_txt.'" target="_blank"><img class="vtip" title="no email txt" src="../images/icons/edit_data.png" /></a>';
				$no_email = '&nbsp;<a href="'.$file_txt.'" target="_blank">data_no_email</a>';
			}else{
				$download_txt = '';
				$no_email = '0';
			}
			
			$cetak = $csv . "_cetak.pdf";
			$file_cetak = '../pdf/'.$tambahlokasipdf.$blth.'/'.$csv.'/'.$cetak;
			if(file_exists($file_cetak))
				$download_cetak = '&nbsp;<a href="'.$file_cetak.'" target="_blank"><img class="vtip" title="cetak" src="../images/icons/printer_icon.png" /></a>';
			else
				$download_cetak = '';
			
			
			$zip = $csv . "_zip.zip";
			$file_zip = '../pdf/'.$tambahlokasipdf.$blth.'/'.$csv.'/produksi/'.$zip;
			if(file_exists($file_zip))
				$download_zip = '&nbsp;<a href="'.$file_zip.'" target="_blank"><img class="vtip" title="cetak" src="../images/icons/printer_icon.png" /></a>';
			else
				$download_zip = '';
			
			
			$p01 = $csv . "_p01.pdf";
			$file_p01 = '../pdf/'.$tambahlokasipdf.$blth.'/'.$csv.'/'.$p01;
			if(file_exists($file_p01))
				$download_p01 = '<br><a href="'.$file_p01.'" title="P01" target="_blank">P01</a>';
			else
				$download_p01 = '';
			
			
			$p02 = $csv . "_p02.pdf";
			$file_p02 = '../pdf/'.$tambahlokasipdf.$blth.'/'.$csv.'/'.$p02;
			if(file_exists($file_p02))
				$download_p02 = '&nbsp;<a href="'.$file_p02.'" title="P02" target="_blank">P02</a>';
			else
				$download_p02 = '';
			
			
			$q01 = $csv . "_q01.pdf";
			$file_q01 = '../pdf/'.$tambahlokasipdf.$blth.'/'.$csv.'/'.$q01;
			if(file_exists($file_q01))
				$download_q01 = '&nbsp;<a href="'.$file_q01.'" title="Q01" target="_blank">Q01</a>';
			else
				$download_q01 = '';
			
			
			$rec = $csv . "_rec.pdf";
			$file_rec = '../pdf/'.$tambahlokasipdf.$blth.'/'.$csv.'/'.$rec;
			if(file_exists($file_rec))
				$download_rec = '&nbsp;<a href="'.$file_rec.'" title="RECONCILE" target="_blank">REC</a>';
			else
				$download_rec = '';
			
			
			$sip = $csv . "_sip.txt";
			$file_sip = '../pdf/'.$tambahlokasipdf.$blth.'/'.$csv.'/'.$sip;
			if(file_exists($file_sip))
				$download_sip = '&nbsp;<a onclick="download_sip('.$m_loading_id.')" title="SIP">SIP</a>';
			else
				$download_sip = '';
			
			
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
            	<td align="right"><?php echo $no_email?></td>
            	<td align="right"><? echo number_format($total_halaman_cetak,0,' ','')?></td>
            	<td align="right"><? echo number_format($total_customer_cetak,0,' ','')?></td>
            	<td align="center">
                	<a onclick="show_det(<?=$m_loading_id?>)"><img class="vtip" title="view" src="../images/icons/icon_view.gif" /></a>
                    <?php echo $del_data; ?>
                    <?php echo $download_csv; ?>
                    <?php echo $download_csv2; ?>
                    <?php echo $download_txt; ?>
                    <?php echo $download_cetak; ?>
                    <?php echo $download_zip; ?>
                    <?php echo $download_p01; ?>
                    <?php echo $download_p02; ?>
                    <?php echo $download_q01; ?>
                    <?php echo $download_rec; ?>
                    <?php echo $download_sip; ?>
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
		?>
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
		$m_loading_id = $_REQUEST['m_loading_id'];
		$flagtrans = $_REQUEST['flagtrans'];
		$blth = $_REQUEST['blth'];
		$input_search = $_REQUEST['input_search'];
		$input_search = str_replace('8764346466435364647768799667654537543756','%',$input_search);
		
		$blth_balik = substr($blth,-4).substr($blth,0,2);
		
		if($blth_balik>$BLTH_BARU){
			$tabeldetail = "detail_" . $blth."_".$flagtrans;
			$sql_cek = "SELECT a.m_loading_id FROM m_loading a LEFT JOIN $tabeldetail b ON a.m_loading_id=b.m_loading_id 
			WHERE a.blth='$blth' AND a.flagtrans='$flagtrans'";
			$exe_cek = @pg_query($sql_cek);
			if($exe_cek){
				$tabeldetail = "detail_" . $blth."_".$flagtrans;
				$tambahlokasipdf = $flagtrans."/";
			}else{
				$tambahlokasipdf = "";
				$tabeldetail = "detail";
			}
		}else{
			$tambahlokasipdf = "";
			$tabeldetail = "detail";
		}
		
		$offset = $_REQUEST['offset'];
		$limit = 25;
		$i = $offset;
		$c = 0;
		
		$sql_sel = "SELECT nomor_rekening, nama, alamat1, alamat2, alamat3, size_pdf, zipcode, blth, nama_file, email, 
		pdf_name, password_pdf, jml_hlm, m_loading_id FROM $tabeldetail WHERE m_loading_id = '$m_loading_id' and (upper(nomor_rekening) like upper('%".$input_search."%') or upper(nama) like upper('%".$input_search."%')) ORDER BY detail_id ASC";
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
            	<td width="12%">Email</td>
            	<td width="12%">Pdf_file</td>
            	<td width="12%">Password_pdf</td>
            	<td width="11%">Jumlah lembar</td>
            	<td width="11%">Size Pdf (Kb)</td>
            	<td width="11%">Lokasi File</td>
            </tr>
        <?php
		while($row_show=pg_fetch_array($qry_show)){
			$i++;
			$nomor_rekening = $row_show['nomor_rekening'];
			$nama = $row_show['nama'];
			$alamat1 = $row_show['email'];
			$pdf_name = $row_show['pdf_name'].'.pdf';
			$password_pdf = $row_show['password_pdf'];
			$jml_hlm = $row_show['jml_hlm'];
			$zipcode = $row_show['zipcode'];
			$file_induk = $row_show['nama_file'];
			$size_pdf = $row_show['size_pdf'];
			
			$lokasi_file = "../pdf/".$tambahlokasipdf.$row_show['blth'] ."/". substr($row_show['nama_file'],0,strlen($row_show['nama_file'])-4);
			//$id = $row_show['m_loading_id'];		
			if($i%2==0){
				$cls = 'table_row_odd';
			}else{
				$cls = 'table_row_even';
			}
			
			if ($flagtrans=='MT')
			{
				$x = str_replace(array('.pdf','.PDF'),'',$pdf_name);
				$w1 = explode('_',$x);
				$filex=$row_show['nama_file'];
				if ( preg_match("/_EST_/i", $filex) || preg_match("/_E_/i", $filex) )
				{
					$f = 'E';
				}else if ( preg_match("/_CE_/i", $filex) )	{
					$f = 'CE';
				}else if(  preg_match("/_C_/i", $filex) ){
					$f = 'C';
				}
				
				$lokasi_file = "../pdf/".$tambahlokasipdf.$row_show['blth'] ."/". substr($row_show['nama_file'],0,strlen($row_show['nama_file'])-4)."/".$w1[2]."/".$f;
			}
			?>
            <tr class="<?=$cls?>">
            	<td align="center"><?=$i?></td>
            	<td><?=$nomor_rekening?></td>
            	<td><?=$nama?></td>
            	<td><?=$alamat1?></td>
            	

                
                <td align="center">
				
                 <? 
				 if(file_exists($lokasi_file ."/". $pdf_name))
				 {
					  echo "<a href= '".$lokasi_file ."/". $pdf_name.  " ' target='_blank' >". $pdf_name ."</a>";
				 }else{
					 echo '<b style="color:red">PDF NOT FOUND</b>';
				 }
				
				 
				 ?>
                
				
                </td>
                
           	  <td align="center"><?=$password_pdf?></td>
           	  <td align="right"><?=number_format($jml_hlm,0)?> &nbsp </td>
           	  <td><?=number_format($size_pdf)?></td>
            	<td><?=$file_induk?></td>
            </tr>
            <?php
		}
		?>
            <tr>
                <td align="center" colspan="11">
                    <?php
					echo paging($offset,$jumlah_data,$limit,'div_detail','script_loading_detail.php','act=show_detail&blth='.$blth.'&flagtrans='.$flagtrans.'&m_loading_id='.$m_loading_id.'&input_search='.$input_search);
                    ?>
                </td>
            </tr>
        </table>
        <?php
	}
	
	function del_data(){
		$m_loading_id = $_REQUEST['m_loading_id'];
		
		$sql = "SELECT blth, loading_file,flagtrans FROM m_loading WHERE m_loading_id = $m_loading_id";
		$qry = pg_query($sql) or die('ERROR: '.$sql);
		$row = pg_fetch_assoc($qry);
		$blth = $row['blth'];
		$flagtrans = $row['flagtrans'];
		$loading_file = $row['loading_file'];
		
		
		$blth_balik = substr($blth,-4).substr($blth,0,2);
		
		$BLTH_BARUS = BLTH_BARU;
		if($blth_balik>$BLTH_BARUS){
			$tabeldetail = "detail_" . $blth."_".$flagtrans;
			$sql_cek = "SELECT a.m_loading_id FROM m_loading a LEFT JOIN $tabeldetail b ON a.m_loading_id=b.m_loading_id 
			WHERE a.blth='$blth' AND a.flagtrans='$flagtrans'";
			$exe_cek = @pg_query($sql_cek);
			if($exe_cek){
				$tabeldetail = "detail_" . $blth."_".$flagtrans;
			}else{
				$tabeldetail = "detail";
			}
		}else{
			$tabeldetail = "detail";
		}
		
		$tabeldetail_mt = "detail_" . $blth."_".'mt';
				$sqlx ="UPDATE $tabeldetail_mt
				SET no_rek_asli = NULL
				WHERE tipe_proses_ematerai='EMATERAI'
				AND nomor_rekening IN (SELECT nomor_rekening FROM $tabeldetail WHERE m_loading_id = $m_loading_id)";//die($sqlx);
				@pg_query($sqlx);
		
		$sql_del = "DELETE FROM m_loading WHERE m_loading_id = $m_loading_id;
					DELETE FROM $tabeldetail WHERE m_loading_id = $m_loading_id;
					DELETE FROM detail_cetak WHERE m_loading_id = $m_loading_id;
					DELETE FROM antrian_email_history WHERE loading_id = $m_loading_id;
					
					DELETE FROM antrian_email WHERE loading_id = $m_loading_id;";
		$qry_del = pg_query($sql_del) or die('ERROR: '.$qry_del);
		
		$file = str_replace(substr($loading_file,-4),'',$loading_file);
		if(trim($blth) == "" || trim($file) == ""){
			echo "Gagal hapus folder pdf !!!";
		}else{
			$dir_pdf = '../pdf/'.$flagtrans.'/'.$blth.'/'.$file.'/';
			hapus_folder($dir_pdf);
			rmdir(rtrim($dir_pdf,'/'));
		}
	}
	
	function download_sip(){
		$m_loading_id = $_REQUEST['m_loading_id'];
		
		$sql = "SELECT blth, loading_file, flagtrans FROM m_loading WHERE m_loading_id = $m_loading_id";
		$qry = pg_query($sql) or die('ERROR: '.$sql);
		$row = pg_fetch_assoc($qry);
		$blth = $row['blth'];
		$loading_file = $row['loading_file'];
		$flagtrans = $row['flagtrans'];
		
		$file = str_replace(substr($loading_file,-4),'',$loading_file);
		$blth_pdf = '../pdf/'.$flagtrans.'/'.$blth.'/';
		$blth_file_pdf = $blth_pdf.$file.'/';
		$filename = $file . "_sip.txt";
		$cetak_sip = $blth_file_pdf.$filename;
		
		if(file_exists($cetak_sip)){
			$allowed_ext = array (
				'zip' => 'application/zip',
				'pdf' => 'application/pdf',
				'txt' => 'application/txt'
			);
			
			$fname=basename($cetak_sip);
			$fext=strtolower(substr(strrchr($fname,"."),1));
			
			if ($allowed_ext[$fext] == '') {
				  $mtype = '';
				  // mime type is not set, get from server settings
				  if (function_exists('mime_content_type')) {
					$mtype = mime_content_type($cetak_sip);
				  }
				  else if (function_exists('finfo_file')) {
					$finfo = finfo_open(FILEINFO_MIME); // return mime type
					$mtype = finfo_file($finfo, $cetak_sip);
					finfo_close($finfo);  
				  }
				  if ($mtype == '') {
					$mtype = "application/force-download";
				  }
			}
			else {				  
				 $mtype = $allowed_ext[$fext];
			}
			
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
			header("Cache-Control: public");
			header("Content-Description: File Transfer");
			header("Content-Type: application/txt");
			header("Content-Disposition: attachment; filename=$fname");
			header("Content-Transfer-Encoding: binary");
			
			@readfile($cetak_sip);
			//@unlink($cetak_sip);
		}else{
			echo "FILE NOT EXISTS";
		}
	}
	
	
	function list_file_local(){
		$dir = "../file_consolidate/";
		$list = '';
		if(is_dir($dir)){
			$list .= '<select id="view_folder" name="view_folder" class="combobox" style="width:400px;height:23px;" onChange="cekFile2()">';
			$list .= '<option value="null">-- pilih --</option>';
			$list .= filesInDir($dir);
			$list .= '</select>';
		}else{
			$list .= 'Directory file_zip not found pada: '."../file_consolidate/";
		}
		echo $list;
	}
	
	function filesInDir($tdir){
		$list = '';
		$dirs = scandir($tdir);
        foreach($dirs as $file)
        {
            if (($file == '.')||($file == '..'))
            {
            }
            elseif (is_dir($tdir.'/'.$file))
            {
                #hanya mencari file dalam folder paling atas tidak ke dalam sub folder
				//filesInDir($tdir.'/'.$file);
            }
            else
            {
                #hanya memproses file zip saja
				if(strtolower(substr($file,-4))=='.dat'){
					$list .= '<option value="'.$file.'">'.$file.'</option>';
				}
            }
        }
		return $list;
	}
?>