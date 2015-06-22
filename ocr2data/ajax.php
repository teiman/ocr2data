<?php

include("tool.php");
include("inc/gateway.inc.php");

//localhost/congen/ajax.php?
//callback=jsonp1296130751987
//&modo=clientessubstring
//&substring=

//echo "ok";

//modo	clientessubstring
///substring	ee

$modo = (isset($_REQUEST["modo"]))?$_REQUEST["modo"]:false;


switch($modo){


	case "guardardatos":

		$hayObservaciones = false;

		$numPedido = false;

		$recon_id_s = sql($_REQUEST["recon_id"]);

		$sql = "SELECT *
				FROM regiones_recon
				LEFT JOIN regiones ON regiones_recon.region_id = regiones.region_id WHERE regiones_recon.recon_id='$recon_id_s' ";

		$res = query($sql);
		while($row = Row($res)){
			$region = $row["region_nombre"];
			$id = $row["regionrecon_id"];
			
			switch($region){
				case "fechaentrega":
					$fecha_s = sql($_REQUEST["fechaentrega"]);
					$sql = "UPDATE regiones_recon SET cierto_text='$fecha_s', calidad=1, validadousuario=1 WHERE regionrecon_id='$id' ";
					query($sql);
					//echo $sql;
					break;

				case "numpedido":
					$numpedido_s = sql($_REQUEST["numpedido"]);
					$sql = "UPDATE regiones_recon SET cierto_text='$numpedido_s', calidad=1, validadousuario=1 WHERE regionrecon_id='$id' ";
					query($sql);
					//echo $sql;

					$numPedido = $_REQUEST["numpedido"];
					break;
				case "observaciones":
					$texto_s = sql($_REQUEST["observaciones"]);
					$sql = "UPDATE regiones_recon SET cierto_text='$texto_s', calidad=1, validadousuario=1 WHERE regionrecon_id='$id' ";
					query($sql);
					$hayObservaciones = true;
					break;


				default:
					echo "reg:$region,??<br>\n";
					break;
			}

		}

		$texto_s = sql($_REQUEST["observaciones"]);

		if (!$hayObservaciones and $texto_s){

			

			$tid = $_REQUEST["tid"];
			$row = queryrow("SELECT region_id FROM regiones WHERE region_nombre='observaciones' AND template_id='$tid' LIMIT 1");

			$id = 0;

			if(!$row){
				$sql = "INSERT INTO regiones (region_nombre,template_id) VALUE ('observaciones','$tid')";
				query($sql);
				$id = $UltimaInsercion;
			} else {
				$id = $row["region_id"];
			}			
			
			$recon_id = CleanID($_REQUEST["recon_id"]);

			$row = queryrow("SELECT region_id FROM regiones_recon WHERE recon_id='$recon_id' AND region_id='$id' LIMIT 1");

			if(!$row){
				$sql = "INSERT INTO regiones_recon (cierto_text,recon_id,region_id,validadousuario) VALUES('$texto_s','$recon_id' ,'$id','1' ) ";
				query($sql);
			} else {
				$sql = "UPDATE regiones_recon SET cierto_text = '$region_id' WHERE recon_id='$recon_id' and region_id='$id'";
				query($sql);
			}


			//INSERT INTO regiones_recon (cierto_text,region_id) VALUES('prueba', '827' )
			//echo $sql;
			//die("rid:". $recon_id . ",ui". $UltimaInsercion);
		}


        //$recon_id_s = sql($_REQUEST["recon_id"]);
		
		marcarEstadoComunicacion($recon_id_s,"tramitado");

		$id_cliente = $_REQUEST["id_cliente"];

		if($id_cliente)
			marcarIdCliente($recon_id_s,$id_cliente);//TODO: diferenciar idcliente en una y otra aplicacion.


		if ($numPedido){
			marcarNumPedido($recon_id_s,$numPedido);
		}

		//die("hola");
		break;
	/*

Fallo de conexión en INSERT INTO regiones (nombre_region,template_id) VALUE ('observaciones','') o Resource id #22

	ºdesconocido
	ºdesconocido
	ºfechaentrega	3-7-710
reg:direccionentrega,??<br>
reg:lineaspedido,??<br>

	id_cliente	24
    nombrecliente

	ºid_comm	77
	ºmodo	guardardatos
	ºnumpedido	701522
	ºobservaciones
	ºrecon_id	11
     */


	case "direccionescliente":

	$id_cliente = CleanID($_REQUEST["id"]);
	$rid		= htmlentities($_REQUEST["rid"]);
	
	$sql = "SELECT * FROM data_direccionescliente WHERE id_cliente = '$id_cliente' ";

	$res = query($sql);


	$html = "";
	$n = 0;
	while($row = Row($res)){

		$nombre = $row["direccion_txt"];
		$id		= $row["id_dircliente"];
		
		$direccion = str_replace(">","&gt;",$direccion);
		$direccion = str_replace("<","&lt;",$direccion);

		$direccion = iconv("ISO-8859-1","UTF-8",$direccion);
		$direccion = nl2br($nombre);
		
		$elementoid = "a_" . $rid . "_" . $n;

		$html = $html . "<a id='$elementoid'  href='javascript:accionDireccion(\"$elementoid\",\"pick\")' class='elementosav es$n a_$rid' title='$id'>\n".
		 " $direccion ".
		" </a>";
		$n++;
	}

	$html .= "<a class='elementosav es$n a_$rid altaDireccion' title='0' href='javascript:accionDireccion(this,\"new\")'>Nueva direcci&oacute;n</a>";

	$html = iconv("ISO-8859-1","UTF-8",$html);

	$datos = array("html"=>$html,"n"=>$n,"ok"=>"ok","sql"=>$sql, "id_cliente"=>$id_cliente);

	//die( var_export($datos,true) );

	//die( "datos:" .json_encode($datos) );
	echo  json_encode($datos);
	break;

// --- ----  ---  ---

	case "clientessubstring":

	$substring_s = sql(trim($_REQUEST["substring"]));

	$sql = "SELECT id_cliente, nombre FROM data_clientes WHERE nombre LIKE '%".$substring_s."%' ";

	$res = query($sql);

	$resultados = array();

	$json = "[" ;

	$t=0;
	while($row = Row($res)){
		$t++;

		$row["id"] = $row["id_cliente"];
		$row["label"] = $row["nombre"];
		$row["name"] = $row["nombre"];
		$row["value"] = $row["id_cliente"];
		$json .= $and . json_encode($row);
		$and = ",";
	}

	echo $json . "]";

	default:
	break;


	case "clientessubstring2":

	$substring_s = sql(trim($_REQUEST["substring"]));

	$sql = "SELECT id_cliente, nombre FROM data_clientes WHERE nombre LIKE '%".$substring_s."%' ";

	$res = query($sql);

	$resultados = array();

	//$json = "{ items: [" ;
	$json = "[" ;

	$t=0;
	while($row = Row($res)){
		$t++;

		$row["id"] = $row["id_cliente"];
		$row["label"] = $row["nombre"];
		$row["name"] = $row["nombre"];
		//$row["value"] = $row["id_cliente"];
		$row["value"] = "C". sprintf('%04d',$row["id_cliente"]). " ".$row["nombre"];

		$json .= $and . json_encode($row);
		$and = ",";
	}

	//echo $json . "],'era':'$substring_s'}";
	echo $json . "]";

	default:
	break;
}



// De la biblioteca; el catalogo de libros.
// ordenador por la asignatura (enseñanaza del español..)


?>