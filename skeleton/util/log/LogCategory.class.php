<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  define('LOGGER_FLAG_INFO',    0x0001);
  define('LOGGER_FLAG_WARN',    0x0002);
  define('LOGGER_FLAG_ERROR',   0x0004);
  define('LOGGER_FLAG_DEBUG',   0x0008);
  define('LOGGER_FLAG_ALL',     LOGGER_FLAG_INFO | LOGGER_FLAG_WARN | LOGGER_FLAG_ERROR | LOGGER_FLAG_DEBUG);
  
  /**
   * The log category is the interface to be used. All logging information
   * is sent to a log category via one of the info, warn, error, debug 
   * methods (or their *f variants which use sprintf).
   *
   * Basic example:
   * <code>
   *   $l= &Logger::getInstance();
   *   $cat= &$l->getCategory();
   *   $cat->addAppender(new ConsoleAppender());
   *
   *   $cat->info('Starting work at', Date::now());
   * </code>
   *
   * @purpose  Base class
   */
  class LogCategory extends Object {
    var 
      $_appenders= array(),
      $_indicators= array(
        LOGGER_FLAG_INFO        => 'info',
        LOGGER_FLAG_WARN        => 'warn',
        LOGGER_FLAG_ERROR       => 'error',
        LOGGER_FLAG_DEBUG       => 'debug'
      );
      
    var
      $flags,
      $identifier,
      $dateformat,
      $format;

    /**
     * Constructor
     *
     * @access  public
     * @param   string identifier
     * @param   string format 
     * @param   string dateformat
     * @param   int flags
     */
    function __construct($identifier, $format, $dateformat, $flags= LOGGER_FLAG_ALL) {
      $this->identifier= $identifier;
      $this->format= $format;
      $this->dateformat= $dateformat;
      $this->flags= $flags;
      $this->_appenders= array();
      parent::__construct();
    }

    /**
     * Sets the flags (what should be logged). Note that you also
     * need to add an appender for a category you want to log.
     *
     * @access  public
     * @param   int flags bitfield with flags (LOGGER_FLAG_*)
     */
    function setFlags($flags) {
      $this->flags= $flags;
    }
    
    /**
     * Gets flags
     *
     * @access  public
     * @return  int flags
     */
    function getFlags() {
      return $this->flags;
    }
    
    /**
     * Private helper function
     *
     * @access private
     */
    function callAppenders() {
      $args= func_get_args();
      $flag= $args[0];
      if (!($this->flags & $flag)) return;
      
      $args[0]= sprintf(
        $this->format,
        date($this->dateformat),
        $this->identifier,
        $this->_indicators[$flag]
      );
      
      foreach (array_keys($this->_appenders) as $appflag) {
        if (!($flag & $appflag)) continue;
        foreach (array_keys($this->_appenders[$appflag]) as $idx) {
          call_user_func_array(
            array(&$this->_appenders[$appflag][$idx], 'append'),
            $args
          );
        }
      }
    }
    
    /**
     * Finalize
     *
     * @access  private
     */
    function finalize() {
      foreach ($this->_appenders as $flags => $appenders) {
        foreach (array_keys($appenders) as $idx) {
          $appenders[$idx]->finalize();
        }
      }
    }
    
    /**
     * Adds an appender for the given log categories. Use
     * logical OR to combine the log types or use 
     * LOGGER_FLAG_ALL (default) to log all types.
     *
     * @access  public
     * @param   Appender appender The appender object
     * @param   int flag default LOGGER_FLAG_ALL
     * @return  Appender
     */
    function &addAppender(&$appender, $flag= LOGGER_FLAG_ALL) {
      $this->_appenders[$flag][]= &$appender;
      return $appender;
    }

    /**
     * Appends a log of type info
     *
     * @access  public
     * @param   mixed args 
     */
    function info() {
      $args= func_get_args();
      array_unshift($args, LOGGER_FLAG_INFO);
      call_user_func_array(
        array(&$this, 'callAppenders'),
        $args
      );
    }

    /**
     * Appends a log of type info in printf-style
     *
     * @access  public
     * @param   string format 
     * @param   mixed args 
     */
    function infof() {
      $args= func_get_args();
      $this->callAppenders(
        LOGGER_FLAG_INFO,
        vsprintf($args[0], array_slice($args, 1))
      );
    }

    /**
     * Appends a log of type warn
     *
     * @access  public
     * @param   mixed args Beliebige Variablen
     */
    function warn() {
      $args= func_get_args();
      array_unshift($args, LOGGER_FLAG_WARN);
      call_user_func_array(
        array(&$this, 'callAppenders'),
        $args
      );
    }

    /**
     * Appends a log of type info in printf-style
     *
     * @access  public
     * @param   string format 
     * @param   mixed args 
     */
    function warnf() {
      $args= func_get_args();
      $this->callAppenders(
        LOGGER_FLAG_WARN,
        vsprintf($args[0], array_slice($args, 1))
      );
    }

    /**
     * Appends a log of type error
     *
     * @access  public
     * @param   mixed args 
     */
    function error() {
      $args= func_get_args();
      array_unshift($args, LOGGER_FLAG_ERROR);
      call_user_func_array(
        array(&$this, 'callAppenders'),
        $args
      );
    }

    /**
     * Appends a log of type info in printf-style
     *
     * @access  public
     * @param   string format 
     * @param   mixed args 
     */
    function errorf() {
      $args= func_get_args();
      $this->callAppenders(
        LOGGER_FLAG_ERROR,
        vsprintf($args[0], array_slice($args, 1))
      );
    }

    /**
     * Appends a log of type debug
     *
     * @access  public
     * @param   mixed args 
     */
    function debug() {
      $args= func_get_args();
      array_unshift($args, LOGGER_FLAG_DEBUG);
      call_user_func_array(
        array(&$this, 'callAppenders'),
        $args
      );
    }
 
     /**
     * Appends a log of type info in printf-style
     *
     * @access  public
     * @param   string format format string
     * @param   mixed args 
     */
    function debugf() {
      $args= func_get_args();
      $this->callAppenders(
        LOGGER_FLAG_DEBUG,
        vsprintf($args[0], array_slice($args, 1))
      );
    }
   
    /**
     * Appends a spearator
     *
     * @access  public
     */
    function mark() {
      $this->callAppenders(
        LOGGER_FLAG_INFO, 
        str_repeat('-', 72)
      );
    }
  }
?>
