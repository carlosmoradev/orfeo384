<?php
session_start();

    $ruta_raiz = ".."; 
    if (!$_SESSION['dependencia'])
        header ("Location: $ruta_raiz/cerrar_session.php");

/**
  * Se anadio compatibilidad con variables globales en Off
  * @autor Jairo Losada 2009-05
  * @licencia GNU/GPL V 3
  */

foreach($_GET as $k=>$v) $$k=$v;

$krd         = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$usua_doc    = $_SESSION["usua_doc"];
$codusuario  = $_SESSION["codusuario"];
$tip3Nombre  = $_SESSION["tip3Nombre"];
$tip3desc    = $_SESSION["tip3desc"];
$tip3img     = $_SESSION["tip3img"];
$ruta_raiz   = "..";

if (isset($_GET["carpeta"]))
    $nomcarpeta      = $_GET["carpeta"];
else
    $nomcarpeta = "";

if (isset($_GET["tipo_carpt"]))
    $tipo_carpt      = $_GET["tipo_carpt"];
else
    $tipo_carpt = "";
    
if (isset($_GET["adodb_next_page"]))
    $adodb_next_page = $_GET["adodb_next_page"];
else
    $adodb_next_page = "";



$sendSession     = session_name().'='.session_id(); 

?>
<html>
<head>
<title>Administracion de orfeo</title>
<link rel="stylesheet" href="../estilos/orfeo.css">
</head>
<body>
<table width="71%" align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
<tr bordercolor="#FFFFFF">
	<td colspan="2" class="titulos4"><div align="center"><strong>M&Oacute;DULO DE ADMINISTRACI&Oacute;N</strong></div></td>
</tr>
<tr bordercolor="#FFFFFF">
<td align="center" class="listado2" width="48%">
    <a href='usuario/mnuUsuarios.php?<?=$sendSession?>&krd=<?=$krd?>' target='mainFrame' class="vinculos">1. USUARIOS Y PERFILES</a>
</td>
<td align="center" class="listado2" width="48%"><a href="tbasicas/adm_dependencias.php?<?=$sendSession?>" class="vinculos" target="mainFrame">2. DEPENDENCIAS</a></td>
</tr>
<tr bordercolor="#FFFFFF">
<td align="center" class="listado2" width="48%"> <a  href="tbasicas/adm_nohabiles.php?<?=$sendSession?>" class="vinculos" target='mainFrame'>3. DIAS NO HABILES</a></td>
<td align="center" class="listado2" width="48%"><a href="tbasicas/adm_fenvios.php?<?=$sendSession?>" class="vinculos" target='mainFrame'>4. ENV&Iacute;O DE CORRESPONDENCIA</a> </td>
</tr>
<tr bordercolor="#FFFFFF">
<td align="center" class="listado2" width="48%"><a href="tbasicas/adm_tsencillas.php?<?=$sendSession?>" class="vinculos" target='mainFrame'>5. TABLAS SENCILLAS</a></td>
<td align="center" class="listado2" width="48%"><a href="tbasicas/adm_trad.php?<?=$sendSession?>&krd=<?=$krd?>" class="vinculos" target='mainFrame'>6. TIPOS DE RADICACI&Oacute;N</a></td>
</tr>
<tr bordercolor="#FFFFFF">
<td align="center" class="listado2" width="48%"><a href="tbasicas/adm_paises.php?<?=$sendSession?>" class="vinculos" target='mainFrame'>7. PA&Iacute;SES</a></td>
<td align="center" class="listado2" width="48%"><a href="tbasicas/adm_dptos.php?<?=$sendSession?>" class="vinculos" target='mainFrame'>8. DEPARTAMENTOS</a></td>
</tr>
<tr bordercolor="#FFFFFF">
<td align="center" class="listado2" width="48%"><a href="tbasicas/adm_mcpios.php?<?=$sendSession?>" class="vinculos" target='mainFrame'>9. MUNICIPIOS</a></td>
<td align="center" class="listado2" width="48%"><a href="tbasicas/adm_tarifas.php?<?=$sendSession?>" class="vinculos" target='mainFrame'>10. TARIFAS</a></td>
</tr>
<tr bordercolor="#FFFFFF">
<td align="center" class="listado2" width="48%"><a href="tbasicas/adm_plantillas.php?<?=$sendSession?>" class="vinculos" target='mainFrame'>11. PLANTILLAS</a></td>
<td align="center" class="listado2" width="48%"><a href="tbasicas/adm_soportes.php?<?=$sendSession?>" class="vinculos" target='mainFrame'>12. SOPORTES</a></td>
</tr>
<tr bordercolor="#FFFFFF">
<!--
<td align="center" class="listado2" width="48%"><a href="adm_plantillas_gen.php?<?=$sendSession?>" class="vinculos" target='mainFrame'>15. CABECERAS DE COMBINACION</a></td>
</tr>
<tr bordercolor="#FFFFFF">
<td align="center" class="listado2" width="48%"><a href="tbasicas/adm_contactos.php?<?=$sendSession?>" class="vinculos" target='mainFrame'>11. CONTACTOS</a></td>
<td align="center" class="listado2" width="48%"><a href="tbasicas/adm_esp.php?<?=$sendSession?>&krd=<?=$krd?>" class="vinculos" target='mainFrame'>12. ENTIDADES</a></td>
</tr>
-->
</table>
<br>



<?
 // MODULO OPCIONAL DE ADMINISTRACION DE FUNCIONARIOS Y ENTIDADES
 /* Por SuperSolidaria
    Modifico y Adapto Jairo Losada 08/2009 */

?>
<!--Estas funciones estan activas pero no se sabe para que son
<table width="71%" align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
<tr bordercolor="#FFFFFF">
	<td colspan="2" class="titulos4" align="center">
          Opcional x SES
        </td>
</tr>
<tr bordercolor="#FFFFFF">
	<td align="center" class="listado2" width="48%">
		<a href='entidad/listaempresas.php?<?=$sendSession?>&krd=<?=$krd?>' target='mainFrame'  class="vinculos">12. ENTIDADES  V.SES</a>
	</td>
	<td align="center" class="listado2" width="48%">
	<a href='usuario/listafuncionarios.php?<?=$sendSession?>' target='mainFrame'  class="vinculos">12.1 FUNCIONARIO - ENTIDAD</a>
</td>
</tr>
</table>
-->
</body>
</html>
