<?php
 include_once '../config.php';
 $PEAR_PATH = $_SESSION["PEAR_PATH"];
 require_once $PEAR_PATH."Mail\\IMAPv2.php";
 $passwdEmail=$_SESSION['passwdEmail'];
 $usuaEmail = $_SESSION['usuaEmail'];
 $usuaDoc = $_SESSION['usua_doc'];
 if($_SESSION['usuario_mail'] and !$usuaEmail) $usuario_mail=$_SESSION['usuario_mail'];
 if($usua_email) $usuario_mail = $usuaEmail;
 if($_SESSION['servidor_mail']) $servidor_mail = $_SESSION['servidor_mail'];
 if($_SESSION['puerto_mail']) $puerto_mail = $_SESSION['puerto_mail'];
 if($_SESSION['protocolo_mail']) $protocolo_mail = $_SESSION['protocolo_mail'];
 $tmpNameEmail = "tmpEmail_".$usuaDoc."_".md5(date("dmy hms")).".html";
 $_SESSION['tmpNameEmail'] = $tmpNameEmail;
 $tmpNameEmail = $_SESSION['tmpNameEmail']; 
 if(!$_SESSION['eMailPid'])
 {
  $_SESSION['eMailAmp']=$_GET['mid'];
  $_SESSION['eMailPid']=$_GET['pid'];
  $eMailPid = $_GET['pid'];
  $eMailMid = $_GET['mid'];
  
 }else{
  $eMailPid = $_SESSION['eMailPid'];
  $eMailMid = $_SESSION['eMailMid'];
  $eMailAmp = $_SESSION['eMailAmp'];
 }
 
 list($a,$b)=split("@",$usuaEmail);
    require('includes/mime_parser.php');
    require('includes/rfc822_addresses.php');
    require("pop3.php");

  /* Uncomment when using SASL authentication mechanisms */
    /*
    require("sasl.php");
    */

    stream_wrapper_register('pop3', 'pop3_stream');  /* Register the pop3 stream handler class */

    $pop3=new pop3_class;
    $pop3->hostname=$servidor_mail;             /* POP 3 server host name                      */
    $pop3->port=995;                         /* POP 3 server host port,
                                                usually 110 but some servers use other ports
                                                Gmail uses 995                              */
    $pop3->tls=1;                            /* Establish secure connections using TLS      */
    $user="jlosada";                        /* Authentication user name                    */
    $password="Jhlc11726";                    /* Authentication password                     */
    $pop3->realm="";                         /* Authentication realm or domain              */
    $pop3->workstation="";                   /* Workstation for NTLM authentication         */
    $apop=0;                                 /* Use APOP authentication                     */
    $pop3->authentication_mechanism="USER";  /* SASL authentication mechanism               */
    $pop3->debug=0;                          /* Output debug information                    */
    $pop3->html_debug=0;                     /* Debug information is in HTML                */
    $pop3->join_continuation_header_lines=1; /* Concatenate headers split in multiple lines */
    $pop3->Open();
    $pop3->Login($user,$password,$apop);
    
    ?>
 
