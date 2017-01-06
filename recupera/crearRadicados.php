<html>
<head>
<link rel="stylesheet" href="../estilos/orfeo.css">
<script src="../js/popcalendar.js"></script>
<script src="../js/mensajeria.js"></script>
 <div id="spiffycalendar" class="text"></div>
</head>
<?
	if (!$ruta_raiz) $ruta_raiz="..";
	include_once("$ruta_raiz/include/db/ConnectionHandler.php");
	require_once("$ruta_raiz/class_control/TipoDocumento.php");
  include_once("buscarFila.php");
	if (!$db) $db = new ConnectionHandler("$ruta_raiz");
	error_reporting(7);
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	$objTipoDocto = new TipoDocumento($db);

	$nombre_us1 = "";$nombre_us2 = "";$nombre_us3 = "";
	$prim_apel_us1 = ""; $prim_apel_us2 = ""; $prim_apel_us3 = "";
	$seg_apel_us1 = ""; $seg_apel_us2 = ""; $seg_apel_us3 = "";

	if (!$ruta_raiz) $ruta_raiz="..";
	error_reporting(7);
	$linkFila = $_GET["path_anexo"];
	$fecha = $_GET["fechaAnexo"];
	$radicadoPadre = $_GET["radicadoPadre"];


echo "***".$fecha."***";

/*
Inserta Anexo
Carlos Barrero 29-10-09
*/

$rutarad=split("_",$linkFila);
print_r($rutarad);

$numrad_e=substr($rutarad[0],-15);
$nombre_archivo_e=$numrad_e."_".$rutarad[1];


$consecutivo=substr($rutarad[1],0,5);
$dia=substr($fecha,5,2);
$hora=substr($fecha,-6);

if(!trim($_GET['path']))
{
	$path=$_GET['path_anexo'];
}
else
	$path=$_GET['path'];	

if($_GET['radsalida']!="")
{
/*
Inserta dir_direcciones
*/
$num_dir=$db->conn->GenID('SEC_DIR_DIRECCIONES');


$ins_dir_dir="insert into sgd_dir_drecciones(sgd_dir_codigo,sgd_dir_tipo,sgd_oem_codigo,sgd_ciu_codigo,radi_nume_radi,sgd_esp_codi,muni_codi,dpto_codi,sgd_dir_direccion,sgd_dir_telefono,sgd_sec_codigo,sgd_dir_nombre,sgd_dir_nomremdes,sgd_trd_codigo)
values(".$num_dir.",1,0,14200,".$_GET['radsalida'].",0,".$_GET['muni_codi'].",".$_GET['depto_codi'].",'".$_GET['direccion']."','".$_GET['telefono']."',0,'".$_GET['nombre']."','',3)";

//echo $ins_dir_dir."<br><br>";

$db->conn->Execute($ins_dir_dir);

/*
Inserta radicado
*/

$ins_radicado="insert into radicado(radi_nume_radi,radi_fech_radi,tdoc_codi,radi_path,radi_usua_actu,radi_depe_actu,radi_usua_radi,radi_depe_radi,ra_asun,radi_nume_deri)
values(".$_GET['radsalida'].",sysdate,0,'".substr($path,-28)."',1,".$_GET['depe_actu'].",1,500,'".$_GET['asunto']."',".$radicadoPadre.")";


//echo $ins_radicado."<br><br>";

$db->conn->Execute($ins_radicado);

}


//busca si el anexo ya existe
$sql_busca="select anex_radi_nume from anexos where anex_nomb_archivo='".$nombre_archivo_e."'";
$rs_busca=$db->conn->Execute($sql_busca);



if(($rs_busca->RecordCount())==0)
{
$sql_ins_anex="insert into anexos(anex_radi_nume,anex_codigo,anex_tipo,anex_tamano,anex_solo_lect,anex_creador,anex_desc,anex_numero,anex_nomb_archivo,anex_borrado,anex_origen,anex_salida";


if($_GET['radsalida']!="")
{
	$sql_ins_anex.=",radi_nume_salida";
}

$sql_ins_anex.=",anex_estado,sgd_rem_destino,sgd_dir_tipo,anex_depe_creador,anex_fech_anex,sgd_apli_codi)
values(".$radicadoPadre.",".$radicadoPadre.$consecutivo.",1,0,'S','ADMIN1','ANEXO RECUPERADO ',1,'".$nombre_archivo_e."','N',0,";

if($_GET['radsalida']!="")
{
	$sql_ins_anex.="1,".$_GET['radsalida'];
}
else

	$sql_ins_anex.="0";


$sql_ins_anex.=",2,1,1,500,to_date('".$dia."/10/2009 ".$hora.":00','dd/mm/yyyy hh24:mi:ss'),0)";


//echo $sql_ins_anex;
echo "<h1><strong>ANEXO CREADO CORRECTAMENTE CON RADICACION</strong></h1>";
}
/*
Si el radicado ya existe
*/
else
{
if($_GET['radsalida']!="")
{
	$sql_ins_anex="update anexos set anex_salida=1,radi_nume_salida=".$_GET['radsalida'].",anex_estado=2 where anex_nomb_archivo='".$nombre_archivo_e."'";

	//echo $sql_ins_anex;
	echo "<h1><strong>ANEXO ACTUALIZADO CORRECTAMENTE</strong></h1>";
}
else
	echo "<h1><strong>EL ANEXO YA EXISTE</strong></h1>";
}
$db->conn->Execute($sql_ins_anex);


//   echo "<br><br>Se va crear el Anexo $linkFila & $fecha";
?>
</table>
</html>
