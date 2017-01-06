<?php 
if(!isset($ruta_raiz))
	$ruta_raiz="../../../";
elseif((! strpos($ruta_raiz,"/") && $ruta_raiz) || strstr($ruta_raiz,-1)==".")
 	$ruta_raiz.="/";
 	
include_once($ruta_raiz."include/db/ConnectionHandler.php");

final class Connection{
	private static $db;
	private static $flag;
	private function Connection(){
		global $ruta_raiz;
		self::$db=new ConnectionHandler($ruta_raiz);
		self::$flag=true;
	}
	public static function getCurrentInstance(){
		if(!self::$flag)
			new Connection();
		return self::$db;
	}
	
}