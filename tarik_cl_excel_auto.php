<?php

//die ( date('m/d/Y', strtotime('8 Aug 2015')) );
//die ( date('m/d/Y') );
// Report all errors except E_NOTICE
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('memory_limit', '-1');


$timezone="Asia/Jakarta";
date_default_timezone_set($timezone);
include $www_location."include/config.php";
include $www_location."include/phpexcel/PHPExcel.php";
include $www_location."include/phpexcel/PHPExcel/IOFactory.php";
$_SESSION['userid']='otomatis';


$con = pg_connect($connection) or die("Could not connect to database!");

class tarik_sof
{
	public $jeda_hari	= 30;
	public $jeda_hari2	= 1;
	public $jeda_hari_dari_log_approval	= 3;
	public $direktori	= "C:/App/htdocs/e_statement_bri"; //live
	public $direktori_sftp	= "E_STATEMENT_BRI"; //live
	//public $direktori = "F:/AppDev/Public/www/e_statement_sunlife"; //testing
	#public $direktori = "F:/AppDev/Public/www/e_statement_uob_x"; //testing
	

	public function cek_file ()
	{
		$pesan=array();
		$sql="SELECT a.m_loading_id, a.approval_date ,  b.m_loading_id, b.flagtrans, b.blth, b.loading_file
		,DATE_PART('day', now()::timestamp - a.approval_date::timestamp)as jeda_hari 
				FROM log_approval a
				INNER JOIN m_loading b ON a.m_loading_id=b.m_loading_id
				WHERE a.approval_date between ( (now() - interval '".$this->jeda_hari." days')::date || ' 00:00:00' )::date
				AND ( (now() + interval '".$this->jeda_hari2." days')::date || ' 23:59:59' )::date
				AND a.create_date IS NOT NULL 
				AND a.approval_date IS NOT NULL 
				AND a.approval_user != 'otomatis' 
				AND a.note_check != 'sample only'
				AND b.status_report_sftp_excel IS NULL
				AND b.flagtrans !='XX'
				--AND b.blth = '082022'
				--AND a.m_loading_id = 5056
				AND DATE_PART('day', now()::timestamp - a.approval_date::timestamp) >=".$this->jeda_hari_dari_log_approval."
				ORDER BY a.approval_date ASC
				LIMIT 5";
			//die ($sql);
			$query = pg_query($sql) or die("Invalid query! auto SOF" . $sql) ;
			$rows = pg_num_rows($query);
			while($row = pg_fetch_array($query))
			{				
				$pesan[ $row['m_loading_id'].'--'.$row['loading_file'].'_report' ] = $this->excel_auto($con, trim($row['m_loading_id']) , trim($row['blth']) , trim($row['flagtrans']) , 'all');
				
				$sql_error_info = "UPDATE m_loading 
							SET waktu_status_report_sftp_excel=now(),
							status_report_sftp_excel='t'  
							WHERE m_loading_id =".trim($row['m_loading_id'])."
							AND blth='".trim($row['blth'])."' 
							AND flagtrans='".trim($row['flagtrans'])."'";
							#die($sql_error_info);
							@pg_query($sql_error_info);
			}
	
		return $pesan;
		
		
	}
	
	protected function excel_auto($con,$m_loading_id, $blth, $flagtrans, $tipe='')
	{
	
			$tipe_gagal='';
			
			$queri_1 = "SELECT loading_file, flagtrans, blth FROM m_loading WHERE m_loading_id = '$m_loading_id' ";
			$qry_l = pg_query($queri_1) or die('ERROR select: '.$queri_1);
			$row_l = pg_fetch_assoc($qry_l);
			//$nama_file_loading = str_replace(array(".TXT",".txt",".zip", ".ZIP", ".csv", ".CSV"),"",$row_l['loading_file']).'.txt';
			$nama_file_loading = str_replace(array(".TXT",".txt",".zip", ".ZIP", ".csv", ".CSV"),"",$row_l['loading_file']).'.XLSX';
			
			
			
			$nama_file_loading = "REPORT_".$nama_file_loading;
			//die($tipe_gagal);
			
	
			$blth_balik = substr($blth,-4).substr($blth,0,2);
		
			//$BLTH_BARU = BLTH_BARU;
			$tabeldetail = "detail_" . $blth."_".$flagtrans;
			$tr_email_new = "tr_email_" . $blth."_".$flagtrans;
			#$tr_email_new = "tr_email";
			
			
		/*	
			if ( $flagtrans == 'OO' || $flagtrans == 'PK' )
		{
			$condv = "WHERE a.m_loading_id = '$m_loading_id' AND a.status='t' ";
		}else{
			$condv = "WHERE a.m_loading_id = '$m_loading_id'  ";
		}
		*/
		
		$condv = "WHERE a.m_loading_id = '$m_loading_id'  ";
		
		
$sQuery = "

select * FROM (
SELECT  					z.produk, a.loading_file as nama_file, b.pdf_name, 
									b.barcode AS document_key, to_char(a.create_date, 'dd Mon yyyy HH24:MI') AS create_date,
									b.nama, 
									d.antrian_id,
									CASE 
									WHEN c.email is not null and d.date_email_send is null THEN 'QUEUE' 
									WHEN d.email is not null and d.date_email_callback is null THEN 'SUCCESS' 
									WHEN d.email is not null and d.date_email_callback is not null THEN 'FAILED' ELSE 'NO STATUS' END AS delivery_status, 
									b.nomor_rekening AS policy_number,
									d.email AS email_address,
									c.email AS email_queue,
									c.tgl_antrian AS queue_date,
									to_char(c.tgl_antrian, 'YYYY-MM-DD HH24:MI') AS queue_datex,
									(SELECT e.tgl_jadwal FROM m_jadwal e WHERE e.jadwal_id=c.jadwal_id)AS send_schedule,
									to_char(d.date_email_send, 'dd Mon yyyy HH24:MI') AS send_datex, 
									d.date_email_send AS send_date, 
									to_char(d.date_email_send, 'YYYY-MM-DD HH24:MI') AS send_datex ,
									d.ket_error AS error_message
									,d.tipe_gagal AS error_type
									,to_char(d.tgl_read, 'YYYY-MM-DD HH24:MI') AS date_readx 
									,d.tgl_read AS date_read
									,to_char(d.date_email_callback, 'YYYY-MM-DD HH24:MI') AS date_email_callbackx
									,d.date_email_callback AS date_email_callback
									,d.body_email_read AS read_message
									,b.tanggal
									,c.error_info as error_info_queue
									,c.tgl_error_info as tgl_error_info_queue
									,to_char(c.tgl_error_info, 'YYYY-MM-DD HH24:MI') AS tgl_error_info_queuex 
									, b.size_pdf
									,d.read_method
									,RIGHT(d.tgl_read2, 6) as timezone_x
									,b.blth
									FROM m_loading a 
									LEFT JOIN $tabeldetail b ON a.m_loading_id = b.m_loading_id 
									LEFT JOIN mproduk z ON a.flagtrans = z.flagtrans 
									LEFT JOIN antrian_email c ON a.m_loading_id = c.loading_id and b.nomor_rekening = c.nomor_rekening and (c.status_sample = 'f' or c.status_sample is null) 
									LEFT JOIN $tr_email_new d ON a.m_loading_id = d.loading_id and b.nomor_rekening = d.nomor_rekening 
									and (d.status_sample = 'f' or d.status_sample is null) 
									
									$condv 
									$tipe_gagal
									ORDER BY a.create_date DESC
									
									) ZZ ORDER BY delivery_status DESC, send_date ASC" ;
		//die($sQuery);
		$rs = pg_query($sQuery) or die("Invalid query!" . $sQuery) ;
		$rows = pg_num_rows($rs);
		
				
				
				ini_set('memory_limit', '-1');
				$objPHPExcel = new PHPExcel();
				$sheet_x = 0;
				$flag_sheet='';
				
				
				
				$styleArray = array(
					'font' => array(
						'name'         => 'Arial',
						'bold'         => true,
						'italic'    => false,
						'size'        => 9
					),
					'borders' => array(
					'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
					'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
					'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
					'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
				),
					'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
						'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
						'wrap'       => true
					)
				);
				
				
					$styleArray_detail = array(
					'font' => array(
						'name'         => 'Arial',
						'bold'         => false,
						'italic'    => false,
						'size'        => 9
					),
					'borders' => array(
					'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
					'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
					'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
					'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
				),
					'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
						'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
						'wrap'       => true
					)
				);
		
		
				
				while($hasil = pg_fetch_array($rs))
				{
					
					
					////////////////////////////////////////////////////////////////
					//report_excel_sftp
					$folder = $this->direktori.'/tmp/report_excel_sftp/'.$hasil['produk'];
					#$folder = $this->direktori.'/tmp/sof_auto_test';
					if(file_exists($folder)==false) mkdir($folder);
					
					
					$folder = $folder.'/'.$hasil['blth'];
					#$folder = $this->direktori.'/tmp/sof_auto_test';
					if(file_exists($folder)==false) mkdir($folder);
					
					
					$filename = $folder.'/'.$nama_file_loading;
					#$filename = $this->direktori.'/tmp/sof_auto_test/'.$nama_file_loading;
					////////////////////////////////////////////////////////////////
					$delivery_status = trim($hasil['delivery_status']);
					
					if ( $flag_sheet != $delivery_status )
					{
						$no_baris = 0;
						$flag_sheet = $delivery_status;
						
						
						$produk =$hasil['produk'];
						$blth = $hasil['blth'];
						
						
						
						//if ($delivery_status=='') continue;
						
						$F = $objPHPExcel->createSheet();
						$F = $objPHPExcel->setActiveSheetIndex($sheet_x);
						$F->setTitle($delivery_status);
						
						//$F = $objPHPExcel->createSheet($sheet_x);
						//$F->setTitle($delivery_status);
						
						//$F = $objPHPExcel->createSheet()->setTitle($delivery_status.$sheet_x);
						//$F = $objPHPExcel->setActiveSheetIndex($sheet_x);
						//$F=$objPHPExcel->getActiveSheet($sheet_x);
						//$objPHPExcel->getActiveSheet()->setTitle($delivery_status);
						//$F->setTitle($delivery_status);
						
						$Line = 1;
						$F = $this->sheet_header($F,$styleArray);
						/*
						if (strtolower( $delivery_status ) == strtolower('SUCCESS') ) 
						{
							//antrian
							$F = $this->sheet_success_header($F,$styleArray);
						}else if (strtolower( $delivery_status ) == strtolower('QUEUE') ) {
							$F = $this->sheet_queue_header($F,$styleArray);
						}
						*/
						$sheet_x++;
					}
					
					////////////////////////////////////////////////////////////
					//cek total sum attachment size
					$total_size_estat = floor($hasil['size_pdf']);
					if ( $hasil['delivery_status'] > 0)
					{
						$sql_att = "SELECT aeaf.antrian_id , SUM(maf.ukuran)AS total_attach FROM 
						antrian_email_attach_file aeaf
						LEFT m_attach_file maf ON aeaf.m_attach_file_id = maf.m_attach_file_id 
						WHERE aeaf.antrian_id = ".$hasil['antrian_id']." GROUP BY aeaf.antrian_id";
						$result_att = pg_query($sql_att) or die("Invalid query!" . $sql_att) ;
						while($hasil_att = pg_fetch_array($result_att))
						{
							$total_size_attachment = floor($hasil_att['total_attach']/1024);
					
						}
			
						
						
						$total_semua_attachment = $total_size_estat+$total_size_attachment;
						$sizeberbayar = ceil($total_semua_attachment/300);
					}else{
						
						$total_size_attachment = 0;
						$total_semua_attachment = $total_size_estat+$total_size_attachment;
						$sizeberbayar = ceil($total_semua_attachment/300);
					}
					
					////////////////////////////////////////////////////////////
					
					
					$a = str_replace("\n","", $hasil['read_message']);
					$b = str_replace(chr(13),"", $a);
					$hasil['read_message']= preg_replace('/<[^>]*>/', '', $b);
					
					$a = str_replace("\n","", $hasil['error_message']);
					$b = str_replace(chr(13),"", $a);
					$hasil['error_message']= preg_replace('/<[^>]*>/', '', $b);
					
					$a = str_replace("\n","", $hasil['error_info_queue']);
					$b = str_replace(chr(13),"", $a);
					$hasil['error_info_queue']= preg_replace('/<[^>]*>/', '', $b);
					
					$no_baris++;
					if (strtolower( $delivery_status ) == strtolower('SUCCESS') ) 
					{
						$F = $this->sheet_success_detail($F,$styleArray_detail,$hasil,$no_baris, $total_size_estat, $total_size_attachment, $total_semua_attachment, $sizeberbayar);
					}else if (strtolower( $delivery_status ) == strtolower('FAILED') ) {
						$F = $this->sheet_failed_detail($F,$styleArray_detail,$hasil,$no_baris, $total_size_estat, $total_size_attachment, $total_semua_attachment, $sizeberbayar);
					}else if (strtolower( $delivery_status ) == strtolower('QUEUE') ) {
						$F = $this->sheet_queue_detail($F,$styleArray_detail,$hasil,$no_baris, $total_size_estat, $total_size_attachment, $total_semua_attachment, $sizeberbayar);
					}else{
						$F = $this->sheet_no_status_detail($F,$styleArray_detail,$hasil,$no_baris, $total_size_estat, $total_size_attachment, $total_semua_attachment, $sizeberbayar);
					}
					
					
		
				}
				
				if ( file_exists($filename) ) 
				{
					unlink($filename);
				}	
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				#$objWriter->save('php://output');
				#$objWriter->save($GLOBALS['direktori_pdf']."list_data_'.$m_loading_id.'_'.$loading_file .'.xls");
				//die("$filename");
				$objWriter->save($filename);
				//die("export excel");
				
				
				
				
				#$n=  $this->TransferSftpDeliveryReport($this->direktori.'/tmp/sof_auto/',$nama_file_loading,$filename2, $flagtrans);
				$n=  $this->kirim_ke_ftp($this->direktori_sftp, $produk, $blth, $filename, '', $nama_file_loading);
				return $n;
			
	}
	
	
	protected function sheet_header($F,$styleArray)
	{
		//No	Nama File	Nomor Rekening	Nama	Email	Tanggal Kirim	Tanggal Read	Pesan Read	Tipe Read	PDF Size	Attachment Size/Brosur	Total Size	Berbayar

		$Line = 1;
		
		$F = $this->jarak_kolom($F);
		
		
		
		
		$F->setCellValueExplicit('A' . $Line, "No", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('A' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('B' . $Line, "Nama File", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('B' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('C' . $Line, "Nomor Rekening", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('C' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('D' . $Line, "Nama", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('D' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('E' . $Line, "Email", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('E' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('F' . $Line, "Nama PDF", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('F' . $Line)->applyFromArray($styleArray);
		
		//antrian
		$F->setCellValueExplicit('G' . $Line, "Tanggal Antrian", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('G' . $Line)->applyFromArray($styleArray);
		
		//terbaca
		$F->setCellValueExplicit('H' . $Line, "Tanggal Kirim", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('H' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('I' . $Line, "Tanggal Terbaca", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('I' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('J' . $Line, "Pesan Terbaca", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('J' . $Line)->applyFromArray($styleArray);
	
		//gagal
		$F->setCellValueExplicit('K' . $Line, "Tanggal Gagal", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('K' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('L' . $Line, "Pesan Gagal", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('L' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('M' . $Line, "Klasifikasi Gagal", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('M' . $Line)->applyFromArray($styleArray);
		
		
		
		$F->setCellValueExplicit('N' . $Line, "PDF Size", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('N' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('O' . $Line, "Attachment Size/Brosur", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('O' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('P' . $Line, "Total Size", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('P' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('Q' . $Line, "Berbayar", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('Q' . $Line)->applyFromArray($styleArray);
		
		$F->setCellValueExplicit('R' . $Line, "Status Reject Antrian", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('R' . $Line)->applyFromArray($styleArray);
		
		$F->getStyle('A' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('B' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('C' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('D' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('E' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('F' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('G' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('H' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('I' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('J' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('K' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('L' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('M' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('N' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('O' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('P' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('Q' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('R' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		
		return $F;
		
	}
	
	protected function sheet_success_detail($F,$styleArray,$row,$no_baris, $total_size_estat, $total_size_attachment, $total_semua_attachment, $sizeberbayar)
	{
		//No	Nama File	Nomor Rekening	Nama	Email	Tanggal Kirim	Tanggal Read	Pesan Read	Tipe Read	PDF Size	Attachment Size/Brosur	Total Size	Berbayar

				
				
		$Line = ($no_baris+1);
		
		
		$F = $this->jarak_kolom($F);
		
		$F->setCellValueExplicit('A' . $Line, $no_baris, PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('A' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('B' . $Line, $row['nama_file'], PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('B' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('C' . $Line, $row['policy_number'], PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('C' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('D' . $Line, $row['nama'], PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('D' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('E' . $Line, $row['email_address'], PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('E' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('F' . $Line, $row['pdf_name'].'.pdf', PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('F' . $Line)->applyFromArray($styleArray);
		
		
		//antrian
		$F->setCellValueExplicit('G' . $Line, "-", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('G' . $Line)->applyFromArray($styleArray);
		
		//sukses
		$F->setCellValueExplicit('H' . $Line, $row['send_datex'], PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('H' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('I' . $Line, $row['date_readx'] .' '.$row['timezone_x'], PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('I' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('J' . $Line, $row['read_message'], PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('J' . $Line)->applyFromArray($styleArray);
		
		//gagal
		$F->setCellValueExplicit('K' . $Line, "-", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('K' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('L' . $Line, "-", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('L' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('M' . $Line, "-", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('M' . $Line)->applyFromArray($styleArray);
		
		
		
		$F->setCellValueExplicit('N' . $Line, $total_size_estat, PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('N' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('O' . $Line, $total_size_attachment, PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('O' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('P' . $Line, $total_semua_attachment, PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('P' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('Q' . $Line, $sizeberbayar, PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('Q' . $Line)->applyFromArray($styleArray);
		
		$F->setCellValueExplicit('R' . $Line, "-", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('R' . $Line)->applyFromArray($styleArray);
		
		$F->getStyle('A' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('B' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('C' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('D' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('E' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('F' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('G' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('H' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('I' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('J' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('K' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('L' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('M' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('N' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('O' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('P' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('Q' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('R' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		
		return $F;
		
	}
	
	protected function sheet_failed_detail($F,$styleArray,$row,$no_baris, $total_size_estat, $total_size_attachment, $total_semua_attachment, $sizeberbayar)
	{
		//No	Nama File	Nomor Rekening	Nama	Email	Tanggal Kirim	Tanggal Read	Pesan Read	Tipe Read	PDF Size	Attachment Size/Brosur	Total Size	Berbayar

				
				
		$Line = ($no_baris+1);
		
		
		$F = $this->jarak_kolom($F);
		
		$F->setCellValueExplicit('A' . $Line, $no_baris, PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('A' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('B' . $Line, $row['nama_file'], PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('B' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('C' . $Line, $row['policy_number'], PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('C' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('D' . $Line, $row['nama'], PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('D' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('E' . $Line, $row['email_address'], PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('E' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('F' . $Line, $row['pdf_name'].'.pdf', PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('F' . $Line)->applyFromArray($styleArray);
		
		
		//antrian
		$F->setCellValueExplicit('G' . $Line, "-", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('G' . $Line)->applyFromArray($styleArray);
		
		//sukses
		$F->setCellValueExplicit('H' . $Line, "-", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('H' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('I' . $Line, "-", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('I' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('J' . $Line, "-", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('J' . $Line)->applyFromArray($styleArray);
		
		//gagal
		$F->setCellValueExplicit('K' . $Line, $row['date_email_callbackx'], PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('K' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('L' . $Line, $row['error_message'], PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('L' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('M' . $Line, $row['error_type'], PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('M' . $Line)->applyFromArray($styleArray);
		
		
		
		$F->setCellValueExplicit('N' . $Line, $total_size_estat, PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('N' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('O' . $Line, $total_size_attachment, PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('O' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('P' . $Line, $total_semua_attachment, PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('P' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('Q' . $Line, $sizeberbayar, PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('Q' . $Line)->applyFromArray($styleArray);
		
		$F->setCellValueExplicit('R' . $Line, "-", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('R' . $Line)->applyFromArray($styleArray);
		
		$F->getStyle('A' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('B' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('C' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('D' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('E' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('F' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('G' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('H' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('I' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('J' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('K' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('L' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('M' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('N' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('O' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('P' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('Q' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('R' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		
		
		///$F->getRowDimension('A')->setRowHeight(300);
		
		return $F;
		
	}
	
	
	protected function sheet_queue_detail($F,$styleArray,$row,$no_baris, $total_size_estat, $total_size_attachment, $total_semua_attachment, $sizeberbayar)
	{
		//No	Nama File	Nomor Rekening	Nama	Email	Tanggal Kirim	Tanggal Read	Pesan Read	Tipe Read	PDF Size	Attachment Size/Brosur	Total Size	Berbayar

				
				
		$Line = ($no_baris+1);
		
		
		$F = $this->jarak_kolom($F);
		
		$F->setCellValueExplicit('A' . $Line, $no_baris, PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('A' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('B' . $Line, $row['nama_file'], PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('B' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('C' . $Line, $row['policy_number'], PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('C' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('D' . $Line, $row['nama'], PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('D' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('E' . $Line, $row['email_queue'], PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('E' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('F' . $Line, $row['pdf_name'].'.pdf', PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('F' . $Line)->applyFromArray($styleArray);
		
		
		//antrian
		$F->setCellValueExplicit('G' . $Line, $row['queue_datex'], PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('G' . $Line)->applyFromArray($styleArray);
		
		//sukses
		$F->setCellValueExplicit('H' . $Line, "-", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('H' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('I' . $Line, "-", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('I' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('J' . $Line, "-", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('J' . $Line)->applyFromArray($styleArray);
		
		//gagal
		$F->setCellValueExplicit('K' . $Line, "-", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('K' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('L' . $Line, "-", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('L' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('M' . $Line, "-", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('M' . $Line)->applyFromArray($styleArray);
		
		
		
		$F->setCellValueExplicit('N' . $Line, $total_size_estat, PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('N' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('O' . $Line, $total_size_attachment, PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('O' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('P' . $Line, $total_semua_attachment, PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('P' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('Q' . $Line, $sizeberbayar, PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('Q' . $Line)->applyFromArray($styleArray);
		
		$F->setCellValueExplicit('R' . $Line, $row['error_info_queue'], PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('R' . $Line)->applyFromArray($styleArray);
		
		$F->getStyle('A' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('B' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('C' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('D' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('E' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('F' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('G' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('H' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('I' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('J' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('K' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('L' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('M' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('N' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('O' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('P' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('Q' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('R' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		
		return $F;
		
	}
	
	protected function sheet_no_status_detail($F,$styleArray,$row,$no_baris, $total_size_estat, $total_size_attachment, $total_semua_attachment, $sizeberbayar)
	{
		//No	Nama File	Nomor Rekening	Nama	Email	Tanggal Kirim	Tanggal Read	Pesan Read	Tipe Read	PDF Size	Attachment Size/Brosur	Total Size	Berbayar

				
				
		$Line = ($no_baris+1);
		
		$F = $this->jarak_kolom($F);
		
		$F->setCellValueExplicit('A' . $Line, $no_baris, PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('A' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('B' . $Line, $row['nama_file'], PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('B' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('C' . $Line, $row['policy_number'], PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('C' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('D' . $Line, $row['nama'], PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('D' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('E' . $Line, $row['email_address'], PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('E' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('F' . $Line, $row['pdf_name'].'.pdf', PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('F' . $Line)->applyFromArray($styleArray);
		
		
		//antrian
		$F->setCellValueExplicit('G' . $Line, "-", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('G' . $Line)->applyFromArray($styleArray);
		
		//sukses
		$F->setCellValueExplicit('H' . $Line, "-", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('H' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('I' . $Line, "-", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('I' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('J' . $Line, "-", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('J' . $Line)->applyFromArray($styleArray);
		
		//gagal
		$F->setCellValueExplicit('K' . $Line, "-", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('K' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('L' . $Line, "-", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('L' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('M' . $Line, "-", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('M' . $Line)->applyFromArray($styleArray);
		
		
		
		$F->setCellValueExplicit('N' . $Line, $total_size_estat, PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('N' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('O' . $Line, $total_size_attachment, PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('O' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('P' . $Line, $total_semua_attachment, PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('P' . $Line)->applyFromArray($styleArray);
		$F->setCellValueExplicit('Q' . $Line, $sizeberbayar, PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('Q' . $Line)->applyFromArray($styleArray);
		
		$F->setCellValueExplicit('R' . $Line, "-", PHPExcel_Cell_DataType::TYPE_STRING)->getStyle('R' . $Line)->applyFromArray($styleArray);
		
		$F->getStyle('A' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('B' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('C' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('D' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('E' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('F' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('G' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$F->getStyle('H' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('I' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('J' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('K' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('L' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('M' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('N' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('O' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('P' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('Q' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		$F->getStyle('R' . $Line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);	
		
		return $F;
		
	}
	
	
	protected function jarak_kolom($F)
	{
		
		$F->getColumnDimension('A')->setWidth(10);
		$F->getColumnDimension('B')->setWidth(30);
		$F->getColumnDimension('C')->setWidth(30);
		$F->getColumnDimension('D')->setWidth(40);
		$F->getColumnDimension('E')->setWidth(50);
		$F->getColumnDimension('F')->setWidth(50);
		$F->getColumnDimension('G')->setWidth(25);
		$F->getColumnDimension('H')->setWidth(25);
		$F->getColumnDimension('I')->setWidth(25);
		$F->getColumnDimension('J')->setWidth(20);
		$F->getColumnDimension('K')->setWidth(25);
		$F->getColumnDimension('L')->setWidth(20);
		$F->getColumnDimension('M')->setWidth(50);
		$F->getColumnDimension('N')->setWidth(20);
		$F->getColumnDimension('O')->setWidth(20);
		$F->getColumnDimension('P')->setWidth(20);
		$F->getColumnDimension('Q')->setWidth(20);
		$F->getColumnDimension('R')->setWidth(20);
		
		return $F;
	}
	
	
	
	
	
	
	
	
	
	
	protected function kirim_ke_ftp($tipe_estat,$produk, $blth, $dir_input_file, $dir_output_file, $nama_file)
	{
		//die("$nama_file, $dir_input_file,");
		$ftp_server = "192.168.2.206";
		$ftp_conn = ftp_connect($ftp_server,21) or die("Could not connect to $ftp_server");
		$login = ftp_login($ftp_conn, 'user_report_estat', 'appdev321#');

		ftp_mkdir($ftp_conn, $tipe_estat);
		ftp_chdir($ftp_conn, $tipe_estat);
		ftp_mkdir($ftp_conn, $produk);
		ftp_chdir($ftp_conn, $produk);
		ftp_mkdir($ftp_conn, $blth);
		ftp_chdir($ftp_conn, $blth);

		// upload file
		if (ftp_put($ftp_conn, $nama_file, $dir_input_file, FTP_BINARY))
		  {
		  $xyz = "Successfully uploaded $file.";
		  }
		else
		  {
		   $xyz =  "Error uploading $file.";
		  }

		// close connection
		ftp_close($ftp_conn);
		
		return $xyz;
	
	}
	
	
	
	
	
	protected function kirim_ke_sftp($produk, $blth, $dir_input_file, $dir_output_file, $nama_file)
	{
		
		set_include_path(get_include_path() . PATH_SEPARATOR . $this->direktori.'/include/phpseclib0.3.0/');	
		include_once('Net/SFTP.php');
		
		$sftp = new Net_SFTP('192.168.2.206',21);
		if (!$sftp->login('user_report_estat', 'appdev321#')) 
		{
			exit('Login Failed');
		} 

		$sftp->mkdir($produk);
		$sftp->chdir($produk);
		$sftp->mkdir($blth);
		$sftp->chdir($blth);
		//$sftp->chdir('..');
		//$sftp->chdir('..');
		
		//$sftp->put('filename.remote', 'filename.local', NET_SFTP_LOCAL_FILE);
		if ( file_exists($dir_input_file) ) 
		{
			$sftp->put($nama_file, $dir_input_file, NET_SFTP_LOCAL_FILE);
			return 'transfer SFTP Berhasil';
		}else{
			return 'transfer SFTP GAGAL';
		}
		
		
		
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}


			
	
	
	
	
	$tarik_sof = new tarik_sof();
	$n=$tarik_sof->cek_file();
	$x = (empty($n) ? 'data kosong': $n );
	echo '<pre>';
	print_r($n);
	echo '</pre>';




pg_close($con);
?>