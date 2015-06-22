<?php

include("tool.php");
include_once("inc/paginabasica.inc.php");
include_once("class/recon.class.php");
include_once("inc/xml.inc.php");

$page->setAttribute( 'cuerpo', 'src', 'modexportar.htm' );


switch($modo){    
    case "carga":
        //No se usa        
        break;

    default:
        //go back
        break;
}


//ocr2data.exportplugin

$pluginName = getParametro("ocr2data.exportplugin");

//$pluginName = str_replace(".php","",$pluginName);




$rows = $config->getTree($pluginName);

$page->addRows('lista', $rows );


$page->addVar( 'cuerpo', 'id', $pluginName );



$page->Volcar();


exit();


