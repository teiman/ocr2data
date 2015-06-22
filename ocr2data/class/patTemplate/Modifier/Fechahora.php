<?php

class patTemplate_Modifier_Fechahora extends patTemplate_Modifier
{
	/*
    var $defaults = array(
                        'decimals'  => 2,
                        'point'     => '.',
                        'separator' => ','
                    );
     *
     */
	function modify($fecha, $params = array())
	{
	    //$params = array_merge($this->defaults, $params);

		list($fecha, $hora) = split(" ",$fecha);
		list($h,$m,$s) = split(":", $hora);
		list($agno,$mes,$dia) = split("-",$fecha);

	//	$salida = sprintf($params["format"], $dia , $mes , $agno, $h,$m, $s );
		//$salida = sprintf($params["format"], $dia , $mes , $agno );
		 $salida = $dia."-".$mes."-".$agno." ".$h.":". $m ;

		//$templates	= $this->parseString( $salida );
		//echo "salida:$salida,  ", $hora , "| $h, $m, $s, ($salida)". $params["format"]  ." \n";

	    return $salida;
	}
}

?>