

CKEDITOR.dialog.add( 'myDialog', function( editor )
{
	//console.log("hola");


	setTimeout(function(){

		var id = $("div[name=fileuploader2]").attr("id");

		$("#"+ id).append( $("<br clear='both'><div id='cajanueva'></div><br><div><a id='resultadofile' href='#'></a></div>"));
		createUploader('cajanueva');

	}, 100);
	
	return {
		title : 'Subir imagenes',
		minWidth : 400,
		minHeight : 200,
		buttons:[CKEDITOR.dialog.okButton],
		contents : [
			{
				id : 'fileuploader2',
				label : 'Subir',
				title : 'Subit',
				elements :
				[

				]
			}
		]
	};
} );




