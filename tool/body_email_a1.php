<?php 


function get_curdate_ind_a1($date)
{
	$x = explode(' ',$date);
	
	return $x[0].' '.bulan_ind($x[1]).' '.$x[2];
}

function bulan_ind($m)	{
		$date_mon = array('','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
		
		return $date_mon[$m];
}

function get_code_report_a1($code_report,$tipe)
{
		$sql_sel_code_pdf = "SELECT $tipe FROM m_trxn_type_addendum WHERE trxn_type = '$code_report' LIMIT 1";
		$qry_sel_code_pdf = pg_query($sql_sel_code_pdf) or die('ERROR select code_pdf: '.$sql_sel_code_pdf);
		
		$n_row 		= pg_num_rows($qry_sel_code_pdf);
		if($n_row > 0)
		{
			
			while($row_sel_code_pdf = pg_fetch_array($qry_sel_code_pdf)){
				
				$source = $row_sel_code_pdf[$tipe];
				//$source = str_replace(#no_polis#, $detail[no_polis], "#no_polis");
			}
			return $source;
		}else{
			die("KODE $tipe tidak ditemukan pada m_trxn_type_addendum trxn_type = '$code_report'");
		}
		
}

function get_salutation_a1($sex,$tipe)
{
	$x=array();
	$x['F']['ind'] ='Ibu';
	$x['F']['eng'] ='Mrs.';
	$x['M']['ind'] ='Bapak';
	$x['M']['eng'] ='Mr.';
	
	return $x[$sex][$tipe];
}

function get_body_email_a1()
{
	$x = '
<html>
	<head>
		<meta name="viewport" content="width=device-width" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>E_Policy Manulife</title>
		<style>
			/* -------------------------------------
			GLOBAL RESETS
			------------------------------------- */
			
			/*All the styling goes here*/
			
			.pt1 {
			text-align: justify;
			font-family: Arial, Helvetica, sans-serif;
			font-size: 14px;
			} 
			
			.hurufmargin {
			margin-left: 0;
			font-family: Arial, Helvetica, sans-serif;
			font-size: 14px;
			text-align: justify;
			}
			
			.marginkiri2 {
			text-align: justify;
			font-family: Arial, Helvetica, sans-serif;
			font-size: 11px;
			color:#A9A9A9;
			} 
			
			img {
			border: none;
			-ms-interpolation-mode: bicubic;
			max-width: 100%; 
			}
			
			body {
			background-color: #f6f6f6;
			font-family: Arial, Helvetica, sans-serif;
			text-align: justify;
			-webkit-font-smoothing: antialiased;
			font-size: 14px;
			line-height: 1.4;
			margin: 0;
			padding: 0;
			-ms-text-size-adjust: 100%;
			-webkit-text-size-adjust: 100%; 
			}
			
			table {
			border-collapse: separate;
			mso-table-lspace: 0pt;
			mso-table-rspace: 0pt;
			width: 100%; }
			table td {
			font-family: sans-serif;
			font-size: 14px;
			vertical-align: top; 
			}
			
			/* -------------------------------------
			BODY & CONTAINER
			------------------------------------- */
			
			.body {
			background-color: #f6f6f6;
			width: 100%; 
			}
			
			/* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
			.container {
			display: block;
			margin: 0 auto !important;
			/* makes it centered */
			max-width: 580px;
			padding: 10px;
			width: 580px; 
			}
			
			/* This should also be a block element, so that it will fill 100% of the .container */
			.content {
			box-sizing: border-box;
			display: block;
			margin: 0 auto;
			max-width: 580px;
			padding: 10px; 
			}
			
			/* -------------------------------------
			HEADER, FOOTER, MAIN
			------------------------------------- */
			.main {
			background: #ffffff;
			border-radius: 3px;
			width: 100%; 
			}
			
			.wrapper {
			box-sizing: border-box;
			padding: 20px; 
			}
			
			.content-block {
			padding-bottom: 10px;
			padding-top: 10px;
			}
			
			.footer {
			clear: both;
			margin-top: 10px;
			text-align: center;
			width: 100%; 
			}
			.footer td,
			.footer p,
			.footer span,
			.footer a {
			color: #999999;
			font-size: 12px;
			text-align: center; 
			}
			
			/* -------------------------------------
			TYPOGRAPHY
			------------------------------------- */
			h1,
			h2,
			h3,
			h4 {
			color: #000000;
			font-family: sans-serif;
			font-weight: 400;
			line-height: 1.4;
			margin: 0;
			margin-bottom: 30px; 
			}
			
			h1 {
			font-size: 35px;
			font-weight: 300;
			text-align: center;
			text-transform: capitalize; 
			}
			
			p,
			ul,
			ol {
			font-family: sans-serif;
			font-size: 14px;
			font-weight: normal;
			margin: 0;
			margin-bottom: 15px; 
			}
			p li,
			ul li,
			ol li {
			list-style-position: inside;
			margin-left: 5px; 
			}
			
			a {
			color: #3498db;
			text-decoration: underline; 
			}
			
			/* -------------------------------------
			BUTTONS
			------------------------------------- */
			.btn {
			box-sizing: border-box;
			width: 100%; }
			.btn > tbody > tr > td {
			padding-bottom: 15px; }
			.btn table {
			width: auto; 
			}
			.btn table td {
			background-color: #ffffff;
			border-radius: 5px;
			text-align: center; 
			}
			.btn a {
			background-color: #ffffff;
			border: solid 1px #3498db;
			border-radius: 5px;
			box-sizing: border-box;
			color: #3498db;
			cursor: pointer;
			display: inline-block;
			font-size: 14px;
			font-weight: bold;
			margin: 0;
			padding: 12px 25px;
			text-decoration: none;
			text-transform: capitalize; 
			}
			
			.btn-primary table td {
			background-color: #3498db; 
			}
			
			.btn-primary a {
			background-color: #3498db;
			border-color: #3498db;
			color: #ffffff; 
			}
			
			/* -------------------------------------
			OTHER STYLES THAT MIGHT BE USEFUL
			------------------------------------- */
			.last {
			margin-bottom: 0; 
			}
			
			.first {
			margin-top: 0; 
			}
			
			.align-center {
			text-align: center; 
			}
			
			.align-right {
			text-align: right; 
			}
			
			.align-left {
			text-align: left; 
			}
			
			.clear {
			clear: both; 
			}
			
			.mt0 {
			margin-top: 0; 
			}
			
			.mb0 {
			margin-bottom: 0; 
			}
			
			.preheader {
			color: transparent;
			display: none;
			height: 0;
			max-height: 0;
			max-width: 0;
			opacity: 0;
			overflow: hidden;
			mso-hide: all;
			visibility: hidden;
			width: 0; 
			}
			
			.powered-by a {
			text-decoration: none; 
			}
			
			hr {
			border: 0;
			border-bottom: 1px solid #f6f6f6;
			margin: 20px 0; 
			}
			
			/* -------------------------------------
			RESPONSIVE AND MOBILE FRIENDLY STYLES
			------------------------------------- */
			@media only screen and (max-width: 620px) {
			table[class=body] h1 {
			font-size: 28px !important;
			margin-bottom: 10px !important; 
			}
			table[class=body] p,
			table[class=body] ul,
			table[class=body] ol,
			table[class=body] td,
			table[class=body] span,
			table[class=body] a {
			font-size: 16px !important; 
			}
			table[class=body] .wrapper,
			table[class=body] .article {
			padding: 10px !important; 
			}
			table[class=body] .content {
			padding: 0 !important; 
			}
			table[class=body] .container {
			padding: 0 !important;
			width: 100% !important; 
			}
			table[class=body] .main {
			border-left-width: 0 !important;
			border-radius: 0 !important;
			border-right-width: 0 !important; 
			}
			table[class=body] .btn table {
			width: 100% !important; 
			}
			table[class=body] .btn a {
			width: 100% !important; 
			}
			table[class=body] .img-responsive {
			height: auto !important;
			max-width: 100% !important;
			width: auto !important; 
			}
			}
			
			/* -------------------------------------
			PRESERVE THESE STYLES IN THE HEAD
			------------------------------------- */
			@media all {
			.ExternalClass {
			width: 100%; 
			}
			.ExternalClass,
			.ExternalClass p,
			.ExternalClass span,
			.ExternalClass font,
			.ExternalClass td,
			.ExternalClass div {
			line-height: 100%; 
			}
			.apple-link a {
			color: inherit !important;
			font-family: inherit !important;
			font-size: inherit !important;
			font-weight: inherit !important;
			line-height: inherit !important;
			text-decoration: none !important; 
			}
			#MessageViewBody a {
			color: inherit;
			text-decoration: none;
			font-size: inherit;
			font-family: inherit;
			font-weight: inherit;
			line-height: inherit;
			}
			.btn-primary table td:hover {
			background-color: #34495e !important; 
			}
			.btn-primary a:hover {
			background-color: #34495e !important;
			border-color: #34495e !important; 
			} 
			}
			
		</style>
	</head>
	<body class="">
		<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
			<tr>
				<td>&nbsp;</td>
				<td class="container">
					<div class="content">
						
						<!-- START CENTERED WHITE CONTAINER -->
						<table role="presentation" class="main">
							
							<!-- START MAIN CONTENT AREA -->
							<tr>
								<td class="wrapper">
									<table role="presentation" border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td>
												
												
												<div>
													<!--<div class="pd1" style="padding-left:20px;padding-right:20px;padding-bottom:0px;padding-top:0px;">-->
													
													
													<p class="hurufmargin">
													[SALUTATION_IND] [OWNER_NAME] yang terhormat,</p>
													
													<p class="hurufmargin">
													Terima kasih atas kepercayaan dan kesetiaan [SALUTATION_IND] menjadikan Manulife Indonesia sebagai mitra dalam merencanakan masa depan [SALUTATION_IND] dan keluarga.</p>
													
													<p class="hurufmargin">
													Bersama ini kami melampirkan laporan elektronik <i>(e-statement)</i> [CODE_REPORT_IND] [SALUTATION_IND] per tanggal [CURDATE_IND]. [SALUTATION_IND] dapat mengakses e-statement tersebut dengan menggunakan panduan kata sandi sebagai berikut: ddMmmyyyy, dimana:</p>
													
													
													<table cellpadding="0" cellspacing="0" border="0" width="550" class="hurufmargin">
														<tbody>
															<tr>
																<td valign="top" width="10">
																&bull;</td>
																<td  valign="top" width="50">
																dd</td>
																<td  valign="top" width="10">
																:</td>
																<td valign="top">
																Tanggal lahir anda Contoh: 05</td>
															</tr>
															
															<tr>
																<td valign="top" >
																&bull;</td>
																<td  valign="top">
																Mmm</td>
																<td  valign="top">
																:</td>
																<td valign="top">
																3 huruf pertama bulan lahir dalam format bahasa Inggris, dengan huruf pertama adalah huruf Kapital Contoh: Aug, Oct, Nov</td>
															</tr>
															
															
															<tr>
																<td valign="top">
																&bull;</td>
																<td  valign="top">
																yyyy</td>
																<td  valign="top">
																:</td>
																<td  valign="top">
																4 digit tahun lahir Contoh: 2016</td>
															</tr>
															
														</tbody>
													</table>
													<br>
													<p class="hurufmargin">
													Contoh: tanggal lahir 15 Agustus 2016, maka password adalah 15Aug2016</p>
													
													<table border="1" cellpadding="5" cellspacing="0">
														<tr>
															<td>
															Sebagai bentuk inovasi layanan untuk kemudahan nasabah mengenai metode pembayaran, bagi Nasabah yang melakukan Transfer dapat menggunakan cara pembayaran melalui e-Invoice yang akan kami kirimkan pada saat polis jatuh tempo ke alamat email yang tercatat di data kami. Mohon pastikan alamat email Bapak/Ibu sesuai.  
															<br>
															<br>
															Silakan merubah cara bayar Bapak/Ibu dari <b>Transfer</b> menjadi <b>Autopay</b>.

															</td>
														</td>
													</table>
													<br>
													<p class="hurufmargin">
													Jika [SALUTATION_IND] memerlukan informasi lebih lanjut, silakan menghubungi Manulife Customer Contact Center melalui telepon di (021) 2555 7777 atau e-mail ke CustomerServiceID@Manulife.com. Kami akan melayani [SALUTATION_IND] dengan senang hati</p>
													
												
												
													
													<p class="hurufmargin">
														&nbsp;
													</p>
													
													<p class="hurufmargin">
														Salam hormat,<br>
													PT. Asuransi Jiwa Manulife Indonesia</p>
													
													
													
													<p class="marginkiri2">
													<i>Email ini beserta lampirannya mungkin berisi informasi yang bersifat pribadi, rahasia, dan tertutup. Jika Anda bukan penerima yang dituju, maka Anda tidak diperkenankan untuk membaca, memanfaatkan, menyebarkan atau mendistribusikan email ini beserta seluruh lampirannya. Jika Anda menerima email ini tanpa disengaja, harap segera menghubungi Manulife Indonesia dan menghpus email ini beserta seluruh lampirannya. Kami berhak untuk melakukan tindakan hukum berdasarkan hukum yang berlaku karena penyalahgunaan informasi yang terkandung pada email ini.</i></p>
													
													<br><br>
													
											</div>
											
											
											<div>
												<!--<div class="pd1" style="padding-left:20px;padding-right:20px;padding-bottom:0px;padding-top:0px;">-->
												
												
												<p class="hurufmargin">
												Dear [SALUTATION_ENG] [OWNER_NAME],</p>
												
												<p class="hurufmargin">
												Thank you for making us as your trusted financial partner who helps you prepare for your family`s financial future. </p>
												
												<p class="hurufmargin">
												We send you herewith the electronic statement of your [CODE_REPORT_ENG] as of [CURDATE_ENG]. You can access your e-statement using a standard password ddMmmyyyy, where:</p>
												
												
													<table cellpadding="0" cellspacing="0" border="0" width="550" class="hurufmargin">
														<tbody>
															<tr>
																<td valign="top" width="10">
																&bull;</td>
																<td  valign="top" width="50">
																dd</td>
																<td  valign="top" width="10">
																:</td>
																<td valign="top">
																2 digits of your birth date</td>
															</tr>
															
															<tr>
																<td valign="top" >
																&bull;</td>
																<td  valign="top">
																Mmm</td>
																<td  valign="top">
																:</td>
																<td valign="top">
																The first 3 letters of your birth month in English, example : Aug (The first letter is uppercase and the rest are lowercase)</td>
															</tr>
															
															
															<tr>
																<td valign="top">
																&bull;</td>
																<td  valign="top">
																yyyy</td>
																<td  valign="top">
																:</td>
																<td  valign="top">
																4 digits year of your birth</td>
															</tr>
															
														</tbody>
													</table>
													<br>
													<p class="hurufmargin">
													Example: 15 August 1990 <br><br>The password for the above example is 15Aug1990.</p>
													
													<table border="1" cellpadding="5" cellspacing="0">
														<tr>
															<td>
															As a form of service innovation to customers'."'".' accessibility regarding payment method, Customers who make Transfers can now use the payment method via e-Invoice that we will send when the policy is due to the email address recorded in our data. Please ensure that your email address is already updated.
															<br>
															<br>
															Kindly be advised to change your payment method from <b>Transfer</b> to <b>Autopay</b>.
															</td>
														</td>
													</table>
													<br>
													<p class="hurufmargin">
													If you need further information, please contact Manulife Customer Contact Center at (021) 2555 7777 or email to CustomerServiceID@Manulife.com. We will be very pleased to serve you.</p>
													
												
												
													
													<p class="hurufmargin">
														&nbsp;
													</p>
													
													<p class="hurufmargin">
														Best regards,<br>
													PT. Asuransi Jiwa Manulife Indonesia</p>
													
											
												
												
												
												
												<p class="marginkiri2">
												<i>This email and its attachments may be confidential and are intended solely for the use of the intended recipient. If you are not the intended recipient (or responsible for delivery of the message to such person), you may not use, copy, distribute or deliver to anyone this message (or any part of its contents) or take any action in reliance on it. In such case, you should destroy this message, and notify us immediately. We reserve the right to take any legal action under applicable law for misuse of the information contained in this email.</i></p>
												
												';

return $x;
}
	
?>