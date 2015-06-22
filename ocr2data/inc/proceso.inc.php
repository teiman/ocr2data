<?php


function microtime_float ()
{
    list ($msec, $sec) = explode(' ', microtime());
    $microtime = (float)$msec + (float)$sec;
    return $microtime;
}


/*
preGenerarResumenes();
preGenerarResumenesAnimorficos();
*/
	//SELECT concat( nombre, ' ', codigo, ' ', medidas, ' ', ref_fabrica )
	//FROM data_productos

function preGenerarResumenes(){
	query("UPDATE data_productos SET resumen=concat( nombre, ' ', codigo, ' ', medidas, ' ', ref_fabrica )");
}

function preGenerarResumenesAnimorficos(){
	$sql = "SELECT id_producto,resumen FROM data_productos ";

	$res = query($sql);

	while($row = Row($res)){
		$resumen_s = sql(animorfico_text($row["resumen"]));
		$id_producto_s = $row["id_producto"];

		query("UPDATE data_productos SET resumen_animorfico='$resumen_s' WHERE id_producto='$id_producto_s' ");
	}
	
}



function comenta($comentario){
	global $cr;

	echo "<div style='margin-top: 10px;margin-bottom:6px'><b><img src='img/1downarrow.gif' align='absmiddle'>". html($comentario) . "</b></div>";
}


function contiene($mystring,$findme){
	$pos = strpos($mystring, $findme);

	if ($pos === false) {
		return false;
	}
	return true;
}


/*
 * Reduce diferencias en una cadena de texto, de modo que las posibilidades
 * de colision con una cadena igual sean mayores
 */
function soft_text($normal){
	$encontrado_soft = strtoupper(trim($normal));
	$encontrado_soft = str_replace("0","O",$encontrado_soft);

	return $encontrado_soft;
}

function vvalido($valor){
	if ($valor>1) return $valor;
	if ($valor<0) return 0;
	return $valor;
}


function buscaProducto($linea){
	global $cr;

	$datos = split(" ",$linea);

	$filtrada = array();
	$filtradaAlfabetico = array();
	$todas = array();

	$vistas[] = array();

	foreach($datos as $dato){
		
		$longitud = strlen($dato);
		$largo = $longitud>3;
		$novacia = $longitud>0;
		$alfabetico = contarNumeros($dato)<1;

		/* las vacias no le interesan a nadie*/
		if($novacia)
			$todas[] = $dato;

		/* cadenas largas y sin numeros puede ser un nombre */
		if($largo and $alfabetico){
			$filtradaAlfabetico[] = $dato;
		}
	}

	$frasetext = join(" ",$filtradaAlfabetico);
	$frasetext_s = sql($frasetext);	

	$todotexto_s = sql(join(" ",$todas));

	$frasetext_animorfico_s = sql(animorfico_text($todotexto_s));


	$pornombre = array();
	$pormedidas = array();
	$porcodigo = array();
	$porresumen = array();
	$porresumen_raw = array();
	$porresumen_animorfico = array();

	if (strlen($frasetext_s)>0){
		$sql = "SELECT id_producto
				FROM data_productos
				WHERE MATCH (nombre) AGAINST ( '$frasetext_s'  )";
		$res = query($sql);

		while($row = Row($res)){
			$pornombre[] = $row["id_producto"];
		}
	}

	if (strlen($frasetext_s)>0){
		$sql = "SELECT id_producto
				FROM data_productos
				WHERE MATCH (resumen) AGAINST ( '$frasetext_s' )";
		$res = query($sql);

		while($row = Row($res)){
			$porresumen[] = $row["id_producto"];
		}
	}

	if (strlen($todotexto_s)>0){
		$sql = "SELECT id_producto
				FROM data_productos
				WHERE MATCH (resumen) AGAINST ( '$todotexto_s'  )";
		$res = query($sql);

		while($row = Row($res)){
			$porresumen_raw[] = $row["id_producto"];
		}
	}

	if (strlen($frasetext_animorfico_s)>0){
		$sql = "SELECT id_producto
				FROM data_productos
				WHERE MATCH (resumen_animorfico) AGAINST ( '$frasetext_animorfico_s'  )";
		$res = query($sql);

		echo "sql: $sql" . $cr;

		while($row = Row($res)){
			$porresumen_animorfico[] = $row["id_producto"];
		}
	}


	$candidatos["pornombre"] = $pornombre;
	$candidatos["porresumen"] = $porresumen;
	$candidatos["porresumen_raw"] = $porresumen_raw;
	$candidatos["porresumen_animorfico"] = $porresumen_animorfico;

	$candidatos["filtrada"] = $frasetext_s;
	$candidatos["animorfico"] = $frasetext_animorfico_s;
	$candidatos["total"] = $todotexto_s;

	return $candidatos;
}


function mayor($a, $b) {
    if ($a == $b) {
        return 0;
    }
    return ($a > $b) ? -1 : 1;
}



function analizarBusqueda($busqueda){
	global $cr;
			/*
             *
            $datosProducto =

			(SOMEER LAMINAş)datos encontrados: array ( 'pornombre' => array ( ),
			'porresumen' => array ( ),
			'porresumen_raw' => array ( 0 => '6', 1 => '8', ), )

             *
             */

	//echo "busqueda: ". var_export($busqueda,true). $cr;

	$puntosCoincideNombre = 14;
	$puntosCoincideResumen = 4;
	$puntosCoincideResumenRaw = 2;
	$puntosCoincideResumenAnimorfico = 1;

	$pesoMaximoInicial = $puntosCoincideNombre * 2;

	$mejores = array();

	$pornombre = $busqueda["pornombre"];
	$porresumen = $busqueda["porresumen"];
	$porresumenraw = $busqueda["porresumen_raw"];
	$porresumenanimorfico = $busqueda["porresumen_animorfico"];

	foreach( $pornombre as $index=>$id_producto){
		$mejores[ $id_producto ] += $puntosCoincideNombre;
	}

	foreach( $porresumen as $index=>$id_producto){
		$mejores[ $id_producto ] += $puntosCoincideResumen;
	}

	foreach( $porresumenraw as $index=>$id_producto){
		$mejores[ $id_producto ] += $puntosCoincideResumenRaw;
	}

	foreach( $porresumenanimorfico as $index=>$id_producto){
		$mejores[ $id_producto ] += $puntosCoincideResumenAnimorfico;
	}

	uasort($mejores, 'mayor');

	$pesoMaximo = $pesoMaximoInicial;

	foreach($mejores as $id_producto => $peso ){
		$pesoMaximo = ($peso>$pesoMaximo)?$peso:$pesoMaximo;
	}

	$id_producto = 0;
	$peso = 0;

	//coge el primero, que sera el mejor
	foreach($mejores as $id_producto => $peso){ break; };

	echo "escoge: ". var_export($mejores,true).$cr;

	$mejor = array( "id_producto"=>$id_producto,
		"peso"=>$peso ,
		"calidad"=>($peso/$pesoMaximo),
		"ocr"=>$busqueda["total"] ,
		"animorfico"=>$busqueda["animorfico"]);


	return $mejor;
}




/*
 * Reduce diferencias en una cadena de texto, de modo que las posibilidades
 * de colision con una cadena igual sean mayores, lo hace por parecido grafico, y lo hace
 * de forma destructiva (gran perdida de informacion) para maximizar colisiones
 */

function animorfico_text($normal){

	$tofind = "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ";
	$replac = "AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn";
	$normal = strtr($normal,$tofind,$replac);

	$normal = strtoupper(trim($normal));
	$normal = str_replace("0","O",$normal);
	$normal = str_replace("6","8",$normal);//es una buena idea?
	$normal = str_replace("E","8",$normal);//es una buena idea?
	$normal = str_replace("L","1",$normal);//es una buena idea?
	$normal = str_replace("|","1",$normal);//es una buena idea?
	$normal = str_replace("I","1",$normal);//es una buena idea?

	$tofind = ",.:;'`\"";
	$replac = "       ";
	
	$normal = strtr($normal,$tofind,$replac);

	$normal = str_replace("    "," ",$normal);
	$normal = str_replace("   "," ",$normal);
	$normal = str_replace("  "," ",$normal);
	$normal = str_replace("  "," ",$normal);

	return $normal;
}


function contarCaracterCadena($cadena,$caracter){
	$len = strlen($cadena);
	$cadenalarga = str_replace($caracter,$caracter.$caracter,$cadena);
	$lenlarga = strlen($cadenalarga);
	return 	$lenlarga - $len;
}

function contarNumeros($str){
	$numnums = 0;
	$len = strlen($str);

	$cero = ord("0");
	$nueve = ord("9");

	for($t=0;$t<$len;$t++){
		$c = $str{$t};

		$v = ord($c);

		if ($v>= $cero and $v<= $nueve)  $numnums++;
	}

	return $numnums;
}



function sacarNumeros($codigo){

	if( is_array($codigo)){
		$codigo = join("",$codigo);
	}

	$len = strlen($codigo);

	$cero = ord("0");
	$nueve = ord("9");
	$validosSignos = split(" ",". , / -");
	$validosAlfabeto = split(" ","A B C D E F G H I J K L M N O P Q R S T U V W X Y Z");

	$viejoNum = false;
	$oldC = "";
	$c = "";
	$viejoEraNumero = true;
	$esNumero = "";

	$numeroHastaElMomento = "";
	$numeros = array();

	for($t=0;$t<$len;$t++){
		$oldC = $c;
		$c = $codigo{$t};
		$v = ord($c);
		$viejoEraNumero = $esNumero;
		$esNumero = ($v>=$cero and $v<=$nueve);

		if ($esNumero)
			$numeroHastaElMomento .= $c;
		else {
			if (strlen($numeroHastaElMomento)>0){
				$numeros[] = $numeroHastaElMomento;
				$numeroHastaElMomento = "";					
			}			
		}
	}

	if(strlen($numeroHastaElMomento)>0)
		$numeros[] = $numeroHastaElMomento;

	return $numeros;
}








function limpiaCodigo($tipo,$codigo){
	global $cr;

	if( is_array($codigo)){
		$codigo = join("",$codigo);
	}


	$codigo = strtoupper($codigo);

	//El 0 es entendido como os ó qus, esto se puede corregir si sabemos que tenemos numeros
	if ($tipo=="numeros|signos|espacios"){
		//$tofind = "QO";
		//$replac = "00";
		//$salida = strtr($salida,$tofind,$replac);
		$codigo = str_replace("Q","0",$codigo);
		$codigo = str_replace("O","0",$codigo);
	}


	$salida = "";

	$len = strlen($codigo);

	$cero = ord("0");
	$nueve = ord("9");
	$validosSignos = split(" ",". , / -");
	$validosAlfabeto = split(" ","A B C D E F G H I J K L M N O P Q R S T U V W X Y Z");

	$viejoNum = false;
	$oldC = "";
	$c = "";

	for($t=0;$t<$len;$t++){
		$oldC = $c;
		$c = $codigo{$t};
		$v = ord($c);

		switch($tipo){
			case "numeroconsignos":
				$esValido = in_array($c, $validosSignos) || ($v>=$cero and $v<=$nueve);

				if (!$esValido) 
				
				$salida{$t} = ($esValido)?$c:" ";
				
				break;
			case "alfanumericoconsignos":
				$esValido = in_array($c, $validosAlfabeto) || 
							in_array($c, $validosSignos) ||
							($v>=$cero and $v<=$nueve);

				$salida{$t} = ($esValido)?$c:" ";

				break;
			case "numeros|signos|espacios":
				$esValido = in_array($c, $validosSignos) ||
							($v>=$cero and $v<=$nueve) ||
							$c ==" " || $v==32;

				//echo "salida:[$salida][$esValido]($c)" . $cr;
				$salida = $salida . (($esValido)?$c:" ");
				break;
			case "separa_numeros_y_letras":

				$esAlfabeto = in_array($c, $validosAlfabeto);
				$anteriorEsAlfabeto = in_array($oldC, $validosAlfabeto);

				if($esAlfabeto and !$anteriorEsAlfabeto){
					$salida = $salida . " " . $c;
				} else {

					if(!$esAlfabeto and $anteriorEsAlfabeto)
						$salida = $salida . " " . $c;
					else
						$salida = $salida . $c;
				}

				break;
			default:
				die("modo filtrado desconocido: [$tipo]");
				break;
		}
	}

//	$codigo = preg_replace('\s{2,}',' ', trim($codigo));

	if (is_array($salida)){
		$salida = join("",$salida);
	}


	return $salida;
}


function contarSignos($cadena){
	$num = 0;
	$num += contarCaracterCadena($cadena,".");
	$num += contarCaracterCadena($cadena,",");
	$num += contarCaracterCadena($cadena,"/");

	return $num;
}




function getResumenProducto($id_producto){

	$id_producto_s = sql($id_producto);

	$sql = "SELECT resumen FROM data_productos WHERE id_producto='$id_producto_s'";

	$row = queryrow($sql);

	return $row["resumen"];
}




/*

Compara dos cadenas, y produce unas estadisticas de comparacion.
 *
 */

function comparaTextosAbsolutos( $ocr_text, $datosDireccionDb, $modo="soft", $debug=false){
	global $cr;


	switch($modo){
		default:
		case "soft":
		$encontrado_soft = soft_text($ocr_text);
		$direccion_soft = soft_text($datosDireccionDb);
		break;
		case "animorfico":
		$encontrado_soft = animorfico_text($ocr_text);
		$direccion_soft = animorfico_text($datosDireccionDb);
		break;

		//int levenshtein  ( string $str1  , string $str2  )

	}



	$lineasEncontrado = split("\n",$encontrado_soft);
	$lineasBasedatos = split("\n",$direccion_soft);

	$maximoPesoGlobal = 0;
	$finalpesoGlobal = 0;
	$ajustadosTotal = 0;
	$numajustadosTotal = 0;

	$datosEncontrados = 0;//numero de palabras encontradas que coinciden  OCR <-> BD
	$longitudEncontrada = 0;//cuantos caracteres suponen el texto encontrado.
	$longitudBD = strlen($direccion_soft);


	$palabras_bd = count(split(" ",str_replace("\n"," ",$direccion_soft)));//cuenta palabras en BD
	$palabras_ocr = count(split(" ",str_replace("\n"," ",$encontrado_soft)));//cuenta palabras en OCR


	foreach($lineasEncontrado as $lineaOCR){
		$palabrasEncontrado = split(" ",$lineaOCR);

		$numLinea = 1;
		$maximoPeso = 0;
		foreach(  $lineasBasedatos as $lineaBD){
			$num = 1; //quantificador num de linea
			$base = 1; //es lineas 0 de la direccion
			$coincidencias = 0; //palabras acertadas
			$fallidas = 0;
			$maximoLocal = 0;
			$pesofinal = 0;

			foreach ($palabrasEncontrado as $palabra){
				if ( strlen($palabra)<2) continue;

				$pesopalabra = strlen($palabra);//

				$peso = ($pesopalabra/$num)/$numLinea; //cuanto mas a la derecha, menos peso.
					//cuanto mas abajo, menos peso
				$num += 0.02;

				if ( contiene($lineaBD,$palabra) ){
					$coincidencias++;
					$pesofinal += $peso;
					if($debug) echo "match: [$palabra],v:$pesopalabra/$num, p:$peso". $cr;
					$datosEncontrados ++;
					$longitudEncontrada += $pesopalabra;
				} else {
					$fallidas++;
					if($debug) echo "fallida: [$palabra]" . $cr;
				}

				$maximoPeso += $peso;
				$maximoLocal += $peso;
			}

			$numLinea += 0.4;//no progresa tan rapido, de modo que la segunda y tercera aun es relevante

			if($debug) echo "BaseDatos<br>";
			if($debug) echo "<div class='dato'>".var_export($lineaBD,true)  . "</div>";

			if($debug) echo "OCR<br>";
			if($debug) echo "<div class='dato'>". $lineaOCR . "</div>";

			if ($maximoLocal){
				$finalpesolocal = $pesofinal/$maximoLocal;
			}else
				$finalpesolocal = 0;

			if($debug) echo "Coincidencias: $coincidencias". $cr;
			if($debug) echo "Peso coincidencias: $pesofinal". $cr;
			if($debug) echo "Peso maximo local: $maximoLocal". $cr;
			if($debug) echo g("b","Peso reajustado local: $finalpesolocal"). $cr;
			if($debug) echo "--------------" . $cr;


			$maximoPesoGlobal += $maximoPeso;
			$finalpesoGlobal += $pesofinal;
			$ajustadosTotal += $finalpesolocal;
			$numajustadosTotal++;
		}

		if ($maximoPesoGlobal){
			$finalajustado = $finalpesoGlobal/$maximoPesoGlobal;
		}else
			$finalajustado = 0;

		$ajusteajustadototal = $ajustadosTotal / $numajustadosTotal;//

		if($debug) echo "TOTAL peso: $finalpesoGlobal". $cr;
		if($debug) echo "TOTAL peso reajustado: $finalajustado". $cr;
		if($debug) echo "TOTALES ajustes: $ajustadosTotal". $cr;
		if($debug) echo "TOTAL Num ajustes: $numajustadosTotal". $cr;
		if($debug) echo "TOTALES ajuste,ajustado: $ajusteajustadototal". $cr;


		if($debug) echo "--------------------------------". $cr;
	}

	if($debug) echo "FINAL peso: $finalpesoGlobal". $cr;
	if($debug) echo "FINAL peso reajustado: $finalajustado". $cr;
	if($debug) echo "FINALES ajustes: $ajustadosTotal". $cr;
	if($debug) echo "FINAL Num ajustes: $numajustadosTotal". $cr;
	if($debug) echo "FINALES ajuste,ajustado: $ajusteajustadototal". $cr;

	if($debug) echo "Coincidencias: $datosEncontrados palabras ". $cr;
	if($debug) echo "Coincidencias longitud:  $longitudEncontrada caracteres" . $cr;
	if($debug) echo "Texto bd longitud:  $longitudBD" . $cr;
	if($debug) echo "Palabras texto BD: $palabras_bd ". $cr;


	$leven = levenshtein  ( $direccion_soft , $encontrado_soft  );




	$razon1 = vvalido($datosEncontrados / $palabras_bd);
	$razon2 = vvalido($longitudEncontrada / $longitudBD);
	$razon3 = vvalido($ajusteajustadototal);

	if ($leven==0)
		$razon4 = 1;
	else
		$razon4 = vvalido(1/$leven);

	$oliver = similar_text( $direccion_soft , $encontrado_soft, $razon5 );

	$razon5 = vvalido($razon5/100);

	//Cada metodo es valorado con la misma fiabilidad y tenido en cuenta para calcular como de parecidas son las cadenas
	$fiabilidad = ($razon1 + $razon2 + $razon3 + $razon4 + $razon5)/5;

	$salida = array("palabras"=>$razon1, "longitud"=>$razon2, "peso"=>$razon3 , "mediametodos"=>$fiabilidad);

	return $salida;

}



?>