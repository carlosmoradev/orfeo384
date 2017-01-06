<?php
/**********************************************************************************
Diseno de un Web Service que permita la interconexion de aplicaciones con Orfeo
**********************************************************************************/

/**
 * @author Donaldo Jinete Forero
 */

//Llamado a la clase nusoap

$ruta_raiz = "../";
define('RUTA_RAIZ','../');


require_once "nusoap/lib/nusoap.php";
include_once RUTA_RAIZ."include/db/ConnectionHandler.php";

//Asignacion del namespace  
$ns="webServices/nusoap";

//Creacion del objeto soap_server
$server = new soap_server();

$server->configureWSDL('Sistema de Gestion Documental Orfeo-JBPM',$ns);

$server->register('informacionRadicado',
	array(
	'objDoc'=> 'xsd:string'
	),
	array('return'=>'xsd:string'),
	$ns
);

function informacionRadicado($objDoc){
	global $ruta_raiz;
	
	if((empty($objDoc))||(strlen($objDoc)<14)){
		return "ERROR: Objeto documental vacio o incompleto : $objDoc : ".strlen($objDoc);
	}
	$db = new ConnectionHandler($ruta_raiz);


	$sql = "SELECT EESP_CODI FROM RADICADO WHERE RADI_NUME_RADI = '{$objDoc}'";
	$rs = $db->query($sql);
	if($rs->EOF){
		return "ERROR: No se encuentra $objDoc en la tabla RADICADO";	
	}
	$sql = "SELECT BE.NOMBRE_DE_LA_EMPRESA as nombre,BE.NIT_DE_LA_EMPRESA as nit,
	BE.SIGLA_DE_LA_EMPRESA as sigla ,BE.DIRECCION as direccion,
	BE.CODIGO_DEL_DEPARTAMENTO as departamento,DPT.DPTO_NOMB as n_departamento,
	BE.CODIGO_DEL_MUNICIPIO as municipio, MUN.MUNI_NOMB as n_municipio,
	BE.TELEFONO_1 as telefono_1, BE.TELEFONO_2 as telefono_2, BE.EMAIL as email, 
	BE.NOMBRE_REP_LEGAL as representante, BE.CARGO_REP_LEGAL as cargo,
	BE.IDENTIFICADOR_EMPRESA as identificador,
	BE.ARE_ESP_SECUE as secuencia,
	BE.ID_CONT as continente,
	BE.ID_PAIS as pais,
	BE.ACTIVA as activa,
	BE.FLAG_RUPS as rups
	FROM BODEGA_EMPRESAS BE,DEPARTAMENTO DPT,MUNICIPIO MUN
	WHERE BE.IDENTIFICADOR_EMPRESA='".$rs->fields['EESP_CODI']."'
	AND BE.CODIGO_DEL_DEPARTAMENTO=DPT.DPTO_CODI
	AND BE.CODIGO_DEL_MUNICIPIO=MUN.MUNI_CODI
	AND BE.CODIGO_DEL_DEPARTAMENTO=MUN.DPTO_CODI";
	
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	$rs = $db->query($sql);
	if($rs->EOF){
		return "ERROR: No se encuentra informacion de la empresa";
	}
	
	$cadena.="&lt;EMPRESA&gt;";
	foreach($rs->fields as $indice=>$valor){
		if(!empty($valor)){
			$cadena.="&lt;".$indice."&gt;"."$valor"."&lt;/".$indice."&gt;\n";
		}
	}
	$cadena.="&lt;/EMPRESA&gt;";
	return $cadena;
	
}

$HTTP_RAW_POST_DATA=isset($HTTP_RAW_POST_DATA)?$HTTP_RAW_POST_DATA:'';
$server->service($HTTP_RAW_POST_DATA);

?>