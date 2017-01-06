<?php
if(!session_id()) session_start();

foreach ($_GET  as $key => $val){ ${$key} = $val;}
foreach ($_POST as $key => $val){ ${$key} = $val;}

$ruta      = '/include';
while(!is_dir($ruta_raiz.$ruta))$ruta_raiz .= empty($ruta_raiz)? "../" : "..";  

if (!$_SESSION['dependencia'])
    header ("Location: $ruta_raiz/cerrar_session.php");

include_once('connectPop3.php');
include_once($PEAR_PATH.'Mail/IMAPv2.php');

if($_SESSION['passwd_mail']) $passwd_mail = $_SESSION['passwd_mail'];
if($_SESSION['usuaEmail'])   $usuaEmail   = $_SESSION['usuaEmail'];

$usuaDoc = $_SESSION['usua_doc'];
if($_SESSION['usuario_mail'] and !$usuaEmail) $usuario_mail=$_SESSION['usuario_mail'];
if($usua_email) $usuario_mail = $usuaEmail;
if($_SESSION['servidor_mail']) $servidor_mail = $_SESSION['servidor_mail'];
if($_SESSION['puerto_mail']) $puerto_mail = $_SESSION['puerto_mail'];
if($_SESSION['protocolo_mail']) $protocolo_mail = $_SESSION['protocolo_mail'];
$_SESSION['tmpNameEmail'] = $tmpNameEmail;

$tmpNameEmail             = "tmpEmail_".$usuaDoc."_".md5(date("dmy hms")).".html";
$tmpNameEmail             = $_SESSION['tmpNameEmail'];

if($_GET["passwd_mail"]) $passwd_mail = $_GET["passwd_mail"];

if(!$_SESSION['eMailPid']){
    $_SESSION['eMailAmp']=$_GET['mid'];
    $_SESSION['eMailPid']=$_GET['pid'];
    $eMailPid = $_GET['pid'];
    $eMailMid = $_GET['mid'];
}else{
    $eMailPid = $_SESSION['eMailPid'];
    $eMailMid = $_SESSION['eMailMid'];
    $eMailAmp = $_SESSION['eMailAmp'];
}

$usuaEmail=$_SESSION['usua_email'];
if(!$usuaEmail and $_GET["usuaEmail"]) $usuaEmail = $_GET["usuaEmail"];
list($a,$b)=split("@",$usuaEmail);
$usuario_mail=$a;
$buzon_mail = $_SESSION['buzon_mail'];
//$connection = "$protocolo_mail://$usuario_mail:$passwd_mail@$servidor_mail:$puerto_mail/$buzon_mail#$opciones_mail";
$connection = "pop3s://$a:$passwd_mail@$servidor_mail:995/INBOX#novalidate-cert";

$msg  = new Mail_IMAPv2();

if (!$msg->connect($connection,false)) 
{
    echo "<span style='font-weight: bold;'>Error:</span> No se pudo realizar la conexion al serv. de correo.";
}
$mbox = $msg->mailbox;
//print_r($msg);
?>
