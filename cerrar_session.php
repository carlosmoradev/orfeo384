<?php 
session_start();
    $ruta_raiz = ".";
    include_once "$ruta_raiz/config.php";
    include_once "$ruta_raiz/include/db/ConnectionHandler.php";
 
    $db     = new ConnectionHandler($ruta_raiz);    
    $fecha  = "'FIN  ".date("Y:m:d H:mi:s")."'";
    $isql   = "UPDATE 
                    usuario 
               SET
                    USUA_SESION =".$fecha." 
               WHERE
                    USUA_SESION like '%".session_id()."%'";
    if (!$db->conn->Execute($isql)) {
        echo "<p><center>No pude actualizar<p><br>";
    }    
   
    session_destroy(); 
?>

<html>    
    <head>
        <title>Sesion cerrada ::: ORFEO :::</title>
       <link rel="stylesheet" href="<?=$ruta_raiz."/estilos/".$ESTILOS_PATH?>/orfeo.css" />
        <script type="text/javascript">
            if (top.location != self.location) top.location = self.location
        </script>
    </head>
    <body align=center>    
    <center>
<br>
        <div id="cerrarPag">            

            <a href="<?=$ruta_raiz?>/login.php" target="_parent">
                <img border="0" src="<?=$ruta_raiz?>/imagenes/cerrarOrfeo.png" width="206">
            </a>
	    <br><br><br>
            <div id="textoCerrar">                
                <a class="enlace" href="<?=$ruta_raiz?>/login.php" target="_parent">
                    <div id="reingreso">Ingresar</div>
                    Su sesion ha expirado                                                
                </a>
            </div>
        </div>
    </center>
<table><tr><td></td></tr></table>
<table><tr><td></td></tr></table>
<table><tr><td></td></tr></table>
<table><tr><td></td></tr></table>
<table><tr><td></td></tr></table>
<table><tr><td></td></tr></table>
<table><tr><td></td></tr></table>
<table><tr><td></td></tr></table>
<table><tr><td></td></tr></table>
<table><tr><td></td></tr></table>
<table><tr><td></td></tr></table>
<table><tr><td></td></tr></table>
<table class='borde_tab'>
 <tr class='titulos1'>
    <td class='listado1'>Sistema de gestion Mantenido Por Correlibre.org</td>
 </tr>
</table>
    </body>
</html>
