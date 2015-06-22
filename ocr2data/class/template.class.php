<?php


function cuantosQue($condicion){
    $row = queryrow("SELECT count(template_id) as cuantos FROM templates  WHERE ". $condicion);


    error_log("sql: $condicion=(". $row["cuantos"]. ")");

    return $row["cuantos"];
}

function cualQue($condicion){
    $row = queryrow("SELECT template_id as cual FROM templates WHERE ". $condicion);

    
    return $row["cual"];
}

function masviejoQue($condicion){
    $row = queryrow("SELECT template_id as cual FROM templates WHERE ". $condicion . " ORDER BY template_id ASC LIMIT 1");
    return $row["cual"];     
}

function xyQue($parte,$usaralto,$usarancho,$dx,$dy){

    if( !$usaralto and !$usarancho)
        return " template_id>0 ";

    $out = "( ";

    if (!$usaralto)
        $out .=  " ancho='$dx' ";
    else 
    if (!$usaralto) 
        $out .=  " alto='$dy' ";    
    else 
        $out .= " ancho='$dx' $parte alto='$dy' ";    

    return $out . " )" ;
}


function tdebug($texto){

    error_log("infotdebug:" .$texto );
}

class template extends Cursor {


	function Load($id) {
		$id = CleanID($id);
		$this->setId($id);
		$this->LoadTable("templates", "template_id", $id);
		return $this->getResult();
	}

  	function setNombre($nombre) {

  	}

  	function getNombre() {
		return $this->get("nombre");
  	}

  	function Crea(){
		$this->setNombre(_("Nueva template"));
        $this->set("status","pendiente");
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

		$sql = "INSERT INTO templates ( $listaKeys ) VALUES ( $listaValues )";

		$resultado = query($sql);

		if ($resultado){
			//die("se inserto");
		    $this->set("template_id",$UltimaInsercion,FORCE);
		}

		return $resultado;
	}

	function Modificacion () {

		$data = $this->export();

		$sql = CreaUpdateSimple($data,"templates","template_id",$this->get("template_id"));

		$res = query($sql);
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ , "W: no actualizo t");
			return false;
		}

		return true;
	}

	function Eliminar(){
		$this->set("eliminado",1);
		$this->Modificacion();
	}
    
    function setOrigen($origen){
        $this->set("origen_adr",trim($origen));
    }


    function findTemplateAdecuada($img,$origen_addr){

        $data = array();

        $img->readSize();

        $dx = intval($img->dx);
        $dy = intval($img->dy);

        $usaralto = $this->get("usaralto")|| 1;
        $usarancho = $this->get("usarancho") || 1;
        $usartamagnos = $usaralto && $usarancho;

        tdebug(__LINE__ . "uH:$usaralto,uW:$usarancho");

        $origen_addr = sql($origen_addr);
        
        //Buscamos los que vengan de esta direccion
        $que = " origen_adr = '$origen_addr' ";
        $s_0 = cuantosQue($que);

        if( $s_0 == 1 ){
            tdebug(__LINE__);
            //Una coincidencia exacta. Tiene que ser este
            $data["id"] = cualQue($que);
            return $data;
        } else
        if( $s_0 >1 ){
            //De esta direccion, cual puede ser el mas apropiado?            
            $que = xyQue("and",$usaralto,$usarancho,$dx,$dy);

            $s_1 = cuantosQue($que);
            if ($s_1== 1){
                tdebug(__LINE__);
                //Este es perfecto, correcto en direccion y tamaño
                $data["id"] = cualQue($que);
                return $data;
            } else 
            if($s_1>1){
                tdebug(__LINE__);
                //Si hay varios donde elegir, cogemos el mas antiguo 
                $data["id"] = masviejoQue($que);
                return $data;
            } else {                
                $que = xyQue("or",$usaralto,$usarancho,$dx,$dy);

                $s_4 = cuantosQue($que);
            
                if( $s_4 == 1){
                    tdebug(__LINE__);
                    //Este es adecuado en al menos un sentido (prr..)
                    $data["id"] = cualQue($que);
                    return $data;
                }else
                if( $s_4 > 1){
                    tdebug(__LINE__);
                    //Si hay varios donde elegir, cogemos el mas antiguo 
                    $data["id"] = masviejoQue($que);
                    return $data;
                }             
            }

            tdebug(__LINE__);
            //En ausencia de cualquier otro criterio, cogemos el mas viejo
            $que = " (origen_adr = '$origen_addr') ";
            $data["id"] = masviejoQue($que);
            return $data;   
        } 

        //Cual puede ser el mas apropiado?        
        $que = xyQue("and",$usaralto,$usarancho,$dx,$dy);

        $s_1 = cuantosQue($que);
        if ($s_1== 1){
            tdebug(__LINE__);
            //Este es perfecto, correcto en direccion y tamaño
            $data["id"] = cualQue($que);
            return $data;
        } else 
        if($s_1>1){
            tdebug($que . __LINE__);
            //Si hay varios donde elegir, cogemos el mas antiguo 
            $data["id"] = masviejoQue($que);
            return $data;
        } else {
            $que = xyQue("or",$usaralto,$usarancho,$dx,$dy);

            $s_4 = cuantosQue($que);
        
            if( $s_4 == 1){
                tdebug(__LINE__);
                //Este es adecuado en al menos un sentido (prr..)
                $data["id"] = cualQue($que);
                return $data;
            }else
            if( $s_4 > 1){
                tdebug(__LINE__);
                //Si hay varios donde elegir, cogemos el mas antiguo 
                $data["id"] = masviejoQue($que);
                return $data;
            }             
        }    
        
        tdebug(__LINE__);
        //No encontrado
        $data["id"] = false;
        return $data;
    }   

}

