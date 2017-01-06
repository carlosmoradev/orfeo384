<html>
<?
$ruta_raiz = "..";
?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<div id="spiffycalendar" class="text"></div>
<div id="spiffycalendar2" class="text"></div>
<link rel="stylesheet" type="text/css" href="../js/spiffyCal/spiffyCal_v2_1.css">
<script language="JavaScript" src="../js/spiffyCal/spiffyCal_v2_1.js"></script>
<script language="javascript"><!--
	setRutaRaiz ('<?=$ruta_raiz?>');
   var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "formulario", "fecha_ini","btnDate1","<?=$fecha_ini?>",scBTNMODE_CUSTOMBLUE);
   var dateAvailable2 = new ctlSpiffyCalendarBox("dateAvailable2", "formulario", "fecha_fin","btnDate2","<?=$fecha_fin?>",scBTNMODE_CUSTOMBLUE);
//-->
</script>
<?php
include "../config.php";
define('ADODB_ASSOC_CASE', 0);
include_once "../include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");	
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$dbCon = $db;
include_once ('../adodb/toexport.inc.php');
include_once ('../adodb/adodb.inc.php');
?>
<hr>
GENERACION DE ARCHIVOS CON DATOS ESTADISTICOS DE RADICICADOS Y SUS ANEXOS
<hr>
<form method="post" action=genCsvArchivo.php name="formulario">
       Fecha Inicial<script language="javascript">
			    dateAvailable.writeControl(); 
			    dateAvailable.dateFormat="yyyy-MM-dd";
			  </script><p>
       Fecha Final<script language="javascript">
			    dateAvailable2.writeControl(); 
			    dateAvailable2.dateFormat="yyyy-MM-dd";
			  </script><p>
				Digite los codigos de dependencia que desea genrear
<input type=text name=genDependencias value='<?=$genDependencias?>'>
<input type=submit name=aceptar value=generar>
</form>
<?
error_reporting(7);

	include_once('../adodb/adodb-errorpear.inc.php');
	include_once('../adodb/adodb.inc.php');
	include_once('../adodb/tohtml.inc.php');
	include_once('../adodb/adodb-paginacion.inc.php');
	include_once('../config.php');
	error_reporting(7);

	
	$db = ADONewConnection('oracle'); # eg 'mysql' o 'postgres'
	$db->Connect($servidor, $usuario, $contrasena, $servicio);

$db->concat("'",a.ra_asun,"'");
if($genDependencias)
{
$where_fecha = " (a.radi_fech_radi <= to_date('$fecha_fin 23:59:59','yyyy-mm-dd hh24:mi:ss') and a.radi_fech_radi >= to_date('$fecha_ini 00:00:00','yyyy-mm-dd hh24:mi:ss') ) and ";
$isql = "select 
a.radi_nume_radi Rad_Padre
, a.radi_fech_radi Fecha_Rad
, b.radi_nume_radi as Rad_Rel
, b.radi_fech_radi Fecha_Rad_Padre
, a.radi_depe_radi Dep_Radicacion
, c.depe_codi Dep_Anterior
, a.radi_depe_actu Dep_Actual
, d.usua_nomb Us_Actual
, c.usua_nomb Us_anterior
, a.ra_asun
from radicado a, radicado b, usuario c, usuario d
where 
 $where_fecha
 a.radi_nume_radi = b.radi_nume_deri (+)
 and a.radi_depe_radi in ($genDependencias)
 and a.radi_usu_ante  = c.usua_login (+)
 and a.radi_usua_actu = d.usua_codi
 and a.radi_depe_actu = d.depe_codi
order by a.radi_nume_radi, a.radi_fech_radi 
";
echo "<hr>la consulta Utilizadad es : $isql<hr>";
$db->conn->debug = true;
$rs = $db->Execute($isql);


echo "Total de Registros " . $rs->RecordCount( );
$rs->MoveFirst();
$path = "../bodega/estadisticasDatabox/EAdep($genDependencias)($fecha_ini~$fecha_fin)(".date("Ymd_h:i").").txt";
$fp = fopen($path, "w");
echo  "<hr>$fp<hr>";
if ($fp) 
{
	rs2tabfile($rs, $fp);
	fclose($fp);
	?>
	<hr>El archivo generado lo puede descargar de 
	<a href='<?=$path?>'><?=$path?></a>
	<hr>
	<?
}
}
?>
</body>
</html>
