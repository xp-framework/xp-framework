<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.log.LogLevel', 
    'util.log.Appender', 
    'util.log.LoggingEvent', 
    'util.log.layout.DefaultLayout'
  );

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
   */
  class LogCategory extends Object {
    protected static $DEFAULT_LAYOUT= NULL;
    protected $_appenders= array();

    public $flags= 0;
    public $identifier= '';
      
    static function __static() {
      self::$DEFAULT_LAYOUT= new DefaultLayout();
    }

    /**
     * Constructor
     *
     * @param   string identifier
     * @param   int flags (defaults to all)
     */
    public function __construct($identifier, $flags= LogLevel::ALL) {
      $this->flags= $flags;
      $this->identifier= $identifier;
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
     * Calls all appenders
     *
     * @param   int level
     * @param   var[] args
     */
    protected function callAppenders($level, $args) {
      if (!($this->flags & $level)) return;
      $event= new LoggingEvent($this, time(), getmypid(), $level, $args);
      foreach ($this->_appenders as $appflag => $appenders) {
        if (!($level & $appflag)) continue;
        foreach ($appenders as $appender) {
          $appender->append($event);
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
        foreach ($this->_appenders[$appflag] as $appender) {
          $appender->finalize();
        }
      }
    }
    
    /**
     * Adds an appender for the given log categories. Use logical OR to 
     * combine the log types or use LogLevel::ALL (default) to log all 
     * types.
     *
     * @param   util.log.Appender appender The appender object
     * @param   int flag default LogLevel::ALL
     * @return  util.log.Appender the appender added
     */
    public function addAppender($appender, $flag= LogLevel::ALL) {
      if ($appender instanceof Appender) {
        // NOOP
      } else if ($appender instanceof LogAppender) {
        $appender= XPClass::forName('util.log.LogAppenderAdapter')->newInstance($appender);
      } else {
        throw new IllegalArgumentException('Expected an util.log.Appender, have '.xp::typeOf($appender));
      }
      
      $appender->getLayout() || $appender->setLayout(self::$DEFAULT_LAYOUT);
      $this->_appenders[$flag][$appender->hashCode()]= $appender;
      return $appender;
    }

    /**
     * Adds an appender for the given log categories and returns this
     * category - for use in a fluent interface way. Use logical OR to 
     * combine the log types or use LogLevel::ALL (default) to log all 
     * types.
     *
     * @param   util.log.Appender appender The appender object
     * @param   int flag default LogLevel::ALL
     * @return  util.log.LogCategory this category
     */
    public function withAppender(Appender $appender, $flag= LogLevel::ALL) {
      $appender->getLayout() || $appender->setLayout(self::$DEFAULT_LAYOUT);
      $this->_appenders[$flag][$appender->hashCode()]= $appender;
      return $this;
    }
    
    /**
     * Remove the specified appender from the given log categories. For usage
     * of log category flags, see addAppender().
     * 
     * @param   util.log.Appender appender
     * @param   int flag default LogLevel::ALL
     */
    public function removeAppender(Appender $appender, $flag= LogLevel::ALL) {
      foreach ($this->_appenders as $f => $appenders) {
        if (!($f & $flag)) continue;
        unset($this->_appenders[$f][$appender->hashCode()]);
        
        // Last appender for this flag removed - remove flag alltogether
        if (0 === sizeof($this->_appenders[$f])) {
          unset($this->_appenders[$f]);
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
      $this->callAppenders(LogLevel::INFO, array(vsprintf($args[0], array_slice($args, 1))));
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
      $this->callAppenders(LogLevel::WARN, array(vsprintf($args[0], array_slice($args, 1))));
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
      $this->callAppenders(LogLevel::ERROR, array(vsprintf($args[0], array_slice($args, 1))));
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
      $this->callAppenders(LogLevel::DEBUG, array(vsprintf($args[0], array_slice($args, 1))));
    }
   
    /**
     * Appends a separator (a "line" consisting of 72 dashes)
     *
     */
    public function mark() {
      $this->callAppenders(LogLevel::INFO, array(str_repeat('-', 72)));
    }
    
    /**
     * Helper method for equals
     *
     * @param   array c1
     * @param   array c2
     * @return  bool
     */
    protected static function appendersAreEqual($c1, $c2) {
      if (sizeof($c1) != sizeof($c2)) return FALSE;
      foreach ($c1 as $f => $appenders) {
        if (!isset($c2[$f])) return FALSE;
        if (sizeof($appenders) != sizeof($c2[$f])) return FALSE;
        foreach ($appenders as $hash => $appender) {
          if (!isset($c2[$f][$hash])) return FALSE;
        }
      }
      return TRUE;
    }

    /**
     * Returns whether another object is equal to this
     *
     * @param   lang.Generic cmp
     * @return  bool
     */
    public function equals($cmp) {
      return (
        $cmp instanceof self &&
        $cmp->identifier === $this->identifier &&
        $cmp->flags === $this->flags &&
        self::appendersAreEqual($cmp->_appenders, $this->_appenders)
      );
    }

    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      $s= $this->getClassName().'(name='.$this->identifier.' flags='.$this->flags.")@{\n";
      foreach ($this->_appenders as $flags => $appenders) {
        $s.= '  '.$flags.": [\n";
        foreach ($appenders as $appender) {
          $s.= '  - '.$appender->toString()."\n"; 
        }
        $s.= "  ]\n";
      }
      return $s.'}';
    }
  }
?>
