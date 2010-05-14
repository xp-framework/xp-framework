<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'xml.parser.XMLParser',
    'xml.Node',
    'xml.parser.ParserCallback'
  );
 
  /**
   * The Tree class represents a tree which can be exported
   * to and imported from an XML document.
   *
   * @test     xp://net.xp_framework.unittest.xml.TreeTest
   * @see      xp://xml.parser.XMLParser
   * @purpose  Tree
   */
  class Tree extends Object implements ParserCallback {
    public 
      $root     = NULL,
      $nodeType = NULL;

    public
      $_cnt     = NULL,
      $_cdata   = NULL,
      $_objs    = NULL;

    protected 
      $version  = '1.0',
      $encoding = 'iso-8859-1';
    
    /**
     * Constructor
     *
     * @param   string rootName default 'document'
     */
    public function __construct($rootName= 'document') {
      $this->root= new Node($rootName);
      $this->nodeType= xp::reflect('xml.Node');
    }

    /**
     * Set encoding
     *
     * @param   string e encoding
     */
    public function setEncoding($e) {
      $this->encoding= $e;
    }

    /**
     * Set encoding and return this tree
     *
     * @param   string e encoding
     * @return  xml.Tree
     */
    public function withEncoding($e) {
      $this->encoding= $e;
      return $this;
    }
    
    /**
     * Retrieve encoding
     *
     * @return  string encoding
     */
    public function getEncoding() {
      return $this->encoding;
    }
    
    /**
     * Returns XML declaration
     *
     * @return  string declaration
     */
    public function getDeclaration() {
      return sprintf(
        '<?xml version="%s" encoding="%s"?>',
        $this->version,
        $this->encoding
      );
    }
    
    /**
     * Retrieve XML representation
     *
     * @param   bool indent default TRUE whether to indent
     * @return  string
     */
    public function getSource($indent= TRUE) {
      return $this->root->getSource($indent, strtolower($this->encoding), '');
    }
    
    /**
     * Add a child to this tree
     *
     * @param   xml.Node child 
     * @return  xml.Node the added child
     * @throws  lang.IllegalArgumentException in case the given argument is not a Node
     */   
    public function addChild(Node $child) {
      return $this->root->addChild($child);
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
      $parser= new XMLParser();
      $tree= new $c();

      $parser->setCallback($tree);
      $parser->parse($string, 1);

      // Fetch actual encoding from parser
      $tree->setEncoding($parser->getEncoding());

      delete($parser);
      return $tree;
    }
    
    /**
     * Construct an XML tree from a file.
     *
     * <code>
     *   $tree= Tree::fromFile(new File('foo.xml'));
     * </code>
     *
     * @param   io.File file
     * @param   string c default __CLASS__ class name
     * @return  xml.Tree
     * @throws  xml.XMLFormatException in case of a parser error
     * @throws  io.IOException in case reading the file fails
     */ 
    public static function fromFile($file, $c= __CLASS__) {
      $parser= new XMLParser();
      $tree= new $c();
      
      $parser->setCallback($tree);
      $file->open(FILE_MODE_READ);
      $string= $file->read($file->size());
      $file->close();
      $parser->parse($string);

      // Fetch actual encoding from parser
      $tree->setEncoding($parser->getEncoding());

      delete($parser);
      return $tree;
    }
    
    /**
     * Callback function for XMLParser
     *
     * @param   resource parser
     * @param   string name
     * @param   string attrs
     * @see     xp://xml.parser.XMLParser
     */
    public function onStartElement($parser, $name, $attrs) {
      $this->_cdata= '';

      $element= new $this->nodeType($name, NULL, $attrs);
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
     * @param   resource parser
     * @param   string name
     * @see     xp://xml.parser.XMLParser
     */
    public function onEndElement($parser, $name) {
      if ($this->_cnt > 1) {
        $node= $this->_objs[$this->_cnt];
        $node->content= $this->_cdata;
        $parent= $this->_objs[$this->_cnt- 1];
        $parent->addChild($node);
        $this->_cdata= '';
      } else {
        $this->root->content= $this->_cdata;
        $this->_cdata= '';
      }
      $this->_cnt--;
      
      // Clean up upon last element
      if (0 === $this->_cnt) {
        unset($this->_cnt, $this->_cdata, $this->_objs);
      }
    }

    /**
     * Callback function for XMLParser
     *
     * @param   resource parser
     * @param   string cdata
     * @see     xp://xml.parser.XMLParser
     */
    public function onCData($parser, $cdata) {
      $this->_cdata.= $cdata;
    }

    /**
     * Callback function for XMLParser
     *
     * @param   resource parser
     * @param   string data
     * @see     xp://xml.parser.XMLParser
     */
    public function onDefault($parser, $data) {
    }
  } 
?>
