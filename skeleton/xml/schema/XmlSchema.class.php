<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
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
   * The secondary components, which must have names, are as follows:
   * <ul>
   *   <li>Attribute group definitions</li>
   *   <li>Identity-constraint definitions</li>
   *   <li>Model group definitions</li>
   *   <li>Notation declarations</li>
   * </ul>
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
   * @see http://www.w3.org/TR/xmlschema-1
   * @see http://www.w3.org/TR/xmlschema-1/#concepts-data-model
   */
  class XmlSchema extends Object {
    var 
      $elements=   array(),
      $attributes= array();
      
  }
?>
