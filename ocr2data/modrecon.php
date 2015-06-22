<?php


include("tool.php");
include("class/template.class.php");
include("class/recon.class.php");
include("inc/paginabasica.inc.php");


$modo = $_REQUEST["modo"];

switch($modo){
    case "eliminar":
        $id = $_REQUEST["recon_id"];
        $recon = new recon();
        $recon->Load($id);
        $recon->eliminar();
    default: break;
}


$mensaje = false;

$page->setAttribute( 'cuerpo', 'src', 'modrecon_lista.html' );

$sql = "SELECT * FROM recon JOIN templates ON recon.template_id = templates.template_id WHERE recon.eliminado=0 ORDER BY recon.recon_id DESC";

$res = query($sql);

$rows = array();


$nn=0;
while($row = Row($res)){
    $row["nn"] = $nn%2;$nn++;
	$rows[] = $row;
}


$page->addRows( 'lista', $rows );

if($mensaje){
	$page->addVar("cuerpo","mensaje",$mensaje);
}


$page->Volcar();


exit();

