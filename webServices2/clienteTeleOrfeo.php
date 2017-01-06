<?php
/**
  * Diseno de un Web Service que permita la interconexion de aplicaciones con Orfeo
  */

/**
 * Servidor de direcciones de Orfeo's
 *
 * @author Orlando Burgos
 * 
 */

$ruta_raiz = "../";
define('RUTA_RAIZ','../');

require_once "nusoap/lib/nusoap.php";
include_once RUTA_RAIZ."include/db/ConnectionHandler.php";
require_once RUTA_RAIZ."fpdf/fpdf.php";

$ns="webServices/nusoap";

//Creacion del objeto soap_server
$server = new soap_server();

$server->configureWSDL('WebServices OrfeoGPL.org TeleOrfeo',$ns);

/*********************************************************************************
Se registran los servicios que se van a ofrecer, el metodo register tiene los sigientes parametros
**********************************************************************************/

//Servicio de transferir archivo

$server->register('getEntidades',
	array(),
	array('return'=>'tns:Matriz'),
	$ns
);

/******************************************************************************
 Funciones para los Servicios
******************************************************************************/


/**
 * Directorio de Entidades de la COmunidad OrfeoGPL.org
 */

function getEntidades(){
	global $ruta_raiz;
	$db = new ConnectionHandler($ruta_raiz);
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	
	$sql = "select SGD_DENT_CODIGO, SGD_DENT_NOMBRE,SGD_DENT_DOC,SGD_DENT_WDSL
				from SGD_DENT_DIRENTIDADES";
	$rs = $db->getResult($sql);
	$i =0;
	while (!$rs->EOF){
			 $entOrfeoGPL[$i]['entCodigo'] = $rs->fields['SGD_DENT_CODIGO'];
			 $entOrfeoGPL[$i]['entNombre']  = $rs->fields['SGD_DENT_NOMBRE'];
			 $entOrfeoGPL[$i]['entDoc'] = $rs->fields['SGD_DENT_DOC'];
			 $entOrfeoGPL[$i]['entWDSL'] =  $rs->fields['SGD_DENT_WDSL'];
			 $i=$i+1;
			 $rs->MoveNext();
	}
	return $entOrfeoGPL;
}


$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);
?>
