<?php
/* This class is part of the XP framework
 *
 * $Id$
 *
 */

  uses('xml.XML', 'xml.PCData');

  /**
   * Represents a node
   *
   * @see   xp://xml.Tree#addChild
   */
  class Node extends XML {
    var 
      $name         = '',
      $attribute    = array(),
      $content      = '',
      $children     = array();

    /**
     * Constructor
     *
     * <code>
     *   $n= &new Node('document');
     *   $n= &new Node('text', 'Hello World');
     *   $n= &new Node('article', '', array('id' => 42));
     *   $n= &new Node(array(
     *     'name'    => 'changedby',
     *     'content' => 'me'
     *   ));
     * </code>
     *
     * @access  public
     * @param   mixed*
     * @throws  IllegalArgumentException
     */
    function __construct() {
      switch (func_num_args()) {
        case 0: 
          parent::__construct();
          break;
          
        case 1:
          if (is_array($arg= func_get_arg(0))) {
            parent::__construct($arg);
            break;
          }
          $this->name= $arg;
          break;
          
        case 2:
          list($this->name, $this->content)= func_get_args();
          parent::__construct();
          break;
          
        case 3:
          list($this->name, $this->content, $this->attribute)= func_get_args();
          parent::__construct();
          break;
          
        default:
          return throw(new IllegalArgumentException('Wrong number of arguments passed'));
      }
    }

    /**
     * Recurse an array
     *
     * @access  protected
     * @param   &xml.Node e element to add array to
     * @param   array a
     */
    function _recurse(&$e, $a) {
      foreach (array_keys($a) as $field) {
        $child= &$e->addChild(new Node(is_numeric($field) 
          ? preg_replace('=s$=', '', $e->name) 
          : $field
        ));
        if (is_array($a[$field])) {
          $this->_recurse($child, $a[$field]);
        } else if (is_object($a[$field])) {
          $this->_recurse($child, get_object_vars($a[$field]));
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
    function fromArray($arr, $name= 'array') {
      $n= &new Node($name);
      $n->_recurse($n, $arr);
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
    function fromObject($obj, $name= NULL) {
      return Node::fromArray(
        get_object_vars($obj), 
        (NULL === $name) ? get_class($obj) : $name
      );
    }
    
    /**
     * Set content
     *
     * @access  public
     * @param   string contennt
     */
    function setContent($content) {
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
     * Retreive XML representation
     *
     * @access  public
     * @param   bool indent defaulf TRUE
     * @param   string inset default ''
     * @return  string XML
     */
    function getSource($indent= TRUE, $inset= '') {
      $xml= $inset.'<'.$this->name;
      $content= is_a($this->content, 'PCData') ? $this->content->pcdata : htmlspecialchars($this->content);
      
      // Attribute
      $sep= '';
      if (isset($this->attribute) and is_array($this->attribute)) {
        $sep= ($indent || sizeof($this->attribute) < 3) ? '' : "\n".$inset;
        foreach (array_keys($this->attribute) as $key) {
          $xml.= $sep.' '.$key.'="'.htmlspecialchars($this->attribute[$key]).'"';
        }
      }
      $xml.= $sep;
      
      // No indent => strip whitespace
      if (!$indent) $content= trim(chop($content));
      
      // No content and no children => close tag
      if (
        (0 == strlen($content)) &&
        (!isset($this->children))
      ) {
        return $xml."/>\n";
      }
      
      $xml.= '>'.($indent ? "\n  ".$inset : '').$content;
      
      // Go through children
      if (isset($this->children)) {
        $xml.= "\n";
        foreach (array_keys($this->children) as $key) {
          $xml.= $this->children[$key]->getSource($indent, $inset.'  ');
        }
        if (!$indent) $xml.= $inset;
      }
      
      return $xml.($indent ? $inset : '').'</'.$this->name.">\n";
    }
    
    /**
     * Add a child node
     *
     * @access  public
     * @param   &xml.Node child
     * @return  &xml.Node added child
     */
    function &addChild(&$child) {
      if (!is_a($child, 'Node')) {
        trigger_error('Type: '.get_class($child), E_USER_NOTICE);
        return throw(new IllegalArgumentException('parameter child must be an xml.Node'));
      }
      
      $this->children[]= &$child;
      return $child;
    }
  }
?>
