<?php
session_start();
$krd = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
import_request_variables("gp", "");
include '../config.php';
$krd =  $_SESSION["krd"];
$dependencia =  $_SESSION["dependencia"];
$_SESSION['eMailAmp']="";
$_SESSION['eMailMid']="";
$_SESSION['eMailPid']="";
$_SESSION['fileeMailAtach']="";
$_SESSION['tipoMedio']="";
//if($_SESSION["msg"]) $msg = $_SESSION["msg"];
$usuaEmail=$_SESSION['usuaEmail'];
$usuario_mail=$_SESSION['usua_email'];
list($a,$b)=split("@",$usuaEmail);
$usuario_mail = $a;
$dominioEmail=$_SESSION['dominioEmail'];
$passwdEmail=$_SESSION['passwdEmail'];
if(!$passwdEmail)
{
  $splitEmail = split("@",$usua_email);
  $usuaEmail = $splitEmail[0];
  $dominioEmail = $splitEmail[1];
  $_SESSION['usuaEmail']=$usuaEmail;
  $_SESSION['dominioEmail']=$dominioEmail;
  $_SESSION['passwdEmail']=$passwd_mail;
}
if(!$dominioEmail)
{
	$splitEmail = split("@",$usua_email);
	$usuaEmail = $splitEmail[0];
	$dominioEmail = $splitEmail[1];
}
$ruta_raiz = "..,";
//if(!$dependencia or !$krd) include "../rec_session.php";
// var_dump($_SESSION);
require_once $PEAR_PATH."Mail\\IMAPv2.php";
list($a,$b)=split("@",$usuario_mail);
$usuaEmail1=$a;
if($_GET['inboxEmail'])
{
  $buzon_mail = $_GET['inboxEmail'];
}else{
	$buzon_mail = "INBOX";
}
$_SESSION['buzon_mail'] = $buzon_mail ;
$_SESSION['opciones_mail'] = $opciones_mail ;
//if(!$msg){
//       echo "Entro a Recargar session . . . ";
include "connectIMAP.php";
//$_SESSION["msg"] = $msg;
//}
//------------------------Funcion Suprime caracteres extra�os----------------------------//
 function sup_tilde($str)
{
 $stdchars= array(" at ","a","e","i","o","u","n","A","E","I","O","U","N"," "," ");
 $tildechars= array("@","=E1","=E9","=ED","=F3","=FA","=F1","=C1","=C9","=CD","=D3","=DA","=D1","=?iso-8859-1?Q?","?=");
 return str_replace($tildechars,$stdchars, $str);
}
//---------------------------------------------------------------------------------------//
//------------- Abre buzon y conexion y cuenta cuantos mensajes existen------------------//

if (!$msg)
{
  header("location: login_email.php?err=1"); 
  echo "<br><span style='font-weight: bold;'>Error:</span> No se pudo establecer coneccion con el Servidor.";
  $_SESSION['passwdEmail']="";
}

$msgcount = $msg->messageCount();

//----------------------------------------------------------------------------------------//
?>
<html>
<head>
<title> Entradas Pendientes </title>
<link rel="stylesheet" href="../estilos/orfeo.css" />
</head>
<body>
<?php
echo " ". $msg->mailboxInfo['folder'].":(".$msgcount.") messages total.<br>.mailbox:".$msg->mailboxInfo['user'];
//echo var_dump($_SESSION);
?>
<table  class="borde_tab" width="100%">
<tr class="titulos3">
<td colspan="5" align="center">EMAILS DE ENTRADA (<?=$usuaEmail?>@<?=$dominioEmail?>) Buzon <?=$buzon_mail?></td>
</tr>
<tr class="titulos5">
<th>
Asunto
</th>
<th>
Remitente
</th>
<th>
Fecha
</th>
<th>
Adjuntos
</th>
</tr>



<?php
if ($msgcount > 0)
{
 $stl=1;
 if($msgcount>=50) $msgcount=50;
 for ($mid = 1; $mid <= $msgcount; $mid++)
 //for ($mid = 1; $mid <= 50; $mid++)
  {
  // Lee las cabecera
  $msg->getHeaders($mid);
  $style = ((isset($msg->header[$mid]['Recent']) && $msg->header[$mid]['Recent'] == 'N') || (isset($msg->header[$mid]['Unseen']) && $msg->header[$mid]['Unseen'] == 'U'))? 'gray' : 'black';
  $msg->getParts($mid);
  if (!isset($msg->header[$mid]['subject']) || empty($msg->header[$mid]['subject']))
  {
  $msg->header[$mid]['subject'] = "<span style='font-style: italic;'>no subject provided</a>";
  }

  echo " <tr class=listado$stl>",
  " <td class='msgitem'>
	<a href='mensaje.php?mid=$mid&amp;pid=".$msg->msg[$mid]['pid']."' target='image'>".
	$msg->header[$mid]['Subject']
	."</a>
    </td>\n".
  " <td class='msgitem'>\n".
  " ", (isset($msg->header[$mid]['from_personal'][0]) && !empty($msg->header[$mid]['from_personal'][0]))? '<span title="'.sup_tilde($msg->header[$mid]['from'][0]).'">'.sup_tilde($msg->header[$mid]['from_personal'][0])."</span>" : sup_tilde( $msg->header[$mid]['from'][0]), "\n",
  " </td>\n",
  " <td class='msgitem'>".date('D d M, Y h:i:s', $msg->header[$mid]['udate'])."</td>\n",
  " <td class='msgitem'>";
	
	/*/Visualiza Inline Parts-----------------------------------------------------------------------
	  if (isset($msg->msg[$mid]['in']['pid']) && count($msg->msg[$mid]['in']['pid']) > 0)
    {
    foreach ($msg->msg[$mid]['in']['pid'] as $i => $inid)
    {
    $fname = (isset($msg->msg[$mid]['in']['fname'][$i]))? $msg->msg[$mid]['in']['fname'][$i] : "No Disponible";
    echo "<a href='attachement.php?mid=$mid&amp;pid=".$msg->msg[$mid]['in']['pid'][$i]."' target='_blank'><img src='../img/ath1.jpg' width=18 height=18 alt='".$fname."' title='".$fname."'></a><br />\n";
    }
    }
		*/
  // Visualiza attachments------------------------------------------------------------------------

  if (isset($msg->msg[$mid]['at']['pid']) && count($msg->msg[$mid]['at']['pid']) > 0)
    {
    foreach ($msg->msg[$mid]['at']['pid'] as $i => $aid)
    {
    $fname = (isset($msg->msg[$mid]['at']['fname'][$i]))? $msg->msg[$mid]['at']['fname'][$i] : "No Disponible";
    echo "<a href='attachement.php?mid={$mid}&amp;pid=".$msg->msg[$mid]['at']['pid'][$i]."' target='_blank' border=0><img src='../img/flujo/docBlanco.gif' border=0 width=18 height=18 alt='".$fname."' title='".$fname."'></a>";
$fname = (isset($msg->msg[$mid]['at']['fname'][$i]))? $msg->msg[$mid]['at']['fname'][$i] : NULL;
    echo " <a href='attachement.php?mid={$mid}&amp;pid=".$msg->msg[$mid]['at']['pid'][$i]."' target='_blank'>".$fname." ".$msg->msg[$mid]['at']['ftype'][$i]." ".$msg->convertBytes($msg->msg[$mid]['at']['fsize'][$i])."</a><br />\n";
//echo "$fname";
    }
    }
		else
		echo "</br>";
	//echo "<a href='' ><img src='./iconos/anexos.gif' width=18 height=18 alt='Carpeta Actual: Entrada -- Numero de Hojas :0' title='Carpeta Actual: Entrada -- Numero de Hojas :0'></a>";
	echo "</td>";
	//echo "<td><a href='../radicacion/chequear.php?".session_name()."=".session_id()."&ent=2&eMailMid={$mid}&eMailAmp&eMailPid={$msg->msg[$mid]['pid']}&fileeMailAtach=".$fname."&tipoMedio=eMail'>Radicar</a></td>",	
   echo    "</tr>\n";
   /*
  // In-line Parts no borrar

  if (isset($msg->msg[$mid]['in']['pid']) && count($msg->msg[$mid]['in']['pid']) > 0)
    {
    foreach ($msg->msg[$mid]['in']['pid'] as $i => $inid)
    {
    $fname = (isset($msg->msg[$mid]['in']['fname'][$i]))? $msg->msg[$mid]['in']['fname'][$i] : NULL;
    echo " Inline part: <a href='attachement.php?mid={$mid}&amp;pid=".$msg->msg[$mid]['in']['pid'][$i]."' target='_blank'>".$fname." ".$msg->msg[$mid]['in']['ftype'][$i]." ".$msg->convertBytes($msg->msg[$mid]['in']['fsize'][$i])."</a><br />\n";
    }
    }

  // Attachments no borrar

  if (isset($msg->msg[$mid]['at']['pid']) && count($msg->msg[$mid]['at']['pid']) > 0)
    {
    foreach ($msg->msg[$mid]['at']['pid'] as $i => $aid)
    {
    $fname = (isset($msg->msg[$mid]['at']['fname'][$i]))? $msg->msg[$mid]['at']['fname'][$i] : NULL;
    echo " Attachment: <a href='attachement.php?mid={$mid}&amp;pid=".$msg->msg[$mid]['at']['pid'][$i]."' target='_blank'>".$fname." ".$msg->msg[$mid]['at']['ftype'][$i]." ".$msg->convertBytes($msg->msg[$mid]['at']['fsize'][$i])."</a><br />\n";
    }
    }*/
		if($stl==1)
	  $stl=2;
		else
		$stl=1;
}

}
else
{
echo "<tr><td colspan='3' style='font-size: 30pt; text-align: center; padding: 50px 0;'>No hay Mensajes</td></tr>";
}
echo "</table>";
/**if ($quota = $msg->getQuota())
{
        if(!$jdjdjdj) die("<hr>Se conecto .. ....");    
echo " Quota: {$quota['STORAGE']['usage']} useados de un total de{$quota['STORAGE']['limit']}\n";
}
if(!$jdjdjdj) die("<hr>Se conecto .. ....");    **/
$msg->close();
?>
</div>

<div align="center" >
<p>" &copy; BT, .<br /></p>
</div>
</body>
</html>
