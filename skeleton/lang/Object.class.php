<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * Basis-Objekt für alle anderen
   */
  class Object {
  
    /**
     * Constructor-Wrapper bis PHP5
     */
    function Object() {
      $args= func_get_args();
      call_user_func_array(
        array(&$this, '__construct'),
        $args
      );
    }

    /**
     * Constructor
     */
    function __construct($params= NULL) {
      if (NULL == $params) return;
      foreach ($params as $key=> $val) $this->$key= $val;
    }
    
    /**
     * Destructor
     */
    function __destruct() {
      unset($this);
    }
    
    /** 
     * Gibt den vollen Namen der Klasse zurück, bspw. lang.Object
     *
     * @return  string Voller Name
     */
    function getName() {
      return $GLOBALS['php_class_names'][get_class($this)];
    }

    function &getClass() {
      return new XPClass($this);
    }

    function toString() {
      return $this->getName().'@'.serialize($this);
    }
  }
