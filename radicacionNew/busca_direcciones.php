<?php
if($radicadopadre) {
  $buscar_d = $radicadopadre;
}
else { //adicionado jcr
  if($nurad) {
    $buscar_d = $nurad;
   }
} //adicionado jcr

if($espcodi) {
  $isql = "select NIT_DE_LA_EMPRESA as SGD_CIU_CEDULA,
      NOMBRE_DE_LA_EMPRESA,
      SIGLA_DE_LA_EMPRESA as SGD_CIU_APELL1,
      IDENTIFICADOR_EMPRESA,
      EMAIL,
      DIRECCION,
      TELEFONO_1 AS SGD_CIU_TELEFONO,
      CODIGO_DEL_DEPARTAMENTO as DPTO_CODI,
      CODIGO_DEL_MUNICIPIO as MUNI_CODI,NOMBRE_REP_LEGAL,
      ID_PAIS,
      ID_CONT
  from BODEGA_EMPRESAS
  where IDENTIFICADOR_EMPRESA = $espcodi ";
$rs = $db->conn->Execute($isql);
    if(!$rs->EOF) {
      $dir_codigo_us3 = $rs->fields["IDENTIFICADOR_EMPRESA"];
      $nombre     = trim($rs->fields["NOMBRE_DE_LA_EMPRESA"]);
      $documento  = $rs->fields["IDENTIFICADOR_EMPRESA"];
      $papel      = trim($rs->fields["SIGLA_DE_LA_EMPRESA"]);
      $sapel      = trim($rs->fields["NOMBRE_REP_LEGAL"]);
      $tel        = $rs->fields["SGD_CIU_TELEFONO"];
      $dir        = trim($rs->fields["DIRECCION"]);
      $mail       = $rs->fields["EMAIL"];
      $cc_documento = $rs->fields["SGD_CIU_CEDULA"];
      $cont       = $rs->fields["ID_CONT"];
      $pais       = $rs->fields["ID_PAIS"];
      $dpto       = $pais."-".$rs->fields["DPTO_CODI"];
      $muni       = $dpto."-".$rs->fields["MUNI_CODI"];
      $tipo       = $rs->fields["SGD_CIU_TIPO"];
      $dir_tipo_us3 = 3;
      $tipo_emp_us3 = 1;
      $nombre_us3 = $nombre;
      $documento_us3 = $documento;
      $cc_documento_us3 = $cc_documento;
      $prim_apel_us3 = $papel ;
      $seg_apel_us3 = $sapel ;
      $telefono_us3 = $tel;
      $direccion_us3 = $dir;
      $mail_us3   = $mail;
      $muni_us3   = $muni;
      $codep_us3  = $dpto;
      $tipo_us3   = $tipo;
      }

   }
    if(!$oem_codigo_us1) {$oem_codigo_us1=0;}
    if(!$document_us1){$documento_us1=0;}

//Nov2 Ini
    if (!empty($anexo) && !empty($numrad)) {
		$isqlRemDest = "select SGD_REM_DESTINO from ANEXOS
                            where ANEX_CODIGO='$anexo' AND
                                    ANEX_RADI_NUME=$numrad";
        $rsRemDest = $db->conn->Execute($isqlRemDest);
            if (!$rsRemDest->EOF){
                $rem_destinoBusq = $rsRemDest->fields['SGD_REM_DESTINO'];
            }
    }else if ( isset($verrad_sal) ) {
      $numrad = $verrad_sal;
      $rem_destinoBusq = $sgd_dir_tipo;
    }

    //Este condicional nos indica si busqueda viene por impresion, toca utilizar
    //variables diferentes a las utilizadas cuando es solo para info general,etc
    if($rem_destinoBusq==7 || substr($rem_destinoBusq,0,1)==7) {
        // Modificacion de radicados para otros destinatarios
        if(isset($anexo))
            $rem_isql = " and sgd_anex_codigo=$anexo ";
        else
            $rem_isql = " and sgd_dir_tipo=$sgd_dir_tipo";
    } else {
                $rem_isql = "";
    }
    //Nov2 fin

	if (!$radi_nume_radi)
         $radi_nume_radi = "RADI_NUME_RADI";
	if (!$buscar_d) $buscar_d = '0';
	 $isql = "select a.*
             from sgd_dir_drecciones a
             where a.RADI_NUME_RADI = $buscar_d $rem_isql ";
    $orderBY = " order by  SGD_DIR_TIPO";
    $isql .= " $orderBY";
	$rs = $db->conn->Execute($isql);
    while(!$rs->EOF&&$rs!=false) {
      $nombre = "";
      $nombreRD = "";
      $ciu = $rs->fields["SGD_CIU_CODIGO"];
      $oem = $rs->fields["SGD_OEM_CODIGO"];
      $esp = $rs->fields["SGD_ESP_CODI"];
      $fun = trim($rs->fields["SGD_DOC_FUN"]);
      $cont = $rs->fields["ID_CONT"];
      $pais = $rs->fields["ID_PAIS"];
      $dpto = $pais."-".$rs->fields["DPTO_CODI"];
      $muni = $dpto."-".$rs->fields["MUNI_CODI"];
      $otro = trim($rs->fields["SGD_DIR_NOMBRE"]);
      $nombreRD = $rs->fields["SGD_DIR_NOMREMDES"];
      $telUsX = trim($rs->fields["SGD_DIR_TELEFONO"]);
      $emailUsX = trim($rs->fields["SGD_DIR_MAIL"]);
      $ik = $rs->fields["SGD_DIR_TIPO"];
      $dir = $rs->fields["SGD_DIR_DIRECCION"];
      if($ik==1) {
        $dir_codigo_us1 = $rs->fields["SGD_DIR_CODIGO"];
        $telefono_us1 = $telUsX;
        $mail_us1 = $emailUsX;
        $nombre_us1 = $nombreRD;
        $otro_us1 = $otro;
        $idpais1 = $pais;
        $idcont1 = $cont;
      }
      if($ik==2) {
        $dir_codigo_us2 = $rs->fields["SGD_DIR_CODIGO"];
        $telefono_us2 = $telUsX;
        $mail_us2 = $emailUsX;
        $nombre_us2 = $nombreRD;
        $otro_us2 = $otro;
        $idpais2 = $pais;
        $idcont2 = $cont;          
      }
      if($rem_isql) $dir_codigo_us7 = $rs->fields["SGD_DIR_CODIGO"];
      if($ciu!=0) {
       $isql = "select * from sgd_ciu_ciudadano where sgd_ciu_codigo=$ciu";
       $rs1 = $db->conn->Execute($isql);
       $tipo_emp = 0;
      }

	  if(!empty($oem)) {
            $isql = "select SGD_OEM_NIT as SGD_OEM_CEDULA,
                SGD_OEM_OEMPRESA as SGD_CIU_NOMBRE,
                SGD_OEM_SIGLA as SGD_CIU_APELL1,
                SGD_OEM_REP_LEGAL as SGD_CIU_APELL2,
                SGD_OEM_CODIGO AS SGD_CIU_CODIGO,
                SGD_OEM_DIRECCION as SGD_CIU_DIRECCION,
                SGD_OEM_TELEFONO AS SGD_CIU_TELEFONO
                             FROM SGD_OEM_OEMPRESAS
                     WHERE SGD_OEM_CODIGO = $oem";
            $rs1 = $db->conn->Execute($isql);
            $tipo_emp = 2;
	   }
	  if($esp!=0) {
          $isql = "SELECT NOMBRE_DE_LA_EMPRESA as SGD_CIU_NOMBRE,
                  SIGLA_DE_LA_EMPRESA as SGD_CIU_APELL2,
                  IDENTIFICADOR_EMPRESA AS SGD_CIU_CODIGO,
                  DIRECCION as SGD_CIU_DIRECCION,
                  TELEFONO_1 AS SGD_CIU_TELEFONO,
                  ID_CONT
               FROM BODEGA_EMPRESAS
               WHERE IDENTIFICADOR_EMPRESA = $esp";
              $rs1 = $db->conn->Execute($isql);
              $tipo_emp = 1;
	   }
	   if($fun!=0) {
            $codiATexto = $db->conn->numToString("a.USUA_EXT");
            $concatTel=$db->conn->Concat("'Ext'","$codiATexto");
        $isql = "select a.USUA_NOMB as SGD_CIU_NOMBRE,
            b.DEPE_NOMB as SGD_CIU_APELL1,
            a.USUA_DOC AS SGD_CIU_CODIGO,
            b.DEPE_NOMB as SGD_CIU_DIRECCION,
            $concatTel AS SGD_CIU_TELEFONO
           FROM USUARIO a,
            SGD_USD_USUADEPE USD,
            dependencia b
           where  USD.depe_codi = b.depe_codi and
            a.USUA_DOC = '$fun' AND
            a.USUA_DOC = USD.USUA_DOC AND
            USD.SGD_USD_DEFAULT = 1";
              $rs1 = $db->conn->Execute($isql);
              $tipo_emp = 6;
              $dir = trim($rs1->fields["SGD_CIU_DIRECCION"]);
	   }
        if($rs1) {
          $nombre = trim($rs1->fields["SGD_CIU_NOMBRE"]);
          $documento = $rs1->fields["SGD_CIU_CODIGO"];
          $papel = trim($rs1->fields["SGD_CIU_APELL1"]);
          $sapel = trim($rs1->fields["SGD_CIU_APELL2"]);
          $tel = trim($rs1->fields["SGD_DIR_TELEFONO"]);
          $dirOrigen = trim($rs->fields["SGD_DIR_DIRECCION"]);
          $mail = trim($rs1->fields["SGD_DIR_MAIL"]);
          $cc_documento = $rs1->fields["SGD_CIU_CEDULA"];
          //$muni = $rs1->fields["SGD_CIU_MUNI"];
          //$codep = $rs1->fields["SGD_CIU_DEPTO"];
          //$tipo = $rs1->fields["SGD_CIU_TIPO"];
	   if($ik==1) {
            $tipo_emp_us1=$tipo_emp;
            if(!$nombre_us1) $nombre_us1=$nombre;
            $documento_us1 = $documento;
            $prim_apel_us1 =$papel ;
            $seg_apel_us1 = $sapel ;
            if(!$telefono_us1) $telefono_us1 = $tel;
            if($dir) $direccion_us1 = trim($dir); else $direccion_us1 = $dirOrigen;
            if(!$mail_us1)$mail_us1 = $mail;
            $muni_us1 = $muni;
            $codep_us1 = $dpto;
            $tipo_us1 = $tipo;
            $cc_documento_us1 = $cc_documento;
            //if(!trim($otro_us1)) $otro_us1 = $otro;
           }
	   if($ik==2) {
              $tipo_emp_us2=$tipo_emp;
              if(!$nombre_us2) $nombre_us2=$nombre;
              $documento_us2 = $documento;
              $prim_apel_us2 =$papel ;
              $seg_apel_us2 = $sapel ;
              if(!$telefono_us2) $telefono_us2 = $tel;
              if($dir) $direccion_us2 = trim($dir); else $direccion_us2 = $dirOrigen;
              if(!$mail_us2) $mail_us2 = $mail;
              $muni_us2 = $muni;
              $codep_us2 = $dpto;
              $tipo_us2 = $tipo;
              $cc_documento_us2 = $cc_documento;
              //if(!trim($otro_us1)) $otro_us2 = $otro;
          }
	   if($rem_isql) {
              $tipo_emp_us7=$tipo_emp;
              $nombre_us7=$nombre;
              $documento_us7 = $documento;
              $prim_apel_us7 =$papel ;
              $seg_apel_us7 = $sapel ;
              $telefono_us7 = $tel;
              if($dir) $direccion_us7 = trim($dir); else $direccion_us7 = $dirOrigen;
              $mail_us7 = $mail;
              $muni_us7 = $muni;
              $idpais7 = $pais;
              $idcont7 = $cont;
              $codep_us7 = $dpto;
              $tipo_us7 = $tipo;
              $cc_documento_us7 = $cc_documento;
              $otro_us7 = $otro;
	  }
	}
        
    $rs->MoveNext();
    $rs->Close();
}

?>
