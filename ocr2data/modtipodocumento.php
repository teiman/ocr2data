<?php


include("tool.php");

include("class/imagen.class.php");
//include("class/json.class.php");
include("inc/paginabasica.inc.php");


$tid = CleanID($_REQUEST["tid"]);

if(!$tid)
	$tid = 4;

$ficheroOriginal = $_REQUEST["image"];

if(!$ficheroOriginal){

	$tid_s = sql($tid);
	$sql = "SELECT * FROM templates WHERE template_id='$tid_s' LIMIT 1";
	$row = queryrow($sql);

	$ficheroOriginal = $row["imagenbase"];

	$ficheroOriginal = str_replace(".tiff",".jpg",$ficheroOriginal);
	$ficheroOriginal = str_replace(".tif",".jpg",$ficheroOriginal);


	if(!$ficheroOriginal)
		$ficheroOriginal = "img5.jpg";
}



$modo = $_REQUEST["modo"];

switch($modo){
	case "areacaptura":

		$path = $rootdata . "scan/";
		$fichero = $ficheroOriginal;


		$img = new imagen($path. $fichero);

		$pos_x = $_REQUEST["pos_x"];
		$pos_y = $_REQUEST["pos_y"];
		$w = $_REQUEST["w"];
		$h = $_REQUEST["h"];

		$img->cropImage($pos_x,$pos_y,$w,$h);

		$out = "ocrme.tiff";
		$path = "tmp/";
		$img->Save("tiff",$path, $out);
		$string = $img->ocr_bloque($path, $out);
		$img->delete();

		$data = array("ocr"=>trim($string));

		//error_log("i:". var_export($data,true));

		echo json_encode($data);

		exit();

		break;

	case "guardarareas":

		$tid = CleanID($_REQUEST["tid"]);
		
		if(!$tid)
			$tid = 4;


		query("DELETE FROM `regiones` WHERE template_id=$tid ");//debug


		$areas = $_REQUEST["areas"];

		$datos = split("#",$areas);

		$regiones = array();
		foreach ($datos as $dato){
			if (!$dato || $dato=="undefined"){
				continue;
			}

			$regiones[] = json_decode($dato);
		}

		foreach ($regiones as $datosregion){
			$region = new region();

			$region->set("region_nombre",$datosregion->registro );
			$region->set("region_x",$datosregion->pos_x);
			$region->set("region_y",$datosregion->pos_y);
			$region->set("region_w",$datosregion->w);
			$region->set("region_h",$datosregion->h);
			$region->set("template_id",$tid);
			$region->Alta();
		}

		echo json_encode(array("ok"=>"ok","php"=>var_export($regiones,true) ));
		exit();

		break;

		break;

}



$volver = $_REQUEST["volver"];

$page->setAttribute( 'cuerpo', 'src', 'formdesigner2.html' );

$page->addVar("cuerpo","ficherotrabajo",$ficheroOriginal);
$page->addVar("cuerpo","webpath",$webdata);
$page->addVar("cuerpo","tid",$tid);
$page->addVar("cuerpo","retorno",$volver);

$page->Volcar();


exit();


?>