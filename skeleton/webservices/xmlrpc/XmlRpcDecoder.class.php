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
     * @return  mixed
     */
    public function decode(Node $node) {
      return $this->_unmarshall($node);
    }
      
    /**
     * Recursively deserialize data for the given node.
     *
     * @param   xml.Node node
     * @return  mixed
     * @throws  lang.IllegalArgumentException if the data cannot be deserialized
     * @throws  lang.ClassNotFoundException in case a XP object's class could not be loaded
     * @throws  xml.XMLFormatException
     */
    protected function _unmarshall(Node $node) {
      if (!isset($node->children[0])) {
        throw new XMLFormatException('Tried to access nonexistant node.');
      }

      $c= $node->children[0];
      switch ($c->getName()) {
        case 'struct':
          $ret= array();
          foreach ($c->children as $child) {
            $data= array();
            $data[$child->children[0]->getName()]= $child->children[0];
            $data[$child->children[1]->getName()]= $child->children[1];
            $ret[$data['name']->getContent()]= $this->_unmarshall($data['value']);
            unset($data);
          }
          
          if (!isset($ret['__xp_class'])) return $ret;
          
          // Check whether this is a XP object. If so, load the class and
          // create an instance without invoking the constructor.
          $fields= XPClass::forName($ret['__xp_class'])->getFields();
          $cname= substr(array_search($ret['__xp_class'], xp::$registry, TRUE), 6);
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
              $name= "\0".substr(array_search($field->getDeclaringClass()->getName(), xp::$registry, TRUE), 6)."\0".$field->getName();
            }
            $s.= 's:'.strlen($name).':"'.$name.'";'.serialize($ret[$field->getName()]);
            $n++;
          }
          return unserialize('O:'.strlen($cname).':"'.$cname.'":'.$n.':{'.$s.'}');
          
        case 'array':
          $ret= array();
          foreach ($c->children[0]->children as $child) {
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
