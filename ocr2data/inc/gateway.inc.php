<?php

/*
 * Gateway para funcionamiento aislado de la plataforma (sin conectar con otra)
 */


function marcarEstadoComunicacion($recon_id,$estado){
  
    $recon_id_s = sql($recon_id);
    $estado_s = sql($estado); 

    $sql = "UPDATE recon SET estado='$estado_s' WHERE recon_id='$recon_id_s' ";
    query($sql);

    error_log("SQL:".$sql);
  
/*
    $id_estado = 12;
    
    switch($estado){
        case "enproceso":
        case "en proceso":
            $id_estado = "13";
            break;
        case "tramitado":
        case "Tramitado":
            $id_estado = "14";
            break;
        case "recibido":
        case "Recibido":
            $id_estado = "12";
            break;
    }


    $sql = "UPDATE communications SET id_status='$id_estado' WHERE id_comm='$id_comm'";
    query($sql);

    //echo $sql;
    */
}



function marcarIdCliente($recon_id, $id_cliente){
    /*
    $sql = "UPDATE communications SET id_contact='$id_cliente' WHERE id_comm='$id_comm'";
    query($sql);
    */
}

function marcarNumPedido($recon_id, $numpedido){
    /*
    $numpedido_s = sql($numpedido);
    $sql = "UPDATE communications SET codcom='$numpedido_s' WHERE id_comm='$id_comm'";
    query($sql);
    */
}




?>