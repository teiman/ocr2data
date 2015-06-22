<?php

/**
 * fichero de imagen manipulable
 *
 * @author oscar
 */
class imagen {
	var $directorio;
	var $fichero;
	var $im;

	var $dx;
	var $dy;

	var $_conocemosSize;
	var $_cargado;
	var $_path;
	var $_fichero;

	function imagen($path, $fichero=false){
		$this->directorio = $path;
		$this->fichero = $fichero;
		$this->_conocemosSize = false;
		$this->dx = 0;
		$this->dy = 0;
		$this->_cargado = false;
		$this->_path = $path;
		$this->_fichero = $fichero;		
	}

	function carga(){
		if ($this->_cargado) return;

		$this->im = new Imagick($this->_path. $this->_fichero);
		$this->_cargado = true;
	}

	function pathImagen(){
		return $this->directorio . $this->fichero;
	}

	function readSize(){
        $this->carga();

		if ($this->_conocemosSize)
			return;

		$marca = "xxxyyyzzz";
		$cmd = 'identify -format "%[fx:w]'.$marca.'%[fx:h]" ' .escapeshellarg($this->_path . "/" . $this->fichero);
		
		$output = array();

		exec($cmd, $output);


		$str0 = $output[0];

		if (!(strpos($str0,$marca)>0)){
			return false;
		}

		$data = split($marca,$str0);

		//error_log("c:". count($data));

		$this->dx = $data[0];
		$this->dy = $data[1];

		error_log("i:x:". $this->dx.  ",y:". $this->dy);

		$this->_conocemosSize = true;
		return true;
	}


	function ocr_area($regiondata, $modo){
		global $rootdata;
		
		$pos_x = $regiondata["region_x"];
		$pos_y = $regiondata["region_y"];
		$w = $regiondata["region_w"];
		$h = $regiondata["region_h"];

		$serial = md5($this->_fichero);

		$out = $serial . "_" . $pos_x . "_". $pos_y . "_" . $w . "_". $h . ".tiff";
		$path = $rootdata . "tmp/";

		if (!file_exists($path. $out)){
			//echo "<b>OCA: Hay que crearlo!</b><br>";
			$this->carga();
			$this->cropImage($pos_x,$pos_y,$w,$h);
			$this->Save("tiff",$path, $out);
		} else {
			//echo "<b>OCA: Existe ($out)(".$this->_fichero.")</b><br>";
		}
		
		$ocr_text = $this->ocr_bloque($rootdata, $out, $modo);
		return $ocr_text;
	}


	function cropImage($pos_x,$pos_y,$w,$h){
		$this->carga();
		$this->im->cropImage($w,$h,$pos_x,$pos_y);
	}


	function Save($format,$path,$file){
		$this->carga();
		$this->im->setImageCompressionQuality(100);
		$this->im->setImageFormat($format);		
		$this->im->writeImage($path .$file);
	}


	function delete(){
		if (!$this->_cargado) return;
		
		$this->im->clear();
		$this->im->destroy();
	}


	function Cerrar(){
		if (!$this->_cargado) return;			

		$this->im->clear();
		$this->im->destroy();
	}


	function runCmd($cmd){
		$output = array();
		exec($cmd, $output);
		error_log("cmd:[$cmd]".var_export($output,true));
	}

	function ocr_bloque($path, $file="out2.tiff", $modo="normal"){
        
       
		$resolution = getParametro("ocr2data.resolucionocr");
        if(!$resolution) $resolution=300;

		$ran = intval(rand()*100000);
		$fileout = "out-$ran.tiff";
		$language = "spa";

        $forcelang = getParametro("ocr2data.lenguajeocr");
        if($forcelang)
            $language = $forcelang;
        

		$tipoComando = "normal";

		switch($modo){
			default:
			case "normal":
				$tipoComando = "normal";
				$language = "spa";
				break;
			case "numeros":
				$language = "num";
				$tipoComando = "num";
				break;
			case "numeroconsignos":
				$language = "num";
				$tipoComando = "num";
				break;
			case "fecha":
				$language = "num";
				$tipoComando = "num";
				break;
		}

		/* normaliza */
		$cmd = "convert -type Grayscale -density $resolution ".$path . "tmp/".$file." -depth 8 $rot -density $resolution -quality 100  ".$path."tmp/fx_$fileout ";
		$this->runCmd($cmd);		

		/* reconoce */		
		switch($tipoComando){
			case "normal":
			default:
				$cmd = "tesseract ".$path."tmp/fx_$fileout ".$path."tmp/out-$ran -l $language ";
                $cmdstr = getParametro("ocr2data.comandomotor");
                if($cmdstr)
                    $cmd = sprintf($cmdstr, $path,$fileout,$path,$ran,$language);

				error_log("modoescaneo: ($tipoComando)");
				break;
			case "num":
				$cmd = "tesseract ".$path."tmp/fx_$fileout ".$path."tmp/out-$ran nobatch digits";
                $cmdstr = getParametro("ocr2data.comandomotornum");
                if($cmdstr)
                    $cmd = sprintf($cmdstr, $path,$fileout,$path,$ran);
				error_log("modoescaneo: ($tipoComando)");
				break;
		}
		
		$this->runCmd($cmd);

		$ocrOutputFile = $path."tmp/out-$ran.txt";
		$txt = file_get_contents($ocrOutputFile);

		//Eliminamos temporales
		unlink($ocrOutputFile);		
		unlink($path."tmp/fx_" . $fileout );

		return $txt;
	}

	
	//paginas estaticas
	// [colores y tal de producto]
	// --elige talla y tal, y eso aparece en las notas de pedido
	// --caja/combo elige cuantos y le se compran de golpe

	function old_totiff(){
		
	}


}


?>