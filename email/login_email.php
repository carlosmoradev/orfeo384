<?php
    session_start();

    $ruta      = '/bodega';
    $ruta_raiz .=  "..";  
    if (!$_SESSION['dependencia'])
        header ("Location: $ruta_raiz/cerrar_session.php");
        
    foreach ($_GET  as $key => $val){ ${$key} = $val;}
    foreach ($_POST as $key => $val){ ${$key} = $val;}


    $usua_email  = $_SESSION["usua_email"];
    $krd         = $_SESSION["krd"];
    $dependencia = $_SESSION["dependencia"];
    $encabezado  = session_name()."          = ".session_id()."";

    if($passwd_mail) {
        $passwdEmail  = $passwd_mail;
        $dominioEmail = $_SESSION['dominioEmail'];
    }

    if($_SESSION['passwdEmail']){
        $passwdEmail =$_SESSION['passwdEmail'];
    }

    if($err==1){
        $error =  " <tr class='titulosError'>
                        <td  colspan='2' >
                            No se pudo establecer conexi&oacute;n con el Servidor
                            <br/> Intenta nuevamente.
                        </td>
                    </tr>";
    }
?>

<head>
    <title>..Vista Previa..</title>
    <link rel="stylesheet" href="../estilos/orfeo.css" type="text/css">
</head>
<html>
    <body>
    <h2 align="center">
    </h2>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <form action="browse_mailbox.php?PHPSESSID=<?=session_id()?>" METHOD=POST>
        <table border="0" hegi  align="center" class="borde_tab">
                <tr>
                    <td >
                       <table  border="0" align="center">
                            <tr> 
                                <td class="titulos4" colspan="2" align="center">
                                    <br>Ingrese Clave de Correo: <br>
                                </td>
                            </tr>
                            <tr> 
                                <td class="listado1" colspan="2" width="182" align="center"> 
                                    Cuenta de correo: <?=$usua_email?><br>
                                </td>
                            </tr>
                            <tr>
                                <td width="144" align="center" >
                                    <input type="password" name="passwd_mail" />
                                </td>
                                <td align="center" >
                                    <input name="Submit" type="submit" class="botones" value="INGRESAR">
                                </td>
                            </tr>

                            <?=$error?> 

                       </table>
                    </td>
                </tr>
        </table>
    </form>
    </body>
</html>

