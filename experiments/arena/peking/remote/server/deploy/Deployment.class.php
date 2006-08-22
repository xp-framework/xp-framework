<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Deployment
   *
   * @see      xp://remote.server.deploy.Deployable
   * @purpose  Deployment
   */
  class Deployment extends Object {
    var
      $origin = '',
      $class  = NULL;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string origin
     * @param   &lang.XPClass class
     */
    function __construct($origin, &$class) {
      $this->origin= $origin;
      $this->class= &$class;
    }
    
    /**
     * Creates a string representation of this object
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return $this->getClassName().'(origin= '.$this->origin.', class= '.$this->class->toString().')';
    }

  } implements(__FILE__, 'remote.server.deploy.Deployable');
?>
