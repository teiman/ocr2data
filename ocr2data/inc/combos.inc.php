<?php

/**
 * Ayuda para creacion de listas desplegables sobre elementos del sistema
 *
 * @package ecomm-aux
 */


//para su uso con combos de etiquetas
define("ETIQUETAS_BASICAS",1);
define("ETIQUETAS_USUARIO",2);
define("ETIQUETAS_ESTADOS",3);



function getComboCentros($idquien=false){
	$sql = "SELECT * FROM interv_centros  ORDER BY centro ASC ";

	$res = query($sql);

	$out = "";
	while ($row= Row($res)){
		$name = iso($row["centro"]);
		$localidad = iso($row["localidad"]);


		$key = $row["id_interv_centro"];

		if ($key == $idquien){
			$selected = " selected='selected' ";
		} else {
			$selected = "";
		}

		$out .= "<option value='$key' $selected>" . html($name) . " (". html($localidad). ")</option>\n";
	}

	return $out;
}


/*<!--
   	id_interv_centro  	int(10)  	 	UNSIGNED  	No  	 	auto_increment  	  Navegar los valores distintivos   	  Cambiar   	  Eliminar   	  Primaria   	  Único   	  Índice   	 Texto completo
	provincia 	tinytext 	latin1_swedish_ci 		No 			Navegar los valores distintivos 	Cambiar 	Eliminar 	Primaria 	Único 	Índice 	Texto completo
	localidad 	tinytext 	latin1_swedish_ci 		No 			Navegar los valores distintivos 	Cambiar 	Eliminar 	Primaria 	Único 	Índice 	Texto completo
	centro*/



function getComboMediadores($idquien=false){
	$sql = "SELECT * FROM interv_usuarios WHERE eliminado=0 ORDER BY nombre_apellidos ASC ";

	$res = query($sql);

	$out = "";
	while ($row= Row($res)){
		$name = $row["nombre_apellidos"];

		$key = $row["id_interv_usuario"];

		if ($key == $idquien){
			$selected = "selected='selected'";
		} else {
			$selected = "";
		}

		$out .= "<option value='$key' $selected>" . html($name) . "</option>\n";
	}

	return $out;
}


function getComboAsociaciones($idquien=false){
	$sql = "SELECT asociacion FROM interv_usuarios WHERE eliminado=0 GROUP BY asociacion ORDER BY asociacion ";

	$res = query($sql);

	$out = "";
	while ($row= Row($res)){
		$name = $row["asociacion"];

		$key = html($row["asociacion"]);

		if ($name == $idquien){
			$selected = "selected='selected'";
		} else {
			$selected = "";
		}

		$out .= "<option value='$key' $selected>" . html($name) . "</option>\n";
	}

	return $out;
}



/*
  	id_interv_usuario  	int(10)  	 	UNSIGNED  	No  	 	auto_increment  	  Navegar los valores distintivos   	  Cambiar   	  Eliminar   	  Primaria   	  Único   	  Índice   	 Texto completo
	user 	tinytext 	latin1_swedish_ci 		No 			Navegar los valores distintivos 	Cambiar 	Eliminar 	Primaria 	Único 	Índice 	Texto completo
	pass 	tinytext 	latin1_swedish_ci 		No 			Navegar los valores distintivos 	Cambiar 	Eliminar 	Primaria 	Único 	Índice 	Texto completo
	perfil 	enum('user', 'admin') 	latin1_swedish_ci 		No 			Navegar los valores distintivos 	Cambiar 	Eliminar 	Primaria 	Único 	Índice 	Texto completo
	nombre_apellidos 	tinytext 	latin1_swedish_ci 		No 			Navegar los valores distintivos 	Cambiar 	Eliminar 	Primaria 	Único 	Índice 	Texto completo
	asociacion 	tinytext 	latin1_swedish_ci 		No 			Navegar los valores distintivos 	Cambiar 	Eliminar 	Primaria 	Único 	Índice 	Texto completo
	eliminado
*/


function genComboTipoEtiqueta($idquien){

	$sql = "SELECT * FROM `label_types` ORDER BY `label_type` ASC";

	$res = query($sql);

	$out = "";
	while ($row= Row($res)){
		$name = $row["label_type"];

		$key = $row["id_label_type"];

		if ($key == $idquien){
			$selected = "selected='selected'";
		} else {
			$selected = "";
		}

		$out .= "<option value='$key' $selected>" . html($name) . "</option>\n";
	}

	return $out;
}




function genComboProfiles($idquien, $especifica=false){

	if ($especifica) {
		$extra = " isgroupprofile='".$especifica["id"]."' AND ";
	}

	$sql = "SELECT * FROM `profiles` WHERE $extra deleted=0 ORDER BY `name` ASC ";

	$res = query($sql);

	$out = "";
	while ($row= Row($res)){
		$name = $row["name"];

		$key = $row["id_profile"];

		if ($key == $idquien){
			$selected = "selected='selected'";
		} else {
			$selected = "";
		}

		$out .= "<option value='$key' $selected>" . html($name) . "</option>\n";
	}

	return $out;
}


function genCombosStatus($idquien){

	$sql = "SELECT * FROM `status` ORDER BY `status` ASC";

	$res = query($sql);

	$out = "";
	while ($row= Row($res)){
		$name = $row["status"];

		$key = $row["id_status"];

		if ($key == $idquien){
			$selected = "selected='selected'";
		} else {
			$selected = "";
		}

		$out .= "<option value='$key' $selected>" . html($name) . "</option>\n";
	}

	return $out;
}



function genComboTarea($idquien){

	$sql = "SELECT * FROM `tasks` ORDER BY `task` ASC";

	$res = query($sql);

	$out = "";
	while ($row= Row($res)){
		$name = $row["task"];

		$key = $row["id_task"];

		if ($key == $idquien){
			$selected = "selected='selected'";
		} else {
			$selected = "";
		}

		$out .= "<option value='$key' $selected>" . html($name) . "</option>\n";
	}

	return $out;
}



function genComboMedios($idquien){

	$sql = "SELECT * FROM `medias` ORDER BY `media` ASC";

	$res = query($sql);

	$out = "";
	while ($row= Row($res)){
		$name = $row["media"];

		$key = $row["id_media"];

		if ($key == $idquien){
			$selected = "selected='selected'";
		} else {
			$selected = "";
		}

		$out .= "<option value='$key' $selected>" . html($name) . "</option>\n";
	}

	return $out;
}



function genComboGrupos($idquien){

	$sql = "SELECT * FROM `groups` ORDER BY `group` ASC";

	$res = query($sql);

	$out = "";
	while ($row= Row($res)){
		$name = $row["group"];

		$key = $row["id_group"];

		if ($key == $idquien){
			$selected = "selected='selected'";
		} else {
			$selected = "";
		}

		$out .= "<option value='$key' $selected>" . html($name) . "</option>\n";
	}

	return $out;
}


function getComboStatus($id_label_type=3){

	$sql = "SELECT * FROM `labels` WHERE id_label_type='$id_label_type' ORDER BY `label` ASC";

	$res = query($sql);

	$out = "";
	while ($row= Row($res)){
		$name = $row["label"];

		$key = $row["id_label"];

		if ($key == $idquien){
			$selected = "selected='selected'";
		} else {
			$selected = "";
		}

		$out .= "<option value='$key' $selected>" . html($name) . "</option>\n";
	}

	return $out;
}





function genComboCanales($idquien){

	$sql = "SELECT * FROM `channels` ORDER BY `channel` ASC";

	$res = query($sql);

	$out = "";
	while ($row= Row($res)){
		$name = $row["channel"];

		$key = $row["id_channel"];

		if ($key == $idquien){
			$selected = "selected='selected'";
		} else {
			$selected = "";
		}

		$out .= "<option value='$key' $selected>" . html($name) . "</option>\n";
	}

	return $out;
}


function genComboCOMDIR($com_dir ){

	$dirs = array("0"=>"Enviados y recibidos","1"=>"Recibidos","2"=>"Enviados");

	$out .= "";

	foreach($dirs as $key=>$value){
		$extra = ($com_dir==$key)?"selected='selected'":"";

		$out .= "<option $extra value='" . $key . "'>" . html($value) . "</option>";
	}

	return $out;
}


function genSelectorNubeEtiquetas($id_label_actual,$id_usuario_actual, $namelabel ){

	$sql = "SELECT label as dato FROM labels WHERE id_label='$id_label_actual'";
	$row = queryrow($sql);
	$labelactual = $row["dato"];

	$sql = "SELECT * FROM labels WHERE id_user='$id_usuario_actual' ORDER BY id_label_type ASC, id_channel ASC, label ASC ";
	$res = query($sql);
	$row = Row($res);

	$forceStart = true;
	$propiosAgotados = false;

	$out .= "<div style='color: #ddd'>";

	$out .= "<input type='text' id='label_seleccionada_texto' value='".html($labelactual)."'>";
	$out .= "<input type='hidden' name='$namelabel' value='".$id_label_actual."' id='id_label_seleccionada'><br/>";

	$out .= "<a href='#'  onclick='select(0,\"\")'>" . html("Borrar etiqueta") . "</a> | ";

	while($row or $forceStart){
		$forceStart = false;

		$decora = "";

		if ($row){

			$decora .= ($row["id_user"]==$id_usuario_actual)?"font-weight: bold;":"";

			$out .= "<a href='#' style='".$decora."' onclick='select(".$row['id_label'].",\"".addslashes($row["label"]) ."\")'>" . html($row["label"]) . "</a> | ";
		}
		
		//siguiente label
		$row = Row($res);
		if ( !$row and !$propiosAgotados){
			//se han agotado los labels propios, intentar los externos
			$sql =  "SELECT * FROM labels WHERE id_user!='$id_usuario_actual' ORDER BY id_label_type ASC, id_channel ASC, label ASC ";
			$res = query($sql);
			$row = Row($res);
			$propiosAgotados = true;
		}


	}

	$out .= "</div>";


	$out .= "<script>";

	$out .=  "  function select(id_seleccion,labelname){
	document.getElementById('id_label_seleccionada').setAttribute('value',id_seleccion);
	document.getElementById('id_label_seleccionada').value = id_seleccion;
	document.getElementById('label_seleccionada_texto').setAttribute('value',labelname);
 }; ";

	$out .= "</script>";


	return $out;

}





function genListEtiquetasComm($id_comm){

	$out = "";

	$sql = "SELECT * FROM labels
		INNER JOIN label_types ON labels.id_label_type = label_types.id_label_type
        INNER JOIN label_coms ON labels.id_label = label_coms.id_label
		WHERE labels.id_label>0 AND label_coms.id_comm = $id_comm
		ORDER BY labels.id_label_type ASC, `label`  ASC";



	$res = query($sql);

	$forceStart = true;
	$propiosAgotados = false;

	$coma = "";

	while($row  =Row($res) ){
		//id_label 	id_label_type 	id_channel 	id_user 	label 	id_label_type 	label_type

		$out .= "$coma " . $row["label"];
		$coma = ",";
	}

	return $out;
}


function genXulComboCentros($selected=false,$xul="listitem"){

	$sql = "SELECT *  FROM interv_centros  ORDER BY  centro ASC";

	$res = query($sql);

	$out = "";

	if (!$res)

		return "";

	while($row=Row($res)){

		$key = $row["id_interv_centro"];

		$value = CleanXulLabel(iso($row["centro"]));

		if ($key!=$selected)

			$out .= "<$xul value='$key' label='$value'/>\n";

		else

			$out .= "<$xul selected value='$key' label='$value'/>\n";

	}

	return $out;

}


function genXulComboLocalidades($selected=false,$xul="listitem"){

	$sql = "SELECT * FROM interv_centros GROUP BY localidad ORDER BY localidad ASC";

	$res = query($sql);

	$out = "";
	if (!$res)
		return "";

	while($row=Row($res)){
		$key = CleanXulLabel(iso($row["localidad"]));

		$value = CleanXulLabel(iso($row["localidad"]));

		if ($key!=$selected)
			$out .= "<$xul value='$key' label='$value'/>\n";
		else
			$out .= "<$xul selected value='$key' label='$value'/>\n";
	}

	return $out;
}


function genXulComboUsuarios($selected=false,$xul="listitem"){

	$sql = "SELECT * FROM interv_usuarios WHERE eliminado=0 ORDER BY nombre_apellidos ASC";

	$res = query($sql);

	$out = "";
	if (!$res)
		return "";

	while($row=Row($res)){
		$key = CleanID($row["id_interv_usuario"]);

		$value = iconv("ISO-8859-1","UTF-8",$row["nombre_apellidos"]);
		$value = CleanXulLabel($value);


		if ($key!=$selected)
			$out .= "<$xul value='$key' label='$value'/>\n";
		else
			$out .= "<$xul selected value='$key' label='$value'/>\n";
	}

	return $out;
}


/*
function genComboCentros($idquien){

	$id = $_SESSION["id_proyecto"];

	$sql = "SELECT * FROM centros WHERE eliminado=0 AND id_proyecto='$id' ORDER BY nombre ASC";
		
	$res = query($sql);
	
	$out = "";
	while ($row= Row($res)){
		$name = $row["nombre"];
		
		$name = iconv("ISO-8859-1","UTF8",$name);
		
		
		$key = $row["id"];
		
		if ($key == $idquien){
			$selected = "selected='selected'";	
		} else {
			$selected = "";
		}			
		
		$out .= "<option value='$key' $selected>" . CleanRealMysql($name) . "</option>\n";				
	}
	
	return $out;	
}

function getCentroFromId($idquien){
	$idquien = intval($idquien);	
	$sql = "SELECT * FROM centros WHERE id='$idquien'";
	$row = queryrow($sql);
	
	if (!$row){
		return "Otro";	
	}
	
	$nombre = $row["nombre"];
	
	return iconv("iso-8859-1","UTF-8",$nombre);
		
}


function genXulComboCentros($selected=false,$xul="menuitem",$callback=false) {
	$sql = "SELECT * FROM centros ORDER BY nombre ASC, id ASC ";
	$res = query($sql);
		
	$out = "";	
	$call = "";
	while($row = Row($res)){
		
		$key = $row["id"];
		$value = iconv("iso-8859-1","UTF8",$row["nombre"]);
		
		if ($callback) 
			$call = "oncommand=\"$callback('$key')\"";
			
		if ($key!=$selected)
			$out .= "<$xul value='$key' label='$value' $call/>";
		else	
			$out .= "<$xul selected value='$key' label='$value' $call/>";

			
	}
	return $out;
}

*/



function getNamePadre($id_cat){

	if(isset($_SESSION["name_id_categoria_" . $id_cat]))
			return $_SESSION["name_id_categoria_" . $id_cat];

	$row = queryrow("SELECT * FROM categorias WHERE id_categoria ='$id_cat'");

	$nombre = $row["nombre"];
	$_SESSION["name_id_categoria_" . $id_cat] = $nombre;

	return $nombre;
}

function getComboCategoriasHoja($idquien=false){
	$sql = "SELECT * FROM categorias WHERE eliminado=0 AND eshoja=1 ORDER BY id_padre ASC, posicion ASC ";

	$res = query($sql);

	$out = "";
	while ($row= Row($res)){
		$name = ($row["nombre"]);

		$namepadre = (getNamePadre($row["id_padre"]));

		$key = $row["id_categoria"];

		if ($key == $idquien){
			$selected = " selected='selected' ";
		} else {
			$selected = "";
		}

		$out .= "<option value='$key' $selected>" .htmlLame($namepadre) . " - " . htmlLame($name). "</option>\n";
	}

	return $out;
}


?>