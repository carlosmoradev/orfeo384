<?php
session_start();
extract($_GET);
extract($_POST);
foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;
/**
  *Pagina Inicio para Consulta Web
	*@autor Jairo Losada - SuperSolidaria
	*@fecha 2009/06
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
.Estilo1 {color: #999999}
-->
</style>
<script>
function loginTrue()
{
	document.formulario.submit();
}
</script>
</head>

<body>
<br>
<?php
error_reporting(7);
define('ADODB_ASSOC_CASE', 1);
$ruta_raiz = "..";
if(session_id()) 
session_destroy();
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
session_start();
$fechah = date("dmy") . "_" . time("hms");
$usua_nuevo=3;
if ($numeroExpediente)
{
	$numeroExpediente = str_replace("-","",$numeroExpediente);
	$numeroExpediente = str_replace("_","",$numeroExpediente);
	$numeroExpediente = str_replace(".","",$numeroExpediente);
	$numeroExpediente = str_replace(",","",$numeroExpediente);
	$numeroExpediente = str_replace(" ","",$numeroExpediente);
	//include "$ruta_raiz/expediente/lista_expedienteWeb.php";
//.....................Consulta de usuario para verificacion de password........................//
$queryRec = "AND (USUA_PASW ='". SUBSTR(md5($drd),1,26) ."' or USUA_NUEVO='0')";
$krdx=strtoupper($krdx);
$query = "select a.*, 
			b.DEPE_NOMB,
			a.USUA_ESTA,
			a.USUA_CODI,
			a.USUA_LOGIN,
			b.DEPE_CODI_TERRITORIAL,
			b.DEPE_CODI_PADRE,
			a.USUA_PERM_ENVIOS,
			a.USUA_PERM_MODIFICA,
			a.USUA_PERM_EXPEDIENTE,
			a.USUA_EMAIL,
			a.USUA_AUTH_LDAP
			$queryTRad
			$queryDepeRad
		from usuario a, DEPENDENCIA b
		where USUA_LOGIN ='$krdx' and  a.depe_codi=b.depe_codi $queryRec";

/** Procedimiento forech que encuentra los numeros de secuencia para las radiciones
*	 @param tpDepeRad[]	array 	Muestra las dependencias que contienen las secuencias para radicion.
*/
$varQuery = $query;
$comentarioDev = ' Busca Permisos de Usuarios ...';
//include "$ruta_raiz/include/tx/ComentarioTx.php";
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$rs = $db->query($query);
//Si no se autentica por LDAP según los permisos de DB
$flag=0; //.....se activa si la validacion es exitosa..............................

if (!0)
{
    //echo $query;
	//Verificamos que la consulta en DB haya sido exitosa con el password digitado
	if(trim($rs->fields["USUA_LOGIN"])==$krdx)
	{
		$validacionUsuario = '';
		$flag=1;
		include "$ruta_raiz/expediente/lista_expedienteWeb.php";
	}
	else
	{	//Password no concuerda con el de la DB, luego no puede ingresar
		$mensajeError = "USUARIO O CONTRASE&Ntilde;A INCORRECTOS";
		$validacionUsuario = 'No Pasa Validacion Base de Datos';
	}
}
else
{	//El usuario tiene Validación por LDAP
	$correoUsuario = $rs->fields['USUA_EMAIL'];
	//Verificamos que tenga correo en la DB, si no tiene no se puede validar por LDAP
	if ( $correoUsuario == '' )
	{	//No tiene correo, entonces error LDAP
		$validacionUsuario = 'No Tiene Correo';
		$mensajeError = "EL USUARIO NO TIENE CORREO ELECTR&Oacute;NICO REGISTRADO";
	}
	else
	{	//Tiene correo, luego lo verificamos por LDAP
		$validacionUsuario = checkldapuser( $correoUsuario, $drd, $ldapServer );
		$mensajeError = $validacionUsuario;
	}
}

	////////////////////////////////////////////////////////////
}
//	$krd = "usWeb";
$datosEnvio = "$fechah&".session_name()."=".trim(session_id())."&ard=$krdx";
if(!$numrad and $flag==0){
  ?>
<form name=formulario action='principal.php?fechah=<?=$datosEnvio?>rad=200590082051111&pasar=no&verdatos=no&idRadicado=<?=$idRadicado?>&estadosTot=<?=md5(date('Ymd'));?>'  method=post >
</form>
<form action="./index_web.php" method="post">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="100%" height="100%" align="center" valign="middle">
    <table width="584" height="440" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="584" valign="top" background="./imagenes/index_web.jpg">
					<table width="584" height="440" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td width="194" height="100" align="center"></td>
              <td width="177" height="100">&nbsp;</td>
              <td width="213" height="100">&nbsp;</td>
            </tr>
            <tr>
              <td height="150" colspan="3">&nbsp;</td>
              <td height="150">&nbsp;</td>
              <td height="150">&nbsp;</td>
            </tr>
            <tr>
              <td height="90"> </td>
              <td height="90" valign="top">

              <table border="0" cellpadding="0" cellspacing="5" align="center">
                <tr>
                  <td class="titulos2" align="center">USUARIO</td>
                  <td class="titulos2" align="center">CONTRASE&Ntilde;A</td>
                  <td class="titulos2" align="center">EXPEDIENTE</td>
                </tr>
                <tr align="left">
                  <td width="50%" align="center"><font size="3" face="Arial, Helvetica, sans-serif">
                    <input type="text" id='krdx' name="krdx" size="13" class="tex_area">
                  </font></td>
                  <td width="50%" align="center" ><b><font size="3" face="Arial, Helvetica, sans-serif">
                    <input type=password name="drd" size="13" class="tex_area">
                  </font></b> </td>
                  <td width="50%" align="center" ><input type="text" name="numeroExpediente" value="<?=$numeroExpediente?>" size="20" class="e_cajas" maxsize="14"></td>
                </tr>
                <tr>
                  <td colspan="3" height="35" align="center"><input type="submit" name="Submit" value="   Ingresar   ">
                      <input type="reset" value="Borrar" class="botones" name="reset">                  </td>
                </tr>
              </table>

                    <p>&nbsp;</p>

                 
              </div></td>
              <td height="90"><div align="right"></div></td>
            </tr>
            <tr>
              <td height="90">&nbsp;</td>
              <td height="90">&nbsp;</td>
              <td height="90">&nbsp;</td>
            </tr>
          </table></td>
        </tr>
    </table></td>
  </tr>
</table>
</form>
<?
}
?>
</body>
</html>
