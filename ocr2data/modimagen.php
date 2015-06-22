<?php


include("tool.php");
include("inc/paginabasica.inc.php");

$mensaje = false;

$page->setAttribute( 'cuerpo', 'src', 'modimagen_lista.html' );

$sql = "SELECT * FROM imagen WHERE eliminado=0";

$res = query($sql);

$rows = array();

while($row = Row($res)){
	$rows[] = $row;
}

$page->addRows( 'lista', $rows );

if($mensaje){
	$page->addVar("cuerpo","mensaje",$mensaje);
}


$page->Volcar();


exit();



?>