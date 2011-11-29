<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'xp.unittest.DefaultListener',
    'xp.unittest.VerboseListener',
    'xp.unittest.QuietListener'
  );

  /**
   * Listeners enumeration
   *
   */
  abstract class TestListeners extends Enum {
    public static $DEFAULT, $VERBOSE, $QUIET;
    
    static function __static() {
      self::$DEFAULT= newinstance(__CLASS__, array(0, 'DEFAULT'), '{
        static function __static() { }
        public function newInstance(OutputStreamWriter $out) {
          return new DefaultListener($out);
        }
      }');
      self::$VERBOSE= newinstance(__CLASS__, array(1, 'VERBOSE'), '{
        static function __static() { }
        public function newInstance(OutputStreamWriter $out) {
          return new VerboseListener($out);
        }
      }');
      self::$QUIET= newinstance(__CLASS__, array(2, 'QUIET'), '{
        static function __static() { }
        public function newInstance(OutputStreamWriter $out) {
          return new QuietListener();
        }
      }');
    }
    
    /**
     * Creates a new listener instance
     *
     * @param   io.streams.OutputStreamWriter out
     * @return  unittest.TestListener
     */
    public abstract function newInstance(OutputStreamWriter $out);
    
    /**
     * Returns all enum members
     *
     * @return  lang.Enum[]
     */
    public static function values() {
      return parent::membersOf(__CLASS__);
    }
  }
?>
