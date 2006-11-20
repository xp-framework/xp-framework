<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'xml.Node',
    'webservices.soap.types.SOAPBase64Binary',
    'webservices.soap.types.SOAPHexBinary',
    'webservices.soap.types.SOAPDateTime',
    'webservices.soap.types.SOAPHashMap'
  );

  /**
   * SOAP Node
   *
   * @see   xp://xml.Node
   */
  class SOAPNode extends Node {
    var 
      $namespace= 'ctl';
    
    /**
     * Get type name by content
     *
     * @access  private
     * @param   &mixed content
     * @return  string typename, e.g. "xsd:string"
     */
    function _typeName(&$content) {
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
     * @access  private
     * @param   &mixed content
     * @return  &mixed content, formatted, if necessary
     */
    function &_contentFormat(&$content) {
      if (is_bool($content)) {
        return $content ? 'true' : 'false';
      }
      return $content;
    }
    
    /**
     * Get content in iso-8859-1 encoding (the default).
     *
     * @access  public
     * @param   string encoding
     * @param   &mixed namespaces
     * @return  &mixed data
     */
    function &getContent($encoding, &$namespaces) {
      $ret= $this->content;
      @list($ns, $t)= explode(':', @$this->attribute[$namespaces[XMLNS_XSI].':type']);
      
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
          sscanf($ret, '%4d-%2d-%2dT%2d:%2d:%2d', $year, $month, $day, $hour, $minute, $second);
          return new Date(mktime($hour, $minute, $second, $month, $day, $year));
          break;
          
        default:
          $t= 'string';
      }
      
      // Decode if necessary
      switch (strtolower($encoding)) {
        case 'utf-8': $ret= utf8_decode($ret); break;
      }

      // Set type
      settype($ret, $t);

      return $ret; 
    }
    
    /**
     * Marshaller
     *
     * @access  private
     * @param   &webservices.soap.SOAPNode child
     * @param   mixed value
     * @param   &webservices.soap.SOAPMapping mapping
     */
    function _marshall(&$child, $value, &$mapping) {
      static $ns= 0;
      
      if (is_scalar($value)) {          // Scalar
        $child->attribute['xsi:type']= $child->_typeName($value);
        $child->setContent($child->_contentFormat($value));
        return;
      }
      
      if (is_null($value)) {            // NULL
        $child->attribute['xsi:nil']= 'true';
        return;
      }
      
      if (is_array($value)) {           // Array
        if (is_numeric(key($value))) {
          $child->attribute['xsi:type']= 'SOAP-ENC:Array';
          $child->attribute['SOAP-ENC:arrayType']= 'xsd:anyType['.sizeof($value).']';
        } else {
          $child->attribute['xsi:type']= 'xsd:struct';
          if (empty($value)) $child->attribute['xsi:nil']= 'true';
        }
        $this->_recurse($child, $value, $mapping);
        return;
      }

      if (is_a($value, 'Parameter')) {  // Named parameter
        $child->name= $value->name;
        $this->_marshall($child, $value->value, $mapping);
        return;
      }
      
      if (is_a($value, 'Date')) {       // Date
        $value= &new SOAPDateTime($value->_utime);
        // Fallthrough intended
      }
      
      if (is_a($value, 'Hashmap')) {    // Hashmap
        $value= &new SOAPHashMap($value->_hash);
        // Fallthrough intended
      }
      
      if (is_a($value, 'SoapType')) {   // Special SoapTypes
        if (FALSE !== ($name= $value->getItemName())) $child->name= $name;
        $this->_marshall($child, $value->toString(), $mapping);
        
        // Specified type
        if (NULL !== ($t= $value->getType())) $child->attribute['xsi:type']= $t;
        
        // A node
        if (isset($value->item)) {
          $child->attribute= $value->item->attribute;
          $child->children= &array_merge($child->children, $value->item->children);
        }
        return;
      }
      
      if (NULL !== ($qname= $mapping->qnameFor($value->getClass()))) {
        $ns++;
        $child->attribute['xmlns:ns'.$ns]= $qname->namespace;
        $child->attribute['xsi:type']= 'ns'.$ns.':'.$qname->localpart;
        
        $this->_recurse($child, get_object_vars($value), $mapping);
        return;
      }
      
      if (is_a($value, 'Collection')) { // XP collection
        $child->attribute['xsi:type']= 'SOAP-ENC:Array';
        $child->attribute['xmlns:xp']= 'http://xp-framework.net/xmlns/xp';
        $child->attribute['SOAP-ENC:arrayType']= 'xp:'.$value->getElementClassName().'['.$value->size().']';
        $this->_recurse($child, $value->values(), $mapping);
        return;
      }
      
      if (is_a($value, 'Object')) {     // XP objects
        $child->attribute['xmlns:xp']= 'http://xp-framework.net/xmlns/xp';
        $child->attribute['xsi:type']= 'xp:'.$value->getClassName();
        $this->_recurse($child, get_object_vars($value), $mapping);
        return;
      }
      
      if (is_object($value)) {          // Any other object, e.g. "stdClass"
        $ns++;
        $child->attribute['xmlns:ns'.$ns]= 'http://xp-framework.net/xmlns/php';
        $child->attribute['xsi:type']= 'ns'.$ns.':'.get_class($value);
        $this->_recurse($child, get_object_vars($value), $mapping);
        return;        
      }
      
      // Any other type is simply ignored
    }
    
    /**
     * Recurse an array
     *
     * @access  protected
     * @param   &xml.Node e element to add array to
     * @param   array a
     * @param   &webservices.soap.SOAPMapping mapping
     */
    function _recurse(&$e, $a, &$mapping) {
      foreach (array_keys($a) as $field) {
        if ('_' == $field{0}) continue;
        $this->_marshall(
          $e->addChild(new SOAPNode(is_numeric($field) ? 'item' : $field)),
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
     *   $n= &Node::fromArray($array, 'elements');
     * </code>
     *
     * @model   static
     * @access  public
     * @param   array arr
     * @param   string name default 'array'
     * @param   &webservices.soap.SOAPMapping mapping
     * @return  &xml.Node
     */
    function &fromArray($arr, $name= 'array', $mapping) {
      $n= &new SOAPNode($name);
      $n->_recurse($n, $arr, $mapping);
      return $n;  
    }
    
    /**
     * Create a node from an object. Will use class name as node name
     * if the optional argument name is omitted.
     *
     * Usage example:
     * <code>
     *   $n= &Node::fromObject($object);
     * </code>
     *
     * @model   static
     * @access  public
     * @param   object obj
     * @param   string name default NULL
     * @param   &webservices.soap.SOAPMapping mapping
     * @return  &xml.Node
     */
    function &fromObject($obj, $name= NULL, $mapping) {
      return SOAPNode::fromArray(
        get_object_vars($obj), 
        (NULL === $name) ? get_class($obj) : $name,
        $mapping
      );
    }
  }
?>
