<?php
	$ruta_raiz = "../..";
	$directorioFinal =  "/home/cmauricio/cedulasimg/";
	$cedulas = array();
	$cedulas[] = "799887213";
	//$cedulas[] = "887213";
	
	$barnumber = $cedula;
	
	foreach ($cedulas as $barnumber) {
		$nurad = $barnumber;
		$fechah = "";
		include($ruta_raiz . "/include/barcode/index.php");
		$newfile = $directorioFinal . $nurad . ".png";
		
		if (!copy($file, $newfile)) {
			echo "fallo en la copia $file...\n";
		}
	}
	
	echo "fin";
?>
