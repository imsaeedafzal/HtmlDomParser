<?php
	
	namespace HtmlDomParser;
	
	use DOMElement;
	
	class HtmlDomElement extends DOMElement
	{
		use AttributeHandler;
		
		public $properties;
		
		
		public function getAttributes(){
			return $this->getElementAttributes($this);
		}
		
		public function copyAttributes(HtmlDomElement $newElement, HtmlDomElement $element){
			$this->copyElementAttributes($this->ownerDocument, $newElement, $element);
		}
		
		public function __set($name, $value)
		{
			$name = strtolower($name);
			
			if($name == 'outerhtml'){
				$html = $this->elementOuterHtml($this->ownerDocument, $this, $value, false);
				$propertyId = $this->getLineNo();
				$this->properties[$propertyId] = $html;
				$this->properties[$name] = $html; // to get the updated outerHtml if property set before calling
				return $html;
			}
			
			else if ($name == 'innerhtml') {
				$html = $this->elementInnerHtml($this->ownerDocument, $this, $value, false);
				$propertyId = $this->getLineNo();
				$this->properties[$propertyId] = $html;
				return $html;
			}
			
			else {
				$this->properties[$name] = $value;
				//$trace = debug_backtrace();
				//trigger_error('Undefined property via __set(): ' . $name
				//	. ' in ' . $trace[0]['file'] . ' on line '
				//	. $trace[0]['line'], E_USER_NOTICE);
			}
		
		}
		
		public function __get($name)
		{
			$name = strtolower($name);
			
			if ($name == 'outerhtml') {
				$propertyId = $this->getLineNo();
				$html = $this->nodeValue;
				if(!isset($this->properties[$name])) {
					// Return the outerHtml with innerHtml if the outerHtml property hasn't been set
					$html = $this->ownerDocument->saveXML($this);
				}
				else if(isset($this->properties[$propertyId])) {
					// Return the updated outerHtml
					$html = $this->properties[$propertyId];
				}
				return $html;
			}
			
			else if ($name == 'innerhtml') {
				$propertyId = $this->getLineNo();
				$html = $this->nodeValue;
				if(isset($this->properties[$propertyId])) {
					$html = $this->properties[$propertyId];
				}
				return $html;
			}
			
			else {
				return (isset($this->properties[$name])) ? $this->properties[$name] : null;
			}
		}
		
		public function __toString()
		{
			return '[' . $this->tagName . ']';
		}
	}
