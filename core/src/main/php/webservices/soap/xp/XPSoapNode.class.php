<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'xml.Node',
    'webservices.soap.Parameter',
    'webservices.soap.types.SOAPBase64Binary',
    'webservices.soap.types.SOAPHexBinary',
    'webservices.soap.types.SOAPDateTime',
    'webservices.soap.types.SOAPHashMap',
    'webservices.soap.types.SOAPLong',
    'webservices.soap.types.SOAPDouble'
  );

  /**
   * SOAP Node
   *
   * @see   xp://xml.Node
   */
  class XPSoapNode extends Node {
    public 
      $namespace= 'ctl';
    
    /**
     * Get type name by content
     *
     * @param   var content
     * @return  string typename, e.g. "xsd:string"
     */
    protected function _typeName($content) {
      static $tmap= array(      // Mapping PHP-typename => SOAP-typename
        'double'        => 'float',
        'integer'       => 'int'
      );
      
      $t= gettype($content);
      if (isset($tmap[$t])) $t= $tmap[$t];
      return 'xsd:'.$t;
    }
    
    /**
     * Format content
     *
     * @param   var content
     * @return  var content, formatted, if necessary
     */
    protected function _contentFormat($content) {
      if (is_bool($content)) {
        return $content ? 'true' : 'false';
      }
      return $content;
    }
    
    /**
     * Get content in iso-8859-1 encoding (the default).
     *
     * @param   string encoding
     * @param   var namespaces
     * @return  var data
     */
    public function getContent($encoding= NULL, $namespaces= NULL) {
      $ret= parent::getContent();
      @list($ns, $t)= explode(':', @$this->getAttribute($namespaces[XMLNS_XSI].':type'));
      
      switch (strtolower($t)) {
        case 'base64':
        case 'base64binary':
          
          return new SOAPBase64Binary($ret, $encoded= TRUE);
          break;
        
        case 'hexbinary':
          return new SOAPHexBinary($ret, $encoded= TRUE);
          break;
        
        case 'boolean':
          return (
            (0 == strncasecmp('true', $ret, 4)) || 
            (0 == strncasecmp('1', $ret, 1))
          ) ? TRUE : FALSE;
         
        case 'long':
        case 'int':
          $t= 'integer';
          break;
          
        case 'decimal':  
        case 'float':
        case 'double':
          $t= 'double';
          break;
          
        case 'date':
        case 'datetime':    // ISO 8601: http://www.w3.org/TR/xmlschema-2/#ISO8601 http://www.w3.org/TR/xmlschema-2/#dateTime
          return new Date($ret);
          break;
          
        default:
          $t= 'string';
      }
      
      // Decode if necessary
      switch (strtolower($encoding)) {
        case 'utf-8': $ret= iconv('utf-8', xp::ENCODING, $ret); break;
      }

      // Set type
      settype($ret, $t);

      return $ret; 
    }
    
    /**
     * Marshaller
     *
     * @param   webservices.soap.xp.XPSoapNode child
     * @param   var value
     * @param   webservices.soap.xp.XPSoapMapping mapping
     */
    protected function _marshall($child, $value, $mapping) {
      static $ns= 0;

      if ($value instanceof String) {
        $value= (string)$value;
        // Fallthrough intended
      }

      if ($value instanceof Double) {
        $value= new SOAPDouble($value->floatValue());
        // Fallthrough intended
      }

      if ($value instanceof Boolean) {
        $value= $value->value;
        // Fallthrough intended
      }

      if ($value instanceof Long) {
        $value= new SOAPLong($value->value);
        // Fallthrough
      }

      if ($value instanceof Integer) {
        $value= $value->intValue();
        // Fallthrough
      }

      if (is_scalar($value)) {          // Scalar
        $child->setAttribute('xsi:type', $child->_typeName($value));
        $child->setContent($child->_contentFormat($value));
        return;
      }
      
      if (is_null($value)) {            // NULL
        $child->setAttribute('xsi:nil', 'true');
        return;
      }
      
      if (is_array($value)) {           // Array
        if (is_numeric(key($value))) {
          $child->setAttribute('xsi:type', 'SOAP-ENC:Array');
          $child->setAttribute('SOAP-ENC:arrayType', 'xsd:anyType['.sizeof($value).']');
        } else {
          $child->setAttribute('xsi:type', 'xsd:struct');
          if (empty($value)) $child->setAttribute('xsi:nil', 'true');
        }
        $this->_recurse($child, $value, $mapping);
        return;
      }

      if ($value instanceof Parameter) {  // Named parameter
        $child->setName($value->name);
        $this->_marshall($child, $value->value, $mapping);
        return;
      }

      if ($value instanceof Date) {       // Date
        $value= new SOAPDateTime($value->getHandle());
        // Fallthrough intended
      }
      
      if ($value instanceof Hashmap) {    // Hashmap
        $value= new SOAPHashMap($value->_hash);
        // Fallthrough intended
      }

      if ($value instanceof SoapType) {   // Special SoapTypes
        if (FALSE !== ($name= $value->getItemName())) $child->setName($name);
        $this->_marshall($child, $value->toString(), $mapping);

        // Specified type
        if (NULL !== ($t= $value->getType())) $child->setAttribute('xsi:type', $t);

        // A node
        if (isset($value->item)) {
          // TODO FIXME
          $child->setAttributes($value->item->getAttributes());
          $newChildren= array_merge($child->getChildren(), $value->item->getChildren());
          $child->clearChildren();

          foreach ($newChildren as $c) {
            $child->addChild($c);
          }
        }

        return;
      }
      
      if (($value instanceof Generic) && NULL !== ($qname= $mapping->qnameFor($value->getClass()))) {
        $ns++;
        $child->setAttribute('xmlns:ns'.$ns, $qname->namespace);
        $child->setAttribute('xsi:type', 'ns'.$ns.':'.$qname->localpart);
        
        $this->_recurse($child, get_object_vars($value), $mapping);
        return;
      }
      
      if ($value instanceof Generic) {     // XP objects
        $child->setAttribute('xmlns:xp', 'http://xp-framework.net/xmlns/xp');
        $child->setAttribute('xsi:type', 'xp:'.$value->getClassName());
        $this->_recurse($child, get_object_vars($value), $mapping);
        return;
      }
      
      if (is_object($value)) {          // Any other object, e.g. "stdClass"
        $ns++;
        $child->setAttribute('xmlns:ns'.$ns, 'http://xp-framework.net/xmlns/php');
        $child->setAttribute('xsi:type', 'ns'.$ns.':'.get_class($value));
        $this->_recurse($child, get_object_vars($value), $mapping);
        return;        
      }
      
      // Any other type is simply ignored
    }
    
    /**
     * Recurse an array
     *
     * @param   xml.Node e element to add array to
     * @param   array a
     * @param   webservices.soap.xp.XPSoapMapping mapping
     */
    protected function _recurse($e, $a, $mapping) {
      foreach (array_keys($a) as $field) {
        if ('_' == $field{0}) continue;
        $this->_marshall(
          $e->addChild(new self(is_numeric($field) ? 'item' : $field)),
          $a[$field],
          $mapping
        );
      }
    }
    
    /**
     * Create a node from an array
     *
     * Usage example:
     * <code>
     *   $n= Node::fromArray($array, 'elements');
     * </code>
     *
     * @param   array arr
     * @param   string name default 'array'
     * @param   webservices.soap.xp.XPSoapMapping mapping
     * @return  xml.Node
     */
    public static function fromArray($arr, $name= 'array', $mapping= NULL) {
      $n= new self($name);
      $n->_recurse($n, $arr, $mapping);
      return $n;  
    }
    
    /**
     * Create a node from an object. Will use class name as node name
     * if the optional argument name is omitted.
     *
     * Usage example:
     * <code>
     *   $n= Node::fromObject($object);
     * </code>
     *
     * @param   lang.Generic obj
     * @param   string name default NULL
     * @param   webservices.soap.xp.XPSoapMapping mapping
     * @return  xml.Node
     */
    public static function fromObject($obj, $name= NULL, $mapping= NULL) {
      return self::fromArray(
        get_object_vars($obj), 
        (NULL === $name) ? get_class($obj) : $name,
        $mapping
      );
    }
  }
?>
