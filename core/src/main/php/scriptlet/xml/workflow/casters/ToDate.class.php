<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('scriptlet.xml.workflow.casters.ParamCaster', 'util.Date');

  /**
   * Casts given values to date objects
   *
   * @test      xp://net.xp_framework.unittest.scriptlet.workflow.ToDateTest
   * @purpose   Caster
   */
  class ToDate extends ParamCaster {
    protected static $parse= 'date_parse';
  
    static function __static() {
      $p= date_parse('Feb 31');
      if (0 === $p['warning_count']) {
        self::$parse= array(__CLASS__, 'parse');
      }
    }
    
    /**
     * Parse a date and verify number of days in month. Workaround for
     * broken PHP versions that consider 31 a valid day in February! 
     *
     * Note: This method is called by castValue() only if PHP is 
     * actually broken - a test in the static initializer determines
     * this!
     *
     * @see     php://date_parse
     * @param   string v
     * @return  var
     */
    protected static function parse($v) {
      static $dim= array(
        FALSE => array(-1, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31), 
        TRUE  => array(-1, 31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31),
      );

      $p= date_parse($v);
      if ($p['warning_count'] > 0 || $p['error_count']) return $v;
      
      // Date parser says it's OK, now verify # of days by looking
      // at days in month table (take leap years into consideration!)
      $l= $p['year'] % 400 == 0 || ($p['year'] > 1582 && $p['year'] % 100 == 0 ? FALSE : $p['year'] % 4 == 0);
      if ($p['day'] > $dim[$l][$p['month']]) $p['warning_count']++;
      return $p;
    }

    /**
     * Cast a given value
     *
     * @see     xp://scriptlet.xml.workflow.casters.ParamCaster
     * @param   array value
     * @return  array value
     */
    public function castValue($value) {
      $return= array();
      foreach ($value as $k => $v) {
        if ('' === $v) return 'empty';
        
        $pv= call_user_func(self::$parse, $v);
        if (
          !is_int($pv['year']) ||
          !is_int($pv['month']) ||
          !is_int($pv['day']) ||
          0 < $pv['warning_count'] ||
          0 < $pv['error_count']
        ) {
          return 'invalid';
        }
        
        try {
          $date= Date::create($pv['year'], $pv['month'], $pv['day'], $pv['hour'], $pv['minute'], $pv['second']);
        } catch (IllegalArgumentException $e) {
          return $e->getMessage();
        }

        $return[$k]= $date;
      }

      return $return;
    }
  }
?>
