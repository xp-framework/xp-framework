<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * RMI Connector
   *
   * @see      xp://rmi.RMIObject
   * @purpose  Base class
   */
  class RMIConnector extends Object {
      
    /**
     * Get a value by its name
     *
     * @model   abstract
     * @access  public
     * @param   &rmi.RMIObject object
     * @param   string name
     * @return  &mixed value
     * @throws  rmi.RMIException to indicate failure
     */
    public abstract function getValue(&$object, $name) ;
    
    /**
     * Set a value by its name
     *
     * @model   abstract
     * @access  public
     * @param   &rmi.RMIObject object
     * @param   string name
     * @param   &mixed value
     */
    public abstract function setValue(&$object, $name, &$value) ;
    
    /**
     * Invoke a method
     *
     * @model   abstract
     * @access  public
     * @param   &rmi.RMIObject object
     * @param   string name
     * @param   &array args
     * @return  &mixed value
     */
    public abstract function invokeMethod(&$object, $name, &$args) ;
  }
?>
