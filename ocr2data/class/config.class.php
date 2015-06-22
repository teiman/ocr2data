<?php

/**
 * Clase de gestion de configuracion
 *
 *
 * @package ocr2data-clases
 */


class Config {


/**
 * Se asegura que la sesion contenga los datos de parametros y los carga si no es
 * el caso
 *
 */
	function CreaSiNoExiste(){

		if ( !($_SESSION["parametros_globales"]) ){
			$sql = "SELECT * FROM system_param";
			$res = query($sql);
			$param = array();
			while($row=Row($res)){
				$param[ $row["system_param_title"]  ] = $row["system_param_value"];
			}
			$_SESSION["parametros_globales"] = $param;

			//echo "<h1>".var_export($param,true). "</h1>";
		}
	}
/**
 * Fuerza la recarga de parametros desde el dispositivo externo
 *
 *
 */
	function Reload(){
		unset( $_SESSION["parametros_globales"] );
		$this->CreaSiNoExiste();
	}

/**
 * devuelve el valor del parametro indicado
 *
 * @param string $clave
 * @return string
 */

	function get($clave){

		$this->CreaSiNoExiste();

		$param = $_SESSION["parametros_globales"];

		return $param[$clave];
	}

/**
 * Modifica el valor de un parametro de configuracion tanto en memoria como en el
 * dispositivo externo
 *
 * @param string $clave nombre del parametro a modificar
 * @param string $valor  nuevo valor del parametro
 * @param boolean $force parametro opcional (reservado)
 */
	function set($clave, $valor, $force=true){
		$this->CreaSiNoExiste();
		
		$param = $_SESSION["parametros_globales"];
		$param[$clave] = $valor;
		$_SESSION["parametros_globales"] = $param;

		$valor_s = sql($valor);
		$clave_s = sql($clave);
		
		$sql = "UPDATE system_param SET system_param_value = '$valor_s' WHERE system_param_title='$clave_s' ";
		query($sql);
	}


	function altaclave($nombreclave,$valor){
		$nombreclave_s = sql($nombreclave);
		$valor_s = sql($valor);
		$sql = "INSERT INTO system_param ( system_param_title,system_param_value) VALUES ('$nombreclave_s','$valor_s')";
		query($sql);

		$this->CreaSiNoExiste();
		$param = $_SESSION["parametros_globales"];
		$param[$clave] = $valor;
		$_SESSION["parametros_globales"] = $param;
	}


	function existe($nombreclave){
		$nombreclave_s= sql($nombreclave);
		$sql = "SELECT * FROM system_param WHERE  system_param_title='$nombreclave_s' LIMIT 1";
		$row = queryrow($sql);
		return $row?true:false;
	}

    function getTree($pluginName){
        $pluginName_s= sql($pluginName);
        $sql = "SELECT * FROM system_param WHERE system_param_title LIKE 'export_".$pluginName_s.".%'";
        //$row = queryrow($sql);
        //return $row?true:false;
    
    //    die($sql);

        $res = query($sql);
        $rows = array();

        $nn = 0;
        while($row = Row($res)){
            $row["nn"] = $nn%2;
            $rows[] = $row;
        
            $nn++;
        }
        
        return $rows;
    }
}

$config = new Config();

$debugParametros = true;

/**
 *
 * @global class $config la instancia unica de configuracion
 * @param string $clave  parametro que quiere leer
 * @return <type> 
 */
function getParametro($clave,$volatil=false){
	global $config, $debugParametros;

	if ($volatil or $debugParametros){
		//recarga el dato desde db, puesto que la version en memoria puede estar obsoleta
		$_SESSION["parametros_globales"] = false;
	}


	return $config->get($clave);
}


function setParametro($clave, $valor){
	global $config;
	return $config->set($clave,$valor);
}



?>