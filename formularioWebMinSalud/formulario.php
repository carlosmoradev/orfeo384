<?
session_start();
/**
 * Modulo de Formularios Web para atencion a Ciudadanos.
 * @autor Carlos Barrero   carlosabc81@gmail.com SuperSolidaria
 * @fecha 2009/05
 *
 */
foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

define('ADODB_ASSOC_CASE', 1);

// $depeRadicaFormularioWeb = 900;  // Es radicado en la Dependencia 900
// $usuaRecibeWeb = 1; // Usuario que Recibe los Documentos Web
// $secRadicaFormularioWeb = 900;

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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<title>Formulario QRDP</title>

<!-- Meta Tags -->
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<!-- CSS -->
<link rel="stylesheet" href="css/structure.css" type="text/css" />
<link rel="stylesheet" href="css/form.css" type="text/css" />

<!-- JavaScript -->
<script type="text/javascript" src="scripts/wufoo.js"></script>
<!-- prototype -->
<script type="text/javascript" src="prototype.js"></script>
<!--funciones-->
<script type="text/javascript" src="ajax.js"></script>

</head>

<body id="public">

<div id="container"
	style="background-image: url(../imagenes/logoOrfeoFondo.gif);">

<h1>&nbsp;</h1>

<form id="contactoOrfeo" class="wufoo topLabel" autocomplete="on"
	enctype="multipart/form-data" method="GET" action="formulariotx.php"
	name="quejas">

<div class="info">
<center><img src='../logoEntidad.gif'></center>
<h4><?=$db->entidad_largo?></h4>
<br />
<h4>RECUERDE. Este formulario solo es para registrar <u>quejas contra
entidades supervisadas.</u></h4>
Campos requeridos ( <font color="#FF0000">*</font> )</div>

<ul>


	<li id="foli0" class="   "><label class="desc" id="title0" for="Field0">

	Nombre Remitente </label> <span> <input id="Field0"
		name="nombre_remitente" type="text" class="field text" value=""
		size="20" tabindex="1" onkeypressS="return alpha(event,letters);" /> <label
		for="Field0">Nombres</label> </span> <span> <input id="Field1"
		name="apellidos_remitente" type="text" class="field text" value=""
		size="20" tabindex="2" onkeypressS="return alpha(event,letters);" />&nbsp;<font
		color="#FF0000">*</font> <label for="Field1">Apellidos</label> </span>
	</li>


	<li id="foli3" class="   "><label class="desc" id="title3" for="Field3">Documento
	de Identificaci&oacute;n (solo numeros) </label>
	<div><input id="Field3" name="cedula" type="text"
		class="field text medium" value="" maxlength="255" tabindex="3"
		onkeypressS="return alpha(event,numbers)" /> &nbsp;<font
		color="#FF0000">*</font></div>
	</li>


	<li id="foli112" class="   "><label class="desc" id="title112"
		for="label"> Departamento</label>
	<div><select id="label" name="depto" class="field select medium"
		tabindex="19" onchange="trae_municipio()">
		<option value="0" selected="selected">Seleccione</option>
		<?=$depto ?>
	</select> &nbsp;<font color="#FF0000">*</font></div>
	</li>
	<li id="foli112" class="   "><label class="desc" id="title112"
		for="label2"> Municipio<img src="images/loading_animated2.gif"
		width="48" height="48" style="display: none" id="loader1" /></label>
	<div id="div-contenidos"><select id="label2" name="muni"
		class="field select medium" tabindex="19">
		<option value="0" selected="selected">Seleccione..</option>
	</select> &nbsp;<font color="#FF0000">*</font></div>
	</li>
	<li id="foli4" class="   "><label class="desc" id="title4" for="Field4">
	Direcci&oacute;n Remitente </label>
	<div><input id="direccion_remitente" name="direccion_remitente"
		type="text" class="field text large" value="" maxlength="255"
		tabindex="4" /> &nbsp;<font color="#FF0000">*</font></div>
	</li>


	<li id="foli4" class="   "><label class="desc" id="title4" for="label3">
	Tel&eacute;fono Remitente </label>
	<div><input id="label3" name="telefono_remitente" type="text"
		class="field text large" value="" maxlength="255" tabindex="4"></div>
	</li>
	<li id="foli4" class="   "><label class="desc" id="title4" for="label4">
	E-mail Remitente </label>
	<div><input id="label4" name="email" type="text"
		class="field text large" value="" maxlength="255" tabindex="4"></div>
	</li>
	<li id="foli4" class="   "><label class="desc" id="title4" for="label5">Nit
	Entidad o Empresa<img src="images/loading_animated2.gif" width="48"
		height="48" style="display: none" id="loader2" /> (solo numeros) </label>
	<div><input id="label5" name="nit" type="text" class="field text large"
		value="" maxlength="255" tabindex="4" onchange="trae_entidad()"
		onkeypress="return alpha(event,numbers);" /> &nbsp;<font
		color="#FF0000">*</font></div>
	<div id="div-contenidos2" style="display: none"></div>
	</li>
	<li id="foli109" class="   "><font color="#CCCCCC">___________________________________________________________________________________</font>
	<br />
	&nbsp; <label class="desc" id="title109" for="Field109">Tipo de
	Solicitud </label>
	<div><select id="tipo" name="tipo" class="field select maximun"
		tabindex="18">
		<?= $tipo ?>
	</select> &nbsp;<font color="#FF0000">*</font></div>
	</li>
	<li id="foli4" class="   "><label class="desc" id="title4" for="label7">Referente
	al Radicado No. (solo numeros, 14 d&iacute;gitos)<img
		src="images/loading_animated2.gif" width="48" height="48"
		style="display: none" id="loader3" /> </label>
	<div><input id="label7" name="radicado" type="text"
		class="field text large" value="" maxlength="255" tabindex="4"
		onchange="trae_radicado()" onkeypress="return alpha(event,numbers);" />
	</div>
	<div id="div-contenidos3" style="display: none"></div>
	</li>
	<li id="foli4" class="   "><label class="desc" id="title4" for="label6">Asunto</label>
	<div><input id="label6" name="asunto" type="text"
		class="field text large" value="" maxlength="255" tabindex="4" />
	&nbsp;<font color="#FF0000">*</font></div>
	</li>
	<li id="foli111" class="    "><label class="desc" id="title111"
		for="Field111">Descripci&oacute;n</label>

	<div><textarea id="desc" name="desc" class="field textarea small"
		rows="10" cols="50" tabindex="5"></textarea> &nbsp;<font
		color="#FF0000">*</font></div>
	</li>


	<li class="buttons"><input id="saveForm" type="submit" value="Enviar"
		onclick="return valida_form();" /> <input name="button" type="button"
		id="button" onclick="window.close();" value="Cancelar" /></li>

	<li style="display: none"><label for="comment">No llene esto</label> <textarea
		name="comment" id="comment" rows="1" cols="1"></textarea></li>
</ul>
</form>

</div>
<!--container-->

</body>
</html>
