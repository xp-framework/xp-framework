<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */

  // {{{ class BigDecimal 
  class BigDecimal {
    protected
      $value    = '';
    
    public function __construct($value) {
      $this->value= $value;
    }
    
    public static operator + (BigDecimal $d1, BigDecimal $d2) {
      return new BigDecimal(bcadd($d1->value, $d2->value));
    }

    public static operator - (BigDecimal $d1, BigDecimal $d2) {
      return new BigDecimal(bcsub($d1->value, $d2->value));
    }

    public static operator * (BigDecimal $d1, BigDecimal $d2) {
      return new BigDecimal(bcmul($d1->value, $d2->value));
    }

    public static operator / (BigDecimal $d1, BigDecimal $d2) {
      return new BigDecimal(bcdiv($d1->value, $d2->value));
    }

    public static operator % (BigDecimal $d1, BigDecimal $d2) {
      return new BigDecimal(bcmod($d1->value, $d2->value));
    }
    
    public function toString() {
      return $this->value;
    }
  }
  // }}}
  
  // {{{ main
  Reflection::export(new ReflectionClass('BigDecimal'));
  
  // Create some complex numbers
  $d1= new BigDecimal('92233720368547758075');
  $d2= new BigDecimal('192233726199758075');
  $add= $d1 + $d2;
  $sub= $d1 - $d2;
  $mul= $d1 * $d2;
  $div= $d1 / $d2;
  $mod= $d1 % $d2;

  // Test addition and subtraction
  printf("First big decimal:                %s\n", $d1->toString());
  printf("Second big decimal:               %s\n", $d2->toString());
  printf("Addition (first + second):        %s\n", $add->toString());
  printf("Subtraction (first - second):     %s\n", $sub->toString());
  printf("Multiplication (first * second):  %s\n", $mul->toString());
  printf("Division (first / second):        %s\n", $div->toString());
  printf("Modulus (first %% second):         %s\n", $mod->toString());
  // }}}
?>
