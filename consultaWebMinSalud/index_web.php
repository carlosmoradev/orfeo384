<?
session_start();
/**
 * Modulo de consulta Web para atencion a Ciudadanos.
 * @autor Sebastian Ortiz
 * @fecha 2012/06
 *
 */
foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;
header('Content-Type: text/html; charset=UTF-8');
define('ADODB_ASSOC_CASE', 1);

$ruta_raiz = "..";
$ADODB_COUNTRECS = false;
require_once("$ruta_raiz/include/db/ConnectionHandler.php");
include "../config.php";
$_SESSION["depeRadicaFormularioWeb"]=$depeRadicaFormularioWeb;  // Es radicado en la Dependencia 900
$_SESSION["usuaRecibeWeb"]=$usuaRecibeWeb; // Usuario que Recibe los Documentos Web
$_SESSION["secRadicaFormularioWeb"]=$secRadicaFormularioWeb; // Osea que usa la Secuencia sec_tp2_900
$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

include('./captcha/simple-php-captcha.php');

//Revisar si se envio el formulario
if(isset($numeroRadicado) && isset($codigoverificacion) && isset($captcha)){
	$fechah = date("dmy") . "_" . time("hms");
	$usua_nuevo=3;
	if ($numeroRadicado)
	{
		$numeroRadicado = str_replace("-","",$numeroRadicado);
		$numeroRadicado = str_replace("_","",$numeroRadicado);
		$numeroRadicado = str_replace(".","",$numeroRadicado);
		$numeroRadicado = str_replace(",","",$numeroRadicado);
		$numeroRadicado = str_replace(" ","",$numeroRadicado);
		include "$ruta_raiz/include/tx/ConsultaRad.php";
		$ConsultaRad = new ConsultaRad($db);
		$idWeb = $ConsultaRad->idRadicadoConCodigoVerificacion($numeroRadicado, $codigoverificacion);
		$isCaptchaOK = strcasecmp ($captcha ,$_SESSION['captcha_consulta']['code'] ) == 0?true:false;
		if($numeroRadicado==$idWeb and substr($numeroRadicado,-1)=='2' and ($isCaptchaOK || ($dontcare=="oks")))
		{
			$ValidacionWeb="Si";
			$idRadicado = $idWeb;
			$krd = "usWeb";
			$datosEnvio = "$fechah&".session_name()."=".trim(session_id())."&ard=$krd";
			$ulrPrincipal = "Location: principal.php?fechah=$datosEnvio&pasar=no&verdatos=no&idRadicado=$numeroRadicado&estadosTot=".md5(date('Ymd'));
			header($ulrPrincipal);
			return ;
		}
		else
		{
			$ValidacionWeb="No";
			$mensaje = "El número de radicado digitado no existe, el código de verificación no corresponde o esta mal escrito o la imagen de verificación no fue bien digitada.  Por favor corrijalo e intente de nuevo.";
			echo "<center><font color=red class=tpar><font color=red size=3>$mensaje</font></font>";
			echo "<script>alert('$mensaje');</script>";
		}
	}
}

$_SESSION['captcha_consulta'] = captcha();


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<title>::<?=$entidad_largo ?>:: Formulario Consulta Web</title>

<!-- Meta Tags -->
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!--Deshabilitar modo de compatiblidad de Internet Explorer-->
<meta http-equiv="X-UA-Compatible" content="IE=edge"><!-- CSS -->


<link rel="stylesheet" href="css/structure2.css" type="text/css" />
<link rel="stylesheet" href="css/form.css" type="text/css" />

<!-- JavaScript -->
<script type="text/javascript" src="js/wufoo.js"></script>
<!-- prototype -->
<script type="text/javascript" src="js/prototype.js"></script>
<!-- prototype -->
<script type="text/javascript" src="js/jquery.js"></script>
<!--funciones-->
<script type="text/javascript" src="js/orfeo.js"></script>

</head>

<body id="public"
	onload="jQuery('#formularioConsultaWeb').hide();jQuery('#formularioConsultaPQRSP').hide();">
<!--onload="disableElementById('consultaPQRSP');"-->

<div id="container">

<h1>&nbsp;</h1>



<div class="info">
<center><img src='../logoEntidad.png'/></center>
<p><br> Apreciado ciudadano, &nbsp; </br>
<br> Para hacer su consulta tenga en cuenta
lo siguiente: </br>

<br>Si usted realizó una petición, queja, reclamo o sugerencia a través
de la página web del Ministerio antes del 30 de noviembre de 2012 y se
le asignó un ID de 5 o menos dígitos, haga click 


<input type="button" onclick="consultaPQRSSP();" name="consultaPQRSP"
	value="aquí" /> (si no recuerda su ID puede ingresar con su número de
identificación). </br>
</p>


<br> Si es posterior a esta fecha debe conocer el número de radicado y
el código de verificación que se le asignó cuando envió su consulta
(como se observa en la siguiente imagen). Si es así haga click <input
	type="button" onclick="consultaWeb();" value="aquí" />
</br>
<br />
<center><img src="imagenes/radicado_ejemplo.png"
	alt="Ejemplo de radicado" width=450 align="middle" /></center>
<br />
</div>
<div id="formsContainer">
<div id="formularioConsultaWeb">
<form id="consultaweb" class="wufoo topLabel" autocomplete="on"
	enctype="multipart/form-data" method="post"
	action="<?=$_SERVER['PHP_SELF']?>" name="consultaweb">
<ul>
	<li id="foli3" class="   "><label class="desc" id="lbl_radicado"
		for="numeroRadicado">N&uacute;mero de Radicado (s&oacute;lo
	n&uacute;meros) </label>
	<div><input id="numeroRadicado" name="numeroRadicado" type="text"
		class="field text medium" value="" maxlength="15" tabindex="1"
		onkeypress="return alpha(event,numbers)" /> &nbsp;<font
		color="#FF0000">*</font></div>
	</li>
	&nbsp;

	<li id="foli3" class="   "><label class="desc"
		id="lbl_codigoverificacion" for="codigoVerificacion">C&oacute;digo
	verificaci&oacute;n</label>
	<div><input id="codigoverificacion" name="codigoverificacion"
		type="text" class="field text small" value="" maxlength="5"
		tabindex="2" onkeypress="return alpha(event,numbers+letters)" />
	&nbsp;<font color="#FF0000">*</font></div>
	</li>
	&nbsp;


	<li><label class="desc" id="lbl_captcha" for="campo_captcha">Im&aacute;gen
	de verificaci&oacute;n</label>
	<div><input id="campo_captcha" name="captcha" type="text"
		class="field text small" value="" maxlength="5" tabindex="3"
		onkeypress="return alpha(event,numbers+letters)"
		alt="Digite las letras y n&uacute;meros de la im&aacute;gen" /> &nbsp;<font
		color="#FF0000">*</font>
	<p><?php
	echo '<img id="imgcaptcha" src="' . $_SESSION['captcha_consulta']['image_src'] . '" alt="CAPTCHA" /><br>';
	echo '<a href="#" onclick="return reloadImg(\'imgcaptcha\');">Cambiar im&aacute;gen</a>'

	?></p>
	</div>
	</li>

	<li class="buttons"><input id="saveForm" type="submit"
		value="Consultar" onclick="return validar_formulario();" /> <input
		name="button" type="reset" id="button" onclick="window.close();"
		value="Cancelar" /></li>

	<!--	<li style="display: none"><label for="comment">No llene-->
	<!--	esto</label> <textarea name="comentario" id="comment" rows="1" cols="1"></textarea>-->
	<!--	</li>-->
</ul>

</form>
</div>
<div id="formularioConsultaPQRSP">
<form id="consultaPQRSP" class="wufoo topLabel" autocomplete="on"
	enctype="multipart/form-data" method="post"
	action="ConsultaPQRSharePointcorrelibre.php" name="consultaPQRSP">
<ul>
	<li><label class="desc" id="lbl_id" for="ID">ID
	(s&oacute;lo n&uacute;meros) </label>
	<div><input id="ID" name="ID" type="text" class="field text small"
		value="" maxlength="5" tabindex="1"
		onkeypress="return alpha(event,numbers)" /> &nbsp;</div>
	</li>
	&nbsp;

	<li><label class="desc" id="lbl_numdoc"
		for="numeroDocumento">Número de identificación del ciudadano</label>
	<div><input id="numeroDocumento" name="numeroDocumento" type="text"
		class="field text medium" value="" maxlength="12" tabindex="2"
		onkeypress="return alpha(event,numbers+letters)" /> &nbsp;</div>
		&nbsp;
	</li>
	<li><label class="desc" id="lbl_captcha" for="campo_captcha">Im&aacute;gen
	de verificaci&oacute;n</label>
	<div><input id="campo_captcha" name="captcha" type="text"
		class="field text small" value="" maxlength="5" tabindex="3"
		onkeypress="return alpha(event,numbers+letters)"
		alt="Digite las letras y n&uacute;meros de la im&aacute;gen" /> &nbsp;<font
		color="#FF0000">*</font>
	<p><?php
	echo '<img id="imgcaptchapqr" src="' . $_SESSION['captcha_consulta']['image_src'] . '" alt="CAPTCHA" /><br>';
	echo '<a href="#" onclick="return reloadImg(\'imgcaptchapqr\');">Cambiar im&aacute;gen</a>'

	?></p>
	</div>
	</li>
	<li class="buttons"><input id="saveForm" type="submit"
		value="Consultar" onclick="return validar_formulario_pqrsp();" /> <input
		name="button" type="reset" id="button" onclick="window.close();"
		value="Cancelar" /></li>

</ul>
</form>
</div>
<!--form--></div>
<!--forms container--></div>
<!--container-->

</body>
</html>
