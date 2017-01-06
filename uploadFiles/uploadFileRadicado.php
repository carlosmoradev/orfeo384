<?php
session_start();
/**
  * Se añadio compatibilidad con variables globales en Off
  * @autor Jairo Losada 2009-05
  * @licencia GNU/GPL V 3
  */
$krd = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$usua_doc = $_SESSION["usua_doc"];
$codusuario = $_SESSION["codusuario"];
$tip3Nombre=$_SESSION["tip3Nombre"];
$tip3desc = $_SESSION["tip3desc"];
$tip3img =$_SESSION["tip3img"];

if (isset($_GET["carpeta"]))
    $nomcarpeta = $_GET["carpeta"];
else
    $nomcarpeta = "";

if (isset($_GET["tipo_carpt"]))
    $tipo_carpt = $_GET["tipo_carpt"];
else
    $tipo_carpt = "";
    
if (isset($_GET["adodb_next_page"]))
    $adodb_next_page = $_GET["adodb_next_page"];
else
    $adodb_next_page = "";

if(isset($_GET["dep_sel"])) $dep_sel=$_GET["dep_sel"];
if(isset($_GET["btn_accion"])) $btn_accion=$_GET["btn_accion"];
if(isset($_GET["orderNo"])) $orderNo=$_GET["orderNo"];
if(isset($_REQUEST["orderTipo"])) $orderTipo=$_GET["orderTipo"];
if(isset($_REQUEST["busqRadicados"])) $busqRadicados=$_REQUEST["busqRadicados"];
if(isset($_REQUEST["Buscar"])) $Buscar=$_REQUEST["Buscar"];
if(isset($busq_radicados_tmp) && isset($_REQUEST["$busq_radicados_tmp"])) $$busq_radicados_tmp=$_REQUEST["$busq_radicados_tmp"];

$ruta_raiz = "..";
if(!isset($_SESSION['dependencia']))	include "$ruta_raiz/rec_session.php";
require_once("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler($ruta_raiz);
error_reporting(7);
$verrad = "";
?>
<HTML>
<head>
<link rel="stylesheet" href="<?=$ruta_raiz?>/estilos/orfeo.css">
<?php include_once "$ruta_raiz/js/funtionImage.php"; ?>
<!-- Adicionado Carlos Barrero SES 02/10/09-->
<script>
	function borrad(ruta)
		{
			if(document.formulario.valRadio.checked==false)
				{
					alert('Seleccione un radicado.');
					return false;
				}
			else
				{
					if(confirm("Esta seguro de borrar la imágen del radicado "+formulario.valRadio.value+" ?"))
						window.location=ruta+document.formulario.valRadio.value;
					else
						return false;
				}
		}
</script>
</head>
<BODY>
<FORM ACTION="<?=$_SERVER['PHPSELF']?>?<?=session_name()?>=<?=session_id()?>" method="POST">
<?
$varBuscada = "RADI_NUME_RADI";
include "$ruta_raiz/envios/paEncabeza.php";
include "$ruta_raiz/envios/paBuscar.php";
$encabezado = "".session_name()."=".session_id()."&depeBuscada=$depeBuscada&filtroSelect=$filtroSelect&tpAnulacion=$tpAnulacion&carpeta=$carpeta&tipo_carp=$tipo_carp&chkCarpeta=$chkCarpeta&busqRadicados=$busqRadicados&nomcarpeta=$nomcarpeta&agendado=$agendado&";
$linkPagina = "$PHP_SELF?$encabezado&orderTipo=$orderTipo&orderNo=$orderNo";
$encabezado = "".session_name()."=".session_id()."&adodb_next_page=1&depeBuscada=$depeBuscada&filtroSelect=$filtroSelect&tpAnulacion=$tpAnulacion&carpeta=$carpeta&tipo_carp=$tipo_carp&nomcarpeta=$nomcarpeta&agendado=$agendado&orderTipo=$orderTipo&orderNo=";
?>
</FORM>
<!--
Modificación Carlos Barrero -SES- permite borrar imagen vinculadas
<FORM ACTION="formUpload.php?krd=<?=$krd?>&<?=session_name()?>=<?=session_id()?>" method="POST">
-->
<FORM ACTION="formUpload.php?krd=<?=$krd?>&<?=session_name()?>=<?=session_id()?>" method="POST" name="formulario">
<center><input type="submit" value="Asociar Imagen del Radicado" name=asocImgRad class="botones_largo">
  <input type="button" value="Borrar Imagen del Radicado" name=borraImgRad class="botones_largo" onClick="return borrad('borraPath.php?krd=<?=$krd?>&<?=session_name()?>=<?=session_id()?>&numrad=');">

<!--
<center><input type="submit" value="Asociar Imagen del Radicado" name=asocImgRad class="botones_largo"></center>
-->
<?

if($Buscar AND $busq_radicados_tmp)
{

	include "$ruta_raiz/include/query/uploadFile/queryUploadFileRad.php";

	$rs=$db->conn->Execute($query);

	if ($rs->EOF)  {
		echo "<hr><center><b><span class='alarmas'>No se encuentra ningun radicado con el criterio de busqueda</span></center></b></hr>";
	}
	else{
		$orderNo =1;
		$orderTipo=" Desc ";
		$pager = new ADODB_Pager($db,$query,'adodb', true,$orderNo,$orderTipo);
		$pager->checkAll = false;
		$pager->checkTitulo = true;
		$pager->toRefLinks = $linkPagina;
		$pager->toRefVars = $encabezado;
		$pager->descCarpetasGen=$descCarpetasGen;
		$pager->descCarpetasPer=$descCarpetasPer;
		$pager->Render($rows_per_page=100,$linkPagina,$checkbox=chkAnulados);
	}
}
?>
</FORM>
</BODY>
</HTML>
