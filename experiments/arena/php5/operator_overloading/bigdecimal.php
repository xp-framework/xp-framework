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

    public static operator ++ (BigDecimal $d1) {
      return new BigDecimal(bcadd($d1->value, 1));
    }

    public static operator - (BigDecimal $d1, BigDecimal $d2) {
      return new BigDecimal(bcsub($d1->value, $d2->value));
    }

    public static operator -- (BigDecimal $d1) {
      return new BigDecimal(bcsub($d1->value, 1));
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

    public static operator __compare(BigDecimal $d1, BigDecimal $d2) {
      return bccomp($d1->value, $d2->value);
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
  printf("First big decimal:                %s\n", $d1->toString());
  printf("Second big decimal:               %s\n", $d2->toString());
  
  // Test comparions
  echo "------------------------------------------------------------\n";
  printf("Equal (first == second):          %s\n", var_export($d1 == $d2, 1));
  printf("Smaller (first < second):         %s\n", var_export($d1 < $d2, 1));
  printf("Larger (second > first):          %s\n", var_export($d1 > $d2, 1));
  printf("Comparison (second <=> first):    %s\n", var_export($d1 <=> $d2, 1));
  
  $add= $d1 + $d2;
  $sub= $d1 - $d2;
  $mul= $d1 * $d2;
  $div= $d1 / $d2;
  $mod= $d1 % $d2;

  // Test addition, subtraction, multiplication, division and modulus
  echo "------------------------------------------------------------\n";
  printf("Addition (first + second):        %s\n", $add->toString());
  printf("Subtraction (first - second):     %s\n", $sub->toString());
  printf("Multiplication (first * second):  %s\n", $mul->toString());
  printf("Division (first / second):        %s\n", $div->toString());
  printf("Modulus (first %% second):         %s\n", $mod->toString());

  // Test increment / decrement (postfix)
  echo "------------------------------------------------------------\n";
  $d1--;
  $d2++;
  printf("First big decimal (after \$d1--)   %s\n", $d1->toString());
  printf("Second big decimal (after \$d2++)  %s\n", $d2->toString());

  // Test increment / decrement (prefix)
  echo "------------------------------------------------------------\n";
  ++$d1;
  --$d2;
  printf("First big decimal (after ++\$d1)   %s\n", $d1->toString());
  printf("Second big decimal (after --\$d2)  %s\n", $d2->toString());
  // }}}
?>
