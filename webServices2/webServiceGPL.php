<?php
/**********************************************************************************
Diseno de un Web Service que permita la interconexion de aplicaciones con Orfeo
**********************************************************************************/

//Llamado a la clase nusoap
require_once("nusoap/lib/nusoap.php");

//Asignacion del namespace  
//http://wiki.superservicios.gov.co:81/~wduarte/br3.6.0/
$ns="webServices/noap";

//Creacion del objeto soap_server
$server = new soap_server();


$server->configureWSDL('Web Service OrfeoGPl.org ',$ns);


//Servicio que entrega todos los usuarios de Orfeo
$server->register('radicarDocumento',
	array(),
	array('return'=>'tns:Vector'),
	$ns
);

/*********************************************************************************
Se registran los servicios que se van a ofrecer, el metodo register tiene los sigientes parametros
**********************************************************************************/

//Servicio de transferir archivo
$server->register('UploadFile',  									 //nombre del servicio                 
    array('bytes' => 'xsd:base64binary', 'filename' => 'xsd:string'),//entradas        
    array('return' => 'xsd:string'),   								 // salidas
    $ns,                         									 //Elemento namespace para el metodo
    $ns . '#UploadFile',   											 //Soapaction para el metodo	
    'rpc',                 											 //Estilo
  	'encoded',             
    'Upload a File'        
);

//Servicio para crear un expediente
$server->register('crearExpediente',  								//nombre del servicio                 
    array('nurad' => 'xsd:string',									//numero de radicado	
     'usuario' => 'xsd:string',										//usuario que genero la radicacion
     'anoExp' => 'xsd:string',										//ano del expediente
     'fechaExp' => 'xsd:string',									//fecha expediente
     'codiSRD'=>'xsd:string',										//Serie del Expediendte
     'codiSBRD'=>'xsd:string',										//Subserie del expediente
     'codiProc'=>'xsd:string',										//Codigo del proceso
     'digCheck'=>'xsd:string',
     'tmr'=>'xsd:string',										//digCheck	
     ),																//entradas        	
    array('return' => 'xsd:string'),   								// salidas
    $ns                     									 	//Elemento namespace para el metod       
);


//Servicio para crear un expediente
$server->register('crearRadicado',  			//nombre del servicio  
	array('nombreRemitente' => 'xsd:string',	//usuario que genero la radicacion
	'direccionRemitente' => 'xsd:string',		//ano del expediente
	'asunto' => 'xsd:string',						//fecha expediente
	'referenciaDoc'=>'xsd:string',				//Serie del Expediendte
	'telefono'=>'xsd:string',						//Subserie del expediente
	'mail'=>'xsd:string'								//Codigo del proceso
	),														//entradas
	array('return' => 'xsd:string'),   			// salidas
	$ns 							 	//Elemento namespace para el metod 
);

//Servicio que entrega todos los usuarios de Orfeo
$server->register('darUsuario',
	array(),
	array('return'=>'tns:Matriz'),
	$ns
);

//Servicio que entrega un usuario especifico de Orfeo
$server->register('darUsuarioSelect',
	array(
	'usuaEmail'=> 'xsd:string',
	'usuaDoc' => 'xsd:string'
	),
	array('return'=>'tns:Vector'),
	$ns
);


/**********************************************************************************
Se registran los tipos complejos y/o estructuras de datos
***********************************************************************************/


//Adicionando un tipo complejo MATRIZ
 
$server->wsdl->addComplexType(
    'Matriz',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
	array(),
    array(
    array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:Vector[]')
    ),
    'tns:Vector'
);

//Adicionando un tipo complejo VECTOR

$server->wsdl->addComplexType(
    'Vector',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
	array(),
    array(
    array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'xsd:string[]')
    ),
    'xsd:string'
);




/******************************************************************************
 Servicios  que se ofrecen
******************************************************************************/


/**
 * Esta funcion pretende almacenar todos los usuarios de orfeo, con la informacion
 * de correo, cedula, dependencia y codigo del usuario
 * @author German A. Mahecha
 * @return Matriz con todos los usuarios de Orfeo
 */
function darUsuario(){
	$ruta_raiz = "..";
	include_once("$ruta_raiz/include/db/ConnectionHandler.php");
	$db = new ConnectionHandler("$ruta_raiz");
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	
	$sql = "select DEPE_CODI, USUA_CODI, USUA_DOC, USUA_EMAIL  from usuario";
	
	$rs = $db->getResult($sql);
	$i =0;
	while (!$rs->EOF){
			 $usuario[$i]['email'] = $rs->fields['USUA_EMAIL'];
			 $usuario[$i]['codusuario']  = $rs->fields['USUA_CODI'];
			 $usuario[$i]['dependencia'] = $rs->fields['DEPE_CODI'];
			 $usuario[$i]['documento'] =  $rs->fields['USUA_DOC'];
			 $i=$i+1;
			 $rs->MoveNext();
	}
	return $usuario;
}

/**
 * Nos retorna un vector con la informacion de un usuario en particular de Orfeo
 * @param $usuaEmail, correo electronico que tiene en LDAP
 * @param $usuaDoc,   cedula o documento de un usuario
 * @author German A. Mahecha
 * @return 0, si no encuentra el usuario. 
 */
function darUsuarioSelect ($usuaEmail='',$usuaDoc=''){
	$ruta_raiz = "..";
	include_once("$ruta_raiz/include/db/ConnectionHandler.php");
	$db = new ConnectionHandler("$ruta_raiz");
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	
	if ($usuaEmail != ''){
		$sql = "select DEPE_CODI, USUA_CODI, USUA_DOC, USUA_EMAIL  from usuario where UPPER(USUA_EMAIL) = UPPER('$usuaEmail')";
	}elseif ($usuaDoc !=''){
		$sql = "select DEPE_CODI, USUA_CODI, USUA_DOC, USUA_EMAIL  from usuario where USUA_DOC = $usuaDoc";
	}else {
		return $usuario;
	}
	
	$rs = $db->getResult($sql);
	while (!$rs->EOF){
			 $usuario['email'] = $rs->fields['USUA_EMAIL'];
			 $usuario['codusuario']  = $rs->fields['USUA_CODI'];
			 $usuario['dependencia'] = $rs->fields['DEPE_CODI'];
			 $usuario['documento'] =  $rs->fields['USUA_DOC'];
			 $rs->MoveNext();
	}
	return $usuario;
}


/**
 * UploadFile es una funcion que permite almacenar cualquier tipo de archivo en el lado del servidor
 * @param $bytes 
 * @param $filename es el nombre del archivo con que queremos almacenar en el servidor
 * @author German A. Mahecha
 * @return Retorna un String indicando si la operacion fue satisfactoria o no
 */
function UploadFile($bytes, $filename){
	$var = explode(".",$filename);
	//try{
		//direccion donde se quiere guardar los archivos
		$path = getPath($filename);
		$fp = fopen("$path", "w") or die("fallo");
		// decodificamos el archivo 
		$bytes=base64_decode($bytes);
		$salida=true;
		if( is_array($bytes) ){
			foreach($bytes as $k => $v){
				$salida=($salida && fwrite($fp,$bytes));
			}
		}else{
			$salida=fwrite($fp,$bytes);
		}
		fclose($fp);
	/*}catch (Exception $e){
		return "error";  
	}*/
	if ($salida){
	return "exito";
	}else{
	return "error";	
	}

}
/**
 * Esta funcion permite obtener el path donde se debe almacenar el archivo
 * @param $filename, el nombre del archivo 
 * @author German A. Mahecha
 * @return Retorna el path
 */
function getPath($filename){
	$var = explode(".",$filename);
	$path = "../bodega/";
	$path .= substr($var[0],0,4);
	$path .= "/".substr($var[0],4,$digitosDependencia);
	$path .= "/docs/".$filename;
	return  $path;
}

/**
 * Esta funcion permite crear un expediente a partir de un radicado
 * @param $nurad, este parametro es el numero de radicado
 * @param $usuario, este parametro es el usuario que crea el expediente, es el usuario de correo
 * @author German A. Mahecha
 * @return El numero de expediente para asignarlo en aplicativo de contribuciones AI 
 */
function crearExpediente($nurad,$usuario,$anoExp,$fechaExp,$codiSRD,$codiSBRD,$codiProc,$digCheck,$tmr){
		
	$ruta_raiz = "..";
	include_once("$ruta_raiz/include/db/ConnectionHandler.php");
	include_once("$ruta_raiz/include/tx/Expediente.php");
	$db = new ConnectionHandler("$ruta_raiz");
	$expediente = new Expediente($db);
	
	//Aqui busco la informacion necesaria del usuario para la creacion de expedientes
	$sql= "select USUA_CODI,DEPE_CODI,USUA_DOC from usuario where upper(usua_email) = upper ('".$usuario."@superservicios.gov.co')";
	$rs = $db->conn->query($sql);
	while (!$rs->EOF){
		$codusuario  = $rs->fields['USUA_CODI'];
		$dependencia = $rs->fields['DEPE_CODI'];
		$usua_doc =  $rs->fields['USUA_DOC'];
		$usuaDocExp = $usua_doc; 
		$rs->MoveNext();
	} 
	
	//Insercion para el TMR
    $sql =	"insert into sgd_rdf_retdocf (sgd_mrd_codigo,radi_nume_radi,depe_codi,usua_codi,usua_doc,sgd_rdf_fech)";
    $sql .= " values ($tmr,$nurad,$dependencia,$codusuario,'$usua_doc',SYSDATE)";
    
    $db->conn->query($sql);
   
		
	$trdExp = substr("00".$codiSRD,-2) . substr("00".$codiSBRD,-2);
	$secExp = $expediente->secExpediente($dependencia,$codiSRD,$codiSBRD,$anoExp);
	$consecutivoExp = substr("00000".$secExp,-5);
	$numeroExpediente = $anoExp . $dependencia . $trdExp . $consecutivoExp . $digCheck;
	 
	$numeroExpedienteE = $expediente->crearExpediente( $numeroExpediente,$nurad,$dependencia,$codusuario,$usua_doc,$usuaDocExp,$codiSRD,$codiSBRD,'false',$fechaExp,$codiProc);
	
	$insercionExp = $expediente->insertar_expediente( $numeroExpediente,$nurad,$dependencia,$codusuario,$usua_doc);	

	return $numeroExpedienteE;
}


/**
 * Esta funcion permite crear un expediente a partir de un radicado
 * @param $nurad, este parametro es el numero de radicado
 * @param $usuario, este parametro es el usuario que crea el expediente, es el usuario de correo
 * @author Orlando Burgos
 * @return El numero de expediente para asignarlo en aplicativo de contribuciones AI 
 */

// function crearRadicado($nombreRemitente,$direccionRemitente,$asunto,$referenciaDoc,$telefono,$mail,$radicadoPadre=null){

function crearRadicado(){
	$ruta_raiz = "..";
	
	include_once("$ruta_raiz/include/db/ConnectionHandler.php");
	
	include_once("$ruta_raiz/include/tx/Radicacion.php");
	$dbGPL = new ConnectionHandler("$ruta_raiz");
	
		
	$rad = new Radicacion($dbGPL);

	//Aqui busco la informacion necesaria del usuario para la creacion de expedientes
	$sql= "select * from SGD_OEM_OEMPRESAS ";
	$rs=$dbGPL->conn->query($sql);
	
	
	while (!$rs->EOF){
		$oemCodigo  = $rs->fields['SGD_OEM_CODIGO'];
		$oemNombre = $rs->fields['SGD_OEM_OEMPRESA'];
		$oemSigla = $rs->fields['SGD_OEM_SIGLA'];
		$oemNit =  $rs->fields['SGD_OEM_NIT'];
		$rs->MoveNext();
	} 
	
	
	$rad->radiCuentai = "'".trim($referenciaDoc)."'";
	$rad->mrecCodi =  3; // "dd/mm/aaaa"
	$rad->radiPais =  "170";
	$rad->raAsun = "v".$asunto;
	$rad->radiDepeActu = "'529'";
	$rad->radiDepeRadi = "'529'";
	$rad->radiUsuaActu = "1" ;
	$rad->carpCodi = 1;
	$rad->carPer = 0;
	$rad->radiPath = 'null';
	$rad->tdocCodi=1;
	$rad->tdidCodi=1;
	$rad->dependencia = "529";
	$rad->radiDepeActu = "'529'";
	$rad->radiDepeRadi = "'529'";
	$rad->radiUsuaActu = "1" ;
	$codTx = 2;
	$flag = 1;
	$ent = 2;
	$noRad = $rad->newRadicado($ent, "900");
	if(!$ruta_raiz){
	}
	return "111" .$noRad;
}


$server->service($HTTP_RAW_POST_DATA);

?>
