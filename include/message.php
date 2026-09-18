<? if ($_SESSION[$pesan] != "") { ?>
<script type="text/javascript">
	alert("<?= $_SESSION[$pesan] ?>");
</script>
<? } ?>
<?
	$_SESSION[$pesan] = "";
?>
