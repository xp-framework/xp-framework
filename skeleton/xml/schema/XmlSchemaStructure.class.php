<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  define('WSDL_TYPE_COMPLEX',   'complex');
  define('WSDL_TYPE_SIMPLE',    'simple');
  
  define('XSD_TYPE_STRING',     'string');
  define('XSD_TYPE_BOOLEAN',    'boolean');
  define('XSD_TYPE_INT',        'int');
  define('XSD_TYPE_DOUBLE',     'double');
  
  /**
   * Wraps XML Schema structures
   * 
   * A sample XML representation for a complex type is
   * <xmp>
   *   <xsd:complexType name="DirectoryCategory">                      
   *     <xsd:all>                                                     
   *       <xsd:element name="fullViewableName" type="xsd:string"/>    
   *       <xsd:element name="specialEncoding" type="xsd:string"/>     
   *     </xsd:all>                                                    
   *   </xsd:complexType>                                              
   * </xmp>
   *
   * @see http://www.w3.org/TR/xmlschema-1/#Simple_Type_Definition
   * @see http://www.w3.org/TR/xmlschema-1/#Complex_Type_Definition
   */
  class XmlSchemaStructure extends Object {
    var 
      $elements=        array(),
      $type=            '',
      $name=            '',
      $content=         NULL,
      $restriction=     NULL;
     
    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     * @param   string type default WSDL_TYPE_COMPLEX
     */ 
    function __construct($name, $type= WSDL_TYPE_COMPLEX) {
      $this->name= $name;
      $this->type= $type;
      parent::__construct();
    }
    
    /**
     * Returns whether this structure is a complex type
     *
     * @access  public
     * @return  bool TRUE when this is a complex type
     */
    function isComplexType() {
      return $this->type == WSDL_TYPE_COMPLEX;
    }
    
    /**
     * Returns whether this structure is a simple type
     *
     * @access  public
     * @return  bool TRUE when this is a simple type
     */
    function isSimpleType() {
      return $this->type == WSDL_TYPE_SIMPLE;
    }
    
    /**
     * Add an element
     *
     * @access  public
     * @param   string name
     * @param   string type
     * @param   string namespace default 'xsd' 
     */
    function addElement($name, $type, $namespace= 'xsd') {
      $this->elements[$name]= &new stdClass();
      $this->elements[$name]->type= $type;
      $this->elements[$name]->namespace= $namespace;
    }
    
    /**
     * Gets an element by its name. The returned object will have
     * the properties type and namespace
     *
     * @access  public
     * @param   string name
     * @return  &object element
     */
    function &getElement($name) {
      return isset($this->elements[$name]) ? $this->elements[$name] : NULL;
    }
  }
?>
