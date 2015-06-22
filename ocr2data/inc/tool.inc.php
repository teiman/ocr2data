<?php





function getRowsNovedades($modo="minilista"){
	global $cat_s;
	$id_categoria_s = sql($categoria);

	if ($modo=="minilista")
		$lim = 4;
	else
		$lim = 15;

	$sql = "SELECT * FROM contenidos WHERE eliminado=0 ORDER BY id_contenido DESC, nombre ASC LIMIT $lim";//TODO: num novedades parametrizable

	$res = query($sql);

	$rows = array();
	while($row = Row($res)){

		//CSSDESCARGAR
		if ($row["tipo"]=="html")
			$row["cssdescargar"] = "ocultar";
		else
			$row["cssdescargar2"] = "ocultar";

		$rows[] = $row;
	}

	return $rows;
}


function getMigasdePan($cat){

	$cat_s = sql(CleanID($cat));

	$rows = array($row);

	if(0){
		$row = queryrow("SELECT * FROM categorias WHERE id_categoria='$cat_s'");

		$id_padre = $row["id_padre"];

		if (!$id_padre)
			return array($row);		
	}
	
	$id_padre = $cat_s;


	while($row = queryrow("SELECT * FROM categorias WHERE id_categoria='$id_padre'")){

		array_unshift($rows,$row);

		$id_padre = $row["id_padre"];
		$id_categoria = $row["id_categoria"];

		if(!$id_categoria)
			break;
	}

	$row = queryrow("SELECT * FROM categorias WHERE id_categoria='0' and Eliminado='0' ");
	array_unshift($rows,$row);


	$rowsclean = array();
	foreach($rows as $key=>$datos){
		if($datos)
			$rowsclean[] =$datos;
	}
	
	return $rowsclean;
}




?>