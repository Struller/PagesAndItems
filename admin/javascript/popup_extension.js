/**
* @package Pages-and-Items (com_pagesanditems)
* @version 2.1.0
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
 */

var popupExtension = 
{
	
	initialize: function()
	{

	},

	setSize: function()
	{
		//var documentHtml = document.getElement('form');
		var documentHtml = document.getElement('html');
		//var documentHtml = document.getElement('body');
		var documentSize = documentHtml.getSize();
		
		var document_y;
		if(documentSize.size)
		{
			document_y = documentSize.size.y;
			document_y = document_y-2;
			//alert(document_y);
		}
		else
		{
			document_y = documentSize.y;
			document_y = document_y-2;
			//alert(document_y);
		}
		var top_y;
		var bottom_y;
		var content_y;
		
		//document.id(
		var fieldset_top = $('fieldset_top');
		
		var topSize = fieldset_top.getSize();
		if(topSize.size)
		{
			top_y = parseInt(topSize.size.y)+ parseInt(fieldset_top.getStyle('margin-bottom').replace('px','')) + parseInt(fieldset_top.getStyle('margin-top').replace('px',''));
		}
		else
		{
			top_y = parseInt(topSize.y)+ parseInt(fieldset_top.getStyle('margin-bottom').replace('px','')) + parseInt(fieldset_top.getStyle('margin-top').replace('px',''));
		}
		//document.id(
		var fieldset_bottom = $('fieldset_bottom');
		var bottomSize = fieldset_bottom.getSize();
		if(bottomSize.size)
		{
			bottom_y = parseInt(bottomSize.size.y)+ parseInt(fieldset_bottom.getStyle('margin-bottom').replace('px','')) + parseInt(fieldset_bottom.getStyle('margin-top').replace('px',''));
		}
		else
		{
			bottom_y = parseInt(bottomSize.y)+ parseInt(fieldset_bottom.getStyle('margin-bottom').replace('px','')) + parseInt(fieldset_bottom.getStyle('margin-top').replace('px',''));
		}
		
		//document.id(
		var fieldset_content = $('fieldset_content');
		
		//document.id(
		if($('formContent'))
		{
			//document.id(
			var formContent = $('formContent');
		}
		else
		{
			//document.id(
			var formContent = $('adminForm');
		}
		var diff_y = parseInt(fieldset_content.getStyle('margin-bottom').replace('px','')) + parseInt(fieldset_content.getStyle('margin-top').replace('px',''));
		fieldset_content.setStyle('height',(document_y-bottom_y-top_y-diff_y)+'px');
		
		var contentSize = fieldset_content.getSize();
		if(contentSize.size)
		{
			content_y = parseInt(contentSize.size.y)+ parseInt(fieldset_content.getStyle('margin-bottom').replace('px','')) + parseInt(fieldset_content.getStyle('margin-top').replace('px',''));
		}
		else
		{
			content_y = parseInt(contentSize.y)+ parseInt(fieldset_content.getStyle('margin-bottom').replace('px','')) + parseInt(fieldset_content.getStyle('margin-top').replace('px',''));
		}
		
		formContent.setStyle('height',(document_y-bottom_y-top_y-diff_y)+'px');
		formContent.setStyle(' overflow-y','auto');
	}
};

window.addEvent('domready', function()
{
	popupExtension.setSize();
	popupExtension.initialize();

});

