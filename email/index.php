<?php
session_start();

foreach ($_GET  as $key => $val){ ${$key} = $val;}
foreach ($_POST as $key => $val){ ${$key} = $val;}

if (!$_SESSION['dependencia'])  header ("Location: $ruta_raiz/cerrar_session.php");
$ruta      = '/bodega';
$ruta_raiz =  "..";  

$dependencia = $_SESSION["dependencia"];
$encabezado  = session_name()."= ".session_id()."&krd = $krd";

?>
<html>
<head>
<title>Email Entrante - OrfeoGPL.org</title>
</head>
<frameset rows="30%,70%" border="10" name="filas">
<frame name="image" src="./image.php?<?=$encabezado?>" name="columnas">
<frame name="formulario" src="login_email.php?<?=$encabezado?>" parent="secundario" resize=true>
</html>
