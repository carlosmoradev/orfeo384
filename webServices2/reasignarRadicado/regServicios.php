<?

//  reasignar radicado

$server->register( 'reasignarRadicado',   //nombre del servicio 
    array('numeroRadicado' => 'xsd:string','usuarioOrigen' => 'xsd:string', 'usuarioDestino' => 'xsd:string', 'comentario' => 'xsd:string'),//entradas
    array('return' => 'xsd:string'), // salidas
    $ns,
$ns.'#reasignarRadicado',
	'rpc',
	'encoded',
	'reasignar radicado'
);

?>
