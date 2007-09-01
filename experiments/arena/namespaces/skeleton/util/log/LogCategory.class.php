<?php
/* This class is part of the XP framework
 *
 * $Id: LogCategory.class.php 10282 2007-05-07 14:52:17Z friebe $
 */

  namespace util::log;

  define('LOGGER_FLAG_INFO',    0x0001);
  define('LOGGER_FLAG_WARN',    0x0002);
  define('LOGGER_FLAG_ERROR',   0x0004);
  define('LOGGER_FLAG_DEBUG',   0x0008);
  define('LOGGER_FLAG_ALL',     LOGGER_FLAG_INFO | LOGGER_FLAG_WARN | LOGGER_FLAG_ERROR | LOGGER_FLAG_DEBUG);
  
  /**
   * The log category is the interface to be used. All logging information
   * is sent to a log category via one of the info, warn, error, debug 
   * methods which accept any number of arguments of any type (or 
   * their *f variants which use sprintf).
   *
   * Basic example:
   * <code>
   *   $cat= Logger::getInstance()->getCategory();
   *   $cat->addAppender(new ConsoleAppender());
   *
   *   // ...
   *   $cat->info('Starting work at', Date::now());
   *
   *   // ...
   *   $cat->debugf('Processing %d rows took %.3f seconds', $rows, $delta);
   *
   *   try {
   *     // ...
   *   } catch (SocketException $e) {
   *     $cat->warn('Caught', $e);
   *   }
   * </code>
   *
   * @test     xp://net.xp_framework.unittest.logging.LogCategoryTest
   * @purpose  Base class
   */
  class LogCategory extends lang::Object {
    public 
      $_appenders= array(),
      $_indicators= array(
        LOGGER_FLAG_INFO        => 'info',
        LOGGER_FLAG_WARN        => 'warn',
        LOGGER_FLAG_ERROR       => 'error',
        LOGGER_FLAG_DEBUG       => 'debug'
      );
      
    public
      $flags,
      $identifier,
      $dateformat,
      $format;

    /**
     * Constructor
     *
     * @param   string identifier
     * @param   string format 
     * @param   string dateformat
     * @param   int flags
     */
    public function __construct($identifier, $format, $dateformat, $flags= LOGGER_FLAG_ALL) {
      $this->identifier= $identifier;
      $this->format= $format;
      $this->dateformat= $dateformat;
      $this->flags= $flags;
      $this->_appenders= array();
      
    }

    /**
     * Sets the flags (what should be logged). Note that you also
     * need to add an appender for a category you want to log.
     *
     * @param   int flags bitfield with flags (LOGGER_FLAG_*)
     */
    public function setFlags($flags) {
      $this->flags= $flags;
    }
    
    /**
     * Gets flags
     *
     * @return  int flags
     */
    public function getFlags() {
      return $this->flags;
    }
    
    /**
     * Private helper function
     *
     */
    public function callAppenders() {
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
            array($this->_appenders[$appflag][$idx], 'append'),
            $args
          );
        }
      }
    }

    /**
     * Retrieves whether this log category has appenders
     *
     * @return  bool
     */
    public function hasAppenders() {
      return !empty($this->_appenders);
    }
    
    /**
     * Finalize
     *
     */
    public function finalize() {
      foreach ($this->_appenders as $flags => $appenders) {
        foreach (array_keys($appenders) as $idx) {
          $appenders[$idx]->finalize();
        }
      }
    }
    
    /**
     * Adds an appender for the given log categories. Use logical OR to 
     * combine the log types or use LOGGER_FLAG_ALL (default) to log all 
     * types.
     *
     * @param   util.log.LogAppender appender The appender object
     * @param   int flag default LOGGER_FLAG_ALL
     * @return  util.log.LogAppender the appender added
     */
    public function addAppender($appender, $flag= LOGGER_FLAG_ALL) {
      $this->_appenders[$flag][]= $appender;
      return $appender;
    }

    /**
     * Adds an appender for the given log categories and returns this
     * category - for use in a fluent interface way. Use logical OR to 
     * combine the log types or use LOGGER_FLAG_ALL (default) to log all 
     * types.
     *
     * @param   util.log.LogAppender appender The appender object
     * @param   int flag default LOGGER_FLAG_ALL
     * @return  util.log.LogCategory this category
     */
    public function withAppender($appender, $flag= LOGGER_FLAG_ALL) {
      $this->_appenders[$flag][]= $appender;
      return $this;
    }
    
    /**
     * Remove the specified appender from the given log categories. For usage
     * of log category flags, see addAppender().
     * 
     * @param   util.log.LogAppender appender
     * @param   int flag default LOGGER_FLAG_ALL
     */
    public function removeAppender($appender, $flag= LOGGER_FLAG_ALL) {
      foreach (array_keys($this->_appenders) as $f) {
        if (!($f & $flag)) continue;
        
        foreach (array_keys($this->_appenders[$f]) as $g) {
          if ($this->_appenders[$f][$g] === $appender) {
            unset ($this->_appenders[$f][$g]);
          }
        }
      }
    }

    /**
     * Appends a log of type info. Accepts any number of arguments of
     * any type. 
     *
     * The common rule (though up to each appender on how to realize it)
     * for serialization of an argument is:
     *
     * <ul>
     *   <li>For XP objects, the toString() method will be called
     *       to retrieve its representation</li>
     *   <li>Strings are printed directly</li>
     *   <li>Any other type is serialized using var_export()</li>
     * </ul>
     *
     * Note: This also applies to warn(), error() and debug().
     *
     * @param   mixed* args
     */
    public function info() {
      $args= func_get_args();
      array_unshift($args, LOGGER_FLAG_INFO);
      call_user_func_array(
        array($this, 'callAppenders'),
        $args
      );
    }

    /**
     * Appends a log of type info in sprintf-style. The first argument
     * to this method is the format string, containing sprintf-tokens,
     * the rest of the arguments are used as argument to sprintf. 
     *
     * Note: This also applies to warnf(), errorf() and debugf().
     *
     * @see     php://sprintf
     * @param   string format 
     * @param   mixed* args
     */
    public function infof() {
      $args= func_get_args();
      $this->callAppenders(
        LOGGER_FLAG_INFO,
        vsprintf($args[0], array_slice($args, 1))
      );
    }

    /**
     * Appends a log of type warn
     *
     * @param   mixed* args
     */
    public function warn() {
      $args= func_get_args();
      array_unshift($args, LOGGER_FLAG_WARN);
      call_user_func_array(
        array($this, 'callAppenders'),
        $args
      );
    }

    /**
     * Appends a log of type info in printf-style
     *
     * @param   string format 
     * @param   mixed* args
     */
    public function warnf() {
      $args= func_get_args();
      $this->callAppenders(
        LOGGER_FLAG_WARN,
        vsprintf($args[0], array_slice($args, 1))
      );
    }

    /**
     * Appends a log of type error
     *
     * @param   mixed* args
     */
    public function error() {
      $args= func_get_args();
      array_unshift($args, LOGGER_FLAG_ERROR);
      call_user_func_array(
        array($this, 'callAppenders'),
        $args
      );
    }

    /**
     * Appends a log of type info in printf-style
     *
     * @param   string format 
     * @param   mixed* args
     */
    public function errorf() {
      $args= func_get_args();
      $this->callAppenders(
        LOGGER_FLAG_ERROR,
        vsprintf($args[0], array_slice($args, 1))
      );
    }

    /**
     * Appends a log of type debug
     *
     * @param   mixed* args
     */
    public function debug() {
      $args= func_get_args();
      array_unshift($args, LOGGER_FLAG_DEBUG);
      call_user_func_array(
        array($this, 'callAppenders'),
        $args
      );
    }
 
    /**
     * Appends a log of type info in printf-style
     *
     * @param   string format format string
     * @param   mixed* args
     */
    public function debugf() {
      $args= func_get_args();
      $this->callAppenders(
        LOGGER_FLAG_DEBUG,
        vsprintf($args[0], array_slice($args, 1))
      );
    }
   
    /**
     * Appends a separator (a "line" consisting of 72 dashes)
     *
     */
    public function mark() {
      $this->callAppenders(
        LOGGER_FLAG_INFO, 
        str_repeat('-', 72)
      );
    }
  }
?>
