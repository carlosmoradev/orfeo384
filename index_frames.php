<?php
session_start();
    $ruta_raiz = "."; 
    if (!$_SESSION['dependencia'])
        header ("Location: $ruta_raiz/cerrar_session.php");
        
    if(!isset($fechah)){
        $fechah = "";
    }
    if (!isset($swLog)){
        $swLog = "";
    }
?>
<html>
	<head>
	<title>.:: Sistema de Gesti&oacute;n Documental ::.</title>
	<link rel="shortcut icon" href="imagenes/gnu.gif" />
	</head>
	<frameset rows="40,864*" frameborder="NO" border="0" framespacing="0" cols="*">
	  <frame name="topFrame" scrolling="NO" noresize src='f_top.php?<?=session_name()."=".session_id()?>&fechah=<?=$fechah?>' />
	  <frameset cols="175,947*" border="0" framespacing="0" rows="*" >
	    <frame name='leftFrame' scrolling='AUTO' src='correspondencia.php?<?=session_name()."=".session_id()?>&fechah=<?=$fechah?>' marginwidth='0' marginheight='0' scrolling='AUTO'/>
	    <frame name='mainFrame' src='alertas/index.php?<?=session_name()."=".session_id()?>&swLog=<?=$swLog?>&fechah=<?=$fechah?>&tipo_alerta=1' scrolling='AUTO'/>
	</frameset>
	<noframes></noframes>
</frameset>
</html>
