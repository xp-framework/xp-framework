<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xml.Node', 'util.Date', 'lang.types.Bytes');

  /**
   * Encoder for data structures into XML-RPC format
   *
   * @test     xp://net.xp_framework.unittest.scriptlet.rpc.XmlRpcEncoderTest
   * @see      http://xmlrpc.com
   * @purpose  XML-RPC-Encoder
   */
  class XmlRpcEncoder extends Object {
  
    /**
     * Encode given data into XML-RPC format
     *
     * @param   mixed data
     * @return  xml.Node
     */
    public function encode($data) {
      return $this->_marshall($data);
    }

    /**
     * Recursivly serialize data to the given node.
     *
     * Scalar values are natively supported by the protocol, so we just encode
     * them as the spec tells us. As arrays and structs / hashes are the same
     * in PHP, and structs are the more powerful construct, we're always encoding 
     * arrays as structs.
     * 
     * XP objects are encoded as structs, having their FQDN stored in the member
     * __xp_class.
     *
     * @param   xml.Node node
     * @param   mixed data
     * @throws  lang.IllegalArgumentException in case the data could not be serialized.
     */
    protected function _marshall($data) {
      $value= new Node('value');
      
      // Handle objects:
      // - util.Date objects are serialized as dateTime.iso8601
      // - lang.types.Bytes object are serialized as base64
      // - Provide a standard-way to serialize Object-derived classes
      if ($data instanceof Date) {
        $value->addChild(new Node('dateTime.iso8601', $data->toString('Ymd\TH:i:s')));
        return $value;
      } else if ($data instanceof Bytes) {
        $value->addChild(new Node('base64', base64_encode($data)));
        return $value;
      } else if ($data instanceof Generic) {

        $n= $value->addChild(new Node('struct'));
        $n->addChild(Node::fromArray(array(
          'name'  => '__xp_class',
          'value' => array('string' => $data->getClassName())
        ), 'member'));
        
        $values= (array)$data;
        foreach ($data->getClass()->getFields() as $field) {
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
          $member= $n->addChild(new Node('member'));
          $member->addChild(new Node('name', $field->getName()));
          $member->addChild($this->_marshall($values[$name]));
        }
        return $value;
      }
      
      switch (xp::typeOf($data)) {
        case 'integer':
          $value->addChild(new Node('int', $data));
          break;
          
        case 'boolean':
          $value->addChild(new Node('boolean', (string)(int)$data));
          break;
          
        case 'double':
        case 'float':
          $value->addChild(new Node('double', $data));
          break;
        
        case 'array':
          if ($this->_isVector($data)) {
            $n= $value->addChild(new Node('array'))->addChild(new Node('data'));
            for ($i= 0, $s= sizeof($data); $i < $s; $i++) {
              $n->addChild($this->_marshall($data[$i]));
            }
          } else {
            $n= $value->addChild(new Node('struct'));
            foreach ($data as $name => $v) {
              $member= $n->addChild(new Node('member'));
              $member->addChild(new Node('name', $name));
              $member->addChild($this->_marshall($v));
            }
          }
          break;
        
        case 'string':
          $value->addChild(new Node('string', $data));
          break;
          
        case 'NULL':
          $value->addChild(new Node('nil'));
          break;
        
        default:
          throw new IllegalArgumentException('Cannot serialize data of type "'.xp::typeOf($data).'"');
      }
      
      return $value;
    }

    /**
     * Checks whether an array is a numerically indexed array
     * (a vector) or a key/value hashmap.
     *
     * @param   array data
     * @return  bool
     */
    protected function _isVector($data) {
      $start= 0;
      foreach (array_keys($data) as $key) {
        if ($key !== $start++) return FALSE;
      }
      
      return TRUE;
    }
  }
?>
