<?
session_start();
/**
  * Envio de Documentos entre Entidades TeleOrfeo
  * @auto Orlando Burgos  SuperServicios
  * @fecha 200808 
  */

$krd = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$usua_doc = $_SESSION["usua_doc"];
$codusuario = $_SESSION["codusuario"];

error_reporting(7);
require_once('../nusoap/lib/nusoap.php');

$wsdl="http://localhost/orfeo-3.7.2/webServices2/OrfeoGPLorg.php?wsdl"; 



$client=new soapclient2($wsdl, 'wsdl');  

?>
<FORM ACTION="envioTeleOrfeo.php" method="GET">
<?
echo "Entidad a la Cual se radicara un Documento ";
$arregloDatos = array();

$arregloDatos[0] = "Entidad solicitante";

error_reporting(7);
$a = $client->call('getEntidades',$arregloDatos);
?>
	<select name="entidadOrfeoGPL" onChange="submit();">
	<?
	foreach ($a as $ent => $v1) {
		?>
		<option value='-1'>Seleccione Una Entidad</option>
		<option value='<?=$ent?>'><?=$v1[1]?></option>
	<?    
	}
	?>
	</select>
</FORM>
<?
$entidadOrfeoGPL = $_GET["entidadOrfeoGPL"];
echo "<hr> <a href='". $a[$entidadOrfeoGPL][3] ."' target=wdslent>  ". $a[$entidadOrfeoGPL][3] ." </a> <hr>";
$wdslEntidad = $a[$entidadOrfeoGPL][3];
if (url_exists($wdslEntidad)) {
    echo "Entidad En Linea";
} else {
    echo "Entidad No Disponible Temporalmente. . . ";
}

function url_exists($wdslEntidad) {
    $hdrs = @get_headers($wdslEntidad);
    return is_array($hdrs) ? preg_match('/^HTTP\\/\\d+\\.\\d+\\s+2\\d\\d\\s+.*$/',$hdrs[0]) : false;
} 
?>

<HTML>


Entidad
</HTML>

