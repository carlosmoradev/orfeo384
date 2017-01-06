<?php
 /*
  * Invocado por una funcion javascript (funlinkArchivo(numrad,rutaRaiz))
  * Consulta el path del radicado 
  * @author Liliana Gomez Velasquez
  * @since 5 de noviembre de 2009
  * @category imagenes
 */
session_start();
foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;
$krd                = $_SESSION["krd"];
$dependencia        = $_SESSION["dependencia"];
$ln          = $_SESSION["digitosDependencia"];
$digitos_totales = 11 + $ln;
if (!$ruta_raiz) $ruta_raiz = ".";

if (isset($db)) unset($db);
include_once("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");
$db->conn->SetFetchMode( ADODB_FETCH_ASSOC );
include_once "$ruta_raiz/tx/verLinkArchivo.php";

$verLinkArchivo = new verLinkArchivo($db);

if (strlen( $numrad) <= $digitos_totales){

  $resulVali = $verLinkArchivo->valPermisoRadi($numrad);
  $verImg = $resulVali['verImg'];
  $pathImagen = $resulVali['pathImagen'];
  if(substr($pathImagen,0,9) == "../bodega") {
  	$pathImagen=str_replace('../bodega','./bodega',$pathImagen);
  	$file = $pathImagen;
  }elseif(substr($pathImagen,0,12) == "../../bodega") {
    $pathImagen=str_replace('../../bodega','./bodega',$pathImagen);
  	$file = $pathImagen;
  }
  	else {
  		$file = $ruta_raiz. "/bodega/".$pathImagen;
  }	
}else {
//Se trata de un anexo	
  $resulValiA = $verLinkArchivo->valPermisoAnex($numrad);
  $verImg = $resulValiA['verImg'];
  $pathImagen = $resulValiA['pathImagen'];
  $file="$ruta_raiz/bodega/".substr(trim($numrad),0,4)."/".intval(substr(trim($numrad),4,$ln))."/docs/".trim($pathImagen);
	
}
$fileArchi = $file;
$tmpExt = explode('.',$pathImagen);
$filedatatype = $pathImagen;
// Si se tiene una extension 
if(count($tmpExt)>1){
   $filedatatype =  $tmpExt[count($tmpExt)-1];
}
if($verImg=="SI"){
  if (file_exists($fileArchi)) {
    header('Content-Description: File Transfer');
    switch($filedatatype)
      {
         case 'odt':
			   header('Content-Type: application/vnd.oasis.opendocument.text');
			   break;
         case 'doc':
               header('Content-Type: application/msword');
               break;
         case 'tif':
               header('Content-Type: image/TIFF');
               break;
         case 'pdf':
               header('Content-Type: application/pdf');
               break;  
         case 'xls':
               header('Content-Type: application/vnd.ms-excel');
               break;
         case 'csv':
               header('Content-Type: application/vnd.ms-excel');
               break;
         case 'ods':
               header('Content-Type: application/vnd.ms-excel');
               break;  
         case 'html':
               header('Content-Type: text/html');
               break; 
         default :
		      header('Content-Type: application/octet-stream');
			  break;  
        }
         
        if ($filedatatype == 'html') {
	        header('Content-Disposition: inline; filename='.basename($file));
         }else{
            header('Content-Disposition: attachment; filename='.basename($file));
	    }

        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        //ob_clean();
        //flush();
        readfile($file);
        exit;
    }else {
 	   die ("<B><CENTER>  NO se encontro el Archivo  </a><br>");
    }
  }elseif($verImg == "NO"){ 
  	die ("<B><CENTER>  NO tiene permiso para acceder al Archivo </a><br>");
  }
else{
    die ("<B><CENTER>  NO se ha podido encontrar informacion del Documento</a><br>"); 
}
?>
