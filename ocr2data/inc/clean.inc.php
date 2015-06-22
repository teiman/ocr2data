<?php

/**
 * Ayudas para sanitificar entradas, escapar variables
 *
 * @package ecomm-aux
 */


function CleanCOD($texto){
	$texto = str_replace(" ","",$texto);
	$texto = str_replace("COD","",$texto);
	return intval($texto)-1000;	
}



function iso($original){
	$str = iconv("iso-8859-1","UTF-8",$original);
	if (!$str)
		return $original;

	return $str;
}


function utf8($original){
	$str = iconv("UTF-8","iso-8859-1//IGNORE",$original);
	if (!$str) {
		return $original;
	}

	return $str;
}



function CleanParaWeb($valor){
	$str= htmlentities($valor,ENT_QUOTES,'UTF-8');
	if (!$str){
		$str= htmlentities($valor,ENT_QUOTES);
		if (!$str)
			$str = $valor;
	}
	return $str;
}

function CleanXSS($valor){
	return strip_tags($valor);
}


function CleanFechaFromDB($fecha){
	if (!$fecha)
		return "";
		
	if ($fecha=="--")
		return "";

	if ($fecha == "0000-00-00")
		return "";

	list($agno,$mes,$dia) = split("-",$fecha);
	return($dia . "-" . $mes . "-" . $agno);
}


function CleanDatetimeToFechaES($fecha){
	if (!$fecha)
		return "";
	if ($fecha=="--")
		return "";

	$datos = split(" ",$fecha);
	
	return CleanFechaFromDB($datos[0]);	
}



function CleanFechaES($fecha){
	if (!$fecha)
		return "";

	$fecha	= str_replace("/","-",$fecha);

	if ($fecha == "DD-MM-AAAA")
		return "";


	list($dia,$mes,$agno) = split("-",$fecha);



	$fecha =($agno . "-".$mes."-".$dia);

	if ($fecha=="--")
		return "";

	return $fecha;
}

function CleanCP($local){
	$local = CleanTo($local);
	$local = str_replace('"',"",$local);
	$local = trim($local);
	return strtoupper(trim(CleanTo($local))); 	
}




function Corta($str,$len,$padstr=".."){
	$reallen = strlen($str);
	$lenpad = strlen($padstr);

	if ($reallen+$lenpad <= $len)
		return $str;

	$newstr = substr($str, 0, $len-$lenpad) .$padstr;

	return $newstr;
}



function CleanInt($int){
	return intval($int,10);	
}

function CleanPass($pass){
	return CleanText($pass);	
}

function CleanLogin($login){
	return CleanText($login);	
}

function CleanXulLabel($label){

	$label = str_replace("\n","",$label);
	$label = str_replace("\r","",$label);
	$label = str_replace("\13","",$label);
	$label = str_replace("'"," ",$label);

	return $label;	
}

function CleanCB($cb){
	$ref = str_replace("\t","",$cb);
	$ref = trim($cb);
	$ref = str_replace(" ","",$ref);
	return $ref;	
}

function CleanRef($ref){
	return CleanReferencia($ref);
}

function CleanReferencia($ref){
	$ref = trim($ref);
	$ref = str_replace(" ","",$ref);	
	$ref = strtoupper($ref);
	
	return $ref;	
}

function CleanDinero($val){
	return CleanFloat($val);	
}

//Heavy, quita metacaracteres y espacios. Util para palabras
function CleanTo($text,$to="")  {
	$text = str_replace("'",$to,$text);
	$text = str_replace("\\",$to,$text);
	$text = str_replace("@",$to,$text);
	$text = str_replace("#",$to,$text);
	$text = str_replace(" ",$to,$text);
	$text = str_replace("\t",$to,$text);
	
	return $text;	
}


function CleanText($text){
	return CleanTo($text," ");	
}

function Clean($text){
	return CleanTo($text," ");
}



//Para limpiar nombres
function CleanPersonales($text,$to=" ")  {
	$text = str_replace("'",$to,$text);
	$text = str_replace("\\",$to,$text);
	$text = str_replace("#",$to,$text);
	$text = str_replace(" ",$to,$text);
	$text = str_replace("\t",$to,$text);	
	return $text;	
}


//Para identificadores 
function CleanID($IdentificadorNumerico) {
	return 	intval($IdentificadorNumerico);
}

//Para numeros positivos
function CleanIndexPositivo($num){
	$num = intval($num);
	if ($num<0)
		return - $num;
	return $num;	
}

//Convierte texto en html
function CleanToHtml($str) {	
	$str = htmlentities($str,ENT_QUOTES,'UTF-8'); 
	return str_replace("\n","<br>",$str);	
	//return nl2br($str);
}


function html($str){
	$out = 	htmlentities($str,ENT_QUOTES,'UTF-8'); ;
	if (!$out)	return $str;
	return $out;
}


function htmlLame($str){

	$str = str_replace("<","&lt;",$str);
	$str = str_replace(">","&lt;",$str);
	$str = str_replace('"',"&quot;",$str);
	$str = str_replace("'","&#180;",$str);

	return $str;
}

function entichar($chr){
	return "&#" . ord($chr) . ";";
}

function fichero($file){	//fichero sin paths, ni cosas raras.
	$file = str_replace("/","",$file);
	$file = str_replace("..","",$file);

	return $file;
}



function CleanHTMLtoBD($text) {
	$text = str_replace("#",entichar("#"),$text);
	$text = str_replace("'",entichar("'"),$text);	
	$text = str_replace("\\",entichar("\\"),$text);	
	$text = strip_tags($text,"<br>");
	//$text = str_replace("<br>","\n",$text);	
	return $text;		
}

function CleanBDtoTexto($text) {
	$text = str_replace("\\'","'",$text);
	return $text;	
}

function CleanDoMagicQuotes($text) {
	if( get_magic_quotes_gpc())
		return $text;
	return addslashes($text);
}

function CleanNL2BR($text){
	return nl2br($text);	
}

function CodificaScript($script) {
	return str_replace("'","@",$script);	
}

function DescodificaScript($script,$IdAlojamiento) {
	$IdAlojamiento = CleanID($IdAlojamiento);
	$sql = "SELECT DISTINCT URLFotoVirtual FROM dat_fotosvisitavirtual WHERE IdAlojamiento=$IdAlojamiento";
	
		//AddError(__FILE__ . __LINE__ , "Info: $sql");	
		
	$cambiados = array();
		
	$res = query($sql);
	
	if ($res) {
		while($row = Row($res)) {
			$img = $row["URLFotoVirtual"];
			
			if (!$cambiados[$img])
				$script = eregi_replace($img,"fullres/" . $img ,$script);
			$cambiados[$img] = 1;
		//	AddError(0,"Info: reemplazando $img");		
		}	
	} else {
		//AddError(__FILE__ . __LINE__ , "W: no le gusto $sql");	
	}
	
	//die("caput!");
	
	// Full Texts   	  IdFotoVirtual   	  IdAlojamiento   	  URLFotoVirtual
	 
	return str_replace("@","'",$script);
}

function Convertir2Textoplano($html) {
	$out = str_replace("<br>","\n",$html);
	$out = strip_tags($out);		
	return $out;
}

//Elimina los atributos del html
function SimplificaHTML($html){
	$out = "";
	//Interprete de HTML
	$a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
	foreach($a as $i=>$e)	{
		if($i%2==0)		{
			//Text
			$out .= $e;			
		}	else		{
			//Etiqueta			
			//$out .= "<$e>";
			
			//Extraer atributos			
			$tag=array_shift(explode(' ',$e));
			$out .= "<$tag>";
		}
	}
	
	return $out;
}

function CleanFloat($val) {	
	$val = str_replace(",", ".", $val );
	return (float)$val;	
}

function Convertir2WebCSS($html,$titulo=""){

$begin =<<<HEREDOC
<html><head>
<style type="text/css">
<!--
%CUERPOCSS%
//-->
</style>
<title>$titulo</title>
</head>
<body lang='es'>
HEREDOC;
	$css = CargarContenidoFichero("trv.css");
	
	$begin = str_replace("%CUERPOCSS%",$css,$begin);	
	
	$end = "</body></html>";
	return $begin . $html . $end;
}

//Para localizadores 
function CleanLocalizador($local) {
	return trim($local); 	
}

//Para DNI
function CleanDNI($local) {
	$local = trim($local);
	return strtoupper(trim(CleanTo($local))); 	
}


function CleanUrl($url){
	$url = str_replace("'","",$url);
	$url = trim($url);
	return $url;	
}

/*
function CleanCP($cp){
	$cp = trim($cp);
	return $cp;
}*/


function sql($dato){
	return CleanRealMysql($dato);
}

function CleanRealMysql($dato,$quitacomilla=false){
	global  $link;
	
	if (!$link){
		//NOTA:
		//  mysql real escape necesita exista una conexion,
		// ..por eso si no hay ninguna establecida, la abrimos. 
		forceconnect();
	}
		
	if ($quitacomilla)
		$dato = str_replace("'"," ",$dato);
	$dato_s = mysql_real_escape_string($dato);
	return $dato_s;
}

function CleanNif($nif){
	return CleanDNI($nif);	
}

function CleanEmail($correo){
	return CleanCorreo($correo);	
}

function CleanCC($cc){
	$cc = trim($cc);
	$cc = str_replace(" ","",$cc);
	return $cc;	
}


function CleanCorreo($correo){
	$correo = trim($correo);
	$correo = str_replace(" ","",$correo);
	return $correo;
}

function esCorreoValido($correo){
	$correo = CleanCorreo($correo);
	list($usuario,$host) = split("\@",$correo);

	$len = strlen($usuario);
	if ($len<1)	return false;
	$len = strlen($host);
	if ($len<1)	return false;
	return true;
}

function CleanTelefono($tel){
	$tel = trim($tel);
	$tel = str_replace(" ","",$tel);
	return $tel;	
}


function esTelefonoValido($tel){

	if (!$tel or $tel=="")
		return false;		
			
	$len = strlen($tel);
	if ($len<6)	return false;
	
	return true;
}


function FormatMoney($val) {
	$val = CleanDinero($val);
	//return htmlentities(money_format('%.2n $euro;', $val),ENT_QUOTES,'ISO-8859-15');
	//return money_format('%.2n &euro;', $val);
	return number_format($val, 2, ',', ""). " &euro;";
}

function FormatUnits($val) {
	return $val . " u.";	
}


if(function_exists("iconv")) {
	function iso2utf($text) {	
		return iconv("ISO-8859-1","UTF8",$text);
	}
	function utf8iso($text){
		return iconv("UTF8","ISO-8859-1//TRANSLIT",$text);		
	}	
	
} else {
	//TODO: buscar alternativa que no sea lenta
	function iso2utf($text) {	
		return $text;
	}
	function utf8iso($text){
		return $text;		
	}			
}


function CleanNombre($nombre){	
	return $nombre;	
}



function CleanParaXul($texto){
	//global $entHtml, $entXML;
	$entHtml 	= Array( '&aacute;', '&Aacute;', '&acirc;', '&Acirc;', '&agrave;', '&Agrave;', '&aring;', '&Aring;', '&atilde;', '&Atilde;', '&auml;', '&Auml;', '&aelig;', '&AElig;', '&ccedil;', '&Ccedil;', '&eth;', '&ETH;', '&eacute;', '&Eacute;', '&ecirc;', '&Ecirc;', '&egrave;', '&Egrave;', '&euml;', '&Euml;', '&iacute;', '&Iacute;', '&icirc;', '&Icirc;', '&igrave;', '&Igrave;', '&iuml;', '&Iuml;', '&ntilde;', '&Ntilde;', '&oacute;', '&Oacute;', '&ocirc;', '&Ocirc;', '&ograve;', '&Ograve;', '&oslash;', '&Oslash;', '&otilde;', '&Otilde;', '&ouml;', '&Ouml;', '&szlig;', '&thorn;', '&THORN;', '&uacute;', '&Uacute;', '&ucirc;', '&Ucirc;', '&ugrave;', '&Ugrave;', '&uuml;', '&Uuml;', '&yacute;', '&Yacute;', '&yuml;', '&half;', '&frac12;', '&frac14;', '&frac34;', '&frac18;', '&frac38;', '&frac58;', '&frac78;', '&sup1;', '&sup2;', '&sup3;', '&plus;', '&plusmn;', '&equals;', '&gt;', '&divide;', '&times;', '&curren;', '&pound;', '&dollar;', '&cent;', '&yen;', '&num;', '&percnt;', '&ast;', '&commat;', '&lsqb;', '&bsol;', '&rsqb;', '&lcub;', '&horbar;', '&verbar;', '&rcub;', '&micro;', '&ohm;', '&deg;', '&ordm;', '&ordf;', '&sect;', '&para;', '&middot;', '&larr;', '&rarr;', '&uarr;', '&darr;', '&copy;', '&reg;', '&trade;', '&brvbar;', '&not;', '&sung;', '&excl;', '&iexcl;', '&quot;', '&apos;', '&lpar;', '&rpar;', '&comma;', '&lowbar;', '&hyphen;', '&period;', '&sol;', '&colon;', '&semi;', '&quest;', '&iquest;', '&laquo;', '&raquo;', '&lsquo;', '&rsquo;', '&ldquo;', '&rdquo;', '&nbsp;', '&shy;', '&emsp;', '&ensp;', '&emsp13;', '&emsp14;', '&numsp;', '&puncsp;', '&thinsp;', '&hairsp;', '&mdash;', '&ndash;', '&dash;', '&blank;', '&hellip;', '&nldr;', '&frac13;', '&frac23;', '&frac15;', '&frac25;', '&frac35;', '&frac45;', '&frac16;', '&frac56;', '&incare;', '&block;', '&uhblk;', '&lhblk;', '&blk14;', '&blk12;', '&blk34;', '&marker;', '&cir;', '&squ;', '&rect;', '&utri;', '&dtri;', '&star;', '&bull;', '&squf;', '&utrif;', '&dtrif;', '&ltrif;', '&rtrif;', '&clubs;', '&diams;', '&hearts;', '&spades;', '&malt;', '&dagger;', '&Dagger;', '&check;', '&cross;', '&sharp;', '&flat;', '&male;', '&female;', '&phone;', '&telrec;', '&copysr;', '&caret;', '&lsquor;', '&ldquor;', '&fflig;', '&filig;', '&ffilig;', '&ffllig;', '&fllig;', '&mldr;', '&rdquor;', '&rsquor;', '&vellip;', '&hybull;', '&loz;', '&lozf;', '&ltri;', '&rtri;', '&starf;', '&natur;', '&rx;', '&sext;', '&target;', '&dlcrop;', '&drcrop;', '&ulcrop;', '&urcrop;', '&aacute;', '&Aacute;', '&acirc;', '&Acirc;', '&agrave;', '&Agrave;', '&aring;', '&Aring;', '&atilde;', '&Atilde;', '&auml;', '&Auml;', '&aelig;', '&AElig;', '&ccedil;', '&Ccedil;', '&eth;', '&ETH;', '&eacute;', '&Eacute;', '&ecirc;', '&Ecirc;', '&egrave;', '&Egrave;', '&euml;', '&Euml;', '&iacute;', '&Iacute;', '&icirc;', '&Icirc;', '&igrave;', '&Igrave;', '&iuml;', '&Iuml;', '&ntilde;', '&Ntilde;', '&oacute;', '&Oacute;', '&ocirc;', '&Ocirc;', '&ograve;', '&Ograve;', '&oslash;', '&Oslash;', '&otilde;', '&Otilde;', '&ouml;', '&Ouml;', '&szlig;', '&thorn;', '&THORN;', '&uacute;', '&Uacute;', '&ucirc;', '&Ucirc;', '&ugrave;', '&Ugrave;', '&uuml;', '&Uuml;', '&yacute;', '&Yacute;', '&yuml;', '&abreve;', '&Abreve;', '&amacr;', '&Amacr;', '&aogon;', '&Aogon;', '&cacute;', '&Cacute;', '&ccaron;', '&Ccaron;', '&ccirc;', '&Ccirc;', '&cdot;', '&Cdot;', '&dcaron;', '&Dcaron;', '&dstrok;', '&Dstrok;', '&ecaron;', '&Ecaron;', '&edot;', '&Edot;', '&emacr;', '&Emacr;', '&eogon;', '&Eogon;', '&gacute;', '&gbreve;', '&Gbreve;', '&Gcedil;', '&gcirc;', '&Gcirc;', '&gdot;', '&Gdot;', '&hcirc;', '&Hcirc;', '&hstrok;', '&Hstrok;', '&Idot;', '&Imacr;', '&imacr;', '&ijlig;', '&IJlig;', '&inodot;', '&iogon;', '&Iogon;', '&itilde;', '&Itilde;', '&jcirc;', '&Jcirc;', '&kcedil;', '&Kcedil;', '&kgreen;', '&lacute;', '&Lacute;', '&lcaron;', '&Lcaron;', '&lcedil;', '&Lcedil;', '&lmidot;', '&Lmidot;', '&lstrok;', '&Lstrok;', '&nacute;', '&Nacute;', '&eng;', '&ENG;', '&napos;', '&ncaron;', '&Ncaron;', '&ncedil;', '&Ncedil;', '&odblac;', '&Odblac;', '&Omacr;', '&omacr;', '&oelig;', '&OElig;', '&racute;', '&Racute;', '&rcaron;', '&Rcaron;', '&rcedil;', '&Rcedil;', '&sacute;', '&Sacute;', '&scaron;', '&Scaron;', '&scedil;', '&Scedil;', '&scirc;', '&Scirc;', '&tcaron;', '&Tcaron;', '&tcedil;', '&Tcedil;', '&tstrok;', '&Tstrok;', '&ubreve;', '&Ubreve;', '&udblac;', '&Udblac;', '&umacr;', '&Umacr;', '&uogon;', '&Uogon;', '&uring;', '&Uring;', '&utilde;', '&Utilde;', '&wcirc;', '&Wcirc;', '&ycirc;', '&Ycirc;', '&Yuml;', '&zacute;', '&Zacute;', '&zcaron;', '&Zcaron;', '&zdot;', '&Zdot;', '&half;', '&frac12;', '&frac14;', '&frac34;', '&frac18;', '&frac38;', '&frac58;', '&frac78;', '&sup1;', '&sup2;', '&sup3;', '&plus;', '&plusmn;', '&equals;', '&gt;', '&divide;', '&times;', '&curren;', '&pound;', '&dollar;', '&cent;', '&yen;', '&num;', '&percnt;', '&ast;', '&commat;', '&lsqb;', '&bsol;', '&rsqb;', '&lcub;', '&horbar;', '&verbar;', '&rcub;', '&micro;', '&ohm;', '&deg;', '&ordm;', '&ordf;', '&sect;', '&para;', '&middot;', '&larr;', '&rarr;', '&uarr;', '&darr;', '&copy;', '&reg;', '&trade;', '&brvbar;', '&not;', '&sung;', '&excl;', '&iexcl;', '&quot;', '&apos;', '&lpar;', '&rpar;', '&comma;', '&lowbar;', '&hyphen;', '&period;', '&sol;', '&colon;', '&semi;', '&quest;', '&iquest;', '&laquo;', '&raquo;', '&lsquo;', '&rsquo;', '&ldquo;', '&rdquo;', '&nbsp;', '&shy;', '&emsp;', '&ensp;', '&emsp13;', '&emsp14;', '&numsp;', '&puncsp;', '&thinsp;', '&hairsp;', '&mdash;', '&ndash;', '&dash;', '&blank;', '&hellip;', '&nldr;', '&frac13;', '&frac23;', '&frac15;', '&frac25;', '&frac35;', '&frac45;', '&frac16;', '&frac56;', '&incare;', '&block;', '&uhblk;', '&lhblk;', '&blk14;', '&blk12;', '&blk34;', '&marker;', '&cir;', '&squ;', '&rect;', '&utri;', '&dtri;', '&star;', '&bull;', '&squf;', '&utrif;', '&dtrif;', '&ltrif;', '&rtrif;', '&clubs;', '&diams;', '&hearts;', '&spades;', '&malt;', '&dagger;', '&Dagger;', '&check;', '&cross;', '&sharp;', '&flat;', '&male;', '&female;', '&phone;', '&telrec;', '&copysr;', '&caret;', '&lsquor;', '&ldquor;', '&fflig;', '&filig;', '&ffilig;', '&ffllig;', '&fllig;', '&mldr;', '&rdquor;', '&rsquor;', '&vellip;', '&hybull;', '&loz;', '&lozf;', '&ltri;', '&rtri;', '&starf;', '&natur;', '&rx;', '&sext;', '&target;', '&dlcrop;', '&drcrop;', '&ulcrop;', '&urcrop;');
	$entXML		= Array( '&#x00E1;', '&#x00C1;', '&#x00E2;', '&#x00C2;', '&#x00E0;', '&#x00C0;', '&#x00E5;', '&#x00C5;', '&#x00E3;', '&#x00C3;', '&#x00E4;', '&#x00C4;', '&#x00E6;', '&#x00C6;', '&#x00E7;', '&#x00C7;', '&#x00F0;', '&#x00D0;', '&#x00E9;', '&#x00C9;', '&#x00EA;', '&#x00CA;', '&#x00E8;', '&#x00C8;', '&#x00EB;', '&#x00CB;', '&#x00ED;', '&#x00CD;', '&#x00EE;', '&#x00CE;', '&#x00EC;', '&#x00CC;', '&#x00EF;', '&#x00CF;', '&#x00F1;', '&#x00D1;', '&#x00F3;', '&#x00D3;', '&#x00F4;', '&#x00D4;', '&#x00F2;', '&#x00D2;', '&#x00F8;', '&#x00D8;', '&#x00F5;', '&#x00D5;', '&#x00F6;', '&#x00D6;', '&#x00DF;', '&#x00FE;', '&#x00DE;', '&#x00FA;', '&#x00DA;', '&#x00FB;', '&#x00DB;', '&#x00F9;', '&#x00D9;', '&#x00FC;', '&#x00DC;', '&#x00FD;', '&#x00DD;', '&#x00FF;', '&#x00BD;', '&#x00BD;', '&#x00BC;', '&#x00BE;', '&#x215B;', '&#x215C;', '&#x215D;', '&#x215E;', '&#x00B9;', '&#x00B2;', '&#x00B3;', '&#x002B;', '&#x00B1;', '&#x003D;', '&#x003E;', '&#x00F7;', '&#x00D7;', '&#x00A4;', '&#x00A3;', '&#x0024;', '&#x00A2;', '&#x00A5;', '&#x0023;', '&#x0025;', '&#x2217;', '&#x0040;', '&#x005B;', '&#x005C;', '&#x005D;', '&#x007B;', '&#x2015;', '&#x007C;', '&#x007D;', '&#x00B5;', '&#x2126;', '&#x00B0;', '&#x00BA;', '&#x00AA;', '&#x00A7;', '&#x00B6;', '&#x00B7;', '&#x2190;', '&#x2192;', '&#x2191;', '&#x2193;', '&#x00A9;', '&#x00AE;', '&#x2122;', '&#x00A6;', '&#x00AC;', '&#x2669;', '&#x0021;', '&#x00A1;', '&#x0022;', '&#x0027;', '&#x0028;', '&#x0029;', '&#x002C;', '&#x005F;', '&#x2010;', '&#x002E;', '&#x002F;', '&#x003A;', '&#x003B;', '&#x003F;', '&#x00BF;', '&#x00AB;', '&#x00BB;', '&#x2018;', '&#x2019;', '&#x201C;', '&#x201D;', '&#x00A0;', '&#x00AD;', '&#x2003;', '&#x2002;', '&#x2004;', '&#x2005;', '&#x2007;', '&#x2008;', '&#x2009;', '&#x200A;', '&#x2014;', '&#x2013;', '&#x2010;', '&#x2423;', '&#x2026;', '&#x2025;', '&#x2153;', '&#x2154;', '&#x2155;', '&#x2156;', '&#x2157;', '&#x2158;', '&#x2159;', '&#x215A;', '&#x2105;', '&#x2588;', '&#x2580;', '&#x2584;', '&#x2591;', '&#x2592;', '&#x2593;', '&#x25AE;', '&#x25CB;', '&#x25A1;', '&#x25AD;', '&#x25B5;', '&#x25BF;', '&#x22C6;', '&#x2022;', '&#x25AA;', '&#x25B4;', '&#x25BE;', '&#x25C2;', '&#x25B8;', '&#x2663;', '&#x2666;', '&#x2665;', '&#x2660;', '&#x2720;', '&#x2020;', '&#x2021;', '&#x2713;', '&#x2717;', '&#x266F;', '&#x266D;', '&#x2642;', '&#x2640;', '&#x260E;', '&#x2315;', '&#x2117;', '&#x2041;', '&#x201A;', '&#x201E;', '&#xFB00;', '&#xFB01;', '&#xFB03;', '&#xFB04;', '&#xFB02;', '&#x2026;', '&#x201C;', '&#x2018;', '&#x22EE;', '&#x2043;', '&#x25CA;', '&#x2726;', '&#x25C3;', '&#x25B9;', '&#x2605;', '&#x266E;', '&#x211E;', '&#x2736;', '&#x2316;', '&#x230D;', '&#x230C;', '&#x230F;', '&#x230E;', '&#x00E1;', '&#x00C1;', '&#x00E2;', '&#x00C2;', '&#x00E0;', '&#x00C0;', '&#x00E5;', '&#x00C5;', '&#x00E3;', '&#x00C3;', '&#x00E4;', '&#x00C4;', '&#x00E6;', '&#x00C6;', '&#x00E7;', '&#x00C7;', '&#x00F0;', '&#x00D0;', '&#x00E9;', '&#x00C9;', '&#x00EA;', '&#x00CA;', '&#x00E8;', '&#x00C8;', '&#x00EB;', '&#x00CB;', '&#x00ED;', '&#x00CD;', '&#x00EE;', '&#x00CE;', '&#x00EC;', '&#x00CC;', '&#x00EF;', '&#x00CF;', '&#x00F1;', '&#x00D1;', '&#x00F3;', '&#x00D3;', '&#x00F4;', '&#x00D4;', '&#x00F2;', '&#x00D2;', '&#x00F8;', '&#x00D8;', '&#x00F5;', '&#x00D5;', '&#x00F6;', '&#x00D6;', '&#x00DF;', '&#x00FE;', '&#x00DE;', '&#x00FA;', '&#x00DA;', '&#x00FB;', '&#x00DB;', '&#x00F9;', '&#x00D9;', '&#x00FC;', '&#x00DC;', '&#x00FD;', '&#x00DD;', '&#x00FF;', '&#x0103;', '&#x0102;', '&#x0101;', '&#x0100;', '&#x0105;', '&#x0104;', '&#x0107;', '&#x0106;', '&#x010D;', '&#x010C;', '&#x0109;', '&#x0108;', '&#x010B;', '&#x010A;', '&#x010F;', '&#x010E;', '&#x0111;', '&#x0110;', '&#x011B;', '&#x011A;', '&#x0117;', '&#x0116;', '&#x0113;', '&#x0112;', '&#x0119;', '&#x0118;', '&#x01F5;', '&#x011F;', '&#x011E;', '&#x0122;', '&#x011D;', '&#x011C;', '&#x0121;', '&#x0120;', '&#x0125;', '&#x0124;', '&#x0127;', '&#x0126;', '&#x0130;', '&#x012A;', '&#x012B;', '&#x0133;', '&#x0132;', '&#x0131;', '&#x012F;', '&#x012E;', '&#x0129;', '&#x0128;', '&#x0135;', '&#x0134;', '&#x0137;', '&#x0136;', '&#x0138;', '&#x013A;', '&#x0139;', '&#x013E;', '&#x013D;', '&#x013C;', '&#x013B;', '&#x0140;', '&#x013F;', '&#x0142;', '&#x0141;', '&#x0144;', '&#x0143;', '&#x014B;', '&#x014A;', '&#x0149;', '&#x0148;', '&#x0147;', '&#x0146;', '&#x0145;', '&#x0151;', '&#x0150;', '&#x014C;', '&#x014D;', '&#x0153;', '&#x0152;', '&#x0155;', '&#x0154;', '&#x0159;', '&#x0158;', '&#x0157;', '&#x0156;', '&#x015B;', '&#x015A;', '&#x0161;', '&#x0160;', '&#x015F;', '&#x015E;', '&#x015D;', '&#x015C;', '&#x0165;', '&#x0164;', '&#x0163;', '&#x0162;', '&#x0167;', '&#x0166;', '&#x016D;', '&#x016C;', '&#x0171;', '&#x0170;', '&#x016B;', '&#x016A;', '&#x0173;', '&#x0172;', '&#x016F;', '&#x016E;', '&#x0169;', '&#x0168;', '&#x0175;', '&#x0174;', '&#x0177;', '&#x0176;', '&#x0178;', '&#x017A;', '&#x0179;', '&#x017E;', '&#x017D;', '&#x017C;', '&#x017B;', '&#x00BD;', '&#x00BD;', '&#x00BC;', '&#x00BE;', '&#x215B;', '&#x215C;', '&#x215D;', '&#x215E;', '&#x00B9;', '&#x00B2;', '&#x00B3;', '&#x002B;', '&#x00B1;', '&#x003D;', '&#x003E;', '&#x00F7;', '&#x00D7;', '&#x00A4;', '&#x00A3;', '&#x0024;', '&#x00A2;', '&#x00A5;', '&#x0023;', '&#x0025;', '&#x2217;', '&#x0040;', '&#x005B;', '&#x005C;', '&#x005D;', '&#x007B;', '&#x2015;', '&#x007C;', '&#x007D;', '&#x00B5;', '&#x2126;', '&#x00B0;', '&#x00BA;', '&#x00AA;', '&#x00A7;', '&#x00B6;', '&#x00B7;', '&#x2190;', '&#x2192;', '&#x2191;', '&#x2193;', '&#x00A9;', '&#x00AE;', '&#x2122;', '&#x00A6;', '&#x00AC;', '&#x2669;', '&#x0021;', '&#x00A1;', '&#x0022;', '&#x0027;', '&#x0028;', '&#x0029;', '&#x002C;', '&#x005F;', '&#x2010;', '&#x002E;', '&#x002F;', '&#x003A;', '&#x003B;', '&#x003F;', '&#x00BF;', '&#x00AB;', '&#x00BB;', '&#x2018;', '&#x2019;', '&#x201C;', '&#x201D;', '&#x00A0;', '&#x00AD;', '&#x2003;', '&#x2002;', '&#x2004;', '&#x2005;', '&#x2007;', '&#x2008;', '&#x2009;', '&#x200A;', '&#x2014;', '&#x2013;', '&#x2010;', '&#x2423;', '&#x2026;', '&#x2025;', '&#x2153;', '&#x2154;', '&#x2155;', '&#x2156;', '&#x2157;', '&#x2158;', '&#x2159;', '&#x215A;', '&#x2105;', '&#x2588;', '&#x2580;', '&#x2584;', '&#x2591;', '&#x2592;', '&#x2593;', '&#x25AE;', '&#x25CB;', '&#x25A1;', '&#x25AD;', '&#x25B5;', '&#x25BF;', '&#x22C6;', '&#x2022;', '&#x25AA;', '&#x25B4;', '&#x25BE;', '&#x25C2;', '&#x25B8;', '&#x2663;', '&#x2666;', '&#x2665;', '&#x2660;', '&#x2720;', '&#x2020;', '&#x2021;', '&#x2713;', '&#x2717;', '&#x266F;', '&#x266D;', '&#x2642;', '&#x2640;', '&#x260E;', '&#x2315;', '&#x2117;', '&#x2041;', '&#x201A;', '&#x201E;', '&#xFB00;', '&#xFB01;', '&#xFB03;', '&#xFB04;', '&#xFB02;', '&#x2026;', '&#x201C;', '&#x2018;', '&#x22EE;', '&#x2043;', '&#x25CA;', '&#x2726;', '&#x25C3;', '&#x25B9;', '&#x2605;', '&#x266E;', '&#x211E;', '&#x2736;', '&#x2316;', '&#x230D;', '&#x230C;', '&#x230F;', '&#x230E;');


	$texto = htmlentities($texto, ENT_QUOTES);
	$texto = str_replace($entHtml, $entXML, $texto);
	
	return $texto;	
}



function getExtension($name){
	$file =  basename($name);
	$info = pathinfo($file);
	$ext = strtolower($info["extension"]);

	return $ext;
}




?>