/**
* @package Pages-and-Items (com_pagesanditems)
* @version 2.1.0
* @copyright	Copyright (C) . All rights reserved.
* @license	GNU/GPL, see LICENSE.php


 */

var popupManager = 
{
	
	initialize: function()
	{

	},

	setSize: function()
	{
		var documentHtml = document.getElement('html');
		var documentSize = documentHtml.getSize();
		
		var document_y;
		if(documentSize.size)
		{
			document_y = documentSize.size.y;
			document_y = document_y-2;
		}
		else
		{
			document_y = documentSize.y;
			document_y = document_y-2;
		}
		var top_y;
		var bottom_y;
		var content_y;
		
		var fieldset_top = document.id('fieldset_top');
		
		var topSize = fieldset_top.getSize();
		if(topSize.size)
		{
			top_y = parseInt(topSize.size.y)+ parseInt(fieldset_top.getStyle('margin-bottom').replace('px','')) + parseInt(fieldset_top.getStyle('margin-top').replace('px',''));
		}
		else
		{
			top_y = parseInt(topSize.y)+ parseInt(fieldset_top.getStyle('margin-bottom').replace('px','')) + parseInt(fieldset_top.getStyle('margin-top').replace('px',''));
		}

		var fieldset_bottom = document.id('fieldset_bottom');
		var bottomSize = fieldset_bottom.getSize();
		if(bottomSize.size)
		{
			bottom_y = parseInt(bottomSize.size.y)+ parseInt(fieldset_bottom.getStyle('margin-bottom').replace('px','')) + parseInt(fieldset_bottom.getStyle('margin-top').replace('px',''));
		}
		else
		{
			bottom_y = parseInt(bottomSize.y)+ parseInt(fieldset_bottom.getStyle('margin-bottom').replace('px','')) + parseInt(fieldset_bottom.getStyle('margin-top').replace('px',''));
		}
		

		var fieldset_content = document.id('fieldset_content');
		
		if(document.id('formContent'))
		{
			var formContent = document.id('formContent');
		}
		else
		{
			var formContent = document.id('adminForm');
		}
		var diff_y = parseInt(fieldset_content.getStyle('margin-bottom').replace('px','')) + parseInt(fieldset_content.getStyle('margin-top').replace('px',''));
		var diff_padding_y = parseInt(fieldset_content.getStyle('padding-bottom').replace('px','')) + parseInt(fieldset_content.getStyle('padding-top').replace('px',''));
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
		
		var contentcontainer = document.id('contentcontainer');
		var contentcontainerSize = contentcontainer.getSize();
		if(contentcontainerSize.size)
		{
			content_y = parseInt(contentcontainerSize.size.y);
		}
		else
		{
			content_y = parseInt(contentcontainerSize.y);
		}
		//alert();
		//formContent.setStyle('height',(document_y-bottom_y-top_y-diff_y-diff_padding_y)+'px');
		formContent.setStyle('height',(content_y)+'px');
		formContent.setStyle(' overflow-y','auto');
	}
};

window.addEvent('domready', function()
{
	popupManager.setSize();
	popupManager.initialize();

});

