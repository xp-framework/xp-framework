<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Format base class
   *
   * @purpose  Provide a base class to all format classes
   * @see      xp://text.format.MessageFormat#setFormatter
   * @model    static
   */
  class IFormat extends Object {
    public
      $formatString = '';

    /**
     * Constructor
     *
     * @access  public
     * @param   string f default NULL format string
     */
    public function __construct($f= NULL) {
      $this->formatString= $f;
    }

    /**
     * Get an instance
     *
     * @access  public
     * @return  &text.format.Format
     */
    public function &getInstance($name) {
      static $instance= array();
      
      if (!isset($instance[$name])) $instance[$name]= new $name();
      return $instance[$name];
    }
      
    /**
     * Apply format to argument
     *
     * @access  public
     * @param   mixed fmt
     * @param   &mixed argument
     * @return  string
     * @throws  lang.IllegalAccessException
     */
    public function apply($fmt, &$argument) { 
      throw(new IllegalAccessException('Calling apply method of base class text.format.Format'));
    }
    
    /**
     * Formats this message with the given arguments
     *
     * @access  public
     * @param   mixed* args
     * @throws  lang.FormatException
     */
    public function format() {
      $a= func_get_args();
      return $this->apply($this->formatString, $a);
    }
  }
?>
