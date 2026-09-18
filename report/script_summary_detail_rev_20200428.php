<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/function.php"); ?>
<? require_once("../include/PHPMailer/class.phpmailer.php"); 
/** Include path **/
ini_set('include_path', ini_get('include_path').';../include/PHPExcel/Classes/');
ini_set('memory_limit', '-1');
/** PHPExcel */
include '../include/PHPExcel/PHPExcel.php';
/** PHPExcel_IOFactory */
include '../include/phpexcel/PHPExcel/IOFactory.php';
?>
<?
	$act = $_REQUEST['act'];
	$con = pg_connect($connection) or die("Could not connect to database!");
	
	/***SETTING JUMLAH HALAMAN BERBAYAR***/
	define("HLM_BERBAYAR","4");
	define("SIZE_BERBAYAR","300");
	
	if($_REQUEST['xls']=='y') {
	HeaderingExcel("Antrian_email.xls");
	}
	
	#if($_REQUEST['sof']=='y') 
	#{
		
		#$filenya = $_REQUEST['cycle'];
		//die('aa '.$filenya);
	#sof_text($con,$filenya);
	#}
	
	$arr_pr = explode('|', $_REQUEST['pr']);
	$flagtrans = $arr_pr[0];
	$menuid = $arr_pr[1];
	$blth = $_REQUEST['blth'];
	$cycle = $_REQUEST['cycle'];
	$per_send = explode('|', $_REQUEST['periode_kirim']);
	$sent_from = $per_send[0];
	$sent_to = $per_send[1];
	
	$curloc= '../include/phpexcel/';
	$namafilexls ='summary_detail_p_'.$blth.'_pr_'.$flagtrans.'.xls';
	$locateFile ='../excel/'.$namafilexls.'';
	
		$sql_produk = "SELECT produk FROM mproduk WHERE flagtrans='$flagtrans'";
		$exe_produk = pg_query($sql_produk);
		$row_produk = pg_fetch_array($exe_produk);
		$nama_produk = $row_produk['produk'];
		$title = str_replace("..::","",$title);
		$title = str_replace("::..","",$title);
		$header_utama = $title ." | ". $client ." | ". $nama_produk;
	
	switch($act){
		case 'blth': blth(); break;
		case 'view': view($header_utama); break;
		case 'xls': export_excel_ckemail($blth, $flagtrans, $cycle, $per_send, $curloc, $locateFile);
		case 'ba' : export_ba_ckemail($client, $blth, $flagtrans, $cycle, $per_send, $curloc, $locateFileBA); break;
		case 'show_list_pdf': show_list_pdf($header_utama); break;
		case 'show_list_hal': show_list_hal($header_utama); break;
		case 'show_list_email': show_list_email($con,$header_utama); break;
		case 'show_total_email': show_total_email($con,$header_utama); break;
		case 'show_email_dikirim': show_email_dikirim($con,$header_utama); break;
		case 'show_list_antrian': show_list_antrian($header_utama); break;
		case 'show_email_sukses': show_email_sukses($header_utama); break;
		case 'export_excel_show_email_sukses': export_excel_show_email_sukses($blth, $flagtrans, $cycle, $per_send, $curloc, $locateFile); 
		case 'export_excel_show_email_gagal': export_excel_show_email_gagal($blth, $flagtrans, $cycle, $per_send, $curloc, $locateFile); 
		case 'export_excel_show_email_dikirim': export_excel_show_email_dikirim($blth, $flagtrans, $cycle, $per_send, $curloc, $locateFile); 
		case 'export_show_total_email': export_show_total_email($blth, $flagtrans, $cycle, $per_send, $curloc, $locateFile); 
		case 'show_email_gagal': show_email_gagal($header_utama); break;
		case 'show_email_terbaca': show_email_terbaca($header_utama); break;
		case 'show_email_others': show_email_others($header_utama); break;
		case 'show_email_sample_sukses': show_email_sample_sukses($header_utama); break;
		case 'show_email_sample_gagal': show_email_sample_gagal($header_utama); break;
		case 'view_bodymailterbaca': view_bodymailterbaca();break;
		case 'view_bodymailothers': view_bodymailothers();break;
		case 'view_template': view_template(); break;
		case 'load_template': load_template(); break;
	}
	
	function blth(){
		$flagtrans = $_REQUEST['flagtrans'];
		?>
        <select id="blth" name="blth" class="combobox" onChange="cek()">
        	<option value=""></option>
			<?php
            $sql_sel_blth = "SELECT blth
                                FROM m_loading
                                WHERE flagtrans = '$flagtrans'
								GROUP BY blth
                                ORDER BY substring(blth, 3, 4) desc, substring(blth, 1, 2) desc";
            $qry_sel_blth = pg_query($sql_sel_blth) or die('ERROR select blth: '.$sql_sel_blth);
			while($row_sel_blth=pg_fetch_array($qry_sel_blth)){
				?>
                <option value="<?=$row_sel_blth['blth']?>"><?=$row_sel_blth['blth']?></option>
                <?php
			}
		?>
        </select>
        <?php
	}
	
	function view($header_utama){
		$arr_pr = explode('|', $_REQUEST['pr']);
		$flagtrans = $arr_pr[0];
		$menuid = $arr_pr[1];
		$cekemail_p = $_REQUEST['cekemail'];
		$cekemail_p = 0;	//SEMENTARA FITUR CEK BOUNCE-BACK DINON-AKTIFKAN DULU
		$blth = $_REQUEST['blth'];
		$cycle = $_REQUEST['cycle'];
		$per_send = explode('|', $_REQUEST['periode_kirim']);
		$sent_from = $per_send[0];
		$sent_to = $per_send[1];
		$get_respon ='';
		
		$total_selisih = 0;
		
		$blth_balik = substr($blth,-4).substr($blth,0,2);
		
			$BLTH_BARU = BLTH_BARU;
			if($blth_balik>$BLTH_BARU){
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

		
		if ($cekemail_p == 1){
			//$get_respon = get_from_mailserver($blth, $flagtrans, $cycle, $per_send);
			$get_respon = get_from_mailserver($blth, $flagtrans, $cycle, $per_send,'',1);
		}
		
	if (($get_respon=='connect') || (empty($cekemail_p))){
		?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>REPORT - SUMMARY DETAIL</title>
            </head>
            <? require_once("../include/script.php"); ?>
            <body>
            	<table align="center" border="0" width="1000">
                	<tr>
                    	<td>
                        	<table align="center" border="0" cellpadding="2" cellspacing="0" width="100%">
                            	<tr height="40">
                                	<td class="title" colspan="2" align="center" valign="middle">
                                    	<?php
										judul($menuid);
										?>
                                    </td>
                                </tr>
                                <tr height="20">
                                	<td colspan="2"><b><u><?=$header_utama?></u></b></td>
                                </tr>
								<tr height="20">
                                	<td width="110"><b>Periode</b></td>
                                    <td>: <?=$blth?></td>
                                </tr>
								<?php
									if (!empty($cycle)){
										echo '<tr height="20">
												<td><b>Cycle</b></td>
												<td>: '.$cycle.'</td>
											</tr>';
									}
									if (!empty($sent_from)){
										echo '<tr height="20">
												<td><b>Tanggal Kirim</b></td>
												<td>: '.$sent_from.' s/d '.$sent_to.'</td>
											</tr>';
									}
								?>
                            </table>
                        </td>
                    </tr>
                    <tr>
                    	<td>
                        	<table align="center" border="1" cellpadding="1" cellspacing="0" width="100%" style="border-collapse:collapse;">
                            	<tr height="30">
                                	<td width="4%" align="center" rowspan="2" bgcolor="#79BAEC"><b>No</b></td>
                                	<td align="center" rowspan="2" bgcolor="#79BAEC"><b>Tanggal Loading</b></td>
                                	<td align="center" rowspan="2" bgcolor="#79BAEC"><b> No EXIST</b></td>
                                	<td align="center" rowspan="2" bgcolor="#79BAEC"><b> Summary Hana Bank</b></td>
                                	<td align="left" rowspan="2" bgcolor="#79BAEC">&nbsp;<b>Nama File</b></td>
                                	<td align="center" rowspan="2" bgcolor="#79BAEC"><b>Jml PDF</b></td>
                                	<td align="center" rowspan="2" bgcolor="#79BAEC"><b>Jml Halaman</b></td>
                                	<td align="center" colspan="3" width="15%" bgcolor="#79BAEC"><b>PDF</b></td>
                                	<!--<td align="center" rowspan="2" bgcolor="#cd853f"><b>SIZE<br/>(berbayar)</b></td>-->
                                	<td align="center" colspan="8" width="30%" bgcolor="#79BAEC"><b>EMAIL</b></td>
                                	<td align="center" colspan="2" width="10%" bgcolor="#79BAEC"><b>SAMPLE</b></td>
                                </tr>
								<tr bgcolor="#add8e6" style="font-size:8pt;font-family:helvetica;">
								<td width="5%" align="center"><b>(<=<?php echo HLM_BERBAYAR;?>hlm)</b></td>
								<td width="5%" align="center"><b>(><?php echo HLM_BERBAYAR;?>hlm)</b></td>
								<td width="5%" align="center" bgcolor="#ffd700"><b>hlm<br/>Berbayar</b></td>
								<td align="center" width="5%" bgcolor="#F08080"><b>SELISIH</b></td>
								<td align="center" width="5%" bgcolor="#cd853f"><b>Total</b></td>
								<td align="center" width="5%" bgcolor="#CC9999"><b>Antrian</b></td>
								<td align="center" width="5%" bgcolor="#CC9999"><b>Dikirim</b></td>
                                <td align="center" width="5%" bgcolor="#CC9999"><b>Sukses</b></td>
                                <td align="center" width="5%" bgcolor="#CC9999"><b>Gagal</b></td>
								<td align="center" width="8%" bgcolor="#CC9999"><b>Terbaca</b></td>
								<td align="center" width="5%" bgcolor="#CC9999"><b>Others</b></td>
								<td align="center" width="5%"><b>Sukses</b></td>
                               	<td align="center" width="5%"><b>Gagal</b></td>
								</tr>
                                <?php
									if (!empty($cycle)){
										#$w_cycle ="and a.loading_file = '".$cycle."'";
										$w_cycle ="and loading_file = '".$cycle."'";
									}else{
										$w_cycle ="";
									}
									if (!empty($sent_from)){
										$js_pkirim ="AND (date_email_send BETWEEN '".$sent_from."' AND '".$sent_to."')";
										$p_kirim = "and a.m_loading_id in (select loading_id from tr_email where date_email_send BETWEEN '".$sent_from."' AND '".$sent_to."') ";
										$wh_total = "WHERE d.date_email_send BETWEEN '".$sent_from."' AND '".$sent_to."'";
									}else{
										$js_pkirim = "";
										$p_kirim = "";
										$wh_total = "";
									}
									//email terbaca--> AND (read_method is null OR read_method='outlook')
									$i = 0;
									$HLM_BERBAYAR = HLM_BERBAYAR;
									
									$sql_data = "


select 
a.m_loading_id,
											to_char(a.create_date,'yyyy-mm-dd HH24:MI') as create_date,
											a.loading_file,
											a.total_customer as jml_pdf,
											a.total_halaman as jml_lembar,
 c.jml_email_gratis,
 cb.jml_email_bayar,
 ds.total_hlm_berbayar,
 (te.total_email +  COALESCE(NULLIF(d.jml_antrian::text, ''),'0')::integer  ) as total_email ,
 te.total_email as jml_email,
 --d.jml_antrian
 COALESCE(NULLIF(d.jml_antrian::text, ''),'0') as jml_antrian,
 e.email_sukses,
 f.email_gagal,
 et.email_terbaca,
 gt.gambar_terbaca,


											g.sample_sukses,
											h.sample_gagal,
											0 as jml_email_others
											
											
										

from (select * from m_loading a where blth='$blth' AND flagtrans='$flagtrans' ".$w_cycle.") a
LEFT JOIN (SELECT COUNT(nomor_rekening) as jml_email_gratis, m_loading_id FROM $tabeldetail WHERE jml_hlm <= $HLM_BERBAYAR GROUP BY m_loading_id) as c ON a.m_loading_id=c.m_loading_id
LEFT JOIN (SELECT COUNT(nomor_rekening) as jml_email_bayar, m_loading_id FROM $tabeldetail WHERE jml_hlm > $HLM_BERBAYAR GROUP BY m_loading_id )as cb ON a.m_loading_id = cb.m_loading_id	
LEFT JOIN (SELECT SUM(jml_hlm-$HLM_BERBAYAR)AS total_hlm_berbayar,m_loading_id FROM $tabeldetail WHERE jml_hlm > $HLM_BERBAYAR GROUP BY m_loading_id ) ds ON a.m_loading_id = ds.m_loading_id


LEFT JOIN(
select loading_id as m_loading_id, count(*) as total_email from tr_email 
WHERE loading_id IN (select m_loading_id from m_loading where blth='$blth')  
and (status_sample = 'f' or status_sample is null) group by loading_id ) te ON a.m_loading_id = te.m_loading_id




LEFT JOIN(
select loading_id as m_loading_id, count(*) as jml_antrian from antrian_email 
WHERE loading_id IN (select m_loading_id from m_loading where blth='$blth')  
and (status_sample = 'f' or status_sample is null) group by loading_id ) d ON a.m_loading_id = d.m_loading_id




LEFT JOIN(
select loading_id as m_loading_id, count(*) as email_sukses from tr_email 
WHERE loading_id IN (select m_loading_id from m_loading where blth='$blth')  
and (status_sample = 'f' or status_sample is null )
and date_email_callback is null 
and nomor_rekening is not null 
AND status_sample IS NOT TRUE group by loading_id ) e ON a.m_loading_id = e.m_loading_id


LEFT JOIN(
select loading_id as m_loading_id, count(*) as email_gagal from tr_email 
WHERE loading_id IN (select m_loading_id from m_loading where blth='$blth')  
and (status_sample = 'f' or status_sample is null) 
and date_email_callback is not null 
and nomor_rekening is not null 
AND status_sample IS NOT TRUE group by loading_id )f ON a.m_loading_id = f.m_loading_id


LEFT JOIN(
select loading_id as m_loading_id, count(*) as email_terbaca from tr_email 
WHERE loading_id IN (select m_loading_id from m_loading where blth='$blth')  
and (status_sample = 'f' or status_sample is null) 
and date_email_callback is null AND tgl_read is not null
and nomor_rekening is not null 
AND status_sample IS NOT TRUE group by loading_id )et ON a.m_loading_id = et.m_loading_id



LEFT JOIN(
select loading_id as m_loading_id, count(*) as gambar_terbaca from tr_email 
WHERE loading_id IN (select m_loading_id from m_loading where blth='$blth')  
and (status_sample = 'f' or status_sample is null) 
and date_email_callback is null AND tgl_read is not null AND lower(read_method)='img_email'
and nomor_rekening is not null 
AND status_sample IS NOT TRUE group by loading_id )gt ON a.m_loading_id = gt.m_loading_id





LEFT JOIN(
select loading_id as m_loading_id, count(*) as sample_sukses from tr_email 
WHERE loading_id IN (select m_loading_id from m_loading where blth='$blth')  
and status_sample = 't'
and date_email_callback is null 
and nomor_rekening is not null group by loading_id )g ON a.m_loading_id = g.m_loading_id




LEFT JOIN(
select loading_id as m_loading_id, count(*) as sample_gagal from tr_email 
WHERE loading_id IN (select m_loading_id from m_loading where blth='$blth')  
and status_sample = 't' 
and date_email_callback is not null 
and nomor_rekening is not null  group by loading_id )h ON a.m_loading_id = h.m_loading_id


ORDER BY a.create_date ASC
			";
									
									
									
									
									
									
									/*
									
									$sql_data = "
										SELECT
											a.m_loading_id,
											to_char(a.create_date,'yyyy-mm-dd HH24:MI') as create_date,
											a.loading_file,
											a.total_customer as jml_pdf,
											a.total_halaman as jml_lembar,
											c.jml_email_gratis,
											cb.jml_email_bayar,
											ds.total_hlm_berbayar,
											te.total_email,
											je.jml_email,
											d.jml_antrian,
											e.email_sukses,
											et.email_terbaca,
											gt.gambar_terbaca,
											f.email_gagal,
											g.sample_sukses,
											h.sample_gagal,
											(SELECT DISTINCT(count(tr_email_id)) as jml_email_others FROM bounce_inbox WHERE blth='$blth' AND m_loading_id=a.m_loading_id  AND sample_status is not true)AS jml_email_others
										FROM m_loading a
											LEFT JOIN $tabeldetail b
												ON a.m_loading_id = b.m_loading_id
											LEFT JOIN (
												SELECT COUNT(nomor_rekening) as jml_email_gratis, m_loading_id
												FROM $tabeldetail WHERE jml_hlm <= $HLM_BERBAYAR
												GROUP BY m_loading_id
											) c
												ON a.m_loading_id = c.m_loading_id
											
											LEFT JOIN (
												SELECT COUNT(nomor_rekening) as jml_email_bayar, m_loading_id
												FROM $tabeldetail WHERE jml_hlm > $HLM_BERBAYAR
												GROUP BY m_loading_id
											) cb
												ON a.m_loading_id = cb.m_loading_id		
												
								
											LEFT JOIN (
												SELECT count(tr_email_id) as jml_email, loading_id
												FROM tr_email
												WHERE status_sample IS NOT TRUE ".$js_pkirim."
												GROUP BY loading_id
											) je
												ON a.m_loading_id = je.loading_id
											
											LEFT JOIN(
											SELECT COUNT(*)as total_email,a.m_loading_id FROM m_loading a 
LEFT JOIN $tabeldetail b ON a.m_loading_id = b.m_loading_id 
LEFT JOIN antrian_email c ON a.m_loading_id = c.loading_id and b.nomor_rekening = c.nomor_rekening and (c.status_sample = 'f' or c.status_sample is null) 
LEFT JOIN tr_email d ON a.m_loading_id = d.loading_id and b.nomor_rekening = d.nomor_rekening 
and (d.status_sample = 'f' or d.status_sample is null) 
LEFT JOIN antrian_email_history e ON d.antrian_id = e.antrian_id $wh_total GROUP BY a.m_loading_id 
											) te ON a.m_loading_id = te.m_loading_id
											
											
											
											
												
											LEFT JOIN (
												SELECT SUM(jml_hlm-$HLM_BERBAYAR)AS total_hlm_berbayar,m_loading_id 
												FROM $tabeldetail WHERE jml_hlm > $HLM_BERBAYAR 
												GROUP BY m_loading_id
											) ds ON a.m_loading_id = ds.m_loading_id		
											
											LEFT JOIN (
												SELECT count(antrian_id) as jml_antrian, loading_id
												FROM antrian_email
												GROUP BY loading_id
											) d
												ON a.m_loading_id = d.loading_id

											LEFT JOIN (
												SELECT count(*)  as email_sukses, loading_id
												FROM tr_email a
												WHERE date_email_callback is null ".$js_pkirim."
												and a.nomor_rekening is not null
												AND status_sample IS NOT TRUE
												GROUP BY loading_id
												) e
												ON a.m_loading_id = e.loading_id
												
											LEFT JOIN (
												SELECT count(tr_email_id) as email_gagal, loading_id
												FROM tr_email a 
												WHERE date_email_callback is not null ".$js_pkirim."
												and a.nomor_rekening is not null
												AND status_sample IS NOT TRUE
												GROUP BY loading_id
											) f
												ON a.m_loading_id = f.loading_id
												
											LEFT JOIN (
												SELECT count(tr_email_id) as email_terbaca, loading_id
												FROM (select * from tr_email where tgl_read is not null) a
												WHERE date_email_callback is null ".$js_pkirim."
												AND status_sample IS NOT TRUE
												GROUP BY loading_id
											) et
												ON a.m_loading_id = et.loading_id
												
											LEFT JOIN (
												SELECT count(tr_email_id) as gambar_terbaca, loading_id
												FROM (select * from tr_email where tgl_read is not null) a
												WHERE date_email_callback is null ".$js_pkirim."
												AND status_sample IS NOT TRUE 
												AND read_method = 'img_email'
												GROUP BY loading_id
											) gt
												ON a.m_loading_id = gt.loading_id
												
											LEFT JOIN (
												SELECT count(tr_email_id) as sample_sukses, loading_id
												FROM tr_email a 
												WHERE date_email_callback is null ".$js_pkirim."
												and a.status_sample is true 
												GROUP BY loading_id
											) g
												ON a.m_loading_id = g.loading_id
											LEFT JOIN (
												SELECT count(tr_email_id) as sample_gagal, loading_id
												FROM tr_email a 
												WHERE date_email_callback is not null ".$js_pkirim."
												and a.status_sample is true 
												GROUP BY loading_id
											) h
												ON a.m_loading_id = h.loading_id
										WHERE a.blth = '$blth' and a.flagtrans = '$flagtrans' 
										".$p_kirim."
										".$w_cycle."
										GROUP BY a.m_loading_id, to_char(a.create_date,'yyyy-mm-dd HH24:MI'), a.loading_file, a.total_customer, a.total_halaman,
											c.jml_email_gratis, te.total_email,cb.jml_email_bayar, je.jml_email, ds.total_hlm_berbayar, d.jml_antrian, e.email_sukses, f.email_gagal, et.email_terbaca, gt.gambar_terbaca, g.sample_sukses, h.sample_gagal
										ORDER BY a.m_loading_id
									"; 
									
									*/
									//echo $sql_data;
									$qry_data = pg_query($sql_data) or die('ERROR select summary detail: '.$sql_data);//echo $sql_data;
									while($row_data = pg_fetch_array($qry_data)){
										
										$row_data['jml_email_others'] = 0;
										
										$i++;
										$m_loading_id = $row_data['m_loading_id'];
										$count_asg = $row_data['jml_antrian'] + $row_data['email_sukses'] + $row_data['email_gagal'];
										if ($count_asg < $row_data['jml_email']){
											$msg_warning ='<img src="../images/warning.gif" title="Belum di proses"/>';
										}else{
											$msg_warning ='';
										}
										
										$csv = str_replace(substr($row_data['loading_file'],-4),'',$row_data['loading_file']);
										$file_csv = '../pdf/'.$flagtrans.'/'.$blth.'/'.$csv.'/no_email_'.$csv.'.txt';
										if(file_exists($file_csv))
											$download_csv = '&nbsp;<a href="'.$file_csv.'" target="_blank">file</a>';
										else
											$download_csv = '&nbsp;-';
										
										$file_hana_bank = '../tmp/hana_bank/hana_bank_'.$blth.'_'.$csv.'.DAT.xlsx';
										if(file_exists($file_hana_bank))
											$download_hana_bank = '&nbsp; <a href="'.$file_hana_bank.'" target="_blank">excel</a>';
										else
											$download_hana_bank = '&nbsp;-';
			
									?>
									<tr height="30">
										<td align="center"><?php echo $i?></td>
										<td align="center" style="font-size:8pt;" width="12%">
										<?php echo date("d-m-Y",strtotime($row_data['create_date']))?><br/>
										<span style="font-size:7pt;"><?php echo date("H:i:s",strtotime($row_data['create_date']))?></span>
										</td>
										<td align="left">&nbsp;<?php echo $download_csv;?></td>
										<td align="left">&nbsp;<?php echo $download_hana_bank;?></td>
										<td align="left">&nbsp;<?php echo $row_data['loading_file']?></td>
										
										<td align="right"><a onClick="window.open('script_summary_detail_rev.php?act=show_list_pdf&pr=<?=$flagtrans?>&blth=<?php echo $blth;?>&m_loading_id=<?php echo $m_loading_id?>')"><?php echo number_format($row_data['jml_pdf'],0,'',',')?></a>&nbsp;&nbsp;</td>
										<td align="right"><a onClick="window.open('script_summary_detail_rev.php?act=show_list_pdf&pr=<?=$flagtrans?>&blth=<?php echo $blth;?>&m_loading_id=<?php echo $m_loading_id?>')"><?php echo number_format($row_data['jml_lembar'],0,'',',')?></a>&nbsp;&nbsp;</td>
										<td align="right" bgcolor="#afeeee"><a onClick="window.open('script_summary_detail_rev.php?act=show_list_hal&tipe=0&pr=<?=$flagtrans?>&blth=<?php echo $blth;?>&m_loading_id=<?=$m_loading_id?>')"><?php echo number_format($row_data['jml_email_gratis'],0,'',',');?></a>&nbsp;&nbsp;</td>
										<td align="right" bgcolor="#afeeee"><a onClick="window.open('script_summary_detail_rev.php?act=show_list_hal&tipe=1&pr=<?=$flagtrans?>&blth=<?php echo $blth;?>&m_loading_id=<?=$m_loading_id?>')"><?php echo number_format($row_data['jml_email_bayar'],0,'',',');?></a>&nbsp;&nbsp;</td>
										<td align="right" bgcolor="#fafad2"><a onClick="window.open('script_summary_detail_rev.php?act=show_list_email&tipe=1&pr=<?=$flagtrans?>&blth=<?php echo $blth;?>&m_loading_id=<?=$m_loading_id?>')"><?php echo number_format($row_data['total_hlm_berbayar'],0,'',',');?></a>&nbsp;&nbsp;</td>
										
										<td align="right" bgcolor="#<?php 
										$xyz= ($row_data['jml_antrian']+$row_data['jml_email'])-$row_data['total_email'];
										echo ( !empty( $xyz ) ? 'F08080': '3CB371'); ?>"><?php echo number_format( ($row_data['jml_antrian']+$row_data['jml_email'])-$row_data['total_email'],0,'',',');?>&nbsp;&nbsp;</td>
										
										<td align="right" bgcolor="#deb887"><a onClick="window.open('script_summary_detail_rev.php?act=show_total_email&pr=<?=$flagtrans?>&blth=<?php echo $blth;?>&m_loading_id=<?=$m_loading_id?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>')"><?php echo number_format($row_data['total_email'],0,'',',');?></a>&nbsp;&nbsp;</td>
										<td align="right" bgcolor="#DDBBBB"><a onClick="window.open('script_summary_detail_rev.php?act=show_list_antrian&pr=<?=$flagtrans?>&blth=<?php echo $blth;?>&m_loading_id=<?php echo $m_loading_id?>')"><?php echo number_format($row_data['jml_antrian'],0,'',',')?></a>&nbsp;&nbsp;</td>
										<td align="right" bgcolor="#DDBBBB"><a onClick="window.open('script_summary_detail_rev.php?act=show_email_dikirim&pr=<?=$flagtrans?>&blth=<?php echo $blth;?>&m_loading_id=<?php echo $m_loading_id?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>')"><?php echo number_format($row_data['jml_email'],0,'',',')?></a>&nbsp;&nbsp;</td>
										<td align="right" bgcolor="#DDBBBB"><a onClick="window.open('script_summary_detail_rev.php?act=show_email_sukses&pr=<?=$flagtrans?>&blth=<?php echo $blth;?>&m_loading_id=<?php echo $m_loading_id?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>')"><?php echo number_format($row_data['email_sukses'],0,'',',')?></a>&nbsp;&nbsp;<?php echo $msg_warning; ?></td>
										<td align="right" bgcolor="#DDBBBB"><a onClick="window.open('script_summary_detail_rev.php?act=show_email_gagal&pr=<?=$flagtrans?>&blth=<?php echo $blth;?>&m_loading_id=<?php echo $m_loading_id?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>')"><?php echo number_format($row_data['email_gagal'],0,'',',')?></a>&nbsp;&nbsp;<?php echo $msg_warning; ?></td>
										<td align="right" bgcolor="#DDBBBB" valign="middle">
										<!-- TABLE EMAIL TERBACA -->
										<table border="0" style="border-collapse:collapse" width="100%" height="100%">
										<tr>
										<td style="font-size:7pt;background-color:#fff0f5;font-family:helvetica;font-weight:bold;" align="right">all</td>
										<td style="font-size:7pt;background-color:#ffe4e1;font-family:helvetica;font-weight:bold;" align="right">img</td>
										</tr>
										<tr>
										<td height="100%" width="50%" style="font-size:8pt;background-color:#fff0f5;" align="right">
										<!--terbaca selain melalui img-->
										<a onClick="window.open('script_summary_detail_rev.php?act=show_email_terbaca&source=0&pr=<?=$flagtrans?>&blth=<?php echo $blth;?>&m_loading_id=<?php echo $m_loading_id?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>')"><?php echo number_format($row_data['email_terbaca'],0,'',',');?></a>
										</td>
										<td height="100%" width="50%" style="font-size:8pt;background-color:#ffe4e1;" align="right">
										<!-- terbaca melalui img-->
										<a onClick="window.open('script_summary_detail_rev.php?act=show_email_terbaca&source=1&pr=<?=$flagtrans?>&blth=<?php echo $blth;?>&m_loading_id=<?php echo $m_loading_id?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>')"><?php echo number_format($row_data['gambar_terbaca']);?></a>
										</td>
										</tr>
										</table>
										<!-- END TABLE EMAIL TERBACA -->
										</td>
										<td align="right" bgcolor="#DDBBBB"><a onClick="window.open('script_summary_detail_rev.php?act=show_email_others&pr=<?=$flagtrans?>&blth=<?=$blth?>&m_loading_id=<?php echo $m_loading_id?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>')"><?php echo number_format($row_data['jml_email_others'],0,'',',')?></a>&nbsp;&nbsp;</td>
										<td align="right"><a onClick="window.open('script_summary_detail_rev.php?act=show_email_sample_sukses&pr=<?=$flagtrans?>&blth=<?php echo $blth;?>&m_loading_id=<?php echo $m_loading_id?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>')"><?php echo number_format($row_data['sample_sukses'],0,'',',')?></a>&nbsp;&nbsp;</td>
										<td align="right"><a onClick="window.open('script_summary_detail_rev.php?act=show_email_sample_gagal&pr=<?=$flagtrans?>&blth=<?php echo $blth;?>&m_loading_id=<?php echo $m_loading_id?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>')"><?php echo number_format($row_data['sample_gagal'],0,'',',')?></a>&nbsp;&nbsp;</td>
									</tr>
									<?php
										$m_loading_id_all .=''.$m_loading_id.'|';
										$total_pdf = $total_pdf + $row_data['jml_pdf'];
										$total_lembar = $total_lembar + $row_data['jml_lembar'];
										#$total_semua_email = $total_email + $row_data['total_email'];
										$total_semua_email = $total_semua_email + $row_data['total_email'];
										$total_email = $total_email + $row_data['jml_email'];
										$total_email_gratis = $total_email_gratis + $row_data['jml_email_gratis'];
										$total_email_bayar = $total_email_bayar + $row_data['jml_email_bayar'];
										$total_hlm_berbayar = $total_hlm_berbayar + $row_data['total_hlm_berbayar'];
										$total_antrian = $total_antrian + $row_data['jml_antrian'];
										$total_e_sukses = $total_e_sukses + $row_data['email_sukses'];
										$total_e_gagal = $total_e_gagal + $row_data['email_gagal'];
										$total_e_terbaca = $total_e_terbaca + $row_data['email_terbaca'];
										$total_g_terbaca = $total_g_terbaca + $row_data['gambar_terbaca'];
										$total_e_others = $total_e_others + $row_data['jml_email_others'];
										$total_s_sukses = $total_s_sukses + $row_data['sample_sukses'];
										$total_s_gagal = $total_s_gagal + $row_data['sample_gagal'];
										
										$total_selisih = $total_selisih + (($row_data['jml_antrian']+$row_data['jml_email'])-$row_data['total_email']);
									}
								?>
                                <tr height="30" style="font-weight:bold" bgcolor="#87ceeb">
                                	<td align="center" colspan="5">T O T A L</td>
                                	<td align="right"><a onClick="window.open('script_summary_detail_rev.php?act=show_list_pdf&pr=<?=$flagtrans?>&blth=<? echo $blth?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&m_loading_id=<?php echo trim($m_loading_id_all,'|')?>')"><?php echo number_format($total_pdf,0,'',',')?></a>&nbsp;&nbsp;</td>
                                	<td align="right"><?php echo number_format($total_lembar,0,'',',')?>&nbsp;&nbsp;</td>
                                	<td align="right" bgcolor="#87ceeb"><a onClick="window.open('script_summary_detail_rev.php?act=show_list_email&pr=<?=$flagtrans?>&tipe=0&blth=<? echo $blth?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&m_loading_id=<?php echo trim($m_loading_id_all,'|')?>')"><?php echo number_format($total_email_gratis,0,'',',');?></a>&nbsp;&nbsp;</td>
                                	<td align="right" bgcolor="#87ceeb"><a onClick="window.open('script_summary_detail_rev.php?act=show_list_email&pr=<?=$flagtrans?>&tipe=1&blth=<? echo $blth?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&m_loading_id=<?php echo trim($m_loading_id_all,'|')?>')"><?php echo number_format($total_email_bayar,0,'',',');?></a>&nbsp;&nbsp;</td>
                                	<td align="right" bgcolor="#FFD700"><a onClick="window.open('script_summary_detail_rev.php?act=show_list_email&pr=<?=$flagtrans?>&tipe=1&blth=<? echo $blth?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&m_loading_id=<?php echo trim($m_loading_id_all,'|')?>')"><?php echo number_format($total_hlm_berbayar,0,'',',');?></a>&nbsp;&nbsp;</td>
									
									
									<td align="right" bgcolor="#<?php echo ( !empty( $total_selisih) ? 'F08080': '3CB371'); ?>"><?php echo $total_selisih;?>&nbsp;&nbsp;</td>
									
									
                                	<td align="right" bgcolor="#cd853f"><a onClick="window.open('script_summary_detail_rev.php?act=show_total_email&pr=<?=$flagtrans?>&blth=<? echo $blth?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&m_loading_id=<?php echo trim($m_loading_id_all,'|')?>')"><?php echo number_format($total_semua_email,0,'',',');?></a>&nbsp;&nbsp;</td>
                                	<td align="right" bgcolor="#CC9999"><a onClick="window.open('script_summary_detail_rev.php?act=show_list_antrian&pr=<?=$flagtrans?>&blth=<? echo $blth?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&m_loading_id=<?php echo trim($m_loading_id_all,'|')?>')"><?php echo number_format($total_antrian,0,'',',')?></a>&nbsp;&nbsp;</td>
                                	<td align="right" bgcolor="#CC9999"><a onClick="window.open('script_summary_detail_rev.php?act=show_email_dikirim&tipe=2&pr=<?=$flagtrans?>&blth=<? echo $blth?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&m_loading_id=<?php echo trim($m_loading_id_all,'|')?>')"><?php echo number_format($total_email,0,'',',')?></a>&nbsp;&nbsp;</td>
                                	<td align="right" bgcolor="#CC9999"><a onClick="window.open('script_summary_detail_rev.php?act=show_email_sukses&pr=<?=$flagtrans?>&blth=<? echo $blth?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&m_loading_id=<?php echo trim($m_loading_id_all,'|')?>')"><?php echo number_format($total_e_sukses,0,'',',')?></a>&nbsp;&nbsp;</td>
                                	<td align="right" bgcolor="#CC9999"><a onClick="window.open('script_summary_detail_rev.php?act=show_email_gagal&pr=<?=$flagtrans?>&blth=<? echo $blth?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&m_loading_id=<?php echo trim($m_loading_id_all,'|')?>')"><?php echo number_format($total_e_gagal,0,'',',')?></a>&nbsp;&nbsp;</td>
									<td align="right" bgcolor="#CC9999" valign="middle">
									<!-- TABLE EMAIL TERBACA -->
									<table border="0" style="border-collapse:collapse" width="100%" height="100%">
										<tr>
										<td style="font-size:7pt;background-color:#fff0f5;font-family:helvetica;font-weight:bold;" align="right">all</td>
										<td style="font-size:7pt;background-color:#ffe4e1;font-family:helvetica;font-weight:bold;" align="right">img</td>
										</tr>
										<tr>
										<td height="100%" width="50%" style="font-size:8pt;background-color:#fff0f5;" align="right">
										<!--terbaca selain melalui img-->
										<a onClick="window.open('script_summary_detail_rev.php?act=show_email_terbaca&source=0&pr=<?=$flagtrans?>&blth=<? echo $blth?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&m_loading_id=<?php echo trim($m_loading_id_all,'|')?>')"><?php echo number_format($total_e_terbaca,0,'',',');?></a>
										</td>
										<td height="100%" width="50%" style="font-size:8pt;background-color:#eee5de;" align="right">
										<!-- terbaca melalui img-->
										<a onClick="window.open('script_summary_detail_rev.php?act=show_email_terbaca&source=1&pr=<?=$flagtrans?>&blth=<? echo $blth?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&m_loading_id=<?php echo trim($m_loading_id_all,'|')?>')"><?php echo number_format($total_g_terbaca,0,'',',');?></a>
										</td>
										</tr>
										</table>
									<!-- END TABLE EMAIL TERBACA -->
									</td>
									<td align="right" bgcolor="#CC9999"><a onClick="window.open('script_summary_detail_rev.php?act=show_email_others&pr=<?=$flagtrans?>&blth=<? echo $blth?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&m_loading_id=<?php echo trim($m_loading_id_all,'|')?>')"><?php echo number_format($total_e_others,0,'',',')?></a>&nbsp;&nbsp;</td>
                                	<td align="right"><a onClick="window.open('script_summary_detail_rev.php?act=show_email_sample_sukses&pr=<?=$flagtrans?>&blth=<? echo $blth?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&m_loading_id=<?php echo trim($m_loading_id_all,'|')?>')"><?php echo number_format($total_s_sukses,0,'',',')?></a>&nbsp;&nbsp;</td>
                                	<td align="right"><a onClick="window.open('script_summary_detail_rev.php?act=show_email_sample_gagal&pr=<?=$flagtrans?>&blth=<? echo $blth?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&m_loading_id=<?php echo trim($m_loading_id_all,'|')?>')"><?php echo number_format($total_s_gagal,0,'',',')?></a>&nbsp;&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </body>
        </html>
        <?php
		}else{
			echo $get_respon;
		}
	}
	
	function show_list_pdf($header_utama){
		$m_loading_id = $_REQUEST['m_loading_id'];
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['pr'];
		
		$blth_balik = substr($blth,-4).substr($blth,0,2);
		
			$BLTH_BARU = BLTH_BARU;
			if($blth_balik>$BLTH_BARU){
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
		
		$per_send = explode('|', $_REQUEST['periode_kirim']);
		$sent_from = $per_send[0];
		$sent_to = $per_send[1];
		
		$m_loading_id_ck = strpos($m_loading_id, '|');
		if ($m_loading_id_ck !== false){
			$gtotal = true;
		}else{
			$gtotal = false;
		}
		
		if ($gtotal == true){
		$condv = "WHERE d.m_loading_id in (".str_replace('|',',', $m_loading_id).")";
		$condv_nama = "WHERE m_loading_id in (".str_replace('|',',', $m_loading_id).")";
		}else{
		$condv = "WHERE d.m_loading_id = $m_loading_id";
		$condv_nama = "WHERE m_loading_id = $m_loading_id";
		}
									
		
		$sql = "SELECT loading_file,blth FROM m_loading ".$condv_nama;
		$qry = pg_query($sql) or die('ERROR query: '.$sql);
		while($row = pg_fetch_array($qry)){
		$nama_file .= "<span style='font-family:verdana;font-size:8pt;color:#347822;'>".$row['loading_file'] . ", </span>";
		$blth = $row['blth'];
		}
		
		$p_nama_file = strlen($nama_file);
		$nama_file = substr($nama_file,0,$p_nama_file-9);
		
		?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>LIST PDF</title>
            </head>
            <? require_once("../include/script.php"); ?>
            <body>
            	<table align="center" border="0" width="1000">
                	<tr>
                    	<td>
                        	<table align="center" border="0" cellpadding="2" cellspacing="0" width="100%">
                            	<tr height="30">
                                	<td class="title" colspan="2" align="center" valign="middle">
                                    	LIST PDF
                                    </td>
                                </tr>
								<tr height="20">
                                	<td colspan="2"><b><u><?=$header_utama?></u></b></td>
                                </tr>
								<!--
								<tr height="20">
                                	<td align="left"><b>Nama File : <?=$nama_file?></b></td>
                                </tr>
								-->
								<?php
								if (!empty($sent_from)){
									$label = ''.$sent_from.' s/d '.$sent_to.'';
								?>
									<tr height="30">
										<td><b>Periode: <?=$label?></b></td>
										<td align="right"><a onClick="window.open('script_summary_detail_rev.php?act=show_list_pdf<?php if (!empty($sent_from)){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>&xls=y')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a></td>
									</tr>
								<?php
								}else{
									if ($gtotal != true){$label='Periode: '.$blth;}
								?>
									<tr height="30">
										<td><b><?=$label?></b></td>
										<td align="right"><a onClick="window.open('script_summary_detail_rev.php?act=show_list_pdf&blth=<?php echo $blth?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>&xls=y')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a></td>
									</tr>
								<?php
								}?>
                            </table>
                        </td>
                    </tr>
                    <tr>
                    	<td>
                        	<table align="center" border="1" cellpadding="2" cellspacing="0" width="100%">
                            	<tr height="30" bgcolor="#FF0000" style="color:#FFF">
                                	<td width="4%" align="center"><b>No</b></td>
									<?php
										if ($gtotal == true){
											echo '<td align="center"><b>Cycle</b></td>';
										}
									?>
                                	<td align="center"><b>Nomor Kartu</b></td>
                                	<td align="center"><b>Cycle</b></td>
                                	<td align="left">&nbsp;<b>Nama</b></td>
                                	<td align="center"><b>Nama PDF</b></td>
                                	<td align="center"><b>Password PDF</b></td>
                                    <td align="center"><b>Size PDF (Kb)</b></td>
                                	<td align="center"><b>Jumlah Halaman</b></td>
                                </tr>
                                <?php
									$i = 0;
									$limit = 500;
									if(isset($_REQUEST['offset']))	$offset = $_REQUEST['offset']; else $offset = 0;
									
									$sql_datas = "
										SELECT d.flagtrans, d.blth, d.nomor_customer, d.nama, d.pdf_name, d.password_pdf, d.jml_hlm, d.nama_file, d.nomor_rekening, d.size_pdf
										FROM $tabeldetail d
										".$condv."
										ORDER BY d.m_loading_id, d.nomor_rekening ASC
									";
									$sql_data .= $sql_datas . " LIMIT $limit OFFSET $offset";
									if($_REQUEST['xls']=='y'){
									$qry_data = pg_query($sql_datas) or die('ERROR select data: '.$sql_datas);
									} else {
									$qry_data = pg_query($sql_data) or die('ERROR select data: '.$sql_data);
									}
									$qry_datas = pg_query($sql_datas) or die('ERROR select data: '.$sql_datas);
									$jumlah_data = pg_num_rows($qry_datas);
									$i = $offset;
									
									while($row_data = pg_fetch_array($qry_data)){
										$i++;
										//SETTING LOKASI FOLDER PDF
										$panjang_folder = strlen(trim($row_data['nama_file']));
										$balik_namafolder = strrev($row_data['nama_file']);
										$posisi_titik = strpos($balik_namafolder,".");
										$balik_namafolder = substr($balik_namafolder,$posisi_titik+1,$panjang_folder);
										$namafolder = strrev($balik_namafolder);
								?>
                                <tr height="30">
                                	<td align="center"><?php echo $i?></td>
									<?php
										if ($gtotal == true){
											echo '<td align="center">&nbsp;'.$row_data['nama_file'].'</td>';
										}
									?>
                                	<td align="center">&nbsp;<?php echo $row_data['nomor_rekening']?></td>
                                	<td align="center"><?php echo $row_data['nama_file']?></td>
                                	<td align="left">&nbsp;<?php echo $row_data['nama']?></td>
                                	<td align="center"><?php
									$pdf_location = "../pdf/".$row_data['flagtrans']."/".$row_data['blth']."/".$namafolder."/";?>
									<a href="<?=$pdf_location.$row_data['pdf_name'].".pdf"?>" target="blank"><?echo $row_data['pdf_name'].".pdf"?></a>
									</td>
                                	<td align="center"><?php echo $row_data['password_pdf']?></td>
                                    <td align="center"><?php echo number_format($row_data['size_pdf'])?></td>
                                	<td align="right"><?php echo number_format($row_data['jml_hlm'],0,',','')?>&nbsp;&nbsp;</td>
                                </tr>
                                <?php
									}
								?>
                            </table>
                        </td>
                    </tr>
                </table>
				<br/>
				<div align="center">
				 <?php
                      if($_REQUEST['xls']<>'y'){  
					  echo pagings($offset,$jumlah_data,$limit,'div_paging','script_summary_detail_rev.php','act=show_list_pdf&blth='.$blth.'&pr='.$flagtrans.'&m_loading_id='.$m_loading_id.'&tipe='.$tipe);
					  }
                    ?>
					</div>
            </body>
        </html>
        <?php
	}
	
	
	
	
	
	function show_list_hal($header_utama){
		$HLM_BERBAYAR = HLM_BERBAYAR;
		$m_loading_id = $_REQUEST['m_loading_id'];
		$blth = $_REQUEST['blth'];
		$tipe = $_REQUEST['tipe'];
		$flagtrans = $_REQUEST['pr'];
		
			$blth_balik = substr($blth,-4).substr($blth,0,2);
		
			$BLTH_BARU = BLTH_BARU;
			if($blth_balik>$BLTH_BARU){
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
		
		switch($tipe){
		case 0:
		$vw_email = " AND jml_hlm <= $HLM_BERBAYAR";
		$ket_vw_email = "( <= $HLM_BERBAYAR Halaman )";
		break;
		case 1:
		$vw_email = " AND jml_hlm > $HLM_BERBAYAR";
		$ket_vw_email = "( > $HLM_BERBAYAR Halaman)";
		break;
		case 2:
		$vw_email = " ";
		$ket_vw_email = "";
		break;
		}
		
		
		$per_send = explode('|', $_REQUEST['periode_kirim']);
		$sent_from = $per_send[0];
		$sent_to = $per_send[1];
		
		$m_loading_id_ck = strpos($m_loading_id, '|');
		if ($m_loading_id_ck !== false){
			$gtotal = true;
		}else{
			$gtotal = false;
		}
		
		if ($gtotal == true){
		$condv = "WHERE m_loading_id in (".str_replace('|',',', $m_loading_id).")";
		$condv_nama = "WHERE m_loading_id in (".str_replace('|',',', $m_loading_id).")";
		}else{
		$condv = "WHERE m_loading_id = $m_loading_id";
		$condv_nama = "WHERE m_loading_id = $m_loading_id";
		}
									
		
		$sql = "SELECT loading_file,blth FROM m_loading ".$condv_nama;
		$qry = pg_query($sql) or die('ERROR query: '.$sql);
		while($row = pg_fetch_array($qry)){
		$nama_file .= "<span style='font-family:verdana;font-size:8pt;color:#347822;'>".$row['loading_file'] . ", </span>";
		$blth = $row['blth'];
		}
		
		$p_nama_file = strlen($nama_file);
		$nama_file = substr($nama_file,0,$p_nama_file-9);
		
		?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>LIST PDF</title>
            </head>
            <? require_once("../include/script.php"); ?>
            <body>
            	<table align="center" border="0" width="1000">
                	<tr>
                    	<td>
                        	<table align="center" border="0" cellpadding="2" cellspacing="0" width="100%">
                            	<tr height="30">
                                	<td class="title" colspan="2" align="center" valign="middle">
                                    	LIST PDF<br/><?php echo $ket_vw_email;?>
                                    </td>
                                </tr>
								<tr height="20">
                                	<td colspan="2"><b><u><?=$header_utama?></u></b></td>
                                </tr>
								<!--
								<tr height="20">
                                	<td align="left"><b>Nama File : <?=$nama_file?></b></td>
                                </tr>
								-->
								<?php
								if (!empty($sent_from)){
									$label = ''.$sent_from.' s/d '.$sent_to.'';
								?>
									<tr height="30">
										<td><b>Periode: <?=$label?></b></td>
										<td align="right"><a onClick="window.open('script_summary_detail_rev.php?act=show_list_pdf<?php if (!empty($sent_from)){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>&xls=y')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a></td>
									</tr>
								<?php
								}else{
									if ($gtotal != true){$label='Periode: '.$blth;}
								?>
									<tr height="30">
										<td><b><?=$label?></b></td>
										<td align="right"><a onClick="window.open('script_summary_detail_rev.php?act=show_list_pdf&blth=<?php echo $blth?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>&xls=y')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a></td>
									</tr>
								<?php
								}?>
                            </table>
                        </td>
                    </tr>
                    <tr>
                    	<td>
                        	<table align="center" border="1" cellpadding="2" cellspacing="0" width="100%" style="border-collapse:collapse;border-color:#000000;">
                            	<tr height="30" bgcolor="#b22222" style="color:#FFF">
                                	<td width="4%" align="center"><b>No</b></td>
									<?php
										if ($gtotal == true){
											echo '<td align="center"><b>Cycle</b></td>';
										}
									?>
                                	<td align="center"><b>Nomor Kartu</b></td>
                                	<td align="center"><b>Cycle</b></td>
                                	<td align="left">&nbsp;<b>Nama</b></td>
                                	<td align="center"><b>Nama PDF</b></td>
                                	<td align="center"><b>Pswd PDF</b></td>
                                	<td align="center"><b>Jml Hlm</b></td>
                                </tr>
                                <?php
									$i = 0;
									$limit = 500;
									if(isset($_REQUEST['offset']))	$offset = $_REQUEST['offset']; else $offset = 0;
									
									$sql_datas = "
										SELECT flagtrans, blth, nomor_customer, nama, pdf_name, password_pdf, jml_hlm, nama_file, nomor_rekening
										FROM $tabeldetail 
										".$condv." $vw_email
										ORDER BY m_loading_id, nomor_rekening ASC
									";
									$sql_data .= $sql_datas . " LIMIT $limit OFFSET $offset";
									if($_REQUEST['xls']=='y'){
									$qry_data = pg_query($sql_datas) or die('ERROR select data: '.$sql_datas);
									} else {
									$qry_data = pg_query($sql_data) or die('ERROR select data: '.$sql_data);
									}
									$qry_datas = pg_query($sql_datas) or die('ERROR select data: '.$sql_datas);
									$jumlah_data = pg_num_rows($qry_datas);
									$i = $offset;
									#echo $sql_data;
									while($row_data = pg_fetch_array($qry_data)){
										$i++;
										//SETTING LOKASI FOLDER PDF
										$panjang_folder = strlen(trim($row_data['nama_file']));
										$balik_namafolder = strrev($row_data['nama_file']);
										$posisi_titik = strpos($balik_namafolder,".");
										$balik_namafolder = substr($balik_namafolder,$posisi_titik+1,$panjang_folder);
										$namafolder = strrev($balik_namafolder);
								?>
                                <tr height="30">
                                	<td align="center"><?php echo $i?></td>
									<?php
										if ($gtotal == true){
											echo '<td align="center">&nbsp;'.$row_data['nama_file'].'</td>';
										}
									?>
                                	<td align="center">&nbsp;<?php echo $row_data['nomor_rekening']?></td>
                                	<td align="center"><?php echo $row_data['nama_file']?></td>
                                	<td align="left">&nbsp;<?php echo $row_data['nama']?></td>
                                	<td align="center"><?php
									$pdf_location = "../pdf/".$row_data['flagtrans']."/".$row_data['blth']."/".$namafolder."/";?>
									<a href="<?=$pdf_location.$row_data['pdf_name'].".pdf"?>" target="blank"><?echo $row_data['pdf_name'].".pdf"?></a>
									</td>
                                	<td align="center"><?php echo $row_data['password_pdf']?></td>
                                	<td align="right"><?php echo number_format($row_data['jml_hlm'],0,',','')?>&nbsp;&nbsp;</td>
                                </tr>
                                <?php
									}
								?>
                            </table>
                        </td>
                    </tr>
                </table>
				<br/>
				<div align="center">
				 <?php
                      if($_REQUEST['xls']<>'y'){  
					  echo pagings($offset,$jumlah_data,$limit,'div_paging','script_summary_detail_rev.php','act=show_list_hal&blth='.$blth.'&pr='.$flagtrans.'&m_loading_id='.$m_loading_id.'&tipe='.$tipe);
					  }
                    ?>
					</div>
            </body>
        </html>
        <?php
	}
	
	
	
	
	function show_list_email($con,$header_utama){
	$HLM_BERBAYAR = HLM_BERBAYAR;
		$m_loading_id = $_REQUEST['m_loading_id'];
		$blth = $_REQUEST['blth'];
		$tipe = $_REQUEST['tipe'];
		$flagtrans = $_REQUEST['pr'];
		
			$blth_balik = substr($blth,-4).substr($blth,0,2);
		
			$BLTH_BARU = BLTH_BARU;
			if($blth_balik>$BLTH_BARU){
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
			
		switch($tipe){
		case 0:
		$vw_email = " AND a.jml_hlm <= $HLM_BERBAYAR";
		$ket_vw_email = "( <= $HLM_BERBAYAR Halaman )";
		break;
		case 1:
		$vw_email = " AND a.jml_hlm > $HLM_BERBAYAR";
		$ket_vw_email = "( > $HLM_BERBAYAR Halaman)";
		break;
		case 2:
		$vw_email = " ";
		$ket_vw_email = "";
		break;
		}
		
		
		
		$per_send = explode('|', $_REQUEST['periode_kirim']);
		$sent_from = $per_send[0];
		$sent_to = $per_send[1];
		if (!empty($sent_from)){
			$pk_where = "AND date_email_send BETWEEN '".$sent_from."' AND '".$sent_to."'";
		}else{
			$pk_where = "";
		}
		
		$m_loading_id_ck = strpos($m_loading_id, '|');
		if ($m_loading_id_ck !== false){
			$gtotal = true;
		}else{
			$gtotal = false;
		}
		
		if ($gtotal == true){
			$condv = "WHERE m_loading_id in (".str_replace('|',',', $m_loading_id).")";
			$condv2 = "AND loading_id in (".str_replace('|',',', $m_loading_id).")";
			$condv_nama = "WHERE m_loading_id in (".str_replace('|',',', $m_loading_id).")";
			}else{
			$condv = "WHERE m_loading_id = '$m_loading_id'";
			$condv2 = "AND m_loading_id = '$m_loading_id'";
			$condv_nama = "WHERE m_loading_id = '$m_loading_id'";
		}
		
		$sql = "SELECT loading_file,blth FROM m_loading ".$condv_nama;
		$qry = pg_query($sql) or die('ERROR query: '.$sql);
		while($row = pg_fetch_array($qry)){
		$nama_file .= "<span style='font-family:verdana;font-size:8pt;color:#347822;'>".$row['loading_file'] . ", </span>";
		$blth = $row['blth'];
		}
		
		$p_nama_file = strlen($nama_file);
		$nama_file = substr($nama_file,0,$p_nama_file-9);
		
		$vetable = '
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>List PDF</title>
            </head>
            <? require_once("../include/script.php"); ?>
            <body>
            	<table align="center" border="0" width="1000">
                	<tr>
                    	<td>
                        	<table align="center" border="0" cellpadding="2" cellspacing="0" width="100%">
                            	<tr height="30">
                                	<td style="font-size:12pt;font-weight:bold;" colspan="4" align="center" valign="middle">
                                    	LIST PDF<br/>'.$ket_vw_email.'
                                    </td>
                                </tr>
                                <tr height="20">
                                	<td colspan="3" ><b>Tanggal: '.date("d-m-Y H:i").'</b></td>
                                    <td align="right"></td>
                                </tr>
								<tr height="20">
                                	<td colspan="2"><b><u>'.$header_utama.'</u></b></td>
                                </tr>
                                    ';?>
									<?php
									if ($gtotal != true){$label='Periode: '.$blth;}
								$vetable .= '
									<tr>
									<td colspan="2"><b>'.$label.'</b></td>
									<td align="right">
									<a onClick="window.open(\'script_summary_detail_rev.php?act=show_list_email&pr='.$flagtrans.'&m_loading_id='.$m_loading_id.'&xls=y&tipe='.$tipe.'\')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a>
									</td>
                                </tr>'; 
                                    $vetable .= '
                            </table>
                        </td>
                    </tr>
                    <tr>
                    	<td>
                        	<table align="center" border="1" cellpadding="2" cellspacing="0" width="100%" style="border-collapse:collapse;">
                            	<tr height="30" bgcolor="#3cb371">
                                	<td align="right"><b>No</b>&nbsp;</td>
                                	<td align="center"><b>Cycle</b></td>
                                	<td align="center"><b>No Kartu</b></td>
                                	<td align="left">&nbsp;<b>Nama</b></td>
                                	<td align="left">&nbsp;<b>Email</b></td>
                                	<td align="center"><b>Jml Hlm</b></td>';
									if($tipe=='1'){
									$vetable .= '<td align="right" bgcolor="#ffd700"><b>Hlm Berbayar</b>&nbsp;</td>';
									}
                                $vetable .= '<td align="left">&nbsp;<b>File PDF</b></td>
                                </tr>';
									$i = 0;

									$limit = 500;
									if(isset($_REQUEST['offset']))	$offset = $_REQUEST['offset']; else $offset = 0;
									
									$sql_datas = "
									SELECT a.*
									FROM $tabeldetail a
									$condv
									$vw_email
									ORDER BY a.nomor_rekening, a.nama, a.email";
									#echo $sql_datas;
									$sql_data .= $sql_datas . " LIMIT $limit OFFSET $offset";
									if($_REQUEST['xls']=='y'){
									$qry_data = pg_query($sql_datas) or die('ERROR select data: '.$sql_datas);
									} else {
									$qry_data = pg_query($sql_data) or die('ERROR select data: '.$sql_data);
									}
									$qry_datas = pg_query($sql_datas) or die('ERROR select data: '.$sql_datas);
									$jumlah_data = pg_num_rows($qry_datas);
									$i = $offset;
									//echo $sql_datas;
									while($row_data = pg_fetch_array($qry_data)){
									//SETTING LOKASI FOLDER PDF
										$panjang_folder = strlen(trim($row_data['nama_file']));
										$balik_namafolder = strrev($row_data['nama_file']);
										$posisi_titik = strpos($balik_namafolder,".");
										$balik_namafolder = substr($balik_namafolder,$posisi_titik+1,$panjang_folder);
										$namafolder = strrev($balik_namafolder);
										$pdf_location = "../pdf/".$row_data['flagtrans']."/".$row_data['blth']."/".$namafolder."/";
										$link_pdf = $pdf_location.$row_data['pdf_name'].".pdf";
										$selisihnya = $row_data['jml_hlm']-$HLM_BERBAYAR;
										$total_hlm += $row_data['jml_hlm'];
										$total_hlm_berbayar += $selisihnya;
										//$b_email = rtrim($row_data['email'],';');
										if(strlen($row_data['emailnya'])>0)	$b_email = $row_data['email']; else $b_email = rtrim($row_data['email'],";"); 
										#$arr_email = explode(';',$b_email);
										#foreach($arr_email as $emailnya){
										$i++;
											$vetable .='
                                <tr height="30">
                                	<td align="right">'.$i.'&nbsp;</td>
                                	<td align="center">'.$row_data['nama_file'].'</td>
                                	<td align="center">&nbsp;'.$row_data['nomor_rekening'].'</td>
                                	<td align="left">&nbsp;'.$row_data['nama'].'</td>
                                	<td align="left">&nbsp;'.$b_email.'</td>
                                	<td align="right">'.$row_data['jml_hlm'].'</td>';
									if($tipe=='1'){
									$vetable .= '<td align="right" bgcolor="#fafad2">'.$selisihnya.'</td>';
									}
                                	$vetable .= '<td align="left">&nbsp;<a href="'.$link_pdf.'" target="blank">'.$row_data['pdf_name'].".pdf".'</a></td>
                                </tr>';
										#}
								
								}
								$vetable .= '<tr bgcolor="#3cb371">
								<td colspan="5"><b>TOTAL</b></td>
								<td align="right"><b>'.$total_hlm.'</b></td>';
								if($tipe=='1'){
								$vetable .= '<td align="right" bgcolor="#ffd700"><b>'.$total_hlm_berbayar.'</b></td>';
								}
								$vetable .= '<td></td>
								</tr>';
								$vetable .= '
                            </table>
                        </td>
                    </tr>
					<tr>
					<td><div align="center">';
                      if($_REQUEST['xls']<>'y'){  
					  $vetable .= pagings($offset,$jumlah_data,$limit,'div_paging','script_summary_detail_rev.php','act=show_list_email&pr='.$flagtrans.'&blth='.$blth.'&m_loading_id='.$m_loading_id.'&tipe='.$tipe);
					  }
					$vetable .='</div></td>
					</tr>
					</table>
				<br/>
				</body>
        </html>';
        echo $vetable;
				
	}
	
	
	
	
	
	
	function show_total_email($con,$header_utama){
	$SIZE_BERBAYAR = SIZE_BERBAYAR;
		$m_loading_id = $_REQUEST['m_loading_id'];
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['pr'];
		$sof = $_REQUEST['sof'];
		
			$blth_balik = substr($blth,-4).substr($blth,0,2);
		
			$BLTH_BARU = BLTH_BARU;
			if($blth_balik>$BLTH_BARU){
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
		
		
		$per_send = explode('|', $_REQUEST['periode_kirim']);
		$sent_from = $per_send[0];
		$sent_to = $per_send[1];
		if (!empty($sent_from)){
			$pk_where = "AND d.date_email_send BETWEEN '".$sent_from."' AND '".$sent_to."'";
		}else{
			$pk_where = "";
		}
		
		$m_loading_id_ck = strpos($m_loading_id, '|');
		if ($m_loading_id_ck !== false){
			$gtotal = true;
		}else{
			$gtotal = false;
		}
		
		if ($gtotal == true){
			$condv = "WHERE a.m_loading_id in (".str_replace('|',',', $m_loading_id).") $pk_where";
			}else{
			$condv = "WHERE a.m_loading_id = '$m_loading_id' $pk_where";
		}
		
		
		if($sof=='y') 
		{
		$condv = "WHERE d.date_email_send BETWEEN '".$sent_from."' AND '".$sent_to."'";
		}
		#echo $sof;
		?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>Delivery Report</title>
            </head>
            <? require_once("../include/script.php"); ?>
            <body>
            	<table align="center" border="0" width="1000">
                	<tr>
                    	<td>
                        	<table align="center" border="0" cellpadding="2" cellspacing="0" width="100%">
                            	<tr height="30">
                                	<td style="font-size:12pt;font-weight:bold;" colspan="4" align="center" valign="middle">
                                    	Delivery Report<br/>
                                    </td>
                                </tr>
                                <tr height="20">
                                	<td colspan="3" ><b>Tanggal: <?php echo date("d-m-Y H:i");?></b></td>
                                    <td align="right"></td>
                                </tr>
								<tr height="20">
                                	<td colspan="2"><b><u><?php echo $header_utama;?></u></b></td>
                                </tr>
									<?php
									if ($gtotal != true){$label='Periode: '.$blth;} ?>
									<tr>
									<td colspan="2"><b><?php echo $label;?></b></td>
									<td align="right">
									<?php if($_REQUEST['xls']<>'y'){ ?>
									<a onClick="window.open('script_summary_detail_rev.php?act=export_show_total_email&pr=<?php echo $flagtrans;?><?php if (!empty($sent_from)){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&blth=<?php echo $blth;?>&m_loading_id=<?php echo $m_loading_id;?>&sof=<?php echo $sof;?>&xls=y&tipe=<?php echo $tipe;?>')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a>
									<a onClick="window.open('script_summary_detail_rev.php?act=export_show_total_email&pr=<?php echo $flagtrans;?><?php if (!empty($sent_from)){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&blth=<?php echo $blth;?>&m_loading_id=<?php echo $m_loading_id;?>&sof=y&cycle=<?php echo $m_loading_id;?>')"><img height="25px" title="PENGIRIMAN PERIODE INI" src="../images/icons/download_file.png" /></a>
									<?php } ?>
									</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                    	<td>
                        	<table align="center" border="1" cellpadding="2" cellspacing="0" width="150%" style="border-collapse:collapse;border-color:#000000">
                            	<tr height="30" bgcolor="#3cb371">
                                	<td align="right" ><b>No</b>&nbsp;</td>
                                	<td align="center" ><b>Cycle</b></td>
                                	<td align="center" ><b>Delivery Status</b></td>
                                	<td align="left" >&nbsp;<b>Nomor Rekening</b></td>
                                	<td align="left" >&nbsp;<b>Nama</b></td>
                                	<td align="left" >&nbsp;<b>Email</b></td>
                                	<td align="center" width="8%"><b>Tgl Antrian</b></td>
                                	<td align="center" width="8%"><b>Jadwal Kirim</b></td>
                                	<td align="center" width="8%"><b>Tgl Kirim</b></td>
                                	<td align="center" width="8%"><b>Tgl Bounce Back</b></td>
                                	<td align="left" ><b>Pesan Error</b></td>
									<td align="center" width="8%"><b>Tgl Read</b></td>
                                	<td align="left" ><b>Pesan Read</b></td>
									<!--
									<?php #if($_REQUEST['xls']<>'y'){ ?>
									<td align="left" >&nbsp;<b>Template</b></td>
									<?php #} ?>
									-->
                                </tr>
								
								<?php
									$i = 0;

									$limit = 500;
									if(isset($_REQUEST['offset']))	$offset = $_REQUEST['offset']; else $offset = 0;
									
									$sql_datas = "
									SELECT  
									a.loading_file,
									d.antrian_id,
									CASE 
									WHEN c.email is not null and d.date_email_send is null THEN 'Q' 
									WHEN d.email is not null and d.date_email_callback is null and d.tgl_read IS NULL THEN 'S' 
									WHEN d.email is not null and d.date_email_callback is null and d.tgl_read IS NOT NULL  THEN 'S (R)' 
									WHEN d.email is not null and d.date_email_callback is not null THEN 'F' ELSE 'NO STATUS' END AS delivery_status, 
									b.nomor_rekening AS policy_number, b.nama, 
									d.email AS email_address,
									c.tgl_antrian AS queue_date,
									(SELECT e.tgl_jadwal FROM m_jadwal e WHERE e.jadwal_id=c.jadwal_id)AS send_schedule,
									to_char(d.date_email_send, 'dd Mon yyyy hh24:mi:ss') AS send_date, 
									d.ket_error AS error_message
									,to_char(d.date_email_callback, 'dd Mon yyyy hh24:mi:ss') AS tanggal_callback
									,to_char(d.tgl_read, 'dd Mon yyyy hh24:mi:ss') AS tanggal_read
									,d.body_email_read AS pesan_read
									
									FROM m_loading a 
									LEFT JOIN $tabeldetail b ON a.m_loading_id = b.m_loading_id 
									LEFT JOIN antrian_email c ON a.m_loading_id = c.loading_id and b.nomor_rekening = c.nomor_rekening and (c.status_sample = 'f' or c.status_sample is null) 
									LEFT JOIN tr_email d ON a.m_loading_id = d.loading_id and b.nomor_rekening = d.nomor_rekening 
									and (d.status_sample = 'f' or d.status_sample is null) 
									LEFT JOIN antrian_email_history e ON d.antrian_id = e.antrian_id
									
									$condv
									
									ORDER BY a.create_date DESC";
									#echo $sql_datas;
									$sql_data .= $sql_datas . " LIMIT $limit OFFSET $offset";
									if($_REQUEST['xls']=='y'){
									$qry_data = pg_query($sql_datas) or die('ERROR select data: '.$sql_datas);
									} else {
									$qry_data = pg_query($sql_data) or die('ERROR select data: '.$sql_data);
									}
									$qry_datas = pg_query($sql_datas) or die('ERROR select data: '.$sql_datas);
									$jumlah_data = pg_num_rows($qry_datas);
									$i = $offset;
									//echo $sql_datas;
									while($row_data = pg_fetch_array($qry_data)){
									$loading_file = $row_data['loading_file'];
									$delivery_status = $row_data['delivery_status'];
									$policy_number = $row_data['policy_number'];
									
									if($delivery_status=='Q') {
									$queue_date = $row_data['queue_date']; 
									$send_schedule = $row_data['send_schedule'];
									$send_date = "-";
									} else {
									$queue_date = "-";
									$send_schedule = "-";
									$send_date = $row_data['send_date'];
									}
									
									$email_address = $row_data['email_address'];
									$error_message = $row_data['error_message'];
									$nama = $row_data['nama'];
									
									$tanggal_callback = $row_data['tanggal_callback'];
									$tanggal_read = $row_data['tanggal_read'];
									$pesan_read = $row_data['pesan_read'];
										$i++;
										?>
                                <tr height="30">
                                	<td align="right" style="font-size:11px;font-family:helvetica"><?php echo $i;?></td>
                                	<td align="center" ><?php echo $loading_file;?></td>
                                	<td align="center" ><?php echo $delivery_status;?></td>
                                	<td align="left" >&nbsp;<?php echo $policy_number;?></td>
                                	<td align="left" >&nbsp;<?php echo $nama;?></td>
                                	<td align="left" >&nbsp;<?php echo $email_address;;?></td>
                                	<td align="center" ><?php echo $queue_date;?></td>
                                	<td align="center" ><?php echo $send_schedule;?></td>
                                	<td align="center" ><?php echo $send_date;?></td>
									<td align="center" ><?php echo $tanggal_callback;?></td>
                                	<td align="left" ><?php echo $error_message;?></td>
									<td align="center" ><?php echo $tanggal_read;?></td>
                                	<td align="left" ><?php echo $pesan_read;?></td>
									<!--
									<?php #if($_REQUEST['xls']<>'y'){ ?>
									<td align="center">
									<a onClick="view_template('<?php echo $row_data['antrian_id'];?>')"><img title="view template" src="../images/icons/icon_view.gif" /></a>
									</td>
									<?php #} ?>
									-->
                                </tr>
								<?php } ?>
                            </table>
                        </td>
                    </tr>
					<tr>
					<td><div align="center">
					<?php 
                      if($_REQUEST['xls']<>'y'){  
					  if (!empty($sent_from)){ $periodekirim = '&periode_kirim='.$_GET['periode_kirim']; }
					  echo pagings($offset,$jumlah_data,$limit,'div_paging','script_summary_detail_rev.php','act=show_total_email&blth='.$blth.'&pr='.$flagtrans.'&sof='.$sof.'&m_loading_id='.$m_loading_id.$periodekirim);
					  } ?>
					</div></td>
					</tr>
					</table>
				<br/>
				</body>
        </html>
		<?php 
		
	}
	
	
	
	
	
	
	function export_show_total_email($blth, $flagtrans, $cycle, $per_send, $curloc, $locateFile){
	
	$m_loading_id = $_REQUEST['m_loading_id'];
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['pr'];
		$sof = $_REQUEST['sof'];
		
			$blth_balik = substr($blth,-4).substr($blth,0,2);
		
			$BLTH_BARU = BLTH_BARU;
			if($blth_balik>$BLTH_BARU){
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
	
	$per_send = explode('|', $_REQUEST['periode_kirim']);
		$sent_from = $per_send[0];
		$sent_to = $per_send[1];
		if (!empty($sent_from)){
			$pk_where = "AND d.date_email_send BETWEEN '".$sent_from."' AND '".$sent_to."'";
		}else{
			$pk_where = "";
		}
		
		$m_loading_id_ck = strpos($m_loading_id, '|');
		if ($m_loading_id_ck !== false){
			$gtotal = true;
		}else{
			$gtotal = false;
		}
		
		if ($gtotal == true){
			$condv = "WHERE a.m_loading_id in (".str_replace('|',',', $m_loading_id).") $pk_where";
			}else{
			$condv = "WHERE a.m_loading_id = '$m_loading_id' $pk_where";
		}
		
		
		if($sof=='y') 
		{
		$condv = "WHERE d.date_email_send BETWEEN '".$sent_from."' AND '".$sent_to."'";
		}
		
		
		
		
		//include $curloc.'PHPExcel.php';
		//include $curloc.'PHPExcel/Writer/Excel5.php';
		

		// Create new PHPExcel object
		
		$sql_datas = "SELECT  
									a.loading_file,
									b.barcode AS document_key, d.antrian_id,
									CASE 
									WHEN c.email is not null and d.date_email_send is null THEN 'Q' 
									WHEN d.email is not null and d.date_email_callback is null and d.tgl_read IS NULL THEN 'S' 
									WHEN d.email is not null and d.date_email_callback is null and d.tgl_read IS NOT NULL  THEN 'S (R)' 
									WHEN d.email is not null and d.date_email_callback is not null THEN 'F' ELSE 'NO STATUS' END AS delivery_status, 
									b.nomor_rekening , b.nama, 
									d.email AS email_address,
									c.tgl_antrian AS queue_date,
									(SELECT e.tgl_jadwal FROM m_jadwal e WHERE e.jadwal_id=c.jadwal_id)AS send_schedule,
									to_char(d.date_email_send, 'dd Mon yyyy hh24:mi') AS send_date, 
									d.ket_error AS error_message
									,to_char(d.date_email_callback, 'dd Mon yyyy hh24:mi:ss') AS tanggal_callback
									,to_char(d.tgl_read, 'dd Mon yyyy hh24:mi:ss') AS tanggal_read
									,d.body_email_read AS pesan_read
									
									FROM m_loading a 
									LEFT JOIN $tabeldetail b ON a.m_loading_id = b.m_loading_id 
									LEFT JOIN antrian_email c ON a.m_loading_id = c.loading_id and b.nomor_rekening = c.nomor_rekening 
									and (c.status_sample = 'f' or c.status_sample is null) 
									LEFT JOIN tr_email d ON a.m_loading_id = d.loading_id and b.nomor_rekening = d.nomor_rekening 
									and (d.status_sample = 'f' or d.status_sample is null) 
									LEFT JOIN antrian_email_history e ON d.antrian_id = e.antrian_id
									
									$condv
									
									ORDER BY a.create_date DESC";
		$qry_datas = pg_query($sql_datas) or die('ERROR select data: '.$sql_datas);
		$sum_sql_datas = pg_num_rows($qry_datas);
		
		if($sum_sql_datas > 20000){
		
		//
			// create pembuatan csv
			
			$dest = '../tmp/csv/';
			$name_file = 'list_total_.csv';
			//die($dest.$name_file);
			if(file_exists($dest.$name_file)){ // check if file already
				unlink($dest.$name_file);
			}
			
			$handle = fopen($dest.$name_file, 'a+');
			$nl_dos	 = "\r\n";
			
			// header csv 
			$header = "No,"; 
			$header .= "Nama File,"; 
			$header .= "Delivery Status,";
			$header .= "Nomor Rekening,";
			$header .= "Nama,"; 
			$header .= "Email,";
			$header .= "Tanggal Kirim,";
			$header .= "Tanggal Bounce Back,";
			$header .= "Pesan Error,";
			$header .= "Tanggal Read,";
			$header .= "Pesan Read,";
			
			
			fwrite($handle, $header. $nl_dos);
			$nomor = 1;
			while($row_data = pg_fetch_array($qry_datas)){//extract each record
			
			if($delivery_status=='Q') {
				$queue_date = $row_data['queue_date']; 
				$send_schedule = $row_data['send_schedule'];
				$send_date = "-";
				} else {
				$queue_date = "-";
				$send_schedule = "-";
				$send_date = $row_data['send_date'];
				$tanggal_callback = $row_data['tanggal_callback'];
				$tanggal_read = $row_data['tanggal_read'];
				$pesan_read = $row_data['pesan_read'];
			}
				$total_size_estat = floor($row_data['size_pdf']);
				$total_size_attachment = floor($row_data['total_attach']/1024);
				$total_semua_attachment = $total_size_estat+$total_size_attachment;
				$sizeberbayar = ceil($total_semua_attachment/300);
				
				
				if ( empty($row_data['tanggal_callback']) )
				{
					$tanggal_callback ='';
				}else{
					$tanggal_callback = date("d-m-Y H:i:s", strtotime( $row_data['tanggal_callback'] ));
				}
				
				if ( empty($row_data['tanggal_read'] ) )
				{
					$tgl_read ='';
				}else{
					$tgl_read = date("d-m-Y H:i:s", strtotime( $row_data['tanggal_read'] ));
				}
				$row_data['pesan_read'] = str_replace(chr(13),"", $row_data['body_email_read']);

				$data = str_replace (',', ' ', $nomor) . ',' ; 
				$data .= str_replace (',', ' ', $row_data['loading_file']) . ',';
				$data .= str_replace (',', ' ', $row_data['delivery_status']).' ' . ',';
				$data .= str_replace (',', ' ', "'".$row_data['nomor_rekening']).' ' . ',';
				$data .= str_replace (',', ' ', $row_data['nama']) . ',';
				$data .= str_replace (',', ' ', $row_data['email_address']) . ',';
				$data .= date("d-m-Y H:i:s", strtotime($send_date)) . ',';
				$data .= $tanggal_callback . ',';
				$data .= str_replace (',', ' ', $row_data['error_message']) . ',';
				$data .= $tgl_read . ',';
				$data .= str_replace (',', ' ', $row_data['pesan_read']) . ',';
				

				fwrite($handle, $data. $nl_dos); // write this variable in csv file
				
				++$Line;
				$nomor+=1;
			}
			fclose($handle);
			
			header('Content-type: text/plain');                             
			//header('application/vnd.ms-excel');                             
			// What file will be named after downloading                                  
			//header('Content-Disposition: attachment; filename="list_email_sukses_'.$m_loading_id.'_'.$loading_file. '.csv"');
			header('Content-Disposition: attachment; filename="'.$name_file .'"');
			// File to download                                
			readfile($dest.$name_file); 
			unlink($dest.$name_file);
			exit;
		
		
		//
		}
		else
		{
		//
		
		$objPHPExcel = new PHPExcel();
		$F=$objPHPExcel->getActiveSheet();
		
		$styleArray = array(
			'font' => array(
				'name'         => 'Arial',
				'bold'         => true,
				'italic'    => false,
				'size'        => 12
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap'       => true
			)
		);
		
		$F->mergeCellsByColumnAndRow(0,1,8,1)->setCellValue('A1', 'DELIVERY REPORT')->getStyle('A1')->applyFromArray($styleArray);
		$F->mergeCellsByColumnAndRow(0,2,8,2)->setCellValue('A2', 'Keterangan Delivery Status: Q=Queque, S=Success No Read, S(R)=Success Read, F=Failed')->getStyle('A2')->applyFromArray($styleArray);
		
		$Line=4;
		
		$F->getColumnDimension('A')->setWidth(5);
		$F->getColumnDimension('B')->setWidth(32);
		$F->getColumnDimension('C')->setWidth(17);
		$F->getColumnDimension('D')->setWidth(30);
		$F->getColumnDimension('E')->setWidth(30);
		$F->getColumnDimension('F')->setWidth(25);
		$F->getColumnDimension('G')->setWidth(25);
		$F->getColumnDimension('H')->setWidth(25);
		$F->getColumnDimension('I')->setWidth(65);
		$F->getColumnDimension('J')->setWidth(25);
		$F->getColumnDimension('K')->setWidth(65);
		#$F->getColumnDimension('I')->setWidth(25);
		#$F->getColumnDimension('J')->setWidth(25);
		
		
		$styleArray = array(
			'font' => array(
				'name'         => 'Arial',
				'bold'         => true,
				'italic'    => false,
				'size'        => 12
			),
			'borders' => array(
			'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
			'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
			'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
			'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
		),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap'       => true
			)
		);
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$Line.':K'.$Line.'')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('ffdfdfdf');
		
		$F->setCellValueExplicit('A'.$Line, "No", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('A'.$Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('B'.$Line, "Nama File", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('B'.$Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('C'.$Line, "Delivery Status", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('C'.$Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('D'.$Line, "Nomor Rekening", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('D'.$Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('E'.$Line, "Nama", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('E'.$Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('F'.$Line, "Email", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('F'.$Line)->applyFromArray($styleArray);
		#$F->setCellValueExplicit('G'.$Line, "Tgl Antrian", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('G'.$Line)->applyFromArray($styleArray);
		#$F->setCellValueExplicit('H'.$Line, "Jadwal Kirim", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('H'.$Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('G'.$Line, "Tanggal Kirim", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('G'.$Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('H'.$Line, "Tanggal Bounce Back", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('H'.$Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('I'.$Line, "Pesan Error", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('I'.$Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('J'.$Line, "Tanggal Read", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('J'.$Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('K'.$Line, "Pesan Read", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('K'.$Line)->applyFromArray($styleArray);
		
		$Line=5;
		$nomor = 1;
		while($row_data = pg_fetch_array($qry_datas)){//extract each record
		$document_key = $row_data['document_key'];
		$loading_file = $row_data['loading_file'];
		$delivery_status = $row_data['delivery_status'];
		$policy_number = $row_data['nomor_rekening'];
									
		if($delivery_status=='Q') {
		$queue_date = $row_data['queue_date']; 
		$send_schedule = $row_data['send_schedule'];
		$send_date = "-";
		} else {
		$queue_date = "-";
		$send_schedule = "-";
		$send_date = $row_data['send_date'];
		}
									
		$email_address = $row_data['email_address'];
		$error_message = $row_data['error_message'];
		$nama = $row_data['nama'];
		#$tanggal_callback = ( empty($row_data['tanggal_callback']) ? '-': $row_data['tanggal_callback']) ;
		if ( empty($row_data['tanggal_callback']) )
		{
			$tanggal_callback ='';
		}else{
			$tanggal_callback = date("d-m-Y H:i:s", strtotime( $row_data['tanggal_callback'] ));
		}
		
		if ( empty($row_data['tanggal_read'] ) )
		{
			$tgl_read ='';
		}else{
			$tgl_read = date("d-m-Y H:i:s", strtotime( $row_data['tanggal_read'] ));
		}
		#$tgl_read = (empty($row_data['tanggal_read']) ? '-': $row_data['tanggal_read']);
		$pesan_read = (empty($row_data['pesan_read']) ? '-': $row_data['pesan_read']);
		
		$styleArray = array(
		'borders' => array(
			'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
			'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
			'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
			'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
		)
		);
		
		$F->getStyle('A'.$Line.'')->applyFromArray($styleArray);
		$F->getStyle('B'.$Line.'')->applyFromArray($styleArray);
		$F->getStyle('C'.$Line.'')->applyFromArray($styleArray);
		$F->getStyle('D'.$Line.'')->applyFromArray($styleArray);
		$F->getStyle('E'.$Line.'')->applyFromArray($styleArray);
		$F->getStyle('F'.$Line.'')->applyFromArray($styleArray);
		$F->getStyle('G'.$Line.'')->applyFromArray($styleArray);
		$F->getStyle('H'.$Line.'')->applyFromArray($styleArray);
		$F->getStyle('I'.$Line.'')->applyFromArray($styleArray);
		$F->getStyle('J'.$Line.'')->applyFromArray($styleArray);
		$F->getStyle('K'.$Line.'')->applyFromArray($styleArray);
		#$F->getStyle('I'.$Line.'')->applyFromArray($styleArray);
		#$F->getStyle('J'.$Line.'')->applyFromArray($styleArray);
		
			$F->setCellValueExplicit('A'.$Line, $nomor, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('B'.$Line, $loading_file, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('C'.$Line, $delivery_status, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('D'.$Line, $policy_number, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('E'.$Line, $nama, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('F'.$Line, $email_address, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('G'.$Line, date("d-m-Y H:i:s", strtotime($send_date)), PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('H'.$Line, $tanggal_callback, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('I'.$Line, $error_message, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('J'.$Line, $tgl_read, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('K'.$Line, $pesan_read, PHPExcel_Cell_DataType::TYPE_STRING);
				#->setCellValueExplicit('H'.$Line, $total_size_attachment, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				#->setCellValueExplicit('I'.$Line, $total_semua_attachment, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				#->setCellValueExplicit('J'.$Line, $sizeberbayar, PHPExcel_Cell_DataType::TYPE_NUMERIC);
			++$Line;
			$nomor+=1;
		}
		// Redirect output to a clients web browser (Excel5)
		/*
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="list_total_email_'.$m_loading_id.'_'.$loading_file.'.xls"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		*/
		
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");;
header('Content-Disposition: attachment;filename="list_total_email_'.$m_loading_id.'_'.$loading_file.'.xlsx"');
header("Content-Transfer-Encoding: binary ");
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 
$objWriter->setOffice2003Compatibility(true);
$objWriter->save('php://output');
		exit;
		}
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	function show_email_dikirim($con,$header_utama){
	$SIZE_BERBAYAR = SIZE_BERBAYAR;
		$m_loading_id = $_REQUEST['m_loading_id'];
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['pr'];
		
		$blth_balik = substr($blth,-4).substr($blth,0,2);
		
			$BLTH_BARU = BLTH_BARU;
			if($blth_balik>$BLTH_BARU){
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
		
		
		$per_send = explode('|', $_REQUEST['periode_kirim']);
		$sent_from = $per_send[0];
		$sent_to = $per_send[1];
		if (!empty($sent_from)){
			$pk_where = "AND te.date_email_send BETWEEN '".$sent_from."' AND '".$sent_to."'";
		}else{
			$pk_where = "";
		}
		
		$m_loading_id_ck = strpos($m_loading_id, '|');
		if ($m_loading_id_ck !== false){
			$gtotal = true;
		}else{
			$gtotal = false;
		}
		
		if ($gtotal == true){
			$condv = "WHERE te.loading_id in (".str_replace('|',',', $m_loading_id).") $pk_where";
			}else{
			$condv = "WHERE te.loading_id = '$m_loading_id' $pk_where";
		}
		

		
		?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>Email Dikirim</title>
            </head>
            <? require_once("../include/script.php"); ?>
            <body>
            	<table align="center" border="0" width="1000">
                	<tr>
                    	<td>
                        	<table align="center" border="0" cellpadding="2" cellspacing="0" width="100%">
                            	<tr height="30">
                                	<td style="font-size:12pt;font-weight:bold;" colspan="4" align="center" valign="middle">
                                    	EMAIL DIKIRIM<br/>
                                    </td>
                                </tr>
                                <tr height="20">
                                	<td colspan="3" ><b>Tanggal: <?php echo date("d-m-Y H:i");?></b></td>
                                    <td align="right"></td>
                                </tr>
								<tr height="20">
                                	<td colspan="2"><b><u><?php echo $header_utama;?></u></b></td>
                                </tr>
									<?php
									if ($gtotal != true){$label='Periode: '.$blth;} ?>
									<tr>
									<td colspan="2"><b><?php echo $label;?></b></td>
									<td align="right">
									<?php if($_REQUEST['xls']<>'y'){ ?>
									<a onClick="window.open('script_summary_detail_rev.php?act=export_excel_show_email_dikirim&pr=<?php echo $flagtrans;?>&blth=<?php echo $blth;?><?php if (!empty($sent_from)){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&m_loading_id=<?php echo $m_loading_id;?>&xls=y&tipe=<?php echo $tipe;?>')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a>
									<?php } ?>
									</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                    	<td>
                        	<table align="center" border="1" cellpadding="2" cellspacing="0" width="100%" style="border-collapse:collapse;border-color:#000000">
                            	<tr height="30" bgcolor="#3cb371">
                                	<td align="right" rowspan="2"><b>No</b>&nbsp;</td>
                                	<td align="center" rowspan="2"><b>Cycle</b></td>
                                	<td align="center" rowspan="2"><b>No Kartu</b></td>
                                	<td align="left" rowspan="2">&nbsp;<b>Nama</b></td>
                                	<td align="left" rowspan="2">&nbsp;<b>Email</b></td>
                                	<td align="left" rowspan="2">&nbsp;<b>Tgl Kirim</b></td>
                                	<td align="center" colspan="4" bgcolor="#cd853f"><b>Size (KB)</b></td>
									<?php if($_REQUEST['xls']<>'y'){ ?>
									<td align="left" rowspan="2">&nbsp;<b>Template</b></td>
									<?php } ?>
                                </tr>
								<tr>
                                	<td align="center" bgcolor="#deb887"><b>PDF</b></td>
                                	<td align="center" bgcolor="#deb887"><b>Brosur</b></td>
                                	<td align="center" bgcolor="#deb887"><b>Total</b></td>
                                	<td align="center" bgcolor="#eeb333"><b>Berbayar</b></td>
                                </tr>
								<?php
									$i = 0;

									$limit = 500;
									if(isset($_REQUEST['offset']))	$offset = $_REQUEST['offset']; else $offset = 0;
									/*
									
									
									(SELECT SUM(ukuran)FROM m_attach_file WHERE m_attach_file_id IN(SELECT m_attach_file_id FROM antrian_email_attach_file WHERE antrian_id=a.antrian_id)) AS total_attach
									*/
									$sql_datas = "
									SELECT
									 te.antrian_id, te.nomor_rekening, te.email, te.date_email_send,
									 d.nama, d.size_pdf,
									 ml.loading_file,
									 SUM(maf.ukuran)AS total_attach
									FROM tr_email te
									LEFT JOIN $tabeldetail d ON te.loading_id = d.m_loading_id and te.nomor_rekening = d.nomor_rekening
									LEFT JOIN m_loading ml ON te.loading_id = ml.m_loading_id
									LEFT JOIN antrian_email_attach_file aeaf ON te.antrian_id = aeaf.antrian_id
									LEFT JOIN m_attach_file maf ON aeaf.m_attach_file_id::integer = maf.m_attach_file_id::integer
									$condv AND te.status_sample is not true
									GROUP BY te.antrian_id, te.nomor_rekening, te.email, te.date_email_send,
									 d.nama, d.size_pdf,
									 ml.loading_file
									ORDER BY te.date_email_send";
									#echo $sql_datas;
									$sql_data .= $sql_datas . " LIMIT $limit OFFSET $offset";
									if($_REQUEST['xls']=='y'){
									$qry_data = pg_query($sql_datas) or die('ERROR select data: '.$sql_datas);
									} else {
									$qry_data = pg_query($sql_data) or die('ERROR select data: '.$sql_data);
									}
									$qry_datas = pg_query($sql_datas) or die('ERROR select data: '.$sql_datas);
									$jumlah_data = pg_num_rows($qry_datas);
									$i = $offset;
									//echo $sql_datas;
									while($row_data = pg_fetch_array($qry_data)){
									$total_size_estat = floor($row_data['size_pdf']);
									$total_size_attachment = floor($row_data['total_attach']/1024);
									$total_semua_attachment = $total_size_estat+$total_size_attachment;
									$sizeberbayar = ceil($total_semua_attachment/$SIZE_BERBAYAR);
									
									$i++;
										?>
                                <tr height="30">
                                	<td align="right" style="font-size:11px;font-family:helvetica"><?php echo $i;?></td>
                                	<td align="center" style="font-size:11px;font-family:helvetica"><?php echo $row_data['loading_file'];?></td>
                                	<td align="center" style="font-size:11px;font-family:helvetica">&nbsp;<?php echo $row_data['nomor_rekening'];?></td>
                                	<td align="left" style="font-size:11px;font-family:helvetica">&nbsp;<?php echo $row_data['nama'];?></td>
                                	<td align="left" style="font-size:11px;font-family:helvetica">&nbsp;<?php echo $row_data['email'];?></td>
                                	<td align="left" style="font-size:11px;font-family:helvetica">&nbsp;<?php echo date("d-m-Y H:i:s", strtotime($row_data['date_email_send']));?></td>
									<td bgcolor="#f5deb3" align="right"><b><?php echo $total_size_estat;?></b></td>
									<td bgcolor="#f5deb3" align="right"><b><?php echo $total_size_attachment;?></b></td>
									<td bgcolor="#f5deb3" align="right"><b><?php echo $total_semua_attachment;?></b></td>
									<td bgcolor="#eeb333" align="right"><b><?php echo $sizeberbayar;?></b></td>
									<?php if($_REQUEST['xls']<>'y'){ ?>
									<td align="center">
									<a onClick="view_template('<?php echo $row_data['antrian_id'];?>','<?php echo $blth;?>','<?php echo $flagtrans;?>')"><img title="view template" src="../images/icons/icon_view.gif" /></a>
									</td>
									<?php } ?>
                                </tr>
								<?php } ?>
                            </table>
                        </td>
                    </tr>
					<tr>
					<td><div align="center">
					<?php 
                      if($_REQUEST['xls']<>'y'){  
					  if (!empty($sent_from)){ $periodekirim = '&periode_kirim='.$_GET['periode_kirim']; }
					  echo pagings($offset,$jumlah_data,$limit,'div_paging','script_summary_detail_rev.php','act=show_email_dikirim&pr='.$flagtrans.'&blth='.$blth.'&m_loading_id='.$m_loading_id.$periodekirim);
					  } ?>
					</div></td>
					</tr>
					</table>
				<br/>
				</body>
        </html>
		<?php 
		
	}
	
	
	
	
	
	
	function export_excel_show_email_dikirim($blth, $flagtrans, $cycle, $per_send, $curloc, $locateFile){
	
	$m_loading_id = $_REQUEST['m_loading_id'];
		$per_send = explode('|', $_REQUEST['periode_kirim']);
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['pr'];
		
		$sent_from = $per_send[0];
		$sent_to = $per_send[1];
		if (!empty($sent_from)){
			$pk_where = " AND te.date_email_send BETWEEN '".$sent_from."' AND '".$sent_to."'";
		}else{
			$pk_where = "";
		}
		
		
		$m_loading_id_ck = strpos($m_loading_id, '|');
		if ($m_loading_id_ck !== false){
			$gtotal = true;
		}else{
			$gtotal = false;
		}
		
		
		if ($gtotal == true){
			$condv = " WHERE te.loading_id in (".str_replace('|',',', $m_loading_id).") $pk_where";
		}else{
			$condv = " WHERE te.loading_id = $m_loading_id $pk_where";
		}
		
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
		
		
		//include $curloc.'PHPExcel.php';
		//include $curloc.'PHPExcel/Writer/Excel5.php';
		

		// Create new PHPExcel object
		
		$sql_datas = "SELECT te.antrian_id, te.nomor_rekening, te.email, te.date_email_send,
						d.nama, d.size_pdf,
						ml.loading_file,
						SUM(maf.ukuran)AS total_attach
						FROM tr_email te
						LEFT JOIN $tabeldetail d ON te.loading_id = d.m_loading_id and te.nomor_rekening = d.nomor_rekening
						LEFT JOIN m_loading ml ON te.loading_id = ml.m_loading_id
						LEFT JOIN antrian_email_attach_file aeaf ON te.antrian_id = aeaf.antrian_id
						LEFT JOIN m_attach_file maf ON aeaf.m_attach_file_id::integer = maf.m_attach_file_id::integer
						$condv AND te.status_sample is not true
						GROUP BY te.antrian_id, te.nomor_rekening, te.email, te.date_email_send,
						d.nama, d.size_pdf,
						ml.loading_file
						ORDER BY te.date_email_send";
		$qry_datas = pg_query($sql_datas) or die('ERROR select data: '.$sql_datas);
		
		$sum_sql_datas = pg_num_rows($qry_datas);
		
		if($sum_sql_datas > 20000){
		
			// create pembuatan csv
			
			$dest = '../tmp/csv/';
			$name_file = 'list_email_dikirim_.csv';
			//die($dest.$name_file);
			if(file_exists($dest.$name_file)){ // check if file already
				unlink($dest.$name_file);
			}
			
			$handle = fopen($dest.$name_file, 'a+');
			$nl_dos	 = "\r\n";
			
			// header csv 
			$header = "No,"; 
			$header .= "Nama File,"; 
			$header .= "Nomor Rekening,";
			$header .= "Nama,";
			$header .= "Email,"; 
			$header .= "Tanggal Kirim,";
			$header .= "PDF Size,";
			$header .= "Attachment Size/Brosur,";
			$header .= "Total Size,";
			$header .= "Berbayar,";
			
			fwrite($handle, $header. $nl_dos);
			$nomor = 1;
			while($row_data = pg_fetch_array($qry_datas)){//extract each record
				$total_size_estat = floor($row_data['size_pdf']);
				$total_size_attachment = floor($row_data['total_attach']/1024);
				$total_semua_attachment = $total_size_estat+$total_size_attachment;
				$sizeberbayar = ceil($total_semua_attachment/300);

				$data = str_replace (',', ' ', $nomor) . ',' ; 
				$data .= str_replace (',', ' ', $row_data['loading_file']) . ',';
				$data .= str_replace (',', ' ', "'".$row_data['nomor_rekening']).' ' . ',';
				$data .= str_replace (',', ' ', $row_data['nama']) . ',';
				$data .= str_replace (',', ' ', $row_data['email']) . ',';
				$data .= date("d-m-Y H:i:s", strtotime($row_data['date_email_send'])) . ',';
				$data .= $total_size_estat . ',';
				$data .= $total_size_attachment . ',';
				$data .= $total_semua_attachment . ',';
				$data .= $sizeberbayar . ',';

				fwrite($handle, $data. $nl_dos); // write this variable in csv file
				
				++$Line;
				$nomor+=1;
			}
			fclose($handle);
			
			header('Content-type: text/plain');                             
			//header('application/vnd.ms-excel');                             
			// What file will be named after downloading                                  
			//header('Content-Disposition: attachment; filename="list_email_sukses_'.$m_loading_id.'_'.$loading_file. '.csv"');
			header('Content-Disposition: attachment; filename="'.$name_file .'"');
			// File to download                                
			readfile($dest.$name_file); 
			unlink($dest.$name_file);
			exit;
		
		
		//
		}else{
			$objPHPExcel = new PHPExcel();
			$F=$objPHPExcel->getActiveSheet();
			
			$styleArray = array(
				'font' => array(
					'name'         => 'Arial',
					'bold'         => true,
					'italic'    => false,
					'size'        => 12
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
					'wrap'       => true
				)
			);
			
			$F->mergeCellsByColumnAndRow(0,1,9,1)->setCellValue('A1', 'LIST EMAIL DIKIRIM')->getStyle('A1')->applyFromArray($styleArray);
			
			$Line=3;
			
			$F->getColumnDimension('A')->setWidth(5);
			$F->getColumnDimension('B')->setWidth(32);
			$F->getColumnDimension('C')->setWidth(17);
			$F->getColumnDimension('D')->setWidth(30);
			$F->getColumnDimension('E')->setWidth(30);
			$F->getColumnDimension('F')->setWidth(25);
			$F->getColumnDimension('G')->setWidth(25);
			$F->getColumnDimension('H')->setWidth(25);
			$F->getColumnDimension('I')->setWidth(25);
			$F->getColumnDimension('J')->setWidth(25);
			
			
			$styleArray = array(
				'font' => array(
					'name'         => 'Arial',
					'bold'         => true,
					'italic'    => false,
					'size'        => 12
				),
				'borders' => array(
				'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
				'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
				'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
				'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
			),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
					'wrap'       => true
				)
			);
			
			$objPHPExcel->getActiveSheet()->getStyle('A'.$Line.':J'.$Line.'')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('ffdfdfdf');
			
			$F->setCellValueExplicit('A'.$Line, "No", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('A'.$Line)->applyFromArray($styleArray);
			$F->setCellValueExplicit('B'.$Line, "Nama File", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('B'.$Line)->applyFromArray($styleArray);
			$F->setCellValueExplicit('C'.$Line, "Nomor Rekening", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('C'.$Line)->applyFromArray($styleArray);
			$F->setCellValueExplicit('D'.$Line, "Nama", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('D'.$Line)->applyFromArray($styleArray);
			$F->setCellValueExplicit('E'.$Line, "Email", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('E'.$Line)->applyFromArray($styleArray);
			$F->setCellValueExplicit('F'.$Line, "Tanggal Kirim", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('F'.$Line)->applyFromArray($styleArray);
			$F->setCellValueExplicit('G'.$Line, "PDF Size", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('G'.$Line)->applyFromArray($styleArray);
			$F->setCellValueExplicit('H'.$Line, "Attachment Size/Brosur", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('H'.$Line)->applyFromArray($styleArray);
			$F->setCellValueExplicit('I'.$Line, "Total Size", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('I'.$Line)->applyFromArray($styleArray);
			$F->setCellValueExplicit('J'.$Line, "Berbayar", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('J'.$Line)->applyFromArray($styleArray);
			
			$Line=4;
			$nomor = 1;
			while($row_data = pg_fetch_array($qry_datas)){//extract each record
			$total_size_estat = floor($row_data['size_pdf']);
			$total_size_attachment = floor($row_data['total_attach']/1024);
			$total_semua_attachment = $total_size_estat+$total_size_attachment;
			$sizeberbayar = ceil($total_semua_attachment/300);
			
			$styleArray = array(
			'borders' => array(
				'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
				'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
				'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
				'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
			)
			);
			
			$F->getStyle('A'.$Line.'')->applyFromArray($styleArray);
			$F->getStyle('B'.$Line.'')->applyFromArray($styleArray);
			$F->getStyle('C'.$Line.'')->applyFromArray($styleArray);
			$F->getStyle('D'.$Line.'')->applyFromArray($styleArray);
			$F->getStyle('E'.$Line.'')->applyFromArray($styleArray);
			$F->getStyle('F'.$Line.'')->applyFromArray($styleArray);
			$F->getStyle('G'.$Line.'')->applyFromArray($styleArray);
			$F->getStyle('H'.$Line.'')->applyFromArray($styleArray);
			$F->getStyle('I'.$Line.'')->applyFromArray($styleArray);
			$F->getStyle('J'.$Line.'')->applyFromArray($styleArray);
			
			$loading_file = $row_data['loading_file'];
			
				$F->setCellValueExplicit('A'.$Line, $nomor, PHPExcel_Cell_DataType::TYPE_STRING)
					->setCellValueExplicit('B'.$Line, $row_data['loading_file'], PHPExcel_Cell_DataType::TYPE_STRING)
					->setCellValueExplicit('C'.$Line, $row_data['nomor_rekening'], PHPExcel_Cell_DataType::TYPE_STRING)
					->setCellValueExplicit('D'.$Line, $row_data['nama'], PHPExcel_Cell_DataType::TYPE_STRING)
					->setCellValueExplicit('E'.$Line, $row_data['email'], PHPExcel_Cell_DataType::TYPE_STRING)
					->setCellValueExplicit('F'.$Line, date("d-m-Y H:i:s", strtotime($row_data['date_email_send'])), PHPExcel_Cell_DataType::TYPE_STRING)
					->setCellValueExplicit('G'.$Line, $total_size_estat, PHPExcel_Cell_DataType::TYPE_NUMERIC)
					->setCellValueExplicit('H'.$Line, $total_size_attachment, PHPExcel_Cell_DataType::TYPE_NUMERIC)
					->setCellValueExplicit('I'.$Line, $total_semua_attachment, PHPExcel_Cell_DataType::TYPE_NUMERIC)
					->setCellValueExplicit('J'.$Line, $sizeberbayar, PHPExcel_Cell_DataType::TYPE_NUMERIC);
				++$Line;
				$nomor+=1;
			}
			// Redirect output to a clients web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="list_email_dikirim_'.$m_loading_id.'_'.$loading_file .'.xls"');
			header('Cache-Control: max-age=0');

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
			exit;
		}
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function show_list_antrian($header_utama){
		$m_loading_id = $_REQUEST['m_loading_id'];
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['pr'];
		
		$blth_balik = substr($blth,-4).substr($blth,0,2);
		
			$BLTH_BARU = BLTH_BARU;
			if($blth_balik>$BLTH_BARU){
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
			
		
		$per_send = explode('|', $_REQUEST['periode_kirim']);
		$sent_from = $per_send[0];
		$sent_to = $per_send[1];
		
		$m_loading_id_ck = strpos($m_loading_id, '|');
		if ($m_loading_id_ck !== false){
			$gtotal = true;
		}else{
			$gtotal = false;
		}
		
		if ($gtotal == true){
		$add_select ="d.nama_file,";
		$condv = "a.loading_id in (".str_replace('|',',', $m_loading_id).")";
		$condv_nama = "WHERE m_loading_id in (".str_replace('|',',', $m_loading_id).")";
		$group_order ="GROUP BY d.nama_file, a.loading_id, tgl_antrian, d.nomor_rekening, a.nama, a.email, a.pdf_name, b.tgl_jadwal
		ORDER BY a.loading_id, tgl_antrian DESC";
		}else{
		$add_select ="";
		$condv = "a.loading_id = $m_loading_id";
		$condv_nama = "WHERE m_loading_id = $m_loading_id";
		$group_order ="GROUP BY tgl_antrian, d.nomor_rekening, a.nama, a.email, a.pdf_name, b.tgl_jadwal
		ORDER BY tgl_antrian DESC";
		}
								
		$sql = "SELECT loading_file,blth FROM m_loading ".$condv_nama;
		$qry = pg_query($sql) or die('ERROR query: '.$sql);
		while($row = pg_fetch_array($qry)){
		$nama_file .= "<span style='font-family:verdana;font-size:8pt;color:#347822;'>".$row['loading_file'] . ", </span>";
		$blth = $row['blth'];
		}
		
		$p_nama_file = strlen($nama_file);
		$nama_file = substr($nama_file,0,$p_nama_file-9);
		?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title><?= $title ?></title>
            </head>
            <? require_once("../include/script.php"); ?>
            <body>
            	<table align="center" border="0" width="1000">
                	<tr>
                    	<td>
                        	<table align="center" border="0" cellpadding="2" cellspacing="0" width="100%">
                            	<tr height="30">
                                	<td class="title" colspan="2" align="center" valign="middle">
                                    	LIST DI ANTRIAN EMAIL
                                    </td>
                                </tr>
								<tr height="20">
                                	<td colspan="2"><b><u><?=$header_utama?></u></b></td>
                                </tr>
								 <tr height="20">
                                	<td colspan="3" ><b>Nama File: <?=$nama_file;?></b></td>
									</tr>
								<?php
								if (!empty($sent_from)){
									$label = ''.$sent_from.' s/d '.$sent_to.'';
								?>
									<tr height="30">
										<td><b>Periode: <?=$label?></b></td>
										<td align="right"><a onClick="window.open('script_summary_detail_rev.php?act=show_list_antrian<?php if (!empty($sent_from)){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>&xls=y')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a></td>
									</tr>
								<?php
								}else{
									if ($gtotal != true){$label='Periode: '.$blth;}
								?>
									<tr height="30">
										<td><b><?=$label?></b></td>
										<td align="right"><a onClick="window.open('script_summary_detail_rev.php?act=show_list_antrian&blth=<?php echo $blth?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>&xls=y')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a></td>
									</tr>
								<?php
								}?>
                            </table>
                        </td>
                    </tr>
                    <tr>
                    	<td>
                        	<table align="center" border="1" cellpadding="2" cellspacing="0" width="100%">
                            	<tr height="30" bgcolor="#FF0000" style="color:#FFF">
                                	<td width="4%" align="center"><b>No</b></td>
									<?php
									if ($gtotal == true){
										echo '<td align="center"><b>Cycle</b></td>';
									}
									?>
                                	<td align="center"><b>Nomor Kartu</b></td>
                                	<td align="center"><b>Nama</b></td>
                                	<td align="center"><b>Alamat Email</b></td>
                                	<td align="center"><b>Nama PDF</b></td>
									<td align="center"><b>Tanggal Antrian</b></td>
                                	<td align="center"><b>Jadwal Kirim</b></td>
                                </tr>
                                <?php

								$i = 0;
								$limit = 500;
								if(isset($_REQUEST['offset']))	$offset = $_REQUEST['offset']; else $offset = 0;
								
								$sql_datas = "
									SELECT ".$add_select." to_char(tgl_antrian,'DD-Mon-YYYY HH24:MI:SS')as tgl_antrian, d.nomor_rekening, a.nama, a.email, a.pdf_name,
										CASE
											WHEN to_char(b.tgl_jadwal,'dd-mm-yyyy hh24:mi:ss') is null THEN 'TIDAK TERJADWAL'
											ELSE to_char(b.tgl_jadwal,'dd-mm-yyyy hh24:mi:ss')
										END AS tgl_jadwal
									FROM antrian_email a
										LEFT JOIN m_jadwal b
											ON a.jadwal_id = b.jadwal_id
										JOIN $tabeldetail d
											ON d.nomor_rekening = a.nomor_rekening AND d.m_loading_id = a.loading_id
									WHERE ".$condv."
									".$group_order."
									
								";//--a.nomor_customer, a.nomor_rekening, a.nama, a.email, a.pdf_name
								$sql_data .= $sql_datas . " LIMIT $limit OFFSET $offset";
								if($_REQUEST['xls']=='y'){
								$qry_data = pg_query($sql_datas) or die('ERROR select data: '.$sql_datas);
								} else {
								$qry_data = pg_query($sql_data) or die('ERROR select data: '.$sql_data);
								}
								$qry_datas = pg_query($sql_datas) or die('ERROR select data: '.$sql_datas);
								$jumlah_data = pg_num_rows($qry_datas);
								$i = $offset;
								
								while($row_data = pg_fetch_array($qry_data)){
									$i++;
									//$m_loading_id = $row_data['m_loading_id'];
								?>
                                <tr height="30">
                                	<td align="center"><?php echo $i?></td>
									<?php
									if ($gtotal == true){
										echo '<td align="center">'.$row_data['nama_file'].'</td>';
									}
									?>
                                	<td align="center">&nbsp;<?php echo $row_data['nomor_rekening']?></td>
                                	<td align="center"><?php echo $row_data['nama']?></td>
                                	<td align="center"><?php echo $row_data['email']?></td>
                                	<td align="center"><?php echo $row_data['pdf_name']?></td>
									<td align="center"><?php echo $row_data['tgl_antrian']?></td>
                                	<td align="center"><?php echo $row_data['tgl_jadwal']?></td>
                                </tr>
                                <?php
									}
								?>
                            </table>
                        </td>
                    </tr>
					<tr>
					<td>
					<div align="center">
				 <?php
                      if($_REQUEST['xls']<>'y'){  
					  echo pagings($offset,$jumlah_data,$limit,'div_paging','script_summary_detail_rev.php','act=show_list_antrian&blth='.$blth.'&pr='.$flagtrans.'&m_loading_id='.$m_loading_id.'&tipe='.$tipe);
					  }
                    ?>
					</div>
					</td>
					</tr>
                </table>
            </body>
        </html>
        <?php
	}
	
	function show_email_sukses($header_utama){
	$SIZE_BERBAYAR = SIZE_BERBAYAR;
		$m_loading_id = $_REQUEST['m_loading_id'];
		$per_send = explode('|', $_REQUEST['periode_kirim']);
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['pr'];
		
		$sent_from = $per_send[0];
		$sent_to = $per_send[1];
		if (!empty($sent_from)){
			$pk_where = "AND te.date_email_send BETWEEN '".$sent_from."' AND '".$sent_to."'";
		}else{
			$pk_where = "";
		}
		
		
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
		
		$sql = "SELECT loading_file FROM m_loading WHERE m_loading_id = ".$m_loading_id;
		$qry = pg_query($sql) or die('ERROR query: '.$sql);
		$row = pg_fetch_assoc($qry);
		$nama_file = $row['loading_file'];
		
		$per_send = explode('|', $_REQUEST['periode_kirim']);
		$sent_from = $per_send[0];
		$sent_to = $per_send[1];
		
		$m_loading_id_ck = strpos($m_loading_id, '|');
		if ($m_loading_id_ck !== false){
			$gtotal = true;
		}else{
			$gtotal = false;
		}
		
		?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>LIST EMAIL SUKSES TERKIRIM</title>
            </head>
            <? require_once("../include/script.php"); ?>
            <body>
            	<table align="center" border="0" width="1000">
                	<tr>
                    	<td>
                        	<table align="center" border="0" cellpadding="2" cellspacing="0" width="100%">
                            	<tr height="30">
                                	<td class="title" colspan="5" align="center" valign="middle">
                                    	LIST EMAIL SUKSES
                                    </td>
                                </tr>
                                <tr height="20">
                                	<td colspan="2"><b><u><?=$header_utama?></u></b></td>
                                </tr>
								<?php
								if (!empty($sent_from)){
									$label = ''.$sent_from.' s/d '.$sent_to.'';
								?>
									<tr height="30">
										<td><b>Periode: <?=$label?></b></td>
										<td align="right">
										<a onClick="window.open('script_summary_detail_rev.php?act=export_excel_show_email_sukses<?php if (!empty($sent_from)){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>&xls=y')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a>
										</td>
									</tr>
								<?php
								} else {
								?>
									<tr height="30">
										<td><b><?=$label?></b></td>
										<td align="right">
										<!--<a onClick="window.open('script_summary_detail_rev.php?act=show_email_sukses&blth=<?php echo $blth?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>&xls=y')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a>-->
										<a onClick="window.open('script_summary_detail_rev.php?act=export_excel_show_email_sukses&blth=<?php echo $blth;?><?php if (!empty($sent_from)){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>')"><img height="40px" title="testing download excel" src="../images/icons/xls_icon.gif" /></a>
										</td>
									</tr>
									<?php } ?>
                            </table>
                        </td>
                    </tr>
                    <tr>
                    	<td>
                        	<table align="center" border="1" cellpadding="2" cellspacing="0" width="100%" style="border-collapse:collapse;border-color:#000000">
                            	<tr height="30" bgcolor="#FF0000" style="color:#FFF">
                                	<td width="4%" align="center" rowspan="2"><b>No</b></td>
									<td align="center" rowspan="2"><b>Cycle</b></td>
                                	<td align="center" rowspan="2"><b>Nomor Kartu</b></td>
                                	<td align="left" rowspan="2">&nbsp;<b>Nama</b></td>
                                	<td align="center" rowspan="2"><b>Alamat Email</b></td>
									<td align="left" rowspan="2">&nbsp;<b>Tgl Kirim</b></td>
									
									<td align="left" rowspan="2">&nbsp;<b>Tgl Read</b></td>
									<td align="left" rowspan="2">&nbsp;<b>Pesan Read</b></td>
									<td align="left" rowspan="2">&nbsp;<b>Tipe Read</b></td>
									
                                	<td align="center" colspan="4" bgcolor="#cd853f"><b>Size (KB)</b></td>
									<?php if($_REQUEST['xls']<>'y'){ ?>
									<td align="left" rowspan="2">&nbsp;<b>Template</b></td>
									<?php } ?>
                                </tr>
								<tr>
                                	<td align="center" bgcolor="#deb887"><b>PDF</b></td>
                                	<td align="center" bgcolor="#deb887"><b>Brosur</b></td>
                                	<td align="center" bgcolor="#deb887"><b>Total</b></td>
                                	<td align="center" bgcolor="#eeb333"><b>Berbayar</b></td>
                                </tr>
								
                                <?php
								
								if ($gtotal == true){
									$condv = "WHERE te.loading_id in (".str_replace('|',',', $m_loading_id).") $pk_where";
								}else{
									$condv = "WHERE te.loading_id = '$m_loading_id' $pk_where";
								}
								
								$i = 0;
								$limit = 500;
								if(isset($_REQUEST['offset']))	$offset = $_REQUEST['offset']; else $offset = 0;
								
								$sql_datas = "SELECT
									 te.antrian_id, te.nomor_rekening, te.email, te.date_email_send,
									 d.nama, d.size_pdf,
									 ml.loading_file,
									 SUM(maf.ukuran)AS total_attach
									 ,te.tgl_read, te.body_email_read, te.read_method
									FROM tr_email te
									LEFT JOIN $tabeldetail d ON te.loading_id = d.m_loading_id and te.nomor_rekening = d.nomor_rekening
									LEFT JOIN m_loading ml ON te.loading_id = ml.m_loading_id
									LEFT JOIN antrian_email_attach_file aeaf ON te.antrian_id = aeaf.antrian_id
									LEFT JOIN m_attach_file maf ON aeaf.m_attach_file_id::integer = maf.m_attach_file_id::integer
									$condv AND te.status_sample is not true
									AND te.email_callback IS NULL 
									GROUP BY te.antrian_id, te.nomor_rekening, te.email, te.date_email_send,
									 d.nama, d.size_pdf,
									 ml.loading_file ,te.tgl_read, te.body_email_read, te.read_method
									ORDER BY te.date_email_send";
							
								//echo $sql_datas;
								$sql_data .= $sql_datas . " LIMIT $limit OFFSET $offset";
								if($_REQUEST['xls']=='y'){
								$qry_data = pg_query($sql_datas) or die('ERROR select data: '.$sql_datas);
								} else {
								$qry_data = pg_query($sql_data) or die('ERROR select data: '.$sql_data);
								}
								$qry_datas = pg_query($sql_datas) or die('ERROR select data: '.$sql_datas);
								$jumlah_data = pg_num_rows($qry_datas);
								$i = $offset;
									
								while($row_data = pg_fetch_array($qry_data)){
									$i++;
									$antrian_id = $row_data['antrian_id'];
									$total_size_estat = floor($row_data['size_pdf']);
									$total_size_attachment = floor($row_data['total_attach']/1024);
									$total_semua_attachment = $total_size_estat+$total_size_attachment;
									$sizeberbayar = ceil($total_semua_attachment/$SIZE_BERBAYAR);
									
									
									$tgl_read = $row_data['tgl_read'];
									$body_email_read = str_replace(chr(13),"", $row_data['body_email_read']);
									$body_email_read = str_replace('>',"", $row_data['body_email_read']);

									$read_method =  ( empty($row_data['body_email_read']) && empty($row_data['tgl_read'])) ? '' : 'x' ;
									
									if ( !empty( $read_method ) )
									{
										$read_method = ( empty($row_data['read_method'])) ? 'others' : $row_data['read_method'] ;
									}
								?>
                                <tr height="30">
                                	<td align="center"><?php echo $i?></td>
									<td align="left">&nbsp;<?php echo $row_data['loading_file'];?></td>
                                	<td align="center"><?php echo '&nbsp;'.$row_data['nomor_rekening']?></td>
                                	<td align="left">&nbsp;<?php echo $row_data['nama']?></td>
                                	<td align="left"><?php echo $row_data['email']?></td>
                                	<td align="center"><?php echo $row_data['date_email_send']?></td>
									
									<td align="center"><?php echo $tgl_read?></td>
									<td align="left"><?php echo $body_email_read?></td>
									<td align="center"><?php echo $read_method?></td>
									
									<td bgcolor="#f5deb3" align="right"><b><?php echo $total_size_estat;?></b></td>
									<td bgcolor="#f5deb3" align="right"><b><?php echo $total_size_attachment;?></b></td>
									<td bgcolor="#f5deb3" align="right"><b><?php echo $total_semua_attachment;?></b></td>
									<td bgcolor="#eeb333" align="right"><b><?php echo $sizeberbayar;?></b></td>
									<?php if($_REQUEST['xls']<>'y'){ ?>
                                    <td align="center">
									<a onClick="view_template('<?php echo $antrian_id;?>','<?php echo $blth;?>','<?php echo $flagtrans;?>')"><img title="view template" src="../images/icons/icon_view.gif" /></a>
									</td>
									<?php } ?>
                                </tr>
                                <?php
									}
								?>
                            </table>
                        </td>
                    </tr>
					<tr>
					<td>
					<br/>
				<div align="center">
				 <?php
                      if($_REQUEST['xls']<>'y'){  
					  echo pagings($offset,$jumlah_data,$limit,'div_paging','script_summary_detail_rev.php','act=show_email_sukses&blth='.$blth.'&pr='.$flagtrans.'&m_loading_id='.$m_loading_id.'&tipe='.$tipe);
					  }
                    ?>
					</div>
					</td>
					</tr>
                </table>
            </body>
        </html>
        <?php
	}
	
	
	
	//** REPORT DENGAN NOMOR REKENING TANPA SPASI **//
	//** 18 Des 2013 **//
	
		function export_excel_show_email_sukses($blth, $flagtrans, $cycle, $per_send, $curloc, $locateFile) {
		
		$m_loading_id = $_REQUEST['m_loading_id'];
		$per_send = explode('|', $_REQUEST['periode_kirim']);
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['pr'];
		//die($m_loading_id);
		$sent_from = $per_send[0];
		$sent_to = $per_send[1];
		if (!empty($sent_from)){
			$pk_where = "AND te.date_email_send BETWEEN '".$sent_from."' AND '".$sent_to."'";
		}else{
			$pk_where = "";
		}
		
		
		$m_loading_id_ck = strpos($m_loading_id, '|');
		if ($m_loading_id_ck !== false){
			$gtotal = true;
		}else{
			$gtotal = false;
		}
		
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
		
		
		if ($gtotal == true){
			$condv = "WHERE te.loading_id in (".str_replace('|',',', $m_loading_id).") $pk_where";
		}else{
			$condv = "WHERE te.loading_id = '$m_loading_id' $pk_where";
		}

		
		
		//include $curloc.'PHPExcel.php';
		//include $curloc.'PHPExcel/Writer/Excel5.php';
		

		// Create new PHPExcel object
		
		$sql_datas = "SELECT
							 te.antrian_id, te.nomor_rekening, te.email, te.date_email_send,
							 d.nama, d.size_pdf,
							 ml.loading_file,
							 SUM(maf.ukuran)AS total_attach
							 ,te.tgl_read, te.body_email_read, te.read_method
							FROM tr_email te
							LEFT JOIN $tabeldetail d ON te.loading_id = d.m_loading_id and te.nomor_rekening = d.nomor_rekening
							LEFT JOIN m_loading ml ON te.loading_id = ml.m_loading_id
							LEFT JOIN antrian_email_attach_file aeaf ON te.antrian_id = aeaf.antrian_id
							LEFT JOIN m_attach_file maf ON aeaf.m_attach_file_id::integer = maf.m_attach_file_id::integer
							$condv AND te.status_sample is not true
							AND te.email_callback IS NULL 
							GROUP BY te.antrian_id, te.nomor_rekening, te.email, te.date_email_send,
							 d.nama, d.size_pdf,
							 ml.loading_file
							  ,te.tgl_read, te.body_email_read, te.read_method
							ORDER BY te.date_email_send";
		//die	($sql_datas);				
		$qry_datas = pg_query($sql_datas) or die('ERROR select data: '.$sql_datas);
		$sum_sql_datas = pg_num_rows($qry_datas);
		
		
		// buat logic untuk hitung jumlah excel
		if($sum_sql_datas > 20000){
		
			// create pembuatan csv
			
			$dest = '../tmp/csv/';
			$name_file = 'list_email_sukses_.csv';
			//die($dest.$name_file);
			if(file_exists($dest.$name_file)){ // check if file already
				unlink($dest.$name_file);
			}
			
			$handle = fopen($dest.$name_file, 'a+');
			$nl_dos	 = "\r\n";
			
			
			
			/*
			$F->setCellValueExplicit('G'.$Line, "Tanggal Read", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('G'.$Line)->applyFromArray($styleArray);
			$F->setCellValueExplicit('H'.$Line, "Pesan Read", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('H'.$Line)->applyFromArray($styleArray);
			$F->setCellValueExplicit('I'.$Line, "Tipe Read", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('I'.$Line)->applyFromArray($styleArray);
				
			*/
			// header csv 
			$header = "No,"; 
			$header .= "Nama File,"; 
			$header .= "Nomor Rekening,";
			$header .= "Nama,";
			$header .= "Email,"; 
			$header .= "Tanggal Kirim,";
			$header .= "Tanggal Read,";
			$header .= "Pesan Read,";
			$header .= "Tipe Read,";
			$header .= "PDF Size,";
			$header .= "Attachment Size/Brosur,";
			$header .= "Total Size,";
			$header .= "Berbayar,";
			
			fwrite($handle, $header. $nl_dos);
			$nomor = 1;
			while($row_data = pg_fetch_array($qry_datas)){//extract each record
				$total_size_estat = floor($row_data['size_pdf']);
				$total_size_attachment = floor($row_data['total_attach']/1024);
				$total_semua_attachment = $total_size_estat+$total_size_attachment;
				$sizeberbayar = ceil($total_semua_attachment/300);

				$tgl_read= date("d-m-Y H:i:s", strtotime($row_data['tgl_read']));
				
				if ( $tgl_read=='01-01-1970 07:00:00' )
				{
					$tgl_read='';
				}
				
				$x123 = trim(preg_replace('/\s\s+/', ' ',$row_data['body_email_read']));
				$string123 = trim(preg_replace('/\s+/', ' ', $x123));
				
				$data = str_replace (',', ' ', $nomor) . ',' ; 
				$data .= str_replace (',', ' ', $row_data['loading_file']) . ',';
				$data .= str_replace (',', ' ', "'".$row_data['nomor_rekening']).' ' . ',';
				$data .= str_replace (',', ' ', $row_data['nama']) . ',';
				$data .= str_replace (',', ' ', $row_data['email']) . ',';
				$data .= date("d-m-Y H:i:s", strtotime($row_data['date_email_send'])) . ',';
				$data .= str_replace (',', ' ', $tgl_read) . ',';
				$data .= str_replace (',', ' ', $string123) . ',';
				$data .= str_replace (',', ' ', $row_data['read_method']) . ',';
				$data .= $total_size_estat . ',';
				$data .= $total_size_attachment . ',';
				$data .= $total_semua_attachment . ',';
				$data .= $sizeberbayar . ',';

				fwrite($handle, $data. $nl_dos); // write this variable in csv file
				
				++$Line;
				$nomor+=1;
			}
			fclose($handle);
			
			header('Content-type: text/plain');                             
			//header('application/vnd.ms-excel');                             
			// What file will be named after downloading                                  
			//header('Content-Disposition: attachment; filename="list_email_sukses_'.$m_loading_id.'_'.$loading_file. '.csv"');
			header('Content-Disposition: attachment; filename="'.$name_file .'"');
			// File to download                                
			readfile($dest.$name_file); 
			unlink($dest.$name_file);
			exit;
		
		
		//
		}else{
				$objPHPExcel = new PHPExcel();
				$F=$objPHPExcel->getActiveSheet();
				
				$styleArray = array(
					'font' => array(
						'name'         => 'Arial',
						'bold'         => true,
						'italic'    => false,
						'size'        => 12
					),
					'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
						'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
						'wrap'       => true
					)
				);
				
				$F->mergeCellsByColumnAndRow(0,1,5,1)->setCellValue('A1', 'LIST EMAIL SUKSES TERKIRIM')->getStyle('A1')->applyFromArray($styleArray);
				
				$Line=3;
				
				$F->getColumnDimension('A')->setWidth(5);
				$F->getColumnDimension('B')->setWidth(32);
				$F->getColumnDimension('C')->setWidth(17);
				$F->getColumnDimension('D')->setWidth(30);
				$F->getColumnDimension('E')->setWidth(30);
				$F->getColumnDimension('F')->setWidth(25);
				$F->getColumnDimension('G')->setWidth(25);
				$F->getColumnDimension('H')->setWidth(25);
				$F->getColumnDimension('I')->setWidth(25);
				$F->getColumnDimension('J')->setWidth(25);
				$F->getColumnDimension('K')->setWidth(25);
				$F->getColumnDimension('L')->setWidth(25);
				$F->getColumnDimension('M')->setWidth(25);
				
				
				$styleArray = array(
					'font' => array(
						'name'         => 'Arial',
						'bold'         => true,
						'italic'    => false,
						'size'        => 12
					),
					'borders' => array(
					'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
					'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
					'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
					'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
				),
					'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
						'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
						'wrap'       => true
					)
				);
				
				$objPHPExcel->getActiveSheet()->getStyle('A'.$Line.':M'.$Line.'')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('ffdfdfdf');
				
				$F->setCellValueExplicit('A'.$Line, "No", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('A'.$Line)->applyFromArray($styleArray);
				$F->setCellValueExplicit('B'.$Line, "Nama File", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('B'.$Line)->applyFromArray($styleArray);
				$F->setCellValueExplicit('C'.$Line, "Nomor Rekening", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('C'.$Line)->applyFromArray($styleArray);
				$F->setCellValueExplicit('D'.$Line, "Nama", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('D'.$Line)->applyFromArray($styleArray);
				$F->setCellValueExplicit('E'.$Line, "Email", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('E'.$Line)->applyFromArray($styleArray);
				$F->setCellValueExplicit('F'.$Line, "Tanggal Kirim", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('F'.$Line)->applyFromArray($styleArray);
				
				$F->setCellValueExplicit('G'.$Line, "Tanggal Read", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('G'.$Line)->applyFromArray($styleArray);
				$F->setCellValueExplicit('H'.$Line, "Pesan Read", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('H'.$Line)->applyFromArray($styleArray);
				$F->setCellValueExplicit('I'.$Line, "Tipe Read", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('I'.$Line)->applyFromArray($styleArray);
				
				$F->setCellValueExplicit('J'.$Line, "PDF Size", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('J'.$Line)->applyFromArray($styleArray);
				$F->setCellValueExplicit('K'.$Line, "Attachment Size/Brosur", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('K'.$Line)->applyFromArray($styleArray);
				$F->setCellValueExplicit('L'.$Line, "Total Size", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('L'.$Line)->applyFromArray($styleArray);
				$F->setCellValueExplicit('M'.$Line, "Berbayar", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('M'.$Line)->applyFromArray($styleArray);

				$Line=4;
				$nomor = 1;
				while($row_data = pg_fetch_array($qry_datas)){//extract each record
				$total_size_estat = floor($row_data['size_pdf']);
				$total_size_attachment = floor($row_data['total_attach']/1024);
				$total_semua_attachment = $total_size_estat+$total_size_attachment;
				$sizeberbayar = ceil($total_semua_attachment/300);
				
				$tgl_read= date("d-m-Y H:i:s", strtotime($row_data['tgl_read']));
				
				if ( $tgl_read=='01-01-1970 07:00:00' )
				{
					$tgl_read='';
				}
				
				$styleArray = array(
				'borders' => array(
					'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
					'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
					'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
					'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
				)
				);
				
				$F->getStyle('A'.$Line.'')->applyFromArray($styleArray);
				$F->getStyle('B'.$Line.'')->applyFromArray($styleArray);
				$F->getStyle('C'.$Line.'')->applyFromArray($styleArray);
				$F->getStyle('D'.$Line.'')->applyFromArray($styleArray);
				$F->getStyle('E'.$Line.'')->applyFromArray($styleArray);
				$F->getStyle('F'.$Line.'')->applyFromArray($styleArray);
				$F->getStyle('G'.$Line.'')->applyFromArray($styleArray);
				$F->getStyle('H'.$Line.'')->applyFromArray($styleArray);
				$F->getStyle('I'.$Line.'')->applyFromArray($styleArray);
				$F->getStyle('J'.$Line.'')->applyFromArray($styleArray);
				
				$F->getStyle('K'.$Line.'')->applyFromArray($styleArray);
				$F->getStyle('L'.$Line.'')->applyFromArray($styleArray);
				$F->getStyle('M'.$Line.'')->applyFromArray($styleArray);
				
				$loading_file = $row_data['loading_file'];
				
					$F->setCellValueExplicit('A'.$Line, $nomor, PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('B'.$Line, $row_data['loading_file'], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('C'.$Line, $row_data['nomor_rekening'], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('D'.$Line, $row_data['nama'], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('E'.$Line, $row_data['email'], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('F'.$Line, date("d-m-Y H:i:s", strtotime($row_data['date_email_send'])), PHPExcel_Cell_DataType::TYPE_STRING)
						
						->setCellValueExplicit('G'.$Line, $tgl_read, PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('H'.$Line, $row_data['body_email_read'], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('I'.$Line, $row_data['read_method'], PHPExcel_Cell_DataType::TYPE_STRING)
						
						->setCellValueExplicit('J'.$Line, $total_size_estat, PHPExcel_Cell_DataType::TYPE_NUMERIC)
						->setCellValueExplicit('K'.$Line, $total_size_attachment, PHPExcel_Cell_DataType::TYPE_NUMERIC)
						->setCellValueExplicit('L'.$Line, $total_semua_attachment, PHPExcel_Cell_DataType::TYPE_NUMERIC)
						->setCellValueExplicit('M'.$Line, $sizeberbayar, PHPExcel_Cell_DataType::TYPE_NUMERIC);
					++$Line;
					$nomor+=1;
				}
				// Redirect output to a clients web browser (Excel5)
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="list_email_sukses_'.$m_loading_id.'_'.$loading_file . '_part' . $i.'.xls"');
				header('Cache-Control: max-age=0');

				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				$objWriter->save('php://output');
		
		}
	}
	
	/**
	* create file and output
	* delete file excel temp
	*/
	function create_zip($dir_part, $name_file_zip){
		error_reporting(E_ALL);
		$zip = new ZipArchive();
		$file_zip = $dir_part . $name_file_zip;
		
		if ($zip->open($file_zip, ZipArchive::CREATE)!==TRUE) {
			exit("cannot open <$file_zip>\n");
		}
		
		// adding_file_to zip
		$arr_nama_file_part = glob($dir_part ."*.xls");
		
		foreach ($arr_nama_file_part as $nama_file_part){
			//echo $nama_file_part;
			$zip->addFile($nama_file_part); // name file adding in here
		}
		
		$zip->close();
		
		// or however you get the path
		header("Content-Type: application/zip");
		header('Content-Disposition: attachment; filename="'.$name_file_zip.'"');
		header("Content-Length: " . filesize($file_zip));
		header('Cache-Control: max-age=0');

		readfile($file_zip);
		// delete file part
		foreach ($arr_nama_file_part as $nama_file_part){
			unlink($nama_file_part);
		}
		exit;
	}

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function show_email_gagal($header_utama){
	$SIZE_BERBAYAR = SIZE_BERBAYAR;
		$m_loading_id = $_REQUEST['m_loading_id'];
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['pr'];
		
		$per_send = explode('|', $_REQUEST['periode_kirim']);
		$sent_from = $per_send[0];
		$sent_to = $per_send[1];
		if (!empty($sent_from)){
			$pk_where = "AND a.date_email_send BETWEEN '".$sent_from."' AND '".$sent_to."'";
		}else{
			$pk_where = "";
		}
		
		$blth_balik = substr($blth,-4).substr($blth,0,2);
		
			$BLTH_BARU = BLTH_BARU;
			if($blth_balik>$BLTH_BARU){
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
			
			
		$sql = "SELECT loading_file FROM m_loading WHERE m_loading_id = ".$m_loading_id;
		$qry = pg_query($sql) or die('ERROR query: '.$sql);
		$row = pg_fetch_assoc($qry);
		$nama_file = $row['loading_file'];
		
		$per_send = explode('|', $_REQUEST['periode_kirim']);
		$sent_from = $per_send[0];
		$sent_to = $per_send[1];
		
		$m_loading_id_ck = strpos($m_loading_id, '|');
		if ($m_loading_id_ck !== false){
			$gtotal = true;
		}else{
			$gtotal = false;
		}
		?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>LIST EMAIL GAGAL TERKIRIM</title>
            </head>
            <? require_once("../include/script.php"); ?>
            <body>
            	<table align="center" border="0" width="1000">
                	<tr>
                    	<td>
                        	<table align="center" border="0" cellpadding="2" cellspacing="0" width="100%">
                            	<tr height="30">
                                	<td class="title" colspan="5" align="center" valign="middle">
                                    	LIST EMAIL GAGAL
                                    </td>
                                </tr>
                                <tr height="20">
                                	<td colspan="2"><b><u><?=$header_utama?></u></b></td>
                                </tr>
								<?php
								if (!empty($sent_from)){
									$label = ''.$sent_from.' s/d '.$sent_to.'';
								?>
									<tr height="30">
										<td><b>Periode: <?=$label?></b></td>
										<td align="right"><a onClick="window.open('script_summary_detail_rev.php?act=export_excel_show_email_gagal<?php if (!empty($sent_from)){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>&xls=y')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a></td>
									</tr>
								<?php
								}else{
									if ($gtotal == true){$label='Periode: '.$blth;}else{$label='Nama File: '.$nama_file;}
								?>
									<tr height="30">
										<td><b><?=$label?></b></td>
										<td align="right">
										<!--<a onClick="window.open('script_summary_detail_rev.php?act=show_email_gagal&blth=<?php echo $blth?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>&xls=y')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a>-->
										<a onClick="window.open('script_summary_detail_rev.php?act=export_excel_show_email_gagal&blth=<?php echo $blth;?><?php if (!empty($sent_from)){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>')"><img height="40px" title="testing download excel" src="../images/icons/xls_icon.gif" /></a>
										</td>
									</tr>
								<?php
								}?>
                            </table>
                        </td>
                    </tr>
                    <tr>
                    	<td>
                        	<table align="center" border="1" cellpadding="2" cellspacing="0" width="100%" style="border-collapse:collapse;border-color:#000000;">
                            	<tr height="30" bgcolor="#FF0000" style="color:#FFF">
                                	<td width="4%" align="center" rowspan="2"><b>No</b></td>
									<td align="center" rowspan="2"><b>Cycle</b></td>
									<td align="center" rowspan="2"><b>Nomor Kartu</b></td>
                                	<td align="center" rowspan="2"><b>Nama</b></td>
                                	<td align="center" rowspan="2"><b>Alamat Email</b></td>
                                	<td align="center" rowspan="2"><b>Tgl Kirim</b></td>
                                	<td align="center" rowspan="2"><b>Tgl Feedback</b></td>
                                	<td align="center" rowspan="2"><b>Pesan Error</b></td>
                                	<td align="center" rowspan="2"><b>Klasifikasi Error</b></td>
									<td align="center" colspan="4" bgcolor="#cd853f"><b>Size (KB)</b></td>
                                	<td align="center"><b>Template</b></td>
                                </tr>
								<tr>
                                	<td align="center" bgcolor="#deb887"><b>PDF</b></td>
                                	<td align="center" bgcolor="#deb887"><b>Brosur</b></td>
                                	<td align="center" bgcolor="#deb887"><b>Total</b></td>
                                	<td align="center" bgcolor="#eeb333"><b>Berbayar</b></td>
                                </tr>
                                <?php
								
								if ($gtotal == true){
									$condv = "WHERE te.loading_id in (".str_replace('|',',', $m_loading_id).") $pk_where";
								}else{
									$condv = "WHERE te.loading_id = '$m_loading_id' $pk_where";
								}
								
								$i = 0;
								$limit = 500;
								if(isset($_REQUEST['offset']))	$offset = $_REQUEST['offset']; else $offset = 0;
								
								$sql_datas = "SELECT
									 te.antrian_id, te.nomor_rekening, te.email, te.date_email_send, te.date_email_callback, te.ket_error,
									 d.nama, d.size_pdf, te.nama_customer, 
									 ml.loading_file,
									 SUM(maf.ukuran)AS total_attach
									FROM tr_email te
									LEFT JOIN $tabeldetail d ON te.loading_id = d.m_loading_id and te.nomor_rekening = d.nomor_rekening
									LEFT JOIN m_loading ml ON te.loading_id = ml.m_loading_id
									LEFT JOIN antrian_email_attach_file aeaf ON te.antrian_id = aeaf.antrian_id
									LEFT JOIN m_attach_file maf ON aeaf.m_attach_file_id::integer = maf.m_attach_file_id::integer
									$condv AND te.status_sample is not true
									AND te.ket_error IS NOT NULL 
									GROUP BY te.antrian_id, te.nomor_rekening, te.email, te.date_email_send,
									 d.nama, d.size_pdf, te.date_email_callback, te.ket_error, te.nama_customer, 
									 ml.loading_file
									ORDER BY te.date_email_send";

								$sql_data .= $sql_datas . " LIMIT $limit OFFSET $offset";
								#echo $sql_datas;
								if($_REQUEST['xls']=='y'){
								$qry_data = pg_query($sql_datas) or die('ERROR select data: '.$sql_datas);
								} else {
								$qry_data = pg_query($sql_data) or die('ERROR select data: '.$sql_data);
								}
								$qry_datas = pg_query($sql_datas) or die('ERROR select data: '.$sql_datas);
								$jumlah_data = pg_num_rows($qry_datas);
								$i = $offset;
								
								while($row_data = pg_fetch_array($qry_data)){
									$i++;
									$antrian_id = $row_data['antrian_id'];
									$total_size_estat = floor($row_data['size_pdf']);
									$total_size_attachment = floor($row_data['total_attach']/1024);
									$total_semua_attachment = $total_size_estat+$total_size_attachment;
									$sizeberbayar = ceil($total_semua_attachment/$SIZE_BERBAYAR);
								?>
                                <tr height="30">
                                	<td align="center"><?php echo $i?></td>
									<td align="left">&nbsp;<?php echo $row_data['loading_file'];?></td>
									<td align="left">&nbsp;<?php echo $row_data['nomor_rekening']?></td>
                                	<td align="left"><?php echo $row_data['nama']?></td>
                                	<td align="left"><?php echo $row_data['email']?></td>
                                	<td align="left"><?php echo date("d-m-Y H:i:s", strtotime($row_data['date_email_send']))?></td>
                                	<td align="left"><?php echo date("d-m-Y H:i:s", strtotime($row_data['date_email_callback']))?></td>
                                	<td align="left"><?php echo $row_data['ket_error']?></td>
                                	<td align="left"><?php echo $row_data['nama_customer']?></td>
									<td bgcolor="#f5deb3" align="right"><b><?php echo $total_size_estat;?></b></td>
									<td bgcolor="#f5deb3" align="right"><b><?php echo $total_size_attachment;?></b></td>
									<td bgcolor="#f5deb3" align="right"><b><?php echo $total_semua_attachment;?></b></td>
									<td bgcolor="#eeb333" align="right"><b><?php echo $sizeberbayar;?></b></td>
                                    <td align="center"><a onClick="view_template('<?php echo $antrian_id?>','<?php echo $blth;?>','<?php echo $flagtrans;?>')"><img title="view template" src="../images/icons/icon_view.gif" /></a></td>
                                </tr>
                                <?php
									}
								?>
                            </table>
                        </td>
                    </tr>
					<tr>
					<td>
					<div align="center">
				 <?php
                      if($_REQUEST['xls']<>'y'){  
					  echo pagings($offset,$jumlah_data,$limit,'div_paging','script_summary_detail_rev.php','act=show_email_gagal&blth='.$blth.'&pr='.$flagtrans.'&m_loading_id='.$m_loading_id.'&tipe='.$tipe);
					  }
                    ?>
					</div>
					</td>
					</tr>
                </table>
            </body>
        </html>
        <?php
	}
	
	
	
	//** REPORT DENGAN NOMOR REKENING TANPA SPASI **//
	//** 18 Des 2013 **//
	
		function export_excel_show_email_gagal($blth, $flagtrans, $cycle, $per_send, $curloc, $locateFile) {
		
		$m_loading_id = $_REQUEST['m_loading_id'];
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['pr'];
		
		$m_loading_id_ck = strpos($m_loading_id, '|');
		if ($m_loading_id_ck !== false){
			$gtotal = true;
		}else{
			$gtotal = false;
		}
		
		$blth_balik = substr($blth,-4).substr($blth,0,2);
		
			$BLTH_BARU = BLTH_BARU;
			if($blth_balik>$BLTH_BARU){
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
			
		$per_send = explode('|', $_REQUEST['periode_kirim']);
		$sent_from = $per_send[0];
		$sent_to = $per_send[1];
		if (!empty($sent_from)){
			$pk_where = "AND te.date_email_send BETWEEN '".$sent_from."' AND '".$sent_to."'";
		}else{
			$pk_where = "";
		}

		if ($gtotal == true){
			$condv = "WHERE te.loading_id in (".str_replace('|',',', $m_loading_id).") $pk_where";
		}else{
			$condv = "WHERE te.loading_id = '$m_loading_id' $pk_where";
		}
								
		
		//include $curloc.'PHPExcel.php';
		//include $curloc.'PHPExcel/Writer/Excel5.php';
		// Create new PHPExcel object
		
		$sql_datas = "SELECT
									 te.antrian_id, te.nomor_rekening, te.email, te.date_email_send, te.date_email_callback, te.ket_error,
									 d.nama, d.size_pdf, te.nama_customer, 
									 ml.loading_file,
									 SUM(maf.ukuran)AS total_attach
									FROM tr_email te
									LEFT JOIN $tabeldetail d ON te.loading_id = d.m_loading_id and te.nomor_rekening = d.nomor_rekening
									LEFT JOIN m_loading ml ON te.loading_id = ml.m_loading_id
									LEFT JOIN antrian_email_attach_file aeaf ON te.antrian_id = aeaf.antrian_id
									LEFT JOIN m_attach_file maf ON aeaf.m_attach_file_id::integer = maf.m_attach_file_id::integer
									$condv AND te.status_sample is not true
									AND te.ket_error IS NOT NULL 
									GROUP BY te.antrian_id, te.nomor_rekening, te.email, te.date_email_send,
									 d.nama, d.size_pdf, te.date_email_callback, te.ket_error,te.nama_customer, 
									 ml.loading_file
									ORDER BY te.date_email_send";
		$qry_datas = pg_query($sql_datas) or die('ERROR select data: '.$sql_datas);
		
		//
		$qry_sum_datas = pg_num_rows($qry_datas);
		
		if($qry_sum_datas >= 10000){
			$nomor = 1;
			
			$dest = '../tmp/';
			$name_file = 'GAGAL_'.$nama_file_loading. '.csv';
			
			if(file_exists($dest.$name_file)){ // check if file already
				unlink($dest.$name_file);
			}
			
			$handle = fopen($dest.$name_file, 'a+');
			$nl_dos	 = "\r\n";
			
			$header = "No,";
			$header .= "Nama File,";
			$header .= "Nomor Rekening,";
			$header .= "Nama,";
			$header .= "Email,";
			$header .= "Tanggal Kirim,";
			$header .= "Tanggal Feedback,";
			$header .= "Pesan Feedback,";
			$header .= "Klasifikasi,";
			$header .= "PDF Size,";
			$header .= "Attachment Size/Brosur,";
			$header .= "Total Size,";
			$header .= "Berbayar,";
			fwrite($handle, $header. $nl_dos);
			
			while($row_data = pg_fetch_array($qry_datas)){
				
			$total_size_estat = floor($row_data['size_pdf']);
			$total_size_attachment = floor($row_data['total_attach']/1024);
			$total_semua_attachment = $total_size_estat+$total_size_attachment;
			$sizeberbayar = ceil($total_semua_attachment/300);
			
			$document_key = $row_data['document_key'];
			$loading_file = $row_data['loading_file'];			
			$delivery_status = $row_data['delivery_status'];
			$policy_number = $row_data['nomor_rekening'];
			$nama = $row_data['nama'];
			
			$tanggal_kirim = date("d-m-Y H:i:s", strtotime($row_data['date_email_send']));
			$tanggal_callback = date("d-m-Y H:i:s", strtotime($row_data['date_email_callback']));
			$ket_error = $row_data['ket_error'] ;
			$klasifikasi_error = $row_data['nama_customer'] ;
			
			if($delivery_status=='Q') {
			$queue_date = $row_data['queue_date']; 
			$send_schedule = $row_data['send_schedule'];
			$send_date = "-";
			} else {
			$queue_date = "-";
			$send_schedule = "-";
			$send_date = $row_data['send_date'];
			}
										
			$email = $row_data['email'];
			$error_message = $row_data['error_message'];
			
					$data  = str_replace(',', ' ',$nomor) . ',';
					$data .= str_replace(',', ' ',$loading_file) . ','; 
					$data .= str_replace(',', ' ',$policy_number.' ') . ','; 
					$data .= str_replace(',', ' ',$nama) . ','; 
					$data .= str_replace(',', ' ',$email) . ','; 
					$data .= str_replace(',', ' ',$tanggal_kirim). ',';
					$data .= str_replace(',', ' ',$tanggal_callback) . ','; 
					$data .= str_replace(',', ' ',$ket_error) . ',';
					$data .= str_replace(',', ' ',$klasifikasi_error) . ',';
					$data .= str_replace(',', ' ',$total_size_estat) . ',';
					$data .= str_replace(',', ' ',$total_size_attachment) . ','; 
					$data .= str_replace(',', ' ',$total_semua_attachment) . ',';
					$data .= str_replace(',', ' ',$sizeberbayar) . ',';
					fwrite($handle, $data. $nl_dos); // write this variable in csv file
				$nomor+=1;
			}
			
			fclose($handle);
			
			header('Content-type: text/plain');                             
			//header('application/vnd.ms-excel');                             
			// What file will be named after downloading                                  
			//header('Content-Disposition: attachment; filename="list_email_sukses_'.$m_loading_id.'_'.$loading_file. '.csv"');
			header('Content-Disposition: attachment; filename="'.$name_file.'"');
			// File to download                                
			readfile($dest.$name_file); 
			//unlink($dest.$name_file);
			exit;
		}
		//
		
		else
		{
		
		
		
		$objPHPExcel = new PHPExcel();
		$F=$objPHPExcel->getActiveSheet();
		
		$styleArray = array(
			'font' => array(
				'name'         => 'Arial',
				'bold'         => true,
				'italic'    => false,
				'size'        => 12
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap'       => true
			)
		);
		
		$F->mergeCellsByColumnAndRow(0,1,6,1)->setCellValue('A1', 'LIST EMAIL GAGAL TERKIRIM')->getStyle('A1')->applyFromArray($styleArray);
		
		
		$Line=3;
		
		$F->getColumnDimension('A')->setWidth(5);
		$F->getColumnDimension('B')->setWidth(32);
		$F->getColumnDimension('C')->setWidth(17);
		$F->getColumnDimension('D')->setWidth(30);
		$F->getColumnDimension('E')->setWidth(30);
		$F->getColumnDimension('F')->setWidth(22);
		$F->getColumnDimension('G')->setWidth(22);
		$F->getColumnDimension('H')->setWidth(125);
		$F->getColumnDimension('I')->setWidth(70);
		$F->getColumnDimension('J')->setWidth(25);
		$F->getColumnDimension('K')->setWidth(25);
		$F->getColumnDimension('L')->setWidth(25);
		$F->getColumnDimension('M')->setWidth(25);
		
		
		$styleArray = array(
			'font' => array(
				'name'         => 'Arial',
				'bold'         => true,
				'italic'    => false,
				'size'        => 12
			),
			'borders' => array(
			'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
			'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
			'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
			'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
		),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap'       => true
			)
		);
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$Line.':M'.$Line.'')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('ffdfdfdf');

		$F->setCellValueExplicit('A'.$Line, "No", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('A'.$Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('B'.$Line, "Nama File", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('B'.$Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('C'.$Line, "Nomor Rekening", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('C'.$Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('D'.$Line, "Nama", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('D'.$Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('E'.$Line, "Email", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('E'.$Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('F'.$Line, "Tanggal Kirim", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('F'.$Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('G'.$Line, "Tanggal Feedback", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('G'.$Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('H'.$Line, "Pesan Feedback", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('H'.$Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('I'.$Line, "Klasifikasi", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('I'.$Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('J'.$Line, "PDF Size", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('J'.$Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('K'.$Line, "Attachment Size/Brosur", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('K'.$Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('L'.$Line, "Total Size", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('L'.$Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('M'.$Line, "Berbayar", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('M'.$Line)->applyFromArray($styleArray);
		
		$Line=4;
		$nomor = 1;
		while($row_data = pg_fetch_array($qry_datas)){//extract each record
		$total_size_estat = floor($row_data['size_pdf']);
		$total_size_attachment = floor($row_data['total_attach']/1024);
		$total_semua_attachment = $total_size_estat+$total_size_attachment;
		$sizeberbayar = ceil($total_semua_attachment/300);
		
		$styleArray = array(
		'borders' => array(
			'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
			'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
			'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
			'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
		)
		);
		
		$F->getStyle('A'.$Line.'')->applyFromArray($styleArray);
		$F->getStyle('B'.$Line.'')->applyFromArray($styleArray);
		$F->getStyle('C'.$Line.'')->applyFromArray($styleArray);
		$F->getStyle('D'.$Line.'')->applyFromArray($styleArray);
		$F->getStyle('E'.$Line.'')->applyFromArray($styleArray);
		$F->getStyle('F'.$Line.'')->applyFromArray($styleArray);
		$F->getStyle('G'.$Line.'')->applyFromArray($styleArray);
		$F->getStyle('H'.$Line.'')->applyFromArray($styleArray);
		$F->getStyle('I'.$Line.'')->applyFromArray($styleArray);
		$F->getStyle('J'.$Line.'')->applyFromArray($styleArray);
		$F->getStyle('K'.$Line.'')->applyFromArray($styleArray);
		$F->getStyle('L'.$Line.'')->applyFromArray($styleArray);
		$F->getStyle('M'.$Line.'')->applyFromArray($styleArray);
		
		
		$loading_file = $row_data['loading_file'];
		
			$F->setCellValueExplicit('A'.$Line, $nomor, PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('B'.$Line, $row_data['loading_file'], PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('C'.$Line, $row_data['nomor_rekening'], PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('D'.$Line, $row_data['nama'], PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('E'.$Line, $row_data['email'], PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('F'.$Line, date("d-m-Y H:i:s", strtotime($row_data['date_email_send'])), PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('G'.$Line, date("d-m-Y H:i:s", strtotime($row_data['date_email_callback'])), PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('H'.$Line, $row_data['ket_error'], PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('I'.$Line, $row_data['nama_customer'], PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('J'.$Line, $total_size_estat, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('K'.$Line, $total_size_attachment, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('L'.$Line, $total_semua_attachment, PHPExcel_Cell_DataType::TYPE_NUMERIC)
				->setCellValueExplicit('M'.$Line, $sizeberbayar, PHPExcel_Cell_DataType::TYPE_NUMERIC);
				++$Line;
			$nomor+=1;
		}
		// Redirect output to a clients web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="list_email_gagal_'.$m_loading_id.'_'.$loading_file.'.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
		}
	}
	
	
	
	
	
	
	function show_email_terbaca($header_utama){
		$m_loading_id = $_REQUEST['m_loading_id'];
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['pr'];
		
		$blth_balik = substr($blth,-4).substr($blth,0,2);
		
			$BLTH_BARU = BLTH_BARU;
			if($blth_balik>$BLTH_BARU){
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
			
			
		$source = $_REQUEST['source'];
		$per_send = explode('|', $_REQUEST['periode_kirim']);
		$sent_from = $per_send[0];
		$sent_to = $per_send[1];
		if (!empty($sent_from)){
			$pk_where = "AND date_email_send BETWEEN '".$sent_from."' AND '".$sent_to."'";
		}else{
			$pk_where = "";
		}
		
		$sql = "SELECT loading_file FROM m_loading WHERE m_loading_id = ".$m_loading_id;
		$qry = pg_query($sql) or die('ERROR query: '.$sql);
		$row = pg_fetch_assoc($qry);
		$nama_file = $row['loading_file'];
		
		$per_send = explode('|', $_REQUEST['periode_kirim']);
		$sent_from = $per_send[0];
		$sent_to = $per_send[1];
		
		$m_loading_id_ck = strpos($m_loading_id, '|');
		if ($m_loading_id_ck !== false){
			$gtotal = true;
		}else{
			$gtotal = false;
		}
		
		if($source=='1'){
			$ws = " AND a.read_method='img_email'";
			$ket_ws = " (IMG)";
		} else {
			//$ws = " AND (a.read_method is null OR a.read_method='outlook')";
			$ws = " ";
			$ket_ws = " ";
		}
		?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>LIST EMAIL SUKSES TERBACA <?php echo $ket_ws;?></title>
            </head>
            <? require_once("../include/script.php"); ?>
            <body>
            	<table align="center" border="0" width="1000">
                	<tr>
                    	<td>
                        	<table align="center" border="0" cellpadding="2" cellspacing="0" width="100%">
                            	<tr height="30">
                                	<td class="title" colspan="5" align="center" valign="middle">
                                    	LIST EMAIL SUKSES TERBACA <?php echo $ket_ws;?>
                                    </td>
                                </tr>
                                <tr height="20">
                                	<td colspan="2"><b><u><?=$header_utama?></u></b></td>
                                </tr>
								<?php
								if (!empty($sent_from)){
									$label = ''.$sent_from.' s/d '.$sent_to.'';
								?>
									<tr height="30">
										<td><b>Periode: <?=$label?></b></td>
										<td align="right"><a onClick="window.open('script_summary_detail_rev.php?act=show_email_terbaca<?php if (!empty($sent_from)){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>&xls=y')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a></td>
									</tr>
								<?php
								}else{
									if ($gtotal == true){$label='Periode: '.$blth;}else{$label='Nama File: '.$nama_file;}
								?>
									<tr height="30">
										<td><b><?=$label?></b></td>
										<td align="right"><a onClick="window.open('script_summary_detail_rev.php?act=show_email_terbaca&blth=<?php echo $blth?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>&xls=y')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a></td>
									</tr>
								<?php
								}?>
                            </table>
                        </td>
                    </tr>
                    <tr>
                    	<td>
                        	<table align="center" border="1" cellpadding="2" cellspacing="0" width="100%" style="border-collapse:collapse">
                            	<tr height="30" bgcolor="#FF0000" style="color:#FFF;">
                                	<td width="4%" align="center"><b>No</b></td>
									<?php
									if ($gtotal == true){
										echo '<td align="center"><b>Cycle</b></td>';
									}
									?>
                                	<td align="center"><b>Nomor Kartu</b></td>
                                	<td align="center"><b>Nama</b></td>
                                	<td align="center"><b>Alamat Email</b></td>
                                	<td align="center"><b>Tanggal Kirim</b></td>
									<td align="center"><b>Tanggal Baca</b></td>
									<td align="center"><b>Ket</b></td>
									<td align="center"><b>View Template</b></td>
                                </tr>
                                <?php
								if ($gtotal == true){
									$condv = " AND loading_id in (".str_replace('|',',', $m_loading_id).")";
									$condv2 = " b.m_loading_id  = a.loading_id ";
								}else{
									$condv = " AND loading_id = $m_loading_id";
									$condv2 = " b.m_loading_id = a.loading_id";
								}
								

								
								$i = 0;
								$limit = 500;
								if(isset($_REQUEST['offset']))	$offset = $_REQUEST['offset']; else $offset = 0;
								
								$sql_datas = "SELECT a.tr_email_id, a.nomor_rekening, a.email, a.date_email_send, a.tgl_read, a.antrian_id, a.body_email_read, a.loading_id,
											(SELECT b.nama FROM $tabeldetail b WHERE ".$condv2." AND b.nomor_rekening = a.nomor_rekening)AS nama,
											(SELECT b.nama_file FROM $tabeldetail b WHERE ".$condv2." AND b.nomor_rekening = a.nomor_rekening)AS nama_file
											FROM tr_email a
											WHERE a.tgl_read IS NOT NULL $ws
											AND a.status_sample IS NOT TRUE $pk_where
											".$condv."
											GROUP BY a.tr_email_id, a.nomor_rekening, a.email, a.date_email_send, a.tgl_read, a.antrian_id, a.body_email_read, a.loading_id";
								$sql_data .= $sql_datas . " LIMIT $limit OFFSET $offset";
								#echo $sql_data;
								if($_REQUEST['xls']=='y'){
								$qry_data = pg_query($sql_datas) or die('ERROR select data: '.$sql_datas);
								} else {
								$qry_data = pg_query($sql_data) or die('ERROR select data: '.$sql_data);
								}
								$qry_datas = pg_query($sql_datas) or die('ERROR select data: '.$sql_datas);
								$jumlah_data = pg_num_rows($qry_datas);
								$i = $offset;
								while($row_data = pg_fetch_array($qry_data)){
									$i++;
									$antrian_id = $row_data['antrian_id'];
									if($_REQUEST['xls']=='y'){
									$norek = $row_data['nomor_rekening'];
									}else {
									$norek = $row_data['nomor_rekening'];
									}
									
									?>
                                <tr height="30">
                                	<td align="center"><?php echo $i?></td>
									<?php
									if ($gtotal == true){
										echo '<td align="left" style="font-size:8pt;">'.$row_data['nama_file'].'</td>';
									}
									?>
                                	<td align="center"><?php print_r($norek);?></td>
                                	<td align="left"><?php echo $row_data['nama']?></td>
                                	<td align="left"><?php echo $row_data['email']?></td>
                                	<td align="center"><?php echo date("d-m-Y H:i",strtotime($row_data['date_email_send']))?></td>
									<td align="center"><?php echo date("d-m-Y H:i",strtotime($row_data['tgl_read']))?></td>
									<td align="left">
                                        <?php
										if($_REQUEST['xls']=='y'){
											echo $row_data['body_email_read'];
										}else{
											?>
											<a onClick="view_bodymailterbaca('<?php echo $row_data['tr_email_id']?>')">detail</a>
											<?php
										}
										?>
									</td>
									<td align="center">
									<?php	if($_REQUEST['xls']<>'y'){	?>
									<a onClick="view_template('<?=$antrian_id?>','<?php echo $blth;?>','<?php echo $flagtrans;?>')"><img title="view template" src="../images/icons/icon_view.gif" /></a>
									<?php } ?>
									</td>
                                </tr>
                                <?php
									}
								?>
                            </table>
                        </td>
                    </tr>
					<tr>
					<td>
					<div align="center">
				 <?php
                      if($_REQUEST['xls']<>'y'){  
					  echo pagings($offset,$jumlah_data,$limit,'div_paging','script_summary_detail_rev.php','act=show_email_terbaca&blth='.$blth.'&source='.$source.'&pr='.$flagtrans.'&m_loading_id='.$m_loading_id.'&tipe='.$tipe);
					  }
                    ?>
					</div>
					</td>
					</tr>
                </table>
            </body>
        </html>
        <?php
	}
	
	function show_email_others($header_utama){
		$m_loading_id = $_REQUEST['m_loading_id'];
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['pr'];
		
		$blth_balik = substr($blth,-4).substr($blth,0,2);
		
			$BLTH_BARU = BLTH_BARU;
			if($blth_balik>$BLTH_BARU){
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
			
			
		$per_send = explode('|', $_REQUEST['periode_kirim']);
		$sent_from = $per_send[0];
		$sent_to = $per_send[1];
		if (!empty($sent_from)){
			$pk_where = "AND date_email_send BETWEEN '".$sent_from."' AND '".$sent_to."'";
		}else{
			$pk_where = "";
		}
		
		$sql = "SELECT loading_file FROM m_loading WHERE m_loading_id = ".$m_loading_id;
		$qry = pg_query($sql) or die('ERROR query: '.$sql);
		$row = pg_fetch_assoc($qry);
		$nama_file = $row['loading_file'];
		
		$per_send = explode('|', $_REQUEST['periode_kirim']);
		$sent_from = $per_send[0];
		$sent_to = $per_send[1];
		
		$m_loading_id_ck = strpos($m_loading_id, '|');
		if ($m_loading_id_ck !== false){
			$gtotal = true;
		}else{
			$gtotal = false;
		}
		?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>LIST EMAIL (STATUS : OTHERS)</title>
            </head>
            <? require_once("../include/script.php"); ?>
            <body>
            	<table align="center" border="0" width="1000">
                	<tr>
                    	<td>
                        	<table align="center" border="0" cellpadding="2" cellspacing="0" width="100%">
                            	<tr height="30">
                                	<td class="title" colspan="5" align="center" valign="middle">
                                    	LIST EMAIL OTHERS
                                    </td>
                                </tr>
                                <tr height="20">
                                	<td colspan="2"><b><u><?=$header_utama?></u></b></td>
                                </tr>
								<?php
								if (!empty($sent_from)){
									$label = ''.$sent_from.' s/d '.$sent_to.'';
								?>
									<tr height="30">
										<td><b>Periode: <?=$label?></b></td>
										<td align="right"><a onClick="window.open('script_summary_detail_rev.php?act=show_email_terbaca<?php if (!empty($sent_from)){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>&xls=y')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a></td>
									</tr>
								<?php
								}else{
									if ($gtotal == true){$label='Periode: '.$blth;}else{$label='Nama File: '.$nama_file;}
								?>
									<tr height="30">
										<td><b><?=$label?></b></td>
										<td align="right"><a onClick="window.open('script_summary_detail_rev.php?act=show_email_others&blth=<?php echo $blth?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>&xls=y')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a></td>
									</tr>
								<?php
								}?>
                            </table>
                        </td>
                    </tr>
                    <tr>
                    	<td>
                        	<table align="center" border="1" cellpadding="2" cellspacing="0" width="100%" style="border-collapse:collapse">
                            	<tr height="30" bgcolor="#FF0000" style="color:#FFFFFF">
                                	<td width="4%" align="center"><b>No</b></td>
									<?php
									if ($gtotal == true){
										echo '<td align="center"><b>Cycle</b></td>';
									}
									?>
                                	<td align="center"><b>Nomor Kartu</b></td>
                                	<td align="center"><b>Nama</b></td>
                                	<td align="center"><b>Alamat Email</b></td>
                                	<td align="center"><b>Tanggal Kirim</b></td>
									<td align="center"><b>Ket</b></td>
									<td align="center"><b>View Template</b></td>
                                </tr>
                                <?php
								if ($gtotal == true){
									$condv = " AND a.m_loading_id in (".str_replace('|',',', $m_loading_id).") $pk_where";
									$condv2 = " d.m_loading_id in (".str_replace('|',',', $m_loading_id).") $pk_where";
								}else{
									$condv = " AND a.m_loading_id = $m_loading_id $pk_where";
									$condv2 = " d.m_loading_id = $m_loading_id $pk_where";
								}
								$i = 0;
								$limit = 500;
								if(isset($_REQUEST['offset']))	$offset = $_REQUEST['offset']; else $offset = 0;
								
								$sql_datas = "SELECT DISTINCT(a.tr_email_id), a.email, a.body_bounce, a.m_loading_id,b.date_email_send,b.nomor_rekening, b.antrian_id,
								(SELECT d.nama FROM $tabeldetail d WHERE $condv2 AND d.nomor_rekening = b.nomor_rekening)AS nama 
								FROM bounce_inbox a 
								LEFT JOIN tr_email b ON b.tr_email_id::integer=a.tr_email_id::integer WHERE a.blth='$blth' 
								$condv 
								AND a.status='others' AND b.status_sample is not true";
								
								#echo $sql_datas;
								$sql_data .= $sql_datas . " LIMIT $limit OFFSET $offset";
								if($_REQUEST['xls']=='y'){
								$qry_data = pg_query($sql_datas) or die('ERROR select data: '.$sql_datas);
								} else {
								$qry_data = pg_query($sql_data) or die('ERROR select data: '.$sql_data);
								}
								$qry_datas = pg_query($sql_datas) or die('ERROR select data: '.$sql_datas);
								$jumlah_data = pg_num_rows($qry_datas);
								$i = $offset;
								while($row_data = pg_fetch_array($qry_data)){
									$i++;
									$antrian_id = $row_data['antrian_id'];
									if($_REQUEST['xls']=='y'){
									$norek = $row_data['nomor_rekening'];
									}else {
									$norek = $row_data['nomor_rekening'];
									}
									
									?>
                                <tr height="30">
                                	<td align="center"><?php echo $i?></td>
									<?php
									if ($gtotal == true){
										echo '<td align="left" style="font-size:8pt;">'.$row_data['nama_file'].'</td>';
									}
									?>
                                	<td align="center"><?php print_r($norek);?></td>
                                	<td align="left"><?php echo $row_data['nama']?></td>
                                	<td align="left"><?php echo $row_data['email']?></td>
                                	<td align="center"><?php echo date("d-m-Y H:i",strtotime($row_data['date_email_send']))?></td>
									<td align="left">
                                        <?php
										if($_REQUEST['xls']=='y'){
											echo htmlentities($row_data['body_bounce']);
										}else{
											?>
											<a onClick="view_bodymailothers('<?php echo $row_data['tr_email_id']?>')">detail</a>
											<?php
										}
										?>
									</td>
									<td align="center">
									<?php	if($_REQUEST['xls']<>'y'){	?>
									<a onClick="view_template('<?php echo $antrian_id?>','<?php echo $blth;?>','<?php echo $flagtrans;?>')"><img title="view template" src="../images/icons/icon_view.gif" /></a>
									<?php } ?>
									</td>
                                </tr>
                                <?php
									}
								?>
                            </table>
                        </td>
                    </tr>
					<tr>
					<td>
					<div align="center">
				 <?php
                      if($_REQUEST['xls']<>'y'){  
					  echo pagings($offset,$jumlah_data,$limit,'div_paging','script_summary_detail_rev.php','act=show_email_others&blth='.$blth.'&pr='.$flagtrans.'&m_loading_id='.$m_loading_id.'&tipe='.$tipe);
					  }
                    ?>
					</div>
					</td>
					</tr>
                </table>
            </body>
        </html>
        <?php
	}
	
	function show_email_sample_sukses($header_utama){
		$m_loading_id = $_REQUEST['m_loading_id'];
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['pr'];
		
		$blth_balik = substr($blth,-4).substr($blth,0,2);
		
			$BLTH_BARU = BLTH_BARU;
			if($blth_balik>$BLTH_BARU){
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
			
			
		$per_send = explode('|', $_REQUEST['periode_kirim']);
		$sent_from = $per_send[0];
		$sent_to = $per_send[1];
		if (!empty($sent_from)){
			$pk_where = "AND date_email_send BETWEEN '".$sent_from."' AND '".$sent_to."'";
		}else{
			$pk_where = "";
		}
		
		$sql = "SELECT loading_file FROM m_loading WHERE m_loading_id = ".$m_loading_id;
		$qry = pg_query($sql) or die('ERROR query: '.$sql);
		$row = pg_fetch_assoc($qry);
		$nama_file = $row['loading_file'];
		
		$per_send = explode('|', $_REQUEST['periode_kirim']);
		$sent_from = $per_send[0];
		$sent_to = $per_send[1];
		
		$m_loading_id_ck = strpos($m_loading_id, '|');
		if ($m_loading_id_ck !== false){
			$gtotal = true;
		}else{
			$gtotal = false;
		}
		?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>LIST EMAIL SAMPLE SUKSES</title>
            </head>
            <? require_once("../include/script.php"); ?>
            <body>
            	<table align="center" border="0" width="1000">
                	<tr>
                    	<td>
                        	<table align="center" border="0" cellpadding="2" cellspacing="0" width="100%">
                            	<tr height="30">
                                	<td class="title" colspan="5" align="center" valign="middle">
                                    	LIST EMAIL SAMPLE SUKSES
                                    </td>
                                </tr>
                                <tr height="20">
                                	<td colspan="2"><b><u><?=$header_utama?></u></b></td>
                                </tr>
								<?php
								if (!empty($sent_from)){
									$label = ''.$sent_from.' s/d '.$sent_to.'';
								?>
									<tr height="30">
										<td><b>Periode: <?=$label?></b></td>
										<td align="right"><a onClick="window.open('script_summary_detail_rev.php?act=show_email_sample_sukses<?php if (!empty($sent_from)){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>&xls=y')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a></td>
									</tr>
								<?php
								}else{
									if ($gtotal == true){$label='Periode: '.$blth;}else{$label='Nama File: '.$nama_file;}
								?>
									<tr height="30">
										<td><b><?=$label?></b></td>
										<td align="right"><a onClick="window.open('script_summary_detail_rev.php?act=show_email_sample_sukses&blth=<?php echo $blth?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>&xls=y')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a></td>
									</tr>
								<?php
								}?>
                            </table>
                        </td>
                    </tr>
                    <tr>
                    	<td>
                        	<table align="center" border="1" cellpadding="2" cellspacing="0" width="100%">
                            	<tr height="30" bgcolor="#FF0000" style="color:#FFF">
                                	<td width="4%" align="center"><b>No</b></td>
									<?php
									if ($gtotal == true){
										echo '<td align="center"><b>Cycle</b></td>';
									}
									?>
                                	<td align="center"><b>Nomor Kartu</b></td>
                                	<td align="center"><b>Nama</b></td>
                                	<td align="center"><b>Alamat Email</b></td>
                                	<td align="center"><b>Tanggal Kirim</b></td>
                                	<td align="center"><b>View Template</b></td>
                                </tr>
                                <?php
								if ($gtotal == true){
									$condv = " AND a.loading_id in (".str_replace('|',',', $m_loading_id).")";
									$condv_sub = "m_loading_id in (".str_replace('|',',', $m_loading_id).")";
								}else{
									$condv = " AND a.loading_id = $m_loading_id";
									$condv_sub = "m_loading_id = $m_loading_id";
								}
								$i = 0;
								$limit = 500;
								if(isset($_REQUEST['offset']))	$offset = $_REQUEST['offset']; else $offset = 0;
								
								$sql_datas = "SELECT a.nomor_rekening, a.email, a.date_email_send, a.antrian_id, a.ket_error, 
								(SELECT nama FROM $tabeldetail WHERE nomor_rekening=a.nomor_rekening LIMIT 1)AS nama,
								(SELECT loading_file FROM m_loading WHERE m_loading_id=a.loading_id LIMIT 1)AS loading_file
											FROM tr_email a 
											WHERE a.email_callback IS NULL
											AND a.status_sample IS TRUE 
											AND a.email_sukses = TRUE
											".$condv."
											GROUP BY a.nomor_rekening, a.email, a.date_email_send, a.antrian_id, a.ket_error, nama, loading_file
											ORDER BY a.antrian_id ";
								//echo $sql_data;
								$sql_data .= $sql_datas . " LIMIT $limit OFFSET $offset";
								if($_REQUEST['xls']=='y'){
								$qry_data = pg_query($sql_datas) or die('ERROR select data: '.$sql_datas);
								} else {
								$qry_data = pg_query($sql_data) or die('ERROR select data: '.$sql_data);
								}
								$qry_datas = pg_query($sql_datas) or die('ERROR select data: '.$sql_datas);
								$jumlah_data = pg_num_rows($qry_datas);
								$i = $offset;
								
								while($row_data = pg_fetch_array($qry_data)){
									$i++;
									$antrian_id = $row_data['antrian_id'];
								?>
                                <tr height="30">
                                	<td align="center"><?php echo $i?></td>
									<?php
									if ($gtotal == true){
										echo '<td align="center">'.$row_data['nama_file_as'].'</td>';
									}
									?>
                                	<td align="center">&nbsp;<?php echo $row_data['nomor_rekening']?></td>
                                	<td align="center"><?php echo $row_data['nama']?></td>
                                	<td align="center"><?php echo $row_data['email']?></td>
                                	<td align="center"><?php echo $row_data['date_email_send']?></td>
                                    <td align="center"><a onClick="view_template('<?=$antrian_id?>','<?php echo $blth;?>','<?php echo $flagtrans;?>')"><img title="view template" src="../images/icons/icon_view.gif" /></a></td>
                                </tr>
                                <?php
									}
								?>
                            </table>
                        </td>
                    </tr>
					<tr>
					<td>
					<div align="center">
				 <?php
                      if($_REQUEST['xls']<>'y'){  
					  echo pagings($offset,$jumlah_data,$limit,'div_paging','script_summary_detail_rev.php','act=show_email_sample_sukses&pr='.$flagtrans.'&m_loading_id='.$m_loading_id.'&tipe='.$tipe);
					  }
                    ?>
					</div>
					</td>
					</tr>
                </table>
            </body>
        </html>
        <?php
	}
	

	
	function show_email_sample_gagal($header_utama){
		$m_loading_id = $_REQUEST['m_loading_id'];
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['pr'];
		
		$blth_balik = substr($blth,-4).substr($blth,0,2);
		
			$BLTH_BARU = BLTH_BARU;
			if($blth_balik>$BLTH_BARU){
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
			
			
		$per_send = explode('|', $_REQUEST['periode_kirim']);
		$sent_from = $per_send[0];
		$sent_to = $per_send[1];
		if (!empty($sent_from)){
			$pk_where = "AND date_email_send BETWEEN '".$sent_from."' AND '".$sent_to."'";
		}else{
			$pk_where = "";
		}
		
		$sql = "SELECT loading_file FROM m_loading WHERE m_loading_id = ".$m_loading_id;
		$qry = pg_query($sql) or die('ERROR query: '.$sql);
		$row = pg_fetch_assoc($qry);
		$nama_file = $row['loading_file'];
		
		$per_send = explode('|', $_REQUEST['periode_kirim']);
		$sent_from = $per_send[0];
		$sent_to = $per_send[1];
		
		$m_loading_id_ck = strpos($m_loading_id, '|');
		if ($m_loading_id_ck !== false){
			$gtotal = true;
		}else{
			$gtotal = false;
		}
		?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>LIST SAMPLE GAGAL TERKIRIM</title>
            </head>
            <? require_once("../include/script.php"); ?>
            <body>
            	<table align="center" border="0" width="1000">
                	<tr>
                    	<td>
                        	<table align="center" border="0" cellpadding="2" cellspacing="0" width="100%">
                            	<tr height="30">
                                	<td class="title" colspan="5" align="center" valign="middle">
                                    	LIST SAMPLE GAGAL TERKIRIM
                                    </td>
                                </tr>
                                <tr height="20">
                                	<td colspan="2"><b><u><?=$header_utama?></u></b></td>
                                </tr>
								<?php
								if (!empty($sent_from)){
									$label = ''.$sent_from.' s/d '.$sent_to.'';
								?>
									<tr height="30">
										<td><b>Periode: <?=$label?></b></td>
										<td align="right"><a onClick="window.open('script_summary_detail_rev.php?act=show_email_gagal<?php if (!empty($sent_from)){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>&xls=y')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a></td>
									</tr>
								<?php
								}else{
									if ($gtotal == true){$label='Periode: '.$blth;}else{$label='Nama File: '.$nama_file;}
								?>
									<tr height="30">
										<td><b><?=$label?></b></td>
										<td align="right"><a onClick="window.open('script_summary_detail_rev.php?act=show_email_gagal&blth=<?php echo $blth?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>&xls=y')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a></td>
									</tr>
								<?php
								}?>
                            </table>
                        </td>
                    </tr>
                    <tr>
                    	<td>
                        	<table align="center" border="1" cellpadding="2" cellspacing="0" width="100%">
                            	<tr height="30" bgcolor="#FF0000" style="color:#FFF">
                                	<td width="4%" align="center"><b>No</b></td>
									<?php
									if ($gtotal == true){
										echo '<td align="center"><b>Cycle</b></td>';
									}
									?>
									<?php
									
									if ($gtotal == true){
										$condv = " AND a.loading_id in (".str_replace('|',',', $m_loading_id).")";
										$condv2 = " ";
									}else{
										$condv = " AND a.loading_id = $m_loading_id";
										$condv2 = " b.m_loading_id = $m_loading_id AND";
									}
								
									?>
                                	<td align="center"><b>Nomor Kartu</b></td>
                                	<td align="center"><b>Nama</b></td>
                                	<td align="center"><b>Alamat Email</b></td>
                                	<td align="center"><b>Tgl Kirim</b></td>
                                	<td align="center"><b>Tgl Feedback</b></td>
                                	<td align="center"><b>Pesan Error</b></td>
                                	<!--<td align="center"><b>View Template</b></td>-->
                                </tr>
                                <?php
								if ($gtotal == true){
									$condv = " AND a.loading_id in (".str_replace('|',',', $m_loading_id).")";
									$condv2 = " b.m_loading_id in (".str_replace('|',',', $m_loading_id).")";
								}else{
									$condv = " AND a.loading_id = $m_loading_id";
									$condv2 = " b.m_loading_id = $m_loading_id";
								}
								
								$i = 0;
								$limit = 500;
								if(isset($_REQUEST['offset']))	$offset = $_REQUEST['offset']; else $offset = 0;
								
								$sql_datas = "SELECT a.nomor_rekening, a.email, a.date_email_send, a.date_email_callback, a.antrian_id,
								(SELECT nama FROM $tabeldetail WHERE nomor_rekening=a.nomor_rekening LIMIT 1)AS nama,
								(SELECT loading_file FROM m_loading WHERE m_loading_id=a.loading_id LIMIT 1)AS loading_file
											FROM tr_email a 
											WHERE a.email_callback IS NOT NULL 
											AND a.status_sample IS TRUE 
											".$condv."
											ORDER BY a.antrian_id ";
								$sql_data .= $sql_datas . " LIMIT $limit OFFSET $offset";
								if($_REQUEST['xls']=='y'){
								$qry_data = pg_query($sql_datas) or die('ERROR select data: '.$sql_datas);
								} else {
								$qry_data = pg_query($sql_data) or die('ERROR select data: '.$sql_data);
								}
								$qry_datas = pg_query($sql_datas) or die('ERROR select data: '.$sql_datas);
								$jumlah_data = pg_num_rows($qry_datas);
								$i = $offset;
								while($row_data = pg_fetch_array($qry_data)){
									$i++;
									$antrian_id = $row_data['antrian_id'];
								?>
                                <tr height="30">
                                	<td align="center"><?php echo $i?></td>
									<?php
									if ($gtotal == true){
										echo '<td align="left">&nbsp;'.$row_data['loading_file'].'</td>';
									}
									?>
									<td align="left"><?php echo '&nbsp;'.$row_data['nomor_rekening']?></td>
                                	<td align="left"><?php echo $row_data['nama']?></td>
                                	<td align="left"><?php echo $row_data['email']?></td>
                                	<td align="left"><?php echo date("d-m-Y H:i:s", strtotime($row_data['date_email_send']))?></td>
                                	<td align="left"><?php echo date("d-m-Y H:i:s", strtotime($row_data['date_email_callback']))?></td>
                                	<td align="left"><?php echo $row_data['ket_error']?></td>
                                    <!--<td align="center"><a onClick="view_template(<?=$antrian_id?>)"><img title="view template" src="../images/icons/icon_view.gif" /></a></td>-->
                                </tr>
                                <?php
									}
								?>
                            </table>
                        </td>
                    </tr>
					<tr>
					<td>
					<div align="center">
				 <?php
                      if($_REQUEST['xls']<>'y'){  
					  echo pagings($offset,$jumlah_data,$limit,'div_paging','script_summary_detail_rev.php','act=show_email_sample_gagal&pr='.$flagtrans.'&m_loading_id='.$m_loading_id.'&tipe='.$tipe);
					  }
                    ?>
					</div>
					</td>
					</tr>
                </table>
            </body>
        </html>
        <?php
	}
	
	function view_template(){
		$antrian_id = $_REQUEST['antrian_id'];
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['flagtrans'];
		?>
        <iframe id="frame_view" src="script_summary_detail_rev.php?act=load_template&blth=<?php echo $blth;?>&flagtrans=<?php echo $flagtrans;?>&antrian_id=<?=$antrian_id?>" height="670" width="900" frameborder="0" style="background-color:#fff">
        </iframe>
        <?
	}
	
	function load_template(){
		$antrian_id = $_REQUEST['antrian_id'];
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['flagtrans'];
		
		$blth_balik = substr($blth,-4).substr($blth,0,2);
		
			$BLTH_BARU = BLTH_BARU;
			if($blth_balik>$BLTH_BARU){
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
		
		$sql_attach = "SELECT a.m_attach_file_id, b.location_file, b.name_file, b.ukuran, b.cid 
						FROM antrian_email_attach_file a
							INNER JOIN m_attach_file b
								ON a.m_attach_file_id::integer = b.m_attach_file_id::integer
						WHERE a.antrian_id = ".$antrian_id;
		$qry_attach = pg_query($sql_attach) or die('ERROR select attach: '.$sql_attach);
		
		$sql_show = "SELECT a.email, a.status_sample,
						b.from_name, b.reply_to_email, b.subject_email, b.isi_email, b.flagtrans,
						c.email_from,
						d.alamat1, d.alamat2, d.alamat3, d.blth, d.nama_file, d.nama, d.pdf_name, d.password_pdf, d.tipe_kartu, d.ket_produk, d.email AS emailsebenarnya
					FROM tr_email a
						INNER JOIN template_email b
							ON a.template_id = b.template_email_id
						INNER JOIN mail_server c
							ON b.mail_server_id = c.mail_server_id
						INNER JOIN $tabeldetail d
							ON a.loading_id = d.m_loading_id
								and a.nomor_rekening = d.nomor_rekening
					WHERE a.antrian_id = ".$antrian_id;
		$qry_show = pg_query($sql_show) or die('ERROR show: '.$sql_show);
		$row_show = pg_fetch_assoc($qry_show);
		
		$from_name = $row_show['from_name'];
		$email_from = $row_show['email_from'];
		$reply_to = $row_show['reply_to_email'];
		$nama_customer = $row_show['nama'];
		$email_customer = $row_show['email'];
		$subjek = $row_show['subject_email'];
		$status_sample = $row_show['status_sample'];
		$password_pdf = $row_show['password_pdf'];
		$isi_email = $row_show['isi_email'];
		$nama_file = $row_show['nama_file'];
		$emailsebenarnya = $row_show['emailsebenarnya'];
			$blth = $row_show['blth'];
		
			if($status_sample=='t' || $status_sample=='TRUE'){
				$ganti = "<hr>kalimat ini hanya muncul untuk email sample saja.<p>password untuk dokumen ini adalah ".$password_pdf . "<br> Periode: " .$blth	. "<br> Cycle : " . $nama_file . "<br> Email Sebenarnya : <b>" . $emailsebenarnya . "</b>";
			}else{
				$ganti = "";
			}
			$isi_email = str_replace("@@#sample#@@",$ganti,$isi_email);
		$flagtrans = $row_show['flagtrans'];
		$pdf_name = $row_show['pdf_name'];
		$nama_file = $row_show['nama_file'];
		
		$detail['nama']		= $row_show['nama'];
		$detail['alamat1']	= $row_show['alamat1'];
		$detail['alamat2']	= $row_show['alamat2'];
		$detail['alamat3']	= $row_show['alamat3'];
		$detail['ket_produk']	= $row_show['ket_produk'];
		$blth				= $row_show['blth'];
		$detail['tipe_kartu']	= $row_show['tipe_kartu'];
			$periode = substr($blth,0,2).'-'.substr($blth,2,4);
			$periode = period($periode);
		$detail['periode']	= $periode;
		//$pdf_location = $row_show['pdf_location']; "../pdf/032012/STMEXT28/"
		
		//SETTING LOKASI FOLDER PDF
		$panjang_folder = strlen(trim($row_show['nama_file']));
		$balik_namafolder = strrev($row_show['nama_file']);
		$posisi_titik = strpos($balik_namafolder,".");
		$balik_namafolder = substr($balik_namafolder,$posisi_titik+1,$panjang_folder);
		$namafolder = strrev($balik_namafolder);
										
		$pdf_location = "../pdf/".$flagtrans."/".$blth."/".$namafolder."/";
		
		
		
		//EMBED IMAGE
		$isi_template = $isi_email;
		$cek_embed = substr_count($isi_template,'src="cid:1');
		if($cek_embed>0){
			$cidpos = strpos($isi_template,'src="cid:1');
			$kodecid = substr($isi_template, $cidpos, 13);
			$kodecid = substr($kodecid, -4);
			$sql_cid = "SELECT location_file,name_file FROM m_attach_file WHERE cid='$kodecid'";
			$exe_cid = pg_query($sql_cid);
			$row_cid = pg_fetch_array($exe_cid);
			$location_file = $row_cid['location_file'] . $row_cid['name_file'];
			$isi_email = str_replace('<img src="cid:'.$kodecid.'" />',"<img src='$location_file'>",$isi_email);
		} 
		
		?>        
		<script type="text/javascript" src="../script/ajax.js"></script>
		<script type="text/javascript" src="../script/jquery.js"></script>
        <script type="text/javascript" src="../script/function.js"></script>

        <style>
			table{
				background-color:#fff;
				color: #000000;
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 12px;
				margin: 20px 0px 20px 0px;
				padding: 0px 0px 0px 0px;
			}

			.button {
				background-color: #C60323;
				border: #000000 1px solid;
				color: #FFFFFF;
				cursor: pointer;
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 12px;
				font-weight: bold;
				height: 30px;
				border-radius: 10px;
			}
			
			.textbox_3 {
				background-color: #CCFFCC;
				border: #666666 1px solid;
				color: #0000CC;
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 12px;
				width: 200px;
				border-radius: 5px;
			}
			
			.textbox_4 {
				background-color: #CCFFCC;
				border: #666666 1px solid;
				color: #0000CC;
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 12px;
				width: 400px;
				border-radius: 5px;
			}
		</style>
		<div id="div_body">
		<form id="form_editor" name="form_editor" method="post">
            <table align="center" width="100%">
            	<tr>
                	<td>From</td>
                    <td><input type="text" id="text_from" name="text_from" class="textbox_4" value="<?=$from_name.' < '.$email_from.' >'?>" disabled="disabled" /></td>
                </tr>
            	<tr>
                	<td>Reply To</td>
                    <td><input type="text" id="text_reply_to" name="text_reply_to" class="textbox_4" value="<?=$reply_to?>" disabled="disabled" /></td>
                </tr>
            	<tr>
                	<td>To</td>
                    <td><input type="text" id="text_to" name="text_to" class="textbox_4" value="<?=$nama_customer.' < '.$email_customer.' >'?>" disabled="disabled" /></td>
                </tr>
            	<tr>
                	<td>Subject</td>
                    <td><input type="text" id="text_subjek" name="text_subjek" class="textbox_4" value="<?=replace_msg_email($flagtrans,$subjek,$detail)?>" disabled="disabled" /></td>
                </tr>
            	<tr>
                	<td>PDF File</td>
                    <td>
                    	<a href="<?=$pdf_location.$pdf_name.".pdf"?>"><input type="button" class="button" value="<?=$pdf_name?>" /></a>
                    </td>
                </tr>
            	<tr>
                	<td>Attachment</td>
                    <td>
						<?php
                        while($row_attach = pg_fetch_array($qry_attach)){
						if($row_attach['cid']==''){
                            $location_file = $row_attach['location_file'];
                            $name_file = $row_attach['name_file'];
                            $ukuran = $row_attach['ukuran'];
                        	echo '<a href="'.$location_file.$name_file.'"><input type="button" class="button" value="'.$name_file.' ( '.$ukuran.' Kb )" /></a>';
							}
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>Content</td>
                    <td style="border-color:#000; border-width:1px; border:solid"><?=replace_msg_email($flagtrans,$isi_email,$detail)?></td>
                </tr>
            </table>
        </form>
        </div>
        <?
	}
	
	
	
	function get_from_mailserver($blth, $flagtrans, $nama_file, $per_send){
		$sql_sel_mail_server = "SELECT c.mail_server_id, c.email_from, c.email_pass, c.email_inbox, c.email_bounce_back, a.loading_id 
								FROM tr_email a
									INNER JOIN template_email b
										ON a.template_id = b.template_email_id
									INNER JOIN mail_server c
										ON b.mail_server_id = c.mail_server_id
									INNER JOIN m_loading d
										ON a.loading_id = d.m_loading_id
								WHERE d.blth = '$blth' and d.flagtrans = '$flagtrans'";
		if($nama_file!='' && $nama_file!='null'){
			$sql_sel_mail_server .= " and d.loading_file = '$nama_file'";
		}
		$sql_sel_mail_server .= " GROUP BY c.mail_server_id, c.email_from, c.email_pass, c.email_inbox, c.email_bounce_back, a.loading_id ";
		//echo $sql_sel_mail_server;
		$qry_sel_mail_server = pg_query($sql_sel_mail_server) or die('ERROR: select mail server: '.$sql_sel_mail_server);
		
		while($row_mail_server = pg_fetch_array($qry_sel_mail_server)){
			$email_inbox = $row_mail_server['email_inbox'];
			$email_from = $row_mail_server['email_bounce_back'];
			$email_pass = $row_mail_server['email_pass'];
			$loading_id = $row_mail_server['loading_id'];
			
			//$mbox = imap_open("{your.imap.host:143}", "username", "password");
			$mbox = imap_open($email_inbox, $email_from, $email_pass) or die("can't connect: " . imap_last_error());
			
			$email_bounce_message='';
			for($i = 1; $i <= imap_num_msg($mbox); $i++){
				$header_info = imap_headerinfo($mbox, $i);
				if (strtoupper(substr($header_info->senderaddress, 0, 13)) == "MAILER-DAEMON") {
					cekBounce($mbox, $i, $header_info->senderaddress, $nama_file, $loading_id, $per_send, '',1);
						imap_delete($mbox, $i);
				}else{
					$header = pg_escape_string($header_info->senderaddress);
					$body = pg_escape_string(imap_body($mbox, $i));
					
					$sql = " insert into bounce_inbox(
									header_bounce, body_bounce, date_bounce
								)values(
									'$header'::varchar(100), '$body'::varchar(400), now()
								)";
					@pg_query($sql);
						imap_delete($mbox, $i);
				}
			}
			
			imap_expunge($mbox);
			imap_close($mbox);
		}
		return 'connect';
	}
	
	function cekBounce($mbox, $i, $header, $nama_file, $loading_id, $per_send){
		$sent_from = $per_send[0];
		$sent_to = $per_send[1];
		if (!empty($nama_file)){
			$w_cycle ="AND loading_id =".$loading_id."";
		}else{
			$w_cycle ="";
		}
		if (!empty($sent_from) && !empty($sent_to)){
			$w_pkirim ="AND (date_email_send BETWEEN '".$sent_from."' AND '".$sent_to."')";
		}else{
			$w_pkirim = "";
		}
									
		$arr_bounce = explode(chr(13) . chr(10), imap_body($mbox, $i));
		$email_bounce_message = addslashes($arr_bounce[5]);
		$email_bounce_message .= " " . addslashes($arr_bounce[6]);
		
		$slice = preg_replace('/[\=\"\']/','',imap_body($mbox, $i));
		$arr_message = explode("|",$slice);
		
		//AMBIL blth dari hidden input
		$email_bounce_message = preg_replace('/[\'\/]/','',$email_bounce_message);
		$client = $arr_message[1];
		$flagtrans = $arr_message[2];
		$tr_email_id = $arr_message[3];
		$email = $arr_message[4];
		
		if($email==''){
			// Ambil Alamat Email Dari Error Msg
			preg_match('/\(([0-9a-zA-Z\.\-\_\@]+)\)/',addslashes($arr_bounce[6]),$matches); 
			$email = $matches[1];
		}elseif($email==''){
			preg_match('/<([0-9a-zA-Z\.\-\_\@]+)>/',addslashes($arr_bounce[6]),$matches); 
			$email=$matches[1];
		} 
		
		if(trim($email)<>''){ 
			$sql = "UPDATE tr_email";
			$sql .= " SET email_callback = FALSE, date_email_callback = NOW(), ket_error='$email_bounce_message'";
			$sql .= " WHERE tr_email_id = $tr_email_id and trim(email) = '".trim($email)."' ".$w_cycle." ".$w_pkirim." ";
			//echo $sql;
			$query = pg_query($sql) or die("Invalid query!".$sql);
			
			if(!pg_affected_rows($query)){
				$sql = "	INSERT INTO bounce_inbox(";
				$sql .= " 			header_bounce, body_bounce, date_bounce";
				$sql .= " 		)VALUES(";
				$sql .= "			'$header'::varchar(100), '$slice'::varchar(400), now())";
				
				@pg_query($sql);
			}
		}else{
			$sql = "	INSERT INTO bounce_inbox(";
			$sql .= " 			header_bounce, body_bounce, date_bounce";
			$sql .= " 		)VALUES(";
			$sql .= "			'$header'::varchar(100), '$slice'::varchar(400), now())";
			
			@pg_query($sql);
		}
	}
	
	
	function export_excel_ckemail($blth, $flagtrans, $cycle, $per_send, $curloc, $locateFile){
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="summary_detail_p'.$flagtrans.'_b'.$blth.'.xls"');
		header('Cache-Control: max-age=0');

		$cekemail_p = $_REQUEST['cekemail'];
		if ($cekemail_p == 1){
			//$get_respon = get_from_mailserver($blth, $flagtrans, $cycle, $per_send);
			$get_respon = get_from_mailserver($blth, $flagtrans, $cycle, $per_send,'',1);
		}
		
		if (($get_respon=='connect') || (empty($cekemail_p))){
			export_excel($curloc, $locateFile);
		}
		
	}
	
	function export_excel($curloc, $locateFile) {
		$arr_pr = explode('|', $_REQUEST['pr']);
		$flagtrans = $arr_pr[0];
		$menuid = $arr_pr[1];
		$blth = $_REQUEST['blth'];
		$cycle = $_REQUEST['cycle'];
		$per_send = explode('|', $_REQUEST['periode_kirim']);
		$sent_from = $per_send[0];
		$sent_to = $per_send[1];
		
		//include $curloc.'PHPExcel.php';
		//include $curloc.'PHPExcel/Writer/Excel5.php';
		
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("App Dev");
		$objPHPExcel->getProperties()->setLastModifiedBy("Estatement");
		$objPHPExcel->getProperties()->setTitle("AXA Estatement");
		$objPHPExcel->getProperties()->setSubject("Report Excel");
		
		$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(50);
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
		
		$objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow(0,1,10,1);
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray(
		array(
			'font' => array(
				'name'         => 'Arial',
				'bold'         => true,
				'italic'    => false,
				'size'        => 12
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap'       => true
			)
		)
		);
		
		$objPHPExcel->setActiveSheetIndex(0);
		
		//$objPHPExcel->getActiveSheet()->SetCellValue('F1','No. ');
		//$objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Tanggal: '.date('j F Y',time()).'');
		
		$judul = judul_ex_xls($menuid);
		$objPHPExcel->getActiveSheet()->SetCellValue('A1',$judul);
		
		$objPHPExcel->getActiveSheet()->SetCellValue('B3',"Periode");
		
		$blth_m = datemonth_inttostr(substr($blth,0,2));
		$objPHPExcel->getActiveSheet()->SetCellValue('C3',''.$blth_m.' '.substr($blth, -4).'');
		
		if (empty($cycle) && empty($sent_from)){
		$y_post = 5;
		$l_post = 'C3';
		}else{
		if (!empty($cycle) && !empty($sent_from)){
			$objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow(2,5,4,5);
			$objPHPExcel->getActiveSheet()->SetCellValue('B4',"Cycle");			$objPHPExcel->getActiveSheet()->SetCellValue('C4',$cycle);
			$objPHPExcel->getActiveSheet()->SetCellValue('B5',"Tanggal Kirim");	$objPHPExcel->getActiveSheet()->SetCellValue('C5',''.$sent_from.' s/d '.$sent_to.'');
			$y_post = 7;
			$l_post = 'C5';
		}else{
			if (empty($cycle)){
				$objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow(2,4,4,4);
				$lable = 'Tanggal Kirim';
				$val = ''.$sent_from.' s/d '.$sent_to.'';
			}else{
				$lable = 'Cycle';
				$val = $cycle;
			}
			$objPHPExcel->getActiveSheet()->SetCellValue('B4',$lable);			$objPHPExcel->getActiveSheet()->SetCellValue('C4',$val);
			$y_post = 6;
			$l_post = 'C4';
		}
		}
		
		$objPHPExcel->getActiveSheet()->duplicateStyleArray(
		array(
			'font' => array(
				'name'         => 'Arial',
				'bold'         => true,
				'italic'    => false,
				'size'        => 9
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap'       => true
			)
		),
		//'B3:C'.$l_post.''
		'B3:B'.$l_post.''
		);
		$objPHPExcel->getActiveSheet()->duplicateStyleArray(
		array(
			'font' => array(
				'name'         => 'Arial',
				'bold'         => true,
		
				'italic'    => false,
				'size'        => 9
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap'       => true
			)
		),
		'A'.$y_post.':K'.$y_post.''
		);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$y_post.':K'.$y_post.'')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('ffdfdfdf');
		$styleArray = array(
		'borders' => array(
			'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
			'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
			'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
			'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
		)
		);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$y_post.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('B'.$y_post.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('C'.$y_post.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('D'.$y_post.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('E'.$y_post.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$y_post.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('G'.$y_post.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('H'.$y_post.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('I'.$y_post.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('J'.$y_post.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('K'.$y_post.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('L'.$y_post.'')->applyFromArray($styleArray);
		
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$y_post.'',"No");
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$y_post.'',"Tanggal Loading");
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$y_post.'',"Nama File");
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$y_post.'',"Jumlah PDF");
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$y_post.'',"Jumlah Halaman");
		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$y_post.'',"Jumlah Email (<=4hlm)");
		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$y_post.'',"Jumlah Email (>=5hlm)");
		$objPHPExcel->getActiveSheet()->SetCellValue('H'.$y_post.'',"Email di Antrian");
		$objPHPExcel->getActiveSheet()->SetCellValue('I'.$y_post.'',"Email Sukses");
		$objPHPExcel->getActiveSheet()->SetCellValue('J'.$y_post.'',"Email Gagal");
		$objPHPExcel->getActiveSheet()->SetCellValue('K'.$y_post.'',"Sample Sukses");
		$objPHPExcel->getActiveSheet()->SetCellValue('L'.$y_post.'',"Sample Gagal");
		
		$getRowData_dtail = get_RowData($blth, $flagtrans, $cycle, $sent_from, $sent_to);
		//print_r($getRowData_dtail);
		$not=1;
		while($row_data = pg_fetch_array($getRowData_dtail)){
		$atrow=$not + $y_post;
		
		$t_jml_pdf += $row_data['jml_pdf'];
		$t_jml_lembar += $row_data['jml_lembar'];
		$t_jml_email += $row_data['jml_email'];
		#$t_jml_email += $row_data['jml_email'];
		$t_jml_email_bayar += $row_data['jml_email_bayar'];
		$t_jml_antrian += $row_data['jml_antrian'];
		$t_email_sukses += $row_data['email_sukses'];
		$t_email_gagal += $row_data['email_gagal'];
		$t_sample_sukses += $row_data['sample_sukses'];
		$t_sample_gagal += $row_data['sample_gagal'];
		if (empty($row_data['create_date'])){$row_data['create_date']=0;}
		if (empty($row_data['loading_file'])){$row_data['loading_file']=0;}
		if (empty($row_data['jml_pdf'])){$row_data['jml_pdf']=0;}
		if (empty($row_data['jml_lembar'])){$row_data['jml_lembar']=0;}
		if (empty($row_data['jml_email'])){$row_data['jml_email']=0;}
		if (empty($row_data['jml_email_bayar'])){$row_data['jml_email_bayar']=0;}
		if (empty($row_data['jml_antrian'])){$row_data['jml_antrian']=0;}
		if (empty($row_data['email_sukses'])){$row_data['email_sukses']=0;}
		if (empty($row_data['email_gagal'])){$row_data['email_gagal']=0;}
		if (empty($row_data['sample_sukses'])){$row_data['sample_sukses']=0;}
		if (empty($row_data['sample_gagal'])){$row_data['sample_gagal']=0;}
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$atrow.'',$not);
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$atrow.'',$row_data['create_date']);
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$atrow.'',$row_data['loading_file']);
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$atrow.'',$row_data['jml_pdf']);
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$atrow.'',$row_data['jml_lembar']);
		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$atrow.'',$row_data['jml_email']);
		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$atrow.'',$row_data['jml_email_bayar']);
		$objPHPExcel->getActiveSheet()->SetCellValue('H'.$atrow.'',$row_data['jml_antrian']);
		$objPHPExcel->getActiveSheet()->SetCellValue('I'.$atrow.'',$row_data['email_sukses']);
		$objPHPExcel->getActiveSheet()->SetCellValue('J'.$atrow.'',$row_data['email_gagal']);
		$objPHPExcel->getActiveSheet()->SetCellValue('K'.$atrow.'',$row_data['sample_sukses']);
		$objPHPExcel->getActiveSheet()->SetCellValue('L'.$atrow.'',$row_data['sample_gagal']);
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$atrow.':K'.$atrow.'')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('fff8f8f8');
		$objPHPExcel->getActiveSheet()->getStyle('A'.$atrow.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('B'.$atrow.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('C'.$atrow.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('D'.$atrow.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('E'.$atrow.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$atrow.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('G'.$atrow.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('H'.$atrow.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('I'.$atrow.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('J'.$atrow.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('K'.$atrow.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('L'.$atrow.'')->applyFromArray($styleArray);
		$not++;
		}
		$typost=$atrow+1;
		$objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow(0,$typost,2,$typost);
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$typost.'','Total');
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$typost.'',$t_jml_pdf);
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$typost.'',$t_jml_lembar);
		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$typost.'',$t_jml_email);
		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$typost.'',$t_jml_email_bayar);
		$objPHPExcel->getActiveSheet()->SetCellValue('H'.$typost.'',$t_jml_antrian);
		$objPHPExcel->getActiveSheet()->SetCellValue('I'.$typost.'',$t_email_sukses);
		$objPHPExcel->getActiveSheet()->SetCellValue('J'.$typost.'',$t_email_gagal);
		$objPHPExcel->getActiveSheet()->SetCellValue('K'.$typost.'',$t_sample_sukses);
		$objPHPExcel->getActiveSheet()->SetCellValue('L'.$typost.'',$t_sample_gagal);
		
		$objPHPExcel->getActiveSheet()->duplicateStyleArray(
			array(
				'font' => array(
					'name'         => 'Arial',
					'bold'         => true,
					'italic'    => false,
					'size'        => 9
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
					'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
					'wrap'       => true
				)
			),
			'A'.$typost.''
		);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$typost.':K'.$typost.'')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('ffeeeeee');
		$objPHPExcel->getActiveSheet()->getStyle('A'.$typost.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('B'.$typost.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('C'.$typost.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('D'.$typost.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('E'.$typost.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$typost.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('G'.$typost.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('H'.$typost.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('I'.$typost.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('J'.$typost.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('K'.$typost.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('L'.$typost.'')->applyFromArray($styleArray);
		
		$export_xls = export_xls($objPHPExcel, $locateFile, 'Periode '.$blth.'');
	}
	
		
	function export_xls($objPHPExcel, $locateFile, $title){
		$objPHPExcel->getActiveSheet()->setTitle($title);
		$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
		//$createExcel = $objWriter->save($locateFile);
		$createExcel = $objWriter->save('php://output');
		
		return true;
	}
	
	function judul_ex_xls($menu_id){
		$sql_sel_judul = "SELECT a.menugroup, b.menu
							FROM menugroup1 a
								INNER JOIN menu1 b
									ON a.menugroupid = b.menugroupid
							WHERE b.menuid = $menu_id";
		$qry_sel_judul = pg_query($sql_sel_judul) or die('ERROR select judul: '.$sql_sel_judul);
		$row_sel_judul = pg_fetch_assoc($qry_sel_judul);
		return $row_sel_judul['menugroup'].' - '.$row_sel_judul['menu'];
	}
	
	function get_RowData($blth, $flagtrans, $cycle, $sent_from, $sent_to){
	$HLM_BERBAYAR = HLM_BERBAYAR;
		if (!empty($cycle)){
			$w_cycle ="and a.loading_file = '".$cycle."'";
		}else{
			$w_cycle ="";
		}
		if (!empty($sent_from)){
			$js_pkirim ="AND (date_email_send BETWEEN '".$sent_from."' AND '".$sent_to."')";
			$p_kirim = "and a.m_loading_id in (select loading_id from tr_email where date_email_send BETWEEN '".$sent_from."' AND '".$sent_to."') ";
		}else{
			$js_pkirim = "";
			$p_kirim = "";
		}
		$i = 0;
		
		$blth_balik = substr($blth,-4).substr($blth,0,2);
		
			$BLTH_BARU = BLTH_BARU;
			if($blth_balik>$BLTH_BARU){
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
			
		$sql_data = "SELECT
											a.m_loading_id,
											to_char(a.create_date,'yyyy-mm-dd HH24:MI') as create_date,
											a.loading_file,
											a.total_customer as jml_pdf,
											a.total_halaman as jml_lembar,
											c.jml_email_gratis,
											cb.jml_email_bayar,
											ds.total_hlm_berbayar,
											je.jml_email,
											d.jml_antrian,
											e.email_sukses,
											et.email_terbaca,
											f.email_gagal,
											g.sample_sukses,
											h.sample_gagal,
											(SELECT DISTINCT(count(tr_email_id)) as jml_email_others FROM bounce_inbox WHERE blth='$blth' AND m_loading_id=a.m_loading_id  AND sample_status is not true)AS jml_email_others
										FROM m_loading a
											LEFT JOIN $tabeldetail b
												ON a.m_loading_id = b.m_loading_id
											LEFT JOIN (
												SELECT SUM(n_email) as jml_email_gratis, m_loading_id
												FROM $tabeldetail WHERE jml_hlm <= $HLM_BERBAYAR
												GROUP BY m_loading_id
											) c
												ON a.m_loading_id = c.m_loading_id
											
											LEFT JOIN (
												SELECT SUM(n_email) as jml_email_bayar, m_loading_id
												FROM $tabeldetail WHERE jml_hlm > $HLM_BERBAYAR
												GROUP BY m_loading_id
											) cb
												ON a.m_loading_id = cb.m_loading_id		
												
								
											LEFT JOIN (
												SELECT count(tr_email_id) as jml_email, loading_id
												FROM tr_email
												WHERE status_sample IS NOT TRUE
												GROUP BY loading_id
											) je
												ON a.m_loading_id = je.loading_id
												
												
											LEFT JOIN (
												SELECT SUM(jml_hlm-$HLM_BERBAYAR)AS total_hlm_berbayar,m_loading_id 
												FROM $tabeldetail WHERE jml_hlm > $HLM_BERBAYAR 
												GROUP BY m_loading_id
											) ds ON a.m_loading_id = ds.m_loading_id		
											
											LEFT JOIN (
												SELECT count(antrian_id) as jml_antrian, loading_id
												FROM antrian_email
												GROUP BY loading_id
											) d
												ON a.m_loading_id = d.loading_id

											LEFT JOIN (
												SELECT count(*)  as email_sukses, loading_id
												FROM tr_email a
												WHERE date_email_callback is null ".$js_pkirim."
												and a.nomor_rekening is not null
												AND status_sample IS NOT TRUE
												GROUP BY loading_id
												) e
												ON a.m_loading_id = e.loading_id
												
											LEFT JOIN (
												SELECT count(tr_email_id) as email_gagal, loading_id
												FROM tr_email a 
												WHERE date_email_callback is not null ".$js_pkirim."
												and a.nomor_rekening is not null
												AND status_sample IS NOT TRUE
												GROUP BY loading_id
											) f
												ON a.m_loading_id = f.loading_id
												
											LEFT JOIN (
												SELECT count(tr_email_id) as email_terbaca, loading_id
												FROM (select * from tr_email where tgl_read is not null) a
												WHERE date_email_callback is null ".$js_pkirim."
												AND status_sample IS NOT TRUE
												GROUP BY loading_id
											) et
												ON a.m_loading_id = et.loading_id
												
											LEFT JOIN (
												SELECT count(tr_email_id) as sample_sukses, loading_id
												FROM tr_email a 
												WHERE date_email_callback is null ".$js_pkirim."
												and a.status_sample is true 
												GROUP BY loading_id
											) g
												ON a.m_loading_id = g.loading_id
											LEFT JOIN (
												SELECT count(tr_email_id) as sample_gagal, loading_id
												FROM tr_email a 
												WHERE date_email_callback is not null ".$js_pkirim."
												and a.status_sample is true 
												GROUP BY loading_id
											) h
												ON a.m_loading_id = h.loading_id
										WHERE a.blth = '$blth' and a.flagtrans = '$flagtrans' 
										".$p_kirim."
										".$w_cycle."
										GROUP BY a.m_loading_id, to_char(a.create_date,'yyyy-mm-dd HH24:MI'), a.loading_file, a.total_customer, a.total_halaman,
											c.jml_email_gratis, cb.jml_email_bayar, je.jml_email, ds.total_hlm_berbayar, d.jml_antrian, e.email_sukses, f.email_gagal, et.email_terbaca, g.sample_sukses, h.sample_gagal
										ORDER BY a.m_loading_id";
		$qry_data = pg_query($sql_data) or die('ERROR select summary detail: '.$sql_data);
		
		return $qry_data;
	}
	
	function datemonth_inttostr($intmont){
		$datemontarr=array(
			'01' => 'Januari', 
			'02' => 'Februari', 
			'03' => 'Maret', 
			'04' => 'April', 
			'05' => 'Mei', 
			'06' => 'Juni', 
			'07' => 'Juli', 
			'08' => 'Agustus', 
			'09' => 'September', 
			'10' => 'Oktober', 
			'11' => 'Nopember', 
			'12' => 'Desember'
		);
		return $datemontarr[$intmont];
	}
	
	function HeaderingExcel($filename) {
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=$filename");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Pragma: public");
	}
	
	pg_close($con);
	
	
	
	
	
	
	
	
	/** START BERITA ACARA **/
	
	function export_ba_ckemail($client_, $blth, $flagtrans, $cycle, $per_send, $curloc, $locateFileBA){
		$cekemail_p = $_REQUEST['cekemail'];
		$client = $_REQUEST['client'];
		if ($cekemail_p == 1){
			$get_respon = get_from_mailserver($blth, $flagtrans, $cycle, $per_send, $client);
		}
		
		if (($get_respon=='connect') || (empty($cekemail_p))){
			export_ba($client_, $curloc, $locateFileBA);
		}
		
	}
	
	function export_ba($client, $curloc, $locateFileBA) {
	$HLM_BERBAYAR = HLM_BERBAYAR;
		$arr_pr = explode('|', $_REQUEST['pr']);
		$flagtrans = $arr_pr[0];
		$menuid = $arr_pr[1];
		$blth = $_REQUEST['blth'];
		$cycle = $_REQUEST['cycle'];
		$per_send = explode('|', $_REQUEST['periode_kirim']);
		$sent_from = $per_send[0];
		$sent_to = $per_send[1];
		
		$sql_produk="
			SELECT produk FROM mproduk 
			WHERE status=TRUE AND flagtrans='".$flagtrans."'";
		$qry_produk=pg_query($sql_produk)or die('Err: $sql_produk');
		$row_produk=pg_fetch_assoc($qry_produk);
		
		$produk = $row_produk["produk"];
		require_once("../include/tcpdf/config/lang/eng.php");
		require_once("../include/tcpdf/tcpdf.php");
		
		class MYPDF extends TCPDF {
			public function Footer() {
				$this->SetY(-15);
				$this->SetFont('times', '', 8);
				$this->Cell(0, 10, 'Halaman '.$this->getAliasNumPage().' dari '.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
			}			
		}
		
		$pdf = new MYPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
				
		$pdf->SetCreator('Appdev Application E-Statement '.$client);
		$pdf->SetAuthor('PT Datanet Indomedia');
		$pdf->SetTitle('Berita Acara '.$client.' Agent - '.$produk);
		$pdf->SetSubject('Berita Acara '.$client.' Agent - '.$produk);
		$pdf->SetKeywords('');
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->setPrintHeader(false);
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
			
		//--- Begin Content ---
		$x = 5; 
		$y = 5; 
		$pdf->SetMargins($x,$y,5);
		$pdf->AddPage();
			
		
		$pdf->SetXY($x,$y);
		$pdf->SetFont('times', 'B', 14);
		$pdf->Cell(287,5,'BERITA ACARA '.$client.' '.strtoupper($produk),0,1,'C');
		
		$pdf->SetFont('times', 'B', 9);
		$pdf->Ln();
		$pdf->Cell(25,5,'Periode',0,0,'L');
		$pdf->Cell(2,5,':',0,0,'C');		
		$pdf->Cell(60,5,$blth,0,1,'L');
		
		if (!empty($cycle)){
			$pdf->Cell(25,5,'Cycle',0,0,'L');
			$pdf->Cell(2,5,':',0,0,'C');		
			$pdf->Cell(60,5,$cycle,0,1,'L');			
		}
		if (!empty($sent_from)){
			$pdf->Cell(25,5,'Tanggal Kirim',0,0,'L');
			$pdf->Cell(2,5,':',0,0,'C');		
			$pdf->Cell(60,5,$sent_from.' s/d '.$sent_to,0,1,'L');			
		}
		
		$pdf->Ln(0.5);
		$pdf->SetFont('times', 'B', 8);
		$pdf->Cell(287,5,'Print Date : '.date('d-m-Y H:i'),0,1,'R');
		$pdf->Cell(8,10,'No',1,0,'C');
		$pdf->Cell(25,10,'Tanggal Loading',1,0,'C');
		$pdf->Cell(62,10,'Nama File',1,0,'C');
		$pdf->Cell(15,10,'Jml PDF',1,0,'C');
		$pdf->Cell(15,10,'Jml Hlm',1,0,'C');
		$pdf->Cell(56,5,'PDF',1,0,'C');
		$pdf->Cell(60,5,'Email',1,0,'C');
		$pdf->Cell(30,5,'Sample',1,1,'C');
		
		$pdf->SetXY($x+125,$pdf->GetY());
		$pdf->Cell(15,5,'<= '.$HLM_BERBAYAR.' hlm',1,0,'C');
		$pdf->Cell(15,5,'> '.$HLM_BERBAYAR.' hlm',1,0,'C');
		$pdf->Cell(26,5,'Hlm Berbayar',1,0,'C');
		$pdf->Cell(15,5,'Antrian',1,0,'C');
		$pdf->Cell(15,5,'Dikirim',1,0,'C');
		$pdf->Cell(15,5,'Sukses',1,0,'C');
		$pdf->Cell(15,5,'Gagal',1,0,'C');
		$pdf->Cell(15,5,'Sukses',1,0,'C');
		$pdf->Cell(15,5,'Gagal',1,1,'C');
		
		$pdf->SetFont('times', '', 9);
		
		$getRowData_dtail = get_RowData($blth, $flagtrans, $cycle, $sent_from, $sent_to);
		//print_r($getRowData_dtail);
		$no=1;
		while($row_data = pg_fetch_array($getRowData_dtail)){																																	
			$pdf->Cell(8,5,$no.".",1,0,'R');
			$pdf->Cell(25,5,$row_data['create_date'],1,0,'C');
			$pdf->Cell(62,5,$row_data['loading_file'],1,0,'L');
			$pdf->Cell(15,5,number_format($row_data['jml_pdf'],0,'',','),1,0,'R');
			$pdf->Cell(15,5,number_format($row_data['jml_lembar'],0,'',','),1,0,'R');
			$pdf->Cell(15,5,number_format($row_data['jml_email_gratis'],0,'',','),1,0,'R');
			$pdf->Cell(15,5,number_format($row_data['jml_email_bayar'],0,'',','),1,0,'R');
			$pdf->Cell(26,5,number_format($row_data['total_hlm_berbayar'],0,'',','),1,0,'R');
			$pdf->Cell(15,5,number_format($row_data['jml_antrian'],0,'',','),1,0,'R');
			$pdf->Cell(15,5,number_format($row_data['jml_email'],0,'',','),1,0,'R');
			$pdf->Cell(15,5,number_format($row_data['email_sukses'],0,'',','),1,0,'R');
			$pdf->Cell(15,5,number_format($row_data['email_gagal'],0,'',','),1,0,'R');
			$pdf->Cell(15,5,number_format($row_data['sample_sukses'],0,'',','),1,0,'R');
			$pdf->Cell(15,5,number_format($row_data['sample_gagal'],0,'',','),1,1,'R');
			
			$total_pdf = $total_pdf + $row_data['jml_pdf'];
			$jml_lembar = $jml_lembar + $row_data['jml_lembar'];
			$total_email = $total_email + $row_data['jml_email'];
			$total_email_gratis = $total_email_gratis + $row_data['jml_email_gratis'];
			$total_hlm_berbayar = $total_hlm_berbayar + $row_data['total_hlm_berbayar'];
			$total_email_bayar = $total_email_bayar + $row_data['jml_email_bayar'];
			$total_tanpa_email = $total_tanpa_email + $row_data['jml_tanpa_email'];
			$total_tanpa_email_bayar = $total_tanpa_email_bayar + $row_data['jml_tanpa_email_bayar'];
			$total_antrian = $total_antrian + $row_data['jml_antrian'];
			$total_e_sukses = $total_e_sukses + $row_data['email_sukses'];
			$total_e_gagal = $total_e_gagal + $row_data['email_gagal'];
			$total_s_sukses = $total_s_sukses + $row_data['sample_sukses'];
			$total_s_gagal = $total_s_gagal + $row_data['sample_gagal'];
			$no++;
		}
		
		
		$pdf->SetFont('times', 'B', 9);
		$pdf->Cell(95,5,'TOTAL',1,0,'C');
		$pdf->Cell(15,5,number_format($total_pdf,0,'',','),1,0,'R');
		$pdf->Cell(15,5,number_format($jml_lembar,0,'',','),1,0,'R');
		$pdf->Cell(15,5,number_format($total_email_gratis,0,'',','),1,0,'R');
		$pdf->Cell(15,5,number_format($total_email_bayar,0,'',','),1,0,'R');
		$pdf->Cell(26,5,number_format($total_hlm_berbayar,0,'',','),1,0,'R');
		$pdf->Cell(15,5,number_format($total_antrian,0,'',','),1,0,'R');
		$pdf->Cell(15,5,number_format($total_email,0,'',','),1,0,'R');
		$pdf->Cell(15,5,number_format($total_e_sukses,0,'',','),1,0,'R');
		$pdf->Cell(15,5,number_format($total_e_gagal,0,'',','),1,0,'R');
		$pdf->Cell(15,5,number_format($total_s_sukses,0,'',','),1,0,'R');
		$pdf->Cell(15,5,number_format($total_s_gagal,0,'',','),1,1,'R');
		
		$pdf->SetFont('times', '', 12);
		$pdf->MultiCell(287,5,'Demikian Berita Acara ini dibuat dengan sebenarnya dengan itikad baik untuk dipergunakan sebagaimana mestinya.'."\n",0,'J',0,1,$x,$pdf->GetY()+10,true);
		
		$pdf->SetFont('times', 'B', 12);
		$pdf->SetXY($x+60,$pdf->GetY()+10);
		$pdf->Cell(20,5,'Pihak Pertama',0,0,'C');
		
		$pdf->SetXY($pdf->GetX()+100,$pdf->GetY());
		$pdf->Cell(20,5,'Pihak Kedua',0,0,'C');
		
		$pdf->SetFont('times', 'U', 12);
		$pdf->SetXY($x+60,$pdf->GetY()+30);
		$pdf->Cell(20,5,'(PT Datanet Indomedia)',0,0,'C');
		
		$pdf->SetXY($pdf->GetX()+100,$pdf->GetY());
		$pdf->Cell(20,5,'(PT '.$client.')',0,0,'C');
				
		$pdf->Output($locateFileBA, 'I');
		$pdf->Output($locateFileBA, 'F');
		
	}
	
	/** END BERITA ACARA **/
	
	
	
	
	
	/** PAGINATION **/
	
	function makeLinks($str,$offset,$bold="false",$divload,$halaman_load,$value){
		if($bold){
			$str="<b>".$str."</b>";
		}
	
		return '<a href="'.$halaman_load.'?'.$value.'&offset='.$offset.'" class="path">'.$str.'</a>';
	}

	function pagings($curRec,$totalRec,$maxRec,$divload,$halaman_load,$value){
		
		$totalPage=ceil($totalRec/$maxRec);
		$curPage=ceil(($curRec+1)/$maxRec);
		$str="";
		
		/*--------------------------prev button-----------------------*/
		if($curPage>1){
			$rec=($curPage-2)*$maxRec;					
			$str.=" ".makeLinks("prev",$rec,$bold,$divload,$halaman_load,$value)." ";			
		}
		
		/*-------------------------generate page number----------------*/
		for($i=1;$i<=$totalPage;$i++){
			if($i==$curPage){
				$bold=true;
			}else{
				$bold=false;
			}
			$rec=($i-1)*$maxRec;					
			$str.=" ".makeLinks($i,$rec,$bold,$divload,$halaman_load,$value)." ";
		}
		
		/*--------------------------next button-----------------------*/
		if($curPage<$totalPage){
			$rec=($curPage*$maxRec);					
			$str.=" ".makeLinks("next",$rec,$bold,$divload,$halaman_load,$value)." ";			
		}
		
		return $str;
		
	}
	
	/** END PAGINATION **/
	
	
	/** FUNGSI MEMBUKA TEMPLATE EMAIL TERBACA **/
		function view_bodymailterbaca(){
		$tr_email_id = $_REQUEST['tr_email_id'];
		$sql_data = "SELECT body_email_read FROM tr_email WHERE tr_email_id = $tr_email_id";
		$qry_data = pg_query($sql_data) or die('ERROR select summary detail: '.$sql_data);
		$row_data = pg_fetch_assoc($qry_data);
		echo '<pre>'.$row_data['body_email_read'].'</pre>';
	}

	/** FUNGSI MEMBUKA TEMPLATE EMAIL OTHERS **/
		function view_bodymailothers(){
		$tr_email_id = $_REQUEST['tr_email_id'];
		$sql_data = "SELECT body_bounce FROM bounce_inbox WHERE tr_email_id = $tr_email_id";
		$qry_data = pg_query($sql_data) or die('ERROR select summary detail: '.$sql_data);
		$row_data = pg_fetch_assoc($qry_data);
		echo '<pre>'.htmlentities($row_data['body_bounce']).'</pre>';
		//echo '<pre>'.$tr_email_id.'</pre>';
	}
	
	
	
	
	
	
	
	
	
	
	
	function sof_text($con,$cycle)
	{	
//	echo $sQuery."<br>";		
		$queri_1 = "select loading_file from m_loading where m_loading_id = '$cycle' ";
		$qry_data = pg_query($queri_1) or die('ERROR select: '.$queri_1);
		$row_data = pg_fetch_assoc($qry_data);
		$nama_file_loading = str_replace('TXT','SOF', strtoupper($row_data['loading_file'])  );
		
		$posisi_titik = strpos($nama_file_loading,".");
		$nama_file_loading = substr($nama_file_loading,0, $posisi_titik). '.SOF';
		
		
$sQuery = "	SELECT  a.blth, a.loading_file, to_char(a.create_date, 'dd Mon yyyy hh24:mi') AS create_date, 
						b.nomor_rekening, b.nama, b.flagtrans, b.pdf_name || '.pdf' AS pdf_name, to_char(e.tgl_antrian, 'dd-Mon-yyyy hh24:mi') AS tgl_antrian, 
						c.email AS email_antrian, 
						to_char(f.tgl_jadwal, 'dd Mon yyyy hh24:mi') AS tgl_jadwal_send, 
						to_char(d.date_email_send, 'dd Mon yyyy hh24:mi') AS date_email_send,
						b.email as email_detail, 
						d.email AS email_kirim, d.ket_error, to_char(d.tgl_read, 'dd-Mon-yyyy hh24:mi') AS tgl_read ,b.password_pdf,
						CASE WHEN c.email is not null and d.date_email_send is null THEN 'Q' 
						WHEN d.email is not null and d.date_email_callback is null THEN 'S' 
						WHEN d.email is not null and d.date_email_callback is not null 
						THEN 'F' ELSE 'NO STATUS' END AS status 
						FROM m_loading a 
						LEFT JOIN detail b ON a.m_loading_id = b.m_loading_id 
						LEFT JOIN antrian_email c ON a.m_loading_id = c.loading_id and b.nomor_rekening = c.nomor_rekening 
						and (c.status_sample = 'f' or c.status_sample is null) 
						LEFT JOIN tr_email d ON a.m_loading_id = d.loading_id and b.nomor_rekening = d.nomor_rekening 
						and (d.status_sample = 'f' or d.status_sample is null) 
						LEFT JOIN antrian_email_history e ON d.antrian_id = e.antrian_id
						LEFT JOIN m_jadwal f on f.jadwal_id = e.jadwal_id 
						WHERE b.m_loading_id = '$cycle'
						ORDER BY a.create_date DESC " ;
		//die	($sQuery);			
		$rs = pg_query($con,$sQuery) or die("Invalid query!" . $sQuery) ;
		$rows = pg_num_rows($rs);
		if ($rows < 1)
		{
		echo "No Record" ;
		}
		else
		{


				echo $rows . " records " . "<br>";
				$filename = '../tmp/'.$nama_file_loading;
				$fh = fopen($filename,'w+') or die("can't open:  $php_errormsg");
				
				$rows = pg_num_rows($rs);
				while($hasil = pg_fetch_array($rs))
				{
						
						$barcode			= trim($hasil['barcode']);
						$status	= trim($hasil['status']);
						$nomor_rekening		= trim($hasil['nomor_rekening']);
						$email_detail		= trim($hasil['email_detail']);	
						$tgl_antrian			= trim($hasil['tgl_antrian']);
						$tgl_jadwal_send			= trim($hasil['tgl_jadwal_send']);
						$date_email_send		= trim($hasil['date_email_send']);
						$ket_error		= trim($hasil['ket_error']);
						
						
						$kata = '"'.($barcode).'","'.
								($status).'","'.
								($nomor_rekening).'","'.
								($email_detail).'","'.
								($tgl_antrian).'","'.
								($tgl_jadwal_send).'","'.
								($date_email_send).'","'.
								($ket_error).'"'.
								"\r";	
				$counter = $counter + 1; 								
						fwrite($fh,$kata);
				}
				
				$akhir = "";
				fwrite($fh,$akhir);
		
				fclose($fh);
				echo "KLIK KANAN, LALU SAVE LINK AS UNTUK MENDOWNLOAD SOF File <BR>";
				
				echo "<a href=\"../tmp/".$filename."\" > $nama_file_loading </a>";
		}
	}
	
	
?>
	

		<script>
		function view_bodymailterbaca(val){
		var tr_email_id = val;
		$.post('script_summary_detail_rev.php?act=view_bodymailterbaca&tr_email_id='+tr_email_id,"",function(respon){
			$.fancybox(respon);
		});
		}
		function view_bodymailothers(val){
		var tr_email_id = val;
		$.post('script_summary_detail_rev.php?act=view_bodymailothers&tr_email_id='+tr_email_id,"",function(respon){
			$.fancybox(respon);
		});
		}
		function view_template(val,vel,vul){
		var antrian_id = val;
		var blth = vel;
		var flagtrans = vul;
		$.post('script_summary_detail_rev.php?act=view_template&blth='+blth+'&flagtrans='+flagtrans+'&antrian_id='+antrian_id,"",function(respon){
			$.fancybox(respon);
		});
		}
		</script>
		<style>
		.path{
		font-family: Tahoma;
		font-size: 13px;
		text-decoration:underline;
		color:#000;
		}
		.path:hover{
			font-family: Tahoma;
			font-size: 13px;
			text-decoration:underline;
			color:#ff4422;	
		} 
		</style>