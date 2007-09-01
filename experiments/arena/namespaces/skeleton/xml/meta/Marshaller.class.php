<?php
/* This class is part of the XP framework's experiments
 *
 * $Id: Marshaller.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace xml::meta;

  uses(
    'xml.Tree',
    'xml.QName',
    'xml.XMLFormatException'
  );

  /**
   * Marshalls XML from objects by using annotations.
   *
   * Example:
   * <code>
   *   // [...create transmission object...]
   *
   *   try {
   *     $xml= Marshaller::marshal($transmission);
   *   } catch (XPException $e) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   *
   *   echo $xml;
   * </code>
   *
   * @test     xp://net.xp_framework.unittest.xml.MarshallerTest
   * @ext      dom
   * @see      http://castor.org/xml-mapping.html
   * @purpose  XML databinding
   */
  class Marshaller extends lang::Object {
  
    /**
     * Iterate over class methods with @xmlfactory annotation
     *
     * @param   lang.Object instance
     * @param   lang.XPClass class
     * @param   xml.Node node
     */
    protected static function recurse($instance, $class, $node) {
    
      // Namespace handling
      if ($class->hasAnnotation('xmlns')) {
        foreach ($class->getAnnotation('xmlns') as $prefix => $url) {
          $node->setAttribute('xmlns:'.$prefix, $url);
        }
      }

      foreach ($class->getMethods() as $method) {
        if (!$method->hasAnnotation('xmlfactory', 'element')) continue;
        
        $element= $method->getAnnotation('xmlfactory', 'element');
        
        // Attributes
        if ('@' == $element{0}) {
          $node->setAttribute(substr($element, 1), $method->invoke($instance));
          continue;
        }
        
        // Node content
        if ('.' == $element) {
          $node->setContent($method->invoke($instance));
          continue;
        }
        
        // Create subnodes based on runtime type of method:
        //
        // - For scalar types, create a node with the element's name and set
        //   the node's content to the value
        //
        // - For arrays we iterate over keys and values (FIXME: we assume the 
        //   array is a string => scalar map!)
        //
        // - For collections, add a node with the element's name and invoke
        //   the recurse() method for each value in the collection.
        //
        // - For objects, add a new node and invoke the recurse() method
        //   on it.
        $result= $method->invoke($instance);
        if (is_scalar($result) || NULL === $result) {
          $node->addChild(new xml::Node($element, $result));
        } else if (is_array($result)) {
          $child= $node->addChild(new xml::Node($element));
          foreach ($result as $key => $val) {
            $child->addChild(new xml::Node($key, $val));
          }
        } else if (is('lang.Collection', $result)) {
          $elementClass= $result->getElementClass();
          foreach ($result->values() as $value) {
            self::recurse($value, $elementClass, $node->addChild(new xml::Node($element)));
          }
        } else if (is('lang.types.ArrayList', $result)) {
          foreach ($result->values as $value) {
            $node->addChild(new xml::Node($element, $value));
          }
        } else if (is('lang.Generic', $result)) {
          self::recurse($result, $result->getClass(), $node->addChild(new xml::Node($element)));
        }
      }
    }

    /**
     * Marshal an object to xml
     *
     * @param   lang.Object instance
     * @param   xml.QName qname default NULL
     * @return  string xml
     */
    public static function marshal($instance, $qname= ) {
      $class= $instance->getClass();

      // Create XML tree and root node. Use the information provided by the
      // qname argument if existant, use the class` non-qualified (and 
      // lowercased) name otherwise.
      $tree= new xml::Tree();
      if ($qname) {
        $prefix= $qname->prefix ? $qname->prefix : $qname->localpart{0};
        $tree->root->setName($prefix.':'.$qname->localpart);
        $tree->root->setAttribute('xmlns:'.$prefix, $qname->namespace);
      } else if ($class->hasAnnotation('xmlns')) {
        $tree->root->setName(key($class->getAnnotation('xmlns')).':'.get_class($instance));
      } else {
        $tree->root->setName(strtolower(get_class($instance)));
      }
      
      self::recurse($instance, $class, $tree->root);
      return $tree->getSource(INDENT_DEFAULT);
    }
  }
?>
