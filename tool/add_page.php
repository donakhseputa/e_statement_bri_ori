<?php
function add_page($pdf,
						$hlm,$jml_hlm,
						$url_img,$ximage,$yimage,
						$x,$y,$row_message,
						$no_card,$statement_date,$payment_due_date,$credit_limit,$cash_advan_limit,
						$purchase_debit,$cash_advance,$new_balance,$min_payment,$avail_credit_limit,
						$nama,$address1,$address2,$address3,$city,$zipcode,
						$prev_bonus,$earned_bonus,$point_bonus,$redeemed_bonus,$adjusment_bonus,$point_avail,
						$max_retail_interest,$retail_interest,$max_cash_advan_interest,$cash_advan_interest,
						$prev_balance,$payment_credit,
						$barcode,$posko,$kurir,$nourut,$file){
		$pdf->AddPage();
		
						
						
						
					
					
		$pdf->Image($url_img,$ximage,$yimage,217,302);
		
		
		if (substr($no_card,0,1) == "5") //teks utk MASTER
			{
				$x_teks_master = 175; //$x+168
				$y_teks_master = 13;

					$pdf->SetFont('helvetica','B',5);
					$pdf->SetXY($x_teks_master,$y_teks_master+26.5);
					$pdf->Cell(34,3,"Diterima di seluruh dunia",0,1,'L');
					$pdf->SetFont('Helvetica-Condensed-Light-Li','',5.5);		
					$pdf->SetX($x_teks_master);
					$pdf->MultiCell(35.75,2,"Kartu Kredit BRI dapat digunakan untuk berbelanja di seluruh outlet yang berlogo MasterCard diseluruh dunia.",0,'J');
					
					$pdf->SetFont('helvetica','B',5);
					$pdf->SetXY($x_teks_master,$y_teks_master+56.5);
					$pdf->Cell(34,3,"BRI Protection Plus",0,1,'L');
					$pdf->SetFont('Helvetica-Condensed-Light-Li','',5.5);		
					$pdf->SetX($x_teks_master);
					$pdf->MultiCell(35.75,2,"Perlindungan asuransi jiwa bagi Pemegang Utama Kartu Kredit BRI yang akan melunasi tagihan kartu kredit dan memberikan kelebihan dana* yang bisa dipergunakan oleh keluarga terkasih apabila musibah yang tidak diharapkan terjadi dengan tiba-tiba.",0,'J');
					$pdf->SetXY($x_teks_master-0.5,$y_teks_master+71);
					$pdf->Cell(34,3,"*  Jika  masih terdapat  sisa  manfaat  asuransi",0,1,'L');
					$pdf->SetXY($x_teks_master,$y_teks_master+73.5);
					$pdf->Cell(34,3,"**",0,1,'L');
					$pdf->SetXY($x_teks_master+2,$y_teks_master+74);
					$pdf->MultiCell(33.5,2,"Gratis pembayaran premi bulan pertama apabila terjadi musibah meninggal dunia akibat kecelakaan.",0,'J');
					
					$pdf->SetFont('helvetica','B',5);
					$pdf->SetXY($x_teks_master,$y_teks_master+100.5);
					$pdf->Cell(34,3,"Gratis Akses Executive Lounge",0,1,'L');
					$pdf->SetFont('Helvetica-Condensed-Light-Li','',5.5);		
					$pdf->SetX($x_teks_master);
					$pdf->MultiCell(35.75,2,"Bagi Anda pemegang Kartu Kredit BRI jenis Gold dan Platinum yang bepergian dengan pesawat udara dapat menikmati fasilitas Executive Airport Lounge selama menunggu  waktu  keberangkatan di lebih dari 30 Executive Lounge di bandara seluruh Indonesia.",0,'J');
			
					
					$pdf->SetFont('helvetica','B',5);
					$pdf->SetXY($x_teks_master,$y_teks_master+135);
					$pdf->Cell(34,3,"Program BRING (Belanja RINGan)",0,1,'L');
					$pdf->SetFont('Helvetica-Condensed-Light-Li','',5.5);		
					$pdf->SetX($x_teks_master);
					$pdf->MultiCell(35.99,2,"Anda dapat memiliki produk pilihan melalui program cicilan BRING yang kami tawarkan melalui katalog belanja setiap bulannya.",0,'J');
					
					$pdf->SetFont('helvetica','B',5);
					$pdf->SetXY($x_teks_master,$y_teks_master+164);
					$pdf->MultiCell(35.75,2,"Kemudahan  Pembayaran  Tagihan Rutin di ATM BRI",0,'J');
					$pdf->SetFont('Helvetica-Condensed-Light-Li','',5.5);		
					$pdf->SetXY($x_teks_master,$y_teks_master+169);
					$pdf->MultiCell(35.75,2,"Anda dapat melakukan pembayaran rutin seperti listrik (PLN), telepon (Telkom), telepon selular (Telkomsel dan Matrix), dan biaya pendidikan, Universitas Terbuka melalui ATM BRI.",0,'J');
					
					$pdf->SetFont('helvetica','B',5);
					$pdf->SetXY($x_teks_master,$y_teks_master+198);
					$pdf->Cell(34,3,"Kemudahan  Pembelian  Pulsa Isi Ulang",0,1,'L');
					$pdf->SetFont('Helvetica-Condensed-Light-Li','',5.5);		
					$pdf->SetX($x_teks_master);
					$pdf->MultiCell(35.75,2,"Anda dapat melakukan pembelian pulsa isi ulang di seluruh Indonesia untuk berbagai  operator GSM (Simpati, XL Bebas, IM3 Smart, Mentari, Star One, dan Kartu As)",0,'J');
					
					$pdf->SetFont('helvetica','B',5);
					$pdf->SetXY($x_teks_master,$y_teks_master+230);
					$pdf->Cell(34,3,"Auto Payment",0,1,'L');
					$pdf->SetFont('Helvetica-Condensed-Light-Li','',5.5);		
					$pdf->SetX($x_teks_master);
					$pdf->MultiCell(35.75,2,"Anda dapat melakukan pembayaran listrik (PLN) dan Telepon (Telkom) secara otomatis dengan mendaftar terlebih dahulu melalui ATM BRI atau menghubungi Call BRI di 14017 atau (021) 57 987 400.",0,'J');
					
					$pdf->SetFont('helvetica','B',5);
					$pdf->SetXY($x_teks_master,$y_teks_master+262);
					$pdf->Cell(34,3,"Layanan 24 Jam",0,1,'L');
					$pdf->SetFont('Helvetica-Condensed-Light-Li','',5.5);		
					$pdf->SetX($x_teks_master);
					$pdf->MultiCell(35.75,2,"Customer Service kami siap membantu Anda selama 4 jam sehari dan 7 hari seminggu, termasuk hari Minggu dan hari libur. Hubungi kami di Call BRI di 14017 atau (021)57 987 400.",0,'J');
			
	
			} //selesai utk teks MASTER
			
		$pdf->SetFont('Arial','B',8);
		$y_kotak_atas = 23.5;
		/*$pdf->SetXY($x+2,$y+21.5);
		$pdf->Cell(31,5,$no_card,0,0,'C');
				
		$pdf->SetXY($x+34,$y+21.5);
		$pdf->Cell(24,5,$statement_date,0,0,'C');
		
		$pdf->SetXY($x+60,$y+21.5);
		$pdf->Cell(40,5,$payment_due_date,0,0,'C');
		
		$pdf->SetXY($x+101,$y+21.5);
		$pdf->Cell(22,5,$credit_limit,0,0,'C');
		
		$pdf->SetXY($x+126,$y+21.5);
		$pdf->Cell(34,5,$cash_advan_limit,0,0,'C');*/
		
		$pdf->SetXY($x+2,$y+$y_kotak_atas);
		$pdf->Cell(31,5,$no_card,0,0,'C');
				
		$pdf->SetXY($x+34,$y+$y_kotak_atas);
		$pdf->Cell(24,5,$statement_date,0,0,'C');
		
		$pdf->SetXY($x+60,$y+$y_kotak_atas);
		$pdf->Cell(40,5,$payment_due_date,0,0,'C');
		
		$pdf->SetXY($x+101,$y+$y_kotak_atas);
		$pdf->Cell(22,5,$credit_limit,0,0,'C');
		
		$pdf->SetXY($x+126,$y+$y_kotak_atas);
		$pdf->Cell(34,5,$cash_advan_limit,0,0,'C');
		
		//$y_2 = $pdf->GetY()+17;
		$y_2 = $pdf->GetY()+17.2;
		$pdf->SetXY($x+3,$y_2);
		$pdf->Cell(30,5,$purchase_debit,0,0,'C');
		
		$pdf->SetXY($x+34,$y_2);
		$pdf->Cell(28,5,$cash_advance,0,0,'C');
		
		$pdf->SetXY($x+66,$y_2);
		$pdf->Cell(22,5,$new_balance,0,0,'C');
		
		$pdf->SetXY($x+94,$y_2);
		$pdf->Cell(33,5,$min_payment,0,0,'C');
		
		$pdf->SetXY($x+131,$y_2);
		$pdf->Cell(28,5,$avail_credit_limit,0,0,'C');
		
		$x_barcode = $x+1;
		$y_barcode = $y+76;
		$pdf->SetXY($x_barcode,$y_barcode);
		$pdf->SetFont('C39P24DhTt','',45);
		$pdf->Cell(70,4,$barcode,0,1,'L');
		
		$pdf->SetFillColor(255,255,255);
		$pdf->Rect($x_barcode,$y_barcode-7,53,5.5,'F');
		$pdf->SetFillColor(255,255,255);
		$pdf->Rect($x_barcode,$y_barcode+5,53,5.5,'F');
		
		$pdf->SetXY($x_barcode+52,$y_barcode-1);
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(10,3,$posko,0,1,'L');
		
		$pdf->SetX($x_barcode+52);
		$pdf->Cell(10,3,$kurir,0,1,'L');
		
		$pdf->SetXY($x_barcode,$y_barcode+5);
		$pdf->Cell(70,3,$barcode." - ".$nourut." / ".$file,0,0,'L');
		
		
		
		$x_nama = $x+1;
		$y_nama = $y+49;
		$pdf->SetFont('BRICARD02','',9);
		
		$pdf->SetXY($x_nama,$y_nama);
		$pdf->Cell(70,4,'Yth. Bapak/Ibu',0,1,'L');
		
		$pdf->SetX($x_nama);
		$pdf->Cell(70,4,$nama,0,1,'L');
		
		if($hlm==1){
			$pdf->SetX($x_nama);
			$pdf->Cell(70,4,$address1,0,1,'L');
			
			$pdf->SetX($x_nama);
			$pdf->Cell(70,4,$address2,0,1,'L');
			
			$pdf->SetX($x_nama);
			$pdf->Cell(70,4,$address3,0,1,'L');
			
			$pdf->SetX($x_nama);
			$pdf->Cell(70,4,$city.' '.$zipcode,0,1,'L');
		}
		
		
		
		$x_attention = $x+84;
		$y_attention = $y+48;
		$pdf->SetXY($x_attention,$y_attention);
		/*$pdf->SetFont('Arial','B',8);
		$pdf->Cell(70,3,'PERHATIAN:',0,1,'C');
		
		$pdf->SetX($x_attention);
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(70,3,'BANK BRI TIDAK PERNAH MEMBERI KUASA KEPADA',0,1,'C');
		
		$pdf->SetX($x_attention);
		$pdf->Cell(70,3,'SIAPAPUN UNTUK MENGAMBIL KEMBALI KARTU',0,1,'C');
		
		$pdf->SetX($x_attention+3);
		$pdf->Cell(21,3,'KREDIT ANDA.',0,0,'L');
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(50,3,'HATI2 JIKA ADA PIHAK YANG',0,1,'L');
		
		$pdf->SetX($x_attention);
		$pdf->Cell(70,3,'MENGATAS NAMAKAN BANK BRI YANG INGIN',0,1,'C');
		
		$pdf->SetX($x_attention);
		$pdf->Cell(70,3,'MENGAMBIL KARTU KREDIT ANDA. JIKA',0,1,'C');
		
		$pdf->SetX($x_attention);
		$pdf->Cell(70,3,'MENEMUKAN KECURIGAAN HARAP MENGHUBUNGI',0,1,'C');
		
		$pdf->SetX($x_attention);
		$pdf->Cell(70,3,'CALL CENTER 24 JAM PADA NO.TLP. 021.579-87400',0,1,'C');
		
		$pdf->SetX($x_attention);
		$pdf->Cell(70,3,'atau 14017.',0,1,'C');*/
		
		$pdf->SetFont('Arial','',9);
		if(substr($no_card,0,4) == '4365'){
			$pdf->MultiCell(70,4,'Untuk meningkatkan pelayanan kami Kepada Pemegang Kartu Kredit BRI Touch, Terhitung Mulai Oktober 2012, Reward Rupiah Kartu Kredit BRI Touch berubah menjadi BRI Point. Info Lebih Lanjut Call BRI 14017',0,'C');
		}else{
			$pdf->MultiCell(70,4,'Kartu Kredit BRI dapat dibayarkan melalu Jaringan Luas ATM BRI, dapat pula menggunakan Kartu ATM Bank apapun melalui Jaringan ATM Bersama, Link, dan Prima. Info Lebih Lanjut Hubungi Call BRI 14017',0,'C');
		}
		
		
		
		$x_hlm = $x+133;
		$y_hlm = $y+82;
		$pdf->SetXY($x_hlm,$y_hlm);
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(20,5,'Halaman: '.$hlm.' / '.$jml_hlm);

		/*JUMLAH BARIS MESSAGE HARUS DI-SET DI ATAS*/
		$y_message = $y+219-(3.5*$row_message);
		$x_message = $x+4;
		//$y_message = $y+184;
		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($x_message,$y_message);
		//$pdf->Cell(150,3.5,'Nasabah Kartu Kredit BRI yang terhormat, terima kasih atas kesetiaan Anda menggunakan Kartu Kredit BRI. Untuk lebih',0,1,'C');
		$pdf->Cell(150,3.5,'Nikmati Layanan E-Statement BRI, tagihan dapat anda terima dimana saja anda berada,',0,1,'C');
		
		$pdf->SetX($x_message);
		//$pdf->Cell(150,3.5,'meningkatkan pelayanan kami kepada Anda maka mulai tgl. 1 Januari 2012 pembayaran tagihan listrik (PT. PLN) dan',0,1,'C');
		$pdf->Cell(150,3.5,'Ketik BRI(spasi)ES(spasi)Nama Pada Kartu#16 Digit No Kartu Kredit#Alamat Email KIRIM KE 9333.',0,1,'C');
		
		$pdf->SetX($x_message);
		//$pdf->Cell(150,3.5,'telepon (PT. Telkom) melalui BRI akan dikenakan biaya administrasi sebesar Rp 2.500,-',0,1,'C');
		$pdf->Cell(150,3.5,'E-Statement BRI Praktis, Cepat, dan Aman',0,1,'C');
		
		$pdf->Ln();
		/************TERSEDIA ($row_message) BARIS UNTUK MESSAGE************/
		
		
		
		if($hlm==1){
			$y_point = $y+227;
			$pdf->SetFont('Arial','B',8);
			$pdf->SetXY($x+5,$y_point);
			$pdf->Cell(21,5,$prev_bonus,0,0,'C');
			
			$pdf->SetX($x+31);
			$pdf->Cell(21,5,$earned_bonus,0,0,'C');
			
			$pdf->SetX($x+57);
			$pdf->Cell(21,5,$point_bonus,0,0,'C');
			
			$pdf->SetX($x+83);
			$pdf->Cell(21,5,$redeemed_bonus,0,0,'C');
			
			$pdf->SetX($x+109);
			$pdf->Cell(21,5,$adjusment_bonus,0,0,'C');
			
			$pdf->SetX($x+135);
			$pdf->Cell(21,5,$point_avail,0,0,'C');
		}
		
		
		
		$y_info = $y+252;
		$pdf->SetFont('Arial','B',8);
		$pdf->SetXY($x+6,$y_info);
		$pdf->Cell(38,5,$max_retail_interest.'    '.$retail_interest,0,0,'C');
		
		$pdf->SetX($x+47);
		$pdf->Cell(41,5,$max_cash_advan_interest.'    '.$cash_advan_interest,0,0,'C');
		
		$pdf->SetX($x+91);
		$pdf->Cell(33,5,$prev_balance,0,0,'C');
		
		$pdf->SetX($x+126);
		$pdf->Cell(31,5,$payment_credit,0,0,'C');
		
		$record = 0;
	}
?>