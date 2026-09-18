<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/function.php"); ?>
<?
	ini_set('memory_limit', '-1');
	
		$con		= pg_connect($connection) or die("Could not connect to database!");
	
		$flagtrans	= 'CO';		
		$blth		= '052025';
		$file		= 'PTSTMTC_020525.DAT';
		$file2		= 'PTSTMTC_020525XX.DAT';
		//$loading_id	= 8052;
		$pr 		= 'CO|25';
		$tabeldetail= 'detail_052025_co';
		$norek 		= $_POST['norek'];
		$status 	= 1;
		$template_id = 10;
		$template_id_salah = 33;
		$tipe_attach = 1;;		
		//$email_sample = $_POST['text_sample_email'];
		//$ord = $_POST['pilihan_order'];
		$check_attach_auto = 'on';
		$jml_record = 0;
		//$tabeldetail = 'detail_052024_bc';
		
		//kueri norek yang mau di kirimkan
		$str_pdf1	= "	
			 select d.nomor_rekening, d.email 
			 from detail_052025_co d
			 where nomor_rekening IN( select nomor_rekening
			 from tr_email_052025_co 
			 where loading_id = 8052 and status_sample = FALSE
			 and template_id = $template_id_salah
			 group by nomor_rekening)
			 and d.m_loading_id = 8052
			 ";
		//echo $str_pdf1;die();
		$pg_pdf1	= pg_query($str_pdf1)or die("ERROR : ".$str_pdf1);
		$i=1;
		
		
		$tr_email_rev = 'tr_email_'.$blth.'_'.$flagtrans;
		
		// ************  20150306 Revisi ************ //
		//-- DROP VIEW $nama_view;
		$nama_view='vw_'.date('dmyhis');
		$sql_view = "
			CREATE OR REPLACE VIEW $nama_view AS 
					select 
					a.status_sample, a.tr_email_id, a.nomor_rekening, a.email, a.loading_id, a.date_email_send, a.email_callback, b.flagtrans, b.loading_file, b.blth
					from $tr_email_rev a
					LEFT JOIN m_loading b ON b.m_loading_id=a.loading_id
					where b.loading_file='$file2' AND a.template_id = $template_id_salah
					AND status_sample = 'f';
			";
		//ALTER TABLE $nama_view OWNER TO postgres;
		//die ( $sql_view );
		$qry_view = @pg_query($sql_view);
		// ************  20150306 Revisi ************ //
		//die();
		
		
		while($data_pdf1 = pg_fetch_object($pg_pdf1))
		{
			$norek	= $data_pdf1->nomor_rekening;
			
			
			////CEK EMBED CODE
			//$sql_cek = "SELECT isi_email FROM template_email WHERE template_email_id='$template_id'";
			//$exe_cek = pg_query($sql_cek);
			//$row_cek = pg_fetch_array($exe_cek);
			//$isi_template = $row_cek['isi_email'];
			//$cek_embed = substr_count($isi_template,'src="cid:1');
			//if($cek_embed>0){
			//	$cidpos = strpos($isi_template,'src="cid:1');
			//	$kodecid = substr($isi_template, $cidpos, 13);
			//	$kodecid = substr($kodecid, -4);
			//	$sql_cid = "SELECT m_attach_file_id FROM m_attach_file WHERE cid='$kodecid'";
			//	$exe_cid = pg_query($sql_cid);
			//	$row_cid = pg_fetch_array($exe_cid);
			//	$cid = $row_cid['m_attach_file_id'];
			//} else {
			//	$cid = "";
			//}
			
			$cid = "";
			
			//echo 'INI CID : '.$cid;die();
			//END CEK EMBED CODE			
			
			
			$sql_sel_cust_det = "SELECT b.nomor_customer, b.nomor_rekening, b.blth, b.flagtrans, b.email,
										b.m_loading_id, b.nama, b.nama_file, b.jml_hlm, c.tr_email_id, c.date_email_send, c.email_callback, b.pdf_name as pdf_name
									FROM $tabeldetail b 
										LEFT JOIN $nama_view c
											ON b.m_loading_id = c.loading_id
												and b.nomor_rekening = c.nomor_rekening
									WHERE b.blth = '".$blth."' AND b.email!=''";
			if($file!=''){
				$sql_sel_cust_det .= " and b.nama_file = '$file'";
			}
			if($norek!=''){
				$sql_sel_cust_det .= " and b.nomor_rekening = '$norek'";
			}
			if($status==1){
				$sql_sel_cust_det .= " and c.tr_email_id is null";
			}elseif($status==2){
				$sql_sel_cust_det .= " and c.tr_email_id is not null and c.email_callback is null";
			}elseif($status==3){
				$sql_sel_cust_det .= " and c.tr_email_id is not null and c.email_callback is not null";
			}
			$sql_sel_cust_det .= " $urut";
			//echo $sql_sel_cust_det;	die();
			$qry_sel_cust_det = pg_query($sql_sel_cust_det) or die('ERROR join customer dan detail: '.$sql_sel_cust_det);
			
			//$loading_id[] = array();
			//$nomor_customer[] = array();
			//$nomor_rekening[] = array();
			//$nama[] = array();
			//$blthnya[] = array();
			//$email[] = array();
			//$status_sample[] = array();
			//$pdf_name[] = array();
			//$pdf_location[] = array();
			//$flag_attach[] = array();
			
			if(pg_num_rows($qry_sel_cust_det)>0){
				while($row_sel_cust_det = pg_fetch_array($qry_sel_cust_det)){
					$loading_id[]		= $row_sel_cust_det['m_loading_id'];
					$nomor_customer[]	= $row_sel_cust_det['nomor_customer'];
					$nomor_rekening[]	= $row_sel_cust_det['nomor_rekening'];
					$nama[]				= addslashes($row_sel_cust_det['nama']);
					$blthnya[]			= $row_sel_cust_det['blth'];
					$email_x = str_replace(";","##",$row_sel_cust_det['email']);
					$m_loading_id		= $row_sel_cust_det['m_loading_id'];
					
					if($email_sample=='' || $email_sample=='null'){
						$email[] = rtrim($email_x,'##');
						$status_sample[] = 'f';
						$bypass_apr = "N";
					}else{
						$email_sample = str_replace(';','##',trim($email_sample,';'));
						$email[] = $email_sample;
						$status_sample[] = 't';
						$bypass_apr = "Y";
					}
					$pdf_name[]			= $row_sel_cust_det['pdf_name'] . ".pdf";
					$pdf_location[]		= '../pdf/'.$row_sel_cust_det['flagtrans'].'/'.$row_sel_cust_det['blth'].'/'.str_replace(substr($row_sel_cust_det['nama_file'],-4),'',$row_sel_cust_det['nama_file']).'/';
					$flag_attach[]		= $row_sel_cust_det['flag_attach'];
				}
				
				
				//DITAMBAHKAN PADA 7 MARET 2014
				/**Log Approval**/
				$log_approval_id = log_approval();
				if($bypass_apr == "Y"){
					bypass_approval($log_approval_id);
				}
				/**End Log Approval**/
				
				
				
				
				$no_rek_exists = "";
				for($i=0;$i<count($nomor_rekening);$i++){
					$arr_email = explode('##',$email[$i]);
					for($k=0;$k<count($arr_email);$k++){
						$sql_sel_antrian = "SELECT nomor_rekening
											FROM antrian_email
											WHERE loading_id = ".$loading_id[$i]."
												and nomor_rekening = '".$nomor_rekening[$i]."'
												and email = '".addslashes($arr_email[$k])."'";
						//die($sql_sel_antrian);
						$qry_sel_antrian = @pg_query($sql_sel_antrian);
						if(@pg_num_rows($qry_sel_antrian)==0){
							
							
						if ( $flagtrans == 'CO')
						{
							//$template_id = template_corporate_split($template_id, $nomor_customer[$i], $nomor_rekening[$i]);
							$template_id = $template_id;
						}
							
							
							
							if ( $flagtrans == 'BC')
							{
								$template_id = template_email_bri_tokopedia($template_id, $nomor_customer[$i], $nomor_rekening[$i]);
							}
							
							
							$sql_ins_antrian = "INSERT INTO antrian_email(
													nomor_customer, nomor_rekening, nama,
													pdf_name, pdf_location, userid,
													tgl_antrian, template_email_id, email,
													loading_id, status_sample, blth, flagtrans,
													log_approval_id
												)VALUES(
													'".$nomor_customer[$i]."', '".$nomor_rekening[$i]."', E'".$nama[$i]."',
													'".$pdf_name[$i]."', '".$pdf_location[$i]."', '".$_SESSION['userid']."',
													now(), ".$template_id.", E'".addslashes($arr_email[$k])."',
													".$loading_id[$i].", '".$status_sample[$i]."', '".$blthnya[$i]."', '$flagtrans',
													'$log_approval_id'
												);";
							//die($sql_ins_antrian);
							$qry_ins_antrian = @pg_query($sql_ins_antrian)or die("ERROR: " . $sql_ins_antrian);
							if(@pg_affected_rows($qry_ins_antrian)){
								$jml_record++;
								$sql_sel_antrian_id = "SELECT last_value as antrian_id FROM antrian_email_antrian_id_seq";
								$qry_sel_antrian_id = pg_query($sql_sel_antrian_id) or die('ERROR select antrian id: '.$sql_sel_antrian_id);
								$row_sel_antrian_id = pg_fetch_assoc($qry_sel_antrian_id);
								$antrian_id = $row_sel_antrian_id['antrian_id'];
								
								if($cid<>''){
									$sql_ins_attach_id = "INSERT INTO antrian_email_attach_file(antrian_id, m_attach_file_id)
															VALUES($antrian_id,".$cid.")";
									$qry_ins_attach_id = pg_query($sql_ins_attach_id) or die('ERROR insert into antrian_email_attach_file_embed: '.$sql_ins_attach_id);
								}
								
								
							
							// start selectif attachment credit card
							$status_pdf_belakang = TRUE; //kz attachment auto TRUE di aktifkan
							if ( $status_pdf_belakang)
							{
								if($flagtrans=="BC"){
									if(trim(strtolower($check_attach_auto))=="on"){
										$tambahan_where = "";
										
										$cari = lampiran_pdf_belakang($nomor_rekening[$i],$nomor_customer[$i]);
										//if ($cari != "") {$tambahan_where = " and lower(name_file) = 'visa_m_saku.jpg' ";}
										if ($cari != "") {$tambahan_where = " and lower(name_file) = lower('$cari') ";}
										
										if($tambahan_where!=""){
											$sql_sel_attach = "SELECT m_attach_file_id, name_file, location_file
																FROM m_attach_file
																WHERE blth = 'ALL'
																	and flagtrans = '$flagtrans'
																	and status = TRUE
																	$tambahan_where";
											$qry_sel_attach = pg_query($sql_sel_attach) or die($sql_sel_attach);
											$row_dat = pg_num_rows($qry_sel_attach);
											if($row_dat>0){
												$arr_sel_attach = pg_fetch_array($qry_sel_attach);
												$lokasi_file	= $arr_sel_attach[2];
												$nama_attachment= $arr_sel_attach[1];
												if(is_file($lokasi_file.$nama_attachment)){
													$attach_file_auto = $arr_sel_attach[0];
													$sql_ins_attach_id = "INSERT INTO antrian_email_attach_file(antrian_id, m_attach_file_id)
																			VALUES($antrian_id,".$attach_file_auto.")";
													$qry_ins_attach_id = pg_query($sql_ins_attach_id) or die('ERROR insert into antrian_email_attach_file: '.$sql_ins_attach_id);
												}
											}
										}
									}
								}
							}
							// end selecctif attachment
								
								
								if($tipe_attach == 1){
									for($c=0;$c<count($arr_attach_file_id);$c++){
										$sql_ins_attach_id = "INSERT INTO antrian_email_attach_file(antrian_id, m_attach_file_id)
																VALUES($antrian_id,".$arr_attach_file_id[$c].")";
										$qry_ins_attach_id = pg_query($sql_ins_attach_id) or die('ERROR insert into antrian_email_attach_file: '.$sql_ins_attach_id);
									}
								}elseif($tipe_attach == 2){
									for($c=0;$c<count($arr_attach_file_id);$c++){
										if($flag_attach[$i][$c] == '1'){
											$sql_ins_attach_id = "INSERT INTO antrian_email_attach_file(antrian_id, m_attach_file_id)
																	VALUES($antrian_id,".$arr_attach_file_id[$c].")";
											$qry_ins_attach_id = pg_query($sql_ins_attach_id) or die('ERROR insert into antrian_email_attach_file: '.$sql_ins_attach_id);
										}
									}
								}
							}
						}else{
							if($no_rek_exists==""){
								$no_rek_exists = $nomor_rekening[$i];
							}else{
								$no_rek_exists .= ', '.$nomor_rekening[$i];
							}
						}
					}
				}
				update_log_approval($log_approval_id, $jml_record, $m_loading_id, 0, 0);
			}
			
			
			// ************  20150306 Revisi ************ //
			$sql_view = "
			DROP VIEW $nama_view;";
			//ALTER TABLE $nama_view OWNER TO postgres;
			//die ( $sql_view );
			//$qry_view = @pg_query($sql_view);
			// ************  20150306 Revisi ************ //
			
			echo $pr.'|'.$jml_record.'|'.$no_rek_exists;
			
		}
		
		
		
	
	
	function template_email($flagtrans){
		$sql_template = "SELECT template_email_id,nama_template FROM template_email WHERE flagtrans='$flagtrans' and status ='t'";
		$qry_template = pg_query($sql_template) or die('ERROR select template: '.$sql_template);
		?>
        <select id="combo_template" name="combo_template" class="combobox" onchange="cekFile();template_name();">
        	<option selected="selected" value=""></option>
			<?
            	while($row_template = pg_fetch_array($qry_template)){
					?>
                    <option value="<?=$row_template['template_email_id']?>"><?=$row_template['nama_template']?></option>
                    <?
            }
			?>
         </select>
         <?
	}
	
	function template_name(){
		$template_id = $_REQUEST['template_id'];
		
		$sql_template = "SELECT template_email_id, nama_template FROM template_email WHERE template_email_id='$template_id'";
		$qry_template = pg_query($sql_template) or die('ERROR select template: '.$sql_template);
		$row_template = pg_fetch_array($qry_template)
		?>
        <input type="hidden" id="combo_template_name" name="combo_template_name" value="<?=$row_template['nama_template']?>" />
        <?
	}
	
	function group_sample($blth,$flagtrans,$file){
		if($file != ""){$where_file = "and nama_file = '$file' ";}
		$sql_norek = "
			SELECT substring(nomor_rekening,1,8) as norek
			FROM detail
			WHERE blth = '$blth' and flagtrans = '$flagtrans' $where_file
			GROUP BY substring(nomor_rekening,1,8)
			ORDER BY norek
			LIMIT 10;
		";
		$qry_norek = @pg_query($sql_norek);
		while($row_norek = @pg_fetch_array($qry_norek)){
			echo $row_norek['norek'] . "<br>";
		}
	}
	
	@pg_close($con);
?>