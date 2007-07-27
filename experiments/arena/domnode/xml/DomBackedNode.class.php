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
  class DomBackedNode extends Object {
    public
      $backing= NULL;

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
      $this->transient= array($name, $content, $attribute);
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
          $n->appendChild(self::fromArray($a[$field], $nname));
        } else if (is_object($a[$field])) {
          $n->appendChild(self::fromObject($a[$field], $nname));
        } else {
          $n->appendChild(new self($nname, $a[$field]));
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
      $this->backing->textContent= $content;
    }
    
    /**
     * Get content (all CDATA)
     *
     * @return  string content
     */
    public function getContent() {
      return $this->backing->textContent;
    }

    /**
     * Set an attribute
     *
     * @param   string name
     * @param   string value
     */
    public function setAttribute($name, $value) {
      $this->backing->setAttribute($name, $value);
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
      return $this->backing->hasAttribute($name)
        ? $this->backing->getAttribute($name)
        : $default
       ;
    }

    /**
     * Checks whether a specific attribute is existant
     *
     * @param   string name
     * @return  bool
     */
    public function hasAttribute($name) {
      return $this->backing->hasAttribute($name);
    }

    /**
     * Add a child node
     *
     * @param   xml.Node child
     * @return  xml.Node added child
     * @throws  lang.IllegalArgumentException in case the given argument is not a Node
     */
    public function addChild(DomBackedNode $child) {
      $child->backing= $this->backing->appendChild(new DOMElement($child->transient[0], (string)$child->transient[1]));
      foreach ($child->transient[2] as $key => $val) {
        $child->backing->setAttribute($key, $val);
      }
      unset($child->transient);
      return $child;
    }
  }
?>
