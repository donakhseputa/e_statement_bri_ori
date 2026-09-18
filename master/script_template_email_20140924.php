<? require_once("../include/config.php"); ?>
<? require_once("../include/session.php"); ?>
<? require_once("../include/function.php"); ?>
<?
	$act = $_REQUEST['act'];
	$flagtrans = $_REQUEST['flagtrans'];
	$template_id = $_REQUEST['template_id'];
	$con = pg_connect($connection) or die("Could not connect to database!");
	
	switch($act){
		case 'combo_produk': combo_produk(); break;
		case 'combo_template': combo_template($flagtrans); break;
		case 'view_template': view_template($flagtrans,$template_id); break;
		case 'create_template': create_template($flagtrans); break;
		case 'save_template': save_template($flagtrans); break;
		case 'new_template': new_template($flagtrans); break;
		case 'load_template': load_template($flagtrans,$template_id); break;
		case 'edit_template': edit_template($template_id); break;
		case 'update_template': update_template($template_id); break;
		case 'delete_template': delete_template($template_id); break;
	}
	
	function combo_produk(){
		$sql_produk = "SELECT * FROM mproduk ORDER BY produk";
		$qry_produk = pg_query($sql_produk) or die('ERROR select produk: '.$sql_produk);
		?>
        <select id="combo_produk" name="combo_produk" class="combobox" onchange="cek_produk();combo_template_fg(this.value)">
        	<option selected="selected" value=""></option>
			<?
            	while($row_produk = pg_fetch_array($qry_produk)){
					?>
                    <option value="<?=$row_produk['flagtrans']?>"><?=$row_produk['produk']?></option>
                    <?
            }
			?>
         </select>
         <?
	}
	
	function combo_template($flagtrans){
		$sql_template = "SELECT template_email_id,nama_template FROM template_email WHERE flagtrans='$flagtrans'";
		$qry_template = pg_query($sql_template) or die('ERROR select template: '.$sql_template);
		?>
        <select id="combo_template" name="combo_template" class="combobox" onchange="cek_template();">
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
	
	function view_template($flagtrans,$template_id){
		?>
        <iframe id="frame_view" src="script_template_email.php?act=load_template&flagtrans=<?=$flagtrans?>&template_id=<?=$template_id?>" height="670" width="900" frameborder="0" style="background-color:#fff">
        </iframe>
        <?
	}
	
	function create_template($flagtrans){
		?>
        <iframe id="frame_create" src="script_template_email.php?act=new_template&flagtrans=<?=$flagtrans?>" height="670" width="900" frameborder="0" style="background-color:#fff">
        </iframe>
        <?
	}
	
	function new_template($flagtrans)
	{
		?>
		<script type="text/javascript" src="../script/ajax.js"></script>
		<script type="text/javascript" src="../script/jquery.js"></script>
        <script type="text/javascript" src="../script/function.js"></script>
		<script type="text/javascript" src="../include/ckeditor/ckeditor.js"></script>
        <script>
			function cek_template(flagtrans){
				if($("#template_name").val()!=''){
					if($("#mail_server").val()!=''){
						save_template(flagtrans);
					}else{
						alert('Mail Server can not empty !!!');
					}
				}else{
					alert('Template Name can not empty !!!');
				}
			}
			
			function save_template(flagtrans){
				var content = CKEDITOR.instances.content_form.getData();
				$("#content").val(content);
				$.post('script_template_email.php?act=save_template&flagtrans='+flagtrans,$('#form_editor').serialize(),function(respon_save){
					if(respon_save=='sukses'){	
						alert('Template berhasil disimpan');
						parent.jQuery.fancybox.close();
					}
					else{
						alert(respon_save);
					}
				});
			}       
		</script>
        <style>
			table{
				background-color:#fff;
				color: #000000;
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 12px;
				margin: 20px 0px 20px 0px;
				padding: 0px 0px 0px 0px;
			}

			.button {
				background-color: #C60323;
				border: #000000 1px solid;
				color: #FFFFFF;
				cursor: pointer;
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 12px;
				font-weight: bold;
				height: 30px;
				border-radius: 10px;
			}

			.combobox {
				background-color: #CCFFCC;
				border: #666666 1px solid;
				color: #0000CC;
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 12px;
				border-radius: 5px;
			}

			.textbox_2 {
				background-color: #CCFFCC;
				border: #666666 1px solid;
				color: #0000CC;
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 12px;
				width: 100px;
				border-radius: 5px;
			}
			
			.textbox_3 {
				background-color: #CCFFCC;
				border: #666666 1px solid;
				color: #0000CC;
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 12px;
				width: 200px;
				border-radius: 5px;
			}
			
			.textbox_4 {
				background-color: #CCFFCC;
				border: #666666 1px solid;
				color: #0000CC;
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 12px;
				width: 400px;
				border-radius: 5px;
			}
			
		</style>
		<div id="div_body">
		<form id="form_editor" name="form_editor" method="post">
            <table>
            	<tr>
                	<td width="15%">Template Name</td>
                    <td colspan="3">
                    	<input type="text" id="template_name" name="template_name" class="textbox_2" maxlength="20" />
                    </td>
                </tr>
            	<tr>
                	<td>Mail Server</td>
                    <td colspan="3">
                    	<select id="mail_server" name="mail_server" class="combobox">
                            <option value=""></option>
                        <?php
						$sql_sel_mail_server = "SELECT mail_server_id, email_from FROM mail_server WHERE status = 'T' ORDER BY email_host";
						$qry_sel_mail_server = pg_query($sql_sel_mail_server) or die('ERROR select mail_server: '.$sql_sel_mail_server);
						while($row = pg_fetch_array($qry_sel_mail_server)){
							?>
                            <option value="<?=$row['mail_server_id']?>"><?=$row['email_from']?></option>
                            <?php
						}
						?>
                        </select>
                    </td>
                </tr>
            	<tr>
                	<td>Reply To</td>
                    <td colspan="3">
                    	<input type="text" id="reply_to" name="reply_to" class="textbox_3" maxlength="100" />
                    </td>
                </tr>
            	<tr>
                	<td>From Name</td>
                    <td colspan="3">
                    	<input type="text" id="from_name" name="from_name" class="textbox_3" maxlength="50" />
                    </td>
                </tr>
            	<tr>
                	<td>Subject</td>
                    <td>
                    	<input type="text" id="subjek" name="subjek" class="textbox_4" maxlength="200" />
                    </td>
                  <td>Status</td>
                    <td><select id="combo_status3" name="combo_status3" class="combobox">
                      <option value="T">AKTIF</option>
                      <option value="F">TIDAK AKTIF</option>
                    </select></td>
                </tr>
                <tr>
                	<td>Content</td>
                    <td colspan="3">
                        <textarea name="content_form" id="content_form" ></textarea>
                    </td>
                </tr>
                <tr>
                	<td>&nbsp;</td>
                    <td colspan="3">
                    	<?=show_table_code_email($flagtrans)?>
                    </td>
                </tr>
                <tr>
                	<td colspan="4" align="center">
                    	<input type="button" value="SAVE" class="button" onclick="cek_template('<?=$flagtrans?>')" />
                        <input type="hidden" id="content" name="content" value="" />
                    </td>
                </tr>
            </table>
        </form>
        </div>
        <script>
			CKEDITOR.replace('content_form',{toolbar:'appdev'});
        </script>
        <?
	}
	
	function save_template($flagtrans){
		$nama_template = $_POST['template_name'];
		$mail_server_id = $_POST['mail_server'];
		$reply_to = $_POST['reply_to'];
		$from_name = $_POST['from_name'];
		$subjek = $_POST['subjek'];
		$content = $_POST['content'];
		$status = $_POST['combo_status3'];
		
		$sql_cek_template = "SELECT nama_template FROM template_email WHERE flagtrans = '$flagtrans' and nama_template = '$nama_template'";
		$qry_cek_template = pg_query($sql_cek_template) or die('ERROR select nama_template: '.$sql_cek_template);
		if(pg_num_rows($qry_cek_template)==0){
			$sql_ins_template = "INSERT INTO template_email(
										nama_template,
										subject_email,
										isi_email,
										flagtrans,
										reply_to_email,
										mail_server_id,
										from_name, status
									)VALUES(
										'".trim($nama_template)."',
										'$subjek',
										'$content',
										'$flagtrans',
										'$reply_to',
										$mail_server_id,
										'$from_name', '$status'
									)";
			$qry_ins_template = pg_query($sql_ins_template) or die('ERROR insert template: '.$sql_ins_template);
			if(pg_affected_rows($qry_ins_template)){
				echo 'sukses';
			}
		}else{
			echo "Template Name sudah ada untuk produk ini";
		}
	}
	
	function load_template($flagtrans,$template_id)
	{
		$sql_sel_template = "SELECT a.*, b.email_from
								FROM template_email a
									LEFT JOIN mail_server b
										ON a.mail_server_id = b.mail_server_id
								WHERE a.template_email_id = '$template_id'";
		$qry_sel_template = pg_query($sql_sel_template) or die('ERROR select template: '.$sql_sel_template);
		$row_sel_template = pg_fetch_assoc($qry_sel_template);
		$nama_template = $row_sel_template['nama_template'];
		$subjek = $row_sel_template['subject_email'];
		$isi_email = $row_sel_template['isi_email'];
		$subjek = $row_sel_template['subject_email'];
		$reply_to = $row_sel_template['reply_to_email'];
		$from_name = $row_sel_template['from_name'];
		$mail_server = $row_sel_template['email_from'];
		$status = $row_sel_template['status'];
		
		if(strtoupper($status)=='T'){
				$status = 'AKTIF';
			}else{
				$status = 'TIDAK AKTIF';
			}
		
		?>        
		<script type="text/javascript" src="../script/ajax.js"></script>
		<script type="text/javascript" src="../script/jquery.js"></script>
        <script type="text/javascript" src="../script/function.js"></script>
		<script type="text/javascript" src="../include/ckeditor/ckeditor.js"></script>
        <script>
			function edit_template(template_id,flagtrans){
				window.location='script_template_email.php?act=edit_template&template_id='+template_id+'&flagtrans='+flagtrans;
			}
		</script> 
        <style>
			table{
				background-color:#fff;
				color: #000000;
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 12px;
				margin: 20px 0px 20px 0px;
				padding: 0px 0px 0px 0px;
			}

			.button {
				background-color: #C60323;
				border: #000000 1px solid;
				color: #FFFFFF;
				cursor: pointer;
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 12px;
				font-weight: bold;
				height: 30px;
				border-radius: 10px;
			}
			
			.textbox_3 {
				background-color: #CCFFCC;
				border: #666666 1px solid;
				color: #0000CC;
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 12px;
				width: 200px;
				border-radius: 5px;
			}
			
			.textbox_4 {
				background-color: #CCFFCC;
				border: #666666 1px solid;
				color: #0000CC;
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 12px;
				width: 400px;
				border-radius: 5px;
			}
			
			.combobox {
				background-color: #CCFFCC;
				border: #666666 1px solid;
				color: #0000CC;
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 12px;
				border-radius: 5px;
			}
		</style>
		<div id="div_body">
		<form id="form_editor" name="form_editor" method="post">
            <table align="center" width="100%">
            	<tr>
                	<td width="15%">Template Name</td>
                    <td colspan="3"><input type="text" id="text_name" name="text_name" class="textbox_3" value="<?=$nama_template?>" disabled="disabled" /></td>
                </tr>
            	<tr>
                	<td>Mail Server</td>
                    <td colspan="3"><input type="text" id="text_mail_server" name="text_mail_server" class="textbox_4" value="<?=$mail_server?>" disabled="disabled" /></td>
                </tr>
            	<tr>
                	<td>Reply To</td>
                    <td colspan="3"><input type="text" id="text_reply_to" name="text_reply_to" class="textbox_4" value="<?=$reply_to?>" disabled="disabled" /></td>
                </tr>
            	<tr>
                	<td>From Name</td>
                    <td colspan="3"><input type="text" id="text_from_name" name="text_from_name" class="textbox_4" value="<?=$from_name?>" disabled="disabled" /></td>
                </tr>
            	<tr>
                	<td>Subject</td>
                    <td><input type="text" id="text_subjek" name="text_subjek" class="textbox_4" value="<?=$subjek?>" disabled="disabled" /></td>
                    <td>Status</td>
                    <td><input type="text" id="text_status" name="text_status" class="textbox_3" value="<?=$status?>" disabled="disabled" /></td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <td>Content</td>
                    <td colspan="3" style="border-color:#000; border-width:1px; border:solid"><?=$isi_email?></td>
                </tr>
                <tr>
                	<td>&nbsp;</td>
                    <td colspan="3">
                    	<?=show_table_code_email($flagtrans)?>
                    </td>
                </tr>
                <tr>
                	<td>&nbsp;</td>
                    <td colspan="3">
                    	kode sample: @@#sample#@@
                    </td>
                </tr>
                <tr>
                	<td colspan="4" align="center">
                    	<input type="button" id="button_edit" name="button_edit" value="EDIT" class="button" onclick="edit_template('<?=$template_id?>','<?=$flagtrans?>')" />
                        <input type="button" id="button_update" name="button_update" value="UPDATE" class="button" onclick="" style="display:none" />
                    </td>
                </tr>
            </table>
        </form>
        </div>
        <?
	}
	
	function edit_template($template_id){
		$flagtrans = $_REQUEST['flagtrans'];
		$sql_sel_template = "SELECT * FROM template_email WHERE template_email_id = '$template_id'";
		$qry_sel_template = pg_query($sql_sel_template) or die('ERROR select template: '.$sql_sel_template);
		$row_sel_template = pg_fetch_assoc($qry_sel_template);
		$nama_template = $row_sel_template['nama_template'];
		$subjek = $row_sel_template['subject_email'];
		$isi_email = $row_sel_template['isi_email'];
		$reply_to = $row_sel_template['reply_to_email'];
		$from_name = $row_sel_template['from_name'];
		$mail_server_id = $row_sel_template['mail_server_id'];
		$status = $row_sel_template['status'];
		
		if($status=='t')
			{
				$nama_status = 'AKTIF';
				$value_status_lainnya = 'F';
				$nama_status_lainnya = 'TIDAK AKTIF';
			}
		else
			{
				$nama_status = 'TIDAK AKTIF';
				$value_status_lainnya = 'T';
				$nama_status_lainnya = 'AKTIF';
			}
		
		?>
		<script type="text/javascript" src="../script/ajax.js"></script>
		<script type="text/javascript" src="../script/jquery.js"></script>
        <script type="text/javascript" src="../script/function.js"></script>
		<script type="text/javascript" src="../include/ckeditor/ckeditor.js"></script>
        <script>
			function cek_template(template_id){
				if($("#template_name").val()!=''){
					if($("#mail_server").val()!=''){
						update_template(template_id);
					}else{
						alert('Mail Server can not empty !!!');
					}
				}else{
					alert('Template Name can not empty !!!');
				}
			}
			
			function update_template(template_id){
				var content = CKEDITOR.instances.content_form.getData();
				$("#content").val(content);
				$.post('script_template_email.php?act=update_template&template_id='+template_id,$("#form_editor").serialize(),function(respon_update){
					if(respon_update=='sukses'){	
						alert('Template berhasil di-update');
						parent.jQuery.fancybox.close();
					}
					else{
						alert(respon_update);
					}
				});
			}
		</script>
        <style>
			table{
				background-color:#fff;
				color: #000000;
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 12px;
				margin: 20px 0px 20px 0px;
				padding: 0px 0px 0px 0px;
			}

			.button {
				background-color: #C60323;
				border: #000000 1px solid;
				color: #FFFFFF;
				cursor: pointer;
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 12px;
				font-weight: bold;
				height: 30px;
				border-radius: 10px;
			}

			.combobox {
				background-color: #CCFFCC;
				border: #666666 1px solid;
				color: #0000CC;
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 12px;
				border-radius: 5px;
			}
			
			.textbox_3 {
				background-color: #CCFFCC;
				border: #666666 1px solid;
				color: #0000CC;
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 12px;
				width: 200px;
				border-radius: 5px;
			}
			
			.textbox_4 {
				background-color: #CCFFCC;
				border: #666666 1px solid;
				color: #0000CC;
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 12px;
				width: 400px;
				border-radius: 5px;
			}
		</style>
		<div id="div_body">
		<form id="form_editor" name="form_editor" method="post">
            <table>
            	<tr>
                	<td width="15%">Template Name</td>
                    <td colspan="3">
                    	<input type="text" id="template_name" name="template_name" class="textbox_3" maxlength="20" value="<?=$nama_template?>" />
                    </td>
                </tr>
            	<tr>
                	<td width="15%">Mail Server</td>
                    <td colspan="3">
                    	<select id="mail_server" name="mail_server" class="combobox">
                        	<option value=""></option>
                        <?php
						$sql_sel_mail_server = "SELECT mail_server_id, email_from FROM mail_server WHERE status = 'T' ORDER BY email_host";
						$qry_sel_mail_server = pg_query($sql_sel_mail_server) or die('ERROR select mail server: '.$sql_sel_mail_server);
						while($row = pg_fetch_array($qry_sel_mail_server)){
							if($row['mail_server_id']==$mail_server_id){
								$slc = "selected='selected'";
								echo $mail_server_id;
							}
							?>
                            <option value="<?=$row['mail_server_id']?>" <?=$slc?>><?=$row['email_from']?></option>
                            <?php
						}
						?>
                        </select>
                    </td>
                </tr>
            	<tr>
                	<td width="15%">Reply To</td>
                    <td colspan="3">
                    	<input type="text" id="reply_to" name="reply_to" class="textbox_3" maxlength="100" value="<?=$reply_to?>" />
                    </td>
                </tr>
            	<tr>
                	<td width="15%">From Name</td>
                    <td colspan="3">
                    	<input type="text" id="from_name" name="from_name" class="textbox_3" maxlength="50" value="<?=$from_name?>" />
                    </td>
                </tr>
            	<tr>
                	<td width="15%">Subject</td>
                    <td>
                    	<input type="text" id="subjek" name="subjek" class="textbox_4" maxlength="200" value="<?=$subjek?>" />
                    </td>
                    <td>Status</td>
                    <td><select id="combo_status" name="combo_status" class="combobox">
                      
                     
                      <option value="<?=$status?>" selected><?=$nama_status?></option>
                      <option value="<?=$value_status_lainnya?>"><?=$nama_status_lainnya?></option>
                    
                      
                    </select>
                                        
                    </td>
                </tr>
                <tr>
                	<td>Content</td>
                    <td colspan="3">
                        <textarea name="content_form" id="content_form" ><?=$isi_email?></textarea>
                    </td>
                </tr>
                <tr>
                	<td>&nbsp;</td>
                    <td colspan="3">
                    	<?=show_table_code_email($flagtrans)?>
                    </td>
                </tr>
                <tr>
                	<td>&nbsp;</td>
                    <td colspan="3">
                    	kode sample: @@#sample#@@
                    </td>
                </tr>
                <tr>
                	<td colspan="4" align="center">
                    	<input type="button" value="UPDATE" class="button" onclick="cek_template(<?=$template_id?>)" />
                        <input type="hidden" id="content" name="content" value="" />
                    </td>
                </tr>
            </table>
        </form>
        </div>
        <script>
       $("#mail_server").val("<?=$mail_server_id?>");
			CKEDITOR.replace('content_form',{toolbar:'appdev'});
        </script>
        <?
	}
	
	function update_template($template_id){
		$nama_template = $_POST['template_name'];
		$subjek = $_POST['subjek'];
		$content = $_POST['content'];
		$reply_to = $_POST['reply_to'];
		$from_name = $_POST['from_name'];
		$mail_server_id = $_POST['mail_server'];
		$status = $_POST['combo_status'];
		$sql_cek_template = "SELECT nama_template FROM template_email WHERE flagtrans = '$flagtrans' and nama_template = '$nama_template' and template_email_id != $template_id";
		$qry_cek_template = pg_query($sql_cek_template) or die('ERROR select nama_template: '.$sql_cek_template);
		if(pg_num_rows($qry_cek_template)==0){
			$sql_ins_template = "UPDATE template_email
									SET
										nama_template = '$nama_template',
										subject_email = '$subjek',
										isi_email = '$content',
										reply_to_email = '$reply_to',
										from_name = '$from_name',
										mail_server_id = '$mail_server_id',
										status = '$status'
									WHERE template_email_id = $template_id";
			$qry_ins_template = pg_query($sql_ins_template) or die('ERROR insert template: '.$sql_ins_template);
			if(pg_affected_rows($qry_ins_template)){
				echo 'sukses';
			}
		}else{
			echo "Template Name sudah ada untuk produk ini";
		}
	}
	
	function delete_template($template_id){
		$sql_del_template = "DELETE FROM template_email WHERE template_email_id = $template_id";
		$qry_del_template = pg_query($sql_del_template) or die('ERROR delete template: '.$sql_del_template);
		if(pg_affected_rows($qry_del_template)>0){
			echo "SUKSES";
		}else{
			echo "GAGAL DELETE TEMPLATE";
		}
	}
	
	pg_close($con);
?>