<?php
  import('xml.Node');

  class WSDLNode extends Node {
    var $_tmap, $_cmap;
    var $namespace= 'ctl';
    
    function __construct($params= NULL) {
      Node::__construct($params);
      
      $this->_tmap= new StdClass();             // Typnamen-Mapping
      $this->_tmap->export= array(                      
        'double'        => 'float',
        'integer'       => 'int'
      );
      $this->_tmap->import= array_flip($this->_tmap->export);
    }
    
    function _typeName($content) {
    
      // Pfusch! :-)
      if (strstr($content, '\1')) {
        $t= substr($content, 0, strpos($content, '\1'));
      } else {
        $t= gettype($content);
      }
      if (isset($this->_tmap->export[$t])) $t= $this->_tmap->export[$t];
      return $t;
    }
    
    function _contentFormat($content) {
    
      // Pfusch! :-)
      if (strstr($content, '\1')) $content= substr($content, strpos($content, '\1')+ 2);

      switch (gettype($content)) {
        case 'boolean': return $content ? 'true' : 'false';
        case 'NULL': $this->attribute['xsi:null']= 'true'; return '';
        case 'string': return htmlspecialchars($content);
      }
      return $content;
    }
    
    function getContent($encoding= NULL) {
      $ret= $this->content;
      list($ns, $t)= explode(':', $this->attribute['xsi:type']);
      
      switch ($t) {
        case 'base64Binary':
          $ret= base64_decode($ret);
          $t= 'string';
          break;
          
        case 'long':
          $t= 'int';
          break;
          
        case 'date':
        case 'dateTime':    // ISO 8601: http://www.w3.org/TR/xmlschema-2/#ISO8601 http://www.w3.org/TR/xmlschema-2/#dateTime
          $ret= strtotime(str_replace('T', ' ', $ret));
          $t= 'int';
          
          break;
      }        
      if (isset($this->_tmap->import[$t])) $t= $this->_tmap->import[$t];

      // TODO: Andere Encodings?
      if ($encoding == 'utf-8') $ret= utf8_decode($ret);

      // echo 'Setting "'.$ret.'" to '.$t."\n";
      try(); {
        settype($ret, $t);
      } if ($e= catch(E_ANY_EXCEPTION)) {
        settype($ret, 'string');         // Default "string"
      } 
      return $ret;
    }
      
    function _recurseArray(&$elem, $arr) {
      $nodeType= get_class($this);
      if (!is_array($arr)) return;
      foreach ($arr as $field=> $value) {
        $child= &$elem->addChild(new $nodeType(array(
          'name'        => (is_numeric($field) ? preg_replace('=s$=', '', $elem->name) : $field)
        )));
        if (is_array($value)) {
          $this->_recurseArray($child, $value);
          if (is_numeric(key($value))) {
            $child->attribute['xsi:type']= 'SOAP-ENC:Array';
            $child->attribute['SOAP-ENC:arrayType']= 'xsd:anyType['.sizeof($value).']';
          } else {
            $child->attribute['xsi:type']= 'xsd:ur-type';
          }
        } else if (is_object($value) && ('soap' == substr(get_class($value), 0, 4))) {
          $child->attribute['xsi:type']= 'xsd:'.$value->getType();
          $child->setContent($value->toString());
        } else if (is_object($value)) {
          $this->_recurseArray($child, get_object_vars($value));
          $child->attribute['xsi:type']= 'xsd:struct';
        } else {
          $child->attribute['xsi:type']= 'xsd:'.$child->_typeName($value);
          $child->setContent($child->_contentFormat($value));
        }
      }
    }

  }
?>
