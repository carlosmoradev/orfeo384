<?
session_start();
/**
 * Modulo de Formularios Web para atencion a Ciudadanos.
 * @autor Sebastian Ortiz
 * @fecha 2012/06
 *
 */
foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

define('ADODB_ASSOC_CASE', 1);

$ruta_raiz = "..";
$ADODB_COUNTRECS = false;
require_once("$ruta_raiz/include/db/ConnectionHandler.php");
include "../config.php";
$_SESSION["depeRadicaFormularioWeb"]=$depeRadicaFormularioWeb;  // Es radicado en la Dependencia 900
$_SESSION["usuaRecibeWeb"]=$usuaRecibeWeb; // Usuario que Recibe los Documentos Web
$_SESSION["secRadicaFormularioWeb"]=$secRadicaFormularioWeb; // Osea que usa la Secuencia sec_tp2_900
$_SESSION["idFormulario"] = sha1(microtime(true).mt_rand(10000,90000));
$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

include('./funciones.php');
include('./formulario_sql.php');
include('./captcha/simple-php-captcha.php');
$_SESSION['captcha_formulario'] = captcha();

//TamaNo mAximo del todos los archivos en bytes 10MB = 10(MB)*1024(KB)*1024(B) =  10485760 bytes
$max_file_size  = 10485760;

if(!isset($isFacebook)){
	$isFacebook = 0;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<title>:: <?=$entidad_largo ?>:: Formulario PQRS</title>

<!-- Meta Tags -->
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<!--Deshabilitar modo de compatiblidad de Internet Explorer-->
<meta http-equiv="X-UA-Compatible" content="IE=edge"><!-- CSS -->
<link rel="stylesheet" href="css/structure2.css" type="text/css" />
<link rel="stylesheet" href="css/form.css" type="text/css" />
<link rel="stylesheet" href="css/fineuploader.css" type="text/css" />

<!-- JavaScript --> <script type="text/javascript"
	src="scripts/wufoo.js"></script> <!-- prototype --> <script
	type="text/javascript" src="prototype.js"></script> <!-- jQuery --> <script
	src="scripts/jquery.js"></script> <!-- FineUploader --> <script
	type="text/javascript" src="scripts/jquery.fineuploader-3.0.js"></script>
<!--funciones--> <script type="text/javascript" src="ajax.js"></script>
<script>
    window.onload = createUploader;
</script>

</head>

<body id="public">

<div id="container">

<h1>&nbsp;</h1>

<form id="contactoOrfeo" class="wufoo topLabel" autocomplete="on"
	enctype="multipart/form-data" method="post" action="formulariotx.php"
	name="quejas">

<div class="info">
<center><img src='../logoEntidad.png'></center>
<p><br> Apreciado ciudadano: </br>
&nbsp <br>Al diligenciar el formulario, tenga en cuenta lo siguiente: </br>

<br>En cualquier caso su requerimiento puede realizarse de manera
anónima o identificada. Si usted opta por presentar su comunicación en
forma anónima, no será posible que reciba de manera directa respuesta
por parte de este Ministerio. Los campos con (<font color="#FF0000">*</font>
) son obligatorios. </br>

</p>
</div>

<ul>
	<tr>
		<td>
		<li id="li_tipoSolicitud"><label class="desc" id="title_tipoSolicitud" for="tipoSolicitud">Tipo
		de petición <font color="#FF0000">*</font></label>
		<div><select id="tipoSolicitud" name="tipoSolicitud"
			class="field select maximun" tabindex="1">
			<option value="0" selected="selected">Seleccione</option>
			<?=$tipo; ?>
			<!-- 
		<option value="1">Petici&oacute;n</option>
		<option value="2">Queja</option>
		<option value="3">Reclamo</option>
		<option value="4">Sugerencia</option>
		-->
		</select> &nbsp;</div>
		</li>
		</td>
		<td>
		<li id="li_anonimo"><label class="desc" id="title_Anonimo" for="anonimo">¿Desea que su
		petición sea anónima?<font color="#FF0000">*</font></label>
		<div><select id="chkAnonimo" name="anonimo" tabindex="2"
			onChange="if (checkAnonimo()){document.getElementById('campo_asunto').focus(); alert('Si usted opta por presentar su comunicación en forma anónima, no será posible que reciba de manera directa respuesta por parte de este Ministerio.')}else{document.getElementById('tipoDocumento').focus()};">
			<option value=0 selected="selected">No</option>
			<option value=1>Sí</option>
		</select> &nbsp;</div>

		</li>
		</td>
	</tr>

	<li id="li_tipoDocumento"><label class="desc" id="title_tipoDocumento"
		for="tipoDocumento">Tipo de documento <font color="#FF0000">*</font></label>
	<div><select id="tipoDocumento" name="tipoDocumento"
		class="field select maximun" tabindex="3">
		<option value="0" selected="selected">Seleccione</option>
		<option value="1">C&eacute;dula de ciudadan&iacute;a</option>
		<?php  //No cambiar el valor 5 de NIT porque se valida  formulariotx.php para guardarlo en empresa ?>
		<option value="5">NIT</option>
		<option value="3">C&eacute;dula extranjer&iacute;a</option>
		<option value="2">Tarjeta de identidad</option>
		<option value="6">NUIP</option>
		<option value="4">Pasaporte</option>
	</select> &nbsp;</div>
	</li>

	<li id="li_numeroDocumento" class="   "><label class="desc" id="lbl_numid"
		for="campo_numid">N&uacute;mero de identificaci&oacute;n (solo
	n&uacute;meros o letras) <font color="#FF0000">*</font></label>
	<div><input id="campo_numid" name="numid" type="text"
		class="field text medium" value="" maxlength="11" tabindex="4"
		onkeypress="return alpha(event,numbers+letters)" /> &nbsp;</div>
	</li>

	<li id="li_nombre"><label class="desc" id="title_Nombre" for="campo_nombre"> Nombre
	del remitente o raz&oacute;n social <font color="#FF0000">*</font> </label>
	<div><input id="campo_nombre" name="nombre_remitente" type="text"
		class="field text" value="" size="20" tabindex="5"
		onkeypressS="return alpha(event,letters);" /></div>
	</li>

	<li id="li_apellido"><label for="campo_apellido" id="lbl_apellido" class="desc">Apellidos o tipo de
	empresa <font color="#FF0000">*</font></label>
	<div><input id="campo_apellido" name="apellidos_remitente" type="text"
		class="field text" value="" size="20" tabindex="6"
		onkeypress="return alpha(event,letters);" /></div></span></li>

	<li id="li_pais" class="   "><label class="desc" id="lbl_pais"
		for="label"> País <font color="#FF0000">*</font> </label>
	<div><select id="slc_pais" name="pais" class="field select medium"
		tabindex="7" onchange="cambia_pais()">
		<?=$pais ?>
	</select> &nbsp;<font color="#FF0000"></font></div>
	</li>

	<li id="li_departamento" class="   "><label class="desc" id="lbl_deptop"
		for="label"> Departamento <font color="#FF0000">*</font> </label>
	<div><select id="slc_depto" name="depto" class="field select medium"
		tabindex="8" onchange="trae_municipio()">
		<option value="0" selected="selected">Seleccione</option>
		<?=$depto ?>
	</select> &nbsp;<font color="#FF0000"></font></div>
	</li>
	<li id="li_municipio" class="   "><label class="desc" id="lbl_municipio"
		for="label2"> Municipio <font color="#FF0000">*</font> </label>
	<div id="div-contenidos"><select id="slc_municipio" name="muni"
		class="field select medium" tabindex="9">
		<option value="0" selected="selected">Seleccione..</option>
	</select> &nbsp;<font color="#FF0000"></font></div>
	</li>

	<li id="li_direccion"> <label class="desc" id="lbl_direccion" for="campo_direccion">Direcci&oacute;n
	</label>
	<div><input id="campo_direccion" name="direccion" type="text"
		class="field text medium" value="" maxlength="150" tabindex="10"
		onkeypress="return alpha(event,numbers+letters+signs+custom)" />
	&nbsp;</div>
	</li>

	<li id="li_telefono"><label class="desc" id="lbl_telefono" for="campo_telefono">Tel&eacute;fono
	<font color="#FF0000">*</font></label>
	<div><input id="campo_telefono" name="telefono" type="text"
		class="field text medium" value="" maxlength="50" tabindex="11"
		onkeypress="return alpha(event,numbers+alpha)" /> &nbsp;</div>
	</li>

	<li id="li_email"><label class="desc" id="lbl_email" for="campo_email"> E-mail <font
		color="#FF0000">*</font></label>
	<div><input id="campo_email" name="email" type="text"
		class="field text medium" value="" maxlength="50" tabindex="12"></div>
	</li>


	<!-- 
		<li><label class="desc" id="lbl_tema"
		for="campo_tema">Tema </label>
	<div><select id="campo_tema" name="tema"
		class="field select maximun" tabindex="11">
		<option value="0" selected="selected">Seleccione</option>
		<option value="1">Acerca del Ministerio</option>
		<option value="2">correlibre</option>
		<option value="3">Protecci&oacute;n Social</option>
		<option value="4">Atenci&oacute;n al Ciudadano</option>
		<option value="5">Centro de Comunicaciones</option>
	</select> &nbsp;<font color="#FF0000">*</font></div>
	</li>
	&nbsp;
	-->

<!--	<li id="li_medioRespuesta"><label class="desc" id="lbl_mediorespuesta"-->
<!--		for="campo_mediorespuesta">Seleccione un medio por el cual se le-->
<!--	dar&aacute; respuesta <font color="#FF0000">*</font> </label>-->
<!--	<div><select id="campo_mediorespuesta" name="mediorespuesta"-->
<!--		class="field select maximun" tabindex="13">-->
<!--		<option value="0" selected="selected">Correo Electr&oacute;nico</option>-->
<!--		<option value="1">Correo Postal</option>-->
<!---->
<!--	</select> &nbsp;<font color="#FF0000"></font></div>-->
<!--	</li>-->

	<li id="li_tipoPoblacion"> <label class="desc" id="title_tipoPoblacion" for="tipoPoblacion">Tipo
	de poblaci&oacute;n </label>
	<div><select id="tipoPoblacion" name="tipoPoblacion"
		class="field select maximun" tabindex="14">
		<?=$temas;?>
		<!--		<option value="0" selected="selected">No aplica</option>-->
		<!--		<option value="1">Poblaci&oacute;n Desplazada</option>-->
		<!--		<option value="2">Mujer Gestante</option>-->
		<!--		<option value="3">Ni&ntilde;os, Ni&ntilde;as, Adolescentes</option>-->
		<!--		<option value="4">Veterano Fueza P&uacute;blica</option>-->
		<!--		<option value="5">Adulto Mayor</option>-->
	</select> &nbsp;<font color="#FF0000"></font></div>
	</li>
	&nbsp;

	<li id="li_asunto" class="   "><label class="desc" id="lbl_asunto"
		for="campo_asunto">Tema de su petición<font color="#FF0000">*</font></label>
	<div><input id="campo_asunto" name="asunto" type="text"
		class="field text large" value="" maxlength="80" tabindex="15" />
	&nbsp;</div>
	</li>
	<li id="li_comentario" class="    "><label class="desc" id="lbl_comentario"
		for="campo_comentario">Comentario<font color="#FF0000">*</font></label>

	<div><textarea id="campo_comentario" name="comentario"
		class="field textarea small" rows="10" cols="50" tabindex="16"
		onkeyup="countChar(this)" defaultValue="Escriba ac&aacute; ..."></textarea>
	<input type="hidden" id="adjuntosSubidos" name="adjuntosSubidos"
		value="" /> &nbsp;</div>
	<div align="right" id="charNum"></div>
	</li>
	<!--	<div>-->
	<!--	<li id="adjuntos" class="   "><label class="desc" id="lbl_adjuntos"-->
	<!--		for="campo_adjuntos">Adjuntos(Máximo 5MB por archivo, 20MB en total)</label>-->
	<!--	 <input type="hidden" name="MAX_FILE_SIZE" value="<?php //echo $max_file_size; ?>" /> -->
	<!--	<input class="field file large" id="campo_adjuntos" name="userfile[]"-->
	<!--		type="file" onChange="addInputFile();" /></li>-->
	<!--	</div>-->

	<li id="li_upload">
	<div id="filelimit-fine-uploader"></div>
	<div id="availabeForUpload"></div>
	&nbsp;
	</li>
	
	<li id="li_imagenVerificacion"><label class="desc" id="lbl_captcha" for="campo_captcha">Imagen de
	verificaci&oacute;n (Digite en el recuadro las letras o número de la
	imagen). <font color="#FF0000">*</font></label>
	<div><input id="campo_captcha" name="captcha" type="text"
		class="field text small" value="" maxlength="5" tabindex="20"
		onkeypress="return alpha(event,numbers+letters)"
		alt="Digite las letras y n&uacute;meros de la im&aacute;gen" /> &nbsp;
	<p><?php
	echo '<img id="imgcaptcha" src="' . $_SESSION['captcha_formulario']['image_src'] . '" alt="CAPTCHA" /><br>';
	echo '<a href="#" onClick="return reloadImg(\'imgcaptcha\');">Cambiar imagen<a>'
        	
    ?></p>
    <input type="hidden" name="pqrsFacebook" value="<?=$isFacebook?>" />
    <input type="hidden" name="idFormulario" value="<?=$_SESSION["idFormulario"]?>" />
	</div>
	</li>

	<li id="li_botones" class="buttons"><input id="saveForm" type="submit" value="Enviar"
		onclick="return valida_form();" /> <input name="button" type="button"
		id="button" onclick="window.close();" value="Cancelar" /></li>

	<!--	<li style="display: none"><label for="comment">No llene-->
	<!--	esto</label> <textarea name="comentario" id="comment" rows="1" cols="1"></textarea>-->
	<!--	</li>-->
</ul>
</form>

</div>
<!--container-->

</body>
</html>
