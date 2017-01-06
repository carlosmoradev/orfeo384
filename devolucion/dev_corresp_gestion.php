<?php
session_start();

 foreach ($_GET as $key => $valor)   ${$key} = $valor;
 foreach ($_POST as $key => $valor)   ${$key} = $valor;

 $krd            = $_SESSION["krd"];
 $dependencia    = $_SESSION["dependencia"];
 $usua_doc       = $_SESSION["usua_doc"];
 $codusuario     = $_SESSION["codusuario"];
 $depe_codi_territorial     = $_SESSION["depe_codi_territorial"];
 $anoActual = date("Y");
 $ruta_raiz = "..";
 include_once "$ruta_raiz/include/db/ConnectionHandler.php";
 $db = new ConnectionHandler("$ruta_raiz");
$ruta_raiz = "..";
if (!is_object($db))
{	include_once "$ruta_raiz/include/db/ConnectionHandler.php";
	$db = new ConnectionHandler("$ruta_raiz");
}


if (!defined('ADODB_FETCH_ASSOC'))	define('ADODB_FETCH_ASSOC',2);
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
$encabezado_i = "estado_sal=$estado_sal&motivo_devol=$motivo_devol&estado_sal_max=$estado_sal_max&pagina_sig=$pagina_sig&dep_sel=$dep_sel&krd=$krd";
?>
<head>
<link rel="stylesheet" href="../estilos/orfeo.css">
<?php include_once "$ruta_raiz/js/funtionImage.php"; ?>
</head>
<BODY>
<center><span class=vinculos> <a href="cuerpoDevGestion.php?<?=$encabezado_i?>&<?=session_name().'='.session_id()."&devolucion=1"?>"> Devolver al Listado
 </a></span></CENTER>
<TABLE width="100%" class='borde_tab' cellspacing="5">
  <TR>
    <TD height="30" valign="middle"   class='titulos5' align="center">DEVOLUCION DE DOCUMENTOS</td>
  </tr>
</table>
<div id="spiffycalendar" class="text"></div>
<form name="new_product"  action='dev_corresp_gestion.php?<?=session_name()."=".session_id()."&krd=$krd&fecha_h=$fechah&$encabezado_i"?>' method=post><center>
<?
if(!$devolver_rad or $motivo_devol== 0)
{
?>
<table><tr><td></td></tr></table>
<center>
<table width="350" class="borde_tab" cellpadding="5">
  <TR>
    <TD width="125" height="21"  class='titulos2'>Tipo de Devolucion<br></TD>
    <TD width="225" align="right" valign="top" class='listado2'>
		<?
		$ss_RADI_DEPE_ACTUDisplayValue = "----- Escoja un Motivo -----";
		$valor = 0;
		include "$ruta_raiz/include/query/devolucion/querytipo_dev_corresp.php";
		$sql = "select $sqlConcat ,SGD_DEVE_CODIGO from SGD_DEVE_DEV_ENVIO
				WHERE SGD_DEVE_INDI > 1
				 order by SGD_DEVE_CODIGO";
			$rsDep = $db->conn->Execute($sql);
			print $rsDep->GetMenu2("motivo_devol","$motivo_devol", $blank1stItem = "$valor:$ss_RADI_DEPE_ACTUDisplayValue", false, 0," class='select'");
	$municodi="";$muninomb="";$depto="";
	?>
   </TD>
  </TR>
  <TR>
    <TD height="26" class='titulos2'>Comentarios</TD>
    <TD valign="top" class='listado2'>
	<input type=text name=comentarios_dev value='<?=$comentarios_dev?>' class=tex_area size=70>
    </TD>
  </TR><tr>
   </tr><tr><td height="26" colspan="2" valign="top" class='titulos2'> <center>
     <input type=SUBMIT name='devolver_rad'  value = 'CONFIRMAR DEVOLUCION' class='botones_largo' ></center></td>
  </tr>
</TABLE>
</center>
<table><tr><td></td></tr></table>
<?php
}else
{//<input type=SUBMIT name='devolver_rad'  value = 'CONFIRMAR DEVOLUCION' class=ebuttons2 onclick="markDev();"></center></td>
	error_reporting(7);
	$isql = "select SGD_DEVE_DESC
		from SGD_DEVE_DEV_ENVIO
		WHERE SGD_DEVE_CODIGO = $motivo_devol
		";
	$sim = 0;
	if (!defined('ADODB_FETCH_ASSOC'))	define('ADODB_FETCH_ASSOC',2);
    $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$rs = $db->conn->Execute($isql);
	$motivo = $rs->fields["SGD_DEVE_DESC"];
}
error_reporting(7);
/*
*Procediminiento que recorre el array de valores de radicados a devolver.....
*/
if(!$radicados_dev  or $motivo_devol==0)
{

    $num = count($checkValue);
	$i = 0;
	while ($i < $num)
	{
	 $record_id = key($checkValue);
	 $radicados_dev .= $record_id .",";
	 next($checkValue);
	$i++;
	}
	$radicados_devOrginal = $radicados_dev;
	$radicados_dev = str_replace("-","",$radicados_dev);
	$radicados_dev .= "9999";
}

echo "<input type=hidden name=radicados_dev value='$radicados_dev'>";
echo "<input type=hidden name=radicados_devOrginal value='$radicados_devOrginal'>";
if($devolver_rad  and $motivo_devol==0)
{
 echo "
		 <script>
		 alert('Elija un Motivo de devolucion.');
		 </script>
		 ";
}
if($devolver_rad  and $motivo_devol)
{
 if($motivo_devol != 0)
    {
	 $systemDate = $db->conn->OffsetDate(0,$db->conn->sysTimeStamp);
	$sqlConcat = $db->conn->Concat("'$comentarios_dev'","'-'","sgd_renv_observa");


	$radicados_devOrginal2 = $radicados_devOrginal;
	$radicados_devOrginal = $radicados_devOrginal . ")";
	$radicados_devOrginal = str_replace(",)","", $radicados_devOrginal);
	$radicados_devOrginal = str_replace("-"," and SGD_DIR_TIPO=",$radicados_devOrginal );
	$radicados_devOrginal = str_replace(",",") or (radi_nume_salida=",$radicados_devOrginal );
	$radicados_devOrginal = "((radi_nume_salida=$radicados_devOrginal))";
	$condicionUp = $radicados_devOrginal;

	$isqlu = "update anexos
			set
			 anex_estado=2,
			sgd_deve_fech=$systemDate,
			sgd_deve_codigo = $motivo_devol
		  where $condicionUp ";
	    $rs = $db->conn->Execute($isqlu);
	
            
        $num = count($checkValue);
	$radicados_devOrginal = $radicados_devOrginal2;
       
	   $i = 0;
		while ($i < $num)
	 	{
	   	$record_id = key($checkValue);
	   	$radicados_sel = $record_id;
		$radicados_lis .= $record_id .",";
 	   	$chkt = $radicados_sel;
		$systemDate = $db->conn->OffsetDate(0,$db->conn->sysTimeStamp);
                
		$isql_hl= "insert
		into hist_eventos(DEPE_CODI, HIST_FECH, USUA_CODI, RADI_NUME_RADI, HIST_OBSE, USUA_CODI_DEST, USUA_DOC, SGD_TTR_CODIGO)
		values ($dependencia, $systemDate ,$codusuario,$chkt,'Devolucion ($motivo). $comentarios_dev',NULL,'$usua_doc',28)";

		$rs = $db->conn->Execute($isql_hl);
                $isq_bp = " select distinct ANEX_RADI_NUME from anexos where radi_nume_salida = $chkt";

                $rsbp = $db->conn->Execute($isq_bp);
                $radi_nume_padre = $rsbp->fields["ANEX_RADI_NUME"];
                if($radi_nume_padre != $chkt)
		{
			$isql_hl= "insert
			into hist_eventos(DEPE_CODI   ,HIST_FECH,USUA_CODI  ,RADI_NUME_RADI   ,HIST_OBSE         ,USUA_CODI_DEST,USUA_DOC   ,SGD_TTR_CODIGO)
			values ($dependencia, $systemDate ,$codusuario,$radi_nume_padre,'Devolucion($chkt, $motivo). $comentarios_dev',NULL,'$usua_doc','28')";
			$rs = $db->conn->Execute($isql_hl);
    		}
		next($checkValue);
	   $i++;
	 }
	?>
	<table><tr><td></td></tr></table>
	<table><tr><td></td></tr></table>
	<TABLE width="100%" class='borde_tab' cellspacing="5"><TR><TD height="30" valign="middle"   class='listado2' align="center">
		<center><b>Se ha realizado la devolucion de los siguientes registros enviados<br>
		<?=$radicados_lis?></b></center>
	</td></tr></table>
	<table><tr><td></td></tr></table>
	<table><tr><td></td></tr></table>
	<?
	//echo "DEVUELTOS  ".$radicados_dev;
	$sqlFecha = $db->conn->SQLDate("d-m-Y H:i A","a.SGD_RENV_FECH");


 $radicados_devOrginal = $radicados_devOrginal . ")";
 $radicados_devOrginal = str_replace(",)","", $radicados_devOrginal);
 $radicados_devOrginal = str_replace("-"," and a.SGD_DIR_TIPO=",$radicados_devOrginal );
 $radicados_devOrginal = str_replace(",",") or (radi_nume_salida=",$radicados_devOrginal );
 $radicados_devOrginal = "and ((radi_nume_salida=$radicados_devOrginal))";
 //$condicion = "and $sqlConcatC in($radicados_dev)";
 $condicion = "and $sqlConcatC in($radicados_dev)";
 $condicion = $radicados_devOrginal;

 //$where_impresion = $condicion;
 include "$ruta_raiz/include/query/devolucion/querycuerpoDevGestion.php";

  $rs = $db->conn->Execute($isql);
  $pager = new ADODB_Pager($db,$isql,'adodb', true,$orderNo,$orderTipo);
	$pager->toRefLinks = $linkPagina;
	$pager->toRefVars = $encabezado;
	$pager->checkAll = true;
	$pager->checkTitulo = false;
	$pager->Render($rows_per_page=20,$linkPagina);
		//echo $radicados_dev;
   }
   else {
	echo "<span class=etexto><b>No se actualizaron los registros <br>Debe seleccionar un tipo de devolucion<br>";
	echo "<input type=hidden name=devolucion_rad value=si>";
  }
 }
 if(!$devolver_rad  or !$motivo_devol)
 {
 $sqlFecha = $db->conn->SQLDate("d-m-Y H:i A","a.SGD_RENV_FECH");
 include "$ruta_raiz/include/query/devolucion/querydev_corresp_gestion.php";
 $radicados_dev = str_replace(",9999", "", $radicados_dev);
 //$radicados_devOrginal = ",9999";
 $radicados_devOrginal = $radicados_devOrginal . ")";
 $radicados_devOrginal = str_replace(",)","", $radicados_devOrginal);
 $radicados_devOrginal = str_replace("-"," and a.SGD_DIR_TIPO=",$radicados_devOrginal );
 $radicados_devOrginal = str_replace(",",") or (radi_nume_salida=",$radicados_devOrginal );
 $radicados_devOrginal = "and ((radi_nume_salida=$radicados_devOrginal))";

 $condicion = "and $sqlConcatC in($radicados_dev)";
 $condicion = $radicados_devOrginal;

$isql = 'select 
			a.anex_estado AS "CHU_ESTADO"
		        ,CAST(a.radi_nume_salida as varchar(20)) AS "IMG_RADICADO_SALIDA"
			,c.RADI_PATH AS "HID_RADI_PATH"						
		        ,substr(trim( CAST( a.sgd_dir_tipo AS VARCHAR(5) ) ),2,5) AS COPIA
			,CAST(a.anex_radi_nume as varchar(20)) AS RADICADO_PADRE
			,c.radi_fech_radi AS FECHA_RADICADO
			,dir.sgd_dir_nomremdes||'."'/'".'||dir.sgd_dir_nombre||'."'<br>'".'||dir.sgd_dir_direccion AS DESCRIPCION
			,a.sgd_fech_impres AS FECHA_IMPRESION
			,a.anex_creador AS GENERADO_POR
                        , '. $nombre .  ' AS "CHK_ANULAR"
                        , a.sgd_dir_tipo     AS "HID_sgd_dir_tipo"
			,a.anex_nomb_archivo AS "HID_ANEX_NOMB_ARCHIVO" 
			,a.anex_tamano       AS "HID_ANEX_TAMANO"
			,a.ANEX_RADI_FECH    AS "HID_ANEX_RADI_FECH" 
			,' . "'WWW'" . '     AS "HID_WWW" 
			,' . "'9999'" . '    AS "HID_9999"     
			,a.anex_tipo         AS "HID_ANEX_TIPO" 
			,a.anex_radi_nume    AS "HID_ANEX_RADI_NUME" 
			,a.sgd_dir_tipo      AS "HID_SGD_DIR_TIPO"
			,a.sgd_deve_codigo   AS "HID_SGD_DEVE_CODIGO"
		from anexos a,usuario b, radicado c, sgd_dir_drecciones dir
		where a.ANEX_ESTADO>=' .$estado_sal. ' '.
				$dependencia_busq2 .'
				and a.ANEX_ESTADO <= ' . $estado_sal_max . '
				and a.radi_nume_salida=c.radi_nume_radi
				and a.radi_nume_salida=dir.radi_nume_radi
				and a.sgd_dir_tipo    =dir.sgd_dir_tipo
				and a.anex_creador=b.usua_login 
			        and a.anex_borrado= ' . "'N'" . '
				and a.sgd_dir_tipo != 7
				and (a.sgd_deve_codigo >= 90 or a.sgd_deve_codigo =0 or a.sgd_deve_codigo is null)
				AND
				((c.SGD_EANU_CODIGO != 2
				AND c.SGD_EANU_CODIGO != 1) 
				or c.SGD_EANU_CODIGO IS NULL)
                                 ' . 
			        $condicion . '	
                                order by a.radi_nume_salida ';

  $rs = $db->conn->Execute($isql);
  $pager = new ADODB_Pager($db,$isql,'adodb', false,$orderNo,$orderTipo);
	$pager->toRefLinks = $linkPagina;
	$pager->toRefVars = $encabezado;
	$pager->checkAll = true;
	$pager->checkTitulo = false;
	$pager->Render($rows_per_page=20,$linkPagina,$checkbox=chkEnviar);

}
?>
</form>
<script>
function markDev()
{
	for(i=1;i<document.new_product.elements.length;i++)
	document.new_product.elements[i].checked=1;
}
</script>
</html>