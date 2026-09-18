<? require_once("../include/config.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?= $title ?></title>
<? require_once("../include/script.php"); ?>
<? require_once("../include/message.php"); ?>
<script type="text/javascript">
	function CheckForm() {
		var obj = document.form_login;
		var msg = "";
		if (obj.pass_login.value == "") {
			msg = "\n- Password harap diisi." + msg;
			obj.pass_login.focus();
		}
		if (obj.user_login.value == "") {
			msg = "\n- User ID harap diisi." + msg;
			obj.user_login.focus();
		}
		if (msg == "") {
			return true;
		} else {
			msg = "Daftar error :" + msg;
			alert(msg);
			return false;
		}
	}
</script>
</head>

<body>
<table id="container" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td align="center" valign="top">
			<? require_once("../include/header.php"); ?>
		</td>
	</tr>
	<tr>
		<td height="500" align="center" valign="top">
			<form action="login.php" method="post" name="form_login" onsubmit="return CheckForm();">
			<table id="login_box" border="0" cellpadding="2" cellspacing="0">
				<tr>
					<td width="40%">&nbsp;</td>
					<td width="5%">&nbsp;</td>
					<td width="55%">&nbsp;</td>
				</tr>
				<tr>
					<td align="right" valign="middle">User ID</td>
					<td>&nbsp;</td>
					<td align="left" valign="middle">
						<input name="user_login" type="text" class="textbox_2" maxlength="10" />
					</td>
				</tr>
				<tr>
					<td align="right" valign="middle">Password</td>
					<td>&nbsp;</td>
					<td align="left" valign="middle">
						<input name="pass_login" type="password" class="textbox_2" maxlength="10" />
					</td>
				</tr>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3" align="center" valign="middle">
						<input name="login" type="submit" class="button" value="LOGIN" />
					</td>
				</tr>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
	<tr>
		<td align="center" valign="top">
			<? require_once("../include/footer.php"); ?>
		</td>
	</tr>
</table>
<script type="text/javascript">
	document.form_login.user_login.focus();
</script>
</body>
</html>
