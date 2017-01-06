<?php

class ConnectionException extends Exception{
	protected $consulta;
	protected $tipo;
	function __construct($mensaje){
		parent::__construct($mensaje,1024);
	}
}

?>
