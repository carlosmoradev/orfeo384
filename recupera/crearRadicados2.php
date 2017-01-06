<?
session_start();
$radicadoS = $_POST[$radicadoS];
$datosRad = $_SESSION["R".$radicadoS];
print_r($_SESSION);
?>
<html>
<head>
<link rel="stylesheet" href="../estilos/orfeo.css">
<script src="../js/popcalendar.js"></script>
<script src="../js/mensajeria.js"></script>
 <div id="spiffycalendar" class="text"></div>
</head>
<body>

</body>
</html>