<?php

if (!isset($tipoMed)){
    $tipoMed = "";
}
if(!isset($buscar_por_cuentai)){
    $buscar_por_cuentai = "";
}
if(!isset($buscar_por_radicado)){
    $buscar_por_radicado = "";
}
if(!isset($buscar_por_exp)){
    $buscar_por_exp = "";
}
if(!isset($buscar_por_doc)){
    $buscar_por_doc = "";
}
if(!isset($buscar_por_nombres)){
    $buscar_por_nombres = "";
}
if(!isset($pnomb)){
    $pnomb = "";
}

?><html>
<head>
<link href="<?=$ruta_raiz."/estilos/".$_SESSION["ESTILOS_PATH"]?>/orfeo.css" rel="stylesheet" type="text/css"><script >
function solonumeros()
{
 jh =  document.getElementById('buscar_por_radicado').value;
 if(jh)
 {
    var1 =  parseInt(jh);
		if(var1 != jh)
		{
			alert("Atencion: El numero de Radicado debe ser de solo Numeros. ");
			return false;
		}else{
			document.getElementById('buscar_por_radicado').value = var1;
			numCaracteres = document.getElementById('buscar_por_radicado').value.length;
                        <?php
                       $ln=$_SESSION["digitosDependencia"];
                       $lnr=11+$ln;
                        ?>

			if(numCaracteres>=13 && numCaracteres<=<?php echo $lnr; ?>)
			{
				document.formulario.submit();
			}else
			{
				alert("Atencion: El numero de Caracteres del radicado es de <?php echo $lnr; ?>. (Digito :"+numCaracteres+")");
			}
			
		}
 }else{
 	document.formulario.submit();
 }
}
</script>
</head>
<body onLoad='document.getElementById("buscar_por_radicado").focus();'>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="BORDE_TAB">
	<tr align="center" >
	<td class="titulos2">
	<center>VERIFICAR RADICACION PREVIA <?=$tRadicacionDesc ?>  <b>  <font  size=1 color=green>(  <?=$dependencia ?>
      -->
      <?=$tpDepeRad[$ent]?>
      )<?=$tipoMed ?></b></font></center> </td>   </tr>
</table>
</b>
   <table width="100%" border="0" cellspacing="1" cellpadding="1" class="borde_tab">
     <tr>
       <td width="100%" colspan="5" align="CENTER" class="titulos2" >DATO A BUSCAR </td>
     </tr>
     <tr>
       <td width="15%" align="right" class="titulos2" colspan="1" ><font size="-1">
         <? if(isset($and_cuentai)) $datoss=" checked ";  else $datoss= " "; ?>
         <input  type="checkbox" name=and_cuentai value=1 <?=$datoss?> disabled>
       </font></td>
       <td colspan="2" align="left" class="titulos2" > REFERENCIA (Cuenta I, Oficio) </td>
       <td width="46%" colspan="2" class="listado2"><input name="buscar_por_cuentai" type="text" class="ecajasfecha" id="cuentai" size="35" value="<?=$buscar_por_cuentai ?>">
       </td>
     </tr>
     <tr>
       <td width="15%"  align="right" class="titulos2" olspan="2" ><font size="-1">
         <? if(isset($and_radicado)) $datoss=" checked ";  else $datoss= " "; ?>
         <input  type="checkbox" name=and_radicado value=1 <?=$datoss?> disabled>
       </font></td>
       <td  colspan="2" align="left" class="titulos2" >No. Radicado </td>
       <td width="46%"  colspan="2" class="listado2"><input name="buscar_por_radicado" type="text" class="ecajasfecha" id="buscar_por_radicado" size="35" value="<?=$buscar_por_radicado ?>">
       </td>
     </tr>
     <tr>
       <td width="15%"  align="right" class="titulos2" olspan="2" ><font size="-1">
         <? if(isset($and_expediente)) $datoss=" checked ";  else $datoss= " "; ?>
         <input  type="checkbox" name=and_expediente value=1 <?=$datoss?> disabled>
       </font></td>
       <td height="15" colspan="2" align="left" class="titulos2" >Expediente </td>
       <td width="46%"  colspan="2" class="listado2"><input name="buscar_por_exp" type="text" class="ecajasfecha" id="buscar_por" size="35" value="<?=$buscar_por_exp ?>">
       </td>
     </tr>
     <tr>
       <td width="15%"  align="right" class="titulos2" olspan="2" ><font size="-1">
         <? if(isset($and_doc)) $datoss=" checked ";  else $datoss= " "; ?>
         <input  type="checkbox" name=and_doc value=1 <?=$datoss?> disabled>
       </font></td>
       <td height="15" colspan="2" align="left" class="titulos2" >Identificacion (T.I.,C.C.,Nit) * </td>
       <td width="46%"  colspan="2" class="listado2"><input name="buscar_por_doc" type="text" class="ecajasfecha" id="cuentai" size="35" value="<?=$buscar_por_doc ?>">
       </td>
     </tr>
     <?
if($ent!=22)
{
?>
     <tr>
       <td width="15%"  align="right" class="titulos2" olspan="2" ><font size="-1">
         <? if(isset($and_nombres)) $datoss=" checked ";  else $datoss= " "; ?>
         <input  type="checkbox" name=and_nombres value=1 <?=$datoss?> disabled>
       </font></td>
       <td colspan="2" align="left" class="titulos2" >Nombres</td>
       <td width="46%"  colspan="2" class="listado2"><input name="buscar_por_nombres" type="text" class="ecajasfecha" id="buscar_por_nombres" size="35" value="<?=$buscar_por_nombres ?>">
       </td>
     </tr>
     <?
}
?>
     <tr>
       <td width="15%" align="right" class="titulos2" > Rango de Fechas de Radicaci&oacute;n<strong><font size="-1" face="Arial, Helvetica, sans-serif" class="style4"> </font></strong></td>
       <td width="19%" align="left" class="titulos2" ><font face="Arial, Helvetica, sans-serif" class="etextomenu"> <font size="-1"><script language="javascript">
	dateAvailable.writeControl();
	dateAvailable.dateFormat="yyyy/MM/dd";
	</script>
       </font></font></td>
       <td colspan="3" align="left" class="titulos2" ><font face="Arial, Helvetica, sans-serif" class="etextomenu"> <font size="-1"><script language="javascript">
	dateAvailable2.writeControl();
	dateAvailable2.dateFormat="yyyy/MM/dd";
	</script>
       </font></font></td>
     </tr>
     <?
if(isset($mostrar_dep) && $mostrar_dep=="ddd")
{
?>
     <tr>
       <td  colspan="2" align="left" class="titulos2" > Dependencia de Radicacion</td>
       <td  colspan="2" class="listado2"><input name="buscar_por_dep_rad" type="text" class="ecajasfecha" id="cuentai" size="35" value="<?=$buscar_por_dep_rad?>">
       </td>
     </tr>
     <?
}
?>
     <tr align="center">
       <td colspan="5" class="titulos2"><input name="Submit" onClick="solonumeros();" type="button" class="botones" value="BUSCAR" onSelect="solonumeros();"  >
           <input name="Submit"  type="hidden" class="ebuttons2" value="BUSCAR">
           <!--<input type="reset" value="BORRAR" class="ebuttons2">-->
       </td>
     </tr>
     <input type='hidden' name='pnom' value='<?=$pnomb ?>'>
     <? 
	//echo"<input type='hidden' name='numdoc' value='$numdoc'>";
	$pnom=$pnomb;
    echo "";
?>
   </table>
</body>
</html>
