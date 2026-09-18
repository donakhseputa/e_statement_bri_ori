<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/function.php"); ?>
<?php
	$act = $_REQUEST['act'];
	$con = pg_connect($connection) or die('Could not connect to database!');
	
	switch($act){
		case 'log_customer'			: log_customer(); break;
		case 'show_customer'		: show_customer(); break;
		case 'editcust'				: editcust(); break;
		case 'kirim_ulang'			: kirim_ulang(); break;
	}
	
	function editcust(){
		$m_customer_id = $_REQUEST['m_customer_id'];
		$txt_norek = $_REQUEST['txt_norek'];
		$txt_email = $_REQUEST['txt_email'];
		
		$sqlupdcust = " INSERT INTO m_customer_history 
						SELECT *,now() AS date_created FROM m_customer WHERE m_customer_id=".$m_customer_id;
			
		$qryupdcust = pg_query($sqlupdcust) or die('ERROR SQL Upd Cust: '.$sqlupdcust);
		
		$sqlupdcust = " UPDATE m_customer SET email1='".$txt_email."' WHERE m_customer_id=".$m_customer_id;
		
		$qryupdcust = pg_query($sqlupdcust) or die('ERROR SQL Upd Cust: '.$sqlupdcust);
		
		if($qryupdcust){
			echo "Edit Customer dengan nomer rekening ".$txt_norek." berhasil";
		}
	}
	
	function log_customer(){
		?>
		<script type="text/javascript" src="../script/vtips/vtip.js"></script>
        <style>
        .vtip {
            cursor:help;
        }
        
        .vtip input {
            cursor:help;
        }
        
        p#vtip {
            display: none;
            position: absolute;
            padding: 2px;
            margin:auto;
            left: 5px;
            font-size: 12px;
            background-color: #CCFFCC;
            border: 1px solid #666666;
            -moz-border-radius: 5px;
            -webkit-border-radius: 5px;
            z-index: 9999;
        }
        
        p#vtip #vtipArrow {
            position: absolute;
            top: -10px;
            left: 5px
        }      
        </style>
		<?php
		$blth = $_REQUEST['blth'];
		$blth_report = $_REQUEST['blth_report'];
		$flagtrans = $_REQUEST['flagtrans'];
		$offset = $_REQUEST['offset'];
		$input_search = $_REQUEST['input_search'];
		$input_search = str_replace('8764346466435364647768799667654537543756',' ',$input_search);
		$limit = 50;
		$i = $offset;
		$c = 0;
		
		$sql_sel = "SELECT 
		log_customer_ku_id, userid, to_char(create_date,'yyyy-mm-dd HH24:mi:ss') as create_date, nama_file, total_customer, noexist, location_noexist, status_ku  
		FROM log_customer_ku 
		WHERE blth = '$blth' and flagtrans = '$flagtrans' and (upper(nama_file) like upper('%".$input_search."%') or upper(userid) like upper('%".$input_search."%'))  
		ORDER BY create_date DESC";
		$sql_show = $sql_sel." LIMIT $limit OFFSET $offset";
		$qry_sel = pg_query($sql_sel) or die('ERROR select: '.$sql_sel);
		$qry_show = pg_query($sql_show) or die('ERROR show: '.$sql_show);
		$jumlah_data = pg_num_rows($qry_sel);
		?>
        <table width="100%" align="center" cellpadding="1" cellspacing="2" border="0">
        	<tr class="table_header">
            	<td width="5%">NO</td>
            	<td width="12%" align="left">User</td>
            	<td width="12%">Tanggal Loading</td>
            	<td width="20%" align="left">Nama File</td>
            	<td width="12%">Total <i>Customer</i></td>
            	<td width="15%">Total <i>No-Exist</i></td>
            	<td width="10%">ACT</td>
            </tr>
        <?php
		while($row_show=pg_fetch_array($qry_show)){
			$i++;
			$log_customer_id = $row_show['log_customer_ku_id'];
			$userid = $row_show['userid'];
			$create_date = $row_show['create_date'];
			$nama_file = $row_show['nama_file'];
			$total_customer = $row_show['total_customer'];
			$total_customer_noexist = $row_show['noexist'];
			
			if($i%2==0){
				$cls = 'table_row_odd';
			}else{
				$cls = 'table_row_even';
			}
			
			?>
            <tr class="<?php echo $cls?>">
            	<td align="center" height="30"><?=$i?></td>
            	<td><?php echo $userid?></td>
            	<td align="center"><?php echo $create_date?></td>
            	<td><a href="../tmp/<?php echo $nama_file?>" target="_blank"><?php echo $nama_file?></a></td>
            	<td align="right"><?php echo number_format($total_customer,0,' ','')?></td>
            	<td align="right"><?php echo number_format($total_customer_noexist,0,' ','')?>&nbsp;
				<a href="<?php echo $row_show['location_noexist'];?>" target="_blank">[rekap]</a>
				</td>
            	<td align="center">
				<a onclick="show_cust(<?php echo $log_customer_id?>)"><img title="view" src="../images/icons/icon_view.gif" /></a>
				<?php if($row_show['status_ku']=='0') { ?>
				<a onclick="if(confirm('Proses ini akan membuat cycle baru sesuai dengan Daftar KU yang telah diupload...\n\Apakah Anda Ingin Melanjutkan?')){ kirim_ulang('<?php echo $log_customer_id?>','<?php echo $blth;?>','<?php echo $flagtrans;?>','<?php echo $blth_report;?>'); } " <?php echo $disabled;?>><img title="KIRIM ULANG" src="../images/icons/rfrsh_data.png" /></a>
				<?php } ?>
				</td>
            </tr>
            <?php
		}
		?>
            <tr>
                <td align="center" colspan="11">
                    <?php
                        echo paging($offset,$jumlah_data,$limit,'div_customer','script_loading_customer_ku.php','act=log_customer&blth='.$blth.'&flagtrans='.$flagtrans.'&input_search='.$input_search);
                    ?>
                </td>
            </tr>
        </table>
        <?php
	}
	
	function show_customer(){
		$log_customer_id = $_REQUEST['log_customer_id'];
		$input_search = $_REQUEST['input_search'];
		$input_search = str_replace('8764346466435364647768799667654537543756','%',$input_search);
		
		$offset = $_REQUEST['offset'];
		$limit = 100;
		$i = $offset;
		$c = 0;
		
		$sql_sel = "SELECT 
		m_customer_ku_id, blth, nomor_customer, nomor_rekening, email1 
		FROM m_customer_ku 
		WHERE log_customer_ku_id = '$log_customer_id' and (upper(nomor_customer) like upper('%".$input_search."%') or upper(nomor_rekening) like upper('%".$input_search."%') or upper(email1) like upper('%".$input_search."%')) 
		AND existed_mcust = '1' 
		ORDER BY nomor_rekening";
		#echo $sql_sel;
		$sql_show = $sql_sel." LIMIT $limit OFFSET $offset";
		$qry_sel = pg_query($sql_sel) or die('ERROR select: '.$sql_sel);
		$qry_show = pg_query($sql_show) or die('ERROR show: '.$sql_show);
		$jumlah_data = pg_num_rows($qry_sel);
		?>
        <table width="100%" align="center" cellpadding="1" cellspacing="2" border="0">
        	<input type="hidden" id="log_cust_id" value="<?php echo $log_customer_id?>" />
        	<tr class="table_header">
            	<td width="5%" align="left">&nbsp;NO</td>
            	<td width="25%" align="center">Nomor Rekening</td>
            	<td align="left">e-Mail</td>
            </tr>
        <?php
		while($row_show=pg_fetch_array($qry_show)){
			$i++;
			$nomor_customer = $row_show['nomor_customer'];
			$nomor_rekening = $row_show['nomor_rekening'];
			$email = $row_show['email1'];
			$custid = $row_show['m_customer_id'];
			$blth = $row_show['blth'];
			
			if($i%2==0){
				$cls = 'table_row_odd';
			}else{
				$cls = 'table_row_even';
			}
			?>
            <tr class="<?php echo $cls?>">
            	<td align="left">&nbsp;<?php echo $i?></td>
            	<td align="center"><?php echo $nomor_rekening?></td>
            	<td><?php echo $email?></td>
			</tr>
            <?php
		}
		?>
            <tr>
                <td align="center" colspan="11">
                    <?php
                        echo paging($offset,$jumlah_data,$limit,'div_customer','script_loading_customer_ku.php','act=show_customer&blth='.$blth.'&flagtrans='.$flagtrans.'&log_customer_id='.$log_customer_id.'&input_search='.$input_search);
                    ?>
                </td>
            </tr>
        </table>
        <?php
	}	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function kirim_ulang(){
		$log_customer_id = $_REQUEST['log_customer_id'];
		$flagtrans = $_REQUEST['flagtrans'];
		$blth = $_REQUEST['blth'];
		//die ( $blth );
		$nomor_rekening = "";
		$detail_id = "";
		
		$blth_report = $_REQUEST['blth_report'];
		
		$blth_balik = substr($blth,-4).substr($blth,0,2);
		
			$BLTH_BARUS = BLTH_BARU;
			if($blth_balik>$BLTH_BARUS){
				$tabeldetail = strtolower("detail_".$blth."_".$flagtrans);
				$tabeldetail2 = strtolower("detail_".$blth_report."_".$flagtrans);
			}else{
				$tabeldetail = "detail";
			}
		
		//NAMA CYCLE KU BARU
		$sql_nama = "SELECT nama_file FROM log_customer_ku WHERE log_customer_ku_id='$log_customer_id'";
		$exe_nama = @pg_query($sql_nama)or die('ERROR SELECT CYCLE KU: '.$sql_nama);
		$row_nama = pg_fetch_array($exe_nama);
		$namacycle_KU = str_replace(" ","_",$row_nama['nama_file']);
		
		//NAMA CYCLE KU BARU
		$KUcycle = "KU_" . str_replace(array("KU",".TXT","_"),"",strtoupper($namacycle_KU)) . ".CCSTMNCBS";	//KHUSUS UNTUK NISP, NAMA FILE SANGAT PANJANG, E-STATEMENT LAIN CUKUP 4 KARAKTER .XXX
		//INSERT m_loading BARU
		$m_loading_id = m_loading($flagtrans,$blth_report,$KUcycle);
		
		//AMBIL NOMOR REKENING PADA DAFTAR KU
		$sql_sel = "SELECT nomor_rekening FROM  m_customer_ku WHERE log_customer_ku_id='$log_customer_id' AND existed_mcust='1'";
		$exe_sel = @pg_query($sql_sel)or die('ERROR AMBIL NOMOR REKENING: '.$sql_sel);
		$n_sel = pg_num_rows($exe_sel);
		$total_cust = 0;
		while($row_sel = pg_fetch_array($exe_sel)){
			$nomor_rekening .= "'".$row_sel['nomor_rekening']."',";
			$total_cust +=1;
			
			$sql_sel2 = "SELECT detail_id FROM $tabeldetail WHERE nomor_rekening='".$row_sel['nomor_rekening']."' AND blth='$blth' AND flagtrans='$flagtrans'  AND substring(nama_file from 1 for 3) != 'KU_'  ORDER BY detail_id DESC LIMIT 1";
			$exe_sel2 = @pg_query($sql_sel2)or die('ERROR AMBIL NOMOR REKENING: '.$sql_sel2);
			$row_sel2 = pg_fetch_array($exe_sel2);
			$detail_id .= $row_sel2['detail_id'] . ",";
			
			
		}
		
		//BARIS NOMOR REKENING KU
		$nomor_rekening = rtrim($nomor_rekening,",");
		$detail_id = rtrim($detail_id,",");
		
		
		//BUAT FOLDER BARU UNTUK PDF
		$loc_pdf_baru="../pdf/";
		if(!file_exists($loc_pdf_baru)) mkdir($loc_pdf_baru)or die('err1');
		$loc_pdf_baru.= $flagtrans."/";
		if(!file_exists($loc_pdf_baru)) mkdir($loc_pdf_baru)or die('err1');
		$loc_pdf_baru.= $blth_report."/";
		if(!file_exists($loc_pdf_baru)) mkdir($loc_pdf_baru)or die('err1');
		$loc_pdf_baru.= str_replace(".CCSTMNCBS","",strtoupper($KUcycle));
		if(!file_exists($loc_pdf_baru)) mkdir($loc_pdf_baru)or die('err1');
		
		
		//AMBIL PDF LAMA
		$sql_pdf = "SELECT flagtrans, nama_file, pdf_name FROM $tabeldetail WHERE blth='$blth' AND flagtrans='$flagtrans'  AND detail_id IN($detail_id) ORDER BY detail_id ASC";
		$exe_pdf = pg_query($sql_pdf);
		while($row_pdf = pg_fetch_array($exe_pdf)){
			$pdf_name = $row_pdf['pdf_name'];
			$nama_file = str_replace(".CCSTMNCBS","", strtoupper($row_pdf['nama_file']));
			$loc_pdf_lama = "../pdf/" . $flagtrans . "/" . $blth . "/" . $nama_file ;
			//COPY PDF KE FOLDER BARU
			copy($loc_pdf_lama.'/'.$pdf_name.'.pdf', $loc_pdf_baru.'/'.$pdf_name.'.pdf');
		}
		
		
		//INSERT DETAIL KU SEBAGAI CYCLE BARU
		
		
		//CREATE TABLE BARU
		

			$tabeldetail_pk = $tabeldetail2."_p_k";
			$tabeldetail_un = $tabeldetail2."_unique";
			
			$cek_tabel = "SELECT detail_id FROM $tabeldetail2";
			$exe_tabel = @pg_query($cek_tabel);
			if(!$exe_tabel)
			{
				$buat_tabel = "CREATE TABLE $tabeldetail2
								(						
								 detail_id serial NOT NULL,
								  m_loading_id integer,
								  nomor_customer character varying(20),
								  nomor_rekening character varying(20),
								  nama character varying(60),
								  alamat1 character varying(200),
								  alamat2 character varying(200),
								  alamat3 character varying(200),
								  alamat4 character varying(200),
								  alamat5 character varying(200),
								  city character varying(40),
								  zipcode character varying(9),
								  flagtrans character varying(2),
								  blth character varying(6),
								  nama_file character varying(50),
								  pdf_name character varying(100),
								  password_pdf character varying(25),
								  jml_hlm integer,
								  flag_attach character varying(3),
								  tipe_kartu character varying(40),
								  no_rek_asli character varying(20),
								  ket_produk character varying(60),
								  email text,
								  n_email integer DEFAULT 1,
								  size_pdf numeric(6,2),
								  barcode text,
								  nama_produk text,
								  tanggal text,
								  total_produk text,
								  kode_cab character varying(10),
								  cabang character varying(50),
								  CONSTRAINT $tabeldetail_pk PRIMARY KEY (detail_id),
								  CONSTRAINT $tabeldetail_un UNIQUE (nomor_rekening, nama_file, blth)
								) 
								WITHOUT OIDS;
								ALTER TABLE $tabeldetail2 OWNER TO postgres;";
				$exe_tabel = @pg_query($buat_tabel);
			}
		
		//CREATE TABLE BARU

			
		$sql_ins_detail = "INSERT INTO $tabeldetail2(m_loading_id,nomor_customer,nomor_rekening,nama,alamat1,alamat2,alamat3,city,zipcode,
flagtrans,blth,nama_file,pdf_name,password_pdf,jml_hlm,flag_attach,tipe_kartu,no_rek_asli,ket_produk,email,n_email,size_pdf,barcode)
SELECT '$m_loading_id' AS m_loading_id,b.nomor_customer,b.nomor_rekening,b.nama,b.alamat1,b.alamat2,b.alamat3,b.city,b.zipcode,
b.flagtrans, '$blth_report', '$KUcycle' AS nama_file,b.pdf_name,b.password_pdf,b.jml_hlm,b.flag_attach,b.tipe_kartu,b.no_rek_asli,b.ket_produk,b.email,b.n_email,b.size_pdf,b.barcode 
FROM $tabeldetail b WHERE b.blth='$blth' AND b.flagtrans='$flagtrans'  
AND b.detail_id IN($detail_id)";
		$exe_ins_detail = @pg_query($sql_ins_detail)or die('ERROR INSERT DETAIL: '.$sql_ins_detail);
		
		
		
		//UPDATE ALAMAT EMAIL DENGAN EMAIL KU
		$sql_view_detail = "SELECT detail_id, nomor_rekening FROM $tabeldetail2 WHERE m_loading_id='$m_loading_id'";
		$exe_view_detail = @pg_query($sql_view_detail);
		while($row_view_detail = pg_fetch_array($exe_view_detail)){
		$view_nomor_rekening = $row_view_detail['nomor_rekening'];
		$view_detail_id = $row_view_detail['detail_id'];
		#ADA KEMUNGKINAN EMAIL KU LEBIH DARI 1 UNTUK SETIAP NOMOR REKENING, MAKA UPDATE DENGAN MENGGUNAKAN EMAIL TERBARU DARI m_customer_ku
			$sql_cek = "SELECT email1 FROM m_customer_ku WHERE log_customer_ku_id='$log_customer_id' AND nomor_rekening = '$view_nomor_rekening'";
			$exe_cek = pg_query($sql_cek);
			$totalemailcustomernya = 0;
			$email_baru = "";
			while($row_m_customer = pg_fetch_array($exe_cek)){
				$totalemailcustomernya += 1;
				$email_baru .= $row_m_customer['email1']. ";";
				}
			if($totalemailcustomernya==1) $email_baru = rtrim($email_baru,";");
			
			//UPDATE EMAIL KU PADA CYCLE BARU
			$sql_update_email = "UPDATE $tabeldetail2 SET email='".addslashes($email_baru)."', n_email='$totalemailcustomernya' WHERE detail_id='$view_detail_id'";
			$exe_update_email = @pg_query($sql_update_email) or die("ERROR UPDATE: " . $sql_update_email);
			
		}
		
		
		
		//UDPATE m_loading
		update_m_loading_new($m_loading_id, $total_cust,$tabeldetail2);
		//UPDATE log_customer_ku
		update_status_log_customer_ku($log_customer_id);
		
		
		echo "<div align='center'>
		<span style='font-family:verdana;font-size:14px;font-weight:bold;text-align:center'>
		Cycle Baru Telah Dibuat... <br/>Silakan Lanjutkan Proses Kirim ke Antrian...<br/>
		Nama Cycle ini Adalah: <font color='red'><b>".$KUcycle."</b></font>
		</span>
		</div>";
		
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	function update_m_loading_new($m_loading_id, $total_cust,$tabeldetail){
	// Message
		$sql_total = "SELECT SUM(jml_hlm) AS total_hlm FROM $tabeldetail WHERE m_loading_id='$m_loading_id'";
		$exe_total = pg_query($sql_total);
		$row_total = pg_fetch_array($exe_total);
		$total_hlm = $row_total['total_hlm'];
		
		$sql_upd_m_load = "UPDATE m_loading
							SET
								total_halaman = '$total_hlm',
								total_customer = '$total_cust'	WHERE m_loading_id = '$m_loading_id'";
		$qry_upd_m_load = pg_query($sql_upd_m_load) or die('ERROR update m_loading: '.$sql_upd_m_load);
	}
	
	
	
	
	function m_loading($flagtrans,$blth,$file){
		$file = str_replace(".ccstmncbs",".CCSTMNCBS", $file);
		$sql_ins_m_loading = "INSERT INTO m_loading(
									userid,
									create_date,
									flagtrans,
									blth,
									loading_file
								)
								VALUES(
									'".$_SESSION['userid']."',
									now(),
									'$flagtrans',
									'$blth',
									'$file'
								)";
		$qry_ins_m_loading = pg_query($sql_ins_m_loading) or die('ERROR insert m_loading: '.$sql_ins_m_loading);
		
		$sql_sel_m_loading = "SELECT last_value FROM m_loading_loading_id_seq";
		$qry_sel_m_loading = pg_query($sql_sel_m_loading) or die('ERROR select m_loading: '.$sql_sel_m_loading);
		$row_sel_m_loading = pg_fetch_assoc($qry_sel_m_loading);
		$m_loading_id = $row_sel_m_loading['last_value'];
		
		return $m_loading_id;
	}
	
	function update_status_log_customer_ku($log_customer_id){
		$sql_upd_log_customer = "UPDATE log_customer_ku SET status_ku='1', ku_date=NOW() WHERE log_customer_ku_id = $log_customer_id";
		$qry_upd_log_customer = pg_query($sql_upd_log_customer) or die('ERROR update log_customer: '.$sql_upd_log_customer);
	}
	?>