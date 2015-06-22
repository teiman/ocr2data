<?php


include("tool.php");
include_once("inc/paginabasica.inc.php");
include_once("class/imagen.class.php");
include_once("class/recon.class.php");
include_once("inc/lectura.inc.php");
include_once("inc/gateway.inc.php");

$demo           = true;
$lineaCierra    = 0;//991; //77-91
$nombrecliente  = "";
$dircliente_encontrado_id = false;


$modo       = $_REQUEST["modo"];
$framenum   = CleanID($_REQUEST["framenum"]);
$id_comm    = CleanID($_REQUEST["id_comm"]);
$recon_id   = CleanID($_REQUEST["recon_id"]);


$recon = new recon();

switch($modo){

	case "marcaenprocesoysigue":
		$recon_id = $_REQUEST["recon_id"];

		marcarEstadoComunicacion($recon_id,"en proceso");


		die();//Aqui acaba.
		break;

    case "inicia":
        $id_comm = $_REQUEST["id_comm"];
        $recon_id = $_REQUEST["recon_id"];

        if($id_comm>0){
	    	$sql = "SELECT * FROM recon WHERE id_comm='$id_comm' LIMIT 1";
    		$row = queryrow($sql);
		    $recon_id = $row["recon_id"];
        } else {
            $recon_id = $_REQUEST["id_recon"];
        }

		$esCarga = $recon->Load($recon_id);
		if(!$esCarga) {
			//die("no cargo". $id_comm . ",sql:". $sql);
			//TODO: no asi

			header("Location: error.php?causa=RECON_NOT_FOUND");
			return;
		}

		$_SESSION["ReconUltimoVisto"] =  $recon_id;
		break;

    case "cargarprimeropendiente":  
        $sql = "SELECT * FROM recon WHERE estado='pendiente' AND eliminado=0 ORDER BY recon_id ASC LIMIT 1";
        $row = queryrow($sql);
        $recon_id = $row["recon_id"];
        $id_comm = $row["id_comm"];
  


        if(!$recon_id){
            $recon_id = $_SESSION["ReconUltimoVisto"];

            $sql = "SELECT * FROM recon WHERE estado='pendiente' and recon_id>='$recon_id'  AND eliminado=0 ORDER BY recon_id ASC";
            $row = queryrow($sql);
            $recon_id = $row["recon_id"];
            $id_comm = $row["id_comm"];
            $tid    = $row["template_id"];
            

            if(!$recon_id){
                header("Location: completo.php?redireccion=NO_NEXT_RECONID");
                return;
            }

            $_SESSION["ReconUltimoVisto"] =  $recon_id;
        }

        $esCarga = $recon->Load($recon_id);
        if(!$esCarga) {
            header("Location: error.php?causa=SIGUE_NO_CARGA");
            return;
        }

        $_SESSION["ReconUltimoVisto"] =  $recon_id;

        break;

    case "cargarsiguiente":
        $recon_id = $_REQUEST["recon_id"];

        if(!$recon_id){
            $recon_id = $_SESSION["ReconUltimoVisto"];
        }


        $sql = "SELECT * FROM recon WHERE estado='pendiente' and recon_id>'$recon_id'  AND eliminado=0  ORDER BY recon_id ASC";
        $row = queryrow($sql);
        $recon_id = $row["recon_id"];
        $id_comm = $row["id_comm"];
        $tid    = $row["template_id"];
        

        if(!$recon_id){
            //header("Location: ../modcentral.php?redireccion=default2");

            header("Location: completo.php?redireccion=NO_NEXT_RECONID");
            return;
        }

        $_SESSION["ReconUltimoVisto"] =  $recon_id;

        $esCarga = $recon->Load($recon_id);
        if(!$esCarga) {
            header("Location: error.php?causa=SIGUE_NO_CARGA");
            return;
            //die("no pudo cargar recon: ()");
        }

        break;

	case "sigue":
	default:
		$recon_id = $_REQUEST["recon_id"];

		if(!$recon_id){
			$recon_id = $_SESSION["ReconUltimoVisto"];


			$sql = "SELECT * FROM recon WHERE estado='pendiente' and recon_id>'$recon_id'  AND eliminado=0 ORDER BY recon_id ASC";
			$row = queryrow($sql);
			$recon_id = $row["recon_id"];
			$id_comm = $row["id_comm"];
			$tid	= $row["template_id"];
			

			if(!$recon_id){
				//header("Location: ../modcentral.php?redireccion=default2");

				header("Location: completo.php?redireccion=NO_NEXT_RECONID");
				return;
			}

			$_SESSION["ReconUltimoVisto"] =  $recon_id;
		}


		$esCarga = $recon->Load($recon_id);
		if(!$esCarga) {
			header("Location: error.php?causa=SIGUE_NO_CARGA");
			return;
			//die("no pudo cargar recon: ()");
		}

		$_SESSION["ReconUltimoVisto"] =  $recon_id;
		break;
}


$page->setAttribute( 'cuerpo', 'src', 'corrigeusuario.html' );


/*
 * Informacion sobre el reconocimiento
 *
 */
$tid = $recon->get("template_id");


/*
 * Preparar para la web
 *
 */


$interfaces_js = array();
$elegirCliente = false;
$id_dircliente = 0;
$id_cliente = 0;
$q_id_dircliente=0;
$textoObservaciones = false;

$interfaces = array();
$numRegiones = 0;


/*
 * Recuperamos regiones
 *
 */

$res = $recon->getRegionesRecon();



while($row = Row($res)){
    $numRegiones++;

	$regular = $row["region_nombre"];
	$id = $row["regionrecon_id"];

	$datoscomplejos = "";
	$tagname = "input";
	$type = "text";
	$name = $regular;
	$class =  $regular;
	$class2 = "";
	$name = "desconocido";
	$label = $regular;
	$comportamientoespecial = "ninguno";

	$cuantosLineasPedido =0;
	$q = $row["calidad"];//calidad
	$saltarDemo = false;

	$ocultarLinea = "";


	switch($regular){
		case "lineaspedido":
			$tagname="div";
			$class="bloquecomplejo";
			$codigo = $row["cierto_text"];
			//$codigo = str_replace("'"," ",$row["cierto_text"]);

			error_log("lineasped codigo:". $codigo);

			eval('$data = '.$codigo. ';');


			$newdata = array();


			//HACK: siempre hay al menos una linea, esto esta aqui porque sino la conversion a json es inestable y genera un array en lugar de un hash :(
			$newdata[999] = array("calidad"=>0, "cierto_text"=>"", "cierto_id"=>"");


			foreach($data as $key=>$value){

				$calidad = $value["calidad"];

				if($calidad<UMBRAL_ROJO) {
					//$value["resumen"] = "";
					//$data[$key] = $value;

					//Ignoramos este campo

				} else {
					//Solo admitimos campos seguros
					

					//error_log("vvx:". var_export($value, true) );
					
					$id_producto = sql($value["id_producto"]);
					
					if($id_producto) {
						$rowdata = queryrow("SELECT * FROM data_productos WHERE id_producto='$id_producto'");

						if ($rowdata){
							$value["ref"] = $rowdata["codigo"];

						}
					}


					/*
						[Thu Feb 17 17:07:30 2011] [error] [client 127.0.0.1] vvx:
						array (\n  'id_producto' => 7,\n  'peso' => 21,\n  'calidad' => 0.75,\n
						'ocr' => '1 CM10150 CM10150 COLCHON 90X190',\n  'animorfico' => '1 CM1O15O CM1O15O CO1CHON 9OX19O',\n
					 'numLinea' => 1,\n  'resumen' => 'Colchon Enroll - Twister 0570 90x190 RF.10570',\n),
					referer: http://localhost/ecogen/webcongen/modframe.php?id_comm=70   */

					$newdata[$key] =$value;
				}
			}

			//error_log("ri:$recon_id, data:". var_export($data,true) );

			$data = $newdata;

			$data_json = json_encode($data);

			//error_log("json:" . $data_json);

			$nameDataBloque = "lineaspedido";

			$js =  " lineas.".$nameDataBloque." = [".$cuantosLineasPedido."];\n ";
			$js .= " lineas.".$nameDataBloque."[".$cuantosLineasPedido."] = " . $data_json . ";\n ";

			$datoscomplejos = g("script",$js)  ;

			$interfaces_js[] = array("name"=>$nameDataBloque,"num"=>$cuantosLineasPedido);

			$label = "";
			$ocultarLinea = "ocultar";


			$cuantosLineasPedido ++;
			break;

		case "observaciones":

/*
  row:array (
 * 'regionrecon_id' => '880',
 * 'recon_id' => '6',
 * 'region_id' => '93',
 * 'ocr_text' => '',
 * 'cierto_text' => 'prueba',
 * 'calidad' => '0',
 * 'cierto_id' => '0',
 * 'validadousuario' => '1',
 * 'eliminado' => '0',
 * 'template_id' => '8',
 * 'tipo' => '',
 * 'region_nombre' => 'observaciones',
 * 'region_x' => '0',
 * 'region_y' => '0',
 * 'region_w' => '0',
 * 'region_h' => '0', )
 */

			//die("row". var_export($row,true));
		
			if ($row["cierto_text"])
				$textoObservaciones = $row["cierto_text"];

			$saltarDemo = true;
			continue;
			break;
		case "numpedido":
			$label = "Nº pedido";

			$value = $row["cierto_text"];
			$value = str_replace("/"," ",$value);
			$value = trim($value);

			$comportamientoespecial = "alfanumerico";
			$name = "numpedido";
			
			break;

		case "fechaentrega":
			$label = "Fecha entrega";
			$value = $row["cierto_text"];
			$comportamientoespecial = "fecha";

			$name = "fechaentrega";
			break;

		case "direccioncliente":
			$label = "Dirección cliente";
			$tagname = "textarea";
			$datoscomplejos = html($row["cierto_text"]);

			$elegirCliente = true;
			
			$q = $row["calidad"];

			if($q_id_cliente<$q) {
				$id_dircliente = $row["cierto_id"];
				$q_id_dircliente = $q;

				$dircliente_encontrado_id = $id_dircliente;

				//error_log(__LINE__ . "ceid:". $cliente_encontrado_id);
			} else {
                //error_log(__LINE__ . "ceid(x1x): no le molo: ". $q_id_cliente);
            }


			if ($q<UMBRAL_ROJO){
				$datoscomplejos = "";
			}

			$class2 = "ocultofijo";

			$comportamientoespecial = "direccioncliente";
			$saltarDemo = true;
			break;

		case "direccionentrega":
			$label = "Dirección entrega";
			$tagname = "textarea";
			$datoscomplejos = html($row["cierto_text"]);

			$elegirCliente = true;
			
			$q = $row["calidad"];		

			if($q_id_dircliente<$q) {
				$id_dircliente = $row["cierto_id"];
				$q_id_dircliente = $q;
               
			    $dircliente_encontrado_id = $id_dircliente;
				error_log(__LINE__ . "ceid:". $dircliente_encontrado_id . ",data". var_export($row,true));
			}

			if ($q<UMBRAL_ROJO){
				$datoscomplejos = "";
			}

			//$class2 = "ocultofijo";
			$comportamientoespecial = "direccioncliente";
			//$saltarDemo = true;
			break;

		default:
			$name = $regular;
			break;
	}

	if($demo && $saltarDemo)	continue;//no incluiremos este campo en demo


	/* No mostramos datos rojos (nada fiables)*/
	if($q<=UMBRAL_ROJO)
		$text = "";



	$data = array(
		"id"=>$id,
		"q"=>$q,
		"class2"=>$class2,
		"tagname" => $tagname,
		"label" => $label,
		"text" => $text,
		"name" => $name,
		"type" => $type,
		"value"=> $value,
		"class" => $class,
		"ocultar"=>$ocultarLinea,
		"comportamiento"=>$comportamientoespecial,
		"datoscomplejos"=>$datoscomplejos
	);

	$interfaces[] = $data;
}


if(!$numRegiones){
    header("Location: modtipodocumento.php?tid=".$tid."&volver=lectura.php%3Fmodo%3Dinicia%26id_recon%3D".$recon_id);
    exit();
}


if($elegirCliente and $dircliente_encontrado_id){
	$sql = "SELECT * FROM data_direccionescliente WHERE id_dircliente=$dircliente_encontrado_id";

	$row = queryrow($sql);

	$id_cliente = $row["id_cliente"];
    error_log(__LINE__ . "buscando idc:". $id_cliente);
}


$page->addRows( 'lista', $interfaces );

$data_json = json_encode($interfaces_js);
$page->addVar("cuerpo","interfacesjs",$data_json);

$id_comm = $recon->get("id_comm");

$imagenVisible = "image_".$id_comm.".jpg";

$page->addVar("cuerpo","ficherotrabajo",$webdata . $imagenVisible);
$page->addVar("cuerpo","datos",json_encode($row) );


if ($id_cliente){
	$id_cliente_s = sql($id_cliente);
	$row = queryrow("SELECT * FROM data_clientes WHERE id_cliente='$id_cliente_s'");
	$nombrecliente = $row["nombre"];
	
    error_log("id:$id_cliente_s,n:$nombrecliente");

	//$colorcliente =

	//$q_id_cliente

	$color = "";
	
	if ($q_id_cliente<0.3)
		$color = "rojo";
	else if($q_id_cliente<0.5){
		$color = "naranja";
	} else
		$color = "verde";
	
	$page->addVar("cuerpo","colorcliente",$color);
}


if($q_id_dircliente<0.27){//era 0.3
	$nombrecliente = "";
    error_log(__LINE__ . "qidcliente es bajo:". $q_id_dircliente);
}


$page->addVar("cuerpo","elegircliente",$elegirCliente);
$page->addVar("cuerpo","id_cliente",$id_cliente);
$page->addVar("cuerpo","idrecon",$recon_id);
$page->addVar("cuerpo","idcomm",$id_comm);
$page->addVar("cuerpo","tid",$tid);
$page->addVar("cuerpo","framenum",$framenum);
$page->addVar("cuerpo","nombrecliente",$nombrecliente);

$page->addVar("cuerpo","observaciones",$textoObservaciones);


//die("nc:". $nombrecliente . ",id_cliente:". $id_cliente . ",dircliente_encontrado_id:". $dircliente_encontrado_id .",eC:". $elegirCliente . ",Q:". $q_id_dircliente);

/*
 * Ajustes visuales
 *
 */


$usuarioLogueado = $_SESSION["user_nombreapellido"];
if (!$usuarioLogueado){
	$usuarioLogueado = "Usuario";
}

$page->setAttribute("cabeza",'nombreusuario',"(".$usuarioLogueado.")");




$page->Volcar();


exit();

?>