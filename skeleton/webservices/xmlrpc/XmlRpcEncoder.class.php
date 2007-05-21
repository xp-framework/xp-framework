<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xml.Node');

  /**
   * Encoder for data structures into XML-RPC format
   *
   * @ext      xml
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
      
      if (is('Generic', $data)) {
        if (is('util.Date', $data)) {
          return $value->addChild(new Node('dateTime.iso8601', $data->toString('Ymd\TH:i:s')));
        }
        
        // Provide a standard-way to serialize Object-derived classes
        $cname= xp::typeOf($data);
        $data= (array)$data;
        $data['__xp_class']= $cname;
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
          $struct= $value->addChild(new Node('struct'));
          if (sizeof($data)) foreach (array_keys($data) as $idx) {
            $member= $struct->addChild(new Node('member'));
            $member->addChild(new Node('name', $idx));
            $member->addChild($this->_marshall($data[$idx]));
          }
          $struct;
          break;
        
        case 'string':
          $value->addChild(new Node('string', $data));
          break;
          
        case 'NULL':
          $value->addChild(new Node('nil'));
          break;
        
        default:
          throw(new IllegalArgumentException('Cannot serialize data of type "'.xp::typeOf($data).'"'));
          break;
      }
      
      return $value;
    }
  }
?>
