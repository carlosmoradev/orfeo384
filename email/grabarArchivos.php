<?
error_reporting(7);
$ruta_raiz = "..";

		$codigoAnexo = $nurad."000$numPartesi";
		$tmpNameEmail = $nurad."_000".$numPartesi.".".$aExtension;
		$directorio = substr($nurad,0,4) ."/". substr($nurad,4,3)."/docs/";
		$fileEmailMsg = "../bodega/$directorio".$tmpNameEmail;
	
		$file1=fopen($fileEmailMsg,'w');
		$archivo = $body['message'];
		fputs($file1,$body['message']);
		fclose($file1);
		$anexoTamano = $msg->msg[$eMailMid]['at']['fsize'][$i];
		echo "<br>Grabado Archivo en ---> <a href='$fileEmailMsg'> $fn </a>";
		$radicadoAttach .= "< ". $fname ." Tama&ntilde;o :". $anexoTamano . " >";
		$fileEmailMsg = str_replace("..","",$fileEmailMsg);
		$fecha_hoy = Date("Y-m-d");
		if(!$db->conn) echo "No hay conexion";
		$sqlFechaHoy=$db->conn->DBDate($fecha_hoy);
		$record["ANEX_RADI_NUME"] =$nurad;
		$record["ANEX_CODIGO"] =$codigoAnexo;
		$record["ANEX_TAMANO"] ="'".$anexoTamano."'";
		$record["ANEX_SOLO_LECT"] ="'S'";
		$record["ANEX_CREADOR"] ="'".$krd."'";
		$record["ANEX_DESC"] ="' Archivo:.". $fname."'";
		$record["ANEX_NUMERO"] =$numPartesi;
		$record["ANEX_NOMB_ARCHIVO"] ="'".$tmpNameEmail."'";
		$record["ANEX_BORRADO"] ="'N'";
		$record["ANEX_DEPE_CREADOR"] =$dependencia;
		$record["SGD_TPR_CODIGO"] ='0';
		$record["ANEX_TIPO"] ="1";
		$record["ANEX_FECH_ANEX"] =$sqlFechaHoy;
		$db->insert("anexos", $record, "true");
  
  
	echo "<br> Documento de Radicado ---> <a href='$fileRadicado' target='image'> $fileRadicado </a>";
	$file1=fopen($fileRadicado,'w');
	fputs($file1,$archivoRadicado);
	fclose($file1);
	str_replace('..','',$fileRadicado);
	$isqlRadicado = "update radicado set RADI_PATH = '$fileRadicado' where radi_nume_radi = $nurad";
	$rs=$db->conn->query($isqlRadicado);
	//print("Ha efectuado la transaccion($isql)($dependencia)");
	if (!$rs)	//Si actualizo BD correctamente
	{	
		echo "Fallo la Actualizacion del Path en radicado < $isqlRadicado >";
	}
  else
 {
 	print("No hay Correo disponible");
 }




//$msgMng->manageMail('move', array($eMailMid), 'trash');
?>
