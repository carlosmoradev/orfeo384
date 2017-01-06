<?
session_start();

$radicadoS = $_POST["radicadoS"];
echo "=>".$radicadoS;
$datosRad = $_SESSION["R".$radicadoS];
//print_r($datosRad);
?>
<html>
<head>
<link rel="stylesheet" href="../estilos/orfeo.css">
<script src="../js/popcalendar.js"></script>
<script src="../js/mensajeria.js"></script>
 <div id="spiffycalendar" class="text"></div>
</head>
<body>
Generar Radicado de Entrada No. <a href='recuperaAnexos.php?verradicado=<?=$datosRad['RAD_E'] ?>'><?=$datosRad['RAD_E'] ?> <br> </a>
Generar Radicado de Salida No. <a href='recuperaAnexos.php?verradicado=<?=$datosRad['RAD_S'] ?>'><?=$datosRad['RAD_S'] ?> </a><br>
<hr>
Datos <br><br>
<?
foreach($datosRad as $key=>$value)
{
  echo "" . $key . " = " . $value . "<br>";
}
?>

</body>
</html>
