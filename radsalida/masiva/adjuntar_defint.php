<?php
session_start();
/**
  * Pagina adjuntar Masivaa.php que muestra el contenido de las Carpetas
  * Creado en la SSPD en el año 2003
  * 
  * Se añadio compatibilidad con variables globales en Off
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
$year        = date("Y");

/**
 * Programa controlador que coordina la combinacion de 
 * correspondencia definitiva, una vez se ha 
 * generado la pruena desde adjuntar_masiva.php
 * @author      Sixto Angel Pinzon
 * @version     1.0
 */


$ruta_raiz = "../..";
require_once "$ruta_raiz/jhrtf/jhrtf.php";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";	
require_once("$ruta_raiz/class_control/CombinaError.php");
$conexion = new ConnectionHandler($ruta_raiz);
$conexion->conn->StartTrans();
$conexion->conn->SetFetchMode(ADODB_FETCH_ASSOC);	 

//variable con elementos de sesion
$phpsession = session_name()."=".session_id();
//variable con elementos de sesion
$params=$phpsession."&codiTRD=$codiTRD&depe_codi_territorial=$depe_codi_territorial&usua_nomb=$usua_nomb&depe_nomb=$depe_nomb&tipo=$tipo";

//Funcion que calcula el tiempo transcurrido
 function microtime_float()
{
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}
?>

<html>
<head>
<link rel="stylesheet" href="../../estilos/orfeo.css">
<title>Prueba Masiva</title>
<script>
function regresar()
{	document.formDefinitivo.action="menu_masiva.php?"+'<?=$params?>';
	document.formDefinitivo.submit();
}
function abrirArchivoaux(url){
			       nombreventana='Documento';
			       window.open(url, nombreventana,  'status, width=900,height=500,screenX=100,screenY=75,left=50,top=75');
			       return; }
</script>
</head>
<body>
<form action="adjuntar_defint.php?<?=$params?>" method="post" enctype="multipart/form-data" name="formDefinitivo">
<input type=hidden name=pNodo value='<?=$pNodo?>'>
<input type=hidden name=codProceso value='<?=$codProceso?>'>
<input type=hidden name=tipoRad value='<?=$tipoRad?>'>
<?
echo "<center><span class=etextomenu align=left>";
			echo "<TABLE border=0 width 60% cellpadding='0' cellspacing='5' class='borde_tab'>
			<TR ALIGN=LEFT><TD width=20% class='titulos2'>DEPENDENCIA :</td><td class='listado2'>".$_SESSION['depe_nomb']."</TD>
			<TR ALIGN=LEFT><TD class='titulos2'>USUARIO RESPONSABLE :</td><td class='listado2'>".$_SESSION['usua_nomb']."</TD>
			<TR ALIGN=LEFT><TD class='titulos2'>FECHA :</td><td class='listado2'>" . date("d-m-Y - h:mi:s") ."</TD></TR>
			</TABLE>";
?>
<center>
<span class="info">Se ha realizado la combinaci&oacute;n de correspondencia DEFINITIVA</span><BR>
</center>
<?

$time_start = microtime_float();

//Verifica que el objeto de combinacion masiva 
//se encuentre en la sesion, refrenciado como 'masiva'

$masiva = new jhrtf($archInsumo,$ruta_raiz,$arcPDF,$conexion);
$masiva->cargar_csv();
$masiva->validarArchs();

if(!$masiva){	
    echo("ERROR ! NO LLEGA LA INFORMACION DE RADICACION MASIVA");	
}else{

	$masiva->setConexion($conexion);
	$masiva->setDefinitivo("si");

	echo "<span class='leidos'>";	
	$masiva->codProceso=$codProceso;
	list ($masiva->codFlujo, $masiva->codArista) = split('-', $pNodo);
	$masiva->tipoDocto=$tipo;
	$masiva->combinar_csv($dependencia,$codusuario,$usua_doc,$usua_nomb,$depe_codi_territorial,$codiTRD,$tipoRad);
	$archInsumo =  $masiva->getInsumo();

	include("$ruta_raiz/config.php");

    //El include del servlet hace que se altere el 
    //valor de la variable  $estadoTransaccion 
    //como 0 si se pudo procesar el documento,
	// -1 de lo contrario
	$estadoTransaccion=-1;
	//El archivo que ingreso es odt, luego se utiliza el nuevo combinador
	if ($extension  == 'odt'){
				//Se incluye la clase que maneja la combinacion masiva
				include ( "$ruta_raiz/radsalida/masiva/OpenDocText.class.php" );
				define ( 'WORKDIR', '../../bodega/tmp/workDir/' );
				define ( 'CACHE', WORKDIR . 'cacheODT/' );
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
				//Se establece el modo Debug, poner en true para pruebas y muestra mensajes de lo que va ocurriendo
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
                                    Problemas en el servidor abriendo 
                                    archivo ODT para combinaci&oacute;n.
                                    </td>
                                </tr>
                            </table>" );
				}
				
				$odt->cargarContenido();
				//Se recorre el archivo de insumo
				foreach ( $contenidoCSV as $line_num => $line ) {
				  //En el archivo de Insumo la línea 4 contiene el nombre de las variables
				   if ( $line_num == 4 ) {
				   		$cadaVariable = explode( ',' , $line );		   	
				   }else if ( $line_num > 4 ) { //Desde la línea 5 hasta el final del archivo de insumo están los datos de reemplazo
				   		$cadaValor =  explode( ",",$line ) ;
				   		$odt->setVariable( $cadaVariable, $cadaValor );
				   }
				}
				$tipoUnitario = '0';

				//Se guardan los cambios del archivo temporal para su descarga
				$archivoF = $odt->salvarCambios( null, $archivo, $tipoUnitario );
				$intBodega = strpos($archivoF, "/bodega");
				if ( $intBodega === false ){
				   	  $rutaTMP = $ruta_raiz . '/bodega';
			    }else {
				      $rutaTMP = $ruta_raiz;
                }
	            if(!copy('../'.WORKDIR.$odt->nombreOdt,'../'.$rutaTMP.$archivoF)){
                    echo "Error al copiar $rutaTMP.$archivoF";
                }
				
				$estadoTrans=$masiva->confirmarMasiva();
				if  ($estadoTrans){	
					$_SESSION["masiva"]=$masiva;
					echo "<BR><span class='info'><a  class='vinculos' href=javascript:abrirArchivoaux('$rutaTMP$archivoF')>GuardarArchivo</a>"; 
		   			echo "</span>"; 
		   			echo "<span class='info'>";  
		   			echo "<BR><a class='vinculos' href=javascript:abrirArchivoaux('$arcPDF')> Abrir Listado</a>"; 
		   			echo "</span>"; 
		   			//Contabilizamos tiempo final
					$time_end = microtime_float();
					$time = $time_end - $time_start;
		   			echo "<span class='info'>";  
					echo "<br><b>Se demor&oacute;: $time segundos la Operaci&oacute;n total.</b>";
		   			echo "</span>"; 
		   			echo "<input type=hidden name=masiva2 value=$masiva>";
				}
				else
				{	echo "<BR><span class='alarmas'>Se gener&oacute; un problema al alctualizar la base de datos, intente mas tarde($estadoTrans)";
		   			echo "</span>"; 
				}
	}
}

?>
</form>
</body>
</html>
