<?php

include("tool.php");
include("class/template.class.php");
include("inc/paginabasica.inc.php");


$modo = $_REQUEST["modo"];

if (1){
    //Modos de funcionamiento

    $listando = true;
    $edit = false;
}

$mensaje = false;

switch($modo){
	case "alta":
		$nombre = trim($_REQUEST["nombre"]);

		if( strlen($nombre)>0 ){
			$template = new template();
            $template->Crea();
			$template->set("nombre",$nombre);
			$template->Alta();

			$mensaje = "alta realizada";
		} else {
			$mensaje = "El nombre debe ser de al menos 3 caracteres";
		}
		break;

    case "guardar":
        $template_id = CleanID($_REQUEST["template_id"]);
        $template = new template();
        if ($template->Load($template_id)){

            $template->set("nombre",trim($_REQUEST["nombre"]));
            $template->set("alto",trim($_REQUEST["alto"]));
            $template->set("ancho",trim($_REQUEST["ancho"]));
            $template->setOrigen($_REQUEST["origen_adr"]);


            $template->set("usaralto",($_REQUEST["usaralto"]=="on"?1:0));
            $template->set("usarancho",($_REQUEST["usarancho"]=="on"?1:0));


            $template->Modificacion();
        }

        $listando = false;
        $edit = true;
        break;

    case "edit":
        $template_id = CleanID($_REQUEST["id"]);
        $template = new template();
        if ($template->Load($template_id)){
            //weeeeeeeeeeee
        }

        $listando = false;
        $edit = true;

        break;
	case "eliminar":
		$template_id = CleanID($_REQUEST["template_id"]);
		$template = new template();
		if ($template->Load($template_id)){
			$template->Eliminar();
		}
		break;
}

if ($listando){
    $page->setAttribute( 'cuerpo', 'src', 'modtemplate_lista.html' );
    
    $sql = 'SELECT * FROM templates WHERE eliminado=0 ORDER BY template_id DESC';
    
    $res = query($sql);
    
    $rows = array();
    

    $nn = 0;
    while($row = Row($res)){
        $row["nn"] = $nn%2;
	    $rows[] = $row;    

        $nn++;    
    }
    
    $page->addRows( 'lista', $rows );
    
    if($mensaje){
	    $page->addVar("cuerpo","mensaje",$mensaje);
    }
}

if($edit){
    $page->setAttribute( 'cuerpo', 'src', 'modtemplate_edit.html' );   
    
    $data = $template->export();

    $page->addArrayFromCursor( 'cuerpo',$template, 
        array('nombre','ancho','alto','origen_adr','template_id','usaralto','usarancho') );
}


$page->Volcar();


exit();


?>