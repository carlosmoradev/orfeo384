<?
session_start();
foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;


/**
 *Pagina Inicio para Consulta Web
 *@autor Jairo Losada - SuperSolidaria
 *@fecha 2009/06
 *Modificado
 *Sebastian Ortiz VasquezcorrelibreSocial 2012
 */
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>ORFEO : : : : Consulta web de estado de documentos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	background-color: #FFFFFF;
}
-->
</style>
<!--prototype-->
<script type="text/javascript" src="js/prototype.js"></script>
<!--funciones-->
<script type="text/javascript" src="js/orfeo.js"></script>

</head>

<body>
<br>
<?php
error_reporting(0);
define('ADODB_ASSOC_CASE', 1);
/** FORMULARIO DE LOGIN A ORFEO
 * Aqui se inicia session
 * @PHPSESID		String	Guarda la session del usuario
 * @db 					Objeto  Objeto que guarda la conexion Abierta.
 * @iTpRad				int		Numero de tipos de Radicacion
 * @$tpNumRad	array 	Arreglo que almacena los numeros de tipos de radicacion Existentes
 * @$tpDescRad	array 	Arreglo que almacena la descripcion de tipos de radicacion Existentes
 * @$tpImgRad	array 	Arreglo que almacena los iconos de tipos de radicacion Existentes
 * @query				String	Consulta SQL a ejecutar
 * @rs					Objeto	Almacena Cursor con Consulta realizada.
 * @numRegs		int		Numero de registros de una consulta
 */
$ruta_raiz = "..";
//TODO Este reinicio de la session en este punto, hace imposible validar el captcha una vez se envie el formulario. Entonces la validacion se hace solo desde el cliente.
if(session_id()) session_destroy();
include('./captcha/simple-php-captcha.php');
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
session_start();
$_SESSION['captcha'] = captcha();
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
	if($numeroRadicado==$idWeb and substr($numeroRadicado,-1)=='2')
	{
		$ValidacionWeb="Si";
		$idRadicado = $idWeb;
	}
	else
	{
		$ValidacionWeb="No";
		$mensaje = "El numero de radicado digitado no existe, el codigo de verificacion no corresponde o esta mal escrito o la imagen de verificacion no fue bien digitada.  Por favor corrijalo e intente de nuevo.";
		echo "<center><font color=red class=tpar><font color=red size=3>$mensaje</font></font>";
		echo "<script>alert('$mensaje');</script>";
	}
}
$krd = "usWeb";
$datosEnvio = "$fechah&".session_name()."=".trim(session_id())."&ard=$krd";

?>
<form name=formulario
	action='principal.php?fechah=<?=$datosEnvio?>&pasar=no&verdatos=no&idRadicado=<?=$idRadicado?>&estadosTot=<?=md5(date('Ymd'));?>'
	method=post><?
	if($ValidacionWeb=="Si")
	{
		?> <script>
loginTrue();
</script> <?
	}
	?></form>
<form action="./index_web.php" method="post" name="consultaweb" id="consultaweb"	>
<table width="100%" height="100%" border="0" cellpadding="0"
	cellspacing="0">
	<tr>
		<td width="100%" height="100%" align="center" valign="middle">
		<table width="584" height="440" border="0" cellpadding="0"
			cellspacing="0">
			<tr>
				<!-- <td width="584" valign="top" background="./imagenes/index_web.jpg"> --> 
				<td width="584" valign="top">
				<table width="584" height="440" border="0" cellpadding="0"
					cellspacing="0">
					<tr>
						<td width="194" height="100">&nbsp;</td>
						<td width="177" height="100">&nbsp;</td>
						<td width="213" height="100">&nbsp;</td>
					</tr>
					<tr>
						<td height="150">&nbsp;</td>
						<td height="150">&nbsp;</td>
						<td height="150">&nbsp;</td>
					</tr>
					<tr>
						<td height="90">&nbsp;</td>
						<td height="90"  valign="top">
						<div align="right">

						<p>
						<label>Numero de radicado</label><input type="text" name="numeroRadicado"
							value="<?=$numeroRadicado?>" size="20" maxlength="15" class="e_cajas"
							maxsize="14" onkeypress="return alpha(event,numbers);">
							<label>Codigo de verificacion</label>
							<input type="text" name="codigoverificacion" value="" maxlength="5" onkeypress="return alpha(event,letters+numbers);">
							<label>Imagen de verificacion (Sensible a mayusculas y minusculas)</label>					
						<input id="campo_captcha" name="captcha" type="text"
							class="e_cajas" value="" maxlength="5" tabindex="20"						
							alt="Digite las letras y n&uacute;meros de la im&aacute;gen" onkeypress="return alpha(event,letters+numbers);" />
						&nbsp;
						<?php
						echo '<img src="' . $_SESSION['captcha']['image_src'] . '" alt="CAPTCHA" />';							
						?></p>
						
						<p><input type="submit" name="Submit" value="   Consultar   " onClick="return validar_formulario();"></p>
						
						</div>
						</td>
						<td height="90">
						<div align="right"></div>
						</td>
					</tr>
					<tr>
						<td height="100">&nbsp;</td>
						<td height="100">&nbsp;</td>
						<td height="100">&nbsp;</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</form>
</body>
</html>
