<?php
  import('xml.XML');

  class Node extends XML {
    var 
      $name,
      $attribute,
      $content;
    
    function __construct($params= NULL) {
      XML::__construct($params);
    }
    
    function _recurseArray(&$elem, $arr) {
      $nodeType= get_class($this);
      foreach ($arr as $field=> $value) {
        $child= &$elem->addChild(new $nodeType(array(
          'name'        => (is_numeric($field) ? preg_replace('=s$=', '', $elem->name) : $field)
        )));
        if (is_array($value)) {
          $this->_recurseArray($child, $value);
        } else if (is_object($value)) {
          $this->_recurseArray($child, get_object_vars($value));
        } else {
          if (is_string($value)) $value= htmlspecialchars($value);
          $child->setContent($value);
        }
      }
    }
    
    function fromArray($arr, $name= 'array') {
      $this->name= $name;
      $this->_recurseArray($this, $arr);
      return $this;  
    }
    
    function fromObject($obj) {
      $this->name= get_class($obj);
      $this->_recurseArray($this, get_object_vars($obj));
      return $this;
    }
    
    function setContent($content) {
      $this->content= $content;
    }
    
    function getContent() {
      return $this->content;
    }
    
    function getSource($indent= TRUE, $inset= '') {
      $xml= $inset.'<'.$this->name;
      
      // Attribute
      $sep= '';
      if (isset($this->attribute) and is_array($this->attribute)) {
        $sep= ($indent || sizeof($this->attribute)< 3) ? '' : "\n{$inset}";
        foreach ($this->attribute as $key=> $val) {
          $xml.= sprintf('%s %s="%s"', $sep, $key, $val);
        }
      }
      $xml.= $sep;
      
      // Kein Content (oder leer?) *UND* keine weiteren Elemente? => Tag zumachen!
      if (!$indent && isset($this->content)) $this->content= trim(chop($this->content));
      if (
        (!isset($this->content) || @$this->content === '') &&
        (!isset($this->children))
      ) {
        return $xml."/>\n";
      }
      
      if ($indent) {
        $xml.= ">\n";
        if (isset($this->content) && $this->content !== '') $xml.= "{$inset}  {$this->content}\n";
      } else {
        $xml.= '>';
        if (isset($this->content) && $this->content !== '') $xml.= $this->content;
      }
      
      // Unterelemente, falls vorhanden
      if (isset($this->children)) {
        if (!$indent) $xml.= "\n";
        foreach ($this->children as $idx=> $child) $xml.= $this->children[$idx]->getSource($indent, $inset.'  ');
        if (!$indent) $xml.= $inset;
      }
      
      return $xml.($indent ? $inset : '').'</'.$this->name.">\n";
    }
    
    function &addChild($child) {
      if (!is_object($child)) return throw(E_PARAM_EXCEPTION);
      $this->children[]= &$child;
      return $child;
    }
  }
?>
