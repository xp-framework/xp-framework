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
    var
      $formatString = '';

    /**
     * Constructor
     *
     * @access  public
     * @param   string f default NULL format string
     */
    function __construct($f= NULL) {
      $this->formatString= $f;
      parent::__construct();
    }

    /**
     * Get an instance
     *
     * @access  public
     * @return  &text.format.Format
     */
    function &getInstance($name) {
      static $__instance= array();
      
      if (!isset($__instance[$name])) $__instance[$name]= new $name();
      return $__instance[$name];
    }
      
    /**
     * Apply format to argument
     *
     * @access  public
     * @param   mixed fmt
     * @param   &mixed argument
     * @return  string
     * @throws  IllegalAccessException
     */
    function apply($fmt, &$argument) { 
      return throw(new IllegalAccessException('Calling apply method of base class text.format.Format'));
    }
    
    /**
     * Formats this message with the given arguments
     *
     * @access  public
     * @param   mixed* args
     * @throws  FormatException
     */
    function format() {
      $a= func_get_args();
      return $this->apply($this->formatString, $a);
    }
  }
?>
