<?php 


$timezone="Asia/Jakarta";
date_default_timezone_set($timezone);
include "include/config.php";
include "include/function.php";
$_SESSION['userid']='otomatis';

$con = pg_connect($connection) or die("Could not connect to database!");
	
$blth = GetBLTH(date('mY'),-1);
$tabeldetail = "tr_email_".GetBLTH(date('mY'),-1)."_bc";

$sql ="
TRUNCATE TABLE _tr_email_log_bulan_lalu;
INSERT INTO _tr_email_log_bulan_lalu(tr_email_id, nomor_customer, nomor_rekening, email, email_sukses, 
       date_email_send, date_email_callback, ket_error, email_callback, 
       count_sent, loading_id, antrian_id, template_id, nama_customer, 
       tgl_read, status_sample, body_email_read, read_method, delay_info, 
       info_device, tipe_gagal, respon, tgl_read2, kode_kirim, k_nama_body_email, 
       k_lampiran_email, waktu_update
			)


  SELECT

  tr_email_id, nomor_customer, nomor_rekening, email, email_sukses, 
       date_email_send, date_email_callback, ket_error, email_callback, 
       count_sent, loading_id, antrian_id, template_id, nama_customer, 
       tgl_read, status_sample, body_email_read, read_method, delay_info, 
       info_device, tipe_gagal, respon, tgl_read2, kode_kirim, k_nama_body_email, 
       k_lampiran_email, now()
      from $tabeldetail 
where status_sample='f'
order by tr_email_id asc
";
			//die($sql);
			$query = pg_query($sql) or die("Invalid query!" . $sql) ;
		
		if ( $query )
		{
			echo "berhasil insert";
		}else{
			echo "gagal insert";
		}
	
	
	


pg_close($con);
?>