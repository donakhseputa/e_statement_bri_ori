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
        <title><?= $title ?></title>
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
			
			var pr = $("#pr").val();
			var arr_pr = pr.split('|');
			var product = arr_pr[0];
			
			alert('Anda memilih Periode: '+blth);
			
			if(confirm('Untuk menghindari kesalahan loading, tolong dicek kembali!\n\nApakah Anda ingin melanjutkan?')){
				if(product=='BC'){
					$("#form_upload_customer").attr('action','proses_loading_customer.php?pr='+pr+'|upload');
					return true;
				}else if(product=='CO'){
					$("#form_upload_customer").attr('action','proses_loading_customer.php?pr='+pr+'|upload');
					return true;
				}else if(product=='BD'){
					$("#form_upload_customer").attr('action','proses_loading_customer.php?pr='+pr+'|upload');
					return true;
				}else{
					alert('Produk belum didaftarkan. Hubungi APPDEV !!!');
				}
			}else{
				return false;
			}
		}
		
		function DoEditCust(){
			//alert($("#log_customer_id").val());
			//return false;
			$.post('script_loading_customer.php?act=editcust',$("#form_edit_customer").serialize(),function(respon){
				alert(respon);
				$("#blth_show").val($("#blth_hid").val());
				show_cust($("#log_customer_id").val());
			});
		}
		
		function EditCust(custid,norek,email,log_customer_id,blth){
			$("#div_form_load").hide(500);
			$("#div_form_ecust").show(500);	
			$("#txt_norek").val(norek);
			$("#txt_email").val(email);//m_customer_id
			$("#log_customer_id").val(log_customer_id);
			$("#period_hid").val();
			$("#m_customer_id").val(custid);
			$("#blth_hid").val(blth);
		}
		
		function GotoLoadForm(){			
			$("#div_form_ecust").hide(500);
			$("#div_form_load").show(500);	
		}
		
		function show_cust(log_customer_id){
			Loaddiv('div_customer','script_loading_customer.php','act=show_customer&log_customer_id='+log_customer_id+'&offset=0');
		}
		
		function show_blth_log(){
			var flagtrans = $("#flagtrans").val();
			var blth = $("#blth_show").val();
			Loaddiv('div_customer','script_loading_customer.php','act=log_customer&flagtrans='+flagtrans+'&blth='+blth+'&offset=0');
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
                    <table width="90%" border="0" cellpadding="0" cellspacing="0">
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
										<table width="80%" border="0" cellpadding="2" cellspacing="2" align="center">
											<tr>
												<td width="20%">Periode (BLTH)</td>
												<td width="80%">
													<select id="blth" name="blth" class="combobox">
														<option value="<?=GetBLTH(date('mY'),-1)?>"><?=GetBLTH(date('mY'),-1)?></option>
														<option value="<?=date('mY')?>" selected="selected"><?=date('mY')?></option>
														<option value="<?=GetBLTH(date('mY'),1)?>"><?=GetBLTH(date('mY'),1)?></option>
													</select>
												</td>
											</tr>
											<tr>
												<td height="30" colspan="2">&nbsp;</td>
											</tr>
											<tr>
												<td height="30" colspan="2" align="center" valign="middle">Silakan masukkan file berformat .txt</td>
											</tr>
											<tr>
												<td align="center" valign="middle" colspan="2">
													<input type="file" class="textbox_4" name="loading_file" id="loading_file" size="80" onchange="cekFile()" />
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
												<td width="20%">Nomor Rekening</td>
												<td width="80%">
													<input type='text' readonly id='txt_norek' name='txt_norek' size=25 value=''/>
												</td>
											</tr>
											<tr>
												<td width="20%">Email</td>
												<td width="80%">
													<input type='text' id='txt_email' name='txt_email' size=50 value=''/>
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
                        	<td align="left" height="30">Periode: 
                                <select id="blth_show" name="blth_show" class="combobox" onchange="show_blth_log()">
                                    <?php
                                    $sql_sel_blth = "SELECT blth
                                                    FROM log_customer
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
	Loaddiv('div_customer','script_loading_customer.php','act=log_customer&flagtrans='+flagtrans+'&blth='+blth+'&offset=0');
</script>
<?
	pg_close($con);
?>