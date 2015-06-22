<?php


define("COMMAND_LYNX", "/usr/bin/lynx");
define("COMMAND_ANTIWORD", "/usr/bin/antiword");
define("COMMAND_PDFTOTEXT","/usr/bin/pdftotext");
define("COMMAND_STRINGS","/usr/bin/strings");


function func_txt($file="ejemplos/test.txt"){
	return array("texto"=>file_get_contents($file));
}


function func_extraepdf($file="ejemplos/prueba.pdf"){
	//sudo apt-get install poppler-utils
	$tmp = tempnam("/tmp", "FOO");

	$cmd = COMMAND_PDFTOTEXT . " -layout -q -nopgbrk $file $tmp";
	$return = system($cmd);
	
	$data = func_txt($tmp);
	return array("texto"=> $data["texto"]);
}

function func_extraedoc($file="ejemplos/prueba.doc"){

	//sudo apt-get install poppler-utils
	$tmp = tempnam("/tmp", "FOO");

	$cmd = COMMAND_ANTIWORD . " -i 1  $file > $tmp";
	$return = system($cmd);

	$data = func_txt($tmp);
	return array("texto"=> $data["texto"]);
}


function func_htm($file="ejemplos/ejemplo2.html"){
	//sudo apt-get install poppler-utils

	$tmp = tempnam("/tmp", "FOO");

	$cmd = COMMAND_LYNX . " --dump $file > $tmp";
	$return = system($cmd);

	$data = func_txt($tmp);
	return array("texto"=> $data["texto"]);
}


function func_binary($file="ejemplos/file.dat"){
	//sudo apt-get install poppler-utils

	$tmp = tempnam("/tmp", "FOO");

	$cmd = COMMAND_STRINGS . " $file > $tmp";
	$return = system($cmd);

	$data = func_txt($tmp);
	return array("texto"=> $data["texto"]);
}




function getFileFunction($name){

	$file = basename($name);
	$info = pathinfo($file);

	$ext = strtolower($info["extension"]);

	switch($ext){
		case "ps":
		case "pdf":
			return "func_extraepdf";
		case "html":
		case "htm":
		case "xhtml":
		case "xhtm":
		case "aspx":
		case "asp":
			return "func_htm";
		case "doc":
			return "func_extraedoc";
		default:
			return "func_binary";
	}
}




?>
