<?php 
	

$GLOBALS['dir_root'] 						= 'C:/App/htdocs/e_statement_bri/';



require_once($GLOBALS['dir_root']."include/config.php");
require_once($GLOBALS['dir_root'].'include/tcpdf/tcpdf.php');
require_once($GLOBALS['dir_root'].'include/FPDI/fpdi.php');
require_once($GLOBALS['dir_root'].'tool/body_email_a1.php');


$con = pg_connect($connection) or die("Could not connect to database!");

ini_set('memory_limit', '-1');
$GLOBALS['dir_root_all_pdf'] 				= $GLOBALS['dir_root'] .'EMBOSS_EMATERAI/_ALL_PDF/';
$GLOBALS['dir_root_master_ketentuan'] 				= $GLOBALS['dir_root'] .'tmp/master/PL/master_ketentuan/';
$GLOBALS['dir_root_master_blank_halaman'] 				= $GLOBALS['dir_root'] .'tmp/master/PL/master_blank_halaman/NO_PRODUK.pdf';
$GLOBALS['dir_root_master_cover_front'] 	= $GLOBALS['dir_root'] .'tmp/master/PL/master_cover/00000_cover_front.pdf';
$GLOBALS['dir_root_master_cover_back'] 		= $GLOBALS['dir_root'] .'tmp/master/PL/master_cover/99999_cover_back.pdf';
$GLOBALS['waktu_awal'] 						= strtotime(date("Y-m-d H:i:s"));
$tabel_status 								= 'master_proses_after_ematerai';
$temp 										= 'temp_csv_a1';
$temp_status 								= 'temp_csv_a1_status';

$status 						= cek_flag_proses_ready($tabel_status);
$n_split 						= cek_n_split($tabel_status);
$arry_1 						= cek_arry_1($temp);


$GLOBALS['flagtrans'] 			= $arry_1['flagtrans'];
$GLOBALS['blth'] 				= $arry_1['blth'];
$GLOBALS['m_loading_id'] 		= $arry_1['m_loading_id'];
$GLOBALS['nama_file']			= trim($arry_1['nama_file']);
$GLOBALS['nama_file_plain']		= str_replace(array(".dat",".DAT"),'',$GLOBALS['nama_file']);
#$GLOBALS['nama_file_plain']		= str_replace(array(".csv",".CSV"),'',$GLOBALS['nama_file']);
#$GLOBALS['nama_file_plain']		= str_replace(array(".xlsx",".XLSX"),'', $GLOBALS['nama_file_plain'] );




/*
buat_log_txt("",$flagtrans,$blth,$file,$file_p,'1');
buat_log_txt("",$flagtrans,$blth,$file,$file_p,'2',$waktu_awal);
buat_log_txt("Err : ".$msg_error,$flagtrans,$blth,$file,$file_p);
*/
if ($status == 'ready' )
{
	buat_log_txt("",$GLOBALS['flagtrans'],$GLOBALS['blth'],$GLOBALS['nama_file'],'','1');
	update_status('onproses');
	buat_log_txt("Update Status Convert menjadi onproses , dengan jumlah per split $n_split ",$GLOBALS['flagtrans'],$GLOBALS['blth'],$GLOBALS['nama_file'],'');
	
	//set_tidak_ada_null();
	
	proses_split_dan_inject($n_split, $temp, $temp_status );
}else{
	die("tidak ada proses");
}


function set_tidak_ada_null()
{
	
	$tabeldetail = strtolower("detail_".$GLOBALS['blth']."_"."mt");
	$file_replace = str_replace(substr( $GLOBALS['nama_file'],-7),'',$GLOBALS['nama_file']);
	$sql = "UPDATE $tabeldetail 
		set no_rek_asli = NULL 
	WHERE lower(nama_file) like lower('%$file_replace%')
						AND no_rek_asli ='tidak ada'
						";
	pg_query($sql);
}


function proses_split_dan_inject($n_split, $temp, $temp_status )
{
	
	$tabeldetail = strtolower("detail_".$GLOBALS['blth']."_"."mt");
	$file_replace = str_replace(substr( $GLOBALS['nama_file'],-7),'',$GLOBALS['nama_file']);
	$sql = "SELECT * ,nomor_rekening as policy_number
						FROM 
						$tabeldetail WHERE lower(nama_file) like lower('%$file_replace%')
						AND no_rek_asli IS NULL
						ORDER BY detail_id ASC LIMIT $n_split
						";
	#die($sql);					
	//$sql		= "SELECT *, policy_no as policy_number, birth_dt as dob FROM ".$temp." WHERE 
	//status_a1 IS NULL AND policy_no IS NOT NULL AND estatement_ind='Y' 
	//ORDER BY id ASC LIMIT $n_split";
	$query 		= pg_query($sql);
	$n_row 		= pg_num_rows($query);
	//die ( $sql );
	if($n_row > 0)
	{
		
		$GLOBALS['record_not_exists_ketentuan'] = '';
		$GLOBALS['record_not_exists_email'] = '';
		//$urutan = 0;
		$urutan = get_urutan_no_pdf_ematerai();
		while($row = @pg_fetch_array($query))
		{
			$halaman = 0;
			
			update_temp_status($temp_status, $row['policy_number'], $GLOBALS['m_loading_id'], 'running');
			buat_log_txt("-- START Proses ".$row['policy_number'],$GLOBALS['flagtrans'],$GLOBALS['blth'],$GLOBALS['nama_file'],'');
			
			
			$pdf_input_fpdf = NULL;
			$pdf_output_fpdi = NULL;
			$password_pdf = NULL;
			
			
			#$row['dob'] =  preg_replace( "/\r|\n/", "", $row['dob'] );
			
			$arr_get_nama_pdf 		= get_nama_pdf($row['policy_number']);
			
			//die(print_r($arr_get_nama_pdf));
			
			if (empty( $arr_get_nama_pdf['nama_file'] ))
			{
				update_temp_status($temp_status, $row['policy_number'], $GLOBALS['m_loading_id'], 'tidak ada');
				
				
				
				buat_log_txt("------ File PDF MASTER EMBOSS TIDAK ADA ".$row['policy_number'],$GLOBALS['flagtrans'],$GLOBALS['blth'],$GLOBALS['nama_file'],'');
				//update_insert_temp_csv($temp, $row['policy_number'], $GLOBALS['m_loading_id'], 'status_a1', 'no_pdf');
				$GLOBALS['record_not_exists_ketentuan'] .= $row['policy_number'] .";"."PDF MASTER EMBOSS EMATERAI".";".$arr_pr[0] .";". chr(13);
				continue;
			}
			$urutan++;
			update_urutan_no_pdf_ematerai($urutan);
			buat_file_pdf_temp( $row['policy_number'] );
							
			
			
			$pdf_input_fpdf 		= $arr_get_nama_pdf['direktori'].$arr_get_nama_pdf['nama_file'];
			
			//$nama_file_gabung = str_pad($urutan, 6, "0", STR_PAD_LEFT).'_Billing_Statement'.'_'.substr($row['policy_number'],-4).'.pdf';
			$nama_file_gabung = str_pad($urutan, 6, "0", STR_PAD_LEFT).'_Billing_Statement'.'_'.substr($row['policy_number'],-5).'.pdf';
					
			$pdf_output_fpdi 		= $GLOBALS['direktori_pdf'].$nama_file_gabung;
			
			
			
			//$nama_file_gabung 		= masking_nomor_polis($arr_get_nama_pdf['nama_file']);
			//$nama_file_gabung 		= str_pad( $row['id'] , 5, "0", STR_PAD_LEFT).'_'.$nama_file_gabung;
			
			//$password_pdf 			= trim($row['dob']);
			
			//update_insert_temp_status($temp_status, $row['policy_number'], $GLOBALS['m_loading_id'], 'status_cover', 'proses');
			//update_insert_temp_status($temp_status, $row['policy_number'], $GLOBALS['m_loading_id'], 'status_cover', 'selesai');
			
			//update_insert_temp_status($temp_status, $row['policy_number'], $GLOBALS['m_loading_id'], 'status_polis_page3', 'proses');
			//update_insert_temp_status($temp_status, $row['policy_number'], $GLOBALS['m_loading_id'], 'status_polis_page3', 'selesai');
			
			//update_insert_temp_status($temp_status, $row['policy_number'], $GLOBALS['m_loading_id'], 'status_ketentuan', 'proses');
			//update_insert_temp_status($temp_status, $row['policy_number'], $GLOBALS['m_loading_id'], 'status_ketentuan', 'selesai');
			
			//update_insert_temp_status($temp_status, $row['policy_number'], $GLOBALS['m_loading_id'], 'status_spaj', 'proses');
			//update_insert_temp_status($temp_status, $row['policy_number'], $GLOBALS['m_loading_id'], 'status_spaj', 'selesai');
			
			//update_insert_temp_status($temp_status, $row['policy_number'], $GLOBALS['m_loading_id'], 'status_combine', 'proses');
			//update_insert_temp_status($temp_status, $row['policy_number'], $GLOBALS['m_loading_id'], 'status_combine', 'selesai');
			
			/*
			if(file_exists( $pdf_input_fpdf_rev )){
				unlink($pdf_input_fpdf_rev);
			}
			*/
			
			
			
			
			if(file_exists( $pdf_output_fpdi )){
				unlink($pdf_output_fpdi);
			}
			
			copy($pdf_input_fpdf,$pdf_output_fpdi);
			cek_masih_running($pdf_output_fpdi);
			
			/*
			$pdf_input_fpdf_rev = str_replace(array(".pdf", ".PDF"), "_rev1.pdf", $pdf_input_fpdf);
			$command = 'C:/pdftk/bin/pdftk ' . $pdf_input_fpdf . ' output ' . $pdf_input_fpdf_rev ;
			///$command = 'C:/qpdf/bin/qpdf --decrypt ' . $pdf_input_fpdf . ' ' . $pdf_input_fpdf_rev; 
			//die($command);
			exec($command);
			cek_masih_running($pdf_input_fpdf_rev);
			
			//die("AAAAAAAAAAAAAAAAAAAAAAAAAAA");
			$halaman = cetak_pdf_password( $pdf_input_fpdf_rev	 , $pdf_output_fpdi , $password_pdf);
			//die($halaman);
			cek_masih_running($pdf_output_fpdi);
			*/
			
			
			if(file_exists( $pdf_output_fpdi ))
			{
				
				
			
			
			//die("aaaa");
			
			
			
			//die("bbbb");
			
			
			buat_log_txt("-- END Proses ".$row['policy_number'],$GLOBALS['flagtrans'],$GLOBALS['blth'],$GLOBALS['nama_file'],'');
			
			#update_insert_temp_csv($temp, $row['policy_number'], $GLOBALS['m_loading_id'], 'status_a1', 'selesai');
			update_temp_status($temp_status, $row['policy_number'], $GLOBALS['m_loading_id'], 'selesai');
			
			buat_log_txt("##### UPDATE STATUS DONE untuk polis ".$row['policy_number'],$GLOBALS['flagtrans'],$GLOBALS['blth'],$GLOBALS['nama_file'],'');
			
			insert_detail_copy($GLOBALS['m_loading_id'], $row['policy_number'], $GLOBALS['nama_file'], $nama_file_gabung , $GLOBALS['flagtrans'], $GLOBALS['blth'] );
			
			
			}
			
		}
		
		buat_csv_noexist('',$GLOBALS['record_not_exists_ketentuan'],'no_exist_ketentuan_polis');
		
		if ( !empty($GLOBALS['record_not_exists_email'] ))
		{
			buat_csv_noexist('',$GLOBALS['record_not_exists_email'],'no_exist_email');
		}
		
		
		update_status('ready');
		die("Proses part selesai");
	}else{
		#$sisa_proses = cek_sisa_yang_belum_terproses($temp);
		$sisa_proses = cek_sisa_yang_belum_terproses($tabeldetail,$file_replace);
		if ( $sisa_proses == 'ada' )
		{
			update_status('onproses');
		}else{
			update_status('off');
			update_m_loading($GLOBALS['m_loading_id'],'','', $GLOBALS['flagtrans'], $GLOBALS['blth']);
			
			update_urutan_no_pdf_ematerai(0);
		}
		
		$detail_db = 'detail_'.$GLOBALS['blth'].'_'.$GLOBALS['flagtrans'];
		#create_report_convert2($GLOBALS['m_loading_id'],$detail_db,$GLOBALS['blth'],$GLOBALS['nama_file'],$GLOBALS['flagtrans']);
		create_csv($GLOBALS['m_loading_id'],$detail_db,$GLOBALS['blth'],$GLOBALS['nama_file'],$GLOBALS['flagtrans']);
		buat_log_txt("",$GLOBALS['flagtrans'],$GLOBALS['blth'],$GLOBALS['nama_file'],'','2', $GLOBALS['waktu_awal']);
		
		
		
		$filesx = glob($GLOBALS['dir_root_all_pdf'].'/*'); //get all file names
		foreach($filesx as $filex){
			if(is_file($filex))
			@unlink($filex); //delete file
		}

		die("PROSES SELESAI");
	}
	
}





function cek_sisa_yang_belum_terproses($tabeldetail,$file_replace)
{
	$sql = "SELECT * FROM 
						$tabeldetail WHERE lower(nama_file) like lower('%$file_replace%')
						AND no_rek_asli IS NULL
						ORDER BY detail_id ASC
						";
	#$sql_cek_dat = "SELECT * FROM $tabel_status WHERE status IS NULL";
		$qry_cek_dat = pg_query($sql) or die('ERROR cek m_loading 4: '.$sql);
		$jml = @pg_num_rows($qry_cek_dat);
		$row = pg_fetch_assoc($qry_cek_dat);
		if($jml == 0){
			$status_akhir = "selesai";
		}else{
			$status_akhir = "ada";
			
		}
		
		return $status_akhir;
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


function buat_file_pdf_temp($no_polis)
{
	$loc_bill=$GLOBALS['dir_root']."pdf/".$GLOBALS['flagtrans']."/";
	if(!file_exists($loc_bill)) mkdir($loc_bill)or die('error buat folder pdf');
	$loc_bill.=$GLOBALS['blth']."/";
	if(!file_exists($loc_bill)) mkdir($loc_bill)or die('error buat folder pdf');
	$loc_bill.=$GLOBALS['nama_file_plain']."/";
	if(!file_exists($loc_bill)) mkdir($loc_bill)or die('error buat folder pdf');
	$GLOBALS['direktori_pdf'] =$loc_bill ;
	
	
	/*
	$loc_bill.=$no_polis."/";
	if(!file_exists($loc_bill)) mkdir($loc_bill)or die('error buat folder pdf');
	$GLOBALS['direktori_temp'] =$loc_bill ;
	*/
	return true;
}

function unlink_buat_file_pdf_temp($no_polis)
{
	$loc_bill=$GLOBALS['dir_root']."pdf/".$GLOBALS['flagtrans']."/";
	if(!file_exists($loc_bill)) mkdir($loc_bill)or die('error buat folder pdf');
	$loc_bill.=$GLOBALS['blth']."/";
	if(!file_exists($loc_bill)) mkdir($loc_bill)or die('error buat folder pdf');
	$loc_bill.=$GLOBALS['nama_file_plain']."/";
	if(!file_exists($loc_bill)) mkdir($loc_bill)or die('error buat folder pdf');
	$loc_bill.=$no_polis."/";
	if(!file_exists($loc_bill)) mkdir($loc_bill)or die('error buat folder pdf');
	
	hapus_folder($loc_bill);
	rmdir(rtrim($loc_bill,'/'));
	return true;
}

function insert_temp_status($temp_status, $no_polis, $m_loading_id)
{
	
	$sql_del = "INSERT INTO $temp_status(
           nomor_polis, m_loading_id)
    VALUES ('$no_polis','$m_loading_id');";
	$qry_del = pg_query($sql_del) or die('ERROR: '.$qry_del);

}

function update_temp_status($temp_status, $no_polis, $m_loading_id, $statusnya='')
{
	$tabeldetail = strtolower("detail_".$GLOBALS['blth']."_"."mt");
	$file_replace = str_replace(substr($GLOBALS['nama_file'],-7),'',$GLOBALS['nama_file']);
	$sql_del = "UPDATE $tabeldetail
   SET no_rek_asli='$statusnya'
    WHERE lower(nama_file) like lower('%$file_replace%') AND nomor_rekening='$no_polis';
";
	$qry_del = pg_query($sql_del) or die('ERROR: '.$qry_del);

}

function update_insert_temp_status($temp_status, $no_polis, $m_loading_id, $proses, $ket)
{
	
	$sql_del = "UPDATE $temp_status set $proses='$ket' 
	WHERE nomor_polis='$no_polis' AND m_loading_id='$m_loading_id'";
	$qry_del = pg_query($sql_del) or die('ERROR: '.$qry_del);

}
function update_insert_temp_csv($temp_status, $no_polis, $m_loading_id, $proses, $ket)
{
	
	$sql_del = "UPDATE $temp_status set $proses='$ket' 
	WHERE policy_no='$no_polis' AND m_loading_id='$m_loading_id'";
	$qry_del = pg_query($sql_del) or die('ERROR: '.$qry_del);

}



function update_status($tabel_status)
{
	
	$sql_del = "UPDATE master_proses_after_ematerai set keterangan='$tabel_status', waktu=now();";
	$qry_del = pg_query($sql_del) or die('ERROR: '.$qry_del);

}



function cek_flag_proses_ready($tabel_status)
{
	$sql_cek_dat = "SELECT * FROM $tabel_status WHERE keterangan='ready' and (cast( now() as timestamp))-(cast( waktu as timestamp)) >  '1 seconds'";
	#die($sql_cek_dat);
		$qry_cek_dat = pg_query($sql_cek_dat) or die('ERROR cek m_loading 4: '.$sql_cek_dat);
		$jml = @pg_num_rows($qry_cek_dat);
		$row = pg_fetch_assoc($qry_cek_dat);
		if($jml == 0){
			$status_akhir = "selesai";
		}else{
			$status_akhir = "ready";
			
		}
		
		return $status_akhir;
}


function cek_arry_1($tabel_status)
{
	$x=array();
	#$sql_cek_dat = "SELECT * FROM $tabel_status  LIMIT 1";
	$sql_cek_dat = "SELECT * FROM m_loading Where total_customer IS NULL AND flagtrans='BC' ORDER BY m_loading_id DESC  LIMIT 1";
	#die($sql_cek_dat);
		$qry_cek_dat = pg_query($sql_cek_dat) or die('ERROR cek m_loading 4: '.$sql_cek_dat);
		$jml = @pg_num_rows($qry_cek_dat);
		$row = pg_fetch_assoc($qry_cek_dat);
		if($jml == 0){
			die("ERROR CEK FLAGTRANS BLTH");
		}else{
			$x['flagtrans'] = $row['flagtrans'];
			$x['blth'] = $row['blth'];
			$x['m_loading_id'] = $row['m_loading_id'];
			$x['nama_file'] = $row['loading_file'];
			
		}
		
		return $x;
}



function cek_n_split($tabel_status)
{
	$x=array();
	$sql_cek_dat = "SELECT * FROM $tabel_status  LIMIT 1";
		$qry_cek_dat = pg_query($sql_cek_dat) or die('ERROR cek m_loading 4: '.$sql_cek_dat);
		$jml = @pg_num_rows($qry_cek_dat);
		$row = pg_fetch_assoc($qry_cek_dat);
		if($jml == 0){
			die("ERROR CEK JUMLAH SPLIT PROSES");
		}else{
			$x = $row['jumlah_proses'];
		}
		
		return $x;
}

	function buat_log_txt($log_txt,$flagtrans,$blth,$file,$file_p,$flag='',$waktu_awal=''){
		$nfile = str_replace(substr($file,-4),'',$file);
		if(is_dir($GLOBALS['dir_root'].'temp_file/')){
			$dir = $GLOBALS['dir_root'].'temp_file/logs/';
		}else{
			if(!is_dir($GLOBALS['dir_root'].'tmp/')) mkdir($GLOBALS['dir_root'].'tmp',0777);
			$dir = $GLOBALS['dir_root'].'tmp/logs/';
		}
		if(!is_dir($dir)) mkdir($dir,0777);
		$dir .= $flagtrans.'/';
		if(!is_dir($dir)) mkdir($dir,0777);
		$dir .= $blth.'/';
		if(!is_dir($dir)) mkdir($dir,0777);
		$dir .= $nfile.'/';
		if(!is_dir($dir)) mkdir($dir,0777);
		$file_txt = $dir.$nfile.".txt";
		#flag hanya untuk start saja
		if($flag==1){
			#flag open awal
			if(is_file($file_txt)){
				$rename = $nfile.date("Y_m_d H_i_s").".txt";
				$rename = str_replace(' ','_',$rename);
				$file_rename = $dir.$rename;
				if(is_file($file_rename)){
					unlink($file_rename);
				}else{
					rename($file_txt,$file_rename);
				}
			}
			$open_file = fopen($file_txt,'a+');
			$awal = echo_memory_usage() . " -- ".'['.str_pad(date("Y-m-d H:i:s"),20,' ',STR_PAD_RIGHT).']';
			$log_txt = "START BATCH PROSES COPY FILE PDF EMBOS EMATERAI ESTAT BRI";
			fwrite($open_file,$awal." ".$log_txt.chr(13));
			$awal = echo_memory_usage() . " -- ".'['.str_pad(date("Y-m-d H:i:s"),20,' ',STR_PAD_RIGHT).']';
			$log_txt = "File CSV	: ".$file;
			fwrite($open_file,$awal." ".$log_txt.chr(13));
			fclose($open_file);
		}else if($flag==2){
			$waktu_txt = '';
			$waktu_akhir = strtotime(date("Y-m-d H:i:s"));
			$waktu = $waktu_akhir-$waktu_awal;
			$menit = floor($waktu / 60);	$detik = $waktu % 60;
			$waktu_txt = "";
			if($menit>0){
				$waktu_txt .= " $menit Minutes";
			}
			$waktu_txt .= " $detik Seconds";
			$log_txt = "Time Process	: ".$waktu_txt;
			
			#data keterangan 
			$open_file = fopen($file_txt,'a+');
			$awal = echo_memory_usage() . " -- ".'['.str_pad(date("Y-m-d H:i:s"),20,' ',STR_PAD_RIGHT).']';
			fwrite($open_file,$awal." ".$log_txt.chr(13));
			$awal = echo_memory_usage() . " -- ".'['.str_pad(date("Y-m-d H:i:s"),20,' ',STR_PAD_RIGHT).']';
			$log_txt = "FINISH PROSES COPY FILE PDF EMBOS EMATERAI ESTAT BRI";
			fwrite($open_file,$awal." ".$log_txt.chr(13));
			fclose($open_file);
		}else{
			#data keterangan 
			$awal = echo_memory_usage() . " -- ".'['.str_pad(date("Y-m-d H:i:s"),20,' ',STR_PAD_RIGHT).']';
			$open_file = fopen($file_txt,'a+');
			fwrite($open_file,$awal." ".$log_txt.chr(13));
			fclose($open_file);
		}
		
	}
	
	
	function echo_memory_usage() { 
        $mem_usage = memory_get_usage(true); 
		return number_format($mem_usage); 
        /*
        if ($mem_usage < 1024) 
		{
            return number_format($mem_usage,0,' ','')." bytes"; 
        }elseif ($mem_usage < 1048576) {
            return number_format(  round($mem_usage/1024,2) ,0,' ','')." kilobytes"; 
		}else{
            return number_format( round($mem_usage/1048576,2),0,' ','') ." megabytes"; 
        } 
		*/
    } 
	
	
	function hapus_folder($dir)
	{
        $dir = rtrim($dir,"/");
        $mydir = opendir($dir);
        while(false !== ($file = readdir($mydir)))
		{
            if($file != "." && $file != "..")
			{
                @chmod($dir."/".$file, 0777);
                if(is_dir($dir."/".$file))
				{
                    chdir('.');
                    hapus_folder($dir."/".$file);
                    rmdir($dir."/".$file) or die("couldn't delete ".$dir.$file."<br />");
                }
				else
				{
                    unlink($dir."/".$file) or die("couldn't delete ".$dir.$file."<br />");
                }
            }
        }
        closedir($mydir);
    }


function insert_detail_copy($m_loading_id,$nomor_rekening,$nama_file,$nama_pdf, $flagtrans,$blth)
{
	$file_replace = str_replace(substr($GLOBALS['nama_file'],-7),'',$GLOBALS['nama_file']);
	$detail = 'detail_'.$GLOBALS['blth'].'_'.$GLOBALS['flagtrans'];
	$detail2 = 'detail_'.$GLOBALS['blth'].'_'.'mt';
	$nama_pdf = str_replace('.pdf','',$nama_pdf);
	
	$sql = "
	INSERT INTO $detail(
            m_loading_id, nomor_customer, nomor_rekening, nama, 
            alamat1, alamat2, alamat3, alamat4, alamat5, city, zipcode, flagtrans, 
            blth, nama_file, pdf_name, password_pdf, jml_hlm, flag_attach, 
            tipe_kartu, no_rek_asli, ket_produk, email, n_email, size_pdf, 
            barcode, nama_produk, tanggal, total_produk, kode_cab, cabang, 
            total_ematerai, jumlah_kartu_ematerai, tipe_proses_ematerai)
    select
	$m_loading_id, nomor_customer, nomor_rekening, nama, 
            alamat1, alamat2, alamat3, alamat4, alamat5, city, zipcode, '".$GLOBALS['flagtrans']."', 
            '".$GLOBALS['blth']."', '".$GLOBALS['nama_file']."', '$nama_pdf', password_pdf, jml_hlm, flag_attach, 
            tipe_kartu, no_rek_asli, ket_produk, email, n_email, size_pdf, 
            barcode, nama_produk, tanggal, total_produk, kode_cab, cabang, 
            total_ematerai, jumlah_kartu_ematerai, tipe_proses_ematerai
			from $detail2
			WHERE lower(nama_file) like lower('%$file_replace%')
			AND nomor_rekening = '$nomor_rekening' LIMIT 1
			";
			
	$qry_ins_detail = pg_query($sql) or die('ERROR insert into detail 2: '.$sql);
		if(pg_affected_rows($qry_ins_detail)==0){
			echo "GAGAL INSERT DETAIL UNTUK $nomor_rekening";
			die();
		}

}
	
function insert_detail($m_loading_id,$tgl_terminate,$detail,$nomor_customer,$nomor_rekening,
								$nama,$address1,$address2,
								$address3,$city,$zipcode,
								$flagtrans,$blth,$nama_file,
								$pdf_name,$password_pdf,$jml_hlm,$email,$barcode, $filesizePdf, $tipe_surat,
								$distribution_chanel_code, $hnw, $epol_stat, $cyle_date){
		#if(substr($nomor_rekening,0,4)=="4645") $tipe_kartunya = "LIQUID PLATINUM"; else $tipe_kartunya = "SOLID TITANIUM";	
		$tipe_kartunya = $epol_stat;
		$sql_ins_detail = "INSERT INTO $detail(
								m_loading_id,nomor_customer,nomor_rekening,
								nama,alamat1,alamat2,tanggal,
								alamat3,city,zipcode,
								flagtrans,blth,nama_file,
								pdf_name,password_pdf,jml_hlm,tipe_kartu,
								email,barcode,ket_produk
								,size_pdf, cabang, kode_cab
							)VALUES(
								$m_loading_id,'$nomor_customer','$nomor_rekening',
								E'".addslashes($nama)."', E'".addslashes($address1)."', E'".addslashes($address2)."','$tgl_terminate',
								E'".addslashes($address3)."','$city','$zipcode',
								'$flagtrans','$blth','$nama_file',
								'$pdf_name','$password_pdf',$jml_hlm, '$tipe_kartunya',
								E'".addslashes($email)."','$cyle_date', E'".addslashes($tipe_surat)."'
								,$filesizePdf, E'".addslashes($distribution_chanel_code)."', E'".addslashes($hnw)."'
							)";
							//die($sql_ins_detail);
		$qry_ins_detail = pg_query($sql_ins_detail) or die('ERROR insert into detail: '.$sql_ins_detail);
		if(pg_affected_rows($qry_ins_detail)==0){
			echo "GAGAL INSERT DETAIL UNTUK $nomor_rekening";
			die();
		}
	}
	
	
	function fsize($file){
		$size	= filesize($file);
		
		$size /= 1024;
		
		return round($size, 2);
		
		
		/*$a		= array("B", "KB", "MB", "GB", "TB", "PB");
		$pos	= 0;
		
		while($size >= 1024){
			$size /= 1024;
			$pos++;
		}
		
		return round($size, 2) . " " . $a[$pos];*/
	}
	
	
	function update_m_loading($m_loading_id,$total_hlm,$customer, $flagtrans, $blth){
		$tabel_detail = "detail_".$blth."_". $flagtrans;
		$sql_upd_m_load = "UPDATE m_loading
							SET
								total_halaman = (select sum(jml_hlm) as a FROM $tabel_detail WHERE m_loading_id='$m_loading_id'),
								total_customer = (select count(*) as b FROM $tabel_detail WHERE m_loading_id='$m_loading_id')
							WHERE m_loading_id::integer = $m_loading_id";
		$qry_upd_m_load = pg_query($sql_upd_m_load) or die('ERROR update m_loading: '.$sql_upd_m_load);
	}
	
	function create_csv($m_loading_id,$detail_db,$blth,$file,$flagtrans){
		#$csv = str_replace(substr($file,-4),'',$file);
		$csv = str_replace(array(".xlsx", ".XLSX"), "", $file);
		$csv = str_replace(array(".dat", ".DAT"), "", $file);
		//$csv = str_replace(array(".csv", ".CSV"), "", $file);
		//$csv = str_replace(array(".xls", ".XLS", ".xlsx", ".XLSX", ".txt", ".TXT"), "", $csv);
		//die($csv. " aaaa");
		
		$file_csv = $GLOBALS['dir_root'].'pdf/'.$flagtrans.'/'.$blth.'/'.$csv.'/'.$csv.'_pass.csv';
		$header = "'nomor_polis';'nama';'pdf_name';'password_pdf';".chr(13);
		$detail="";
		
		$sql = "SELECT nomor_rekening,nama,pdf_name,password_pdf FROM $detail_db WHERE m_loading_id = $m_loading_id";
		$qry = pg_query($sql) or die('ERROR: '.$sql);
		while($row = pg_fetch_array($qry)){
			$nomor_rekening = $row['nomor_rekening'];
			$nama = $row['nama'];
			$pdf_name = $row['pdf_name'];
			$password_pdf = $row['password_pdf']; 
			
			$detail .= "'".$nomor_rekening."';'".$nama."';'".$pdf_name."';'".$password_pdf."';".chr(13);
		}
		
		$open_file = fopen($file_csv,'w');
		fwrite($open_file,$header.$detail);
		fclose($open_file);
	}
	
	
	function create_report_convert($m_loading_id,$detail_db,$blth,$file,$flagtrans){
		#$csv = str_replace(substr($file,-4),'',$file);
		$csv = str_replace(array(".xlsx", ".XLSX"), "", $file);
		$csv = str_replace(array(".csv", ".CSV"), "", $file);
		$csv = str_replace(array(".xls", ".XLS", ".xlsx", ".XLSX", ".txt", ".TXT"), "", $csv);
		//die($csv. " aaaa");
		
		
		$sql = "INSERT INTO temp_csv_a1_status_summary(
            nomor_polis, status_cover, status_polis_page3, status_ketentuan, 
            status_spaj, status_combine, m_loading_id)
SELECT nomor_polis, status_cover, status_polis_page3, status_ketentuan, 
            status_spaj, status_combine, m_loading_id from temp_csv_a1_status WHERE m_loading_id = '$m_loading_id' ORDER BY id ASC
";
		$qry = pg_query($sql) or die('ERROR: '.$sql);
		
		
		$file_csv = $GLOBALS['dir_root'].'pdf/'.$flagtrans.'/'.$blth.'/'.$csv.'/'.$csv.'_report_convert.csv';
		$header = "'nomor_polis';'status_cover';'status_polis_page3';'status_ketentuan';'status_spaj';'status_combine';".chr(13);
		$detail="";
		
		$sql = "SELECT * FROM temp_csv_a1_status WHERE m_loading_id = '$m_loading_id' ORDER BY id ASC";
		$qry = pg_query($sql) or die('ERROR: '.$sql);
		while($row = pg_fetch_array($qry)){
			$nomor_polis = $row['nomor_polis'];
			$status_cover = $row['status_cover'];
			$status_polis_page3 = $row['status_polis_page3'];
			$status_ketentuan = $row['status_ketentuan'];
			$status_spaj = $row['status_spaj'];
			$status_combine = $row['status_combine'];
			
			$detail .= "'".$nomor_polis."';'".$status_cover."';'".$status_polis_page3."';'".$status_ketentuan."';'".$status_spaj."';'".$status_combine."';".chr(13);
		}
		
		$open_file = fopen($file_csv,'w');
		fwrite($open_file,$header.$detail);
		fclose($open_file);
	}
	
	function create_report_convert2($m_loading_id,$detail_db,$blth,$file,$flagtrans){
		#$csv = str_replace(substr($file,-4),'',$file);
		$csv = str_replace(array(".xlsx", ".XLSX"), "", $file);
		//$csv = str_replace(array(".csv", ".CSV"), "", $file);
		//$csv = str_replace(array(".xls", ".XLS", ".xlsx", ".XLSX", ".txt", ".TXT"), "", $csv);
		//die($csv. " aaaa");
		
		
		$sql = "INSERT INTO temp_csv_a1_status_summary(
            nomor_polis, status_cover, status_polis_page3, status_ketentuan, 
            status_spaj, status_combine, m_loading_id)
SELECT nomor_polis, status_cover, status_polis_page3, status_ketentuan, 
            status_spaj, status_combine, m_loading_id from temp_csv_a1_status WHERE m_loading_id = '$m_loading_id' ORDER BY id ASC
";
		$qry = pg_query($sql) or die('ERROR: '.$sql);
		
		
		$file_csv = $GLOBALS['dir_root'].'pdf/'.$flagtrans.'/'.$blth.'/'.$csv.'/'.$csv.'_report_convert_SUMMARY.txt';
		
			$header = "1. Total Polis Dengan Status estatement_ind = Y : ##total_polis## polis".chr(13);
			$header .= "2. Total File ELECTRONIC ADDENDUM: ##total_inject## polis".chr(13);
			$header .= "3. Total Missing File ELECTRONIC ADDENDUM: ##no_pdf## polis".chr(13);
			$header .= "4. Total Data Duplikat : ##total_duplikat## polis".chr(13);
			$header .= "5. Total Data Tanpa Email File ELECTRONIC ADDENDUM: ##no_email## polis dari ##total_inject## polis".chr(13);
			$header .= "6. Total Data Dengan Status estatement_ind = N : ##estatement_ind## polis dari ##total_all## polis".chr(13);
			
			$header .= "==duplikat==".chr(13);
			$header .= "============================================".chr(13);
			$header .= "Detail missing file ELECTRONIC ADDENDUM:".chr(13);
			$header .= "No Polis | Keterangan".chr(13);
			
			
		$detail="";
		$total_polis=0;
		$total_inject=0;
		$no_pdf=0;
		$total_duplikat=0;
		$total_duplikat_array=array();
		$total_duplikat_text='';
		$total_duplikat_text .= "============================================".chr(13);
		$total_duplikat_text .= "Detail Duplikat Nomor Polis file ELECTRONIC ADDENDUM:".chr(13);
		$total_duplikat_text .= "No Polis | Keterangan".chr(13);
		
		
		$sql = "SELECT * FROM 
						temp_csv_a1 where estatement_ind = 'Y'  AND status_a1 = 'duplikat' ";
		$qry = pg_query($sql) or die('ERROR: '.$sql);
		while($row = pg_fetch_array($qry)){
			$nomor_polis = $row['policy_no'];
			$total_duplikat++;
			
			if ( isset($total_duplikat_array[$nomor_polis]) )
			{
				$total_duplikat_array[$nomor_polis]  = $total_duplikat_array[$nomor_polis] +1;
			}else{
				$total_duplikat_array[$nomor_polis] = 1;
			}
			
			
			
		}
		
		
		if (!empty($total_duplikat_array))
		{
			foreach($total_duplikat_array as $key_d => $val_d) 
			{
				$total_duplikat_text .= "$key_d | total duplikat : $val_d kali".chr(13);
			}
		}
		
		
		
		
		
		$sql = "SELECT * FROM temp_csv_a1_status WHERE m_loading_id = '$m_loading_id' ORDER BY id ASC";
		$qry = pg_query($sql) or die('ERROR: '.$sql);
		while($row = pg_fetch_array($qry)){
			$nomor_polis = $row['nomor_polis'];
			$status_cover = $row['status_cover'];
			$status_polis_page3 = $row['status_polis_page3'];
			$status_ketentuan = $row['status_ketentuan'];
			$status_spaj = $row['status_spaj'];
			$status_combine = $row['status_combine'];
			
			if ( $status_cover == 'selesai') $total_inject++;
			if ( $status_cover == 'TIDAK ADA') 
			{
				$detail .= "$nomor_polis | File ELECTRONIC ADDENDUM tidak ada".chr(13);
				$no_pdf++;
			}
			//$detail .= "'".$nomor_polis."';'".$status_cover."';'".$status_polis_page3."';'".$status_ketentuan."';'".$status_spaj."';'".$status_combine."';".chr(13);
			
			$total_polis++;
		}
		
		
		$header = str_replace("##total_duplikat##", $total_duplikat ,$header);
		if ( $total_duplikat > 0){
			$header = str_replace("==duplikat==", $total_duplikat_text ,$header);
			$total_polis = $total_polis + $total_duplikat;
		}else{
			$header = str_replace("==duplikat==", '' ,$header);
		}
		
		$header = str_replace("##total_polis##", $total_polis ,$header);
		$header = str_replace("##total_inject##", $total_inject ,$header);
		$header = str_replace("##no_pdf##", $no_pdf ,$header);
		
		
		
		$sql = "SELECT count(*) as no_email FROM $detail_db WHERE m_loading_id = '$m_loading_id' AND email=''";
		$qry = pg_query($sql) or die('ERROR: '.$sql);
		while($row = pg_fetch_array($qry)){
			$no_email = $row['no_email'];
		}
		$header = str_replace("##no_email##", $no_email ,$header);
		
		
		$sql = "SELECT count(*) as estatement_ind FROM temp_csv_a1 WHERE m_loading_id = '$m_loading_id' AND estatement_ind!='Y'";
		$qry = pg_query($sql) or die('ERROR: '.$sql);
		while($row = pg_fetch_array($qry)){
			$estatement_ind = $row['estatement_ind'];
		}
		$header = str_replace("##estatement_ind##", $estatement_ind ,$header);
		
		$sql = "SELECT count(*) as total_all FROM temp_csv_a1 WHERE m_loading_id = '$m_loading_id' ";
		$qry = pg_query($sql) or die('ERROR: '.$sql);
		while($row = pg_fetch_array($qry)){
			$total_all = $row['total_all'];
		}
		$header = str_replace("##total_all##", $total_all ,$header);
		
		
		$open_file = fopen($file_csv,'w');
		fwrite($open_file,$header.$detail);
		fclose($open_file);
	}
	
	function masking_nomor_polis($nama_file)
	{
		$nama_file = str_replace(array(".pdf", ".PDF"), "", $nama_file);
		$var = substr_replace($nama_file, str_repeat("X", 3), -3); ///masking XXX di belakang
		//$var = substr_replace($nama_file, str_repeat("X", 3), 0,3); ///masking XXX di depan
		return $var.'.pdf';
	}
	

	
	function cek_nama_pdf_sudah_ada($m_loading_id,$flagtrans,$blth,$nama_pdf){
		$temp = $detail = 'detail_'.$blth.'_'.$flagtrans;
		$sql		= "SELECT * FROM ".$temp." WHERE pdf_name like '%$nama_pdf%' AND m_loading_id::integer=$m_loading_id";
		$query 		= pg_query($sql);
		$n_row 		= pg_num_rows($query);
		//die ( $sql );
		if($n_row > 0)
		{
			return '_'.$n_row;
		}else{
			return '';
		}
	}
	
	
	function buat_csv_noexist($nama_file,$isi, $f)
	{
		
		$dir = $GLOBALS['dir_root'].'pdf/';
		
		if(!is_dir($dir)) mkdir($dir,0777);
		$dir .= $GLOBALS['flagtrans'].'/';
		if(!is_dir($dir)) mkdir($dir,0777);
		$dir .= $GLOBALS['blth'] .'/';
		if(!is_dir($dir)) mkdir($dir,0777);
		$dir .= $GLOBALS['nama_file_plain'].'/';
		if(!is_dir($dir)) mkdir($dir,0777);
		$file_txt = $dir. $f."_" .$GLOBALS['nama_file_plain'].".txt";
		
		
			$file_csv = $file_txt;
			#$header = "NO_POLIS;FILE_PDF_MASTER;FILE_PDF_DI_DATA;".chr(13);
			
			$open_file = fopen($file_csv,'a');
			#fwrite($open_file,$header.$isi);
			fwrite($open_file,$isi);
			fclose($open_file);
			
	}
	
	
	
	function cetak_pdf_password( $pdf_in, $pdf_out, $password='')
	{
		//require_once($GLOBALS['dir_root'].'include/tcpdf/config/lang/eng.php');
		//require_once($GLOBALS['dir_root'].'include/tcpdf/tcpdf.php');
		//require_once($GLOBALS['dir_root'].'include/FPDI/fpdi.php');

				
		$var_c='@ppD3v#';
		//$var_c= random_numbers(8);
		
		$pdf = new FPDI('P', 'mm', 'A4'); //FPDI extends TCPDF

		$pages = $pdf->setSourceFile( $pdf_in );
		//die($pdf_in.' -- '.$pages);
		/*
		for ($i = 1; $i <= $pages; $i++) 
		{
			$pdf->AddPage();
			$page = $pdf->ImportPage($i);
			$pdf->useTemplate($page, 0, 0);
		}

		$pdf->SetProtection($permissions=array('modify','copy','assemble'), $password, $var_c, $mode=3, $pubkeys=null);
		$pdf->Output( $pdf_out ,'F');
		
		return $i;
		*/
		
		$command = 'C:/pdftk/bin/pdftk ' . $pdf_in . ' output ' . $pdf_out . ' allow Printing owner_pw ' . $var_c . ' user_pw ' . $password;
		exec($command);
					
		return $pages;
		
	}
	
	
	function get_nama_pdf($nomor_rekening)
	{
		$x = array();
		$sql		= "SELECT * FROM master_proses_after_ematerai_file WHERE lower(nama_file) like lower('%".$nomor_rekening."%') LIMIT 1";
		//die($sql);
		$query 		= pg_query($sql);
		$n_row 		= pg_num_rows($query);
		if($n_row > 0)
		{
			
			while($row = @pg_fetch_array($query))
			{
				$x['direktori'] = $row['direktori'];
				$x['nama_file'] = $row['nama_file'];
			}
			return $x;
		}
		
	}
	
	function get_urutan_no_pdf_ematerai()
	{
		$x = array();
		$sql		= "SELECT urutan FROM _urutan_no_pdf_ematerai WHERE
		id::integer = 1
		LIMIT 1";
		//die($sql);
		$query 		= pg_query($sql);
		$n_row 		= pg_num_rows($query);
		$x = 0;
		if($n_row > 0)
		{
			
			while($row = @pg_fetch_array($query))
			{
				$x = $row['urutan'];
			}
			return $x;
		}
		
	}
	
	
	function update_urutan_no_pdf_ematerai($x)
	{
		//$x = array();
		$sql		= "UPDATE _urutan_no_pdf_ematerai
				   SET urutan=$x
				 WHERE id::integer = 1";
		//die($sql);
		$query 		= pg_query($sql);
		
	}
	
	

@pg_close($con);
?>