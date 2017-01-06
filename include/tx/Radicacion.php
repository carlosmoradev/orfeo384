<?php
class Radicacion
{
  /** Aggregations: */

  /** Compositions: */

   /*** Attributes: ***/
	 /**
   * Clase que maneja los Historicos de los documentos
   *
   * @param int Dependencia Dependencia de Territorial que Anula
   * @db Objeto conexion
   * @access public
   */

	/**
	  *  VARIABLES DE DATOS PARA LOS RADICADOS
		*/
	var $db;
	var $tipRad;
	var $radiTipoDeri;
	var $nivelRad;
	var $radiCuentai;
	var $eespCodi;
	var $mrecCodi;
	var $radiFechOfic;
	var $radiNumeDeri;
	var $tdidCodi;
	var $descAnex;
	var $radiNumeHoja;
	var $radiPais;
	var $raAsun;
	var $radiDepeRadi;
	var $radiUsuaActu;
	var $radiDepeActu;
	var $carpCodi;
	var $carpPer;
	var $radiNumeRadi;
	var $trteCodi;
	var $radiNumeIden;
	var $radiFechRadi;
	var $sgd_apli_codi;
	var $tdocCodi;
    
    var	$nofolios;
    var	$noanexos;
    var	$guia;

	var $estaCodi;
	var $radiPath;
	var $nguia;
	var $tsopt;
	var $urgnt;
	var $dptcn;
	
	/*
	 * Código de verificación para consultar radicado vía consultaWeb
	 * @author Sebastian Ortiz AGPLV3 Ministerio de la Protección Social 2012
	 */
	
	var $codigoverificacion;

	/**
	  *  VARIABLES DEL USUARIO ACTUAL
		*/
	var $dependencia;
	var $usuaDoc;
	var $usuaLogin;
	var $usuaCodi;
	var $codiNivel=1;
	var $noDigitosRad;
	var $noDigitosDep=3;

    function Radicacion($db){
        /**
         * Constructor de la clase Historico
         * @db variable en la cual se recibe el cursor sobre el cual se esta trabajando.
         *
         */
        global $HTTP_SERVER_VARS,$PHP_SELF,$HTTP_SESSION_VARS,$HTTP_GET_VARS,$krd;
        //global $HTTP_GET_VARS;
        $this->db=$db;

        $this->noDigitosRad = 6;
        $curr_page = $id.'_curr_page';
        $this->dependencia= $_SESSION['dependencia'];
        $this->usuaDoc    = $_SESSION['usua_doc'];
        $this->usuaDoc    =$_SESSION['nivelus'];
        $this->noDigitosDep = $_SESSION['digitosDependencia']; 
        $this->usuaLogin  = $krd;
        $this->usuaCodi   = $_SESSION['codusuario'];
        isset($_GET['nivelus']) ? $this->codiNivel = $_GET['nivelus'] : $this->codiNivel = $_SESSION['nivelus'];
    }



function newRadicado($tpRad, $tpDepeRad)
{
	/** FUNCION QUE INSERTA UN RADICADO NUEVO
	* Busca el Nivel de Base de datos.
	* */
		$whereNivel = "";
    
        $sql = "SELECT CODI_NIVEL FROM USUARIO WHERE USUA_CODI = ".$this->radiUsuaActu." AND DEPE_CODI=".$this->radiDepeActu;
		# Busca el usuairo Origen para luego traer sus datos.
		//return "2011 $sql";
        
		$rs = $this->db->conn->Execute($sql); # Ejecuta la busqueda
		$usNivel = $rs->fields["CODI_NIVEL"];
		# Busca el usuairo Origen para luego traer sus datos.

		$SecName = "SECR_TP$tpRad"."_".$tpDepeRad;
    
		$secNew=$this->db->conn->nextId($SecName);
		
		if($secNew==0){
			$this->db->conn->RollbackTrans();
			$secNew=$this->db->conn->nextId($SecName);
			if($secNew==0) die("<hr><b><font color=red><center>Error no genero un Numero de Secuencia<br>SQL: $secNew</center></font></b><hr>");
		}
		$newRadicado = date("Y") . str_pad($this->dependencia,$this->noDigitosDep,"0",STR_PAD_LEFT) . str_pad($secNew,$this->noDigitosRad,"0", STR_PAD_LEFT) . $tpRad;
		
		if(!$this->radiTipoDeri){
		    $recordR["radi_tipo_deri"]= "0";
		}else{
			$recordR["radi_tipo_deri"]= $this->radiTipoDeri;
		}
		
		if(!$this->carpCodi) $this->carpCodi = 0;
		if(!$this->carpPer)  $this->carpPer = 0;
		if(!$this->radiNumeDeri) $this->radiNumeDeri = 0;
		if(!$this->nivelRad) $this->nivelRad=0;
		if(!$this->mrecCodi) $this->mrecCodi=0;


		$recordR["SGD_SPUB_CODIGO"] =  $this->nivelRad;
		$this->radiCuentai="'".str_replace("'"," ",$this->radiCuentai)."'";
		$recordR["RADI_CUENTAI"] =  $this->radiCuentai;
		$recordR["EESP_CODI"]    =	$this->eespCodi?$this->eespCodi:0;
		$recordR["MREC_CODI"]    =	$this->mrecCodi;

        $fechofic = $this->radiFechOfic;

        if(!empty($fechofi)){
            switch ($this->db->driver){
                case 'postgres':
                    $recordR["radi_fech_ofic"]=	"'".$fechofi."'";
                    break;
                default:
                    $recordR["radi_fech_ofic"]=	$this->db->conn->DBDate($this->radiFechOfic);
            }
        }
   
		$recordR["RADI_NUME_DERI"]  = $this->radiNumeDeri;
		$recordR["RADI_USUA_RADI"]  = $this->usuaCodi;
		$recordR["RADI_PAIS"]       = "'".$this->radiPais."'";
		$this->raAsun = str_replace("'"," ",$this->raAsun);
		$recordR["RA_ASUN"]         = "'".$this->raAsun."'";
 		$this->descAnex = str_replace("'"," ",$this->descAnex);		
		$recordR["radi_desc_anex"]  = "'".$this->descAnex."'";
		$recordR["RADI_DEPE_RADI"]  = $this->radiDepeRadi;
		$recordR["RADI_USUA_ACTU"]  = $this->radiUsuaActu;
		$recordR["carp_codi"]       = $this->carpCodi;
		$recordR["CARP_PER"]        = $this->carpPer;
		$recordR["RADI_NUME_RADI"]  = $newRadicado;
		$recordR["TRTE_CODI"]       = $this->trteCodi;
		$recordR["RADI_FECH_RADI"]  = $this->db->conn->OffsetDate(0,$this->db->conn->sysTimeStamp);
		$recordR["RADI_DEPE_ACTU"]  = $this->radiDepeActu;
		$recordR["TDOC_CODI"]       = $this->tdocCodi;
                $recordR["RADI_NUME_GUIA"]  = "'$this->guia'";
		if(!empty($this->nofolios)){
			      $recordR["RADI_NUME_FOLIO"] = $this->nofolios;
		  }

		  if(!empty($this->noanexos)){
			      $recordR["RADI_NUME_ANEXO"] = $this->noanexos;
		  }

		$recordR["TDID_CODI"]       = $this->tdidCodi;
		$recordR["depe_codi"]       = $this->dependencia;
		$recordR["sgd_trad_codigo"] = $tpRad;

		if(!$usNivel) $usNivel=1;
		$recordR["CODI_NIVEL"]=$usNivel;
		if($this->radiPath)  $recordR["RADI_PATH"] = "'".$this->radiPath."'";
		
		/*
		 * Codigo de verificación
		 */
		$recordR["SGD_RAD_CODIGOVERIFICACION"] = "'" . substr(sha1(microtime()), 0 , 5) . "'";
		
		$whereNivel = "";

		$insertSQL = $this->db->insert("RADICADO", $recordR, "false");
		
		//return "2011 $SecName  --> $secNew --> $newRadicado ->" ;				
		// $insertSQL = $this->db->conn->Replace("RADICADO", $recordR, "RADI_NUME_RADI", false);

		if(!$insertSQL)
		{
			echo "<hr><b><font color=red>Error no se inserto sobre radicado<br>SQL: ".$this->db->querySql."</font></b><hr>";
		}
		//$this->db->conn->CommitTrans();
		return $newRadicado;
  }


  function updateRadicado($radicado, $radPathUpdate = null)
  {
		$recordR["radi_cuentai"]    = $this->radiCuentai;
		$recordR["eesp_codi"]       = $this->eespCodi;
		$recordR["mrec_codi"]       = $this->mrecCodi;
		$recordR["radi_fech_ofic"]  = $this->db->conn->DBDate($this->radiFechOfic);
		$recordR["radi_pais"]       = "'".$this->radiPais."'";
		$recordR["ra_asun"]         = "'".$this->raAsun."'";
		$recordR["radi_desc_anex"]  = "'".$this->descAnex."'";
		$recordR["trte_codi"]       = $this->trteCodi;
		$recordR["tdid_codi"]       = $this->tdidCodi;
		$recordR["radi_nume_radi"]  = $radicado;
		$recordR["SGD_APLI_CODI"]   = $this->sgd_apli_codi;
		$recordR["RADI_NUME_FOLIO"] = $this->nofolios;
		$recordR["TDOC_CODI"] 		= $this->tdocCodi;		
		$recordR["RADI_NUME_ANEXO"] = $this->noanexos;
		$recordR["RADI_NUME_GUIA"]  = "'$this->guia'";
		
		// Linea para realizar radicacion Web de archivos pdf
		if(!empty($radPathUpdate) && $radPathUpdate != ""){
			$archivoPath = explode(".", $radPathUpdate);
			// Sacando la extension del archivo
			$extension = array_pop($archivoPath);
			if($extension == "pdf"){
				$recordR["radi_path"] = "'" . $radPathUpdate . "'";
			}
		}
		$insertSQL = $this->db->conn->Replace("RADICADO", $recordR, "radi_nume_radi", false);
		return $insertSQL;
  }

  /** FUNCION ANEXOS IMPRESOS RADICADO
    * Busca los anexos de un radicado que se encuentran impresos.
    * @param $radicado int Contiene el numero de radicado a Buscar
    * @return Arreglo con los anexos impresos
    * Fecha de creaci�n: 10-Agosto-2006
    * Creador: Supersolidaria
    * Fecha de modificaci�n:
    * Modificador:
    */
  function getRadImpresos($radicado)
  {
	$sqlImp = "SELECT A.RADI_NUME_SALIDA
                   FROM ANEXOS A, RADICADO R
                   WHERE A.ANEX_RADI_NUME=R.RADI_NUME_RADI
                   AND ( A.ANEX_ESTADO=3 OR A.ANEX_ESTADO=4 )
                   AND R.RADI_NUME_RADI = ".$radicado;
    // print $sqlImp;
	$rsImp = $this->db->conn->query( $sqlImp );
    
	if ( $rsImp->EOF )
        {
	   $arrAnexos[0] = 0;
	}
           else
           {
             $e = 0;
             while( $rsImp && !$rsImp->EOF )
             {
                $arrAnexos[ $e ] = $rsImp->fields['RADI_NUME_SALIDA'];
                $e++;
                $rsImp->MoveNext();
             }
	  }
	return $arrAnexos;
  }


    /** FUNCION DATOS DE UN RADICADO
    * Busca los datos de un radicado.
    * @param $radicado int Contiene el numero de radicado a Buscar
    * @return Arreglo con los datos del radicado
    * Fecha de creaci�n: 29-Agosto-2006
    * Creador: Supersolidaria
    * Fecha de modificaci�n:
    * Modificador:
    */
    function getDatosRad( $radicado )
    {
        $query  = 'SELECT RAD.RADI_FECH_RADI, RAD.RADI_PATH, TPR.SGD_TPR_DESCRIP,';
        $query .= ' RAD.RA_ASUN';
        $query .= ' FROM RADICADO RAD';
        $query .= ' LEFT JOIN SGD_TPR_TPDCUMENTO TPR ON TPR.SGD_TPR_CODIGO = RAD.TDOC_CODI';
        $query .= ' WHERE RAD.RADI_NUME_RADI = '.$radicado;
        // print $query;
        $rs = $this->db->conn->query( $query );
        
        $arrDatosRad['fechaRadicacion'] = $rs->fields['RADI_FECH_RADI'];
        $arrDatosRad['ruta'] = $rs->fields['RADI_PATH'];
        $arrDatosRad['tipoDocumento'] = $rs->fields['SGD_TPR_DESCRIP'];
        $arrDatosRad['asunto'] = $rs->fields['RA_ASUN'];
            
        return $arrDatosRad;
    }


 /**
     * Metodo que inserta direcciones de un radicado.
     * Usa la tabla SGD_DIR_DRECCIONES
     * @autor 12/2009 Fundacion Correlibre
     *        07/2009 adaptacion DNP por Jairo Losada
     * @version Orfeo 3.8.0
     * @param $tipoAccion numeric Indica 0--> es un parametro 
     * de Radicado Nuevo o 1-> Que es una modificacion a la Existente.
     **/

   function insertDireccion($radiNumeRadi, $dirTipo,$tipoAccion){
      if($tipoAccion==0) {
       $nextval = $this->db->conn->nextId("sec_dir_direcciones");
       $this->dirCodigo = $nextval;
      }
      $this->dirTipo = $dirTipo;
      $record = array();
      if($this->trdCodigo) $record['SGD_TRD_CODIGO'] = $this->trdCodigo;
      if($this->grbNombresUs) $record['SGD_DIR_NOMREMDES'] = $this->grbNombresUs;
      if($this->ccDocumento) $record['SGD_DIR_DOC']    = $this->ccDocumento;
      if($this->muniCodi) $record['MUNI_CODI']      = $this->muniCodi;
      if($this->dpto_tmp1) $record['DPTO_CODI']      = $this->dpto_tmp1;
      if($this->idPais) $record['ID_PAIS']        = $this->idPais;
      if($this->idCont) $record['ID_CONT']        = $this->idCont;
      if($this->funCodigo) $record['SGD_DOC_FUN']    = $this->funCodigo;
      if($this->oemCodigo) $record['SGD_OEM_CODIGO'] = $this->oemCodigo;
      if($this->ciuCodigo)$record['SGD_CIU_CODIGO'] = $this->ciuCodigo;
      if($this->espCodigo) $record['SGD_ESP_CODI']   = $this->espCodigo;
      $record['RADI_NUME_RADI'] = $radiNumeRadi;
      //$record['SGD_SEC_CODIGO'] = 0;
      if($this->direccion) $record['SGD_DIR_DIRECCION'] = $this->direccion;
      if($this->dirTelefono) $record['SGD_DIR_TELEFONO'] = $this->dirTelefono;
      if($this->dirMail) $record['SGD_DIR_MAIL']   = $this->dirMail;
      if($this->dirTipo and $tipoAccion==0) $record['SGD_DIR_TIPO']   = $this->dirTipo;
      if($this->dirCodigo) $record['SGD_DIR_CODIGO'] = $this->dirCodigo;
      if($this->dirNombre) $record['SGD_DIR_NOMBRE'] = $this->dirNombre;
	  
      $ADODB_COUNTRECS = true;
      //$insertSQL = $this->db->insert("SGD_DIR_DRECCIONES", $record, "true");
	  if($tipoAccion==0){
      $insertSQL = $this->db->conn->Replace("SGD_DIR_DRECCIONES",
                                                $record,
                                                array('RADI_NUME_RADI','SGD_DIR_TIPO'),
                                                $autoquote = true);
												$insertSQL = "ddddddddd ddccccwww ";
	  }else{
	  	$recordWhere['RADI_NUME_RADI'] = $radiNumeRadi;	
		$recordWhere['SGD_DIR_TIPO']   = $dirTipo;	
		$insertSQL = $this->db->update("SGD_DIR_DRECCIONES",
                                                $record,
                                                $recordWhere);
	  }
	  
      if(!$insertSQL) {
			  $this->errorNewRadicado .= "<hr><b><font color=red>Error no se inserto sobre sgd_dir_drecciones<br>SQL:". $this->db->querySql .">> $insertSQL </font></b><hr>";
			  $insertSQL =-1;
		  }else{
			  $this->errorNewRadicado .= "<hr><b><font color=green>0: Ok </font></b><hr>";
			  $insertSQL =1;
		  }
		  
      return $insertSQL;
    } 
	

} // Fin de Class Radicacion
?>
