<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xml.Tree', 'text.parser.DateParser');

  /**
   * Class representing wddx messages. It can handle serialization and
   * deserialization to/from wddx.
   *
   * @ext      xml
   * @see      http://www.openwddx.org/downloads/dtd/wddx_dtd_10.txt
   * @purpose  Serialize wddx packets.
   */
  class WddxMessage extends Tree {

    /**
     * Constructor.
     *
     */
    public function __construct() {
      parent::__construct('wddxPacket');
      $this->root->setAttribute('version', '1.0');
    }
    
    /**
     * Create a WddxMessage object from an XML document.
     *
     * @param   string string
     * @return  webservices.wddx.WddxMessage
     */
    public static function fromString($string) {
      return parent::fromString($string, 'WddxMessage');
    }    
    
    /**
     * Sets the comment in a Wddx packet
     *
     * @param   string comment
     */
    public function create($comment= NULL) {
      $h= $this->root->addChild(new Node('header'));
      if ($comment) $h->addChild(new Node('comment', $comment));
    }
    
    /**
     * Set data for the message
     *
     * @param   mixed[] arr
     */
    public function setData($arr) {
      $d= $this->root->addChild(new Node('data'));
      if (sizeof($arr)) foreach (array_keys($arr) as $idx) {
        $this->_marshall($d, $arr[$idx]);
      }
    }
    
    /**
     * Marshall method to serialize data into the Wddx message.
     *
     * @param   xml.Node node
     * @param   mixed data
     * @throws  lang.IllegalArgumentException if passed data could not be serialized
     */
    protected function _marshall($node, $data) {
      switch (xp::typeOf($data)) {
        case 'NULL':
          $node->addChild(new Node('null'));
          break;
        
        case 'boolean':
          $node->addChild(new Node('boolean', NULL, array(
            'value' => $data ? 'true' : 'false'
          )));
          break;
        
        case 'string':
          $node->addChild(new Node('string', $data));
          break;
        
        case 'double':
        case 'integer':
          $node->addChild(new Node('number', $data));
          break;
        
        case 'array':
          $s= $node->addChild(new Node('struct'));
          foreach (array_keys($data) as $idx) {
            $this->_marshall($s->addChild(new Node('var', NULL, array(
              'name'  => $idx
            ))), $data[$idx]);
          }
          break;
        
        case 'util.Date':
          
          // FIXME
          $node->addChild(new Node('dateTime', $data->toString('r')));
          break;
        
        case 'lang.Collection':
          $a= $node->addChild(new Node('array', NULL, array(
            'length'  => sizeof($data)
          )));
          foreach (array_keys($data) as $idx) {
            $this->_marshall($a, $data[$idx]);
          }
          break;
        
        default:
          throw(new IllegalArgumentException('Found datatype which cannot be serialized: '.xp::typeOf($data)));
      }
    }
    
    /**
     * Retrieve data from wddx message.
     *
     * @return  mixed[]
     * @throws  lang.IllegalStateException if no payload data could be found in the message
     */
    public function getData() {
      $ret= array();
      foreach (array_keys($this->root->children) as $idx) {
        if ('header' == $this->root->children[$idx]->getName())
          continue;
        
        // Process params node
        foreach (array_keys($this->root->children[$idx]->children) as $params) {
          try {
            $ret[]= $this->_unmarshall($this->root->children[$idx]->children[$params]);
          } catch (Exception $e) {
            throw($e);
          }
        }
        
        return $ret;
      }
      
      throw(new IllegalStateException('No payload found.'));
    }
    
    /**
     * Umarshall method for deserialize data from wddx message
     *
     * @param   xml.Node node
     * @return  mixed[]
     * @throws  lang.IllegalArgumentException if document is not well-formed
     */
    protected function _unmarshall($node) {
      switch ($node->getName()) {
        case 'null': return NULL;
        case 'boolean': return ($node->getContent() == 'true' ? TRUE : FALSE);
        case 'string': return $node->getContent();
        case 'dateTime': 
          $parser= new DateParser();
          return $parser->parse($node->getContent());
        
        case 'number':
          if ($node->getContent() == intval($node->getContent())) return intval($node->getContent());
          return (double)$node->getContent();
        
        case 'char':
          return chr($node->getAttribute('code'));
        
        case 'binary':
          // TBI
          return;
        
        case 'array':
          $arr= array();
          foreach (array_keys($node->children) as $idx) {
            $arr[]= $this->_unmarshall($node->children[$idx]);
          }
          return $arr;
        
        case 'struct':
          $struct= array();
          foreach (array_keys($node->children) as $idx) {
            $struct[$node->children[$idx]->getAttribute('name')]= $this->_unmarshall($node->children[$idx]);
          }
          return $struct;
      }
      
      throw(new IllegalArgumentException('Cannot unserialize not well-formed WDDX document'));
    }
  }
?>
