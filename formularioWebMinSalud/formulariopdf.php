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
include_once "../config.php";
$ruta_raiz= "..";
require_once("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler($ruta_raiz);

$sql_depeNomb = "select depe_nomb from dependencia where depe_codi = ". $_SESSION['depeRadicaFormularioWeb'];
				$rs_depeNomb = $db->conn->Execute($sql_depeNomb);
				if(!$rs_depeNomb->EOF){
					$depeNomb = substr($rs_depeNomb->fields["DEPE_NOMB"],0,40);
				}
				
$pdf=new PDF_Code39();
$pdf->AddPage();

$pdf->Code39(110,25,$_SESSION['radcom'],1,8);
$pdf->Text(130,37,textoPDF("Radicado N°. ".$_SESSION['radcom']));
$pdf->Image('images/logo_entidad_radicacion_web.gif',20,20,75);
//$pdf->SetFont('Arial','',16);
//$pdf->Text(110,40,textoPDF(textoPDF($entidad_largo)));
$pdf->Text(110,41,textoPDF(date('d')." - ".date('m')." - ".date('Y')." ".date('h:i:s')) . "   Folios: N/A (WEB)   Anexos: ". $_SESSION['cantidad_adjuntos'] );
$pdf->SetFont('Arial','',8);
$pdf->Text(110,45,textoPDF("Destino: ". $depeNomb ." - Rem/D: ". textoPDF(substr($_SESSION['nombre_remitente'],0,10))." ".textoPDF(substr($_SESSION['apellidos_remitente'],0,10))));
$pdf->SetFont('Arial','',7);
$pdf->Text(110,48,textoPDF("Consulte el estado de su trámite en nuestra página web http://www.correlibre.org"));
$pdf->Text(135,51,textoPDF("Código de verificación: " . $_SESSION['codigoverificacion']));
//$pdf->Text(110,51,textoPDF(strtoupper($_SESSION['nombre_remitente'])." ".strtoupper($_SESSION['apellidos_remitente'])));
//$pdf->Text(110,55,$_SESSION['cedula']!='0'?$_SESSION['cedula']:$_SESSION['nit']);

$pdf->Text(12,67,textoPDF("Bogotá D.C., ".date('d')." de ".nombremes(date('m'))." de ".date('Y')));
$pdf->Text(12,81,textoPDF("Señores"));
$pdf->SetFont('','B');
$pdf->Text(12,85,textoPDF($entidad_largo));
$pdf->SetFont('','');
$pdf->Text(12,89,textoPDF("Ciudad"));
$pdf->Text(12,99,textoPDF("Asunto : ".mb_strtoupper(textoPDF($_SESSION['asunto']))));
$pdf->SetXY(11,105);
//$pdf->MultiCell(0,4,textoPDF($_SESSION['desc'],0));
$pdf->MultiCell(0,4,$_SESSION['desc'],0);
$pdf->Text(12,236,"Atentamente,");
$pdf->SetFont('','B');
$pdf->Text(12,246,textoPDF(textoPDF(($_SESSION['nombre_remitente']))." ".textoPDF($_SESSION['apellidos_remitente'])));
$pdf->SetFont('','');
$pdf->Text(12,250,$_SESSION['cedula']!='0'?$_SESSION['cedula']:$_SESSION['nit']);
$pdf->Text(12,254,textoPDF($_SESSION['direccion_remitente']));
$pdf->Text(12,258,textoPDF($_SESSION['telefono_remitente']));
$pdf->Text(12,262,textoPDF($_SESSION['email']));
//guarda documento en un SERVIDOR
 // $pdf->Output("../bodega/tmp/".$_SESSION['radcom'].".pdf",'F');
$pdf->Output("../bodega/$rutaPdf",'F');

//Realizar el conteo de hojas del radicado final//
$conteoPaginas = getNumPagesPdf("../bodega/$rutaPdf");

$sqlu = "UPDATE radicado SET radi_nume_hoja= $conteoPaginas where radi_nume_radi=" . $_SESSION['radcom'];
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC); 
$db->conn->Execute($sqlu);


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

////escribe el log
//		$nombre_archivo = '../bodega/log/quejas.txt';
//		$gestor = fopen($nombre_archivo, 'a');
//		fwrite($gestor, $texto);
//		fclose($gestor);

// muestra el pdf
$pdf->Output();
?>
