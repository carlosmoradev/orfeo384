<?
ini_set("display_errors",1);
$ruta_raiz = "..";
include_once    ("$ruta_raiz/include/db/ConnectionHandler.php");
require_once    ("$ruta_raiz/class_control/Mensaje.php");

$iSql = " select * 
    from radicado 
   where radi_nume_radi >=201242402472622
       and radi_nume_radi <=201242402475082
";

$iSql = "select * from radicado
 where cast(radi_nume_radi as varchar) like '20124240%2' 
 and radi_path like '%html%' and (ra_asun like 'Creacio%'
 or  ra_asun like 'pqr:%')";

$iSql = " select * from radicado 
where
 cast(radi_nume_radi as varchar) >=  '201242402499982' 
 and cast(radi_nume_radi as varchar)  <= '201242402502542'";


$iSql = "select * from radicado 
 where cast(radi_nume_radi as varchar) >=  '201242402522702'
 and cast(radi_nume_radi as varchar)  <= '201242402522722'";


$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);


$rs = $db->conn->query($iSql);
$f = fopen("archivo.html", "rb");
$data=fread($f,4500); 
  fclose ($f);
while(!$rs->EOF){
  $radicado = $rs->fields["RADI_NUME_RADI"]; 
  $iSql2 = " select sgd_dir_nomremdes, sgd_dir_nombre
                ,sgd_dir_mail
                ,sgd_dir_telefono
                ,sgd_dir_direccion
                ,SGD_DIR_DOC
		,deptotmp, munitmp
              from sgd_dir_drecciones
              where radi_nume_radi=$radicado and
              sgd_dir_tipo=1
              ";
  $rsDir = $db->conn->query($iSql2);
  
  
  $radiFechRadi = $rs->fields["RADI_FECH_RADI"]; 
  $radiFechOficio = $rs->fields["RADI_FECH_OFIC"]; 
  $radiCuentaI = $rs->fields["RADI_CUENTAI"];
  echo "--".$radiCuentaI." *<hr>";
  $pqrE = $rs->fields["RA_ASUN_COMPLETA"];
  $dirNombre = strtoupper($rsDir->fields["SGD_DIR_NOMREMDES"]);
  $dirDoc = strtoupper($rsDir->fields["SGD_DIR_DOC"]);
  $dirE = strtoupper($rsDir->fields["SGD_DIR_DIRECCION"]);
  $dirEmail = $rsDir->fields["SGD_DIR_MAIL"];
  $dirTelefono = $rsDir->fields["SGD_DIR_TELEFONO"];
  $dptoE = $rsDir->fields["DEPTOTMP"];
  $muniE = $rsDir->fields["MUNITMP"];
  $paisE = $rs->fields["ID_PAIS"];
  $paisE = "COLOMBIA"; 
  $dataFinal = str_replace("RAD_E",$radicado,$data);
  $dataFinal = str_replace("PQR_E",$pqrE,$dataFinal);
  $dataFinal = str_replace("ID_E",$radiCuentaI,$dataFinal); 
  $dataFinal = str_replace("FECHA_E",$radiFechRadi,$dataFinal);
  $dataFinal = str_replace("NOMBRE_E",$dirNombre,$dataFinal);
  $dataFinal = str_replace("EMAIL_E",$dirEmail,$dataFinal);
  $dataFinal = str_replace("DOC_E",$dirDoc,$dataFinal);
  $dataFinal = str_replace("DIRECCION_E",$dirE,$dataFinal);
  $dataFinal = str_replace("TELEFONO_E",$dirTelefono,$dataFinal);
  $dataFinal = str_replace("PAIS_E",$paisE,$dataFinal);
  $dataFinal = str_replace("DEPARTAMENTO_E",$dptoE,$dataFinal);
  $dataFinal = str_replace("MUNICIPIO_E",$muniE,$dataFinal);
  $dataFinal = str_replace("ENVIADO_EL_E",$radiFechOficio,$dataFinal);
  echo $dataFinal ."<br>";

  $fp = fopen($radicado.".html", "w");
    fputs ($fp, $dataFinal);
  fclose ($fp);
  
  $rs->MoveNext();


 

}


?>
