<?php
session_start();

    $ruta_raiz = ".."; 
    if (!$_SESSION['dependencia'])
        header ("Location: $ruta_raiz/cerrar_session.php");
/**
 *				//// O J O	\\\\\
 * 				\\\\		/////
 * 
 Este modulo de consulta fue codificada su logica. En las entidades
 donde la búsqueda duplique, triplique, etc el mismo registro se debe
 ejecutar las siguientes sentencias: 
 
UPDATE SGD_DIR_DRECCIONES SET sgd_oem_codigo=null WHERE sgd_oem_codigo=0;
UPDATE SGD_DIR_DRECCIONES SET sgd_ciu_codigo=null WHERE sgd_ciu_codigo=0;
UPDATE SGD_DIR_DRECCIONES SET sgd_esp_codi=null WHERE sgd_esp_codi=0;
UPDATE SGD_DIR_DRECCIONES SET sgd_doc_fun=null WHERE sgd_doc_fun=0;
 */


$verrad = "";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");	

if (!$_SESSION['dependencia'])   include "$ruta_raiz/rec_session.php"; 

if($orden_cambio==1)
	(!$orderTipo) ? $orderTipo="desc" : $orderTipo="";

if(!$orderNo)  
{
	$orderNo="0";
	$order = 1;
}else
{
	$order = $orderNo +1;
}

if(!isset($fecha_ini)) $fecha_ini = date("Y/m/d");	 
if(!isset($fecha_fin)) $fecha_fin = date("Y/m/d");	

$encabezado1 = "$PHP_SELF?".session_name()."=".session_id()."&krd=$krd";
$linkPagina = "$encabezado1&n_nume_radi=$n_nume_radi&s_RADI_NOM=$s_RADI_NOM&s_solo_nomb=$s_solo_nomb&s_entrada=$s_entrada&s_salida=$s_salida&fecha_ini=$fecha_ini&fecha_fin=$fecha_fin&fecha1=$fecha1&tipoDocumento=$tipoDocumento&dependenciaSel=$dependenciaSel&orderTipo=$orderTipo&orderNo=$orderNo";
$encabezado = "".session_name()."=".session_id()."&krd=$krd&n_nume_radi=$n_nume_radi&s_RADI_NOM=$s_RADI_NOM&s_solo_nomb=$s_solo_nomb&s_entrada=$s_entrada&s_salida=$s_salida&fecha_ini=$fecha_ini&fecha_fin=$fecha_fin&fecha1=$fecha1&tipoDocumento=$tipoDocumento&dependenciaSel=$dependenciaSel&orderTipo=$orderTipo&orderNo=";
$nombreSesion = "".session_name()."=".session_id();
  
/* Se recibe el numero del Expediente a Buscar */
if($nume_expe or $adodb_next_page or $orderNo or $orderTipo or $orden_cambio or $dependenciaSel )
{
	//Se valida rango de fechas
	$sqlFecha = $db->conn->SQLDate('Y/m/d',"E.SGD_EXP_FECH"); 
	$where_general = " WHERE ".$sqlFecha . " BETWEEN '$fecha_ini' AND '$fecha_fin'" ;
	
	/* Se recibe la dependencia actual para bsqueda */
	if ($dependenciaSel == "99999")
		$where_general .= " AND R.RADI_DEPE_ACTU IS NOT NULL ";
	else
		$where_general .= " AND R.RADI_DEPE_ACTU = ".$dependenciaSel;	
	
	// Se valida el expediente
	if ($nume_expe) $where_general .= " AND E.SGD_EXP_NUMERO LIKE '%$nume_expe%' ";
	
	switch ($orderNo)
	{
		case 0:	
			$c_order = " ORDER BY 1 ";
			break;
		case 2:	
			$c_order = " ORDER BY 3 ";
			break;
		case 4:	
			$c_order = " ORDER BY 5 ";
			break;
		case 5:	
			$c_order = " ORDER BY 6 ";
			break;
		case 6:	
			$c_order = " ORDER BY 7 ";
			break;
		case 7:	
			$c_order = " ORDER BY 8 ";
			break;
		case 8:	
			$c_order = " ORDER BY 9 ";
			break;
	}
	$c_order .= (!$orderTipo) ? "asc" : "desc";

	$sql = 
    "SELECT 
		E.SGD_EXP_NUMERO    AS Expediente,
		$sqlFecha           AS DAT_FECHA,
        R.RADI_NUME_RADI    AS Radicado, 
		R.RADI_PATH         AS HID_RADI_PATH,
		R.RADI_NUME_RADI    AS HID_RADI_NUME_RADI,
		R.RA_ASUN           AS Asunto,
		R.RADI_CUENTAI      AS Cuenta_Interna,
		R.RADI_NUME_HOJA    AS N_Hojas 

    FROM 
        SGD_EXP_EXPEDIENTE E, 
        RADICADO R
    $where_general
		AND R.RADI_NUME_RADI = E.RADI_NUME_RADI";
	
	$rs = $db->conn->Execute($sql);
	if ($rs)
	{
		$nregis = $rs->recordcount();
		$fldTotal = $nregis;
	}
	else
		$fldTotal = 0;
	$ADODB_COUNTRECS = false;
     
    $pager = new ADODB_Pager($db,$sql,'adodb', true,$orderNo,$orderTipo);
	$pager->checkAll = false;
	$pager->checkTitulo = true; 	
	$pager->toRefLinks = $linkPagina;
	$pager->toRefVars = $encabezado;
}

$sql = "SELECT DEPE_NOMB, DEPE_CODI FROM DEPENDENCIA where depe_estado=1 ORDER BY 1";
$rs = $db->conn->execute($sql);
$cmb_dep = $rs->GetMenu2('dependenciaSel', $dependenciaSel, $blank1stItem = "99999:Todas las Dependencias",false,0,'class=select');
?>
<html>
<head>
<title>Consultas</title>
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<meta http-equiv="cache-control" content="no-cache">
<link rel="stylesheet" href="Site.css" type="text/css">
<link rel="stylesheet" href="../estilos/orfeo.css">
<script>
function limpiar()
{
	document.Search.elements['nume_expe'].value = "";
	document.Search.elements['dependenciaSel'].value = "99999";
}
</script>
</head>
<body class="PageBODY">
<div id="spiffycalendar" class="text"></div>
<link rel="stylesheet" type="text/css" href="../js/spiffyCal/spiffyCal_v2_1.css">
<script language="JavaScript" src="../js/spiffyCal/spiffyCal_v2_1.js"></script>
<script language="javascript"><!--
var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "Search", "fecha_ini","btnDate1","<?=$fecha_ini?>",scBTNMODE_CUSTOMBLUE);
var dateAvailable1 = new ctlSpiffyCalendarBox("dateAvailable1", "Search", "fecha_fin","btnDate2","<?=$fecha_fin?>",scBTNMODE_CUSTOMBLUE);
//-->
</script>
<form  name="Search"  action='<?=$_SERVER['PHP_SELF']?>?<?=$encabezado?>' method=post>
<table>
<tbody>
<tr>
	<td valign="top">
		<input type="hidden" name="FormName" value="Search"><input type="hidden" name="FormAction" value="search">
		<table border=0 cellpadding=0 cellspacing=2 class='borde_tab'>
		<tbody>
		<tr>
			<td class="titulos4" colspan="13"><a name="Search">B&uacute;squeda de Expedientes</a></td>
		</tr>
		<tr>
			<td class="titulos5">Expediente</td>
			<td class="listado5">
				<input class="tex_area" type="text" name="nume_expe" maxlength="" value="<?=$nume_expe?>" size="" >
			</td>
		</tr>
		<tr>
			<td class="titulos5">Desde Fecha (yyyy/mm/dd)</td>
			<td class="listado5">
				<script language="javascript">
					dateAvailable.writeControl();
					dateAvailable.dateFormat="yyyy/MM/dd";
				</script>
			</td>
		</tr>
		<tr>
			<td class="titulos5">Hasta Fecha (yyyy/mm/dd)</td>
			<td class="listado5">
				<script language="javascript">
					dateAvailable1.writeControl();
					dateAvailable1.dateFormat="yyyy/MM/dd";
				</script>
			</td>
		</tr>
		<tr> 
			<td class="titulos5">Dependencia Actual</td>
			<td class="listado5">
				<?php echo $cmb_dep; ?>
			</td>
		</tr>
		<tr> 
			<td colspan="3" align="right">
				<input class="botones" value="Limpiar" onclick="limpiar();" type="button">
				<input class="botones" value="B&uacute;squeda" type="submit">
			</td>
		</tr>
		</tbody> 
		</table>
	</td>
	<td valign="top">
		<a class="vinculos" href="busquedaHist.php?<?=session_name()."=".session_id()."&fechah=$fechah&primera=1&ent=2"; ?>">B&uacute;squeda por Hist&oacute;rico</a><br>	
		<a class="vinculos" href="busquedaPiloto.php?<?=session_name()."=".session_id()."&fechah=$fechah&primera=1&ent=2&s_Listado=VerListado"; ?>">B&uacute;squeda Cl&aacute;sica</a><br>
		<a class="vinculos" href="busquedaUsuActu.php?<?=session_name()."=".session_id()."&fechah=$fechah&primera=1&ent=2"; ?>">B&uacute;squeda por Usuarios</a>
	</td>
</tr>
</tbody>
</table>
<?php
if($nume_expe or $adodb_next_page or $orderNo or $orderTipo or $orden_cambio or $dependenciaSel )
{
?>
<table>
<tbody>
<tr>
	<td valign="top">
		<table width="100%" class="FormTABLE">
		<tbody>
		<tr>
			<td colspan="5" class="info"><b>Total Registros Encontrados: <?=$fldTotal?></b></td>
		</tr>
		</tbody>
		</table>
	</td>
</tr>
<tr>
	<td>
<?php
	$pager->Render($rows_per_page=20,$linkPagina,$checkbox=chkAnulados);
?>
	</td>
</tr>
</tbody>
</table>
<?php
}
?>
</form>
</body>
</html>
