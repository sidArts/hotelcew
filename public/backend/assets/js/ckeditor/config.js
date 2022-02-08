/**

 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.

 * For licensing, see LICENSE.md or http://ckeditor.com/license

 */



CKEDITOR.editorConfig = function( config ) {

	// Define changes to default configuration here. For example:

	// config.language = 'fr';

	// config.uiColor = '#AADC6E';

	

  //||||||||||||||||||||||| Custom Configuration |||||||||||||||||||||||

  	

	//....... SIZE Settings ...........

	  //config.width = '75%';

	  

	//...... TOOLBAR Settings .........

	  config.toolbarGroups = [

	  							{ name: 'document', 	groups: [ 'mode', 'document', 'doctools' ] },

								{ name: 'clipboard',   	groups: [ 'clipboard', 'undo' ] },

								{ name: 'links' }, 		

								{ name: 'insert' },

								{ name: 'basicstyles', 	groups: [ 'basicstyles', 'cleanup' ] },

								{ name: 'paragraph',   	groups: [ 'indent', 'blocks', 'align' ] },

								{ name: 'styles',   	groups: [ 'styles', 'format', 'fontsize' ]  }, 

								{ name: 'colors' }

							 ];

	  

	//.... IMAGE UPLOAD Settinsg ......

	config.filebrowserBrowseUrl 		= BASE_URL +'public/assets/kcfinder/browse.php?opener=ckeditor&type=files';

   	config.filebrowserUploadUrl 		= BASE_URL +'public/assets/kcfinder/upload.php?opener=ckeditor&type=files';

   	config.filebrowserImageBrowseUrl 	= BASE_URL +'public/assets/kcfinder/browse.php?opener=ckeditor&type=images';

   	config.filebrowserImageUploadUrl 	= BASE_URL +'public/assets/kcfinder/upload.php?opener=ckeditor&type=images';

   	config.filebrowserFlashBrowseUrl 	= BASE_URL +'public/assets/kcfinder/browse.php?opener=ckeditor&type=flash';

   	config.filebrowserFlashUploadUrl 	= BASE_URL +'public/assets/kcfinder/upload.php?opener=ckeditor&type=flash';

	

  //||||||||||||||||||||||| Custom Configuration |||||||||||||||||||||||

};

