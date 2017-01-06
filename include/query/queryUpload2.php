<?

	switch($db->driver)
	{
	case 'mssql':
	
	break;
	case 'oracle':
	case 'oci8':
		$tamano = round($tamano);
		break;
	case 'oci805':
	case 'ocipo':
	 	$tamano = " to_number($tamano) ";
	break;

	default:
                $tamano =  $tamano;

	}

?>
