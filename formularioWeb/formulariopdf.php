<?php
session_start();
/**
  * Se añadio compatibilidad con variables globales en Off
  * @autor Jairo Losada 2009-05
  * @Fundacion CorreLibre.org
  * @licencia GNU/GPL V 3
  */

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

require('barcode.php');
include('funciones.php');

$pdf=new PDF_Code39();
$pdf->AddPage();
$pdf->Code39(110,45,$_SESSION['radcom'],1,10);
$pdf->Image('../imagenes/PIEDEPAGINA_1.gif',30,275,160,19);
$pdf->Image('../logoEntidadWeb.gif',55,10,100,24);
$pdf->Text(110,63,"Entidad Usuaria de Orfeo Rad No. ".$_SESSION['radcom']);
$pdf->Text(110,67,"Fecha : ".date('d')."/".date('m')."/".date('Y')." ".date('h:i:s'));
$pdf->Text(110,71,strtoupper($_SESSION['sigla']));
$pdf->Text(110,75,$_SESSION['nit']);
$pdf->Text(12,87,"Monteria, ".date('d')." de ".nombremes(date('m'))." de ".date('Y'));
$pdf->Text(12,101,"Senores");
$pdf->SetFont('','B');
$pdf->Text(12,105,$_SESSION['entidad']);
$pdf->SetFont('','');
$pdf->Text(12,109,"Ciudad");
$pdf->Text(12,119,"Asunto : ".strtoupper($_SESSION['asunto']));
$pdf->SetXY(11,125);
$pdf->MultiCell(0,4,$_SESSION['desc'],0);
$pdf->Text(12,236,"Atentamente,");
$pdf->SetFont('','B');
$pdf->Text(12,246,strtoupper($_SESSION['nombre_remitente'])." ".strtoupper($_SESSION['apellidos_remitente']));
$pdf->SetFont('','');
$pdf->Text(12,250,$_SESSION['cedula']);
$pdf->Text(12,254,$_SESSION['direccion_remitente']);
$pdf->Text(12,258,$_SESSION['telefono_remitente']);
$pdf->Text(12,262,$_SESSION['email']);
//guarda documento en un SERVIDOR
 // $pdf->Output("../bodega/tmp/".$_SESSION['radcom'].".pdf",'F');
$pdf->Output("../bodega/$rutaPdf",'F');
/*
//envia el archivo a un SERVIDOR por FTP
$archivo = "C:\\www\\data\\quejas\\".$_SESSION['radcom'].".pdf";
$archivo_remoto = '/'.date('Y').'/440/'.$_SESSION['radcom'].'.pdf';

// configurar la conexion basica
$id_con = ftp_connect('192.127.28.10');

// iniciar sesion con nombre de usuario y contrasenya
//$resultado_login = ftp_login($id_con, 'orfeo','orfeo');
$resultado_login = ftp_login($id_con, 'pruebas','pruebas');
// cargar un archivo
$texto=$_SESSION['radcom']." ";
if (ftp_put($id_con, $archivo_remoto, $archivo, FTP_BINARY)) {
  $texto.="se ha cargado $archivo satisfactoriamente ";
} else {
 $texto.="Hubo un problema durante la transferencia de $archivo ";
}
$texto.=date('Y-m-d h:i:s')."\n";
// cerrar la conexion
ftp_close($id_con);
*/

//escribe el log
		$nombre_archivo = '../bodega/log/quejas.txt';
		$gestor = fopen($nombre_archivo, 'a');
		fwrite($gestor, $texto);
		fclose($gestor);

// muestra el pdf
$pdf->Output();
?>
