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
            if($("#blth").val()!='' && $("#combo_template").val()!='' && $("#jeda").val()>0 && $("#rec_split").val()>0){
				$("#button_kirim").show(500);
			}else{
				$("#button_kirim").hide(500);
			}
        }
		
		function changeBlth(blth){
			Loaddiv('blth_record','script_kirim_antrian_sk.php','flagtrans=<?=$flagtrans?>'+'&act=blth_record&blth='+blth);
			$('#checkbox_nama_file').removeAttr('disabled');
			$('#check_attach').removeAttr('disabled');
			cekFile();
			cekBlth();
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
			$("#check_attach").removeAttr('checked');
			cek_attach();
		}
		
		function cekForm(){
			var blth = $("#blth").val();
			var nama_file = $("#nama_file").val();
			var combo_template_name = $("#combo_template_name").val();
			var norek = $("#norek").val();
			var tgl_jadwal = $("#tgl_jadwal").val();
			var hour_jadwal = $("#hour_jadwal").val();
			var min_jadwal = $("#min_jadwal").val();
			var jeda = $("#jeda").val();
			var rec_split = $("#rec_split").val();
			
			/*hitung selisih waktu*/
			var time_server = new Date(<?php echo date('Y')?>, <?php echo date('m')?>, <?php echo date('d')?>, <?php echo date('H')?>, <?php echo date('i')?>, 0, 0);
			var time_server_ms = time_server.getTime(time_server);
			
			var arr_tgl_jadwal = tgl_jadwal.split("-");
			var year_jadwal = arr_tgl_jadwal[0];
			var mon_jadwal = arr_tgl_jadwal[1];
			var day_jadwal = arr_tgl_jadwal[2];
			var time_client = new Date(year_jadwal, mon_jadwal, day_jadwal, hour_jadwal, min_jadwal, 0, 0);
			var time_client_ms = time_client.getTime(time_client);
			
			var selisih = time_client_ms - time_server_ms;
			var menit = selisih / 60 / 1000;
			
			//alert("client: "+time_client_ms);
			//alert("server: "+time_server_ms);
			//alert(menit);
			/*end of hitung selisih waktu*/
			
			/*konfirmasi sebelum melakukan pengiriman ke antrian*/
			if($("#check_status").is(':checked')){
				var status = $("#status").val();
				if(status == "1") status = "Belum pernah dikirim";
				if(status == "2") status = "Sudah pernah kirim dengan status sukses";
				if(status == "3") status = "Sudah pernah kirim dengan status gagal";
			}else{
				var status = "";
			}
			if($("#check_attach").is(':checked')){
				var attach_tambahan = "Ya";
			}else{
				var attach_tambahan = "Tidak";
			}
			if($("#checkbox_norek").is(':checked')){
				var norek = "nomor rekening: " + $("#norek").val() + "\n";
				if($("#norek").val() != ""){
					if($("#check_sample").is(':checked')){
						var alamat_sample = "sample: Ya\nalamat sample: " + $("#text_sample_email").val() + "\n";
					}else{
						var alamat_sample = "";
					}
				}else{
					var alamat_sample = "";
				}
			}else{
				var norek = "";
				var alamat_sample = "";
			}
			
			var msg = "";
			msg = msg + "periode: " + blth + "\n";
			msg = msg + "file loading: " + nama_file + "\n";
			msg = msg + "template email: " + combo_template_name + "\n";
			msg = msg + "status pengiriman: " + status + "\n";
			msg = msg + "attachment tambahan: " + attach_tambahan + "\n";
			msg = msg + norek;
			msg = msg + alamat_sample;
			msg = msg + "jadwal awal: " + tgl_jadwal + " " + hour_jadwal + ":" + min_jadwal + "\n";
			msg = msg + "pengiriman dilakukan sebanyak " + rec_split + " email setiap " + jeda + " menit" + "\n";
			/*end of konfirmasi sebelum melakukan pengiriman ke antrian*/
			
			var pr = $("#pr").val();
			var arr_pr = pr.split('|');
			var product = arr_pr[0];
			
			if(menit > 3){
				if(confirm(msg)){
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
					return false;
				}
			}else{
				alert("Jadwal pengiriman awal harus lebih dari 30 menit daripada waktu sekarang. Set lagi !");
				return false;
			}
		}
		
		function kirim(){
			var cek = cekForm();
			if(cek==true){
				$("#div_button").html('<img src="../images/ajax.gif" align="center"><img src="../images/ajax.gif" align="center"><img src="../images/ajax.gif" align="center"><img src="../images/ajax.gif" align="center"><img src="../images/ajax.gif" align="center">');
				$.post('script_split_jadwal_kirim_antrian_sk.php?act=kirim',$("#form_antrian").serialize(),function(respon){
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
				Loaddiv('div_file_loading','script_split_jadwal_kirim_antrian_sk.php','act=nama_file&blth='+blth+'&flagtrans=<?=$flagtrans?>');
				Loaddiv('div_file_customer','script_split_jadwal_kirim_antrian_sk.php','act=file_customer&blth='+blth+'&flagtrans=<?=$flagtrans?>');
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
		
		function ganti_order1(){
			var urut = $("#rad_ord").val();
			$('#checkbox_norek').removeAttr('disabled');
			$('#checkbox_norek').removeAttr('checked');
			$('#norek').attr('disabled','disabled');
			$('#norek').val('');
			document.form_antrian.pilihan_order.value=urut;
		}
		
		function oncheck_norek(){
			if($("#checkbox_norek").attr('checked')==true){
				var blth = $("#blth").val();
				var nama_file = $("#nama_file").val();
				var pr = $("#pr").val();
				var pilihan_order = $("#pilihan_order").val();
				var arr_pr = pr.split('|');
				$("#text_sample_email").val('');
				Loaddiv('div_norek','script_split_jadwal_kirim_antrian_sk.php','flagtrans=<?=$flagtrans?>&act=norek&ord='+pilihan_order+'&blth='+blth+'&file='+nama_file);
			}else{
				$("#norek").val('');
				$("#norek").attr('disabled','disabled');
				clear_sample();
			}
		}
		
		function goto(pr){
			window.location.href = "index_split_jadwal_kirim_antrian_sk.php?pr="+pr;
		}
		
		function cek_attach(){
			if($("#check_attach").attr('checked')==true){
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
			if($("#checkbox_nama_file").attr('checked')==true){
				var nama_file = $("#nama_file").val();
			}else{
				var nama_file = '';
			}
			var blth = $("#blth").val();
			
			Loaddiv('div_attach','script_split_jadwal_kirim_antrian_sk.php','flagtrans=<?=$flagtrans?>&act=attach_file&blth='+blth+'&nama_file='+nama_file);
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
				Loaddiv('email_address','script_split_jadwal_kirim_antrian_sk.php','flagtrans=<?=$flagtrans?>&act=email&blth='+blth+'&nama_file='+nama_file+'&norek='+norek);
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
				Loaddiv('email_sample','script_split_jadwal_kirim_antrian_sk.php','act=sample');
			}
		}
		
		function template_name(){
			var template_id = $("#combo_template").val();
			Loaddiv('div_template_name','script_split_jadwal_kirim_antrian_sk.php','act=template_name&template_id='+template_id);
		}
		function clear_sample(){
			$("#text_sample_email").val('');
			$("#email_name").hide(500);
			$("#check_sample").removeAttr('checked');
			$("#check_sample").hide(500);
			$("#email_address").hide(500);
			$("#email_sample").hide(500);
		}
		
		function CheckKeyDecimal(obj, e, reg) {
			var key = window.event ? e.keyCode : e.charCode;
			var keychar = String.fromCharCode(key);
			var keytest = reg.test(keychar);
		
			var dectest = true;
			var mintest = true;
			if (keychar == "." && obj.value.indexOf(".") > -1) {
				dectest = false;
			}
			if (keychar == "-" && obj.value.indexOf("-") > -1) {
				mintest = false;
			}
		
			var alltest = keytest && dectest && mintest;
		
			return alltest;
		}
		
		function cek_status(){
			if($("#check_status").attr('checked')){
				$("#status").attr('disabled',false);
			}else{
				$("#status").attr('disabled',true);
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
                                <form id="form_antrian" name="form_antrian">
                                    <table width="100%" border="0" cellpadding="2" cellspacing="2" align="center">
                                    	<tr>
                                        	<td width="55%" height="150" valign="top">
                                            	<table width="100%" border="0" cellpadding="2" cellspacing="2" align="center" style="border-collapse:collapse;">
                                                    <input type="hidden" id="pr" name="pr" value="<?=$pr?>" />
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
                                                        <td height="30" colspan="2">File Customer</td>
                                                        <td>
                                                            <div id="div_file_customer">
                                                                <select disabled="disabled" id="file_cust" name="file_cust" class="combobox"><option></option></select>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td height="30">File Loading</td>
                                                        <td width="3%">
                                                            <input type="checkbox" id="checkbox_nama_file" name="checkbox_nama_file" disabled="disabled" onClick="oncheck_file()" />
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
                                                        <td height="10" colspan="2">Sorting <font size='2pt;'></font></td>
                                                        <td height="10" colspan="2">
														<select name="rad_ord" id="rad_ord" onChange="ganti_order1()" class="combobox" >
															<option value="1">No Rekening</option>
															<option value="2">Nama (ASC)</option>
															<option value="3">Nama (DESC)</option>
															<option value="4">Halaman (ASC)</option>
															<option value="5" SELECTED>Halaman (DESC)</option>
														</select><input type="hidden" size="3" value="5" name="pilihan_order" id="pilihan_order"/>
														</td>
													</tr>
													<tr>
                                                        <td height="30">Nomor Rekening</td>
                                                        <td width="3%">
                                                            <input type="checkbox" id="checkbox_norek" name="checkbox_norek" disabled="disabled" onClick="oncheck_norek()" />
                                                            <input type="hidden" id="flagtrans" name="flagtrans" value="<?php echo $flagtrans;?>"/>
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
                                                        <td><input type="checkbox" name="check_sample" id="check_sample" style="display:none" onClick="func_sample()" /></td>
                                                        <td>
                                                        	<div id="email_address"></div>
                                                            <div id="email_sample" style="display:none">
                                                                <input type="text" name="text_sample_email" id="text_sample_email" class="textbox_3" value="" />
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td width="30%"  height="30" colspan="2">Pengiriman Awal</td>
                                                        <td width="35%" colspan="2">
                                                            <input type="text" id="tgl_jadwal" name="tgl_jadwal" value="<?php echo date('Y-m-d') ?>" class="textbox_2" readonly>&nbsp;
                                                            <select id="hour_jadwal" name="hour_jadwal" class="combobox">
                                                                <?php
                                                                for ($i = 0; $i <= 23; $i++){
                                                                    if(strlen($i)==1) $i='0'.$i;
                                                                ?>
                                                                <option value="<?= $i ?>"<? if ($i == date("H")) { echo(" selected=\"selected\""); } ?>><?= $i ?></option>
                                                                <? } ?>
                                                            </select>&nbsp;:&nbsp;
                                                            <select id="min_jadwal" name="min_jadwal" class="combobox">
                                                                <? for ($i = 0; $i <= 59; $i++) { 
                                                                    if(strlen($i)==1) $i='0'.$i;
                                                                ?>
                                                                <option value="<?= $i ?>"<? if ($i == date("i")) { echo(" selected=\"selected\""); } ?>><?= $i ?></option>
                                                                <? } ?>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td width="30%"  height="30" colspan="2">Jeda antarjadwal</td>
                                                        <td width="35%" colspan="2">
                                                            <input type="text" id="jeda" name="jeda" class="textbox_1" onkeypress="return CheckKeyDecimal(this, event, /[0-9\r\0]/);" onchange="cekFile()" value="1" /> menit
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td width="30%"  height="30" colspan="2">Record per Jadwal</td>
                                                        <td width="35%" colspan="2">
                                                            <input type="text" id="rec_split" name="rec_split" class="textbox_1" onkeypress="return CheckKeyDecimal(this, event, /[0-9\r\0]/);" onchange="cekFile()" value="100" /> record
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
														<td>
															<div id="div_template">
                                                            	<select id="combo_template" name="combo_template" class="combobox" onChange="template_name()"><option value=""></option></select>
                                                        	</div>
                                                        	<div id="div_template_name">
                                                            	<input type="hidden" id="combo_template_name" name="combo_template_name" value="" />
                                                        	</div>
															</td>
                                                    </tr>
                                                	<tr>
			                                        	<td width="30%" height="30">Status</td>
                                                        <td width="10%"><input type="checkbox" id="check_status" name="check_status" checked="checked" onClick="cek_status()" /></td>
                                                        <td>
                                                        	<select id="status" name="status" class="combobox" disabled="disabled">
                                                            	<option value="1">Belum dikirim</option>
                                                                <option value="2">Sudah dikirim - Sukses</option>
                                                                <option value="3">Sudah dikirim - Gagal</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                    	<td height="50" valign="top">Attachment Files</td>
                                                        <td valign="top">
                                                        	<input type="hidden" id="tipe_attach" name="tipe_attach" value="1" />
                                                        	<input type="checkbox" id="check_attach" name="check_attach" disabled="disabled" onClick="cek_attach()" />
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
                                                	<input type="button" id="button_kirim" name="button_kirim" class="button" value="KIRIM KE ANTRIAN" style="display:none" onClick="kirim();" />
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
	Loaddiv('div_blth','script_split_jadwal_kirim_antrian_sk.php','act=blth&flagtrans=<?=$flagtrans?>');
	Loaddiv('div_template','script_split_jadwal_kirim_antrian_sk.php','act=template_email&flagtrans=<?=$flagtrans?>');
	cek_status();
	$("#tgl_jadwal").datepicker({dateFormat: 'yy-mm-dd'});
</script>
<?
	@pg_close($con);
?>