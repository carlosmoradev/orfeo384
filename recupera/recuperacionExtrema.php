<html>
<head>
<link rel="stylesheet" href="../estilos/orfeo.css">
<script src="../js/popcalendar.js"></script>
<script src="../js/mensajeria.js"></script>
 <div id="spiffycalendar" class="text"></div>
</head>
<?
error_reporting(0);
if (!$ruta_raiz) $ruta_raiz="..";
include_once("buscarFila.php");
error_reporting(7);

if (!$ruta_raiz) $ruta_raiz="..";
error_reporting(7);
if($_GET["verradicado"]) $verradicado = $_GET["verradicado"];
echo "<P>Radicado :" .$verradicado ."</P>";

$path = substr($verradicado,0,4 )."/".substr($verradicado,4,3 )."/docs/1".$verradicado."*" ;
$comando = "ls -l /var/www/bodegaProd/$path sort"; 
exec($comando,$a);
$buscarAnexos = new buscarFila();
$buscarAnexos->ini = "2009";
$buscarAnexos->fin = "1";
?>


<?
$radIni = $_POST["radIni"];
$radFin = $_POST["radFin"];
?>
<html>
<body>
<form method=post action=recuperacionExtrema.php>
	<input type=text name=radIni  value='<?=$radIni?>'>
	<input type=text name=radFin value='<?=$radFin?>'>
	<input type=SUBMIT value='Listar'>
</form>
<table class='e_tablas' width='100%'>
<td  class=titulos2>Documento Anexo</td>
<td  class=titulos2>Radicado</td>
<td  class=titulos2>____</td>
<td  class=titulos2>Opcion</td>
</tr>
<?
$buscarAnexos = new buscarFila();
$buscarAnexos->ini = "2009";
$buscarAnexos->fin = "1";
for($num=$radIni;$num<=$radFin;$num++)
{
	$numConsecutivo = str_pad($num,6,"0", STR_PAD_LEFT);
  $numRadicado = "2009440".$numConsecutivo."";
	$path = substr($numRadicado,0,4 )."/".substr($numRadicado,4,3)."/docs/1".$numRadicado."* ";
	$comando = "ls -l ../bodega/$path "; 
	//$comando = "ls -l ../bodega/* "; 
  $a="";
	exec($comando,$a);
?>
	<tr>
	<td  class=titulos2><?=$comando?></td>
	<td  class=titulos2><? print_r($a) ?><br></td>
	<td  class=titulos2></td>
	<td  class=titulos2></td>
	</tr>
<?
}
?>
</table>
</body>
</html>