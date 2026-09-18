<?php 
	
	function cek_double_tabeldetail( $tabeldetail, $nomor_rekening, $m_loading_id )
	{
		$sql_cek_dat = "SELECT * FROM $tabeldetail WHERE nomor_rekening = '$nomor_rekening' and m_loading_id = $m_loading_id";
		$qry_cek_dat = pg_query($sql_cek_dat) or die('ERROR cek tabeldetail double: '.$sql_cek_dat);
		$jml = pg_num_rows($qry_cek_dat);
		if($jml > 0){
			die( "Nomor rekening $nomor_rekening double");
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
	
	function pesan_wording($OLS_CUST_NBR)
	{
	
		///////////////////////////////////////////////////////////////////// PESAN KOTAK SUMMARY ///////////////////////////////////////////
				///////////////////////////////////////////////////////////////////// PESAN KOTAK SUMMARY ///////////////////////////////////////////	
					if ( substr($OLS_CUST_NBR,0,8) == "43650204" ) //BRI Pegadaian - 20201123
					{
						//$text1_cover_summary = 'Tagihan kartu emas Anda di lembar tagihan ini ditambah dengan transaksi  Anda s.d. tanggal 15 bulan ini akan dikonversikan menjadi GTE. Pembayaran GTE dapat dilakukan melalui menu Bayar Gadai Pegadaian Digital'; //20211221
						$text1_cover_summary = "Dalam rangka meningkatkan layanan kepada nasabah Kartu Kredit co-branding BRI efektif per tagihan bulan Oktober 2022 BRI akan mengenakan biaya e-statement sebesar Rp 5.000/bulan/kartu. Biaya e-statement hanya dikenakan apabila dalam 1 siklus tagihan terdapat transaksi/tagihan."."\n"; //20220923
					}
					else if ( substr($OLS_CUST_NBR,0,8) == "43597201" || substr($OLS_CUST_NBR,0,8) == "43597202") // tokopedia 20211125
					{
						//$text1_cover_summary = "Yth Nasabah Kartu Kredit BRI, untuk kelancaran anda dalam bertransaksi pastikan alamat pengiriman Kartu Kredit BRI anda sudah benar dan lengkap. Apabila ada perubahan alamat mohon dapat dilakukan pengkinian data melalui : "."\n"."Info Contact BRI 14017 / 1500017" ; //20210319
						$text1_cover_summary = "Dalam rangka meningkatkan layanan kepada nasabah Kartu Kredit co-branding BRI efektif per tgl 1 Oktober 2022 BRI akan mengenakan biaya e-statement sebesar Rp 5.000/bulan/kartu. Biaya e-statement hanya dikenakan apabila dalam 1 siklus tagihan terdapat transaksi/tagihan."."\n" ; //20220907
					}
					else if ( substr($OLS_CUST_NBR,0,8) == "55200233" ) //OVO
					{
						//$text1_cover_summary = "Yth Nasabah Kartu Kredit BRI, untuk kelancaran anda dalam bertransaksi pastikan alamat pengiriman Kartu Kredit BRI anda sudah benar dan lengkap. Apabila ada perubahan alamat mohon dapat dilakukan pengkinian data melalui : "."\n"."1. BRICC Mobile (dapat didownload melalui Appstore / Playstore)"."\n"."2. Info Contact BRI 14017 /1500017" ; //20210319
						$text1_cover_summary = "Dalam rangka meningkatkan layanan kepada nasabah Kartu Kredit co-branding BRI efektif per tgl 1 Oktober 2022 BRI akan mengenakan biaya e-statement sebesar Rp 5.000/bulan/kartu. Biaya e-statement hanya dikenakan apabila dalam 1 siklus tagihan terdapat transaksi/tagihan."."\n" ; //20220907
					}
					else if ( substr($OLS_CUST_NBR,0,8) == "55200205" ) //NEXCARD
					{
						//$text1_cover_summary = "Yth Nasabah Kartu Kredit BRI, untuk kelancaran anda dalam bertransaksi pastikan alamat pengiriman Kartu Kredit BRI anda sudah benar dan lengkap. Apabila ada perubahan alamat mohon dapat dilakukan pengkinian data melalui : "."\n"."1. BRICC Mobile (dapat didownload melalui Appstore / Playstore)"."\n"."2. Info Contact BRI 14017 /1500017" ; //20210319
						$text1_cover_summary = "Dalam rangka meningkatkan layanan kepada nasabah Kartu Kredit co-branding BRI efektif per tgl 1 Oktober 2022 BRI akan mengenakan biaya e-statement sebesar Rp 5.000/bulan/kartu. Biaya e-statement hanya dikenakan apabila dalam 1 siklus tagihan terdapat transaksi/tagihan."."\n" ; //20220907
					}
					else if ( substr($OLS_CUST_NBR,0,8) == "43596503" || substr($OLS_CUST_NBR,0,8) == "43596504" ) //SAMSUNG
					{
						//$text1_cover_summary = "Yth Nasabah Kartu Kredit BRI, untuk kelancaran anda dalam bertransaksi pastikan alamat pengiriman Kartu Kredit BRI anda sudah benar dan lengkap. Apabila ada perubahan alamat mohon dapat dilakukan pengkinian data melalui : "."\n"."1. BRICC Mobile (dapat didownload melalui Appstore / Playstore)"."\n"."2. Info Contact BRI 14017 /1500017" ; //20210319
						$text1_cover_summary = "Dalam rangka meningkatkan layanan kepada nasabah Kartu Kredit co-branding BRI efektif per tgl 1 Oktober 2022 BRI akan mengenakan biaya e-statement sebesar Rp 5.000/bulan/kartu. Biaya e-statement hanya dikenakan apabila dalam 1 siklus tagihan terdapat transaksi/tagihan."."\n" ; //20220907
					}
					else if ( substr($OLS_CUST_NBR,0,8) == "43650203" ) //Traveloka
					{
						//$text1_cover_summary = "Yth Nasabah Kartu Kredit BRI, untuk kelancaran anda dalam bertransaksi pastikan alamat pengiriman Kartu Kredit BRI anda sudah benar dan lengkap. Apabila ada perubahan alamat mohon dapat dilakukan pengkinian data melalui : "."\n"."1. BRICC Mobile (dapat didownload melalui Appstore / Playstore)"."\n"."2. Info Contact BRI 14017 /1500017" ; //20210319
						$text1_cover_summary = "Dalam rangka meningkatkan layanan kepada nasabah Kartu Kredit co-branding BRI efektif per tgl 1 Oktober 2022 BRI akan mengenakan biaya e-statement sebesar Rp 5.000/bulan/kartu. Biaya e-statement hanya dikenakan apabila dalam 1 siklus tagihan terdapat transaksi/tagihan."."\n" ; //20220907
					}
					else if ( substr($OLS_CUST_NBR,0,8) == "43596501" || substr($OLS_CUST_NBR,0,8) == "43596502" ) //BTN
					{
						//$text1_cover_summary = "Yth Nasabah Kartu Kredit BRI, untuk kelancaran anda dalam bertransaksi pastikan alamat pengiriman Kartu Kredit BRI anda sudah benar dan lengkap. Apabila ada perubahan alamat mohon dapat dilakukan pengkinian data melalui : "."\n"."1. BRICC Mobile (dapat didownload melalui Appstore / Playstore)"."\n"."2. Info Contact BRI 14017 /1500017" ; //20210319
						$text1_cover_summary = "Dalam rangka meningkatkan layanan kepada nasabah Kartu Kredit co-branding BRI efektif per tgl 1 Oktober 2022 BRI akan mengenakan biaya e-statement sebesar Rp 5.000/bulan/kartu. Biaya e-statement hanya dikenakan apabila dalam 1 siklus tagihan terdapat transaksi/tagihan."."\n" ; //20220907
					}else{
						$text1_cover_summary = "Yth Nasabah Kartu Kredit BRI, untuk keamanan & kenyamanan transaksi Kartu Kredit BRI anda, notifikasi transaksi via SMS dengan nominal kurang dari Rp.500 ribu dinonaktifkan per 15 September 2022. Untuk keamanan & kenyamanan transaksi, segera daftarkan alamat email anda dan download aplikasi BRICC Mobile melalui App Store & Play Store agar mendapatkan fitur push notification transaksi."."\n" ; //20220811
					}
				///////////////////////////////////////////////////////////////////// PESAN KOTAK SUMMARY ///////////////////////////////////////////
				///////////////////////////////////////////////////////////////////// PESAN KOTAK SUMMARY ///////////////////////////////////////////	
				
				return $text1_cover_summary;
	}
	
	
	function cek_status_cyber_split_ganjil_genap()
	{
		//untuk split cyber ganjil genap antrian_id dan jadwal_id hanya ada 'on' atau 'off'
		
		
		return 'off';
		
		
		
		#return 'on';
		
		#return 'off';
	}
	
	function cek_status_aws()
	{
		//untuk split aws ganjil genap antrian_id dan jadwal_id hanya ada 'online' atau 'offline'

		//return 'offline';
		
		
		
		return 'online';
		
		
		/*
		$host = 'amazon.com';
		if($socket =@ fsockopen($host, 80, $errno, $errstr, 30)) {
			return 'online';
			fclose($socket);
		} else {
			return 'offline';
		}
		*/
		#return 'offline';
	}
	
	
	function cek_kode_kirim($email)
	{
		$x = 1;
		if ( preg_match("/@yahoo/i", $email) )
		{
			if ( preg_match("/@yahoo.com/i", $email) )
			{
				$x = 4;
			}else{
				$x = 3;
			}
			
		}else if ( preg_match("/@ymail/i", $email) ){
			$x = 3;
		}else if ( preg_match("/@rocketmail/i", $email) ){
			$x = 3;
		}else if ( preg_match("/@gmail/i", $email) ){
			$x = 2;
		}else if ( preg_match("/briagro/i", $email) ){
			$x = 3;
		}
		
		
		
		if ($x == 1)
		{
			if ( preg_match("/@bri.co.id/i", $email) )
			{
				$x = 1;
			}else if ( preg_match("/@corp.bri.co.id/i", $email) ){
				$x = 1;
			}else if ( preg_match("/@btn.co.id/i", $email) ){
				$x = 1;
			}else if ( preg_match("/@hotmail/i", $email) ){
				$x = 1;
			}else{
				$x = 3;
			}
		}
		
		
		/*
		//#####if ( $x == 3 || $x == 4 )
		//#####{
			$blth_sebelumnya = GetBLTH(date('mY'),-1);
			//$tabel_sebelumnya = "tr_email_".$blth_sebelumnya."_bc";
			$tabel_sebelumnya = "tr_email_012023_bc";
			$sql_sebelumnya		= "SELECT * FROM $tabel_sebelumnya 
			WHERE lower(tipe_gagal) like lower('%Suppression%')
			AND lower(email) = lower('$email')
			LIMIT 1";
			//die($sql);
			$query_sebelumnya 		= pg_query($sql_sebelumnya);
			$n_row 		= pg_num_rows($query_sebelumnya);
			if($n_row > 0)
			{
				$x = 2;
			}
		//#####}
		*/
		
		/*
		
		
		if ($x == 1)
		{
			if ( preg_match("/@bri.co.id/i", $email) )
			{
				$x = 1;
			}else if ( preg_match("/@corp.bri.co.id/i", $email) ){
				$x = 1;
			}else if ( preg_match("/@hotmail/i", $email) ){
				$x = 1;
			}else if ( preg_match("/@dskorea.com/i", $email) ){
				$x = 1;
			}else if ( preg_match("/@newgraha.com/i", $email) ){
				$x = 1;
			}else if ( preg_match("/@petrochina.co.id/i", $email) ){
				$x = 1;
			}else if ( preg_match("/@bumiputera.com/i", $email) ){
				$x = 1;
			}else if ( preg_match("/@posindonesia.co.id/i", $email) ){
				$x = 1;
			}else if ( preg_match("/@krl.co.id/i", $email) ){
				$x = 1;
			}else if ( preg_match("/@kai.id/i", $email) ){
				$x = 1;
			}else if ( preg_match("/@car.co.id/i", $email) ){
				$x = 1;
			}else if ( preg_match("/@reska.id/i", $email) ){
				$x = 1;
			}else if ( preg_match("/@ag.co.id/i", $email) ){
				$x = 1;
			}else if ( preg_match("/@dskorea.com/i", $email) ){
				$x = 1;
			}else if ( preg_match("/@dskorea.com/i", $email) ){
				$x = 1;
			}else if ( preg_match("/@dskorea.com/i", $email) ){
				$x = 1;
			}else{
				$x = 3;
			}
		}
		*/
		
		return $x;
	}
	

function encryptIt_message_id( $q ) {
    $cryptKey  = '4l4yk3y4l4yk3y12';
    $qEncoded      = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), $q, MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ) );
    return( $qEncoded );
}

function decryptIt_message_id( $q ) {
    $cryptKey  = '4l4yk3y4l4yk3y12';
    $qDecoded      = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), base64_decode( $q ), MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ), "\0");
    return( $qDecoded );
}

	
	function cek_sk_tr($blth, $flagtrans)
	{
		$blth_balik = substr($blth,-4).substr($blth,0,2);
			
				$BLTH_BARUS = $GLOBALS['sk_tr'];
				if($blth_balik>$BLTH_BARUS){
				$tabeldetail = strtolower("tr_email_".$blth."_".$flagtrans);
				$tabeldetail_pl = $tabeldetail."_p_k";
				$tabeldetail_un = $tabeldetail."_unique";
				$tabelindex = $tabeldetail."_index";
				
				$cek_tabel = "SELECT tr_email_id FROM $tabeldetail";
				$exe_tabel = @pg_query($cek_tabel);
				if(!$exe_tabel){
					
				$buat_tabel = "
CREATE TABLE $tabeldetail
(
  tr_email_id serial NOT NULL,
  nomor_customer character varying(500),
  nomor_rekening text,
  email text,
  email_sukses boolean,
  date_email_send timestamp without time zone,
  date_email_callback timestamp without time zone,
  ket_error character varying(700),
  email_callback boolean,
  count_sent integer,
  loading_id integer,
  antrian_id integer,
  template_id integer,
  nama_customer text,
  tgl_read timestamp without time zone,
  status_sample boolean,
  body_email_read text,
  read_method character varying(12),
  delay_info text,
  info_device text,
  tipe_gagal text,
  respon text,
  tgl_read2 text,
  kode_kirim integer,
  k_nama_body_email text,
  k_lampiran_email text,
  CONSTRAINT $tabeldetail_pl PRIMARY KEY (tr_email_id )
)
WITH (
  OIDS=FALSE
);";
					$exe_tabel = pg_query($buat_tabel)or die("ERROR: " . $buat_tabel);
					//indexing
					//$sql_ind = "CREATE INDEX $tabelindex ON $tabeldetail  USING btree (blth, flagtrans, nama_file,nomor_rekening);";
					$sql_ind = "CREATE INDEX $tabelindex ON $tabeldetail  USING btree (nomor_rekening, loading_id, email);";
					$exe_ind = pg_query($sql_ind)or die("ERROR: " . $sql_ind);
				}
		}else{
			return 'tr_email';
		}
		
		return $tabeldetail;
	}
	
	
	function template_corporate_split($template_id, $nomor_customer , $nomor_rekening)
	{
		$sql_sel_attach = "SELECT * FROM tmp_split WHERE nomor_customer='$nomor_customer' AND nomor_rekening='$nomor_rekening' LIMIT 1";
		$qry_sel_attach = pg_query($sql_sel_attach) or die($sql_sel_attach);
		$row_dat = pg_num_rows($qry_sel_attach);
		if($row_dat>0)
		{
			$sql= "SELECT * FROM template_email WHERE FLAGTRANS = 'BC' AND status='t' ORDER BY template_email_id DESC  LIMIT 1";
			$qry = pg_query($sql) or die($sql);
			$row1 = pg_num_rows($qry);
			if($row1>0){
				$dt = pg_fetch_assoc($qry);
				return $dt['template_email_id'];
			}else{
				return $template_id;
			}
		}else{
			return $template_id;
		}
	}
	
	
	
	function check_email_address($email) {
        // First, we check that there's one @ symbol, and that the lengths are right
        if (!preg_match("/^[^@]{1,64}@[^@]{1,255}$/", $email)) {
            // Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
            return false;
        }
        // Split it into sections to make life easier
        $email_array = explode("@", $email);
        $local_array = explode(".", $email_array[0]);
        for ($i = 0; $i < sizeof($local_array); $i++) {
            if (!preg_match("/^(([A-Za-z0-9!#$%&'*+\/=?^_`{|}~-][A-Za-z0-9!#$%&'*+\/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$/", $local_array[$i])) {
                return false;
            }
			if ( preg_match('/[\']/', $local_array[$i]) ) {
                return false;
            }
        }
        if (!preg_match("/^\[?[0-9\.]+\]?$/", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
            $domain_array = explode(".", $email_array[1]);
            if (sizeof($domain_array) < 2) {
                return false; // Not enough parts to domain
            }
            for ($i = 0; $i < sizeof($domain_array); $i++) {
                if (!preg_match("/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$/", $domain_array[$i])) {
                    return false;
                }
            }
        }

        return true;
    }
	
	
	function GetBLTH($period, $difference) {
		$month = (int)substr($period, 0, 2) + $difference;
		$year = (int)substr($period, 2, 4);
		while ($month > 12) {
			$month -= 12;
			$year++;
		}
		while ($month < 1) {
			$month += 12;
			$year--;
		}
		$result = str_pad($month, 2, "0", STR_PAD_LEFT) . $year;

		return $result;
	}
	
	###### PAGINATION ######	
	
	function makeLink($str,$offset,$bold="false",$divload,$halaman_load,$value){
		if($bold){
			$str="<b>".$str."</b>";
		}
	
		return '<a onclick="var lsque=$(\'#list_antrian\').val(); Loaddiv(\''.$divload.'\',\''.$halaman_load.'\',\''.$value.'&offset='.$offset.'&lsque=\'+lsque)">'.$str.'</a>';
	
	}

	function paging($curRec,$totalRec,$maxRec,$divload,$halaman_load,$value){
		
		$totalPage=ceil($totalRec/$maxRec);
		$curPage=ceil(($curRec+1)/$maxRec);
		$str="";
		
		/*--------------------------prev button-----------------------*/
		if($curPage>1){
			$rec=($curPage-2)*$maxRec;					
			$str.=" ".makeLink("prev",$rec,$bold,$divload,$halaman_load,$value)." ";			
		}
		
		/*-------------------------generate page number----------------*/
		for($i=1;$i<=$totalPage;$i++){
			if($i==$curPage){
				$bold=true;
			}else{
				$bold=false;
			}
			$rec=($i-1)*$maxRec;					
			$str.=" ".makeLink($i,$rec,$bold,$divload,$halaman_load,$value)." ";
		}
		
		/*--------------------------next button-----------------------*/
		if($curPage<$totalPage){
			$rec=($curPage*$maxRec);					
			$str.=" ".makeLink("next",$rec,$bold,$divload,$halaman_load,$value)." ";			
		}
		
		return $str;
		
	}
	###### END OF PAGINATION ######
	
	function replace_msg_email($flagtrans,$source,$detail){
		$sql_sel_code_email = "SELECT * FROM code_email WHERE flagtrans = '$flagtrans' and status = TRUE";
		$qry_sel_code_email = pg_query($sql_sel_code_email) or die('ERROR select code_email: '.$sql_sel_code_email);
		while($row_sel_code_email = pg_fetch_array($qry_sel_code_email)){
			$code_email = $row_sel_code_email['code_email'];
			$code_php = $row_sel_code_email['code_php'];
			
			$source = str_replace($code_email,$detail[$code_php],$source);
		}
		return $source;
	}
	
	function show_table_code_email($flagtrans){
		?>
        <table width="100%" border="1" style="border:1px; border-color:#FFF; background-color:#E5E5E5" cellpadding="2" cellspacing="0">
            <tr>
                <td width="10%"><b>viewed</b></td>
                <td width="30%"><b>code</b></td>
                <td width="10%"><b>viewed</b></td>
                <td width="30%"><b>code</b></td>
            </tr>
            <tr>
            <?php
                $c_td = 0;
                $sql_show_code = "SELECT * FROM code_email WHERE flagtrans = '$flagtrans' and status = TRUE";
                $qry_show_code = pg_query($sql_show_code) or die('ERROR select code: '.$sql_show_code);
                while($row_show_code = pg_fetch_array($qry_show_code)){
                    $c_td++;
                    ?>
                        <td><?=$row_show_code['code_name'];?></td>
                        <td><?=$row_show_code['code_email'];?></td>
                    <?php
                    if($c_td%2==0){
                        echo "</tr><tr>";
                    }
                }
            ?>
            </tr>
        </table>
        <?php
	}
	
	function period($m)	{
		$date_mon = array('','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
		$data = explode('-',$m);
		if(substr($data[0],0,1)=='0'){
			$data[0]=substr($data[0],1,1);
		}
		$data[0] = $date_mon[$data[0]];
		$data = implode(' ',$data);
		return $data;
	}
	
	function judul($menu_id){
		$sql_sel_judul = "SELECT a.menugroup, b.menu
							FROM menugroup1 a
								INNER JOIN menu1 b
									ON a.menugroupid = b.menugroupid
							WHERE b.menuid = $menu_id";
		$qry_sel_judul = pg_query($sql_sel_judul) or die('ERROR select judul: '.$sql_sel_judul);
		$row_sel_judul = pg_fetch_assoc($qry_sel_judul);
		echo $row_sel_judul['menugroup'].' - '.$row_sel_judul['menu'];
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
	
	function file_write($filename, $flag, $mode) { 
		if (file_exists($filename)) {
			if (!is_writable($filename)) {
				if (!chmod($filename, $mode)) {
					echo "Cannot change the mode of file ($filename)<br>Ulangi proses upload !!!";
					exit;
				};
			}
		}
		if (!$fp = fopen($filename, $flag)) {
			echo "Cannot open file ($filename)<br>Ulangi proses upload !!!";
			exit;
		}
		if (!fclose($fp)) {
			echo "Cannot close file ($filename)<br>Ulangi proses upload !!!";
			exit;
		}
	}
	
	function create_file_not_exist($loc_file_not_exist,$file_not_exist_txt, $content){
		chmod($loc_file_not_exist, 0777);		
		if (!$handle = fopen($loc_file_not_exist.$file_not_exist_txt, 'w')) {
			 die("<div class='alert failed'>Sorry, Can't open file ".$file_not_exist_txt." </div>");				 
		}
		if (fwrite($handle, $content) === FALSE) {
			die("<div class='alert failed'>Sorry, Can't write to file ".$file_not_exist_txt." </div>");				
		}		
		return true;
		fclose($handle);		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/**UPGRADE 16 SEPTEMBER 2014**/
	
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
	
	
	
	
	
	function log_approval(){
		$sql_ins_approval = "
			INSERT INTO log_approval(userid, create_date)
			VALUES('".$_SESSION['userid']."', now());
		";
		$qry_ins_approval = pg_query($sql_ins_approval) or die('ERROR insert log_approval: '.$sql_ins_approval);
		
		$sql_sel_log_approval = "SELECT last_value FROM log_approval_log_approval_id_seq";
		$qry_sel_log_approval = pg_query($sql_sel_log_approval) or die('ERROR select log_approval: '.$sql_sel_log_approval);
		$row_sel_log_approval = pg_fetch_assoc($qry_sel_log_approval);
		$log_approval_id = $row_sel_log_approval['last_value'];
		
		return $log_approval_id;
	}
	
	function bypass_approval($log_approval_id){
		$sql_apr = "
			UPDATE log_approval
			SET
				is_approval = 't',
				is_check = '1',
				approval_date = now(),
				check_date = now(),
				note_check = 'sample only',
				check_userid = '".$_SESSION['user_id']."',
				approval_user = '".$_SESSION['user_id']."'
			WHERE log_approval_id = '" . $log_approval_id . "'
		";
		$qry_apr = pg_query($sql_apr);
	}
	
	function update_log_approval($log_approval_id, $total_email, $loading_id, $rec_split, $jeda){
		$sql_upd_log_approval = "
			UPDATE log_approval
			SET total_email = $total_email, m_loading_id='$loading_id', rec_split='$rec_split', jeda='$jeda' 
			WHERE log_approval_id = $log_approval_id";
		$qry_upd_log_approval = pg_query($sql_upd_log_approval) or die('ERROR update log_approval: '.$sql_upd_log_approval);
	}
	
	
	
	
	function update_log_approval_sk($log_approval_id, $total_email, $loading_id, $rec_split, $jeda){
		$sql_upd_log_approval = "
			UPDATE log_approval
			SET total_email = $total_email, m_loading_id='$loading_id', rec_split='$rec_split', jeda='$jeda' 
			WHERE log_approval_id = $log_approval_id";
		$qry_upd_log_approval = pg_query($sql_upd_log_approval) or die('ERROR update log_approval: '.$sql_upd_log_approval);
	}
	
	
	function getAkses($menu_id){
		$sql = "
			SELECT akses FROM usermenu1 WHERE menuid = '" . $menu_id . "' and userid = '" . $_SESSION['user_id'] . "';
		";
		$qry = pg_query($sql);
		$row = pg_fetch_assoc($qry);
		$akses = $row['akses'];
		
		return $akses;
	}
	
	function explore_directory($dirloc)
	{
		if (file_exists($dirloc))
		{
			$iterator = new DirectoryIterator($dirloc);
			foreach ($iterator as $fileinfo) {
				if ($fileinfo->isFile()) {
					$info_base = pathinfo($fileinfo->getFilename());
					if (!empty($info_base['extension'])){
						$file_ext = '.'.basename($info_base['extension']);
					}else{
						$file_ext = '';
					}
					$getfiledetail[$fileinfo->getCTime().$fileinfo->getFilename()]=array('file_name' => $fileinfo->getFilename(),
															'created_date' => $fileinfo->getCTime(),
															'modified_date' => $fileinfo->getMTime(),
															'file_size' => $fileinfo->getSize(),
															'file_basename' => basename($info_base['filename']),
															'file_extension' => $file_ext);
				}
			}
			
			
			return $getfiledetail;
			
		}else{			
			return array();
		}
	}
	
	function zip_directory($dirloc, $zip_name)
	{
		$error		= '';
		$explore_result = explore_directory($dirloc);
		
		if (!empty($explore_result))
		{
			if(!file_exists($zip_name))
			{
				$zip = new ZipArchive(); 													// Load zip library 
				if($zip->open($zip_name, ZIPARCHIVE::CREATE)!==TRUE)
				{ 
					$error .= "* Sorry ZIP creation failed at this time";
				}
				foreach($explore_result as $keyfile => $valArr)
				{
					$zip->addFile($dirloc.$valArr['file_name']);
				}
				$zip->close();
			}else{
				$error .= "File zip telah tersedia";
			}
		}else{
			$error .= "Tidak ada data";
		}
		
		return $error;
	}
	
	
	function get_nfile_ext($file){
		$file = trim($file);
		$return = '';
		$ex = explode('.',$file);
		#hanya ambil yang ada extension nya
		if(count($ex)>1) $return = '.'.$ex[count($ex)-1];
		
		return $return;
	}
	
	function get_nfile($file){
		$file = trim($file);
		$return = "";
		$ext = get_nfile_ext($file);
		$return = str_replace($ext,'',$file);
		return $return;
	}
	
	
	
	function lampiran_pdf_belakang($no_card_dat , $nomor_customer='')
	{
		
		#die("ERRORR INSERT PDF BACK BILLING TAMBAHAN");
		
					if (substr($no_card_dat,0,1) == "4") //VISA
					{
						#if ( substr($no_card_dat,0,6) == "468740") //Infinite - 20160419 Bowo
						if ( substr($no_card_dat,0,8) == "46874001" || substr($no_card_dat,0,8) == "46874002") //Infinite
						{
							
							#$url_pdf_jpg = 'FA_Syarat_ketentuan.pdf';
							#$url_pdf_jpg = 'Syarat_ketentuan_INFINITE.pdf';
							#$url_pdf_jpg = 'Syarat_ketentuan_All_Jan22.pdf';
							$url_pdf_jpg = 'Syarat_ketentuan_All.pdf';
						
						}
						elseif (substr($no_card_dat,0,8) == "43650203") //TRAVELOKA
						{
							if ( preg_match("/43650204/i", $nomor_customer)  ) //BIN PEGADAIAN
								//jika ada yang traveloka pindah ke pegadaian karena nomor rekening berdasarkan cif, nomor customer berdasarkan nomor kartu
							{
								$url_pdf_jpg = 'Syarat_ketentuan_BRI_Pegadaian.pdf';
							}else{
								$url_pdf_jpg = 'Syarat_ketentuan_Traveloka_Paylater.pdf';
							}
							
							
						}
						elseif (substr($no_card_dat,0,8) == "43650204") //BRI PEGADAIAN
						{
							#$url_pdf_jpg = 'Syarat_ketentuan_BRI_Pegadaian.pdf';
							
							if ( preg_match("/43650203/i", $nomor_customer)  ) //BIN TRAVELOKA
							//jika ada yang pegadaian pindah ke traveloka karena nomor rekening berdasarkan cif, nomor customer berdasarkan nomor kartu
							{
								$url_pdf_jpg = 'Syarat_ketentuan_Traveloka_Paylater.pdf';
							}else{
								$url_pdf_jpg = 'Syarat_ketentuan_BRI_Pegadaian.pdf';
							}
							
						}
						#elseif (substr($no_card_dat,0,6) == "435972") //Hana bank - 20160727 Bowo
						elseif ( substr($no_card_dat,0,8) == "43597201" || substr($no_card_dat,0,8) == "43597202" ) //Hana bank Platinum // telah berubah menjadi tokopedia 20211125
						{
							$url_pdf_jpg = 'Syarat_ketentuan_All.pdf';
							
						}
						
						//BIN SAMSUNG BARU 18.07.2024
						elseif ( substr($no_card_dat,0,8) == "43596503" || substr($no_card_dat,0,8) == "43596504" ) //SAMSUNG PLATINUM DAN SAMSUNG SIGANTURE
						{
							$url_pdf_jpg = 'Syarat_ketentuan_All.pdf';
							
						}
						#elseif (substr($no_card_dat,0,6) == "435965") //Hana bank - 20160727 Bowo
						elseif ( substr($no_card_dat,0,8) == "43596501" || substr($no_card_dat,0,8) == "43596502" ) //Hana bank GOLD
						{
							
							////////////////////////////////////////////////////
							
							if (
							substr($no_card_dat,0,16) == "4359650100032409" || substr($no_card_dat,0,16) == "4359650100028605" ||
							substr($no_card_dat,0,16) == "4359650100027201" || substr($no_card_dat,0,16) == "4359650100033704" ||
							substr($no_card_dat,0,16) == "4359650100005009" || substr($no_card_dat,0,16) == "4359650200026400" ||
							substr($no_card_dat,0,16) == "4359650100011007" || substr($no_card_dat,0,16) == "4359650100033209" ||
							substr($no_card_dat,0,16) == "4359650280004608" || substr($no_card_dat,0,16) == "4359650200014000" ||
							substr($no_card_dat,0,16) == "4359650100005108" || substr($no_card_dat,0,16) == "4359650180007107" ||
							substr($no_card_dat,0,16) == "4359650100029306" || substr($no_card_dat,0,16) == "4359650100031500" ||
							substr($no_card_dat,0,16) == "4359650180003403" || substr($no_card_dat,0,16) == "4359650100023200"
							)
							{
								$url_pdf_jpg = 'Syarat_ketentuan_KEBHanaBank.pdf';
							}else{
								if ( substr($no_card_dat,0,8) == "43596501" )
								{
									$url_pdf_jpg = 'Syarat_ketentuan_BRI_BTN_Regular.pdf';
								}else if ( substr($no_card_dat,0,8) == "43596502" ){
									$url_pdf_jpg = 'Syarat_ketentuan_BRI_BTN_Platinum.pdf';
								}else{
									#$url_pdf_jpg = 'Syarat_ketentuan_All_Jan22.pdf';
									$url_pdf_jpg = 'Syarat_ketentuan_All.pdf';
								}
							
							}
							////////////////////////////////////////////////////
							
							
						}
						else //VISA
						{
							#if (substr($no_card_dat,0,6) == "436502") //BRI TOUCH 
							if ( substr($no_card_dat,0,8) == "43650201" || substr($no_card_dat,0,8) == "43650202" ) //BRI TOUCH 
							{
								#$url_pdf_jpg = 'Syarat_ketentuan_All_Card_01.pdf';
								#$url_pdf_jpg = 'Syarat_ketentuan_TOUCH.pdf';
								#$url_pdf_jpg = 'Syarat_ketentuan_All_Jan22.pdf';
								$url_pdf_jpg = 'Syarat_ketentuan_All.pdf';
							}else{
								#$url_pdf_jpg = 'Syarat_ketentuan_TOUCH.pdf';
								#$url_pdf_jpg = 'Syarat_ketentuan_All_Jan22.pdf';
								$url_pdf_jpg = 'Syarat_ketentuan_All.pdf';
							}
							
							
						}
					}
					elseif (substr($no_card_dat,0,1) == "5") //MASTER
					{
						//55200205
						if ( substr($no_card_dat,0,6) == "518828" || substr($no_card_dat,0,6) == "518856" )  // gold dan silver
						{
							if (  substr($no_card_dat,0,8) == "51885633" ) //BNP GOLD - 20180109 kz //AGRO
							{
								#$url_pdf_jpg = 'Syarat_ketentuan_MASTER.pdf';
								#$url_pdf_jpg = 'Syarat_ketentuan_All_Jan22.pdf';
								$url_pdf_jpg = 'Syarat_ketentuan_All.pdf';
							}
							elseif ( substr($no_card_dat,0,8) == "51885603" || substr($no_card_dat,0,8) == "51885613")  //World Access
							{
								#$url_pdf_jpg = 'Promo_World_Access.jpg';
								#$url_pdf_jpg = 'Syarat_ketentuan_World_Access.pdf';
								#$url_pdf_jpg = 'Syarat_ketentuan_All_Jan22.pdf';
								$url_pdf_jpg = 'Syarat_ketentuan_All.pdf';
								
								if (  substr($no_card_dat,0,8) == "51885613" ) 
								{
									#$url_pdf_jpg = 'Promo_World_Access_Pekerja.jpg';
									#$url_pdf_jpg = 'Syarat_ketentuan_World_Access.pdf';
									#$url_pdf_jpg = 'Syarat_ketentuan_All_Jan22.pdf';
									$url_pdf_jpg = 'Syarat_ketentuan_All.pdf';
								}
							}
							elseif ( substr($no_card_dat,0,8) == "51882852" ) //Wonderful Indonesia
							{
								#$url_pdf_jpg = 'Syarat_ketentuan_MASTER.pdf';
								#$url_pdf_jpg = 'Syarat_ketentuan_All_Jan22.pdf';
								$url_pdf_jpg = 'Syarat_ketentuan_All.pdf';
							}
							elseif ( substr($no_card_dat,0,8) == "51882892" ) //PROMOT3R - 20180109 kz
							{
								#$url_pdf_jpg = 'Syarat_ketentuan_MASTER.pdf';
								#$url_pdf_jpg = 'Syarat_ketentuan_All_Jan22.pdf';
								$url_pdf_jpg = 'Syarat_ketentuan_All.pdf';
							}else{
								
								if ( substr($no_card_dat,0,8) == "51882801" || substr($no_card_dat,0,8) == "51882821" || substr($no_card_dat,0,8) == "51882841" 
								|| substr($no_card_dat,0,8) == "51885601" || substr($no_card_dat,0,8) == "51885621" )
								{
									#$url_pdf_jpg = 'Promo_Easy_Card_Pekerja.jpg';
									#$url_pdf_jpg = 'Promo_Easy_Card.jpg';
									#$url_pdf_jpg = 'Syarat_ketentuan_MASTER.pdf';
									#$url_pdf_jpg = 'Syarat_ketentuan_All_Jan22.pdf';
									$url_pdf_jpg = 'Syarat_ketentuan_All.pdf';
								}else if ( substr($no_card_dat,0,8) == "51882802" || substr($no_card_dat,0,8) == "51882822" || substr($no_card_dat,0,8) == "51882842" 
								|| substr($no_card_dat,0,8) == "51885602" || substr($no_card_dat,0,8) == "51885622" || substr($no_card_dat,0,8) == "51885652" )
								{
									
									#$url_pdf_jpg = 'Syarat_ketentuan_MASTER.pdf';
									#$url_pdf_jpg = 'Syarat_ketentuan_All_Jan22.pdf';
									$url_pdf_jpg = 'Syarat_ketentuan_All.pdf';
								}else{
									#$url_pdf_jpg = 'Syarat_ketentuan_MASTER.pdf';
									#$url_pdf_jpg = 'Syarat_ketentuan_All_Jan22.pdf';
									$url_pdf_jpg = 'Syarat_ketentuan_All.pdf';
								}
								
								
							}
							
						//}elseif ( substr($no_card_dat,0,8) == "55200233" ) {  //BNP Platinum
							//#$url_pdf_jpg = 'Syarat_ketentuan_MASTER.pdf';
							
						}else if ( substr($no_card_dat,0,8) == "54758201" || substr($no_card_dat,0,8) == "54758202" || substr($no_card_dat,0,8) == "54758203" ) { //Business Card
							#$url_pdf_jpg = 'Syarat_ketentuan_MASTER.pdf';
							#$url_pdf_jpg = 'Syarat_ketentuan_All_Jan22.pdf';
							$url_pdf_jpg = 'Syarat_ketentuan_All.pdf';
							
						}else if ( substr($no_card_dat,0,8) == "55200201" || substr($no_card_dat,0,8) == "55200202" ) { //Platinum
							#$url_pdf_jpg = 'Syarat_ketentuan_MASTER.pdf';
							#$url_pdf_jpg = 'Syarat_ketentuan_All_Jan22.pdf';
							$url_pdf_jpg = 'Syarat_ketentuan_All.pdf';
							
							if ( substr($no_card_dat,0,8) == "55200201" )
							{
								#$url_pdf_jpg = 'Promo_Platinum_Pekerja.jpg';
								#$url_pdf_jpg = 'Syarat_ketentuan_MASTER.pdf';
								#$url_pdf_jpg = 'Syarat_ketentuan_All_Jan22.pdf';
								$url_pdf_jpg = 'Syarat_ketentuan_All.pdf';
							}
							
						}else if ( substr($no_card_dat,0,8) == "55347901" || substr($no_card_dat,0,8) == "55347902" || substr($no_card_dat,0,8) == "55347903" ) { //Platinum
							#$url_pdf_jpg = 'Syarat_ketentuan_MASTER.pdf';
							#$url_pdf_jpg = 'Syarat_ketentuan_All_Jan22.pdf';
							$url_pdf_jpg = 'Syarat_ketentuan_All.pdf';
						}else if ( substr($no_card_dat,0,8) == "55200233" ) { //Ovo
							$url_pdf_jpg = 'Syarat_ketentuan_BRI_OVO.pdf';
						}
						else if ( substr($no_card_dat,0,8) == "55200205" ) 
						{ //nex card
							$url_pdf_jpg = 'Syarat_ketentuan_BRI_NEXCARD.pdf';
						}
						else{
							//$url_pdf = '';
							#$url_pdf_jpg = 'Syarat_ketentuan_MASTER.pdf';
							#$url_pdf_jpg = 'Syarat_ketentuan_All_Jan22.pdf';
							$url_pdf_jpg = 'Syarat_ketentuan_All.pdf';
						}
						
					}
					elseif (substr($no_card_dat,0,1) == "3") //JCB
					{
						
							if (substr($no_card_dat,0,6) == "356510") //JCB 
							{
								#$url_pdf_jpg = 'Syarat_ketentuan_All_Card_03.pdf';
								#$url_pdf_jpg = 'Syarat_ketentuan_JCB.pdf';
								#$url_pdf_jpg = 'Syarat_ketentuan_All_Jan22.pdf';
								$url_pdf_jpg = 'Syarat_ketentuan_All.pdf';
							}else{
								#$url_pdf_jpg = 'Syarat_ketentuan_JCB.pdf';
								#$url_pdf_jpg = 'Syarat_ketentuan_All_Jan22.pdf';
								$url_pdf_jpg = 'Syarat_ketentuan_All.pdf';
							}
							
						
					}
					
					
					
					
		return $url_pdf_jpg;
	}
	
	function formatBytes($bytes, $precision = 2) 
	{
		$units  = array();
		$units[]='kB';
		$units[]='MB';
		$units[]='GB';
		$units[]='TB';
		#$units = ['Byte', 'Kilobyte', 'Megabyte', 'Gigabyte', 'Terabyte'];
		$i = 0;
		while($bytes > 1024) {
			$bytes /= 1024;
			$i++;
		}
		return round($bytes, $precision) . ' ' . $units[$i];
	}
	
	function cek_log_error_kirim($email)
	{
		$email = trim($email);
		$sql= "SELECT * FROM log_error_kirim WHERE lower(email) = lower('$email') 
		AND error_info like '%recipients failed%'
		AND tgl_kirim between (now() - interval '6 hours')::timestamp without time zone
		AND now()
		order by log_error_kirim_id desc limit 1";
			$qry = pg_query($sql) or die($sql);
			$row1 = pg_num_rows($qry);
			if($row1>0){
			
				$status_email_valid = false;
				if ( preg_match("/@gmail.com/i", $email)  )
				{
					$status_email_valid = true;
				}
				
				if ( preg_match("/@gmail.co.id/i", $email)  )
				{
					$status_email_valid = true;
				}
				
				
				if ($status_email_valid === false)
				{
					$dt = pg_fetch_assoc($qry);
					$antrian_id = $dt['antrian_id'];
					$loading_id = $dt['loading_id'];
					$sql2 = "UPDATE antrian_email
					   SET kode_kirim=99
					 WHERE antrian_id = $antrian_id AND lower(email) = lower('$email')
					 AND loading_id=$loading_id;";
					 $qry2 = pg_query($sql2) or die($sql2);
				}
				
				return 'ada gagal';
			}else{
				return 'kosong';
			}
		
	}
	
	
	
	
	function template_email_bri_tokopedia($template_id, $nomor_customer_x , $nomor_rekening)
	{
	
	
		$flag_z ='';
		if ( preg_match("/43597201/i", $nomor_customer_x) )
		{
			$flag_z = 'tokopedia';
		}else if ( preg_match("/43597202/i", $nomor_customer_x) ){
			$flag_z = 'tokopedia';
		}else if ( preg_match("/43596501/i", $nomor_customer_x) ){
			$flag_z = 'bri_btn';
		}else if ( preg_match("/43596502/i", $nomor_customer_x) ){
			$flag_z = 'bri_btn';
		}else if ( preg_match("/55200233/i", $nomor_customer_x) ){
			$flag_z = 'ovo';
		}else if ( preg_match("/55200205/i", $nomor_customer_x) ){
			$flag_z = 'nexcard';
		}else if ( preg_match("/43596503/i", $nomor_customer_x) ){
			$flag_z = 'samsung';
		}else if ( preg_match("/43596504/i", $nomor_customer_x) ){
			$flag_z = 'samsung';
		}
		else{
			$flag_z = 'normal';
		}
		
		
		
		if ( $flag_z == 'bri_btn' )
		{
			$no_card_dat = $nomor_rekening;
				if (
							substr($no_card_dat,0,16) == "4359650100032409" || substr($no_card_dat,0,16) == "4359650100028605" ||
							substr($no_card_dat,0,16) == "4359650100027201" || substr($no_card_dat,0,16) == "4359650100033704" ||
							substr($no_card_dat,0,16) == "4359650100005009" || substr($no_card_dat,0,16) == "4359650200026400" ||
							substr($no_card_dat,0,16) == "4359650100011007" || substr($no_card_dat,0,16) == "4359650100033209" ||
							substr($no_card_dat,0,16) == "4359650280004608" || substr($no_card_dat,0,16) == "4359650200014000" ||
							substr($no_card_dat,0,16) == "4359650100005108" || substr($no_card_dat,0,16) == "4359650180007107" ||
							substr($no_card_dat,0,16) == "4359650100029306" || substr($no_card_dat,0,16) == "4359650100031500" ||
							substr($no_card_dat,0,16) == "4359650180003403" || substr($no_card_dat,0,16) == "4359650100023200"
							)
							{
								$flag_z = 'normal';
							}
			
		}
	
			
			
		if($flag_z == 'tokopedia')
		{
			$sql= "SELECT * FROM template_email WHERE flagtrans = 'BC' AND status='t' 
			AND lower(subject_email) like lower('%tokopedia%')
			ORDER BY template_email_id DESC  LIMIT 1";
			$qry = pg_query($sql) or die($sql);
			$row1 = pg_num_rows($qry);
			if($row1>0){
				$dt = pg_fetch_assoc($qry);
				return $dt['template_email_id'];
			}else{
				return $template_id;
			}
		}else if($flag_z == 'bri_btn'){
			$sql= "SELECT * FROM template_email WHERE flagtrans = 'BC' AND status='t' 
			AND lower(subject_email) like lower('%btn%')
			ORDER BY template_email_id DESC  LIMIT 1";
			$qry = pg_query($sql) or die($sql);
			$row1 = pg_num_rows($qry);
			if($row1>0){
				$dt = pg_fetch_assoc($qry);
				return $dt['template_email_id'];
			}else{
				return $template_id;
			}
		}else if($flag_z == 'ovo'){
			$sql= "SELECT * FROM template_email WHERE flagtrans = 'BC' AND status='t' 
			AND lower(subject_email) like lower('%ovo%')
			ORDER BY template_email_id DESC  LIMIT 1";
			$qry = pg_query($sql) or die($sql);
			$row1 = pg_num_rows($qry);
			if($row1>0){
				$dt = pg_fetch_assoc($qry);
				return $dt['template_email_id'];
			}else{
				return $template_id;
			}
		}
		else if($flag_z == 'nexcard'){
			$sql= "SELECT * FROM template_email WHERE flagtrans = 'BC' AND status='t' 
			AND lower(subject_email) like lower('%nex%')
			ORDER BY template_email_id DESC  LIMIT 1";
			$qry = pg_query($sql) or die($sql);
			$row1 = pg_num_rows($qry);
			if($row1>0){
				$dt = pg_fetch_assoc($qry);
				return $dt['template_email_id'];
			}else{
				return $template_id;
			}
		}
		else if($flag_z == 'samsung'){
			$sql= "SELECT * FROM template_email WHERE flagtrans = 'BC' AND status='t' 
			AND lower(subject_email) like lower('%samsung%')
			ORDER BY template_email_id DESC  LIMIT 1";
			$qry = pg_query($sql) or die($sql);
			$row1 = pg_num_rows($qry);
			if($row1>0){
				$dt = pg_fetch_assoc($qry);
				return $dt['template_email_id'];
			}else{
				return $template_id;
			}
		}
		else{
			$sql= "SELECT * FROM template_email WHERE flagtrans = 'BC' AND status='t' 
			AND lower(subject_email) not like lower('%tokopedia%')
			AND lower(subject_email) not like lower('%btn%')
			AND lower(subject_email) not like lower('%ovo%')
			AND lower(subject_email) not like lower('%nex%')
			AND lower(subject_email) not like lower('%samsung%')
			AND lower(subject_email) not like lower('%testing%')
			ORDER BY template_email_id DESC  LIMIT 1";
			$qry = pg_query($sql) or die($sql);
			$row1 = pg_num_rows($qry);
			if($row1>0){
				$dt = pg_fetch_assoc($qry);
				return $dt['template_email_id'];
			}else{
				return $template_id;
			}
		}
	}
	
	
	
	
	
	
	
	
	
	
	function prioritas_kirim_antrian_email($no_card_dat , $nomor_customer_x='')
	{
		
		/*
		//Prioritas dari BRI 20230120
		- Nasabah Cobrand Tokopedia
		- Nasabah CObrand Selain Tokopedia
		- Nasabah Infinite & Platinum
		- sisanya


tokopedia
43597201
43597202

///////////////////


BTN Reguler
43596501

BTN Platinum
43596502

Ovo
55200233

TRAVELOKA
43650203

PEGADAIAN
43650204

NEXCARD
55200205

//////////////////////


Infinite
46874001
46874002



Platinum
55200201
55200202

Platinum
55347901
55347902

//////////////////////

BRI TOUCH
43650201
43650202





AGRO
51885633

World access
51885603
51885613

Wonderful Indonesia
51882852

PROMOT3R
51882892

Easy Cash
51882801
51882821
51882841
51885601
51885621

kartu blm diketahui
51882802
51882822
51882842
51885602
51885622
51885652

BNP Platinum
55200233

Business Card
54758201
54758202
54758203

JCB
356510



1. Nasabah Cobranding Tokopedia (BIN : 43597201 dan 43597202)

2. Nasabah Cobranding selain Tokopedia (OVO : 55200233, BTN : 43596501 dan 43596502, Traveloka : 43650203, Pegadaian : 43650204)

3. Nasabah Infinite & Platinum (BIN Infinite : 46874001 dan 46874002, Platinum, 55200201 dan    55200202)

4. Nasabah Reguler (diluar poin 1 sd.3)
		*/
		
		$kode_prioritas = 5;
		if ( preg_match("/43597201/i", $nomor_customer_x) )
		{
			//$flag_z = 'tokopedia';
			$kode_prioritas = 1;
		}else if ( preg_match("/43597202/i", $nomor_customer_x) ){
			//$flag_z = 'tokopedia';
			$kode_prioritas = 1;
		}else if ( preg_match("/43596501/i", $nomor_customer_x) ){
			//$flag_z = 'bri_btn';
			$kode_prioritas = 2;
		}else if ( preg_match("/43596502/i", $nomor_customer_x) ){
			//$flag_z = 'bri_btn';
			$kode_prioritas = 2;
		}else if ( preg_match("/55200233/i", $nomor_customer_x) ){
			//$flag_z = 'bri_ovo';
			$kode_prioritas = 2;
		}else if ( preg_match("/43650203/i", $nomor_customer_x) ){
			//$flag_z = 'bri_traveloka';
			$kode_prioritas = 2;
		}else if ( preg_match("/43650204/i", $nomor_customer_x) ){
			//$flag_z = 'bri_pegadaian';
			$kode_prioritas = 2;
		}
		else if ( preg_match("/55200205/i", $nomor_customer_x) ){
			//$flag_z = 'bri_NEXCARD';
			$kode_prioritas = 2;
		}
		else if ( preg_match("/43596503/i", $nomor_customer_x) ){
			//$flag_z = 'bri_SAMSUNG';
			$kode_prioritas = 2;
		}
		else if ( preg_match("/43596504/i", $nomor_customer_x) ){
			//$flag_z = 'bri_SAMSUNG';
			$kode_prioritas = 2;
		}
		else if ( preg_match("/46874001/i", $nomor_customer_x) ){
			//$flag_z = 'bri_Infinite';
			$kode_prioritas = 3;
		}else if ( preg_match("/46874002/i", $nomor_customer_x) ){
			//$flag_z = 'bri_Infinite';
			$kode_prioritas = 3;
		}else if ( preg_match("/55200201/i", $nomor_customer_x) ){
			//$flag_z = 'bri_platinum';
			$kode_prioritas = 3;
		}else if ( preg_match("/55200202/i", $nomor_customer_x) ){
			//$flag_z = 'bri_platinum';
			$kode_prioritas = 3;
		}else if ( preg_match("/55347901/i", $nomor_customer_x) ){
			//$flag_z = 'bri_platinum';
			$kode_prioritas = 3;
		}else if ( preg_match("/55347902/i", $nomor_customer_x) ){
			//$flag_z = 'bri_platinum';
			$kode_prioritas = 3;
		}else{
			//$flag_z = 'normal';
			$kode_prioritas = 5;
		}
					
					
					
		return $kode_prioritas;
	}
	
	
?>