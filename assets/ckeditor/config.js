/**
 * @license Copyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {

  config.toolbarGroups = [
		{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
		{ name: 'links', groups: [ 'links' ] },
		{ name: 'insert', groups: [ 'insert' ] },
		{ name: 'tools', groups: [ 'tools' ] },
		{ name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
		{ name: 'styles', groups: [ 'styles' ] },
		'/',
		{ name: 'colors', groups: [ 'colors' ] },
		{ name: 'others', groups: [ 'others' ] },
		{ name: 'about', groups: [ 'about' ] },
		{ name: 'forms', groups: [ 'forms' ] }
	];

  config.removeButtons = 'Save,NewPage,Print,Find,Replace,SelectAll,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,About,Flash,Smiley,PageBreak,Iframe,ShowBlocks,Subscript,Superscript,Underline,CreateDiv,JustifyLeft,JustifyCenter,JustifyRight,JustifyBlock,Language,BidiRtl,BidiLtr,Font,FontSize,TextColor,BGColor';
  config.allowedContent=true;

	// Set the most common block elements.
	config.format_tags = 'p;h1;h2;h3;pre';

	// Simplify the dialog windows. 移除:連結(目標、進階)、影像(連結、進階)
	config.removeDialogTabs = 'link:target;image:advanced';

  var array = location.pathname.split('/');
  var baseurl = "//"+location.hostname+'/';
  if(array[1] != 'admin') baseurl += array[1]+'/';

  config.filebrowserUploadUrl = baseurl+'assets/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files'; // 預設值-上傳 (指向 type/Files)

  config.skin = 'moonocolor';
  // 範本
  config.templates = 'empty';
  config.templates_files = [
    baseurl+'assets/templates/mytemplate.js'
  ]
  config.extraPlugins = 'preview2';
  config.height = '30em';     // CSS unit (em).
  config.baseHref = baseurl;
};
