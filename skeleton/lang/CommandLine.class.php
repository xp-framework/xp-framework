<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.Enum');

  /**
   * Handles command line quoting
   *
   * Composing a command line
   * ------------------------
   * Handled by the <tt>compose</tt> method.
   *
   * For Windows
   * ~~~~~~~~~~~
   * - Surround string with double quotes
   * - Replace double quotes inside with triple quotes (""")
   * 
   * For Un*x
   * ~~~~~~~~
   * - Surround string with single quotes
   * - As single quotes may not appear inside a string enclosed in
   *   single quotes, split it and add a single quoted escaped by
   *   a backslash. So: he said: 'Hello' will become the following:
   *   'he said: '\''Hello'\''
   *
   * @see      xp://lang.Process
   * @test     xp://net.xp_framework.unittest.core.CommandLineTest
   */
  abstract class CommandLine extends Enum {
    public static $WINDOWS, $UNIX;
    
    static function __static() {
      self::$WINDOWS= newinstance(__CLASS__, array(0, 'WINDOWS'), '{
        static function __static() { }
        public function parse($cmd) {
          $parts= array();
          $o= 0;
          while (FALSE !== ($p= strcspn($cmd, " ", $o))) {
            $option= substr($cmd, $o, $p);
            if (1 === substr_count($option, \'"\')) {
              $l= $o+ $p;
              $qp= strpos($cmd, \'"\', $l)+ 1;
              $option.= substr($cmd, $l, $qp- $l);
              $o= $qp+ 1;
            } else {
              $o+= $p+ 1;
            }
            if (\'"\' === $option{0}) $option= substr($option, 1, -1);
            $parts[]= $option;
          }
          return $parts;
        }
        
        protected static function quote($arg) {
          if (!strstr($arg, " ") && !strstr($arg, \'"\')) return $arg;
          return \'"\'.str_replace(\'"\', \'"""\', $arg).\'"\';
        }
        
        public function compose($command, $arguments= array()) {
          $cmd= self::quote($command);
          foreach ($arguments as $arg) {
            $cmd.= " ".self::quote($arg);
          }
          return $cmd;
        
        }
      }');
      self::$UNIX= newinstance(__CLASS__, array(1, 'UNIX'), '{
        static function __static() { }
        public function parse($cmd) {
          $parts= array();
          $o= 0;
          while (FALSE !== ($p= strcspn($cmd, " ", $o))) {
            $option= substr($cmd, $o, $p);
            if (1 === substr_count($option, \'"\')) {
              $l= $o+ $p;
              $qp= strpos($cmd, \'"\', $l)+ 1;
              $option.= substr($cmd, $l, $qp- $l);
              $o= $qp+ 1;
            } else if (1 === substr_count($option, "\'")) {
              $l= $o+ $p;
              $qp= strpos($cmd, "\'", $l)+ 1;
              $option.= substr($cmd, $l, $qp- $l);
              $o= $qp+ 1;
            } else {
              $o+= $p+ 1;
            }
            if (\'"\' === $option{0} || "\'" === $option{0}) $option= substr($option, 1, -1);
            $parts[]= $option;
          }
          return $parts;
        }
        
        protected static function quote($arg) {
          if (!strstr($arg, " ") && !strstr($arg, "\'")) return $arg;
          return "\'".str_replace("\'", "\'\\\'\'", $arg)."\'";
        }
        
        public function compose($command, $arguments= array()) {
          $cmd= self::quote($command);
          foreach ($arguments as $arg) {
            $cmd.= " ".self::quote($arg);
          }
          return $cmd;
        
        }
      }');
    }

    /**
     * Returns all enum members
     *
     * @return  lang.Enum[]
     */
    public static function values() {
      return parent::membersOf(__CLASS__);
    }
    
    /**
     * Returns the command line implementation for the given operating 
     * system.
     *
     * @param   string os operating system name, e.g. PHP_OS
     * @return  lang.CommandLine
     */
    public static function forName($os) {
      if (0 === strncasecmp($os, 'Win', 3)) {
        return self::$WINDOWS;
      } else {
        return self::$UNIX;
      }
    }

    /**
     * Parse command line
     *
     * @param   string cmd
     * @return  string[] parts
     */
    public abstract function parse($cmd);
    
    /**
     * Build command line from a command and - optionally - arguments
     *
     * @param   string command
     * @param   string[] arguments default []
     * @return  string
     */
    public abstract function compose($command, $arguments= array());
  }
?>
