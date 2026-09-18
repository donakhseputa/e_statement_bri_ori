<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/function.php"); ?>
<?
	$pr = trim($_GET["pr"]);
	$arr_pr = explode("|", $pr);
	$menu_id = (int)trim($arr_pr[1]);
	$con = pg_connect($connection) or die("Could not connect to database!");
	
	$i = 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?= $title ?></title>
    </head>
    <script>
		function add_server(){
			$.post('script_mail_server.php?act=add_server','',function(respon){
				$.fancybox(respon);
			});
		}
		
		function save(){
			$.post('script_mail_server.php?act=save',$("#form_add").serialize(),function(respon_save){
				if(respon_save=='sukses'){
					alert('Berhasil disimpan');
					parent.jQuery.fancybox.close();
					Loaddiv('div_mail_server','script_mail_server.php','act=show_table');
				}else{
					alert(respon_save);
				}
			});
		}
		
		function edit_server(val){
			$.post('script_mail_server.php?act=edit_server&mail_server_id='+val,'',function(respon_edit){
				$.fancybox(respon_edit);
			});
		}

		function test_server(val){
			$.post('script_mail_server.php?act=test_server&mail_server_id='+val,'',function(respon_edit){
				$.fancybox(respon_edit);
			});
		}
		
		function test_kirim(val){
			$.post('script_mail_server.php?act=test_kirim&mail_server_id='+val,'',function(respon_edit){
				$.fancybox(respon_edit);
			});
		}
		
		function view_inbox(val){
			$.post('script_mail_server.php?act=view_inbox&mail_server_id='+val,'',function(respon_edit){
				$.fancybox(respon_edit);
			});
		}
		function edit(val){
			$.post('script_mail_server.php?act=edit&mail_server_id='+val,$("#form_edit").serialize(),function(respon_edit){
				if(respon_edit=='sukses'){
					alert('Berhasil di-update');
					parent.jQuery.fancybox.close();
					Loaddiv('div_mail_server','script_mail_server.php','act=show_table');
				}else{
					alert(respon_edit);
				}
			});
		}
		
		function delete_conf(val){
			var cek = confirm('Hapus ?');
			if(cek){
				delete_server(val);
			}
		}
		
		function delete_server(val){
			$.post('script_mail_server.php?act=delete_server&mail_server_id='+val,'',function(respon_delete){
				if(respon_delete=='sukses'){
					alert('Berhasil dihapus');
					Loaddiv('div_mail_server','script_mail_server.php','act=show_table');
				}else{
					alert(respon_delete);
				}
			});
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
                            	<div id="div_mail_server">
                                    <table width="100%" cellpadding="0" cellspacing="0" border="1" style="border:1px;border-style:solid">
                                    	<tr class="table_header">
                                        	<td colspan="2">EMAIL FROM</td>
                                            <td colspan="3">FEEDBACK EMAIL</td>
                                            <td width="10%" rowspan="2">User</td>
                                            <td width="17%" rowspan="2">Waktu</td>
                                            <td width="13%" rowspan="2">Status</td>
                                            <td rowspan="2">ACT</td>
                                        </tr>
                                        <tr class="table_header">
                                            <td width="13%">Email Host</td>
                                            <td width="13%">From</td>
                                            <td width="13%">Feedback Host</td>
                                            <td width="18%">Inbox</td>
                                            <td width="10%">Password</td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        <tr>
                        	<td height="30">&nbsp;</td>
                        </tr>
                        <tr>
                        	<td height="30" align="center">
                            	<input type="button" name="button_add" id="button_add" value="ADD" class="button" onclick="add_server()" />
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
	Loaddiv('div_mail_server','script_mail_server.php','act=show_table');
</script>
<?
	pg_close($con);
?>