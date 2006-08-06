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
     * @access  public
     */
    #[@test]
    function concatOperator() {
      $this->assertSourcecodeEquals(
        preg_replace('/\n\s*/', '', 'class String extends xp·lang·Object{
          protected $buffer;

          function __operatorconcat($a, $b) {
            return new String($a->buffer.$b); 
          }

          public static function main(){
            echo String::__operatorconcat(new String(\'Hello\'), \'!\'); 
          }
        };'),
        $this->emit('class String { 
          protected $buffer;

          public static operator . (String $a, string $b) {
            return new String($a->buffer.$b);
          }

          public static void main(){
            echo new String("Hello")."!";
          }
        }')
      );
    }

    /**
     * Tests binary operators "+", "-", "*" and "/"
     *
     * @access  public
     */
    #[@test]
    function binaryOperators() {
      $this->assertSourcecodeEquals(
        preg_replace('/\n\s*/', '', 'class Integer extends xp·lang·Object{
          protected $value;

          function __operatorplus($a, $b) {
            return new Integer($a->value+$b->value); 
          }

          function __operatorminus($a, $b) {
            return new Integer($a->value-$b->value); 
          }

          function __operatortimes($a, $b) {
            return new Integer($a->value*$b->value); 
          }

          function __operatordivide($a, $b) {
            return new Integer($a->value/$b->value); 
          }
          
          public static function main(){
            echo Integer::__operatorplus(new Integer(1), new Integer(2)); 
          }
        };'),
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
