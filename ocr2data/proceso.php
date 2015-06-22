<?php

include("tool.php");
include_once("class/recon.class.php");
include_once("class/imagen.class.php");
include_once("class/regionrecon.class.php");

include_once("inc/proceso.inc.php");

if(1){
    set_time_limit (0);//run script forever
	ignore_user_abort(TRUE);//run script in background
}

$ANCHO_DEFECTO = 792;

header('Content-type: text/html; charset=utf-8');


$busca_id_cliente = 15;

?>
<style>

* {
 font-size: 12px;
}

.dato {
  font-size: 11px;
  font-family: terminal,mono;
  background-color: #eee;
  padding: 3px;

}

.resultado_nivel1 {
	border: 2px solid #ccc;
	padding:10px;
}

</style>
<?php


$cr = "<br />\n";

preGenerarResumenes();
preGenerarResumenesAnimorficos();


comenta("Buscamos un reconocimiento");


$recon_id = intval($_REQUEST["recon_id"]);

comenta("Cogemos la primera libre");
	
$sql = "SELECT * FROM recon ".
        " WHERE estado='sin procesar' and recon_id>='$recon_id' ".
        " ORDER BY recon_id ASC LIMIT 1 ";
$row = queryrow($sql);

$recon_id = $row['recon_id'];

if (!$recon_id){
    comenta("No hemos encontrado nuevos por procesar");
    exit();
}


//TODO: comprobar que el recon_id proporcionado esta pendiente
echo "recon: $recon_id," . $cr;

comenta("Cargamos un reconocimiento");

$recon = new recon();

$loaded = $recon->Load($recon_id);

echo ($loaded)?"R($recon_id) cargado":"fallo en la carga de R($recon_id)";


if (!$loaded ){
	die("No se pudo cargar ($recon_id)");
}

comenta("Template?");

$template = $recon->getTemplate();

echo ($template)?"T cargado":"fallo en la carga de T";

if (!$template){
	die("sin template");
}

$tid = $template->get("template_id");


comenta("Reconocimiento de imagenes  recon_id($recon_id)");

$sql = "SELECT * FROM imagen WHERE recon_id = '$recon_id' LIMIT 1";

$datosimage= queryrow($sql);
$registro = $datosimage["fichero"];

echo "imagen: fichero(". html($registro). ")" . $cr;

comenta("Reconocimiento de regiones  recon_id($recon_id)");

$path_originales = $rootdata. "scan/";


$fichero = $datosimage["fichero"];

comenta("Limpiamos regiones reconocidas para $recon_id en fichero ($fichero)");

query("DELETE FROM regiones_recon WHERE recon_id='$recon_id' ");
query("DELETE FROM recon_ciertos WHERE recon_id='$recon_id' ");



comenta("Reconocimiento de regiones, para  tid:$tid");


$codigoGuardar = "";

$sql = "SELECT * FROM regiones WHERE template_id = '$tid'";


$resRegiones = query($sql);

$numprocesados = 0;
$inicio = microtime_float();

while($regiondata = Row($resRegiones)){
	$numprocesados++;
	echo "<blockquote>";

	$requiereDatosDirecciones = false;
	$modo = false;
	$registro	= $regiondata["region_nombre"];
	$rid		= $regiondata["region_id"];
	$recogeNumPedido = false;

	switch($registro){
		case "direccionentrega":
			$requiereDatosDirecciones = true;
			$modo = "direccion";
			break;
		case "direccioncliente":
			$requiereDatosDirecciones = true;
			$modo = "direccion";
			break;
		case "numeroconsignos":
			$modo = "numeroconsignos";
			break;
		case "numpedido":
			$modo = "numpedido";
			$recogeNumPedido = true;
			break;
		case "fechaentrega":
			$modo = "fecha";
			break;
		case "lineaspedido":
			$modo = "lineaspedido";
			break;
		default:
			break;
	}

	comenta("Reconocemos un area ($rid), registro[$registro]");

	echo "se cargara: ($path_originales)($fichero)". $cr;


	$img = new imagen($path_originales, $fichero);
	
	$ocr_text = $img->ocr_area($regiondata,$modo);
	$ocr_text = str_replace("'"," ",$ocr_text);
	$ocr_text = str_replace("\""," ",$ocr_text);

	$img->Cerrar();

	echo "se reconocio:". $cr;
	echo "<pre>";
	echo html($ocr_text);
	echo "</pre>";

	$maximos = array();

	comenta("Almacenamiento region reconocida, en bruto  recon_id($recon_id)");

	$rr = new regionrecon();

	$rr->set("recon_id",$recon_id);
	$rr->set("region_id",$regiondata["region_id"]);
	$rr->set("ocr_text",$ocr_text);

	$altarr = $rr->Alta();

	echo ($altarr)?"Se dio de alta region reconocimiento":"Error: rr alta";
	echo $cr;



	comenta("Extraccion de datos borrosos");

	$rowsDirecciones = array();

	if ($requiereDatosDirecciones) {
		$sql = "SELECT * FROM data_direccionescliente ";// WHERE id_cliente='$id_cliente'  ";
		$res = query($sql);

		while($row = Row($res)){
			$row["datos_txt"] = $row["direccion_txt"];

			$rowsDirecciones[] = $row;
		}
	}


	/* -------------------------------------------------- */

	$analisis["normal"]		= array("media"=>0,"datociertoid"=>0,"datociertodata"=>0);
	$analisis["animorfica"] = array("media"=>0,"datociertoid"=>0,"datociertodata"=>0);


	if ($modo=="lineaspedido"){

		$lineas  = split("\n",$ocr_text);
		$lineasOriginal = count($lineas);

		//Quitamos las lineas "vacias"
		$filtradas = array();
		foreach($lineas as $linea){
			if (strlen($linea)>2)
				$filtradas[] = $linea;
		}

		$lineasFiltradas = count($filtradas);

		$previoanalisis =  var_export($filtradas,true);


		$productos = array();

		$numLinea = 0;
		foreach($filtradas as $linea){

			$resumenBusqueda = buscaProducto($linea);

			$datosProducto = analizarBusqueda($resumenBusqueda);

			if($datosProducto["id_producto"]){
				$datosProducto["numLinea"] = $numLinea;
				$datosProducto["resumen"] = getResumenProducto($datosProducto["id_producto"]);
				$productos[$numLinea] = $datosProducto;
			}

			/* Seguimos un registro de que linea estamos analizando. Esto sera
             * util para luego hacer coincidir este dato con la cantidad
             */
			$numLinea++;
		}
		

		$media = $lineasFiltradas / $lineasOriginal;

		foreach($productos as $producto){
			$media = ($media*2 + $producto["calidad"]*1 )/(2+1);
		}


		$codigo = var_export($productos,true);

		echo "<table>";
		echo g("tr",g("td","OCR:").g("td", "<pre>". html($ocr_text)  . "</pre>"  ) );
		echo g("tr",g("td valign='top'","OCR limpio:").g("td", "<pre>". html($previoanalisis) . "</pre>"));
		echo g("tr",g("td valign='top'","Detección:").g("td", "<pre>". html($codigo) . "</pre>"));
		echo g("tr",g("td","MEDIA:").g("td",$media ));
		echo "</table>";

		$analisis["normal"]		= array("media"=>(float)$media,"datociertoid"=>1,"datociertodata"=>$codigo);



	} else 	if ($modo=="fecha"){




		$codigo = trim(strtoupper($ocr_text));


		$codigo = limpiaCodigo("numeros|signos|espacios",$codigo);

		$numeros = sacarNumeros($codigo);


		$sep = "-";

		$numnumeros = count($numeros);
		$t1 = $numeros[0];
		$t2 = $numeros[1];
		$t3 = $numeros[2];
		$t4 = $numeros[3];

		if($t1==0){
			$t1=$t2;
			$t2=$t3;
			$t3=$t4;
			$t4="";
		}


		$tt1 = "";
		$tt2 = "";
		$tt3 = "";



        if ($numnumeros==1){
            //No hemos podido separar las partes de la fecha adecuadamente. Vamos a intentarlo ahora
            $len = strlen($codigo);
            
            if ($len>7){
                // Esperamos una fecha con la forma 332410052010  (10-5-2010)
                $y = substr($codigo, -4);
                $m = substr($codigo, -6,2);
                $d = substr($codigo, 0,2);
                if ($d>12) $d = substr($codigo, -8,2);
            } else  if ($len==6){
                // Esperamos una fecha con la forma 110510  (11-5-2010)
                $y = substr($codigo, -2);
                $m = substr($codigo, -4,2);
                $d = substr($codigo, 0,2);
            } else {
                $y = substr($codigo, -2);
                $m = substr($codigo, -4,2);
                $d = substr($codigo, 0,2);
                error_log("nn1:$d,$m,$y,$codigo");
            }

            $t1 = intval($d);
            $t2 = intval($m);
            $t3 = intval($y);

            $numnumeros = 3;

            error_log("nn1: ($t1,$t2,$t3,$t4)". __LINE__);            
        }

		error_log("ccx: ($t1,$t2,$t3,$t4)". __LINE__);

		//Tres numeros => correcto!		
		if ($numnumeros<3){

			error_log("ccx: ". __LINE__);
			//tenemos año?
			$tenemosAgno = ($t1>2000 or $t2>2000)?true:false;

			if (!$tenemosAgno){
				$tt1 = $t1;
				$tt2 = $t2;
				$tt3 = date("Y");
			} else {
				if ($t2>2000) {
					$tt3 = $t2;
					if ($t1>12){
						$tt1= $t1;
					} else {
						$tt1 = 1;
						$tt2 = $t1;
					}
				} else {
					if ($t2>2000){
						$tt1 = 1;
						$tt2 = $t1;
						$tt3 = $t2;
					}
				}
			}
			
			$fecha = $tt1 . $sep . $tt2 . $sep . $tt3;

			error_log("ccx: ($fecha)". __LINE__);
		} else 	if ($numnumeros==3){

			error_log( __LINE__ . ",ccx,i: $numnumeros");

			$tt1 = $t1;
			$tt2 = $t2;
			$tt3 = $t3;


			$test = strlen(str_replace($t3,"",date("Y")));

			//error_log( __LINE__ . ",i: test:($test)");

			if ($test<3){
				
				$tt3 = date("Y");
			}

			if ( $tt3 == date("y") ){
				$tt3 = date("Y");
			}


			$fecha = $tt1 . $sep . $tt2 . $sep . $tt3;
			error_log("ccx: ($fecha)". __LINE__);

		}  else {

			//quizas hay mas de 3, pero el tercero es 2000 y pico?
			if( $t3 >2000) {
				$tt1 = $t1;
				$tt2 = $t2;
				$tt3 = $t3;

				$fecha = $tt1 . $sep . $tt2 . $sep . $tt3;
			} else {
				$tt1 = $t1;
				$tt2 = $t2;
				$tt3 = $t3;

				$test = strlen(str_replace($t3,"",date("Y")));
				if ($test<3){
					$tt3 = date("Y");
				}

				if ( $tt3 == date("y") ){
					$tt3 = date("Y");
				}		

				$fecha = $tt1 . $sep . $tt2 . $sep . $tt3;
			}

			error_log("ccx: ($fecha)". __LINE__);
		}




		$codigo = $fecha;

		error_log("ccx: ($fecha)". __LINE__);

		//Calidad en funcion de...  valores erroneos baja
		// ...estabilidad sube (estabilidad: igual numero de numeros detectados que numeros tenidos en cuenta)

		$numerosFecha = sacarNumeros($codigo);

		$media = 1;

		if ($numerosFecha[0]>31){
			//echo "mal dia" . $cr;
			$media *= 0.5;
		}
		if ($numerosFecha[1]>12){
			//echo "mal mes" . $cr;
			$media *= 0.5;
		}
		if ($numerosFecha[2]<2000 or $numerosFecha[2]>2500  ){
			//echo "mal agno:" .$numerosFecha[2]. $cr;
			$media *= 0.5;
		}
		


		$c1 = count($numerosFecha);
		$c2 = count($numeros);

		if ($c1>$c2){
			$p1 = $c2;
			$p2 = $c1;
		} else {
			$p1 = $c1;
			$p2 = $c2;
		}

		$media *= ($p1+1)/($p2+1);

		echo "<table>";
		echo g("tr",g("td","OCR:").g("td", html($ocr_text)) );
		echo g("tr",g("td","OCR limpio:").g("td", html($codigo)));
		echo g("tr",g("td","MEDIA:").g("td",$media ));
		echo "</table>";

		$analisis["normal"]		= array("media"=>(float)$media,"datociertoid"=>$codigo,"datociertodata"=>$codigo);
	} else if ($modo=="numeroconsignos"){


		$codigo = trim(strtoupper($ocr_text));

		$codigo = limpiaCodigo("numeros|signos|espacios",$codigo);

		$longitud = strlen($codigo);
		$numeros = contarNumeros($codigo);
		$signos = contarSignos($codigo);

		$media = ( ($signos + $numeros)/$longitud     );

		echo "<table>";
		echo g("tr",g("td","OCR:").g("td", html($ocr_text)) );
		echo g("tr",g("td","OCR limpio:").g("td", html($codigo)));
		echo g("tr",g("td","MEDIA:").g("td",$media ));
		echo "</table>";

		$analisis["normal"]		= array("media"=>(float)$media,"datociertoid"=>$codigo,"datociertodata"=>$codigo);
		
		/*
		'2.010I2.813 j '  => '2.010 2.813'
		*/
	} else if($modo == "direccion"){
		comenta("Comprueba Literal");

		$maximoEncontrado = 0;
		$maximoEncontradoID = 0;

		foreach( $rowsDirecciones as $row){
			$datosDb = utf8($row["datos_txt"]);


			$compara = comparaTextosAbsolutos( $ocr_text, $datosDb );

			if(0){
			echo "<table>";
			echo g("tr",g("td","BD:").g("td", html($datosDb)) );
			echo g("tr",g("td","OCR:").g("td", html($ocr_text)));
			echo g("tr",g("td","Razon  'palabras tiene'/'palabras encontradas':").g("td", $compara["palabras"]));
			echo g("tr",g("td","Razon  'longitud encontrada'/'longitud tiene':").g("td", $compara["longitud"] ));
			echo g("tr",g("td","Puntuacion peso:").g("td",$compara["peso"] ));
			echo g("tr",g("td","MEDIA:").g("td",$compara["mediametodos"] ));
			echo "</table>";
			}

			$media = $compara["mediametodos"] ;

			if ($media>$maximoEncontrado ){
				$maximoEncontrado = $media;
				$id_dircliente_mejor = $row["id_dircliente"];
				$bdmejor = $datosDb;
			}
		}

		comenta("Resultados comparacion literal");

		echo "<table>";
		echo g("tr",g("td","BD:").g("td", ($bdmejor)) );
		echo g("tr",g("td","OCR:").g("td", html($ocr_text)));
		echo g("tr",g("td","id_dircliente mejor:").g("td", $id_dircliente_mejor  ));
		echo g("tr",g("td","MEDIA:").g("td",$maximoEncontrado ));
		echo "</table>";

		$analisis["normal"] = array("media"=>$maximoEncontrado,"datociertoid"=>$id_dircliente_mejor,"datociertodata"=>$bdmejor);

		/* - - - - - - - - - - - - - - - - - - - - - - - - */

		comenta("Comprueba Borroso Animorfico");//compara con imagenes similares

		$maximoEncontrado = 0;
		$maximoEncontradoID = 0;

		foreach( $rowsDirecciones as $row){
			$datosDb = utf8($row["direccion_txt"]);

			//echo "----------------------" . $cr;
			//echo "IdCliente: ". $row["id_cliente"] . $cr;

			$compara = comparaTextosAbsolutos( $ocr_text, $datosDb, "animorfico");

			if(0){
			echo "<table>";
			echo g("tr",g("td","BD:").g("td", html($datosDb)) );
			echo g("tr",g("td","OCR:").g("td", html($ocr_text)));
			echo g("tr",g("td","Razon  'palabras tiene'/'palabras encontradas':").g("td", $compara["palabras"]));
			echo g("tr",g("td","Razon  'longitud encontrada'/'longitud tiene':").g("td", $compara["longitud"] ));
			echo g("tr",g("td","Puntuacion peso:").g("td",$compara["peso"] ));
			echo g("tr",g("td","MEDIA:").g("td",$compara["mediametodos"] ));
			echo "</table>";
			}

			$media = $compara["mediametodos"] ;

			if ($media>$maximoEncontrado ){
				$maximoEncontrado = $media;
				$id_dircliente_mejor = $row["id_dircliente"];
				$busca_id_cliente = $row["id_cliente"];
				$bdmejor = $datosDb;
			}
		}


		

		comenta("Resultados comparacion animorfica");

		echo "<table>";
		echo g("tr",g("td","BD:").g("td", ($bdmejor)) );
		echo g("tr",g("td","OCR:").g("td", html($ocr_text)));
		echo g("tr",g("td","id_dircliente mejor:").g("td", $id_dircliente_mejor  ));
		echo g("tr",g("td","MEDIA:").g("td",$maximoEncontrado ));
		echo "</table>";

		$analisis["animorfica"] = array("media"=>$maximoEncontrado,"datociertoid"=>$id_dircliente_mejor,"datociertodata"=>$bdmejor);

		comenta("Conciliacion tests");
	 } else if($modo=="numpedido"){


			$codigo = trim(strtoupper($ocr_text));
			//$codigo = limpiaCodigo("numeros|signos|espacios",$codigo);
			$codigo = str_replace("Nº","",$codigo);
			$codigo = str_replace("N".chr(194),"",$codigo);

			//die("cod:$codigo,go:".ord($codigo[1]));
			$codigo = preg_replace('/[^(\x20-\x7F)]*/','', $codigo);


			$codigo = str_replace("PEDIDO","",$codigo);

			$longitud = strlen($codigo);
			$numeros = contarNumeros($codigo);
			$signos = contarSignos($codigo);

			$media = ( ($signos + $numeros)/$longitud     );

			echo "<table>";
			echo g("tr",g("td","OCR:").g("td", html($ocr_text)) );
			echo g("tr",g("td","OCR limpio:").g("td", html($codigo)));
			echo g("tr",g("td","MEDIA:").g("td",$media ));
			echo "</table>";

			$analisis["normal"]		= array("media"=>(float)$media,"datociertoid"=>$codigo,"datociertodata"=>$codigo);

			/*
			'2.010I2.813 j '  => '2.010 2.813'
			*/
	}
	/* -------------------------------------------------- */


	//HACK, demo
	if ($recogeNumPedido){
		$codigoGuardar = $codigo;
	}


	$media1 = $analisis["normal"]["media"];
	$cierto1 = $analisis["normal"]["datociertoid"];

	$media2 = $analisis["animorfica"]["media"];
	$cierto2 = $analisis["animorfica"]["datociertoid"];

	$mediafinal = 0;
	$ciertofinal = 0;

	$acuerdo = true;

	//ajuste del peso de cada prueba
	$pesoPrueba1 = 2;
	$pesoPrueba2 = 1;


	//echo "debug: m1:$media1,c1:$cierto1, m2:$media2, c2:$cierto2" . $cr;

	if ($cierto1 == $cierto2){
		$mediafinal = ($media1*($pesoPrueba1) + $media2 * ($pesoPrueba2)) / ($pesoPrueba1+$pesoPrueba2);
		$ciertofinal = $cierto1;
		$ciertodata =  $analisis["normal"]["datociertodata"];
	} else {
		//comparacion ajustada al peso
		//estamos buscando cual prueba ha dado mas calidad, discriminando favorablemente para la prueba mas rigida
		$primero = (($media1*(1+$pesoPrueba1/$pesoPrueba2))>($media2*(1+$pesoPrueba2/$pesoPrueba1)))?true:false;
		$acuerdo = false;

		if ($primero){
			$ciertofinal = $cierto1;
			$mediafinal = $media1;
			$ciertodata =  $analisis["normal"]["datociertodata"];
		} else {
			$ciertofinal = $cierto2;
			$mediafinal = $media2;
			$ciertodata =  $analisis["animorfica"]["datociertodata"];
		}

		//echo "debug: primero:$primero, m:$media1,c1:$cierto1, m2:$media2, c2:$cierto2" . $cr;

		$mediafinal *= 0.6;//Si hay desacuerdo, la calidad que realmente tenemos probablmente no es mucha
			// de modo que reducimos el indice para reflejar eso.
	}

	echo "<table class='resultado_nivel1'>";
	echo g("tr",g("td","Media final:").g("td", ($mediafinal)) );
	echo g("tr",g("td","Cierto ID:").g("td", html($ciertofinal)));
	echo g("tr",g("td","Acuerdo:").g("td", ($acuerdo)?"Si":"No ($cierto1),($cierto2)"  ));
	echo "</table>";

	//Actualiza area reconocida
	$rr->set("cierto_text",$ciertodata);
	$rr->set("cierto_id",$ciertofinal);
	$rr->set("calidad",$mediafinal);
	$rr->Modificacion();

	//Crea area de documento
	$recon->reconocido($rr, $mediafinal, $ciertofinal );

	echo "</blockquote><hr>";
}

comenta("Cerrado");


//Se ha procesado este recon, ahora queda pendiente
$recon->set("estado","pendiente");
$recon->Modificacion();

comenta("Cargamos dato imagen");

$datosImagen = $recon->getImagenData();
$ficheroOriginal = $datosImagen["fichero"];


$path = $rootdata . "scan/";
$fichero = $ficheroOriginal;

comenta("Cargamos la imagen original");

$img = new imagen($path. $fichero);
$img->Carga();

	
comenta("Generamos la imagen que se visualizara");
	
$height	=	$img->im->getImageHeight();
$width	=	$img->im->getImageWidth();

	$newwidth = $ANCHO_DEFECTO;
	$newheight = $newwidth/$width  * $height;

	$img->im->resizeImage($newwidth,$newheight,Imagick::FILTER_CATROM,1);


	
comenta("Guardamos en un path visible por el usuario");

$id_comm = $recon->get("id_comm");

$out = "image_".$id_comm.".jpg";
$path = $rootdata . "web/";


comenta("Guardamos como [$path][$out]");

$img->Save("jpg",$path, $out);



comenta("Corremos plugins");

$recon->plugins();


comenta("Cerrado");

$fin = microtime_float();

$tardo = $fin - $inicio;

$media = $tardo / $numprocesados;

echo "Info: $tardo,  para $numprocesados,  media: $media" . $cr;



    if(0){
        /*
        //HACK para demo
        if ($busca_id_cliente != 16 and $busca_id_cliente ){
	        $id_comm = $recon->get("id_comm");
	        $busca_id_cliente = intval($busca_id_cliente);
	        $sql = "UPDATE communications SET id_contact='$busca_id_cliente' WHERE id_comm='$id_comm'";
	        query($sql);
        }
        
        if ($codigoGuardar ){
	        $id_comm = $recon->get("id_comm");
        
	        $codigo_s = sql($codigoGuardar);
	        $sql = "UPDATE communications SET codcom='$codigo_s' WHERE id_comm='$id_comm'";
	        query($sql);
        
        
	        //die($sql);
        }*/
    }


?>
<ul>
<li>recon_id: [<?php echo $recon_id ?>]</li>
<li>id comm: [<?php echo $id_comm ?>]</li>
<li>id cliente: [<?php echo $busca_id_cliente ?>]</li>
</ul>

<ul>
 <!--<li><a href="menuprincipal.php">Menu</a></li> -->
 <li><a href="proceso.php?recon_id=<?php echo ($recon_id+1) ?>">Siguiente</a></li>
</ul>
