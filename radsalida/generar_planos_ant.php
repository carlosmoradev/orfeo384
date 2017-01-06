<?php
session_start();

    if (!$_SESSION['dependencia'])
        header ("Location: $ruta_raiz/cerrar_session.php");

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;
define('ADODB_ASSOC_CASE', 2);

if(!$no_planilla_Inicial or intval($no_planilla_Inicial) == 0) die ("<table class=borde_tab width='100%'><tr><td class=titulosError><center>Debe colocar un Numero de Planilla v&aacute;lido</center></td></tr></table>");
if($generar_listado){
  $ruta_raiz = "..";
  include_once ("$ruta_raiz/include/db/ConnectionHandler.php");
  include_once ("$ruta_raiz/adodb/toexport.inc.php");
  include_once ("$ruta_raiz/adodb/adodb.inc.php");
  include_once ("$ruta_raiz/class_control/planillas-class.php");

  $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
  $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC; 

  /*NOMBRE	DIRECCION DESTINATARIO	
   * CIUDAD DESTINATARIO	DEPARTAMENTO	
   * PESO	ADICIONAL 1*/
  $isqlSipos = "SELECT
                sgd_renv_nombre,
                sgd_renv_dir, 
                sgd_renv_pais,
                sgd_renv_mpio,
                sgd_renv_depto, 
                sgd_renv_peso,
                radi_nume_sal,
                sgd_dir_tipo
            FROM 
                SGD_RENV_REGENVIO 
            WHERE 
                SGD_RENV_PLANILLA = '" .$no_planilla_Inicial."' 
                AND SGD_RENV_DESTINO != 'Local' and SGD_RENV_DESTINO != 'Nacional'
                AND SGD_FENV_CODIGO = $tipo_envio
                AND DEPE_CODI=  $dependencia 
                ";
 
  if(!empty($fecha_mes )){
        $isqlSipos .= " AND ".$sqlChar." = ".$fecha_mes;
  }

  $isqlSipos         = $isqlSipos . " order by sgd_renv_depto, sgd_renv_mpio";
  $rsSipos         = $db->conn->Execute($isqlSipos);
  $encabezado = 'NOMBRE;DIRECCION DESTINATARIO;CIUDAD DESTINATARIO;DEPARTAMENTO;PESO;ADICIONAL 1;ADICIONAL 2';
  
  while(!$rsSipos->EOF){
      $nom  = $rsSipos->fields['sgd_renv_nombre'];
      $pais = $rsSipos->fields['sgd_renv_pais'];

      $data[] = array('NOMBRE' => $rsSipos->fields['sgd_renv_nombre'], 'DIRECCION DESTINATARIO' => $rsSipos->fields['sgd_renv_dir'],
                'CIUDAD DESTINATARIO'=> $rsSipos->fields['sgd_renv_mpio'] ,'DEPARTAMENTO' => $rsSipos->fields['sgd_renv_depto'],
                'PESO' => $rsSipos->fields['sgd_renv_peso'],'ADICIONAL 1' => $rsSipos->fields['radi_nume_sal'] ,'ADICIONAL 2' => $rsSipos->fields['sgd_dir_tipo']);
 
     $rsSipos->MoveNext();
  }

  $numPlanilla=$no_planilla_Inicial . "_".$krd ;
  $planillaGEn=new panillasClass();
  $planillaGEn->setRuta_raiz('.');
  $planillaGEn->setData($data);
  $planillaGEn->setNumPlanilla($numPlanilla);
  $planillaGEn->setRutaArchivo('');
  $planillaGEn->setRutaArchivo("$ruta_raiz/bodega/pdfs");
  $planillaGEn->setEncabezado($encabezado);
  $g=$planillaGEn->generarSipos();

}
?>
<table class=borde_tab width='100%'><tr><td class=titulos2><center>ARCHIVOS PLANOS 
  <td><a href='<?php echo $g['csv'];?>'  >CSV</a></td>
  <td><a href='<?=$g['txt']?>' target='<?=date("dmYh").time("his")?>'>TXT</a></td>
</center></td></tr></table>
