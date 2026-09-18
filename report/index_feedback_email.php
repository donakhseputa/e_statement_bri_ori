<? require_once("../include/config.php"); ?>
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
		function fg_check_nama_file(){
			var blth = $("#blth").val();
			var flagtrans = $("#flagtrans").val();
			
			if(blth!=''){
				$("#check_nama_file").removeAttr('disabled');
			}else{
				$("#check_nama_file").removeAttr('checked');
				$("#check_nama_file").attr('disabled','disabled');
				$("#nama_file").val('');
				$("#nama_file").attr('disabled','disabled');
			}
			Loaddiv('span_rec_blth','script_feedback_email.php','act=rec_blth&blth='+blth+'&flagtrans='+flagtrans);
		}
		
		function show_nama_file(){
			if($("#check_nama_file").attr('checked')){
				nama_file();
			}else{
				$("#nama_file").val('');
				$("#nama_file").attr('disabled','disabled');
			}
		}
		
		function nama_file(blth){
			var flagtrans = $("#flagtrans").val();
			var blth = $("#blth").val();
			Loaddiv('div_nama_file','script_feedback_email.php','act=nama_file&blth='+blth+'&flagtrans='+flagtrans);
		}
		
		function button_export(){
			var blth = $("#blth").val();
			var report_menu = $("#report_menu").val();
			var tipe_report = $("#tipe_report").val();
			
			if(blth!='' && report_menu!=0 && tipe_report!=0){
				$("#button_export").show(500);
			}else{
				$("#button_export").hide(500);
			}
		}
		
		function cekForm(){
			var flagtrans = $("#flagtrans").val();
			var blth = $("#blth").val();
			var nama_file = $("#nama_file").val();
			
			if(blth!=''){
				if($("#check_nama_file").attr('checked')){
					if(nama_file!=''){
						return true;
					}else{
						alert('Nama File can not empty !');
						return false;
					}
				}
				return true;
			}else{
				alert('Periode can not empty !');
				return false;
			}
		}
		
		function show_report(){
			var flagtrans = $("#flagtrans").val();
			var blth = $("#blth").val();
			var nama_file = $("#nama_file").val();
			var report_menu = $("#report_menu").val();
			var tipe_report = $("#tipe_report").val();
			
			var cek = cekForm();
			if(cek){
				window.open('script_feedback_email.php?act=show_report&flagtrans='+flagtrans+'&blth='+blth+'&nama_file='+nama_file+'&report_menu='+report_menu+'&tipe_report='+tipe_report);
				/*$("#div_show").html('<img src=<img src="../images/ajax.gif" align="center"><img src="../images/ajax.gif" align="center"><img src="../images/ajax.gif" align="center"><img src="../images/ajax.gif" align="center"><img src="../images/ajax.gif" align="center">');
				$.post('script_feedback_email.php?act=show_report',$("#form_feedback").serialize(),function(respon){
					window.open(respon);
					//alert(respon);
				});*/
			//Loaddiv('div_show','script_feedback_email.php','act=show_report&flagtrans='+flagtrans+'&blth='+blth+'&nama_file='+nama_file);
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
                                	<form name="form_feedback" id="form_feedback">
                                        <table width="90%" border="0" cellpadding="0" cellspacing="0">
                                            <input type="hidden" id="pr" name="pr" value="<?=$pr?>" />
                                            <input type="hidden" id="flagtrans" name="flagtrans" value="<?=$flagtrans?>" />
                                            <tr>
                                                <td height="30" width="20%">Periode Loading</td>
                                                <td width="5%">&nbsp;</td>
                                                <td width="20%">
                                                    <div id="div_blth">
                                                        <select id="blth" name="blth" class="combobox">
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span id="span_rec_blth">
                                                    <input type="text" class="textbox_1" disabled="disabled" value="0" />
                                                    </span> record
                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="30">Nama File</td>
                                                <td>
                                                    <input type="checkbox" id="check_nama_file" name="check_nama_file" disabled="disabled" onclick="show_nama_file()" />
                                                </td>
                                                <td>
                                                    <div id="div_nama_file">
                                                        <select id="nama_file" class="combobox" disabled="disabled"></select>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="30">Feedback Report</td>
                                                <td>&nbsp;</td>
                                                <td>
                                                    <select name="report_menu" id="report_menu" class="combobox" onchange="button_export()">
                                                        <option value="0"></option>
                                                        <option value="1">Sukses Kirim</option>
                                                        <option value="2">Gagal Kirim</option>
                                                        <option value="3">other</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="30">Tipe Report</td>
                                                <td>&nbsp;</td>
                                                <td>
                                                    <select name="tipe_report" id="tipe_report" class="combobox" onchange="button_export()">
                                                        <option value="0"></option>
                                                        <option value="1">VIEW</option>
                                                        <option value="2">XLS</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <tr>
                        	<td height="30">&nbsp;</td>
                        </tr>
                        <tr>
                        	<td height="30">&nbsp;</td>
                        </tr>
                        <tr>
                        	<td align="center">
                            	<input type="button" class="button" id="button_export" name="button_export" value="EXPORT" style="display:none" onclick="show_report()" />
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
	Loaddiv('div_blth','script_feedback_email.php','act=blth&flagtrans=<?=$flagtrans?>');
</script>
<?
	pg_close($con);
?>