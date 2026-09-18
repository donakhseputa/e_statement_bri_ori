<?php
	

$timezone="Asia/Jakarta";
date_default_timezone_set($timezone);
include $www_location."include/config.php";
$_SESSION['userid']='otomatis';


$con = pg_connect($connection) or die("Could not connect to database!");	
	
$x = array();
	
$sql="
select * from m_loading 
--Where (flagtrans='MT' OR flagtrans='BC') 
order by m_loading_id DESC LIMIT 50
";

$query = pg_query($sql) or die("Invalid query! FSRV 1" . $sql) ;
			$rows = pg_num_rows($query);
		
		if ($rows > 0 ){
			while($row = pg_fetch_array($query))
			{		
				$tabeldetail = "detail_".$row['blth'].'_'.strtolower($row['flagtrans']);
				$tabel_tr_email = "tr_email_".$row['blth'].'_'.strtolower($row['flagtrans']);
				
				
				if ( $row['flagtrans'] == 'MT')
				{
					
					$n = count_pdf_transfer_to_fsrv( $row['m_loading_id'], $tabeldetail);
					update_m_loading('n_fsrv_done',$row['m_loading_id'],$n, 'integer');
					
					$start = get_fsrv_start( $row['m_loading_id'], $tabeldetail);
					update_m_loading('fsrv_start',$row['m_loading_id'],$start, 'text');
					
					$end = get_fsrv_end( $row['m_loading_id'], $tabeldetail); 
					update_m_loading('fsrv_end',$row['m_loading_id'],$end, 'text');
					
					$range = range_2_tanggal( $end, $start);
					update_m_loading('lama_transfer_fsrv',$row['m_loading_id'],$range, 'text');
					
					
				}
				
				if ( $row['flagtrans'] != 'MT')
				{
					$n2 = count_sisa_email_diantrian( $row['m_loading_id'], 'antrian_email');
					update_m_loading('n_sisa_diantrian',$row['m_loading_id'],$n2, 'integer', true);
					
					$start2 = get_blast_start( $row['m_loading_id'], $tabel_tr_email);
					update_m_loading('blast_start',$row['m_loading_id'],$start2, 'text');
					
					$end2 = get_blast_end( $row['m_loading_id'], $tabel_tr_email);
					update_m_loading('blast_end',$row['m_loading_id'],$end2, 'text');
					
					$range2 = range_2_tanggal( $end2, $start2);
					update_m_loading('lama_blast_estat',$row['m_loading_id'],$range2, 'text');
					
					
			
			
				}
				
				update_m_loading('last_updated',$row['m_loading_id'],'now()', 'date');
			}
		}
		
		echo "total data $rows telah diupdate;";
		
	
	
	function get_blast_end($m_loading_id, $tabeldetail)
	{
		
		$total = 'NULL';
		$sql="
		select to_timestamp( max(date_email_send)::text, 'YYYY-MM-DD hh24:mi')::timestamp without time zone 
		as total  from $tabeldetail WHERE loading_id=$m_loading_id AND status_sample='f'
		";

		$query = pg_query($sql) or die("Invalid query! FSRV 1" . $sql) ;
			$rows = pg_num_rows($query);
		
		if ($rows > 0 ){
			while($row = pg_fetch_array($query))
			{		
		
				$total =  $row['total'];
			}
		}	
		return $total;
		
	}
	
	function get_blast_start($m_loading_id, $tabeldetail)
	{
		
		$total = 'NULL';
		$sql="
		select to_timestamp( min(date_email_send)::text, 'YYYY-MM-DD hh24:mi')::timestamp without time zone 
		as total  from $tabeldetail WHERE loading_id=$m_loading_id AND status_sample='f'
		";

		$query = pg_query($sql) or die("Invalid query! FSRV 2" . $sql) ;
			$rows = pg_num_rows($query);
		
		if ($rows > 0 ){
			while($row = pg_fetch_array($query))
			{		
		
				$total =  $row['total'];
			}
		}	
		return $total;
		
	}
	
	
	
	function count_sisa_email_diantrian($m_loading_id, $tabeldetail)
	{
		
		$total = 0;
		$sql="
		select count(*) as total from $tabeldetail WHERE error_info IS NULL AND loading_id = $m_loading_id AND kode_kirim != 99
		 AND status_sample='f'";

		$query = pg_query($sql) or die("Invalid query! FSRV 3" . $sql) ;
			$rows = pg_num_rows($query);
		
		if ($rows > 0 ){
			while($row = pg_fetch_array($query))
			{		
		
				$total =  $row['total'];
			}
		}	
		return $total;
		
	}
	
	
	
	
	
	
	function range_2_tanggal($tanggal_1, $tanggal_2)
	{
			
			//die("$tanggal_1 -- $tanggal_2");
			
			
			// Declare and define two dates 
			$date1 = strtotime("$tanggal_1");  
			$date2 = strtotime("$tanggal_2");  
			//die("$date1 -- $date2");
			  
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
	
	
	function get_fsrv_end($m_loading_id, $tabeldetail)
	{
		
		$total = 'NULL';
		$sql="
		select to_timestamp( max(barcode), 'YYYY-MM-DD hh24:mi')::timestamp without time zone 
		as total  from $tabeldetail WHERE barcode IS NOT NULL AND m_loading_id=$m_loading_id
		";

		$query = pg_query($sql) or die("Invalid query! FSRV 4" . $sql) ;
			$rows = pg_num_rows($query);
		
		if ($rows > 0 ){
			while($row = pg_fetch_array($query))
			{		
		
				$total =  $row['total'];
			}
		}	
		return $total;
		
	}
	
	function get_fsrv_start($m_loading_id, $tabeldetail)
	{
		
		$total = 'NULL';
		$sql="
		select to_timestamp( min(barcode), 'YYYY-MM-DD hh24:mi')::timestamp without time zone 
		as total  from $tabeldetail WHERE barcode IS NOT NULL AND m_loading_id=$m_loading_id
		";

		$query = pg_query($sql) or die("Invalid query! FSRV 5" . $sql) ;
			$rows = pg_num_rows($query);
		
		if ($rows > 0 ){
			while($row = pg_fetch_array($query))
			{		
		
				$total =  $row['total'];
			}
		}	
		return $total;
		
	}
	
	
	
	
	
	function count_pdf_transfer_to_fsrv($m_loading_id, $tabeldetail)
	{
		
		$total = 0;
		$sql="
		select count(*) as total from $tabeldetail WHERE barcode IS NOT NULL AND m_loading_id=$m_loading_id
		";

		$query = pg_query($sql) or die("Invalid query! FSRV 6" . $sql) ;
			$rows = pg_num_rows($query);
		
		if ($rows > 0 ){
			while($row = pg_fetch_array($query))
			{		
		
				$total =  $row['total'];
			}
		}	
		return $total;
		
	}
	
	
	
	function update_m_loading($kolomnya, $m_loading_id, $value, $tipe, $n_perubahan = false)
	{
		
		if ($tipe == 'text' )
		{
			$string = " SET $kolomnya='$value'";
		}else{
			if ($n_perubahan)
			{
				$n_kenaikan = n_sebelumnya($m_loading_id);
				$perubahan = ( $n_kenaikan - $value );
				$string = " SET $kolomnya=$value, n_kenaikan=$perubahan ";
			}else{
				$string = " SET $kolomnya=$value ";
			}
			
		}
		$sql2="
					UPDATE m_loading
					  $string
					 WHERE m_loading_id = $m_loading_id;

					";
					pg_query($sql2);
	
	
	}
	
	
	function n_sebelumnya($m_loading_id)
	{
		
		$total = 0;
		$sql="
		select n_sisa_diantrian from m_loading  WHERE m_loading_id = $m_loading_id
		";

		$query = pg_query($sql) or die("Invalid query! FSRV n_kenaikan" . $sql) ;
			$rows = pg_num_rows($query);
		
		if ($rows > 0 ){
			while($row = pg_fetch_array($query))
			{		
				if (empty(  $row['n_sisa_diantrian'] ))
				{
					$total = 0;
				}else{
					$total =  $row['n_sisa_diantrian'];
				}
				
			}
		}	
		return $total;
		
	}
	
	
pg_close($con);