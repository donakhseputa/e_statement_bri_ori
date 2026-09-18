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
	
	$file_excel =  $arr_pr[4];
	#die($file_excel);
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
	
	function timer(elem, starttime, endtime, speed, funktion, count) {
			if (!endtime) endtime = 0;
			if (!starttime) starttime = 10;
			if (!speed) speed = 1;
			speed = speed * 1000;
			if ($(elem).html() || $(elem).val()) {
				if (count == "next" && starttime > endtime) starttime--;
				else if (count == "next" && starttime < endtime) starttime++;
				if ($(elem).html()) $(elem).html(starttime);
				else if ($(elem).val()) $(elem).val(starttime);
				if (starttime != endtime && $(elem).html()) setTimeout(function() {
					timer(elem, $(elem).html(), endtime, speed / 1000, funktion, 'next');
				}, speed);
				if (starttime != endtime && $(elem).val()) setTimeout(function() {
					timer(elem, $(elem).val(), endtime, speed / 1000, funktion, 'next');
				}, speed);
				if (starttime == endtime && funktion) funktion();
			} else return;
		}
		
		function upload_detail(val,flag,rad_cust, file_php){
	
			
			if(flag=='BC'){
			//alert(val);return false;
					//$.post("script_loading_detail_a1_excel.php",{pr:val},function(respon){
					$.post(file_php,{pr:val},function(respon){
						//alert ( respon );
						var arr_respon = respon.split('|');
						var pesan = arr_respon[0];
						var jml_record = arr_respon[1];
						var record_not_exists = arr_respon[2];
						var waktu = arr_respon[3];
						var flagtrans = arr_respon[4];
						var menit = parseInt(waktu / 60);
						var detik = parseInt(waktu % 60);
						
						$("#div_message").html("-- "+ pesan + " --<br /><br />");
						//$("#div_message").html("-- "+ pesan + " --<br /><br /> Jumlah Record = " + jml_record + " record --<br /><br />" + record_not_exists + "<br /><br />-- Total waktu impor data = " + menit + " menit " + detik + " detik --");
						$("#div_image").html('SELESAI');
						//$("#div_button").html('<input name="finish" type="button" class="button" value="FINISH" onclick="goto(\''+flagtrans+'\');" />');
						pesan = pesan.trim();
						if ( pesan != 'FILE PERNAH DI PROSES SEBELUMNYA' && pesan != 'HEADER CSV SALAH')
						{
							
							$("#div_message").html("");
							$("#div_image").html("");
							 $('#pesan_logout').show()
							timer("#timer", 60, 0, 1, function() {
								//alert("The End");
								location.href = "../tool/index_loading_detail_a1.php?pr=BC|13";
							});
							
						}
						
					});
			}
		
		
		}
		
		function goto(flagtrans){
			window.location.href = "../home/index.php?pr="+flagtrans;
		}
	</script>
    <?php
		/*
		$loc_bill="../temp_file/billing/";
		if(!file_exists($loc_bill)) mkdir($loc_bill)or die('err1');//else echo $loc_bill.":ada";
		$loc_bill.=$product."/";
		if(!file_exists($loc_bill)) mkdir($loc_bill)or die('err1');//else echo $loc_bill.":ada";
		$loc_bill.=$blth."/";
		if(!file_exists($loc_bill)) mkdir($loc_bill)or die('err1');//else echo $loc_bill.":ada";
		
		$folder_dest="../temp_file/billing/".$product."/".$blth."/";
		chmod("../temp_file/", 0777);
		$file = $_FILES["loading_file"]["name"];
		$file = strtoupper(str_replace(" ", "_", $file));
		//$filecust = $_FILES["loading_file_customer"]["name"];
		//$filecust = strtoupper(str_replace(" ", "_", $filecust));
		//$filecust = str_replace(".TXT","",$filecust);
		//$filecust = $filecust . "_" . date("Ymd_His").".TXT";
		
		
		chmod("../tmp/", 0777);
		//if( (move_uploaded_file($_FILES["loading_file"]["tmp_name"], $folder_dest . $file))
		//&& (move_uploaded_file($_FILES["loading_file_customer"]["tmp_name"], $folder_dest .$filecust)) )
	
		if( (move_uploaded_file($_FILES["loading_file"]["tmp_name"], $folder_dest . $file)) )
		{
			file_write($folder_dest . $file, "r", "0777");
			//file_write($folder_dest . $filecust, "r", "0777");
			$error = 0;
		}else{
			$error = 1;
		}
		*/
		
		
		$file = $_POST['cycle_loading'];
		$file = strtoupper(strtolower(str_replace('.dat','_F.dat',$file)));
		
		#die ("namaf filenya : ".$file);
		/*
		if ( preg_match("/1CIF/i", $file) )
		{
			$file_php = "script_loading_detail_a1_excel_1cifxxxxxxxxx.php";
		
		
		
		}else{
			$file_php = "script_loading_detail_a1_excel.php";
		}
		*/
		$file_php = "script_loading_detail_a1_excel.php";
		
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
						<tr align="center" height="30">
						<td height="30" style="display:none;" id="pesan_logout">Proses Loading CSV Berhasil<br>
						<img src="../images/ajax.gif" alt="loading..." border="0" />
						<img src="../images/ajax.gif" alt="loading..." border="0" />
						<img src="../images/ajax.gif" alt="loading..." border="0" />
						<img src="../images/ajax.gif" alt="loading..." border="0" />
						<br>Data sedang di sinkronisasi.. mohon menunggu.......
						<br>halaman akan di refresh setelah <b id="timer">5</b> <b>second</b></td>
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
			upload_detail('<?php echo $product.'|'.$menu_id.'|'.$act.'|'.$blth.'|'.$file.'|'.$file_mcustomer.'|'.$filecust?>','<?php echo $product?>','<?php echo $rad_cust?>', '<?php echo $file_php?>');
	<?php
		}
	?>
</script>
<?php
	pg_close($con);
?>