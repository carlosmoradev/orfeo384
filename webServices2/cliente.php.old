
<?php 
require_once('nusoap/lib/nusoap.php');

$wsdl="http://wiki.superservicios.gov.co:81/~wduarte/br3.6.0/webServices/servidor.php?wsdl"; 

$client=new soapclient2($wsdl, 'wsdl');  
//$extension = explode(".",$archivo_name);
//copy($archivo, "../bodega/tmp/visitas/".$archivo_name);

$arregloDatos = array();

$a = $client->call('darUsuario',$arregloDatos);
var_dump($a);



?>



