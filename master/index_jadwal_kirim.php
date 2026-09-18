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
		function add_jadwal(){
			$.post('script_jadwal_kirim.php?act=add_jadwal','',function(respon){
				$.fancybox(respon);
				$( "#tgl_jadwal" ).datepicker({dateFormat: 'yy-mm-dd'});
			});
		}
		
		function save(){
			$.post('script_jadwal_kirim.php?act=save',$("#form_add").serialize(),function(respon_save){
				if(respon_save=='sukses'){
					alert('Berhasil disimpan');
					parent.jQuery.fancybox.close();
					Loaddiv('div_jadwal','script_jadwal_kirim.php','act=show_table');
				}else{
					alert(respon_save);
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
			$.post('script_jadwal_kirim.php?act=delete_jadwal&jadwal_id='+val,'',function(respon_delete){
				if(respon_delete=='sukses'){
					alert('Berhasil dihapus');
					Loaddiv('div_jadwal','script_jadwal_kirim.php','act=show_table');
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
                            	<div id="div_jadwal">
                                    <table width="100%" cellpadding="0" cellspacing="0" border="1" style="border:1px;border-style:solid">
                                        <tr class="table_header">
                                            <td width="25%">Jadwal</td>
                                            <td width="20%">Produk</td>
                                            <td width="15%">User</td>
                                            <td width="20%">Create</td>
                                            <td width="10%">Status</td>
                                            <td width="10%">Delete</td>
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
                            	<input type="button" name="button_add" id="button_add" value="ADD" class="button" onclick="add_jadwal()" />
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
	Loaddiv('div_jadwal','script_jadwal_kirim.php','act=show_table');
</script>
<?
	pg_close($con);
?>