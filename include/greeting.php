<? require_once("server_time.php"); ?>
<?
	function Greeting() {
		$jam = date("H");
		switch ($jam) {
			case '04' :
			case '05' :
			case '06' :
			case '07' :
			case '08' :
			case '09' :
			case '10' :
			case '11' :
				$greet = "Selamat Pagi, ";
				break;
			case '12' :
			case '13' :
			case '14' :
			case '15' :
				$greet = "Selamat Siang, ";
				break;
			case '16' :
			case '17' :
			case '18' :
				$greet = "Selamat Sore, ";
				break;
			case '19' :
			case '20' :
			case '21' :
			case '22' :
			case '23' :
			case '00' :
			case '01' :
			case '02' :
			case '03' :
				$greet = "Selamat Malam, ";
				break;
		}
		return $greet;
	}
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="100%" height="25" align="right" valign="middle"><strong><?= greeting() . $_SESSION["user_name"] . " -"?></strong>&nbsp;<strong><script type="text/javascript">clock();</script></strong></td>
	</tr>
</table>
