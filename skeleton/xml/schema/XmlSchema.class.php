<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('xml.schema.XmlSchemaStructure');
  
  /**
   * XML Schema wrapper
   *
   * From 
   * An XML Schema is a set of ·schema components. There are 13 kinds of 
   * component in all, falling into three groups. The primary components, 
   * which may (type definitions) or must (element and attribute 
   * declarations) have names are as follows:
   * <ul>
   *   <li>Simple type definitions</li>
   *   <li>Complex type definitions</li>
   *   <li>Attribute declarations</li>
   *   <li>Element declarations</li>
   * </ul>
   *
   * The secondary components, which must have names, are as follows:
   * <ul>
   *   <li>Attribute group definitions</li>
   *   <li>Identity-constraint definitions</li>
   *   <li>Model group definitions</li>
   *   <li>Notation declarations</li>
   * </ul>
   *
   * Finally, the "helper" components provide small parts of other components; 
   * they are not independent of their context:
   * <ul>
   *   <li>Annotations</li>
   *   <li>Model groups</li>
   *   <li>Particles</li>
   *   <li>Wildcards</li>
   *   <li>Attribute Uses</li>
   * </ul>
   *
   * @see      http://www.w3.org/TR/xmlschema-1
   * @see      http://www.w3.org/TR/xmlschema-1/#concepts-data-model
   * @purpose  XMLSchema
   */
  class XmlSchema extends Object {
    var
      $complexTypes = array();

    /**
     * Add a complex type
     *
     * @access  public
     * @param   &xml.schema.XmlSchemaStructure struct
     * @return  &xml.schema.XmlSchemaStructure
     */      
    function &addComplexType(&$struct) {
      if (!is_a($struct, 'XmlSchemaStructure')) {
        trigger_error('Type: '.get_class($message), E_USER_NOTICE);
        return throw(new IllegalArgumentException('struct is not a xml.schema.XmlSchemaStructure'));
      }
      
      $this->complexTypes[]= &$struct;
      return $struct;
    }
    
    /**
     * Get the list of complex types
     *
     * @access  public
     * @return  &xml.schema.XmlSchemaStructure[]
     */
    function &getComplexTypes() {
      return $this->complexTypes;
    }
  
  }
?>
