<?php

/**
 * SimpleXMLElement + cdata
 *
 */

class SimpleXMLExtended extends SimpleXMLElement{
	
	/**
	 * Wrap all node content into cdata element
	 *
	 * @param string $name
	 * @param string $value
	 */
	public function addChildCD($name, $value){
		if(is_string($value)){
			$element =  parent::addChild($name);
			$element->addCData($value);
			return $element;
		}else {
			return parent::addChild($name, $value);
		}
	}
	
	/**
	 * Add cdata to the node
	 *
	 * @param string $cdata_text
	 */
	public function addCData($cdata_text){
		$node= dom_import_simplexml($this);   
   		$no = $node->ownerDocument;   
   		$node->appendChild($no->createCDATASection($cdata_text)); 
	}
	
	/**
	 * Get formated output
	 *
	 * @return string
	 */
	public function asXMLFormated(){
		$dom = dom_import_simplexml($this)->ownerDocument;
		$dom->formatOutput = true;
		return $dom->saveXML();		
	}
}

?>