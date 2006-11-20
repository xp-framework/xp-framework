<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Webdav property object
   *
   * @purpose  Webdav property
   */
  class WebdavProperty extends Object {
    var
      $name         = NULL,
      $value        = NULL,
      $protected    = FALSE,
      $attributes   = array(),
      $nsname       = '',
      $nsprefix     = '';

    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     * @param   string value default NULL
     */
    function __construct($name, $value= NULL) {
      $this->name= $name;
      $this->value= $value;
    }
  
    /**
     * Set Name
     *
     * @access  public
     * @param   string name The name
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
     * Set Value
     *
     * @access  public
     * @param   mixed value The value
     */
    function setValue($value) {
      $this->value= $value;
    }

    /**
     * Get Value
     *
     * @access  public
     * @return  mixed
     */
    function getValue() {
      return $this->value;
    }

    /**
     * Set namespace name (e.g. http://apache.org/dav/props/)
     *
     * @access  public
     * @param   string namespace Namespace
     */
    function setNamespaceName($namespace) {
      $this->nsname= $namespace;
    }

    /**
     * Get Namespace
     *
     * @access  public
     * @return  string
     */
    function getNamespaceName() {
      return $this->nsname;
    }
    
    /**
     * Set namespace prefix (e.g. ns1)
     *
     * @access public
     * @param  string prefix The namespace prefix
     */
    function setNamespacePrefix($prefix) {
      $this->nsprefix= $prefix;
    }
    
    /**
     * Get namespace prefix
     *
     * @access public
     * @return string
     */
    function getNamespacePrefix() {
      return $this->nsprefix;
    }

    /**
     * Set Protected
     *
     * @access  public
     * @param   bool protected
     */
    function setProtected($protected) {
      $this->protected= $protected;
    }

    /**
     * Get Protected
     *
     * @access  public
     * @return  bool
     */
    function getProtected() {
      return $this->protected;
    }
    
    /**
     * Return property's attributes
     *
     * @access  public
     * @return  array[]
     */
    function getAttributes() {
      return $this->attributes;
    }
    
    /**
     * Convert value to valid string
     *
     * @access  public
     * @return  string
     */
    function toString() {
      switch (xp::typeOf($this->value)) {
        case 'boolean': 
          return $this->value ? 'T' : 'F';

        case 'util.Date':
          $this->attributes['xmlns:b']= 'urn:uuid:c2f41010-65b3-11d1-a29f-00aa00c14882/';
          $this->attributes['b:dt']= 'dateTime.rfc1123';
          return $this->value->toString($this->name == 'getlastmodified' ? 'D, j M Y H:m:s \G\M\T' : 'Y-m-d\TH:m:s\Z');

        default:
          return (string)$this->value;
      }
    }
  }
?>
