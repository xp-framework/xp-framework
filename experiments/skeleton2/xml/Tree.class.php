<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'xml.XML',
    'xml.XMLParser',
    'xml.Node'
  );

  /**
   * Kapselt einen XML-Baum
   *
   * @see xml.XMLParser
   */
  class Tree extends XML {
    public 
      $root     = NULL,
      $children = array(),
      $nodeType = 'node';

    public
      $_cnt,
      $_cdata,
      $_objs;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   array params default NULL
     */
    public function __construct($params= NULL) {
      $this->_objs= array();        
      $this->root= new Node('document');
      XML::__construct($params);
    }
    
    /**
     * Retrieve XML representation
     *
     * @access  public
     * @param   bool indent default TRUE whether to indent
     * @return  string
     */
    public function getSource($indent= TRUE) {
      return (isset($this->root)
        ? $this->root->getSource($indent)
        : NULL
      );
    }
     
    /**
     * Add a child to this tree
     *
     * @access  public
     * @param   &xml.Node child 
     * @return  &xml.Node the added child
     */   
    public function addChild(&$child) {
      return $this->root->addChild($child);
    }

    /**
     * Construct an XML tree from a string
     *
     * <code>
     *   $tree= Tree::fromString('<document>...</document>');
     * </code>
     *
     * @model   static
     * @access  public
     * @param   string string
     * @param   string c default __CLASS__ class name
     * @return  &xml.Tree
     */
    public static function fromString($string, $c= __CLASS__) {
      $parser= new XMLParser();
      $tree= XPClass::forName($c)->newInstance();
      try {
        $parser->callback= $tree;
        $result= $parser->parse($string, 1);
        $parser->__destruct();
      } catch (XPException $e) {
        throw ($e);
      }
      
      return $tree;
    }
    
    /**
     * Construct an XML tree from a file
     *
     * <code>
     *   $tree= Tree::fromFile(new File('foo.xml');
     * </code>
     *
     * @model   static
     * @access  public
     * @param   &io.File file
     * @param   string c default __CLASS__ class name
     * @return  &xml.Tree
     */ 
    public static function fromFile(&$file, $c= __CLASS__) {
      $parser= new XMLParser();
      $tree= XPClass::forName($c)->newInstance();
      
      try {
        $parser->callback= $this;
        $parser->dataSource= $file->uri;
        $file->open(FILE_MODE_READ);
        $string= $file->read($file->size());
        $file->close();
        
        // Now, parse it
        $result= $parser->parse($string);
        $parser->__destruct();
      } catch (XPException $e) {
        throw ($e);
      }
      
      return $tree;
    }
    
    /**
     * Callback function for XMLParser
     *
     * @access  magic
     * @see     xp://xml.XMLParser
     */
    public function onStartElement($parser, $name, $attrs) {
      $this->_cdata= "";

      $element= XPClass::forName($this)->newInstance->nodeType(array(
        'name'          => $name,
        'attribute'     => $attrs,
        'content'       => ''
      ));  

      if (!isset($this->_cnt)) {
        $this->root= $element;
        $this->_objs[1]= $element;
        $this->_cnt= 1;
      } else {
        $this->_cnt++;
        $this->_objs[$this->_cnt]= $element;
      }
    }
   
    /**
     * Callback function for XMLParser
     *
     * @access  magic
     * @see     xp://xml.XMLParser
     */
    public function onEndElement($parser, $name) {
      if ($this->_cnt > 1) {
        $node= $this->_objs[$this->_cnt];
        $node->content= $this->_cdata;
        $parent= $this->_objs[$this->_cnt- 1];
        $parent->addChild($node);
        $this->_cdata= "";
      }
      $this->_cnt--;
    }

    /**
     * Callback function for XMLParser
     *
     * @access  magic
     * @see     xp://xml.XMLParser
     */
    public function onCData($parser, $cdata) {
      $this->_cdata.= $cdata;
    }

    /**
     * Callback function for XMLParser
     *
     * @access  magic
     * @see     xp://xml.XMLParser
     */
    public function onDefault($parser, $data) {
    }
  }
?>
