<table border="0" cellpadding="0" cellspacing="0" width="160">
  <tr>
   <td colspan="2"><img name="menu_r3_c1" src="imagenes/menu_r3_c1.gif" width="148" height="23" border="0" alt=""></td>
  </tr>
  <tr>
   <td>&nbsp;</td>
   <td valign="top"><table width="150" border="0" cellpadding="0" cellspacing="0" class="titulos2">
     <tr>
       <td valign="top"><table width="150"  border="0" cellpadding="0" cellspacing="3" bgcolor="">
		<?php
		if (!isset($i)){
		    $i=0;
		}
		$i++;
		
		if (!isset($des)){
		    $dep = "";
		}
		
		
		foreach ($_SESSION["tpNumRad"] as $key => $valueTp) 
		{
  			//$valueImg = "";  //  <- Variable no usada
			$valueDesc = $tpDescRad[$key];
			//$valueImg = $tpImgRad[$key];  //<- Variable no usada
    		$encabezado = "$phpsession&krd=$krd&fechah=$fechah&primera=1&ent=$valueTp&depende=$dependencia";
    		if($tpPerRad[$valueTp]==1 or $tpPerRad[$valueTp]==3)
			{ 
	?>
       	<tr valign="middle">
           <!-- <td width="25"><img src="imagenes/menu.gif" width="15" height="18" name="plus<?=$i?>"></td> -->
           <td width="125"><a class="menu_princ" onclick="cambioMenu(<?=$i?>);" href="radicacion/chequear.php?<?=$encabezado?>" alt='<?=$valueDesc?>' title='<?=$valueDesc?>'  target='mainFrame'> <?=$valueDesc?> </a></td>
         </tr>
        
		<?php
		}
		$i++;
		}
		// Realiza Link a pagina de combiancion de correspondencia masiva
		if ($_SESSION["usua_masiva"]==1) {
		?>
		<tr valign="middle">
           <!-- <td width="25"><img src="imagenes/menu.gif" width="15" height="18" name="plus<?=$i?>"></td> -->
           <td width="125"><a  onclick="cambioMenu(<?=$i?>);" href='radsalida/masiva/menu_masiva.php?<?=$phpsession ?>&krd=<?=$krd?>&<? echo "fechah=$fechah"; ?>'  target='mainFrame' class="menu_princ">Masiva</a></td>
         </tr>
         <?php
		}
         $i++;
		if ($_SESSION["dependencia"]==1999 || $_SESSION["dependencia"]==1529 || $_SESSION["dependencia"]==1810)
	 		{
		?>
         <tr valign="middle">
           <!-- <td width="25"><img src="imagenes/menu.gif" width="15" height="18" name="plus<?=$i?>"></td> -->
           <td width="125"><a  onclick="cambioMenu(<?=$i?>);" href='fax/index.php?<?=$phpsession ?>&krd=<?=$krd?>&<? echo "fechah=$fechah&usr=".md5($dep)."&primera=1&ent=2&depende=$dependencia"; ?>' alt='Rad Fax'  target='mainFrame' class="menu_princ">Rad Fax</a></td>
         </tr>
          <?php
			}
			 $i++;	
		if ($_SESSION["perm_radi"]>=1)
	 		{
		?>
         <tr valign="middle">
           <!-- <td width="25"><img src="imagenes/menu.gif" width="15" height="18" name="plus<?=$i?>"></td> -->
           <td width="125"><a  onclick="cambioMenu(<?=$i?>);" href='uploadFiles/uploadFileRadicado.php?<?=$phpsession ?>&krd=<?=$krd?>&<? echo "fechah=$fechah&usr=".md5($dep)."&primera=1&ent=2&depende=$dependencia"; ?>' alt='Asociar imagen de radicado'  target='mainFrame' class="menu_princ">Asociar Imagenes</a></td>
         </tr>
          <?php
			}
			 $i++;	
 if ($_SESSION["usuaPermRadEmail"]==1)
     {
   ?>
         <tr valign="middle">
           <!-- <td width="25"><img src="imagenes/menu.gif" width="15" height="18" name="plus<?=$i?>"></td> --> 
           <td width="125"><a  onclick="cambioMenu(<?=$i?>);" href='email/index.php?<?=$phpsession ?>&krd=<?=$krd?>&<? echo "fechah=$fechah&usr=".md5($dep)."&primera=1&ent=2&depende=$dependencia"; ?>' alt='Rad Fax'  target='mainFrame<?=$fechah?>' class="menu_princ">e-Mail</a></td>
         </tr>
          <?
   }
?>
       </table></td>
     </tr>
   </table></td>
   </tr>
   </table>
