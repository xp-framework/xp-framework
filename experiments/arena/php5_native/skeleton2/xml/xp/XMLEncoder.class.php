<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

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
    public function __construct(&$file) {
      $this->file= &$file;
      $this->file->open(FILE_MODE_WRITE);
      
    }
    
    /**
     * Private helper method
     *
     * @access  private
     * @param   &xml.Node node
     * @param   &mixed value
     */
    public function _recurse(&$node, &$value, $name= 'xp:property') {
      foreach (array_keys($value) as $key) {
        $n= &$node->addChild(new Node($name, NULL, array(
          'name'    => $key,
          'type'    => strtolower(gettype($value[$key]))
        )));
        
        switch (gettype($value[$key])) {
          case 'object':
            if ([]is('Generic', $key)) {
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
     * Write object
     *
     * @access  public
     * @param   &lang.Object o
     * @throws  io.IOException in case write/format fails
     */
    public function writeObject(&$o) {
    
      // Create header
      $tree= new Tree('xp:object');
      $tree->root->setAttribute('xmlns:xp', 'http://xp-framework.net/ns/');
      $tree->root->setAttribute('class',    $o->getClassName());
      
      // Properties
      $this->_recurse($tree->root, $o= get_object_vars($o));
      
      $this->file->writeLine($tree->getDeclaration());
      $this->file->writeLine($tree->getSource(0));
      
    }
    
    /**
     * Close
     *
     * @access  public
     * @return  bool success
     */    
    public function close() {
      $this->file->close();
    }
  }
?>
