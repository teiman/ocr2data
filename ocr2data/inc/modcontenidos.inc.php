<?php


function getRandomDir(){
	$key = rand();

	$tiny = base_convert($key, 10, 36);

	$len = strlen($tiny);
	$out = array();
	$out[] = date("Y");
	
	for($t=0;$t<$len;$t++){
		$out []= $tiny[$t] ;
	}

	$dir = implode($out,"/") . "/";

	$salida = array("dirtxt"=>$dir, "dirs"=>$out);



	return $salida;
}


function createPath($path="upload/",$dirs){

	$dircreate = $path;
	foreach ($dirs as $dir){

		$dircreate .= $dir . "/";

		$cmd = "mkdir $dircreate ";

		error_log("cmd:" . $cmd);

		system($cmd);
	}

}







?>