<?php


include_once("template.class.php");
include_once("imagen.class.php");

/**
 * Description of reconclass
 *
 * @author oscar
 */

class recon extends Cursor {

	function Usuario() {
		return $this;
	}

	function Load($id) {
		$id = CleanID($id);
		$this->setId($id);
		$this->LoadTable("recon", "recon_id", $id);
		return $this->getResult();
	}

  	function setNombre($nombre) {
		//return $this->get("region_nombre");
  	}

  	function getNombre() {
		//return $this->get("region_nombre");
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

		$sql = "INSERT INTO recon ( $listaKeys ) VALUES ( $listaValues )";

		$resultado = query($sql);

		if ($resultado){
		    $this->set("recon_id",$UltimaInsercion,FORCE);
		}



		return $resultado;
	}



	function Modificacion () {

		$data = $this->export();

		$sql = CreaUpdateSimple($data,"recon","recon_id",$this->get("recon_id"));

		$res = query($sql);
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ , "W: no actualizo recon");
			return false;
		}


		$this->ActualizarIndexado();

		return true;
	}

    function ActualizarIndexado(){
        //???
    }



	function getImagenData(){
		$recon_id_s = $this->get("recon_id");

		$sql = "SELECT * FROM imagen WHERE recon_id = '$recon_id_s' LIMIT 1";
		$datosimage= queryrow($sql);
		return $datosimage;
	}

	//$template = $recon->getTemplate();
	//$tid = $template->get("template_id");

	function getRegionesRecon(){
		$recon_id_s = $this->get("recon_id");

		$sql = "SELECT * FROM regiones_recon INNER JOIN regiones ON regiones_recon.region_id = regiones.region_id  WHERE recon_id='$recon_id_s'";
		return query($sql);
	}

	function getTemplate(){
		$template = new template();
		if ($template->Load($this->get("template_id"))){
			return $template;
		}
		return false;
	}

    function newTemplate($nombre,$imagenbase,$img=false,$origen_addr=false){
        //template_id nombre  imagenbase

        $path_parts = pathinfo($imagenbase);
        $filename = $path_parts["filename"] .".". $path_parts["extension"];

        $template = new template();
        $template->Crea();
        $template->set("nombre",$nombre);
        $template->set("imagenbase",$filename);
        $template->setOrigen($origen_addr);

        if($img){
            $img->readSize();

            $template->set("ancho",$img->dx);
            $template->set("alto",$img->dy);
        }

        $template->Alta();
        
        $tid = $template->get("template_id");
        $this->updateTemplate($tid);
    }

    function updateTemplate($tid){
        $this->set("template_id",$tid);
        $id = $this->get("recon_id");        

        $sql = "UPDATE recon SET template_id = '$tid' WHERE recon_id = '$id' ";
        query($sql);
    }



	function reconocido($rr, $mediafinal, $ciertofinal ){		
		$region_id_s = sql($rr->get("region_id"));
		$recon_id_s = sql($this->get("recon_id"));
		$valor_s = sql($ciertofinal);

		$sql = "INSERT INTO recon_ciertos (region_id,recon_id,valor) VALUES ( '$region_id_s','$recon_id_s','$valor_s' ) ";
		query($sql);
	}

    function plugins(){
        $pluginName = getParametro("ocr2data.exportplugin");

        if(!$pluginName) return;

        include("plugins/$pluginName/on_new_recon.php");       
    }

    function eliminar(){

        $id = $this->get("recon_id");
        $this->set("eliminado",1);

        $sql = "UPDATE recon SET eliminado=1 WHERE recon_id='$id'";
        query($sql);
    }


}

