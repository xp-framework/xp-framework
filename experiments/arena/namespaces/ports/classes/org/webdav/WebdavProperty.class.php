<?php
/* This class is part of the XP framework
 *
 * $Id: WebdavProperty.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace org::webdav;

  /**
   * Webdav property object
   *
   * @purpose  Webdav property
   */
  class WebdavProperty extends lang::Object {
    public
      $name         = NULL,
      $value        = NULL,
      $protected    = FALSE,
      $attributes   = array(),
      $nsname       = '',
      $nsprefix     = '';

    /**
     * Constructor
     *
     * @param   string name
     * @param   string value default NULL
     */
    public function __construct($name, $value= NULL) {
      $this->name= $name;
      $this->value= $value;
    }
  
    /**
     * Set Name
     *
     * @param   string name The name
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
     * Set Value
     *
     * @param   mixed value The value
     */
    public function setValue($value) {
      $this->value= $value;
    }

    /**
     * Get Value
     *
     * @return  mixed
     */
    public function getValue() {
      return $this->value;
    }

    /**
     * Set namespace name (e.g. http://apache.org/dav/props/)
     *
     * @param   string namespace Namespace
     */
    public function setNamespaceName($namespace) {
      $this->nsname= $namespace;
    }

    /**
     * Get Namespace
     *
     * @return  string
     */
    public function getNamespaceName() {
      return $this->nsname;
    }
    
    /**
     * Set namespace prefix (e.g. ns1)
     *
     * @param  string prefix The namespace prefix
     */
    public function setNamespacePrefix($prefix) {
      $this->nsprefix= $prefix;
    }
    
    /**
     * Get namespace prefix
     *
     * @return string
     */
    public function getNamespacePrefix() {
      return $this->nsprefix;
    }

    /**
     * Set Protected
     *
     * @param   bool protected
     */
    public function setProtected($protected) {
      $this->protected= $protected;
    }

    /**
     * Get Protected
     *
     * @return  bool
     */
    public function getProtected() {
      return $this->protected;
    }
    
    /**
     * Return property's attributes
     *
     * @return  array[]
     */
    public function getAttributes() {
      return $this->attributes;
    }
    
    /**
     * Convert value to valid string
     *
     * @return  string
     */
    public function toString() {
      switch (::xp::typeOf($this->value)) {
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
