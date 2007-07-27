<?php
/* This class is part of the XP framework
 *
 * $Id: Node.class.php 9496 2007-02-26 15:27:20Z friebe $
 *
 */

  uses(
    'xml.PCData',
    'xml.CData',
    'xml.XMLFormatException'
  );
  
  define('INDENT_DEFAULT',    0);
  define('INDENT_WRAPPED',    1);
  define('INDENT_NONE',       2);

  define('XML_ILLEGAL_CHARS',   "\x00\x01\x02\x03\x04\x05\x06\x07\x08\x0b\x0c\x0e\x0f\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1a\x1b\x1c\x1d\x1e\x1f");

  /**
   * Represents a node
   *
   * @see   xp://xml.Tree#addChild
   * @test  xp://net.xp_framework.unittest.xml.NodeTest
   */
  class Node extends Object {
    public 
      $name         = '',
      $attribute    = array(),
      $content      = NULL,
      $children     = array();

    /**
     * Constructor
     *
     * <code>
     *   $n= new Node('document');
     *   $n= new Node('text', 'Hello World');
     *   $n= new Node('article', '', array('id' => 42));
     * </code>
     *
     * @param   string name
     * @param   string content default NULL
     * @param   array<string, string> attribute default array() attributes
     * @throws  lang.IllegalArgumentException
     */
    public function __construct($name, $content= NULL, $attribute= array()) {
      $this->name= $name;
      $this->attribute= $attribute;
      $this->setContent($content);
    }

    /**
     * Create a node from an array
     *
     * Usage example:
     * <code>
     *   $n= Node::fromArray($array, 'elements');
     * </code>
     *
     * @param   array arr
     * @param   string name default 'array'
     * @return  xml.Node
     */
    public static function fromArray($a, $name= 'array') {
      $n= new self($name);
      $sname= rtrim($name, 's');
      foreach (array_keys($a) as $field) {
        $nname= is_numeric($field) || '' == $field ? $sname : $field;
        if (is_array($a[$field])) {
          $n->addChild(self::fromArray($a[$field], $nname));
        } else if (is_object($a[$field])) {
          $n->addChild(self::fromObject($a[$field], $nname));
        } else {
          $n->addChild(new self($nname, $a[$field]));
        }
      }
      return $n;  
    }
    
    /**
     * Create a node from an object. Will use class name as node name
     * if the optional argument name is omitted.
     *
     * Usage example:
     * <code>
     *   $n= Node::fromObject($object);
     * </code>
     *
     * @param   object obj
     * @param   string name default NULL
     * @return  xml.Node
     */
    public static function fromObject($obj, $name= NULL) {
      if (!method_exists($obj, '__sleep')) {
        $vars= get_object_vars($obj);
      } else {
        $vars= array();
        foreach ($obj->__sleep() as $var) $vars[$var]= $obj->{$var};
      }
      foreach ($vars as $key => $value) { if ('_' == $key{0}) unset($vars[$key]); }

      return self::fromArray($vars, (NULL === $name) ? get_class($obj) : $name);
    }

    /**
     * Set Name
     *
     * @param   string name
     */
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }
    
    /**
     * Set content
     *
     * @param   string content
     * @throws  xml.XMLFormatException in case content contains illegal characters
     */
    public function setContent($content) {

      // Scan the given string for illegal characters.
      if (is_string($content)) {  
        if (strlen($content) > ($p= strcspn($content, XML_ILLEGAL_CHARS))) {
          throw(new XMLFormatException(
            'Content contains illegal character at position '.$p. ' / chr('.ord($content{$p}).')'
          ));
        }
      }
      
      $this->content= $content;
    }
    
    /**
     * Get content (all CDATA)
     *
     * @return  string content
     */
    public function getContent() {
      return $this->content;
    }

    /**
     * Set an attribute
     *
     * @param   string name
     * @param   string value
     */
    public function setAttribute($name, $value) {
      $this->attribute[$name]= $value;
    }
    
    /**
     * Retrieve an attribute by its name. Returns the default value if the
     * attribute is non-existant
     *
     * @param   string name
     * @param   mixed default default NULL
     * @return  string
     */
    public function getAttribute($name, $default= NULL) {
      return isset($this->attribute[$name]) ? $this->attribute[$name] : $default;
    }

    /**
     * Checks whether a specific attribute is existant
     *
     * @param   string name
     * @return  bool
     */
    public function hasAttribute($name) {
      return isset($this->attribute[$name]);
    }
    
    /**
     * Retrieve XML representation
     *
     * Setting indent to 0 (INDENT_DEFAULT) yields this result:
     * <pre>
     *   <item>  
     *     <title>Website created</title>
     *     <link/>
     *     <description>The first version of the XP web site is online</description>
     *     <dc:date>2002-12-27T13:10:00</dc:date>
     *   </item>
     * </pre>
     *
     * Setting indent to 1 (INDENT_WRAPPED) yields this result:
     * <pre>
     *   <item>
     *     <title>
     *       Website created
     *     </title>
     *     <link/>
     *     <description>
     *       The first version of the XP web site is online
     *     </description>
     *     <dc:date>
     *       2002-12-27T13:10:00
     *     </dc:date>  
     *   </item>
     * </pre>
     *
     * Setting indent to 2 (INDENT_NONE) yields this result (wrapped for readability,
     * returned XML is on one line):
     * <pre>
     *   <item><title>Website created</title><link></link><description>The 
     *   first version of the XP web site is online</description><dc:date>
     *   2002-12-27T13:10:00</dc:date></item>
     * </pre>
     *
     * @param   int indent default INDENT_WRAPPED
     * @param   string inset default ''
     * @return  string XML
     */
    public function getSource($indent= INDENT_WRAPPED, $inset= '') {
      $xml= $inset.'<'.$this->name;
      
      switch (gettype($this->content)) {
        case 'string': 
          $content= htmlspecialchars($this->content); 
          break;

        case 'object':
          if ($this->content instanceof PCData) {
            $content= $this->content->pcdata;
          } else if ($this->content instanceof CData) {
            $content= '<![CDATA['.str_replace(']]>', ']]]]><![CDATA[>', $this->content->cdata).']]>';
          }
          break;

        case 'float':
        
          // Check for integers bigger than MAX_INT
          if ($this->content - floor($this->content) == 0) {
            $content= number_format($this->content, 0, NULL, NULL);
            break;
          }
          // Break missing intentionally

        default: 
          $content= $this->content; 
          break;
      }

      switch ($indent) {
        case INDENT_DEFAULT:
        case INDENT_WRAPPED:
          if (!empty($this->attribute)) {
            $sep= (sizeof($this->attribute) < 3) ? '' : "\n".$inset;
            foreach (array_keys($this->attribute) as $key) {
              $xml.= $sep.' '.$key.'="'.htmlspecialchars($this->attribute[$key]).'"';
            }
            $xml.= $sep;
          }

          // No content and no children => close tag
          if (0 == strlen($content)) {
            if (empty($this->children)) return $xml."/>\n";
            $xml.= '>';
          } else {
            $xml.= '>'.($indent ? "\n  ".$inset.$content : trim($content));
          }

          if (!empty($this->children)) {
            $xml.= ($indent ? '' : $inset)."\n";
            foreach (array_keys($this->children) as $key) {
              $xml.= $this->children[$key]->getSource($indent, $inset.'  ');
            }
            $xml= ($indent ? substr($xml, 0, -1) : $xml).$inset;
          }
          return $xml.($indent ? "\n".$inset : '').'</'.$this->name.">\n";
          
        case INDENT_NONE:
          foreach (array_keys($this->attribute) as $key) {
            $xml.= ' '.$key.'="'.htmlspecialchars($this->attribute[$key]).'"';
          }
          $xml.= '>'.trim($content);
          
          if (!empty($this->children)) {
            foreach (array_keys($this->children) as $key) {
              $xml.= $this->children[$key]->getSource($indent, $inset);
            }
          }
          return $xml.'</'.$this->name.'>';
      }
    }
    
    /**
     * Retrieve XML representation as DOM element
     *
     * @param   php.DOMDocument doc
     * @return  php.DOMElement
     */
    public function getDomNode(DOMDocument $doc) {
      $element= $doc->createElement($this->name);
      
      // Set all attributes
      foreach ($this->attribute as $key => $value) {
        $element->setAttribute($key, htmlspecialchars($value));
      }
      
      // Set content
      switch (TRUE) {
        case is_null($this->content): break;
        case is_string($this->content): $element->nodeValue= htmlspecialchars($this->content); break;
        case is_scalar($this->content): $element->nodeValue= $this->content; break;
        
        case $this->content instanceof CData:
        case $this->content instanceof PCData: {
          $element->appendChild($this->content->getDomNode($doc));
          break;
        }
        
        default: throw new XMLFormatException('Content is neither string nor cdata: '.xp::stringOf($this->content));
      }
      
      // Process all children
      foreach ($this->children as $child) {
        $element->appendChild($child->getDomNode($doc));
      }
      
      return $element;
    }
    
    /**
     * Add a child node
     *
     * @param   xml.Node child
     * @return  xml.Node added child
     * @throws  lang.IllegalArgumentException in case the given argument is not a Node
     */
    public function addChild(Node $child) {
      $this->children[]= $child;
      return $child;
    }
  }
?>
