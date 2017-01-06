<?php
if(!session_id()) session_start();

foreach ($_GET  as $key => $val){ ${$key} = $val;}
foreach ($_POST as $key => $val){ ${$key} = $val;}

$ruta      = '/include';
$ruta_raiz =  "..";  

if (!$_SESSION['dependencia'])
    header ("Location: $ruta_raiz/cerrar_session.php");

include_once "$ruta_raiz/config.php";

$usuaDoc = $_SESSION['usua_doc'];

if($_SESSION['usua_email'])     $usuaEmail      = $_SESSION['usua_email'];
if($_SESSION['servidor_mail'])  $servidor_mail  = $_SESSION['servidor_mail'];
if($_SESSION['puerto_mail'])    $puerto_mail    = $_SESSION['puerto_mail'];
if($_SESSION['protocolo_mail']) $protocolo_mail = $_SESSION['protocolo_mail'];
if($_SESSION['passwd_mail'])    $passwd_mail    = $_SESSION['passwd_mail'];

if(empty($_SESSION['passwd_mail']) && $passwd_mail)
    $_SESSION['passwd_mail'] = $passwd_mail;

//$usuaEmail = "sistemas@mallamaseps.com.co";  // login.microsoftonline.com
/**
correo: sistemas@mallamaseps.com.co
**/
$passwdEmail = "Corsis*2014";     
$servidor_mail = "outlook.office365.com";
list($a,$b)    = preg_split('/[@]/',$usuaEmail);
$usuaEmail1    = $a;
require("pop3.php");
$user                           = $usuaEmail;    // Authentication user name */
$password                       = $passwd_mail;   // Authentication password  */

$apop                           = 0;              // Use APOP authentication  */
$pop3                           = new pop3_class();
$pop3->hostname                 = $servidor_mail;
$pop3->port                     = $puerto_mail;
$pop3->port                     = 995;
$pop3->tls                      = 1;     /* Establish secure connections using TLS      */
$pop3->realm                    = "";    /* Authentication realm or domain              */
$pop3->workstation              = "";    /* Workstation for NTLM authentication         */
$pop3->authentication_mechanism = "USER";/* SASL authentication mechanism               */
$pop3->debug                    = 2;     /* Output debug information                    */
$pop3->html_debug               = 1;     /* Debug information is in HTML                */
$pop3->join_continuation_header_lines=1; /* Concatenate headers split in multiple lines */
$pop3->Open();

//$connect = $pop3->Login($a,$password,$apop);

//var_dump($connect);

echo " $a,$password,$apop ";
$connect = $pop3->Login($usuaEmail,$password,$apop);
var_dump($connect);
?>
 
