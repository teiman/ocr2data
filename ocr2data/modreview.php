<?php


include("tool.php");
include_once("inc/paginabasica.inc.php");
include_once("class/recon.class.php");
include_once("inc/xml.inc.php");

$page->setAttribute( 'cuerpo', 'src', 'modreview.htm' );

$recon = new recon();

switch($modo){    
    case "carga":
        $id_recon = CleanID($_REQUEST["id_recon"]);

        $esCarga = $recon->Load($id_recon);
        if(!$esCarga) {
            header("Location: error.php?causa=SIGUE_NO_CARGA");
            return;
        }
        break;

    default:
        //go back
        break;
}


if($esCarga){

    $sql = "SELECT * FROM recon_ciertos LEFT JOIN regiones ON recon_ciertos.region_id = regiones.region_id WHERE recon_id=$id_recon";
    
    $res = query($sql);
    
    $rows = array();
    
    while($row = Row($res)){
        $rows[] = $row;
    }

}


$headers = array("version"=>"1.0",
    "app"=>"ocr2data",
    "template_id"=>$recon->get("template_id"),
    "estado"=>$recon->get("estado")
);



$regiones_txt = array2xml($rows,"regiones","region");
$header_txt = array2xml($headers,"cabecera","1");

$xml_txt = toXML( "recon", $header_txt . $regiones_txt  );





$page->addRows( 'lista', $rows );

if($mensaje){
    $page->addVar("cuerpo","mensaje",$mensaje);
}



$page->Volcar();


exit();


