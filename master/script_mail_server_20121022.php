<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<?
	$act = $_REQUEST['act'];
	$con = pg_connect($connection) or die("Could not connect to database!");
	
	switch($act){
		case 'show_table': show_table(); break;
		case 'add_server': add_server(); break;
		case 'save': save(); break;
		case 'edit_server': edit_server(); break;
		case 'edit': edit(); break;
		case 'delete_server': delete_server(); break;
	}
	
	function show_table(){
		?>
        <table width="100%" cellpadding="0" cellspacing="0" border="1" style="border:1px;border-style:solid">
            <tr class="table_header">
                <td colspan="2">EMAIL FROM</td>
                <td colspan="3">FEEDBACK EMAIL</td>
                <td width="10%" rowspan="2">User</td>
                <td width="17%" rowspan="2">Waktu</td>
                <td width="13%" rowspan="2">Status</td>
                <td rowspan="2">ACT</td>
            </tr>
            <tr class="table_header">
                <td width="13%">Email Host</td>
                <td width="13%">From</td>
                <td width="13%">Feedback Host</td>
                <td width="18%">Inbox</td>
                <td width="10%">Password</td>
            </tr>
        <?php
		$sql_sel_mail_server = "SELECT * FROM mail_server ORDER BY email_host";
		$qry_sel_mail_server = pg_query($sql_sel_mail_server) or die('ERROR select mail_server: '.$sql_sel_mail_server);
		while($row_sel_mail_server=pg_fetch_array($qry_sel_mail_server)){
			$i++;
			$mail_server_id = $row_sel_mail_server['mail_server_id'];
			$email_host = $row_sel_mail_server['email_host'];
			$email_from = $row_sel_mail_server['email_from'];
			$email_bounce_back = $row_sel_mail_server['email_bounce_back'];
			$email_inbox = $row_sel_mail_server['email_inbox'];
			$email_pass = $row_sel_mail_server['email_pass'];
			$status = $row_sel_mail_server['status'];
			$userid = $row_sel_mail_server['userid'];
			$waktu = $row_sel_mail_server['waktu'];
			
			if(strtoupper($status)=='T'){
				$status = 'AKTIF';
			}else{
				$status = 'TIDAK AKTIF';
			}
			if($i%2==0){
				$cls = 'table_row_odd';
			}else{
				$cls = 'table_row_even';
			}
			?>
			<tr class="<?=$cls?>">
				<td><?=$email_host?></td>
				<td><?=$email_from?></td>
				<td><?=$email_bounce_back?></td>
                <td><?=$email_inbox?></td>
				<td><?=$email_pass?></td>
				<td><?=$userid?></td>
				<td align="center"><?=$waktu?></td>
				<td align="center"><?=$status?></td>
				<td align="center">
					<a onClick="edit_server(<?=$mail_server_id?>)"><img src="../images/icons/edit_data.png" border="0" /></a>&nbsp;
					<a onclick="delete_conf(<?=$mail_server_id?>)"><img src="../images/icons/del_data.png" border="0" /></a>
				</td>
			</tr>
			<?php
		}
		?>
        </table>
        <?php
	}
	
	function add_server(){
		?>
        <form name="form_add" id="form_add">
            <table width="525">
                <tr>
                    <td align="center" colspan="2" style="font-size:16px"><b>ADD MAIL SERVER</b></td>
                </tr>
                <tr>
                    <td height="20">&nbsp;</td>
                </tr>
                <tr>
                    <td width="30%" rowspan="2">Host</td>
                    <td height="30" valign="bottom"><input type="text" name="text_host" class="textbox_3" maxlength="20"></td>
                </tr>
                <tr>
                	<td height="30" valign="top" style="font-size:10px"><i>berisi ip address</i></td>
                </tr>
                <tr>
                    <td rowspan="2">From</td>
                    <td height="30" valign="bottom"><input type="text" name="text_from" class="textbox_4" maxlength="100"></td>
                </tr>
                <tr>
                 <td height="30" valign="top" style="font-size:10px"><i>berisi email pengirim</i></td>
                </tr>
                <tr>
                  <td rowspan="2">Inbox</td>
                	<td height="30" valign="bottom" style="font-size:10px"><input name="text_inbox" type="text" class="textbox_4" id="text_inbox" maxlength="200" /></td>
                </tr>
                <tr>
                  <td height="30" valign="top" style="font-size:10px"><i>berisi konfigurasi mail box</i></td>
                </tr>
                <tr>
                    <td rowspan="2">Password</td>
                    <td height="30" valign="bottom"><input type="text" name="text_password" class="textbox_3" maxlength="10"></td>
                </tr>
                <tr>
                	<td height="30" valign="top" style="font-size:10px"><i>berisi password mail server</i></td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td>AKTIF</td>
                </tr>
                <tr>
                    <td colspan="2" align="center"><input type="button" name="button_save" id="button_save" class="button" value="SAVE" onClick="save()"></td>
                </tr>
            </table>
</form>
        <?php
	}
	
	function save(){
		$email_host = $_POST['text_host'];
		$email_from = $_POST['text_from'];
		$email_inbox = $_POST['text_inbox'];
		$email_password = $_POST['text_password'];
		
		$sql_ins_mail_server = "INSERT INTO mail_server(
										email_host,
										email_from,
										email_inbox,
										email_pass,
										status,
										userid,
										waktu
									)VALUES(
										'$email_host',
										'$email_from',
										'$email_inbox',
										'$email_password',
										'T',
										'".$_SESSION['userid']."',
										now()
									)";
		$qry_sel_mail_server = pg_query($sql_ins_mail_server) or die('ERROR insert mail server: '.$sql_ins_mail_server);
		echo 'sukses';
	}
	
	function edit_server(){
		$mail_server_id = $_REQUEST['mail_server_id'];
		
		$sql_sel_mail_server = "SELECT * FROM mail_server WHERE mail_server_id = $mail_server_id";
		$qry_sel_mail_server = pg_query($sql_sel_mail_server) or die('ERROR select mail_server: '.$sql_sel_mail_server);
		$row = pg_fetch_assoc($qry_sel_mail_server);
		
		$email_host = $row['email_host'];
		$email_from = $row['email_from'];
		$email_inbox = $row['email_inbox'];
		$email_pass = $row['email_pass'];

		$status = $row['status'];
		$userid = $row['userid'];
		$waktu = $row['waktu'];
		?>
        <form name="form_edit" id="form_edit">
            <table width="525">
                <tr>
                    <td align="center" colspan="2" style="font-size:16px"><b>EDIT MAIL SERVER</b></td>
                </tr>
                <tr>
                    <td height="20">&nbsp;</td>
                </tr>
                <tr>
                    <td width="30%" rowspan="2">Host</td>
                    <td height="30" valign="bottom"><input type="text" name="text_host" class="textbox_3" maxlength="20" value="<?=$email_host?>"></td>
                </tr>
                <tr>
                	<td height="30" valign="top" style="font-size:10px"><i>berisi ip address</i></td>
                </tr>
                <tr>
                    <td rowspan="2">From</td>
                    <td height="30" valign="bottom"><input type="text" name="text_from" class="textbox_4" maxlength="100" value="<?=$email_from?>"></td>
                </tr>
                <tr>
                  <td height="30" valign="top" style="font-size:10px"><i>berisi email pengirim</i></td>
                </tr>
                <tr>
                  <td rowspan="2">Inbox</td>
                	<td height="30" valign="bottom" style="font-size:10px"><input name="text_inbox" type="text" class="textbox_4" id="text_mail_box" value="<?=$email_inbox?>" maxlength="200" /></td>
                </tr>
                <tr>
                  <td height="30" valign="top" style="font-size:10px"><i>berisi konfigurasi mail box</i></td>
                </tr>
                <tr>
                    <td rowspan="2">Password</td>
                    <td height="30" valign="bottom"><input type="text" name="text_password" class="textbox_3" maxlength="10" value="<?=$email_pass?>"></td>
                </tr>
                <tr>
                	<td height="30" valign="top" style="font-size:10px"><i>berisi password mail server</i></td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td height="30">
                    	<select id="combo_status" name="combo_status" class="combobox">
                        	<option value="T">AKTIF</option>
                            <option value="F">TIDAK AKTIF</option>
                        </select>
                </tr>
                <tr>
                    <td colspan="2" align="center"><input type="button" name="button_update" id="button_update" class="button" value="UPDATE" onClick="edit(<?=$mail_server_id?>)"></td>
                </tr>
            </table>
</form>
        <?php
	}
	
	function edit(){
		$mail_server_id = $_REQUEST['mail_server_id'];
		$email_host = $_POST['text_host'];
		$email_inbox = $_POST['text_inbox'];
		$email_from = $_POST['text_from'];
		$email_password = $_POST['text_password'];
		$status = $_POST['combo_status'];
		
		$sql_upd_mail_server = "UPDATE mail_server
								SET
									email_host = '$email_host',
									email_from = '$email_from',
									email_inbox = '$email_inbox',
									email_pass = '$email_password',
									status = '$status',
									userid = '".$_SESSION['userid']."',
									waktu = now()
								WHERE mail_server_id = $mail_server_id";
		$qry_upd_mailserver = pg_query($sql_upd_mail_server) or die('ERROR insert mail server: '.$sql_upd_mail_server);
		echo 'sukses';
	}
	
	function delete_server(){
		$mail_server_id = $_REQUEST['mail_server_id'];
		
		$sql_del_mail_server = "DELETE FROM mail_server WHERE mail_server_id = $mail_server_id";
		$qry_del_mail_server = pg_query($sql_del_mail_server) or die('ERROR delete mail_server: '.$sql_del_mail_server);
		echo 'sukses';
	}
	
	pg_close($con);
?>