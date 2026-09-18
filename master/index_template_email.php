<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/function.php"); ?>
<?
	$pr = trim($_GET["pr"]);
	$arr_pr = explode("|", $pr);
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
        function cek_produk(){
            if($("#combo_produk").val()!=''){
				$("#button_create").show(500);
			}else{
				$("#button_create").hide(500);
				cek_template();
			}
        }
		
        function cek_template(){
            if($("#combo_produk").val()!=''){
				if($("#combo_template").val()!=''){
					$("#button_view").show(500);
					$("#button_delete").show(500);
				}else{
					$("#button_view").hide(500);
					$("#button_delete").hide(500);
				}
			}else{
				$("#button_view").hide(500);
				$("#button_delete").hide(500);
				combo_template_fg($("#combo_template").val());
			}
        }
		
		function combo_template_fg(flagtrans){
			Loaddiv('div_template','script_template_email.php','act=combo_template&flagtrans='+flagtrans);
		}
		
		function view_template(){
			var flagtrans = $("#combo_produk").val();
			var template_id = $("#combo_template").val();
			$.post('script_template_email.php?act=view_template&flagtrans='+flagtrans+'&template_id='+template_id,$("#form_template").serialize(),function(respon){
				$.fancybox(respon);
			});
		}
		
		function create_template(){
			var flagtrans = $("#combo_produk").val();
			$.post('script_template_email.php?act=create_template&flagtrans='+flagtrans,$("#form_template").serialize(),function(respon){
				$.fancybox(respon);
			});
		}
		
		function delete_template(){
			var flagtrans = $("#combo_produk").val();
			var template_id = $("#combo_template").val();
			var tanya_delete = confirm('Yakin akan dihapus ?');
			if(tanya_delete){
				$.post('script_template_email.php?act=delete_template&flagtrans='+flagtrans+'&template_id='+template_id,$("#form_template").serialize(),function(respon){
					alert(respon);
					window.location.reload();
				});
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
                                <? require_once("../include/page_title.php"); ?>
                            </td>
                        </tr>
                        <tr>
                        	<td>
                            	<form id="form_template" name="form_template">
                                    <table width="100%">
                                        <tr>
                                            <td width="15%" height="30">Produk</td>
                                            <td>
                                                <div id="div_produk">
                                                    <select id="combo_produk" name="combo_produk" class="combobox">
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td height="30">Template</td>
                                            <td>
                                                <div id="div_template">
                                                    <select id="combo_template" name="combo_template" class="combobox">
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td height="30">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td align="center" width="100%" colspan="2">
                                                <table width="50%" align="center">
                                                    <tr>
                                                        <td align="right" width="30%">
                                                            <input type="button" id="button_view" name="button_view" class="button" value="VIEW" onclick="view_template()" style="display:none" />
                                                        </td>
                                                        <td width="5%">&nbsp;</td>
                                                        <td align="center" width="30%">
                                                            <input type="button" id="button_create" name="button_create" class="button" value="CREATE" onclick="create_template()" style="display:none" />
                                                        </td>
                                                        <td width="5%">&nbsp;</td>
                                                        <td align="left">
                                                            <input type="button" id="button_delete" name="button_delete" class="button" value="DELETE" onclick="delete_template()" style="display:none" />
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>                                    
                                </form>
                            </td>
                        </tr>
                        <tr>
                        	<td width="100%" align="center">
                            	<div id="div_create">
                                </div>
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
            <input type="hidden" id="n" value="0"  />                        
        </table>
    </body>
</html>
<script>
	Loaddiv('div_produk','script_template_email.php','act=combo_produk');
</script>
<?
	pg_close($con);
?>