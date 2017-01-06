<?php
session_start();

$ruta      = '/bodega';
$ruta_raiz =   "..";  

if (!$_SESSION['dependencia'])
    header ("Location: $ruta_raiz/cerrar_session.php");
    
foreach ($_GET  as $key => $val){ ${$key} = $val;}
foreach ($_POST as $key => $val){ ${$key} = $val;}


?>
<html>
<head>
<title>WebMail OrfeoGpl.org</title>
<link rel="stylesheet" href="../estilos/orfeo.css" />
</head>
<body>
<?php

function sup_tilde($str){

    $stdchars= array("@","a","e","i","o"
                    ,"u","n","A","E","I"
                    ,"O","U","N"," " ," "
                    ,"!","", " ","", ""
                    ,"","","á","é","í"
                    ,"ó","ú");

    $tildechars= array( "@","=E1","=E9","=ED","=F3"
                        ,"=FA","=F1","=C1","=C9","=CD"
                        ,"=D3","=DA","=D1","=?iso-8859-1?Q?","?=",
                        "=A1","=?Windows-1252?Q?", "=20","=?ISO-8859-1?Q?", "=2C",
                        "=2E", "=?ISO-8859-1?B?", "a?","e?","i?",
                        "o?","u?");
    return str_replace($tildechars,$stdchars, $str);
}

include_once("connectPop3.php");

if(!$connect){
	$result=$pop3->ListMessages("",1);
	?>
	<table  class="borde_tab" width="100%" cellpadding="0" cellspacing="0">
	<tr class=titulos2>
		<th colspan=6><br/><h2 align="left">Buzon de <?=strtoupper($user)?> <br></h2>
	</tr>  
	<tr class=titulos4>
	<th>No</th>
	<th>Fecha</th>
	<th>Asunto</th>
	<th>Remite</th>
	<th>Para</th>
	<th>Ad</th>
	</tr>
	<?
	for($i=1; $i<=count($result);$i++){
		$mailAsunto = "";
		$mailFecha  = "";
		$mailFrom   = "";
		$mailToF    = "";
		$mailAttach = "";
		$pop3->RetrieveMessage($i,$headers,$body,12);

		$mailAtach= "";

		for($iK=1; $iK<=count($headers)-1;$iK++){
			if(substr(trim($headers[$iK]),0,8)=="Subject:"){
                $mailAsunto = $headers[$iK];
                $mailAsunto = substr(sup_tilde(imap_utf8(trim($mailAsunto,"Subject:"))),0,70);
				$mailAsunto = empty($mailAsunto)? "Sin asunto .." : $mailAsunto;
			}

			if(substr(trim($headers[$iK]),0,5)=="From:"){
				$mailFrom = substr($headers[$iK],0,150);
                $mailFrom = sup_tilde(imap_utf8($mailFrom));
			}

			if(substr(trim($headers[$iK]),0,3)=="To:"){
				$mailTo = $headers[$iK];
				$mailToArray = array();
				$mailToArray= explode(", ",$mailTo,100);
				$mailto = "";
				$value="";
				
				foreach ($mailToArray as $key =>$value){
					 if($key>=0) { $mailToF .= htmlentities($mailToArray[$key] ) ."<br>" ;}
				}
				$mailToF = substr($mailToF,0,150);
				$mailToF = sup_tilde(imap_utf8(trim($mailToF,"To:"))); 
			}

			if(substr($headers[$iK],0,5)=="Date:") { $mailFecha = $headers[$iK];$mailFecha = str_replace("Date:","",$mailFecha);}
			if(substr($headers[$iK],0,20)=="X-MS-Has-Attach: yes") {$mailAttach= "<img src='../imagenes/correo.gif'>";}
			if(substr($headers[$iK],0,11)=="Message-ID:") { $mailID = $headers[$iK];$mailID = str_replace("Message-ID:","",$mailID);}
		}

		$mailRemite = $headers[0];
		$b=2;
		if((fmod($i,2)==0 )){  $claseLines = "listado1"; }else{ $claseLines = "listado2";}
	?>
		<tr class=<?=$claseLines?>>
			<td width=20 align="center"><?=$i?></td>
			<td width=50><?=$mailFecha?></td>
			<td width=200><a href="mensaje.php?PHPSESSID=<?=session_id()?> &msgNo=<?=$i?> &krd=<?=$krd?> &usuaEmail=<?=$usuaEmail?> &passwdEmail=<?=$passwdEmail?>" target=image><?=$mailAsunto?></a></td>
			<td width=200><?=$mailFrom?></td>
			<td width=300><?=$mailToF?> </td>
			<td width=10><?=$mailAttach?></td>
		</tr>
		<?
	}
}else{
	$err = 1;
	include "login_email.php";
	die;
}
	?>
	</table>
	</BODY>
</HTML>
