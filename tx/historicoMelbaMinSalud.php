<?php
session_start();

$ruta_raiz = ".."; 
if (!$_SESSION['dependencia'])
    header ("Location: $ruta_raiz/cerrar_session.php");

// Modificado 2010 aurigadl@gmail.com

/**
* Creado en la SSPD en el año 2003
* Se añadio compatibilidad con variables globales en Off
* @autor Jairo Losada 2009-05
* @licencia GNU/GPL V 3
*/

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

define('ADODB_ASSOC_CASE', 2);
$verrad         = "";
$krd            = $_SESSION["krd"];
$dependencia    = $_SESSION["dependencia"];
$usua_doc       = $_SESSION["usua_doc"];
$codusuario     = $_SESSION["codusuario"];
?>

<html>
<head>
    <link rel="stylesheet" href="<?=$ruta_raiz."/estilos/".$_SESSION["ESTILOS_PATH"]?>/orfeo.css">
    <script src="js/popcalendar.js"></script>
    <script src="js/mensajeria.js"></script>
    <div id="spiffycalendar" class="text"></div>
    <?php include_once "$ruta_raiz/js/funtionImage.php"; ?>
</head>

<?php include "$ruta_raiz/envios/paEncabeza.php"; ?>

<body bgcolor="#FFFFFF" topmargin="0" onLoad="window_onload();">

<?php

include_once    ("$ruta_raiz/include/db/ConnectionHandler.php");
require_once    ("$ruta_raiz/class_control/Mensaje.php");

if (!$db) $db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

$objMensaje     = new Mensaje($db);
$mesajes        = $objMensaje->getMsgsUsr($_SESSION['usua_doc'],$_SESSION['dependencia']);

 
  $sqlFecha = $db->conn->SQLDate("Y-m-d H:i A","b.RADI_FECH_RADI");                
  
  
 $isql = "select desc_tran,fecha,usuario,comentario,depe_origen,depe_destino,respuesta,fecha_limite
 from hist_eventostemp 
  where operacion IN ('AD','ADD','ADT','ARD','CFD','DD','DDD','DDF','MFD','REE','REI','RIS','RS','RSD','TRA','DES')
  and radi_nume_radi = $verradicado
 order by fecha desc" ;
 
  $rs     =$db->conn->Execute($isql);
  if ($rs->EOF)  {
      echo "<hr><center><b><span class='alarmas'>No se encuentra ningun Registro del Radicado Buscado</span></center></b></hr>";
  }
  else{
  $pager = new ADODB_Pager($db,$isql,'adodb', true,$orderNo,$orderTipo);
  $pager->checkAll = false;
  $pager->checkTitulo = true;
  $pager->toRefLinks = $linkPagina;
  $pager->toRefVars = $encabezado;
  $pager->descCarpetasGen=$descCarpetasGen;
  $pager->descCarpetasPer=$descCarpetasPer;
  if($_GET["adodb_next_page"]) $pager->curr_page = $_GET["adodb_next_page"];
  $pager->Render($rows_per_page=500,$linkPagina,$checkbox=chkAnulados);
  

}


  ?>
</body>
</html>
