<?php
$mailbox = "{202.51.252.22:110/pop3/tls/novalidate-cert}INBOX";
	$mailbox_user = "danareksa@indocorp.com";
	$mailbox_password = "trial";
$mbox = imap_open($mailbox, $mailbox_user, $mailbox_password) or die("can't connect: " . imap_last_error());

    // $imap = imap_open("{my.server.com:143}INBOX", "user", "pass");
     $n_msgs = imap_num_msg($mbox);
     $s = microtime(true);
     for ($i=0; $i<$n_msgs; $i++) {
          $header = imap_header($mbox, $i);
     }
     $e = microtime(true);
     echo ($e - $s);
     imap_close($mbox);
?>