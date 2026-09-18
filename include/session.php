<?
	if(strtoupper($_SESSION["user_id"]) != "APPDEV"){
		if ($_SESSION["user_id"] == "" || $_SERVER["HTTP_REFERER"] == "") {
			//header("Location: http://192.168.2.148/menus/?go=docman");		
			//header("Location: http://www.indointernal.com");		
			header("Location: http://www.indointernal.com/integrator/");
			//header("Location: http://192.165.1.1/integrator/");
			//header("Location: ../../index.php");
		}
	}else{
		if ($_SESSION["user_id"] == "") {
			//header("Location: http://192.168.2.148/menus/?go=docman");			
			//header("Location: http://www.indointernal.com");
			header("Location: http://www.indointernal.com/integrator/");
			//header("Location: http://192.165.1.1/integrator/");
			//header("Location: ../../index.php");
		}
	}
?>