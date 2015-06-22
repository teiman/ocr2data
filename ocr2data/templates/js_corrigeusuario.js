


if (typeof window.loadFirebugConsole == "undefined") {
     var console = {
        log: function(foo) {//do nothing
			},
        dir: function(foo) {//do nothing
			}
     };
}



var Key = {};

Key.teclaAbajo = 40;
Key.teclaEnter = 13;
Key.teclaDer = 39;
Key.teclaIzq = 37;
Key.teclaArriba = 38;


Global.cambiaCliente = function(id_cliente){
	this.id_cliente = id_cliente;

	$("#id_cliente").val(id_cliente);
};


function getText(dato){
	if(!dato || dato=="undefined")
		return "";

	return dato;
}


lineas.generarLineaProductos = function( name, i,index ,val) {

	var calidad = val["calidad"];

	if (calidad<0.3)
		return;


	lineas.lineasCount++;

	lineas.lastNameindex = index;
	lineas.lastName = name;

	var color = getColorFromQ(val["calidad"]);
	var uid = name + "_" + i + "_" + index;

	var cantidad = val["unid"];	
	if(!cantidad) cantidad = 1;


    //Su html-izado
	var html = "";
	html += "";
	html += "<table width='100%'><tr><td width='30px'>";
	html += "<input type='hidden' name='"+uid+"' value='"+getText(val["id_producto"])+"' class='id_producto visible interface'/>";
	//html += "</td><td>";
	html += "<input type='text' name='"+uid+"_ref' value='"+getText(val["ref"])+"' class='referencia editable interface q_color_"+color+"'/>";
	html += "</td><td width='20px'>";
	html += "<input type='text' name='"+uid+"_cantidad' value='"+cantidad+"' class='int3 editable interface q_color_"+color+"'/>";
	html += "</td><td>";
	html += "<input type='text' name='"+uid+"_txt' value='"+getText(val["resumen"])+"' class='producto editable interface q_color_"+color+"'/>";
	html += "</td>";

	html += "</td></tr></table>";


    //Añade el nuevo boton
	$("#lineas").append($(html));


    //Los nuevos botones tambine seran navegables.
    $('.interface').keydown(NavegadorKeyDown);
    $('.interface').keyup(NavegadorKeyUp);

};


lineas.generarLineaProductosCabecera = function() {


	var html = "";
	html += "";
	html += "<table width='100%' style='margin-top:4px'><tr><td style='width: 94px;zborder:1px solid red'>";
	html += "Ref";
	html += "</td><td style='width: 46px;zborder:1px solid red'>";
	html += "Unid.";
	html += "</td><td zstyle='width:218px'>";
	html += "Nombre";
	html += "</td>";
	html += "</tr></table>";

	$("#lineas").append($(html));
};

lineas.generarBotonNuevaLinea = function() {


    var html = "<input type='button' value='nueva linea' id='crearNuevaLinea' class='activarNavegador  interface' />";

	var $html= $(html);

	$($html).click(function(){
		//alert("nueva linea!");

		lineas.lastNameindex = parseInt(lineas.lastNameindex)  + 1 ;

		var val = {"id_producto":0,"ref":"","resumen":"","calidad":1,"unid":1};

		var name = lineas.lastName;//TODO: esto no es multigrupos !!HACK!!.
		var index = lineas.lastNameindex;
		var i = lineas.lastIndex;
		

		lineas.generarLineaProductos(name, i, index ,val);
	});

	$("#lineas2").append($html);


	$('#crearNuevaLinea').keydown(NavegadorKeyDown).keyup(NavegadorKeyUp);

};



lineas.generarInterface = function( name,val) {

	var conjunto = lineas[name];
	var arreglo = conjunto[val];

	$(arreglo).each( function(i,datos){
			$("#lineas").append("<label class='cabeceraTituloProductos'>Productos</label>");

			lineas.generarLineaProductosCabecera();

			lineas.lineasCount = 0;
			for (index in datos){
				var value = datos[index];
				/*
				clog("-----------")
				clog("gI,datos:")
				clog(datos);
				clog("gI,value:")
				clog(value);
				clog("gI,index:"+index);*/

				lineas.generarLineaProductos( name, i,index, value);				
			}
			if (lineas.lineasCount==0){
				lineas.lastNameindex = parseInt(lineas.lastNameindex)  + 1 ;

				var val = {"id_producto":0,"ref":"","resumen":"","calidad":1,"unid":1};

				var name = lineas.lastName;//TODO: esto no es multigrupos !!HACK!!.
				var index = lineas.lastNameindex;
				var i = lineas.lastIndex;


				lineas.generarLineaProductos(name, i, index ,val);
			}


			lineas.generarBotonNuevaLinea();
	});
};


function ProtegerTodos(){

	$("input.editable").addClass("readonlyKey");
	$("textarea.editable").addClass("readonlyKey");
	$("select.editable").addClass("readonlyKey");

	$(".readonlyKey").attr("xreadonly","xreadonly")
		.data("modo","bloqueado").removeClass("editando");
}

function Desproteger(elemento){
	$(elemento).removeAttr("readonly")
		.removeClass("readonlyKey")
		.data("modo","desbloqueado")
		.addClass("editando");

	if ($(elemento).hasClass("comp_iniciacambiacliente")){
		iniciacambiacliente(elemento);
	} else	if ($(elemento).hasClass("comp_direccioncliente")){
		iniciacambiadireccioncliente(elemento);
	}
}

function iniciacambiadireccioncliente(elemento){

	var padre = "#" +$(elemento).data("a_padre");

	$(padre).removeClass("oculto");

	$(elemento).addClass("oculto");

	var a_hijas = "."+$(elemento).data("a_hijas");
	var $first = $(a_hijas).eq(0);
	$first.focus();



	$(a_hijas).keydown(function(event){
			var key = event.keyCode || event.which;

	        if (key === Key.teclaAbajo) {
				event.preventDefault();

				var res = siguienteEnlaceDeGrupo($(a_hijas), this);

				if (res)
					$(res).focus();
			} else  if (key === Key.teclaArriba) {
				event.preventDefault();

				var res = anteriorEnlaceDeGrupo($(a_hijas), this);

				if (res)
					$(res).focus();
			} else if (key == Key.teclaEnter) {
				//event.preventDefault();
				//teclaEnter
				clog("resultado");
				clog($(this).html())
			}
			clog("keydown!"+event);
		});	


}


function iniciacambiacliente(elemento){

	$("#eleccioncliente_box").removeClass("oculto");
	$("#inicia_eleccioncliente_box").addClass("oculto");

	$(".comp_iniciacambiacliente").focus();
	$(".comp_focuscambiacliente").focus();
}


function cierracambiacliente(data){

	clog("Se cierra y cambia cliente");
	clog(data);

	$("#eleccioncliente_box").addClass("oculto");
	$("#inicia_eleccioncliente_box").removeClass("oculto");

	var id_cliente = data.attributes.id_cliente;

	$("#cambioCliente").val( data.attributes.nombre) ;
	$("#cambioCliente").focus();


	Global.cambiaCliente( id_cliente );

	clog("regenerando direcciones");
	generarEleccion_Direccion();//Direcciones que dependen del cliente seleccionado deben re-generarse.
}



function Seleccionar(elemento){
	$(".seleccionado").removeClass("seleccionado").addClass("des-seleccionado");

	if(elemento) {
		var tipo = $(elemento).attr("type");
		if(  tipo !="button"  && tipo != "submit" && tipo != "hidden" ){
			$(elemento).addClass("seleccionado");
			$(elemento).removeClass("des-seleccionado");			
		}
	}
}


$.fn.focusNextInputField = function() {
    return this.each(function() {
        var fields = $(this).parents('form:eq(0),body').find('button,input:not([type=hidden],.ignorainterface,.oculto,.ocultofijo),textarea,select');
        var index = fields.index( this );
        if ( index > -1 && ( index + 1 ) < fields.length ) {
			clog("next,index:"+index);
			clog("next:");
			clog( fields.eq( index + 1 ) );
            return fields.eq( index + 1 ).focus();
        }
		return false;
    });
};

$.fn.focusPrevInputField = function() {
    return this.each(function() {
        var fields = $(this).parents('form:eq(0),body').find('button,input:not([type=hidden],.ignorainterface,.oculto,.ocultofijo),textarea,select');
        var index = fields.index( this );
        if ( index > 0 ) {
			clog("next,index:"+index);
			clog("next:");
			clog( fields.eq( index - 1 ) );
            return fields.eq( index - 1 ).focus();
        }
        return false;
    });
};


function onClienteCambiado(data){

   Global.cambiaCliente( data.attributes.id_cliente );

   cierracambiacliente(data);
}



var elementFocused;



function generarEleccion_Cliente(){
	//$("#cabeceralista").append($caja);
	setTimeout(function(){

		clog("Crear autosuggest de cliente");
		$("#eleccioncliente").autoSuggest(
			"ajax.php",
			{minChars: 1, matchCase: false, queryParam: 'substring', extraParams: '&modo=clientessubstring2',
			emptyText:'...',startText:"Cliente...",selectionLimit:1,limitText:'Elimine la seleccion anterior'
			,resultClick: onClienteCambiado}
		);

	},0);


}

function accionDireccion(nodo,modo){
	clog("padre:");

	var me	= $("#"+nodo);
	var padre = $("#"+nodo).parent().get(0);

	var socio = $(padre).data("a_socio");



	clog("padre:")
	clog(padre);

	clog("socio:")
	clog(socio);

	if( modo == "pick" ){
		$(padre).addClass("oculto");
		$("#"+socio).val( $.trim( $(me).text()) );
		$("#"+socio).removeClass("oculto");

		$("#"+socio).focus();
	} else if ( modo=="new" ){
		$(padre).addClass("oculto");
		$("#"+socio).removeClass("oculto");
		$("#"+socio).focus();

		$("#"+socio).removeAttr("readonly")
		.removeClass("readonlyKey")
		.data("modo","desbloqueado");		
	}


}


function cargaListadoDirecciones(rid, data){


	clog("cLD:"+rid);
	$("#" + rid).html( data.html );

}

function generarEleccion_Direccion(){
	clog("func: generar eleccion direcciones");

	if($(".areadireccion").length){
		$(".areadireccion").remove();
	}

	generarEleccion_Direccion.index = 0;

	$(".comp_direccioncliente").each(function(){

		var rngid = "elige_direccionescliente_"+ parseInt(Math.random()*1000);
		var selector = $("<div class='areadireccion oculto savana' id='"+rngid+"'>Cargando direcciones...</div>");
		var newid = $(this).attr("id") + "_dinamico";

		clog(this);

		$(selector).data("a_socio", $(this).attr("id") );
		clog("guardando " +  $(this).attr("id") + " en a_socio");
		
		$(selector).insertBefore(this);

		var id_cliente = Global.id_cliente;
		var data_cache = Global.direccionesCliente[ id_cliente ];

		if (data_cache && data_cache!="undefined"){
			clog("cache,return: cargando listado direcciones, desde cache para id_Cliente:["+id_cliente+"]");
			cargaListadoDirecciones( rngid, data_cache);
			return;
		}
		
		$(this).data("a_hijas", "a_"+newid);
		$(this).data("a_padre", rngid);
		

	

		clog("ajax: cargando direcciones de cliente");
		$.ajax({
			type: 'POST',
			url: "ajax.php",
			dataType: "json",
			data: {
				modo: "direccionescliente",
				id: Global.id_cliente,
				rid: newid
			},
			success: function( data ) {
				clog("ajax,return: cargando listado direcciones"+Global.id_cliente);

				Global.direccionesCliente[Global.id_cliente] = data;

				cargaListadoDirecciones( rngid, data);
			}
		});
	});

}

function LeeEntero(dato){
	var num = parseInt(dato);

	if (isNaN(num)){
			return 0;
	}
		
	return num;
}

function activarNavegadorCampos(){
		$('.activarNavegador').keydown(NavegadorKeyDown);
		$('.activarNavegador').keyup(NavegadorKeyUp);


		$(".activarNavegador").focus(function () {
			Seleccionar(this);
		});

		$(".activarNavegador").blur(function () {
			 $(this).removeClass("seleccionado");
			 $(this).addClass("des-seleccionado");
		});

		$('.activarNavegador').removeClass('activarNavegador');
}


function generarEleccion_Fechas(){
	clog("func: generar eleccion direcciones");

	generarEleccion_Direccion.index = 0;

	$(".comp_fecha").each(function(){

		var color = "verde";
		if ( $(this).hasClass("q_color_rojo") ){
			color = "rojo";
		} else if ( $(this).hasClass("q_color_naranja") ){
			color = "naranja";
		}


		clog("color:"+color);

		var clases = "datofecha editable interface comp_fecha readonlyKey activarNavegador q_color_"+color;

		var rngid = "coge_fecha_"+ parseInt(Math.random()*10000);

		var selector = $("<table><tr><td><input name='fecha_d' id='"+rngid+"_d' maxlength='2' class='"+clases+" datodia'/></td><td>-</td>"+
				"<td><input name='fecha_m' id='"+rngid+"_m' maxlength='2' class='"+clases+" datomes2'  style='width: 5em' /></td><td>-</td>"+
				"<td>20<input name='fecha_a' id='"+rngid+"_a' maxlength='2' class='"+clases+" datoagno' /></td><tr></table>");
		$(selector).insertBefore(this);

		//Lo ocultamos al programa, este campo ya no se usara visualmente
		$(this).addClass("oculto");

		var fecha = $(this).val() + "";
		var partesfecha = fecha.split("-");
		var dia = LeeEntero(partesfecha[0]);
		var mes = LeeEntero(partesfecha[1]);
		var agno = LeeEntero(partesfecha[2]);

		if (dia<=0)	dia = 1;
		if (dia>31)	dia = 31;

		if (mes<=0)	mes = 1;
		if (mes>12)	mes = 12;

		if (agno<0) agno = 0;
		if (agno>2000) agno = agno - 2000;
		if (agno>200) agno = agno - 200; //todo: mejor hacer un ajuste de cadenas  XX03 => 03 

		$("#"+rngid+"_d").val(sprintf("%02d",dia));
		$("#"+rngid+"_m").val(sprintf("%02d",mes));
		$("#"+rngid+"_a").val(sprintf("%02d",agno));

		activarNavegadorCampos();

	});

/*
 *
numpedido editable interface comp_ninguno readonlyKey
 *
 */

}



function clog(mensaje){
	console.log(mensaje);
}

function NavegadorKeyUp(event) {
	var key = event.keyCode || event.which;

	var modo = $(this).data("modo");

	if (modo =="bloqueado"){
		if (key === Key.teclaEnter) {
			event.preventDefault();
			ProtegerTodos();
			Desproteger(this);
		} else if (key== Key.teclaAbajo  ){
			elementFocused = $(this).focusNextInputField();

		} else if (key== Key.teclaArriba ){
			var nextElement = $(this).focusPrevInputField();

		} 	else {
			event.preventDefault();
			//What is this, I don't even

		}
	} else	if (modo=="desbloqueado"){
		if (key === Key.teclaEnter) {
			event.preventDefault();
			ProtegerTodos();
		}
	} else {
		if (key== Key.teclaAbajo  ){
			elementFocused = $(this).focusNextInputField();
		} else if (key== Key.teclaArriba  ){
			elementFocused = $(this).focusPrevInputField();
		}
	}

	return false;
}

function NavegadorKeyDown(event) {
	var key = event.keyCode || event.which;

	var modo = $(this).data("modo");
	if (modo=="desbloqueado"){
		if (key === Key.teclaEnter) {
			event.preventDefault();
		}
	} else 	if (modo=="bloqueado"){
		if (key === Key.teclaEnter) {
			event.preventDefault();
		}
	}
}


function getColorFromQ(q){
	var color = "";
			if (q>0.5){
				color = "verde";
			} else if (q>0.3){
				color = "naranja";
			} else {
				color = "rojo";
			}
	return color;
}


function enviarTodosDatosServidor(){

	var todosDatos = "";

	$(".editable").each(function(){
		var nombre = $(this).attr("name");
		var valor = $(this).val();

		todosDatos += nombre + "="+encodeURIComponent(valor)+ "&";
	});

	$(".enviardato").each(function(){
		var nombre = $(this).attr("name");
		var valor = $(this).val();

		todosDatos += nombre + "="+encodeURIComponent(valor)+ "&";
	});

	todosDatos += "modo=guardardatos";
	
	
	$.ajax({
		type: 'POST',
		url: "ajax.php",
		data: todosDatos,
		success: function( data ) {
			clog("datos enviados con exito");

		}
	});

}


function requiereFoco(){

	var focuseame = top.esFoco(0);

	if(focuseame){
		$(".readonlyKey")[0].focus();

		clog("Puesto el foco");

		alert("me has dado el foco");
	} else {
			setTimeout(requiereFoco,100);
	}
}


function enviarFormulario(){ //tramitado

	enviarTodosDatosServidor();	
	
	setTimeout(function(){
		clog("cambiando ventana");
		window.location.href = "lectura.php?modo=cargarsiguiente&ultimo="+Global.id_comm+"&recon_id="+Global.recon_id;
	},100);

    if(typeof top.CambioPantalla == 'function' ){
	    top.CambioPantalla();
    }
};


function onLoadPageAlta(){
	clog("Carga pagina");
	clog("Autocrea interfaces");

	$(lineas.interfaces).each(function(i,val){
		clog("/i:"+i);
		clog(val);
		clog("vna:" +val.name);
		clog("vnu:" +val.num);
		lineas.generarInterface(val.name,val.num);
	});

	clog("Ajustes de navegacion");

	$("div.interface").removeClass("interface");

	ProtegerTodos();

	Seleccionar($(".readonlyKey")[0]);

	$('.interface').keydown(NavegadorKeyDown);
    $('.interface').keyup(NavegadorKeyUp);


	$(":input").focus(function () {
		 //elementFocused = this;
		Seleccionar(this);
	});

	$(":input").blur(function () {
		 $(this).removeClass("seleccionado");
	});

	clog("Ajustes finales");

	if (lineas.eligeCliente=='1'){
		clog("Generando eleccion de cliente");
		generarEleccion_Cliente();
	}

	if ( $(".comp_direccioncliente").length  ){
		clog("Generando eleccion direccion");
		generarEleccion_Direccion();
	}

	/*
	if ($(".comp_fecha").length){
		clog("Generando eleccion fechas");
		generarEleccion_Fechas();
	}*/

	if ($(".comp_fecha").length){
		$(".comp_fecha").each(function(){
			$(this).css("width","10em");
		});
	}


	$("#grupocontroles").submit(function(){
		return false;
	});



	$(".q_colorificar").each(function(){
		clog("Coloreando");
		var color = "";

		var id = $(this).attr("id") + "";
		id = id.replace("elemento_","");

		var strid = "#q_"+id;

		var existe = $(strid).length;

		if( existe ){
			var q = $(strid).val();

			//<input id="q_717" class="dato_q" type="hidden" value="0.0597214"/>
			clog("leyendo "+id+",q:"+q);

			color = getColorFromQ(q);

			$(this).addClass("q_color_"+color);
		} else {
			clog("not found for"+ $(this).attr("id"));
		}

		$(".q_colorificar").removeClass("q_colorificar");
	});


	$(document).bind('keydown', 'Ctrl+1', function(evt){
		//alert("Se pulso control-s");
		evt.stopPropagation( );
		evt.preventDefault( );
		setTimeout(enviarFormulario,0);
	    return false;
	});

	$(document).bind('keydown', 'Ctrl+2', function(evt){
		evt.stopPropagation( );
		evt.preventDefault( );

		top.location = "modrecon.php";

	    return false;
	});


	$("#tramitado").click(enviarFormulario);


	clog("Fin de la generacion en carga");

    $("#cambioCliente").focus();

	//requiereFoco();
}


function Cancelar(){
	top.location = "modrecon.php";
}


//..

function siguienteEnlaceDeGrupo(elementos,saltar){
	var seleccionado = false;
	var primeroVisto = false;
	var esVisto = false;

	clog("siguiente...");

	$(elementos).data("el_seleccionado",false);
	$(saltar).data("el_seleccionado",true);

	$(elementos).each(function(){
		if ($(this).data("el_seleccionado")  ){
			esVisto = true;
			clog("visto..");
			return;
		} else {
			clog("..");

			if (!primeroVisto)
				primeroVisto = this;
		}

		if( !seleccionado && esVisto){
			clog("seleccionado!..");
			seleccionado = this;
		}
	});


	if (!seleccionado)
		return primeroVisto;


	return seleccionado;
}


function anteriorEnlaceDeGrupo(elementos,saltar){
	var anterior = false;
	var elegido = false;
	var ultimoVisto = false;

	clog("anterior...");

	$(elementos).data("el_seleccionado",false);
	$(saltar).data("el_seleccionado",true);

	$(elementos).each(function(){
		if ($(this).data("el_seleccionado")){
			return;
		}

		if (anterior)
			elegido = anterior;

		anterior = this;
		ultimoVisto = this;
	});

	if (!anterior)
		return ultimoVisto;

	return anterior;
}


function EditarTemplate(tid){

	var volverUrl = escape("lectura.php?modo=inicia&id_recon="+tid);

	document.location.href = "modtipodocumento.php?tid="+tid + "&volver="+ volverUrl;
}


function SalirMarcandoEnProceso(){
	$("#emisorEnProceso").submit();//TODO: ajax?

	setTimeout(function(){
		clog("cambiando ventana");
		window.location.href = "lectura.php?modo=cargarsiguiente&ultimo="+Global.id_comm+"&desde=saliendomarca";
	},100);
	

    if(typeof top.CambioPantalla == 'function' ){
	    top.CambioPantalla();
    }	
}




