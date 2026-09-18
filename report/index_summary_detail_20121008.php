<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/function.php"); ?>
<?
	$pr = trim($_GET["pr"]);
	$arr_pr = explode("|", $pr);
	$flagtrans = trim($arr_pr[0]);
	$menu_id = (int)trim($arr_pr[1]);
	
	$con = pg_connect($connection) or die("Could not connect to database!");
	$akses = userAcces($connection,$menu_id);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?= $title ?></title>
    </head>
    <script>
		function cek(){
			var blth = $("#blth").val();
			var tipe_rpt = $("#tipe_rpt").val();
			
			if(blth!='' && tipe_rpt!=''){
				$("#button_export").show(500);
			}else{
				$("#button_export").hide(500);
			}
		}
		
		function export_rpt(){
			var blth = $("#blth").val();
			var tipe_rpt = $("#tipe_rpt").val();
			var pr = $("#pr").val();
			
			window.open('script_summary_detail.php?act='+tipe_rpt+'&blth='+blth+'&pr='+pr);
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
                                	<input type="hidden" id="pr" name="pr" value="<?=$pr?>" />
                                	<tr>
                                    	<td height="30" width="30%">Periode Loading</td>
                                        <td>
                                        	<div id="div_blth">
                                                <select id="blth" name="blth" class="combobox">
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                    	<td>Tipe Report</td>
                                        <td>
                                        	<select id="tipe_rpt" name="tipe_rpt" class="combobox" onchange="cek()">
                                            	<option value=""></option>
                                            	<?php
												if(substr($akses,0,1)==1){
													?>
                                                    <option value="view">VIEW</option>
                                                    <?php
												}
												?>
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
                            	<input type="button" class="button" id="button_export" name="button_export" value="EXPORT" style="display:none" onclick="export_rpt()" />
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
	Loaddiv('div_blth','script_summary_detail.php','act=blth&flagtrans=<?=$flagtrans?>');
</script>
<?
	pg_close($con);
?>