<?
class buscarFila{
  var $fila;
  var $ini;
  var $fin;
  function radicado(){
		$comando = "catdoc /". $this->fila;
		//echo "<br>$comando<br>";
		exec($comando, $return);
		//print_r($return);
		foreach($return as $linea)
		{
			preg_match("/(?P<foo>".$this->ini.")(.*)(?P<bar>".$this->fin.")/",
						$linea,
						$return);
			//echo "$linea<br>";
			//echo "<hr>".$return;
			if($return && strlen($return[0])==14) $numBuscado = $return[0];
		}
		return $numBuscado;
  }
function string(){
		$comando = "catdoc /". $this->fila;
		//echo "<br>$comando<br>";
		exec($comando, $return);
		//print_r($return);
		foreach($return as $linea)
		{
			preg_match("/(?P<foo>".$this->ini.")(.*)(?P<bar>".$this->fin.")/",
						$linea,
						$return);
			//echo "$linea<br>";
			//echo "<hr>".$return;
			if($return)$numBuscado = $return[0];
		}
		return $numBuscado;
  }

	function buscarEncabezado(){
	$comando = "catdoc /" .$this->fila;
	exec($comando, $return);
	$i=0;
	$encabezado ="";
  $entro = "No";
	foreach($return as $linea)
	{
		preg_match("/(?P<foo>Se)(.*)(?P<bar>)/",
					$linea,
					$return);
		//echo "$linea<br>";
	
		if($return ||  ($i>=1 && $i<=6))
		{ 
			if(trim($linea)!="") 
			{ 
				$i++;
				$encabezado[] = $linea;
				$entro = "Si";
			}
		}
	}
  if($entro != "Si"){
	foreach($return as $linea)
	{
		preg_match("/(?P<foo>Senor)(.*)(?P<bar>)/",
					$linea,
					$return);
		//echo "$linea<br>";
		if($return ||  ($i>=1 && $i<=6))
		{ 
			$i++;
			$encabezado[] = $linea;
			$entro = "Si";
		}
	}
  }
 if($entro != "Si"){
	foreach($return as $linea)
	{
		preg_match("/(?P<foo>Se)(.*)(?P<bar>)/",
					$linea,
					$return);
		//echo "$linea<br>";
		if($return ||  ($i>=1 && $i<=6))
		{ 
			$i++;
			$encabezado[] = $linea;
		}
	}
	}
	return $encabezado;
}

}
?>