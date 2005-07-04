<?php
/* This class is part of the XP framework
 *
 * $Id$
 *
 */

  uses('xml.XML', 'xml.PCData', 'xml.CData');
  
  define('INDENT_DEFAULT',    0);
  define('INDENT_WRAPPED',    1);
  define('INDENT_NONE',       2);

  define('XML_ILLEGAL_CHARS',   "\x00\x01\x02\x03\x04\x05\x06\x07\x08\x0b\x0c\x0e\x0f\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1a\x1b\x1c\x1d\x1e\x1f");

  /**
   * Represents a node
   *
   * @see   xp://xml.Tree#addChild
   */
  class Node extends XML {
    var 
      $name         = '',
      $attribute    = array(),
      $content      = NULL,
      $children     = array();

    /**
     * Constructor
     *
     * <code>
     *   $n= &new Node('document');
     *   $n= &new Node('text', 'Hello World');
     *   $n= &new Node('article', '', array('id' => 42));
     * </code>
     *
     * @access  public
     * @param   string name
     * @param   string content default NULL
     * @param   array attribute default array() attributes
     * @throws  lang.IllegalArgumentException
     */
    function __construct($name, $content= NULL, $attribute= array()) {
      $this->name= $name;
      $this->attribute= $attribute;
      $this->setContent($content);
    }

    /**
     * Recurse an array
     *
     * @access  protected
     * @param   array a
     */
    function _recurse($a) {
      $sname= rtrim($this->name, 's');
      foreach (array_keys($a) as $field) {
        $child= &$this->addChild(new Node(is_numeric($field) || '' == $field
          ? $sname
          : $field
        ));

        if (is_array($a[$field])) {
          $child->_recurse($a[$field]);
        } else if (is_object($a[$field])) {
          if (!method_exists($a[$field], '__sleep')) {
            $vars= get_object_vars($a[$field]);
          } else {
            $vars= array();
            foreach ($a[$field]->__sleep() as $var) $vars[$var]= $a[$field]->{$var};
          }
          $child->_recurse($vars);
        } else {
          $child->setContent($a[$field]);
        }
      }
    }
    
    /**
     * Create a node from an array
     *
     * Usage example:
     * <code>
     *   $n= &Node::fromArray($array, 'elements');
     * </code>
     *
     * @model   static
     * @access  public
     * @param   array arr
     * @param   string name default 'array'
     * @return  &xml.Node
     */
    function &fromArray($arr, $name= 'array') {
      $n= &new Node($name);
      $n->_recurse($arr);
      return $n;  
    }
    
    /**
     * Create a node from an object. Will use class name as node name
     * if the optional argument name is omitted.
     *
     * Usage example:
     * <code>
     *   $n= &Node::fromObject($object);
     * </code>
     *
     * @model   static
     * @access  public
     * @param   object obj
     * @param   string name default NULL
     * @return  &xml.Node
     */
    function &fromObject($obj, $name= NULL) {
      if (!method_exists($obj, '__sleep')) {
        $vars= get_object_vars($obj);
      } else {
        $vars= array();
        foreach ($obj->__sleep() as $var) $vars[$var]= $obj->{$var};
      }
      return Node::fromArray(
        $vars, 
        (NULL === $name) ? get_class($obj) : $name
      );
    }

    /**
     * Set Name
     *
     * @access  public
     * @param   string name
     */
    function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  string
     */
    function getName() {
      return $this->name;
    }
    
    /**
     * Set content
     *
     * @access  public
     * @param   string content
     * @throws  xml.XMLFormatException in case content contains illegal characters
     */
    function setContent($content) {
      if (is_string($content)) {

        // Scan the given string for illegal characters. If no illegal characters are 
        // found, strtok returns the unchanged string. Otherwise the string up to the 
        // illegal character will be returned (or - if the char is the first in the 
        // string, the string excluding the first char will be returned).
        if (strlen($content) > ($p= strcspn($content, XML_ILLEGAL_CHARS))) {
          return throw(new XMLFormatException(
            'Content contains illegal character at position '.$p. ' / chr('.ord($content{$p}).')'
          ));
        }
      }
      
      $this->content= $content;
    }
    
    /**
     * Get content (all CDATA)
     *
     * @access  public
     * @return  string content
     */
    function getContent() {
      return $this->content;
    }

    /**
     * Set an attribute
     *
     * @access  public
     * @param   string name
     * @param   string value
     */
    function setAttribute($name, $value) {
      $this->attribute[$name]= $value;
    }
    
    /**
     * Retrieve an attribute by its name. Returns the default value if the
     * attribute is non-existant
     *
     * @access  public
     * @param   string name
     * @param   mixed default default NULL
     * @return  string
     */
    function getAttribute($name, $default= NULL) {
      return isset($this->attribute[$name]) ? $this->attribute[$name] : $default;
    }

    /**
     * Checks whether a specific attribute is existant
     *
     * @access  public
     * @param   string name
     * @return  bool
     */
    function hasAttribute($name) {
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
     * @access  public
     * @param   int indent default INDENT_WRAPPED
     * @param   string inset default ''
     * @return  string XML
     */
    function getSource($indent= INDENT_WRAPPED, $inset= '') {
      $xml= $inset.'<'.$this->name;
      switch (gettype($this->content)) {
        case 'string': 
          $content= htmlspecialchars($this->content); 
          break;

        case 'object':
          if (is_a($this->content, 'PCData')) {
            $content= $this->content->pcdata;
          } else if (is_a($this->content, 'CData')) {
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
            if (empty($this->children)) {
              return $xml."/>\n";
            }
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
     * Add a child node
     *
     * @access  public
     * @param   &xml.Node child
     * @return  &xml.Node added child
     * @throws  lang.IllegalArgumentException
     */
    function &addChild(&$child) {
      if (!is_a($child, 'Node')) {
        return throw(new IllegalArgumentException(
          'Parameter child must be an xml.Node (given: '.xp::typeOf($child).')'
        ));
      }

      $this->children[]= &$child;
      return $child;
    }
  }
?>
