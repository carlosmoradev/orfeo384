<?php
session_start();
require_once("conexion.inc.php");
require_once("reporte.inc.php");
require_once("pdf/class.ezpdf.php");
set_time_limit(0);

$query= $_SESSION['paginador.instancia.consulta'];
if($query){
$conexion= conectar();
$resultado= consultar($query, $conexion);
$datos=null;
 $i=0;  
$columnas= explode(",",$_REQUEST['columnas']);
while($fila= retorna_fila($resultado)){
   foreach($columnas as $campo){
     if($campo=="tipo_salida")
         $datos[$i][]=resolverSalida($fila[$campo]);
      elseif($campo=="tipo_material")
                  $datos[$i][]=resolverTipoMaterial($fila[$campo]);
	 elseif($campo=="estado")
				  $datos[$i][]=resolverEstadoMaterial($fila[$campo]);
             elseif($campo==="  " || $campo==="&nbsp;" || $campo==="")
			   $datos[$i][]=$i;
			 else
             $datos[$i][]=$fila[$campo];
   }
   $i++;
 }        
$encabezados=explode("|",$_REQUEST['encabezados']);
$titulo="Centro de Gestión Documental \n Centro de Documentación \n";
$pdf = new Cezpdf('LETTER','landscape');
$pdf->selectFont('pdf/fonts/Helvetica.afm');
$pdf->ezSetCmMargins(2,2,2,2);
$headerTabla="Resultados de la consulta";
$pdf->ezText($titulo."\n",20,array('justification'=>'center'));
$pdf->ezImage("../img/escudo.jpg");

if(count($datos)>0){
	$pdf->ezTable($datos,$encabezados,$headerTabla,array('fontSize'=>8,'width'=>700,'xPos'=>'center','shaded'=>0));
}else{
	$pdf->ezText("No se encontraron datos en esta búsqueda",20,array('justification'=>'center'));
}
$pdf->ezStream();
}
else{
header("Location: ../index.php");
}
?>
