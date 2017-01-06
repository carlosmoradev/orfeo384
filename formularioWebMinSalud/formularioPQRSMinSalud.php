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
$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

include('./funciones.php');
include('./formulario_sql.php');
include('./captcha/simple-php-captcha.php');
$_SESSION['captcha_formulario'] = captcha();

//TamaNo mAximo del todos los archivos en bytes 20MB = 20(MB)*1024(KB)*1024(B) =  20971520 bytes
$max_file_size  = 20971520;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<title>:: <?=$entidad_largo ?>:: Formulario PQRS</title>

<!-- Meta Tags -->
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" >

<!-- CSS -->
<link rel="stylesheet" href="css/structure2.css" type="text/css" />
<link rel="stylesheet" href="css/form.css" type="text/css" />

<!-- JavaScript -->

<script type="text/javascript" src="scripts/wufoo.js"></script>
<!-- prototype -->
<script type="text/javascript" src="prototype.js"></script>
<!--funciones-->
<script type="text/javascript" src="ajax.js"></script>

</head>

<body id="public">

<div id="container">

<h1>&nbsp;</h1>

<form id="contactoOrfeo" class="wufoo topLabel" autocomplete="on"
	enctype="multipart/form-data" method="post" action="formulariotx.php"
	name="quejas">

<div class="info">
<center><img src='../logoEntidad.png'></center>
<h4><?=$db->entidad_largo?></h4>
<p>
<br>
Se&ntilde;or Ciudadano al diligenciar el formulario, tenga en cuenta lo siguiente:
</br><br>
<br>
En cualquier caso su requerimiento puede realizarse de manera anónima o
identificada. Si usted opta por presentar su comunicación en forma Anónima, no será posible que reciba de manera directa respuesta por parte de éste Ministerio. 
Los campos tipo de petición, país, asunto, comentario y
codigo de verificaci&oacute;n son obligatorios (<font color="#FF0000">*</font>).
</br>
</p></div>

<ul>
<tr><td>
	<li><label class="desc" id="title_tipoSolicitud"
		for="tipoSolicitud">Tipo de Petición <font color="#FF0000">*</font></label>
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
	</select> &nbsp;<font color="#FF0000">*</font></div>
	</li>
	</td>
	<td>
		<li><label class="desc" id="title_Anonimo"
		for="anonimo">¿Desea que su petición sea anónima?<font color="#FF0000">*</font></label>
	<div><select id="chkAnonimo" name="anonimo"
	 tabindex="2" onChange="if (checkAnonimo()){document.getElementById('campo_asunto').focus(); alert('Señor Usuario, si usted opta por presentar su comunicación en forma Anónima, no será posible que reciba de manera directa respuesta por parte de éste Ministerio.')}else{document.getElementById('tipoDocumento').focus()};">
	 <option value=0 selected="selected">No</option>
	 <option value=1 >Sí</option> </select>
	  &nbsp;</div>
	 
	</li>
	</td>
	</tr>
	&nbsp;

	<li id="li_tipoDocumento"><label class="desc" id="title_tipoDocumento"
		for="tipoDocumento">Tipo de Documento <font color="#FF0000">*</font></label>
	<div><select id="tipoDocumento" name="tipoDocumento"
		class="field select maximun" tabindex="2">
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
	&nbsp;

	<li id="foli3" class="   "><label class="desc" id="lbl_numid"
		for="campo_numid">N&uacute;mero de Identificaci&oacute;n
	(s&oacute;lo n&uacute;meros o letras) <font color="#FF0000">*</font></label>
	<div><input id="campo_numid" name="numid" type="text"
		class="field text medium" value="" maxlength="11" tabindex="3"
		onkeypress="return alpha(event,numbers+letters)" /> &nbsp;</div>
	</li>

	<li><label class="desc" id="title_Nombre" for="campo_nombre">
	Nombre De Remitente o Raz&oacute;n social <font
		color="#FF0000">*</font> </label> <div> <input
		id="campo_nombre" name="nombre_remitente" type="text"
		class="field text" value="" size="20" tabindex="4"
		onkeypressS="return alpha(event,letters);" /> </div></li>
		
		<li><label for="campo_apellido" class="desc">Apellidos o Tipo De Empresa <font
		color="#FF0000">*</font></label><div><input
		id="campo_apellido" name="apellidos_remitente" type="text"
		class="field text" value="" size="20" tabindex="5"
		onkeypress="return alpha(event,letters);" /> </div> &nbsp; <font
		color="#FF0000"></font> 
	</span></li>

	<li><label class="desc" id="lbl_direccion" for="campo_direccion">Direcci&oacute;n <font
		color="#FF0000">*</font></label>
	<div><input id="campo_direccion" name="direccion" type="text"
		class="field text medium" value="" maxlength="150" tabindex="6"
		onkeypress="return alpha(event,numbers+letters+signs+custom)" />
	&nbsp;</div>
	</li>

	<li><label class="desc" id="lbl_telefono" for="campo_telefono">Tel&eacute;fono (S&oacute;lo n&uacute;meros)<font
		color="#FF0000">*</font></label>
	<div><input id="campo_telefono" name="telefono" type="text"
		class="field text medium" value="" maxlength="50" tabindex="7"
		onkeypress="return alpha(event,numbers)" /> &nbsp;</div>
	</li>

	<li><label class="desc" id="lbl_email" for="campo_email">
	E-mail <font
		color="#FF0000">*</font></label>
	<div><input id="campo_email" name="email" type="text"
		class="field text medium" value="" maxlength="50" tabindex="8"></div>
	</li>

	<li id="foli112" class="   "><label class="desc" id="lbl_pais"
		for="label"> Pais <font
		color="#FF0000">*</font> </label>
	<div><select id="slc_pais" name="pais"
		class="field select medium" tabindex="9" onchange="cambia_pais()">		
		<?=$pais ?>
	</select> &nbsp;<font color="#FF0000"></font></div>
	</li>

	<li id="foli112" class="   "><label class="desc" id="lbl_deptop"
		for="label"> Departamento <font
		color="#FF0000">*</font> </label>
	<div><select id="slc_depto" name="depto"
		class="field select medium" tabindex="9" onchange="trae_municipio()">
		<option value="0" selected="selected">Seleccione</option>
		<?=$depto ?>
	</select> &nbsp;<font color="#FF0000"></font></div>
	</li>
	<li id="foli112" class="   "><label class="desc"
		id="lbl_municipio" for="label2"> Municipio <font
		color="#FF0000">*</font> </label>
	<div id="div-contenidos"><select id="slc_municipio" name="muni"
		class="field select medium" tabindex="10">
		<option value="0" selected="selected">Seleccione..</option>
	</select> &nbsp;<font color="#FF0000"></font></div>
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

		<li><label class="desc" id="lbl_mediorespuesta"
		for="campo_mediorespuesta">Seleccione un medio por el cual se le dar&aacute; respuesta <font
		color="#FF0000">*</font> </label>
	<div><select id="campo_mediorespuesta" name="mediorespuesta"
		class="field select maximun" tabindex="11">
		<option value="0" selected="selected" >Correo Electr&oacute;nico</option>
		<option value="1">Correo Postal</option>
		
	</select> &nbsp;<font color="#FF0000"></font></div>
	</li>
	&nbsp;

	
	<li><label class="desc" id="title_tipoPoblacion"
		for="tipoPoblacion">Tipo de Poblaci&oacute;n </label>
	<div><select id="tipoPoblacion" name="tipoPoblacion"
		class="field select maximun" tabindex="12">
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
	
	<li id="foli4" class="   "><label class="desc" id="lbl_asunto"
		for="campo_asunto">Asunto<font color="#FF0000">*</font></label>
	<div><input id="campo_asunto" name="asunto" type="text"
		class="field text large" value="" maxlength="255" tabindex="13" />
	&nbsp;</div>
	</li>
	<li id="foli111" class="    "><label class="desc" id="lbl_comentario"
		for="campo_comentario">Comentario<font
		color="#FF0000">*</font></label>

	<div><textarea id="campo_comentario" name="comentario" class="field textarea small"
		rows="10" cols="50" tabindex="14"   onkeyup="countChar(this)" defaultValue="Escriba ac&aacute; ..."></textarea> &nbsp;</div>
		<div align="right" id="charNum">
	</div>
	</li>
	&nbsp;
	
	<div>
		<li id="adjuntos" class="   "><label class="desc" id="lbl_adjuntos"
		for="campo_adjuntos">Adjuntos(Máximo 5MB por archivo, 20MB en total)</label>
		<!-- <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_file_size; ?>" /> --> 
		<input class="field file large" id="campo_adjuntos" name="userfile[]" type="file" onChange="addInputFile();" />	
		</li>
	</div>
		

	<li><label class="desc" id="lbl_captcha" for="campo_captcha">Im&aacute;gen de verificaci&oacute;n (Digite las letras o  números de la imágen en el recuadro). <font color="#FF0000">*</font></label>
	<div><input id="campo_captcha" name="captcha" type="text"
		class="field text small" value="" maxlength="5" tabindex="20"
		onkeypress="return alpha(event,numbers+letters)" alt="Digite las letras y n&uacute;meros de la im&aacute;gen" />
	&nbsp;<p>
	<?php
        	echo '<img id="imgcaptcha" src="' . $_SESSION['captcha_formulario']['image_src'] . '" alt="CAPTCHA" /><br>';
        	echo '<a href="#" onClick="return reloadImg(\'imgcaptcha\');">Cambiar im&aacute;gen<a>'
        	
    ?>
	</p> </div>
	</li>
	
		<li class="buttons"><input id="saveForm" type="submit"
		value="Enviar" onclick="return valida_form();" /> <input
		name="button" type="button" id="button" onclick="window.close();"
		value="Cancelar" /></li>

<!--	<li style="display: none"><label for="comment">No llene-->
<!--	esto</label> <textarea name="comentario" id="comment" rows="1" cols="1"></textarea>-->
<!--	</li>-->
</ul>
</form>

</div>
<!--container-->

</body>
</html>
