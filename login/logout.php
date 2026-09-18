<? require_once("../include/config.php"); ?>
<?
	//session_unregister("userid");
	//session_unregister("username");
	//session_unregister($pesan);
    session_destroy();
	header("Location: ../include/session.php");
?>
