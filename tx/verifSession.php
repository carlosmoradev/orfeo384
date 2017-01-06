<?
  /** verificacion si el radicado se encuentra en el usuario Actual
    *
    */
  /*
		$sql = "SELECT 
					R.RADI_USUA_ACTU AS USU,
					R.RADI_DEPE_ACTU AS DEPE,
					R.SGD_SPUB_CODIGO AS PRIVRAD					
				FROM 
					RADICADO R
				WHERE 
					R.RADI_NUME_RADI=$verrad"; 
		# Busca el usuairo Origen para luego traer sus datos.
		$rs = $db->conn->Execute($sql); # Ejecuta la busqueda 
		$verCodusuario = $rs->fields["USU"]; 
		$verDependencia = $rs->fields["DEPE"];  
		$verSeguridadRad = $rs->fields["PRIVRAD"];  
		
		//Buscamos nivel de seguridad del expediente en que se encuentra el radicado
		$sqlExp = "SELECT 
					EXP.SGD_EXP_PRIVADO AS PRIVEXP
				FROM 
					RADICADO R, SGD_SEXP_SECEXPEDIENTES SEXP, SGD_EXP_EXPEDIENTE EXP
				WHERE 
					R.RADI_NUME_RADI=$verrad AND R.RADI_NUME_RADI = EXP.RADI_NUME_RADI AND EXP.SGD_EXP_NUMERO = SEXP.SGD_EXP_NUMERO" ; 
		
		$rs = $db->conn->Execute( $sqlExp );
		$verSeguridadExp = $rs->fields["PRIVEXP"];  
		
		if( ( $verSeguridadExp == 0 || $verSeguridadExp == 2 ) && $codusuario == $verCodusuario && $dependencia == $verDependencia )
		{		
			$verradPermisos = "Full";
		}
		elseif ( $verSeguridadExp == 1 && $codusuario == $verCodusuario && $dependencia == $verDependencia && $_SESSION["codusuario"] == 1 ) {
			$verradPermisos = "Full";
		}else
		{
			$verradPermisos = "Otro";
			$mostrar_opc_envio = 0;
			$modificar = false;			
		}
*/
/*
   * verificacion si el radicado se encuentra en el usuario Actual
   * Modificado para unificar reglas de validacion
   * visibilidad del documento
   */
   //Consulta Informados
    $usuaInformado= "";
    $isqlI = "select USUA_DOC
         from INFORMADOS
         where RADI_NUME_RADI='$verrad'
         and USUA_DOC= " .$_SESSION[ 'usua_doc' ];
    $rsI=$db->conn->Execute($isqlI);
    if (!$rsI->EOF){
	 $usuaInformado=$rsI->fields["USUA_DOC"];
    }         

        $responsableExp = "";
		$sql = "SELECT 
					R.RADI_USUA_ACTU AS USU,
					R.RADI_DEPE_ACTU AS DEPE,
					R.RADI_USU_ANTE AS USUA_ANTE,
					R.SGD_SPUB_CODIGO AS PRIVRAD,
					U.USUA_DOC AS USUA_DOCU,
					U.CODI_NIVEL AS CODI_NIVELR			
				FROM 
					RADICADO R,
					USUARIO U
				WHERE 
					R.RADI_NUME_RADI=$verrad
					AND R.RADI_USUA_ACTU = U.USUA_CODI
                    AND R.RADI_DEPE_ACTU = U.DEPE_CODI";

		# Busca el usuairo Origen para luego traer sus datos.
		$rs = $db->conn->Execute($sql); # Ejecuta la busqueda 
		$verCodusuario = $rs->fields["USU"]; 
		$verDependencia = $rs->fields["DEPE"];  
		$verSeguridadRad = $rs->fields["PRIVRAD"];  
		$usua_ante = $rs->fields["USUA_ANTE"];
		$nivelRadicado=$rs->fields["CODI_NIVELR"];
		$USUA_ACTU_R = $rs->fields["USUA_DOCU"];
		
		//Buscamos nivel de seguridad del expediente en que se encuentra el radicado
		$sqlExp = "SELECT 
					EXP.SGD_EXP_PRIVADO AS PRIVEXP, USUA_DOC_RESPONSABLE AS RESPONSABLE
				FROM 
					RADICADO R, SGD_SEXP_SECEXPEDIENTES SEXP, SGD_EXP_EXPEDIENTE EXP
				WHERE 
					R.RADI_NUME_RADI=$verrad 
					AND R.RADI_NUME_RADI = EXP.RADI_NUME_RADI 
					AND EXP.SGD_EXP_NUMERO = SEXP.SGD_EXP_NUMERO
					AND EXP.sgd_exp_estado<>2" ; 
					
		$rsE = $db->conn->Execute( $sqlExp );
		$verSeguridadExp = $rsE->fields["PRIVEXP"];  
		$responsableExp = $rsE->fields["RESPONSABLE"];
		/*
		 * Modificado el 11112009
		 * para el manejo de seguridad Radicado incluido en mas de un expediente
		 */
		  $usu_session =$_SESSION[ 'usua_doc' ];
		  if ($responsableExp <> $usu_session)
		    {

		    	$sqlExpR = "SELECT 
					EXP.SGD_EXP_PRIVADO AS PRIVEXP, USUA_DOC_RESPONSABLE AS RESPONSABLE
				FROM 
					RADICADO R, SGD_SEXP_SECEXPEDIENTES SEXP, SGD_EXP_EXPEDIENTE EXP
				WHERE 
					R.RADI_NUME_RADI=$verrad 
					AND R.RADI_NUME_RADI = EXP.RADI_NUME_RADI 
					AND EXP.SGD_EXP_NUMERO = SEXP.SGD_EXP_NUMERO
					AND EXP.sgd_exp_estado<>2
					AND USUA_DOC_RESPONSABLE = '$usu_session' " ; 
		         $rsER = $db->conn->Execute( $sqlExpR );
		        if (!$rsER->EOF){
		             $verSeguridadExp = $rsER->fields["PRIVEXP"];  
		             $responsableExp = $rsER->fields["RESPONSABLE"];
		        }
		    	
		    }
 		     
		//El radicado se encuentra en un expediente privado, y es el usuario 
		//dueno de la seguridad o es de la dependencia de seguridad puede ver
		if( ( $verSeguridadExp == 0 || $verSeguridadExp == 2 ) && $codusuario == $verCodusuario && $dependencia == $verDependencia )
		{		
			$verradPermisos = "Full";
		}
		elseif ( $verSeguridadExp == 1 && $codusuario == $verCodusuario && $dependencia == $verDependencia && $_SESSION["codusuario"] == 1 ) {
			//El radicado se encuentra en un expediente privado, con seguridad
			// de jefe y el usuario es jefe, puede ver el radicado
			$verradPermisos = "Full";
		}elseif ($verSeguridadRad == 1 && ($responsableExp == $_SESSION[ 'usua_doc' ] || $USUA_ACTU_R == $_SESSION["usua_doc"] || $usuaInformado == $_SESSION["usua_doc"]) ){
			//No cumple con las condiciones de seguridad de expediente, pero 
			//El usuario es el responsable del expediente donde esta el radicado
			//privado, luego puede verlo
			$verradPermisos = "PasaSegExp";
		}elseif($verSeguridadRad == 1 && $usua_ante == $krd && $verDependencia == '999'){
			//No cumple con las condiciones de seguridad de expediente, pero 
			//El usuario que archivo el documento es el mismo que esta consultando
			//Modificado el 11112009 Liliana Gomez Velasquez
			//luego puede verlo
			$verradPermisos = "PasaSegExp";
		} elseif ($verSeguridadRad != 1 && ($_SESSION["nivelus"] >= $nivelRadicado || $USUA_ACTU_R == $_SESSION["usua_doc"]  || $responsableExp == $_SESSION["usua_doc"] || $usuaInformado == $_SESSION["usua_doc"])){
		
		    //El Radicado no es privado y  
			//El usuario que consulta tiene el mismo nivel que el usuario actual
			//Modificado el 11112009 Liliana Gomez Velasquez
			//luego puede verlo
			$verradPermisos = "PasaSegExp";	
		}
		 else{
			$verradPermisos = "Otro";
			$mostrar_opc_envio = 0;
			$modificar = false;			
		}
?>  
