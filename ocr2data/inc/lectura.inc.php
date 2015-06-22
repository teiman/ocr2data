<?php



//define("UMBRAL_ROJO",0.3);
define("UMBRAL_ROJO",0.15);

define("UMBRAL_NARANJA",0.5);
$ANCHO_DEFECTO = 792;


function cdie($linea,$actual){
	return;

	if ($linea <= $actual)
		die("--cerrado proceso en linea: ($actual) --");
	echo "--- paso ---\n";
}




















?>