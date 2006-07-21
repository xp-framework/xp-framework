<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xml.Tree', 'io.File', 'io.FileUtil');

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
    public function __construct(&$file) {
      $this->file= &$file;
    }
    
    /**
     * Private helper
     *
     * @access  public
     * @param   &xml.Node node
     * @return  array result
     */
    public function _recurse(&$node, $trim) {
      $result= array();
      for ($i= 0, $s= sizeof($node->children); $i < $s; $i++) {
        $type= &$node->children[$i]->attribute['type'];
        $name= &$node->children[$i]->attribute['name']; 
        
        switch ($type) {
          case 'array':
            $result[$name]= $this->_recurse($node->children[$i], $trim);
            break;
            
          case 'object':
            try {
              $class= &XPClass::forName($node->children[$i]->attribute['class']);
            } catch (ClassNotFoundException $e) {
              throw($e);
            }
            $result[$name]= &$class->newInstance();
            foreach ($this->_recurse($node->children[$i], $trim) as $k => $v) {
              $result[$name]->$k= $v;
            }
            break;
            
          default:
            $c= ($trim
              ? trim($node->children[$i]->content)
              : $node->children[$i]->content
            );
              
            $result[$name]= cast($c, $type);
            break;
        }
      }
      
      return $result;
    }
    
    /**
     * Read object
     *
     * @access  public
     * @param   bool trim default FALSE whether to trim whitespace
     * @return  &lang.Object object
     * @throws  lang.Exception in case read/format fails
     */
    public function &readObject($trim= FALSE) {
      try {
        do {
          if (!($buf= FileUtil::getContents($this->file))) break;
          if (!($tree= &Tree::fromString($buf))) break;
          if (!($class= &XPClass::forName($tree->root->attribute['class']))) break;
          $result= &$class->newInstance();
        } while (0);
      } catch (Exception $e) {
        throw($e);
      }
      
      foreach ($this->_recurse($tree->root, $trim) as $k => $v) {
        $result->$k= $v;
      }      
      return $result;
    }
    
    /**
     * Close
     *
     * @access  public
     * @return  bool success
     * @deprecated
     */
    public function close() {
      return TRUE;
    }
  }
?>
