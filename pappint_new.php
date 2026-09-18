<?php
	session_start();
	function redirecto($targetpage){
		$host==$_SERVER['HTTP_HOST'];
		$url==rtrim(dirname($_SERVER['PHP_SELF']),'/\\');
		if(headers_sent()){
			echo "<script type=\"text/javascript\">window.location.href='$targetpage';</script>";
		}else{
			header("Location:$targetpage");
		}
	}
	#echo $_GET['cid'];
	if(isset($_GET['cid'])){
		$id = $_GET['cid'];
	}elseif(isset($_GET['id'])){
		$id = $_GET['id'];
	}
	echo "Please Wait...";
	if(isset($id)){
		//pg_connect('dbname=integrator user=app password=app123 host=192.168.2.230 port=5432');
		pg_connect('host=localhost port=5432 dbname=integrator user=postgres password=Indomedia123');
		//pg_connect('host=192.168.101.14 port=5432 dbname=integrator user=postgres password=123456');
		
		//pg_connect('dbname=ADMIN user=app password=app123 host=192.168.2.230 port=5432');
		//pg_connect('dbname=integrator user=postgres password=123456 host=192.165.1.1 port=5432');
		
		$p_query = "select u.user_id, u.user_name, u.user_password from user1 u";
		$p_query .= " inner join user_aplikasi ua on u.user_id=ua.user_id";
		$p_query .= " inner join m_aplikasi ma on ua.aplikasi_id::integer=ma.aplikasi_id";
		$p_query .= " where upper(u.user_id)='".strtoupper(trim($_GET['user_id']))."' and user_password='".trim($_GET['pid'])."'";
		$p_query .= " order by u.user_id limit 1";
		
		/*$p_query = "select firstname,u.userlogin,password,namaapp,iduser from mstuser u";
		$p_query .= " inner join tlsuserakses t on u.kduser=t.kduser";
		$p_query .= " inner join mstapplication a on t.kdapplication=a.kdapplication";
		$p_query .= " where upper(u.userlogin)='".strtoupper(trim($_GET['user_id']))."' and password='".trim($_GET['pid'])."'";
		$p_query .= " order by u.userlogin limit 1";*/
		
		echo $p_query;
		$result = pg_query($p_query)or die($p_query);
		$hasil=pg_fetch_array($result);
		
		if(pg_num_rows($result)>0){
			//session_register('user_name');
			//session_register('id');
			//session_register('user_id');
			//session_register('userid');
			$user_id=trim($_GET['user_id']);																/*untuk id dari user*/
			$userid=trim($_GET['user_id']);																/*untuk id dari user*/
			//$user_name=trim($hasil['firstname']);			/*integrator pa ary (dbname = ADMIN)*/			/*untuk nama dari user*/
			$user_name=trim($hasil['user_name']);			/*integrator luqman (dbname = integrator)*/		/*untuk nama dari user*/
			$_SESSION['user_id']=$user_id;
			$_SESSION['userid']=$user_id;
			$_SESSION['user_name']=$user_name;
			//$cid=$_GET['cid'];
			$targetpage="home/index.php";
			#echo $targetpage;
			redirecto($targetpage);
		}
	}else{
		$targetpage="/".$id."/login/index.php";
		redirecto($targetpage);
	}
	#echo $targetpage;
?>