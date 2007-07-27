<?php
/* This class is part of the XP framework
 *
 * $Id: Tree.class.php 9495 2007-02-26 15:26:48Z friebe $
 */
 
  uses(
    'xml.XML',
    'xml.DomBackedNode'
  );
 
  /**
   * The Tree class represents a tree which can be exported
   * to and imported from an XML document.
   *
   * @see      xp://xml.parser.XMLParser
   * @purpose  Tree
   */
  class DomBackedTree extends XML {
    protected
      $backing= NULL,
      $root   = NULL;

    public
      $_cnt,
      $_cdata,
      $_objs;
    
    /**
     * Constructor
     *
     * @param   string rootName default 'document'
     */
    public function __construct($rootName= 'document') {
      $this->backing= new DOMDocument();
      $this->root= $this->backing->appendChild(new DOMElement($rootName));
    }
    
    /**
     * Retrieve XML representation
     *
     * @param   bool indent default TRUE whether to indent
     * @return  string
     */
    public function getSource($indent= TRUE) {
      return $this->backing->saveXML();
    }
    
    /**
     * Retrieve XML representation as DOM document
     *
     * @return  php.DOMDocument
     */
    public function getDomTree() {
      return $this->backing;
    }
    
    /**
     * Add a child to this tree
     *
     * @param   xml.Node child 
     * @return  xml.Node the added child
     * @throws  lang.IllegalArgumentException in case the given argument is not a Node
     */   
    public function addChild(DomBackedNode $child) {
      try {
        $child->backing= $this->root->appendChild(new DOMElement($child->transient[0]));
        $child->backing->appendChild(new DOMText($child->transient[1]));
        foreach ($child->transient[2] as $key => $val) {
          $child->backing->setAttribute($key, $val);
        }
        unset($child->transient);
        return $child;
      } catch (DOMException $e) {
        throw new IllegalArgumentException($e->getMessage());
      }
    }

    /**
     * Construct an XML tree from a string.
     *
     * <code>
     *   $tree= Tree::fromString('<document>...</document>');
     * </code>
     *
     * @param   string string
     * @param   string c default __CLASS__ class name
     * @return  xml.Tree
     * @throws  xml.XMLFormatException in case of a parser error
     */
    public static function fromString($string, $c= __CLASS__) {
      $self= new $c();
      $self->doc->loadXML($string);
      return $self;
    }
    
    /**
     * Construct an XML tree from a file.
     *
     * <code>
     *   $tree= Tree::fromFile(new File('foo.xml');
     * </code>
     *
     * @param   io.File file
     * @param   string c default __CLASS__ class name
     * @return  xml.Tree
     * @throws  xml.XMLFormatException in case of a parser error
     * @throws  io.IOException in case reading the file fails
     */ 
    public static function fromFile($file, $c= __CLASS__) {
      $self= new $c();
      $self->doc->load($file);
      return $self;
    }
  } 
?>
