<?php

/**
 * Gestion de parametros de configuracion
 *
 * modificación de parametros
 * @package ocr2data-core
 */


include("tool.php");
include("class/template.class.php");
include("inc/paginabasica.inc.php");


$page->setAttribute( 'cuerpo', 'src', 'modconfig.htm' );

$mostrarListado = false;
$mostrarEdicion = false;

/*
$page->addVar('headers', 'titulopagina', $trans->_('Gestion de parametros')  );
$page->addVar('page', 'labelalta',  $trans->_("Alta de parametro") );
$page->addVar('page', 'labellistar',  $trans->_("Listar") );
*/


if (!$_SESSION[ $template["modname"] . "_list_size"])
	$_SESSION[ $template["modname"] . "_list_size"] = 100;

$modo = $_REQUEST["modo"];

$nombreUsuarioMostrar = "";

switch($modo){

	case "change-list-size":
		$listsize = $_REQUEST["list-size"];

		if ($listsize)
			$_SESSION[ $template["modname"] . "_list_size"] = $listsize;

		$mostrarListado = true;

		break;

	case "filtrar-elemento":

		$filtranombre_s = sql(trim($_REQUEST["filtrar-elemento"]));

		$extracondicion  = " AND system_param_title LIKE '%$filtranombre_s%' ";

		/*<input type="hidden" name="modo" value="filtrar-elemento" />
	<input type="hidden" name="filtrar-elemento" value="" id="filtra-list-value" />*/
		$mostrarListado = true;
		break;

	case "guardarcambios":
	
		$config->set(trim($_POST["system_param_title"]),$_POST["system_param_value"] );
		
		$mostrarListado = true;
		break;

	case "guardaralta":
		$config->altaclave(trim($_POST["system_param_title"]),$_POST["system_param_value"] );

		$mostrarListado = true;
		break;

	case "modificar":
		$mostrarEdicion = true;


		$id_s = sql($_REQUEST["id"]);
		$row = queryrow( "SELECT * FROM system_param WHERE id_system_param='$id_s'  ");

		$metodo = "Modificar";
		$newmodo = "guardarcambios";
		break;

	case "alta":
		$mostrarEdicion = true;

		$metodo = "Alta";
		$newmodo = "guardaralta";
		break;
    case "eliminar":

        $id =  CleanID($_REQUEST["id"]);

        $sql = "DELETE FROM system_param WHERE id_system_param='$id'";
        query($sql);


		$mostrarListado = true;
        break;
	default:
		$mostrarListado = true;
		break;

}


if ($mostrarEdicion){
	$page->configMenu($newmodo);

	//$page->setAttribute( 'bloque', 'src', 'edicion_parametro.htm' );

	$page->addVar( 'cuerpo', 'modname',		$template["modname"] );
	$page->addVar( 'cuerpo', 'modoediciontxt',	$metodo );
	$page->addVar( 'cuerpo', 'modoedicion',	$newmodo );


	$page->addVar( 'cuerpo', 'id_system_param',	$row["id_system_param"] );
	$page->addVar( 'cuerpo', 'system_param_title',	$row["system_param_title"] );
	$page->addVar( 'cuerpo', 'system_param_value',	$row["system_param_value"] );

	//$page->addVar( 'edicion', 'activahtml',	$config->get("enabled")?"checked='checked'":"");

	//$page->addArrayFromCursor( 'edicion',$config, array("id_system_param","system_param_title",'system_param_value')  );

    $page->addVar( 'cuerpo', 'queno', 'ocultar' );//ocultar miscelanea cuando se modifica un campo

}

if ($mostrarListado){
	$page->configMenu("bloque");

    $page->addVar( 'cuerpo', 'quesi', 'ocultar' );//ocultar miscelanea cuando se lista campos

	//$page->setAttribute( 'listado', 'src', 'listado_parametros.htm' );

	$maxfilas = $_SESSION[ $template["modname"] . "_list_size"];
	$min = intval($_REQUEST["min"]);

	//die("n:".$maxfilas);
	
	$list = array();

	$sql = "SELECT * FROM system_param WHERE id_system_param > 0 $extracondicion ORDER BY `system_param_title` ASC LIMIT $min,$maxfilas";
	$res = query($sql);

	$numFilas = 0;
    $nn = 0;
	while($row = Row($res) ){
		$estiloApropiado = ($numFilas %2)?"filaImpar":"filaPar";
		$numFilas++;

		$fila = array("modname"=>$template["modname"], "id"=>$row["id_system_param"]
				,"name"=>$row["system_param_title"]
				,"value"=>$row["system_param_value"]
                ,"nn"=>($nn%2)

		);

        $nn++;

		$list[] = $fila;
	}

	$page->addRows('lista', $list );
	$page->configNavegador( $min, $maxfilas,$numFilas);
}





$page->Volcar();



?>