<?php



	require_once("../include/config.php");
	require_once("../include/session.php");
	require_once("../include/function.php");
	#if(is_file("../include/chr_symbol.php"))	require_once("../include/chr_symbol.php");
	date_default_timezone_set("Asia/Jakarta");
	//error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED | E_STRICT);
	
	ini_set('memory_limit', '-1');
	
	
	$con = pg_connect($connection) or die("Could not connect to database!");
	
	$act				= $_REQUEST['act'];
	$hidden_dir_1		= $_REQUEST['hidden_dir_1'];
	$hidden_dir_2		= $_REQUEST['hidden_dir_2'];
	
	//die($act);
	switch($act){

		case 'file_to_db'				: file_to_db( $hidden_dir_1, $hidden_dir_2 ); break;
		case 'function_1_generate_ocr'			: function_1_generate_ocr(); break;
		case 'function_2_generate_pdf'			: function_2_generate_pdf(); break;
		case 'function_3_insert_ocr'			: function_3_insert_ocr(); break;
		case 'function_4_stamp_pdf'				: function_4_stamp_pdf(); break;
		
		
		
		
		case 'check_process'			: check_process(); break;
		case 'check_sftp'				: check_sftp(); break;
		case 'info_ip_staging'			: info_ip_staging(); break;
		
	
	}
	
	
	
	function file_to_db( $hidden_dir_1, $hidden_dir_2 )
	{
		$sql		= "TRUNCATE TABLE master_proses_after_ematerai_file_ce_from_bri;
						TRUNCATE TABLE master_proses_after_ematerai_file_ce_from_produksi;
						TRUNCATE TABLE master_proses_after_ematerai_file_ce_from_produksi_ocr;
						TRUNCATE TABLE master_proses_after_ematerai_file_ce_from_produksi_ocr_summary;
						UPDATE master_proses_after_ematerai_produksi SET keterangan='off', waktu=NULL;
";
		$query 		= pg_query($sql);
		
		$status='';
		$respon_array = array();
		
		$dir= array();
		$dir[]= $hidden_dir_1;
		$dir[]= $hidden_dir_2;
		
		
		
		foreach( $dir as $key => $val ) 
		{
			$row = array();
			$row['folder'] = $val;
			
			if($row)
			{
				$folder=$row['folder'];
				if(is_dir($folder)){
					if ($dh = opendir($folder)) 
					{
						while (($file = readdir($dh)) !== false) 
						{
							if($file != "." && $file != ".." && $file != "...")
							{
								if(strtoupper(substr($file,-4))== ".PDF")
								{
									$tabel = get_tabel_file_to_db($folder);
									$sql		= "
									INSERT INTO $tabel(
											 nama_file, direktori)
									VALUES ('".$file."', '".$row['folder']."');
									";
									$query 		= pg_query($sql);
								} 
							} 
						}
						
						$status .= 'OK';
					}else{
						//echo $folder." tidak bisa dibuka (suggest: periksa hak akses folder pada administrator)";
					}
				}else{
					//echo $folder." tidak ditemukan (suggest: periksa setting default folder)";
				}
			}
			
		}
		
		$proses_ke = 0;
		
		$respon_array[]= $status;
		$respon_array[]= html_proses_bulet($proses_ke);
		$respon_array[]= div_tombol_lanjutan($proses_ke);
		//echo $status;
		//die($respon_array);
		echo json_encode($respon_array);
		
	}
	
	
	function html_proses_bulet($n_proses_running)
	{
		$total_proses = cek_total_proses();

//$x = array();
//$x = array('Status 1','Status 2','Status 3','Status 4', 'Status 5');


		$html = '
		<div class="container">
			<ul class="progressbar">';
		
		for ($i = 0; $i < $total_proses['total']; $i++) {
			$html .= '<li ';
			if ($i < $n_proses_running)
			{
				$html .= 'class="active"';
			}
			$html .= '>';
			$html .= "(PROSES ".($i+1).")<br>";
			$html .= $total_proses['rincian'][$i];
			if ($i < $n_proses_running)
			{
				$html .= ' <br><i>(selesai)</i>';
			}
			$html .= '</li>';
		
		}
		$html .= '
        </ul><br>
		 </div>';
	
	
		return $html;
	}
	
	function cek_total_proses()
	{
		$array_data=array();
		$sql = "SELECT * FROM master_proses_after_ematerai_produksi 
		ORDER BY id ASC";
		$query = pg_query($sql) or die('ERROR cek m_loading: '.$sql);
		$jml = @pg_num_rows($query);
		if($jml > 0){
			//$array_data['rincian'] = '';
			while($row = pg_fetch_array($query)){
				$array_data['rincian'][] = $row['nama_proses'];
			}
		}
		$array_data['total'] = $jml;
		return $array_data;
	}
	
	function div_tombol_lanjutan($n_proses_running)
	{
		
		$array_data=array();
		$html = '<input id="tombol_proses" name="tombol_proses" type="button" class="button"'; 
		$sql = "SELECT * FROM master_proses_after_ematerai_produksi 
		ORDER BY id ASC";
		$query = pg_query($sql) or die('ERROR cek m_loading: '.$sql);
		$jml = @pg_num_rows($query);
		if($jml > 0){
			//$array_data['rincian'] = '';
			$i = 0;
			while($row = pg_fetch_array($query)){
				if ($i == $n_proses_running )
				{
					$html .='value="PROSES '.($i+1).' - '.$row['nama_proses'].'" onclick="cek_proses_'.($i+1).'()"/><br><br><br><br>';
				}
				
				$i++;
			}
		}
		return $html;
	}
	
	function get_tabel_file_to_db($folder)
	{
		if ( preg_match("/PDF_PRODUKSI/i", $folder) )
		{
			return 'master_proses_after_ematerai_file_ce_from_produksi';
		}else if ( preg_match("/PDF_C_DAN_CE_EMATERAI_FROM_BRI/i", $folder) ){
			return 'master_proses_after_ematerai_file_ce_from_bri';
		}else{
			die("ERROR NAMA TABEL FILE TO DB");
		}
		
	}
	
	function function_1_generate_ocr()
	{
		
		$array_data=array();
		
		$sql = "SELECT * FROM master_proses_after_ematerai_file_ce_from_produksi 
		ORDER BY id ASC";
		$query = pg_query($sql) or die('ERROR cek function_1_generate_ocr: '.$sql);
		$jml = @pg_num_rows($query);
		if($jml > 0){
			//$array_data['rincian'] = '';
			$i = 0;
			update_proses(1, 'running');
			while($row = pg_fetch_array($query)){
					
					$direktori = $row['direktori'];
					$file_in = $row['nama_file'];
					$file_out = str_replace(array('.PDF','.pdf'),'.TXT', $file_in);
					if(file_exists( $direktori.$file_out )){
						unlink($direktori.$file_out);
					}
					//C:/gs2/gs9.56.1/bin/gswin64c.exe -sDEVICE=ocr -o C:/gs2/TEST.TXT -r200 C:/gs2/R1NCS.DAT.PDF
					
					$command = 'C:/gs2/gs9.56.1/bin/gswin64c.exe -sDEVICE=ocr -o ' . $direktori.$file_out . ' -r200 ' . $direktori.$file_in ;
					exec($command);
					cek_masih_running($direktori.$file_out);
					
					update_waktu_proses_ocr( $row['id'] );
					if(file_exists( $direktori.$file_out )){
						insert_file_ce_from_produksi_ocr($direktori, $file_out);
					}
				
				$i++;
			}
			update_proses(1, 'selesai');
			$status="1";
			
		}else{
			$status="0";
		}
		$proses_ke = 1;
		$respon_array[]= $status;
		$respon_array[]= html_proses_bulet($proses_ke);
		$respon_array[]= div_tombol_lanjutan($proses_ke);
		
		echo json_encode($respon_array);
	}
	
	
	
	
	function function_2_generate_pdf()
	{
		$array_data=array();
		
		$sql = "SELECT * FROM master_proses_after_ematerai_file_ce_from_bri 
		ORDER BY id ASC";
		$query = pg_query($sql) or die('ERROR cek function_2_generate_pdf: '.$sql);
		$jml = @pg_num_rows($query);
		if($jml > 0){
			//$array_data['rincian'] = '';
			$i = 0;
			update_proses(2, 'running');
			while($row = pg_fetch_array($query)){
					
					$direktori = $row['direktori'];
					$file_in = $row['nama_file'];
					$file_out = str_replace(array('.PDF','.pdf'),'_info.TXT', $file_in);
					$file_out2 = str_replace(array('.PDF','.pdf'),'_tmp_ematerai.PDF', $file_in);
					$file_out3 = str_replace(array('.PDF','.pdf'),'_unsecured.PDF', $file_in);
					$file_out4 = str_replace(array('.PDF','.pdf'),'_ematerai_gs_text.PDF', $file_in);
					$file_out5 = str_replace(array('.PDF','.pdf'),'_ematerai_gs_vector.PDF', $file_in);
					$file_out6 = str_replace(array('.PDF','.pdf'),'_ematerai_final_temp.PDF', $file_in);
					$file_out7 = str_replace(array('.PDF','.pdf'),'_ematerai_final_xyz.PDF', $file_in);
					//$file_out8 = str_replace(array('.PDF','.pdf'),'_ematerai_final.PDF', $file_in);
					if(file_exists( $direktori.$file_out )){
						unlink($direktori.$file_out);
					}
					if(file_exists( $direktori.$file_out2 )){
						unlink($direktori.$file_out2);
					}
					if(file_exists( $direktori.$file_out3 )){
						unlink($direktori.$file_out3);
					}
					
					
					$file = $direktori.$file_in ;
					//C:/gs2/gs9.56.1/bin/gswin64c.exe -sDEVICE=ocr -o C:/gs2/TEST.TXT -r200 C:/gs2/R1NCS.DAT.PDF
					//pdftk in.pdf dump_data output report.txt
					///////////////////////////////////////////////////////////
					$status_password_pdf = 0;
					
					$command = 'C:/pdftk/bin/pdftk ' . $direktori.$file_in . ' dump_data output ' . $direktori.$file_out ;
					//die($command);
					exec($command);
					cek_masih_running($direktori.$file_out);
					
					
					if(file_exists( $direktori.$file_out )){
						$status_password_pdf = 0;
						$command = 'C:/pdftk/bin/pdftk ' . $direktori.$file_in . ' cat 1-1 output ' . $direktori.$file_out2 ;
						exec($command);
						cek_masih_running($direktori.$file_out2);
					}else{
						$status_password_pdf = 1;
						$password_admin_pdf = "@BRI#".cek_password_file($file_in);
						//$command = 'C:/pdftk/bin/pdftk ' . $direktori.$file_in . ' input_pw @BRI#30032022 output ' . $direktori.$file_out3 ; 
						$command = 'C:/pdftk/bin/pdftk ' . $direktori.$file_in . ' input_pw '.$password_admin_pdf.' output ' . $direktori.$file_out3 ; 
						exec($command);
						cek_masih_running($direktori.$file_out3);
						
						$command = 'C:/pdftk/bin/pdftk ' . $direktori.$file_out3 . ' cat 2-2 output ' . $direktori.$file_out2; 
						exec($command);
						cek_masih_running($direktori.$file_out2);
						
						
						if(file_exists( $direktori.$file_out3 )){
							unlink($direktori.$file_out3);
						}
					}	
						
						///////////////////////menghapus text dan tutup denga nstamp
						//C:/gs2/gs9.56.1/bin/gswin64c.exe -o C:/gs2/hapus_text1.pdf -sDEVICE=pdfwrite -dFILTERTEXT C:/gs2/ematerai.PDF
						//C:/gs2/gs9.56.1/bin/gswin64c.exe -o C:/gs2/hapus_text2.pdf -sDEVICE=pdfwrite -dFILTERVECTOR C:/gs2/hapus_text1.pdf

						//menghapus text di ematerai
						$command = 'C:/gs2/gs9.56.1/bin/gswin64c.exe -o ' . $direktori.$file_out4 . ' -sDEVICE=pdfwrite -dFILTERTEXT ' . $direktori.$file_out2; 
						exec($command);
						cek_masih_running($direktori.$file_out4);
						if(file_exists( $direktori.$file_out2 )){
							unlink($direktori.$file_out2);
						}
						
						//menghapus barcode (vector) di ematerai
						$command = 'C:/gs2/gs9.56.1/bin/gswin64c.exe -o ' . $direktori.$file_out5 . ' -sDEVICE=pdfwrite -dFILTERVECTOR ' . $direktori.$file_out4; 
						exec($command);
						cek_masih_running($direktori.$file_out5);
						if(file_exists( $direktori.$file_out4 )){
							unlink($direktori.$file_out4);
						}
						
						
						//menutup gambar logo dan footer gambar dengan stamp
						//C:/pdftk/bin/pdftk  C:/test/test.PDF multistamp C:/test/MASTER_TUTUP_LOGO.PDF output C:/test/multistamp.pdf
						$command = 'C:/pdftk/bin/pdftk ' . $direktori.$file_out5 . ' multistamp ' . 'E:/_CONVERT_BRI_CETAK_EMATERAI/_master/MASTER_TUTUP_LOGO.PDF output '. $direktori.$file_out6; 
						exec($command);
						cek_masih_running($direktori.$file_out6);
						if(file_exists( $direktori.$file_out5 )){
							unlink($direktori.$file_out5);
						}
						
						
						//menutup LOGO
						$command = 'C:/pdftk/bin/pdftk E:/_CONVERT_BRI_CETAK_EMATERAI/_master/MASTER_PRODUKSI.PDF '. ' multistamp ' .$direktori.$file_out6. ' output '. $direktori.$file_out7; 
						exec($command);
						cek_masih_running($direktori.$file_out7);
						if(file_exists( $direktori.$file_out6 )){
							unlink($direktori.$file_out6);
						}
						
						
						///////////////////////menghapus text dan tutup denga nstamp
					
					
					update_status_password_pdf($row['id'] ,$status_password_pdf);
					if(file_exists( $direktori.$file_out7 )){
						insert_status_pisah_ematerai_pdf($direktori ,$file_out7);
					}
					
					///////////////////////////////////////////////////////////
					//$command = 'C:/gs2/gs9.56.1/bin/gswin64c.exe -sDEVICE=ocr -o ' . $direktori.$file_out . ' -r200 ' $direktori.$file_in ;
					//exec($command);
					//cek_masih_running($direktori.$file_out);
					
				
				
				$i++;
			}
			update_proses(2, 'selesai');
			$status="1";
			
		}else{
			$status="0";
		}
		$proses_ke = 2;
		$respon_array[]= $status;
		$respon_array[]= html_proses_bulet($proses_ke);
		$respon_array[]= div_tombol_lanjutan($proses_ke);
		
		echo json_encode($respon_array);
	}
	
	
	
	
	function function_3_insert_ocr()
	{
		
		$array_data=array();
		
		$sql = "SELECT * FROM master_proses_after_ematerai_file_ce_from_produksi_ocr 
		ORDER BY id ASC";
		$query = pg_query($sql) or die('ERROR cek function_3_insert_ocr: '.$sql);
		$jml = @pg_num_rows($query);
		if($jml > 0){
			//$array_data['rincian'] = '';
			$i = 0;
			update_proses(3, 'running');
			while($row = pg_fetch_array($query)){
					
					$direktori = $row['direktori'];
					$file_in = $row['nama_file'];
					$dirfile = $direktori.$file_in;
					
					
					if(file_exists($dirfile))
					{
						$customer_n = 0;
						$data_array = array();
						$open_dat = fopen($dirfile,'r') or die('Err: Error Open File! '.$dirfile);
						$halaman_sekarang = 0;
						
						
						while(!feof($open_dat))
						{
							$row_dat = fgets($open_dat);
							if ( preg_match("/XYZ:/i", $row_dat) || preg_match("/XYZ :/i", $row_dat))
							{
								$customer_n++;
								$nama = "Customer $customer_n";
								$tmp_cif = explode(':',$row_dat);
								//$cif = $customer_n.'-'.trim($tmp_cif[1]);
								$cif = $customer_n.'-'.trim($tmp_cif[1]);
								$cif2 = trim($tmp_cif[1]);
								$data_array[$cif]['cif'] = $cif2;
								$data_array[$cif]['nama'] = $nama;
								
								if ( $halaman_sekarang == 0)
								{
									$data_array[$cif]['start']= 1;
								}else{
									$data_array[$cif]['start']= ($halaman_sekarang+1);
								}
								$data_array[$cif]['tot_hlm'] = 0;
							}
							
							
							if ( preg_match("/Halaman:/i", $row_dat) || preg_match("/Halaman :/i", $row_dat))
							{
								$halaman_sekarang++;
								$data_array[$cif]['end'] = $halaman_sekarang;
								$data_array[$cif]['tot_hlm']++;
						
							}
					
						}
						fclose($open_dat);
						
						///////////////////////////////////////////////////
							//die(print_r($data_array));
							
							foreach( $data_array as $key => $value ) 
							{
								$sql2 = "INSERT INTO master_proses_after_ematerai_file_ce_from_produksi_ocr_summary(
												nama_cust, cif, mulai, selesai, total_halaman, nama_file, 
												direktori)
										VALUES (
												'".$value['nama']."', '".$value['cif']."', 
												".$value['start'].", ".$value['end'].", ".$value['tot_hlm'].",
												'".$file_in."', '".$direktori."'
												);
									";
								pg_query($sql2) or die('ERROR insert txt ocr to db : '.$sql2);
							}
						///////////////////////////////////////////////////
					}
					
					
					
					
			}
			update_proses(3, 'selesai');
			$status="1";
			
		}else{
			$status="0";
		}
		$proses_ke = 3;
		$respon_array[]= $status;
		$respon_array[]= html_proses_bulet($proses_ke);
		$respon_array[]= div_tombol_lanjutan($proses_ke);
		
		echo json_encode($respon_array);
	}
	
	
	
	function function_4_stamp_pdf()
	{
		$master_pdf_kosong = 'E:/_CONVERT_BRI_CETAK_EMATERAI/_master/MASTER_PRODUKSI.PDF';
		$dir_final_stamp = 'E:/_CONVERT_BRI_CETAK_EMATERAI/_FINAL_PDF/';
		$dir_awal_produksi = 'E:/_CONVERT_BRI_CETAK_EMATERAI/PDF_PRODUKSI/';
		$array_data=array();
		$array_data_final_stamp=array();
		$array_tgl_cycle=array();
		$array_nama_file_awal=array();
		$array_nama_siap_stamp=array();
		$array_nama_siap_stamp_xyz=array();
		
		$sql = "SELECT * FROM master_proses_after_ematerai_file_ce_from_produksi_ocr_summary 
		ORDER BY id ASC";
		$query = pg_query($sql) or die('ERROR cek function_3_insert_ocr: '.$sql);
		$jml = @pg_num_rows($query);
		if($jml > 0){
			//$array_data['rincian'] = '';
			$i = 0;
			update_proses(4, 'running');
			
			$file_txt = '';
			$tot_hlm_file = 0;
			while($row = pg_fetch_array($query)){
					
					$direktori 	= $row['direktori'];
					$nama_file 	= $row['nama_file'];
					$cif 		= $row['cif'];
					$nama_folder = str_replace(array('.txt','.TXT'),'',$nama_file);
					
					
					
					
					$mulai =  $row['mulai'];
					$selesai =  $row['selesai'];
					$total_halaman =  $row['total_halaman'];
					
					if($file_txt != $nama_file )
					{
						$pdfLoc=$direktori.$nama_folder.'/';
						$pdfLoc_xyz=$direktori.$nama_folder.'_XYZ/';
						$array_data[] = $pdfLoc;
						$array_data_xyz[] = $pdfLoc_xyz;
						$array_data_final_stamp[] = $nama_folder.'_FINALISASI.PDF';
						$array_data_final_stamp_fix[] = $nama_folder.'_FINALISASI_FIX.PDF';
						$array_nama_file_awal[] =  $nama_folder.'.PDF';
						$array_nama_siap_stamp[] =  $nama_folder.'_SIAP_STAMP.PDF';
						$array_nama_siap_stamp_xyz[] =  $nama_folder.'_SIAP_STAMP_TUTUP_XYZ.PDF';
						if(!file_exists($pdfLoc)) mkdir($pdfLoc)or die('Error mkdir '.$pdfLoc);
						if(!file_exists($pdfLoc_xyz)) mkdir($pdfLoc_xyz)or die('Error mkdir '.$pdfLoc_xyz);
						$file_txt = $nama_file;
						$tot_hlm_file = 1;
					}
					
					
					
					/////////////////////////////////////////////////////////
					$sql2 = "SELECT * FROM master_proses_after_ematerai_file_ce_from_bri 
					WHERE lower(nama_file) like lower('%ematerai_final_xyz.PDF%')
					AND lower(nama_file) like lower('%$cif%')
					ORDER BY id DESC LIMIT 1";
					$query2 = pg_query($sql2) or die('ERROR cek function_3_insert_ocr: '.$sql2);
					$jml2 = @pg_num_rows($query2);
					if($jml2 > 0){
						while($row2 = pg_fetch_array($query2)){
							
							$direktori2 	= $row2['direktori'];
							$nama_file2		= $row2['nama_file'];
							
							
							$tgl_cycle_temp = explode('_',$nama_file2);
							$tgl_cycle = $tgl_cycle_temp[1];
							
							$nama_file_pdf			=  str_pad($mulai, 5, 0, STR_PAD_LEFT).'_'.'SPLIT_EMATERAI_'.$cif.'_'.$tgl_cycle.'.PDF';
							$nama_file_pdf_xyz			=  str_pad($mulai, 5, 0, STR_PAD_LEFT).'_'.'XYZ_SPLIT_EMATERAI_'.$cif.'_'.$tgl_cycle.'.PDF';
							
							
						
							copy( $direktori2.$nama_file2 , $pdfLoc.$nama_file_pdf);
							copy( 'E:/_CONVERT_BRI_CETAK_EMATERAI/_master/MASTER_TUTUP_LOGO_XYZ.PDF' , $pdfLoc_xyz.$nama_file_pdf_xyz);
						}
					}else{
						$nama_file_pdf			=  str_pad($mulai, 5, 0, STR_PAD_LEFT).'_'.'SPLIT_EMATERAI_KOSONG_'.$cif.'.PDF';
						copy($master_pdf_kosong , $pdfLoc.$nama_file_pdf);
					}
					cek_masih_running($pdfLoc.$nama_file_pdf);
					/////////////////////////////////////////////////////////
					/////////////////////////////////////////////////////////
					//copy file tambahan file sisa
					
					for ($ii = $mulai; $ii < $selesai; $ii++) 
					{
						$nomor_pdfnya= ($ii+1);
						$nama_file_pdf			=  str_pad($nomor_pdfnya, 5, 0, STR_PAD_LEFT).'_'.'SPLIT_BLANK.PDF';
						copy($master_pdf_kosong , $pdfLoc.$nama_file_pdf);
						copy($master_pdf_kosong , $pdfLoc_xyz.$nama_file_pdf);
						cek_masih_running($pdfLoc.$nama_file_pdf);
					}
					/////////////////////////////////////////////////////////
					
					
					
			}
			
			
			//$tgl_cycle
			//die(print_r($array_data));
			//$array_data
			//$array_data_final_stamp
			
			$dir_final_stamp = $dir_final_stamp.date('Ymd_His').'_CYC_'.$tgl_cycle.'/';
			if(!file_exists($dir_final_stamp)) mkdir($dir_final_stamp)or die('Error mkdir '.$dir_final_stamp);
			
			
			foreach( $array_data as $key => $value ) 
			{
				//gabungkan 1 folder
				$file_gabung = $array_data[$key].'*.PDF';
				$file_gabung_xyz = $array_data_xyz[$key].'*.PDF';
				$file_siap_stamp = $array_data[$key].$array_nama_siap_stamp[$key];
				$file_siap_stamp_xyz = $array_data[$key].$array_nama_siap_stamp_xyz[$key];
				$file_awal_produksi = $dir_awal_produksi.$array_nama_file_awal[$key];
				$file_finalisasi_stamp = $dir_final_stamp.$array_data_final_stamp[$key];
				$file_finalisasi_stamp_fix = $dir_final_stamp.$array_data_final_stamp_fix[$key];
				
				
				
				if(file_exists( $file_siap_stamp )){
					unlink($file_siap_stamp);
				}
				$command = 'C:/pdftk/bin/pdftk ' . $file_gabung . ' cat output ' . $file_siap_stamp;
				exec($command);
				cek_masih_running($file_siap_stamp);
				
				if(file_exists( $file_siap_stamp_xyz )){
					unlink($file_siap_stamp_xyz);
				}
				$command = 'C:/pdftk/bin/pdftk ' . $file_gabung_xyz . ' cat output ' . $file_siap_stamp_xyz;
				exec($command);
				cek_masih_running($file_siap_stamp_xyz);
				
				
				//stamp dengan master
				if(file_exists( $file_finalisasi_stamp )){
					unlink($file_finalisasi_stamp);
				}
				$command = 'C:/pdftk/bin/pdftk ' . $file_siap_stamp . ' multistamp ' . $file_awal_produksi.' output '.$file_finalisasi_stamp; 
				exec($command);
				cek_masih_running($file_finalisasi_stamp);
				
				
				//stamp dengan master
				if(file_exists( $file_finalisasi_stamp_fix )){
					unlink($file_finalisasi_stamp_fix);
				}
				$command = 'C:/pdftk/bin/pdftk ' . $file_finalisasi_stamp . ' multistamp ' . $file_siap_stamp_xyz.' output '.$file_finalisasi_stamp_fix; 
				exec($command);
				cek_masih_running($file_finalisasi_stamp_fix);
				
				if(file_exists( $file_finalisasi_stamp_fix )){
					unlink($file_finalisasi_stamp);
				}
				
							
			}
			update_proses(4, 'selesai');
			$status="1";
			
		}else{
			$status="0";
		}
		$proses_ke = 4;
		$respon_array[]= $status;
		$respon_array[]= html_proses_bulet($proses_ke);
		$respon_array[]= div_tombol_lanjutan($proses_ke);
		
		echo json_encode($respon_array);
	}
	
	function update_proses($n, $status)
	{
		$sql = "UPDATE master_proses_after_ematerai_produksi
			SET keterangan='$status' , waktu=now()
			WHERE id = $n";
		pg_query($sql) or die('ERROR cek update_proses: '.$sql);
		
	
	}
	
	function update_status_password_pdf($n, $status)
	{
		$sql = "UPDATE master_proses_after_ematerai_file_ce_from_bri
			SET status_password_pdf='$status' , waktu_proses=now() 
			WHERE id = $n";
		pg_query($sql) or die('ERROR cek master_proses_after_ematerai_file_ce_from_bri: '.$sql);
		
	
	}
	
	
	function update_waktu_proses_ocr($n)
	{
		$sql = "UPDATE master_proses_after_ematerai_file_ce_from_produksi
			SET waktu_proses=now()
			WHERE id = $n";
		pg_query($sql) or die('ERROR cek master_proses_after_ematerai_file_ce_from_produksi: '.$sql);
		
	
	}
	
	function insert_file_ce_from_produksi_ocr($direktori, $nama_file)
	{
		$sql = "INSERT INTO master_proses_after_ematerai_file_ce_from_produksi_ocr(
             nama_file, direktori, waktu_proses)
				VALUES ( '$nama_file', '$direktori', now());";
		pg_query($sql) or die('ERROR cek master_proses_after_ematerai_file_ce_from_produksi_ocr: '.$sql);
		
	
	}
	
	function insert_status_pisah_ematerai_pdf($direktori, $nama_file)
	{
		$sql = "INSERT INTO master_proses_after_ematerai_file_ce_from_bri(
             nama_file, direktori, waktu_proses, status_password_pdf)
				VALUES ( '$nama_file', '$direktori', now(), 'f');";
		pg_query($sql) or die('ERROR cek master_proses_after_ematerai_file_ce_from_bri: '.$sql);
		
	
	}
	
	function cek_password_file($file)
	{
		$a = str_replace(array('.pdf','.PDF'),'',$file);
		$b = explode('_',$a);
		return $b[1];
	}
	
	
	
function cek_masih_running($filePath)
{
	
	if (file_exists($filePath)) {
	  $fileModificationUnixTime = filemtime($filePath);
	  while (filemtime($filePath) != $fileModificationUnixTime) {
		//echo 'No changes found.';
		sleep(2);
		//clearstatcache(); // clears the cached result
	  }
	 // echo 'Changes found';
	}else{
		//die("FILE $filePath TIDAK ADA" );
	}
}
	

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function check_process()
	{
		
		$sql_cek_dat = "SELECT * FROM master_proses_after_ematerai where keterangan='off'";
		$qry_cek_dat = pg_query($sql_cek_dat) or die('ERROR cek m_loading: '.$sql_cek_dat);
		$jml = @pg_num_rows($qry_cek_dat);
		if($jml == 0){
			echo "1";
		}else{
			echo "0";
		}
		
	}
	
	
	function info_ip_staging()
	{
		
		$sql= "select ip from m_sftp_server_sumber where status='t' LIMIT 1";
		$qry = @pg_query($sql);;
		$row = @pg_fetch_assoc($qry);
		$ip_m_sftp_server_sumber = $row['ip'];
		
		
		$sql= "select ip from m_sftp_server_tujuan where status='t' LIMIT 1";
		$qry = @pg_query($sql);;
		$row = @pg_fetch_assoc($qry);
		$ip_m_sftp_server_tujuan = $row['ip'];
		
		
		if ( !empty($ip_m_sftp_server_sumber) && !empty($ip_m_sftp_server_tujuan) )
		{
			echo '<div class="alert alert-info">
				IP SFTP Source Server : <b>'.$ip_m_sftp_server_sumber.'</b><br>
				IP SFTP Destination Server : <b>'.$ip_m_sftp_server_tujuan.'</b>
				</div>';
		}else{
			echo '';
		}
	}
	
	
	
	function check_sftp()
	{
		
		$config=config('tujuan');

		
		// Connect to FTP Server
			$conn_id = @ftp_connect($config['ip'], $config['port'] );
			if($conn_id){
			// Login to FTP Server
				$login_result = @ftp_login($conn_id, $config['key_user'], $config['key_pass']);
				if($login_result) @ftp_pasv( $conn_id,true);
			}
			$ket = '';$ket_key = 'TUJUAN';
			if($tipe=='tujuan2'){
				$ket=' 2';$ket_key = 'TUJUAN2';
			}
			// Verify Log In Status
			if ((!$conn_id) || (!$login_result)) {
				echo 'LOGIN FTP FAILED';
			} else 
			{
				echo 'LOGIN FTP SUCCESS';
			}
			
			if ($conn_id) ftp_close($conn_id);
	}
	
	@pg_close($con);
?>