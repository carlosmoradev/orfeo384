<?php
session_start();
$ruta_raiz 		= "../../"; 
include_once "$ruta_raiz/config.php";
$verradicado        = $verrad;
$krdOld             = $krd;
$menu_ver_tmpOld    = $menu_ver_tmp;
$menu_ver_Old       = $menu_ver;

foreach ($_GET as $key=>$valor)
${$key} = $valor;
foreach ($_POST as $key=>$valor)
${$key} = $valor;
$nomcarpeta = $_GET["nomcarpeta"];
if ($_GET["tipo_carp"])
$tipo_carp = $_GET["tipo_carp"];

$krd            = $_SESSION["krd"           ];
$dependencia    = $_SESSION["dependencia"   ];
$usua_doc       = $_SESSION["usua_doc"      ];
$codusuario     = $_SESSION["codusuario"    ];
$tip3Nombre     = $_SESSION["tip3Nombre"    ];
$tip3desc       = $_SESSION["tip3desc"      ];
$tip3img        = $_SESSION["tip3img"       ];

if (!$krd)
$krd = $krdOld;

$ruta_raiz = "../..";

include_once "$ruta_raiz/include/db/ConnectionHandler.php";

if ($verradicado)  
$verrad = $verradicado;

$numrad = $verrad;

$db             = new ConnectionHandler($ruta_raiz);    
$db->conn->SetFetchMode(3);    

include $ruta_raiz.'/ver_datosrad.php';    
?>
<html>
<head>
<title>Sticker web</title>
<style type="text/css">

body {
      margin-bottom:0;
                margin-left:0;
                margin-right:0;
                margin-top:0;
                padding-bottom:0;
                padding-left:0;
                padding-right:0;
                padding-top:0
    font-family: Arial, Helvetica, sans-serif;            
}

.stik1{
    font-size: 9px;
}

.stik2{
    font-size: 7px;
    text-align: center;
    vertical-align:top;
}
.stik3{
    font-size: 9px;            
    text-align: center;
}
        
</style>
</head>
<?
$noRad = $_REQUEST['nurad'];
$numeroRadSeparado = substr($noRad,0,4)."-".substr($noRad,4,3)."-".substr($noRad,7,6)."-".substr($noRad,13,1);
?>
<body topmargin="0" leftmargin="0"  onload="window.print();">
    <table width="260px" cellpadding="0" cellspacing="0">
        <tr>
            <td class="stik2" width=0>  
               <img src="../../logoEntidad.gif" WIDTH="80px" BORDER=0 align="left" />               
            </td>
            <td  align=left width="180px">
		<center>
                <font size="2">                                      
                 <?=$numeroRadSeparado ?>                                        
                </font></center>
		<div  align="left">
		<font size=1>           
                 <?=substr($radi_fech_radi,0,16)?> Us <?=$usuarioLoginRadicador?>
		<br> Destino: <?=substr($dependenciaDestino,0,20)?>
         	<br> Rem/Des : <?=substr($_GET['remite'],0,20); ?> 
		<br> Asunto: <?=substr($ra_asun,0,20)?>
		<br>
		</font>
	
		</div>
            </td>                     
        </tr>
	<tr>
	<td colspan=2 align=center>
	<font size=1 align=center><b><?=$entidad_largo?></b>

	</font>
	
	<center><font face='Free 3 of 9' size="6">
            <?php print '*'.$_REQUEST['nurad'].'*'; ?>
        </font>	</center> 
	</td>
	</tr>
    </table>
</body>
</html>
