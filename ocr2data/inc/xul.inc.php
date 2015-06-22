<?php

/**
 * Utilitarios para trabajar en xul
 *
 * @package ecomm-aux
 */


function StartXul($titulo="",$predata="",$css=false,$datepicker){
	global $esPruebas,$tituloSitio;

	header("Content-type: application/vnd.mozilla.xul+xml");

	//	header("Pragma: no-cache");
	//	header("Cache-control: no-cache");
	header("Content-languaje: es");
	
	$cr = "<"."?";
	$crf = "?".">";	
	
	$titulobreve = str_replace(" ","-",trim(strtolower($titulo)));
	
	echo $predata;	
	
	echo $cr .'xml version="1.0" encoding="UTF-8"'.$crf;
	echo $cr .'xml-stylesheet href="chrome://global/skin/" type="text/css"'.$crf;									
	echo $cr .'xml-stylesheet href="css/xul.css" type="text/css"'.$crf;

	if ($datepicker){
		echo str_replace("@","?","<@xul-overlay href='complementos/datepicker/datepicker-overlay.php' type='application/vnd.mozilla.xul+xml'@>");
	}

	if($css)	echo $cr . "xml-stylesheet href='data:text/css,$css'" . $crf;

?>
<window id="<?php echo $titulobreve ?>" title="<?php echo $tituloSitio ?>"
        xmlns:html="http://www.w3.org/1999/xhtml"        
        xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">        
        <script  type="application/x-javascript" >
        function $(nombre) { return document.getElementById(nombre); };                
        function id(nombre) { return document.getElementById(nombre); };


		function pc(entidad,atributo,valor){
			var xent;
			if (!(xent = $(entidad))) return;
			xent.setAttribute(atributo,valor);		
		}       
        </script>           
	<?php

}

function StartXulOverlay($titulo,$predata=""){

	header("Content-type: application/vnd.mozilla.xul+xml");

	$cr = "<?";
	$crf = "?>";	
	
	$titulobreve = str_replace(" ","-",trim(strtolower($titulo)));
	 echo $predata;
	 
	 echo '<?xml version="1.0" encoding="UTF-8"?>';
	 echo '<overlay
        xmlns:html="http://www.w3.org/1999/xhtml"        
        xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">';          	
}

function declareOverlay($url) {
	return "<" ."?xml-overlay href='$url'?"."/>";
}

function EndXul() {
	echo "</window>";	
}

function EndXulOverlay() {
	echo "</overlay>";	
}
  
 	
	
function xulMakeMenuOptions( $elementos ) {
	
	$out = "";
	foreach ($elementos as $key=>$value) {
			$out .= "<menuitem label='$key' oncommand=\"$value\"/>";
	}
	return $out;
}

function xulMakeMenuOptionsCommands( $elementos ) {
	
	$out = "";
	foreach ($elementos as $key=>$value) {
			$out .= "<menuitem command=\"$value\"/>";
	}
	return $out;
}

function xulMakePopup($nombre,$cuerpo){
	$nombreBreve = str_replace(" ","-",trim(strtolower($nombre)));
	
	$out = "<menupopup id='".$nombreBreve."-popup'>\n";
	return $out . $cuerpo . "</menupopup>\n";
}
	
function xulMakeMenu($nombre,$elementos){
	
	$nombreBreve = str_replace(" ","-",trim(strtolower($nombre)));
	$out = "<menu id='menu-".$nombreBreve."' label='$nombre'>";
	
	
	$cuerpo = xulMakePopup($nombre,xulMakeMenuOptions($elementos));

	return $out . $cuerpo . "</menu>\n";	
}	
	
function xulMakeMenuCommands($nombre,$elementos){
	
	$nombreBreve = str_replace(" ","-",trim(strtolower($nombre)));
	$out = "<menu id='menu-".$nombreBreve."' label='$nombre'>";
	
	
	$cuerpo = xulMakePopup($nombre,xulMakeMenuOptionsCommands($elementos));

	return $out . $cuerpo . "</menu>\n";	
}	
		
	


?>