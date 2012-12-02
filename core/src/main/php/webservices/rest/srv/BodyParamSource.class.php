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
  class BodyParamSource extends RestParamSource {

    static function __static() {
      parent::$sources['body']= new XPClass(__CLASS__);
    }

    /**
     * Constructor
     *
     */
    public function __construct() {
      // Empty
    }

    /**
     * Read this parameter from the given request
     *
     */
    public function read($type, $target, $request) {
      return RestFormat::forMediaType($target['input'])->read($request, $type); 
    }

    /**
     * Creates a string representation of this object
     *
     * @return string
     */
    public function toString() {
      return 'body';
    }

    /**
     * Returns whether a given value is equal to this instance
     *
     * @param  var cmp
     * @return bool
     */
    public function equals($cmp) {
      return $cmp instanceof self;
    }
  }
?>