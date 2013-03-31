<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xml.XMLFormatException', 'util.Date', 'lang.types.Bytes');

  /**
   * XML-RPC decoder
   *
   * @test     xp://net.xp_framework.unittest.scriptlet.rpc.XmlRpcDecoderTest
   * @see      http://xmlrpc.com
   * @purpose  Decode XML-RPC data
   */
  class XmlRpcDecoder extends Object {
  
    /**
     * Decode XML node-set into the data structures they represent
     *
     * @param   xml.Node node
     * @return  var
     */
    public function decode(Node $node) {
      return $this->_unmarshall($node);
    }
      
    /**
     * Recursively deserialize data for the given node.
     *
     * @param   xml.Node node
     * @return  var
     * @throws  lang.IllegalArgumentException if the data cannot be deserialized
     * @throws  lang.ClassNotFoundException in case a XP object's class could not be loaded
     * @throws  xml.XMLFormatException
     */
    protected function _unmarshall(Node $node) {

      // Simple form: If no subnode indicating the type exists, the type
      // is string, e.g. <value>Test</value>
      if (!$node->hasChildren()) return (string)$node->getContent();

      // Long form - with subnode, the type is derived from the node's name,
      // e.g. <value><string>Test</string></value>.
      $c= $node->nodeAt(0);
      switch ($c->getName()) {
        case 'struct':
          $ret= array();
          foreach ($c->getChildren() as $child) {
            $data= array();
            $data[$child->nodeAt(0)->getName()]= $child->nodeAt(0);
            $data[$child->nodeAt(1)->getName()]= $child->nodeAt(1);
            $ret[$data['name']->getContent()]= $this->_unmarshall($data['value']);
            unset($data);
          }
          
          if (!isset($ret['__xp_class'])) return $ret;
          
          // Check whether this is a XP object. If so, load the class and
          // create an instance without invoking the constructor.
          $fields= XPClass::forName($ret['__xp_class'])->getFields();
          $cname= array_search($ret['__xp_class'], xp::$cn, TRUE);
          $s= ''; $n= 0;
          foreach ($fields as $field) {
            if (!isset($ret[$field->getName()])) continue;
            $m= $field->getModifiers();
            if ($m & MODIFIER_STATIC) {
              continue;
            } else if ($m & MODIFIER_PUBLIC) {
              $name= $field->getName();
            } else if ($m & MODIFIER_PROTECTED) {
              $name= "\0*\0".$field->getName();
            } else if ($m & MODIFIER_PRIVATE) {
              $name= "\0".array_search($field->getDeclaringClass()->getName(), xp::$cn, TRUE)."\0".$field->getName();
            }
            $s.= 's:'.strlen($name).':"'.$name.'";'.serialize($ret[$field->getName()]);
            $n++;
          }
          return unserialize('O:'.strlen($cname).':"'.$cname.'":'.$n.':{'.$s.'}');
          
        case 'array':
          $ret= array();
          foreach ($c->nodeAt(0)->getChildren() as $child) {
            $ret[]= $this->_unmarshall($child);
          }
          return $ret;
        
        case 'int': case 'i4':
          return (int)$c->getContent();
        
        case 'double':
          return (double)$c->getContent();
        
        case 'boolean':
          return (bool)$c->getContent();
        
        case 'string':
          return (string)$c->getContent();
        
        case 'dateTime.iso8601':
          return Date::fromString($c->getContent());
        
        case 'nil':
          return NULL;

        case 'base64':
          return new Bytes(base64_decode($c->getContent()));
          
        default:
          throw new IllegalArgumentException('Could not decode node as its type is not supported: '.$c->getName());
      }
    }
  }
?>
