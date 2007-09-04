<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.Enum');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class Aggregation extends Enum {
    public static
      $AVERAGE,
      $MEDIAN,
      $MAXIMUM,
      $MINIMUM;
    
    static function __static() {
      self::$AVERAGE= newinstance(__CLASS__, array(0, 'AVERAGE'), '{
        static function __static() {}
        public function calculate(array $values) {
          return array_sum($values) / sizeof ($values);
        }
      }'
      );
      
      self::$MEDIAN= newinstance(__CLASS__, array(0, 'MEDIAN'), '{
        static function __static() {}
        public function calculate(array $values) {
          sort($values);
          if (sizeof($values) % 2 != 0) return $values[((sizeof($values)+ 1) / 2)- 1];
          return 0.5 * (
            $values[intval(sizeof($values) / 2)- 1] +
            $values[intval(sizeof($values) / 2)]
          );            
        }
      }'
      );
      
      self::$MAXIMUM= newinstance(__CLASS__, array(0, 'MAXIMUM'), '{
        static function __static() {}
        public function calculate(array $values) {
          $max= $values[0];
          foreach ($values as $v) { $max= max($max, $v); }
          return $max;
        }
      }'
      );

      self::$MINIMUM= newinstance(__CLASS__, array(0, 'MINIMUM'), '{
        static function __static() {}
        public function calculate(array $values) {
          $min= $values[0];
          foreach ($values as $v) { $min= min($min, $v); }
          return $min;
        }
      }'
      );
    }
    
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
