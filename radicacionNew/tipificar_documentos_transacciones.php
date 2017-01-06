<?php

 	$krdOld=$krd;
 	session_start();
 	if(!$krd) $krd = $krdOld;
 	$ruta_raiz = "..";
    require_once("$ruta_raiz/_conf/constantes.php");
 	if(empty($_SESSION['dependencia'])) {
 		include (ORFEOPATH . "rec_session.php");
 	}	
	
    include_once("$ruta_raiz/include/db/ConnectionHandler.php");
	$db = new ConnectionHandler("$ruta_raiz");
	if (!defined('ADODB_FETCH_ASSOC')) define('ADODB_FETCH_ASSOC',2);
   	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	include_once ("../include/query/busqueda/busquedaPiloto1.php");
	include_once "$ruta_raiz/include/tx/Historico.php";
    include_once ("$ruta_raiz/class_control/TipoDocumental.php");
    $trd = new TipoDocumental($db);
	//$vars = get_defined_vars(); print_r($vars["_SESSION"]);exit;
	//$usua_doc = $_SESSION[]; 
	
if ($borrar)
{		
		$sqlE = "SELECT $radi_nume_radi RADI_NUME_RADI
					FROM SGD_RDF_RETDOCF r 
					WHERE RADI_NUME_RADI = '$nurad'
				    AND  SGD_MRD_CODIGO =  '$codiTRDEli'";
		$rsE=$db->conn->query($sqlE);
		$i=0;
		while(!$rsE->EOF)
		{
	    	$codiRegE[$i] = $rsE->fields['RADI_NUME_RADI'];
	    	$i++;
			$rsE->MoveNext();
		}
		$TRD = $codiTRDEli;
		include "$ruta_raiz/radicacion/detalle_clasificacionTRD.php";
	    $observa = "*Eliminada TRD*".$deta_serie."/".$deta_subserie."/".$deta_tipodocu;
		
		$Historico = new Historico($db);		  
  		 
		$radiModi = $Historico->insertarHistorico($codiRegE, $dependencia, $codusuario, $dependencia, $codusuario, $observa, 33); 
		$radicados = $trd->eliminarTRD($nurad,$coddepe,$usua_doc,$codusua,$codiTRDEli);
		$mensaje="Archivo eliminado<br> ";
		
		//guardar el registro en el historico de tipo documental.
		//permite controlar cambios del td de un radicado
			
		$queryGrabar	= "INSERT INTO SGD_HMTD_HISMATDOC(											
                                        SGD_HMTD_FECHA,
                                        RADI_NUME_RADI,
                                        USUA_CODI,
                                        SGD_HMTD_OBSE,
                                        USUA_DOC,
                                        DEPE_CODI
                                        )";
		
    	$queryGrabar 	.= " VALUES(
    						".$db->conn->OffsetDate(0,$db->conn->sysTimeStamp).",
    						$nurad,
    						$codusua,
							'El usuario: $usuax borro la trd',
							$usua_doc,
							$dependencia)";	
							
		$ejecutarQuerey	= $db->conn->Execute($queryGrabar);
		
    	if(empty($ejecutarQuerey)){
    		echo 'No se guardo el registro en historico documental';
    	}
 	}
  /*
  * Proceso de modificaci�n de una clasificaci�n TRD
  */
	if ($modificar && $tdoc !=0 && $tsub !=0 && $codserie !=0)  
  	{
		$sqlH = "SELECT 
					$radi_nume_radi RADI_NUME_RADI,
				    SGD_MRD_CODIGO 
					FROM SGD_RDF_RETDOCF r
				WHERE 
					RADI_NUME_RADI = '$nurad'
				    AND  DEPE_CODI = '$coddepe'";
					
		$rsH = $db->conn->query($sqlH);	
		$codiActu = $rsH->fields['SGD_MRD_CODIGO'];
		$i = 0;
		
		while (!$rsH->EOF) {
		    $codiRegH[$i] = $rsH->fields['RADI_NUME_RADI'];
		    $i++;
		    $rsH->MoveNext();
		}	
		$TRD = $codiActu;
		include "$ruta_raiz/radicacion/detalle_clasificacionTRD.php";
		      
		$observa 	= "*Modificado TRD* ".$deta_serie."/".$deta_subserie."/".$deta_tipodocu;
		//echo '0-' . $observa . " - Usuario:" . $_SESSION["usua_nomb"] . " - Dependencia:" . $_SESSION["depe_nomb"];
		$observa 	= $observa." - Usuario:".$usuax." - Dependencia:".$depex;
		//echo '1-' . $observa;
		$Historico 	= new Historico($db);
		//$radiModi = $Historico->insertarHistorico($codiRegH, $coddepe, $codusua, $coddepe, $codusua, $observa, 32);
		$radiModi 	= $Historico->insertarHistorico($codiRegH, $dependencia, $codusuario, $dependencia, $codusuario, $observa, 32);
		
		//Actualiza el campo tdoc_codi de la tabla Radicados		
		
		$radiUp 	= $trd->actualizarTRD($codiRegH, $tdoc);
		$mensaje 	= "Registro Modificado";
		$isqlTRD 	= "	select 
							SGD_MRD_CODIGO 
		      			from 
							SGD_MRD_MATRIRD 
		      			where 
							DEPE_CODI 			= '$coddepe'
		      		 	   	and SGD_SRD_CODIGO 	= '$codserie'
		      		       	and SGD_SBRD_CODIGO = '$tsub'
		      			   	and SGD_TPR_CODIGO 	= '$tdoc'";
		      
		$rsTRD 		= $db->conn->Execute($isqlTRD);
		$codiTRDU 	= $rsTRD->fields['SGD_MRD_CODIGO'];
		
		$sqlUA 		= "UPDATE 
							SGD_RDF_RETDOCF 
						SET 
							SGD_MRD_CODIGO = '$codiTRDU',
		      				USUA_CODI = '$codusua'
		      			WHERE 
							RADI_NUME_RADI = '$nurad' 
							AND DEPE_CODI =  '$coddepe'";
							
		$rsUp = $db->conn->query($sqlUA);
		
		//guardar el registro en el historico de tipo documental.
		//permite controlar cambios del td de un radicado
		$isqlTRD = "
					select 
						SGD_MRD_CODIGO
					from 
						SGD_MRD_MATRIRD
					where 
						DEPE_CODI 			= '$dependencia'
				 	    and SGD_SRD_CODIGO 	= '$codserie'
				        and SGD_SBRD_CODIGO = '$tsub'
					    and SGD_TPR_CODIGO 	= '$tdoc'";
						
		$rsTRD = $db->conn->Execute($isqlTRD);		    		
		$codiTRD = $rsTRD->fields['SGD_MRD_CODIGO'];
    	
			
		$queryGrabar	= "INSERT INTO SGD_HMTD_HISMATDOC(											
                                        SGD_HMTD_FECHA,
                                        RADI_NUME_RADI,
                                        USUA_CODI,
                                        SGD_HMTD_OBSE,
                                        USUA_DOC,
                                        DEPE_CODI,
                                        SGD_MRD_CODIGO
                                        )";
		
    	$queryGrabar 	.= " VALUES(
    						".$db->conn->OffsetDate(0,$db->conn->sysTimeStamp).",
    						$nurad,
    						$codusua,
							'El usuario: $usuax Cambio el tipo de documento',
							$usua_doc,
							$dependencia,
							'$codiTRD')";	

		$ejecutarQuerey	= $db->conn->Execute($queryGrabar);
		
    	if(empty($ejecutarQuerey)){
    		echo 'No se guardo el registro en historico documental';
    	}
		
			
		$mensaje = "Registro Modificado   <br> ";		
	}
		$tdoc = '';
		$tsub = '';
		$codserie = '';

?>

</script>
<body bgcolor="#FFFFFF" topmargin="0">
<br>
<div align="center">
<p>
<?=$mensaje?>
</p>
<input type='button' value='   Cerrar   ' class='botones_largo' onclick='opener.regresar();window.close();'>
</body>
</html> 
