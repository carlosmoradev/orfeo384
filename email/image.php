<?php
if(!session_id()) session_start();

$ruta      = '/include';
$ruta_raiz = null;

while(!is_dir($ruta_raiz.$ruta))$ruta_raiz .= empty($ruta_raiz)? "../" : "..";  
if (!$_SESSION['dependencia'])
    header ("Location: $ruta_raiz/cerrar_session.php");

include_once($ruta_raiz.'/config.php');
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
	<title>..Vista Previa..</title>
  	<link rel="stylesheet" href="<?=$ruta_raiz."estilos/".$_SESSION["ESTILOS_PATH"]?>/orfeo.css">
	</head>
	<body>
		</br>
		</br>
		</br>
		<table width="80%" class='borde_tab' cellspacing='5' align="center">
			<tr class='titulos2'>
				<td align="center">
					<img src="<?=$ruta_raiz?>/img/alert.jpg" alt="">
				</td>
			</tr>
			<tr class='titulos2'>
				<td align="center">
					<font size="1">
						<h2>No hay Imagen Cargada</h2>
					</font>
				</td>
			</tr>
		</table>
	</body>
</html>
