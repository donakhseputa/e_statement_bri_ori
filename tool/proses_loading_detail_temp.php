<?php require_once("../include/config.php"); ?>
<?php require_once("../include/session.php"); ?>
<?php require_once("../include/function.php"); ?>
<?php

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
        <title><?php echo $title ?></title>
    </head>
    <script>
		function upload_detail(val,flag,rad_cust){
			if(flag=='BE'){
				$.post("script_loading_detail_be.php",{pr:val},function(respon){
					var arr_respon = respon.split('|');
					var pesan = arr_respon[0];
					var jml_record = arr_respon[1];
					var record_not_exists = arr_respon[2];
					var waktu = arr_respon[3];
					var flagtrans = arr_respon[4];
					var menit = parseInt(waktu / 60);
					var detik = parseInt(waktu % 60);
					
					$("#div_message").html("-- "+ pesan + "--<br /><br /> Jumlah Record = " + jml_record + " record --<br /><br />-- Total waktu impor data = " + menit + " menit " + detik + " detik --<br /><br />" + record_not_exists + "<br />");
					$("#div_image").html('SELESAI');
					$("#div_button").html('<input name="finish" type="button" class="button" value="FINISH" onclick="goto(\''+flagtrans+'\');" />');
				});
			}
		}
		
		function goto(flagtrans){
			window.location.href = "../home/index.php?pr="+flagtrans;
		}
	</script>
    <?php
		#delete temporary file
		#$folder_tmp = "../tmp/unzip/".$product."/";
		#if(is_dir($folder_tmp)){
		#	hapus_folder($folder_tmp);
		#}
		$loc_bill="../temp_file/billing/";
		if(!file_exists($loc_bill)) mkdir($loc_bill)or die('err1');//else echo $loc_bill.":ada";
		$loc_bill.=$product."/";
		if(!file_exists($loc_bill)) mkdir($loc_bill)or die('err1');//else echo $loc_bill.":ada";
		$loc_bill.=$blth."/";
		if(!file_exists($loc_bill)) mkdir($loc_bill)or die('err1');//else echo $loc_bill.":ada";
		
		$folder_dest="../temp_file/billing/".$product."/".$blth."/";
		chmod("../temp_file/", 0777);
		$file = $_FILES["loading_file"]["name"];
		$file = str_replace(" ", "_", $file);
		
		
		chmod("../tmp/", 0777);
		$ext = strtolower(get_nfile_ext($file));
		if($ext=='.txt'){
			/*$exp = explode("_",$file);
			if(strtolower($exp[0])=='samira')
			{*/
				if((move_uploaded_file($_FILES["loading_file"]["tmp_name"], $folder_dest . $file))){
					file_write($folder_dest . $file, "r", "0777");
					$error = 0;
				}else{
					$error = 1;
				}
			
			/*}else
			{
				$error = 3;
			}*/
		}else{
			$error = 2;
		}
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
                            	<?php
									if($error==0){
								?>
									<div id="div_image">
										<img src="../images/ajax.gif" alt="loading..." border="0" />
										<img src="../images/ajax.gif" alt="loading..." border="0" />
										<img src="../images/ajax.gif" alt="loading..." border="0" />
										<img src="../images/ajax.gif" alt="loading..." border="0" />
										<img src="../images/ajax.gif" alt="loading..." border="0" />
									</div>
                                <?php
									}
								?>
                            </td>
                        </tr>
                        <tr>
                        	<td align="center" height="30">
                            	<div id="div_message">
                                <?php
									switch ($error) {
										case 0:
											echo("-- File telah berhasil diupload dengan sukses --<br />");
											echo("-- Data file sedang diimpor ke database, <u>harap menunggu</u> --");
											break;
										case 1:
											echo("-- PERHATIAN !!! --<br /><br />");
											echo("-- file gagal diupload ke server --");
											break;
										case 2:
											echo("-- PERHATIAN !!! --<br /><br />");
											echo("-- file yang diupload bukan zip atau rar --");
											break;
										case 3:
											echo("-- PERHATIAN !!! --<br /><br />");
											echo("-- fromat penamaan file salah atau belum terdaftar <br>Contoh : samira_*.txt --");
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
			upload_detail('<?php echo $product.'|'.$menu_id.'|'.$act.'|'.$blth.'|'.$file.'|'.$file_mcustomer.'|'.$filecust?>','<?php echo $product?>','<?php echo $rad_cust?>');
	<?php
		}
	?>
</script>
<?php
	pg_close($con);
?>