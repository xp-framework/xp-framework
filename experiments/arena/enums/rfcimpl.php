<?php
  require('lang.base.php');
  
  class Enum extends Object {
    var 
      $ordinal  = 0,
      $name     = '';

    function __construct($name) {
      $this->ordinal= constant($name);
      $this->name= $name;
    }
    
    function toString() {
      return $this->getClassName().'@'.$this->ordinal.' {'.$this->name.'}';
    }

    function &registry($enum, $value= NULL) {
      static $e= array();

      if (is_array($value)) {
        $e[$enum]= $value;
      } elseif (is_int($value)) {
        return $e[$enum][$value];
      }
      return $e[$enum];
    }
  }

  function colorOf($coin) {
    switch ($coin->ordinal) {
      case penny: return 'copper';
      case nickel: return 'nickel';
      case dime: case quarter: return 'silver';
    }
  }

  function nameOf($member) {
    return $member->name;
  }

  /// {{{ Coin
  // Generated from:
  //   enum Coin {
  //     penny(1), nickel(5), dime(10), quarter(25);
  // 
  //     var $value= 0;
  // 
  //     function __construct($name, $value) { 
  //       parent::__construct($name); 
  //       $this->value= $value; 
  //     }
  // 
  //     function value() { return $this->value; }
  //   }
  define('penny',   0, 0);
  define('nickel',  1, 0);
  define('dime',    2, 0);
  define('quarter', 3, 0);
  
  class Coin extends Enum {
    var $value= 0;
    
    function __static() {
      Enum::registry(__CLASS__, array(
        penny   => new Coin('penny', 1),
        nickel  => new Coin('nickel', 5),
        dime    => new Coin('dime', 10),
        quarter => new Coin('quarter', 25)
      ));
    }

    function __construct($name, $value) { 
      parent::__construct($name); 
      $this->value= $value; 
    }

    function value() { return $this->value; }

    function size() { return 4; }

    function values() { return Enum::registry(__CLASS__); }

    function valueOf($ordinal) { return Enum::registry(__CLASS__, $ordinal); }

  } Coin::__static();
  // }}}

  // {{{ Day
  // Generated from:
  //   enum Day { Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, Sunday }
  define('Monday',      0, 0);
  define('Tuesday',     1, 0);
  define('Wednesday',   2, 0);
  define('Thursday',    3, 0);
  define('Friday',      4, 0);
  define('Saturday',    5, 0);
  define('Sunday',      6, 0);

  class Day extends Enum {

    function __static() {
      Enum::registry(__CLASS__, array(
        Monday      => new Day('Monday'),
        Tuesday     => new Day('Tuesday'),
        Wednesday   => new Day('Wednesday'),
        Thursday    => new Day('Thursday'),
        Friday      => new Day('Friday'),
        Saturday    => new Day('Saturday'),
        Sunday      => new Day('Sunday')
      ));
    }

    function size() { return 7; }

    function values() { return Enum::registry(__CLASS__); }

    function valueOf($ordinal) { return Enum::registry(__CLASS__, $ordinal); }
  } Day::__static();
  // }}}

  // {{{ Operation
  // Generated from:
  //   enum Operation {
  //     plus { 
  //       function evaluate($x, $y) { return $x + $y; } 
  //     }
  //     minus { 
  //       function evaluate($x, $y) { return $x - $y; } 
  //     }
  //     times { 
  //       function evaluate($x, $y) { return $x * $y; } 
  //     }
  //     divided_by { 
  //       function evaluate($x, $y) { return $x / $y; } 
  //     }
  //
  //     function evaluate($x, $y);
  //   }
  define('plus',        0, 0);
  define('minus',       1, 0);
  define('times',       2, 0);
  define('divided_by',  3, 0);

  class Operation extends Enum {

    function __static() {
      Enum::registry(__CLASS__, array(
        plus        => new Operation('plus'),
        minus       => new Operation('minus'),
        times       => new Operation('times'),
        divided_by  => new Operation('divided_by')
      ));
    }
    
    function evaluate($x, $y) {
      return call_user_func(array(&$this, '_evaluate'.$this->name), $x, $y);
    }
    
    function _evaluateplus($x, $y) { return $x + $y; }
    function _evaluateminus($x, $y) { return $x - $y; }
    function _evaluatetimes($x, $y) { return $x * $y; }
    function _evaluatedivided_by($x, $y) { return $x / $y; }

    function size() { return 4; }

    function values() { return Enum::registry(__CLASS__); }

    function valueOf($ordinal) { return Enum::registry(__CLASS__, $ordinal); }
  } Operation::__static();
  // }}}

  // {{{ main
  echo 'Coin: ', Coin::size(), " values:\n";
  foreach (Coin::values() as $coin) {
    echo $coin->name, ': ', $coin->value(), '¢ (', colorOf($coin), ")\n";
  }
  echo "\n";

  echo "Weekdays:\n";
  foreach (range(Monday, Friday) as $day) {
    echo $day, ': ', nameOf(Day::valueOf($day)), "\n";
  }
  echo "\n";
  
  echo "Operations:\n";  
  $x= 2;
  $y= 4;
  foreach (Operation::values() as $op) {
    printf("%d %s %s = %.1f\n", $x, $op->name, $y, $op->evaluate($x, $y));
  }
  // }}}
?>
