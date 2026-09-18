<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/function.php"); ?>
<?
	$pr = $_GET['pr'];
	$arr_pr = explode('|',$pr);
	$product = $arr_pr[0];
	$menu_id = $arr_pr[1];
	$act = $arr_pr[2];
	$blth = $_POST['blth'];
	$rad_cust = $_POST['rad_cust'];
	$file_mcustomer = $_POST['file_mcustomer'];
	$con = pg_connect($connection) or die("Could not connect to database!");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?= $title ?></title>
    </head>
    <script>
		function upload_detail(val,flag,rad_cust){
			if(flag=='BC'){
				if(rad_cust==2){
					$.post("script_loading_detail_bc.php",{pr:val},function(respon){
						var arr_respon = respon.split('|');
						var pesan = arr_respon[0];
						var jml_record = arr_respon[1];
						var record_not_exists = arr_respon[2];
						var waktu = arr_respon[3];
						var flagtrans = arr_respon[4];
						var menit = parseInt(waktu / 60);
						var detik = parseInt(waktu % 60);
						
						$("#div_message").html("-- "+ pesan + "--<br /><br /> Jumlah Record = " + jml_record + " record --<br /><br />nomor rekening yang tidak ada di master customer:<br />" + record_not_exists + "<br /><br />-- Total waktu impor data = " + menit + " menit " + detik + " detik --");
						$("#div_image").html('SELESAI');
						$("#div_button").html('<input name="finish" type="button" class="button" value="FINISH" onclick="goto(\''+flagtrans+'\');" />');
					});
				}else if(rad_cust==1){
					$.post("script_loading_detail_nocust_bc.php",{pr:val},function(respon){
						var arr_respon = respon.split('|');
						var pesan = arr_respon[0];
						var jml_record = arr_respon[1];
						var record_not_exists = arr_respon[2];
						var waktu = arr_respon[3];
						var flagtrans = arr_respon[4];
						var menit = parseInt(waktu / 60);
						var detik = parseInt(waktu % 60);
						
						$("#div_message").html("-- "+ pesan + "--<br /><br /> Jumlah Record = " + jml_record + " record --<br /><br />nomor rekening yang tidak memiliki email:<br />" + record_not_exists + "<br /><br />-- Total waktu impor data = " + menit + " menit " + detik + " detik --");
						$("#div_image").html('SELESAI');
						$("#div_button").html('<input name="finish" type="button" class="button" value="FINISH" onclick="goto(\''+flagtrans+'\');" />');
					});
				}
			}else if(flag=='CO'){
				if(rad_cust==2){
					$.post("script_loading_detail_co.php",{pr:val},function(respon){
						var arr_respon = respon.split('|');
						var pesan = arr_respon[0];
						var jml_record = arr_respon[1];
						var record_not_exists = arr_respon[2];
						var waktu = arr_respon[3];
						var flagtrans = arr_respon[4];
						var menit = parseInt(waktu / 60);
						var detik = parseInt(waktu % 60);
						
						$("#div_message").html("-- "+ pesan + "--<br /><br /> Jumlah Record = " + jml_record + " record --<br /><br />nomor rekening yang tidak ada di master customer:<br />" + record_not_exists + "<br /><br />-- Total waktu impor data = " + menit + " menit " + detik + " detik --");
						$("#div_image").html('SELESAI');
						$("#div_button").html('<input name="finish" type="button" class="button" value="FINISH" onclick="goto(\''+flagtrans+'\');" />');
					});
				}else if(rad_cust==1){
					var pesan = 'maaf, fungsi "tanpa customer" belum tersedia';
					var waktu = 0;
					var menit = parseInt(waktu / 60);
					var detik = parseInt(waktu % 60);
					var flagtrans = flag;
					
					$("#div_message").html("-- "+ pesan + "--<br /><br />-- Total waktu impor data = " + menit + " menit " + detik + " detik --");
					$("#div_image").html('SELESAI');
					$("#div_button").html('<input name="finish" type="button" class="button" value="FINISH" onclick="goto(\''+flagtrans+'\');" />');
				}
			}else if(flag=='BD'){
				if(rad_cust==2){
					$.post("script_loading_detail_bd.php",{pr:val},function(respon){
						var arr_respon = respon.split('|');
						var pesan = arr_respon[0];
						var jml_record = arr_respon[1];
						var record_not_exists = arr_respon[2];
						var waktu = arr_respon[3];
						var flagtrans = arr_respon[4];
						var menit = parseInt(waktu / 60);
						var detik = parseInt(waktu % 60);
						
						$("#div_message").html("-- "+ pesan + "--<br /><br /> Jumlah Record = " + jml_record + " record --<br /><br />nomor rekening yang tidak ada di master customer:<br />" + record_not_exists + "<br /><br />-- Total waktu impor data = " + menit + " menit " + detik + " detik --");
						$("#div_image").html('SELESAI');
						$("#div_button").html('<input name="finish" type="button" class="button" value="FINISH" onclick="goto(\''+flagtrans+'\');" />');
					});
				}else if(rad_cust==1){
					var pesan = 'maaf, fungsi "tanpa customer" belum tersedia';
					var waktu = 0;
					var menit = parseInt(waktu / 60);
					var detik = parseInt(waktu % 60);
					var flagtrans = flag;
					
					$("#div_message").html("-- "+ pesan + "--<br /><br />-- Total waktu impor data = " + menit + " menit " + detik + " detik --");
					$("#div_image").html('SELESAI');
					$("#div_button").html('<input name="finish" type="button" class="button" value="FINISH" onclick="goto(\''+flagtrans+'\');" />');
				}
			}else{
				alert('PRODUK BELUM TERDAFTAR, HUBUNGI APPDEV');
			}
		}
		
		function goto(flagtrans){
			window.location.href = "../home/index.php?pr="+flagtrans;
		}
	</script>
    <?
		if(move_uploaded_file($_FILES["loading_file"]["tmp_name"], "../tmp/" . $_FILES["loading_file"]["name"])){
			file_write("../tmp/" . $_FILES["loading_file"]["name"], "r", "0777");
			//chmod("../tmp/" . $_FILES["loading_file"]["name"], 0777);
			$file = $_FILES["loading_file"]["name"];
			$error = 0;
		}else{
			$error = 1;
		}
	?>
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
                        	<td align="center" height="30">
                            	<?
									if($error==0){
								?>
									<div id="div_image">
										<img src="../images/ajax.gif" alt="loading..." border="0" />
										<img src="../images/ajax.gif" alt="loading..." border="0" />
										<img src="../images/ajax.gif" alt="loading..." border="0" />
										<img src="../images/ajax.gif" alt="loading..." border="0" />
										<img src="../images/ajax.gif" alt="loading..." border="0" />
									</div>
                                <?
									}
								?>
                            </td>
                        </tr>
                        <tr>
                        	<td align="center" height="30">
                            	<div id="div_message">
                                <?
									switch ($error) {
										case 0:
											echo("-- File telah berhasil diupload dengan sukses --<br />");
											echo("-- Data file sedang diimpor ke database, <u>harap menunggu</u> --");
											break;
										case 1:
											echo("-- PERHATIAN !!! --<br /><br />");
											echo("-- file gagal diupload ke server --");
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
                            	<div id="div_button">
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
        </table>
    </body>
</html>
<script>
	<?
		if($error==0){
	?>
			upload_detail('<?=$pr.'|'.$blth.'|'.$file.'|'.$file_mcustomer?>','<?=$product?>','<?=$rad_cust?>');
	<?
		}
	?>
</script>
<?
	pg_close($con);
?>