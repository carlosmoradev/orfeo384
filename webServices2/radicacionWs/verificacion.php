<?php
	define('ADODB_ASSOC_CASE', 0);
	$ruta_raiz 	= "../..";
	$pearLib 	= $ruta_raiz . "/pear/";
	include_once($ruta_raiz . "/include/db/ConnectionHandler.php");
	include_once($ruta_raiz . "/captcha/captcha.class.php");
	require_once($pearLib . "HTML/Template/IT.php");
	//asginacion de variables
	$arregloTipo = array();
	$arregloTipo["Q"] = "QUEJA_CHECKED";
	$arregloTipo["R"] = "RECLAMO_CHECKED";
	$arregloTipo["S"] = "SUGERENCIA_CHECKED";
	// Cedula del usuario que remite la queja o sugerencia
	$cc_documento_us1 = (!empty($_POST["nit"])) ? $_POST["nit"] : null;
	$nombre_us1 	= (!empty($_POST["nombre"])) ? $_POST["nombre"] : null;
	$prim_apel_us1 	= (!empty($_POST["apellido"])) ? $_POST["apellido"] : null;
	$seg_apel_us1 	= (!empty($_POST["apellido2"])) ? $_POST["apellido2"] : ' ';
	$telefono_us1 	= (!empty($_POST["telefono"])) ? $_POST["telefono"] : null;
	$direccion_us1 	= (!empty($_POST["direccion"])) ? $_POST["direccion"] : null;
	$mail_us1 	= (!empty($_POST["email"])) ? $_POST["email"] : null;	// Correo electronico del remitente
	$descripcion 	= (!empty($_POST["asunto"])) ? $_POST["asunto"] : null;
	$formRadInicio 	= (!empty($_POST["formRadinicio"])) ? $_POST["formRadinicio"] : null;
	// Configuracion de Captcha
	// Arreglo de configuracion
	$CAPTCHA_INIT = array(
		// URL para mostrar las imagenes
		'urlFolderImg'	=> 'http://172.16.0.147:81/~cmauricio/captcha/tmp/',
		// Cadena: contiene la ruta absoluta donde va trabajar captcha
		'tempfolder'	=> '/home/orfeodev/cmauricio/public_html/captcha/tmp/',      
		// string: absolute path (with trailing slash!) to folder which contains your TrueType-Fontfiles.
		// mixed (array or string): basename(s) of TrueType-Fontfiles
		'TTF_folder'	=> "/home/orfeodev/cmauricio/public_html/captcha/ttf/", 
		//'TTF_RANGE'   => array('COMIC.TTF','JACOBITE.TTF','LYDIAN.TTF','MREARL.TTF','RUBBERSTAMP.TTF','ZINJARON.TTF'),
		'TTF_RANGE'	=> array('arial.ttf'),
		'chars'		=> 5,	// integer: number of chars to use for ID
		'minsize'	=> 20,	// integer: minimal size of chars
		'maxsize'	=> 30,	// integer: maximal size of chars
		// integer: define the maximal angle for char-rotation, good results are between 0 and 30
		'maxrotation'	=> 25,      	
		'noise'		=> true,	// boolean: TRUE = noisy chars | FALSE = grid
		'websafecolors'	=> false,	// boolean
		'refreshlink'	=> false,	// boolean
		'lang'		=> 'sp',	// string:  ['en','de','sp']
		'maxtry'	=> 3,		// integer: [1-9]
		'badguys_url'	=> '/',	// string: URL
		'secretstring'	=> 'A very, very secret string which is used to generate a md5-key!',
		'secretposition'=> 15,		// integer: [1-32]
		'debug'		=> false);
	$pathEstilos 	= "../css/";
	$estilo 	= "estilosQrs.css";
	$tituloPagina 	= "Formulario Qrs";
	$archivoExec	= "index.php";
	$archivoExec	= "verificacion.php";
	$estilosRadicacion = $pathEstilos . $estilo;
	$paginaDeInicio = "http://www.superservicios.gov.co/superservicios1/energas/qrs_Aviso2.html";
	$paginaDeInicio = "http://www.superservicios.gov.co/superservicios1/QQRRSS2.htm";
	$ipServidor 	= "172.16.0.147:81";
	$archivoExec	= "http://$ipServidor/~jgonzal/br_3.6.0/radicacionWeb/radicacionQrs/verificacion.php";
	$paginaDeInicio = "http://www.google.com.co/";
	$captcha	=& new hn_captcha($CAPTCHA_INIT);
	$captchaImg	= $captcha->display_form();	
	$db 		= new ConnectionHandler("$ruta_raiz");
	$tpl 		= new HTML_Template_IT($ruta_raiz . "/tpl");
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	$comenzarRad 	= (!empty($HTTP_POST_VARS["radiQrs"])) ? $HTTP_POST_VARS["radiQrs"] : null;
	// Capturando municipios del departamento para construir select 
	// si tiene un departamento asignado
	if ($formRadInicio == true) {
		$formRadInicio = false;
		include("./mostrarFormRadicacion.php");
	} else {
		switch($captcha->validate_submit()){
			case 1:
				// fue enviado y validacion fue exitosa
				// mostrar radicacion
				include("./radicarSugerencia.php");
				break;
			case 2:
				// fue enviado pero no correpondia el valor generado con respecto a la imagen
				// Mostrar de nuevo formulario de radicacion
				include("./mostrarFormRadicacion.php");
				break;
			case 3:
				//include_once("./mostrarFormRadicacion.php");
				$maximoIntentos = $captcha->maxtry;
				include("./mostrarFormRadicacion.php");
				break;
			default:
				include("./mostrarFormRadicacion.php");
		}
	}
?>
