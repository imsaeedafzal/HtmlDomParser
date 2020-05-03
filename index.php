<?php
	
	namespace HtmlDomParser;
	
	$html = file_get_contents('test.php');
	
	$dom = new HtmlDomParser();
	$dom->loadHTML($html);
	
	$selector = '.find-class';
	$elements = $dom->find($selector);
	
	foreach ($elements as $k => $element){
		$htmlContent = "<p>{$k} - Content replaced dynamically for this element.</p>";
		
		// List of all attributes of this element
		$attrs = $element->getAttributes();
		foreach ($attrs as $attrK => $attr){
			// Validate the property name and value
			if($attr->name == 'id' && $attr->value == 'customHeading'){
				$htmlContent = '<h1>Custom heading replaced dynamically.</h1>';
			}
			
			if($attr->name == 'data-replace' && $attr->value == 'true'){
				$htmlContent = '<p>The content of this paragraph is dynamically replaced.</p>';
			}
		}
		
		// Set and get innerHtml
		//$element->innerHTML = $htmlContent;
		//echo $element->innerHTML;
		
		// Set and get outerHtml
		$element->outerHTML = $htmlContent;
		//echo $element->outerHTML;
		
		// Or you can also replace innerHtml and/or outerHtml of this element via main DOM object as well
		//$dom->outerHtml($element, '<h1>New Html Code H1111</h1>');
		//$dom->innerHTML($element, '<h1>New Html Code H1</h1>');
	}
	
	$html = $dom->saveHTML();
	$dom->clearLibXmlErrors();
	echo $html;
