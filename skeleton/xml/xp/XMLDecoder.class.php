<?php
  require('lang.base.php'); 
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
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
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
    
    function close() {
      $this->file->close();
    }
  }

  $d= &new XMLDecoder(new File('php://stdin'));
  $o= $d->readObject();
  $d->close();
  
  var_dump($o, $o->getClassName(), $o->toString());
?>
