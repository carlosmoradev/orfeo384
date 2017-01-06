<?php
/**********************************************************************************
Diseno de un Web Service que permita la interconexion de aplicaciones con Orfeo
**********************************************************************************/

/**
 * @author German Mahecha
 * @author William Duarte (modificacion del archivo original y adicion de funcionalidad)
 * @author Donaldo Jinete Forero
 */

//Llamado a la clase nusoap

$ruta_raiz = "../";
define('RUTA_RAIZ','../');

require_once "nusoap/lib/nusoap.php";
include_once RUTA_RAIZ."include/db/ConnectionHandler.php";
//require_once RUTA_RAIZ."flujo/vistaFlujo.php";
//require_once RUTA_RAIZ."flujo/variables/flujo.php";
require_once RUTA_RAIZ."fpdf/fpdf.php";

//Asignacion del namespace  
$ns="webServices/nusoap";

//Creacion del objeto soap_server
$server = new soap_server();

$server->configureWSDL('Sistema de Gestion Documental Orfeo-Internas',$ns);

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
$server->register('getUsuarioCorreo',
	array(
	'correo'=> 'xsd:string'
	),
	array('return'=>'tns:Vector'),
	$ns
);
$server->register('crearAnexo',  								//nombre del servicio                 
    array('radiNume' => 'xsd:string',									//numero de radicado	
     'file' => 'xsd:base64binary',										//archivo en base 64
     'filename' => 'xsd:string',										//nombre original del archivo
     'correo' => 'xsd:string',									       //correo electronico
     'descripcion'=>'xsd:string',										//descripcion del anexo
     ),																//fin parametros del servicio        	
    array('return' => 'xsd:string'),   								//retorno del servicio
    $ns                     									 	//Elemento namespace para el metod       
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

$server->register('solicitarAnulacion',
	array(
		'radiNume'=>'xsd:string',
		'descripcion'=>'xsd:string'
	),
	array(
		'return'=>'tns:string'
	),
	$ns
);

// Servicio que realiza una radicacion en Orfeo
$server->register('radicarDocumento',
	array(
		'file' => 'xsd:base64binary',										//archivo en base 64
		'fileName' => 'xsd:string',
		'correo' => 'xsd:string',
		'destinatarioOrg'=>'tns:Vector',
		'predioOrg'=>'tns:Vector',
		'espOrg'=>'tns:Vector',
		'asu'=>'xsd:string',
		'med'=>'xsd:string',
		'ane'=>'xsd:string',
		'coddepe'=>'xsd:string',
		'tpRadicado'=>'xsd:string',
		'cuentai'=>'xsd:string',
		'radi_usua_actu'=>'xsd:string',
		'tip_rem'=>'xsd:string',
		'tdoc'=>'xsd:string',
		'tip_doc'=>'xsd:string',
		'carp_codi'=>'xsd:string',
		'carp_per'=>'xsd:string'
	),
	array(
		'return' => 'xsd:string'
	),
	$ns,
	$ns."#radicarDocumento",
	'rpc',
	'encoded',
	'Radicacion de un documento en Orfeo'
);

$server->register('DNEradicarDocumento',
	array(
		'Destino' => 'xsd:string',
		'Observaciones' => 'xsd:string',
		'TipoPersona' => 'xsd:string',
		'TipoDocumento' => 'xsd:string',
		'NumeroDocumento' => 'xsd:string',
		'NombrePersonaNatural' => 'xsd:string',
		'Apellido1PersonaNatural' => 'xsd:string',
		'Apellido2PersonaNatural' => 'xsd:string',
		'NombrePersonaJuridica' => 'xsd:string',
		'NombreEstablecimiento' => 'xsd:string',
		'Direccion ' => 'xsd:string',
		'CodigoDepartamento' => 'xsd:string',
		'CodigoCiudad' => 'xsd:string',
		'CorreoElectronico' => 'xsd:string',
		'MedioEnvio' => 'xsd:string',
		'IdentificadorTramite' => 'xsd:string',
		'TipoTramite' => 'xsd:string',
		'TiempoTespuesta' => 'xsd:string',
		'Asunto' => 'xsd:string',
		'FechaDocumento' => 'xsd:string',
		'NumeroFolios' => 'xsd:string',
		'Radicador' => 'xsd:string',
		'TipoRadicacion' => 'xsd:string',
		'IdDocumentoRelacionado' => 'xsd:string',
	),
	array(
		'return' => 'xsd:string'
	),
	$ns,
	$ns."#DNEradicarDocumento",
	'rpc',
	'encoded',
	'Radicacion de un documento en Orfeo'
);


//Servicio para anular radicacion de Orfeo
$server->register('anularRadicado',
	array(
		'checkValue'=>'tns:Vector',
		'dependencia'=>'xsd:string',
		'usua_doc'=>'xsd:string',
		'observa'=>'xsd:string',
		'codusuario'=>'xsd:string'
	),
	array(
		'return'=>'xsd:string'),
	$ns,
	$ns."#anularRadicado",
	'rpc',
	'encoded',
	'Anular radicacion de un documento en Orfeo'
);


$server->register('anexarExpediente',
	array(
		'numRadicado'=>'xsd:string',
		'numExpediente'=>'xsd:string',
		'usuaLogin'=>'xsd:string',
		'observa'=>'xsd:string'
	),
	array(
		'return'=>'xsd:string'
	),
	$ns,
	$ns."#anexarExpediente",
	'rpc',
	'encoded',
	'Anexar un radicado a un expediente'	
);

$server->register('cambiarImagenRad',
	array(
		'numRadicado'=>'xsd:string',
		'ext'=>'xsd:string',
		'file'=>'xsd:base64binary'
	),
	array(
		'return'=>'xsd:string'
	),
	$ns,
	$ns."#cambiarImagenRad",
	'rpc',
	'encoded',
	'Cambiar imagen a un radicado'
);

$server->register('cambiarImagenCorreo',
	array(
		'numRadicado'=>'xsd:string',
		'texto'=>'xsd:string'
	),
	array(
		'return'=>'xsd:string'
	),
	$ns,
	$ns."#cambiarImagenCorreo",
	'rpc',
	'encoded',
	'Cambiar imagen de un correo radicado'
	
);


$server->register('getInfoUsuario',
	array(
		'usuaLoginMail'=>'xsd:string'
	),
	array(
		'return'=>'tns:Vector'
	),
	$ns,
	$ns.'#getInfoUsuario',
	'rpc',
	'encoded',
	'Obtener informacion de un usuario a partir del correo electronico'
);

$server->register('asociarObjetoFlujo',
	array(
		'nuRad'=>'xsd:string',
		'usuaEmail'=>'xsd:string',
		'tflujo'=>'xsd:string'
	),
	array(
		'return'=>'xsd:string'
	),
	$ns,
	$ns.'#asociarObjetoFlujo',
	'rpc',
	'encoded',
	'Asociar un objeto a un flujo'
);
$server->register('cambioDeEtapaFlujo',
	array(
		'objDoc'=>'xsd:string',
		'flujo'=>'xsd:string',
		'etapa'=>'xsd:string'
	),
	array(
		'return'=>'xsd:string'
	),
	$ns,
	$ns.'#cambioDeEtapaFlujo',
	'rpc',
	'encoded',
	'Cambio de etapa en un flujo'
);
$server->register('informacionJefe',
	array(
		'usuaEmail'=>'xsd:string'
	),
	array(
		'return'=>'tns:Vector'
	),
	$ns,
	$ns.'#informacionJefe',
	'rpc',
	'encoded',
	'Informacion del Jefe de un usuario'
);
$server->register('radicarCorreo',
	array(
		'idEmpresa'=>'xsd:string',
		'correo'=>'xsd:string',
		'asu'=>'xsd:string',
		'usuario'=>'xsd:string'
	),
	array(
		'return'=>'xsd:string'
	),
	$ns,
	$ns.'#radicarCorreo',
	'rpc',
	'encoded',
	'Radicacion de tercer correo'
);

$server->register('asociarExpCorreo',
	array(
		'idEmpresa'=>'xsd:string',
		'tipoServ'=>'xsd:string',
		'tamPres'=>'xsd:string',
		'radicado'=>'xsd:string'
	),
	array(
		'return'=>'xsd:string'
	),
	$ns,
	$ns.'#asociarExpCorreo',
	'rpc',
	'encoded',
	'Asociar a expediente tercer correo'
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
	global $ruta_raiz;
	$db = new ConnectionHandler($ruta_raiz);
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
	global $ruta_raiz;
	$db = new ConnectionHandler($ruta_raiz);
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	
	if ($usuaEmail != ''){
		$sql = "select DEPE_CODI, USUA_CODI, USUA_DOC, USUA_EMAIL  from usuario where UPPER(USUA_EMAIL) = UPPER('$usuaEmail')";
	}elseif ($usuaDoc !=''){
		$sql = "select DEPE_CODI, USUA_CODI, USUA_DOC, USUA_EMAIL  from usuario where USUA_DOC = $usuaDoc";
	}else {
		return "Favor proveer datos";
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
    //$var = explode(".",$filename);
	//try{
		//direccion donde se quiere guardar los archivos
		$path = getPath($filename);
		if(!$fp = fopen("$path", "w")){
			die("fallo");
		}
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
	$path = RUTA_RAIZ."bodega/";
	$path .= substr($var[0],0,4);
	$path .= "/".substr($var[0],4,3);
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
		
	include_once(RUTA_RAIZ."include/tx/Expediente.php");
	global $ruta_raiz;
	$db = new ConnectionHandler($ruta_raiz);
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
 * funcion que rescata los valores de un usuario de orfeo 
 * a partir del correo electonico
 *
 * @param string $correo mail del usuario en orfeo
 * @return array resultado de la consulta;
 */
function getUsuarioCorreo($correo){
	global $ruta_raiz;
	$consulta="SELECT USUA_LOGIN,DEPE_CODI,USUA_EMAIL,CODI_NIVEL,USUA_CODI,USUA_DOC
	           FROM USUARIO WHERE USUA_EMAIL='$correo' AND USUA_ESTA=1";
	$salida=array();
	if(verificarCorreo($correo)){
	$consulta="SELECT USUA_LOGIN,DEPE_CODI,USUA_EMAIL,CODI_NIVEL,USUA_CODI,USUA_DOC
	           FROM USUARIO WHERE USUA_EMAIL='".trim($correo)."' AND USUA_ESTA=1";
	 $db = new ConnectionHandler($ruta_raiz);
	 $rs = $db->query($consulta);
	 
	 if (!$rs->EOF){
		 $salida['email'] = $rs->fields['USUA_EMAIL'];
		 $salida['codusuario']  = $rs->fields['USUA_CODI'];
		 $salida['dependencia'] = $rs->fields['DEPE_CODI'];
		 $salida['documento'] =  $rs->fields['USUA_DOC'];
		 $salida['nivel'] = $rs->fields['CODI_NIVEL'];
		 $salida['login'] = $rs->fields['USUA_LOGIN'];
	   } else {
	   	$salida['error']="El ususario no existe o se encuentra deshabilitado";
	   }
	}else{
		$salida["error"]="el mail no corresponde a un email valido";
	}
	
	return $salida;
}
/**
 * funcion que verifica que un correo electronico cumpla con 
 * un patron estandar
 *
 * @param strig $correo correo a verificar
 * @return boolean
 */
function verificarCorreo($correo){
	 $expresion=preg_match("(^\w+([\.-] ?\w+)*@\w+([\.-]?\w+)*(\.\w+)+)",$correo);
	 return $expresion;
}
/**
 * funcion encargada regenerar un archivo enviado en base64
 *
 * @param string $ruta ruta donde se almacenara el archivo 
 * @param base64 $archivo archivo codificado en base64
 * @param string $nombre nombre del archivo
 * @return boolean retorna si se pudo decodificar el archivo
 */
function subirArchivo($ruta,$archivo,$nombre){
		//try{
		//direccion donde se quiere guardar los archivos
		$fp = @fopen("{$ruta}{$nombre}", "w");
		$bytes=base64_decode($archivo);

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
	return $salida;		
}
/**
 * funcion que crea un Anexo, y ademas decodifica el anexo enviasdo en base 64
 *
 * @param string $radiNume numero del radicado al cual se adiciona el anexo
 * @param base64 $file archivo codificado en base64
 * @param string $filename nombre original del anexo, con extension
 * @param string $correo correo electronico del usuario que adiciona el anexo
 * @param string $descripcion descripcion del anexo
 * @return string mensaje de error en caso de fallo o el numero del anexo en caso de exito
 */
function crearAnexo($radiNume,$file,$filename,$correo,$descripcion){
	global $ruta_raiz;
	$db = new ConnectionHandler($ruta_raiz);
	$usuario=getUsuarioCorreo($correo);
	$error=(isset($usuario['error']))?true:false;
	$ruta=RUTA_RAIZ."bodega/".substr($radiNume,0,4)."/".substr($radiNume,4,3)."/docs/";
	$numAnexos=numeroAnexos($radiNume,$db)+1;
	$maxAnexos=maxRadicados($radiNume,$db)+1;
	$extension=substr($filename,strrpos($filename,".")+1);	
	$numAnexo=($numAnexos > $maxAnexos)?$numAnexos:$maxAnexos;
	$nombreAnexo=$radiNume.substr("00000".$numAnexo,-5);
	$subirArchivo=subirArchivo($ruta,$file,$nombreAnexo.".".$extension);
	$tamanoAnexo = $subirArchivo / 1024; //tamano en kilobytes
	$error=($error && !$subirArchivo)?true:false;
	$fechaAnexado= $db->conn->OffsetDate(0,$db->conn->sysTimeStamp);
	$tipoAnexo=tipoAnexo($extension,$db);
	if(!$error){
		$tipoAnexo=($tipoAnexo)?$tipoAnexo:"NULL";
		$consulta="INSERT INTO ANEXOS (ANEX_CODIGO,ANEX_RADI_NUME,ANEX_TIPO,ANEX_TAMANO,ANEX_SOLO_LECT,ANEX_CREADOR,
		            ANEX_DESC,ANEX_NUMERO,ANEX_NOMB_ARCHIVO,ANEX_ESTADO,SGD_REM_DESTINO,ANEX_FECH_ANEX, ANEX_BORRADO) 
		            VALUES('$nombreAnexo',$radiNume,$tipoAnexo,$tamanoAnexo,'n','".$usuario['login']."','$descripcion'
		            ,$numAnexo,'$nombreAnexo.$extension',0,1,$fechaAnexado, 'N')";

		
		$error=$db->query($consulta);
		
		$consultaVerificacion = "SELECT ANEX_CODIGO FROM ANEXOS WHERE ANEX_CODIGO = '$nombreAnexo'";
		$rs=$db->query($consultaVerificacion);
		$cod = $rs->fields['ANEX_CODIGO'];
	}
	return $cod ? 'Anexo Creado'.$nombreAnexo : 'Error en la adicion verifique: ' . $nombreAnexo;
}
/**
 * funcion que calculcula el numero de anexos que tiene un radicado
 *
 * @param int  $radiNume radicado al cual se realiza se adiciona el anexo
 * @param ConectionHandler $db
 * @return int numero de anexos del radicado
 */
function numeroAnexos($radiNume,$db){
	$consulta="SELECT COUNT(1) AS NUM_ANEX FROM ANEXOS WHERE ANEX_RADI_NUME={$radiNume}";
	$salida=0;	
	$rs=& $db->query($consulta);
		if($rs && !$rs->EOF)
			$salida=$rs->fields['NUM_ANEX'];
		return  $salida;	
}
/**
 * funcioncion que rescata el maxido del anexo de los radicados 
 *
 * @param int $radiNume numero del radicado
 * @param ConnectionHandler $db conexion con la db
 * @return int maximo
 */
function maxRadicados($radiNume,$db){
	$consulta="SELECT max(ANEX_NUMERO) AS NUM_ANEX FROM ANEXOS WHERE ANEX_RADI_NUME={$radiNume}";
		$rs=& $db->query($consulta);
		if($rs && !$rs->EOF)
			$salida=$rs->fields['NUM_ANEX'];
		return  $salida;	
}
/**
 * funcion que consulta el tipo de anexo que se esta generando
 * 
 *
 * @param sting $extension extencion del archivo
 * @param ConnectionHandler $db conexion con la DB
 * @return int
 */
function tipoAnexo($extension,$db){
	$consulta="SELECT ANEX_TIPO_CODI FROM ANEXOS_TIPO WHERE ANEX_TIPO_EXT='".strtolower($extension)."'";
	$salida=null;
	$rs=& $db->query($consulta);
		if($rs && !$rs->EOF)
			$salida=$rs->fields['ANEX_TIPO_CODI'];
	return $salida;		
}
/**
 * funcion que genera la solicitud de anulacion de un numero de radicado
 * de forma automatica
 *
 * @param string $radiNume numero de radicado
 * @param string $descripcion causa por la cula se solicita la anulacion
 * @return string en caso de fallo retorna error 
 */
function solicitarAnulacion( $radiNume, $descripcion, $correo ){
	global $ruta_raiz;
	$db = new ConnectionHandler($ruta_raiz);
	//Se traen los datos del usuario que solicita anulacion
	$usuario=getUsuarioCorreo( $correo );
	
	$verificacionSolicitud = verificaSolAnulacion( $radiNume , $usuario['login'] );
	if( $verificacionSolicitud ){
		$actualizaRadAnulado = "UPDATE radicado SET SGD_EANU_CODIGO=1 WHERE radi_nume_radi = $radiNume";
		$rs=$db->query( $actualizaRadAnulado );
		
		$insertaEnAnulados = "insert into sgd_anu_anulados (RADI_NUME_RADI, SGD_EANU_CODI, SGD_ANU_SOL_FECH, 
							  DEPE_CODI , USUA_DOC, SGD_ANU_DESC , USUA_CODI) values ( $radiNume , 1 , 
							  (SYSDATE+0) , " . $usuario[ 'dependencia' ] . ", " . $usuario[ 'documento' ] . " , 
							  'Solicitud Anulacion.pruebas webservice orfeo', ) " . $usuario[ 'codusuario' ] ;
		$rs=$db->query( $insertaEnAnulados );
		
		//Consulta de insercion historico para la anulacion
		//22418400 = Documento sra Superintendente EvaMaria U
		$insertaHistorico = "insert into HIST_EVENTOS(RADI_NUME_RADI,DEPE_CODI,USUA_CODI,USUA_CODI_DEST,
							 DEPE_CODI_DEST,USUA_DOC,HIST_DOC_DEST,SGD_TTR_CODIGO,HIST_OBSE,HIST_FECH) 
							 values ( $radiNume , " . $usuario[ 'dependencia' ] . ", " . $usuario[ 'codusuario' ] . 
							 " , 1 , 100 , " . $usuario[ 'documento' ] . " , 22418400, 25 ,
							 'Anulacion de Radicado desde Webservice',(SYSDATE+0))"; 
							 
		
		
		return "Exito Solicitando Anulacion";
	}else {
		return "Error Solicitando Anulacion";
	}
}

function verificaSolAnulacion ( $radiNume, $usuaLogin ){
	
	$consultaPermiso = "SELECT SGD_PANU_CODI FROM USUARIO WHERE USUA_LOGIN = '$usuaLogin";
	$rs=$db->query( $consultaPermiso );
	$permisoAnulacion = $rs->fields[ 'SGD_PANU_CODI' ];
	
	if ( $permisoAnulacion == 0) {
		return false;
	}
	
    $consultaYaAnulado =	"SELECT r.RADI_NUME_RADI FROM radicado r, SGD_TPR_TPDCUMENTO c where r.radi_nume_radi is not null 
    and substr(r.radi_nume_radi, 5, 3)=905 and substr(r.radi_nume_radi, 14, 1) not in ( 2 ) 
    and r.tdoc_codi=c.sgd_tpr_codigo and r.sgd_eanu_codigo is null and 
    ( r.SGD_EANU_CODIGO = 9 or r.SGD_EANU_CODIGO = 2 or r.SGD_EANU_CODIGO IS NULL )";  
    
    /*
    $consultaYaAnulado2 = 'SELECT  to_char(b.RADI_NUME_RADI) 
    "IMG_Numero Radicado" , b.RADI_PATH "HID_RADI_PATH" , to_char(b.RADI_NUME_DERI) "Radicado Padre" , 
    b.RADI_FECH_RADI "HOR_RAD_FECH_RADI" , b.RADI_FECH_RADI "Fecha Radicado" , b.RA_ASUN "Descripcion" , 
    c.SGD_TPR_DESCRIP "Tipo Documento" , b.RADI_NUME_RADI "CHK_CHKANULAR" from radicado b, SGD_TPR_TPDCUMENTO c 
    where b.radi_nume_radi is not null and substr(b.radi_nume_radi, 5, 3)=905 and 
    substr(b.radi_nume_radi, 14, 1) in (1, 3, 5, 6) and b.tdoc_codi=c.sgd_tpr_codigo and 
    sgd_eanu_codigo is null and  ( b.SGD_EANU_CODIGO = 9 or b.SGD_EANU_CODIGO = 2 or b.SGD_EANU_CODIGO IS NULL ) 
    order by 4 ';*/
    
	$rs=$db->query($consultaYaAnulado);
	$numRadicado = $rs->fields['RADI_NUME_RADI'];
	if ( !$numRadicado ) {
		return  false;
	}
	return true;
}

/**
 * Esta funcion permite radicar un documento en Orfeo - Version adaptada a DNE
 * @param $Destino  , -10404:Sustancias – Control Nacional
		      -12875:Sustancias Control Especial
		      -10403:Aerocivil
		      -10402:DIMAR
 * @param $Observaciones
 * @param $TipoPersona      , Natural o Juridica
 * @param $TipoDocumento    , CC, TI, RE, CX, PA .....
 * @param $NumeroDocumento   
 * @param $NombreNatural
 * @param $ApellidoPersonaNatural
 * @param $ApellidoPersonaNatural
 * @param $NombrePersonaJuridica
 * @param $NombreEstablecimiento
 * @param $Direccion 
 * @param $CódigoDepartamento
 * @param $CódigoCiudad
 * @param $CorreoElectronico
 * @param $MedioEnvio, Medio de Envio. Para este Ws solo se envia 6
 * @param $IdentificadorTrámite
 * @param $TipoTrámite
 * @param $TiempoTespuesta
 * @param $Asunto          ,Asunto del Doc
 * @param $FechaDocumento  ,Fecha del DOcumento
 * @param $NúmeroFolios    ,Numero de Hojas del Documetnos
 * @param $Radicador       ,Solo GED
 * @param $TipoRadicacion  ,E- Entrada, S Salida
 * @param $IdDocumentoRelacionado
 */


function DNEradicarDocumento($Destino
,$Observaciones
,$TipoPersona
,$TipoDocumento
,$NumeroDocumento
,$NombrePersonaNatural
,$Apellido1PersonaNatural
,$Apellido2PersonaNatural
,$NombrePersonaJuridica
,$NombreEstablecimiento
,$Direccion 
,$CodigoDepartamento
,$CodigoCiudad
,$CorreoElectronico
,$MedioEnvio
,$IdentificadorTramite
,$TipoTramite
,$TiempoTespuesta
,$Asunto
,$FechaDocumento
,$NumeroFolios
,$Radicador
,$TipoRadicacion
,$IdDocumentoRelacionado){

	//Conversiones de datos para compatibilidad con aplicaciones internas
	$radi_usua_actu = 1;
	$coddepe = 900;
	//return ("$TipoRadicacion   $Asunto");
	
	if($TipoRadicacion=="E") { $tpRadicado=2; }
	if($TipoRadicacion=="S") { $tpRadicado=1; }else{ $tpRadicado=2;}

	if($TipoPersona=="N") { $tip_rem=0; $tipo_emp_us1=0;}
	if($TipoPersona=="J") { $tip_rem=2; $tipo_emp_us1=2;} else { $tip_rem=0; $tipo_emp_us1=0;}

	
	
	if($TipoDocumento=="CC") { $tdoc=0; }
	if($TipoDocumento=="TI") { $tdoc=1; }
	if($TipoDocumento=="CE") { $tdoc=2; }
	if($TipoDocumento=="PA") { $tdoc=3; }
	if($TipoDocumento=="RC") { $tdoc=12; }
	if($TipoDocumento=="CE") { $tdoc=7; }
	if($TipoDocumento=="NI") { $tdoc=4; }
	if($TipoDocumento=="PJ") { $tdoc=8; }
	if($TipoDocumento=="EO") { $tdoc=9; }
	if($TipoDocumento=="RM") { $tdoc=10; }
	// Fin
	
	global $ruta_raiz;
	
	include(RUTA_RAIZ."include/tx/Tx.php") ;
	include(RUTA_RAIZ."include/tx/Radicacion.php") ;
	include(RUTA_RAIZ."class_control/Municipio.php") ;

	
	$db = new ConnectionHandler($ruta_raiz) ;
	$hist = new Historico($db) ;
	$tmp_mun = new Municipio($db) ;
	$rad = new Radicacion($db) ;

	$documento_us1 = $tip_rem;
	$cc_documento_us1 = $NumeroDocumento;
	$direccion_us1 = $Direccion;
	$mail_us1 = $CorreoElectronico;
	
	//$tmp_mun->municipio_codigo($destinatario["codep"],$destinatario["muni"]) ;
	$rad->radiTipoDeri = $TipoTramite ;
	$rad->radiCuentai = "'".trim($Destino)."'";
	$rad->eespCodi =  0 ;
	$rad->mrecCodi =  $MedioEnvio;
	$rad->radiFechOfic =  date("Y-m-d");
	if(!$radicadopadre)  $radicadopadre = null;
	$rad->radiNumeDeri = trim($radicadopadre) ;
	$rad->radiPais = 170 ;
	$rad->descAnex = "Folios. ".$NumeroFolios ;
	$rad->raAsun = $Asunto ;
	if($Observaciones) $rad->raAsun .= "<b>Observaciones</b>:". $Observaciones;
	$rad->radiDepeActu = $coddepe ;
	$rad->radiDepeRadi = $coddepe ;
	$rad->radiUsuaActu = $radi_usua_actu ;
	$rad->trteCodi =  $tip_rem;
	$rad->tdocCodi=$tdoc;
	$rad->tdidCodi=0;
	$rad->carpCodi = 0 ;
	$rad->carPer = 0 ;
	$rad->trteCodi=$tip_rem ;
	$rad->radiPath = 'null';
	$codTx = 2 ;
	$flag = 1 ;
	$rad->usuaCodi=$radi_usua_actu ;
	$rad->dependencia=trim($coddepe) ;
	// return "Entro..... al ws -> ($tpRadicado,$coddepe) $TipoRadicacion   <- ". $noRad;
	$noRad = $rad->newRadicado($tpRadicado,$coddepe) ;
//return "Entro..... al ws -> ($tpRadicado,$coddepe) $TipoRadicacion   <- ". $noRad;
	
	//echo "Numero Radicado ". $noRad;
	$nurad = trim($noRad) ;
	// $sql_ret = $rad->updateRadicado($nurad,"/".date("Y")."/".$coddepe."/".$noRad.".pdf");
	
	if ($noRad=="-1")
	{
		return "Error no genero un Numero de Secuencia o Inserto el radicado";		
	}
	$radicadosSel[0] = $noRad;
	
	$hist->insertarHistorico($radicadosSel,  $coddepe , $radi_usua_actu, $coddepe, $radi_usua_actu, " ", $codTx);
	
	$conexion = $db;
	
	/*
		Preparacion de variables para llamar el codigo del
		archivo grb_direcciones.php
	*/
	
	$tipo_emp_us1=trim($tip_rem);
	
	$muni_us1 = trim($CodigoCiudad);
	
	$codep_us1 = trim($coddepe);
	
	if($TipoPersona="N"){
	 $grbNombresUs1 = trim($NombrePersonaNatural) . " " . trim($Apellido1PersonaNatural) . " ". trim($Apellido2PersonaNatural);
	}else{
	 $grbNombresUs1 = trim($NombrePersonaJurídica) . " - " . trim($NombreEstablecimiento);
	}
	$cc_documento_us1 = trim($NumeroDocumento);
	
	$documento_us1 = $NumeroDocumento;
	
	$direccion_us1 = trim($Direccion);
	
	$telefono_us1 = trim(" ");
	
	$mail_us1 = trim($CorreoElectronico);
	
  
  
	//************** INSERTAR DIRECCIONES *******************************
	
	if (!$CodigoCiudad){ $muni_us1 = $CodigoCiudad; }else {$muni_us1 = 1;}
        if (!$CodigoDepartamento){ $codep_us1 = $CodigoCiudad; }else {$codep_us1 = 11;}

	
	// Creamos las valores del codigo del dpto y mcpio desglozando el valor del <SELECT> correspondiente.
	if (!is_null($muni_us1)){
		$tmp_mun = new Municipio($conexion);
		$tmp_mun->municipio_codigo($codep_us1,$muni_us1);
		$tmp_idcont = $tmp_mun->get_cont_codi();
		$tmp_idpais = $tmp_mun->get_pais_codi();
		$muni_tmp1 = explode("-",$muni_us1);
		switch (count($muni_tmp1))
		{	
			case 4:
			{
				$idcont1 = $muni_tmp1[0];
				$idpais1 = $muni_tmp1[1];
				$dpto_tmp1 = $muni_tmp1[2];
				$muni_tmp1 = $muni_tmp1[3];

			}
			break;
		case 3:
			{
				$idcont1 = $tmp_idcont;
				$idpais1 = $muni_tmp1[0];
				$dpto_tmp1 = $muni_tmp1[1];
				$muni_tmp1 = $muni_tmp1[2];
			}
			break;
		case 2:
			{
				$idcont1 = $tmp_idcont;
				$idpais1 = $tmp_idpais;
				$dpto_tmp1 = $muni_tmp1[0];
				$muni_tmp1 = $muni_tmp1[1];
			}
			break;
		}
		unset($tmp_mun);
		unset($tmp_idcont);
		unset($tmp_idpais);
	} 
	
	$newId = false;
	if(!$modificar)
	{
   		$nextval=$conexion->nextId("sec_dir_direcciones");
	}
	
	if ($nextval==-1)
	{
		return "No se encontro la secuencia sec_dir_direcciones ";
	}
	global $ADODB_COUNTRECS;
	//return "Llego aka ddd $nextval $documento_us1 $NumeroDocumento";
	if($documento_us1!='')
	{
	  
		$sgd_ciu_codigo=0;
		$sgd_oem_codigo=0;
		$sgd_esp_codigo=0;
		$sgd_fun_codigo=0;
  		if($tipo_emp_us1==0)
  		{	
  			$sgd_ciu_codigo=$documento_us1;
			$sgdTrd = "1";
		}
		if($tipo_emp_us1==1)
		{	
			$sgd_esp_codigo=$documento_us1;
			$sgdTrd = "3";
		}
		if($tipo_emp_us1==2)
		{	
			$sgd_oem_codigo=$documento_us1;
			$sgdTrd = "2";
		}
		if($tipo_emp_us1==6)
		{	
			$sgd_fun_codigo=$documento_us1;
			$sgdTrd = "4";
		}

		 
		 
		
		$ADODB_COUNTRECS = true;

		$record = array();
		$record['SGD_TRD_CODIGO'] = $sgdTrd;
		$record['SGD_DIR_NOMREMDES'] = $grbNombresUs1;
		$record['SGD_DIR_DOC'] = $cc_documento_us1;
		$record['MUNI_CODI'] = $CodigoCiudad;
		$record['DPTO_CODI'] = $CodigoDepartamento;
		$record['ID_PAIS'] = "170";
		$record['ID_CONT'] = "1";
		$record['SGD_DOC_FUN'] = $sgd_fun_codigo;
		$record['SGD_OEM_CODIGO'] = $sgd_oem_codigo;
		$record['SGD_CIU_CODIGO'] = $sgd_ciu_codigo;
		$record['SGD_ESP_CODI'] = $sgd_esp_codigo;
		$record['RADI_NUME_RADI'] = $noRad;
		$record['SGD_SEC_CODIGO'] = 0;
		$record['SGD_DIR_DIRECCION'] = $direccion_us1;
		$record['SGD_DIR_TELEFONO'] = "'".trim($telefono_us1)."'";
		$record['SGD_DIR_MAIL'] = $mail_us1;
		$record['SGD_DIR_TIPO'] = 1;
		$record['SGD_DIR_CODIGO'] = $nextval;
		$record['SGD_DIR_NOMBRE'] = $otro_us1;
	// return "dddee $grbNombresUs1 - $direccion_us1 - $mail_us1 - $nextval - $CodigoCiudad - $CodigoDepartamento - $cc_documento_us1 - $sgdTrd";
	//$insertSQL = $conexion->conn->Replace("SGD_DIR_DRECCIONES", $record, array('RADI_NUME_RADI','SGD_DIR_TIPO'), $autoquote = true);
	// $insertSQL = $this->db->insert("RADICADO", $recordR, "true");
    
	//$insertSQL = $conexion->db->insert("SGD_DIR_DRECCIONES", $record, "true");
	return $noRad;
	switch ($insertSQL)
	{	case 1:{	//Insercion Exitosa
					$dir_codigo_new = $nextval;
					$newId=true;
				}break;
		case 2:{	//Update Exitoso
					$newId = false;
				}break;
		case 0:{	//Error Transaccion.
					return  "No se ha podido actualizar la informacion de SGD_DIR_DRECCIONES UNO -- $isql --";
				}break;
	}
	unset($record);
	$ADODB_COUNTRECS = false;
}


if($documento_us1!='' and $cc!='')
{
	$sgd_ciu_codigo=0;
	$sgd_oem_codigo=0;
	$sgd_esp_codigo=0;
	$sgd_fun_codigo=0;

	echo "--$sgd_emp_us1--";
	  if($tipo_emp_us1==0){
		$sgd_ciu_codigo=$documento_us1;
		$sgdTrd = "1";
	}
	if($tipo_emp_us1==1){
		$sgd_esp_codigo=$documento_us1;
		$sgdTrd = "3";
	}
	if($tipo_emp_us1==2){
		$sgd_oem_codigo=$documento_us1;
		$sgdTrd = "2";
	}
	if($tipo_emp_us1==6){
		$sgd_fun_codigo=$documento_us1;
		$sgdTrd="4";
	}
	if($newId==true)
		{
		   $nextval=$conexion->nextId("sec_dir_direcciones");
		}
		if ($nextval==-1)
		{
			//$db->conn->RollbackTrans();
			return "No se encontrasena la secuencia sec_dir_direcciones ";
		}
  $num_anexos=$num_anexos+1;
  $str_num_anexos = substr("00$num_anexos",-2);
  $sgd_dir_tipo = "7$str_num_anexos" ;
	$isql = "insert into SGD_DIR_DRECCIONES (SGD_TRD_CODIGO, SGD_DIR_NOMREMDES, SGD_DIR_DOC, MUNI_CODI, DPTO_CODI,
			id_pais, id_cont, SGD_DOC_FUN, SGD_OEM_CODIGO, SGD_CIU_CODIGO, SGD_ESP_CODI, RADI_NUME_RADI, SGD_SEC_CODIGO,
			SGD_DIR_DIRECCION, SGD_DIR_TELEFONO, SGD_DIR_MAIL, SGD_DIR_TIPO, SGD_DIR_CODIGO, SGD_ANEX_CODIGO, SGD_DIR_NOMBRE) ";
	$isql .= "values ('$sgdTrd', '$grbNombresUs1', '$cc_documento_us1', $muni_tmp1, $dpto_tmp1, $idpais1, $idcont1,
						$sgd_fun_codigo, $sgd_oem_codigo, $sgd_ciu_codigo, $sgd_esp_codigo, $nurad, 0, '$direccion_us1',
						'".trim($telefono_us1)."', '$mail_us1', $sgd_dir_tipo, $nextval, '$codigo', '$otro_us7' )";
  $dir_codigo_new = $nextval;
  $nextval++;
  $rsg=$conexion->query($isql);
	if (!$rsg)
	{
		//$conexion->conn->RollbackTrans();
		return "No se ha podido actualizar la informacion de SGD_DIR_DRECCIONES TRES -- $isql --";
	}
}

	//*********************** FIN INSERTAR DIRECCIONES **********************
	

	$retval .=$noRad;
	
	if($filename!=''){
		$ext=explode('.',$filename);
		cambiarImagenRad($retval,$ext[1],$file);
	}
	
	return $retval;
}


/**
 * Esta funcion permite radicar un documento en Orfeo
 * @param $usuEmail, este parametro es el correo electronico del usuario
 * @param $file, Archivo asociado al radicado codificado en Base64 
 * @param $filename, Nombre del archivo que se radica
 * @param $correo, Correo del usuario
 * @param $destinos, arreglo de destinatarios destinatarios,predio,esp
 * @param $asu, Asunto del radicado
 * @param $med, Medio de radicacion
 * @param $ane, descripcion de anexos
 * @param $coddepe, codigo de la dependencia
 * @param $tpRadicado, tipo de radicado
 * @param $cuentai, cuenta interna del radicado
 * @param $radi_usua_actu, 
 * @param $tip_rem
 * @param $tdoc
 * @param $tip_doc
 * @param $carp_codi
 * @param $carp_per 
 * @author Donaldo Jinete Forero
 * @return El numero del radicado o un mensaje de error en caso de fallo
 */


function radicarDocumento($file,$filename,$correo,$destinatarioOrg,$predioOrg,$espOrg,$asu,$med,$ane,$coddepe,
$tpRadicado,$cuentai,$radi_usua_actu,$tip_rem,$tdoc,$tip_doc,$carp_codi,$carp_per)
{
	//Conversiones de datos para compatibilidad con aplicaciones internas
	
	$destinatario = array(
	'documento'=>$destinatarioOrg[0],
	'cc_documento'=>$destinatarioOrg[1],
	'tipo_emp'=>$destinatarioOrg[2],
	'nombre'=>$destinatarioOrg[3],
	'prim_apel'=>$destinatarioOrg[4],
	'seg_apel'=>$destinatarioOrg[5],
	'telefono'=>$destinatarioOrg[6],
	'direccion'=>$destinatarioOrg[7],
	'mail'=>$destinatarioOrg[8],
	'otro'=>$destinatarioOrg[9],
	'idcont'=>$destinatarioOrg[10],
	'idpais'=>$destinatarioOrg[11],
	'codep'=>$destinatarioOrg[12],
	'muni'=>$destinatarioOrg[13]
	);
	
	
	$predio = array(
	'documento'=>$predioOrg[0],
	'cc_documento'=>$predioOrg[1],
	'tipo_emp'=>$predioOrg[2],
	'nombre'=>$predioOrg[3],
	'prim_apel'=>$predioOrg[4],
	'seg_apel'=>$predioOrg[5],
	'telefono'=>$predioOrg[6],
	'direccion'=>$predioOrg[7],
	'mail'=>$predioOrg[8],
	'otro'=>$predioOrg[9],
	'idcont'=>$predioOrg[10],
	'idpais'=>$predioOrg[11],
	'codep'=>$predioOrg[12],
	'muni'=>$predioOrg[13]	
	);
	$esp = array(
	'documento'=>$espOrg[0],
	'cc_documento'=>$espOrg[1],
	'tipo_emp'=>$espOrg[2],
	'nombre'=>$espOrg[3],
	'prim_apel'=>$espOrg[4],
	'seg_apel'=>$espOrg[5],
	'telefono'=>$espOrg[6],
	'direccion'=>$espOrg[7],
	'mail'=>$espOrg[8],
	'otro'=>$espOrg[9],
	'idcont'=>$espOrg[10],
	'idpais'=>$espOrg[11],
	'codep'=>$espOrg[12],
	'muni'=>$espOrg[13]
	);
	
	
	try {
		$radi_usua_actu = getInfoUsuario($radi_usua_actu);
		$radi_usua_actu = trim($radi_usua_actu['usua_codi']);
	
		$coddepe = getInfoUsuario($coddepe);
		$coddepe = trim($coddepe['usua_depe']);
	}catch (Exception $e){
		return $e->getMessage();
	}
	
	
	// Fin
	
	global $ruta_raiz;
	
	include(RUTA_RAIZ."include/tx/Tx.php") ;
	include(RUTA_RAIZ."include/tx/Radicacion.php") ;
	include(RUTA_RAIZ."class_control/Municipio.php") ;

	
	$db = new ConnectionHandler($ruta_raiz) ;
	$hist = new Historico($db) ;
	$tmp_mun = new Municipio($db) ;
	$rad = new Radicacion($db) ;

	
	$tmp_mun->municipio_codigo($destinatario["codep"],$destinatario["muni"]) ;
	$rad->radiTipoDeri = $tpRadicado ;
	$rad->radiCuentai = "'".trim($cuentai)."'";
	$rad->eespCodi =  $esp["documento"] ;
	$rad->mrecCodi =  $med;
	$rad->radiFechOfic =  date("Y-m-d");
	if(!$radicadopadre)  $radicadopadre = null;
	$rad->radiNumeDeri = trim($radicadopadre) ;
	$rad->radiPais =  $tmp_mun->get_pais_codi() ;
	$rad->descAnex = $ane ;
	$rad->raAsun = $asu ;
	$rad->radiDepeActu = $coddepe ;
	$rad->radiDepeRadi = $coddepe ;
	$rad->radiUsuaActu = $radi_usua_actu ;
	$rad->trteCodi =  $tip_rem ;
	$rad->tdocCodi=$tdoc ;
	$rad->tdidCodi=$tip_doc;
	$rad->carpCodi = $carp_codi ;
	$rad->carPer = $carp_per ;
	$rad->trteCodi=$tip_rem ;
	$rad->radiPath = 'null';
	if (strlen(trim($aplintegra)) == 0)
			$aplintegra = "0" ;
	$rad->sgd_apli_codi = $aplintegra ;
	$codTx = 2 ;
	$flag = 1 ;
	$rad->usuaCodi=$radi_usua_actu ;
	$rad->dependencia=trim($coddepe) ;
	$noRad = $rad->newRadicado($tpRadicado,$coddepe) ;
	$nurad = trim($noRad) ;
	$sql_ret = $rad->updateRadicado($nurad,"/".date("Y")."/".$coddepe."/".$noRad.".pdf");
	
	if ($noRad=="-1")
	{
		return "Error no genero un Numero de Secuencia o Inserto el radicado";		
	}
	$radicadosSel[0] = $noRad;
	$hist->insertarHistorico($radicadosSel,  $coddepe , $radi_usua_actu, $coddepe, $radi_usua_actu, " ", $codTx);
	$sgd_dir_us2=2;
	
	$conexion = $db;
	
	/*
		Preparacion de variables para llamar el codigo del
		archivo grb_direcciones.php
	*/
	
	$tipo_emp_us1=trim($destinatario['tipo_emp']);
	$tipo_emp_us2=trim($predio['tipo_emp']);
	
	$muni_us1 = trim($destinatario['muni']);
	$muni_us2 = trim($predio['muni']);
	$muni_us3 = trim($esp['muni']);
	
	$codep_us1 = trim($destinatario['codep']);
	$codep_us2 = trim($predio['codep']);
	$codep_us3 = trim($esp['codep']);
	
	$grbNombresUs1 = trim($destinatario['nombre']) . " " . trim($destinatario['prim_apel']) . " ". trim($destinatario['seg_apel']);
	$grbNombresUs2 = trim($predio['nombre']) . " " . trim($predio['prim_apel']) . " ". trim($predio['seg_apel']);
	
	$cc_documento_us1 = trim($destinatario['cc_documento']);
	$cc_documento_us2 = trim($predio['cc_documento']);
	
	$documento_us1 = trim($destinatario['documento']);
	$documento_us2 = trim($predio['documento']);
	
	$direccion_us1 = trim($destinatario['direccion']);
	$direccion_us2 = trim($predio['direccion']);
	
	$telefono_us1 = trim($destinatario['telefono']);
	$telefono_us2 = trim($predio['telefono']);
	
	$mail_us1 = trim($destinatario['mail']);
	$mail_us2 = trim($predio['mail']);
	
	$otro_us1 = trim($destinatario['otro']);
	$otro_us2 = trim($predio['otro']);
	
	//************** INSERTAR DIRECCIONES *******************************
	
	if (!$muni_us1) $muni_us1 = NULL;
	if (!$muni_us2) $muni_us2 = NULL;
	if (!$muni_us3) $muni_us3 = NULL;
	
	// Creamos las valores del codigo del dpto y mcpio desglozando el valor del <SELECT> correspondiente.
	if (!is_null($muni_us1)){
		$tmp_mun = new Municipio($conexion);
		$tmp_mun->municipio_codigo($codep_us1,$muni_us1);
		$tmp_idcont = $tmp_mun->get_cont_codi();
		$tmp_idpais = $tmp_mun->get_pais_codi();
		$muni_tmp1 = explode("-",$muni_us1);
		switch (count($muni_tmp1))
		{	
			case 4:
			{
				$idcont1 = $muni_tmp1[0];
				$idpais1 = $muni_tmp1[1];
				$dpto_tmp1 = $muni_tmp1[2];
				$muni_tmp1 = $muni_tmp1[3];

			}
			break;
		case 3:
			{
				$idcont1 = $tmp_idcont;
				$idpais1 = $muni_tmp1[0];
				$dpto_tmp1 = $muni_tmp1[1];
				$muni_tmp1 = $muni_tmp1[2];
			}
			break;
		case 2:
			{
				$idcont1 = $tmp_idcont;
				$idpais1 = $tmp_idpais;
				$dpto_tmp1 = $muni_tmp1[0];
				$muni_tmp1 = $muni_tmp1[1];
			}
			break;
		}
		unset($tmp_mun);
		unset($tmp_idcont);
		unset($tmp_idpais);
	}

	if (!is_null($muni_us2))
	{	
		$tmp_mun = new Municipio($conexion);
		$tmp_mun->municipio_codigo($codep_us2,$muni_us2);
		$tmp_idcont = $tmp_mun->get_cont_codi();
		$tmp_idpais = $tmp_mun->get_pais_codi();
		$muni_tmp2 = explode("-",$muni_us2);
		switch (count($muni_tmp2))
		{	
			case 4:
			{	
				$idcont2 = $muni_tmp2[0];
				$idpais2 = $muni_tmp2[1];
				$dpto_tmp2 = $muni_tmp2[2];
				$muni_tmp2 = $muni_tmp2[3];
			}
			break;
		case 3:
			{
				$idcont2 = $tmp_idcont;
				$idpais2 = $muni_tmp2[0];
				$dpto_tmp2 = $muni_tmp2[1];
				$muni_tmp2 = $muni_tmp2[2];
			}
			break;
		case 2:
			{
				$idcont2 = $tmp_idcont;
				$idpais2 = $tmp_idpais;
				$dpto_tmp2 = $muni_tmp2[0];
				$muni_tmp2 = $muni_tmp2[1];
			}
			break;
		}
		unset($tmp_mun);unset($tmp_idcont);unset($tmp_idpais);
	}	
	if (!is_null($muni_us3))
	{	
		$tmp_mun = new Municipio($conexion);
		$tmp_mun->municipio_codigo($codep_us3,$muni_us3);
		$tmp_idcont = $tmp_mun->get_cont_codi();
		$tmp_idpais = $tmp_mun->get_pais_codi();
		$muni_tmp3 = explode("-",$muni_us3);
		switch (count($muni_tmp3))
		{	
			case 4:
			{	
				$idcont3 = $muni_tmp3[0];
				$idpais3 = $muni_tmp3[1];
				$dpto_tmp3 = $muni_tmp3[2];
				$muni_tmp3 = $muni_tmp3[3];
			}
			break;
			case 3:
			{
				$idcont1 = $tmp_idcont;
				$idpais3 = $muni_tmp3[0];
				$dpto_tmp3 = $muni_tmp3[1];
				$muni_tmp3 = $muni_tmp3[2];
			}
			break;
		case 2:
			{
				$idcont3 = $tmp_idcont;
				$idpais3 = $tmp_idpais;
				$dpto_tmp3 = $muni_tmp3[0];
				$muni_tmp3 = $muni_tmp3[1];
			}
			break;
		}
		unset($tmp_mun);unset($tmp_idcont);unset($tmp_idpais);
	}
	
	$newId = false;
	if(!$modificar)
	{
   		$nextval=$conexion->nextId("sec_dir_direcciones");
	}
	if ($nextval==-1)
	{
		return "No se encontro la secuencia sec_dir_direcciones ";
	}
	global $ADODB_COUNTRECS;
	if($documento_us1!='')
	{
		$sgd_ciu_codigo=0;
		$sgd_oem_codigo=0;
		$sgd_esp_codigo=0;
		$sgd_fun_codigo=0;
  		if($tipo_emp_us1==0)
  		{	
  			$sgd_ciu_codigo=$documento_us1;
			$sgdTrd = "1";
		}
		if($tipo_emp_us1==1)
		{	
			$sgd_esp_codigo=$documento_us1;
			$sgdTrd = "3";
		}
		if($tipo_emp_us1==2)
		{	
			$sgd_oem_codigo=$documento_us1;
			$sgdTrd = "2";
		}
		if($tipo_emp_us1==6)
		{	
			$sgd_fun_codigo=$documento_us1;
			$sgdTrd = "4";
		}

	
		
		$ADODB_COUNTRECS = true;

		$record = array();
		$record['SGD_TRD_CODIGO'] = $sgdTrd;
		$record['SGD_DIR_NOMREMDES'] = $grbNombresUs1;
		$record['SGD_DIR_DOC'] = $cc_documento_us1;
		$record['MUNI_CODI'] = $muni_tmp1;
		$record['DPTO_CODI'] = $dpto_tmp1;
		$record['ID_PAIS'] = $idpais1;
		$record['ID_CONT'] = $idcont1;
		$record['SGD_DOC_FUN'] = $sgd_fun_codigo;
		$record['SGD_OEM_CODIGO'] = $sgd_oem_codigo;
		$record['SGD_CIU_CODIGO'] = $sgd_ciu_codigo;
		$record['SGD_OEM_CODIGO'] = $sgd_oem_codigo;
		$record['SGD_ESP_CODI'] = $sgd_esp_codigo;
		$record['RADI_NUME_RADI'] = $nurad;
		$record['SGD_SEC_CODIGO'] = 0;
		$record['SGD_DIR_DIRECCION'] = $direccion_us1;
		$record['SGD_DIR_TELEFONO'] = trim($telefono_us1);
		$record['SGD_DIR_MAIL'] = $mail_us1;
		$record['SGD_DIR_TIPO'] = 1;
		$record['SGD_DIR_CODIGO'] = $nextval;
		$record['SGD_DIR_NOMBRE'] = $otro_us1;

	$insertSQL = $conexion->conn->Replace("SGD_DIR_DRECCIONES", $record, array('RADI_NUME_RADI','SGD_DIR_TIPO'), $autoquote = true);

	switch ($insertSQL)
	{	case 1:{	//Insercion Exitosa
					$dir_codigo_new = $nextval;
					$newId=true;
				}break;
		case 2:{	//Update Exitoso
					$newId = false;
				}break;
		case 0:{	//Error Transaccion.
					return  "No se ha podido actualizar la informacion de SGD_DIR_DRECCIONES UNO -- $isql --";
				}break;
	}
	unset($record);
	$ADODB_COUNTRECS = false;
}



	// ***********************  us2
if($documento_us2!='')
{
	$sgd_ciu_codigo=0;
    $sgd_oem_codigo=0;
    $sgd_esp_codigo=0;
	$sgd_fun_codigo=0;
	
  if($tipo_emp_us2==0){
		$sgd_ciu_codigo=$documento_us2;
		$sgdTrd = "1";
	}
	if($tipo_emp_us2==1){
		$sgd_esp_codigo=$documento_us2;
		$sgdTrd = "3";
	}
	if($tipo_emp_us2==2){
		$sgd_oem_codigo=$documento_us2;
		$sgdTrd = "2";
	}
	if($tipo_emp_us2==6){
		$sgd_fun_codigo=$documento_us2;
		$sgdTrd = "4";
	}
	$isql = "select * from sgd_dir_drecciones where radi_nume_radi=$nurad and sgd_dir_tipo=2";
	$rsg=$conexion->query($isql);

    if 	($rsg->EOF)
	{
		//if($newId==true)
			//{
			   $nextval=$conexion->nextId("sec_dir_direcciones");
			//}
			if ($nextval==-1)
			{
				//$db->conn->RollbackTrans();
				echo "<span class='etextomenu'>No se encontr� la secuencia sec_dir_direcciones ";
			}

		$isql = "insert into SGD_DIR_DRECCIONES(SGD_TRD_CODIGO, SGD_DIR_NOMREMDES, SGD_DIR_DOC, DPTO_CODI, MUNI_CODI,
      			id_pais, id_cont, SGD_DOC_FUN, SGD_OEM_CODIGO, SGD_CIU_CODIGO, SGD_ESP_CODI, RADI_NUME_RADI, SGD_SEC_CODIGO,
      			SGD_DIR_DIRECCION, SGD_DIR_TELEFONO, SGD_DIR_MAIL, SGD_DIR_TIPO, SGD_DIR_CODIGO, SGD_DIR_NOMBRE)
	  			values('$sgdTrd', '$grbNombresUs2', '$cc_documento_us2', $dpto_tmp2, $muni_tmp2, $idpais2, $idcont2,
	  			$sgd_fun_codigo, $sgd_oem_codigo, $sgd_ciu_codigo, $sgd_esp_codigo, $nurad, 0,'".trim($direccion_us2).
	  			"', '".trim($telefono_us2)."', '$mail_us2', 2, $nextval, '$otro_us2')";
   	  $dir_codigo_new = $nextval;
   	  $newId=true;
    }
	 else
	{
	  $newId = false;
		$isql = "update SGD_DIR_DRECCIONES
				set MUNI_CODI=$muni_tmp2, DPTO_CODI=$dpto_tmp2, id_pais=$idpais2, id_cont=$idcont2
				,SGD_OEM_CODIGO=$sgd_oem_codigo
				,SGD_CIU_CODIGO=$sgd_ciu_codigo
				,SGD_ESP_CODI=$sgd_esp_codigo
				,SGD_DOC_FUN=$sgd_fun_codigo
				,SGD_SEC_CODIGO=0
				,SGD_DIR_DIRECCION='$direccion_us2'
				,SGD_DIR_TELEFONO='$telefono_us2'
				,SGD_DIR_MAIL='$mail_us2'
				,SGD_DIR_NOMBRE='$otro_us2'
				,SGD_DIR_NOMREMDES='$grbNombresUs2'
				,SGD_DIR_DOC='$cc_documento_us2'
				,SGD_TRD_CODIGO='$sgdTrd'
			 	where radi_nume_radi=$nurad and SGD_DIR_TIPO=2 ";
	}

	$rsg=$conexion->query($isql);

	if (!$rsg){
		return "No se ha podido actualizar la informacion de SGD_DIR_DRECCIONES DOS -- $isql --";
	}

	}

	
if($documento_us1!='' and $cc!='')
{
	$sgd_ciu_codigo=0;
	$sgd_oem_codigo=0;
	$sgd_esp_codigo=0;
	$sgd_fun_codigo=0;

	echo "--$sgd_emp_us1--";
	  if($tipo_emp_us1==0){
		$sgd_ciu_codigo=$documento_us1;
		$sgdTrd = "1";
	}
	if($tipo_emp_us1==1){
		$sgd_esp_codigo=$documento_us1;
		$sgdTrd = "3";
	}
	if($tipo_emp_us1==2){
		$sgd_oem_codigo=$documento_us1;
		$sgdTrd = "2";
	}
	if($tipo_emp_us1==6){
		$sgd_fun_codigo=$documento_us1;
		$sgdTrd="4";
	}
	if($newId==true)
		{
		   $nextval=$conexion->nextId("sec_dir_direcciones");
		}
		if ($nextval==-1)
		{
			//$db->conn->RollbackTrans();
			return "No se encontrasena la secuencia sec_dir_direcciones ";
		}
  $num_anexos=$num_anexos+1;
  $str_num_anexos = substr("00$num_anexos",-2);
  $sgd_dir_tipo = "7$str_num_anexos" ;
	$isql = "insert into SGD_DIR_DRECCIONES (SGD_TRD_CODIGO, SGD_DIR_NOMREMDES, SGD_DIR_DOC, MUNI_CODI, DPTO_CODI,
			id_pais, id_cont, SGD_DOC_FUN, SGD_OEM_CODIGO, SGD_CIU_CODIGO, SGD_ESP_CODI, RADI_NUME_RADI, SGD_SEC_CODIGO,
			SGD_DIR_DIRECCION, SGD_DIR_TELEFONO, SGD_DIR_MAIL, SGD_DIR_TIPO, SGD_DIR_CODIGO, SGD_ANEX_CODIGO, SGD_DIR_NOMBRE) ";
	$isql .= "values ('$sgdTrd', '$grbNombresUs1', '$cc_documento_us1', $muni_tmp1, $dpto_tmp1, $idpais1, $idcont1,
						$sgd_fun_codigo, $sgd_oem_codigo, $sgd_ciu_codigo, $sgd_esp_codigo, $nurad, 0, '$direccion_us1',
						'".trim($telefono_us1)."', '$mail_us1', $sgd_dir_tipo, $nextval, '$codigo', '$otro_us7' )";
  $dir_codigo_new = $nextval;
  $nextval++;
  $rsg=$conexion->query($isql);
	if (!$rsg)
	{
		//$conexion->conn->RollbackTrans();
		return "No se ha podido actualizar la informacion de SGD_DIR_DRECCIONES TRES -- $isql --";
	}
}

	//*********************** FIN INSERTAR DIRECCIONES **********************
	

	$retval .=$noRad;
	
	if($filename!=''){
		$ext=explode('.',$filename);
		cambiarImagenRad($retval,$ext[1],$file);
	}
	
	return $retval;
}

/**
 * Esta funcion permite anular un  radicado  en Orfeo
 * @param $checkValue, es un arreglo con los numeros de radicado
 * @author Donaldo Jinete Forero
 * @return El numero del radicado o un mensaje de error en caso de fallo
 */
function anularRadicado($checkValue,$dependencia,$usua_doc,$observa,$codusuario)
{
	/*  RADICADOS SELECCIONADOS
	 *  @$setFiltroSelect  Contiene los valores digitados por el usuario separados por coma.
	 *  @$filtroSelect Si SetfiltoSelect contiene algun valor la siguiente rutina 
	 *  realiza el arreglo de la condificacion para la consulta a la base de datos y lo almacena en whereFiltro.
	 *  @$whereFiltro  Si filtroSelect trae valor la rutina del where para este filtro es almacenado aqui.
	 */
	$radicadosXAnular = "";
	include_once(RUTA_RAIZ."include/db/ConnectionHandler.php");
	global $ruta_raiz;
	$db = new ConnectionHandler($ruta_raiz);
	if($checkValue) {
		$num = count($checkValue);
		$i = 0;
		while ($i < $num) {
			$estaRad   = false;
			$record_id = $checkValue[$i];
			// Consulta para verificar el estado del radicado del radicado en sancionados
			$querySancionados = "SELECT ESTADO 
						FROM SANCIONADOS.SAN_RESOLUCIONES 
						WHERE nro_resol = '$record_id'";
			$rs = $db->conn->Execute($querySancionados);
			
			// Si esta el radicado
			if (!$rs->EOF) {
				$estado = $rs->fields["ESTADO"];
				if ($estado != "V") {
					$vigente = false;
				}
				$estaRad = true;
			}
			
			// Si esta el radicado entonces verificar vigencia
			if ($estaRad) {
				// Si se encuentra vigente entonces no se puede anular
				if($vigente) {
					$arregloVigentes[] = $record_id;
				} else {
					$setFiltroSelect .= $record_id;
                                        $radicadosSel[] = $record_id;
					$radicadosXAnular .= "'" . $record_id . "'";
				}
			} else {
				$setFiltroSelect .= $record_id;
				$radicadosSel[] = $record_id;
			}
			
			if($i<=($num-2)) {
				if (!$vigente || !$estaRad) {
					$setFiltroSelect .= ",";
				}
				if ($estaRad && !empty($radicadosXAnular)) {
					$radicadosXAnular .= ",";
				}
			}
  			next($checkValue);
			$i++;
			// Inicializando los valores de comprobacion
			$estaRad = false;
			$vigente = true;
		}
		if ($radicadosSel) {
			$whereFiltro = " and b.radi_nume_radi in($setFiltroSelect)";
		}
	}
	$systemDate = $db->conn->OffsetDate(0,$db->conn->sysTimeStamp);
	include(RUTA_RAIZ.'config.php');
	include_once (RUTA_RAIZ.'anulacion/Anulacion.php');
	include_once (RUTA_RAIZ.'include/tx/Historico.php');
	// Se vuelve crear el objeto por que saca un error con el anterior 
	$db = new ConnectionHandler($ruta_raiz);
	$Anulacion = new Anulacion($db);
	$observa = "Solicitud Anulacion.$observa";
	
	/* Sentencia para consultar en sancionados el estado en que se encuentra el radicado
	 * A = Anulado, V = Vigente, B = Estado temporal 
	 * Si el estado del radicado en sancionados es diferente de V puede realizar la sancion
	 */
	// Si por lo menos hay un radicado por anular
	
	$retval.= "<br> radicadosSel = ". $radicadosSel[0];
	
	if (!empty($radicadosSel[0])) {
		$retval .= "<br>Anulacion
					<br>dependencia = $dependencia
					<br>usua_doc = $usua_doc
					<br>observa = $observa
					<br>codusuario = $codusuario
					<br>systemDate = $systemDate";
		$radicados = $Anulacion->solAnulacion($radicadosSel,
						$dependencia,
						$usua_doc,
						$observa,
						$codusuario,
						$systemDate);
		if (!empty($radicadosXAnular)) {
			$sqlSancionados = "update SGD_APLMEN_APLIMENS 
						set SGD_APLMEN_DESDEORFEO = 2 
						where SGD_APLMEN_REF in($radicadosXAnular)";
			$rs = $db->conn->Execute($sqlSancionados);
		}
		$fecha_hoy =date("Y-m-d");
		$dateReplace = $db->conn->SQLDate("Y-m-d","$fecha_hoy");
		$Historico = new Historico($db);
		/** 
		 * Funcion Insertar Historico 
		 * insertarHistorico($radicados,  
		 * 			$depeOrigen, 
		 *			$usCodOrigen,
		 *			$depeDestino,
		 *			$usCodDestino,
		 *			$observacion,
		 *			$tipoTx)
		 */
		$depe_codi_territorial = $dependencia;
		
		$radicados = $Historico->insertarHistorico($radicadosSel,
								$dependencia,
								$codusuario,
								$depe_codi_territorial,
								1,
								$observa,
								25); 
	}
	return $retval;
}

function anexarExpediente($numRadicado,$numExpediente,$usuaLoginMail,$observa){
		global $ruta_raiz;
		$db = new ConnectionHandler($ruta_raiz);
		include_once (RUTA_RAIZ.'include/tx/Historico.php');
        $estado=estadoRadicadoExpediente($numRadicado,$numExpediente);
        $usua=getInfoUsuario($usuaLoginMail);
        $tipoTx = 53;
    	$Historico = new Historico( $db );
    	$fecha=$db->conn->OffsetDate(0,$db->conn->sysTimeStamp);
    	try{
        switch ($estado){
                case 0:
                        throw new Exception("El documento con numero de radicado  {$numRadicado} ya fue anexado al expediente {$numExpediente}");
                case 1:
                        throw new Exception("El documento con numero de radicado {$numRadicado} ya fue anexado al expediente {$numExpediente} y archivado fisicamente");
                case 2: 
                        $consulta="UPDATE SGD_EXP_EXPEDIENTE SET SGD_EXP_ESTADO=0,SGD_EXP_FECH={$fecha},USUA_CODI=".$usua['usua_codi'].",USUA_DOC='".$usua['usua_doc']."'
                                ,DEPE_CODI=".$usua['usua_depe']." WHERE RADI_NUME_RADI={$numRadicado} 
                                                AND SGD_EXP_NUMERO='{$numExpediente}'";
                                break;
                default:
                        $consulta="INSERT INTO SGD_EXP_EXPEDIENTE (SGD_EXP_NUMERO,RADI_NUME_RADI,SGD_EXP_FECH,SGD_EXP_ESTADO,USUA_CODI,USUA_DOC,DEPE_CODI)
                                          VALUES ('{$numExpediente}',{$numRadicado},{$fecha},0,".$usua['usua_codi'].",'".$usua['usua_doc']."',".$usua['usua_depe'].")";
                        break;
        }
    	}
    	catch (Exception $e){
    		return $e->getMessage();
    	}
        if($db->query($consulta)){
        		$radicados = array($numRadicado);
                $radicados = $Historico->insertarHistoricoExp( $numExpediente, $radicados, $usua['usua_depe'], $usua['usua_codi'], $observa, $tipoTx, 0);
                return $radicados[0];
                
        }else{ 
                throw new Exception("Error y no se realizo la operacion");
        }
}



function cambiarImagenRad($numRadicado,$ext,$file){
	global $ruta_raiz;
	$db = new ConnectionHandler($ruta_raiz);
	$sql="SELECT RAPI_DEPE_RADI,RADI_FECH_OFIC FROM RADICADO WHERE RADI_NUME_RADI='{$numRadicado}'";
	$rs=$db->query($sql);
	if(!$rs->EOF){
		$year=substr($numRadicado,0,4);
		$depe=substr($numRadicado,4,3);
		$path="/{$year}/{$depe}/docs/{$numRadicado}.{$ext}";
		$update="UPDATE RADICADO SET RADI_PATH='{$path}' where RADI_NUME_RADI='{$numRadicado}'";
		if(UploadFile($file,$numRadicado.'.'.$ext)=='exito'){
			$db->query($update);
			return "OK";
		}else{
			throw new Exception("ERROR no se puede copiar el archivo");
		}
	}else{
			throw new Exception("ERROR El radicado no existe");
	}
}



function estadoRadicadoExpediente($numRadicado,$numExpediente){
	global $ruta_raiz;
	$db = new ConnectionHandler($ruta_raiz);
	$salida=-1;
	$consulta="SELECT SGD_EXP_ESTADO FROM SGD_EXP_EXPEDIENTE WHERE RADI_NUME_RADI={$numRadicado} AND SGD_EXP_NUMERO='{$numExpediente}'";
	$resultado=$db->query($consulta);
	if($resultado && !$resultado->EOF){
		$salida=$resultado->fields['SGD_EXP_ESTADO'];
	}
	return $salida;
}


function getInfoUsuario($usuaLoginMail){
		global $ruta_raiz;
		$db = new ConnectionHandler($ruta_raiz);
		$upperMail=strtoupper($usuaLoginMail);
		$lowerMail=strtolower($usuaLoginMail);
        $sql="SELECT USUA_LOGIN,USUA_DOC,DEPE_CODI,CODI_NIVEL,USUA_CODI,USUA_NOMB FROM USUARIO 
                        WHERE  USUA_EMAIL='{$usuaLoginMail}@superservicios.gov.co' OR USUA_EMAIL='{$upperMail}@superservicios.gov.co' OR USUA_EMAIL='{$lowerMail}@superservicios.gov.co' ";
        $rs=$db->query($sql);
                if($rs && !$rs->EOF){
                		$salida['usua_login']=($rs->fields["USUA_LOGIN"]);
                        $salida['usua_doc'] =($rs->fields["USUA_DOC"]);
                        $salida['usua_depe'] =($rs->fields["DEPE_CODI"]);
                        $salida['usua_nivel'] =($rs->fields["CODI_NIVEL"]);
                        $salida['usua_codi'] =($rs->fields["USUA_CODI"]);
                        $salida['usua_nomb'] =($rs->fields["USUA_NOMB"]);
        }else{
        	throw new Exception("El usuario $usuaLoginMail no existe $sql");
        }
        
        return $salida;
}

function asociarObjetoFlujo($nuRad,$usuaEmail,$tflujo){
	$info = getInfoUsuario($usuaEmail);
	try{
		$flujo= new flujo($nuRad,$info['usua_login'],1);
		$flujo->asociarFlujo($tflujo);
	}catch(Exception $e){
		return $e->getMessage();
	}
	return "OK";
}

function cambioDeEtapaFlujo($objDoc,$flujo,$etapa){
	global $ruta_raiz;
	$db = new ConnectionHandler($ruta_raiz);
	try{
		$value = flujo::cambiarEtapa($db,$objDoc,$flujo,$etapa);
	}catch (Exception $e){
		return "ERROR: ".$e->getMessage();
	}
	return ($value)?"OK: Se realizo el cambio de etapa de $objDoc a $etapa en el flujo $flujo":"ERROR";	
}

function informacionJefe($usuaEmail){
	global $ruta_raiz;
	try{
		$info = getInfoUsuario($usuaEmail);
	}catch (Exception $e){
		return array($e->getMessage());
	}
	$db = new ConnectionHandler($ruta_raiz);
	$consulta = "SELECT USUA_NOMB,USUA_EMAIL FROM USUARIO WHERE DEPE_CODI = '{$info['usua_depe']}' AND USUA_CODI='1' ";
	$rs=$db->query($consulta);
	if($rs->EOF){
		return array("No se puede obtener informacion del jefe de la dependencia {$info['usua_depe']}");	
	}
	return array($rs->fields['USUA_NOMB'],$rs->fields['USUA_EMAIL']);
}

function cambiarImagenCorreo($numRadicado,$texto){
	global $ruta_raiz;
	
	if(empty($numRadicado)||empty($texto)){
		return "ERROR: Falta un parametro requerido";
	}
	$pdf=new FPDF();
	$pdf->AddPage();
	$pdf->SetFont('Courier','B',8);
	$pdf->Write(5,$texto);
	$contents=$pdf->Output('','S');
	$base64string = base64_encode($contents);
	return cambiarImagenRad($numRadicado,'pdf',$base64string);
	
}

function radicarCorreo($idEmpresa,$correo,$asu,$usuario){
	
	global $ruta_raiz;
	
	$db = new ConnectionHandler($ruta_raiz);
	$consulta = "SELECT * FROM BODEGA_EMPRESAS WHERE IDENTIFICADOR_EMPRESA='{$idEmpresa}'";
	$rs=$db->query($consulta);
	if($rs->EOF){
		return "ERROR: Obteniendo la empresa";
	}
	
	
	$destinario =	array(
			$idEmpresa,
			$rs->fields['NIT_DE_LA_EMPRESA'],
			'1',//Tipo de empresa ESP
			$rs->fields['NOMBRE_DE_LA_EMPRESA'],
			$rs->fields['SIGLA_DE_LA_EMPRESA'],
			$rs->fields['NOMBRE_REP_LEGAL'],
			$rs->fields['TELEFONO_1'],
			$rs->fields['DIRECCION'],
			$rs->fields['EMAIL'],
			"",
			$rs->fields['ID_CONT'],
			$rs->fields['ID_PAIS'],
			$rs->fields['ID_PAIS']."-".$rs->fields['CODIGO_DEL_DEPARTAMENTO'],
		    $rs->fields['ID_PAIS']."-".$rs->fields['CODIGO_DEL_DEPARTAMENTO']."-".$rs->fields['CODIGO_DEL_MUNICIPIO']);
		   
		    
	$file='';
	$fileName='';

	
	$predio =	array(
			'',
			'',
			'',
			'',
			'',
			'',
			'',
		    '',
		    '',
		    '',
		    '',
		    '',
		    '',
		    '');
	
		    
	$esp =	array(
			$idEmpresa,
			$rs->fields['NIT_DE_LA_EMPRESA'],
			'1',//Tipo de Empresa ESP
			$rs->fields['NOMBRE_DE_LA_EMPRESA'],
			$rs->fields['SIGLA_DE_LA_EMPRESA'],
			$rs->fields['NOMBRE_REP_LEGAL'],
			$rs->fields['TELEFONO_1'],
			$rs->fields['DIRECCION'],
			$rs->fields['EMAIL'],
			"",
			$rs->fields['ID_CONT'],
			$rs->fields['ID_PAIS'],
			$rs->fields['ID_PAIS']."-".$rs->fields['CODIGO_DEL_DEPARTAMENTO'],
		    $rs->fields['ID_PAIS']."-".$rs->fields['CODIGO_DEL_DEPARTAMENTO']."-".$rs->fields['CODIGO_DEL_MUNICIPIO']);
		    
		    
	$med='4';//Medio de envio correo electronico
	$ane='Radicacion de correo electronico';
	$coddepe=$usuario;
	$tpRadicado='1';
	$cuentai='';
	$radi_usua_actu=$usuario;
	$tip_rem='0';
	$tdoc='84';
	$tip_doc='1';
	$carp_codi='1';
	$carp_per='0';
	

	return 
	radicarDocumento(
		$file,
		$fileName,
		$correo,
		$destinario,
		$predio,
		$esp,
		$asu,
		$med,
		$ane,
		$coddepe,
		$tpRadicado,
		$cuentai,
		$radi_usua_actu,
		$tip_rem,
		$tdoc,
		$tip_doc,
		$carp_codi,
		$carp_per
	);	
}

function asociarExpCorreo($idEmpresa,$tipoServ,$tamPres,$radicado){
	global $ruta_raiz;
	$db = new ConnectionHandler($ruta_raiz);
	$depen=null;
	
	switch(strtolower($tamPres)){
		case "p":
			$depen='460';
		break;
		case "g":
			switch (strtoupper($tipoServ)){
				case 'AA':
					$depen='420';
				break;
				case 'A';
					$depen='430';
				break;
				default:
					return "ERROR: No se reconoce el tipo de servicio $tipoServ";
				break;
			}
		break;
		default:
			return "ERROR: debe especificar el tamano del prestador
			( grande o pequeno )";
		break;
	}
	if(!is_null($depen)){
		$sql="SELECT * FROM SGD_SEXP_SECEXPEDIENTES WHERE 
		SGD_SEXP_PAREXP3='{$idEmpresa}' AND DEPE_CODI='{$depen}'";
		$rs = $db->query($sql);
		if(!$rs->EOF){
			return $rs->fields['SGD_EXP_NUMERO'];
		}
	}
	
	
	return "ERROR: no se ha podido completar la operacion";
}

$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);
?>
