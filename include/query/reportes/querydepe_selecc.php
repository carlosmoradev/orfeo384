<?
	/**
	  * CONSULTA VERIFICACION PREVIA A LA RADICACION
	  */
        $ln=$_SESSION["digitosDependencia"];
	switch($db->driver)
	{  
	 case 'mssql':
			$radi_nume_sal = "convert(varchar(14), RADI_NUME_SAL)";
			$where_depe = " and ".$db->conn->substr."(".$radi_nume_sal.", 5, 3) in ($lista_depcod)";
	break;		
	case 'oracle':
	case 'oci8':
	case 'oci805':		
			$where_depe = "and ".$db->conn->substr."(cast(a.radi_nume_sal as varchar(20)), 5, 3) in ($lista_depcod)";
	break;		
	
	//Modificado skina
	default:
		//$where_depe = "and cast(".$db->conn->substr."(cast(a.radi_nume_sal as varchar(20)), 5, 3) as integer) in ($lista_depcod)";
		// Modificado Julio 2012 para tomar directamente del atributo depe_codi
                //$where_depe = "and a.depe_codi in ($lista_depcod)";
                // Modificado el 20 nov 2012 para poder determinar la dependencia que genero el radicado en el listado de devoluciones
                 
                  $where_depe = "and cast(".$db->conn->substr."(cast(a.radi_nume_sal as varchar(20)), 5, $ln) as integer) in ($lista_depcod)";
                  if ($_SESSION["entidad"] = 'correlibre' )
                     {
                       //$lista_depcod  = str_replace("1310","1400",$lista_depcod);
                       $lista_depcod  = str_replace("1400","1310,1400",$lista_depcod);
                       $lista_depcod  = str_replace("1500","1320,1500",$lista_depcod);
                       $lista_depcod  = str_replace("1600","1330,1600",$lista_depcod);
                       $lista_depcod  = str_replace("1700","1340,1700",$lista_depcod);
                     }
                       $where_depe = "and cast(".$db->conn->substr."(cast(a.radi_nume_sal as varchar(20)), 5, $ln) as integer) in ($lista_depcod)";  
                              
	}
?>
