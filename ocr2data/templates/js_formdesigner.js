
/* - - - - - - - - - */

/* - - - - - - - - */


function disableDraggingFor(element) {
  // this works for FireFox and WebKit in future according to http://help.dottoro.com/lhqsqbtn.php
  element.draggable = false;
  // this works for older web layout engines
  element.onmousedown = function(event) {
                event.preventDefault();

                return false;
              };
}

/* - - - - - - - - */

function reajustaImagenPrincipal(){

   var $imagen = $("#imagenplantilla");

   $imagen.removeAttr("width")
   .removeAttr("height")
   .css({ width: "", height: "" }); // Remove css dimensions as well

	var realw = $imagen.width();
	var realh = $imagen.height();

	$imagen.css("width","100%");
	var wplantilla = $("#imagenplantilla").width();

	area.factorw = realw/wplantilla;
}

/* - - - - - - - - */


var area = {"pos_x":0,"pos_y":0 ,"modo":"parar" };

var plantilla = { "documento":"plantilla" };
plantilla.areas = new Array();


function onLoadPageAlta(){

		area.caja = $("#caja");

        $("button").button();

        $("#guarda").button({
            icons: {   primary: "ui-icon-disk"}
           });

       $(".dir").button({
            icons: {   primary: "ui-icon-mail-close"}
           });

       $(".num").button({
            icons: {   primary: "ui-icon-play"}
           });

       $(".misc1").button({
            icons: {   primary: "ui-icon-extlink"}
           });

       $(".misc").button({
            icons: {   primary: "ui-icon-extlink"}
           });

       $(".kill").button({
            icons: {   primary: "ui-icon-closethick"}
           });

       $(".text").button({
            icons: {   primary: "ui-icon-note"}
           });

       $("#cancelar").button({
            icons: {   primary: "ui-icon-close"}
           });

		area.reset = function(){
			this.pos_x = 0;
			this.pos_y = 0;
			this.fin_x = 0;
			this.fin_y = 0;
			area.caja.hide();
		};

		function actualizarCaja(){
			area.caja.css("left",area.pos_x + "px");
			area.caja.css("top",area.pos_y + "px");

			var w = Math.abs(area.fin_x - area.pos_x) ;
			var h = Math.abs(area.fin_y - area.pos_y) ;

			area.w = w;
			area.h = h;

			area.caja.css("width",w + "px");
			area.caja.css("height",h + "px");
		}

		disableDraggingFor(document.getElementById('imagenplantilla'));

	   $("#imagenplantilla").click(function(e){
		  //$('#status').html(e.pageX +', '+ e.pageY);

		  switch(area.modo){
			case "inicio":
				area.pos_x = e.pageX;
				area.pos_y = e.pageY;
				area.modo = "fin";
				area.caja.html("");
				area.caja.css("background-color","transparent");
				break;
			case "fin":
				//toma datos
				area.fin_x = e.pageX;
				area.fin_y = e.pageY;

				//Corrige geometrias raras
				if ( area.fin_x < area.pos_x ){
					var tmp = area.pos_x;
					area.pos_x = area.fin_x;
					area.fin_x = tmp;
				}

				if ( area.fin_y < area.pos_y ){
					var tmp = area.pos_y;
					area.pos_y = area.fin_y;
					area.fin_y = tmp;
				}

				area.caja.show();
				area.modo = "reconoce";

				actualizarCaja();
				plantilla.guardarArea();


				break;
			case "reconoce":
				recogerOCRDeArea();
				area.modo = "parar";
				break;
			case "parar":
				break;
			default:
				break;
		  }

	   });


	   plantilla.guardarArea = function(){

			var position = $("#imagenplantilla").position();

			area.enimagen_x = area.pos_x - position.left;
			area.enimagen_y = area.pos_y - position.top;

			$("#pos_x").val(area.enimagen_x * area.factorw);
			$("#pos_y").val(area.enimagen_y * area.factorw);


			var w = area.w * area.factorw;

			$("#w").val(w);
			$("#h").val(area.h * area.factorw);

			//console.dir(area);
			//alert("w:"+w + ",factorw:" + area.factorw + ",a.h"+area.h);

			var dataArea = {
						registro: area.registro,
						pos_x : $("#pos_x").val(),
						pos_y : $("#pos_y").val(),
						w : $("#w").val(),
						h : $("#h").val()
					};

			//alert(dataArea.toSource());

			plantilla.areas[ area.registro ] = dataArea;

			return dataArea;
		}



		plantilla.guardarServidor = function(){


			//:cadenaAreas
			//direccioncliente, direccionentrega
			var cadena = "";
			//cadena += JSON.stringify(plantilla.areas["direccioncliente"]) + "#";
			cadena += JSON.stringify(plantilla.areas["direccionentrega"]) + "#";
			cadena += JSON.stringify(plantilla.areas["numpedido"]) + "#";
			cadena += JSON.stringify(plantilla.areas["fechaentrega"]) + "#";
			cadena += JSON.stringify(plantilla.areas["lineaspedido"]) + "#";

			var message  = {'areas':cadena,'modo':'guardarareas','test':7,'tid':Template.tid};



			$.ajax({
				  url: "modtipodocumento.php",
				  type: "POST",
				  dataType: 'json',
				  zcontentType: "application/json; charset=utf-8",
				  data: message ,
				  success: function(msg){
                     alert("Template modificada.");  
                     document.location =  "modrecon.php";
				  }
				}
			);

		}




	   function recogerOCRDeArea(){

			var position = $("#imagenplantilla").position();

			area.enimagen_x = area.pos_x - position.left;
			area.enimagen_y = area.pos_y - position.top;

			$("#pos_x").val(area.enimagen_x * area.factorw);
			$("#pos_y").val(area.enimagen_y * area.factorw);

			$("#w").val(area.w * area.factorw);
			$("#h").val(area.h * area.factorw);

			var dataArea = {
						modo: 'areacaptura',
						registro: area.registro,
						pos_x : $("#pos_x").val(),
						pos_y : $("#pos_y").val(),
						w : $("#w").val(),
						h : $("#h").val() };

			$.ajax({
				  url: "modtipodocumento.php",
				  type: "POST",
				  dataType: 'json',
				  data: (dataArea),
				  success: function(msg){
					 //alert("data:"+msg["ocr"]);
					 area.caja.html("<pre>"+ msg["ocr"] + "</pre>");
					 area.caja.css("background-color","#ccc");
				  }
				}
			);
		}

		$("#direccioncliente").click(function(){
			area.reset();
			actualizarCaja();
			area.modo = "inicio";
			area.registro ="direccioncliente";
		});

		$("#direccionentrega").click(function(){
			area.reset();
			actualizarCaja();
			area.modo = "inicio";
			area.registro ="direccionentrega";
		});

		$("#numpedido").click(function(){
			area.reset();
			actualizarCaja();
			area.modo = "inicio";
			area.registro ="numpedido";
		});

		$("#fechaentrega").click(function(){
			area.reset();
			actualizarCaja();
			area.modo = "inicio";
			area.registro ="fechaentrega";
		});

		$("#lineaspedido").click(function(){
			area.reset();
			actualizarCaja();
			area.modo = "inicio";
			area.registro ="lineaspedido";
		});

		$("#guarda").click(function(){
			plantilla.guardarServidor();
		});


		$("#cancelar").click(function(){
			//VolverPagina();//TODO
			//document.location = "../mod
			document.location = Global.DireccionRetorno;
		});

	   reajustaImagenPrincipal();
}

/* - - - - - - - - - */

/* - - - - - - - - - */

