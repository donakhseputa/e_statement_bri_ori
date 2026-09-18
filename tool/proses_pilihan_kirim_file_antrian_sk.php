<?php require_once("../include/config.php"); ?>
<?php require_once("../include/session.php"); ?>
<?php require_once("../include/function.php"); ?>
<?php


	$pr = $_GET['pr'];
	$arr_pr = explode('|',$pr);
	$product = $arr_pr[0];
	$blth = $_POST['blth'];
	
	$con = pg_connect($connection) or die("Could not connect to database!");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?= $title ?></title>
		<link href="../css/bootstrap.min.css" rel="stylesheet">
    </head>
    <script>
		function upload_detail(flag,blth,file,file_p){
			//alert('1');
			 //if(flag=='OO'){
				$.post('script_pilihan_kirim_antrian_sk.php?act=kirim&tanda=file',$("#form_antrian").serialize(),function(respon){
					var arr_respon = respon.split('|');
					var err = arr_respon[0];
					var flagtrans = arr_respon[1];
					var menuid = arr_respon[2];
					var jumlah = arr_respon[3];
					var exists = arr_respon[4];
					var data_ket = jumlah+' record telah dikirim ke antrian';
					if(err != '') data_ket = "Err:"+err+"<br><br> "+data_ket;
					
						
					var data_ket = jumlah+' record telah dikirim ke antrian<br><br>nomor rekening yang sudah ada di antrian:<br>'+exists+'<br>';
					if(err != '') data_ket = "Err:"+err+"<br><br> "+data_ket;
					$("#div_message").html(data_ket);
				});
				$("#div_button").html('<input name="finish" type="button" class="button" value="FINISH" onclick="back(\'<?=$pr?>\');" />');
				/*
				var url = 'script_pilihan_kirim_antrian_sk.php?act=kirim&tanda=file';

				$.ajax({
				   type: "POST",
				   url: url,
				   data: $("#form_antrian").serialize(), // serializes the form's elements.
				   success: function(respon)
				   {
						alert(respon); // show response from the php script.
						var arr_respon = respon.split('|');
						var err = arr_respon[0];
						var flagtrans = arr_respon[1];
						var menuid = arr_respon[2];
						var jumlah = arr_respon[3];
						var exists = arr_respon[4];
						
						var data_ket = jumlah+' record telah dikirim ke antrian<br><br>nomor rekening yang sudah ada di antrian:<br>'+exists+'<br>';
						if(err != '') data_ket = "Err:"+err+"<br><br> "+data_ket;
						$("#div_message").html(data_ket);
				   },
				   error: function (respon){	alert('Isi form ini dengan benar' + respon);	}
				 });*/
			//}else{
			//	alert('PRODUK BELUM TERDAFTAR, HUBUNGI DATANET');
			//}
		}
		

		function back(pr)
		{
			window.location.href = "index_pilihan_kirim_antrian_sk.php?pr="+pr;
		}
	</script>
    <?php
		
		chmod("../tmp/", 0777);
		$loading_folder="";
		$loc_bill="../tmp/file_upload/";
		if(!file_exists($loc_bill)) mkdir($loc_bill)or die('err1');
		//$loc_bill.=$product."/";
		//if(!file_exists($loc_bill)) mkdir($loc_bill)or die('err1');
		
		//$pr = $_POST['pr'];
		$blth = $_POST['blth'];
		$checkbox_nama_file = $_POST['checkbox_nama_file'];
		$nama_file = $_POST['nama_file'];
		$checkbox_inputnorek = $_POST['checkbox_inputnorek'];
		$checkbox_norek = $_POST['checkbox_norek'];
		$flagtrans = $_POST['flagtrans'];
		$cardno_manual = $_POST['cardno_manual'];
		$check_sample = $_POST['check_sample'];
		$text_sample_email = $_POST['text_sample_email'];
		$tgl_jadwal = $_POST['tgl_jadwal'];
		$hour_jadwal = $_POST['hour_jadwal'];
		$min_jadwal = $_POST['min_jadwal'];
		$jeda = $_POST['jeda'];
		$rec_split = $_POST['rec_split'];
		$combo_template = $_POST['combo_template'];
		$combo_template_name = $_POST['combo_template_name'];
		$check_status = $_POST['check_status'];
		$status = $_POST['status'];
		$tipe_kinerja = $_POST['tipe_kinerja'];
		$checkbox_jadwal = $_POST['checkbox_jadwal'];
		$tipe_attach = $_POST['tipe_attach'];

		//echo $pr.' | '.$blth." | ".$loc_bill;die();
		
		$folder_dest=$loc_bill;

		$file = $_FILES["loading_cardno"]["name"];
		$file = strtolower(str_replace(" ", "_", $file));
		
		if(move_uploaded_file($_FILES["loading_cardno"]["tmp_name"], $folder_dest . $file)){
			chmod($folder_dest . $file, 0777);
		}else{
			$error = 1;
		}
		
		$file_p='';
	?>
    <body>
		<?php require_once("../include/script.php"); ?>
        <?php require_once("../include/message.php"); ?>
        <table id="container" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td align="center" valign="top">
                    <?php require_once("../include/header.php"); ?>
                    <?php require_once("../include/menu.php"); ?>
                    <?php require_once("../include/greeting.php"); ?>
					<form id="form_antrian" name="form_antrian" enctype="multipart/form-data" method="post">
					<input type="hidden" name="loading_cardno" id="loading_cardno" value="<?=$file?>">
					<input type="hidden" name="pr" id="pr" value="<?=$pr?>">
					<input type="hidden" name="blth" id="blth" value="<?=$blth?>">
					<input type="hidden" name="checkbox_nama_file" id="checkbox_nama_file" value="<?=$checkbox_nama_file?>">
					<input type="hidden" name="nama_file" id="nama_file" value="<?=$nama_file?>">
					<input type="hidden" name="checkbox_inputnorek" id="checkbox_inputnorek" value="<?=$checkbox_inputnorek?>">
					<input type="hidden" name="checkbox_norek" id="checkbox_norek" value="<?=$checkbox_norek?>">
					<input type="hidden" name="flagtrans" id="flagtrans" value="<?=$flagtrans?>">
					<input type="hidden" name="cardno_manual" id="cardno_manual" value="<?=$cardno_manual?>">
					<input type="hidden" name="check_sample" id="check_sample" value="<?=$check_sample?>">
					<input type="hidden" name="text_sample_email" id="text_sample_email" value="<?=$text_sample_email?>">
					<input type="hidden" name="tgl_jadwal" id="tgl_jadwal" value="<?=$tgl_jadwal?>">
					<input type="hidden" name="hour_jadwal" id="hour_jadwal" value="<?=$hour_jadwal?>">
					<input type="hidden" name="checkbox_jadwal" id="checkbox_jadwal" value="<?=$checkbox_jadwal?>">
					<input type="hidden" name="min_jadwal" id="min_jadwal" value="<?=$min_jadwal?>">
					<input type="hidden" name="jeda" id="jeda" value="<?=$jeda?>">
					<input type="hidden" name="rec_split" id="rec_split" value="<?=$rec_split?>">
					<input type="hidden" name="combo_template" id="combo_template" value="<?=$combo_template?>">
					<input type="hidden" name="combo_template_name" id="combo_template_name" value="<?=$combo_template_name?>">
					<input type="hidden" name="check_status" id="check_status" value="<?=$check_status?>">
					<input type="hidden" name="status" id="status" value="<?=$status?>">
					<input type="hidden" name="tipe_kinerja" id="tipe_kinerja" value="<?=$tipe_kinerja?>">
					<input type="hidden" name="tipe_attach" id="tipe_attach" value="<?=$tipe_attach?>">
					</form>
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
                        	<td align="center" height="30">
                            	<div id="div_message">
                                <?php
									switch ($error) {
										case 0:
											//echo("-- File telah berhasil diupload dengan sukses --<br />");
											//echo("-- Data file sedang diimpor ke database, <u>harap menunggu</u> --");
											echo ('<div class="alert alert-info"><b>PROSES INFO!</b> <br>File telah berhasil diupload dengan sukses
											<br>
											Proses Inject sedang berjalan, <u>harap menunggu</u>... <br>
													<img src="../images/ajax.gif" alt="loading..." border="0" />
													<img src="../images/ajax.gif" alt="loading..." border="0" />
													<img src="../images/ajax.gif" alt="loading..." border="0" />
													<img src="../images/ajax.gif" alt="loading..." border="0" />
													<img src="../images/ajax.gif" alt="loading..." border="0" />
													</div>');
											break;
										case 1:
											echo("-- PERHATIAN !!! --<br /><br />");
											echo("-- file gagal diupload ke server --");
											break;
										case 2:
											echo("-- PERHATIAN !!! --<br /><br />");
											echo("-- file bukan bertipe ZIP (.zip) --");
											break;
										case 3:
											echo("-- PERHATIAN !!! --<br /><br />");
											echo("-- file bukan bertipe txt --");
											break;
										case 4:
											echo("-- PERHATIAN !!! --<br /><br />");
											echo("-- file bukan bertipe pdf --");
											break;
									}
								?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                        	<td height="20">&nbsp;</td>
                        </tr>
                        <tr>
                        	<td align="center" height="30">
                            	<div id="div_button"></div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td align="center" valign="top">
                    <?php require_once("../include/footer.php"); ?>
                </td>
            </tr>
        </table>
    </body>
</html>
<script>
	<?php
		if($error==0){
	?>
			upload_detail('<?=$flagtrans?>','<?=$blth?>','<?=$file?>','<?=$file_p?>');
	<?php
		}
	?>
</script>
<?php
	@pg_close($con);
?>