<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/function.php"); ?>
<? require_once("../include/message.php"); ?>
<?
	$pr = trim($_GET["pr"]);
	$arr_pr = explode("|", $pr);
	$hit_arr_pr = substr_count($pr,"|");
	
	$flagtrans = trim($arr_pr[0]);
	$menu_id = (int)trim($arr_pr[1]);
	$con = pg_connect($connection) or die("Could not connect to database!");
	
	if($hit_arr_pr>1){
	$blth_home = trim($arr_pr[2]);
	$mloading_home = (int)trim($arr_pr[3]);
	$sql_ml = "SELECT loading_file FROM m_loading WHERE m_loading_id='$mloading_home'";
	$exe_ml = pg_query($sql_ml);
	$row_ml = pg_fetch_array($exe_ml);
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?= $title ?></title>
    </head>
    <script>
		function show_blth_rec(){
			var blth = $("#blth").val();
			var flagtrans = $("#flagtrans").val();
			if(blth!=''){
				$("#checkbox_nama_file").removeAttr('disabled');
				$("#button_lihat").show(500);
			}else{
				$("#checkbox_nama_file").removeAttr('checked');
				$("#checkbox_nama_file").attr('disabled','disabled');
				check_nama_file();
				$("#button_lihat").hide(500);
			}
			Loaddiv('span_blth_record','script_lihat_antrian_sk.php','act=show_blth_rec&flagtrans='+flagtrans+'&blth='+blth);
		}
		
		function show_nama_file_rec(){
			var blth = $("#blth").val();
			var flagtrans = $("#flagtrans").val();
			var nama_file = $("#nama_file").val();
			Loaddiv('span_nama_file_rec','script_lihat_antrian_sk.php','act=show_nama_file_rec&flagtrans='+flagtrans+'&blth='+blth+'&nama_file='+nama_file);
		}
		
		function show_nama_file(){
			var blth = $("#blth").val();
			var flagtrans = $("#flagtrans").val();
			Loaddiv('div_nama_file','script_lihat_antrian_sk.php','act=show_nama_file&flagtrans='+flagtrans+'&blth='+blth);
		}
		
		function check_nama_file(){
			if($("#checkbox_nama_file").attr('checked')==true){
				$("#nama_file").removeAttr('disabled');
				show_nama_file();
			}else{
				$("#nama_file").attr('disabled','disabled');
				$("#nama_file").val('');
				show_nama_file_rec();
			}
		}
		
		function show_antrian(){
			var menu_id = $("#menu_id").val();
			var blth = $("#blth").val();
			var flagtrans = $("#flagtrans").val();
			var nama_file = $("#nama_file").val();
			var jml_record = $("#jml_record").val();
			var tipe_jadwal = $("#tipe_jadwal").val();
			if(jml_record == ""){
				alert("Masukkan Jumlah Record !");
			}else{
				$("#button_lihat").hide(500);
				$("#div_filter").hide(500);
				$("#div_show").show(500);
				Loaddiv('div_show','script_lihat_antrian_sk.php','act=show_antrian&menu_id='+menu_id+'&blth='+blth+'&flagtrans='+flagtrans+'&nama_file='+nama_file+'&tipe_jadwal='+tipe_jadwal+'&jml_record='+jml_record+'&offset=0');
			}
		}
		
		function show_filter(){
			$("#button_lihat").show(500);
			$("#div_filter").show(500);
			$("#div_show").hide(500);
		}
		
		function choose_me(row,val){
			if($('#cek_'+row).attr('checked')==false){
				$('#tr_'+row).removeClass('selected');
				$('#cek_'+row).attr('checked',false);
				
				var list_val = $("#list_antrian").val();
				list_val = $("#list_antrian").val().replace(val+',','');
				$("#list_antrian").val(list_val);
			}else{
				$('#tr_'+row).addClass('selected');
				$('#cek_'+row).attr('checked',true);
				
				var list_val = $("#list_antrian").val();
				if(list_val.search(val)==-1){
					list_val = list_val+val+',';
					$("#list_antrian").val(list_val);
				}
			}
		}
		
		function kirim_email(){
			var menu_id = $("#menu_id").val();
			var list_antrian = $("#list_antrian").val();
			var blth = $("#blth").val();
			var flagtrans = $("#flagtrans").val();
			var nama_file = $("#nama_file").val();
			var tipe_jadwal = $("#tipe_jadwal").val();
			var jml_record = $("#jml_record").val();
			
			if(list_antrian!=''){
				var cek = confirm("Apakah Anda yakin akan mengirim semua antrian ?");
				if(cek){
					$("#div_show").html('<img src=../images/ajaxbig.gif>');
					$.post('script_kirim_email_sk.php?act=kirim_email&list='+list_antrian+'&blth='+blth+'&flagtrans=' +flagtrans,'',function(respon_kirim){
						Loaddiv('div_show','script_lihat_antrian_sk.php','act=show_antrian&menu_id='+menu_id+'&blth='+blth+'&flagtrans='+flagtrans+'&nama_file='+nama_file+'&tipe_jadwal='+tipe_jadwal+'&jml_record='+jml_record+'&offset=0');
					});
				}
			}else{
				alert('Antrian Belum Dipilih');
			}
		}
		
		function delete_all_antrian(){
			var menu_id = $("#menu_id").val();
			var list_antrian = $("#list_antrian").val();
			var blth = $("#blth").val();
			var flagtrans = $("#flagtrans").val();
			var nama_file = $("#nama_file").val();
			var tipe_jadwal = $("#tipe_jadwal").val();
			var jml_record = $("#jml_record").val();
			
			if(list_antrian!=''){
				var cek = confirm("Apakah Anda yakin akan menghapus semua antrian ?");
				if(cek){
					$("#div_show").html('<img src=../images/ajaxbig.gif>');
					$.post('script_lihat_antrian_sk.php?act=delete_all_antrian&list='+list_antrian,'',function(respon_kirim){
						Loaddiv('div_show','script_lihat_antrian_sk.php','act=show_antrian&menu_id='+menu_id+'&blth='+blth+'&flagtrans='+flagtrans+'&nama_file='+nama_file+'&tipe_jadwal='+tipe_jadwal+'&jml_record='+jml_record+'&offset=0');
					});
				}
			}else{
				alert('Antrian Belum Dipilih');
			}
		}
		
		function checklist_all(val) {
			var checkbox = document.form_antrian.elements['cek_[]'];
			if ( checkbox.length > 0 ) {
				for (i = 0; i < checkbox.length; i++) {
					if ( val.checked ) {
						checkbox[i].checked = true;
						choose_me((i+1),$('#cek_'+(i+1)).val());
					}
					else {
						checkbox[i].checked = false;
						choose_me((i+1),$('#cek_'+(i+1)).val());
					}
				}
			}
			else {
				if ( val.checked ) {
					checkbox.checked = true;
					choose_me(1,$('#cek_'+1).val());
				}
				else {
					checkbox.checked = false;
					choose_me(1,$('#cek_'+1).val());
				}
			}
		}
		
		function delete_antrian(antrian_id){
			var cek = confirm('Yakin Hapus ?');
			if(cek){
				Loaddiv('div_show','script_lihat_antrian_sk.php','act=delete_antrian&antrian_id='+antrian_id);
				show_antrian();
			}
		}
		
		function view_template(val,vul,vel){
			var antrian_id = val;
			var flagtrans = vul;
			var blth = vel;
			$.post('script_lihat_antrian_sk.php?act=view_template&antrian_id='+antrian_id+'&flagtrans='+flagtrans+'&blth='+blth,$("#form_template").serialize(),function(respon){
				$.fancybox(respon);
			});
		}
		
		function check_qua(log_approval_id){
			var log_approval_id = log_approval_id;
			$.post('script_lihat_antrian_sk.php?act=open_form_check&log_approval_id='+log_approval_id,$("#form_template").serialize(),function(respon){
				$.fancybox(respon);
			});
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
		
		function approve(log_approval_id){
			var cek = confirm('Yakin Approve ?');
			if(cek){
				Loaddiv('div_show','script_lihat_antrian_sk.php','act=approve_antrian&log_approval_id='+log_approval_id);
				show_antrian();
			}
		}
		
		function unapprove(log_approval_id){
			var cek = confirm('Yakin Unapprove ?');
			if(cek){
				Loaddiv('div_show','script_lihat_antrian_sk.php','act=unapprove_antrian&log_approval_id='+log_approval_id);
				show_antrian();
			}
		}
		
		function reschedule(log_approval_id){
			var log_approval_id = log_approval_id;
			var flagtrans = $("#flagtrans").val();
			$.post('script_lihat_antrian_sk.php?act=open_form_reschedule&flagtrans='+flagtrans+'&log_approval_id='+log_approval_id,$("#form_template").serialize(),function(respon){
				$.fancybox(respon);
			});
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
                                <table width="50%" border="0" cellpadding="0" cellspacing="0">
                                	<input type="hidden" id="flagtrans" name="flagtrans" value="<?=$flagtrans?>" />
                                	<input type="hidden" id="menu_id" name="menu_id" value="<?=$menu_id?>" />
                                    <tr>
                                        <td height="30">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td height="30" colspan="2">Periode</td>
                                        <td width="40%">
                                            <div id="div_blth"></div>
                                        </td>
                                        <td>
                                            <span id="span_blth_record">
                                                <input type="text" name="blth_record" id="blth_record" class="textbox_1" disabled="disabled" value="0" />
                                            </span> record
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="30%" height="30">Nama File</td>
                                        <td width="5%">
                                        	<input type="checkbox" id="checkbox_nama_file" name="checkbox_nama_file" disabled="disabled" onclick="check_nama_file()" />
                                        </td>
                                        <td>
                                        	<div id="div_nama_file">
                                            	<select id="nama_file" name="nama_file" class="combobox" disabled="disabled">
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <span id="span_nama_file_rec">
                                                <input type="text" name="nama_file_rec" id="nama_file_rec" class="textbox_1" disabled="disabled" value="0" />
                                            </span> record
                                        </td>
                                    </tr>
                                    <tr>
                                    	<td height="30" colspan="2">Jumlah Record</td>
                                        <td>
                                        	<input type="text" id="jml_record" name="jml_record" class="textbox_1" value="50" onkeypress="return CheckKeyDecimal(this, event, /[0-9\r\0]/);" />
                                        </td>
                                    </tr>
                                    <tr>
                                    	<td height="30" colspan="2">Jadwal</td>
                                        <td>
                                        	<select id="tipe_jadwal" name="tipe_jadwal" class="combobox">
                                            	<option value="1">ALL</option>
                                                <option value="2">YA</option>
                                                <option value="3">TIDAK</option>
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
                        	<td height="30">&nbsp;</td>
                        </tr>
                        <tr>
                        	<td align="center">
                            	<input type="button" class="button" id="button_lihat" name="button_lihat" value="LIHAT ANTRIAN" style="display:none" onclick="show_antrian();" />
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
	var flagtrans = $("#flagtrans").val();
	<?php if($hit_arr_pr>1){ 
	?>
	Loaddiv('div_blth','script_lihat_antrian_sk.php','act=blth&flagtrans='+flagtrans+'&blth_home='+<?php echo $blth_home;?>);
	$("#checkbox_nama_file").removeAttr('disabled');
	$("#checkbox_nama_file").attr("checked","checked");
	Loaddiv('span_nama_file_rec','script_lihat_antrian_sk.php','act=show_nama_file_rec&flagtrans='+flagtrans+'&blth='+<?php echo $blth_home;?>+'&nama_file=<?php echo $row_ml['loading_file'];?>');
	Loaddiv('span_blth_record','script_lihat_antrian_sk.php','act=show_blth_rec&flagtrans='+flagtrans+'&blth='+<?php echo $blth_home;?>);
	Loaddiv('div_nama_file','script_lihat_antrian_sk.php','act=show_nama_file&flagtrans='+flagtrans+'&blth='+<?php echo $blth_home;?>+'&mloading_home='+<?php echo $mloading_home;?>);
	Loaddiv('div_show','script_lihat_antrian_sk.php','act=show_antrian&menu_id='+<?php echo $menu_id;?>+'&blth='+<?php echo $blth_home;?>+'&flagtrans='+flagtrans+'&nama_file=<?php echo $row_ml['loading_file'];?>&tipe_jadwal=1&jml_record=50&offset=0');
	
	<?php } else { ?>
	Loaddiv('div_blth','script_lihat_antrian_sk.php','act=blth&flagtrans='+flagtrans);
	<?php } ?>
</script>
<?
	pg_close($con);
?>