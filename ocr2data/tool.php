<?php

/**
 * Includo central
 *
 * Este include llama a los includes basicos que el resto de modulos esperan esten siempre presente
 * y ajusta algunos valores por defecto que requieren todas las paginas y la gestion de sesiones
 * @package ocr2data-core
 */

$lang = "es";

if( 0 ){
	ini_set("session.gc_maxlifetime",    "86400");
	ini_alter("session.cookie_lifetime", "86400" );


	$expire = 60*60*23;
	ini_set("session.gc_maxlifetime", $expire);

	if (empty($_COOKIE['PHPSESSID'])) {
		session_set_cookie_params($expire);
		session_start();
	} else {
		session_start();
		setcookie("PHPSESSID", session_id(), time() + $expire);
	}
} else {
		//Si no hay sesion, la creamos.

		if (!defined("NO_SESSION")) {
			if (session_id() == "") session_start();
		}
}

$modo = (isset($_REQUEST["modo"])?$_REQUEST["modo"]:false);


if(function_exists("get_magic_quotes_gpc")){
	if (get_magic_quotes_gpc()) {
		function stripslashes_profundo($valor)    {
			$valor = is_array($valor) ?
						array_map('stripslashes_profundo', $valor) :
						stripslashes($valor);
			return $valor;
		}

		$_POST = array_map('stripslashes_profundo', $_POST);
		$_GET = array_map('stripslashes_profundo', $_GET);
		$_COOKIE = array_map('stripslashes_profundo', $_COOKIE);
		$_REQUEST = array_map('stripslashes_profundo', $_REQUEST);
	}
}


if(!function_exists("_")){
	function _($text){
		return $text;
	}
}

function iconv2($cosa,$cosa,$cadena){//fakear un iconv
	return $cadena;
}


$SEPARADOR = DIRECTORY_SEPARATOR;


include_once("config/config.php");
include_once("inc/debug.inc.php");
include_once("inc/clean.inc.php");

include_once("inc/db.inc.php");	


include_once("inc/xul.inc.php");
include_once("inc/html.inc.php");
include_once("inc/supersesion.inc.php");
include_once("inc/combos.inc.php");
include_once("inc/indexador.inc.php");

include_once("inc/tool.inc.php");

include_once("inc/auth.inc.php");

include_once("class/json.class.php");//comunicacion

include_once("class/cursor.class.php");

//include_once("class/contenido.class.php");

include_once("class/config.class.php");

//include_once("clases/pedidos.class.php");
//include_once("clases/delegacion.class.php");
//include_once("clases/cliente.class.php");
//include_once("clases/usuario.class.php");
//include_once("clases/mime.class.php");
include("class/region.class.php");

$template = array();

$script = basename($_SERVER['SCRIPT_NAME']);
$script = substr($script, 0, -4);


$template["modname"] = $script;


include("class/pagina.class.php");

$lang = "es";

$tituloSitio = "Digitalizador.";

if(!isset($_SESSION["user_nombreapellido"])){
    $_SESSION["user_nombreapellido"] = "Operario";
}
