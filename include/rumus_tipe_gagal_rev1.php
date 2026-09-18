<?php
	function get_tipe_gagal_antrian ( $string , $email='')
	{
		
		$tipe_error = cek_tipe_gagal_1_antrian($string ,$email);
		if ( empty($tipe_error) ) $tipe_error='Not defined [Q]';
		
		
		return $tipe_error;
	}
	
	function cek_tipe_gagal_1_antrian($string, $email)
	{
		$x = '';
		if (preg_match("/domain may not exist/i", $string) ||
			preg_match("/The account or domain may not 550 exist/i", $string) ||
			preg_match("/they may be blacklisted/i", $string) ||
			preg_match("/missing the proper dns entries/i", $string) 
			)
		{
			//$x='Domain tujuan tidak ada atau masuk daftar blacklist atau konfigurasi DNS yang tidak tepat [Q]';
			$x='Domain tidak ada/ter-blacklist/kesalahan DNS [Q]';
		}else if (
			preg_match("/Invalid Email Address format/i", $string) 
			)
		{
			$x = 'Struktur format email salah [Q]';
		}else if (
			preg_match("/Temporary local problem/i", $string) 
			)
		{
			$x = 'Temporary local problem [Q]';
		}else if (
			preg_match("/Could not connect to SMTP host/i", $string) 
			)
		{
			$x = 'Could not connect to SMTP host [Q]';
		}
		return $x; 
	}
	
	function get_tipe_gagal_kirim ( $string , $email='')
	{
		
		$tipe_error = cek_tipe_gagal_1($string, $email);
		if ( empty($tipe_error) ) $tipe_error='Not defined [F]';
		
		
		return $tipe_error;
	}
	
	function pengecekan_email($x, $email)
	{
		if (empty($email)) return $x;
		
		$y = '';
		
			$ww = explode('@',$email);
			
			if (empty($ww[0])) return $x;
			
			$xx1 = substr($ww[0], 0, 1);
			$xx2 = substr($ww[0], -1);
			$panjang_acc = strlen($ww[0]);
			
			
		if ( 
			preg_match("/@gmail/i", $email)
			)
		{
			/*
			6–30 karakter
			Nama pengguna boleh berisi huruf (a-z), angka (0-9), dan titik (.).
			Nama pengguna tidak boleh berisi tanda ampersand (&), sama dengan (=), garis bawah (_), apostrof ('), tanda hubung (-), tanda tambah (+), koma (,), tanda kurung (<,>), atau lebih dari satu titik (.) secara berturut-turut.
			Nama pengguna dapat dimulai atau diakhiri dengan karakter non-alfanumerik, kecuali titik (.)
			*/
				
			if ( 
				(strpos( $ww[0] ,'_') !== false)
			)
			{
				$y .= ' [GMAIL (_) (Underscore) tidak diperbolehkan hanya diperbolehkan huruf (a-z) angka (0-9) dan titik (.)]';
			}else if ( 
					(strpos( $ww[0] ,'..') !== false)
					)
			{
				/*
				$haystack = 'how are you';
				$needle = 'are';

				if (strpos($haystack,$needle) !== false) {
					echo "$haystack contains $needle";
				}
				*/
				$y .= ' [GMAIL tidak diperbolehkan lebih dari satu titik (.) secara berturut-turut]';
			}else if ( 
					(strpos( $ww[0] ,'.') !== false)
					)
			{
				if ( $xx1 == '.' ||  $xx2 == '.' )
				{
					$y .= ' [GMAIL tidak diperbolehkan dimulai atau diakhiri titik (.)]';
				}
			}else if ( 
					$panjang_acc < 6 || $panjang_acc > 30
					)
			{
				$y .= ' [GMAIL harus berisi 6-30 karakter]';
			}
		}else if ( 
			preg_match("/@yahoo./i", $email)
			)
		{
			if ( 
					$panjang_acc < 4 || $panjang_acc > 32
					)
			{
				$y .= " [YAHOO harus berisi 4-32 karakter]";
			}else if ( 
					is_numeric( $xx1 )
					)
			{
				$y .= ' [YAHOO karakter pertama harus huruf]';
			}else if ( 
					ctype_alnum($xx2) === FALSE
					)
			{
				$y .= ' [YAHOO karakter terakhir harus huruf/angka]';
			}else if ( 
					(strpos( $ww[0] ,'..') !== false)
					)
			{
				$y .= ' [YAHOO tidak diperbolehkan lebih dari satu titik (.) secara berturut-turut]';
			}else if ( 
					(strpos( $ww[0] ,'__') !== false)
					)
			{
				$y .= ' [YAHOO tidak diperbolehkan lebih dari satu underscore (_) secara berturut-turut]';
			}
			
		}
		
		/*
		YAHOO
		You can’t have more than one ‘.’ or ‘_’ in a row.
		Please use at least one letter in your username.
		Your username has to start with a letter
		Your username has to end with a letter or a number.
		You can only use letters, numbers, periods (‘.’), and underscores (‘_’) in your username.
		
		Yahoo usernames must follow these rules:

		4 characters minimum
		32 characters maximum
		Allowed characters: Letters (a-z), numbers (0-9) and underscore (_)
		Must start with a letter (a-z)
		*/
		return $y;
		
	}
	
	function cek_tipe_gagal_1($string, $email)
	{
		$x = '';
		if ( 
			preg_match("/not exist/i", $string) ||  
			preg_match("/Sorry it didnt work out/i", $string) ||  
			preg_match("/tidak dapat ditemukan/i", $string) || 
			preg_match("/tidak ditemukan/i", $string) || 
			preg_match("/couldnt be found/i", $string) || 
			preg_match("/550-5.1.1/i", $string) || 
			preg_match("/550 5.1.1/i", $string) || 
			preg_match("/mailbox not found/i", $string) || 
			preg_match("/mailbox unavailable/i", $string) || 
			preg_match("/unavailable/i", $string) || 
			preg_match("/No such person at this address/i", $string) || 
			preg_match("/We dont handle mail/i", $string) || 
			preg_match("/550 no mailbox by that name/i", $string) || 
			preg_match("/Recipient not found/i", $string) || 
			preg_match("/Domain facebook.com no longer available/i", $string) || 
			preg_match("/wasnt found/i", $string) || 
			preg_match("/Check to be surethe email address is correct/i", $string) || 
			preg_match("/user unknown/i", $string) || 
			preg_match("/no mailbox here/i", $string) || 
			preg_match("/User doesnt exist/i", $string) || 
			preg_match("/Unknown user/i", $string) || 
			preg_match("/message couldnt be delivered/i", $string) || 
			preg_match("/501 5.5.4 Invalid Address/i", $string) || 
			preg_match("/your message could not be delivered to one or more recipients/i", $string) || 
			preg_match("/No such user/i", $string) 
			)
		{
			$x = 'Akun email tidak terdaftar pada domain tujuan';
			$y = pengecekan_email($x, $email);
			$x .= $y;
			if( empty($y) ){
				$x .= ' [Kesalahan ejaan/kesalahan pengetikan]'; 
			}
			
			
		}else if (
			preg_match("/This mailbox is disabled/i", $string) ||
			preg_match("/reach is disabled/i", $string) ||
			preg_match("/disabled/i", $string)
			)
		{
			$x = 'Akun email tujuan telah dinonaktifkan';
			$x .= ' [dinonaktifkan atas permintaan pribadi/tidak pernah login dalam jangka waktu tertentu/melanggar Persyaratan Layanan]';
			/*
			if ( 
				preg_match("/@yahoo./i", $email)
				)
			{
				$x .= ' [YAHOO dinonaktifkan atas permintaan pribadi/tidak pernah login dalam jangka waktu tertentu/melanggar Persyaratan Layanan]';
			}else if ( 
				preg_match("/@gmail./i", $email)
				)
			{
				$x .= ' [GMAIL dinonaktifkan atas permintaan pribadi/tidak pernah login dalam jangka waktu tertentu/melanggar Persyaratan Layanan]';
			}
			*/
		}else if (
			preg_match("/over quota/i", $string) ||
			preg_match("/quota exceeded/i", $string) ||
			preg_match("/sudah penuh/i", $string) ||
			preg_match("/mailbox is full/i", $string) ||
			preg_match("/452-4.2.2/i", $string) 
			)
		{
			// the inbox of the signer's account is full 
			$x = 'Inbox Akun email tujuan penuh/melebihi batas kuota';
		}else if (	
			preg_match("/size limit exceeded/i", $string) ||
			preg_match("/Message too big/i", $string) ||
			preg_match("/Message size exceeds maximum permitted/i", $string) ||
			preg_match("/message size exceeds maximum size/i", $string) 
			)
		{
			// the inbox of the signer's account is full 
			$x = 'Ukuran pesan melebihi batas maksimum yang diizinkan';		
		}else if (
			preg_match("/421 Requested action aborted/i", $string) ||
			preg_match("/have been failing for a long time/i", $string) ||
			preg_match("/550 relay not permitted/i", $string) ||
			preg_match("/This domain is not hosted here/i", $string) ||
			preg_match("/Invalid domain/i", $string) ||
			preg_match("/Bad address/i", $string) ||
			preg_match("/Could not find a mail server/i", $string) ||
			preg_match("/554 \"Refused/i", $string) 
			)
		{
			$x = 'Domain email pada Akun email salah/tidak valid';
		}else if (
			preg_match("/Quarantine/i", $string) ||
			preg_match("/spam/i", $string) ||
			preg_match("/virus found/i", $string) ||
			preg_match("/blacklisted at dnsbl.sorbs.net/i", $string) ||
			preg_match("/block list/i", $string) 
			)
		{
			$x = 'Email terindikasi sebagai spam';
		}else if (
			preg_match("/Message expired/i", $string) ||
			preg_match("/Pengiriman akan terus dicoba/i", $string) ||
			preg_match("/retry timeout exceeded/i", $string) ||
			preg_match("/It will be retried until/i", $string) 
			)
		{
			//$x = 'Pesan email kadaluarsa gagal mengirim selama waktu re-try';
			$x = 'Pesan email kadaluarsa selama waktu re-try';
		}else if (
			preg_match("/DMARC unauthenticated mail is prohibited/i", $string) ||
			preg_match("/DMARC check failed/i", $string) 
			)
		{
			//x = 'DMARC unauthenticated mail is prohibited';
			$x = 'DMARC check failed';
		
		}else if (
			preg_match("/Hop count exceeded/i", $string) 
			)
		{
			$x = '554-5.4.6 Hop count exceeded - possible mail loop';
		}else if (
			preg_match("/550 Unrouteable address/i", $string) 
			)
		{
			$x = 'Unrouteable address';
		}else if (
			preg_match("/suppression list/i", $string) 
			)
		{
			$x = 'Suppression list account (ses)';
		}else if (
			preg_match("/rejected/i", $string) ||
			preg_match("/rejected it/i", $string) ||
			preg_match("/permitted to relay/i", $string) ||
			preg_match("/blocked your message/i", $string) ||
			preg_match("/has been blocked/i", $string) ||
			preg_match("/menolak pesan Anda/i", $string) ||
			preg_match("/Sender denied/i", $string) ||
			preg_match("/Message not accepted for policy reasons/i", $string) ||
			preg_match("/system is refusing connections/i", $string) ||
			preg_match("/blocked/i", $string) ||
			preg_match("/Your host is not allowed to connect to this server/i", $string) ||
			preg_match("/blacklisted/i", $string) ||
			preg_match("/550 5.7.1 Not Allowed/i", $string) ||
			preg_match("/refused/i", $string) ||
			preg_match("/email rule restriction/i", $string) ||
			preg_match("/Unauthenticated email/i", $string) ||
			preg_match("/Authentication required/i", $string) ||
			preg_match("/Local Policy Violation Status/i", $string) ||
			preg_match("/This IP has sent too many messages this hour/i", $string) ||
			preg_match("/Relay access denied/i", $string)
		)
		{
			//$x = 'Pesan email ditolak oleh Akun email tujuan (policy reasons/email rule restriction)';
			$x = 'Pesan email ditolak oleh tujuan (policy reasons)';
		}
		
		if (!empty($x)) $x = $x.' [F]';
		
		return $x;
		
	}
	
			
	//@pg_close($con);
?>