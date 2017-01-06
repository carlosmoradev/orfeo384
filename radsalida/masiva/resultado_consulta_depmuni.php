<?php
session_start();

    $ruta_raiz = "../..";
    if (!$_SESSION['dependencia'])
        header ("Location: $ruta_raiz/cerrar_session.php");
/**
 * Programa que despliega el resultado de la consulta, seg�n los par�metros enviados desde consulta_depmuni.php
 * @author      Sixto Angel Pinz�n
 * @version     1.0
 */

require_once("$ruta_raiz/include/db/ConnectionHandler.php"); 
if (!$db)	$db = new ConnectionHandler($ruta_raiz);
include "$ruta_raiz/jh_class/funciones_sgd.php";

$consulta = $_POST['consulta'];
?>
<html>
<head>
<title>Consulta de DIVIPOLA</title>
<link rel="stylesheet" href="../../estilos/orfeo.css">
</head>
<body>
<?php
//variable que almacena el dato a consultar
$consulta=strtoupper($consulta);

$isql = "
        SELECT  con.nombre_cont || ' - ' || dep.id_cont as cont,
                dep.dpto_codi || ' - ' || dep.dpto_nomb as dept,
                mun.muni_codi || ' - ' || mun.muni_nomb as muni,  
                pai.id_pais   || ' - ' || pai.nombre_pais as pais
        FROM 
            MUNICIPIO mun 
            LEFT OUTER JOIN sgd_def_paises pai on (mun.id_pais = pai.id_pais)
            LEFT OUTER JOIN departamento dep on (dep.dpto_codi = mun.dpto_codi and dep.id_pais = pai.id_pais)
            LEFT OUTER JOIN sgd_def_continentes con ON  (mun.id_cont = con.id_cont)
        WHERE
		    dep.DPTO_NOMB LIKE '%$consulta%' OR mun.MUNI_NOMB LIKE '%$consulta%'";

$rs     =$db->conn->Execute($isql);
?>
<form action="consulta_depmuni.php?krd<?=$krd?>&dependencia=<?=$dependencia?>" method="post" enctype="multipart/form-data" name="formAdjuntarArchivos">
<input type='hidden' name='<?=session_name()?>' value='<?=session_id()?>'> 
<table width='55%'  cellspacing="5"  align='center' class='borde_tab'>
	<tr align='center' class='titulos5'> 
		<td  class='titulos5' colspan='4'> 
        	RADICACION MASIVA <BR>CONSULTA DE LA DIVISION POLITICA ADMINISTRATIVA <BR>(DIVIPOLA)
        </td>
	</tr>
	<tr> 
		<td class="listado2" height="12" colspan="4"> 
        	<BR>Resultado de b&uacute;squeda: <?=$consulta ?><BR>
		</td>
	</tr>
	<tr> 
		<td width="10%" height="12" align="center" class="titulos3">Continente</td>
		<td width="30%" height="12" align="center" class="titulos3">Pa&iacute;s</td>
		<td width="30%" height="12" align="center" class="titulos3">Departamento</td>
		<td width="30%" height="12" align="center" class="titulos3">Municipio</td>
	</tr>
<?php
	while(!$rs->EOF)
	{			
		$cont = $rs->fields['CONT'];
		$dept = $rs->fields['DEPT'];
		$muni = $rs->fields['MUNI'];
		$pais = $rs->fields['PAIS'];
?>
	<tr align="center"> 
		<td class="listado2" height="12" ><span class="etextomenu"><?=$cont ?></span></td>
		<td class="listado2" height="12" ><span class="etextomenu"><?=$dept ?></span></td>
		<td class="listado2" height="12" ><span class="etextomenu"><?=$muni ?></span></td>
		<td class="listado2" height="12" ><span class="etextomenu"><?=$pais ?></span></td>
	</tr>
<?
		$rs->MoveNext();	
	 }
?>
	<tr align="center"> 
		<td height="30" class="listado2" colspan="4">
			<center>
			<input name="consultar" type="SUBMIT"  class="botones" id="envia22"  value="Consultar"></center>
		</td>
	</tr>
</table>
</form>
</body>
</html>
