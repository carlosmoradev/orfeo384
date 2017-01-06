<?php 
session_start();
$ruta_raiz = "../";
if (!$_SESSION['dependencia'])
header ("Location: $ruta_raiz/cerrar_session.php");
include_once    ("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler($ruta_raiz);

foreach ($_POST as $key => $valor) ${$key} = $valor;
$doc    = new DOMDocument();
//Abrir o crear el archivo de listado en 
//boveda con el nombre plantillas.txt
$direcTor = "bodega/plantillas/genericas";
$archivo1 = $direcTor."combiSencilla.xml";
$archivo2 = $direcTor."combiMasiva.xml";
$archivo3 = $direcTor."plantillas.xml";

$tamArchi = 5054432;
  $sqw="select   sgd_trad_descr, sgd_trad_codigo from sgd_trad_tiporad";
                          $rss = $db->conn->Execute($sqw);
                           $slc = $rss->GetMenu2('tipo',$_POST['tipo'],'-- seleccione --',false,false,'Class="select" id="tipo"');
                         //echo $slc;
/****************************************
* creo el directorio con los permisos. 
*****************************************/
echo ">>>".$direcTor;
if (@!file_exists($direcTor)) { 
    $directorio = mkdir("$direcTor",0777); 
}  


/****************************************
* Plantillas agregar y eliminar 
*****************************************/
function extracta($nomb,$mkd)
{                     extract($_POST);
                      extract($_GET);
                        $cambio = chdir($mkd) or die ("no existe directorio..");
                        $mkdsin=str_replace('/','\/',$mkd);
                    
						$varTemp = "unzip " .  $nomb ." -d  ." ;
                        $varDel="for a in `ls | grep -v styles.xml | grep -v Pictures`; do rm -fr \$a; done";
                        $varSed=" sed -i 's/Pictures/".$mkdsin."\/Pictures/g' *.xml";
                        $verificacion = exec($varTemp) or die ("fallo en  extraccion..".$varTemp);
                        $verificacion = exec($varDel);
                        $verificacion= exec($varSed);
                        $ruta_raiz = "../..";
}
function extractadocx($nombx,$mkdx)
{  
extract($_POST);
extract($_GET);
$cambiox = chdir($mkdx) or die ("no existe directorio..");
$mkdsin=str_replace('/','\/',$mkdx);
$varTempx = "unzip " .  $nombx ." -d  ." ;
$varCpx="cp -rf word/* .";
$varDelx="for a in `ls | grep -v header* | grep -v footer* | grep -v media*`; do rm -fr \$a; done";
$varSedx=" sed -i 's/Picturesggg/".$mkdsin."\/Picturesggg/g' *.xml";
echo $varSedx;
$verificacionx = exec($varTempx) or die ("fallo en  extraccion..".$varTempx);
$verificacionx = exec($varCpx);
$verificacionx = exec($varDelx);
$ruta_raiz = "../..";
}
//Eliminar plantillas si se envian la solicitud
if(($btn_acc == Borrar)){
    if(!empty($nomPlant)){
        $doc->load($archivo3);
        $campos     = $doc->getElementsByTagName("campo");
        
        $doc4 = new DOMDocument();
        $doc4->formatOutput = true;

        $r = $doc4->createElement("campos");
        $doc4->appendChild($r);
        
        foreach($campos as $campo){
            $campTemp1 = $campo->getElementsByTagName("nombre");
            $campTemp2 = $campo->getElementsByTagName("ruta");
            $temp1     = $campTemp1->item(0)->nodeValue;
            $temp2     = $campTemp2->item(0)->nodeValue;

            if(!in_array($temp2,$nomPlant)){
                $b = $doc4->createElement("campo");
                $nombre = $doc4->createElement("nombre");
                $nombre->appendChild(
                    $doc4->createTextNode(trim($temp1))
                );
                $b->appendChild($nombre);
                
                $ruta = $doc4->createElement("ruta");
                $ruta->appendChild(
                    $doc4->createTextNode($temp2)
                );

                $b->appendChild($ruta);
                $r->appendChild($b);
            }
        }
        $doc4->save($archivo3);
    }
} 

//Leer archivo con listado de plantillas
if(@!$doc->load($archivo3)){
    $handler    = fopen($archivo3, "w+");
    $msg        .= " </br>";
    fclose($handler); 
}else{
    $campos     = $doc->getElementsByTagName("campo");
    foreach($campos as $campo){
        $campTemp1 = $campo->getElementsByTagName("nombre");
        $campTemp2 = $campo->getElementsByTagName("ruta");
        $temp1     = $campTemp1->item(0)->nodeValue;
        $temp2     = $campTemp2->item(0)->nodeValue;
        
        $plantill[] = array('nombre' => $temp1,
                            'ruta'   => $temp2);
    }
}


if($btn_acc == adjuntar){
    $tipValido  = 'application/vnd.oasis.opendocument.text'; 
    if($_FILES['userfile']['type'] == $tipValido)
    {
    $nomb       = "plant".time().rand(0,1000).".odt";
    $uploadfile = $direcTor."/tp".$tipo."/".$nomb;
    $ruta = exec("cd ..;pwd");
    $mkd=$ruta."/".$direcTor."/tp".$tipo;
    $uploadfile =$mkd."/".$nomb;
    }
    else
    {
    $nomb       = "plant".time().rand(0,1000).".docx";
    $uploadfile = $direcTor."/tpx".$tipo."/".$nomb;
    $ruta = exec("cd ..;pwd");
    $mkd=$ruta."/".$direcTor."/tpx".$tipo;
    $uploadfile =$mkd."/".$nomb;
    }
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
    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile) &&$_FILES['userfile']['type'] == $tipValido)
      {
        //llama a extraccion de ficheros de cabeceras genericas...................................................................
        extracta($nomb,$mkd);
        //crear listado de plantillas 
        $doc3 = new DOMDocument();
        $doc3->formatOutput = true;
        $r = $doc3->createElement("campos");
        $doc3->appendChild($r);
        $plantill[] = array('nombre' => $_FILES['userfile']['name'], 'ruta'   => $nomb);
        foreach($plantill as $campo)
        {
            $b = $doc3->createElement( "campo" );
            if(!empty($campo))
            {
                $nombre = $doc3->createElement("nombre");
                $nombre->appendChild($doc3->createTextNode(trim($campo['nombre'])) );
                $b->appendChild($nombre);
                $ruta = $doc3->createElement("ruta");
                $ruta->appendChild($doc3->createTextNode($campo['ruta']));
                $b->appendChild($ruta);
            }
            $r->appendChild($b);
        }
        $doc3->save($archivo3);
        $msg .= "El archivo de cabecera generica fue extractado y almacenado</br>";
    }
    else 
    {
        $msg .= "Se extracto y almaceno las cabeceras DOCX</br>";
        extractadocx($nomb,$mkd);
    }
}

if(!empty($plantill)){
    foreach($plantill as $campo){
        $ruta   = $campo['ruta'];
        $nombre = $campo['nombre'];
        $nombArc .= "<input type='checkbox' name='nomPlant[]' value='$ruta'>$nombre<br/>";
    }
}

/****************************************
 * Modificar datos en el xml
 *****************************************/
if($btn_acc == Modificar){
    $formSim  = explode(",", trim($simple));
    $formMas  = explode(",", trim($masiva));
    $formPlan = explode(",", trim($planti));

    //crear listado combinacion simple
    $doc      = new DOMDocument();
    $doc->formatOutput = true;
    $r = $doc->createElement("campos");
    $doc->appendChild($r);

    foreach($formSim as $campo){
        $b = $doc->createElement( "campo" );
        if(!empty($campo)){
            $nombre = $doc->createElement("nombre");
            $nombre->appendChild(
                $doc->createTextNode(trim($campo))
            );
            $b->appendChild($nombre);
        }
        $r->appendChild($b);
    }

    $doc->save($archivo1);
    

    //crear listado combinacion masiva 
    $doc2        = new DOMDocument();
    $doc2->formatOutput = true;

    $r = $doc2->createElement("campos");
    $doc2->appendChild($r);
    
    foreach($formMas as $campo){
        $b = $doc2->createElement( "campo" );
        if(!empty($campo)){
            $nombre = $doc2->createElement("nombre");
            $nombre->appendChild(
                $doc2->createTextNode(trim($campo))
            );
            $b->appendChild($nombre);
        }
        $r->appendChild($b);
    }

    $doc2->save($archivo2);
    
    $msg    .= "Se actulizo la informacion de los campos</br>";
}
 


/*****************************************
 * Leer el archivo existente o crearlo
 * ***************************************/
if(@!$doc->load($archivo1)){
    $handler    = fopen($archivo1, "w+");
    $msg        .= " ";
    fclose($handler); 
}else{
    $plantill[] = array('nombre' => $temp1,
                                'ruta'   => $temp2);
    $campos     = $doc->getElementsByTagName("campo");
    foreach($campos as $campo){
        $campTemp = $campo->getElementsByTagName("nombre");
        $valor    = $campTemp->item(0)->nodeValue;
        $nombSe   .= empty($nombSe)? $valor : ", $valor";
    }
}

if(@!$doc->load($archivo2)){
    $handler    = fopen($archivo2, "w+");
    $msg        .= " ";
    fclose($handler); 
}else{
    $campos     = $doc->getElementsByTagName("campo");
    foreach($campos as $campo){
        $campTemp = $campo->getElementsByTagName("nombre");
        $valor    = $campTemp->item(0)->nodeValue;
        $nombMa  .= empty($nombMa)? $valor : ", $valor";
    }
}


if(empty($nombSe)){
    $nombSe = "*TIPO* 
                , *NOMBRE* 
                , *EMPRESA*
                , *DIR*
                , *EMAIL*
                , *MUNI_NOMBRE*
                , *DEPTO_NOMBRE*
                , *PAIS_NOMBRE* ";
}

if(empty($nombMa)){
    $nombMa = "
            *RAD_S*, *RAD_E_PADRE*, *CTA_INT*  
            , *ASUNTO*, *F_RAD_E*, *SAN_FECHA_RADICADO* 
            , *NOM_R*,  *DIR_R*, *DIR_E*
            , *DEPTO_R*,  *MPIO_R*,  *TEL_R*  
            , *MAIL_R*,  *DOC_R*,  *NOM_P*  
            , *DIR_P*,   *DEPTO_P*,  *MPIO_P*  
            , *TEL_P*,   *MAIL_P*,  *DOC_P*  
            , *NOM_E*,   *DIR_E*,  *MPIO_E* 
            , *DEPTO_E*,  *TEL_E*,  *MAIL_E*  
            , *NIT_E*,  *NUIR_E*,  *F_RAD_S*  
            , *RAD_E*,  *SAN_RADICACION*, *SECTOR*  
            , *NRO_PAGS*,  *DESC_ANEXOS*,  *F_HOY_CORTO*  
            , *F_HOY*,  *NUM_DOCTO*,  *F_DOCTO*  
            , *F_DOCTO1*,   *FUNCIONARIO*,   *LOGIN*  
            , *DEP_NOMB*,   *CIU_TER*,  *DEP_SIGLA*  
            , *TER*,   *DIR_TER* *TER_L*  
            , *NOM_REC*,  *EXPEDIENTE*,  *NUM_EXPEDIENTE*  
            , *DIGNATARIO*, *DEPE_CODI*,   *DEPENDENCIA*  
            , *DEPENDENCIA_NOMBRE*  
         ";
}

?>


<html>
    <head>
        <title>Orfeo- Cabeceras Plantilla.</title>
        <link rel="stylesheet" href="../estilos/orfeo.css">
        <script language="Javascript">
        </script>
    </head>

    <body>
        <form enctype="multipart/form-data" name="formSeleccion" id="formSeleccion" method="post" action="">
            <input type='hidden' name='<?=session_name()?>' value='<?=session_id()?>'> 
            <input type="hidden" name="MAX_FILE_SIZE" value="<?=$tamArchi?>" />
            <table width="100%" border="1" align="center" class="t_bordeGris">
                <tr bordercolor="#FFFFFF">
                    <td width="100%" height="40" align="center" class="titulos4"><b>ADMINISTRADOR DE PLANTILLAS</b></td>
                </tr>
            <!--    <tr class=timparr>
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
                </tr> -->
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
                    <td width="50%" align="left" class="titulos2"><b>&nbsp;Plantilla en formato DOCX / ODT</b></td>
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
