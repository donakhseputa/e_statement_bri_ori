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
		 $('#check_kinerja').removeAttr('disabled');
            if($("#blth").val()!='' && $("#combo_template").val()!='' && $("#jeda").val()>0 && $("#rec_split").val()>0){
				if($("#checkbox_norek").attr('checked')==true && $("#checkbox_inputnorek").attr('checked')==true){
					$("#button_kirim").hide(500);
					$("#button_kirim2").show(500);
				}else{
					$("#button_kirim").show(500);
					$("#button_kirim2").hide(500);
				}
			}else{
				$("#button_kirim").hide(500);
				$("#button_kirim2").hide(500);
			}
        }
		
		function pro_loading(divId){
			//$("#div_button").html('<img src="../images/ajax.gif" align="center"><img src="../images/ajax.gif" align="center"><img src="../images/ajax.gif" align="center"><img src="../images/ajax.gif" align="center"><img src="../images/ajax.gif" align="center">');
			//$("#list_dataasli").innerHTML="<div align='center'><img src='../images/ajax.gif' /> Please Wait...</div>";
			//alert(divId);
			
			/*$('#'+divId).submit(function(evt) {
                evt.preventDefault();

                var formData = new FormData(this);

                $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data:formData,
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {
                    $('#imagedisplay').html("<img src=" + data.url + "" + data.name + ">");
                },
                error: function(data) {
                    $('#imagedisplay').html("<h2>this file type is not supported</h2>");
                }
                });
            });*/

			/*$("#"+divId).submit(function(e) {
			var url = 'script_pilihan_kirim_antrian_sk.php?act=kirim';

			$.ajax({
			   type: "POST",
			   url: url,
			   data: $("#"+divId).serialize(), // serializes the form's elements.
			   success: function(respon)
			   {
				   alert(respon); // show response from the php script.
					var arr_respon = respon.split('|');
					var flagtrans = arr_respon[0];
					var menuid = arr_respon[1];
					var jumlah = arr_respon[2];
					var exists = arr_respon[3];
					$("#div_respon").html(jumlah+' record telah dikirim ke antrian');
					$("#div_exists").html('Daftar nomor rekening yang sudah ada di antrian:<br>'+exists);
					$("#div_button").html('<input type="button" id="button_selesai" name="button_selesai" class="button" value="SELESAI" onclick="goto(\''+flagtrans+'|'+menuid+'\')" />');
			   },
			   error: function (respon){	alert('Isi form ini dengan benar' + respon);	}
			 });

				e.preventDefault(); // avoid to execute the actual submit of the form.
			});*/
			
			/*$('#'+divId).ajaxForm({
				dataType:  'json',
				success:   function(data) {
					if(typeof(data.error) != 'undefined'){
						if(data.error != ''){
							paging_list(1);
							alert(data.error);
						}else{
							//$e("list_dataasli").innerHTML=data.htmlresult;
							//$e("list_dataasli_paging").innerHTML=data.paginglist;
							var arr_respon = data.respon.split('|');
							var flagtrans = arr_respon[0];
							var menuid = arr_respon[1];
							var jumlah = arr_respon[2];
							var exists = arr_respon[3];
							$("#div_respon").html(jumlah+' record telah dikirim ke antrian');
							$("#div_exists").html('Daftar nomor rekening yang sudah ada di antrian:<br>'+exists);
							$("#div_button").html('<input type="button" id="button_selesai" name="button_selesai" class="button" value="SELESAI" onclick="goto(\''+flagtrans+'|'+menuid+'\')" />');
						}
					}else{ alert('Sorry, Server is Maintenance'); }
				},
				error: function (data, status){	alert('Isi form ini dengan benar');	}
			});*/

		}
		
		function cekFileFormat(){
        	var a= $("#loading_cardno").val().substr(-3).toLowerCase();
        	//alert ( a.substr(0,8).toLowerCase() );
            if( $("#loading_cardno").val() !='' ){
				if(a!='txt'){
					alert("FILE BILLING HARUS BERTIPE *.TXT!");
					document.form_antrian.loading_cardno.value='';
				}
            }
		}
		
		function changeBlth(blth){
			Loaddiv('blth_record','script_pilihan_kirim_antrian_sk.php','flagtrans=<?=$flagtrans?>'+'&act=blth_record&blth='+blth);
			$('#checkbox_nama_file').removeAttr('disabled');
			$('#check_attach').removeAttr('disabled');
			$('#check_kinerja').removeAttr('disabled');
			cekFile();
			cekBlth();
		}
		
		function cek_kinerja(){
			if($("#check_kinerja").attr('checked')==true){
				kinerja_file();
			}else{
				$("#kinerja_file1").val('');
				$("#kinerja_file1").hide(500);
				$("#div_kinerja").hide(500);
			}
		}
		
		function kinerja_file(){
		//alert("YOI");
			if($("#checkbox_nama_file").attr('checked')==true){
				var nama_file = $("#nama_file").val();
			}else{
				var nama_file = '';
			}
			var blth = $("#blth").val();
			//alert(nama_file);
			
			Loaddiv('div_kinerja','script_pilihan_kirim_antrian_sk.php','flagtrans=<?=$flagtrans?>&act=kinerja_file&blth='+blth+'&nama_file='+nama_file);
			$("#div_kinerja").show(500);
		}
		
		function cekBlth(){
			 if($("#blth").val()==''){
				 $("#checkbox_nama_file").removeAttr('checked');
				 $("#checkbox_nama_file").attr('disabled','disabled');
				 $("#check_attach").removeAttr('checked');
				 $("#check_attach").attr('disabled','disabled'); 
				 $("#checkbox_inputnorek").removeAttr('checked');
				 $("#checkbox_inputnorek").attr('disabled','disabled');
				 $("#rek").hide(300);
				 $("#checkbox_jadwal").removeAttr('checked');
				 $("#checkbox_jadwal").attr('disabled','disabled');
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
			var flag_waktu = '';
			var msg_waktu = '';
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
			/*if($("#checkbox_norek").is(':checked')){
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
			}*/
			
			if($("#checkbox_jadwal").attr('checked')==true){
				if(menit > 3) flag_waktu = 'ok';
				msg_waktu = tgl_jadwal + " " + hour_jadwal + ":" + min_jadwal;
			}else{
				//bypass no timer
				flag_waktu = 'ok';
				msg_waktu = "N/A";
			}
			var tanda = '';
			var alamat_sample = "";
			var norek = '';
			var cardno_manual = $("#cardno_manual").val();
			if(cardno_manual=='Gunakan , atau ; sebagai pemisah') cardno_manual='';
			if($("#checkbox_norek").attr('checked')==true && $("#checkbox_inputnorek").attr('checked')==true){
				tanda = 'file';
				norek = "file rekening: " + $("#loading_file").val() + "\n";
			}else if($("#checkbox_inputnorek").attr('checked')==true){
				tanda = 'text';
				norek = "nomor rekening: " + cardno_manual + "\n";
			}
			if($("#check_sample").is(':checked')){
				alamat_sample = "sample: Ya\nalamat sample: " + $("#text_sample_email").val() + "\n";
			}
			
			var msg = "";
			msg = msg + "periode: " + blth + "\n";
			msg = msg + "file loading: " + nama_file + "\n";
			msg = msg + "template email: " + combo_template_name + "\n";
			msg = msg + "status pengiriman: " + status + "\n";
			msg = msg + "attachment tambahan: " + attach_tambahan + "\n";
			msg = msg + norek;
			msg = msg + alamat_sample;
			msg = msg + "jadwal awal: " + msg_waktu + "\n";
			msg = msg + "pengiriman dilakukan sebanyak " + rec_split + " email setiap " + jeda + " menit" + "\n";
			/*end of konfirmasi sebelum melakukan pengiriman ke antrian*/
			
			var pr = $("#pr").val();
			var arr_pr = pr.split('|');
			var product = arr_pr[0];
			
			if(flag_waktu=='ok'){
				if(confirm(msg)){
					if(blth!=''){
						if($("#checkbox_nama_file").attr('checked')==true){
							if(nama_file!=''){
								if(tanda!=''){
									if(tanda=='text'){
										if(cardno_manual!=''){
											return true;
										}else{
											alert('Nomor Rekening can not empty !!!');
											return false;
										}
										 
									}else if(tanda=='file'){
										if($("#loading_cardno").val()!=''){
											if($("#loading_file").val().substr(-3).toLowerCase()=='txt'){
												return true;
											}else{
												alert('File must txt !!!');
												return false;
											}
										}else{
											alert('File can not empty !!!');
											return false;
										}
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
				$.post('script_pilihan_kirim_antrian_sk.php?act=kirim&tanda=txt',$("#form_antrian").serialize(),function(respon){
					var arr_respon = respon.split('|');
					var err = arr_respon[0];
					var flagtrans = arr_respon[1];
					var menuid = arr_respon[2];
					var jumlah = arr_respon[3];
					var exists = arr_respon[4];
					
					var data_ket = jumlah+' record telah dikirim ke antrian';
					if(err != '') data_ket = "Err:"+err+"<br><br> "+data_ket;
					
					$("#div_respon").html(data_ket);
					$("#div_exists").html('Daftar nomor rekening yang sudah ada di antrian:<br>'+exists);
					$("#div_button").html('<input type="button" id="button_selesai" name="button_selesai" class="button" value="SELESAI" onclick="goto(\'<?=$flagtrans?>|<?=$menu_id?>\')" />');
				});
			}
		}
		
		function oncheck_file(){
			if($("#checkbox_nama_file").attr('checked')==true){
				var pr = $("#pr").val();
				var blth = $("#blth").val();
				Loaddiv('div_file_loading','script_pilihan_kirim_antrian_sk.php','act=nama_file&blth='+blth+'&flagtrans=<?=$flagtrans?>');
				Loaddiv('div_file_customer','script_pilihan_kirim_antrian_sk.php','act=file_customer&blth='+blth+'&flagtrans=<?=$flagtrans?>');
			}else{
				$("#nama_file").val('');
				$("#nama_file").attr('disabled','disabled');
				$("#norek").val('');
				$("#norek").attr('disabled','disabled');
				$("#checkbox_norek").attr('disabled','disabled');
				$("#checkbox_norek").removeAttr('checked');
				$("#checkbox_inputnorek").attr('disabled','disabled');
				$("#rek").hide(500);
				$("#checkbox_inputnorek").removeAttr('checked');
				$("input[name=namafile_record]").val('0');
				$('#checkbox_norek').attr('disabled','disabled');
				$('#checkbox_norek').removeAttr('checked');
				$('#cardno_manual').val('');
				$("#cardno_manual").show(500); 
				$("#loading_cardno").hide(500);
				document.form_antrian.loading_cardno.value='';
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
		
		function oncheck_inputnorek(){
			cekFile();
			if($("#checkbox_inputnorek").attr('checked')==true){
				$('#div_ket_input').html('Gunakan pemisah dengan , atau ;');
				//$("#cardno_manual").hide(500); 
				$("#rek").show(500);
				$('#checkbox_norek').removeAttr('disabled');
				$('#checkbox_norek').removeAttr('checked');
				clear_sample();
			}else{
				$('#div_ket_input').html('');
				$("#cardno_manual").show(500); 
				$("#loading_cardno").hide(500);
				$("#rek").hide(500);
				$("#norek").attr('disabled','disabled');
				$('#checkbox_norek').attr('disabled','disabled');
				$('#checkbox_norek').removeAttr('checked');
				$('#cardno_manual').val('');
				document.form_antrian.loading_cardno.value='';
				clear_sample();
			}
		}
		
		function oncheck_norek(){
			cekFile();
			if($("#checkbox_norek").attr('checked')==true){
				$('#div_ket_input').html('');
				$("#cardno_manual").hide(500); 
				$("#loading_cardno").show(500);
                $('#check_attach').removeAttr('hidden');
                $('#cardno_manual').val('');
                //$('#check_attach').removeAttr('checked');
				clear_sample();
			}else{
				$('#div_ket_input').html('Gunakan pemisah dengan , atau ;');
				$("#cardno_manual").show(500); 
				$("#loading_cardno").hide(500);
                $('#check_attach').removeAttr('hidden');
				document.form_antrian.loading_cardno.value='';
				clear_sample();
			}
		}
		
		function oncheck_jadwal(){
			//alert('masuk');
			if($("#checkbox_jadwal").attr('checked')==true){
				$("#jadwal").show(500); 
			}else{
				$("#jadwal").hide(500); 
			}
			cekFile();
		}
		
		function goto(pr){
			window.location.href = "index_pilihan_kirim_antrian_sk.php?pr="+pr;
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
			
			Loaddiv('div_attach','script_pilihan_kirim_antrian_sk.php','flagtrans=<?=$flagtrans?>&act=attach_file&blth='+blth+'&nama_file='+nama_file);
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
				//$("#email_address").show(500);
				//Loaddiv('email_address','script_pilihan_kirim_antrian_sk.php','flagtrans=<?=$flagtrans?>&act=email&blth='+blth+'&nama_file='+nama_file+'&norek='+norek);
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
				$("#email_sample").hide(500);
				//$("#email_address").show(500);
			}else{
				//$("#email_address").hide(500);
				$("#email_sample").show(500);
				Loaddiv('email_sample','script_pilihan_kirim_antrian_sk.php','act=sample');
			}
		}
		
		function template_name(){
			var template_id = $("#combo_template").val();
			Loaddiv('div_template_name','script_pilihan_kirim_antrian_sk.php','act=template_name&template_id='+template_id);
		}
		function clear_sample(){
			$("#text_sample_email").val('');
			//$("#email_name").hide(500);
			$("#check_sample").removeAttr('checked');
			//$("#check_sample").hide(500);
			//$("#email_address").hide(500);
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
								<form method="post" enctype="multipart/form-data" id="form_antrian" name="form_antrian" action="proses_pilihan_kirim_file_antrian_sk.php?pr=<?=$pr?>" onSubmit="return cekForm()">
                                    <table width="100%" border="0" cellpadding="2" cellspacing="2" align="center">
                                    	<tr>
                                        	<td width="55%" height="150" valign="top">
                                            	<table width="100%" border="0" cellpadding="2" cellspacing="2" align="center" style="border-collapse:collapse;">
                                                    <input type="hidden" id="pr" name="pr" value="<?=$pr?>" />
                                                    <tr>
                                                        <td width="15%"  height="30" colspan="2">Periode (BLTH)</td>
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
													<!--<tr>
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
													</tr>-->
													<tr>
                                                        <td height="30" colspan="2"></td>
                                                        <td width="3%" colspan="2">
                                                            <input type="checkbox" id="checkbox_inputnorek" name="checkbox_inputnorek" onClick="oncheck_inputnorek()" disabled /> Check untuk isi rekening
                                                        </td>
                                                    </tr>
													<tr>
                                                        <td colspan="2"></td>
														<td colspan="2">
															<div id="rek" style="display:none"><table width="100%" border="0" cellpadding="2" cellspacing="2" align="center" style="border-collapse:collapse;">
																<table width="100%" border="0" cellpadding="2" cellspacing="2" align="center" style="border-collapse:collapse;">
																	<tr>
																		<td width="15%" height="30" colspan="2">Input Nomor Rekening</td>
																		<td width="3%" colspan="2">
																			<input type="checkbox" id="checkbox_norek" name="checkbox_norek" onClick="oncheck_norek()" />
																			<input type="hidden" id="flagtrans" name="flagtrans" value="<?php echo $flagtrans;?>"/> Check untuk upload file
																		</td>
																	</tr>
																	<tr>
																		<td height="30"colspan="2"></td>
																		<td colspan="2">
																			<div id="div_ket_input"></div>
																			<textarea class="textbox_4" name="cardno_manual" id="cardno_manual"></textarea>
																			<input type="file" class="textbox_4" name="loading_cardno" id="loading_cardno" size="80" onchange="cekFileFormat()"/>
																		</td>
																	</tr>
																	<tr>
																		<td>
																			<div id="email_name">
																				Sample Email
																			</div>
																		</td>
																		<td><input type="checkbox" name="check_sample" id="check_sample" onClick="func_sample()" /></td>
																		<td>
																			<!--<div id="email_address"></div>-->
																			<div id="email_sample" style="display:none">
																				<input type="text" name="text_sample_email" id="text_sample_email" class="textbox_3" value="" />
																			</div>
																		</td>
																	</tr>
																</table>
															</div>
														</td>
                                                    </tr>
													<tr>
                                                        <td height="30" colspan="2"></td>
                                                        <td width="3%" colspan="2">
                                                            <input type="checkbox" id="checkbox_jadwal" name="checkbox_jadwal" onClick="oncheck_jadwal()" disabled />
															Check untuk add jadwal
                                                        </td>
                                                    </tr>
													<tr>
														<td colspan="2"></td>
														<td colspan="2">
															<div id="jadwal" style="display:none">
													<table width="100%" border="0" cellpadding="2" cellspacing="2" align="center" style="border-collapse:collapse;">
                                                    <tr>
                                                        <td width="15%"  height="30">Pengiriman Awal</td>
                                                        <td>
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
                                                        <td width="15%"  height="30">Jeda antarjadwal</td>
                                                        <td>
                                                            <input type="text" id="jeda" name="jeda" class="textbox_1" value="1" onkeypress="return CheckKeyDecimal(this, event, /[0-9\r\0]/);" onchange="cekFile()" /> menit
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td width="15%"  height="30">Record per Jadwal</td>
                                                        <td>
                                                            <input type="text" id="rec_split" name="rec_split" class="textbox_1" value="80" onkeypress="return CheckKeyDecimal(this, event, /[0-9\r\0]/);" onchange="cekFile()" /> record
                                                        </td>
                                                    </tr>
													</table>
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
                                                    	<td height="30" valign="top">Loading Kinerja</td>
                                                        <td valign="top">
                                                        	<input type="hidden" id="tipe_kinerja" name="tipe_kinerja" value="1" />
                                                        	<input type="checkbox" id="check_kinerja" name="check_kinerja" disabled="disabled" onclick="cek_kinerja()" />
                                                        </td>
                                                    	<td><div id="div_kinerja" style="display:none"></div></td>
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
                                            	<div id="list_dataasli"></div>
                                            	<div id="div_button">
                                                	<input type="button" id="button_kirim" name="button_kirim" class="button" value="KIRIM KE ANTRIAN" style="display:none" onClick="kirim();" />
													<input type="submit" style="display:none" class="button" id="button_kirim2" name="button_kirim2" value="UPLOAD KE ANTRIAN">
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
	Loaddiv('div_blth','script_pilihan_kirim_antrian_sk.php','act=blth&flagtrans=<?=$flagtrans?>');
	Loaddiv('div_template','script_pilihan_kirim_antrian_sk.php','act=template_email&flagtrans=<?=$flagtrans?>');
	cek_status();
	$("#tgl_jadwal").datepicker({dateFormat: 'yy-mm-dd'});
</script>
<?
	@pg_close($con);
?>