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
   */
  class Dependency extends Object {
    public
      $name     = '';
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     */
    public function __construct($name) {
      $this->name= $name;
      
    }
    
    /**
     * Update this dependency's timestamp
     *
     * @access  public
     * @param   int time Timestamp
     * @return  bool
     */
    public function touch($time) {
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
    public function hasChangedSince($since) {
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
    public abstract function generate(Generator $generator);
  
    /**
     * Process this dependency
     *
     * @model   abstract
     * @access  public
     * @param   string params
     * @return  string xml
     */
    public abstract function process($params);
  
  }
?>
