<?
session_unset();
session_destroy();
session_start();
$cc=$_POST["cc"];
?>
<html>
<head>
<link rel="stylesheet" href="../estilos/orfeo.css">
<script src="../js/popcalendar.js"></script>
<script src="../js/mensajeria.js"></script>
 <div id="spiffycalendar" class="text"></div>
</head>
<body>
<form method=post action=recuperacionExtrema3.php>
	<input type=text name=cc  value='<?=$cc?>'>
	<input type=SUBMIT value='Listar'>
</form>
<?

$comando = "find /var/www/bodegaProd/masiva/tmp_".$cc."* | xargs grep 'RAD_S[^()]=2009'";
echo "$comando";
 exec($comando, $a);

?>
<table class='borde_tab'>
<tr>
	<th class=titulos2>Archivo</th>
	<th class=titulos2>Radicado</th>

</tr>
<?
$i=0;
IF($cc)
{
foreach($a as $key =>$value){
	$datos = explode(":",$value);
	?>
		<tr class=listado2>
			<td><?=$key?></td>
			<td><?=$datos[0]?></td><td>
			<?=$datos[1]?>
		</td>
	<?
	$comando = "cat ".$datos[0]." ";
	//$dat = explode("|",$comando);
	exec($comando,$a1);
?>
	<td>
   <?
			$archivo = "";
			unset($datosRad);
			$radicadoES = "";
			foreach($a1 as $key1 =>$value1){
				//$archivo .= "$value1 <br>";
				$datosFila = str_replace("*","",explode("=", $value1));
				$datosRad[$datosFila[0]] = $datosFila[1];
				if($datosFila[0]=="RAD_E") $radicadoE= $datosFila[1];
				if($datosFila[0]=="RAD_S") $radicadoS= $datosFila[1]; 
				if($datosFila[0]=="ASUNTO") $asunto= $datosFila[1]; 
				 //echo "<hr>$radicadoE - $radicadoS";
			}
			$radicadoES=$radicadoE."-".$radicadoS;
			//print_r($datosRad);
			$_SESSION["R".$radicadoS]=$datosRad;
		?>
		<form action=recuperaRadicados.php method="POST" target='RecuperacionTotal'>
		<INPUT TYPE=hidden name=radicadoS value='<?=$radicadoES?>' >
		<input type=submit name='genEntrada' value='Gen Radicado Entrada'>
		Padre - Generado <?=$radicadoS?>
		
		</form>
		<?=$archivo?>
  </td>
	<td>
	<?=$asunto?>
	</td>
	
	</tr>
<?
$i++;
 //if($i==100) break;
}
}
?>
</table>
</body>
</html>