<?php
class wm_DomXml{

	var $dom;
	var $ver;
	var $isLoaded;

	function wm_DomXml($version, $charset)
	{
		$this->ver = (floor(phpversion())>=5) ? true : false;
		if ($this->ver){
			$this->dom = new DOMDocument($version, $charset);
			$this->dom->preserveWhiteSpace = false;
			$this->dom->formatOutput = false;
		} else {
			$this->dom = domxml_new_doc($version);
		}
	}

	function wm_Load($FileName)
	{
		$this->ver = (floor(phpversion())>=5) ? true : false;
		if ($this->ver){
	@		$this->isLoaded = $this->dom->load($FileName);
		} else {
	@		$this->dom = domxml_open_file($FileName);
			if (!$this->dom) {
				$this->isLoaded = false;
			} else {
				$this->isLoaded = true;
			}
		}
	}

	function GetChildNodes()
	{
		if ($this->ver){
			return $this->dom->documentElement->childNodes;
		} else {
			$root = $this->dom->document_element();
			return $root->child_nodes();
		}
	}

	function GetLength($childNodes)
	{
		if ($this->ver){
			return $childNodes->length;
		} else {
			return count($childNodes);
		}
	}

	function GetNode($childNodes, $intCntr)
	{
		if ($this->ver){
			return $childNodes->Item($intCntr);
		} else {
			return $childNodes[$intCntr];
		}
	}

	function GetName($objNode)
	{
		if ($this->ver){
			return $objNode->nodeName;
		} else {
			return $objNode->node_name();
		}
	}

	function GetData($objNode)
	{
		if ($this->ver){
			return $objNode->textContent;
		} else {
			$Nodes = $objNode->child_nodes();
			if (count($Nodes) > 0)
				return $Nodes[0]->content;
			else
				return '';
		}
	}

	function CreateDomElement($element)
	{
		if ($this->ver){
			$child = $this->dom->createElement($element);
		} else {
			$child = $this->dom->create_element($element);
		}
		return $child;
	}

	function AppendDomElement($element)
	{
		if ($this->ver){
			$this->dom->appendChild($element);
		} else {
			$this->dom->append_child($element);
		}
	}

	function CreateNewElement($element)
	{
		if ($this->ver){
			$child = $this->dom->createElement($element);
		} else {
			$child = $this->dom->create_element($element);
		}
		return $child;
	}

	function AppendElement($parent, $element)
	{
		if ($this->ver){
			$parent->appendChild($element);
		} else {
			$parent->append_child($element);
		}
	}

	function AddAttribute($element, $attr, $value)
	{
		if ($this->ver){
			$x = $element->setAttributeNode(new DOMAttr($attr, $value));
		} else {
			$x = $element->set_attribute($attr, $value);
		}
	}

	function AddAttributes($element, $attr_ar)
	{
		if ($this->ver){
			foreach ($attr_ar as $attr => $value)
				$x = $element->setAttributeNode(new DOMAttr($attr, $value));
		} else {
			foreach ($attr_ar as $attr => $value)
				$x = $element->set_attribute($attr, $value);
		}
	}

	function CreateElementWithCDATA($parent, $element, $cdata)
	{
		if (empty($cdata)) $cdata = ' ';
		if ($this->ver){
			$child = $this->dom->createElement($element);
			$CDATAchild = $this->dom->createCDATASection($cdata);
			$child->appendChild($CDATAchild);
			$parent->appendChild($child);
		} else {
			$child = $this->dom->create_element($element);
			$CDATAchild = $this->dom->create_cdata_section($cdata);
			$child->append_child($CDATAchild);
			$parent->append_child($child);
		}
	}

	function CreateElementWithData($parent, $element, $data)
	{
		if ($this->ver){
			$child = $this->dom->createElement($element);
			$child->appendChild($this->dom->createTextNode($data)); 
			$parent->appendChild($child);
		} else {
			$child = $this->dom->create_element($element);
			$TextChild = $this->dom->create_text_node($data);
			$child->append_child($TextChild); 
			$parent->append_child($child);
		}
	}

	function AddError($parent, $error)
	{
		if ($this->ver){
			$child = $this->dom->createElement('webmail_error');
			$x = $child->setAttributeNode(new DOMAttr('description', $error));
			$parent->appendChild($child);
		} else {
			$child = $this->dom->create_element('webmail_error');
			$x = $child->set_attribute('description', $error);
			$parent->append_child($child);
		}
	}

	function SaveDomXml()
	{
		if ($this->ver){
			$x = $this->dom->saveXML();
		} else { 
			$x = $this->dom->dump_mem(false);
		}
		return $x;
	}
}
?>
