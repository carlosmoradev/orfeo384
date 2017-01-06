<?
  session_start();
  $ruta_raiz = "..";
 foreach ($_GET as $key => $valor)   ${$key} = $valor;
 foreach ($_POST as $key => $valor)   ${$key} = $valor;

 $krd            = $_SESSION["krd"];
 $dependencia    = $_SESSION["dependencia"];
 $usua_doc       = $_SESSION["usua_doc"];
 $codusuario     = $_SESSION["codusuario"];
 $depe_codi_territorial     = $_SESSION["depe_codi_territorial"];
 $anoActual = date("Y");
 $ruta_raiz = "..";

if($_GET["gen_lisDefi"]) $gen_lisDefi=$_GET["gen_lisDefi"];
if($_GET["dep_sel"]) $dep_sel=$_GET["dep_sel"];
if($_GET["orderNo"]) $orderNo=$_GET["orderNo"];
if($_GET["orderTipo"]) $orderTipo=$_GET["orderTipo"];
if($_GET["busqRadicados"]) $busqRadicados=$_GET["busqRadicados"];
if($_GET["estado_sal_max"]) $estado_sal_max=$_GET["estado_sal_max"];
if($_GET["estado_sal"]) $estado_sal=$_GET["estado_sal"];
if($_GET["Buscar"]) $Buscar=$_GET["Buscar"];
 include_once "$ruta_raiz/include/db/ConnectionHandler.php";
 $db = new ConnectionHandler("$ruta_raiz");


 define('ADODB_FETCH_ASSOC',2);
 $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;


  $anoActual = date("Y");
  $ano_ini = date("Y");
  $mes_ini = substr("00".(date("m")-1),-2);
  if ($mes_ini==0) {$ano_ini=$ano_ini-1; $mes_ini="12";}
  $dia_ini = date("d");
  $ano_ini = date("Y");
  if(!$fecha_ini) $fecha_ini = "$ano_ini/$mes_ini/$dia_ini";
  $fecha_fin = date("Y/m/d") ;
  $where_fecha="";
//error_reporting(7);
?>
<html>
<head>
<title>Orfeo. Devolucion de Correspondencia</title>
<link rel="stylesheet" href="../estilos/orfeo.css">
</head>

<body bgcolor="#FFFFFF" topmargin="0" onLoad="window_onload();">
<div id="spiffycalendar" class="text"></div>
<link rel="stylesheet" type="text/css" href="js/spiffyCal/spiffyCal_v2_1.css">
<?
$ruta_raiz = "..";
include_once "$ruta_raiz/js/funtionImage.php";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");
?>
<script>

pedientesFirma="";
function back() {
    history.go(-1);
}

function recargar(){
	window.location.reload();	
}

function continuar(){
	accion = '<?=$pagina_sig?>?<?=session_name()."=".session_id()."&krd=$krd&fechah=$fechah&dep_sel=$dep_sel&estado_sal=$estado_sal&usua_perm_impresion=$usua_perm_impresion&estado_sal_max=$estado_sal_max" ?>';
	alert (accion);
}

function cambioDependecia (dep){
	document.formDep.action="cuerpo_masiva.php?krd=<?=$krd?>&dep_sel="+dep;
	//alert(document.formDep.action);
	document.formDep.submit();
}

function marcar()
{
	marcados = 0;

	for(i=0;i<document.formEnviar.elements.length;i++)
	{
		if(document.formEnviar.elements[i].checked==1)
		{
			marcados++;
		}
	}
	if(marcados>=1)
	{
		
		if (valPendsFirma())
			document.formEnviar.submit();
	}
	else
	{
		alert("Debe seleccionar un radicado");
	}
}

</script>
<?php 

if(!$estado_sal)   {$estado_sal=3;}
if(!$estado_sal_max) $estado_sal_max=3;
if($estado_sal==3)
{
	if($devolucion==1)
	{
		$accion_sal = "Devolucion de Documentos para la Gestion";
		$pagina_sig = "dev_corresp_gestion.php";
		$dev_documentos = "";
		$nomcarpeta="Devolucion de Documentos";
	}
	if(!$dep_sel) $dep_sel = $dependencia;
}
if($busq_radicados)
{
	$busq_radicados = trim($busq_radicados);
	$textElements = split (",", $busq_radicados);
	$newText = "";
	$i = 0;
	foreach ($textElements as $item)
	{
		$item = trim ( $item );
		if ( strlen ( $item ) != 0 )
		{
		   $i++;
		   if ($i != 1) $busq_and = " and "; else $busq_and = " ";
		   $busq_radicados_tmp .= " $busq_and radi_nume_sal like '%$item%' ";
		  }
     }
	 $dependencia_busq1 .= " $busq_radicados_tmp ";
	 if(!$dep_sel) $dep_sel = $dependencia;
}


echo "<hr>$dependencia_busq1<hr>";
$tbbordes = "#CEDFC6";
$tbfondo = "#FFFFCC";
if(!$orno){$orno=1;}
$imagen="flechadesc.gif";


 $encabezado = "".session_name()."=".session_id()."&krd=$krd&filtroSelect=$filtroSelect&accion_sal=$accion_sal&dependencia=$dependencia&tpAnulacion=$tpAnulacion&orderNo=";
 $linkPagina = "$PHP_SELF?$encabezado&accion_sal=$accion_sal&orderTipo=$orderTipo&orderNo=$orderNo";

 $swBusqDep = "si";
 $pagina_actual = "../devolucion/cuerpoDevGestion.php";
 $carpeta = "xx";
 include "../envios/paEncabeza.php";
 $varBuscada = "radi_nume_salida";
 include "../envios/paBuscar.php";
 $accion_sal = "Devolucion de Documentos";
 $pagina_sig = "../devolucion/dev_corresp_gestion.php";
 include "../envios/paOpciones.php";
 $orderNo=98;
 $orderTipo="desc";
 error_reporting(7);
$sqlChar = $db->conn->SQLDate("d-m-Y H:i A","SGD_RENV_FECH");
$dependencia_busq2 .= " and c.radi_depe_radi  = $dep_sel";
 $orderNo=98;
 $orderTipo="desc";
include "$ruta_raiz/include/query/devolucion/querycuerpoDevGestion.php";
 $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
?>
  <form name=formEnviar action='../devolucion/dev_corresp_gestion.php?<?=session_name()."=".session_id()."&krd=$krd" ?>&estado_sal=<?=$estado_sal?>&estado_sal_max=<?=$estado_sal_max?>&pagina_sig=<?=$pagina_sig?>&dep_sel=<?=$dep_sel?>&nomcarpeta=<?=$nomcarpeta?>&orderNo=<?=$orderNo?>' method=post>
 <?
	$encabezado = "".session_name()."=".session_id()."&krd=$krd&estado_sal=$estado_sal&estado_sal_max=$estado_sal_max&accion_sal=$accion_sal&dependencia_busq2=$dependencia_busq2&dep_sel=$dep_sel&filtroSelect=$filtroSelect&nomcarpeta=$nomcarpeta&orderTipo=$orderTipo&orderNo=";
        $encabezado = session_name()."=".session_id()."&dep_sel=$dep_sel&krd=$krd&estado_sal=$estado_sal&usua_perm_impresion=$usua_perm_impresion&fechah=$fechah&estado_sal_max=$estado_sal_max&ascdesc=$ascdesc&orno=";
	$linkPagina = "$PHP_SELF?$encabezado&orderTipo=$orderTipo&orderNo=$orderNo";

	$pager = new ADODB_Pager($db,$isql,'adodb', true,$orderNo,$orderTipo);
	$pager->checkAll = false;
	$pager->checkTitulo = true;
	$pager->toRefLinks = $linkPagina;
	$pager->toRefVars = $encabezado;
	if($_GET["adodb_next_page"]) $pager->curr_page = $_GET["adodb_next_page"];
	$pager->Render($rows_per_page=20,$linkPagina,$checkbox=chkEnviar);

 ?>
  </form>
</body>
</html>