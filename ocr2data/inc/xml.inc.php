<?php





function assocArrayToXML($root_element_name,$elemento,$ar) 
{ 
    $xml = new SimpleXMLElement("<{$root_element_name}></{$root_element_name}>"); 
    $f = create_function('$f,$c,$a,$t',' 
            foreach($a as $k=>$v) { 
                if(is_array($v)) { 
                    $ch=$c->addChild($k); 
                    $f($f,$ch,$v,$t); 
                } else { 
                    $c->addChild($k,$v); 
                } 
            }'); 
    $f($f,$xml,$ar,$elemento); 
    return $xml->asXML(); 
} 

function array2xml($array,$root,$element){
    $out = "<{$root}>\n";
    
    foreach($array as $k=>$v){
        if(is_array($v)){
            $out .= array2xml($v,$element,false);
        } else {

            $out .= "<{$k}>" . $v . "</{$k}>\n";
        }

    }

    $out .= "</{$root}>";
    return $out;
}



function toXML($raiz,$doc){
    return "<?xml version=\"1.0\" encoding=\"utf-8\" ?>" . "<{$raiz}>" .$doc . "</{$raiz}>";

}
