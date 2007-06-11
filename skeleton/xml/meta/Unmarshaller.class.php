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
   *   try {
   *     $t= Unmarshaller::unmarshal($xml, 'com.1and1.qf.xml.types.TransmissionType');
   *   } catch (XPException $e) {
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
     * @param   php.DomElement element
     * @return  string
     */
    protected static function contentOf($element) {
      if (xp::typeOf($element) == 'php.DOMNodeList') {
          return $element->length ? utf8_decode($element->item(0)->textContent) : NULL;
      
      } else switch ($element->nodeType) {
        case 1:   // DOMElement
          return utf8_decode($element->textContent);

        case 2:   // DOMAttr
          return utf8_decode($element->value);
        
        case 3:   // DOMText
        case 4:   // DOMCharacterData
          return utf8_decode($element->data);

        default:
          return is_scalar($element) ? $element : NULL;
      }
    }

    /**
     * Recursively unmarshal
     *
     * @param   xml.XPath xpath
     * @param   php.DomElement context
     * @param   string classname
     * @return  lang.Object
     * @throws  lang.ClassNotFoundException
     * @throws  xml.XPathException
     */
    protected static function recurse($xpath, $context, $classname) {
      $class= XPClass::forName($classname);
      $instance= $class->newInstance();

      // Namespace handling
      if ($class->hasAnnotation('xmlns')) {
        foreach ($class->getAnnotation('xmlns') as $prefix => $url) {
          $xpath->context->registerNamespace($prefix, $url);
        }
      }

      foreach ($class->getMethods() as $method) {
        if (!$method->hasAnnotation('xmlmapping', 'element')) continue;

        // Perform XPath query
        $result= $xpath->query($method->getAnnotation('xmlmapping', 'element'), $context);

        // Iterate over results, invoking the method for each node.
        foreach ($result as $node) {
          if ($method->hasAnnotation('xmlmapping', 'class')) {

            // * If the xmlmapping annotation has a key "class", call recurse()
            //   with the given XPath, the node as context and the key's value
            //   as classname
            $arguments= array(self::recurse(
              $xpath, 
              $node, 
              $method->getAnnotation('xmlmapping', 'class')
            ));
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
                $factoryArgs[]= self::contentOf($xpath->query($pass, $node));
              }
            } else {
              $factoryArgs= array($node->nodeName);
            }
            $arguments= array(self::recurse(
              $xpath, 
              $node, 
              call_user_func_array(
                array($instance, $method->getAnnotation('xmlmapping', 'factory')), 
                $factoryArgs
              )
            ));
          } else if ($method->hasAnnotation('xmlmapping', 'pass')) {
          
            // * If the xmlmapping annotation has a key "pass" (expected to be an
            //   array of XPaths relative to the node), construct the method's
            //   argument list from the XPaths' results.
            $arguments= array();
            foreach ($method->getAnnotation('xmlmapping', 'pass') as $pass) {
              $arguments[]= self::contentOf($xpath->query($pass, $node));
            }
          } else if ($method->hasAnnotation('xmlmapping', 'type')) {

            // * If the xmlmapping annotation contains a key "type", cast the node's
            //   contents to the specified type before passing it to the method.
            $arguments= array(cast(
              utf8_decode($node->textContent),
              $method->getAnnotation('xmlmapping', 'type')
            ));
          } else {

            // * Otherwise, pass the node's content to the method
            $arguments= array(utf8_decode($node->textContent));
          }
          
          $method->invoke($instance, $arguments);
        }
      }

      return $instance;
    }

    /**
     * Unmarshal XML to an object
     *
     * @param   string xml
     * @param   string classname
     * @return  lang.Object
     * @throws  lang.ClassNotFoundException
     * @throws  xml.XMLFormatException
     */
    public static function unmarshal($xml, $classname) {
      try {
        $doc= new DOMDocument();
        $doc->loadXML($xml);
      } catch (DOMException $e) {
        throw new XMLFormatException($e->getMessage());
      }
      return self::recurse(new XPath($doc), $doc->documentElement, $classname);
    }
  }
?>
