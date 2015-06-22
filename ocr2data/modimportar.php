<?php



include("tool.php");
include_once("inc/paginabasica.inc.php");
include_once("class/recon.class.php");
include_once("inc/xml.inc.php");

$page->setAttribute( 'cuerpo', 'src', 'modimportar.htm' );


switch($modo){    
    case "carga":
        //No se usa, porque modimportar.php invoca importar.php directamente, y este es el que realiza la importancion.
        // 
        break;

    default:
        //go back
        break;
}


$page->Volcar();


exit();


