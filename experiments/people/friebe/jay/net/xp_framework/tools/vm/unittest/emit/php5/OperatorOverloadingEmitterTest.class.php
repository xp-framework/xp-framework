<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('net.xp_framework.tools.vm.unittest.emit.php5.AbstractEmitterTest');

  /**
   * Tests PHP5 emitter
   *
   * @purpose  Unit Test
   */
  class OperatorOverloadingEmitterTest extends AbstractEmitterTest {

    /**
     * Tests concat operator (".")
     *
     */
    #[@test]
    public function concatOperator() {
      $this->assertSourcecodeEquals(
        'class main·String extends lang·Object{
          protected $buffer= NULL;

          function __operatorconcat($a, $b) {
            return new main·String($a->buffer.$b); 
          }

          /**
           * @return  void
           */
          public static function main(){
            echo main·String::__operatorconcat(new main·String(\'Hello\'), \'!\'); 
          }
        };',
        $this->emit('class String { 
          protected $buffer;

          public static operator ~ (String $a, string $b) {
            return new String($a->buffer~$b);
          }

          public static void main(){
            echo new String("Hello")~"!";
          }
        }')
      );
    }

    /**
     * Tests binary operators "+", "-", "*" and "/"
     *
     */
    #[@test]
    public function binaryOperators() {
      $this->assertSourcecodeEquals(
        'class main·Integer extends lang·Object{
          protected $value= NULL;

          function __operatorplus($a, $b) {
            return new main·Integer($a->value+$b->value); 
          }

          function __operatorminus($a, $b) {
            return new main·Integer($a->value-$b->value); 
          }

          function __operatortimes($a, $b) {
            return new main·Integer($a->value*$b->value); 
          }

          function __operatordivide($a, $b) {
            return new main·Integer($a->value/$b->value); 
          }
          
          /**
           * @return  void
           */
          public static function main(){
            echo main·Integer::__operatorplus(new main·Integer(1), new main·Integer(2)); 
          }
        };',
        $this->emit('class Integer {
          protected $value;

          public static operator + (Integer $a, Integer $b) { 
            return new Integer($a->value + $b->value); 
          }

          public static operator - (Integer $a, Integer $b) { 
            return new Integer($a->value - $b->value); 
          }

          public static operator * (Integer $a, Integer $b) { 
            return new Integer($a->value * $b->value); 
          }

          public static operator / (Integer $a, Integer $b) { 
            return new Integer($a->value / $b->value); 
          }
          
          public static void main() {
            echo new Integer(1) + new Integer(2);
          }
        }')
      );
    }
  }
?>
