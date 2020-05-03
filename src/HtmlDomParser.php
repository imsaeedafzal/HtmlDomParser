<?php
	
	namespace HtmlDomParser;
	
	class HtmlDomParser
	{
		use HtmlDomParser\AttributeHandler;
		
		public static $disableLibXmlErrors = true;
		public $document;
		
		public function __construct()
		{
			if(self::disableLibXmlErrors()) {
				libxml_use_internal_errors(true);
			}
			
			$this->document = new DOMDocument;
			//$this->document = new DOMDocument('1.0', 'UTF-8');
			
			$this->document->registerNodeClass('DOMElement', 'HtmlDomElement');
			$this->document->registerNodeClass('DOMAttr', 'HtmlDomAttr');
		}
		
		// TODO: Need to check these functions getElementByClass(), getElementByTagNameNS()
		// TODO: Implement more selectors especially with the attributes
		public function find(string $selector) {
			$isClass = (strpos($selector, '.') !== false) ? true : false;
			$isId = (strpos($selector, '#') !== false) ? true : false;
			$isElement = (!$isClass && !$isId) ? true : false;
			$list = null;
			if($isClass && !$isId && !$isElement){
				$list = $this->getElementByClass($selector);
			}
			else if(!$isClass && $isId && !$isElement){
				$list = $this->getElementById($selector);
			}
			else if(!$isClass && !$isId && $isElement){
				$list = $this->document->getElementsByTagName($selector);
			}
			return $list;
		}
		
		public function query($path)
		{
			$xpath = new DOMXPath($this->document);
			// Or you can also use xPath DOM to find elements by tag with/without attributes.
			// e.g.: xPath = //div
			// e.g.: xPath = //div[@class]
			return $elements = $xpath->query($path);
		}
		
		public function getElementById($id)
		{
			$xpath = new DOMXPath($this->document);
			$id = str_replace('#', '', $id);
			// always return first as it's unique instead of returning an array. Use this: ->item(0);
			// Or you can also use xPath DOM to find elements by tag with/without attributes.
			// e.g.: xPath = //div
			// e.g.: xPath = //div[@class]
			return $elements = $xpath->query("//*[@id='$id']");
		}
		
		public function getElementByClass($class)
		{
			$xpath = new DOMXPath($this->document);
			$class = str_replace('.', '', $class);
			return $xpath->query("//*[contains(@class, '$class')]");
		}
		
		public function outerHtml(HtmlDomElement $element, string $html){
			$newElement = $this->getFragmentElement($this->document, $element, $html);
			$element->parentNode->replaceChild($newElement, $element); // to replace the html = outerHtml
		}
		
		public function innerHtml(HtmlDomElement $element, string $html){
			$newElement = $this->getFragmentElement($this->document, $element, $html);
			$element->appendChild($newElement); // to append the html = innerHtml
		}
		
		public function loadHtml($html) : void {
			$this->document->loadHTML($html);
		}
		
		public function saveHtml() : string {
			return $this->document->saveHTML();
		}
		
		public static function disableLibXmlErrors(bool $disable = true) : bool {
			if($disable){
				self::$disableLibXmlErrors = true;
			}
			return self::$disableLibXmlErrors;
		}
		
		public static function clearLibXmlErrors() : void {
			libxml_clear_errors();
		}
		
	}
