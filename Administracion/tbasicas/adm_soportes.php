<?php 
session_start();
    $ruta_raiz = "../..";
    if (!$_SESSION['dependencia'])
        header ("Location: $ruta_raiz/cerrar_session.php");

foreach ($_GET  as $key => $valor) ${$key} = $valor;
foreach ($_POST as $key => $valor) ${$key} = $valor;

$dependencia = $_SESSION["dependencia"];
$usua_doc    = $_SESSION["usua_doc"];
$codusuario  = $_SESSION["codusuario"];

include_once($ruta_raiz."/include/db/ConnectionHandler.php");
include_once($ruta_raiz."/config.php");
$db      = new ConnectionHandler("$ruta_raiz");

//Si se realiza la accion de grabar nuevo
//***************************************
if(!empty($USUA_LOGIN)){
    $query0 = " SELECT 
                    USUA_CODI,
                    DEPE_CODI
                FROM 
                    USUARIO
                WHERE 
                    USUA_LOGIN = '$USUA_LOGIN'";

    $rq = $db->conn->Execute($query0);
    $usuacodi   = $rq->fields["USUA_CODI"];
    $depecodi   = $rq->fields["DEPE_CODI"];
}


if($btn_acc == "Modificar"){
    $rqactu     = "UPDATE
                       SGD_TSOP_TIPOSOPORTE
                   SET
                       SGD_TSOP_DESCR     = '$nombSoporte', 
                       SGD_TSOP_ESTADO    = $Slc_destado,
                       SGD_TSOP_USUA_CODI = $usuacodi,
                       SGD_TSOP_DEPE_CODI = $depecodi
                   WHERE
                       SGD_TSOP_ID        = $IdSoporte"; 
    
    $rq  = $db->conn->Execute($rqactu);
    $msg = "Se modifico con exito"; 

}elseif($btn_acc == "Agregar"){

    switch($db->driver){
        case 'oci8':
                $rqInsrt    =" INSERT INTO
                                   SGD_TSOP_TIPOSOPORTE
                                   (SGD_TSOP_DESCR,
                                   SGD_TSOP_ESTADO,
                                   SGD_TSOP_USUA_CODI,
                                   SGD_TSOP_DEPE_CODI,
                                   SGD_TSOP_ID) 
                               VALUES('$nombSoporte', 
                                      $Slc_destado,
                                      $usuacodi,
                                      $depecodi,
                                      SEC_SOP_ID.nextva)";
                break;

        default: 
                $rqInsrt    =" INSERT INTO
                                   SGD_TSOP_TIPOSOPORTE
                                   (SGD_TSOP_DESCR,
                                   SGD_TSOP_ESTADO,
                                   SGD_TSOP_USUA_CODI,
                                   SGD_TSOP_DEPE_CODI,
                                   SGD_TSOP_ID) 
                               VALUES('$nombSoporte', 
                                      $Slc_destado,
                                      $usuacodi,
                                      $depecodi,
                                      nextval('sgd_tsop_tiposoporte_sgd_tsop_id_seq'))";
    }

    $rq  = $db->conn->Execute($rqInsrt);
    $msg = "Se agrego el registro";
}



//Si seleccionamos un tipo existente Entonces cargamos datos
if($IdSoporte){
    $sql0 = "   SELECT 
                    TS.*,
                    US.USUA_LOGIN
                FROM 
                    SGD_TSOP_TIPOSOPORTE TS, 
                    USUARIO US
                WHERE 
                    TS.SGD_TSOP_USUA_CODI     = US.USUA_CODI
                    AND TS.SGD_TSOP_DEPE_CODI = US.DEPE_CODI
                    AND SGD_TSOP_ID           = $IdSoporte"; 

    $rs   = $db->conn->Execute($sql0);

	if(!$rs->EOF){
		$IdSoporte   = $rs->fields["SGD_TSOP_ID"];
		$usuLog      = $rs->fields["USUA_LOGIN"];
		$nombSoporte = $rs->fields["SGD_TSOP_DESCR"];

        if ($rs->fields['SGD_TSOP_ESTADO']==0)
            {$off='selected'; $on='';}
        else
            {$off=''; $on='selected';}
	}
}


// Consulta para traer los actules tipos de soporte 
$sql1   = "select 
                cast(SGD_TSOP_ID as char(3))".$db->conn->concat_operator."' 
                '".$db->conn->concat_operator."SGD_TSOP_DESCR as ver, 
                SGD_TSOP_ID as IdSoporte 
            FROM 
                SGD_TSOP_TIPOSOPORTE";

$rs       = $db->conn->Execute($sql1);
$slc_dep1 = $rs->GetMenu2('IdSoporte'
                        ,$IdSoporte
                        ,':&lt;&lt seleccione &gt;&gt;'
                        ,false
                        ,false
                        ,'Class="select" Onchange="ver_datos(this.value)" id="IdSoporte"');

// Consulta para traer los usuario actuales del sistema
$sql2   = "SELECT 
                cast(DEPE_CODI as char(".$digitosDependencia."))".$db->conn->concat_operator."' 
                '".$db->conn->concat_operator."USUA_NOMB as ver, 
                USUA_LOGIN 
           FROM 
                USUARIO 
           order by depe_codi";
		
$rs       = $db->conn->Execute($sql2);
$slc_dep2 = $rs->GetMenu2('USUA_LOGIN'
                            ,$usuLog
                            ,':&lt;&lt seleccione &gt;&gt;'
                            ,false
                            ,false
                            ,'Class="select" id="USUA_LOGIN"');
?>

<html>
<head>
<title>Orfeo- Admon de soportes.</title>
    <link rel="stylesheet" href="<?=$ruta_raiz."/estilos/".$_SESSION["ESTILOS_PATH"]?>/orfeo.css">
    <script language="Javascript">

        function ver_datos(x)
        {	var pos=false;
            if (x == ''){
                document.getElementById('IdSoporte').value = " ";
                document.getElementById('USUA_LOGIN').value = " ";
                document.getElementById('Slc_destado').value = " ";
                document.getElementById('nombSoporte').value = " ";
                document.formSeleccion.submit();
                borra_datos(document.formSeleccion);
            }
            else{	
                document.formSeleccion.submit();
            }
        }


    function ValidarInformacion(accion){
        
        if (document.formSeleccion.nombSoporte.value.length < 4)
        {
            alert('Digite el nombre del tipo documental mayor a 4 caracteres');
            document.formSeleccion.nombSoporte.focus();
            return false;
        }
        if (document.formSeleccion.USUA_LOGIN.value == '')
        {
            alert('Seleccione un responsable');
            document.formSeleccion.USUA_LOGIN.focus();
            return false;
        }
        if (document.formSeleccion.Slc_destado.value == '')
        {
            alert('Seleccione un estado');
            document.formSeleccion.Slc_destado.focus();
            return false;
        }
        
        if(accion =='Modificar'){	
            if (document.formSeleccion.IdSoporte.value == ''){
                    alert('Seleccione un tipo documental');
                    return false;
            }	
        }
    }


    </script>
</head>

<body>
<form name="formSeleccion" id="formSeleccion" method="post" action="">
<input type='hidden' name='<?=session_name()?>' value='<?=session_id()?>'> 
<table width="100%" border="1" align="center" class="t_bordeGris">
    <tr bordercolor="#FFFFFF">
        <td width="100%" colspan="4" height="40" align="center" class="titulos4"><b>ADMINISTRADOR DE SOPORTES</b></td>
    </tr>
    <tr class=timparr>
        <td width="15%" align="left" class="titulos2"><b>&nbsp;Seleccione Tipo de soporte</b></td>
        <td width="20%" class="listado2">
            <?php echo $slc_dep1; ?>
        </td>
        <td width="15%" align="left" class="titulos2"><b>&nbsp;Responsable</b></td>
        <td width="50%" class="listado2">
            <?php echo $slc_dep2; ?>
        </td>
    </tr>
	<tr class=timparr>
		<td class="titulos2"><b>&nbsp;Estado</b></td>
		<td class="listado2">
			<select name="Slc_destado" id="Slc_destado" class="select">
				<option value="" selected>&lt; seleccione &gt;</option>
				<option value="0" <?=$off ?>>Inactiva</option>
				<option value="1" <?=$on  ?>>Activa</option>
			</select>
		</td>
		<td align="left" class="titulos2"><b>&nbsp;Nombre.</b></td>
		<td class="listado2"><input class="select100" name="nombSoporte" id="nombSoporte" type="text" value="<?=$nombSoporte?>"></td>
	</tr>
</table>
<table width="100%" border="1" align="center" class="titulosError" class="t_bordeGris">
    <tr><td><center><?=$msg?></center></td></tr>
</table>
<table width="100%" border="1" align="center" cellpadding="0" cellspacing="0" class="listado2">
<tr>
	<td width="20%" align="center"><input name="btn_acc"   type="submit" class="botones" id="btn_accion" value="Agregar" onClick="return ValidarInformacion(this.value);" accesskey="A"></td>
	<td width="20%" align="center"><input name="btn_acc" type="submit" class="botones" id="btn_accion" value="Modificar" onClick="return ValidarInformacion(this.value);" accesskey="M"></td>
</tr>
</table>
</form>
</body>
</html>
