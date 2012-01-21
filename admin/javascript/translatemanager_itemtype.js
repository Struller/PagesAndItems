/**
* @package Pages-and-Items (com_pagesanditems)
* @version 2.0.0
* @copyright Copyright (C) 2009-2010 Michael Struller. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author http://gecko.struller.de
 */

var TranslateManager_itemtype = 
{
	initialize: function()
	{
		/*
		var languageAccordion = new Accordion($$('.panel h3.jpane-toggler'), $$('.panel div.jpane-slider'), 
		{
			onActive: function(toggler, i) { toggler.addClass('jpane-toggler-down'); toggler.removeClass('jpane-toggler'); },
			onBackground: function(toggler, i) { toggler.addClass('jpane-toggler'); toggler.removeClass('jpane-toggler-down'); },
			duration: 300,
			opacity: false,
			display: 1
		});
		this.languageAccordion = languageAccordion;
		*/
		this.frame	= window.frames['languageiframe'];
		this.frameurl	= this.frame.location.href;
		
		//document.id(
		this.languageiframe = $('languageiframe');
		this.languageiframe.manager = this;
		this.languageiframe.addEvent('load',function(){ TranslateManager_itemtype.onLoad(); });
		this.enableButtonCount = 0;
	},
	
	onLoad: function()
	{
		var frameDocument = this.frame.document;
		var iconCss = window.document.getElementById('iconCss').value;
		var snode = frameDocument.createElement('link');
		snode.setAttribute('type','text/css');
		snode.setAttribute('rel','stylesheet');
		snode.setAttribute('href',iconCss);
		frameDocument.getElementsByTagName('head')[0].appendChild(snode);
		
		var languageSelect = frameDocument.getElementById("language_id");
		var languageName = languageSelect.options[languageSelect.selectedIndex].text;
		var languageNameNode = frameDocument.createTextNode(languageName);
		languageSelect.parentNode.appendChild(languageNameNode);
		languageSelect.setAttribute("style","display: none;");
		
		var inputTmpl = frameDocument.createElement('input');
		inputTmpl.setAttribute("type","hidden");
		inputTmpl.setAttribute("name","tmpl");
		inputTmpl.setAttribute("value","component");
		var inputTask = frameDocument.getElementsByName("task")[0];
		inputTask.parentNode.insertBefore( inputTmpl, inputTask.nextSibling );
		
		/*
		var contentframeDocument = this.contentframe.document;
		var refFieldTitle = frameDocument.getElementsByName("refField_title")[0];
		var contentRefFieldTitle = contentframeDocument.getElementsByName("refField_title")[0];
		refFieldTitle.addEvent('change',function()
		{ 
			contentRefFieldTitle.value = refFieldTitle.value;
		});
		*/
		var frameDocumentBody = frameDocument.getElementsByTagName('body')[0];
		frameDocumentBody.setAttribute("style","margin:3px;");
		this.enableApplyButton();
	},
	
	enableApplyButton: function()
	{
		this.enableButtonCount = this.enableButtonCount +1;
		if(this.enableButtonCount == 1)
		{
			var buttonTranslateApply = window.document.getElementById('button_translate_apply');
			buttonTranslateApply.setAttribute("class","button_action");
			buttonTranslateApply.removeAttribute("disabled");
			
			this.languageiframe.setAttribute("class","normal");
			//this.languageAccordion.display(0);
			//this.contentlanguageiframe.setAttribute("class","normal");
		}
	},
	
	onLoadContent: function()
	{
		var contentframeDocument = this.contentframe.document;
		var contenticonCss = window.document.getElementById('iconCss').value;
		var contentsnode = contentframeDocument.createElement('link');
		contentsnode.setAttribute('type','text/css');
		contentsnode.setAttribute('rel','stylesheet');
		contentsnode.setAttribute('href',contenticonCss);
		contentframeDocument.getElementsByTagName('head')[0].appendChild(contentsnode);
		
		var contentlanguageSelect = contentframeDocument.getElementById("language_id");
		var contentlanguageName = contentlanguageSelect.options[contentlanguageSelect.selectedIndex].text;
		var contentlanguageNameNode = contentframeDocument.createTextNode(contentlanguageName);
		contentlanguageSelect.parentNode.appendChild(contentlanguageNameNode);
		contentlanguageSelect.setAttribute("style","display: none;");
		
		var contentinputTmpl = contentframeDocument.createElement('input');
		contentinputTmpl.setAttribute("type","hidden");
		contentinputTmpl.setAttribute("name","tmpl");
		contentinputTmpl.setAttribute("value","component");
		var contentinputTask = contentframeDocument.getElementsByName("task")[0];
		contentinputTask.parentNode.insertBefore( contentinputTmpl, contentinputTask.nextSibling );
		
		var contentframeDocumentBody = contentframeDocument.getElementsByTagName('body')[0];
		contentframeDocumentBody.setAttribute("style","margin:3px;");
		this.enableApplyButton();
	},
	
	translateApply: function()
	{
		this.frame.document.adminForm.task.value='translate.apply';
		this.frame.document.adminForm.submit();
	}
};

window.addEvent('domready', function()
{
	TranslateManager_itemtype.initialize();

});

