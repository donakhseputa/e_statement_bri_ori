    <?php
    require_once("../include/config.php");
	
	
	function range_2_tanggal($tanggal_1, $tanggal_2)
	{
					
			// Declare and define two dates 
			$date1 = strtotime("$tanggal_1");  
			$date2 = strtotime("$tanggal_2");  
			  
			// Formulate the Difference between two dates 
			$diff = abs($date2 - $date1);  
			  
			  
			// To get the year divide the resultant date into 
			// total seconds in a year (365*60*60*24) 
			$years = floor($diff / (365*60*60*24));  
			  
			  
			// To get the month, subtract it with years and 
			// divide the resultant date into 
			// total seconds in a month (30*60*60*24) 
			$months = floor(($diff - $years * 365*60*60*24) 
										   / (30*60*60*24));  
			  
			  
			// To get the day, subtract it with years and  
			// months and divide the resultant date into 
			// total seconds in a days (60*60*24) 
			$days = floor(($diff - $years * 365*60*60*24 -  
						 $months*30*60*60*24)/ (60*60*24)); 
			  
			  
			// To get the hour, subtract it with years,  
			// months & seconds and divide the resultant 
			// date into total seconds in a hours (60*60) 
			$hours = floor(($diff - $years * 365*60*60*24  
				   - $months*30*60*60*24 - $days*60*60*24) 
											   / (60*60));  
			  
			  
			// To get the minutes, subtract it with years, 
			// months, seconds and hours and divide the  
			// resultant date into total seconds i.e. 60 
			$minutes = floor(($diff - $years * 365*60*60*24  
					 - $months*30*60*60*24 - $days*60*60*24  
									  - $hours*60*60)/ 60);  
			  
			  
			// To get the minutes, subtract it with years, 
			// months, seconds, hours and minutes  
			$seconds = floor(($diff - $years * 365*60*60*24  
					 - $months*30*60*60*24 - $days*60*60*24 
							- $hours*60*60 - $minutes*60));  
			  
			// Print the result 
			return "$days hari, $hours jam, $minutes mnt, $seconds dtk";
			#printf("%d years, %d months, %d days, %d hours, ". "%d minutes, %d seconds", $years, $months, $days, $hours, $minutes, $seconds);  
	}
	
	
	$total_email = $_REQUEST['total_email'];
	$flagtrans = $_REQUEST['pr'];
	$blth = $_REQUEST['blth'];
	$tabel_tr_email = 'tr_email_'.$blth.'_'.$flagtrans;
	
	
	$nama_cycle = $_REQUEST['cycle']; 
	$lama = $_REQUEST['hours']; 
	$m_loading_id = $_REQUEST['m_loading_id']; 
	
	$start = $_REQUEST['start']; 
	$end = $_REQUEST['end']; 
	 
	
	 
	$con = pg_connect($connection) or die("Could not connect to database!");
	$sql="select to_char(date_email_send,'YYYY-MM-DD-HH24-MI') as x, COUNT(*) as jumlah   FROM $tabel_tr_email 
		WHERE 
		loading_id = $m_loading_id
		and status_sample = 'f' 
			GROUp BY to_char(date_email_send,'YYYY-MM-DD-HH24-MI') 
			ORDER By 
			to_char(date_email_send,'YYYY-MM-DD-HH24-MI') ASC  ";
	//die($sql);
	$qry_data = pg_query($sql) or die('ERROR select data: '.$sql);
	
	 $dataPoints = array();
	$i=0;
	while($row_data = pg_fetch_array($qry_data)){
		array_push($dataPoints, array("label" => $row_data['x'], "y" =>$row_data['jumlah']));
		//array_push($dataPoints, array("x" => new Date(2010, 0, $i), "y" =>$row_data['jumlah']));
		if($i == 0)
		{
			$start = $row_data['x'];
		}else{
			$end = $row_data['x'];
		}
		$i++;
	}
	pg_close($con);
	$hours = range_2_tanggal( $start, $end);
	
  /*
    $y = 40;
    for($i = 0; $i < 1000; $i++){
    	$y += rand(0, 10) - 5; 
    	array_push($dataPoints, array("x" => $i, "y" => $y));
    }
     */
	 
	//die(print_r($dataPoints));
    ?>
    <!DOCTYPE HTML>
    <html>
    <head> 
    <script>
    window.onload = function () {
     
    var chart = new CanvasJS.Chart("chartContainer", {
    	theme: "light2", // "light1", "light2", "dark1", "dark2"
    	animationEnabled: true,
    	zoomEnabled: true,
    	title: {
    		text: "Nama Cycle: <?php echo $nama_cycle.' ('.$hours.') Total email: '.$total_email;?>"
    	},
    	data: [{
    		type: "area",     
    		dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
			}]
	});
    chart.render();
     
    }
    </script>
    </head>
    <body>
    <div id="chartContainer" style="height: 370px; width: 100%;"></div>
    <script src="canvasjs.min.js"></script>
    </body>
    </html>                              