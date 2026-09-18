<? require_once("../include/config_11.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/function.php"); ?>
<?
	$pr = trim($_GET["pr"]);
	$arr_pr = explode("|", $pr);
	$flagtrans = trim($arr_pr[0]);
	$menu_id = (int)trim($arr_pr[1]);
	
	$con = pg_connect($connection) or die("Could not connect to database!");
	$akses = userAcces($connection,$menu_id);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?= $title ?></title>
    </head>
    <script>
		function $e(){
			var elements = new Array();
			for (i=0; i < arguments.length; i++){
				var element = arguments[i];
				if (typeof element == 'string'){
					element = document.getElementById(element);
				}
				if (arguments.length == 1){
					return element;
				}
				elements.push(element);
			}
			return elements;
		}
		function cek(){
			var blth = $("#blth").val();
			//var obj = document.form_data;
			var xmlHttp = GetXmlHttpObject();
			var url = "ajax/blth.php";
			var par = "pr=<?= $flagtrans ?>|" + blth + "&mr=" + Math.random();
			if (!xmlHttp) {
				return;
			}
			xmlHttp.onreadystatechange = function() {
				var arrResponseText;
				if (xmlHttp.readyState == 4) {
					arrResponseText = xmlHttp.responseText.split("|");
					//document.getElementById("record_blth").value = arrResponseText[0];
					document.getElementById("record_cycle").value = "0";
					document.getElementById("div_cycle").innerHTML = arrResponseText[1];
					if (!document.getElementById("check_cycle").checked) {
						document.getElementById("cycle").disabled = true;
					}
				} else {
					//document.getElementById("record_blth").value = "loading";
					document.getElementById("record_cycle").value = "loading";
					document.getElementById("div_cycle").innerHTML = "<img src=\"../images/ajax.gif\" alt=\"loading...\" border=\"0\" />";
				}
			}
			xmlHttp.open("POST", url, true);
			xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xmlHttp.setRequestHeader("Content-length", par.length);
			xmlHttp.setRequestHeader("Connection", "close");
			xmlHttp.send(par);
			
			var blth = $("#blth").val();
			var tipe_rpt = $("#tipe_rpt").val();
			
			if(blth!='' && tipe_rpt!=''){
				$("#button_export").show(500);
			}else{
				$("#button_export").hide(500);
			}
		}
		function cek_tp(){
			var blth = $("#blth").val();
			var tipe_rpt = $("#tipe_rpt").val();
			
			if(blth!='' && tipe_rpt!=''){
				$("#button_export").show(500);
			}else{
				$("#button_export").hide(500);
			}
		}
		
		function CheckCycle() {
			if ($("#check_cycle").is(":checked")){
				$("#cycle").removeAttr("disabled");
			}else{
				$("#cycle").val(0);
				$("#cycle").attr("disabled",true);				
			}
		}
		
		function ChangeCycle(val){
			var url = "ajax/cycle.php";
			var par = "pr=<?= $flagtrans ?>|" + $("#blth").val() + "|" + val + "&mr=" + Math.random();
			$.post(url +'?'+ par,
				function(respon){
					arrResponseText = respon.split("|");
					$('#record_cycle').val(arrResponseText[0]);
				}
			);
		}
		
		function ChangeTglKirim() {
			if ($("#ck_tglkirim").is(":checked")) {
				$("#day_terima_from").removeAttr("disabled");
				$("#month_terima_from").removeAttr("disabled");
				$("#year_terima_from").removeAttr("disabled");
				$("#day_terima_to").removeAttr("disabled");
				$("#month_terima_to").removeAttr("disabled");
				$("#year_terima_to").removeAttr("disabled");
				$("#hour_terima_from").removeAttr("disabled");
				$("#minute_terima_from").removeAttr("disabled");
				$("#hour_terima_to").removeAttr("disabled");
				$("#minute_terima_to").removeAttr("disabled");
			}else{
				$("#day_terima_from").attr("disabled",true);
				$("#month_terima_from").attr("disabled",true);
				$("#year_terima_from").attr("disabled",true);
				$("#day_terima_to").attr("disabled",true);
				$("#month_terima_to").attr("disabled",true);
				$("#year_terima_to").attr("disabled",true);
				$("#hour_terima_to").attr("disabled",true);
				$("#minute_terima_to").attr("disabled",true);
				$("#hour_terima_from").attr("disabled",true);
				$("#minute_terima_from").attr("disabled",true);
			}
		}
		function export_rpt(){
			var blth = $("#blth").val();
			var cycle;
			var periode_kirim;
			var check_email;
			
			if ($("#check_email").is(":checked")) {
				check_email = $("#check_email").val();
			} else {
				check_email = '';
			}
			
			if ($("#check_cycle").is(":checked")) {
				cycle = $("#cycle").val();
			} else {
				cycle = '';
			}
			
			if ($("#ck_tglkirim").is(":checked")) {
				var sent_from = ''+$("#year_terima_from").val() +'-'+ $("#month_terima_from").val() +'-'+ $("#day_terima_from").val() +' '+ $("#hour_terima_from").val() +':'+ $("#minute_terima_from").val()+'';
				var sent_to = ''+$("#year_terima_to").val() +'-'+ $("#month_terima_to").val() +'-'+ $("#day_terima_to").val() +' '+ $("#hour_terima_to").val() +':'+ $("#minute_terima_to").val()+'';
				periode_kirim = ''+sent_from+'|'+sent_to+'';
			} else {
				periode_kirim = '';
			}
			
			var tipe_rpt = $("#tipe_rpt").val();
			var pr = $("#pr").val();
			
			if (tipe_rpt == 'xls'){
				//window.location.href='script_summary_detail.php?act=xls&blth='+blth+'&pr='+pr+'&cycle='+cycle+'&periode_kirim='+periode_kirim+'&cekemail='+check_email;				
				window.location.href='script_summary_detail_rev_11.php?act=xls&blth='+blth+'&pr='+pr+'&cycle='+cycle+'&periode_kirim='+periode_kirim+'&cekemail='+check_email;				
			}else{
				//window.open('script_summary_detail.php?act='+tipe_rpt+'&blth='+blth+'&pr='+pr+'&cycle='+cycle+'&periode_kirim='+periode_kirim+'&cekemail='+check_email);
				window.open('script_summary_detail_rev_11.php?act='+tipe_rpt+'&blth='+blth+'&pr='+pr+'&cycle='+cycle+'&periode_kirim='+periode_kirim+'&cekemail='+check_email);
			}
		}
	</script>
    <body>
		<? require_once("../include/script.php"); ?>
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
                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td>
                                <? require_once("../include/page_title.php"); ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center">
                            	<div id="div_filter">
                                <table width="70%" border="0" cellpadding="0" cellspacing="0">
                                	<input type="hidden" id="pr" name="pr" value="<?=$pr?>" />
                                	<tr>
                                    	<td width="20%" height="30" align="left">Periode Loading</td>
                                        <td colspan=2 align="left">
                                        	<div id="div_blth">
                                                <select id="blth" name="blth" class="combobox">
                                                </select>
                                            </div>
                                        </td>
										<td align="left">
											<label style="cursor:pointer"><input type="checkbox" value="1" id="check_email" name="check_email">Cek Bounce Back</label>
										</td>
                                    </tr>
									<tr>
										<td height="36" align="left" valign="middle">Cycle</td>
										<td width="1%" align="left" valign="middle"><input id="check_cycle"  name="check_cycle" type="checkbox" onclick="CheckCycle();" /></td>
										<td width="300" align="left" valign="middle"><div id="div_cycle">
											<select id="cycle" name="cycle" class="combobox">
												<option value="0" selected="selected"></option>
											</select>
											</div>										  </td>
										<td align="left" valign="middle"><input id="record_cycle" name="record_cycle" type="text" class="textbox_1" value="0" disabled="disabled" /> record								 </td>
									</tr>
									<tr>
										<td height="36" align="left">Tanggal Kirim</td>
										<td align="left" valign="middle">
											<input id="ck_tglkirim" name="ck_tglkirim" type="checkbox" onclick="ChangeTglKirim();" />
                                        </td>
                                        <td colspan="2" align="left" valign="middle">
                                        	<select id="day_terima_from" name="day_terima_from" class="combobox">
												<? for ($i = 1; $i <= 31; $i++) { ?>
												<option value="<?= str_pad($i, 2, "0", STR_PAD_LEFT) ?>"<? if ($i == 1) { echo(" selected=\"selected\""); } ?>><?= str_pad($i, 2, "0", STR_PAD_LEFT) ?></option>
												<? } ?>
											</select>
											<select id="month_terima_from" name="month_terima_from" class="combobox">
												<? for ($i = 1; $i <= 12; $i++) { ?>
												<option value="<?= str_pad($i, 2, "0", STR_PAD_LEFT) ?>"<? if ($i == date("m")) { echo(" selected=\"selected\""); } ?>><?= date("M", mktime(0, 0, 0, $i, 1, 2000)) ?></option>
												<? } ?>
											</select>
											<select id="year_terima_from" name="year_terima_from" class="combobox">
												<? for ($i = 2009; $i <= date("Y"); $i++) { ?>
												<option value="<?= $i ?>"<? if ($i == date("Y")) { echo(" selected=\"selected\""); } ?>><?= $i ?></option>
												<? } ?>
											</select> : 
											<select id="hour_terima_from" name="hour_terima_from" class="combobox">
												<? for ($i = 0; $i <= 23; $i++) { ?>
												<option value="<?= str_pad($i, 2, "0", STR_PAD_LEFT) ?>"<? if ($i == 0) { echo(" selected=\"selected\""); } ?>><?= str_pad($i, 2, "0", STR_PAD_LEFT) ?></option>
												<? } ?>
											</select>
											<select id="minute_terima_from" name="minute_terima_from" class="combobox">
												<? for ($i = 0; $i <= 59; $i++) { ?>
												<option value="<?= str_pad($i, 2, "0", STR_PAD_LEFT) ?>"<? if ($i == 0) { echo(" selected=\"selected\""); } ?>><?= str_pad($i, 2, "0", STR_PAD_LEFT) ?></option>
												<? } ?>
											</select>
											s/d 
                                            <select id="day_terima_to" name="day_terima_to" class="combobox">
												<? for ($i = 1; $i <= 31; $i++) { ?>
                                                <option value="<?= str_pad($i, 2, "0", STR_PAD_LEFT) ?>"<? if ($i == date("d")) { echo(" selected=\"selected\""); } ?>>
                                                <?= str_pad($i, 2, "0", STR_PAD_LEFT) ?>
                                                </option>
                                                <? } ?>
                                            </select>
                                            <select id="month_terima_to" name="month_terima_to" class="combobox">
												<? for ($i = 1; $i <= 12; $i++) { ?>
                                                <option value="<?= str_pad($i, 2, "0", STR_PAD_LEFT) ?>"<? if ($i == date("m")) { echo(" selected=\"selected\""); } ?>>
                                                <?= date("M", mktime(0, 0, 0, $i, 1, 2000)) ?>
                                                </option>
                                                <? } ?>
                                            </select>
                                            <select id="year_terima_to" name="year_terima_to" class="combobox">
												<? for ($i = 2009; $i <= date("Y"); $i++) { ?>
                                                <option value="<?= $i ?>"<? if ($i == date("Y")) { echo(" selected=\"selected\""); } ?>>
                                                <?= $i ?>
                                                </option>
                                                <? } ?>
                                            </select>
                                            :
                                            <select id="hour_terima_to" name="hour_terima_to" class="combobox">
												<? for ($i = 0; $i <= 23; $i++) { ?>
                                                <option value="<?= str_pad($i, 2, "0", STR_PAD_LEFT) ?>"<? if ($i == 23) { echo(" selected=\"selected\""); } ?>>
                                                <?= str_pad($i, 2, "0", STR_PAD_LEFT) ?>
                                                </option>
                                                <? } ?>
                                            </select>
                                            <select id="minute_terima_to" name="minute_terima_to" class="combobox">
												<? for ($i = 0; $i <= 59; $i++) { ?>
                                                <option value="<?= str_pad($i, 2, "0", STR_PAD_LEFT) ?>"<? if ($i == 59) { echo(" selected=\"selected\""); } ?>>
                                                <?= str_pad($i, 2, "0", STR_PAD_LEFT) ?>
                                                </option>
                                                <? } ?>
                                            </select>
										</td>
									</tr>
							    	<tr>
                                    	<td align="left" height="36">Tipe Report</td>
                                        <td colspan=3 align="left">
											<select id="tipe_rpt" name="tipe_rpt" class="combobox" onchange="cek_tp()">
												<option value="">Pilih</option>
												<? if (substr($akses, 0, 1) == "1") { ?>
												<option value="view">VIEW</option>
												<? } ?>
												<? if (substr($akses, 1, 1) == "1") { ?>
												<option value="xls">Ekspor ke .XLS</option>
												<? } ?>
                                                <? if (substr($akses, 1, 1) == "1") { ?>
												<option value="ba">Berita Acara</option>
												<? } ?>
											</select>		
                                        </td>
                                    </tr>
                                </table>
                                </div>
                            </td>
                        </tr>
                        <tr>
                        	<td height="30">&nbsp;</td>
                        </tr>
                        <tr>
                        	<td align="center">
                            	<input type="button" class="button" id="button_export" name="button_export" value="EXPORT" style="display:none" onclick="export_rpt()" />
                            </td>
                        </tr>
                        <tr>
                        	<td align="center"><div id="div_show"></div>
                            </td>
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
	Loaddiv('div_blth','script_summary_detail_11.php','act=blth&flagtrans=<?=$flagtrans?>');
	ChangeTglKirim();
</script>
<?
	pg_close($con);
?>