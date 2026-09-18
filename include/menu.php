<table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
    	<td width="100%" align="center" valign="middle" style="border-radius: 10px">
        	<table align="center">
            	<tr>
                	<td>
						<?php
						$pr=trim($_GET["pr"]);
						$arr_pr=explode("|", $pr);
						$product_id=trim($arr_pr[0]);
						$sql_menugroup = "SELECT mg.menugroupid, mg.menugroup
										FROM menugroup1 mg
											INNER JOIN (select distinct m.menugroupid, um.userid, m.flagtrans
														from menu1 m
															inner join usermenu1 um
																on m.menuid = um.menuid) mn_g
												ON mg.menugroupid = mn_g.menugroupid
										WHERE upper(mn_g.userid) = upper('".$_SESSION["userid"]."') and mn_g.flagtrans = '$product_id'
										ORDER BY mg.urutan";
                        $qry_menugroup = pg_query($sql_menugroup) or die('ERROR menugroup: '.$sql_menugroup);
                        ?>
                        <div class="horizontalcssmenu">
                            <ul id="cssmenu1">
                                <li><a href="../home/index.php">Home</a></li>
									<?php
                                    while($row_menugroup=pg_fetch_array($qry_menugroup)){
                                        ?>
                                        <li>
                                            <a><?=$row_menugroup['menugroup']?></a>
                                            <ul>
                                                <?php
                                                $sql_menu = "SELECT a.menuid, a.menu, a.flagtrans, a.lokasi, a.status
                                                            FROM menu1 a
                                                                INNER JOIN usermenu1 b
                                                                    ON a.menuid = b.menuid
                                                            WHERE upper(b.userid) = upper('".$_SESSION["userid"]."') and a.menugroupid = ".$row_menugroup['menugroupid']." and a.flagtrans='$product_id' and a.status='t'
                                                            ORDER BY a.urutan";
                                                $qry_menu = pg_query($sql_menu) or die('ERROR menu: '.$sql_menu);
                                                while($row_menu=pg_fetch_array($qry_menu)){
												if($row_menu['status']=='t'){
													?>
													<li><a href="<?=$row_menu['lokasi'].'?pr='.$row_menu['flagtrans'].'|'.$row_menu['menuid']?>"><?=$row_menu["menu"]?></a></li>
													<?php
												}else{
													if(strtoupper($_SESSION["userid"]) == 'APPDEV'){
														?>
														<li><a href="<?=$row_menu['lokasi'].'?pr='.$row_menu['flagtrans'].'|'.$row_menu['menuid']?>"><?=$row_menu["menu"]?></a></li>
														<?php
													}else{
														?>
														<li><a href="../underconstruction/index.php"><?=$row_menu["menu"]?></a></li>
														<?php
													}
												}
											}
											?>
										</ul>
                                    </li>
                                    <?php
                                }
                                ?>
                                <li><a href="../login/logout.php">Logout</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
	</tr>
</table>