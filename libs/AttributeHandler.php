<?php
	
	trait AttributeHandler
	{
		protected function elementOuterHtml(DOMDocument $dom, HtmlDomElement $element, string $html, bool $copyAttributes = false){
			$html = (empty($html) || $html == null) ? '' : $html;
			$newElement = $this->getFragmentElement($dom, $element, $html);
			if($copyAttributes) {
				// Append attributes of the parent element in outerHtml element automatically
				$this->copyAttributesFromHtmlFragment($dom, $newElement, $element);
			}
			
			// Generate outerHtml of the new element to return
			$newHtml = '';
			foreach ($newElement->childNodes as $newElementItem) {
				$newHtml .= $element->ownerDocument->saveXML($newElementItem);
			}
			
			$element->parentNode->replaceChild($newElement, $element); // to replace the html = outerHtml
			return $newHtml;
		}
		
		protected function elementInnerHtml(DOMDocument $dom, HtmlDomElement $element, string $html, bool $copyAttributes = false){
			$html = (empty($html) || $html == null) ? '' : $html;
			$newElement = $this->getFragmentElement($dom, $element, $html);
			if($copyAttributes) {
				// Append attributes of the parent element in outerHtml element automatically
				$this->copyAttributesFromHtmlFragment($dom, $newElement, $element);
			}
			$element->appendChild($newElement); // to append the html = innerHtml
			
			// Generate innerHtml of the element to return
			$inner = '';
			foreach ($element->childNodes as $child) {
				$inner .= $element->ownerDocument->saveXML($child);
			}
			return $inner;
		}
		
		private function copyAttributesFromHtmlFragment(DOMDocument $dom, $newElement, HtmlDomElement $element){
			foreach ($newElement->childNodes as $child) {
				$ele = $dom->importNode($child, true);
				$this->copyElementAttributes($dom, $ele, $element);
			}
		}
		
		protected function copyElementAttributes(DOMDocument $dom, $newElement, HtmlDomElement $element) {
			$attributes = $element->attributes ;
			for($j = $attributes->length-1 ; $j>= 0 ; --$j)
			{
				$attributeName = $attributes->item($j)->nodeName ;
				$attributeValue = $attributes->item($j)->nodeValue ;
				
				// TODO: Skip desired attributes from the list
				// $dom = $this->document in HtmlDomElement class
				$newAttribute = $dom->createAttribute($attributeName);
				$newAttribute->nodeValue = $attributeValue;
				
				$newElement->appendChild($newAttribute); // for Element
			}
			return $newElement;
		}
		
		protected function getElementAttributes(HtmlDomElement $element){
			$attributes  = [];
			if($element->hasAttributes()){
				for($i=0; $i < $element->attributes->length; $i++){
					$attributes[] = $element->attributes[$i];
				}
			}
			return $attributes;
		}
		
		protected function getAttributesByElement(HtmlDomElement $element){
			$attributes  = [];
			if($element->hasAttributes()){
				for($i=0; $i < $element->attributes->length; $i++){
					$attributes[] = $element->attributes[$i];
				}
			}
			return $attributes;
		}
		
		protected function getFragmentElement(DOMDocument $dom, HtmlDomElement $element, string $value){
			$finalElement = null;
			if(empty($value)){
				$value = '<div></div>';
			}
			
			// first, empty the element
			for ($x = $element->childNodes->length - 1; $x >= 0; $x--) {
				$element->removeChild($element->childNodes->item($x));
			}
			
			// $value holds our new inner HTML
			$f = $dom->createDocumentFragment();
			// appendXML() expects well-formed markup (XHTML)
			$result = @$f->appendXML($value); // @ to suppress PHP warnings
			if ($result) {
				if ($f->hasChildNodes()) {
					$finalElement = $f;
				}
			} else {
				// $value is probably ill-formed
				$f = new DOMDocument();
				$value = mb_convert_encoding($value, 'HTML-ENTITIES', 'UTF-8');
				// Using <htmlfragment> will generate a warning, but so will bad HTML
				// (and by this point, bad HTML is what we've got).
				// We use it (and suppress the warning) because an HTML fragment will
				// be wrapped around <html><body> tags which we don't really want to keep.
				// Note: despite the warning, if loadHTML succeeds it will return true.
				$result = @$f->loadHTML('<htmlfragment>' . $value . '</htmlfragment>');
				if ($result) {
					$import = $f->getElementsByTagName('htmlfragment')->item(0);
					foreach ($import->childNodes as $child) {
						$finalElement = $dom->importNode($child, true);
					}
				} else {
					// TODO: Write log to figure out this exception case if occurs
					// oh well, we tried, we really did. :(
					// this element is now empty
				}
			}
			return $finalElement;
		}
		
	}
