<?php
/* This cause is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Incomplete deployment
   *
   * @see      xp://remote.server.deploy.Deployable
   * @purpose  Deployment
   */
  class IncompleteDeployment extends Object {
    var
      $origin = '',
      $cause  = NULL;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string origin
     * @param   &lang.Throwable cause
     */
    function __construct($origin, &$cause) {
      $this->origin= $origin;
      $this->cause= &$cause;
    }

    /**
     * Creates a string representation of this object
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return $this->getClassName().'(origin= '.$this->origin.') caused by '.$this->cause->toString();
    }

  } implements(__FILE__, 'remote.server.deploy.Deployable');
?>
