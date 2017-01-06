<?php

session_start();
    if(empty($_SESSION['dependencia']))
        include "$ruta_raiz/rec_session.php";

    foreach ($_GET  as $key => $val){ ${$key} = $val;}
    foreach ($_POST as $key => $val){ ${$key} = $val;}
    
    $usua_doc    = $_SESSION["usua_doc"];
    $codusuario  = $_SESSION["codusuario"];

    $ruta_raiz = "..";

    include_once "$ruta_raiz/include/db/ConnectionHandler.php";
    require_once("$ruta_raiz/class_control/CombinaError.php");
    include_once "$ruta_raiz/config.php";

    $conexion = new ConnectionHandler($ruta_raiz);

    $hora  = date("H")."_".date("i")."_".date("s");
    // var que almacena el dia de la fecha
    $ddate = date('d');
    // var que almacena el mes de la fecha
    $mdate = date('m');
    // var que almacena el ano de la fecha
    $adate = date('Y');
    // var que almacena  la fecha formateada
    $fecha  = $adate."_".$mdate."_".$ddate;
    $arcCsv = $usua_doc."_".$fecha."_".$hora.".csv";

    //var que almacena el path hacia el PDF final
    $arcPDF      = "$ruta_raiz/bodega/masiva/"."tmp_".$usua_doc."_".$fecha."_".$hora.".pdf";

    $phpsession  = session_name()."=".session_id();

    $dependencia = $_SESSION['dependencia'];

    //var que almacena los parametros de sesion
    $tipo = $codiEst;
    $params=$phpsession."&krd=$krd&dependencia=$dependencia&
        codiEst=$codiEst&
        usua_nomb=$usua_nomb&tipo=$tipo&depe_nomb=$depe_nomb&
        usua_doc=$usua_doc&codusuario=$codusuario";

    //Función que calcula el tiempo transcurrido
    function microtime_float(){
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

?>
<html>
    <head>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link rel="stylesheet" href="../estilos/orfeo.css">
    <script>

    function abrirArchivoaux(url){
        nombreventana='Documento';
        window.open(url, nombreventana,  'status, width=900,height=500,screenX=100,screenY=75,left=50,top=75');
        return; 
    }

    </script>
    </head>

    <body>
    <form action="../radsalida/masiva/adjuntar_defintExcel.php?<?=$params?>" method="post" enctype="multipart/form-data" name="formDefinitivo">
    <input type=hidden name=tipoRad value='<?=$tipoRad?>'>
    <?php
    $time_start = microtime_float();

    $archivoPlantilla = $_FILES['archivoPlantilla']["tmp_name"];
    if(($_FILES['archivoPlantilla']['type'] != 'text/csv') 
        || $_FILES['archivoPlantilla']['size'] > 3073741824){


        echo "el tama&ntilde;o de los archivos no es correcto. 
            <br><br>
            <table>
                <tr>
                    <td>
                        se permiten archivos de 100 Kb m&aacute;ximo.
                    </td>
                </tr>
            </table>";
    }else{
        if(!copy( $archivoPlantilla, "../bodega/masiva/".$arcCsv )){
            echo "error al copiar los archivos";
            
        }else{
            echo "<center><span class=etextomenu align=left>";
            echo "<TABLE border=0 width 60% cellpadding='0' cellspacing='5' class='borde_tab'>
                <TR ALIGN=LEFT><TD width=20% class='titulos2' >DEPENDENCIA :</td><td class='listado2'> ".$_SESSION['depe_nomb']."</TD>	<TR ALIGN=LEFT><TD class='titulos2' >USUARIO RESPONSABLE :</td><td class='listado2'>".$_SESSION['usua_nomb']."</TD>
                <TR ALIGN=LEFT><TD class='titulos2' >FECHA :</td><td class='listado2'>" . date("d-m-Y - h:mi:s") ."</TD></TR></TABLE>";
            require "$ruta_raiz/jhrtf/jhrtfPlano.php";
            $ano = date("Y") ;
            $ruta_raiz = "..";
            $definitivo="si";

            // Se crea el objeto de plano
            $plano = new jhrtf($arcCsv,$ruta_raiz,$arcPDF,$conexion);
            $plano->cargar_csv();
            $plano->validarArchsPlano($tipo);
            if ($plano->hayErrorPlano()){
                  $plano->mostrarErrorPlano();
            }else{  
                    //$masiva->combinar_csv($dependencia,$codusuario,$usua_doc,$usua_nomb,$depe_codi_territorial,$codiTRD,$tipoRad);
                     $plano->actualizar_envio_corr();

                    echo "
                        <center>
                            <span  class=info>
                                <br>Se llev&oacute; a cabo el Cargue del archivo plano.<br> 
                            </span>
                            <span class='info'>
                                <br>
                                <a class='vinculos' href=javascript:abrirArchivoaux('../bodega/masiva/$arcCsv')> Archivo CSV Origen</a>
                            </span> 
                        <center>";			
            }	
        }	
        //Contabilizamos tiempo final
        $time_end = microtime_float();
        $time = $time_end - $time_start;
        echo "<br><b>Se demor&oacute;: $time segundos la Operaci&oacute;n total.</b>";
    }
    ?>
    </form>
    </body>
</html>
