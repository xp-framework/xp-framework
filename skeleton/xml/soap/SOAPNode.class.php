<?php
  uses(
    'xml.Node',
    'xml.soap.types.SOAPBase64Binary',
    'xml.soap.types.SOAPDateTime',
    'xml.soap.types.SOAPNamedItem'
  );

  class SOAPNode extends Node {
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
      $t= gettype($content);
      if (isset($this->_tmap->export[$t])) $t= $this->_tmap->export[$t];
      return $t;
    }
    
    function _contentFormat($content) {
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
        case 'base64':
        case 'base64Binary':
          return new SOAPBase64Binary(base64_decode($ret));
          break;
        
        case 'boolean':
          return $ret == 'false' ? FALSE : TRUE;
         
        case 'long':
          $t= 'int';
          break;
          
        case 'date':
        case 'dateTime':    // ISO 8601: http://www.w3.org/TR/xmlschema-2/#ISO8601 http://www.w3.org/TR/xmlschema-2/#dateTime
          return new SOAPDateTime(strtotime(str_replace('T', ' ', $ret)));
          break;
      }        
      if (isset($this->_tmap->import[$t])) $t= $this->_tmap->import[$t];

      // TODO: Andere Encodings?
      if ($encoding == 'utf-8') $ret= utf8_decode($ret);

      // echo 'Setting "'.$ret.'" to '.$t."\n";
      if (!@settype($ret, $t)) {
        settype($ret, 'string');         // Default "string"
      } 
      return $ret;
    }
      
    function _recurseArray(&$elem, $arr) {
      static $ns;
      
      $nodeType= get_class($this);
      if (!is_array($arr)) return;
      foreach ($arr as $field=> $value) {
      
        // Private Variablen
        if ('_' == $field{0}) continue;
        
        $child= &$elem->addChild(new $nodeType(array(
          'name'        => (is_numeric($field) ? preg_replace('=s$=', '', $elem->name) : $field)
        )));
        
        // Arrays
        if (is_array($value)) {
          $this->_recurseArray($child, $value);
          if (is_numeric(key($value))) {
            $child->attribute['xsi:type']= 'SOAP-ENC:Array';
            $child->attribute['SOAP-ENC:arrayType']= 'xsd:anyType['.sizeof($value).']';
          } else {
            $child->attribute['xsi:type']= 'xsd:ur-type';
          }
          continue;
        }
        
        // Datumstypen
        if (
          (is_object($value)) && 
          (is_a($value, 'Date'))
        ) {
          $value= new SOAPDateTime($value->_utime);
        }
        
        // SOAP-Typen
        if (
          (is_object($value)) && 
          ('soap' == substr(get_class($value), 0, 4))
        ) {
        
          // Namen
          if (FALSE !== ($name= $value->getItemName())) $child->name= $name;
          
          // Typen
          $child->attribute['xsi:type']= 'xsd:'.$value->getType();
          
          // Inhalt
          $child->setContent($value->toString());
          continue;
        }
        
        // Andere Objekte 
        if (is_object($value)) {
          $this->_recurseArray($child, get_object_vars($value));
          $ns++;
          $child->attribute['xsi:type']= 'ns'.$ns.':struct';
          $child->attribute['xmlns:ns'.$ns]= get_class($value);
          continue;
        }
        
        // Skalare Datentypen
        $child->attribute['xsi:type']= 'xsd:'.$child->_typeName($value);
        $child->setContent($child->_contentFormat($value));
      }
    }

  }
?>
