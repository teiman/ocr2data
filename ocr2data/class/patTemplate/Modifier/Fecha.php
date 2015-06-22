<?php

class patTemplate_Modifier_Fecha extends patTemplate_Modifier
{
	/*
    var $defaults = array(
                        'decimals'  => 2,
                        'point'     => '.',
                        'separator' => ','
                    );
     *
     */
	function modify($fecha, $params = array()) {

		list($fecha, $hora) = split(" ",$fecha);
		list($agno,$mes,$dia) = split("-",$fecha);
		
		$salida = $dia . "-" . $mes . "-" . $agno ;
		$salida = str_replace("-","/",$salida);

	    return $salida;
	}
}


?>