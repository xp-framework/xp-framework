<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xml.Tree', 'io.File');

  /**
   * XML Decoder
   *
   * <code>
   *   $d= &new XMLDecoder(new File('object.xml'));
   *   $o= $d->readObject();
   *   $d->close();
   * </code>
   */
  class XMLDecoder extends Object {
  
    /**
     * Constructor
     *
     * @access  public
     * @param   &io.File file
     */
    function __construct(&$file) {
     $this->file= &$file;
      $this->file->open(FILE_MODE_READ);
      parent::__construct();
    }
    
    /**
     * Private helper
     *
     * @access  public
     * @param   &xml.Node node
     * @return  array result
     */
    function _recurse(&$node) {
      $result= array();
      for ($i= 0, $s= sizeof($node->children); $i < $s; $i++) {
        $type= &$node->children[$i]->attribute['type'];
        $name= &$node->children[$i]->attribute['name']; 
        
        switch ($type) {
          case 'array':
            $result[$name]= $this->_recurse($node->children[$i]);
            break;
            
          case 'object':
            try(); {
              $class= ClassLoader::loadClass($node->children[$i]->attribute['class']);
            } if (catch('Exception', $e)) {
              $class= 'stdClass';
            }
            $result[$name]= &cast(
              $this->_recurse($node->children[$i]),
              $class
            );
            break;
            
          default:
            $result[$name]= cast($node->children[$i]->content, $type);
            break;
        }
      }
      
      return $result;
    }
    
    /**
     * Read object
     *
     * @access  public
     * @return  &Object object
     * @throws  Exception in case read/format fails
     */
    function &readObject() {
      $tree= &new Tree();
      
      try(); {
        $buf= '';
        do { 
          $buf.= $this->file->read();
        } while (!$this->file->eof());
        $tree->fromString($buf);
        $name= ClassLoader::loadClass($tree->root->attribute['class']);
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      
      return cast($this->_recurse($tree->root), $name);
    }
    
    /**
     * Close
     *
     * @access  public
     * @return  bool success
     */
    function close() {
      return $this->file->close();
    }
  }
?>
