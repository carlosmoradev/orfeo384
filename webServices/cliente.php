<?php 
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

require_once('nusoap/lib/nusoap.php');

$wsdl="http://localhost/orfeo-3.8.0/webServices/servidor.php?wsdl"; 
echo "Llego aka";
$client=new soapclient2($wsdl, 'wsdl');  
echo "Llego aka";

$arregloDatos = array();

$arregloDatos[0] = 10402;                  /* Destino Valores:
							  -10404:Sustancias – Control Nacional
							  -12875:Sustancias Control Especial
							  -10403:Aerocivil
							  -10402:DIMAR */
$arregloDatos[1] = "Observaciones de la Solicitud";      // Observaciones 
$arregloDatos[2] = "N";                      // Tipo de Persona  ---  -J si es jurídica
					      //       	              -N si es natural
$arregloDatos[3] = "CC";                  // TI: Tarjeta de identidad.
						/* CC: Cédula de Ciudadanía
						CE: Cédula de extranjería
						PA: Pasaporte
						RE: Registro civil
						CX: Cédula extranjero
						NI:  Nit
						NE: Nit de extranjería
						PJ: Personería Jurídica
						EO: Entidad oficial
						RM: Registro mercantil */
$arregloDatos[4] = "79802120";
$arregloDatos[5] = "JairoP";
$arregloDatos[6] = "LosadaP";
$arregloDatos[7] = "CardonaP";
$arregloDatos[8] = "";
$arregloDatos[9] = "";
$arregloDatos[10] = "Cra 13 no 54 67 dd";
$arregloDatos[11] = "11";
$arregloDatos[12] = "1";
$arregloDatos[13] = "jlosada@correlibre.org";
$arregloDatos[14] = "1";
$arregloDatos[15] = "79";
$arregloDatos[16] = "0";
$arregloDatos[17] = "15";
$arregloDatos[18] = "Probando la radicadion por ws";
$arregloDatos[19] = "2010-09-11";
$arregloDatos[20] = "100";
$arregloDatos[21] = "GED";
$arregloDatos[22] = "S";
$arregloDatos[23] = "99999";
$a = $client->call('DNEradicarDocumento',$arregloDatos);
echo "Probando Salida";
var_dump($a);



?>



