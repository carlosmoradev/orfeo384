<?
session_start();
define('ADODB_ASSOC_CASE', 0);
//Limite de registros establecidos en cada entidad
$nregisEntidad = 32;
if ((strtoupper($_SESSION["entidad"]) == "MINSALUD"))
{
   $nregisEntidad = 500;
 }
if(!$no_planilla or intval($no_planilla) == 0) die ("<table class=borde_tab width='100%'><tr><td class=titulosError><center>Debe colocar un Numero de Planilla v&aacute;lido</center></td></tr></table>");
if($generar_listado)
{
   	error_reporting(7);
	$ruta_raiz = "..";
   	if (!defined('ADODB_FETCH_NUM'))	define('ADODB_FETCH_NUM',1);
	$ADODB_FETCH_MODE = ADODB_FETCH_NUM; 

	$fecha_ini = $fecha_busq;
        $fecha_fin = $fecha_busq;
	$fecha_ini = mktime($hora_ini,$minutos_ini,00,substr($fecha_ini,5,2),substr($fecha_ini,8,2),substr($fecha_ini,0,4));
$fecha_fin = mktime($hora_fin,$minutos_fin,59,substr($fecha_fin,5,2),substr($fecha_fin,8,2),substr($fecha_fin,0,4));

	$fecha_ini1 = "$fecha_busq $hora_ini:$minutos_ini:00";
	$fecha_mes = "'" . substr($fecha_ini1,0,7) . "'";
	$sqlChar = $db->conn->SQLDate("Y-m","SGD_RENV_FECH");	

// Si la variable $generar_listado_existente viene entonces este if genera la planilla existente
	$order_isql = " ORDER BY a.SGD_RENV_DEPTO,a.SGD_RENV_DESTINO";	
	include "./oracle_pdf.php";
	$pdf = new PDF('L','pt','A3');
	$pdf->lmargin = 0.2;
	$pdf->SetFont('Arial','',10);
	$pdf->AliasNbPages();

	$head_table = array ("CANTIDAD","CATEGORIA DE CORRESPONDENCIA","NUMERO DE REGISTRO","DESTINATARIO","DIRECCION","DEPARTAMENTO","DESTINO","PESO EN GRAMOS","VALOR ENVIO","VALOR TOTAL PORTES Y TASAS");
	$head_table_size = array (57   ,117                            ,80                  ,300           ,250       ,100   ,100    ,73              ,70          ,80);
	$attr=array('titleFontSize'=>10,'titleText'=>'');
	//$arpdf_tmp = "../bodega/pdfs/planillas/$dependencia_". date("Ymd_hms") . "_jhlc.pdf"; Comentariada Por HLP.
	$arpdf_tmp = "../bodega/pdfs/planillas/".$dependencia."_".date("Ymd_hms")."_jhlc.pdf";
	$pdf->SetFont('Arial','',10);
	$pdf->usuario = $usua_nomb;
	$pdf->dependencia = $dependencianomb;
	$pdf->depe_municipio = $depe_municipio;
	$pdf->entidad_largo = $db->entidad_largo;
	$total_registros = 0;
	$pdf->lmargin = 0.2;
	$i_total3 = 0;
	do
	{  // Amplia
		include "$ruta_raiz/include/query/radsalida/queryListado_planillas.php";	

		$pdf->planilla = $no_planilla;
		if($generar_listado_existente)
		{
			$where_isql = $where_isql2;
		}else
		{  
			$where_isql = $where_isql1;
	}

	$query_t = $query . $where_isql . $order_isql;

       	$pdf->oracle_report($db,$query_t,false,$attr,$head_table,$head_table_size,$arpdf_tmp,0,31);
	
	if ($i_total3 == 0)  {
		$i_total3 = $pdf->numrows;
		$total_registros += $i_total3;
	}
	
include_once    ("$ruta_raiz/include/db/ConnectionHandler.php");
define('ADODB_FETCH_ASSOC',0);

$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC; 

	if($generar_listado_existente)
	{
	    $i_total3 = 0;
	}else{
		error_reporting(7);
                
                $isqlUp = "select sgd_renv_codigo  from SGD_RENV_REGENVIO a,  SGD_FENV_FRMENVIO d $where_isql $order_isql";
		$rsParaUp = $db->conn->Execute($isqlUp);
		$nregis = 0 ;
		$rvCodigo = $rsParaUp->fields["sgd_renv_codigo"] ;
		if ($rvCodigo)  {
//			$rsParaUp->MoveFirst();
			while (!$rsParaUp->EOF) {
				$nregis = $nregis + 1;
				$rsParaUp->MoveNext();
			}
		}
		if ($nregis > 0)  {
			$iSqlPlanilla = "select a.sgd_renv_codigo as sgd_renv_codigo from SGD_RENV_REGENVIO a,  SGD_FENV_FRMENVIO d  $where_isql $order_isql";
		    	$rsParaUp = $db->conn->Execute($iSqlPlanilla);
//			$rsParaUp->MoveFirst();	
                        
			if ($nregis <= $nregisEntidad) $maximo = $nregis; else $maximo = $nregisEntidad;
			for ($cont = 1; $cont <= $maximo; $cont++ )
			{
				$renv_codigo = $rsParaUp->fields["sgd_renv_codigo"];  
				include "$ruta_raiz/include/query/radsalida/queryListado_planillas.php";	
//				$wrc=" WHERE SGD_RENV_CODIGO = $renv_codigo AND SGD_RENV_PLANILLA = '' ";
				$update_isql = "update sgd_renv_regenvio set sgd_renv_planilla='$no_planilla' $wrc";
                                //echo "LA INSTRUCCION DE ACTUALIZACION ES:".$update_isql;
				$rs = $db->query($update_isql);	
				$rsParaUp->MoveNext();
			}
		}
	}
	$no_planilla++;
	$iii++;
	$i_total3 = $i_total3 - $nregisEntidad ;
	}while ($i_total3>0);
	$pdf->Output($arpdf_tmp);

}

  $pager = new ADODB_Pager($db,$query_t,'adodb', true,$orderNo,$orderTipo);
  $pager->checkAll = false;
  $pager->checkTitulo = true;
  if($_GET["adodb_next_page"]) $pager->curr_page = $_GET["adodb_next_page"];
  $pager->Render($rows_per_page=1110,$linkPagina,$checkbox=chkAnulados);

?>
		<TABLE BORDER=0 WIDTH=100% class="borde_tab">
		<TR><TD class="listado2"  align="center"><center>
Se han Generado <b><?=$total_registros?> </b>Registros para Imprimir en <?=$paginas?> Planillas. <br>
<?
if ((strtoupper($_SESSION["entidad"]) != "MINSALUD")){
?>
<a href='<?=$arpdf_tmp?>' target='<?=date("dmYh").time("his")?>'>Abrir Archivo PDF</a></center>
<? }
?>
</td>
</TR>
</TABLE>
</body>
