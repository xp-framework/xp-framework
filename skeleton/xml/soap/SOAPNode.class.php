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
    var $_tmap, $_cmap;
    var $namespace= 'ctl';
    
    /**
     * Constructor
     *
     * @access  public
     * @param   array params default NULL
     */
    function __construct($params= NULL) {
      parent::__construct($params);
      
      $this->_tmap= new StdClass();
      $this->_tmap->export= array(                      
        'double'        => 'float',
        'integer'       => 'int'
      );
      $this->_tmap->import= array_flip($this->_tmap->export);
    }
    
    function _typeName($content) {
      $t= gettype($content);
      if (isset($this->_tmap->export[$t])) $t= $this->_tmap->export[$t];
      return 'xsd:'.$t;
    }
    
    function _contentFormat($content) {
      switch (gettype($content)) {
        case 'boolean': return $content ? 'true' : 'false';
        case 'string': return htmlspecialchars($content);
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
          return new Date(str_replace('T', ' ', $ret));
          break;
      }        
      if (isset($this->_tmap->import[$t])) $t= $this->_tmap->import[$t];

      // TODO: Andere Encodings?
      switch (strtolower($encoding)) {
        case 'utf-8': $ret= utf8_decode($ret); break;
      }

      // Rip HTML entities
      $ret= strtr($ret, array_flip(get_html_translation_table(HTML_ENTITIES)));

      // Set type
      if (!@settype($ret, $t)) settype($ret, 'string');         // Default "string"

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
          $ns++;
          $type= 'ns'.$ns.':struct';
          
          // Class name
          if (method_exists($value, 'getClassName')) {
            $name= $value->getClassName();
            $namespace= 'xp';
          } else {
            $name= get_class($value);
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
