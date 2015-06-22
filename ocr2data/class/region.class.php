<?php

/**
 * Description of regionclass
 *
 * @author oscar
 */

class region extends Cursor {

	function Usuario() {
		return $this;
	}

	function Load($id) {
		$id = CleanID($id);
		$this->setId($id);
		$this->LoadTable("regiones", "region_id", $id);
		return $this->getResult();
	}

  	function setNombre($nombre) {
		return $this->get("region_nombre");
  	}

  	function getNombre() {
		return $this->get("region_nombre");
  	}

  	function Crea(){
		$this->setNombre(_("Nueva region"));
	}


	function Alta(){
		global $UltimaInsercion;

		$data = $this->export();

		$coma = false;
		$listaKeys = "";
		$listaValues = "";

		foreach ($data as $key=>$value){
			if ($coma) {
				$listaKeys .= ", ";
				$listaValues .= ", ";
			}

			$value = sql($value);

			$listaKeys .= " `$key`";
			$listaValues .= " '$value'";
			$coma = true;
		}

		$sql = "INSERT INTO regiones ( $listaKeys ) VALUES ( $listaValues )";

		$resultado = query($sql);

		if ($resultado){
		    $this->set("region_id",$UltimaInsercion,FORCE);
		}

		

		return $resultado;
	}



	function Modificacion () {

		$data = $this->export();

		$sql = CreaUpdateSimple($data,"regiones","region_id",$this->get("region_id"));

		$res = query($sql);
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ , "W: no actualizo contenido");
			return false;
		}


		$this->ActualizarIndexado();

		return true;
	}




}





?>