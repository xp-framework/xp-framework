<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Dependency
   *
   * @model    abstract
   * @purpose  Base class
   * @deprecated
   */
  class Dependency extends Object {
    var
      $name     = '';
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     */
    function __construct($name) {
      $this->name= $name;
      parent::__construct();
    }
    
    /**
     * Update this dependency's timestamp
     *
     * @access  public
     * @param   int time Timestamp
     * @return  bool
     */
    function touch($time) {
      return touch($this->name, $time);
    }
    
    /**
     * Figure out whether this dependency has changed since a given
     * time stamp
     *
     * @access  public
     * @param   int since
     * @return  bool
     */
    function hasChangedSince($since) {
      return filemtime($this->name) > $since;
    }
  
    /**
     * Generate this dependency
     *
     * @model   abstract
     * @access  public
     * @param   &org.apache.xml.generator.Generator generator
     * @return  bool success
     */
    function generate(&$generator) { }
  
    /**
     * Process this dependency
     *
     * @model   abstract
     * @access  public
     * @param   string params
     * @return  string xml
     */
    function process($params) { }
  
  }
?>
