<?php
include_once "../include/Spreadsheet/Excel/Writer.php";

$xls = &new Spreadsheet_Excel_Writer();
$xls->send("mensajes.xls");
$sheet =& $xls->addWorksheet('mensajes');
$sheet->write(0,0,1);
$sheet->setMerge(1,1,1,4);
$format =& $xls->addFormat();
$format->setBold();
$format->setBgColor("blue");
/*$sheet->write(1,1,"Esto es un título");
$sheet->write(0,0,"Recepción");
$sheet->write(0,1,"Nombre");
$sheet->write(0,4,"Respuesta");
$sheet->write(0,5,"Estado");
$sheet->write(1,0,2);*/

$sheet->write(0,0,"20090041260005112"); 
$sheet->write(0,1,"/2009/4126/20090041260005112.tif"); 
$sheet->write(0,2,"2009-06-03 08:21 AM"); 
$sheet->write(0,3,"2009-05-23"); 
$sheet->write(0,4,"20090041260005112"); 
$sheet->write(0,5,"ENVIA PLANILLA PAGO PRIMA COMANDO ANFIBIO PLAN DIARIO BUCEO MAY-09"); 
$sheet->write(0,6,"No definido"); 
$sheet->write(0,7,"428"); 
$sheet->write(0,8,"Comando Infanteria de Marina"); 
$sheet->write(0,9,"3");
 $sheet->write(0,10,"NO TIENE"); 
$sheet->write(0,11,"TCCIM CARLOS ALBERTO JURADO MEDINA");
$sheet->write(0,12,"BATALLON FLUVIAL DE I.M. # 70 TCCIM ");
$sheet->write(0,13,"");
$xls->close();
?>
