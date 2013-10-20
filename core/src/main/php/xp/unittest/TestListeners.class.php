<?php namespace xp\unittest;

/**
 * Listeners enumeration
 */
abstract class TestListeners extends \lang\Enum {
  public static $DEFAULT, $VERBOSE, $QUIET;
  
  static function __static() {
    self::$DEFAULT= newinstance(__CLASS__, array(0, 'DEFAULT'), '{
      static function __static() { }
      public function getImplementation() {
        return \lang\XPClass::forName("xp.unittest.DefaultListener");
      }
    }');
    self::$VERBOSE= newinstance(__CLASS__, array(1, 'VERBOSE'), '{
      static function __static() { }
      public function getImplementation() {
        return \lang\XPClass::forName("xp.unittest.VerboseListener");
      }
    }');
    self::$QUIET= newinstance(__CLASS__, array(2, 'QUIET'), '{
      static function __static() { }
      public function getImplementation() {
        return \lang\XPClass::forName("xp.unittest.QuietListener");
      }
    }');
  }

  /**
   * Creates a new listener instance
   *
   * @return  lang.XPClass
   */
  public abstract function getImplementation();

  /**
   * Creates a new listener instance
   *
   * @param   io.streams.OutputStreamWriter out
   * @return  unittest.TestListener
   */
  public function newInstance(\io\streams\OutputStreamWriter $out) {
    return $this->getImplementation()->newInstance($out);
  }
}
