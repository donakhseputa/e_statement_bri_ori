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
        <title><?php echo  $title ?></title>
    </head>
    <script>
	
		/*
        function cekFile(){
            if(($("#loading_file")!='')&&($("#loading_file_customer")!='')){
				if($("#loading_file").val().substr(-10).toLowerCase()=='.ccstmncbs'){
					if($("#loading_file_customer").val().substr(-4).toLowerCase()=='.txt'){
                	$(".button").show(500);
					}
				}else{
					$(".button").hide(500);
				}
            }
        }
		*/
		
		function cekFile(){
            if(($("#loading_file")!='')){
				//if($("#loading_file").val().substr(-4).toLowerCase()=='.txt'){
					
				if($("#loading_file").val().substr(-4).toLowerCase()=='.xls' || $("#loading_file").val().substr(-5).toLowerCase()=='.xlsx'){	
                	$(".button").show(500);
				}else{
					$(".button").hide(500);
				}
            }
        }
		
        function cekFileCustomer(){
            if($("#loading_file_customer")!=''){
				if($("#loading_file_customer").val().substr(-4).toLowerCase()=='.txt'){
                	}else{
					alert("FILE MASTER CUSTOMER HARUS BERTIPE *.TXT!");
					document.form_upload_detail.loading_file_customer.value='';
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
				if(product=='SL'){
					$("#form_upload_detail").attr('action','proses_loading_detail_sl.php?pr='+pr+'|excel_to_pdf');
				}else{
					alert('PRODUK BELUM TERDAFTAR, HUBUNGI APPDEV');
					return false;
				}
				return true;
			}else{
				return false;
			}
		}
		
		function showRecord(){
			var blth = $("#blth").val();
			
			var pr = $("#pr").val();
			var arr_pr = pr.split('|');
			var product = arr_pr[0];
			
			if(product=='SL'){
				$("#span_record").html("<img src='../images/ajax.gif' align='center' >");
				$.post("script_loading_detail_sl_excel.php",{pr:pr+'|show_record|'+blth},function(respon){
					$("#span_record").html('<input type="text" name="jml_record" id="jml_record" class="textbox_1" disabled="disabled" value="'+respon+'" />');
				});
				//$("#span_file_mcustomer").html("<img src='../images/ajax.gif' align='center' >");
				//$.post("script_loading_detail_sl_excel.php",{pr:pr+'|show_file_mcustomer|'+blth},function(respon){
				//	$("#span_file_mcustomer").html(respon);
				//});
			}else{
				alert('PRODUK BELUM TERDAFTAR, HUBUNGI APPDEV');
			}
		}
		
		function show_det(m_loading_id){
		var flagtrans = $("#flagtrans").val();
		var blth = $("#blth_show").val();
			var input_search = $("#input_search_det").val();
			input_search = input_search.replace(' ','8764346466435364647768799667654537543756');
			Loaddiv('div_detail','script_loading_detail.php','act=show_detail&m_loading_id='+m_loading_id+'&blth='+blth+'&flagtrans='+flagtrans+'&offset=0&input_search='+input_search);
			$("#span_search_log").hide();
			$("#span_search_det").show();
		}
		
		function show_det2(){
			var flagtrans = $("#flagtrans").val();
			var blth = $("#blth_show").val();
			var m_loading_id = $("#m_loading_id").val();
			var input_search = $("#input_search_det").val();
			input_search = input_search.replace(' ','8764346466435364647768799667654537543756');
			Loaddiv('div_detail','script_loading_detail.php','act=show_detail&m_loading_id='+m_loading_id+'&flagtrans='+flagtrans+'&blth='+blth+'&offset=0&input_search='+input_search);
			$("#span_search_log").hide();
			$("#span_search_det").show();
		}
		
		function statusMenu(val,vul){
			var satu = val;
			var dua = vul;
			//alert(satu + ' ' + dua);
		}
		
		function show_blth_log(){
			var flagtrans = $("#flagtrans").val();
			var blth = $("#blth_show").val();
			var input_search = $("#input_search_log").val();
			input_search = input_search.replace(' ','8764346466435364647768799667654537543756');
			Loaddiv('div_detail','script_loading_detail.php','act=log_detail&flagtrans='+flagtrans+'&blth='+blth+'&offset=0&input_search='+input_search);
			$("#span_search_det").hide();
			$("#span_search_log").show();
		}
		
		function gen_gagal_data(m_loading_id){
			var flagtrans = $("#flagtrans").val();
			var blth = $("#blth_show").val();
			//var url = 'script_loading_detail.php','act=gen_gagal_data&m_loading_id='+m_loading_id;
			var cek = 'Aktivitas ini akan mengenerate data gagal \n m_loading_id: '+m_loading_id+'\n Apakah Anda yakin ?';
			if(confirm(cek))
				//var win = window.open('download_gagal_excel.php?m_loading_id='+m_loading_id, '_blank');
				//win.focus();
				
				 $.ajax({
				 url: 'download_gagal_excel.php',
				 type: "POST",
				 data: {
					m_loading_id: m_loading_id
				 },
				 success: function(){window.open(someUrl);},
				 async: false
				});
				//Loaddiv('div_detail','script_loading_detail.php','act=gen_gagal_data&m_loading_id='+m_loading_id);
		}
		
		
		function del_data(m_loading_id){
			var flagtrans = $("#flagtrans").val();
			var blth = $("#blth_show").val();
			var cek = 'Aktivitas ini akan menghapus data di detail dan daftar antrian\nAnda yakin ?';
			if(confirm(cek))
				Loaddiv('div_detail','script_loading_detail.php','act=del_data&m_loading_id='+m_loading_id);
				Loaddiv('div_detail','script_loading_detail.php','act=log_detail&flagtrans='+flagtrans+'&blth='+blth+'&offset=0');
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
                    <table width="90%" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td>
                                <?php require_once("../include/page_title.php"); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <form method="post" enctype="multipart/form-data" id="form_upload_detail" name="form_upload_detail" onsubmit="return cekForm()">
                                    <input type="hidden" id="pr" name="pr" value="<?php echo $pr?>" />
                                    <input type="hidden" id="flagtrans" name="flagtrans" value="<?php echo $flagtrans?>" />
                                    <table width="80%" border="0" cellpadding="2" cellspacing="2" align="center">
										<tr>
											<td colspan="4">PENAMAAN FILE EXCEL<br>
											Reguler : <b>SURAT_LUNAS_06MEI2019TE.XLSX</b><br>
											<br>
											<br>
											<b>note: "PEMISAH NAMA FILE HARUS MENGGUNAKAN UNDERSCORE ('_') JANGAN STRIP"</b>
											<br>
											<br>
											</td>
										</tr>
										
                                        <tr>
                                            <td width="20%">Periode (BLTH)</td>
                                            <td width="15%">
                                                <select id="blth" name="blth" class="combobox" onchange="showRecord();">
                                                    <option value="<?php echo GetBLTH(date('mY'),-2)?>"><?php echo GetBLTH(date('mY'),-2)?></option>
                                                    <option value="<?php echo GetBLTH(date('mY'),-1)?>"><?php echo GetBLTH(date('mY'),-1)?></option>
                                                    <option value="<?php echo date('mY')?>" selected="selected"><?php echo date('mY')?></option>
                                                    <option value="<?php echo GetBLTH(date('mY'),1)?>"><?php echo GetBLTH(date('mY'),1)?></option>
                                                </select>
                                            </td>
                                            <td width="65%">
                                            	<span id="span_record"></span> record
                                            </td>
                                        </tr>
										<!--
                                        <tr>
                                            <td width="20%">Master Customer</td>
                                            <td width="15%" colspan="2">
                                            	<div id="span_file_mcustomer">
                                                    <select id="file_mcustomer" name="file_mcustomer" class="combobox">
                                                    </select>
                                                </div> 
												<i><font size="-4"><input type="file" class="textbox_4" name="loading_file_customer" id="loading_file_customer" size="80" onChange="cekFileCustomer()"/>
												</font></i>
                                            </td>
                                        </tr>
										
                                        <tr>
                                            <td width="20%"></td>
                                            <td colspan="2" style="font-size:11px;"><i>Silahkan masukkan file bertipe <b>*.txt</i><br/>Contoh Format:</b> <a target="_blank" href='../tmp/sampel_mcust_txt.txt'>klik disini</a></td>
                                        </tr>
										<input type="hidden" name="rad_cust" id="rad_cust1" value="1"/>
										-->
										
                                        <tr>
                                            <td height="30" colspan="3">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td height="30" colspan="3" align="center" valign="middle">Silakan masukkan file berformat <b><i>.xls</i></b> atau <b><i>.xlsx</i></b></td>
                                        </tr>
                                        <tr>
                                            <td align="center" valign="middle" colspan="3">
                                                <input type="file" class="textbox_4" name="loading_file" id="loading_file" size="80" onchange="cekFile()" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" height="50">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" align="center" valign="middle">
                                                <input type="submit" class="button" value="UPLOAD" style="display:none" />
                                            </td>
                                        </tr>
                                    </table>
                                </form>
                            </td>
                        </tr>
                        <tr>
                        	<td>
                            	<table width="100%">
                                    <tr>
                                        <td align="left" height="50%">Periode: 
                                            <select id="blth_show" name="blth_show" class="combobox" onchange="show_blth_log()">
                                                <?php
                                                $sql_sel_blth = "SELECT blth
                                                                FROM m_loading
                                                                WHERE flagtrans = '$flagtrans'
                                                                GROUP BY blth
                                                                ORDER BY substring(blth,3,4) DESC, substring(blth,1,2) DESC
																
																";
                                                $qry_sel_blth = pg_query($sql_sel_blth) or die('ERROR '.$sql_sel_blth);
                                                while($row_blth=pg_fetch_array($qry_sel_blth)){
                                                    ?>
                                                    <option value="<?php echo $row_blth['blth']?>"
                                                        <?php
                                                        if($blth==date('mY')){
                                                            echo 'selected';
                                                        }
                                                        ?>><?php echo $row_blth['blth']?></option>
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
                        	<td align="center"><div id="div_detail"></div></td>
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
	showRecord();
	//check_customer();
	
	var flagtrans = $("#flagtrans").val();
	var blth = $("#blth_show").val();
	//alert (blth);
	Loaddiv('div_detail','script_loading_detail.php','act=log_detail&flagtrans='+flagtrans+'&blth='+blth+'&offset=0');
	$("#span_search_log").show();
</script>
<?
	pg_close($con);
?>