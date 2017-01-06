<?php
//	define('ADODB_ASSOC_CASE', 0);
	error_reporting(0);
	define('APLIPATH','/home/orfeodev/jgonzal/public_html/br_3.6.0');
	
	$ruta_raiz = APLIPATH;
	$pearLib = APLIPATH . "/pear/";
	//$departamento = 11;
	//$municipio = 1;
	// Radicando al usuario ADMON1
	$depDestino 	= 905;//524;		//dependencia destino a donde va dirigido
	$usuaDestino 	= 799822761;//51940145;	//Codigo del usuario que va el radicado
	$usuaCodi 	= 1;
	$tipoAnexo 	= 3;
	$fechaRadicacion = date("d-m-Y");
	$numTotalDia 	= 0;		// Tiene el numero de radicado que ha hecho en el dia
	$textoViaWeb 	= "Memorando de solicitud de comisiones";
	$nombreArchivoPdf = "";
	$documento_us1 	= 0;		// Inicializando variable
	$cc_documento_us1 = 0;
	$documento_us1 	= "";		//Numero por verificar
	$tipo_emp_us1 	= 0;
	$depende22 	= "";
	
	// Capturando las variables que llegan por post
	$_POST["nombre"] = "JEFE";
	$_POST["apellido"] = "PRUEBAS";
	
	//$nombre_us1 	= (!empty($_POST["nombre"])) ? $_POST["nombre"] : null;
	//$prim_apel_us1 	= (!empty($_POST["apellido"])) ? $_POST["apellido"] : null;
	//$seg_apel_us1 	= (!empty($_POST["apellido2"])) ? $_POST["apellido2"] : ' ';
	//$telefono_us1 	= (!empty($_POST["telefono"])) ? $_POST["telefono"] : null;
	//$direccion_us1 	= (!empty($_POST["direccion"])) ? $_POST["direccion"] : null;
	//$mail_us1 	= (!empty($_POST["email"])) ? $_POST["email"] : null;	// Correo electronico del remitente
	//$descripcion 	= (!empty($_POST["asunto"])) ? $_POST["asunto"] : null;
	$dptoCodi	= (!empty($_POST["departamento"]["depto_codi"])) ?
					$_POST["departamento"]["depto_codi"] : null;
	$muniCodi 	= (!empty($_POST["municipio"]["depto_codi"])) ? $_POST["municipio"]["codigo"] : null;
	
	// Asignado documento del usuario que llega por post para consulta
	if (!empty($usuaDoc)) {
		$cc_documento_us1 = $usuaDoc;
	} else {
		echo "Error no existe usuario de carga";
		exit();
	}
//	var_dump($cc_documento_us1);
	//$grbNombresUs1 = "CMAURICIO PARRA ROMERO";
	$dptoCodi       = 11;
	$muniCodi       = 1;
	$sqlCiudadano	= "SELECT SGD_CIU_CODIGO,
					SGD_CIU_NOMBRE
				FROM SGD_CIU_CIUDADANO
				WHERE SGD_CIU_CEDULA = '$cc_documento_us1'";
//	var_dump($sqlCiudadano);
	// Colombia
	$idPais 	= 170;
	$dptoCodi 	= 11;
	$muniCodi 	= 1;
	// D.C.
	$codep_us1 	= "170-11";		// Pais-departamento
	// Bogota
	$muni_us1 	= "170-11-1";		// Pais-departamento-municipio
	$direccion_us1	= "SUPERSERVICIOS";	// Direccion del destinatario
	$_SESSION['dependencia'] = $depDestino;
	$_SESSION['usua_doc'] 	= $usuaDestino;
	$_SESSION['codusuario'] = 1;
	$_SESSION['nivelus'] 	= 5;
	global $krd;
	$krd = "JEFE";
	$krd = $usuaLogin;
	include_once($ruta_raiz . "/include/db/ConnectionHandler.php");
	require_once($pearLib 	. "HTML/Template/IT.php");
	require_once($ruta_raiz . "/include/tx/Radicacion.php");
	require_once($ruta_raiz . "/class_control/Municipio.php");
	require_once($ruta_raiz . "/include/tx/Historico.php");
	//include_once($ruta_raiz . "/radicacion/buscar_usuario.php");
	
	$tpl 		= new HTML_Template_IT($ruta_raiz . "/tpl");
	$unMunicipio 	= new Municipio($db);
	$db 		= new ConnectionHandler("$ruta_raiz");
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	$rsCiudadano 	= $db->conn->Execute($sqlCiudadano);
	
	if (!$rsCiudadano->EOF){
		$documento_us1 = $rsCiudadano->Fields("SGD_CIU_CODIGO");
		$grbNombresUs1 = $rsCiudadano->Fields("SGD_CIU_NPMBRE");
		$rsCiudadano->Close();
	}
	
	// Proceso de busqueda del ciudadano
	// Tipo de radicacion
	$tipoRadicado 		= 3;
	$rad->radiUsuaActu 	= $usuaDestino;
	$rad->radiDepeActu 	= $depDestino;
	
	// Creando el radicado
	$rad = new Radicacion($db);
	$rad->radiTipoDeri 	= $tpRadicado;
	$rad->radiCuentai	= "null";
	$rad->eespCodi 		= "null";	//$documento_us3;
	$rad->mrecCodi 		= 1;		//"dd/mm/aaaa"
	$fecha_gen_doc_YMD 	= substr($fecha_gen_doc,6 ,4)."-".
					substr($fecha_gen_doc,3 ,2)."-".
					substr($fecha_gen_doc,0 ,2);
	$rad->radiFechOfic 	= date("d/m/Y");//$fecha_gen_doc_YMD;
	$rad->radiNumeDeri 	= "null";	//trim($radicadopadre);
	$rad->radiPais 		= 170;		//$tmp_mun->get_pais_codi();
	$rad->descAnex 		= ""; 		// Descripcion del anexo
	$rad->raAsun 		= $textoViaWeb; // Asunto del radicado
	$rad->radiDepeActu 	= $depDestino;	// $coddepe;
	$rad->radiDepeRadi 	= $depDestino;	// $coddepe;
	$rad->radiUsuaActu 	= $usuaCodi;	// $radi_usua_actu;
	$rad->trteCodi 		= 0;		// $tip_rem;
	$rad->tdocCodi 		= 0;		// Tipo documeental del radicado
	$rad->tdidCodi 		= 0;		// $tip_doc;
	$rad->carpCodi 		= 0;		// $carp_codi;
	$rad->carPer 		= "null";	// $carp_per;
	$rad->trteCodi 		= 0;		// $tip_rem;
	$rad->radiPath 		= 'null';	// Ruta del radicado
	$rad->sgd_apli_codi 	= 0;		// Por defecto aplicaciones integradas Cero
	$codTx 			= 2;
	$flag 			= 1;
	
	// Realizando radicacion
	$noRad 			= $rad->newRadicado($tipoRadicado,$depDestino);
	if ($noRad=="-1") {
		die("<hr>
			<b><font color=red><center>
			Error no genero un Numero de Secuencia o Inserto el radicado<br>
			SQL </center>
			</font>
			</b>
			<hr>");
	}
	
	if(!empty($noRad) && $noRad!="-1") {
		$radPathPdf = "/" . substr($noRad, 0, 4) .
				"/" . substr($noRad, 4, 3) .
				"/". $noRad . ".csv";
		$sql = "UPDATE RADICADO SET /*ID_PAIS = '$idPais',*/
					MUNI_CODI = '$muniCodi',
					DPTO_CODI ='$dptoCodi'
				WHERE RADI_NUME_RADI = $noRad";
		$db->query($sql);
	}
	
	$radicadosSel[0] = $noRad;
	$dependencia 	= $depDestino;
	$codusuario 	= $usuaCodi;
	$coddepe 	= $depDestino;
	$radi_usua_actu = $usuaCodi;
	$observacion 	= $textoViaWeb;
	$hist 		= new Historico($db);
	$hist->insertarHistorico($radicadosSel,
					$dependencia,
					$codusuario,
					$coddepe,
					$radi_usua_actu,
					$observacion,
					$codTx);
	$conexion 	= $db;
	$flagOperacion = true;
	
	// Si actualizo o inserto el usuario en la tabla de sgd_ciu_ciudadano entonces realice la radicacion
	if ($flagOperacion) {
		//if(true) {
		//	include_once($ruta_raiz . "/radicacion/grb_direcciones.php");
		//}
		// Si ya posee un radicado entonces inserta las direcciones
		if(!empty($noRad) && $noRad !="-1"){
			$nurad = $noRad;
			$cc = false;
			include_once($ruta_raiz . "/radicacion/grb_direcciones.php");
		}
	}
	
	if ($generoCsv) {
		$sqlRad = $rad->updateRadicado($noRad,$radPathPdf);
	}
?>
