<?php
/* This class is part of the XP framework
 *
 * $Id: Quoting.class.php 11510 2009-09-15 15:55:41Z friebe $ 
 */
 
  $package= 'text.csv';
  
  uses('lang.Enum', 'text.csv.QuotingStrategy');

  /**
   * CSV quoting strategy enumeration. The following strategies are
   * available:
   * <ul>
   *   <li>DEFAULT - quotes according to the RFC</li>
   *   <li>EMPTY   - like default strategy but always quotes empty values</li>
   *   <li>ALWAYS  - quotes regardless of value's content</li>
   * </ul>
   *
   * @test     xp://net.xp_framework.unittest.text.csv.QuotingTest
   * @see      xp://text.csv.QuotingStrategy
   * @see      xp://text.csv.CsvFormat#setQuoting
   */
  abstract class text·csv·Quoting extends Enum implements QuotingStrategy {
    public static $DEFAULT= NULL;
    public static $EMPTY= NULL;
    public static $ALWAYS= NULL;
    
    static function __static() {
      self::$DEFAULT= newinstance(__CLASS__, array(0, 'DEFAULT'), '{
        static function __static() { }
        public function necessary($value, $delimiter, $quote) {
          return strcspn($value, $delimiter.$quote."\r\n") < strlen($value);
        }
      }');
      self::$EMPTY= newinstance(__CLASS__, array(1, 'EMPTY'), '{
        static function __static() { }
        public function necessary($value, $delimiter, $quote) {
          return "" === $value || strcspn($value, $delimiter.$quote."\r\n") < strlen($value);
        }
      }');
      self::$ALWAYS= newinstance(__CLASS__, array(2, 'ALWAYS'), '{
        static function __static() { }
        public function necessary($value, $delimiter, $quote) {
          return TRUE;
        }
      }');
    }
  }
?>
