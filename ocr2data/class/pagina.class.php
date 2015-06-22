<?php

/**
 * Template de pagina
 *
 * @package ecomm-clases
 */

//include_once("class/patError.php");
include_once("class/patErrorManager.php");
include_once("class/patTemplate.php");

class Pagina extends patTemplate {


	function Inicia($modname,$pagina=false){
		global $page;//?? porque esto en lugar de $this

		if (!$pagina)
			$pagina = 'basica.html';

		$page->setOption( 'translationFolder', "translations" );
		$page->setOption( 'translationAutoCreate', true );
		//$page->setOption( 'lang',  $lang );

		$page->setRoot( 'templates' );

		//NOTA: activa el cache
		if(0)
		$page->useTemplateCache( 'File', array(
                                            'cacheFolder' => './templates/cache',
                                            'lifetime'    => 60*60,
                                            'filemode'    => 0644
                                        )
                        );

		$page->readTemplatesFromInput($pagina);
		$page->addVar('page', 'modname', $modname );

		$page->addVar("headers","versioncss",rand());
		$page->addVar("headers","modname",$modname);


		if ($pagina=="basica.txt" || $pagina=="central.txt"){
			$page->addVar("cabeza","nombreusuario",getSesionDato("user_nombreapellido") );
			$page->addVar("cabeza","id_user",getSesionDato("id_user") );
		}
	}


	function _($text){	
		return $text;
	}


	function configMenu($option){
		global $template;


		$this->addVar('page', 'menu_0_txt', $this->_("Listar") );
		$this->addVar('page', 'menu_0_url', $template["modname"] . ".php" );
		$this->addVar('page', 'menu_1_url', $template["modname"] . ".php?modo=alta" );

		$this->addVar('page', 'menu_2_url', "#" );
		
		$this->addVar("edicion", "cssbtnremove", "oculto");//lo quitamos de todos sitios

		switch($option){
				case "sololistar":
					$this->addVar('page', 'current0',"current" );
					$this->addVar('page', 'menu_1_css', "oculto" );
					$this->addVar('page', 'menu_2_css', "oculto" );
					break;
				case "listar":
					$this->addVar('page', 'current0', "current" );
					$this->addVar('page', 'menu_2_css', "oculto" );
					break;
				case "guardaralta":
					$this->addVar('page', 'current1', "current" );
					$this->addVar('page', 'menu_2_css', "oculto" );

					break;
				case "guardarcambios":
					$this->addVar('page', 'current2',"current" );
					break;
				default:
					$this->addVar('page', 'menu_2_css', "oculto" );
					
			// santa isabel..

// 10:00 curso.
// parpadea existing.  "Maria Angeles Sanz"
//

					break;
			}


	}




	function configNavegador( $min, $maxfilas,$numFilas ){
		global $template;

	//	if (!$numFilas) return;

		
		$numActivos = 0;
		$pagSiguiente = 0;
		$pagAnterior = 0;

		if ( $min >= $maxfilas ) {
			$pagAnterior = $min - $maxfilas;
			$numActivos++;
		}  else {
			$anteriorDisabled = "disabled='disabled'";
		}

		if  ($numFilas < $maxfilas) {
			$pagSiguiente = $min;
			$siguienteDisabled = "disabled='disabled'";
		} else {
			$numActivos++;
			$pagSiguiente = $min + $maxfilas;
		}


		if ( 0){
			if (!$numActivos) {
				//echo "SAle, porque no hay botones que activar";
				return;//no hay botones activos, asi que ocultamos el navegador, que no es necesario.
			}

			$this->setAttribute( 'navegador', 'src', 'navegador.txt' );


			$this->addVar( 'navegador', 'modname', $template["modname"] );

			$this->addVar( 'navegador', 'paganterior', $pagAnterior );
			$this->addVar( 'navegador', 'pagsiguiente', $pagSiguiente );

			$this->addVar( 'navegador', 'antdisabledhtml', $anteriorDisabled );
			$this->addVar( 'navegador', 'sigdisabledhtml', $siguienteDisabled );
		}else{
			$this->addVar( 'mininavegador', 'paganterior', $pagAnterior );
			$this->addVar( 'mininavegador', 'pagsiguiente', $pagSiguiente );

			if ($min<=0){
				$this->addVar( 'mininavegador', 'firstdisabledhtml', 'imagebotondesactivado');
			}

			if ($siguienteDisabled){
				$this->addVar( 'mininavegador', 'lastdisabledhtml', 'imagebotondesactivado');
			}

			if ($anteriorDisabled){
				$this->addVar( 'mininavegador', 'antdisabledhtml', 'imagebotondesactivado');
			}
			if ($siguienteDisabled){
				$this->addVar( 'mininavegador', 'sigdisabledhtml', 'imagebotondesactivado');
			}
		}
	}

	function Volcar(){
		header("Content-type: text/html; charset=UTF-8");
		$this->displayParsedTemplate();
	}



	function addArrayFromCursor( $subtemplate,&$cursor, $multiple ){

		if (!$multiple) return;

		if (!$cursor) return;//TODO: emitir un error

		foreach($multiple as $key){
			$this->addVar( $subtemplate, $key, $cursor->get($key)  );
		}

	}

	function ajustaMigas($cat_s){
		if ($cat_s==0){
			$this->addVar("page","ocultamigas","oculto");
		}

		$rows = getMigasdePan($cat_s);


		$this->addRows('migas', $rows );
	}



}


?>