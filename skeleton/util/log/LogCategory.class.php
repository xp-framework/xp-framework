<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.log.LogLevel');

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
  class LogCategory extends Object {
    protected static
      $INDICATORS = array(
        LogLevel::INFO  => 'info',
        LogLevel::WARN  => 'warn',
        LogLevel::ERROR => 'error',
        LogLevel::DEBUG => 'debug'
      );
      
    public
      $_appenders= array();

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
    public function __construct($identifier, $format, $dateformat, $flags= LogLevel::ALL) {
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
     * @param   int flags bitfield with flags (LogLevel::*)
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
    public function callAppenders($flag, $args) {
      if (!($this->flags & $flag)) return;
      
      array_unshift($args, sprintf(
        $this->format,
        date($this->dateformat),
        $this->identifier,
        self::$INDICATORS[$flag]
      ));
      
      // Remember currently active flag temporarily
      $this->currFlag= $flag;

      // Support new style log tokens
      $args[0]= preg_replace_callback('#\{[A-Z_]+\}#', array($this, 'tokenCallback'), $args[0]);
      unset($this->currFlag);

      foreach ($this->_appenders as $appflag => $appenders) {
        if (!($flag & $appflag)) continue;
        foreach ($appenders as $appender) {
          call_user_func_array(array($appender, 'append'), $args);
        }
      }
    }

    public function tokenCallback($token) {
      switch ($token[0]) {
        case '{DATE}': return date($this->dateformat);
        case '{PID}': return $this->identifier;
        case '{LEVEL}': return self::$INDICATORS[$this->currFlag];
        case '{CLASS}': {
          $bt= debug_backtrace();
          // Try to find entry scope to LogCategory class; this is 4 or 5 scopes apart
          // depending on whether it was eg. debug() or debugf()...
          for ($i= 4; empty($bt[$i]['class']) || 'LogCategory' == $bt[$i]['class']; $i++) {}
          return xp::nameOf($bt[$i]['class']);
        }
      }

      return $token[0];
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
        foreach ($appenders as $appender) {
          $appenders[$idx]->finalize();
        }
      }
    }
    
    /**
     * Adds an appender for the given log categories. Use logical OR to 
     * combine the log types or use LogLevel::ALL (default) to log all 
     * types.
     *
     * @param   util.log.LogAppender appender The appender object
     * @param   int flag default LogLevel::ALL
     * @return  util.log.LogAppender the appender added
     */
    public function addAppender($appender, $flag= LogLevel::ALL) {
      $this->_appenders[$flag][]= $appender;
      return $appender;
    }

    /**
     * Adds an appender for the given log categories and returns this
     * category - for use in a fluent interface way. Use logical OR to 
     * combine the log types or use LogLevel::ALL (default) to log all 
     * types.
     *
     * @param   util.log.LogAppender appender The appender object
     * @param   int flag default LogLevel::ALL
     * @return  util.log.LogCategory this category
     */
    public function withAppender($appender, $flag= LogLevel::ALL) {
      $this->_appenders[$flag][]= $appender;
      return $this;
    }
    
    /**
     * Remove the specified appender from the given log categories. For usage
     * of log category flags, see addAppender().
     * 
     * @param   util.log.LogAppender appender
     * @param   int flag default LogLevel::ALL
     */
    public function removeAppender($appender, $flag= LogLevel::ALL) {
      foreach ($this->_appenders as $f => $appenders) {
        if (!($f & $flag)) continue;
        
        foreach ($appenders as $idx => $apndr) {
          if ($apndr === $appender) {
            unset($this->_appenders[$f][$idx]);

            // Remove flag line, if last appender had been removed
            if (1 == sizeof($appenders)) {
              unset($this->_appenders[$f]);
            }
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
      $this->callAppenders(LogLevel::INFO, $args);
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
      $this->info(vsprintf($args[0], array_slice($args, 1)));
    }

    /**
     * Appends a log of type warn
     *
     * @param   mixed* args
     */
    public function warn() {
      $args= func_get_args();
      $this->callAppenders(LogLevel::WARN, $args);
    }

    /**
     * Appends a log of type info in printf-style
     *
     * @param   string format 
     * @param   mixed* args
     */
    public function warnf() {
      $args= func_get_args();
      $this->warn(vsprintf($args[0], array_slice($args, 1)));
    }

    /**
     * Appends a log of type error
     *
     * @param   mixed* args
     */
    public function error() {
      $args= func_get_args();
      $this->callAppenders(LogLevel::ERROR, $args);
    }

    /**
     * Appends a log of type info in printf-style
     *
     * @param   string format 
     * @param   mixed* args
     */
    public function errorf() {
      $args= func_get_args();
      $this->error(vsprintf($args[0], array_slice($args, 1)));
    }

    /**
     * Appends a log of type debug
     *
     * @param   mixed* args
     */
    public function debug() {
      $args= func_get_args();
      $this->callAppenders(LogLevel::DEBUG, $args);
    }
 
    /**
     * Appends a log of type info in printf-style
     *
     * @param   string format format string
     * @param   mixed* args
     */
    public function debugf() {
      $args= func_get_args();
      $this->debug(vsprintf($args[0], array_slice($args, 1)));return;
    }
   
    /**
     * Appends a separator (a "line" consisting of 72 dashes)
     *
     */
    public function mark() {
      $this->info(str_repeat('-', 72));
    }
  }
?>
