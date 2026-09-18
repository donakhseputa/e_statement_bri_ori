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
            if($("#blth").val()!='' && $("#combo_template").val()!=''){
				$("#button_kirim").show(500);
			}else{
				$("#button_kirim").hide(500);
			}
        }
		
		function cekBlth(){
			 if($("#blth").val()==''){
				 $("#checkbox_nama_file").removeAttr('checked');
				 $("#checkbox_nama_file").attr('disabled','disabled');
				 $("#check_attach").removeAttr('checked');
				 $("#check_attach").attr('disabled','disabled');
				 cek_attach();
				 oncheck_file();
			 }
		}
		
		function cekForm(){
			var blth = $("#blth").val();
			var nama_file = $("#nama_file").val();
			var norek = $("#norek").val();
			var jadwal_id = $("#jadwal_id").val();
			
			var pr = $("#pr").val();
			var arr_pr = pr.split('|');
			var product = arr_pr[0];
			
			if(jadwal_id!=''){
				if(blth!=''){
					if($("#checkbox_nama_file").attr('checked')==true){
						if(nama_file!=''){
							if($("#checkbox_norek").attr('checked')==true){
								if(norek!=''){
									return true;
								}else{
									alert('Nomor Rekening can not empty !!!');
									return false;
								}
							}
							return true;
						}else{
							alert('Nama File can not empty !!!');
							return false;
						}
					}
					return true;
				}else{
					alert('Periode can not empty !!!');
					return false;
				}
			}else{
				alert('Jadwal can not empty !!!');
				return false;
			}
		}
		
		function kirim(){
			var cek = cekForm();
			if(cek==true){
				$("#div_button").html('<img src="../images/ajax.gif" align="center"><img src="../images/ajax.gif" align="center"><img src="../images/ajax.gif" align="center"><img src="../images/ajax.gif" align="center"><img src="../images/ajax.gif" align="center">');
				$.post('script_jadwal_kirim_antrian.php?act=kirim',$("#form_antrian").serialize(),function(respon){
					var arr_respon = respon.split('|');
					var flagtrans = arr_respon[0];
					var menuid = arr_respon[1];
					var jumlah = arr_respon[2];
					var exists = arr_respon[3];
					$("#div_respon").html(jumlah+' record telah dikirim ke antrian');
					$("#div_exists").html('Daftar nomor rekening yang sudah ada di antrian:<br>'+exists);
					$("#div_button").html('<input type="button" id="button_selesai" name="button_selesai" class="button" value="SELESAI" onclick="goto(\''+flagtrans+'|'+menuid+'\')" />');
				});
			}
		}
		
		function oncheck_file(){
			if($("#checkbox_nama_file").attr('checked')==true){
				var pr = $("#pr").val();
				var blth = $("#blth").val();
				Loaddiv('div_file_loading','script_jadwal_kirim_antrian.php','act=nama_file&blth='+blth+'&flagtrans=<?=$flagtrans?>');
			}else{
				$("#nama_file").val('');
				$("#nama_file").attr('disabled','disabled');
				$("#norek").val('');
				$("#norek").attr('disabled','disabled');
				$("#checkbox_norek").attr('disabled','disabled');
				$("#checkbox_norek").removeAttr('checked');
				$("input[name=namafile_record]").val('0');
				clear_sample();
			}
		}
		
		function oncheck_norek(){
			if($("#checkbox_norek").attr('checked')==true){
				var blth = $("#blth").val();
				var nama_file = $("#nama_file").val();
				var pr = $("#pr").val();
				var arr_pr = pr.split('|');
				$("#text_sample_email").val('');
				Loaddiv('div_norek','script_jadwal_kirim_antrian.php','flagtrans=<?=$flagtrans?>&act=norek&blth='+blth+'&file='+nama_file);
			}else{
				$("#norek").val('');
				$("#norek").attr('disabled','disabled');
				clear_sample();
			}
		}
		
		function goto(pr){
			window.location.href = "index_jadwal_kirim_antrian.php?pr="+pr;
		}
		
		function cek_attach(){
			if($("#check_attach").attr('checked')){
				attach_file();
			}else{
				$("#attach_file1").val('');
				$("#attach_file2").val('');
				$("#attach_file1").hide(500);
				$("#attach_file2").hide(500);
				$("#div_attach").hide(500);
			}
		}
		
		function attach_file(){
			var blth = $("#blth").val();
			
			Loaddiv('div_attach','script_jadwal_kirim_antrian.php','flagtrans=<?=$flagtrans?>&act=attach_file&blth='+blth);
			$("#div_attach").show(500);
		}
		
		function attach_add(){
			if($("#attach_file1").val()!=''){
				$('#attach_file1 option:selected').remove().appendTo('#attach_file2');
			}
		}
		
		function email(){
			var norek = $("#norek").val();
			var blth = $("#blth").val();
			var nama_file = $("#nama_file").val();
			
			if(norek=='' || $("#checkbox_norek").attr('checked')==false){
				clear_sample();
			}else{
				$("#email_name").show(500);
				$("#check_sample").show(500);
				$("#email_address").show(500);
				Loaddiv('email_address','script_jadwal_kirim_antrian.php','flagtrans=<?=$flagtrans?>&act=email&blth='+blth+'&nama_file='+nama_file+'&norek='+norek);
			}
			show_sample();
		}
		
		function show_sample(){
			var norek = $("#norek").val();
			var blth = $("#blth").val();
			var nama_file = $("#nama_file").val();
			
			if((norek=='' || $("#checkbox_norek").attr('checked')==false)){
				clear_sample();
			}else{
				$("#email_name").show(500);
				$("#check_sample").show(500);
				$("#email_address").show(500);
				$("#email_sample").show(500);
			}
		}
		
		function func_sample(){
			if($("#check_sample").attr('checked')==false){
				$("#text_sample_email").val('');
				$("#email_address").show(500);
			}else{
				$("#email_address").hide(500);
				Loaddiv('email_sample','script_jadwal_kirim_antrian.php','act=sample');
			}
		}
		
		function clear_sample(){
			$("#text_sample_email").val('');
			$("#email_name").hide(500);
			$("#check_sample").removeAttr('checked');
			$("#check_sample").hide(500);
			$("#email_address").hide(500);
			$("#email_sample").hide(500);
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
                                <form id="form_antrian" name="form_antrian">
                                    <table width="100%" border="0" cellpadding="2" cellspacing="2" align="center">
                                    	<tr>
                                        	<td width="55%" height="150" valign="top">
                                            	<table width="100%" border="0" cellpadding="2" cellspacing="2" align="center">
                                                    <input type="hidden" id="pr" name="pr" value="<?=$pr?>" />
                                                    <tr>
                                                        <td width="30%"  height="30" colspan="2">Jadwal</td>
                                                        <td width="35%" colspan="2">
                                                        	<div id="div_jadwal">
                                                                <select disabled="disabled" id="jadwal_id" name="jadwal_id" class="combobox"><option></option></select>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td width="30%"  height="30" colspan="2">Periode (BLTH)</td>
                                                        <td width="35%"><div id="div_blth"></div></td>
                                                        <td>
                                                            <span id="blth_record">
                                                                <input type="text" name="jml_record" id="jml_record" class="textbox_1" disabled="disabled" value="0" />
                                                            </span> record
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td height="30">File Loading</td>
                                                        <td width="3%">
                                                            <input type="checkbox" id="checkbox_nama_file" name="checkbox_nama_file" disabled="disabled" onclick="oncheck_file()" />
                                                        </td>
                                                        <td>
                                                            <div id="div_file_loading">
                                                                <select disabled="disabled" id="nama_file" name="nama_file" class="combobox"><option></option></select>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span id="nama_file_record">
                                                                <input type="text" name="namafile_record" id="namafile_record" class="textbox_1" disabled="disabled" value="0" />
                                                            </span> record
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td height="30">Nomor Rekening</td>
                                                        <td width="3%">
                                                            <input type="checkbox" id="checkbox_norek" name="checkbox_norek" disabled="disabled" onclick="oncheck_norek()" />
                                                        </td>
                                                        <td colspan="2">
                                                            <div id="div_norek">
                                                                <select disabled="disabled" id="norek" name="norek" class="combobox"><option></option></select>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                    	<td>
                                                        	<div id="email_name" style="display:none">
                                                            	Alamat Email<br /><br />Sample Email
                                                            </div>
                                                        </td>
                                                        <td><input type="checkbox" name="check_sample" id="check_sample" style="display:none" onclick="func_sample()" /></td>
                                                        <td>
                                                        	<div id="email_address"></div>
                                                            <div id="email_sample" style="display:none">
                                                                <input type="text" name="text_sample_email" id="text_sample_email" class="textbox_3" value="" />
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td width="5%">
                                            </td>
                                            <td valign="top">
                                                <table>
                                                	<tr>
                                                    	<td>Template Email</td>
                                                        <td>&nbsp;</td>
                                                        <td><div id="div_template"></div></td>
                                                    </tr>
                                                	<tr>
			                                        	<td width="30%" height="30">Status</td>
                                                        <td width="10%">&nbsp;</td>
                                                        <td>
                                                        	<select id="status" name="status" class="combobox">
                                                            	<option value="0">Belum dikirim</option>
                                                                <option value="1">Sudah dikirim - Sukses</option>
                                                                <option value="2">Sudah dikirim - Gagal</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                    	<td height="50" valign="top">Attachment Files</td>
                                                        <td valign="top">
                                                        	<input type="checkbox" id="check_attach" name="check_attach" disabled="disabled" onclick="cek_attach()" />
                                                        </td>
                                                    	<td><div id="div_attach" style="display:none"></div></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <table width="90%" border="0" cellpadding="2" cellspacing="2" align="center">
                                        <tr>
                                            <td height="20">&nbsp;</td>
                                        </tr>
                                        <tr>
                                        	<td align="center" height="30">
                                            	<div id="div_respon"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                        	<td align="center" height="30">
                                            	<div id="div_exists"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center">
                                            	<div id="div_button">
                                                	<input type="button" id="button_kirim" name="button_kirim" class="button" value="KIRIM KE ANTRIAN" style="display:none" onclick="kirim();" />
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </form>
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
	Loaddiv('div_jadwal','script_jadwal_kirim_antrian.php','act=jadwal&flagtrans=<?=$flagtrans?>');
	Loaddiv('div_blth','script_jadwal_kirim_antrian.php','act=blth&flagtrans=<?=$flagtrans?>');
	Loaddiv('div_template','script_jadwal_kirim_antrian.php','act=template_email&flagtrans=<?=$flagtrans?>');
</script>
<?
	pg_close($con);
?>