<?

include("tool.php");
include_once("class/recon.class.php");
include_once("class/imagen.class.php");
include_once("class/regionrecon.class.php");
include_once("class/template.class.php");

include_once("inc/proceso.inc.php");
include_once("inc/libocr.php");

header('Content-type: text/html; charset=utf-8');

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

$file = $_REQUEST["path"];

$esCrearTemplate = ($_REQUEST["createmplate"]=="on");
$esAutoDetectarTemplate = ($_REQUEST["autodetectar"]=="on");

$template_name  = $_REQUEST["templatename"];
$id_comm        = $_REQUEST["id_comm"];
$origen_addr    = $_REQUEST["origen_addr"];



function crearOriginalDesdePDF($ini){
    comenta("Es necesario crear imagenes fuente desde pdf");
   
    $pdf = $ini["pathAbsolutoImagen"];

   
    $path_parts = pathinfo($pdf);
    
    $rndname = md5(rand());

    $finalname =  $ini['rootpath'] . "scan/". $rndname . ".tif";

    comenta("Crea y guarda version tiff");
    $cmd=    "convert  -density 200x200 {$pdf} -colorspace Gray -threshold 20% -channel all {$finalname}";
    correYMuestra($cmd);

    $visibleweb =  $ini['rootpath'] . "web/imagen_". $ini["id_comm"]. ".jpg";

    comenta("Crea y guarda version jpg para lector");
    $cmd = "convert -density 200x200 {$finalname} -colorspace Gray {$visibleweb}";
    correYMuestra($cmd);

    comenta("Crea y guarda version jpg para editor de templates");
    $visiblewebt =  $ini['rootpath'] . "scan/". $rndname. ".jpg";//TODO: scan no es un buen folder para esto, seria mejor web/
    $cmd = "convert -density 200x200 {$finalname} -colorspace Gray {$visiblewebt}";
    correYMuestra($cmd);

    return array("finalname"=>$finalname, 
        "finalpath"=>($ini['rootpath'] . "scan/"),
        "rndname"=>($rndname . ".tif"), 
        "visibleweb"=>$visibleweb, 
        "visiblewebt"=>$visiblewebt );
}



function crearOriginalDesdeTIF($ini){
    comenta("Es necesario crear imagenes fuente desde tif");
   
    $pdf = $ini["pathAbsolutoImagen"];
   
    $path_parts = pathinfo($pdf);
    
    $rndname = md5(rand());

    $finalname =  $ini['rootpath'] . "scan/". $rndname . ".tif";

    comenta("Crea y guarda version tiff");
    $cmd=    "convert  -density 200x200 {$pdf} -colorspace Gray -threshold 20% -channel all {$finalname}";
    correYMuestra($cmd);

    $visibleweb =  $ini['rootpath'] . "web/imagen_". $ini["id_comm"]. ".jpg";

    comenta("Crea y guarda version jpg para lector");
    $cmd = "convert -density 200x200 {$finalname} -colorspace Gray {$visibleweb}";
    correYMuestra($cmd);

    comenta("Crea y guarda version jpg para editor de templates");
    $visiblewebt =  $ini['rootpath'] . "scan/". $rndname. ".jpg";//TODO: scan no es un buen folder para esto, seria mejor web/
    $cmd = "convert -density 200x200 {$finalname} -colorspace Gray {$visiblewebt}";
    correYMuestra($cmd);

    return array("finalname"=>$finalname, 
        "finalpath"=>($ini['rootpath'] . "scan/"),
        "rndname"=>($rndname . ".tif"), 
        "visibleweb"=>$visibleweb, 
        "visiblewebt"=>$visiblewebt );
}


function executeimportini($ini){
    global $rootdata;

    $file = $ini["self"];
    $origen_addr = $ini["origen"];
    $id_comm = $ini["id_comm"];

    $ini["rootpath"] = $rootdata;
    $ini["inifile"] = $file;
    
    $base = dirname($file);
    $imagenPathAbsoluto = $base. "/". $ini["imagen"];
    
    $ini["basepath"] = $base . "/";
    $ini["pathAbsolutoImagen"] = $imagenPathAbsoluto;
    
    
    
    
    
//    echo var_dump($ini);
    
    if ($ini["imagentipo"]=="pdf") {
        $final =  crearOriginalDesdePDF($ini);
    } else
    if ($ini["imagentipo"]=="tiff" || $ini["imagentipo"]=="tif") {
        $final =  crearOriginalDesdeTIF($ini);
    } else {
        //de momento es el mas generico que tenemos
        $final =  crearOriginalDesdePDF($ini);
    }
    
    $recon = new recon();
    
    $recon->set("id_comm",$id_comm);
    $recon->set("estado","sin procesar");
    $recon->Alta();
    
    comenta("Imagen: [".$final["finalpath"]."][". $final["rndname"]."]");
    $img = new imagen( $final["finalpath"], $final["rndname"]);      
    
    $template = new template();
    
    comenta("Buscamos una template adecuada");
    $data = $template->findTemplateAdecuada($img,$origen_addr);
    
    if($data["id"]) {
            comenta("Encontramos y usamos tid:".$data["id"]);        
            $recon->updateTemplate($data["id"]);
    
            //$recon->set("estado","sin procesar");
            //$recon->Modificacion();
    } else {
            comenta("Creamos nueva template");
            $recon->newTemplate($template_name,$final["finalname"],$img,$origen_addr);
    }
        
    $recon_id = $recon->get("recon_id");
    
    $parts = pathinfo($final["finalname"]);
    
    $imagen = sql($parts["filename"] . ".". $parts["extension"]);
    
    $sql = "INSERT INTO imagen (recon_id,fichero,estado) VALUES ('$recon_id','$imagen','pendiente')";
    query($sql);
}


//---------------------------------------



comenta("Cargando '{$file}'");


$ini = parse_ini_file($file);
$ini["self"] = $file;

if(!$ini["origen"]){
    $ini["origen"] = $origen_addr;
}

$ini["id_comm"] = $id_comm;


comenta("Procesando");

executeimportini($ini);

