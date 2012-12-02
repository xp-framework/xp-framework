<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('webservices.rest.srv.RestParamSource');

  /**
   * Abstract base class
   *
   */
  class QueryParamSource extends RestParamSource {
    protected $name;

    static function __static() {
      parent::$sources['param']= new XPClass(__CLASS__);
    }

    /**
     * Creates a new query parameter
     *
     * @param  string name
     */
    public function __construct($name) {
      parent::__construct();
      $this->name= $name;
    }

    /**
     * Read this parameter from the given request
     *
     */
    public function read($type, $route, $request) {
      return $request->hasParam($this->name)
        ? $this->convert->convert($type, $request->getParam($this->name))
        : NULL
      ;
    }

    /**
     * Creates a string representation of this object
     *
     * @return string
     */
    public function toString() {
      return 'param(\''.$this->name.'\')';
    }

    /**
     * Returns whether a given value is equal to this instance
     *
     * @param  var cmp
     * @return bool
     */
    public function equals($cmp) {
      return $cmp instanceof self && $cmp->name === $this->name;
    }
  }
?>