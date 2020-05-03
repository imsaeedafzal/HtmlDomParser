<?php
	
	class HtmlDomAttr extends DOMAttr
	{
		public function value($value = null) {
			if($value != null){
				$this->nodeValue = $value;
			}
			return $this->nodeValue;
		}
		
		public function name(){
			return $this->name;
		}
	}
