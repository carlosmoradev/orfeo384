<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);
$ruta_raiz = "../../";
if (!$_SESSION['dependencia'])
    header ("Location: $ruta_raiz/cerrar_session.php");

include_once    ("$ruta_raiz/include/db/ConnectionHandler.php");
require_once    ("$ruta_raiz/class_control/Mensaje.php");

$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

switch($_POST['btn_accion']){

case 'Agregar':

    $sqlInsert="insert into sgd_noh_nohabiles(noh_fecha)values(".$db->conn->DBDate($_POST[fecha_sel]).")";
    $ok=$db->conn->Execute($sqlInsert);
    $ok?$error=1:$error=2;

    break;

case 'Borrar':

    $tmp_val = empty($_POST[noh_fecha])? "" : implode("','",$_POST[noh_fecha]);
    $sqlBorra="delete from sgd_noh_nohabiles where noh_fecha in ('$tmp_val')";
    $ok=$db->conn->Execute($sqlBorra);
    $ok?$error=3:$error=4;

    break;
}

$where      = empty($_POST['slc_anio'])? '' : "WHERE ".$db->conn->SQLDate('Y','NOH_FECHA')." = "."'".$_POST['slc_anio']."'";
$sql_cont   = "SELECT NOH_FECHA as ID,NOH_FECHA as DESCRIP FROM SGD_NOH_NOHABILES $where ORDER BY 1";
$rs_noh     = $db->conn->Execute($sql_cont); 
$slc_fechas = $rs_noh->GetMenu2('noh_fecha',$noh_fecha,false,true,5,"class='select100' multiple size=5 id='noh_fecha'");

if(!$fecha_sel) $fecha_sel=date("Y-m-d");

for($i=2007;$i<=2018;$i++){   
    $sel = ($_POST['slc_anio']==$i) ? "selected" : "";
    $filtro .="<option value='$i' $sel>$i</option>";
}

?>
<html>
<head>
<link rel="stylesheet" href="../../estilos/orfeo.css">
</head>
<body>
<div id="spiffycalendar" class="text"></div>
<link rel="stylesheet" type="text/css" href="<?=$ruta_raiz?>js/spiffyCal/spiffyCal_v2_1.css">
<script language="JavaScript" src="<?=$ruta_raiz?>js/spiffyCal/spiffyCal_v2_1.js"></script>
<script language="javascript">
var dateAvailable  = new ctlSpiffyCalendarBox("dateAvailable", "new_product", "fecha_sel","btnDate1","<?=$fecha_sel?>",scBTNMODE_CUSTOMBLUE);
</script>
<table><tr><td></td></tr><tr><td></td></tr><tr><td></td></tr><tr><td></td></tr></table>
<center><table width="550" class='borde_tab'><tr><td class='titulos4' align="center">Administraci&oacute;n de Dias no Habiles</td></tr></table></center>
<form name="new_product"  action='<?=$_SERVER['PHP_SELF']."?".session_name().'='.session_id()?>' method="post">
<input type='hidden' name='<?=session_name()?>' value='<?=session_id()?>'> 
<center>
<table width="550" class='borde_tab'>
    <tr>
        <td  class='titulos5'>Seleccionar fecha</td>
        <td  class='titulos5'>
<script language="javascript">
dateAvailable.date = "";
dateAvailable.writeControl();
dateAvailable.dateFormat="yyyy-MM-dd";
</script>
         </td>
         <td height="26" colspan="2" valign="top" class='titulos5'>
            <center>
            <input   type="submit" name='btn_accion' id="btn_accion" Value='Agregar' class='botones_mediano'>
            </center>
        </td>
    </tr>
    <tr>
        <td height="26" class='titulos5'>Filtro</td>
        <td height="26" class='titulos5'>
            <select name="slc_anio" id="slc_anio" class="select"  onchange="this.form.submit();">
                <option value="">&lt;&lt Todos los a&ntilde;os &gt;&gt;</option>
                <?echo $filtro;?>
            </select>
        </td>
        <td height="26" class='titulos5'></td>
    </tr>
    <tr border="1">
        <td height="26" class='titulos5'>Fechas registradas</td>
        <td height="26" class='titulos5'>
          <?echo $slc_fechas?>
        </td>
        <td height="26" class='titulos5' align="center">
            <input type="submit" name='btn_accion' id="btn_accion" Value='Borrar' class=botones_mediano>
        </td>
    </tr>
    <tr>
        <td colspan="3" align="center">
        <?echo $msg?>
        </td>
    </tr>
  </table>
  </center>
  </form>
  </body>
  </html>
