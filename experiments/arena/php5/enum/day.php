<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */

  enum Day {
    Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, Sunday;

    private static function isWorkDay(Day $day) {
      return $day->value < self::Saturday;
    }
    
    public static function workDays() {
      return array_filter(self::values(), array('self', 'isWorkDay'));
    }

    private static function isWeekend(Day $day) {
      return $day->value > self::Friday;
    }

    public static function weekend() {
      return array_filter(self::values(), array('self', 'isWeekend'));
    }
  }
  
  function nameOf(Enumeration $e) {
    return $e->name;
  }

  // {{{ main
  echo 'Workdays: ', implode(', ', array_map('nameOf', Day::workDays())), "\n";
  echo 'Weekend:  ', implode(', ', array_map('nameOf', Day::weekend())), "\n";
  // }}}
?>
