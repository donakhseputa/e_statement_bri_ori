<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/function.php"); ?>
<!--script language="javascript" type="text/javascript">
	function test(){
		alert('aloha');
	}
</script-->
<?
	$act = $_REQUEST['act'];
	$con = pg_connect($connection) or die("Could not connect to database!");
	
	switch($act){
		case 'blth': blth(); break;
		case 'view': view(); break;
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
	
	function view(){
		$arr_pr = explode('|', $_REQUEST['pr']);
		$flagtrans = $arr_pr[0];
		$menuid = $arr_pr[1];
		$blth = $_REQUEST['blth'];
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
                                    	<?php
										$sql_sel_judul = "SELECT b.menugroup, a.menu
															FROM menu1 a, menugroup1 b
															WHERE a.menugroupid = b.menugroupid
																and menuid = $menuid";
										$qry_sel_judul = pg_query($sql_sel_judul) or die($sql_sel_judul);
										$row_sel_judul = pg_fetch_assoc($qry_sel_judul);
										echo $row_sel_judul['menugroup'].' - '.$row_sel_judul['menu'];
										?>
                                    </td>
                                </tr>
                                <tr height="30">
                                	<td><b>Periode: <?=$blth?></b></td>
                                    <td>&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                    	<td>
                        	<table align="center" border="1" cellpadding="2" cellspacing="0" width="100%">
                            	<tr height="30">
                                	<td width="4%" align="center"><b>No</b></td>
                                	<td align="center"><b>Tanggal Loading</b></td>
                                	<td align="center"><b>Nama File</b></td>
                                	<td align="center"><b>Jumlah PDF</b></td>
                                	<td align="center"><b>Jumlah Halaman</b></td>
                                	<td align="center"><b>Jumlah Email</b></td>
                                	<td align="center"><b>Email di Antrian</b></td>
                                	<td align="center"><b>Email Sukses</b></td>
                                	<td align="center"><b>Email Gagal</b></td>
                                </tr>
                                <?php
									$i = 0;
									$sql_data = "SELECT
														to_char(a.create_date,'yyyy-mm-dd') as create_date,
														a.loading_file,
														a.total_customer as jml_pdf,
														(SELECT SUM(jml_hlm) FROM detail x WHERE x.m_loading_id = a.m_loading_id) as jml_lembar,
														(SELECT count(*)
															FROM tr_email x
															WHERE x.loading_id = a.m_loading_id
																and x.email IN (SELECT DISTINCT(email1) FROM m_customer WHERE blth='$blth')) AS jml_email,
														(SELECT count(*)
															FROM antrian_email x
															WHERE x.loading_id = a.m_loading_id) as jml_blm_kirim,
														(SELECT count(*)
															FROM tr_email x
															WHERE x.loading_id = a.m_loading_id and date_email_callback is null
																and x.email IN (SELECT DISTINCT(email1) FROM m_customer WHERE blth='$blth')) as email_sukses,
														(select count(*)
															FROM tr_email x
															WHERE x.loading_id = a.m_loading_id and date_email_callback is not null
																and x.email IN (SELECT DISTINCT(email1) FROM m_customer WHERE blth='$blth')) as email_gagal
													FROM m_loading a
														INNER JOIN detail b
															ON a.m_loading_id = b.m_loading_id
													WHERE a.blth = '$blth' and a.flagtrans = '$flagtrans'
													group by a.create_date, a.loading_file, a.total_customer, a.total_halaman, a.m_loading_id
													ORDER BY a.create_date, a.loading_file";
									//echo "$sql_data<br>";
									$qry_data = pg_query($sql_data) or die('ERROR select m_loading: '.$sql_data);
									$totfval=0;
									$totsval=0;
									$tottval=0;
									$totpval=0;
									while($row_data = pg_fetch_array($qry_data)){
										$i++;
										$fval=number_format($row_data['email_gagal'],0,',',''); 
										$totfval=$totfval+$fval;
										($fval==0)?($flink=$fval):($flink='<a href="#" onClick="window.open(\'script_feedback_email.php?act=show_report&flagtrans=BC&blth='.$blth.'&nama_file='.$row_data['loading_file'].'&report_menu=2&tipe_report=1\');">'.$fval.'</a>');
										($totfval==0)?($totflink=$totfval):($totflink='<a href="#" onClick="window.open(\'script_feedback_email.php?act=show_report&flagtrans=BC&blth='.$blth.'&report_menu=2&tipe_report=1\');">'.$totfval.'</a>');

										$sval=number_format($row_data['email_sukses'],0,',',''); 
										$totsval=$totsval+$sval;
										($sval==0)?($slink=$sval):($slink='<a href="#" onClick="window.open(\'script_feedback_email.php?act=show_report&flagtrans=BC&blth='.$blth.'&nama_file='.$row_data['loading_file'].'&report_menu=1&tipe_report=1\');">'.$sval.'</a>');
										($totsval==0)?($totslink=$totsval):($totslink='<a href="#" onClick="window.open(\'script_feedback_email.php?act=show_report&flagtrans=BC&blth='.$blth.'&report_menu=1&tipe_report=1\');">'.$totsval.'</a>');

										$tval=number_format($row_data['jml_email'],0,',',''); 
										$tottval=$tottval+$tval;
										($tval==0)?($tlink=$tval):($tlink='<a href="#" onClick="window.open(\'script_feedback_email.php?act=show_report&flagtrans=BC&blth='.$blth.'&nama_file='.$row_data['loading_file'].'&report_menu=4&tipe_report=1\');">'.$tval.'</a>');
										($tottval==0)?($tottlink=$tottval):($tottlink='<a href="#" onClick="window.open(\'script_feedback_email.php?act=show_report&flagtrans=BC&blth='.$blth.'&report_menu=4&tipe_report=1\');">'.$tottval.'</a>');

										$pval=number_format($row_data['jml_pdf'],0,',',''); 
										$totpval=$totpval+$pval;
										($pval==0)?($plink=$pval):($plink='<a href="#" onClick="window.open(\'script_feedback_email.php?act=show_report&flagtrans=BC&blth='.$blth.'&nama_file='.$row_data['loading_file'].'&report_menu=5&tipe_report=1&tval='.$tval.'\');">'.$pval.'</a>');
										//($totpval==0)?($totplink=$totpval):($totplink='<a href="#" onClick="window.open(\'script_feedback_email.php?act=show_report&flagtrans=BC&blth='.$blth.'&report_menu=5&tipe_report=1&tval='.$tval.'\');">'.$totpval.'</a>');
										
										
										$lval=number_format($row_data['jml_lembar'],0,',','');
										$totlval=$totlval+$lval;
										
										$bval=number_format($row_data['jml_blm_kirim'],0,',','');
										$totbval=$totbval+$bval;
								?>
                                <tr height="30">
                                	<td align="center"><?php echo $i?></td>
                                	<td align="center"><?php echo $row_data['create_date']?></td>
                                	<td align="center"><?php echo $row_data['loading_file']?></td>
                                	<td align="right"><?php echo $plink; ?>&nbsp;&nbsp;</td>
                                	<td align="right"><?php echo $lval?>&nbsp;&nbsp;</td>
                                	<td align="right"><?php echo $tlink; ?>&nbsp;&nbsp;</td>
                                	<td align="right"><?php echo $bval?>&nbsp;&nbsp;</td>
                                	<td align="right"><?php echo $slink; ?>&nbsp;&nbsp;</td>
                                	<td align="right"><?php echo $flink; ?>&nbsp;&nbsp;</td>
                                </tr>
                                <?php
									}
								?>
								<tr height="30">
                                	<td align="center"></td>
                                	<td align="left" colspan=2><b>TOTAL</b></td>
                                	<td align="right"><?php echo '<b>'.$totpval.'</b>'; ?>&nbsp;&nbsp;</td>
                                	<td align="right"><?php echo '<b>'.$totlval.'</b>'; ?>&nbsp;&nbsp;</td>
                                	<td align="right"><?php echo '<b>'.$tottlink.'</b>'; ?>&nbsp;&nbsp;</td>
                                	<td align="right"><?php echo '<b>'.$totbval.'</b>'; ?>&nbsp;&nbsp;</td>
                                	<td align="right"><?php echo '<b>'.$totslink.'</b>'; ?>&nbsp;&nbsp;</td>
                                	<td align="right"><?php echo '<b>'.$totflink.'</b>'; ?>&nbsp;&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </body>
        </html>
        <?php
	}
	
	pg_close($con);
?>