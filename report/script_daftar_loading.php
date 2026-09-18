<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/function.php"); ?>
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
                                	<td width="13%" align="center"><b>User</b></td>
                                	<td width="31%" align="center"><b>Tanggal Loading</b></td>
                                	<td width="20%" align="center"><b>Produk</b></td>
                                	<td width="19%" align="center"><b>Nama File</b></td>
                                	<td width="13%" align="center"><b>Jumlah Data</b></td>
                                </tr>
                                <?php
									$i = 0;
									$sql_data = "SELECT a.userid, to_char(a.create_date,'dd-mm-yyyy HH24:mm:ss') as create_date,
														a.loading_file as nama_file, a.total_customer,
														b.produk
													FROM m_loading a
														INNER JOIN mproduk b
															ON a.flagtrans = b.flagtrans
													WHERE blth = '$blth'";
									//echo $sql_data;
									$qry_data = pg_query($sql_data) or die('ERROR select m_loading: '.$sql_data);
									while($row_data = pg_fetch_array($qry_data)){
										$i++;
								?>
                                <tr height="30">
                                	<td align="center"><?=$i?></td>
                                	<td align="center"><?=$row_data['userid']?></td>
                                	<td align="center"><?=$row_data['create_date']?></td>
                                	<td><?=$row_data['produk']?></td>
                                	<td><?=$row_data['nama_file']?></td>
                                	<td align="right"><?=number_format($row_data['total_customer'],0,',','')?>&nbsp;&nbsp;</td>
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
	
	pg_close($con);
?>