<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */

  // {{{ class Complex 
  class Complex {
    protected
      $imag = 0,
      $real = 0;
    
    public function __construct($real, $imag) {
      $this->real= $real;
      $this->imag= $imag;
    }
    
    public static operator + (Complex $c1, Complex $c2) {
      return new Complex($c1->real + $c2->real, $c1->imag + $c2->imag);
    }

    public static operator - (Complex $c1, Complex $c2) {
      return new Complex($c1->real - $c2->real, $c1->imag - $c2->imag);
    }

    public static operator * (Complex $c1, Complex $c2) {
      return new Complex(
        ($c1->real * $c2->real) - ($c1->imag * $c2->imag), 
        ($c1->real * $c2->imag) + ($c1->imag * $c2->real) 
      );
    }
    
    public function toString() {
      return $this->real.' + '.$this->imag.'i';
    }
  }
  // }}}
  
  // {{{ main
  Reflection::export(new ReflectionClass('Complex'));
  
  // Create some complex numbers
  $c1= new Complex(2, 3);
  $c2= new Complex(3, 4);
  $a= $c1 + $c2;
  $s= $c2 - $c1;
  $m= $c1 * $c2;

  // Test addition and subtraction
  printf("First complex number:            %s\n", $c1->toString());
  printf("Second complex number:           %s\n", $c2->toString());
  printf("Addition (first + second):       %s\n", $a->toString());
  printf("Subtraction (second - first):    %s\n", $s->toString());
  printf("Multiplication (first * second): %s\n", $m->toString());
  // }}}
?>
