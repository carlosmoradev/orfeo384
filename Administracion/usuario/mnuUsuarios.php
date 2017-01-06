<?php
session_start();
	$ruta_raiz = "../..";
    if (!$_SESSION['dependencia'])
        header ("Location: $ruta_raiz/cerrar_session.php");

$sendSession     = session_name().'='.session_id(); 
?>
<html>
<head>
<title>Administrar usuario</title>
<link rel="stylesheet" href="<?=$ruta_raiz."/estilos/".$_SESSION["ESTILOS_PATH"]?>/orfeo.css">
</head>
<body>
<form name='frmMnuUsuarios' action='../formAdministracion.php' method="post">
  <table width="32%" align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
  <tr bordercolor="#FFFFFF">
    <td colspan="2" class="titulos4"><div align="center"><strong>ADMINISTRACION DE USUARIOS Y PERFILES</strong></div></td>
  </tr>
  <tr bordercolor="#FFFFFF">
    <td align="center" class="listado2" width="98%"><a href='crear.php?<?=$sendSession?>&usModo=1' class="vinculos" target='mainFrame'>1. Crear Usuario</a></td>
  </tr>
  <tr bordercolor="#FFFFFF">
    <td align="center" class="listado2" width="98%"><a href='cuerpoEdicion.php?<?=$sendSession?>&usModo=2' class="vinculos" target='mainFrame'>2. Editar Usuario</a></td>
  </tr>
  <tr bordercolor="#FFFFFF">
    <td align="center" class="listado2" width="98%"><a href='cuerpoConsulta.php?<?=$sendSession?>' class="vinculos" target='mainFrame'>3. Consultar Usuario</a></td>
  </tr>
  <tr bordercolor="#FFFFFF">
  	<td align="center" class="listado2">
	<center><input align="middle" class="botones" type="submit" name="Submit" value="Cerrar"></center>
	</td> </tr>
</table>
</form>
</body>
</html>
