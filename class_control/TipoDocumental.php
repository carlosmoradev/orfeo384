<?php

class TipoDocumental
{
    /**
     * Clase que maneja la asignacion de Serie, subserie, tipo documento 
     *
     * @param int Dependencia Dependencia de Territorial que Anula 
     * @db Objeto conexion
     * @access public
     */

    var $db;
     
    function TipoDocumental($db)
    {
        /**
         * Constructor de la clase
         * @db variable en la cual se recibe el cursor sobre el cual se esta trabajando.
         * 
         */
        $this->db = $db;
    }
     
    function consultaTipoTRD( $dependencia )
    {

    } // end of member function cconsultaTipoTRD

    /**
     *
     * @db Cursor de la base de datos que estamos trabajando.
     * @noRadicado numero al cual se le cambiara la fecha
     * @tdoc tipo documental que se asignara al radicado
     */
    function setFechVenci($noRadicado,$tdoc){
        //Agregamos la fecha de vencimiento
        //Sacando los dias de fiesta y los sabados
        $isqla = 'SELECT NOH_FECHA FROM SGD_NOH_NOHABILES';       
        $resa  = $this->db->conn->Execute($isqla);

        while (!$resa->EOF){
            $festi[] = strtotime(substr($resa->fields["NOH_FECHA"],0,10));
            $resa->MoveNext();
        }

        //fecha en que fue radicadoaaaa-mm-dd
        $sql = "SELECT 
            RADI_FECH_RADI ,
            FECH_VCMTO
            FROM 
            RADICADO  
            WHERE 
            RADI_NUME_RADI = '$noRadicado'";

        $rsTmp    = $this->db->conn->Execute($sql); 
        $fechRad  = $rsTmp->fields["RADI_FECH_RADI"];

        //tiempo del tipo documental
        //numero
        if(!empty($tdoc)){
            $sql3 = "SELECT 
                SGD_TPR_TERMINO FROM 
                SGD_TPR_TPDCUMENTO  
                WHERE 
                SGD_TPR_CODIGO = '$tdoc'";

            $rs2 = $this->db->conn->Execute($sql3); 
            $sal = $tiemTdoc = $rs2->fields["SGD_TPR_TERMINO"];
        }

        # Executa la busqueda y obtiene el registro a actualizar.
        // En esta seccion se determinan los tiempo de los radicados
        // se tienen encuenta los dia habiles y se determina cuanto
        // tiempo tiene para dar respuesta.
        //date("j-n-Y",strtotime("2000-10-29 + 1 days"))
        $tiemTdoc = empty($tiemTdoc)? 1 : $tiemTdoc;

        // frad:    Fecha en que se radico el documento 
        // fechfin: Fecha que contendra el resultado de la 
        // sumatoria de los dias habiles y festivos
        $fechfin = $frad  = substr($fechRad,0,10);

        while(!empty($tiemTdoc)){

            $fecha        = strtotime("$fechfin  + 1 day");
            $fechfin      = date("Y-m-d", $fecha);
            $noHabil      = getdate($fecha);
            $diasuma      = $noHabil["wday"];
            while(($diasuma == 0) or ($diasuma == 6) or in_array($fecha,$festi)){
                $fecha        = strtotime("$fechfin  + 1 day");
                $fechfin      = date("Y-m-d", $fecha);
                $noHabil      = getdate($fecha);
                $diasuma      = $noHabil["wday"];
            }
            $tiemTdoc--;
        }

        $resul    = $fechfin;

        $record = array(); 
        $record['FECH_VCMTO'] = $resul;
        $updateSQL = $this->db->conn->GetUpdateSQL($rsTmp, $record, true);
        # Actualiza el registro en la base de datos
        $this->db->conn->Execute($updateSQL); 

     /**echo " <br> fecha radicado $frad
        <br> dias por tipo documental $sal
     <br> fecha procesando $resul"; **/

    }


    /**
     * 
     *
     * @db Cursor de la base de datos que estamos trabajando.
     * @param int dependencia Dependencia que olicita la transaccion 
     * @param int usuadoc Documento de identificaci�n del usuario que solicita la transaccion 
     * @return void
     * @access public
     */
    function insertarTRD($codiTRDS,$codiTRD,$noRadicado, $coddepe , $codusuario, $tdoc = null){
        //Arreglo que almacena los nombres de columna

        $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
        $this->db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

        $sql = "SELECT 
            USUA_DOC
            ,USUA_LOGIN 
            FROM 
            USUARIO 
            WHERE 
            DEPE_CODI=$coddepe
            AND USUA_CODI=$codusuario"; 
        # Busca el usuairo para luego traer sus datos.
        $rs = $this->db->conn->Execute($sql);
        $usDoc = $rs->fields["USUA_DOC"];
        $ADODB_COUNTRECS=true;
        $sql = "SELECT *
            FROM SGD_RDF_RETDOCF 
            WHERE RADI_NUME_RADI = '$noRadicado'
            AND  SGD_MRD_CODIGO =  '$codiTRD'";
        # Executa la busqueda y obtiene el registro a actualizar.
        $rs = $this->db->conn->Execute($sql); 
        $ADODB_COUNTRECS=false;
        if($rs->RowCount()>=1){
            $mensaje_err = "<HR><center><B><FONT COLOR=RED>Esta Tipificacion YA esta incluida. <BR>  VERIFIQUE LA INFORMACION E INTENTE DE NUEVO</FONT></B></center><HR>";
        }else{
            $record = array(); # Inicializa el arreglo que contiene los datos a insertar
            # Asignar el valor de los campos en el registro
            # Observa que el nombre de los campos pueden ser mayusculas o minusculas
            $record["RADI_NUME_RADI"] = $noRadicado;
            $record["DEPE_CODI"]      = $coddepe;
            $record["USUA_CODI"]      = $codusuario;
            $record["USUA_DOC"]       = $usDoc;
            $record["SGD_MRD_CODIGO"] = $codiTRD;
            $record["SGD_RDF_FECH"]   = $this->db->conn->OffsetDate(0,$this->db->conn->sysTimeStamp);
            # Mandar como parametro el recordset vacio y el 
            # arreglo conteniendo los datos a insertar
            # a la funcion GetInsertSQL. Esta procesara los 
            # datos y regresara un enunciado SQL
            # para procesar el INSERT.
            $insertSQL = $this->db->insert("SGD_RDF_RETDOCF", $record, "true");
            $this->setFechVenci($noRadicado,$tdoc);
        }

        return ($codiTRDS);
    }


    function insertarTRDA($codiTRDS,$codiTRD,$noRadicado,$noRadicadoA, $coddepe , $codusuario){ 	
        # Busca el Documento del usuario 
        $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
        $this->db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $sql = "SELECT 
            USUA_DOC
            ,USUA_LOGIN 
            FROM 
            USUARIO 
            WHERE 
            DEPE_CODI=$coddepe
            AND USUA_CODI=$codusuario"; 
        # Busca el usuairo para luego traer sus datos.
        $rs = $this->db->conn->Execute($sql);
        $usDoc = $rs->fields["USUA_DOC"];
        $sql = "SELECT *
            FROM SGD_RDA_RETDOCA 
            WHERE ANEX_RADI_NUME = '$noRadicado'
            AND ANEX_CODIGO = '$noRadicadoA'
            AND  SGD_MRD_CODIGO =  '$codiTRD'";
        $rs = $this->db->conn->Execute($sql); # Executa la busqueda y obtiene el registro a actualizar.
        if($rs->RowCount()>=1) die ("<HR><center><B><FONT COLOR=RED>Esta Tipificacion YA esta incluida. <BR>  VERIFIQUE LA INFORMACION E INTENTE DE NUEVO</FONT></B></center><HR>");
        $record = array(); # Inicializa el arreglo que contiene los datos a insertar
        # Asignar el valor de los campos en el registro
        # Observa que el nombre de los campos pueden ser mayusculas o minusculas
        foreach($codiTRDS as $codiTRDR)
        {
            $record["ANEX_RADI_NUME"] = $noRadicado;
            $record["ANEX_CODIGO"] = $noRadicadoA;
            $record["DEPE_CODI"]      = $coddepe;
            $record["USUA_CODI"]      = $codusuario;
            $record["USUA_DOC"]       = $usDoc;
            $record["SGD_MRD_CODIGO"] = $codiTRD;
            $record["SGD_RDA_FECH"]   = $this->db->conn->OffsetDate(0,$this->db->conn->sysTimeStamp);
            # Mandar como parametro el recordset vacio y el arreglo conteniendo los datos a insertar
            # a la funcion GetInsertSQL. Esta procesara los datos y regresara un enunciado SQL
            # para procesar el INSERT.
            $insertSQL = $this->db->insert("SGD_RDA_RETDOCA", $record, "true");
        }
        return ($codiTRDS);
    }


    /**
     * funcion que registra en el historico el movimiento de eliminacion
     * y elimina el registro de la tabla sgd_rdf_retdocf (registros de asignacion de tipos
     * documentales para cada Radicado)
     * @return void
     * @access public
     */
    function eliminarTRD($nurad,$coddepe,$usua_doc,$codusuario,$codiTRD){
        /*Elimina la clasificacion TRD*/		 
        $isqlE = "delete 
            from SGD_RDF_RETDOCF
            where 
            RADI_NUME_RADI=$nurad 
            and SGD_MRD_CODIGO = $codiTRD
            ";
        $rsE = $this->db->conn->Execute($isqlE);

        /*Tipo Documento del Radicado*/
        $sql = "SELECT  
            TDOC_CODI
            FROM radicado 
            WHERE 
            radi_nume_radi = '$nurad'" ; 
        $rs = $this->db->conn->Execute($sql);
        $tip_dcto =  $rs->fields['TDOC_CODI'];

        /*Tipo Documento de la Calsificacion Eliminada*/
        $sql = "select SGD_TPR_CODIGO
            from SGD_MRD_MATRIRD 
            where SGD_MRD_CODIGO = $codiTRD";
        $rs = $this->db->conn->Execute($sql);
        $tip_trd =  $rs->fields['SGD_TPR_CODIGO'];

        require ("../include/query/busqueda/busquedaPiloto1.php");
        unset($db);

        $this->setFechVenci($nurad, 0);
        /*Verifica si la clasificacion Actual del Radicado es 
         * la misma que la de la clasificacion eliminada
         * */

        if ($tip_trd == $tip_dcto){

            $isqlM = "select $radi_nume_radi RADI_NUME_RADI,
                SGD_MRD_CODIGO
                from SGD_RDF_RETDOCF r
                where 
                r.RADI_NUME_RADI=$nurad";
            $rsM      = $this->db->conn->Execute($isqlM);
            $codiTRDM = $rsM->fields["SGD_MRD_CODIGO"];
            $cod_nvo  = 0;

            if($codiTRDM != ''){
                while(!$rsM->EOF)
                {
                    $cod_nvo =  $rsM->fields['SGD_MRD_CODIGO'];
                    $rsM->MoveNext();		
                }
                $isqlM = "select SGD_TPR_CODIGO
                    from SGD_MRD_MATRIRD 
                    where SGD_MRD_CODIGO = '$cod_nvo'";
                $rsM = $this->db->conn->Execute($isqlM);
                $cod_nvo =  $rsM->fields['SGD_TPR_CODIGO'];
            }

            $sql = "SELECT  
                TDOC_CODI
                FROM radicado 
                WHERE 
                radi_nume_radi = '$nurad'" ; 

            $rs = $this->db->conn->Execute($sql);
            $record = array(); # Inicializa el arreglo que contiene los datos a modificar
            $record['TDOC_CODI'] = $cod_nvo;
            $updateSQL = $this->db->conn->GetUpdateSQL($rs, $record, true);
            $this->db->conn->Execute($updateSQL); # Actualiza el registro en la base de datos

        }
    } 


    /*
     *Elimina el Registro de la TRD de un Anexo
     */
    function eliminarTRDA($nurad,$coddocu,$coddepe,$usua_doc,$codusuario,$codiTRD){	
        include_once ("../include/query/busqueda/busquedaPiloto1.php");
        $isqlE = "select rownum as NUM, 
            ANEX_RADI_NUME,
            ANEX_CODIGO,
            SGD_MRD_CODIGO
            from SGD_RDA_RETDOCA
            where 
            ANEX_RADI_NUME='$nurad'
            and ANEX_CODIGO='$coddocu'
            ";

        $rsE = $this->db->conn->Execute($isqlE);
        if($rsE->RowCount()>1)
        {
            while(!$rsE->EOF)
            {
                if ($rsE->fields['SGD_MRD_CODIGO'] == $codiTRD )
                {
                    $numreg_Eli = $rsE->fields['NUM'];
                }
                $rsE->MoveNext();

            }
            if ($numreg_Eli==$rsE->RowCount())
            {
                $i = $rsE->RowCount() - 1;
                $isqlE = "select  
                    ANEX_RADI_NUME,
                    ANEX_CODIGO,
                    SGD_MRD_CODIGO
                    from SGD_RDA_RETDOCA
                    where 
                    ANEX_RADI_NUME='$nurad'
                    and ANEX_CODIGO='$coddocu'
                    and rownum = '$i'
                    ";

                $rsE = $this->db->conn->Execute($isqlE);
                $cod_nvo =  $rsE->fields['SGD_MRD_CODIGO'];    
                $isqlE = "select SGD_TPR_CODIGO
                    from SGD_MRD_MATRIRD 
                    where SGD_MRD_CODIGO = '$cod_nvo'";

                $rsE = $this->db->conn->Execute($isqlE);
                $cod_nvo =  $rsE->fields['SGD_TPR_CODIGO'];
                $indi_change = "SI";
            }   
        }else
            {
                $cod_nvo = 0;
                $indi_change = "SI";
            }
        if ($indi_change == "SI")
        {
            $sql = "SELECT
                SGD_TPR_CODIGO
                FROM anexos 
                WHERE  ANEX_RADI_NUME = '$nurad'
                and ANEX_CODIGO = '$coddocu'
                ";
            $rs = $this->db->conn->Execute($sql);
            $record = array(); # Inicializa el arreglo que contiene los datos a modificar
            $record['SGD_TPR_CODIGO'] = $cod_nvo;
            $updateSQL = $this->db->conn->GetUpdateSQL($rs, $record, true);

            $this->db->conn->Execute($updateSQL); # Actualiza el registro en la base de datos

        }	 

        $isqlEAnex = "delete 
            from SGD_RDA_RETDOCA
            where 
            ANEX_RADI_NUME='$nurad'
            and ANEX_CODIGO='$coddocu'
            and SGD_MRD_CODIGO = '$codiTRD'
            ";
        $rsE = $this->db->conn->Execute($isqlEAnex);

    }




    function actualizarTRD($radicados,$tdoc){		
        require("../include/query/busqueda/busquedaPiloto1.php");
        unset($db);
        // tdoc_codi = 0 and 
        foreach($radicados as $noRadicado){
            $sql = "SELECT  
                TDOC_CODI
                FROM radicado 
                WHERE 
                radi_nume_radi = " . $noRadicado; 
            # Selecciona el registro a actualizar
            $rs = $this->db->conn->Execute($sql); # Executa la busqueda y obtiene el registro a actualizar.

            $record = array(); # Inicializa el arreglo que contiene los datos a modificar

            # Asignar el valor de los campos en el registro
            # Observa que el nombre de los campos pueden ser mayusculas o minusculas

            $record['TDOC_CODI'] = $tdoc;

            # Mandar como parametro el recordset y el arreglo conteniendo los datos a actualizar
            # a la funcion GetUpdateSQL. Esta procesara los datos y regresara el enunciado sql del
            # update necesario con clausula WHERE correcta.
            # Si no se modificaron los datos no regresa nada.
            $updateSQL = $this->db->conn->GetUpdateSQL($rs, $record, true);
            $this->db->conn->Execute($updateSQL); # Actualiza el registro en la base de datos
            # Si no se modificaron los datos no regresa nada.
            $this->setFechVenci($noRadicado,$tdoc);
        }
        return ($radicados);
    }


    /**
     * Actualiza el tipo documento table Anexos
     */

    function actualizarTRDA($radicados,$coddocu,$tdoc){
        foreach($radicados as $noRadicado){
            //Modificado el 05092005 SGD_TPR_CODIGO = 0  and
            $sqlUA = "SELECT
                SGD_TPR_CODIGO
                FROM anexos 
                WHERE  ANEX_RADI_NUME = '$noRadicado'
                and ANEX_CODIGO = '$coddocu'"
                ; 
            # Selecciona el registro a actualizar
            $rs = $this->db->conn->Execute($sqlUA); # Executa la busqueda y obtiene el registro a actualizar.

            $record = array(); # Inicializa el arreglo que contiene los datos a modificar

            # Asignar el valor de los campos en el registro
            # Observa que el nombre de los campos pueden ser mayusculas o minusculas

            $record['SGD_TPR_CODIGO'] = $tdoc;

            # Mandar como parametro el recordset y el arreglo conteniendo los datos a actualizar
            # a la funcion GetUpdateSQL. Esta procesara los datos y regresara el enunciado sql del
            # update necesario con clausula WHERE correcta.
            # Si no se modificaron los datos no regresa nada.

            $updateSQL = $this->db->conn->GetUpdateSQL($rs, $record, true);

            $this->db->conn->Execute($updateSQL); # Actualiza el registro en la base de datos
            # Si no se modificaron los datos no regresa nada.
        }
        setFechVenci($noRadicado,$tdoc);
        return ($radicados);
    } 


    function actualizarTRDAUnitario($noRadicado,$coddocu){
        $sqlUA = "SELECT
            SGD_TPR_CODIGO
            FROM anexos 
            WHERE  ANEX_CODIGO = '$noRadicado'"
            ; 
        # Selecciona el registro a actualizar
        $rs = $this->db->conn->Execute($sqlUA); # Executa la busqueda y obtiene el registro a actualizar.

        $record = array(); # Inicializa el arreglo que contiene los datos a modificar

        # Asignar el valor de los campos en el registro
        # Observa que el nombre de los campos pueden ser mayusculas o minusculas

        $record['SGD_TPR_CODIGO'] = $coddocu;

        # Mandar como parametro el recordset y el arreglo conteniendo los datos a actualizar
        # a la funcion GetUpdateSQL. Esta procesara los datos y regresara el enunciado sql del
        # update necesario con clausula WHERE correcta.
        # Si no se modificaron los datos no regresa nada.

        $updateSQL = $this->db->conn->GetUpdateSQL($rs, $record, true);

        $this->db->conn->Execute($updateSQL); # Actualiza el registro en la base de datos
        # Si no se modificaron los datos no regresa nada.

        return ($updateSQL);
    } 
}

?>
