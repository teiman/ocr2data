<?php


die("obsoleto");

include("tool.php");



$macro = "INSERT INTO `congen`.`imagen` (
`imagen_id` ,
`imgpadre_id` ,
`fichero` ,
`estado` ,
`eliminado`
)
VALUES (
NULL , '0', '%s', 'pendiente', ''
)";

query("TRUNCATE TABLE imagen");


$sql = sprintf($macro, "img1.tiff");
query($sql);

$sql = sprintf($macro, "img2.tiff");
query($sql);

$sql = sprintf($macro, "img3.tiff");
query($sql);

$sql = sprintf($macro, "img4.tiff");
query($sql);


?>
