<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$ 
 */

  uses('xml.XPath', 'xml.XMLFormatException');

  /**
   * Creates objects from XML by using annotations.
   *
   * Example:
   * <code>
   *   // [...load $xml from a file or a stream...]
   *
   *   try(); {
   *     $t= &Unmarshaller::unmarshal($xml, 'com.1and1.qf.xml.types.TransmissionType');
   *   } if (catch('Exception', $e)) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   *
   *   echo $t->toString();
   * </code>
   *
   * @test     xp://net.xp_framework.unittest.xml.UnmarshallerTest
   * @ext      dom
   * @see      http://castor.org/xml-mapping.html
   * @purpose  XML databinding
   */
  class Unmarshaller extends Object {

    /**
     * Retrieve content of a DomElement
     *
     * @model   static
     * @access  protected
     * @param   &php.DomElement element
     * @return  string
     */
    function contentOf(&$element) {
      switch ($element->type) {
        case 1:   // Nodeset
          return empty($element->nodeset) ? NULL : utf8_decode($element->nodeset[0]->get_content());
          break;

        case 3:   // Text
          return utf8_decode($element->content);

        case 2:   // Attribute
        case 4:   // String
          return utf8_decode($element->value);
          break;

        default:
          return NULL;
      }
    }

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
     * @throws  xml.XPathException
     */
    function &recurse(&$xpath, &$context, $classname) {
      try(); {
        $class= &XPClass::forName($classname);
      } if (catch('ClassNotFoundException', $e)) {
        return throw($e);
      }

      $instance= &$class->newInstance();
      foreach ($class->getMethods() as $method) {
        if (!$method->hasAnnotation('xmlmapping', 'element')) continue;

        // Perform XPath query
        try(); {
          $result= $xpath->query($method->getAnnotation('xmlmapping', 'element'), $context);
        } if (catch('XPathException', $e)) {
          return throw($e);
        }

        // Iterate over results, invoking the method for each node.
        foreach ($result->nodeset as $node) {
          if ($method->hasAnnotation('xmlmapping', 'class')) {

            // * If the xmlmapping annotation has a key "class", call recurse()
            //   with the given XPath, the node as context and the key's value
            //   as classname
            try(); {
              $arguments= array(Unmarshaller::recurse(
                $xpath, 
                $node, 
                $method->getAnnotation('xmlmapping', 'class')
              ));
            } if (catch('Exception', $e)) {
              return throw($e);
            }
          } else if ($method->hasAnnotation('xmlmapping', 'factory')) {

            // * If the xmlmapping annotation has a key "factory", call recurse()
            //   with the given XPath, the node as context and the results from
            //   the specified method as class name. The specified factory method 
            //   is passed the node's tag name if no "pass" key is available.
            //   In case it is, call the factory method with the arguments 
            //   constructed from the "pass" key.
            if ($method->hasAnnotation('xmlmapping', 'pass')) {
              $factoryArgs= array();
              foreach ($method->getAnnotation('xmlmapping', 'pass') as $pass) {
                $factoryArgs[]= Unmarshaller::contentOf($xpath->query($pass, $node));
              }
            } else {
              $factoryArgs= array($node->tagname);
            }

            try(); {
              $arguments= array(Unmarshaller::recurse(
                $xpath, 
                $node, 
                call_user_func_array(
                  array(&$instance, $method->getAnnotation('xmlmapping', 'factory')), 
                  $factoryArgs
                )
              ));
            } if (catch('Exception', $e)) {
              return throw($e);
            }
          } else if ($method->hasAnnotation('xmlmapping', 'pass')) {
          
            // * If the xmlmapping annotation has a key "pass" (expected to be an
            //   array of XPaths relative to the node), construct the method's
            //   argument list from the XPaths' results.
            $arguments= array();
            foreach ($method->getAnnotation('xmlmapping', 'pass') as $pass) {
              $arguments[]= Unmarshaller::contentOf($xpath->query($pass, $node));
            }
          } else if ($method->hasAnnotation('xmlmapping', 'type')) {

            // * If the xmlmapping annotation contains a key "type", cast the node's
            //   contents to the specified type before passing it to the method.
            $arguments= array(cast(
              utf8_decode($node->get_content()),
              $method->getAnnotation('xmlmapping', 'type')
            ));
          } else {

            // * Otherwise, pass the node's content to the method
            $arguments= array(utf8_decode($node->get_content()));
          }
          
          try(); {
            $method->invoke($instance, $arguments);
          } if (catch('Exception', $e)) {
            return throw($e);
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
     * @throws  xml.XMLFormatException
     */
    function &unmarshal($xml, $classname) {
      if (!($dom= domxml_open_mem($xml, DOMXML_LOAD_PARSING, $error))) {
        return throw(new XMLFormatException(xp::stringOf($error)));
      }
      
      $u= &Unmarshaller::recurse(new XPath($dom), $dom->document_element, $classname);
      return $u;
    }
  }
?>
