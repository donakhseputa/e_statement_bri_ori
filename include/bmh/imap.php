<?php
	
	$mailbox = "{202.51.252.22:110/pop3/tls/novalidate-cert}INBOX";
	//$mailbox_user = "app_dev@indocorp.com";
	//$mailbox_password = "123456";
	$mailbox_user = "danareksa@indocorp.com";
	$mailbox_password = "trial";
	

//$mbox = imap_open("{your.imap.host:143}", "username", "password");
$mbox = imap_open($mailbox, $mailbox_user, $mailbox_password) or die("can't connect: " . imap_last_error());

echo "<h1>Mailboxes</h1>\n";
$folders = imap_listmailbox($mbox, "$mailbox", "*");
$jumlah = imap_num_msg($mbox);

for ($i = 1; $i <= imap_num_msg($mbox); $i++) 
{
echo  "<br />\n";
echo "ke :" . $i ;
$header_info = imap_headerinfo($mbox, $i);
//echo "header :" . $header_info;

echo preg_replace('/<input type=hidden name=clientid id=clientid value=\"([^.*]+)\">/','$1',imap_body($mbox, $i)) . "<br />\n";

			$header_info = imap_headerinfo($mbox, $i);
			if (substr($header_info->senderaddress, 0, 13) == "MAILER-DAEMON") 
			{
				//$arr_message = explode("|abc|", imap_body($mbox, $i));
				
				$arr_bounce = explode(chr(13) . chr(10), imap_body($mbox, $i));
				$email_bounce_message = addslashes($arr_bounce[5]);
				echo " " .$header_info->senderaddress, 0, 13 . " " ;
				$email_bounce_message .= " " . addslashes($arr_bounce[6]);
				echo "  ".$email_bounce_message  ;
			
			}

}


/*
if ($folders == false) {
    echo "Call failed<br />\n";
} else {
    foreach ($folders as $val) {
        echo $val . "<br />\n";
    }
}
*/
echo "<h1>Headers in INBOX</h1>\n";
$headers = imap_headers($mbox);
//$body=imap_body($mbox,1);

if ($headers == false) {
    echo "Call failed<br />\n";
} else {
    foreach ($headers as $val) {
        echo $val . "<br />\n";
        
        
       // echo $body;
    }
}

imap_close($mbox);
?> 