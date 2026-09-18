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
		function show_blth_log(){
			var flagtrans = $("#flagtrans").val();
			var pilih_produk = $("#pilih_produk").val();
			var blth = $("#blth_show").val();
			//alert(blth);
			var input_search = $("#input_search_log").val();
			var field_name = $("#field_name").val();
			input_search = input_search.replace(/\s/g,'8764346466435364647768799667654537543756');
			Loaddiv('div_detail','script_search_detail.php','act=log_detail&flagtrans='+flagtrans+'&pilih_produk='+pilih_produk+'&blth='+blth+'&offset=0&field_name='+field_name+'&input_search='+input_search);
		}
		
		function refresh_click(){
			var flagtrans = $("#flagtrans").val();
			var blth = $("#blth_show").val();
			
			$("#input_search_log").val("Search Nomor, Nama, or Email");
			$("#div_detail").html("");
		}
	
		function cek(e,n){
			var charCode;
			if(e && e.which){
				charCode=e.which;
			}else if(window.event){
				e=window.event;
				charCode=e.keyCode;
			}
				
			if(charCode==13){
				show_blth_log();
			}
		}
		
		function kosong(){
			var val_text = $("#input_search_log").val();
			
			if(val_text == "Search Nomor, Nama, or Email"){
				$("#input_search_log").val("");
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
                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                        <input type="hidden" id="pr" name="pr" value="<?php echo $pr?>" />
                        <input type="hidden" id="flagtrans" name="flagtrans" value="<?php echo $flagtrans?>" />
                        <tr>
                            <td>
                                <? require_once("../include/page_title.php"); ?>
                            </td>
                        </tr>
                        <tr>
                        	<td>
                            	<table width="100%">
                                	<tr>
                                    	<td align="left" colspan="4">Keterangan Status:</td>
                                    </tr>
                                	<tr>
                                    	<td align="left" colspan="4">1. EMAIL NOT FOUND -> Data tersebut sudah proses loading tetapi tidak memiliki alamat email di bulan tersebut</td>
                                    </tr>
                                    <tr>
                                    	<td align="left" colspan="4">2. NO STATUS -> Data tersebut sudah proses loading tetapi belum dilakukan proses pengiriman</td>
                                    </tr>
                                    <tr>
                                    	<td align="left" colspan="4">3. QUEUE -> Data tersebut sudah proses loading dan sudah proses antrian tetapi belum dilakukan proses pengiriman</td>
                                    </tr>
                                    <tr>
                                    	<td align="left" colspan="4">4. SUCCESS -> Data tersebut sudah proses loading dan sudah proses pengiriman dengan status dianggap sukses terkirim</td>
                                    </tr>
                                    <tr>
                                    	<td align="left" colspan="4">5. FAILED -> Data tersebut sudah proses loading dan sudah proses pengiriman dengan status gagal terkirim</td>
                                    </tr>
                                    <tr>
                                    	<td align="left" colspan="4">6. DATA NOT FOUND -> Data tersebut tidak ditemukan atau tidak dilakukan proses e-statement</td>
                                    </tr>
                                    <tr>
                                    	<td align="left" colspan="4">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td width="20%" height="50%" align="left">Periode: 
                                            <select id="blth_show" name="blth_show" class="combobox">
                                                <?php echo $flagtrans;
                                                $sql_sel_blth = "
													SELECT blth
													FROM m_loading
													WHERE flagtrans = '$flagtrans'
													GROUP BY blth
													ORDER BY substring(blth,3,4) DESC, substring(blth,1,2) DESC
												";
                                                $qry_sel_blth = pg_query($sql_sel_blth) or die('ERROR '.$sql_sel_blth);
                                                while($row_blth=pg_fetch_array($qry_sel_blth)){
                                                    ?>
                                                    <option value="<?php echo $row_blth['blth']?>"
                                                        <?php
                                                        if($blth==date('mY')){
                                                            echo 'selected';
                                                        }
                                                        ?>><?php echo $row_blth['blth']?></option>
                                                    <?php
                                                }
                                                ?>
                                            	<option value="0">ALL</option>
                                            </select>
                                        </td>
                                        <td width="30%" align="left">Produk : 
                                          <select id="pilih_produk" name="pilih_produk" class="combobox">
                                            <?php
                                                $sql_sel_produk = "
													SELECT flagtrans, produk
													FROM mproduk WHERE status = 't'";
                                                $qry_sel_ = pg_query($sql_sel_produk) or die('ERROR '.$sql_sel_produk);
                                                while($row_produk = pg_fetch_array($qry_sel_))
												{
													 ?>
            										<option value="<?= $row_produk["flagtrans"] ?>"
                       								<?php if($row_produk["flagtrans"]==$flagtrans){
                                                     echo 'selected';
                                                        }
                                                     ?>
                        >
              <?= $row_produk["produk"]  ?>
              </option>
            <? 
													}
                                                ?>
                                            <option value="0">ALL</option>
                                    </select></td>
                                        <td width="7%" align="left">&nbsp;</td>
                                        <td width="37%" height="50%" align="right">&nbsp;
                                            <a onClick="refresh_click()"><img class="vtip" title="refresh" src="../images/icons/rfrsh_data.png" /></a>&nbsp;
                                            <select name="field_name" id="field_name" class="combobox">
                                            	<option value="nomor_rekening" selected="selected">Nomor</option>
                                            	<option value="nama">Nama</option>
                                            	<option value="email">Email</option>
                                            </select>&nbsp;
                                            <label class="vtip" title="Search Nomor, Nama, or Email">
                                                <input type="text" class="textbox_3" id="input_search_log" name="input_search_log" onKeyPress="kosong();cek(event,1)" value="Search Nomor, Nama, or Email" style="font-style:italic;font-size:9px;" onclick="kosong()" />
                                            </label>&nbsp;
                                            <a onClick="show_blth_log()"><img class="vtip" title="search" src="../images/icons/find_data.png" /></a>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                        	<td align="center"><div id="div_detail" style="overflow:auto; width:998px;"></div></td>
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
	$("#input_search_log").focus();
</script>
<?
	pg_close($con);
?>