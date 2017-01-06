<?php
session_start();

    $ruta_raiz = "."; 
    if (!$_SESSION['dependencia'])
        header ("Location: $ruta_raiz/cerrar_session.php");
/*************************************************************************************/
/* ORFEO GPL:Sistema de Gestion Documental		http://www.orfeogpl.org	     */
/*	Idea Original de la SUPERINTENDENCIA DE SERVICIOS PUBLICOS DOMICILIARIOS     */
/*				COLOMBIA TEL. (57) (1) 6913005  orfeogpl@gmail.com   */
/* ===========================                                                       */
/*                                                                                   */
/* Este programa es software libre. usted puede redistribuirlo y/o modificarlo       */
/* bajo los terminos de la licencia GNU General Public publicada por                 */
/* la "Free Software Foundation"; Licencia version 2. 			             */
/*                                                                                   */
/* Copyright (c) 2005 por :	  	  	                                     */
/* SSPS "Superintendencia de Servicios Publicos Domiciliarios"                       */
/*   Jairo Hernan Losada  jlosada@gmail.com                Desarrollador             */
/*   Sixto Angel Pinzón López --- angel.pinzon@gmail.com   Desarrollador             */
/* C.R.A.  "COMISION DE REGULACION DE AGUAS Y SANEAMIENTO AMBIENTAL"                 */ 
/*   Liliana Gomez        lgomezv@gmail.com                Desarrolladora            */
/*   Lucia Ojeda          lojedaster@gmail.com             Desarrolladora            */
/* D.N.P. "Departamento Nacional de Planeación"                                      */
/*   Hollman Ladino       hollmanlp@gmail.com                Desarrollador          */
/*                                                                                   */
/* Colocar desde esta lInea las Modificaciones Realizadas Luego de la Version 3.5    */
/*  Nombre Desarrollador   Correo     Fecha   Modificacion                           */
/*************************************************************************************/
include_once "class_control/AplIntegrada.php";
$objApl = new AplIntegrada($db);
$lkGenerico = "&usuario=$krd&nsesion=".trim(session_id())."&nro=$verradicado"."$datos_envio";
?>
<script src="js/popcalendar.js"></script>
<script>
function regresar()	
{	//window.history.go(0);
	window.location.reload();
}
function CambiarE(est,numeroExpediente) {
        window.open("<?=$ruta_raiz?>/archivo/cambiar.php?<?=session_name()?>=<?=session_id()?>&numRad=<?=$verrad?>&expediente="+ numeroExpediente +"&est="+ est +"&","Cambio Estado Expediente","height=100,width=100,scrollbars=yes");
}
function modFlujo(numeroExpediente,texp,codigoFldExp)
{
<?php
        $isqlDepR = "SELECT RADI_DEPE_ACTU,RADI_USUA_ACTU from radicado
                                                        WHERE RADI_NUME_RADI = '$numrad'";
        $rsDepR = $db->conn->Execute($isqlDepR);
        $coddepe = $rsDepR->fields['RADI_DEPE_ACTU'];
        $codusua = $rsDepR->fields['RADI_USUA_ACTU'];
        $ind_ProcAnex="N";
?>
window.open("<?=$ruta_raiz?>/flujo/modFlujoExp.php?<?=session_name()?>=<?=session_id()?>&codigoFldExp="+codigoFldExp+"&numeroExpediente="+numeroExpediente+"&numRad=<?=$verrad?>&texp="+texp+"&ind_ProcAnex=<?=$ind_ProcAnex?>&codusua=<?=$codusua?>","TexpE<?=$fechaH?>","height=250,width=750,scrollbars=yes");
}

</script>
<table width="100%" border="0" cellpadding="0" cellspacing="1" class=borde_tab>
<tr class=titulos2> 
	<td class="titulos2" colspan="6" >INFORMACION GENERAL </td>
</tr>
</table>
<table border=0 cellspace=2 cellpad=2 WIDTH=100% align="left" class="borde_tab" id=tb_general>
<tr> 
	<td align="right" bgcolor="#CCCCCC" height="25" class="titulos2" >FECHA DE RADICADO</td>
    <td  width="25%" height="25" class="listado2"><?=$radi_fech_radi ?></td>
    <td bgcolor="#CCCCCC" width="25%" align="right" height="25" class="titulos2" >ASUNTO</td>
    <td class='listado2' colspan="3" width="25%"><?=$ra_asun ?></td>
</tr>
<tr> 
    <td align="right" bgcolor="#CCCCCC" height="25" class="titulos2"><?=$tip3Nombre[1][$ent]?></td> 
    <td class='listado2' width="25%" height="25"><?=$nomRemDes["x1"] ?> </td>
	<td bgcolor="#CCCCCC" width="25%" align="right" height="25" class="titulos2" >DIRECCI&Oacute;N CORRESPONDENCIA</td>
	<td class='listado2' width="25%"><?=$dirDireccion["x1"] ?></td>
	<td bgcolor="#CCCCCC" width="25%" align="right" height="25" class="titulos2" >MUN/DPTO</td>
	<td class='listado2' width="25%"><?=$dirDpto["x1"]."/".$dirMuni["x1"] ?></td>
</tr>
<tr> 
	<td align="right" bgcolor="#CCCCCC" height="25" class="titulos2"><?=$tip3Nombre[2][$ent]?></td>
	<td class='listado2' width="25%" height="25"> <?=$nomRemDes["x2"]?></td>
    <td bgcolor="#CCCCCC" width="25%" align="right" height="25" class="titulos2">DIRECCI&Oacute;N CORRESPONDENCIA </td>
    <td class='listado2' width="25%"> <?=$dirDireccion["x2"] ?></td>
    <td bgcolor="#CCCCCC" width="25%" align="right" height="25" class="titulos2">MUN/DPTO</td>
    <td class='listado2' width="25%"> <?=$dirDpto["x2"]."/".$dirMuni["x2"] ?></td>
</tr>
<tr>
	<td align="right" bgcolor="#CCCCCC" height="25" class="titulos2"><?=$tip3Nombre[3][$ent]?></td>
	<td class='listado2' width="25%" height="25"> <?=$nombret_us3 ?> -- <?=$cc_documento_us3?></td>
    <td bgcolor="#CCCCCC" width="25%" align="right" height="25" class="titulos2">DIRECCI&Oacute;N CORRESPONDENCIA </td>
    <td class='listado2' width="25%"> <?=$direccion_us3 ?></td>
    <td bgcolor="#CCCCCC" width="25%" align="right" height="25" class="titulos2">MUN/DPTO</td>
    <td class='listado2' width="25%"> <?=$dpto_nombre_us3."/".$muni_nombre_us3 ?></td>
</tr>
<tr>
	<td height="25" bgcolor="#CCCCCC" align="right" class="titulos2">CANTIDAD: </td>
    <td class='listado2' width="25%" height="25"> HOJAS: <?=$radi_nume_hoja ?> &nbsp;&nbsp;&nbsp FOLIOS: <?=$radi_nume_folio?>  &nbsp;&nbsp;&nbsp;  ANEXOS: <?=$radi_nume_anexo?></td>
    <td bgcolor="#CCCCCC" width="25%" height="25" align="right" class="titulos2"> DESCRIPCION ANEXOS </td>
    <td class='listado2'  width="25%" height="11"> <?=$radi_desc_anex ?></td>
    <td bgcolor="#CCCCCC" width="25%" align="right" height="25" class="titulos2">&nbsp;</td>
    <td class='listado2' width="25%">&nbsp;</td>
</tr>
<tr> 
	<td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">DOCUMENTO<br>Anexo/Asociado</td>
	<td class='listado2' width="25%" height="25">
	<?	
	if($radi_tipo_deri!=1 and $radi_nume_deri)
	   {	echo $radi_nume_deri;
           	 /*
		  * Modificacion acceso a documentos
		  * @author Liliana Gomez Velasquez
		  * @since 10 noviembre 2009
		 */
		 $resulVali = $verLinkArchivo->valPermisoRadi($radi_nume_deri);
                 $verImg = $resulVali['verImg'];
		 if ($verImg == "SI"){
		        echo "<br>(<a class='vinculos' href='$ruta_raiz/verradicado.php?verrad=$radi_nume_deri &session_name()=session_id()&krd=$krd' target='VERRAD$radi_nume_deri_".date("Ymdhi")."'>Ver Datos</a>)";}	
                 else {
                      echo "<br>(<a class='vinculos' href='javascript:noPermiso()'> Ver Datos</a>)"; 
                 }
	   }
	 if($verradPermisos == "Full" or $datoVer=="985")
		{
	?>
		<input type=button name=mostrar_anexo value='...' class=botones_2 onClick="verVinculoDocto();">
	<?
		}
	?>
	</td>
    <td bgcolor="#CCCCCC" width="25%" align="right" height="25" class="titulos2">REF/OFICIO/CUENTA INTERNA </td>
    <td class='listado2' colspan="3" width="25%"> <?=$cuentai ?>&#160;&#160;&#160;&#160;&#160;
    <?
		$muniCodiFac = "";
		$dptoCodiFac = "";
		if($sector_grb==6 and $cuentai and $espcodi)
		{	if($muni_us2 and $codep_us2)
			{	$muniCodiFac = $muni_us2;
				$dptoCodiFac = $codep_us2;
			}
			else
			{	if($muni_us1 and $codep_us1)
				{	$muniCodiFac = $muni_us1;
					$dptoCodiFac = $codep_us1;
				}
			}
	?>
		<a href="./consultaSUI/facturacionSUI.php?cuentai=<?=$cuentai?>&muniCodi=<?=$muniCodiFac?>&deptoCodi=<?=$dptoCodiFac?>&espCodi=<?=$espcodi?>" target="FacSUI<?=$cuentai?>"><span class="vinculos">Ver Facturacion</span></a>
	<?
		}
	?>
    </td>
  </tr>
  <tr> 
	<td align="right" height="25" class="titulos2">IMAGEN</td>
	<td class='listado2' colspan="1"><span class='vinculos'><?=$imagenv ?></span></td>
	<td align="right" height="25"  class="titulos2">ESTADO ACTUAL   </td>
	<td class='listado2' >
		<span class=leidos2><?=$descFldExp?></span>&nbsp;&nbsp;&nbsp;
		<? 
			if($verradPermisos == "Full" or $datoVer=="985")
	  		{
	  	?>
  <input type=button name=mostrar_causal value='...' class=botones_2 onClick="modFlujo('<?=$numExpediente?>',<?=$texp?>,<?=$codigoFldExp?>)">
		<?
			}
		?>
	</td>
	<td align="right" height="25"  class="titulos2">Nivel de Seguridad</td>
	<td class='listado2' colspan="3">
	<?
		if($nivelRad==1)
		{	echo "Confidencial";	}
		else 
		{	echo "P&uacute;blico";	}
		if($verradPermisos == "Full" or $datoVer=="985")
	  	{	$varEnvio = "krd=$krd&numRad=$verrad&nivelRad=$nivelRad";
	?>
		<input type=button name=mostrar_causal value='...' class=botones_2 onClick="window.open('<?=$ruta_raiz?>/seguridad/radicado.php?<?=$varEnvio?>','Cambio Nivel de Seguridad Radicado', 'height=220, width=300,left=350,top=300')">
	<?
		}
	?>
	</td>
</tr>
<tr> 
	<td align="right" height="25" class="titulos2">TRD</td>
	<td class='listado2' colspan="6">
	<?
		if(!$codserie) $codserie = "0";
		if(!$tsub) $tsub = "0";
		if(trim($val_tpdoc_grbTRD)=="///") $val_tpdoc_grbTRD = "";
	?>
		<?=$serie_nombre ?><font color=black>/</font><?=$subserie_nombre ?><font color=black>/</font><?=$tpdoc_nombreTRD ?>
	<?
		if($verradPermisos == "Full" or $datoVer=="985") {
	?>
		<input type=button name=mosrtar_tipo_doc2 value='...' class=botones_2 onClick="ver_tipodocuTRD(<?=$codserie?>,<?=$tsub?>);">
	</td>
</tr>
  <tr>
 
    <td align="right" height="25" class="titulos2">TEMA</td>
    <td class='listado2' colspan="6"> 
      <?=$sector_nombre?>
      <? 
		$nombreSession = session_name();
		$idSession = session_id();
		if ($verradPermisos == "Full"  or $datoVer=="985") {
	  		$sector_grb = (isset($sector_grb)) ? $sector_grb : 1;
	  		$causal_grb = (isset($causal_grb) ||$causal_grb !='') ? $causal_grb : 0;
	  		$deta_causal_grb = (isset($deta_causal_grb) || $deta_causal_grb!='') ? $deta_causal_grb : 0;
	  		
			$datosEnviar = "'$ruta_raiz/causales/mod_causal.php?" . 						$nombreSession . "=" . $idSession .
					"&krd=" . $krd . 
					"&verrad=" . $verrad . 
					"&sector=" . $sector_grb . 
					"&sectorCodigoAnt=" . $sector_grb . 
					"&sectorNombreAnt=" . $sector_nombre . 
					"&causal_grb=" . $causal_grb . 
					"&causal_nombre=" . $causal_nombre . 
					"&deta_causal_grb=" . $deta_causal_grb .
					"&ddca_causal_grb=" . $ddca_causal .  
					"&ddca_causal_nombre=". $ddca_causal_nombre . "'";
	  ?>
      <input type=button name="mostrar_causal" value="..." class="botones_2" onClick="window.open(<?=$datosEnviar?>,'Tipificacion_Documento','height=300,width=750,scrollbars=no')">
      <input type="hidden" name="mostrarCausal" value="N">
      <?
	   }
	   ?>
    </td>
  </tr>
  <tr> 
    <td align="right" height="25" class="titulos2">SUB-TEMA</td>
    <?
	$causal_nombre_grb = $causal_nombre;
	$dcausal_nombre_grb = $dcausal_nombre;
	$$ddca_causal_nombre_grb = $ddca_causal_nombre;
	?>
    <td class='listado2' colspan="6"> 
      <?=$causal_nombre ?>
      / 
      <?=$dcausal_nombre ?>
      / 
      <?=$ddca_causal_nombre ?>
      / 
      <? 
	  if ($verradPermisos == "Full"  or $datoVer=="985" ) {
	  ?>
      	<input type=button name="mostrar_causal" value="..." class='botones_2' onClick="window.open(<?=$datosEnviar?>,'Tipificacion_Documento','height=300,width=750,scrollbars=no')">
      <?
	  } 
	  ?>
    </td>
  </tr>
  <tr> 
    <td align="right" height="25" class="titulos2">POBLACI&Oacute;N</td>
    <td class='listado2' colspan="6"> 
      <?=$tema_nombre ?>
      <? 
	  if ($verradPermisos == "Full"  or $datoVer=="985") {
	  ?>
      <input type=button name="mostrar_temas" id='mostrar_temas'  value='...' class=botones_2 onClick="ver_temas();">
      <?
	  }
}
	  ?>
    </td>
  </tr>
</table>
</form>
