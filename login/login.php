<?php
	require_once("../include/config.php"); 
	$user_login = trim($_POST["user_login"]);
	$pass_login = trim($_POST["pass_login"]);
	$msg = "";

	$con = pg_connect($connection) or die("Could not connect to database!");

	$sql = "SELECT *";
	$sql .= " FROM user1";
	$sql .= " WHERE upper(userid) = upper('$user_login')";
	$qry = pg_query($con, $sql) or die("Invalid query::".$sql);

	if ($row = pg_fetch_assoc($qry)){
			$userid = $row["userid"];
			$username = $row["nama"];
	}else{
		$msg = "Maaf, User ID atau password Anda salah!";
	}
	
	pg_close($con);

	if ($msg == ""){
		//session_register("userid");
		//session_register("user_id");
		//session_register("username");
		//session_register($pesan);

		$_SESSION["userid"] = $userid;
		$_SESSION["user_id"] = $userid;
		$_SESSION["username"] = $username;

		$header_location = "../home/";
	}else{
		//session_register($pesan);
		$_SESSION[$pesan] = $msg;

		$header_location = "index.php";
	}

	header("Location: $header_location");
?>
