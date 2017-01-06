<?php 
error_reporting(7);
require_once('nusoap/lib/nusoap.php');

$wsdl="http://localhost/orfeo-3.7.2/webServices2/servidor.php?wsdl"; 

$client=new soapclient2($wsdl, 'wsdl');  
//$extension = explode(".",$archivo_name);
//copy($archivo, "../bodega/tmp/visitas/".$archivo_name);
echo "Paso 1 <hr>";
$arregloDatos = array();

//$a = $client->call('darUsuario',$arregloDatos);
/*$arregloDatos[0] = 'jgonzal@superservicios.gov.co';
$correo = 'jgonzal@superservicios.gov.co';

print_r($client->call( 'getUsuarioCorreo', $correo ));
$a = $client->call( 'getUsuarioCorreo', $correo );
*/

$filename = '799822761_2007_08_27_14_30_14.doc';
//$filename = '799822761_2007_08_10_18_04_05.odt';
$strFile =  file_get_contents ( $filename );
$strFileEncoded64 = base64_encode($strFile);
echo "Paso 2 <hr>";
//var_dump($strFileEncoded64);
$radiNume = '20099000000032';

$correo = 'jgonzal@superservicios.gov.co';
$descripcion = 'OOOO BBB Prueba de Webservice Creación anexo.';
//$a = $client->call( 'getUsuarioCorreo', $correo );
$arregloDatos[0] = $radiNume;
$arregloDatos[1] = $strFileEncoded64;
$arregloDatos[2] = $filename;
$arregloDatos[3] = $correo;
$arregloDatos[4] = $descripcion;

//var_dump( $arregloDatos);
$a = $client->call( 'crearAnexo', $arregloDatos );
echo "Paso 3 <hr>";
// Display the result
//print_r($a);
var_dump( $a );
echo "Paso 4 <hr>";
// Display the request and response
/*echo '<h2>Request:</h2>';
echo '<pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
echo '<h2>Response:</h2>';
echo '<pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
*/
//var_dump( $a );
//die($a);

?>