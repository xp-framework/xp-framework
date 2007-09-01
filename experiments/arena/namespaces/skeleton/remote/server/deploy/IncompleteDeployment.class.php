<?php
/* This cause is part of the XP framework
 *
 * $Id: IncompleteDeployment.class.php 9151 2007-01-07 13:52:49Z kiesel $ 
 */

  namespace remote::server::deploy;

  uses('remote.server.deploy.Deployable');

  /**
   * Incomplete deployment
   *
   * @see      xp://remote.server.deploy.Deployable
   * @purpose  Deployment
   */
  class IncompleteDeployment extends lang::Object implements Deployable {
    public
      $origin = '',
      $cause  = NULL;
    
    /**
     * Constructor
     *
     * @param   string origin
     * @param   lang.Throwable cause
     */
    public function __construct($origin, $cause) {
      $this->origin= $origin;
      $this->cause= $cause;
    }

    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'(origin= '.$this->origin.') caused by '.$this->cause->toString();
    }

  } 
?>
