<?php




function reajustarHojas(){
	$sql = "SELECT * FROM categorias WHERE eliminado=0";

	$res = query($sql);

	while($row = Row($res)){
		$self_s = $row["id_categoria"];
		$data = queryrow("SELECT id_categoria FROM categorias WHERE id_padre='$self_s' AND eliminado=0");

		$esHoja = 1;

		if ($data["id_categoria"])
			$esHoja = 0;

		query("UPDATE categorias SET eshoja='$esHoja' WHERE id_categoria='$self_s'");
	}
}



/* --------------  */

/*
function func_txt($file="ejemplos/test.txt"){
	return file_get_contents($file);
}


function func_extraepdf($file=""){
	//sudo apt-get install poppler-utils
	$tmp = tempnam("/tmp", "FOO");

	$cmd = " /usr/bin/pdftotext -layout -q -nopgbrk ejemplos/prueba.pdf $tmp";
	$return = system($cmd);

	//if (!$return) {
	//	return "ERROR: no se pudo crear texto\ncmd: $cmd";
	//}

	return func_txt($tmp);
}

function func_extraedoc($file=""){

	//sudo apt-get install poppler-utils
	$tmp = tempnam("/tmp", "FOO");

	$cmd = " /usr/bin/antiword -i 1  ejemplos/prueba.doc > $tmp";
	$return = system($cmd);


	return func_txt($tmp);
}


function func_htm($file=""){
	//sudo apt-get install poppler-utils
	$tmp = tempnam("/tmp", "FOO");

	$cmd = " /usr/bin/lynx --dump ejemplos/ejemplo2.html > $tmp";
	$return = system($cmd);

	return func_txt($tmp);
}

*/

/* --------------  */


class contenido extends Cursor {

	function Usuario() {
		return $this;
	}

	function Load($id) {
		$id = CleanID($id);
		$this->setId($id);
		$this->LoadTable("contenidos", "id_contenido", $id);
		return $this->getResult();
	}

  	function setNombre($nombre) {

  	}

  	function getNombre() {
		return $this->get("nombre");
  	}

  	function Crea(){
		$this->setNombre(_("Nuevo contenido"));
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

		$sql = "INSERT INTO contenidos ( $listaKeys ) VALUES ( $listaValues )";

		$resultado = query($sql);

		if ($resultado){
		    $this->set("id_contenido",$UltimaInsercion,FORCE);
		}

		$this->ActualizarIndexado();

		return $resultado;
	}

	function ActualizarIndexado(){
		
		$this->Indexar();
	}



	function Modificacion () {

		$data = $this->export();

		$sql = CreaUpdateSimple($data,"contenidos","id_contenido",$this->get("id_contenido"));

		$res = query($sql);
		if (!$res) {
			$this->Error(__FILE__ . __LINE__ , "W: no actualizo contenido");
			return false;
		}


		$this->ActualizarIndexado();

		return true;
	}

	function getExtension_raw($name){		

		$file = basename($name);
		$info = pathinfo($file);

		$ext = strtolower($info["extension"]);

		return $ext;
	}

	function getExtension(){
		$name = $this->get("path_archive");

		return $this->getExtension_raw($name);
	}

	function getTipo(){
		return $this->get("tipo");

	}


	function Indexar(){

		$cuantosCaracteres = 8192;

		$self_s = $this->get('id_contenido');

		$row = queryrow("SELECT id_indice FROM indice WHERE id_contenido='$self_s' ");

		$id_indice_s = CleanID($row["id_indice"]);


		$func = getFileFunction($this->get("path_archivo"));

		$file = $this->getValidFile();

		$data = $func($file);

		$texto_s =  sql(substr($data["texto"], 0, $cuantosCaracteres));
		$id_contenido_s = $this->get("id_contenido");


		if ($id_indice){
			$sql = "UPDATE indice SET texto='$texto_s' WHERE id_indice='$id_indice_s'  ";
			query($sql);
		} else {

			$sql = "INSERT INTO indice (id_contenido,texto) VALUES ( '$id_contenido_s','$texto_s' )";
			query($sql);			
		}
	}


	function getValidFile(){

		$basePath = getParametro("basePath");
		$path = $basePath . $this->get("path_archivo");

		return $path;
	}


	function getContenido(){
		$file = $this->getValidFile();
		return file_get_contents($file);
	}

	function setContenido($data){
		$file = $this->getValidFile();
		return file_put_contents($file,$data);
	}

	function getRandomArticle(){

		$rdir = getRandomDir();

		$htmlfile =   $rdir["dirtxt"]. "articulo_". md5(time()) . ".html";
		
		$baseDir = getParametro("basePath");
		createPath($baseDir,$rdir["dirs"]);

		$this->set("path_archivo",$htmlfile);
		$this->setContenido(" ");
		
	}


	function regularTipo($nuevoFichero,$nohtml=true){
		$ext = $this->getExtension_raw($nuevoFichero);

		$ext = strtolower($ext);

		switch($ext){
			case "html":
			case "htm":
			case "asp":
			case "aspx":
				$tipo = ($nohtml)?"datos":"html";
				break;

			case "jpeg":
			case "jpg":
			case "png":
			case "gif":
				$tipo = "imagen";
				break;

			case "flv":
				$tipo = "video";
				break;
			case "pdf":
			case "ps":
				$tipo = "pdf";
				break;

			default:
				$tipo = "datos";
				break;
		}

		//error_log("nt:[".$ext."],para:[" . $nuevoFichero ."]");
		$this->set("tipo",$tipo);
	}
}


?>