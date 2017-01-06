<?php
session_start();
/**
  * Generar Listado Entrega Físicos a las Dependencias
  * Creado 28 de agosto de 2012
  * 
  * @licencia GNU/GPL V 3
  */
foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;
$krd = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$usua_doc = $_SESSION["usua_doc"];
$codusuario = $_SESSION["codusuario"];
$ruta_raiz = "..";
include_once  "../include/db/ConnectionHandler.php";
$db = new ConnectionHandler("..");
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

error_reporting(7);
$anoActual = date("Y");
if(!$fecha_busq) $fecha_busq=date("Y-m-d");
if(!$fecha_busq2) $fecha_busq2=date("Y-m-d");
$ruta_raiz = "..";
$db = new ConnectionHandler("$ruta_raiz");	
?>
<head>
<link rel="stylesheet" href="../estilos/orfeo.css">
</head>
<BODY>
<div id="spiffycalendar" class="text"></div>
<link rel="stylesheet" type="text/css" href="../js/spiffyCal/spiffyCal_v2_1.css">
<script> function abrirArchivo(url){nombreventana='Documento'; window.open(url, nombreventana,  'status, width=900,height=500,screenX=100,screenY=75,left=50,top=75');return; }</script>
<script language="JavaScript" src="../js/spiffyCal/spiffyCal_v2_1.js"></script>
<script language="javascript"><!--
  var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "new_product", "fecha_busq","btnDate1","<?=$fecha_busq?>",scBTNMODE_CUSTOMBLUE);
	var dateAvailable2 = new ctlSpiffyCalendarBox("dateAvailable2", "new_product", "fecha_busq2","btnDate1","<?=$fecha_busq2?>",scBTNMODE_CUSTOMBLUE);
//--></script>
<TABLE width="100%" class='borde_tab' cellspacing="5">
  <TR>
    <TD height="30" valign="middle"   class='titulos5' align="center">LISTADO DE DOCUMENTOS RADICADOS
	</td></tr>
	</table>
	<table><tr><td></td></tr></table>
<form name="new_product"  action='../reportes/generar_listado_entrega.php?<?=session_name()."=".session_id()."&krd=$krd&fecha_h=$fechah&fecha_busq=$fecha_busq&fecha_busq2=$fecha_busq2"?>' method=post>
<center>
<TABLE width="550" class='borde_tab'>
  <!--DWLayoutTable-->
  <TR>
    <TD width="415" height="21"  class='titulos5'> Fecha desde<br>
	<?
	  echo "($fecha_busq)";
	?>
	</TD>
    <TD width="415" align="right" valign="top">

        <script language="javascript">
		dateAvailable.date = "2003-08-05";
		dateAvailable.writeControl();
		dateAvailable.dateFormat="yyyy-MM-dd";
    	  </script>
</TD>
  </TR>
  <TR>
    <TD width="125" height="21"  class='titulos5'> Fecha Hasta<br>
	<?
	  echo "($fecha_busq2)";
	?>
	</TD>
    <TD width="415" align="right" valign="top">
        <script language="javascript">
		        dateAvailable2.date = "2003-08-05";
			    dateAvailable2.writeControl();
			    dateAvailable2.dateFormat="yyyy-MM-dd";
    	  </script>
</TD>
  </TR>
<TD height="26" class='titulos2'> Desde la Hora</TD>
	<TD valign="top" class='listado2'>
<?
    if(!$hora_ini) $hora_ini = 01;
    if(!$hora_fin) $hora_fin = date("H");
    if(!$minutos_ini) $minutos_ini = 01;
    if(!$minutos_fin) $minutos_fin = date("i");
    if(!$segundos_ini) $segundos_ini = 01;
    if(!$segundos_fin) $segundos_fin = date("s");
?>
  <select name=hora_ini class='select'>
  <?
    for($i=0;$i<=23;$i++)
    {
    if ($hora_ini==$i){ $datoss = " selected "; }else{ $datoss = " "; }?>
    <option value='<?=$i?>' '<?=$datoss?>'>
      <?=$i?>
    </option>
      <?
      }
      ?>
</select>:<select name=minutos_ini class='select'>
  <?
    for($i=0;$i<=59;$i++)
    {
    if ($minutos_ini==$i){ $datoss = " selected "; }else{ $datoss = " "; }?>
    <option value='<?=$i?>' '<?=$datoss?>'>
    <?=$i?>
    </option>
    <?
    }
    ?>
  </select>
  </TD>
  </TR>
  <Tr>
    <TD height="26" class='titulos2'> Hasta</TD>
    <TD valign="top" class='listado2'><select name=hora_fin class=select>
    <?
      for($i=0;$i<=23;$i++)
      {
      if ($hora_fin==$i){ $datoss = " selected "; }else{ $datoss = " "; }?>
        <option value='<?=$i?>' '<?=$datoss?>'>
        <?=$i?>
        </option >
        <?
	}
	?>
      </select>:<select name=minutos_fin class=select>
        <?
	for($i=0;$i<=59;$i++)
	{
	if ($minutos_fin==$i){ $datoss = " selected "; }else{ $datoss = " "; }?>
        <option value='<?=$i?>' '<?=$datoss?>'>
        <?=$i?>
        </option>
    <?
      }
      ?>
      </select>
      </TD>
  </TR>
  <TR>
    <TD height="26" class='titulos5'>Dependencia</TD>
    <TD valign="top">
    <?
    $ss_RADI_DEPE_ACTUDisplayValue = "--- TODAS LAS DEPENDENCIAS ---";
    $valor = 0;
    include "$ruta_raiz/include/query/devolucion/querydependencia.php";
    $sqlD = "select $sqlConcat ,depe_codi from dependencia 
	    where depe_estado = '1'
	    order by depe_codi";
    $rsDep = $db->conn->Execute($sqlD);
    print $rsDep->GetMenu2("dep_sel","$dep_sel",$blank1stItem = "$valor:$ss_RADI_DEPE_ACTUDisplayValue", false, 0," onChange='submit();' class='select'");	
    ?>
	</TD>
  </TR>
  <TR>
    <TD height="26" class='titulos5'>MEDIO ENVIO/RECEPCION</TD>
    <TD valign="top">
    <?
    $displayValue = "--- TODOS LOS TIPOS DE ENVIO/RECEPCION ---";
    $valor = 99;
     $sqlMR = "SELECT mrec_desc, mrec_codi  FROM medio_recepcion";
    $rsDep = $db->conn->Execute($sqlMR);
    print $rsDep->GetMenu2("mrecCodi","$mrecCodi",$blank1stItem = "$valor:$displayValue", false, 0," onChange='submit();' class='select'");

    ?>
   </TD>
  </TR>


  <tr>
    <td height="26" colspan="2" valign="top" class='titulos5'> <center>
		<INPUT TYPE=SUBMIT name=generar_informe Value=' Generar Informe ' class=botones_mediano></center>
		</td>
	</tr>
  </TABLE>

<?php

if(!$fecha_busq) $fecha_busq = date("Y-m-d");
if($generar_informe)
{
//
	?>
        </center>
	<span class="etextomenu">
	<b>Listado de documentos Radicados</b><br>
	Fecha Inicial <?=$fecha_busq . "  ". $hora_ini . $minuto_ini ." :00" ?> <br>
	Fecha Final   <?=$fecha_busq2 . "  ". $hora_fin . $minuto_fin ." :59" ?> <br>
	Fecha Generado <? echo date("Ymd - H:i:s"); ?> 
    <?
        $generar_informe = 'generar_informe';
     
        
// Se construye la condicion según la dependencia seleccionada
if ($dep_sel == 0)
{
     $where_depe = " and a.radi_depe_radi not in (900,905,999,9000) ";
}else
{
      $where_depe = " and a.radi_depe_radi = $dep_sel ";
}
// Se construye la condicion segun el parametro fecha
	$fecha_ini = $fecha_busq;
	$fecha_fin = $fecha_busq2;
         
	$fecha_ini = mktime($hora_ini,$minutos_ini,00,substr($fecha_ini,5,2),substr($fecha_ini,8,2),substr($fecha_ini,0,4));
	$fecha_fin = mktime($hora_fin,$minutos_fin,59,substr($fecha_fin,5,2),substr($fecha_fin,8,2),substr($fecha_fin,0,4));
        
	$fecha_iniR = $fecha_busq. " ". $hora_ini . " ".$minutos_ini. ":00";
	$fecha_finR = $fecha_busq2. " ". $hora_fin . " ".$minutos_fin. ":59";
 

        $where_fecha = ' WHERE a.radi_fech_radi BETWEEN
			'.$db->conn->DBTimeStamp($fecha_ini).' and '.$db->conn->DBTimeStamp($fecha_fin).'
			and a.sgd_trad_codigo = 2 ';

//Se cargan los campos de codigo de dependencia para realizar el corte por Dependencia
      $queryDependencias = "select distinct d.depe_codi as codigo, d.depe_nomb as DEPENDENCIA
			    from dependencia d, radicado a
                           ";
//Se adiciona condicion especifica de la dependencia correspondencia

      $query_adicional = " and a.depe_codi = '4230' ";
	if($mrecCodi!=99){
	 $query_adicional .= " and a.mrec_codi=$mrecCodi ";

	}
      $queryDependencias = $queryDependencias .$where_fecha. $query_adicional.  $where_depe. " and a.radi_depe_radi = d.depe_codi order by d.depe_codi";
      
      $rs= $db->conn->Execute($queryDependencias);


//    
  $guion     = "' '";
       
        require "../anulacion/class_control_anu.php";
	$db->conn->SetFetchMode(ADODB_FETCH_NUM);
	$btt = new CONTROL_ORFEO($db);
        $campos_align = array("L","L","L","L","L");
	$campos_tabla = array("radicado","tipo","remitente","folios","anexos");
	$campos_vista = array ("Radicado","Tipo de documento","Remitente","Folios","Anexos");
	$campos_width = array (114        ,150           ,411        ,45        ,50        );
	$btt->campos_align = $campos_align;
	$btt->campos_tabla = $campos_tabla;
	$btt->campos_vista = $campos_vista;
	$btt->campos_width = $campos_width;
        define(FPDF_FONTPATH,'../fpdf/font/');
        //include ("$ruta_raiz/fpdf/html2pdf.php");
        require("$ruta_raiz/fpdf/html_table.php");
        //include "$ruta_raiz/class_control/class_gen.php";
               
         $pdf=new PDF();
     
      $pdf->Open();
      $pdf->SetCreator("HTML2PDF");
      $pdf->SetTitle("Reporte de Documentos Radicados");
      $pdf->SetSubject("Reporte radicados");
      $pdf->SetAuthor("Correspondencia");
      $pdf->SetFont('Arial','',8);
      $logo    = '<img src="../png/cabeceraEntidad.png" width="520">';
      
    
while(!$rs->EOF)
     {

        $deplistado = $rs->fields["CODIGO"];
        $depNomlistado = $rs->fields["DEPENDENCIA"];

        $where_depeSel = " and a.radi_depe_radi = $deplistado ";
        $encabezado =   $logo."<table border=0> 
                        <tr>
                         <td width=520>     </td>
			</tr>
                        <tr>
                         <td width=520>     </td>
			</tr>
                        <tr>
                         <td width=520>     </td>
			</tr>
			<tr>
                         <td width=520> Radicados Entre : $fecha_iniR y $fecha_finR     </td>
			</tr>
			<tr>
			<td  width=520> Dependencia Destino: $deplistado - $depNomlistado</td>
			</tr>
			</table>";

	//Query de Radicados";
	
        $query = "select a.radi_nume_radi as radicado, t.sgd_tpr_descrip as tipo,substr(d.sgd_dir_nomremdes,1,58) as remitente,
                         a.radi_nume_folio as folios, a.radi_nume_anexo as anexos
                  from radicado a
		  left join sgd_tpr_tpdcumento t on t.sgd_tpr_codigo = a.tdoc_codi
		  left join sgd_dir_drecciones d on d.radi_nume_radi = a.radi_nume_radi 
                       and d.sgd_dir_tipo = 1 ";
        
	if($mrecCodi!=99){
         $query .= " and a.mrec_codi=$mrecCodi ";

        }


        $order_isql = " order by a.radi_nume_radi  ";   
	$query_t = $query . $where_fecha . $where_depeSel . $order_isql ;
	$btt->tabla_sql($query_t);
  
          $html = $encabezado;
	
	  $html= $html. $btt->tabla_html;
	
          $pdf->AddPage();
          if(ini_get('magic_quotes_gpc')=='1')
              $html=stripslashes($html);
          $pdf->WriteHTML($html);

     $rs->MoveNext();
}
	$arpdf_tmp = "../bodega/pdfs/planillas/envios/$dependencia_$krd". date("Ymd_hms") . "_envio.pdf";
	$pdf->Output($arpdf_tmp); 
        echo "<B><a class=\"vinculos\" href=\"#\" onclick=\"abrirArchivo('". $arpdf_tmp."?time=".time() ."');\"> Abrir Archivo Pdf</a><br>";
}
?>
</form>
<HR>
