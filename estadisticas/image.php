<?php
session_start();

    $ruta_raiz = "..";
    if (!$_SESSION['dependencia'])
        header ("Location: $ruta_raiz/cerrar_session.php");

$krd = $_SESSION["krd"];
foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;
?>

<html>
<head>
<meta http-equiv="Cache-Control" content="Cache-Control">
<title>ORFEO - IMAGEN ESTADISTICAS </title>
</head>

<body>
<img src='<?=$rutaImagen?>'>
</body>
</html>
