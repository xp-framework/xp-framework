<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */

  uses(
    'xml.Node',
    'xml.soap.types.SOAPBase64Binary',
    'xml.soap.types.SOAPDateTime',
    'xml.soap.types.SOAPNamedItem'
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
     * @param   string encoding default NULL
     * @return  &mixed data
     */
    function &getContent($encoding= NULL) {
      $ret= $this->content;
      @list($ns, $t)= explode(':', @$this->attribute['xsi:type']);
      
      switch (strtolower($t)) {
        case 'base64':
        case 'base64binary':
          
          return new SOAPBase64Binary($ret, $encoded= TRUE);
          break;
        
        case 'boolean':
          return $ret == 'false' ? FALSE : TRUE;
         
        case 'long':
        case 'int':
          $t= 'integer';
          break;
          
        case 'float'.
          $t= 'double';
          break;
          
        case 'date':
        case 'datetime':    // ISO 8601: http://www.w3.org/TR/xmlschema-2/#ISO8601 http://www.w3.org/TR/xmlschema-2/#dateTime
          return new Date(str_replace('T', ' ', $ret));
          break;
      }
      
      // Decode if necessary
      switch (strtolower($encoding)) {
        case 'utf-8': $ret= utf8_decode($ret); break;
      }

      // Rip HTML entities
      $ret= strtr($ret, array_flip(get_html_translation_table(HTML_ENTITIES)));

      // Set type
      if (!@settype($ret, $t)) settype($ret, 'string');         // Default "string"

      return $ret; 
    }
    
    /**
     * Recurse an array
     *
     * @access  private
     * @param   &mixed elem
     * @param   array arr
     */
    function _recurseArray(&$elem, $arr) {
      static $ns;

      $nodeType= get_class($this);
      if (!is_array($arr)) return;
      foreach ($arr as $field => $value) {      
        if ('_' == $field{0}) continue;     // Ignore "private" members
        
        $child= &$elem->addChild(new $nodeType(array(
          'name'        => (is_numeric($field) ? preg_replace('=s$=', '', $elem->name) : $field)
        )));
        unset($type);
       
        if (is_a($value, 'Date')) $value= &new SOAPDateTime($value->_utime);
        
        if (
          (is_object($value)) && 
          (is_a($value, 'SoapType'))
        ) {       
          // SOAP-Types
          
          if (FALSE !== ($name= $value->getItemName())) $child->name= $name;
          if (isset($value->item)) $child= $value->item;
          
          // Inhalt und Typen
          $content= $value->toString();
          $type= $value->getType();
          if (NULL === $type) {
            $type= $child->_typeName($content);
            $content= $child->_contentFormat($content);
          }
        } else if (is_object($value)) {
        
          // Objekte
          $content= get_object_vars($value);
          
          // Class name
          if (method_exists($value, 'getClassName')) {
            $name= $value->getClassName();
            $namespace= 'xp';
            $type= 'xp:struct';
          } else {
            $name= get_class($value);
            $ns++;
            $type= 'ns'.$ns.':struct';
            $namespace= 'ns'.$ns;
          }
          
          $child->attribute['xmlns:'.$namespace]= $name;
        } else if (is_scalar($value)) {
        
          // Skalare Typen
          $type= $child->_typeName($value);
          $content= $child->_contentFormat($value);
        } else if (NULL === $value) {

          // NULL
          $type= NULL;
          $content= '';
          $child->attribute['xsi:nil']= 'true';

        } else {
        
          // Arrays
          $content= &$value;
        }

        // Arrays
        if (is_array($content)) {
          $this->_recurseArray($child, $content);
          if (isset($type)) {
            $child->attribute['xsi:type']= $type;
          } else {
            if (is_numeric(key($value))) {
              $child->attribute['xsi:type']= 'SOAP-ENC:Array';
              $child->attribute['SOAP-ENC:arrayType']= 'xsd:anyType['.sizeof($value).']';
            } else {
              $child->attribute['xsi:type']= 'xsd:ur-type';
            }
          }
          continue;
        }

        // Skalare Datentypen
        if (NULL !== $type) $child->attribute['xsi:type']= $type;
        $child->setContent($content);
      }
    }

  }
?>
