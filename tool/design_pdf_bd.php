<?php
	//////////////////////////////////////
	//halaman pertama					//
	//	nomor rekening dan nomor cif	//
	//	tanggal periode					//
	//	portofolio investasi			//
	//		header portofolio			//
	//		detail portofolio			//
	//		total portofolio			//
	//	akumulasi dana					//
	//		header akumulasi dana		//
	//		detail akumulasi dana		//
	//	rincian transaksi				//
	//		header rincian transaksi	//
	//		detail rincian transaksi	//
	//halaman kedua dan seterusnya		//
	//////////////////////////////////////
	
	
	
	/*halaman pertama*/
	/*password pdf*/
	$row_cek_m_customer = pg_fetch_array($qry_cek_m_customer);
	$password_pdf = $row_cek_m_customer['password_pdf'];
	//$dua_digit = substr($nomor_kunci,6,2);
	//$password_pdf = $dua_digit.$tgl_lahir;
	/*end of password pdf*/
	
	//$pdf = new FPDI_Protection('P','mm',array(216,304.79999999));
	$pdf = new FPDI_Protection('P','mm',array(216,280));
	$pdf->SetAutoPageBreak(true, 7);
	$pdf->SetDisplayMode("real");
	$pdf->SetLeftMargin(1);
	$pdf->SetRightMargin(1);
	$pdf->SetTopMargin(12);
	$pdf->AddFont('BRICARD02','','BRICARD02.php');
	$pdf->AddFont('C39P24DhTt','','C39P24DhTt.php');
	$pdf->AddFont('Helvetica-Condensed-Light-Li','','Helvetica-Condensed-Light-Li.php');
	$pdf->SetProtection(array("print"), $password_pdf, "appdev_bridplk");
	
	addpage($pdf,$x,$y,$ximage,$yimage,$url_img,$cus_name,$cus_adrs,$cus_city,$zip_code,$bartt,$dat_file,$nourut,$hlm,$jml_hlm);
	
	/*nomor rekening dan nomor cif*/
	$x_norek = $x + 90;
	$y_norek = $y + 3;
	//rectfill($pdf,$fillcolor,$drawcolor,$x_awal,$y_awal,$lebar,$tinggi,$flag);
	rectfill($pdf,0,0,$x_norek,$y_norek,95,28,'D');
	
	$pdf->SetFont('BRICARD02','',9);
	
	$pdf->SetXY($x_norek,$y_norek+1);
	$pdf->Cell(40,4,'No. Rekening Britama',0,0,'L');
	$pdf->Cell(55,4,": ".$rek_code[0],0,1,'L');
	
	$pdf->SetX($x_norek);
	$pdf->Cell(40,4,'No. CIF / Identitas',0,0,'L');
	$pdf->Cell(55,4,": ".$cif_code,0,1,'L');
	
	$pdf->SetX($x_norek);
	$pdf->Cell(40,4,'Tanggal Lahir',0,0,'L');
	$pdf->Cell(55,4,": ".$tgl_lahr,0,1,'L');
	
	$pdf->SetX($x_norek);
	$pdf->Cell(40,4,'Usia Pensiun Normal',0,0,'L');
	$pdf->Cell(55,4,": ".$end_umur." Tahun",0,1,'L');
	
	$pdf->SetX($x_norek);
	$pdf->Cell(40,4,'Tanggal Pensiun Normal',0,0,'L');
	$pdf->Cell(55,4,": ".$end_date,0,1,'L');
	/*end of nomor rekening dan nomor cif*/
	
	/*tanggal periode*/
	$x_periode = $x;
	$y_periode = $y + 35;
	
	$pdf->SetFont('BRICARD02','',9);
	
	$pdf->SetXY($x_periode,$y_periode);
	$pdf->Cell(184,4,'LAPORAN PORTOFOLIO INVESTASI',0,1,'C');
	
	$pdf->SetX($x_periode);
	$pdf->Cell(184,4,'Periode : '.$tgl_awal.' s/d '.$tgl_akhr,0,1,'C');
	
	$pdf->SetX($x_periode);
	$pdf->Cell(184,4,'(Dalam Mata Uang Rupiah)',0,1,'C');
	/*end of tanggal periode*/
	
	/*portofolio investasi*/
	$x_portofolio = $x;
	$y_portofolio = $y + 47;
	
	$pdf->SetFont('BRICARD02','',9);
	
	$pdf->SetXY($x_portofolio,$y_portofolio);
	$pdf->Cell(200,4,'Portofolio Investasi',0,1,'L');
	
	/*header portofolio*/
	rectfill($pdf,200,0,$x_portofolio,$y_portofolio+4,76,10,'FD');
	$pdf->SetXY($x_portofolio,$y_portofolio+4);
	$pdf->Cell(76,10,'Jenis Investasi',0,0,'C');
	
	rectfill($pdf,200,0,$x_portofolio+76,$y_portofolio+4,60,5,'FD');
	$pdf->SetXY($x_portofolio+76,$y_portofolio+4);
	$pdf->Cell(60,5,'Nilai Investasi',0,0,'C');
	
	rectfill($pdf,200,0,$x_portofolio+76,$y_portofolio+9,30,5,'FD');
	$pdf->SetXY($x_portofolio+76,$y_portofolio+9);
	$pdf->Cell(30,5,'Saldo Awal',0,0,'C');
	
	rectfill($pdf,200,0,$x_portofolio+106,$y_portofolio+9,30,5,'FD');
	$pdf->SetXY($x_portofolio+106,$y_portofolio+9);
	$pdf->Cell(30,5,'Saldo Akhir',0,0,'C');
	/*end of header portofolio*/
	
	/*detail portofolio*/
	$pdf->SetFont('BRICARD02','',8);
	rectfill($pdf,255,0,$x_portofolio,$y_portofolio+14,76,32,'D');
	rectfill($pdf,255,0,$x_portofolio+76,$y_portofolio+14,30,32,'D');
	rectfill($pdf,255,0,$x_portofolio+106,$y_portofolio+14,30,32,'D');
	$pdf->SetY($y_portofolio+15);
	for($k=0;$k<count($prd_name);$k++){
		$pdf->SetX($x_portofolio);
		$pdf->Cell(76,4,$prd_name[$k],0,1,'L');
		
		$pdf->SetX($x_portofolio+10);
		$pdf->Cell(66,4,$sub_name_mo1[$k],0,0,'L');
		$pdf->Cell(30,4,$sld_awal_mo1[$k],0,0,'R');
		$pdf->Cell(30,4,$sld_akhr_mo1[$k],0,1,'R');
	}
	/*end of detail portofolio*/
	
	/*total portofolio*/
	$pdf->SetFont('BRICARD02','',8);
	rectfill($pdf,255,0,$x_portofolio,$y_portofolio+46,76,4,'D');
	rectfill($pdf,255,0,$x_portofolio+76,$y_portofolio+46,30,4,'D');
	rectfill($pdf,255,0,$x_portofolio+106,$y_portofolio+46,30,4,'D');
	$pdf->SetXY($x_portofolio,$y_portofolio+46);
	$pdf->Cell(76,4,'TOTAL',0,0,'L');
	$pdf->Cell(30,4,$sld_awal_ttl,0,0,'R');
	$pdf->Cell(30,4,$sld_akhr_ttl,0,1,'R');
	/*end of total portofolio*/
	/*end of portofolio investasi*/
	
	/*akumulasi dana*/
	$x_akumulasi = $x;
	$y_akumulasi = $y + 100;
	
	$pdf->SetFont('BRICARD02','',9);
	
	$pdf->SetXY($x_akumulasi,$y_akumulasi);
	$pdf->Cell(200,4,'D P L K - Akumulasi Dana',0,1,'L');
	
	/*header akumulasi dana*/
	$pdf->SetFont('BRICARD02','',7);
	
	rectfill($pdf,200,0,$x_akumulasi,$y_akumulasi+4,38,14,'FD');
	$pdf->SetXY($x_akumulasi,$y_akumulasi+4);
	$pdf->Cell(38,14,'Produk DPLK',0,0,'C');
	
	rectfill($pdf,200,0,$x_akumulasi+38,$y_akumulasi+4,60,4,'FD');
	$pdf->SetXY($x_akumulasi+38,$y_akumulasi+4);
	$pdf->Cell(60,4,'Akumulasi Iuran',0,0,'C');
	
	rectfill($pdf,200,0,$x_akumulasi+38,$y_akumulasi+8,22,10,'FD');
	$pdf->SetXY($x_akumulasi+38,$y_akumulasi+8);
	$pdf->Cell(22,5,'Pembagi Kerja',0,0,'C');
	$pdf->SetXY($x_akumulasi+38,$y_akumulasi+13);
	$pdf->Cell(22,5,'(1)',0,0,'C');
	
	rectfill($pdf,200,0,$x_akumulasi+60,$y_akumulasi+8,20,10,'FD');
	$pdf->SetXY($x_akumulasi+60,$y_akumulasi+8);
	$pdf->Cell(20,5,'Peserta',0,0,'C');
	$pdf->SetXY($x_akumulasi+60,$y_akumulasi+13);
	$pdf->Cell(20,5,'(2)',0,0,'C');
	
	rectfill($pdf,200,0,$x_akumulasi+80,$y_akumulasi+8,18,10,'FD');
	$pdf->SetXY($x_akumulasi+80,$y_akumulasi+8);
	$pdf->Cell(18,5,'Tambahan',0,0,'C');
	$pdf->SetXY($x_akumulasi+80,$y_akumulasi+13);
	$pdf->Cell(18,5,'(3)',0,0,'C');
	
	rectfill($pdf,200,0,$x_akumulasi+98,$y_akumulasi+4,23,14,'FD');
	$pdf->SetXY($x_akumulasi+98,$y_akumulasi+4);
	$pdf->Cell(23,4,'Dana',0,0,'C');
	$pdf->SetXY($x_akumulasi+98,$y_akumulasi+8);
	$pdf->Cell(23,5,'Pindahan',0,0,'C');
	$pdf->SetXY($x_akumulasi+98,$y_akumulasi+13);
	$pdf->Cell(23,5,'(4)',0,0,'C');
	
	rectfill($pdf,200,0,$x_akumulasi+121,$y_akumulasi+4,23,14,'FD');
	$pdf->SetXY($x_akumulasi+121,$y_akumulasi+4);
	$pdf->Cell(23,4,'Hasil',0,0,'C');
	$pdf->SetXY($x_akumulasi+121,$y_akumulasi+8);
	$pdf->Cell(23,5,'Pengembangan',0,0,'C');
	$pdf->SetXY($x_akumulasi+121,$y_akumulasi+13);
	$pdf->Cell(23,5,'(5)',0,0,'C');
	
	rectfill($pdf,200,0,$x_akumulasi+144,$y_akumulasi+4,18,14,'FD');
	$pdf->SetXY($x_akumulasi+144,$y_akumulasi+4);
	$pdf->Cell(18,4,'Penarikan /',0,0,'C');
	$pdf->SetXY($x_akumulasi+144,$y_akumulasi+8);
	$pdf->Cell(18,5,'Biaya',0,0,'C');
	$pdf->SetXY($x_akumulasi+144,$y_akumulasi+13);
	$pdf->Cell(18,5,'(6)',0,0,'C');
	
	rectfill($pdf,200,0,$x_akumulasi+162,$y_akumulasi+4,22,14,'FD');
	$pdf->SetXY($x_akumulasi+162,$y_akumulasi+4);
	$pdf->Cell(22,4,'Nilai Investasi',0,0,'C');
	$pdf->SetXY($x_akumulasi+162,$y_akumulasi+8);
	$pdf->Cell(22,5,'Akhir',0,0,'C');
	$pdf->SetXY($x_akumulasi+162,$y_akumulasi+13);
	$pdf->Cell(22,5,'(7= 1+2+3+4+5-6)',0,0,'C');
	/*end of header akumulasi dana*/
	
	/*detail akumulasi dana*/
	$pdf->SetFont('BRICARD02','',7);
	rectfill($pdf,255,0,$x_akumulasi,$y_akumulasi+18,38,21,'D');
	rectfill($pdf,255,0,$x_akumulasi+38,$y_akumulasi+18,22,21,'D');
	rectfill($pdf,255,0,$x_akumulasi+60,$y_akumulasi+18,20,21,'D');
	rectfill($pdf,255,0,$x_akumulasi+80,$y_akumulasi+18,18,21,'D');
	rectfill($pdf,255,0,$x_akumulasi+98,$y_akumulasi+18,23,21,'D');
	rectfill($pdf,255,0,$x_akumulasi+121,$y_akumulasi+18,23,21,'D');
	rectfill($pdf,255,0,$x_akumulasi+144,$y_akumulasi+18,18,21,'D');
	rectfill($pdf,255,0,$x_akumulasi+162,$y_akumulasi+18,22,21,'D');
	$pdf->SetY($y_akumulasi+19);
	for($k=0;$k<count($trx_desc);$k++){
		$pdf->SetX($x_akumulasi);
		$pdf->Cell(38,4,$trx_desc[$k],0,0,'L');
		$pdf->Cell(22,4,$trx_amn1[$k],0,0,'R');
		$pdf->Cell(20,4,$trx_amn2[$k],0,0,'R');
		$pdf->Cell(18,4,$trx_amn3[$k],0,0,'R');
		$pdf->Cell(23,4,$trx_amn4[$k],0,0,'R');
		$pdf->Cell(23,4,$trx_amn5[$k],0,0,'R');
		$pdf->Cell(18,4,$trx_amn6[$k],0,0,'R');
		$pdf->Cell(22,4,$trx_amn7[$k],0,1,'R');
	}
	/*end of detail akumulasi dana*/
	/*akumulasi dana*/
	
	/*rincian transaksi*/
	$x_rincian = $x;
	$y_rincian = $y + 143;
	
	/*header rincian transaksi*/
	header_rincian($pdf,$x_rincian,$y_rincian,89);
	/*end of header rincian transaksi*/
	
	/*detail rincian transaksi*/
	$temp_dtl_hlm = 1;
	$pdf->SetFont('BRICARD02','',7);
	$pdf->SetY($y_rincian+12);
	
	for($m=1;$m<=count($xtl_name);$m++){
		$pdf->SetX($x_rincian+1);
		$pdf->Cell(75,3,$xtl_name[$m],0,1,'L');
		for($k=1;$k<=count($dtl_desc[$m]);$k++){
			if($temp_dtl_hlm != $dtl_hlm[$m][$k] && $dtl_hlm[$m][$k] > 1){
				/*halaman kedua dan seterusnya*/
				addpage($pdf,$x,$y,$ximage,$yimage,$url_img,$cus_name,'','','',$bartt,$dat_file,$nourut,$dtl_hlm[$m][$k],$jml_hlm);
				header_rincian($pdf,$x_rincian,($y+47),185);
				
				$pdf->SetFont('BRICARD02','',7);
				$pdf->SetY($y+47+12);
				
				$temp_dtl_hlm = $dtl_hlm[$m][$k];
				/*end of halaman kedua dan seterusnya*/
			}
			$pdf->SetX($x_rincian+1);
			$pdf->Cell(15,3,$dtl_date[$m][$k],0,0,'L');
			$pdf->SetX($pdf->GetX()+1);
			$pdf->Cell(50,3,$dtl_desc[$m][$k],0,0,'L');
			$pdf->Cell(7,3,$dtl_unit[$m][$k],0,0,'L');
			$pdf->SetX($pdf->GetX()+1);
			$pdf->Cell(20,3,$dtl_amnt1[$m][$k],0,0,'R');
			$pdf->SetX($pdf->GetX()+1);
			$pdf->Cell(20,3,$dtl_amnt2[$m][$k],0,0,'R');
			$pdf->SetX($pdf->GetX()+1);
			$pdf->Cell(23,3,$dtl_amnt3[$m][$k],0,0,'R');
			$pdf->SetX($pdf->GetX()+1);
			$pdf->Cell(19,3,$dtl_amnt4[$m][$k],0,0,'R');
			$pdf->SetX($pdf->GetX()+1);
			$pdf->Cell(22,3,$dtl_amnt5[$m][$k],0,1,'R');
		}
	}
	/*end of detail rincian transaksi*/
	/*end of rincian transaksi*/
?>