<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'xml.Tree',
    'io.File',
    'io.FileUtil'
  );

  /**
   * XML Decoder
   *
   * <code>
   *   $d= new XMLDecoder(new File('object.xml'));
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
     $this->file= $file;
      $this->file->open(FILE_MODE_READ);
      
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
        $type= $node->children[$i]->attribute['type'];
        $name= $node->children[$i]->attribute['name']; 
        
        switch ($type) {
          case 'array':
            $result[$name]= self::_recurse($node->children[$i], $trim);
            break;
            
          case 'object':
            try {
              $class= ClassLoader::loadClass($node->children[$i]->attribute['class']);
            } catch (XPException $e) {
              $class= 'stdClass';
            }
            $result[$name]= cast(
              self::_recurse($node->children[$i], $trim),
              $class
            );
            break;
            
          default:
            $c= ($trim
              ? trim(chop($node->children[$i]->content))
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
     * @throws  Exception in case read/format fails
     */
    public function readObject($trim= FALSE) {
      try {
        do {
          if (!($buf= FileUtil::getContents($this->file))) break;
          if (!($tree= Tree::fromString($buf))) break;
          $name= ClassLoader::loadClass($tree->root->attribute['class']);
        } while (0);
      } catch (XPException $e) {
        throw ($e);
      }
      
      return cast(self::_recurse($tree->root, $trim), $name);
    }
    
    /**
     * Close
     *
     * @access  public
     * @return  bool success
     */
    public function close() {
      return $this->file->close();
    }
  }
?>
