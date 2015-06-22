<?php


include("tool.php");

$id_comm = CleanID($_REQUEST["id_comm"]);




?>
<HTML>
<HEAD>
<TITLE>Ecomm :: OCR2DATA</TITLE>
	<script type="text/javascript" src="js/jquery-1.4.2.js"></script>

</HEAD>
<script>

	var Framebook = {};
	Framebook.actual=0;
	Framebook.selffocus0 = false;
	Framebook.selffocus1 = false;

	Framebook.id_abrir = <?php echo $id_comm; ?>;

	//alert("bar");
	$(function() {
		//alert("foo");
		Framebook.frame0 = window.open('about:blank','frame0');

		//Framebook.w0.foo('bar');
		Framebook.frame0.document.writeln(
		'<html><head><title>Console</title></head>'
		+'<body bgcolor=white onLoad="self.focus()">'
		+"<center>Cargando...</center"
		+'</body></html>');

		Framebook.frame0.close();
		Framebook.frame0 = window.open('lectura.php?id_comm='+Framebook.id_abrir+"&modo=inicia&framenum=0",'frame0');


		setTimeout(function(){
			Framebook.frame1 = window.open('lectura.php?modo=sigue&ultimo='+Framebook.id_abrir+"&framenum=1",'frame1');
		},1*1000);
	});


	function CambioPantalla(){
		//alert("[desde padre] Pantalla cambiar");
		if( Framebook.actual == 0){
			Framebook.selffocus1 = true;
			Framebook.actual = 1;
			$("frameset").attr("rows","*,100%");
			
		} else {
			Framebook.selffocus1 = true;
			Framebook.actual = 0;
			$("frameset").attr("rows","100%,*");
		}

	}

	function esFoco(numero){
		var f = false;

		if(numero==1){
			f = Framebook.selffocus1;

			Framebook.selffocus1 = false;
			return f;
		} else {
			f = Framebook.selffocus0;

			Framebook.selffocus0 = false;
			return f;
		}
	}


</script>

<FRAMESET ROWS="100%,*" frameborder="0" >
     <FRAME name="frame0" SRC="about:blank" class="frame" frameborder="0" />
     <FRAME name="frame1" SRC="about:blank" class="frame" frameborder="0" />
</FRAMESET>




</HTML>