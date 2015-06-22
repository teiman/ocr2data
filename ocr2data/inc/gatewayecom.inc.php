<?php

/*
 * Ficheros de gateway con el programa Ecom
 */


function marcarEstadoComunicacion($id_comm,$estado){
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
}



function marcarIdCliente($id_comm, $id_cliente){
	$sql = "UPDATE communications SET id_contact='$id_cliente' WHERE id_comm='$id_comm'";
	query($sql);
}

function marcarNumPedido($id_comm, $numpedido){

	$numpedido_s = sql($numpedido);
	$sql = "UPDATE communications SET codcom='$numpedido_s' WHERE id_comm='$id_comm'";
	query($sql);
}




?>