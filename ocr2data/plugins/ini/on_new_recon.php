<?php


$vistos_titulos = array();


function TituloIni($titulo){

    if (isset($vistos_titulo[$titulo]) ) {
        $n = $vistos_titulo[$titulo] + 1;
        
        $newtitulo = $titulo .  "_" . $n;
        $vistos_titulo[$newtitulo] = $n;
    
        return $newtitulo;
    }

    $vistos_titulo[$titulo] = 1;

    return $titulo . "_1";    
}


function ValueIni($value){
    $value = str_replace("\\","\\\\",$value);
    //$value = str_replace("\n","\\n",$value);
    $value = str_replace(array("\r\n", "\n", "\r"),'\\n',$value);

    return $value;
}


//Coger todos los datos reconocidos
$res = $this->getRegionesRecon();

//region_nombre

$data = array();

while($row = Row($res)){
    $data[ TituloIni($row["region_nombre"]) ] = ValueIni($row["cierto_text"]);
}


//Crear fichero ini en salida
{  
    //Crear path donde se guardan
    $pathout = getParametro("export_ini.path");    
    $base = getParametro("ocr2data.datapath");

    $outname = "recon_" . $this->get("recon_id") . ".ini";

    $filename = $base . $pathout . "/". $outname;

    //Guardar
    $cr = "\n";
    $str = "//Generador por OCR2DATA mediante el plugin exportar-ini" . $cr;

    foreach($data as $key=>$value){
        $str .= "$key = $value" . $cr;
    }    

    file_put_contents($filename,$str);

    if(0){
        echo "<xmp>";
        echo "Salvando como [$filename]\n";
        echo $str;
        echo "</xmp>";
    }
}














