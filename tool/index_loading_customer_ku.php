<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/function.php"); ?>
<?
	$pr = trim($_GET["pr"]);
	$arr_pr = explode("|", $pr);

	$flagtrans = trim($arr_pr[0]);
	$menu_id = (int)trim($arr_pr[1]);
	$con = pg_connect($connection) or die("Could not connect to database!");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo $title ?></title>
    </head>
    <script>
        function cekFile(){
            if($("#loading_file")!=''){
				if($("#loading_file").val().substr(-4).toLowerCase()=='.txt'){
                	$(".button").show(500);
				}else{
					$(".button").hide(500);
				}
            }
        }
		
		function cekForm(){
			var blth = $("#blth").val();
			var blth_report = $("#blth2").val();
			
			var pr = $("#pr").val();
			var arr_pr = pr.split('|');
			var product = arr_pr[0];
			
			alert('Anda memilih Periode: '+blth);
			
			if(confirm('Untuk menghindari kesalahan loading, tolong dicek kembali!\n\nApakah Anda ingin melanjutkan?')){
				if(product=='CO' || product=='BC'){
					$("#form_upload_customer").attr('action','proses_loading_customer_ku.php?pr='+pr+'|upload');
				}
				return true;
			}else{
				return false;
			}
		}
		
		function show_cust(log_customer_id){
			var input_search = $("#input_search_det").val();
			input_search = input_search.replace(' ','8764346466435364647768799667654537543756');
				Loaddiv('div_customer','script_loading_customer_ku.php','act=show_customer&log_customer_id='+log_customer_id+'&offset=0&input_search='+input_search);
			$("#span_search_log").hide();
			$("#span_search_det").hide();
		}
		
		function kirim_ulang(log_customer_id,blth,flagtrans,blth_report){
			Loaddiv('div_customer','script_loading_customer_ku.php','act=kirim_ulang&log_customer_id='+log_customer_id+'&flagtrans='+flagtrans+'&blth='+blth+'&blth_report='+blth_report);
			$("#span_search_log").hide();
			$("#span_search_det").show();
		}
		
		function show_det2(){
			var log_customer_id = $("#log_cust_id").val();
			var input_search = $("#input_search_det").val();
			input_search = input_search.replace(' ','8764346466435364647768799667654537543756');
			Loaddiv('div_customer','script_loading_customer_ku.php','act=show_customer&log_customer_id='+log_customer_id+'&offset=0&input_search='+input_search);
			$("#span_search_log").hide();
			$("#span_search_det").show();
		}
		
		function show_blth_log(){
			var flagtrans = $("#flagtrans").val();
			var blth = $("#blth_show").val();
			var blth_report = $("#blth2").val();
			var input_search = $("#input_search_log").val();
			input_search = input_search.replace(' ','8764346466435364647768799667654537543756');
			Loaddiv('div_customer','script_loading_customer_ku.php','act=log_customer&flagtrans='+flagtrans+'&blth='+blth+'&blth_report='+blth_report+'&offset=0&input_search='+input_search);
			$("#span_search_det").hide();
			$("#span_search_log").show();
		}

		
		function GotoLoadForm(){			
			$("#div_form_ecust").hide(500);
			$("#div_form_load").show(500);	
		}
	
		function cek(e,n){
			var charCode;
			if(e && e.which){
				charCode=e.which;
			}else if(window.event){
				e=window.event;
				charCode=e.keyCode;
			}
				
			if(charCode==13){
				if(n == 1){
					show_blth_log();
				}else if(n ==2){
					show_det2();
				}
			}
		}
    </script>
    <body>
		<? require_once("../include/script.php"); ?>
        <? require_once("../include/message.php"); ?>
        <table id="container" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td align="center" valign="top">
                    <? require_once("../include/header.php"); ?>
                    <? require_once("../include/menu.php"); ?>
                    <? require_once("../include/greeting.php"); ?>
                </td>
            </tr>
            <tr>
                <td height="500" align="center" valign="top">
                    <table width="98%" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td>
                                <? require_once("../include/page_title.php"); ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center">
								<div id='div_form_load'>
                                    <form method="post" enctype="multipart/form-data" id="form_upload_customer" name="form_upload_customer" onsubmit="return cekForm()">
                                        <input type="hidden" id="pr" name="pr" value="<?=$pr?>" />
                                        <input type="hidden" id="flagtrans" name="flagtrans" value="<?=$flagtrans?>" />
                                        <table width="50%" border="0" cellpadding="2" cellspacing="2" align="center">
                                            <tr>
                                                <td width="75%">Periode Loading Bulan KU<b>(BLTH)</b></td>
                                                <td width="80%">
                                                    <select id="blth" name="blth" class="combobox">
                                                        <option value="<?php echo date('mY')?>" selected="selected"><?php echo date('mY')?></option>
                                                        <option value="<?php echo GetBLTH(date('mY'),-1)?>"><?php echo GetBLTH(date('mY'),-1)?></option>
                                                        <option value="<?php echo GetBLTH(date('mY'),-2)?>"><?php echo GetBLTH(date('mY'),-2)?></option>
                                                        <option value="<?php echo GetBLTH(date('mY'),-3)?>"><?php echo GetBLTH(date('mY'),-3)?></option>
                                                    </select>
                                                </td>
                                            </tr>
											
											
											<tr>
                                                <td width="75%">Periode Loading Bulan Reporting<b>(BLTH)</b></td>
                                                <td width="80%">
                                                    <select id="blth2" name="blth2" class="combobox">
                                                        <option value="<?php echo date('mY')?>" selected="selected"><?php echo date('mY')?></option>
                                                    </select>
                                                </td>
                                            </tr>
                                           
										   <tr>
                                                <td height="20" colspan="2" align="left" valign="middle" style="color:red;">Silakan masukkan file berformat <b><i>*.txt</i></b>
												<br>
												Nama file harus berawalan <b><i>KU_</i></b>
												</td>
												
                                            </tr>
                                            <tr>
                                                <td align="left" valign="middle" colspan="2">
                                                    <input type="file" class="textbox_4" name="loading_file" id="loading_file" size="80" onchange="cekFile()" />
													<br/><br/>
													<span style="font-family:verdana;font-size:12px;">File ini berisi alamat email baru. <br/>Mohon ikuti format yang ada. Lihat contohnya <a href="../temp_file/ku/sampleKU.txt" target="_blank">di sini</a><br/><br/><b>CATATAN:</b><br/>
													<span style="font-family:courier;font-size:12.5px;"><i>* Efektif mulai <b>29 April 2014</b>
													<br/>* Jika alamat email baru (KU) kosong, aplikasi akan mengambil alamat email pada pengiriman sebelumnya!</i></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" height="50">&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" align="center" valign="middle">
                                                    <input type="submit" class="button" value="UPLOAD" style="display:none" />
                                                </td>
                                            </tr>
                                        </table>
                                    </form>
								</div>
								<div id='div_form_ecust' style="display:none">
									<form method="post" id="form_edit_customer" name="form_edit_customer">
										<input type="hidden" id="pr" name="pr" value="<?=$pr?>" />
										<input type="hidden" id="flagtrans" name="flagtrans" value="<?=$flagtrans?>" />
										<input type="hidden" id="m_customer_id" name="m_customer_id" value="" />
										<input type="hidden" id="log_customer_id" name="log_customer_id" value="" />
										<input type='hidden' id="blth_hid" name="blth_hid" value="">
										<table width="80%" border="0" cellpadding="2" cellspacing="2" align="center">
											<tr>
												<td width="20%">Nomor Customer</td>
												<td width="80%">
													<input type='text' readonly id='txt_nocust' name='txt_nocust' size=25 value='' class="textbox_3" />
												</td>
											</tr>
											<tr>
												<td width="20%">Nomor Rekening</td>
												<td width="80%">
													<input type='text' readonly id='txt_norek' name='txt_norek' size=25 value='' class="textbox_3" />
												</td>
											</tr>
											<tr>
												<td width="20%">Email</td>
												<td width="80%">
													<input type='text' id='txt_email' name='txt_email' size=100 value='' class="textbox_4" />
												</td>
											</tr>
											<tr>
												<td colspan="2" height="50">&nbsp;</td>
											</tr>
											<tr>
												<td colspan="2" align="center" valign="middle">
													<input type="button" class="button" value="Edit" 
													onClick="
														if(confirm('Anda yakin akan mengedit data user ini?'))
															DoEditCust();" />
													<input type="button" class="button" value="Cancel" onClick="GotoLoadForm();" />
												</td>
											</tr>
										</table>
									</form>
								</div>
                            </td>
                        </tr>
                        <tr>
                        	<td align="left" height="30">
                            	<table width="100%">
                                    <tr>
                                        <td align="left" height="50%">Periode: 
                                            <select id="blth_show" name="blth_show" class="combobox" onchange="show_blth_log()">
                                                <?php
                                                $sql_sel_blth = "SELECT blth
                                                                FROM log_customer_ku
                                                                WHERE flagtrans = '$flagtrans'
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
                                        </td>
                                        <td align="right" height="50%">&nbsp;
                                        	<span id="span_search_log" style="display:none">
                                            	<input type="text" class="textbox_3" id="input_search_log" name="input_search_log" onkeypress="cek(event,1)" />&nbsp;<a onclick="show_blth_log()"><img class="vtip" title="search" src="../images/icons/find_data.png" /></a>
                                            </span>
                                        	<span id="span_search_det" style="display:none">
                                                <input type="text" class="textbox_3" id="input_search_det" name="input_search_det" onkeypress="cek(event,2)" />&nbsp;<a onclick="show_det2()"><img class="vtip" title="search" src="../images/icons/find_data.png" /></a>
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                        	<td align="center"><div id="div_customer"></div></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td align="center" valign="top">
                    <? require_once("../include/footer.php"); ?>
                </td>
            </tr>
        </table>
    </body>
</html>
<script>
	var flagtrans = $("#flagtrans").val();
	var blth = $("#blth_show").val();
	var blth_report = $("#blth2").val();
	Loaddiv('div_customer','script_loading_customer_ku.php','act=log_customer&flagtrans='+flagtrans+'&blth='+blth+'&blth_report='+blth_report+'&offset=0');
	$("#span_search_log").show();
</script>
<?
	pg_close($con);
?>