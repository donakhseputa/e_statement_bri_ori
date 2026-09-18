<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/PHPMailer/class.phpmailer.php"); ?>
<?
	error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED | E_STRICT);
	//error_reporting(E_ALL);
	
	$act = $_REQUEST['act'];
	$con = pg_connect($connection) or die("Could not connect to database!");
	
	switch($act){
		case 'show_table': show_table(); break;
		case 'add_server': add_server(); break;
		case 'save': save(); break;
		case 'edit_server': edit_server(); break;
		case 'edit': edit(); break;
		case 'delete_server': delete_server(); break;
		case 'test_server': test_server(); break;
		case 'test_kirim': test_kirim_aws(); break;
		//case 'test_kirim': test_kirim(); break;
		case 'view_inbox': view_inbox(); break;
		
	}
	
	function show_table(){
		?>
        <table width="100%" cellpadding="0" cellspacing="0" border="1" >
            <tr class="table_header">
                <td colspan="2">EMAIL FROM (SMTP)</td>
                <td colspan="2">BOUNCE BACK EMAIL (Mailbox)</td>
                <td width="10%" rowspan="2"> Modified by</td>
                <td width="10%" rowspan="2">Time</td>
				<td width="10%" colspan="3">Test Email</td>
                <td width="5%" rowspan="2">Status</td>
                <td width="15%" rowspan="2">ACT</td>
            </tr>
            <tr class="table_header">
                <td width="10%"> Host</td>
                <td width="10%">From</td>
                <td width="10%"> Parameter</td>
                <td width="10%">Address</td>
                <!--td width="9%">Password</td-->
				<td width="5%">Kirim</td>
                <td width="5%">Inbox</td>
                 <td width="3%">View</td>
            </tr>
        <?php
		$sql_sel_mail_server = "SELECT * FROM mail_server ORDER BY mail_server_id";
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
				<td><?=$email_inbox?></td>
                <td align="center"><?=$email_bounce_back?></td>
				<!--td align="center"><?=$email_pass?></td-->
				<td align="center"><?=$userid?></td>
				<td align="center"><?=$waktu?></td>
				<td align="center"><a onclick="test_kirim(<?=$mail_server_id?>)">[K]</a></td>
				<td align="center"><a onclick="test_server(<?=$mail_server_id?>)">[I]</a></td>
                <td align="center"><a onclick="view_inbox(<?=$mail_server_id?>)">[V]</a></td>
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
                    <td height="14" valign="bottom"><input type="text" name="text_from" class="textbox_4" maxlength="100"></td>
                </tr>
                <tr>
                  <td height="14" valign="top" style="font-size:10px"><i>berisi email pengirim</i></td>
                </tr>
                <tr>
                  <td rowspan="2">Mailbox Address</td>
                 <td height="14" valign="top" style="font-size:10px"><input name="text_feedback" type="text" class="textbox_4" id="text_feedback" maxlength="100" /></td>
                </tr>
                <tr>
                  <td height="14" valign="top" style="font-size:10px"><i>berisialamat mail box</i></td>
                </tr>
                <tr>
                  <td rowspan="2">Mailbox Parameter</td>
                	<td height="30" valign="bottom" style="font-size:10px"><input name="text_inbox" type="text" class="textbox_4" id="text_inbox" maxlength="200" /></td>
                </tr>
                <tr>
                  <td height="30" valign="top" style="font-size:10px"><i>berisi parameter  mail box</i></td>
                </tr>
                <tr>
                    <td rowspan="2"> Mailbox Password</td>
                    <td height="30" valign="bottom"><input type="password" name="text_password" class="textbox_3" maxlength="30"></td>
                </tr>
                <tr>
                	<td height="30" valign="top" style="font-size:10px"><i>berisi password mail box</i></td>
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
		$feedback = $_POST['text_feedback'];
		
		$sql_ins_mail_server = "INSERT INTO mail_server(
										email_host,
										email_from,
										email_inbox,
										email_pass,
										status,
										email_bounce_back,
										userid,
										
										waktu
									)VALUES(
										'$email_host',
										'$email_from',
										'$email_inbox',
										'$email_password',
										'T',
										'$feedback',
										'".$_SESSION['userid']."',
										now()
									)";
		$qry_sel_mail_server = pg_query($sql_ins_mail_server) or die('ERROR insert mail server: '.$sql_ins_mail_server);
		echo 'sukses';
	}
	
	function test_server()
	{
		$mail_server_id = $_REQUEST['mail_server_id'];
		//die($mail_server_id);
		$sql_sel_mail_server = "SELECT * FROM mail_server WHERE mail_server_id = $mail_server_id";
		$qry_sel_mail_server = pg_query($sql_sel_mail_server) or die('ERROR select mail_server: '.$sql_sel_mail_server);
		$row = pg_fetch_assoc($qry_sel_mail_server);
		
		$email_host = $row['email_host'];
		$email_from = $row['email_from'];
		$email_inbox = $row['email_inbox'];
		$email_pass = $row['email_pass'];
        $feedback = $row['email_bounce_back'];
		//$email_host = 'mail.statement.net'; 
echo 'email_host = ' . $email_host .'<br>';
		 
		 
		 
		 $email_host = "{imap.mail.us-east-1.awsapps.com:993/imap/ssl}INBOX";
		// if($mbox = imap_open($email_inbox, $feedback, $email_pass))
		 if($mbox = imap_open($email_host, $email_from, $email_pass))
			 
		 {
		 $message_count = number_format(imap_num_msg($mbox));
		 echo 'Connection success to Mail Box :' . '<br>'.$email_inbox . '<br>'  .$feedback  . '<br>' . $message_count  ." email(s)";
		 }
		 else
		 {
			//echo 'email_host = ' . $email_host;

			 print_r  (imap_errors());
		 }
	}
	
	function view_inbox()
	{
		
		$mail_server_id = $_REQUEST['mail_server_id'];
		
		$sql_sel_mail_server = "SELECT * FROM mail_server WHERE mail_server_id = $mail_server_id";
		$qry_sel_mail_server = pg_query($sql_sel_mail_server) or die('ERROR select mail_server: '.$sql_sel_mail_server);
		$row = pg_fetch_assoc($qry_sel_mail_server);
		
		$email_host = $row['email_host'];
		$email_from = $row['email_from'];
		$email_inbox = $row['email_inbox']; 
		$email_pass = $row['email_pass']; 
        $feedback = $row['email_bounce_back']; 
		
		$email_host = "{imap.mail.us-east-1.awsapps.com:993/imap/ssl}INBOX";
		//$mbox = imap_open($email_inbox, $feedback, $email_pass) or die("can't connect: " . imap_last_error());
		$mbox = imap_open($email_host, $email_from, $email_pass) or die("can't connect: " . imap_last_error());
		$message_count = imap_num_msg($mbox);
	
	
		
		echo "<table border=\"1\" cellspacing=\"0\" width=\"80%\">";
		echo "<tr bgcolor=\"yellow\">
					 <td width=\"2%\" colspan=\"2\">No</td>
					 <td width=\"11%\" colspan=\"2\">From <br> tgl1 <br> subject</td>
					 <td width=\"11%\" colspan=\"2\">status</td>
					 <td width=\"7%\" colspan=\"2\">message_id </td>
					 <td width=\"14%\" colspan=\"2\">msgBody</td>
					 </tr>\n";
					 echo $message_count . "<br>";
		$awal = 1;
		$akhir = $message_count;
		echo "awal = " .$awal . "<br>";
		echo "akhir = " .$akhir . "<br>";
		
		for($i  = $awal; $i  <= $akhir; $i ++){
			
		$header_info = imap_headerinfo($mbox, $i );
			$dataTxt = get_part($mbox, $i , "TEXT/PLAIN");
				   
					// GET HTML BODY
					$dataHtml = get_part($mbox, $i , "TEXT/HTML");
				
					if ($dataHtml != "") {
						$dataHtml_lines = explode("\n", $dataHtml);
						unset($dataHtml);
						foreach ($dataHtml_lines as $x => $line) {
							$dataHtml .= $line . "\n";
						}
						$dataHtml = eregi_replace("<body[^>.]*>","<body>",$dataHtml);
						$msgBody = $dataHtml;
					}
					else {
						$dataTxt_lines = explode("\n", $dataTxt);
						unset($dataTxt);
						foreach ($dataTxt_lines as $x => $line) {
							$dataTxt .= htmlspecialchars($line) . "<br />\n";
						}
						$msgBody = $dataTxt;
					}
					$header = imap_header($mbox, $i ); 
					$mailbox = $header->from[0]->mailbox;
					
					$from = $header->from[0]->mailbox . "@" . $header->from[0]->host;
			
					$subject = $header_info->subject;
					
					if (($header_info->date) <> "")
					{$tgl1 = strtotime($header_info->date);
					$tgl1 = date("Y-m-d H:i",$tgl1);
					}
					$in_reply_to = $header_info->in_reply_to;
					$message_id = $header_info->message_id;
				
				if 
						(
						
						trim(strtoupper(substr($header_info->senderaddress, 0, 13)) == "MAILER-DAEMON") 
						or (strtoupper(substr($header_info->senderaddress, 0, 10)) == "POSTMASTER") 
						or (strtoupper(substr($header_info->senderaddress, 0, 20)) == "MAIL DELIVERY SYSTEM") 
						or (strtoupper(substr($header_info->senderaddress, 0, 18)) == "MICROSOFT EXCHANGE")
						
						)
				{
					$status = "GAGAL"; //$header_info->senderaddress;
					$status = $status ."<br>".$header_info->senderaddress;
					$arr_bounce = explode(chr(13) . chr(10), imap_body($mbox, $i));
					$email_bounce_message = addslashes($arr_bounce[5]);
					$email_bounce_message .= " " . addslashes($arr_bounce[6]);
					
					$slice = preg_replace('/[\=\"\']/','',imap_body($mbox, $i));
					$arr_message = explode("|",$slice);
					
					//AMBIL blth dari hidden input
					$email_bounce_message = preg_replace('/[\'\/]/','',$email_bounce_message);
					$client_feedback = $arr_message[1];
					$flagtrans = $arr_message[2];
					$tr_email_id = $arr_message[3];
					$email = $arr_message[4];
					
					if((int)$header_info->in_reply_to)
					{
					$message_id = "in_reply_to : ". $header_info->in_reply_to;	
					$msgBody = $msgBody;
					}
					else
					{
					$message_id = "tr_email_id : ". $tr_email_id;
					$msgBody = $email_bounce_message;
					
					}
					
					
				}
	
				
				else if 
					(
					  (strtolower(substr($header_info->subject, 0, 5)) == "read:"  )
				   or (strtolower(substr($header_info->subject, 0, 8)) == "checked:"  )	
				   or (strtolower(substr($header_info->subject, 0, 8)) == "dpriksa:"  )	
				   or (strtolower(substr($header_info->subject, 0, 8)) == "dpriksa "  )	
				   or (strtolower(substr($header_info->subject, 0, 7)) == "dibaca:"  )	
				   or (strtolower(substr($header_info->subject, 0, 7)) == "dibaca "  )	
				   or (strtolower(substr($header_info->subject, 0, 13)) == "sudah dibaca:"  )	
				   or (strtolower(substr($header_info->subject, 0, 13)) == "return receipt"  )
				   or (strtolower(substr($header_info->subject, 0, 3)) == "re:"  )
				   or (strtolower(substr($header_info->subject, 0, 4)) == "bls:"  )	
				  
					)
					
				{
					$status = "Dibaca";
					if((int)$header_info->in_reply_to)
					{
					$message_id = "in_reply_to : " .$header_info->in_reply_to;
					}
					elseif((int)$header_info->references)
					{
					$message_id = "references : " .$header_info->references;
					}
				}	
				
				else if(strtoupper(substr($header_info->subject, 0, 9)) == "IMG EMAIL")
				{
					$status = "Dibaca dgn IMG";
				}
					
				else
				{
				$status = "OTHERS";	
				}
					 
					echo "<tr bgcolor=\"$bgColor\">
					 <td width=\"2%\" colspan=\"2\">$i</td>
					 <td width=\"11%\" colspan=\"2\">$from <br> $tgl1 <br> $subject</td>
					 <td width=\"11%\" colspan=\"2\">$status</td>
					 <td width=\"7%\" colspan=\"2\">$message_id </td>
					 <td width=\"14%\" colspan=\"2\">$msgBody</td>
					 </tr>\n";
			 
		}
		
		
		
		
		
	
	imap_close($mbox);
		 
		 
	}
	
 function get_mime_type(&$structure) {
	   $primary_mime_type = array("TEXT", "MULTIPART","MESSAGE", "APPLICATION", "AUDIO","IMAGE", "VIDEO", "OTHER");
	   if($structure->subtype) {
		return $primary_mime_type[(int) $structure->type] . '/' .$structure->subtype;
	   }
		return "TEXT/PLAIN";
	   }
	   
		function get_part($stream, $msg_number, $mime_type, $structure = false, $part_number = false) {
	   
		if (!$structure) {
	
			$structure = imap_fetchstructure($stream, $msg_number);
	
		}
	
		if ($structure) {
	
			if($mime_type == get_mime_type($structure)) {
				if(!$part_number) {
					$part_number = "1";
				   }
				   $text = imap_fetchbody($stream, $msg_number, $part_number);
				   if($structure->encoding == 3) {
					   return imap_base64($text);
				   } else if($structure->encoding == 4) {
					   return imap_qprint($text);
				   } else {
					   return $text;
				   }
			   }
	   
			if($structure->type == 1) /* multipart */ {
				   while(list($index, $sub_structure) = each($structure->parts)) {
					   if($part_number) {
						   $prefix = $part_number . '.';
					   }
					   $data = get_part($stream, $msg_number, $mime_type, $sub_structure, $prefix . ($index + 1));
					   if($data) {
						   return $data;
					   }
				   } // end while
			   } // end multipart
		   } // end structure
		   return false;
	}  

	function test_kirim()
	{
		$mail_server_id = $_REQUEST['mail_server_id'];
		
		$sql_sel_mail_server = "SELECT * FROM mail_server WHERE mail_server_id = $mail_server_id";
		$qry_sel_mail_server = pg_query($sql_sel_mail_server) or die('ERROR select mail_server: '.$sql_sel_mail_server);
		$rowmail = pg_fetch_assoc($qry_sel_mail_server);
		
		$email_from	=$rowmail['email_from'];
		$email_host	=$rowmail['email_host'];
		//$email_tujuan = 'bowor@indointernal.com';
		
		$mail = new PHPMailer();
		$mail->IsSMTP();
					//tambahan autentifikasi 20150220
			$mail->SMTPDebug = 2;  // untuk memunculkan pesan error /debug di layar
			$mail->SMTPAuth = true;  // authentifikasi smtp enable atau disable
			$mail->Username = $rowmail['email_from']; //username email
			$mail->Password =  $rowmail['email_pass']; //password email
			//tambahan autentifikasi 20150220
			
		
		$mail->From = $email_from;
		$mail->FromName = 'E-statement';
		$mail->Host = $email_host;
		$mail->Mailer   = "smtp";
		$mail->Subject  = 'Test E-statement Dari CYber';
		#$mail->Body  = 'Test E-statement Dari CYber';
		$mail->MsgHTML("<html>".'<b>test</b>'."</html>");
		$mail->IsHTML(true);
		//$mail->AddAddress('bowor@indointernal.com', 'APPDEV');
		$mail->AddAddress('ezz.chocolate.lg@gmail.com', 'APPDEV');
		//$mail->AddAddress('ezz_chocolate@yahoo.com', 'APPDEV');
		
		$mail->AddAddress($email_from, 'APPDEV');
				
		if($mail->Send()){	
			echo '<br><br>Success send to : <br>'. $email_from;
		}else{
			echo '<br><br>Failed send to : <br>'. $email_from;
		}
		
		$mail->ClearAddresses();
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
        $feedback = $row['email_bounce_back'];
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
                    <td height="20" valign="bottom"><input type="text" name="text_host" class="textbox_3" maxlength="20" value="<?=$email_host?>"></td>
                </tr>
                <tr>
                	<td height="30" valign="top" style="font-size:10px"><i>berisi ip address SMTP</i></td>
                </tr>
                <tr>
                    <td rowspan="2">From</td>
                    <td height="20" valign="bottom"><input type="text" name="text_from" class="textbox_4" maxlength="100" value="<?=$email_from?>"></td>
                </tr>
                <tr>
                  <td height="30" valign="top" style="font-size:10px"><i>berisi email pengirim</i></td>
                </tr>
                <tr>
                  <td rowspan="2">Mailbox Address</td>
                  <td height="20" valign="top" style="font-size:10px"><input name="text_feedback" type="text" class="textbox_4" id="text_feedback" value="<?=$feedback?>" maxlength="100" /></td>
                </tr>
                <tr>
                  <td height="30" valign="top" style="font-size:10px"><i>berisi  alamat mail box</i></td>
                </tr>
                <tr>
                  <td rowspan="2">Mailbox Parameter</td>
               	  <td height="20" valign="bottom" style="font-size:10px"><input name="text_inbox" type="text" class="textbox_4" id="text_mail_box" value="<?=$email_inbox?>" maxlength="200" /></td>
                </tr>
                <tr>
                  <td height="30" valign="top" style="font-size:10px"><i>berisi parameter mail box</i></td>
                </tr>
                <tr>
                    <td rowspan="2">Mailbox Password</td>
                    <td height="20" valign="bottom"><input type="password" name="text_password" class="textbox_3" maxlength="30" value="<?=$email_pass?>"></td>
                </tr>
                <tr>
               	  <td height="30" valign="top" style="font-size:10px"><i>berisi password mail box</i></td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td height="20">
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
		$feedback = $_POST['text_feedback'];
		
		$sql_upd_mail_server = "UPDATE mail_server
								SET
									email_host = '$email_host',
									email_from = '$email_from',
									email_inbox = '$email_inbox',
									email_pass = '$email_password',
									email_bounce_back = '$feedback',
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
	
	
	
	function test_kirim_aws()
	{
		$mail_server_id = $_REQUEST['mail_server_id'];
		
		$sql_sel_mail_server = "SELECT * FROM mail_server WHERE mail_server_id = $mail_server_id";
		$qry_sel_mail_server = pg_query($sql_sel_mail_server) or die('ERROR select mail_server: '.$sql_sel_mail_server);
		$rowmail = pg_fetch_assoc($qry_sel_mail_server);
		
		$email_from	= $rowmail['email_from'];
		//$email_host	=$rowmail['email_host'];
		//$email_host	= 'email-smtp.ap-south-1.amazonaws.com';
		$email_host	= 'email-smtp.ap-southeast-1.amazonaws.com';
		//$email_tujuan = 'bowor@indointernal.com';
		
		// set, comment or remove the next line.
		$configurationSet = 'ConfigSet';
		
		/*
		
		// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// If necessary, modify the path in the require statement below to refer to the
// location of your Composer autoload.php file.
require 'vendor/autoload.php';

// Replace sender@example.com with your "From" address.
// This address must be verified with Amazon SES.
$sender = 'sender@example.com';
$senderName = 'Sender Name';

// Replace recipient@example.com with a "To" address. If your account
// is still in the sandbox, this address must be verified.
$recipient = 'recipient@example.com';

// Replace smtp_username with your Amazon SES SMTP user name.
$usernameSmtp = 'smtp_username';

// Replace smtp_password with your Amazon SES SMTP password.
$passwordSmtp = 'smtp_password';

// Specify a configuration set. If you do not want to use a configuration
// set, comment or remove the next line.
$configurationSet = 'ConfigSet';

// If you're using Amazon SES in a region other than US West (Oregon),
// replace email-smtp.us-west-2.amazonaws.com with the Amazon SES SMTP
// endpoint in the appropriate region.
$host = 'email-smtp.us-west-2.amazonaws.com';
$port = 587;

// The subject line of the email
$subject = 'Amazon SES test (SMTP interface accessed using PHP)';

// The plain-text body of the email
$bodyText =  "Email Test\r\nThis email was sent through the
    Amazon SES SMTP interface using the PHPMailer class.";

// The HTML-formatted body of the email
$bodyHtml = '<h1>Email Test</h1>
    <p>This email was sent through the
    <a href="https://aws.amazon.com/ses">Amazon SES</a> SMTP
    interface using the <a href="https://github.com/PHPMailer/PHPMailer">
    PHPMailer</a> class.</p>';

$mail = new PHPMailer(true);

try {
    // Specify the SMTP settings.
    $mail->isSMTP();
    $mail->setFrom($sender, $senderName);
    $mail->Username   = $usernameSmtp;
    $mail->Password   = $passwordSmtp;
    $mail->Host       = $host;
    $mail->Port       = $port;
    $mail->SMTPAuth   = true;
    $mail->SMTPSecure = 'tls';
    $mail->addCustomHeader('X-SES-CONFIGURATION-SET', $configurationSet);

    // Specify the message recipients.
    $mail->addAddress($recipient);
    // You can also add CC, BCC, and additional To recipients here.

    // Specify the content of the message.
    $mail->isHTML(true);
    $mail->Subject    = $subject;
    $mail->Body       = $bodyHtml;
    $mail->AltBody    = $bodyText;
    $mail->Send();
    echo "Email sent!" , PHP_EOL;
} catch (phpmailerException $e) {
    echo "An error occurred. {$e->errorMessage()}", PHP_EOL; //Catch errors from PHPMailer.
} catch (Exception $e) {
    echo "Email not sent. {$mail->ErrorInfo}", PHP_EOL; //Catch errors from Amazon SES.
}



		*/
		$mail = new PHPMailer();
		$mail->IsSMTP();
					//tambahan autentifikasi 20150220
			$mail->SMTPDebug = 2;  // untuk memunculkan pesan error /debug di layar
			$mail->SMTPAuth = true;  // authentifikasi smtp enable atau disable
			//$mail->Username = $rowmail['email_from']; //username email
			//$mail->Password =  $rowmail['email_pass']; //password email
			
			//$mail->Username = 'AKIAZ3UZB3BUCKYJPTNH'; //username email
			//$mail->Password =  'BOxeMuMuk1/D9aahsd5QpNflbTQW3OJFw89mM3t3ZXjD'; //password email
			
			$mail->Username = 'AKIA54LF2PUGPZAKDSNA'; //username email
			$mail->Password =  'BAb/R7IhocFuwv74iVHkLUYUKX1eEs852NPudN7mJ+3h'; //password email
			
			$mail->SMTPSecure = 'tls';
			$mail->Port       = 587;
			//$mail->addCustomHeader('X-SES-CONFIGURATION-SET', $configurationSet);
			//tambahan autentifikasi 20150220
			
			//$mail->SMTPSecure = 'notls'; // secure transfer enabled REQUIRED for GMail

		$mail->From = $email_from;
		//$mail->From = 'emailtest@promoblast.net';
		$mail->FromName = 'TEST AWS 1 2.206 bri';
		$mail->Host = $email_host;
		$mail->Mailer   = "smtp";
		$mail->Subject  = 'TEST SUBJECT ';
		//$mail->AddAddress('bowor@indointernal.com', 'APPDEV');
		//$mail->AddAddress($email_from, 'APPDEV');
		
		$mail->AddAddress('ezz_chocolate@yahoo.com', 'APPDEV');
		//$mail->AddAddress('ezz_chocolate32434535353535353@yahoo.com', 'APPDEV');
		
		$mail->MsgHTML('<!doctype html>
<html>
  <head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Simple Transactional Email</title>
    <style>
     
      
      img {
        border: none;
        -ms-interpolation-mode: bicubic;
        max-width: 100%; 
      }

      body {
        background-color: #f6f6f6;
        font-family: sans-serif;
        -webkit-font-smoothing: antialiased;
        font-size: 14px;
        line-height: 1.4;
        margin: 0;
        padding: 0;
        -ms-text-size-adjust: 100%;
        -webkit-text-size-adjust: 100%; 
      }

      table {
        border-collapse: separate;
        mso-table-lspace: 0pt;
        mso-table-rspace: 0pt;
        width: 100%; }
        table td {
          font-family: sans-serif;
          font-size: 14px;
          vertical-align: top; 
      }

      /* -------------------------------------
          BODY & CONTAINER
      ------------------------------------- */

      .body {
        background-color: #f6f6f6;
        width: 100%; 
      }

      /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
      .container {
        display: block;
        margin: 0 auto !important;
        /* makes it centered */
        max-width: 580px;
        padding: 10px;
        width: 580px; 
      }

      /* This should also be a block element, so that it will fill 100% of the .container */
      .content {
        box-sizing: border-box;
        display: block;
        margin: 0 auto;
        max-width: 580px;
        padding: 10px; 
      }

   
      .main {
        background: #ffffff;
        border-radius: 3px;
        width: 100%; 
      }

      .wrapper {
        box-sizing: border-box;
        padding: 20px; 
      }

      .content-block {
        padding-bottom: 10px;
        padding-top: 10px;
      }

      .footer {
        clear: both;
        margin-top: 10px;
        text-align: center;
        width: 100%; 
      }
        .footer td,
        .footer p,
        .footer span,
        .footer a {
          color: #999999;
          font-size: 12px;
          text-align: center; 
      }

    
      h1,
      h2,
      h3,
      h4 {
        color: #000000;
        font-family: sans-serif;
        font-weight: 400;
        line-height: 1.4;
        margin: 0;
        margin-bottom: 30px; 
      }

      h1 {
        font-size: 35px;
        font-weight: 300;
        text-align: center;
        text-transform: capitalize; 
      }

      p,
      ul,
      ol {
        font-family: sans-serif;
        font-size: 14px;
        font-weight: normal;
        margin: 0;
        margin-bottom: 15px; 
      }
        p li,
        ul li,
        ol li {
          list-style-position: inside;
          margin-left: 5px; 
      }

      a {
        color: #3498db;
        text-decoration: underline; 
      }

      /* -------------------------------------
          BUTTONS
      ------------------------------------- */
      .btn {
        box-sizing: border-box;
        width: 100%; }
        .btn > tbody > tr > td {
          padding-bottom: 15px; }
        .btn table {
          width: auto; 
      }
        .btn table td {
          background-color: #ffffff;
          border-radius: 5px;
          text-align: center; 
      }
        .btn a {
          background-color: #ffffff;
          border: solid 1px #3498db;
          border-radius: 5px;
          box-sizing: border-box;
          color: #3498db;
          cursor: pointer;
          display: inline-block;
          font-size: 14px;
          font-weight: bold;
          margin: 0;
          padding: 12px 25px;
          text-decoration: none;
          text-transform: capitalize; 
      }

      .btn-primary table td {
        background-color: #3498db; 
      }

      .btn-primary a {
        background-color: #3498db;
        border-color: #3498db;
        color: #ffffff; 
      }

      /* -------------------------------------
          OTHER STYLES THAT MIGHT BE USEFUL
      ------------------------------------- */
      .last {
        margin-bottom: 0; 
      }

      .first {
        margin-top: 0; 
      }

      .align-center {
        text-align: center; 
      }

      .align-right {
        text-align: right; 
      }

      .align-left {
        text-align: left; 
      }

      .clear {
        clear: both; 
      }

      .mt0 {
        margin-top: 0; 
      }

      .mb0 {
        margin-bottom: 0; 
      }

      .preheader {
        color: transparent;
        display: none;
        height: 0;
        max-height: 0;
        max-width: 0;
        opacity: 0;
        overflow: hidden;
        mso-hide: all;
        visibility: hidden;
        width: 0; 
      }

      .powered-by a {
        text-decoration: none; 
      }

      hr {
        border: 0;
        border-bottom: 1px solid #f6f6f6;
        margin: 20px 0; 
      }

      /* -------------------------------------
          RESPONSIVE AND MOBILE FRIENDLY STYLES
      ------------------------------------- */
      @media only screen and (max-width: 620px) {
        table[class=body] h1 {
          font-size: 28px !important;
          margin-bottom: 10px !important; 
        }
        table[class=body] p,
        table[class=body] ul,
        table[class=body] ol,
        table[class=body] td,
        table[class=body] span,
        table[class=body] a {
          font-size: 16px !important; 
        }
        table[class=body] .wrapper,
        table[class=body] .article {
          padding: 10px !important; 
        }
        table[class=body] .content {
          padding: 0 !important; 
        }
        table[class=body] .container {
          padding: 0 !important;
          width: 100% !important; 
        }
        table[class=body] .main {
          border-left-width: 0 !important;
          border-radius: 0 !important;
          border-right-width: 0 !important; 
        }
        table[class=body] .btn table {
          width: 100% !important; 
        }
        table[class=body] .btn a {
          width: 100% !important; 
        }
        table[class=body] .img-responsive {
          height: auto !important;
          max-width: 100% !important;
          width: auto !important; 
        }
      }

      /* -------------------------------------
          PRESERVE THESE STYLES IN THE HEAD
      ------------------------------------- */
      @media all {
        .ExternalClass {
          width: 100%; 
        }
        .ExternalClass,
        .ExternalClass p,
        .ExternalClass span,
        .ExternalClass font,
        .ExternalClass td,
        .ExternalClass div {
          line-height: 100%; 
        }
        .apple-link a {
          color: inherit !important;
          font-family: inherit !important;
          font-size: inherit !important;
          font-weight: inherit !important;
          line-height: inherit !important;
          text-decoration: none !important; 
        }
        #MessageViewBody a {
          color: inherit;
          text-decoration: none;
          font-size: inherit;
          font-family: inherit;
          font-weight: inherit;
          line-height: inherit;
        }
        .btn-primary table td:hover {
          background-color: #34495e !important; 
        }
        .btn-primary a:hover {
          background-color: #34495e !important;
          border-color: #34495e !important; 
        } 
      }

    </style>
  </head>
  <body class="">
    <span class="preheader">This is preheader text. Some clients will show this text as a preview.</span>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td>&nbsp;</td>
        <td class="container">
          <div class="content">

            <!-- START CENTERED WHITE CONTAINER -->
            <table role="presentation" class="main">

              <!-- START MAIN CONTENT AREA -->
              <tr>
                <td class="wrapper">
                  <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                      <td>
                        <p>Hi there,</p>
                        <p>Sometimes you just want to send a simple HTML email with a simple design and clear call to action. This is it.</p>
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                          <tbody>
                            <tr>
                              <td align="left">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                  <tbody>
                                    <tr>
                                      <td> <a href="http://htmlemail.io" target="_blank">Call To Action</a> </td>
                                    </tr>
                                  </tbody>
                                </table>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                        <p>This is a really simple email template. Its sole purpose is to get the recipient to click the button with no distractions.</p>
                        <p>Good luck! Hope it works.</p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>

            <!-- END MAIN CONTENT AREA -->
            </table>
            <!-- END CENTERED WHITE CONTAINER -->

            <!-- START FOOTER -->
            <div class="footer">
              <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td class="content-block">
                    <span class="apple-link">Company Inc, 3 Abbey Road, San Francisco CA 94102</span>
                    <br> Dont like these emails? <a href="http://i.imgur.com/CScmqnj.gif">Unsubscribe</a>.
                  </td>
                </tr>
                <tr>
                  <td class="content-block powered-by">
                    Powered by <a href="http://htmlemail.io">HTMLemail</a>.
                  </td>
                </tr>
              </table>
            </div>
            <!-- END FOOTER -->

          </div>
        </td>
        <td>&nbsp;</td>
      </tr>
    </table>
  </body>
</html>
');
		$mail->IsHTML(true);
					
					
				
		if($mail->Send()){	
			echo '<br><br>Success send to : <br>'. $email_from;
		}else{
			echo '<br><br>Failed send to : <br>'. $email_from;
		}
		
		$mail->ClearAddresses();
	}
	pg_close($con);
?>