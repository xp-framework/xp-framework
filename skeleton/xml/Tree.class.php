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
    var 
      $root,
      $children;
      
    var
      $_cnt,
      $_cdata,
      $_objs;
    
    var $nodeType= 'node';
    
    /**
     * Constructor
     */
    function __construct($params= NULL) {
      $this->_objs= array();		
      
      $this->root= new Node(array(
        'name'  => 'document'
      ));
      XML::__construct($params);
    }
    
    /**
     * Den XML-Source des Baums zurückgeben
     *
     * @access  public
     * @param   bool indent default TRUE Einrückung
     * @return  string XML-Source
     */
    function getSource($indent= TRUE) {
      return (isset($this->root)
        ? $this->root->getSource($indent)
        : NULL
      );
    }
     
    /**
     * Ein Objekt dem Root-Element hinzufügen
     *
     * @access  public
     * @param   xml.Node child Das Child-Objekt
     * @return  xml.Node das hinzugefügte Objekt
     */   
    function &addChild($child) {
      return $this->root->addChild($child);
    }

    /**
     * Konstruiert einen Baum aus einem String
     *
     * @access  public
     * @param   string string String, der das XML enthält
     * @return  bool Parser-Ergebnis
     */ 
    function fromString($string) {
      $parser= new XMLParser();
      $parser->callback= &$this;
      $result= $parser->parse($string, 1);
      $parser->__destruct();
      return $result;
    }
    
    /**
     * Konstruiert einen Baum aus einem File
     *
     * @access  public
     * @param   io.File file Datei mit dem XML
     * @return  bool Parser-Ergebnis
     * @throws  Exception wenn die Datei nicht gelesen werden kann
     */ 
    function fromFile($file) {
      $parser= new XMLParser();
      $parser->callback= &$this;
      $parser->dataSource= $file->uri;
      
      try(); {
        $file->open(FILE_MODE_READ);
        $string= $file->read($file->size());
        $file->close();
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      $result= $parser->parse($string);
      $parser->__destruct();
      return $result;
    }
    
    /**
     * Private Callback-Funktion
     *
     * @see xml.XMLParser
     */
    function _pCallStartElement($parser, $name, $attrs) {
      $this->_cdata= "";

      $element= new $this->nodeType(array(
        'name'          => $name,
        'attribute'     => $attrs,
        'content'       => ''
      ));  

      if (!isset($this->_cnt)) {
        $this->root= &$element;
        $this->_objs[1]= &$element;
        $this->_cnt= 1;
      } else {
        $this->_cnt++;
        $this->_objs[$this->_cnt]= &$element;
      }
    }
   
    /**
     * Private Callback-Funktion
     *
     * @see xml.XMLParser
     */
    function _pCallEndElement($parser, $name) {
      if ($this->_cnt > 1) {
        $node= &$this->_objs[$this->_cnt];
        $node->content= $this->_cdata;
        $parent= &$this->_objs[$this->_cnt- 1];
        $parent->addChild($node);
        //var_dump('adding '.$node->name.' ['.$this->_cnt.'] to '.$parent->name.' ['.($this->_cnt- 1).']');
        $this->_cdata= "";
      }
      $this->_cnt--;
    }

    /**
     * Private Callback-Funktion
     *
     * @see xml.XMLParser
     */
    function _pCallCData($parser, $cdata) {
      $this->_cdata.= $cdata;
    }

    /**
     * Private Callback-Funktion
     *
     * @see xml.XMLParser
     */
    function _pCallDefault($parser, $data) {
    }
  }
?>
