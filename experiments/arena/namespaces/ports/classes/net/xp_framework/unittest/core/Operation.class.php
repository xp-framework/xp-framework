<?php
/* This class is part of the XP framework
 *
 * $Id: Operation.class.php 10908 2007-07-31 12:06:15Z friebe $
 */

  namespace net::xp_framework::unittest::core;

  ::uses('lang.Enum');
  
  /**
   * Operation enumeration
   * 
   * @purpose  Demo
   */
  abstract class Operation extends lang::Enum {
    public static
      $plus,
      $minus,
      $times,
      $divided_by;
    
    static function __static() {
      self::$plus= ::newinstance(__CLASS__, array(0, 'plus'), '{
        static function __static() { }
        public function evaluate($x, $y) { return $x + $y; } 
      }');
      self::$minus= ::newinstance(__CLASS__, array(1, 'minus'), '{
        static function __static() { }
        public function evaluate($x, $y) { return $x - $y; } 
      }');
      self::$times= ::newinstance(__CLASS__, array(2, 'times'), '{
        static function __static() { }
        public function evaluate($x, $y) { return $x * $y; } 
      }');
      self::$divided_by= ::newinstance(__CLASS__, array(3, 'divided_by'), '{
        static function __static() { }
        public function evaluate($x, $y) { return $x / $y; } 
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
     * Evaluates this operation
     *
     * @param   int x
     * @param   int y
     * @return  float
     */
    public abstract function evaluate($x, $y);
  }
?>
