<html>
<head>
<title>Untitled</title>
<link rel="stylesheet" href="estilos/orfeo.css">
</head>
<body >
<table width="100%" border="0" cellpadding="0" cellspacing="1" class=borde_tab>
  <tr > 
    <td class="titulos2" colspan="6" >HISTORICO </td>
  </tr>
</table>
<?php
	   require_once("$ruta_raiz/class_control/Transaccion.php");
		 require_once("$ruta_raiz/class_control/Dependencia.php");
		 require_once("$ruta_raiz/class_control/usuario.php");
	   error_reporting(7);
	   $trans = new Transaccion($db);
	   $objDep = new Dependencia($db);
	   $objUs = new Usuario($db);
	   $isql = "select USUA_NOMB from usuario where depe_codi=$radi_depe_actu and usua_codi=$radi_usua_actu";
	   $rs = $db->query($isql);			      	   
	   $usuario_actual = $rs->fields["USUA_NOMB"];
	   $isql = "select DEPE_NOMB from dependencia where depe_codi=$radi_depe_actu";
	   $rs = $db->query($isql);			      	   
	   $dependencia_actual = $rs->fields["DEPE_NOMB"];
	   $isql = "select USUA_NOMB from usuario where depe_codi=$radi_depe_radicacion and usua_codi=$radi_usua_radi";

	   $rs = $db->query($isql);			      	   
	   $usuario_rad = $rs->fields["USUA_NOMB"];
	   $isql = "select DEPE_NOMB from dependencia where depe_codi=$radi_depe_radicacion";
	   $rs = $db->query($isql);			      	   
	   $dependencia_rad = $rs->fields["DEPE_NOMB"];
?>
<table  width="100%"  align="center"  border="0" cellpadding="0" cellspacing="1" class="borde_tab"  >
  <tr   align="left">
    <td width=10% class="titulos2" height="24">USUARIO ACTUAL</td>
    <td  width=15% class="listado2" height="24" align="left"><?=$usuario_actual?></td>
    <td width=10% class="titulos2" height="24">DEPENDENCIA ACTUAL</td>
    <td  width=15% class="listado2" height="24"><?=$dependencia_actual?></td>
  </tr>
    <tr  class='etextomenu' align="left">
    <td width=10% class="titulos2" height="24">USUARIO RADICADOR </td>
    <td  width=15% class="listado2" height="24"><?=$usuario_rad?></td>
    <td width=10% class="titulos2" height="24">DEPENDENCIA DE RADICACION </td> 
    <td  width=15% class="listado2" height="24"><?=$dependencia_rad?></td>
  </tr>
 </table>
 <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td height="25" class="titulos4">FLUJO HISTORICO DEL DOCUMENTO</td>
  </tr>
</table>
<table  width="100%" align="center" border="0" cellpadding="0" bgcolor="cdd0FF" cellspacing="1" class="borde_tab" >
  <tr   align="center" class=listado2>
    <td width=10% class="titulos2" height="24">DEPENDENCIA </td>
    <td  width=5% class="titulos2" height="24">FECHA</td>
     <td  width=15% class="titulos2" height="24">TRANSACCION </td>  
    <td  width=15% class="titulos2" height="24" >US. ORIGEN</td>
		<?
		 /** Esta es la columna que se elimino de forma Temporal  USUARIO - DESTINO
			 * <td  width=15% class="grisCCCCCC" height="24" ><font face="Arial, Helvetica, sans-serif"> US. DESTINO</font></td>
			 */
		?>
    <td  width=40% height="24" class="titulos2">COMENTARIO</td>
  </tr>
  <?
  $sqlFecha = $db->conn->SQLDate("d-m-Y H:i A","a.HIST_FECH");

	$isql = "select $sqlFecha AS HIST_FECH1
      , a.DEPE_CODI
			, a.USUA_CODI
			,a.RADI_NUME_RADI
			,a.HIST_OBSE 
			,a.USUA_CODI_DEST
			,a.USUA_DOC
			,a.HIST_OBSE
			,a.SGD_TTR_CODIGO
			from hist_eventos a
		 where 
			a.radi_nume_radi =$verrad
			order by hist_fech desc ";  

	$i=1;
	$rs = $db->query($isql);
	IF($rs)
	{
    while(!$rs->EOF)
	 {
		$usua_doc_dest = "";
		$usua_doc_hist = "";
		$usua_nomb_historico = "";
		$usua_destino = "";
		$numdata =  trim($rs->fields["CARP_CODI"]);
		if($data =="") $rs1->fields["USUA_NOMB"];
	   		$data = "NULL";
		$numerot = $rs->fields["NUM"];
		$usua_doc_hist = $rs->fields["USUA_DOC"];
		$usua_codi_dest = $rs->fields["USUA_CODI_DEST"];
		$usua_dest=intval(substr($usua_codi_dest,3,3));
		$depe_dest=intval(substr($usua_codi_dest,0,3));
		$usua_codi = $rs->fields["USUA_CODI"];
		$depe_codi = $rs->fields["DEPE_CODI"];
		$codTransac = $rs->fields["SGD_TTR_CODIGO"];
		$descTransaccion = $rs->fields["SGD_TTR_DESCRIP"];
		if(!$codTransac) $codTransac = "0";
		$trans->Transaccion_codigo($codTransac);
		$objUs->usuarioDocto($usua_doc_hist);
		$objDep->Dependencia_codigo($depe_codi);

		error_reporting(7);
		if($carpeta==$numdata)
			{
			$imagen="usuarios.gif";
			}
		else
			{
			$imagen="usuarios.gif";
			}
		if($i!=10000)
			{
		?>
  <tr class="titulos2"> <?  
		    $i=1;
			}
			 ?>
    <td class="listado1" >
	<?=$objDep->getDepe_nomb()?></td>
    <td class="listado1">
	<?=$rs->fields["HIST_FECH1"]?>
 </td>
<td class="listado1"  >
  <?=$trans->getDescripcion()?>
</td>
<td class="listado1"  >
   <?=$objUs->get_usua_nomb()?>
</td>
		<?
		 /**
			 *  Campo qque se limino de forma Temporal USUARIO - DESTINO 
			 * <td class="celdaGris"  >
			 * <?=$usua_destino?> </td> 
			 */
		?>
			 <td class="listado1"><?=$rs->fields["HIST_OBSE"]?></td>
  </tr>
  <?
	$rs->MoveNext();
  	}
}
  // Finaliza Historicos
	?>
</table>
  <?
  //empieza datos de envio
include "$ruta_raiz/include/query/queryver_historico.php";

$isql = "select $numero_salida from anexos a where a.anex_radi_nume=$verrad";
$rs = $db->query($isql);			      	   	
$radicado_d= "";
while(!$rs->EOF)
	{
		$valor = $rs->fields["RADI_NUME_SALIDA"];
		if(trim($valor))
		   {
		      $radicado_d .= "'".trim($valor) ."', ";
		   }
		$rs->MoveNext();   		  
	}  

$radicado_d .= "$verrad";
error_reporting(7);
//$db->conn->debug=true;
include "$ruta_raiz/include/query/queryver_historico.php";
$sqlFechaEnvio = $db->conn->SQLDate("d-m-Y H:i A","a.SGD_RENV_FECH");
$isql = "select $sqlFechaEnvio AS SGD_RENV_FECH,
		a.DEPE_CODI,
		a.USUA_DOC,
		a.RADI_NUME_SAL,
		a.SGD_RENV_NOMBRE,
		a.SGD_RENV_DIR,
		a.SGD_RENV_MPIO,
		a.SGD_RENV_DEPTO,
		a.SGD_RENV_PLANILLA,
		b.DEPE_NOMB,
		c.SGD_FENV_DESCRIP,
		$numero_sal,
		a.SGD_RENV_OBSERVA,
		a.SGD_DEVE_CODIGO,
		u.USUA_LOGIN
		from sgd_renv_regenvio a, dependencia b, sgd_fenv_frmenvio c, usuario u
		where
		a.radi_nume_sal in($radicado_d)
		AND a.depe_codi=b.depe_codi
		AND a.sgd_fenv_codigo = c.sgd_fenv_codigo
		and a.usua_doc=cast (u.usua_doc as numeric)
		order by a.SGD_RENV_FECH desc ";
$rs = $db->query($isql);
?>

<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0" class="borde_tab">
<?
  if ((strtoupper($_SESSION["entidad"]) == "correlibre") and ($radi_fech_radi < '2012-10-13' )){
?>
  <tr>
    <td height="50" class="titulos4"><a href="tx/historicoMelbacorrelibre.php?verradicado=<?=$verradicado?>" target="Historico<?=$verradicado?>" > 
    Ver Historico de Melba(correlibre)-MOVIMIENTOS</a>
    </td>
    <td height="50" class="titulos4"><a href="tx/historicoCambiosMelba.php?verradicado=<?=$verradicado?>" target="HistoricoCambios<?=$verradicado?>" > 
    Ver Historico de Melba(correlibre)-TRANSACCIONES</a>
    </td>
   </tr>
<?
  }
?>
</table>
 <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0" class="borde_tab">
  <tr>
    <td height="25" class="titulos4">DATOS DE ENVIO</td>
  </tr>
</table>
<table width="100%"  align="center" border="0" cellpadding="0" cellspacing="1" class="borde_tab" >
  <tr  align="center">
    <td width=10% class="listado2" height="24">RADICADO </td>
    <td width=10% class="listado2" height="24">DEPENDENCIA</td>
    <td  width=15% class="listado2" height="24">FECHA </td>
    <td  width=15% class="listado2" height="24">Destinatario</td>      
    <td  width=15% class="listado2" height="24" >DIRECCION </td>
    <td  width=15% class="listado2" height="24" >DEPARTAMENTO </td>
    <td  width=15% class="listado2" height="24" >MUNICIPIO</td>
    <td  width=15% class="listado2" height="24" >TIPO DE ENVIO</td>
    <td  width=5% height="24" class="listado1"> No. PLANILLA</td>
    <td  width=15% height="24" class="listado1">OBSERVACIONES O DESC DE ANEXOS</td>      
 <td  width=15% height="24" class="listado1">Realizo Envio</td>
  </tr>
  <?
$i=1;
while(!$rs->EOF)
	{
	$radDev = $rs->fields["SGD_DEVE_CODIGO"];
	$radEnviado = $rs->fields["RADI_NUME_SAL"];
	if($radDev)
	{
		$imgRadDev = "<img src='$ruta_raiz/imagenes/devueltos.gif' alt='Documento Devuelto por empresa de Mensajeria' title='Documento Devuelto por empresa de Mensajeria'>";
	}else
	{
		$imgRadDev = "";
	}
	$numdata =  trim($rs->fields["CARP_CODI"]);
	if($data =="") 
		$data = "NULL";
	//$numerot = $rs->RecordCount();
	if($carpeta==$numdata)
		{
		$imagen="usuarios.gif";
		}
	else
		{
		$imagen="usuarios.gif";
		}
	if($i==1)
		{
   ?>
  <tr > <?  $i=1;
			}
			 ?>
    <td class="listado2" >
	<?=$imgRadDev?><?=$radEnviado?></td>
    <td class="listado2" >
	<?=$rs->fields["DEPE_NOMB"]?></td>
    <td class="listado2">
	<?
		echo "<a class=vinculos href='./verradicado.php?verrad=$radEnviado&krd=$krd' target='verrad$radEnviado'><span class='timpar'>".$rs->fields["SGD_RENV_FECH"]."</span></a>";
	?> </td>
    <td class="listado2">
	<?=$rs->fields["SGD_RENV_NOMBRE"]
	?> </td>
    <td class="listado2"  >
	<?=$rs->fields["SGD_RENV_DIR"]?> </td>
    <td class="listado2"  >
	 <?=$rs->fields["SGD_RENV_DEPTO"] ?> </td>
    <td class="listado2"  >
	 <?=$rs->fields["SGD_RENV_MPIO"] ?> </td>
    <td class="listado2"  >
	 <?=$rs->fields["SGD_FENV_DESCRIP"] ?> </td>
    <td class="listado2"  >
	 <?=$rs->fields["SGD_RENV_PLANILLA"] ?> </td>
    <td class="listado2"  >
	 <?=$rs->fields["SGD_RENV_OBSERVA"] ?> </td>
   <td class="listado2"  >
         <?=$rs->fields["USUA_LOGIN"] ?> </td>

  </tr>
  <?
	$rs->MoveNext();  
  }

  // Finaliza Historicos
	?>
</table>
</body>
</html>
