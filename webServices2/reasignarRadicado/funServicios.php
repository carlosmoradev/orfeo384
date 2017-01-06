<? 
//echo reasignarRadicado($resolucion,$tipoNotificacion,$fechaNotificacion,$fechaFijacion );

function reasignarRadicado($numeroRadicado,$usuarioOrigen,$usuarioDestino,$comentario ){
	if($numeroRadicado==Null) return "ERROR: hace falta de numero de radicado";
	if($usuarioOrigen==Null) return "ERROR: hace falta de usuario Origen";
	if($usuarioDestino==Null) return "ERROR: hace falta de usuario destino";
	if($comentario==Null) return "ERROR: hace falta de comentario";
	if (strlen( $numeroRadicado) == "14") {
        	global $ruta_raiz;       
		//consulta si el radicado existe
		$consultaRad ="select radi_nume_radi,radi_path,radi_usua_actu, radi_depe_actu  from radicado where radi_nume_radi=".$numeroRadicado;
		$db = new ConnectionHandler($ruta_raiz);
		$rs2=$db->query( $consultaRad );
		if(!$rs2->EOF){	
			include "../include/tx/Tx.php";
			//valida si posee trd
			$anoRad = substr($numeroRadicado,0,4);
			$isqlTRDP = "select radi_nume_radi as RADI_NUME_RADI from SGD_RDF_RETDOCF r where r.RADI_NUME_RADI='$numeroRadicado'";
 			$rsTRDP = $db->conn->Execute($isqlTRDP);
			$radiNumero = $rsTRDP->fields[0];
			if( !($anoRad == "2005" or $anoRad == "2004" or $anoRad == "2003")  && strlen (trim($radiNumero)==0))
				{	return "ERROR: FALTA CLASIFICACION TRD";	}
			$consultaUsuarioOrigen ="select  usua_codi,depe_codi from usuario where usua_login='".$usuarioOrigen."'";
			$rs1=$db->query( $consultaUsuarioOrigen );
			$dependenciaOrigen=$rs1->fields[1];
			$codusuarioOrigen=$rs1->fields[0];
			if ($rs2->fields[2]!=$rs1->fields[0]){ return "ERROR: El Radicado No Pertence a Esta Usuario. "; }
				$consultaUsuarioOrigen ="select  usua_codi,depe_codi from usuario where usua_login='".$usuarioDestino."'";
				$rs5=$db->query( $consultaUsuarioOrigen );
				$dependenciaDestino=$rs5->fields[1];
				$codusuarioDestino=$rs5->fields[0];
				$codTx=9;
				$carp_codi=0;
				$rs4 = new Tx($db);
		        	$radinums[0]=$numeroRadicado;
				//return " destino= $dependenciaDestino origen= $dependenciaOrigen usuarioOrigen = $codusuarioOrigen destino $codusuarioDestino";
				if(($dependenciaDestino!=$dependenciaOrigen && $codusuarioOrigen!=1) || ($codusuarioDestino!=1 && $dependenciaDestino!=$dependenciaOrigen))
					{ return "ERROR : la reasignacion no se realiza. estan en dependecias diferentes "; }
				$usCodDestino = $rs4->reasignar( $radinums, $usuarioOrigen,$dependenciaDestino,$dependenciaOrigen, $codusuarioDestino,$codusuarioOrigen,"no",$comentario,$codTx,$carp_codi);
				$consultaUsuario ="select radi_nume_radi  from radicado where radi_nume_radi=".$numeroRadicado." and radi_depe_actu =".$dependenciaDestino." and radi_usua_actu =".$codusuarioDestino." and  carp_codi=".$carp_codi;
			$rs3=$db->query( $consultaUsuario );
			if(!$rs3->EOF){	
			  return "OK";
			}
			else{
			    return "ERROR: Fallo el cambio de carpetas, intentar de nuevo.";
			}
		}
		else{
			return "ERROR: El radicado no existe.";
		}
	}
	else{
	    return "ERROR: El numero de radicado es encuentra incompleto. ";
	}
}


?>
