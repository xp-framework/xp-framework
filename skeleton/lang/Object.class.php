<?php
/* Diese Klasse ist Teil des XP-Frameworks
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
    function Object($params= NULL) {
      $this->__construct($params);
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
  }
