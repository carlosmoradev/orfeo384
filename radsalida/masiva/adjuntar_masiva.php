<?php
session_start();

$ruta_raiz = "../..";

if (!$_SESSION['dependencia'])
    header ("Location: $ruta_raiz/cerrar_session.php");


/** * Pagina Menu_Masiva.php que muestra el contenido de las Carpetas
  * Creado en la SSPD en el año 2003
  * 
  * Se anadio compatibilidad con variables globales en Off
  * @autor Jairo Losada 2009-05
  * @licencia GNU/GPL V 3
  */

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

$krd         = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$usua_doc    = $_SESSION["usua_doc"];
$codusuario  = $_SESSION["codusuario"];
$tip3Nombre  = $_SESSION["tip3Nombre"];
$tip3desc    = $_SESSION["tip3desc"];
$tip3img     = $_SESSION["tip3img"];

$archivoPlantilla = $_POST["archivoPlantilla"];


include_once "$ruta_raiz/include/db/ConnectionHandler.php";
require_once("$ruta_raiz/class_control/CombinaError.php");

(!$db) ? $conexion = new ConnectionHandler($ruta_raiz) : $conexion = $db;
$conexion->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$hora=date("H")."_".date("i")."_".date("s");
// var que almacena el dia de la fecha
$ddate=date('d');
// var que almacena el mes de la fecha
$mdate=date('m');
// var que almacena el año de la fecha
$adate=date('Y');
// var que almacena  la fecha formateada
$fecha=$adate."_".$mdate."_".$ddate;

$archivoPlantilla_name = $_FILES['archivoPlantilla']['name'];
//Almacena la extesion del archivo entrante
$extension = trim(substr($archivoPlantilla_name,strpos($archivoPlantilla_name,".")+1,strlen($archivoPlantilla_name)-strpos($archivoPlantilla_name,".")));
//var que almacena el nombre que tendra la pantilla
$arcPlantilla=$usua_doc."_".$fecha."_".$hora.".$extension";

//var que almacena el nombre que tendra el CSV
$arcCsv=$usua_doc."_".$fecha."_".$hora.".csv";
//var que almacena el path hacia el PDF final
$arcPDF="$ruta_raiz/bodega/masiva/"."tmp_".$usua_doc."_".$fecha."_".$hora.".pdf";
$phpsession = session_name()."=".session_id();
//var que almacena los parametros de sesion
$params=$phpsession."&krd=$krd&dependencia=$dependencia&codiTRD=$codiTRD&depe_codi_territorial=$depe_codi_territorial&usua_nomb=$usua_nomb&tipo=$tipo&"
				."depe_nomb=$depe_nomb&usua_doc=$usua_doc&codusuario=$codusuario";

 //Función que calcula el tiempo transcurrido
 function microtime_float()
{
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}

?>
<html>
<head>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<link rel="stylesheet" href="../../estilos/orfeo.css">
<script>
/**
* Confirma la generacion definitiva
*/
function enviar() {

if ( confirm ('Confirma la generacion de un radicado por cada registro del archivo CSV?'))
	document.formDefinitivo.submit();
}


function regresar() {

	document.formDefinitivo.action="menu_masiva.php?"+'<?=$params?>';
	document.formDefinitivo.submit();

}


/**
* Envia el formulario, a consultar divipola
*/
function divipola() {
	document.formDefinitivo.action="consulta_depmuni.php?"+ document.formDefinitivo.params.value;
	document.formDefinitivo.submit();
}


/**
* Cancela el proceso y devuelve el control a menu masiva
*/
function cancelar(){
	document.formDefinitivo.action='menu_masiva.php?'+ document.formDefinitivo.params.value;
	document.formDefinitivo.submit();
}

function abrirArchivoaux(url){
			       nombreventana='Documento';
			       window.open(url, nombreventana,  'status, width=900,height=500,screenX=100,screenY=75,left=50,top=75');
                   return; 
}


</script>
</head>
<body>
<form action="adjuntar_defint.php?<?=$params?>" method="post" enctype="multipart/form-data" name="formDefinitivo">
<input type=hidden name=pNodo value='<?=$pNodo?>'>
<input type=hidden name=codProceso value='<?=$codProceso?>'>
<input type=hidden name=tipoRad value='<?=$tipoRad?>'>
<?php
$time_start = microtime_float();
$archivoPlantilla = $_FILES['archivoPlantilla']['name'];

if ($_FILES['archivoPlantilla']['size'] >= 10000000 || $_FILES['archivoCsv']['size'] >= 10000000 )
{	echo "el tama&nacute;o de los archivos no es correcto. <br><br><table><tr><td><li>se permiten archivos de 100 Kb m&aacute;ximo.</td></tr></table>";
}
else
{
$dirActual = getcwd();

	if(!move_uploaded_file($_FILES['archivoPlantilla']['tmp_name'], "$ruta_raiz/bodega/masiva/".$arcPlantilla))
	{
		echo "error al copiar Plantilla: $archivoPlantilla en $ruta_raiz/bodega/masiva/".$arcPlantilla;
	}elseif (!copy($_FILES['archivoCsv']['tmp_name'], "$ruta_raiz/bodega/masiva/".$arcCsv)) {
		echo "error al copiar CSV: $archivoCsv en $ruta_raiz/bodega/masiva/" .$arcCsv;
	}
	else
	{
		echo "<center><span class=etextomenu align=left>
			<TABLE border=0 width='75%' cellpadding='0' cellspacing='5' class='borde_tab'>
			<tr ALIGN='LEFT'>
				<td width='20%' class='titulos2' >DEPENDENCIA :</td>
				<td class='listado2'> ".$_SESSION['depe_nomb']."</td>	
			<tr ALIGN='LEFT'>
				<td class='titulos2' >USUARIO RESPONSABLE :</td>
				<td class='listado2'>".$_SESSION['usua_nomb']."</td>
			</tr>
			<tr ALIGN='LEFT'>
				<td class='titulos2'>FECHA :</td>
				<td class='listado2'>" . date("d-m-Y - h:mi:s") ."</td>
			</tr>
			</table>";
		require "$ruta_raiz/jhrtf/jhrtf.php"; $ano = date("Y") ;

		//var que almacena nombre del archivo combinado
		//pone el nombre de los archivos de salida con la extension adecuada (odt o .doc)

		if( $extension == 'doc'){
			$archivoFinal 	= "./bodega/$ano/$dependencia/docs/$usua_doc"."_$fecha"."_$hora.doc";
			$archivoTmp     = "./bodega/masiva/tmp_$usua_doc"."_$fecha"."_$hora.doc";
		}else{
			$archivoFinal   = "/bodega/$ano/$dependencia/docs/$usua_doc"."_$fecha"."_$hora.odt";
			$archivoTmp     = "/bodega/tmp/workDir/$usua_doc"."_$fecha"."_$hora.odt";
		}
		
		$ruta_raiz = "../..";
		$definitivo="no";

		$archInsumo="tmp_".$usua_doc."_".$fecha."_".$hora;

		$fp=fopen("$ruta_raiz/bodega/masiva/$archInsumo",'w');
	 	if ($fp)
	 	{	fputs ($fp,"plantilla=$arcPlantilla"."\n");
			fputs ($fp,"csv=$arcCsv"."\n");
			fputs ($fp,"archFinal=$archivoFinal"."\n");
			fputs ($fp,"archTmp=$archivoTmp"."\n");
			fclose($fp);
	 	}
	 	else
	 	{	exit("No hay acceso para crear el archivo $archInsumo");	}

		// Se crea el objeto de masiva
		$masiva = new jhrtf($archInsumo,$ruta_raiz,$arcPDF,$conexion);
		$masiva->cargar_csv();
		$masiva->validarArchs();
		if ($masiva->hayError())
		{	$masiva->mostrarError();
		}
		else
		{
			$masiva->setTipoDocto($tipo);
		 	$_SESSION["masiva"]=$masiva;
		 	echo  "<center><span class=info><br>Se ha realizado la combinaci&oacute;n de correspondencia como una prueba.<br> ";
		 	$masiva->combinar_csv($dependencia,$codusuario,$usua_doc,$usua_nomb,$depe_codi_territorial,$codiTRD,$tipoRad);

			include("$ruta_raiz/config.php");
			//El include del servlet hace que se altere el valor de la variable  $estadoTransaccion como 0 si se pudo procesar el documento, -1 de lo
			// contrario
			$estadoTransaccion=-1;

			//El archivo que ingresar es odt, luego se utiliza el nuevo combinador
			if($extension == 'odt'){

				//Se incluye la clase que maneja la combinacion masiva
				include ( "$ruta_raiz/radsalida/masiva/OpenDocText.class.php" );
				define ( 'WORKDIR', "$ruta_raiz/bodega/tmp/workDir/" );
				define ( 'CACHE', WORKDIR . 'cacheODT/' );
				//echo "<hr> ---> $ruta_raiz/bodega/masiva/$archInsumo" ;
				//Se abre archivo de insumo para lectura de los datos
				$fp=fopen("$ruta_raiz/bodega/masiva/$archInsumo",'r');
			 	if ($fp)
			 	{
			 			$contenidoCSV = file( "$ruta_raiz/bodega/masiva/$archInsumo" );
						fclose($fp);
			 	}
			 	else
			 	{
			 		exit("No hay acceso para crear el archivo $archInsumo");
			 	}

				$accion = false;
				$odt = new OpenDocText();
				//Modod debug en false, para pruebas poner true y saldran mensajes de lo que estan pasando con la combinacion
				$odt->setDebugMode(false);
					
				//Se carga el archivo odt Original
				$odt->cargarOdt( "$ruta_raiz/bodega/masiva/$arcPlantilla", $arcPlantilla );
				$odt->setWorkDir( WORKDIR );
				$accion = $odt->abrirOdt();
				if(!$accion){
                    die( "<CENTER>
                            <table class=borde_tab>
                                <tr>
                                    <td class=titulosError>
                                        Problemas en el servidor abriendo archivo ODT para combinaci&oacute;n.
                                    </td>
                                </tr>
                            </table>" );
				}

				$odt->cargarContenido();

				//Se recorre el archivo de insumo
				foreach ( $contenidoCSV as $line_num => $line ) {
				   if ( $line_num == 4 ) { //Esta linea contiene las variables a reemplazar
				   		$cadaVariable = explode( ',' , $line );
				   }else if ( $line_num > 4 ) { //Desde la línea 5 hasta el final del archivo de insumo están los datos de reemplazo
				   		$cadaValor =  explode( ",",$line ) ;
				   		$odt->setVariable( $cadaVariable, $cadaValor );
				   }
				   echo "";
				   if( connection_status()!=0 ){
				   		$objError = new CombinaError (NO_DEFINIDO);
						echo ($objError->getMessage());
						die;
				   }
				}
				$tipoUnitario = '0';

				//Se guardan los cambios del archivo temporal para su descarga
				$archivoTMP = $odt->salvarCambios( $archivoTmp, null, $tipoUnitario );
               	$odt->borrar();
                $ruta_arch  = $HTTP_SERVER_VARS['SERVER_NAME'];	
				    echo ("<BR><span class='info'> Por favor guarde el archivo y verifique que los datos de combinacion  esten correctos <br>");
					echo ("<a target='_blank' class='vinculos' href='$ruta_raiz/$archivoTMP')>Guardar Archivo</a></span> ");
					echo ("<br><br>");
					echo( "<br><input name='enviaDef' type='button'  class='botones_largo' id='envia21'  onClick='enviar()' value='Generar Definitivo'>");
					echo( "<input name='cancel' type='button'  class='botones' id='envia22'  onClick='cancelar()' value='Cancelar'>");
			}
		}
	}
	//Contabilizamos tiempo final
	$time_end = microtime_float();
	$time = $time_end - $time_start;
	echo "<br><b>Se demor&oacute;: $time segundos la Operaci&oacute;n total.</b>";
}

?>
<input name = 'archivo' type      = 'hidden' value = '<?= $archivoFinal?>'>
<input name = 'arcPDF' type       = 'hidden' value = '<?= $arcPDF ?>'>
<input name = 'tipoRad' type      = 'hidden' value = '<?= $tipoRad?>'>
<input name = 'pNodo' type        = 'hidden' value = '<?= $pNodo?>'>
<input name = 'params' type       = 'hidden' value = "<?= $params?>">
<input name = 'archInsumo' type   = 'hidden' value = "<?= $archInsumo?>">
<input name = 'extension' type    = 'hidden' value = "<?= $extension?>">
<input name = 'arcPlantilla' type = 'hidden' value = '<?= $arcPlantilla?>'>

</form>
</body>
</html>
