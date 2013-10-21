<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'webservices.rest.srv';

  /**
   * Represents input
   *
   */
  abstract class webservices·rest·srv·Input extends Object {
    protected $request;

    /**
     * Creates a new Input instance
     *
     * @param scriptlet.Request $request
     */
    public function __construct(scriptlet·Request $request) {
      $this->request= $request;
    }
  }
?>
