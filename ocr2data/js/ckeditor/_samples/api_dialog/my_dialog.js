/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.dialog.add( 'myDialog', function( editor )
{
	return {
		title : 'Subir imagenes',
		minWidth : 400,
		minHeight : 200,
		contents : [
			{
				id : 'tab1',
				label : 'Subir',
				title : 'Subit',
				elements :
				[
					{
						id : 'input1',
						type : 'file',
						label : 'foto (gif,jpg ó png)'
					}
				]
			}
		]
	};
} );
