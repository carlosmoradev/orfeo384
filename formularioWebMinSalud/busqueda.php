<?
session_start();
/**
  * Se añadio compatibilidad con variables globales en Off
  * @autor Jairo Losada 2009-05
  * @Fundacion CorreLibre.org
  * @licencia GNU/GPL V 3
  */

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;
$ruta_raiz = "..";
$ADODB_COUNTRECS = false;
require_once("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
include('formulario_sql.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!-- CSS -->
<link rel="stylesheet" href="css/structure.css" type="text/css" />
<link rel="stylesheet" href="css/form.css" type="text/css" />
<!--funciones-->
<script type="text/javascript" src="ajax.js"></script>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
<title>&nbsp;B&uacute;squeda avanzada de entidades</title>
</head>

<body id="public">
<div id="container" style="background-image:url(images/fondo_correo.jpg)">

<div class="info">
	<h2>&nbsp;B&uacute;squeda Avanzada de Entidades</h2>
		<br />
	<h4>&nbsp;Ingrese Nombre, SIGLA o NIT de la Entidad.</h4>
</div>
<p >&nbsp;</p>
<form method="POST">
  &nbsp;<input type="text" name="entidad" size="50"/>
  <input type="submit" value='Buscar' name='Buscar' valign='middle'/>
  <input type="button" value='Cancelar' name="Buscar2" valign='middle' onclick="window.close();" />
  <input type="hidden" name="busca" value="busca" />
</form>
<p >&nbsp;</p>
<p >&nbsp;</p>
<?
if(isset($_POST['busca']))
{
?>
	<h4>&nbsp;Resultados de la b&uacute;squeda
	  <label>
	  <input type="submit" value="Pasar datos y cerrar ventana" onclick="pasa_nit();" />
	  </label>
	</h4>
	<h5>
		<center><font color="#FF0000">Seleccione una entidad y pulse el bot&oacute;n <em>Pasar datos y cerrar ventana</em></font></center>
	</h5>
	<br />
<form name="busqueda" id="busqueda">	
  <table width="97%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#CCCCCC">
      <tr>
        <td width="80%"><div align="center"><strong>Nombre</strong></div></td>
        <td width="15%"><div align="center"><strong>Nit</strong></div></td>
        <td width="5%"><div align="center"><strong>Seleccione</strong></div></td>
      </tr>
<?
while (!$rs_bodega->EOF)
{
?>	  
      <tr>
        <td bgcolor="#FFFFFF"><?= $rs_bodega->fields['NOMBRE_DE_LA_EMPRESA']?></td>
        <td bgcolor="#FFFFFF"><?= $rs_bodega->fields['NIT_DE_LA_EMPRESA']?></td>
        <td bgcolor="#FFFFFF" align="center">
			<input type="radio" name="nit" value="<?= $rs_bodega->fields['NIT_DE_LA_EMPRESA']?>" id="nit" /></td>
      </tr>
<?
$rs_bodega->MoveNext();
}
?>	  
  </table><br />&nbsp;
</form>
<?
}
?>
</div>
</body>
</html>
