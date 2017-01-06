<?php
	class Empresa {
		var $sgdOemCodigo;
		var $tdidCodi;
		var $nombre;
		var $representante;
		var $nit;
		var $sigla;
		var $dptoCodi;
		var $muniCodi;
		var $direccion;
		var $telefono;
		var $idCont;
		var $idPais;
		var $db;
		
		/* Constructor de la clase empresas 
		*/
		function Empresa ($db) {
			$this->db = $db;
		}
		
		function getEmpresa($id){
			$sql = "SELECT * FROM SGD_OEM_EMPRESAS WHERE SGD_OEM_CODIGO = $id";
			$rs = $this->db->query($sql);
			return $rs;
		}
	}
?>
