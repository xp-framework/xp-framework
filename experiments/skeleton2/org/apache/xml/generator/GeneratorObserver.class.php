<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Observer
   *
   * @model    abstract
   * @see      xp://org.apache.xml.generator.Generator#addObserver
   * @purpose  Base class
   */
  class GeneratorObserver extends Object {

    /**
     * Callback for when generation of target starts
     *
     * @model   abstract
     * @access  public
     * @param   string target
     */    
    public abstract function onStart($target);

    /**
     * Callback for when generation of target succeeds
     *
     * @model   abstract
     * @access  public
     * @param   string target
     */    
    public abstract function onSuccess($target);
    
    /**
     * Callback for when generation of target is omitted
     *
     * @model   abstract
     * @access  public
     * @param   string target
     */    
    public abstract function onOmit($target);

    /**
     * Callback for when generation of target fails
     *
     * @model   abstract
     * @access  public
     * @param   string target
     * @param   &lang.Exception reason
     */    
    public abstract function onFailure($target, XPException $reason);

    /**
     * Callback for when a new dependency is registered
     *
     * @model   abstract
     * @access  public
     * @param   string target
     */    
    public abstract function onNewDependency($target);

    /**
     * Callback for when a new dependency is updated
     *
     * @model   abstract
     * @access  public
     * @param   string target
     */    
    public abstract function onDependencyUpdated($target);

  }
?>
