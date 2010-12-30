<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$ 
 */

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
  class Marshaller extends Object {
  
    /**
     * Iterate over class methods with @xmlfactory annotation
     *
     * @param   lang.Object instance
     * @param   lang.XPClass class
     * @param   xml.Node node
     * @param   [:var] inject
     */
    protected static function recurse($instance, $class, $node, $inject) {
    
      // Namespace handling
      if ($class->hasAnnotation('xmlns')) {
        foreach ($class->getAnnotation('xmlns') as $prefix => $url) {
          $node->setAttribute('xmlns:'.$prefix, $url);
        }
      }

      foreach ($class->getMethods() as $method) {
        if (!$method->hasAnnotation('xmlfactory', 'element')) continue;
        
        $element= $method->getAnnotation('xmlfactory', 'element');
        
        // Pass injection parameters at end of list
        $arguments= array();
        if ($method->hasAnnotation('xmlfactory', 'inject')) {
          foreach ($method->getAnnotation('xmlfactory', 'inject') as $name) {
            if (!isset($inject[$name])) throw new IllegalArgumentException(
              'Injection parameter "'.$name.'" not found for '.$method->toString()
            );
            $arguments[]= $inject[$name];
          }
        }
        
        // Attributes
        if ('@' == $element{0}) {
          $node->setAttribute(substr($element, 1), $method->invoke($instance, $arguments));
          continue;
        }
        
        // Node content
        if ('.' == $element) {
          $node->setContent($method->invoke($instance, $arguments));
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
        // - For lists, add a node with the element's name and invoke
        //   the recurse() method for each value in the collection.
        //
        // - For objects, add a new node and invoke the recurse() method
        //   on it.
        $result= $method->invoke($instance, $arguments);
        if (is_scalar($result) || NULL === $result) {
          $node->addChild(new Node($element, $result));
        } else if (is_array($result)) {
          $child= $node->addChild(new Node($element));
          foreach ($result as $key => $val) {
            $child->addChild(new Node($key, $val));
          }
        } else if (is('lang.Collection', $result)) {
          $elementClass= $result->getElementClass();
          foreach ($result->values() as $value) {
            self::recurse($value, $elementClass, $node->addChild(new Node($element)), $inject);
          }
        } else if ($result instanceof Traversable) {
          foreach ($result as $value) {
            if ($value instanceof Generic) {
              self::recurse($value, $value->getClass(), $node->addChild(new Node($element)), $inject);
            } else {
              $node->addChild(new Node($element, $value));
            }
          }
        } else if ($result instanceof Generic) {
          self::recurse($result, $result->getClass(), $node->addChild(new Node($element)), $inject);
        }
      }
    }

    /**
     * Marshal an object to xml
     *
     * @param   lang.Object instance
     * @param   xml.QName qname default NULL
     * @return  string xml
     * @deprecated  Use marshalTo() instead
     */
    public static function marshal($instance, $qname= NULL) {
      $class= $instance->getClass();

      // Create XML tree and root node. Use the information provided by the
      // qname argument if existant, use the class` non-qualified (and 
      // lowercased) name otherwise.
      $tree= new Tree();
      if ($qname) {
        $prefix= $qname->prefix ? $qname->prefix : $qname->localpart{0};
        $tree->root->setName($prefix.':'.$qname->localpart);
        $tree->root->setAttribute('xmlns:'.$prefix, $qname->namespace);
      } else if ($class->hasAnnotation('xmlns')) {
        $tree->root->setName(key($class->getAnnotation('xmlns')).':'.$class->getSimpleName());
      } else {
        $tree->root->setName(strtolower($class->getSimpleName()));
      }
      
      self::recurse($instance, $class, $tree->root, array());
      return $tree->getSource(INDENT_DEFAULT);
    }
 
    /**
     * Marshal an object to xml
     *
     * @param   xml.Node target
     * @param   lang.Object instance
     * @param   [:var] inject
     * @return  xml.Node the given target
     */
    public function marshalTo(Node $target, Generic $instance, $inject= array()) {
      $class= $instance->getClass();

      // Add XML namespace from class' "xmlns" annotation if present
      if ($class->hasAnnotation('xmlns')) {
        $target->setName(key($class->getAnnotation('xmlns')).':'.$target->getName());
      }
      
      self::recurse($instance, $class, $target, $inject);
      return $target;
    }
  }
?>
