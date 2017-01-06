<?php
/**********************************************************************************
Diseno de un Web Service que permita la interconexion de aplicaciones con Orfeo
**********************************************************************************/

/**
 * @author German Mahecha
 * @author Aquiles Canto (modificacion del archivo origanla y adicion de funcionalidad)
 */
//Llamado a la clase nusoap
$ruta_raiz = "../";
require_once("../webServices/nusoap/lib/nusoap.php");
include_once($ruta_raiz."include/db/ConnectionHandler.php");
//echo "1";	
//Asignacion del namespace  
//http://wiki.superservicios.gov.co:81/~wduarte/br3.6.0/
$ns="webServices/nusoap";
//echo "2";

//Creacion del objeto soap_server
$server = new soap_server();

$server->configureWSDL('Sistema de Gestión Documental Orfeo-Internas',$ns);

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
$server->register('generarRadicado',
	array(
	'correo'=> 'xsd:string'
	),
	array('return'=>'tns:Vector'),
	$ns
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
     'file' => 'xsd:base64binary',										//archoivo en base 64
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
	global $ruta_raiz;
	$db = new ConnectionHandler("$ruta_raiz");
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	if ($usuaEmail != ''){
		$sql = "select DEPE_CODI, USUA_CODI, USUA_DOC, USUA_EMAIL  from usuario where UPPER(USUA_EMAIL) = UPPER('$usuaEmail')";
		echo $sql;
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
	global $ruta_raiz;
	$var = explode(".",$filename);
	$path = $ruta_raiz."bodega/";
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
		
	global $ruta_raiz;
	include_once($ruta_raiz."include/tx/Expediente.php");
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
 * funcion que rescata los valores de un usuario de orfeo 
 * a partir del correo electonico
 *
 * @param string $correo mail del usuario en orfeo
 * @return array resultado de la consulta;
 */
function getUsuarioCorreo($correo){
	global $ruta_raiz;
	//$salida="pailas papa";
/*	$consulta="SELECT USUA_LOGIN,DEPE_CODI,USUA_EMAIL,CODI_NIVEL,USUA_CODI,USUA_DOC
	           FROM USUARIO WHERE USUA_EMAIL='$correo' AND USUA_ESTA=1";*/
	$salida=array();
	if(verificarCorreo($correo)){
	$consulta="SELECT USUA_LOGIN,DEPE_CODI,USUA_EMAIL,CODI_NIVEL,USUA_CODI,USUA_DOC
	           FROM USUARIO WHERE UPPER(USUA_EMAIL)=UPPER('".trim($correo)."') AND USUA_ESTA=1";
	 $db = &new ConnectionHandler($ruta_raiz);
	 $rs = &$db->query($consulta);
	 //$rs->_numOfRows > 0
	 if (!$rs->EOF){
		 $salida['email'] = $rs->fields['USUA_EMAIL'];
		 $salida['codusuario']  = $rs->fields['USUA_CODI'];
		 $salida['dependencia'] = $rs->fields['DEPE_CODI'];
		 $salida['documento'] =  $rs->fields['USUA_DOC'];
		 $salida['nivel'] = $rs->fields['CODI_NIVEL'];
		 $salida['login'] = $rs->fields['USUA_LOGIN'];
	   } else {
	   	$salida['error']="El ususario no existe o se encuentra deshabilitado: " . $consulta;
	   }
	}else{
		$salida["error"]="el mail no corresponde a un email valido";
	}
	
	return $salida;
}
/**
 * funcion que verifica que un correo electroni cumpla con 
 * un patron standar
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
 * @param string  $radiNume numero del radicado al cual se adiciona el anexo
 * @param base64 $file archivo codificado en base64
 * @param string $filename nombre original del anexo, con extension
 * @param string $correo correo electronico del usuario que adiciona el anexo
 * @param string $descripcion descripcion del anexo
 * @return string mensaje de error en caso de fallo o el numero del anexo en caso de exito
 */
function crearAnexo($radiNume,$file,$filename,$correo,$descripcion){
	global  $ruta_raiz;
	$db = new ConnectionHandler("$ruta_raiz");
	$usuario=getUsuarioCorreo($correo);
	
	$error=(isset($usuario['error']))?true:false;
	$ruta=$ruta_raiz."bodega/".substr($radiNume,0,4)."/".substr($radiNume,4,$digitosDependencia)."/docs/";
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
	return $cod ? 'Anexo Creado' : 'Error en la adicion verifique: ' . $nombreAnexo;
}
/**
 * funcion que calculcula el numero de anexos que tiene un radicado
 *
 * @param int  $radiNume radicado al cual se realiza se adiciona el anexo
 * @param ConectionHandler $db
 * @return int numero de anexos del radicado
 */
function numeroAnexos($radiNume,$db){
	$consulta=$consulta="SELECT COUNT(1) AS NUM_ANEX FROM ANEXOS WHERE ANEX_RADI_NUME={$radiNume}";
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
	$consulta=$consulta="SELECT max(ANEX_NUMERO) AS NUM_ANEX FROM ANEXOS WHERE ANEX_RADI_NUME={$radiNume}";
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
	global  $ruta_raiz;
	$db = new ConnectionHandler( "$ruta_raiz" );
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


$server->service($HTTP_RAW_POST_DATA);
?>
