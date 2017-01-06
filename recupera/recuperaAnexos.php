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

$path = substr($verradicado,0,4 )
."/".substr($verradicado,4,3 )
."/docs/1".$verradicado."*" ;
$comando = "ls -l /var/www/bodegaProd/$path sort"; 
exec($comando,$a);
$buscarAnexos = new buscarFila();
$buscarAnexos->ini = "2009";
$buscarAnexos->fin = "1";
?>
<table class='e_tablas' width='100%'>
<td  class=titulos2>Documento Anexo</td>
<td  class=titulos2>Radicado</td>
<td  class=titulos2>____</td>
<td  class=titulos2>Opcion</td>
</tr>
<?
foreach($a as $key=>$value)
{
$buscarAnexos->ini = "2009";
$buscarAnexos->fin = "1";
//$linea = explode(" ",$value);
?>
<tr class=listado2>
<form action='crearAnexo.php?' method="GET" target='RadicarDocumento<?=date('dmyh')?>'>
<?
if(strlen($value)>=50)
	{
    $numeroRadEncontrado = "";
		$fila = substr($value,47,(strlen($value)-47));
//		$fecha= substr($value,33,13);
		$fecha= substr($value,33,13);

    $linkFila = str_replace("var/www/bodegaProd","../bodega",$fila);
		echo "<td>". $linkFila .  "</a>- $fecha </td>";
		$buscarAnexos->fila = $fila;
		$numeroRadEncontrado = $buscarAnexos->radicado();
		$buscarAnexos->ini = "Asunto:";
		$buscarAnexos->fin = ".";
		$asunto = $buscarAnexos->string();
		echo ">>>>>".$asunto;
		$optionR = "<option value='' >No Hay valor</option>\n";
		$optionD = "<option value='' >No Hay valor</option>\n";
		$optionC = "<option value='' >No Hay valor</option>\n";
		$option = "<option value='' >No Hay valor</option>\n";
		if($numeroRadEncontrado){ 
			echo "<td>Radicado -->".$numeroRadEncontrado."<br>";
			$pathImagen = "../bodega/".substr($numeroRadEncontrado,0,4)."/".substr($numeroRadEncontrado,4,3)."/".$numeroRadEncontrado.".tif";
			if (file_exists($pathImagen)) {
					echo "<a href='$pathImagen'>Tiene Imagen Digitalizada</a>";
			} else {
					echo "No hay Imagen Digitalizada <a href='$pathImagen'>?</a>";
			}
			
			echo "</td><td>";
			$datosR = $buscarAnexos->buscarEncabezado(); 
			foreach($datosR as $id=>$value){
			 //echo "$value <br>";
			 if($id==1) $datoss = "selected"; else $datoss = "";
			 $optionR .= "<option value='$value' '$datoss'>$value</option>\n";
			 if($id==2) $datoss = "selected"; else $datoss = "";
			 $optionD .= "<option value='$value' '$datoss'>$value</option>\n";
			 if($id==3) $datoss = "selected"; else $datoss = "";
			 $optionC .= "<option value='$value' '$datoss'>$value</option>\n";
			 $option .= "<option value='$value' >$value</option>\n";
			}
			echo "Remitente <select name='nombre'>$optionR</select><br>";
			echo "Direccion <select name='direccion'>$optionD</select><br>";			
			echo "Ciudad__ <select name='Ciu'>$optionC</select><br>";
			echo "Telefono <select name='telefono'>$option</select><br>";
			echo "<br><br><br>Asunto >$asunto<br>";
			echo "<>"; 
		}
	
	}
if(!$depe_actu) $depe_actu=999;
?>
<br>Dependencia<INPUT TYPE=TEXT name='depe_actu' value='<?=$depe_actu?>'><br>
<INPUT TYPE=HIDDEN name='asunto' value='<?=$asunto?>'>
<INPUT TYPE=HIDDEN name='path' value='<?=$pathImagen?>'>
<INPUT TYPE=HIDDEN name='depto_codi' value='11'>
<INPUT TYPE=HIDDEN name='muni_codi' value='1'>
<INPUT TYPE=HIDDEN name='asunto' value='<?=$asunto?>'>
<INPUT TYPE=HIDDEN name='path_anexo' value='<?=$linkFila?>'>
<INPUT TYPE=HIDDEN name='fechaAnexo' value='<?=$fecha?>'>
<INPUT TYPE=HIDDEN name='radicadoPadre' value='<?=$verradicado?>'>
<INPUT TYPE=HIDDEN name='radsalida' value='<?=$numeroRadEncontrado?>'>
<?
	if($numeroRadEncontrado)
{
?>
<INPUT TYPE=SUBMIT name='RADICAR' value='Radicar Documento'>
<?
}
?>
</form>
</td>
<td>
<a href='crearAnexo.php?path_anexo=<?=$linkFila?>&fechaAnexo=<?=$fecha?>&radicadoPadre=<?=$verradicado?>&radsalida=<?=$numeroRadEncontrado?>' target='CrearAnexo2009'>
Crear el Anexo
</a>
</td>
</tr>
<?
}
?>

</table>
</html>
