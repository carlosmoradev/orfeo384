<?php
$ruta_raiz = "../../..";
include_once ("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler($ruta_raiz);
$id = (isset($_GET['deta_causal']) && !empty($_GET['deta_causal'])) ? $_GET['deta_causal'] : 0;
//if ($deta_causal and $sector) {
	$isql = "SELECT dcau.SGD_DCAU_CODIGO, cau.SGD_CAU_CODIGO, dcau.SGD_DCAU_DESCRIP
        FROM sgd_cau_causal cau, sgd_dcau_causal dcau
				WHERE cau.SGD_CAU_CODIGO=dcau.SGD_CAU_CODIGO
        AND cau.SGD_CAU_ESTADO=1
        AND dcau.sgd_dcau_estado=1
        AND dcau.SGD_DCAU_DESCRIP LIKE '%".$id."%'
        ORDER BY dcau.SGD_DCAU_DESCRIP ";
	$rs = $db->conn->query($isql);
	if ($rs && !$rs->EOF) {
	?>
	<?
  $i=1;
  do {
    $codigo_dcau =  $rs->fields[0];
    $codigo_cau =  $rs->fields[1];
    $nombre_dcau =  utf8_encode($rs->fields[2]);
    if($ddca_causal==$codigo_ddcau) {
      $datoss = " selected ";
    } else {
      $datoss = " ";
    }
    $temas[$codigo_dcau.'-'.$codigo_cau] = $nombre_dcau;
    $rs->MoveNext();
  }while(!$rs->EOF);
  //}
  }
 echo json_encode($temas);
?>