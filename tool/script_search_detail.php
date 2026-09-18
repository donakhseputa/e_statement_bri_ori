<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/function.php"); ?>
<?php
	$act = $_REQUEST['act'];
	$con = pg_connect($connection) or die('Could not connect to database!');
	
	
	
	
	
	
	switch($act){
		case 'log_detail'			: log_detail(); break;
	}
	
	function log_detail(){
		$blth = $_REQUEST['blth'];
		$flagtrans = $_REQUEST['flagtrans'];
		$pilih_produk = $_REQUEST['pilih_produk'];
		$offset = $_REQUEST['offset'];
		$field_name = $_REQUEST['field_name'];
		$input_search = $_REQUEST['input_search'];
		$input_search = str_replace('8764346466435364647768799667654537543756',' ',$input_search);
		if($pilih_produk <> '0'){
			$sql_flag = " WHERE flagtrans='$pilih_produk'";
			$sql_flagnya = " AND a.flagtrans = '$pilih_produk'";
			$flag_all = " AND flagtrans='$pilih_produk'";
		}else{
			$sql_flag = "";
			$sql_flagnya = "";
			$flag_all = " AND flagtrans !=''";
		}
		
		$GLOBALS['tr_email_sk_br']= cek_sk_tr($blth, $flagtrans);
		
		if($blth <> "0"){
			$blth_search = $blth . " & ";
			$sql_where = " WHERE a.blth = '$blth' AND ";
			$blth_all = " WHERE blth='$blth'";
		}else{
			$sql_where = " WHERE ";
			$blth_all = " WHERE blth!=''";
		}
		if($input_search == "Search Nomor, Nama, or Email"){
			$input_search = "";
		}
		if($field_name == "nomor_rekening"){
			$field_show = "Nomor: ";
			$sql_where_like = "upper(b.".$field_name.") LIKE upper('%".$input_search."%')";
			$sql_where_like .= "OR upper(b.nomor_customer) LIKE upper('%".$input_search."%')";
		}elseif($field_name == "nama"){
			$field_show = "Nama: ";
			$sql_where_like = "upper(b.".$field_name.") LIKE upper('%".$input_search."%')";
		}elseif($field_name == "email"){
			$field_show = "Email: ";
			$sql_where_like = "
				(
					upper(c.email) LIKE upper('%".$input_search."%')
					OR upper(d.email) LIKE upper('%".$input_search."%')
				)
			";
		}
		echo "Search  : ".$blth_search.$field_show.$input_search;
		$limit = 15;
		$i = $offset;
		
		
		
		$BLTH_BARU = BLTH_BARU;
			
		
		$sql_nya = "
			SELECT  a.blth, substring(a.blth,3,4)||substring(a.blth,1,2) as  thn_bln, a.loading_file, to_char(a.create_date, 'dd Mon yyyy hh24:mi') AS create_date, 
			b.nomor_rekening, b.nama, b.flagtrans, b.pdf_name AS pdf_name, to_char(e.tgl_antrian, 'dd-Mon-yyyy hh24:mi') AS tgl_antrian, 
			c.email AS email_antrian, to_char(d.date_email_send, 'dd Mon yyyy hh24:mi') AS date_email_send, 
			d.email AS email_kirim, d.ket_error, to_char(d.tgl_read, 'dd-Mon-yyyy hh24:mi') AS tgl_read ,b.password_pdf,
			CASE 
			WHEN c.email is not null and d.date_email_send is null THEN 'QUEUE' 
			WHEN d.email is not null and d.date_email_callback is null THEN 'SUCCESS' 
			WHEN d.email is not null and d.date_email_callback is not null THEN 'FAILED' ELSE 'NO STATUS' END AS status 
			FROM m_loading a 
			LEFT JOIN tbl_detail b ON a.m_loading_id = b.m_loading_id 
			LEFT JOIN antrian_email c ON a.m_loading_id = c.loading_id and b.nomor_rekening = c.nomor_rekening and (c.status_sample = 'f' or c.status_sample is null) 
			LEFT JOIN ".$GLOBALS['tr_email_sk_br']." d ON a.m_loading_id = d.loading_id and b.nomor_rekening = d.nomor_rekening 
			and (d.status_sample = 'f' or d.status_sample is null) 
			LEFT JOIN antrian_email_history e ON d.antrian_id = e.antrian_id $sql_where 
			 $sql_where_like $sql_flagnya ";
		
		
		$sql = "SELECT distinct blth, substring(blth,3,4)||substring(blth,1,2) as thnbl,flagtrans 
FROM m_loading $blth_all $flag_all  ORDER BY  substring(blth,3,4)||substring(blth,1,2) DESC";
			$qry_blth = pg_query($sql) or die('ERROR show: '.$sql);
			#echo $sql;

			$jumlah_blth = pg_num_rows($qry_blth);
			$status_blth_lewat = 0;

                while($row_blth =pg_fetch_array($qry_blth))
				{
                    $j++;
					 $list_blth = $row_blth['blth'];
					 $list_flagtrans = strtolower($row_blth['flagtrans']);

						 #$tbl_detail = 'detail_'.$list_blth.'_'.$list_flagtrans;
						 
						 $blth_balik = substr($list_blth,-4).substr($list_blth,0,2);
		
						
						if($blth_balik>$BLTH_BARU){
							$tbl_detail = "detail_" . $list_blth."_".$list_flagtrans;
							$sql_cek = "SELECT a.m_loading_id FROM m_loading a LEFT JOIN $tbl_detail b ON a.m_loading_id=b.m_loading_id 
							WHERE a.blth='$list_blth' AND a.flagtrans='$list_flagtrans'";
							$exe_cek = @pg_query($sql_cek);
							if($exe_cek){
								$tbl_detail = "detail_" . $list_blth."_".$list_flagtrans;
							}else{
								$tbl_detail = "detail";
							}
						 $sql_all = $sql_all. str_replace('tbl_detail',$tbl_detail,$sql_nya) . "UNION ";
						 #$sql_all = $sql_all. str_replace('tbl_detail',$tbl_detail,$sql_nya) . "UNION ";
						}else{
							#$tbl_detail = "detail";
							$status_blth_lewat = 1;
						# $sql_all = str_replace('tbl_detail',$tbl_detail,$sql_nya) . "UNION ";
						}
						
			$c = 0;
		}
		
		
		if($status_blth_lewat==1){
		$sql_nyas = "
			SELECT  a.blth, substring(a.blth,3,4)||substring(a.blth,1,2) as  thn_bln, a.loading_file, to_char(a.create_date, 'dd Mon yyyy hh24:mi') AS create_date, 
			b.nomor_rekening, b.nama, b.flagtrans, b.pdf_name AS pdf_name, to_char(e.tgl_antrian, 'dd-Mon-yyyy hh24:mi') AS tgl_antrian, 
			c.email AS email_antrian, to_char(d.date_email_send, 'dd Mon yyyy hh24:mi') AS date_email_send, 
			d.email AS email_kirim, d.ket_error, to_char(d.tgl_read, 'dd-Mon-yyyy hh24:mi') AS tgl_read ,b.password_pdf,
			CASE 
			WHEN c.email is not null and d.date_email_send is null THEN 'QUEUE' 
			WHEN d.email is not null and d.date_email_callback is null THEN 'SUCCESS' 
			WHEN d.email is not null and d.date_email_callback is not null THEN 'FAILED' ELSE 'NO STATUS' END AS status 
			FROM m_loading a 
			LEFT JOIN detail b ON a.m_loading_id = b.m_loading_id 
			LEFT JOIN antrian_email c ON a.m_loading_id = c.loading_id and b.nomor_rekening = c.nomor_rekening and (c.status_sample = 'f' or c.status_sample is null) 
			LEFT JOIN ".$GLOBALS['tr_email_sk_br']." d ON a.m_loading_id = d.loading_id and b.nomor_rekening = d.nomor_rekening 
			and (d.status_sample = 'f' or d.status_sample is null) 
			LEFT JOIN antrian_email_history e ON d.antrian_id = e.antrian_id $sql_where 
			 $sql_where_like $sql_flagnya ";
		$sql_all = $sql_all . $sql_nyas . "UNION ";
		}
		if ($sql_all <> '')
		{
			$sql_sel = rtrim($sql_all,"UNION ");
		}
		
		
		#echo "<br/><br/>SQLNYA: ".$sql_sel;
		$qry_data = @pg_query($sql_sel) or die('ERROR show: '.$sql_sel);
		$qry_show = @pg_query($sql_sel . "  LIMIT $limit OFFSET $offset") or die('ERROR show: '.$sql_sel . " ORDER BY a.m_loading_id LIMIT $limit OFFSET $offset");
		$jumlah_data = pg_num_rows($qry_data);echo "<br/>JUMLAH DATA:".$jumlah_data;
		?>
<style>
		.textarea_1 
		{
			background-color: #CCFFCC;
			border: #666666 1px solid;
			color: #0000CC;
			font-family: Verdana, Arial, Helvetica, sans-serif;
			font-size: 12px;
			height: 50px;
			width: 50px;
		}
		.fontnya 
		{
			font-family: Arial, Helvetica, sans-serif;
			font-size: 12px;
			font-weight:bold;
		}
		
		</style>
        
        <table border="0" align="center" cellpadding="1" cellspacing="2">
        	    	<tr bgcolor="#CCCCFF"><td align="center" class="fontnya">NO</td>
            	<td align="center" class="fontnya">Periode</td>
            	<td align="center" class="fontnya">File<br />
Process Date</td>
            	<td align="center" class="fontnya">Name <br />Nomor</td>
            	<td align="center" class="fontnya">PDF<br />Created Date</td>
            	<td align="center" class="fontnya">Status</td>
            	<td align="center" class="fontnya">Queue Date</td>
            	<td align="center" class="fontnya">Email<br />Sent Date</td>
            	<td align="center" class="fontnya">Note</td>
            </tr>
			<?php
            if($jumlah_data > 0){
                while($row_show=pg_fetch_array($qry_show)){
                    $i++;
                    $nomor_rekening = $row_show['nomor_rekening'];
                    $blth_ = $row_show['blth'];
                    $nama = $row_show['nama'];
                    $periode = $row_show['blth'];
                    $nama_file = $row_show['loading_file'];
                    $tanggal_loading = $row_show['create_date'];
                    $status = $row_show['status'];
                    $email_antrian = $row_show['email_antrian'];
                    $tgl_antrian = $row_show['tgl_antrian'];
                    $date_email_send = $row_show['date_email_send'];
                    $email_kirim = $row_show['email_kirim'];
                    $ket_error = $row_show['ket_error'];
			$flagnya = $row_show['flagtrans'];
					$password_pdf = $row_show['password_pdf'];
										
						
                    $pdf_name = $row_show['pdf_name'].".pdf";
                    //$pdf_location = "../pdf/".$flagtrans."/".$blth_."/".str_replace(substr($nama_file, -10), "", $nama_file)."/".$pdf_name;
			$pdf_location = "../pdf/".$flagnya."/".$blth_."/".substr($nama_file,0,strpos($nama_file, '.'))."/".$pdf_name;


                    if($i%2==0){
                        $cls = 'table_row_odd';
                    }else{
                        $cls = 'table_row_even';
                    }
                    ?>
                    <tr class="<?php echo $cls?>">
                        <td align="center" height="20"><?php echo $i?></td>
                        <td align="center"><?php echo $periode?></td>
                        <td align="center"><?php echo $nama_file?><br /><?php echo $tanggal_loading?></td>
                        <td><?=$nama?><br /><?php echo $nomor_rekening?></td>
                        <td align="center">
                        	<?php
								if(strtoupper($flagnya)<>'BE'){
								if(file_exists($pdf_location))
								{
									echo "<a href= '".$pdf_location ."' target='_BLANK' >". $pdf_name ."</a>"; 
									echo "<br> Pass: ". $password_pdf	;
									echo "<br>"	. date ("d M Y H:i:s", filemtime($pdf_location));
								?>
									<? ?>
								<?
								}else{
								
									if ($flagtrans=='BC')
									{
											//d$\backup_pdf
											$pdf_location2 = str_replace('../', 'D:\\backup_pdf/', $pdf_location );
											
											//$pdf_location3 = str_replace('/', '\\', $pdf_location );
											//$pdf_location3 = str_replace('..\\', "\\\\192.168.2.206\backup_pdf\\", $pdf_location3 ); //file:///C:/Temp/test.pdf
											//$pdf_location3 = 'file://192.168.2.206/backup_pdf/';
											
											$pdf_location3 = str_replace('../', "file://///192.168.2.206/backup_pdf/", $pdf_location ); //file:///C:/Temp/test.pdf

											//file://192.168.2.206/backup_pdf/
											if(file_exists($pdf_location2))
											{
												//echo "<a href="\\192.168.2.206\folder\path" target="_blank">click</a>"; 
												//echo "<a href= '".$pdf_location3 ."' target='_blank' >". $pdf_name ."</a>"; 
												?>

													 <!-- The text field -->
													<input type="text" value="<?php echo $pdf_location3;?>" id="myInput" readonly >

												<?php 
												
												echo "<br> Pass: ". $password_pdf	;
												echo "<br>"	. date ("d M Y H:i:s", filemtime($pdf_location2));
											}else{
											
												//echo "<a href="\\192.168.2.206\folder\path" target="_blank">click</a>"; 
													//echo "<a href= '".$pdf_location3 ."' target='_blank' >". $pdf_name ."</a>"; 
													$pdf_location4 = str_replace('../', 'H:\\backup_pdf/', $pdf_location );
													$pdf_location5 = str_replace('../', "file://///192.168.2.206/backup_pdf2/", $pdf_location ); //
													#die($pdf_location3);
												if(file_exists($pdf_location4))
												{
													
											
													?>

														 <!-- The text field -->
														<input type="text" value="<?php echo $pdf_location5;?>" id="myInput" readonly >

													<?php 
													
													echo "<br> Pass: ". $password_pdf	;
													echo "<br>"	. date ("d M Y H:i:s", filemtime($pdf_location4));
												}else{
												
													//echo $pdf_location." PDF NOT FOUND";
													////////////////////////////////////////////////////////////
													//E:\Backup_PDF_BRI\Materai From Estat
													$pdf_location6 = str_replace('../', 'E:\\Backup_PDF_BRI/Materai From Estat/', $pdf_location );
													$pdf_location7 = str_replace('../', "file://///192.168.2.206/backup_pdf3/", $pdf_location ); //
													
													//$pdf_location6 = str_replace('pdf/BC/','',$pdf_location6);
													//$pdf_location7 = str_replace('pdf/BC/','',$pdf_location7);
													
													//die($pdf_location7);
													if(file_exists($pdf_location6))
													{
														
												
														?>

															 <!-- The text field -->
															<input type="text" value="<?php echo $pdf_location7;?>" id="myInput" readonly >

														<?php 
														
														echo "<br> Pass: ". $password_pdf	;
														echo "<br>"	. date ("d M Y H:i:s", filemtime($pdf_location6));
													}else{
														//echo $pdf_location." PDF NOT FOUND";
														
														//////////////////////////////////////////////////////////
														//E:\Backup_PDF_BRI\Materai From Estat
														//F:\Backup_PDF_BRI\Materai From Estat
														$pdf_location8 = str_replace('../', 'E:\\PDF_ESTATEMENT/', $pdf_location );
														$pdf_location9 = str_replace('../', "file://///192.168.2.206/backup_pdf_x/", $pdf_location ); //
														
														$pdf_location9 = str_replace('pdf/BC/','',$pdf_location9);
														$pdf_location8 = str_replace('pdf/BC/','',$pdf_location8);
														
														//die($pdf_location8);
														if(file_exists($pdf_location8))
														{
															
													
															?>

																 <!-- The text field -->
																<input type="text" value="<?php echo $pdf_location9;?>" id="myInput" readonly >

															<?php 
															
															echo "<br> Pass: ". $password_pdf	;
															echo "<br>"	. date ("d M Y H:i:s", filemtime($pdf_location8));
														}else{
															//echo $pdf_location." PDF NOT FOUND";
															
															//////////////////////////////////////////////////////////
															//E:\Backup_PDF_BRI\Materai From Estat
															//F:\Backup_PDF_BRI\Materai From Estat
															$pdf_location21 = str_replace('../', 'J:\\PDF_ESTATEMENT/', $pdf_location );
															$pdf_location22 = str_replace('../', "file://///192.168.2.206/backup_pdf_x/", $pdf_location ); //
															
															$pdf_location21 = str_replace('pdf/BC/','',$pdf_location21);
															$pdf_location22 = str_replace('pdf/BC/','',$pdf_location22);
															
															//die($pdf_location8);
															if(file_exists($pdf_location21))
															{
																
														
																?>

																	 <!-- The text field -->
																	<input type="text" value="<?php echo $pdf_location22;?>" id="myInput" readonly >

																<?php 
																
																echo "<br> Pass: ". $password_pdf	;
																echo "<br>"	. date ("d M Y H:i:s", filemtime($pdf_location21));
															}else{
																echo $pdf_location." PDF NOT FOUND";
															}
															//////////////////////////////////////////////////////////
															
															
														}
														//////////////////////////////////////////////////////////
													}
													////////////////////////////////////////////////////////////
												}
											
												#echo $pdf_location." PDF NOT FOUND";
											}
									
									}else{
										echo $pdf_location." PDF NOT FOUND";
									}
								}
								}else{
									echo "<font color='red'>BLAST EMAIL</font>";
								}
							?>
                        </td>
                        <td align="center"><b><?php echo $status?></b></td>
                        <td align="center"><?php echo $tgl_antrian?></td>
                        <td align="center"><?php echo $email_kirim?><br /><?php echo $date_email_send?></td>
                        <td align="center">
                        	<?php
							if($ket_error == "")
							{
								if(!empty($row_show['tgl_read']))
								{
                					$tgl_read = "Read : " . "<br/>". $row_show['tgl_read'] ;
									echo $tgl_read;
									
            					}

							}
							else
							{
								?>
                                <textarea class="textarea_1"><?=$ket_error?></textarea>
                                <?php
                            }
							?>
						</td>
          </tr>
                    <?php
                }
                ?>
                <tr>
                    <td align="center" colspan="10">
                        <?php
                            echo paging($offset,$jumlah_data,$limit,'div_detail','script_search_detail.php','act=log_detail&blth='.$blth.'&flagtrans='.$flagtrans.'&pilih_produk='.$pilih_produk.'&field_name='.$field_name.'&input_search='.$input_search);
                        ?>
                    </td>
                </tr>
                <?php
			}else{
                ?>
                <tr>
                    <td align="center" colspan="10" style="color:#F00">
                        <?php
                            echo "<blink><b>DATA NOT FOUND</b></blink>";
                        ?>
                    </td>
                </tr>
                <?php
			}
			?>
</table>
        <?php
	}
?>