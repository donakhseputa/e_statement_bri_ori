<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/function.php"); ?>
<? require_once("../include/PHPMailer/class.phpmailer.php"); ?>
<?
	$act = $_REQUEST['act'];
	$con = pg_connect($connection) or die("Could not connect to database!");
	
	if($_REQUEST['xls']=='y') {
	HeaderingExcel("Ekspor_Excel.xls");
	}
	
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
		case 'show_list_email': show_list_email($con,$header_utama); break;
		case 'show_list_antrian': show_list_antrian($header_utama); break;
		case 'show_email_sukses': show_email_sukses($header_utama); break;
		case 'show_email_gagal': show_email_gagal($header_utama); break;
		case 'show_email_terbaca': show_email_terbaca($header_utama); break;
		case 'show_email_sample_sukses': show_email_sample_sukses($header_utama); break;
		case 'show_email_sample_gagal': show_email_sample_gagal($header_utama); break;
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
		$blth = $_REQUEST['blth'];
		$cycle = $_REQUEST['cycle'];
		$per_send = explode('|', $_REQUEST['periode_kirim']);
		$sent_from = $per_send[0];
		$sent_to = $per_send[1];
		$get_respon ='';
		

		
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
                        	<table align="center" border="1" cellpadding="2" cellspacing="0" width="100%">
                            	<tr height="30">
                                	<td width="4%" align="center" rowspan="2" bgcolor="#79BAEC"><b>No</b></td>
                                	<td align="center" rowspan="2" bgcolor="#79BAEC"><b>Tanggal Loading</b></td>
                                	<td align="center" rowspan="2" bgcolor="#79BAEC"><b>Nama File</b></td>
                                	<td align="center" rowspan="2" bgcolor="#79BAEC"><b>Jumlah PDF</b></td>
                                	<td align="center" rowspan="2" bgcolor="#79BAEC"><b>Jumlah Halaman</b></td>
                                	<td align="center" colspan="2" width="10%" bgcolor="#79BAEC"><b>PDF</b></td>
									<td align="center" rowspan="2" bgcolor="#79BAEC"><b>Jumlah Hlm Berbayar</b></td>
									<td align="center" rowspan="2" bgcolor="#79BAEC"><b>Total Email</b></td>
                                	<td align="center" rowspan="2" bgcolor="#79BAEC"><b>Email di Antrian</b></td>
                                	<td align="center" rowspan="2" bgcolor="#79BAEC"><b>Email Sukses</b></td>
                                	<td align="center" rowspan="2" bgcolor="#79BAEC"><b>Email Gagal</b></td>
									<td align="center" rowspan="2" bgcolor="#79BAEC"><b>Email Terbaca</b></td>
                                	<td align="center" rowspan="2" bgcolor="#79BAEC"><b>Sample Sukses</b></td>
                                	<td align="center" rowspan="2" bgcolor="#79BAEC"><b>Sample Gagal</b></td>
                                </tr>
								<tr bgcolor="#add8e6">
								<td width="5%" align="center"><b>(<=4hlm)</b></td>
								<td width="5%" align="center"><b>(>4hlm)</b></td>
								</tr>
                                <?php
									$qry_data = get_RowData($blth, $flagtrans, $cycle, $sent_from, $sent_to);
									while($row_data = pg_fetch_array($qry_data)){
										$i++;
										$m_loading_id = $row_data['m_loading_id'];
										$count_asg = $row_data['jml_antrian'] + $row_data['email_sukses'] + $row_data['email_gagal'];
										if ($count_asg < $row_data['jml_pdf_grts']){
											$msg_warning ='<img src="../images/warning.gif" title="Belum di proses"/>';
										}else{
											$msg_warning ='';
										}
									?>
									<tr height="30">
										<td align="center"><?php echo $i?></td>
										<td align="center"><?php echo $row_data['create_date']?></td>
										<td align="center"><?php echo $row_data['loading_file']?></td>
										<td align="right"><a onClick="window.open('script_summary_detail.php?act=show_list_pdf&tipe=1&pr=<?=$flagtrans?>&m_loading_id=<?php echo $m_loading_id?>')"><?php echo number_format($row_data['jml_pdf'],0,'',',')?></a>&nbsp;&nbsp;</td>
										<td align="right"><a onClick="window.open('script_summary_detail.php?act=show_list_pdf&tipe=1&pr=<?=$flagtrans?>&m_loading_id=<?php echo $m_loading_id?>')"><?php echo number_format($row_data['jml_lembar'],0,'',',')?></a>&nbsp;&nbsp;</td>
										<td align="right" bgcolor="#afeeee"><a onClick="window.open('script_summary_detail.php?act=show_list_pdf&tipe=2&pr=<?=$flagtrans?>&m_loading_id=<?=$m_loading_id?>')"><?php echo number_format($row_data['jml_pdf_grts'],0,'',',');?></a>&nbsp;&nbsp;</td>
										<td align="right" bgcolor="#afeeee"><a onClick="window.open('script_summary_detail.php?act=show_list_pdf&tipe=3&hlm=yes&pr=<?=$flagtrans?>&m_loading_id=<?=$m_loading_id?>')"><?php echo number_format($row_data['jml_pdf_bayar'],0,'',',');?></a>&nbsp;&nbsp;</td>
										<td align="right"><a onClick="window.open('script_summary_detail.php?act=show_list_pdf&tipe=3&hlm=yes&pr=<?=$flagtrans?>&m_loading_id=<?php echo $m_loading_id?>')"><?php echo number_format($row_data['jml_hlm_bayar'],0,'',',')?></a>&nbsp;&nbsp;</td>
										<td align="right"><a onClick="window.open('script_summary_detail.php?act=show_list_email&tipe=2&pr=<?=$flagtrans?>&m_loading_id=<?php echo $m_loading_id?>')"><?php echo number_format($row_data['total_email'],0,'',',')?></a>&nbsp;&nbsp;</td>
										<td align="right"><a onClick="window.open('script_summary_detail.php?act=show_list_antrian&pr=<?=$flagtrans?>&m_loading_id=<?php echo $m_loading_id?>')"><?php echo number_format($row_data['jml_antrian'],0,'',',')?></a>&nbsp;&nbsp;</td>
										<td align="right"><a onClick="window.open('script_summary_detail.php?act=show_email_sukses&pr=<?=$flagtrans?>&m_loading_id=<?php echo $m_loading_id?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>')"><?php echo number_format($row_data['email_sukses'],0,'',',')?></a>&nbsp;&nbsp;<?php echo $msg_warning; ?></td>
										<td align="right"><a onClick="window.open('script_summary_detail.php?act=show_email_gagal&pr=<?=$flagtrans?>&m_loading_id=<?php echo $m_loading_id?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>')"><?php echo number_format($row_data['email_gagal'],0,'',',')?></a>&nbsp;&nbsp;<?php echo $msg_warning; ?></td>
									<td align="right"><a onClick="window.open('script_summary_detail.php?act=show_email_terbaca&pr=<?=$flagtrans?>&m_loading_id=<?php echo $m_loading_id?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>')"><?php echo number_format($row_data['email_terbaca'],0,'',',')?></a>&nbsp;&nbsp;</td>
										<td align="right"><a onClick="window.open('script_summary_detail.php?act=show_email_sample_sukses&pr=<?=$flagtrans?>&m_loading_id=<?php echo $m_loading_id?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>')"><?php echo number_format($row_data['sample_sukses'],0,'',',')?></a>&nbsp;&nbsp;</td>
										<td align="right"><a onClick="window.open('script_summary_detail.php?act=show_email_sample_gagal&pr=<?=$flagtrans?>&m_loading_id=<?php echo $m_loading_id?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>')"><?php echo number_format($row_data['sample_gagal'],0,'',',')?></a>&nbsp;&nbsp;</td>
									</tr>
									<?php
										$m_loading_id_all .=''.$m_loading_id.'|';
										$total_pdf = $total_pdf + $row_data['jml_pdf'];
										$total_lembar = $total_lembar + $row_data['jml_lembar'];
										$total_total_email = $total_total_email + $row_data['total_email'];
										$total_email = $total_email + $row_data['jml_pdf_grts'];
										$total_hal_bayar = $total_hal_bayar + $row_data['jml_hlm_bayar'];
										$total_email_bayar = $total_email_bayar + $row_data['jml_pdf_bayar'];
										$total_antrian = $total_antrian + $row_data['jml_antrian'];
										$total_e_sukses = $total_e_sukses + $row_data['email_sukses'];
										$total_e_gagal = $total_e_gagal + $row_data['email_gagal'];
										$total_e_terbaca = $total_e_terbaca + $row_data['email_terbaca'];
										$total_s_sukses = $total_s_sukses + $row_data['sample_sukses'];
										$total_s_gagal = $total_s_gagal + $row_data['sample_gagal'];
									}
								?>
                                <tr height="30" style="font-weight:bold" bgcolor="#87ceeb">
                                	<td align="center" colspan="3">T O T A L</td>
                                	<td align="right"><a onClick="window.open('script_summary_detail.php?act=show_list_pdf&tipe=1&pr=<?=$flagtrans?>&blth=<? echo $blth?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&m_loading_id=<?php echo trim($m_loading_id_all,'|')?>')"><?php echo number_format($total_pdf,0,'',',')?></a>&nbsp;&nbsp;</td>
                                	<td align="right"><?php echo number_format($total_lembar,0,'',',')?>&nbsp;&nbsp;</td>
                                	<td align="right" bgcolor="#87ceeb"><a onClick="window.open('script_summary_detail.php?act=show_list_pdf&tipe=2&pr=<?=$flagtrans?>&blth=<? echo $blth?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&m_loading_id=<?php echo trim($m_loading_id_all,'|')?>')"><?php echo number_format($total_email,0,'',',');?></a>&nbsp;&nbsp;</td>
                                	<td align="right" bgcolor="#87ceeb"><a onClick="window.open('script_summary_detail.php?act=show_list_pdf&tipe=3&pr=<?=$flagtrans?>&blth=<? echo $blth?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&m_loading_id=<?php echo trim($m_loading_id_all,'|')?>')"><?php echo number_format($total_email_bayar,0,'',',');?></a>&nbsp;&nbsp;</td>
									<td align="right"><a onClick="window.open('script_summary_detail.php?act=show_list_pdf&tipe=3&hlm=yes&pr=<?=$flagtrans?>&blth=<? echo $blth?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&m_loading_id=<?php echo trim($m_loading_id_all,'|')?>')"><?php echo number_format($total_hal_bayar,0,'',',')?></a>&nbsp;&nbsp;</td>
                                	<td align="right" bgcolor="#87ceeb"><a onClick="window.open('script_summary_detail.php?act=show_list_email&pr=<?=$flagtrans?>&tipe=2&blth=<? echo $blth?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&m_loading_id=<?php echo trim($m_loading_id_all,'|')?>')"><?php echo number_format($total_total_email,0,'',',');?></a>&nbsp;&nbsp;</td>
                                	<td align="right"><a onClick="window.open('script_summary_detail.php?act=show_list_antrian&pr=<?=$flagtrans?>&blth=<? echo $blth?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&m_loading_id=<?php echo trim($m_loading_id_all,'|')?>')"><?php echo number_format($total_antrian,0,'',',')?></a>&nbsp;&nbsp;</td>
                                	<td align="right"><a onClick="window.open('script_summary_detail.php?act=show_email_sukses&pr=<?=$flagtrans?>&blth=<? echo $blth?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&m_loading_id=<?php echo trim($m_loading_id_all,'|')?>')"><?php echo number_format($total_e_sukses,0,'',',')?></a>&nbsp;&nbsp;</td>
                                	<td align="right"><a onClick="window.open('script_summary_detail.php?act=show_email_gagal&pr=<?=$flagtrans?>&blth=<? echo $blth?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&m_loading_id=<?php echo trim($m_loading_id_all,'|')?>')"><?php echo number_format($total_e_gagal,0,'',',')?></a>&nbsp;&nbsp;</td>
									<td align="right"><a onClick="window.open('script_summary_detail.php?act=show_email_terbaca&pr=<?=$flagtrans?>&blth=<? echo $blth?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&m_loading_id=<?php echo trim($m_loading_id_all,'|')?>')"><?php echo number_format($total_e_terbaca,0,'',',')?></a>&nbsp;&nbsp;</td>
                                	<td align="right"><a onClick="window.open('script_summary_detail.php?act=show_email_sample_sukses&pr=<?=$flagtrans?>&blth=<? echo $blth?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&m_loading_id=<?php echo trim($m_loading_id_all,'|')?>')"><?php echo number_format($total_s_sukses,0,'',',')?></a>&nbsp;&nbsp;</td>
                                	<td align="right"><a onClick="window.open('script_summary_detail.php?act=show_email_sample_gagal&pr=<?=$flagtrans?>&blth=<? echo $blth?><?php if (!empty($_GET['periode_kirim'])){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&m_loading_id=<?php echo trim($m_loading_id_all,'|')?>')"><?php echo number_format($total_s_gagal,0,'',',')?></a>&nbsp;&nbsp;</td>
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
		$tipe = $_REQUEST['tipe'];
		$hlm = $_REQUEST['hlm'];
		
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
		if($tipe == 1){
			$wh = "";
		}elseif($tipe == 2){
			$wh = " AND jml_hlm <= 4";
		}elseif($tipe == 3){
			$wh = " AND jml_hlm > 4";
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
								<tr height="20">
                                	<td align="left"><b>Nama File : <?=$nama_file?></b></td>
                                </tr>
								<?php
								if (!empty($sent_from)){
									$label = ''.$sent_from.' s/d '.$sent_to.'';
								?>
									<tr height="30">
										<td><b>Periode: <?=$label?></b></td>
										<td align="right"><a onClick="window.open('script_summary_detail.php?act=show_list_pdf&tipe=<?php echo $tipe?>&hlm=<?php echo $hlm?><?php if (!empty($sent_from)){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>&xls=y')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a></td>
									</tr>
								<?php
								}else{
									if ($gtotal != true){$label='Periode: '.$blth;}
								?>
									<tr height="30">
										<td><b><?=$label?></b></td>
										<td align="right"><a onClick="window.open('script_summary_detail.php?act=show_list_pdf&tipe=<?php echo $tipe?>&hlm=<?php echo $hlm?>&blth=<?php echo $blth?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>&xls=y')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a></td>
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
                                	<td align="left">&nbsp;<b>Nama</b></td>
                                	<td align="center"><b>Nama PDF</b></td>
                                	<td align="center"><b>Password PDF</b></td>
                                	<td align="center"><b>Jumlah Halaman</b></td>
									<?php
									if($hlm == "yes"){
										?>
										<td align="center"><b>Jumlah Halaman Berbayar</b></td>
										<?php
									}
									?>
                                </tr>
                                <?php
									$i = 0;
									$sql_data = "
										SELECT d.flagtrans, d.blth, d.nomor_customer, d.nama, d.pdf_name, d.password_pdf, d.jml_hlm, d.nama_file, d.nomor_rekening, (d.jml_hlm - 4) as jml_hlm_byr
										FROM detail d
										".$condv."
										".$wh."
										ORDER BY d.m_loading_id, d.nomor_rekening ASC
									";
									$qry_data = pg_query($sql_data) or die('ERROR select data: '.$sql_data);//echo $sql_data;
									while($row_data = pg_fetch_array($qry_data)){
										$i++;
										//SETTING LOKASI FOLDER PDF
										$panjang_folder = strlen(trim($row_data['nama_file']));
										$balik_namafolder = strrev($row_data['nama_file']);
										$posisi_titik = strpos($balik_namafolder,".");
										$balik_namafolder = substr($balik_namafolder,$posisi_titik+1,$panjang_folder);
										$namafolder = strrev($balik_namafolder);
										
										$total_jml_hlm = $total_jml_hlm + $row_data['jml_hlm'];
										$total_jml_hlm_byr = $total_jml_hlm_byr + $row_data['jml_hlm_byr'];
								?>
                                <tr height="30">
                                	<td align="center"><?php echo $i?></td>
									<?php
										if ($gtotal == true){
											echo '<td align="center">&nbsp;'.$row_data['nama_file'].'</td>';
										}
									?>
                                	<td align="center">&nbsp;<?php echo $row_data['nomor_rekening']?></td>
                                	<td align="left">&nbsp;<?php echo $row_data['nama']?></td>
                                	<td align="center"><?php
									$pdf_location = "../pdf/".$row_data['blth']."/".$namafolder."/";?>
									<a href="<?=$pdf_location.$row_data['pdf_name'].".pdf"?>" target="blank"><?echo $row_data['pdf_name'].".pdf"?></a>
									</td>
                                	<td align="center"><?php echo $row_data['password_pdf']?></td>
                                	<td align="right"><?php echo number_format($row_data['jml_hlm'],0,',','')?></td>
									<?php
									if($hlm == "yes"){
										?>
										<td align="right"><?php echo number_format($row_data['jml_hlm_byr'],0,',','')?></td>
										<?php
									}
									?>
                                </tr>
                                <?php
									}
								if ($gtotal == true){
									$cols = 6;
								}else{
									$cols = 5;
								}
								?>
								<tr height="30">
                                	<td align="center" colspan="<?php echo $cols?>"><b>TOTAL</b></td>
                                	<td align="right"><b><?php echo number_format($total_jml_hlm,0,',','')?></b></td>
									<?php
									if($hlm == "yes"){
										?>
										<td align="right"><b><?php echo number_format($total_jml_hlm_byr,0,',','')?></b></td>
										<?php
									}
									?>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </body>
        </html>
        <?php
	}
	
	
	
	
	function show_list_email($con,$header_utama){
		$m_loading_id = $_REQUEST['m_loading_id'];
		$blth = $_REQUEST['blth'];
		$tipe = $_REQUEST['tipe'];
		$flagtrans = $_REQUEST['pr'];
		
		switch($tipe){
		case 0:
		$vw_email = "vw_email_gratis vw";
		$ket_vw_email = "( 1-4 Halaman )";
		break;
		case 1:
		$vw_email = "vw_email_bayar vw";
		$ket_vw_email = "( 5 Halaman ke Atas )";
		break;
		case 2:
		$vw_email = "vw_email vw";
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
			$condv = "WHERE vw.m_loading_id in (".str_replace('|',',', $m_loading_id).")";
			$condv_nama = "WHERE m_loading_id in (".str_replace('|',',', $m_loading_id).")";
			}else{
			$condv = "WHERE vw.m_loading_id = $m_loading_id";
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
		
		$vetable = '
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>List Email</title>
            </head>
            <? require_once("../include/script.php"); ?>
            <body>
            	<table align="center" border="0" width="1000">
                	<tr>
                    	<td>
                        	<table align="center" border="0" cellpadding="2" cellspacing="0" width="100%">
                            	<tr height="30">
                                	<td style="font-size:12pt;font-weight:bold;" colspan="4" align="center" valign="middle">
                                    	LIST EMAIL<br/>'.$ket_vw_email.'
                                    </td>
                                </tr>
                                <tr height="20">
                                	<td colspan="3" ><b>Tanggal: '.date("d-m-Y H:i").'</b></td>
                                    <td align="right"></td>
                                </tr>
								<tr height="20">
                                	<td colspan="2"><b><u>'.$header_utama.'</u></b></td>
                                </tr>
                             <tr height="20">
                                	<td colspan="3" ><b>Nama File: '.$nama_file.'</b></td>
									</tr>
                                    ';?>
									<?php
									if ($gtotal != true){$label='Periode: '.$blth;}
								$vetable .= '
									<tr>
									<td colspan="2"><b>'.$label.'</b></td>
									<td align="right">
									<a onClick="window.open(\'script_summary_detail.php?act=show_list_email&pr='.$flagtrans.'&m_loading_id='.$m_loading_id.'&xls=y&tipe='.$tipe.'\')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a>
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
                                	<td width="4%" align="center"><b>No</b></td>
                                	<td align="center"><b>No Kartu</b></td>
                                	<td align="left">&nbsp;<b>Nama</b></td>
                                	<td align="left">&nbsp;<b>Email</b></td>
                                	<td align="left">&nbsp;<b>Jml Hlm</b></td>
                                	<td align="left">&nbsp;<b>File PDF</b></td>
                                </tr>';
									$i = 0;

									$sql_data = "
										SELECT *
										FROM $vw_email
										$condv
										ORDER BY nomor_rekening, nama, email";
									$qry_data = pg_query($sql_data) or die('ERROR select data: '.$sql_data);
									while($row_data = pg_fetch_array($qry_data)){
									//SETTING LOKASI FOLDER PDF
										$panjang_folder = strlen(trim($row_data['nama_file']));
										$balik_namafolder = strrev($row_data['nama_file']);
										$posisi_titik = strpos($balik_namafolder,".");
										$balik_namafolder = substr($balik_namafolder,$posisi_titik+1,$panjang_folder);
										$namafolder = strrev($balik_namafolder);
										
										$i++;
										$pdf_location = "../pdf/".$row_data['blth']."/".$namafolder."/";
										$link_pdf = $pdf_location.$row_data['pdf_name'].".pdf";
								$vetable .='
                                <tr height="30">
                                	<td align="center">'.$i.'</td>
                                	<td align="center">&nbsp;'.$row_data['nomor_rekening'].'</td>
                                	<td align="left">&nbsp;'.$row_data['nama'].'</td>
                                	<td align="left">&nbsp;'.$row_data['email'].'</td>
                                	<td align="left">&nbsp;'.$row_data['jml_hlm'].'</td>
                                	<td align="left">&nbsp;<a href="'.$link_pdf.'" target="blank">'.$row_data['pdf_name'].".pdf".'</a></td>
                                </tr>';
								}
								$vetable .= '
                            </table>
                        </td>
                    </tr>
                </table>
            </body>
        </html>';
        echo $vetable;
		
		/** FUNGSI UNTUK MENGIRIM EMAIL (DIGUNAKAN DI AXA) **/
		/*
		$sqlmail="
			SELECT email_from, email_host, from_name, produk FROM mail_server a 
			JOIN template_email b ON a.mail_server_id=b.mail_server_id 
			JOIN mproduk c ON c.flagtrans=b.flagtrans
			WHERE b.flagtrans='".$flagtrans."'";
		$qrymail=pg_query($con,$sqlmail)or die('Err: $sqlmail');
		$user = $_SESSION["user_id"];
		$ip_user = $_SERVER['REMOTE_ADDR'];
		$ip_server = $_SERVER['SERVER_ADDR'];				
		$browser = $_SERVER['HTTP_USER_AGENT'];												
						
		if (preg_match('|MSIE ([0-9].[0-9]{1,2})|',$browser ,$matched)) {
			$browser_version=$matched[1];
			$browser = 'IE';
		} elseif (preg_match( '|Opera ([0-9].[0-9]{1,2})|',$browser ,$matched)) {
			$browser_version=$matched[1];
			$browser = 'Opera';
		} elseif(preg_match('|Firefox/([0-9\.]+)|',$browser ,$matched)) {
			$browser_version=$matched[1];
			$browser = 'Firefox';
		} elseif(preg_match('|Safari/([0-9\.]+)|',$browser ,$matched)) {
			$browser_version=$matched[1];
			$browser = 'Safari';
		} else {
			$browser_version = 0;
			$browser= 'Other';
		}
		
		$vetable .= "
		<br />
		<br />
		<hr>
		<div id='user_info'>
			<b><u>User Info</u></b><br><br>			
			<strong>User : </strong>".$user."<br />
			<strong>IP Address User: </strong>".$ip_user."<br />
			<strong>IP Address Server: </strong>".$ip_server."<br />
			<strong>Browser : </strong>".$browser." ".$browser_version."<br />					
		</div>";
		
		while($rowmail=pg_fetch_array($qrymail)){
			$email_from	=$rowmail['email_from'];
			$email_host	=$rowmail['email_host'];
			$from_name	=$rowmail['from_name'];
			$produk		=$rowmail['produk'];

			$mail = new PHPMailer();
			$mail->IsSMTP();
			$mail->From = $email_from;
			$mail->FromName = $from_name;
			$mail->Host = $email_host;
			$mail->Mailer   = "smtp";
			//$mail->AddAddress('app_dev@indocorp.com', 'APPDEV');
			$mail->AddAddress('app_dev@indointernal.com', 'APPDEV');
			//$mail->AddAddress('crm@indocorp.com', 'CRM');
			
				$mail->Subject = 'Agent - List Email '.$blth.' / '.$produk;
				$mail->MsgHTML("<html><body>".$vetable."</body></html>");
				$mail->IsHTML(true);
					
						
			if($mail->Send()){	
				$mail->ClearAddresses();
				$mail->ClearAttachments();
				break;
			}
			
			$mail->ClearAddresses();
			$mail->ClearAttachments();
		}
		*/
		
	}
	
	function show_list_antrian($header_utama){
		$m_loading_id = $_REQUEST['m_loading_id'];
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['pr'];
		
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
										<td align="right"><a onClick="window.open('script_summary_detail.php?act=show_list_antrian<?php if (!empty($sent_from)){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>&xls=y')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a></td>
									</tr>
								<?php
								}else{
									if ($gtotal != true){$label='Periode: '.$blth;}
								?>
									<tr height="30">
										<td><b><?=$label?></b></td>
										<td align="right"><a onClick="window.open('script_summary_detail.php?act=show_list_antrian&blth=<?php echo $blth?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>&xls=y')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a></td>
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
								$sql_data = "
									SELECT ".$add_select." to_char(tgl_antrian,'DD-Mon-YYYY HH24:MI:SS')as tgl_antrian, d.nomor_rekening, a.nama, a.email, a.pdf_name,
										CASE
											WHEN to_char(b.tgl_jadwal,'dd-mm-yyyy hh24:mi:ss') is null THEN 'TIDAK TERJADWAL'
											ELSE to_char(b.tgl_jadwal,'dd-mm-yyyy hh24:mi:ss')
										END AS tgl_jadwal
									FROM antrian_email a
										LEFT JOIN m_jadwal b
											ON a.jadwal_id = b.jadwal_id
										JOIN detail d
											ON d.nomor_rekening = a.nomor_rekening AND d.m_loading_id = a.loading_id
									WHERE ".$condv."
									".$group_order."
									
								";//--a.nomor_customer, a.nomor_rekening, a.nama, a.email, a.pdf_name
								$qry_data = pg_query($sql_data) or die('ERROR select data: '.$sql_data);
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
                                	<td align="center"><?php echo $row_data['nomor_rekening']?>&nbsp;</td>
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
                </table>
            </body>
        </html>
        <?php
	}
	
	function show_email_sukses($header_utama){
		$m_loading_id = $_REQUEST['m_loading_id'];
		$per_send = explode('|', $_REQUEST['periode_kirim']);
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['pr'];
		
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
										<td align="right"><a onClick="window.open('script_summary_detail.php?act=show_email_sukses<?php if (!empty($sent_from)){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>&xls=y')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a></td>
									</tr>
								<?php
								}else{
									if ($gtotal == true){$label='Periode: '.$blth;}else{$label='Nama File: '.$nama_file;}
								?>
									<tr height="30">
										<td><b><?=$label?></b></td>
										<td align="right"><a onClick="window.open('script_summary_detail.php?act=show_email_sukses&blth=<?php echo $blth?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>&xls=y')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a></td>
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
                                	<td align="left">&nbsp;<b>Nama</b></td>
                                	<td align="center"><b>Alamat Email</b></td>
                                	<td align="center"><b>Tanggal Kirim</b></td>
                                	<td align="center"><b>View Template</b></td>
                                </tr>
                                <?php

								if ($gtotal == true){
									$condv = " AND a.loading_id in (".str_replace('|',',', $m_loading_id).")";
									$condv2 = " ";
								}else{
									$condv = " AND a.loading_id = $m_loading_id";
									$condv2 = " b.m_loading_id = $m_loading_id AND";
								}
								
								$i = 0;
								$sql_data = "SELECT a.nomor_rekening, a.email, a.date_email_send, a.antrian_id, a.ket_error, c.loading_file, b.nama
											FROM tr_email a 
											LEFT JOIN detail b ON a.nomor_rekening=b.nomor_rekening AND a.loading_id=b.m_loading_id
											LEFT JOIN m_loading c ON a.loading_id=c.m_loading_id
											WHERE a.email_callback IS NULL
											AND a.status_sample IS NOT TRUE 
											AND a.email_sukses = TRUE
											".$condv."
											GROUP BY a.nomor_rekening, a.email, a.date_email_send, a.antrian_id, a.ket_error, b.nama, c.loading_file
											ORDER BY a.antrian_id ";
								//echo $sql_data;
								$qry_data = pg_query($sql_data) or die('ERROR select data: '.$sql_data);
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
                                	<td align="center"><?php echo '&nbsp;'.$row_data['nomor_rekening']?></td>
                                	<td align="left">&nbsp;<?php echo $row_data['nama']?></td>
                                	<td align="left"><?php echo $row_data['email']?></td>
                                	<td align="center"><?php echo $row_data['date_email_send']?></td>
                                    <td align="center"><a onClick="view_template(<?=$antrian_id?>)"><img title="view template" src="../images/icons/icon_view.gif" /></a></td>
                                </tr>
                                <?php
									}
								?>
                            </table>
                        </td>
                    </tr>
                </table>
            </body>
        </html>
        <?php
	}
	
	function show_email_gagal($header_utama){
		$m_loading_id = $_REQUEST['m_loading_id'];
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['pr'];
		
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
										<td align="right"><a onClick="window.open('script_summary_detail.php?act=show_email_gagal<?php if (!empty($sent_from)){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>&xls=y')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a></td>
									</tr>
								<?php
								}else{
									if ($gtotal == true){$label='Periode: '.$blth;}else{$label='Nama File: '.$nama_file;}
								?>
									<tr height="30">
										<td><b><?=$label?></b></td>
										<td align="right"><a onClick="window.open('script_summary_detail.php?act=show_email_gagal&blth=<?php echo $blth?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>&xls=y')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a></td>
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
								$sql_data = "SELECT a.nomor_rekening, a.email, a.date_email_send, a.date_email_callback, a.antrian_id, a.ket_error, c.loading_file, b.nama
											FROM tr_email a 
											LEFT JOIN detail b ON a.nomor_rekening=b.nomor_rekening AND a.loading_id=b.m_loading_id
											LEFT JOIN m_loading c ON a.loading_id=c.m_loading_id 
											WHERE a.email_callback IS NOT NULL 
											AND a.status_sample IS NOT TRUE 
											".$condv."
											GROUP BY a.nomor_rekening, a.email, a.date_email_send, a.date_email_callback, a.antrian_id, a.ket_error, c.loading_file, b.nama
											ORDER BY a.antrian_id ";
								$qry_data = pg_query($sql_data) or die('ERROR select data: '.$sql_data);
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
                </table>
            </body>
        </html>
        <?php
	}
	
	function show_email_terbaca($header_utama){
		$m_loading_id = $_REQUEST['m_loading_id'];
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['pr'];
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
                <title>LIST EMAIL SUKSES TERBACA</title>
            </head>
            <? require_once("../include/script.php"); ?>
            <body>
            	<table align="center" border="0" width="1000">
                	<tr>
                    	<td>
                        	<table align="center" border="0" cellpadding="2" cellspacing="0" width="100%">
                            	<tr height="30">
                                	<td class="title" colspan="5" align="center" valign="middle">
                                    	LIST EMAIL SUKSES TERBACA
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
										<td align="right"><a onClick="window.open('script_summary_detail.php?act=show_email_terbaca<?php if (!empty($sent_from)){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>&xls=y')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a></td>
									</tr>
								<?php
								}else{
									if ($gtotal == true){$label='Periode: '.$blth;}else{$label='Nama File: '.$nama_file;}
								?>
									<tr height="30">
										<td><b><?=$label?></b></td>
										<td align="right"><a onClick="window.open('script_summary_detail.php?act=show_email_terbaca&blth=<?php echo $blth?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>&xls=y')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a></td>
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
									<td align="center"><b>Tanggal Baca</b></td>
									<!--<td align="center"><b>Keterangan</b></td>-->
									<td align="center"><b>View Template</b></td>
                                </tr>
                                <?php
								if ($gtotal == true){
									$condv = " AND loading_id in (".str_replace('|',',', $m_loading_id).")";
									$condv2 = " b.m_loading_id in (".str_replace('|',',', $m_loading_id).")";
								}else{
									$condv = " AND loading_id = $m_loading_id";
									$condv2 = " b.m_loading_id = $m_loading_id";
								}
								$i = 0;
								$sql_data = "SELECT a.nomor_rekening, a.email, a.date_email_send, a.tgl_read, a.antrian_id,
											(SELECT b.nama FROM detail b WHERE ".$condv2." AND b.nomor_rekening = a.nomor_rekening)AS nama
											FROM tr_email a, detail b
											WHERE a.tgl_read IS NOT NULL 
											AND a.status_sample IS NOT TRUE 
											".$condv."
											GROUP BY a.nomor_rekening, a.email, a.date_email_send, a.tgl_read, a.antrian_id";
								//echo $sql_data;
								$qry_data = pg_query($sql_data) or die('ERROR select data: '.$sql_data);
								while($row_data = pg_fetch_array($qry_data)){
									$i++;
									$antrian_id = $row_data['antrian_id'];
								?>
                                <tr height="30">
                                	<td align="center"><?php echo $i?></td>
									<?php
									if ($gtotal == true){
										echo '<td align="center">'.$row_data['nama_file'].'</td>';
									}
									?>
                                	<td align="center">&nbsp;<?php echo $row_data['nomor_rekening']?></td>
                                	<td align="left"><?php echo $row_data['nama']?></td>
                                	<td align="left"><?php echo $row_data['email']?></td>
                                	<td align="center"><?php echo date("d-m-Y H:i",strtotime($row_data['date_email_send']))?></td>
									<td align="center"><?php echo date("d-m-Y H:i",strtotime($row_data['tgl_read']))?></td>
									<?php /* <td align="left"><?php echo $row_data['keterangan']?> -- 
                                        <?php
										if($_REQUEST['xls']=='y'){
											echo $row_data['body_email'];
										}else{
											?>
											<a onClick="view_bodymailterbaca('<?php echo $row_data['read_email_id']?>')">detail</a>
											<?php
										}
										?>
									</td> */ ?>
									<td align="center"><a onClick="view_template(<?=$antrian_id?>)"><img title="view template" src="../images/icons/icon_view.gif" /></a></td>
                                </tr>
                                <?php
									}
								?>
                            </table>
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
										<td align="right"><a onClick="window.open('script_summary_detail.php?act=show_email_sample_sukses<?php if (!empty($sent_from)){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>&xls=y')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a></td>
									</tr>
								<?php
								}else{
									if ($gtotal == true){$label='Periode: '.$blth;}else{$label='Nama File: '.$nama_file;}
								?>
									<tr height="30">
										<td><b><?=$label?></b></td>
										<td align="right"><a onClick="window.open('script_summary_detail.php?act=show_email_sample_sukses&blth=<?php echo $blth?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>&xls=y')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a></td>
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
								$sql_data = "SELECT a.nomor_rekening, a.email, a.date_email_send, a.antrian_id, a.ket_error, c.loading_file, b.nama
											FROM tr_email a 
											LEFT JOIN detail b ON a.nomor_rekening=b.nomor_rekening
											LEFT JOIN m_loading c ON a.loading_id=c.m_loading_id
											WHERE a.email_callback IS NULL
											AND a.status_sample IS TRUE 
											AND a.email_sukses = TRUE
											".$condv."
											GROUP BY a.nomor_rekening, a.email, a.date_email_send, a.antrian_id, a.ket_error, b.nama, c.loading_file
											ORDER BY a.antrian_id ";
								//echo $sql_data;
								$qry_data = pg_query($sql_data) or die('ERROR select data: '.$sql_data);
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
                                    <td align="center"><a onClick="view_template(<?=$antrian_id?>)"><img title="view template" src="../images/icons/icon_view.gif" /></a></td>
                                </tr>
                                <?php
									}
								?>
                            </table>
                        </td>
                    </tr>
                </table>
            </body>
        </html>
        <?php
	}
	

	
	function show_email_sample_gagal($xls,$header_utama){
		$m_loading_id = $_REQUEST['m_loading_id'];
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['pr'];
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
										<td align="right"><a onClick="window.open('script_summary_detail.php?act=show_email_gagal<?php if (!empty($sent_from)){echo '&periode_kirim='.$_GET['periode_kirim'];}?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>&xls=y')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a></td>
									</tr>
								<?php
								}else{
									if ($gtotal == true){$label='Periode: '.$blth;}else{$label='Nama File: '.$nama_file;}
								?>
									<tr height="30">
										<td><b><?=$label?></b></td>
										<td align="right"><a onClick="window.open('script_summary_detail.php?act=show_email_gagal&blth=<?php echo $blth?>&pr=<?php echo $flagtrans;?>&m_loading_id=<?php echo $m_loading_id?>&xls=y')"><img height="40px" title="download excel" src="../images/icons/xls_icon.gif" /></a></td>
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
								$sql_data = "SELECT a.nomor_rekening, a.email, a.date_email_send, a.date_email_callback, a.antrian_id, a.ket_error, c.loading_file, b.nama
											FROM tr_email a 
											LEFT JOIN detail b ON a.nomor_rekening=b.nomor_rekening 
											LEFT JOIN m_loading c ON a.loading_id=c.m_loading_id 
											WHERE a.email_callback IS NOT NULL 
											AND a.status_sample IS TRUE 
											".$condv."
											GROUP BY a.nomor_rekening, a.email, a.date_email_send, a.date_email_callback, a.antrian_id, a.ket_error, c.loading_file, b.nama
											ORDER BY a.antrian_id ";
								$qry_data = pg_query($sql_data) or die('ERROR select data: '.$sql_data);
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
                </table>
            </body>
        </html>
        <?php
	}
	
	function view_template(){
		$antrian_id = $_REQUEST['antrian_id'];
		?>
        <iframe id="frame_view" src="script_summary_detail.php?act=load_template&antrian_id=<?=$antrian_id?>" height="670" width="900" frameborder="0" style="background-color:#fff">
        </iframe>
        <?
	}
	
	function load_template(){
		$antrian_id = $_REQUEST['antrian_id'];
		
		$sql_attach = "SELECT a.m_attach_file_id, b.location_file, b.name_file, b.ukuran
						FROM antrian_email_attach_file a
							INNER JOIN m_attach_file b
								ON a.m_attach_file_id = b.m_attach_file_id
						WHERE a.antrian_id = ".$antrian_id;
		$qry_attach = pg_query($sql_attach) or die('ERROR select attach: '.$sql_attach);
		
		$sql_show = "SELECT a.email, a.status_sample,
						b.from_name, b.reply_to_email, b.subject_email, b.isi_email, b.flagtrans,
						c.email_from,
						d.alamat1, d.alamat2, d.alamat3, d.blth, d.nama_file, d.nama, d.pdf_name, d.password_pdf
					FROM tr_email a
						INNER JOIN template_email b
							ON a.template_id = b.template_email_id
						INNER JOIN mail_server c
							ON b.mail_server_id = c.mail_server_id
						INNER JOIN detail d
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
			$blth = $row_show['blth'];
		
			if($status_sample=='t' || $status_sample=='TRUE'){
				//$ganti = "<hr>kalimat ini hanya muncul untuk email sample saja.<p>password untuk dokumen ini adalah ".$password_pdf;
				$ganti = "<hr>kalimat ini hanya muncul untuk email sample saja.<p>Password untuk dokumen ini adalah ".$password_pdf . "<br> <br>Periode: " .$blth	. "<br> Cycle : ".$nama_file;
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
		$blth				= $row_show['blth'];
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
										
		$pdf_location = "../pdf/".$blth."/".$namafolder."/";
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
                            $location_file = $row_attach['location_file'];
                            $name_file = $row_attach['name_file'];
                            $ukuran = $row_attach['ukuran'];
                        	echo '<a href="'.$location_file.$name_file.'"><input type="button" class="button" value="'.$name_file.' ( '.$ukuran.' Kb )" /></a>';
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
					//	imap_delete($mbox, $i);
				}else{
					$header = pg_escape_string($header_info->senderaddress);
					$body = pg_escape_string(imap_body($mbox, $i));
					
					$sql = " insert into bounce_inbox(
									header_bounce, body_bounce, date_bounce
								)values(
									'$header'::varchar(100), '$body'::varchar(400), now()
								)";
					@pg_query($sql);
					//	imap_delete($mbox, $i);
				}
			}
			
			//imap_expunge($mbox);
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
		
		include $curloc.'PHPExcel.php';
		include $curloc.'PHPExcel/Writer/Excel5.php';
		
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("App Dev");
		$objPHPExcel->getProperties()->setLastModifiedBy("Estatement");
		$objPHPExcel->getProperties()->setTitle("Estatement");
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
		
		$objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow(0,1,14,1);
		
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
		
		$judul = judul_ex_xls($menuid);
		$objPHPExcel->getActiveSheet()->SetCellValue('A1',$judul);
		
		$objPHPExcel->getActiveSheet()->SetCellValue('B3',"Periode");
		
		$blth_m = datemonth_inttostr(substr($blth,0,2));
		$objPHPExcel->getActiveSheet()->SetCellValue('C3',''.$blth_m.' '.substr($blth, -10).'');
		
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
		'A'.$y_post.':O'.$y_post.''
		);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$y_post.':O'.$y_post.'')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('ffdfdfdf');
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
		$objPHPExcel->getActiveSheet()->getStyle('M'.$y_post.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('N'.$y_post.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('O'.$y_post.'')->applyFromArray($styleArray);
		
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$y_post.'',"No");
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$y_post.'',"Tanggal Loading");
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$y_post.'',"Nama File");
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$y_post.'',"Jumlah PDF");
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$y_post.'',"Jumlah Halaman");
		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$y_post.'',"Jumlah PDF (<=4hlm)");
		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$y_post.'',"Jumlah PDF (>4hlm)");
		$objPHPExcel->getActiveSheet()->SetCellValue('H'.$y_post.'',"Jumlah Hlm Berbayar");
		$objPHPExcel->getActiveSheet()->SetCellValue('I'.$y_post.'',"Total Email");
		$objPHPExcel->getActiveSheet()->SetCellValue('J'.$y_post.'',"Email di Antrian");
		$objPHPExcel->getActiveSheet()->SetCellValue('K'.$y_post.'',"Email Sukses");
		$objPHPExcel->getActiveSheet()->SetCellValue('L'.$y_post.'',"Email Gagal");
		$objPHPExcel->getActiveSheet()->SetCellValue('M'.$y_post.'',"Email Terbaca");
		$objPHPExcel->getActiveSheet()->SetCellValue('N'.$y_post.'',"Sample Sukses");
		$objPHPExcel->getActiveSheet()->SetCellValue('O'.$y_post.'',"Sample Gagal");
		
		$getRowData_dtail = get_RowData($blth, $flagtrans, $cycle, $sent_from, $sent_to);
		$not=1;
		while($row_data = pg_fetch_array($getRowData_dtail)){
		$atrow=$not + $y_post;
		
		$t_jml_pdf += $row_data['jml_pdf'];
		$t_jml_lembar += $row_data['jml_lembar'];
		$t_jml_email += $row_data['jml_pdf_grts'];
		$t_jml_email += $row_data['jml_pdf_grts'];
		$t_jml_email_bayar += $row_data['jml_pdf_bayar'];
		$t_jml_hlm_bayar += $row_data['jml_hlm_bayar'];
		$t_jml_total_email += $row_data['total_email'];
		$t_jml_antrian += $row_data['jml_antrian'];
		$t_email_sukses += $row_data['email_sukses'];
		$t_email_gagal += $row_data['email_gagal'];
		$t_email_terbaca += $row_data['email_terbaca'];
		$t_sample_sukses += $row_data['sample_sukses'];
		$t_sample_gagal += $row_data['sample_gagal'];
		if (empty($row_data['create_date'])){$row_data['create_date']=0;}
		if (empty($row_data['loading_file'])){$row_data['loading_file']=0;}
		if (empty($row_data['jml_pdf'])){$row_data['jml_pdf']=0;}
		if (empty($row_data['jml_lembar'])){$row_data['jml_lembar']=0;}
		if (empty($row_data['jml_pdf_grts'])){$row_data['jml_pdf_grts']=0;}
		if (empty($row_data['jml_pdf_bayar'])){$row_data['jml_pdf_bayar']=0;}
		if (empty($row_data['jml_hlm_bayar'])){$row_data['jml_hlm_bayar']=0;}
		if (empty($row_data['total_email'])){$row_data['total_email']=0;}
		if (empty($row_data['jml_antrian'])){$row_data['jml_antrian']=0;}
		if (empty($row_data['email_sukses'])){$row_data['email_sukses']=0;}
		if (empty($row_data['email_gagal'])){$row_data['email_gagal']=0;}
		if (empty($row_data['email_terbaca'])){$row_data['email_terbaca']=0;}
		if (empty($row_data['sample_sukses'])){$row_data['sample_sukses']=0;}
		if (empty($row_data['sample_gagal'])){$row_data['sample_gagal']=0;}
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$atrow.'',$not);
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$atrow.'',$row_data['create_date']);
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$atrow.'',$row_data['loading_file']);
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$atrow.'',$row_data['jml_pdf']);
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$atrow.'',$row_data['jml_lembar']);
		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$atrow.'',$row_data['jml_pdf_grts']);
		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$atrow.'',$row_data['jml_pdf_bayar']);
		$objPHPExcel->getActiveSheet()->SetCellValue('H'.$atrow.'',$row_data['jml_hlm_bayar']);
		$objPHPExcel->getActiveSheet()->SetCellValue('I'.$atrow.'',$row_data['total_email']);
		$objPHPExcel->getActiveSheet()->SetCellValue('J'.$atrow.'',$row_data['jml_antrian']);
		$objPHPExcel->getActiveSheet()->SetCellValue('K'.$atrow.'',$row_data['email_sukses']);
		$objPHPExcel->getActiveSheet()->SetCellValue('L'.$atrow.'',$row_data['email_gagal']);
		$objPHPExcel->getActiveSheet()->SetCellValue('M'.$atrow.'',$row_data['email_terbaca']);
		$objPHPExcel->getActiveSheet()->SetCellValue('N'.$atrow.'',$row_data['sample_sukses']);
		$objPHPExcel->getActiveSheet()->SetCellValue('O'.$atrow.'',$row_data['sample_gagal']);
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$atrow.':O'.$atrow.'')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('fff8f8f8');
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
		$objPHPExcel->getActiveSheet()->getStyle('M'.$atrow.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('N'.$atrow.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('O'.$atrow.'')->applyFromArray($styleArray);
		$not++;
		}
		$typost=$atrow+1;
		$objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow(0,$typost,2,$typost);
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$typost.'','Total');
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$typost.'',$t_jml_pdf);
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$typost.'',$t_jml_lembar);
		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$typost.'',$t_jml_email);
		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$typost.'',$t_jml_email_bayar);
		$objPHPExcel->getActiveSheet()->SetCellValue('H'.$typost.'',$t_jml_hlm_bayar);
		$objPHPExcel->getActiveSheet()->SetCellValue('I'.$typost.'',$t_jml_total_email);
		$objPHPExcel->getActiveSheet()->SetCellValue('J'.$typost.'',$t_jml_antrian);
		$objPHPExcel->getActiveSheet()->SetCellValue('K'.$typost.'',$t_email_sukses);
		$objPHPExcel->getActiveSheet()->SetCellValue('L'.$typost.'',$t_email_gagal);
		$objPHPExcel->getActiveSheet()->SetCellValue('M'.$typost.'',$t_email_terbaca);
		$objPHPExcel->getActiveSheet()->SetCellValue('N'.$typost.'',$t_sample_sukses);
		$objPHPExcel->getActiveSheet()->SetCellValue('O'.$typost.'',$t_sample_gagal);
		
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
		$objPHPExcel->getActiveSheet()->getStyle('A'.$typost.':O'.$typost.'')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('ffeeeeee');
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
		$objPHPExcel->getActiveSheet()->getStyle('M'.$typost.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('N'.$typost.'')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('O'.$typost.'')->applyFromArray($styleArray);
		
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
		$sql_data = "
			SELECT
				a.m_loading_id,
				to_char(a.create_date,'yyyy-mm-dd HH24:MI') as create_date,
				a.loading_file,
				a.total_customer as jml_pdf,
				a.total_halaman as jml_lembar,
				byr.jml_hlm_bayar,
				bc.total_email,
				c.jml_pdf_grts,
				cb.jml_pdf_bayar,
				d.jml_antrian,
				e.email_sukses,
				et.email_terbaca,
				f.email_gagal,
				g.sample_sukses,
				h.sample_gagal
			FROM m_loading a
				LEFT JOIN detail b
					ON a.m_loading_id = b.m_loading_id
				LEFT JOIN (
					SELECT count(*) as total_email, m_loading_id
					FROM vw_email
					GROUP BY m_loading_id
				) bc 
					ON a.m_loading_id = bc.m_loading_id
				LEFT JOIN (
					SELECT count(*) as jml_pdf_grts, m_loading_id
					FROM detail
					WHERE jml_hlm <= 4
					GROUP BY m_loading_id
				) c
					ON a.m_loading_id = c.m_loading_id
				LEFT JOIN (
					SELECT count(*) as jml_pdf_bayar, m_loading_id
					FROM detail
					WHERE jml_hlm > 4
					GROUP BY m_loading_id
				) cb
					ON a.m_loading_id = cb.m_loading_id
				LEFT JOIN (
					SELECT sum(jml_hlm - 4) as jml_hlm_bayar, m_loading_id
					FROM detail
					WHERE jml_hlm > 4
					GROUP BY m_loading_id
				) byr
					ON a.m_loading_id = byr.m_loading_id
				LEFT JOIN (
					SELECT count(*) as jml_antrian, loading_id
					FROM antrian_email
					GROUP BY loading_id
				) d
					ON a.m_loading_id = d.loading_id
				LEFT JOIN (
						SELECT count(*) as email_sukses, loading_id
						FROM tr_email a
						WHERE date_email_callback is null ".$js_pkirim."
						and a.nomor_rekening is not null
						AND status_sample IS NOT TRUE
						GROUP BY loading_id
						) e
					ON a.m_loading_id = e.loading_id
				LEFT JOIN (
					SELECT count(*) as email_gagal, loading_id
					FROM tr_email a 
					WHERE date_email_callback is not null ".$js_pkirim."
					and a.nomor_rekening is not null
					AND status_sample IS NOT TRUE
					GROUP BY loading_id
				) f
					ON a.m_loading_id = f.loading_id
					
				LEFT JOIN (
					SELECT count(*) as email_terbaca, loading_id
					FROM (select * from tr_email where tgl_read is not null) a
						LEFT JOIN vw_email b
							ON a.loading_id = b.m_loading_id and a.nomor_rekening = b.nomor_rekening
					WHERE date_email_callback is null ".$js_pkirim."
					and b.nomor_rekening is not null
					AND status_sample IS NOT TRUE
					GROUP BY loading_id
				) et
					ON a.m_loading_id = et.loading_id
				LEFT JOIN (
					SELECT count(*) as sample_sukses, loading_id
					FROM tr_email a 
					WHERE date_email_callback is null ".$js_pkirim."
					and a.status_sample is true 
					GROUP BY loading_id
				) g
					ON a.m_loading_id = g.loading_id
				LEFT JOIN (
					SELECT count(*) as sample_gagal, loading_id
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
				bc.total_email, c.jml_pdf_grts, cb.jml_pdf_bayar, d.jml_antrian, e.email_sukses, f.email_gagal, et.email_terbaca, g.sample_sukses, h.sample_gagal,
				byr.jml_hlm_bayar
			ORDER BY a.m_loading_id
		";
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
		$pdf->Cell(85,10,'Nama File',1,0,'C');
		$pdf->Cell(36,5,'Jumlah',1,0,'C');
		#$pdf->Cell(35,5,'PDF Tanpa Email',1,0,'C');
		$pdf->Cell(54,5,'PDF Dgn Email',1,0,'C');
		$pdf->Cell(45,5,'Email',1,0,'C');
		$pdf->Cell(30,5,'Sample',1,1,'C');
		
		$pdf->SetXY($x+118,$pdf->GetY());
		$pdf->Cell(18,5,'PDF',1,0,'C');
		$pdf->Cell(18,5,'Jml Hlm',1,0,'C');
		#$pdf->Cell(25,5,'Email',1,0,'C');
		#$pdf->Cell(17.5,5,'[<5hlm]',1,0,'C');
		#$pdf->Cell(17.5,5,'[>4hlm]',1,0,'C');
		$pdf->Cell(18,5,'< = 4 hlm',1,0,'C');
		$pdf->Cell(18,5,'> = 5 hlm',1,0,'C');
		$pdf->Cell(18,5,'Total',1,0,'C');
		$pdf->Cell(15,5,'Antrian',1,0,'C');
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
			$pdf->Cell(85,5,$row_data['loading_file'],1,0,'L');
			$pdf->Cell(18,5,number_format($row_data['jml_pdf'],0,'',','),1,0,'R');
			$pdf->Cell(18,5,number_format($row_data['jml_lembar'],0,'',','),1,0,'R');
			$pdf->Cell(18,5,number_format($row_data['jml_pdf_grts'],0,'',','),1,0,'R');
			$pdf->Cell(18,5,number_format($row_data['jml_pdf_bayar'],0,'',','),1,0,'R');
			$pdf->Cell(18,5,number_format($row_data['total_email'],0,'',','),1,0,'R');
			$pdf->Cell(15,5,number_format($row_data['jml_antrian'],0,'',','),1,0,'R');
			$pdf->Cell(15,5,number_format($row_data['email_sukses'],0,'',','),1,0,'R');
			$pdf->Cell(15,5,number_format($row_data['email_gagal'],0,'',','),1,0,'R');
			$pdf->Cell(15,5,number_format($row_data['sample_sukses'],0,'',','),1,0,'R');
			$pdf->Cell(15,5,number_format($row_data['sample_gagal'],0,'',','),1,1,'R');
			
			$total_pdf = $total_pdf + $row_data['jml_pdf'];
			$jml_lembar = $jml_lembar + $row_data['jml_lembar'];
			$total_total_email = $total_total_email + $row_data['total_email'];
			$total_email = $total_email + $row_data['jml_pdf_grts'];
			$total_email_bayar = $total_email_bayar + $row_data['jml_pdf_bayar'];
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
		$pdf->Cell(118,5,'TOTAL',1,0,'C');
		$pdf->Cell(18,5,number_format($total_pdf,0,'',','),1,0,'R');
		$pdf->Cell(18,5,number_format($jml_lembar,0,'',','),1,0,'R');
		#$pdf->Cell(20,5,number_format($total_email,0,'',','),1,0,'R');
		#$pdf->Cell(17.5,5,number_format($total_tanpa_email,0,'',','),1,0,'R');
		#$pdf->Cell(17.5,5,number_format($total_tanpa_email_bayar,0,'',','),1,0,'R');
		$pdf->Cell(18,5,number_format($total_email,0,'',','),1,0,'R');
		$pdf->Cell(18,5,number_format($total_email_bayar,0,'',','),1,0,'R');
		$pdf->Cell(18,5,number_format($total_total_email,0,'',','),1,0,'R');
		$pdf->Cell(15,5,number_format($total_antrian,0,'',','),1,0,'R');
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
?>
<script>
	function view_template(val){
		var antrian_id = val;
		$.post('script_summary_detail.php?act=view_template&antrian_id='+antrian_id,"",function(respon){
			$.fancybox(respon);
		});
	}
</script>