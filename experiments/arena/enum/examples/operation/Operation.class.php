<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('lang.Enum');
  
  /**
   * Operation enumeration
   * 
   * @purpose  Demo
   */
  abstract class Operation extends Enum {
    public static
      $plus,
      $minus,
      $times,
      $divided_by;
    
    static function __static() {
      self::$plus= newinstance(__CLASS__, array(0, 'plus'), '{
        static function __static() { }
        public function evaluate($x, $y) { return $x + $y; } 
      }');
      self::$minus= newinstance(__CLASS__, array(0, 'minus'), '{
        static function __static() { }
        public function evaluate($x, $y) { return $x - $y; } 
      }');
      self::$times= newinstance(__CLASS__, array(0, 'times'), '{
        static function __static() { }
        public function evaluate($x, $y) { return $x * $y; } 
      }');
      self::$divided_by= newinstance(__CLASS__, array(0, 'divided_by'), '{
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
