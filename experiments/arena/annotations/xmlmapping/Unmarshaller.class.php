<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$ 
 */

  uses('xml.XPath');

  /**
   * Creates objects from XML by using annotations.
   *
   * @ext      dom
   * @see      http://castor.org/xml-mapping.html
   * @purpose  XML databinding
   */
  class Unmarshaller extends Object {

    /**
     * Recursively unmarshal
     *
     * @model   static
     * @access  protected
     * @param   &xml.XPath xpath
     * @param   &php.DomElement context
     * @param   string classname
     * @return  &lang.Object
     * @throws  lang.ClassNotFoundException
     */
    function &recurse(&$xpath, &$context, $classname) {
      try(); {
        $class= &XPClass::forName($classname);
      } if (catch('ClassNotFoundException', $e)) {
        return throw($e);
      }

      $instance= &$class->newInstance();
      foreach ($class->getMethods() as $method) {
        if (!$method->hasAnnotation('xmlmapping', 'xpath')) continue;

        // Perform XPath query
        $result= &$xpath->query($method->getAnnotation('xmlmapping', 'xpath'), $context);

        // Iterate over results, invoking the method for each node.
        //
        // * If the xmlmapping annotation has a key "class", call recurse()
        //   with the given XPath, the node as context and the key's value
        //   as classname
        //
        // * Otherwise, pass the node's content to the method
        foreach ($result->nodeset as $node) {
          if ($method->hasAnnotation('xmlmapping', 'class')) {
            $method->invoke($instance, array(Unmarshaller::recurse(
              $xpath, 
              $node, 
              $method->getAnnotation('xmlmapping', 'class')
            )));
          } else if ($method->hasAnnotation('xmlmapping', 'type')) {
            $method->invoke($instance, array(cast(
              $node->get_content(),
              $method->getAnnotation('xmlmapping', 'type')
            )));
          } else {
            $method->invoke($instance, array($node->get_content()));
          }
        }
      }

      return $instance;
    }

    /**
     * Unmarshal XML to an object
     *
     * @model   static
     * @access  public
     * @param   string xml
     * @param   string classname
     * @return  &lang.Object
     * @throws  lang.ClassNotFoundException
     */
    function &unmarshal($xml, $classname) {
      with ($dom= &domxml_open_mem($xml)); {
        return Unmarshaller::recurse(new XPath($dom), $dom->document_element, $classname);
      }
    }
  }
?>
