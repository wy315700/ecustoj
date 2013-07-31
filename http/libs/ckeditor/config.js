/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';

	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';         
	// config.toolbar = 'OJFull';
	config.toolbar_Basic = [
		['Styles', 'Format', 'Font', 'FontSize', 'Bold', 'Italic', 'Underline', 'Strike'], 
		['TextColor', 'BGColor'], 
		['Maximize', 'Source']
	];
	config.toolbar_OJBasic = [
		['Styles', 'Format', 'Font', 'FontSize', 'Bold', 'Italic', 'Underline', 'Strike'], 
		['TextColor', 'BGColor'], 
		['PasteText', 'PasteFromWord'],
		['Maximize', 'Source']
	];
	config.toolbar_Full =[
		['Source', '-', 'Save', 'NewPage', 'Preview', '-', 'Templates'],
		['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Print', 'SpellChecker', 'Scayt'],
  		['Undo', 'Redo', '-', 'Find', 'Replace', '-', 'SelectAll', 'RemoveFormat'],
  		'/',
 		['Bold', 'Italic', 'Underline', 'Strike', '-', 'Subscript', 'Superscript'],
		['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', 'Blockquote'],
		['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
		['Link', 'Unlink', 'Anchor'],
		['Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak'],
		'/',
		['Styles', 'Format', 'Font', 'FontSize'],
		['TextColor', 'BGColor'],
		['Maximize', 'ShowBlocks', '-', 'About']
	];
	config.toolbar_OJFull =[
		['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Print', 'SpellChecker', 'Scayt'],
  		['Undo', 'Redo', '-', 'Find', 'Replace', '-', 'SelectAll', 'RemoveFormat'],
		['Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak'],
  		'/',
		['Bold', 'Italic', 'Underline', 'Strike', '-', 'Subscript', 'Superscript'],
		['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', 'Blockquote'],
		['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
		['Link', 'Unlink', 'Anchor'],
		['Maximize', 'ShowBlocks', '-', 'About'],
		'/',
		['Styles', 'Format', 'Font', 'FontSize'],
		['TextColor', 'BGColor'],
		['Source']
	];
	
    config.font_names = 'Courier New, Courier, Monospace;Arial;Comic Sans MS;Courier New;Cursive;Fantasy;MingLiU;Miriam;MollBoran;Monospace;Mv Boli;Sans, Sans-Serif, Serif;Script;Simsun;System;Tahoma;Times New Roman;Verdana;Raavi;Terminal;黑体;仿宋;微软雅黑;宋体;幼圆;华文细黑;华文彩云;华文行楷;华文琥珀;楷体;隶书';
    config.fontSize_sizes = 'smaller;larger;xx-small;x-small;small;medium;large;x-large;xx-large;6px;9px;12px;15px;18px;21px;24px;27px;30px;36px;48px;64px;72px';
    config.enterMode = CKEDITOR.ENTER_BR;
    config.shiftEnterMode = CKEDITOR.ENTER_P;

};
