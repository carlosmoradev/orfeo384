<?
session_start(); 
ini_set("display_errors",1);
/*
 * Lista Subseries documentales
 * @autor Jairo Losada SuperSOlidaria 
 * @fecha 2009/06 Modificacion Variables Globales.
 */
foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;
$ruta_raiz = ".."; 
var_dump($_FILES);
/**
 * Retorna la cantidad de bytes de una expresion como 7M, 4G u 8K.
 *
 * @param char $var
 * @return numeric
 */
function return_bytes($val)
{	$val = trim($val);
	$ultimo = strtolower($val{strlen($val)-1});
	switch($ultimo)
	{	// El modificador 'G' se encuentra disponible desde PHP 5.1.0
		case 'g':	$val *= 1024;
		case 'm':	$val *= 1024;
		case 'k':	$val *= 1024;
	}
	return $val;
}

/*  REALIZAR TRANSACCIONES
 *  Este archivo realiza las transacciones de radicados en Orfeo.
 */

$ruta_raiz = "..";
include ("$ruta_raiz/include/upload/upload_class.php"); //classes is the map where the class file is stored (one above the root)
$max_size = return_bytes(ini_get('upload_max_filesize')); // the max. size for uploading
$my_upload = new file_upload;
$my_upload->language="es";
$my_upload->upload_dir = "$ruta_raiz/bodega/tmp/"; // "files" is the folder for the uploaded files (you have to create this folder)
$my_upload->extensions = array(".tif", ".pdf",  ".jpg", ".odt", ".png" , ".gif"); // specify the allowed extensions here
//$my_upload->extensions = "de"; // use this to switch the messages into an other language (translate first!!!)
$my_upload->max_length_filename = 150; // change this value to fit your field length in your database (standard 100)
$my_upload->rename_file = true;
var_dump($_FILES);
	$tmpFile = $_FILES['upload']['name'];
	$newFile = "pbDigitalizada";
	$uploadDir = "$ruta_raiz/bodega/digitalizadas/";
  echo $tmpFile ."\n --- ";
	$fileGrb = "$ruta_raiz/bodega/digitalizadas/fila".".".substr($tmpFile,-3);
echo $fileGrb;
	$my_upload->upload_dir = $uploadDir;

	$my_upload->the_temp_file = $_FILES['upload']['tmp_name'];
	$my_upload->the_file = $_FILES['upload']['name'];
	$my_upload->http_error = $_FILES['upload']['error'];
	$my_upload->replace = (isset($_POST['replace'])) ? $_POST['replace'] : "n"; // because only a checked checkboxes is true
	$my_upload->do_filename_check = (isset($_POST['check'])) ? $_POST['check'] : "n"; // use this boolean to check for a valid filename
	//$new_name = (isset($_POST['name'])) ? $_POST['name'] : "";
	if($my_upload->upload($newFile)) {
		// new name is an additional filename information, use this to rename the uploaded file
		$full_path = $my_upload->upload_dir.$my_upload->file_copy;
		$info = $my_upload->get_uploaded_file_info($full_path);
		// ... or do something like insert the filename to the database
    echo "Fila ok $info";
	}else{
		die("Ocurrio un Error la Fila no fue cargada Correctamente ".$my_upload->show_error_string()." - ".nl2br($info)."");
	}
?>
