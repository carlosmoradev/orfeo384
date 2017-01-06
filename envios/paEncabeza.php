<?php 

if (isset($nomcarpeta))
    $nomcarpetaOLD = $nomcarpeta;
else{
    $nomcarpetaOLD = "";
    $nomcarpeta = "";
}

if (!isset($_GET['carpeta'])) {
    $carpeta = "0";
    $nomcarpeta = "Entrada";
}

if (!isset($pagina_actual))
    $pagina_actual = "";
    
if (!isset($estado_sal_max))
    $estado_sal_max = "";    

if (!isset($pagina_sig))
    $pagina_sig = "";

if (!isset($dep_sel))
    $dep_sel = "";
    
$accion  = $pagina_actual .'?'. session_name().'='. session_id().
      '&estado_sal_max='.$estado_sal_max.'&pagina_sig='. 
      $pagina_sig .'&dep_sel='. $dep_sel. '&nomcarpeta='.
      $nomcarpeta .'method=GET';
      
      
if(isset($_GET['nomcarpeta']))
    $getNombreCarpeta = $_GET['nomcarpeta'];
else
    $getNombreCarpeta = "";
?>
<table cellspacing="1" border="0" align="center" width="100%" valign="top" class="borde_tab">
<tr >
  <td class=titulos2 width='35%'>
    Listado De:
  </td>
  <td class=titulos2 >
    Usuario
  </td>
  <td class=titulos2 >
    Dependencia
  </td>
</tr>            
<tr>
  <td class="info">
      <?= $getNombreCarpeta ?>
  </td>              
  <td class="info">
      <?= $_SESSION['usua_nomb']?>
  </td>
  <?php
        if (!isset($swBusqDep))
            $swBusqDep = false;
            
        if (!$swBusqDep) {
  ?>

<td class="info">
  <?= $_SESSION['depe_nomb']?>
    </td>
<? } else { ?>
  <form name=formboton action="<?=$accion ?>">
  <input type='hidden' name='<?=session_name()?>' value='<?=session_id()?>'> 
  <input type=hidden name=estado_sal value='<?=$estado_sal?>'>
  <input type=hidden name=estado_sal_max value='<?=$estado_sal_max?>'>
  <td>
    <?php
    include_once "$ruta_raiz/include/query/envios/queryPaencabeza.php";
    $sqlConcat = $db->conn->Concat($conversion, "'-'", depe_nomb);
    $sql       = "select $sqlConcat ,depe_codi from dependencia where depe_estado = 1
                            order by depe_codi";
    $rsDep     = $db->conn->Execute($sql);
    if (!$depeBuscada)
        $depeBuscada = $dependencia;
    print $rsDep->GetMenu2("dep_sel", "$dep_sel", false, false, 0, " onChange='submit();' class='select'");
    ?>
  </td>
  </form>
<? } ?> 
</tr>
</table>
