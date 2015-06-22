<?php




function eliminarFichero($file){
	global $cr;
	echo time(). ": eliminado $file". $cr;
	unlink( $file );
}


function cmd_wrapper($cmd, $input='')
         {$proc=proc_open($cmd, array(0=>array('pipe', 'r'), 1=>array('pipe', 'w'), 2=>array('pipe', 'w')), $pipes);
          fwrite($pipes[0], $input);fclose($pipes[0]);
          $stdout=stream_get_contents($pipes[1]);fclose($pipes[1]);
          $stderr=stream_get_contents($pipes[2]);fclose($pipes[2]);
          $rtn=proc_close($proc);
          return array('stdout'=>$stdout,
                       'stderr'=>$stderr,
                       'return'=>$rtn
                      );
         }


function correYMuestra($cmd){
	global $cr;

	echo time(). ": Corriendo comando '$cmd' $cr";
	$salida = cmd_wrapper($cmd);

	echo "<div class='output'>";

	if ( strlen($salida["stdout"])>1){
		echo "<div class='stdout'><pre>";
		echo $salida["stdout"]. $cr;
		echo "</pre></div>";
	}
	if (strlen($salida["stderr"])>1){
		echo "<div class='stderr'><pre>".$salida["stderr"]. "</pre></div>";
	}
	
	echo "</div>";
}


function randomname(){
	return abs(intval(abs(rand()*90000)));
}



function calculaCalidadTexto($name, $target){
	$original = $target;

	$tofind = "BCDFGHJKLMNPQRSTVWXYZbcdfghjklmnpqrstvwxyz";
	$replac = "CCCCCCCCCCCCCCCCCCCCCccccccccccccccccccccc";
	$target  = strtr($target,$tofind,$replac);

	if(0){
		//Internacional
		$tofind = "AÀÁÂÃÄÅaàáâãäåOÒÓÔÕÖØòóôõöøEÈÉÊËeèéêëIÌÍÎÏiìíîïUÙÚÛÜuùúûü";
		$replac = "VVVVVVvvvvvvvvVVVVVVvvvvvvvVVVVVvvvvvVvVVVvvvvvVVVVVvvvvv";
	}else{
		//Español
		$tofind = "AÁaáOÓoóEÉeéIÍiíUÚuú";
		$replac = "VVvvVVvvVVvvVVvvVVvv";
	}

	$target  = strtr($target,$tofind,$replac);

    $target  = strtr($target," cv","pppp");//untested
    $target  = strtr($target," vc","ppp");//untested


	$target  = strtr($target," Cv","pppp");
	$target  = strtr($target," Vc","ppp");
	$target  = strtr($target,"cv","pp");

	$tofind = "0123456789";
	$replac = "NNNNNNNNNN";
	$target  = strtr($target,$tofind,$replac);

	$target = str_replace("NNNNNNN","ppppppp",$target);
	$target = str_replace("NNNNNN","pppppp",$target);
	$target = str_replace("NNNNN","ppppp",$target);
	$target = str_replace("NNNN","pppp",$target);
	$target = str_replace("NNN","ppp",$target);
	$target = str_replace("NN","p",$target);


	$lenorg = strlen($original);
	$nump  = substr_count($target,"p" );

	$q = $nump/($lenorg+1);

	/*
	echo "\n-----------\nStats\n";
	echo "Fichero: $name\n";
	echo "Longitud original: $lenorg ch\n";
	echo "Puntuacion:        $nump\n";
	echo "Calidad:           $q\n";
     */

	return $q; //calidad,  0.56=malo, 0.71=mediocre, 0.81 = bueno

}


function ExtraerTextoDePaginas($path_origin_pdf, $faxfiles,$resolution,$girado=0){

	foreach ($faxfiles as $file) {

		//nota: ignora los que no son resultados de un split.
		if (!strstr($file, "ocr_"))
			continue;

		if($debug) echo time(). ": fichero: ". $file .$cr;

		if ($girado)
			$rot =" -rotate \"$girado\" ";


		$cmd = "cd $path_origin_pdf/tmp; convert -type Grayscale -density $resolution $file -depth 8 $rot -density $resolution -quality 100  fx_$file ";
		correYMuestra($cmd);


		//if($debug) echo  time(). ": comando conversion: ". $cmd . $cr;

		//convert -type Grayscale -density 300 ./tmp/r180.tif -depth 8  -density 300 -quality 100  ./tmp/r180.tif

		$ran        = intval(rand()*900000);

		$cmd = " cd $path_origin_pdf/tmp ; tesseract fx_$file out-$ran -l spa ";
		correYMuestra($cmd);

		//if($debug) echo  time(). ": comando extraccion: ". $cmd . $cr;

		$outfile = NormalizarPath( $path_origin_pdf  . "/tmp/" ). "out-$ran.txt";

		// get fax content
		if ($content = file_get_contents($outfile)){
			$faxcontent .= $content;
		}

		//eliminarFichero($outfile);//ya no es necesario el TXT
		//eliminarFichero( NormalizarPath($path_origin_pdf . "/tmp/") . $file);//no es necesario el ocr_???.tif
	}

	return $faxcontent;
}



function ExtraeTextoPDF( $data){
	global $cr;
	$debug = true;

	if(0)
		$resolution = 50;//30 = para pruebas, insuficiente para extraer textos
    else
		$resolution = 300; //300 = modo de calidad, para produccion


	$pdf = $data["pdf"];
	$path_origin_pdf = $data["path_origin_pdf"];

	$file = randomname() . ".tif";

	$cmd = " cd $path_origin_pdf ; convert -type Grayscale -density $resolution $pdf -depth 8  -density $resolution -quality 100  ./tmp2/$file ";

	correYMuestra(  $cmd  );

	if($debug) echo time(). ": Conversion a TIFF: ". $cmd . $cr;

	$cmd =  "cd $path_origin_pdf ; tiffsplit ./tmp2/$file ./tmp/ocr_";
	correYMuestra(  $cmd  );
	if($debug) echo time(). ": Paginacion TIFF: ". $cmd . $cr;

	eliminarFichero( NormalizarPath($path_origin_pdf . "/tmp2/") . $file);//ya no es necesario el original.tif multipagina

	$basedir = NormalizarPath($path_origin_pdf .  "/tmp/");

	mkdir($basedir);

	if ( !is_dir($basedir) and $debug ){
		echo time(). ": basedir $basedir no es dir " . $cr;
	}

	if ( !is_dir($path_origin_pdf) and $debug  ){
		echo time(). ": path_origin_pdf $path_origin_pdf no es dir " . $cr;
	}

	// list the tiff files
	$faxfiles = scandir($basedir);

	if($debug) echo time(). ": escaneando $basedir  (". print_r($faxfiles,true) .")" . $cr;

	$faxcontent = "";


	$faxcontent = ExtraerTextoDePaginas($path_origin_pdf,$faxfiles,$resolution,0);

	$q = calculaCalidadTexto($file,$faxcontent);
	$qfinal = $q;

	$decente = 0.71;

	if ($q<$decente){ //menos de 0.71 es calidad insuficiente, probamos a girarlo

		$q_old = $q;

		echo time(). ": [$q] es calidad insuficiente, probamos a girarlo\n";
		$faxcontent2 = ExtraerTextoDePaginas($path_origin_pdf,$faxfiles,$resolution,180);

		$q = calculaCalidadTexto($file,$faxcontent2);

		if ($q>$q_old){ //parece que la version girada obtiene mas calidad, de modo que usamos la girada
			$faxcontent = $faxcontent2;
			$qfinal = $q;
		}
	}

	if ($q<$decente){ //menos de 0.71 es calidad insuficiente, probamos a girarlo

		$q_old = $q;

		echo time(). ": [$q] es calidad insuficiente, probamos a girarlo\n";
		$faxcontent2 = ExtraerTextoDePaginas($path_origin_pdf,$faxfiles,$resolution,90);

		$q = calculaCalidadTexto($file,$faxcontent2);

		if ($q>$q_old){ //parece que la version girada obtiene mas calidad, de modo que usamos la girada
			$faxcontent = $faxcontent2;
			$qfinal = $q;
		}
	}

	if ($q<$decente){ //menos de 0.71 es calidad insuficiente, probamos a girarlo

		$q_old = $q;

		echo time(). ": [$q] es calidad insuficiente, probamos a girarlo\n";
		$faxcontent2 = ExtraerTextoDePaginas($path_origin_pdf,$faxfiles,$resolution,-90);

		$q = calculaCalidadTexto($file,$faxcontent2);

		if ($q>$q_old){ //parece que la version girada obtiene mas calidad, de modo que usamos la girada
			$faxcontent = $faxcontent2;
			$qfinal = $q;
		}
	}

	echo time(). ": calidad es [$qfinal] " . $cr;

	return $faxcontent;
}



?>