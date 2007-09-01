<?php
/* This class is part of the XP framework
 *
 * $Id: IFormat.class.php 10594 2007-06-11 10:04:54Z friebe $
 */

  namespace text::format;

  /**
   * Format base class
   *
   * @purpose  Provide a base class to all format classes
   * @see      xp://text.format.MessageFormat#setFormatter
   */
  class IFormat extends lang::Object {
    public
      $formatString = '';

    /**
     * Constructor
     *
     * @param   string f default NULL format string
     */
    public function __construct($f= ) {
      $this->formatString= $f;
    }

    /**
     * Get an instance
     *
     * @return  text.format.Format
     */
    public function getInstance($name) {
      static $instance= array();
      
      if (!isset($instance[$name])) $instance[$name]= new $name();
      return $instance[$name];
    }
      
    /**
     * Apply format to argument
     *
     * @param   mixed fmt
     * @param   mixed argument
     * @return  string
     * @throws  lang.IllegalAccessException
     */
    public function apply($fmt, $argument) { 
      throw(new lang::IllegalAccessException('Calling apply method of base class text.format.Format'));
    }
    
    /**
     * Formats this message with the given arguments
     *
     * @param   mixed* args
     * @throws  lang.FormatException
     */
    public function format() {
      $a= func_get_args();
      return $this->apply($this->formatString, $a);
    }
  }
?>
