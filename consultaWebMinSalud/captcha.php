<?
session_start();
/**
  * @autor Sebastian Ortiz V.
  * @correlibre
  * @licencia GNU/GPL V 3
  */

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;
include('./captcha/simple-php-captcha.php');
header('Content-Type: text/html; charset=UTF-8');
//Si la acccion es recargar
if(isset($recargar) && $recargar=="si"){
	$_SESSION['captcha_consulta'] = captcha();
	echo $_SESSION['captcha_consulta']['image_src'];
	return;
}

if(strcasecmp ($captcha ,$_SESSION['captcha_consulta']['code'] ) == 0){
	echo "true";
}
else {
	echo "false";
}

?>
