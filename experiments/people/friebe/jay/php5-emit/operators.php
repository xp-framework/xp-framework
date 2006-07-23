<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$ 
 */
  
  require('__xp__.php');
  
  // {{{ Original
  // class Complex {
  //   protected
  //     $imag = 0,
  //     $real = 0;
  // 
  //   public __construct($real, $imag) {
  //     $this->real= $real;
  //     $this->imag= $imag;
  //   }
  // 
  //   public static operator + (Complex $c1, Complex $c2) {
  //     return new Complex($c1->real + $c2->real, $c1->imag + $c2->imag);
  //   }
  // 
  //   public static operator - (Complex $c1, Complex $c2) {
  //     return new Complex($c1->real - $c2->real, $c1->imag - $c2->imag);
  //   }
  // 
  //   public static operator * (Complex $c1, Complex $c2) {
  //     return new Complex(
  //       ($c1->real * $c2->real) - ($c1->imag * $c2->imag), 
  //       ($c1->real * $c2->imag) + ($c1->imag * $c2->real) 
  //     );
  //   }
  // 
  //   public string toString() {
  //     return $this->real.' + '.$this->imag.'i';
  //   }
  // }
  // 
  // $c1= new Complex(2, 3);
  // $c2= new Complex(3, 4);
  // $a= $c1 + $c2;
  // $s= $c2 - $c1;
  // $m= $c1 * $c2;
  // 
  // printf("First complex number:            %s\n", $c1->toString());
  // printf("Second complex number:           %s\n", $c2->toString());
  // printf("Addition (first + second):       %s\n", $a->toString());
  // printf("Subtraction (second - first):    %s\n", $s->toString());
  // printf("Multiplication (first * second): %s\n", $m->toString());
  // }}}

  // {{{ Generated version
  class Complex {
    protected
      $imag = 0,
      $real = 0;

    public function __construct($real, $imag) {
      $this->real= $real;
      $this->imag= $imag;
    }

    public static function  __operatorplus(Complex $c1, Complex $c2) {
      return new Complex($c1->real + $c2->real, $c1->imag + $c2->imag);
    }

    public static function __operatorminus(Complex $c1, Complex $c2) {
      return new Complex($c1->real - $c2->real, $c1->imag - $c2->imag);
    }

    public static function __operatortimes(Complex $c1, Complex $c2) {
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

  $c1= new Complex(2, 3);
  $c2= new Complex(3, 4);
  $a= $c1->__operatorplus($c1, $c2);
  $s= $c2->__operatorminus($c2, $c1);
  $m= $c1->__operatortimes($c1, $c2);

  printf("First complex number:            %s\n", $c1->toString());
  printf("Second complex number:           %s\n", $c2->toString());
  printf("Addition (first + second):       %s\n", $a->toString());
  printf("Subtraction (second - first):    %s\n", $s->toString());
  printf("Multiplication (first * second): %s\n", $m->toString());
  // }}}
?>
