<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  require('lang.base.php'); 
  uses('xml.Tree', 'io.File');
 
  /**
   * XML encoder
   *
   * <code>
   *   $d= &new XMLEncoder(new File('object.xml'));
   *   $d->writeObject($o);
   *   $d->close();
   * </code>
   *
   * <pre>
   *   <?xml version="1.0"?>
   *   <xp:object class="util.Binford" xmlns:xp="http://xp.php3.de/ns/">
   *     <xp:property name="poweredBy" type="integer">6100</xp:property>
   *   </xp:object>
   * </pre>
   *
   * @see      http://java.sun.com/j2se/1.4.1/docs/api/java/beans/XMLEncoder.html
   * @purpose  Encode objects to XML
   */
  class XMLEncoder extends Object {
  
    /**
     * Constructor
     *
     * @access  public
     * @param   &io.File file
     */
    function __construct(&$file) {
      $this->file= &$file;
      $this->file->open(FILE_MODE_WRITE);
      parent::__construct();
    }
    
    /**
     * Private helper method
     *
     * @access  private
     * @param   &xml.Node node
     * @param   &mixed value
     */
    function _recurse(&$node, &$value, $name= 'xp:property') {
      foreach (array_keys($value) as $key) {
        $n= &$node->addChild(new Node(array(
          'name'      => $name,
          'attribute' => array(
            'name'    => $key,
            'type'    => strtolower(gettype($value[$key]))
          )
        )));
        
        switch (gettype($value[$key])) {
          case 'object':
            if (is_a($value[$key], 'Object')) {
              $n->attribute['class']= $value[$key]->getClassName();
            }
            $this->_recurse($n, $o= get_object_vars($value[$key]), 'xp:property');
            break;
            
          case 'array':
            $this->_recurse($n, $value[$key], 'xp:array');
            break;
            
          default: 
            $n->setContent($value[$key]);
        }
      }
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function writeObject(&$o) {
    
      // Create header
      $tree= &new Tree();
      $tree->root->name= 'xp:object';
      $tree->root->attribute['xmlns:xp']= 'http://xp.php3.de/ns/';
      $tree->root->attribute['class']= $o->getClassName();
      
      // Properties
      $this->_recurse($tree->root, $o= get_object_vars($o));
      
      $this->file->writeLine($tree->getDeclaration());
      $this->file->writeLine($tree->getSource(0));
      
    }
    
    function close() {
      $this->file->close();
    }
  }
  
  uses('io.File', 'util.Binford');
  $b= &new File('/home/thekid/timm.jpg');
  $b->test= array('a', 'b', 'c' => array('c', 'd'));
  $b->dummy= &new Binford();
  $d= &new XMLEncoder(new File('php://stdout'));
  $d->writeObject($b);
  $d->close();
  
?>
