<?php 
session_start();

foreach ($_GET  as $key => $val){ ${$key} = $val;}
foreach ($_POST as $key => $val){ ${$key} = $val;}
$ruta      = '/include';
$ruta_raiz = "..";  
if (!$_SESSION['dependencia'])
    header ("Location: $ruta_raiz/cerrar_session.php");
include_once($ruta_raiz.'/config.php');
include_once("connectIMAP2.php");
$codusuario  = $_SESSION["codusuario"];
$dependencia = $_SESSION["dependencia"];
?>
<html>

<head>
  <link rel="stylesheet" href="<?=$ruta_raiz."/estilos/".$_SESSION["ESTILOS_PATH"]?>/orfeo.css">
  <style type="text/css">
    #flotante { position: absolute; top:100; left: 550px; visibility: visible;}
  </style>

<script>
    function asociarMail(){
        numeroRad = parent.frames['formulario'].document.getElementById('numeroRadicado').value;
        if(numeroRad>=1){
            document.getElementById('numeroRadicado').value = numeroRad;
            document.getElementById('formAsociarMail').submit();
        }else{
            alert(" ? No se generado un Radicado ! ");
        }
    }
</script>

</head>

<body>
  <form method=get name=formasociarmail id=formAsociarMail action='mensaje.php'>
    <input type=hidden name=numeroRadicado id=numeroRadicado>
    <input type=hidden name=passwdEmail value=<?=$passwdEmail?> >
    <input type=hidden name=usuaEmail  	value=<?=$usuaEmail?> >
    <input type=hidden name=msgNo  		value=<?=$msgNo?> >
    <input type=hidden name=krd  		value=<?=$krd?> >
    <input type=hidden name=PHPSESSID  	value=<?=$PHPSESSID?> >
    <input type=hidden name=dependencia value=<?=$dependencia?> >
  </form>
<?
//-------Funcion Suprime caracteres no imprimibles------------------------//
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

if($msgNo){

    $datos   = $msg->getHeaders($msgNo);
    $msgPid  = $msg->structure[$msgNo]["pid"];

    $contenidoEmail      = $head;
    $cuerpoMail          =
    $MailAdjuntos        =
    $eMailRemitente      =
    $eMailsubject        =
    $mailAsunto          =
    $mailFecha           =
    $eMailNombreRemitent =
    $eMaildate           =
    $mailFrom            = null;
    $iAnexo              = 0;

    $eMailRemitente       = $mailFrom    = sup_tilde(imap_utf8($msg->header[$msgNo]['from'][0]));
    $eMailsubject         = $mailAsunto  = sup_tilde(imap_utf8($msg->header[$msgNo]['subject']));
    $eMaildate            = $mailFecha   = sup_tilde(imap_utf8($msg->header[$msgNo]['date']));
    $eMailNombreRemitente = $mailRemite  = sup_tilde(imap_utf8($msg->header[$msgNo]['from_personal'][0]));

    $_SESSION['eMailRemitente']       = $eMailRemitente;
    $_SESSION['eMailNombreRemitente'] = $eMailNombreRemitente;
    $_SESSION['eMailSubject']         = $eMailsubject;
    $_SESSION['eMailtipoMedio']       = 'eMail';

    $pop3->RetrieveMessage($msgNo,$headers,$body,100);

    $headRadicado = "
        <TABLE width=\"100%\" cellspacing=\"7\" border=\"0\" cellpadding=\"0\" class=\"borde_tab\" >
        <tr><td width=60%>&nbsp;</td>
        <td >
        <FONT face='free3of9,FREE3OF9, FREE3OF9X,Free 3 of 9' SIZE=12>*$numeroRadicado*</FONT><br>
        Radicado No. $numeroRadicado<br>
        Fecha : ".date("Y/m/d")."
        </td></tr>
        </TABLE>";
    $mailFromD = split(' ', $mailFrom);
    $countC = count($mailFromD);
    if( $countC >=2 ) {
        $mailFromD = $mailFromD[($countC-1)];
        $mailFromD = str_replace("<","",trim($mailFromD));
        $mailFromD = str_replace(">","",$mailFromD);
    }else{
        $mailFromD = trim($mailFrom);
    }


    $encabezado = "krd=$krd&PHPSESSID=".session_id()."&eMailMid=$msgNo&ent=2&eMailMid=$msgNo&datoP=".md5($krd)."&rtb=".md5("aa22")."&tipoMedio=eMail&usuaEmail=$usuaEmail&passwdEmail=$passwdEmail&mailFrom=$mailFromD?>&mailAsunto=".str_replace("#"," ",htmlentities($mailAsunto));
?>
    <table width="100%" class="borde_tab">
      <TR class=titulos2><TD align=right>
      <font size=1>
      <a href='../radicacion/chequear.php?<?=$encabezado?>' target='formulario'>
      Radicar Este Correo</a> ||
      <a href='#' onClick="asociarMail();">
      Asociar Mail a Radicado</a> ||
      <a href='#' onClick="window.open('deleteMail.php?<?=$encabezado?>','Borrando_Mail','menubar=1,resizable=1,width=200,height=150'); ">
      Borrar Email</a> || 
      <a href='browse_mailbox.php?<?="krd=$krd&PHPSESSID=".session_id()?>' target='formulario'>
      Bandeja de entrada</a></font>  
    </td></TR></table>
<?


    $head .=$headRadicado;

    $head .="<table><tr><td></td></tr></table><table class=borde_tab width=100%>
        <TR><TD CLASS=titulos2 width=15%>Correo</TD>
        <TD CLASS=LISTADO2>$mailFrom</TD></TR>
        <TR> <TD CLASS=titulos2>Nombre</TD>
        <TD CLASS=LISTADO2>$mailRemite</TD></TR
        <TR> <TD CLASS=titulos2> Fecha </TD>
        <TD CLASS=LISTADO2>$mailFecha</TD></TR
        <TR><TD CLASS=titulos2>Asunto </TD>
        <TD CLASS=LISTADO2>$mailAsunto</TD></TR>";

//----------------------------------------------------------------------------//
// Asociar el mensaje al radicado
//----------------------------------------------------------------------------//
    foreach($msgPid as $key => $value){

        $entro = 2;
        $body  = $msg->getBody($msgNo,$value);

        if($body["ftype"]=="text/html" || $body["ftype"]=="text/plain") {

            if($body["charset"] == 'utf-8')
            {
                $cuerpoMail = $body["message"];

            }elseif($body["charset"] == 'windows-1252' ||
                    $body["charset"] == 'iso-8859-1' )
            {
                $cuerpoMail = "<pre>". utf8_encode($body["message"]);

            }elseif(empty($body["charset"]))
            {
                $cuerpoMail = $body["message"];

            }else
            {
                $cuerpoMail = "<pre>". utf8_encode($body["message"]);
            }
            $entro = 1;
        }

        if($body["ftype"]=="text/plain"){
            $entro = 2;
        }

        if(($body["ftype"]=="image/jpeg" or $body["ftype"]=="image/gif" or $body["ftype"]=="image/png") 
            and !empty($body[fname])){

            $fname = explode('.',$body["fname"],2);
            $buscarReg = '/cid:'.$fname[0].'(.*[a-z0-9])@(.*)"/';
            $buscarReg = '/cid:'.$fname[0].'(.*[a-z0-9])/';

			$imagenPbExt = str_replace("image/","",$body["ftype"]);

			if($imagenPbExt=="jpeg") $imagenPbExt= "jpg";
			
            $imagen      = "../bodega/tmp/".preg_replace('/[^a-zA-Z0-9.]/i', '_',trim($body["fname"]));
            $imagenMail  = $parts[0];
            $imagenMailX = explode('"',$imagenMail,2);
            $imagenMail  = $imagenMailX[0];
            $cuerpoMail  = str_replace(str_replace('"','',$imagenMail),$imagen, $cuerpoMail);

            //echo "<pre>". htmlspecialchars($cuerpoMail);
            //echo "<hr>";
            
            //preg_replace('/(src)(.)+\"/', $imagen, $cuerpoMail, 1);

            //echo "<pre>". htmlspecialchars($cuerpoMail);
            //$imagenPb      =  'title="'.$fname[0] ."." .$imagenPbExt.'"';
            //$imagenPbFinal =  'src="../bodega/tmp/'.$fname[0] ."." .$imagenPbExt.'" ';
            //$cuerpoMail = str_replace($imagenPb,$imagenPbFinal, $cuerpoMail);

            $file = fopen($imagen,"w");
            
            if(!fputs($file,$body["message"])) echo "<hr> No se guardo Imagen.  $imagen";
            fclose($file);
        }

        if($numeroRadicado){
            include_once "../include/db/ConnectionHandler.php";
            $db = new ConnectionHandler("..");
            $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        }

        if($entro==2 and $body["fname"]){
            $iAnexo++;
            $fname  = preg_replace('/[^a-zA-Z0-9.]/i', '', trim(sup_tilde($body["fname"])));
            $imagen = "../bodega/tmp/".$fname;

            if(!$numeroRadicado){
                $file = fopen($imagen,"w");
                if(!fputs($file,$body["message"])) echo "<hr> No se guardo Archivo.  $imagen";
                fclose($file);
                $mailAdjuntos .= "<a target='_blank' href='$imagen'>".$fname."</a><br>";
            }else{
                $sqll = "SELECT 
                            USUA_LOGIN 
                         FROM 
                            USUARIO 
                         WHERE 
                             USUA_CODI = $codusuario 
                             AND DEPE_CODI = $dependencia";

                $rss    = $db->conn->query($sqll);
                $usulog = $rss->fields["USUA_LOGIN"];

                $aExtension  = substr($fname,-5,5);
                $aExt        = split(".",$fname,2);
                $codigoAnexo = $numeroRadicado."000$iAnexo";
                $fina        = explode(".", $aExt[1]);
                $bExt        = $fina[count($fina)-1];
                $iSql        = "SELECT ANEX_TIPO_CODI FROM ANEXOS_TIPO WHERE ANEX_TIPO_EXT = '".$bExt."'";
                $rs          = $db->conn->query($iSql);
                $anexTipo    = $rs->fields["ANEX_TIPO_CODI"];
                if(!$anexTipo) $anexTipo = 0;
                $nomcort      = substr($aExt[1],-30);
                $nomcort      =  preg_replace('/[^a-zA-Z0-9.]/i', '_',trim($nomcort));
                $tmpNameEmail = $numeroRadicado."_000".$iAnexo.$nomcort;
                $directorio   = substr($numeroRadicado,0,4) ."/".$_SESSION["dependencia"] ."/docs/";
                $fileEmailMsg = "../bodega/$directorio".$tmpNameEmail;
                $file         = fopen($fileEmailMsg,"w");
                if(!fputs($file,$body["message"])) echo "<hr> No se guardo Archivo.  $imagen";
                fclose($file);
                $mailAdjuntos .= "<a target='_blank' href='$fileEmailMsg'>".$nomcort."</a><br>";
                $cuerpoMail = str_ireplace($imagen,$fileEmailMsg,$cuerpoMail);
                $fecha_hoy  = Date("Y-m-d");
                if(!$db->conn) echo "No hay conexion";
                $sqlFechaHoy=$db->conn->DBDate($fecha_hoy);
                $record["ANEX_RADI_NUME"]    = $numeroRadicado;
                $record["ANEX_CODIGO"]       = $codigoAnexo;
                $record["ANEX_SOLO_LECT"]    = "'S'";
                $record["ANEX_CREADOR"]      = "'$usulog'";
                $record["ANEX_DESC"]         = "' Archivo:.". $fname."'";
                $record["ANEX_NUMERO"]       = $iAnexo;
                $record["ANEX_NOMB_ARCHIVO"] = "'".$tmpNameEmail."'";
                $record["ANEX_BORRADO"]      = "'N'";
                $record["ANEX_DEPE_CREADOR"] = $dependencia;
                $record["SGD_TPR_CODIGO"]    = '0';
                $record["ANEX_TIPO"]         = $anexTipo;
                $record["ANEX_FECH_ANEX"]    = $sqlFechaHoy;
                $db->insert("anexos", $record, "true");
            }

        }

        if(sup_tilde($msg->header[$msgNo]['from'][0]) and !$pidMail) $pidMail=$value;

    }

    echo $head;
    $cuerpoMail  =  "<TABLE class=borde_tab WIDTH=100%><tr><td>".$cuerpoMail."</td></tr></table>";
    $cuerpoMail  =  sup_tilde(imap_utf8($cuerpoMail));


    if($cuerpoMail) echo $cuerpoMail; else $cuerpoMailPlain;
    
    if($mailAdjuntos){
        $adjuntosHtml =  "
            <table><tr><td></td></tr></table><table class=borde_tab width=100%><tr><td class=titulos2>
            Archivos Adjuntos
            </td></tr><tr><td class=listado2>$mailAdjuntos
            </td></tr></table>";
        echo $adjuntosHtml;
    }

    if($numeroRadicado){
        $archivoRadicado = "";
        $tmpNameEmail    = $numeroRadicado.".html";
        $depenx          = $_SESSION["dependencia"] * 1;
        $directorio      = substr($numeroRadicado,0,4) ."/".$depenx."/";

        $fileRadicado    = "../bodega/$directorio".$tmpNameEmail;
        $archivoRadicado = "<HTML>
            <HEAD>
            <link rel='stylesheet' href='../estilos/orfeo.css'>
            <meta name='tipo_contenido'  content='text/html;' http-equiv='content-type' charset='utf-8'>
            </HEAD>
            <BODY>".
            $head .
            "".
            $cuerpoMail.
            ""
            ."<hr>".$adjuntosHtml
            ."</BODY></HTML>";
        $archivoRadicado = str_replace("../","",$archivoRadicado);
        $file1=fopen($fileRadicado,'w');
        fputs($file1,$archivoRadicado);
        fclose($file1);
        str_replace('..','',$fileRadicado);
        $isqlRadicado = "update radicado set RADI_PATH = '$fileRadicado' where radi_nume_radi = $numeroRadicado";
        $rs=$db->conn->query($isqlRadicado);
        //print("Ha efectuado la transaccion($isql)($dependencia)");
        if (!$rs)	//Si actualizo BD correctamente
        {	
            echo "Fallo la Actualizacion del Path en radicado < $isqlRadicado >";
        }else{
            $radicadosSel[] = $numeroRadicado;
            $codTx = 42;	//C?digo de la transacci?n
            $noRadicadoImagen = $numeroRadicado;
            $observa = "Mail(".utf8_decode($mailAsunto).")";
            include "$ruta_raiz/include/tx/Historico.php";

            $hist        = new Historico($db);
            $codusuario  = $_SESSION["codusuario"];
            $dependencia = $_SESSION["dependencia"];

            $hist->insertarHistorico($radicadosSel,  $dependencia , $codusuario, $dependencia, $codusuario, $observa, $codTx);
echo "xxxx";
            include "enviarMail.php";
        }
    }

}else{
    print("No hay Correo disponible");
}
//--Variable con la Cabecera en formato html----------------------------------//

?>
</body>
</html>
