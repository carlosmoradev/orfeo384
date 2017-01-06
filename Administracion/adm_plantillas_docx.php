<?php 
session_start();
$ruta_raiz = "../";
if (!$_SESSION['dependencia'])
header ("Location: $ruta_raiz/cerrar_session.php");
include_once    ("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler($ruta_raiz);
error_reporting(7);
foreach ($_POST as $key => $valor) ${$key} = $valor;
$doc    = new DOMDocument();
//Abrir o crear el archivo de listado en 
//boveda con el nombre plantillas.txt
$direcTor = "bodega/plantillas/genericas";
$tamArchi = 5054432;
$sqw="select   sgd_trad_descr, sgd_trad_codigo from sgd_trad_tiporad";
$rss = $db->conn->Execute($sqw);
$slc = $rss->GetMenu2('tipo',$_POST['tipo'],'-- seleccione --',false,false,'Class="select" id="tipo"');
//echo $slc;
/****************************************
* creo el directorio con los permisos. 
*****************************************/
if (@!file_exists($direcTor)) 
{ 
    $directorio = mkdir("$direcTor",0777); 
}  
/****************************************
* Plantillas agregar y eliminar 
*****************************************/
function extracta($nomb,$mkd)
{  
extract($_POST);
extract($_GET);
$cambio = chdir($mkd) or die ("no existe directorio..");
$varTemp = "unzip " .  $nomb ." -d  ." ;
$varCp="cp -rf word/* .";
$varDel="for a in `ls | grep -v header* | grep -v footer* | grep -v media*`; do rm -fr \$a; done";
$verificacion = exec($varTemp) or die ("fallo en  extraccion..".$varTemp);
$verificacion= exec($varCp);
$verificacion = exec($varDel);
$ruta_raiz = "../..";
}

if($btn_acc == adjuntar)
{
    $nomb       = "plant".time().rand(0,1000).".docx";
    $uploadfile = $direcTor."/tpx".$tipo."/".$nomb;
    $tipValido  = 'application/vnd.oasis.opendocument.text';       
    $ruta = exec("cd ..;pwd");
    $mkd=$ruta."/".$direcTor."/tpx".$tipo;
    $uploadfile =$mkd."/".$nomb;
    if(!is_dir($mkd))
    {
    $varMk = mkdir("$mkd",0777) or die ("No pudo crear directoriox".$direcTor ); 
    }
    else
    {
    $cambio = chdir($mkd); 
     $verificacion = exec("rm -rf *");
     }
    chmod("$mkd",0777) or die ("No cambio permisosx".$direcTor ); 
    //echo "**.......$uploadfile....$ruta.....$base";
    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile) )
      {
        //llama a extraccion de ficheros de cabeceras genericas...................................................................
        extracta($nomb,$mkd);
        //crear listado de plantillas 
      }
}

?>


<html>
    <head>
        <title>Orfeo- Cabeceras Plantilla.</title>
        <link rel="stylesheet" href="<?=$ruta_raiz.$ESTILOS_PATH?>orfeo.css">
        <script language="Javascript">
        </script>
    </head>

    <body>
        <form enctype="multipart/form-data" name="formSeleccion" id="formSeleccion" method="post" action="">
            <input type='hidden' name='<?=session_name()?>' value='<?=session_id()?>'> 
            <input type="hidden" name="MAX_FILE_SIZE" value="<?=$tamArchi?>" />
            <table width="100%" border="1" align="center" class="t_bordeGris">
                <tr bordercolor="#FFFFFF">
                    <td width="100%" height="40" align="center" class="titulos4"><b>ADMINISTRADOR DE CABECERAS DOCX</b></td>
                </tr>
                <tr class=timparr>
                    <td width="15%" align="left" class="titulos2"><b>&nbsp;Campos para combinaci&oacute;n sencilla separados por comas</b></td>
                </tr>
                <tr class=timparr>
                    <td class="listado2"><textarea rows="1" name="simple" class="select100"><?=$nombSe ?></textarea></td>
                </tr>
                <tr class=timparr>
                    <td width="15%" align="left" class="titulos2"><b>&nbsp;Campos combinaci&oacute;n masiva:</b> separados por comas</td>
                </tr>
                <tr class=timparr>
                    <td class="listado2"><textarea rows="1" name="masiva" class="select100"><?=$nombMa ?></textarea></td>
                </tr>
                <tr class=timparr>
                    <td class="listado2"> Tipo Plantilla 
                    <?php
                           $sqw="select   sgd_trad_descr, sgd_trad_codigo from sgd_trad_tiporad";
                          $rss = $db->conn->Execute($sqw);
                           $slc = $rss->GetMenu2('tipo',$_POST['tipo'],'-- seleccione --',false,false,'Class="select" id="tipo"');
                         echo $slc;
                         
                       ?>
                    </td>
                </tr>
            </table>

            <table width="100%" border="1" align="center" class="titulosError">
                <tr><td><center><?=$msg?></center></td></tr>
            </table>

            <table width="100%" border="1" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="20%" align="center"></td>
                </tr>
            </table>

            <table width="100%" border="1" align="center" class="t_bordeGris">
                <tr>
                    <td width="50%" align="left" class="titulos2"><b>&nbsp;Plantillas en formato DOCX</b></td>
                </tr>
                <tr class="listado1">
                    <td align="left" valign="top">
                         <input name="userfile" type="file"> 
                         <input class="botones" type="submit" name="btn_acc" value="adjuntar" class="botones">
                         <br/><br/>
                        <?=$nombArc ?>
                        <br/>
                    </td>
                </tr>
            </table>

        </form>
    </body>
</html>
