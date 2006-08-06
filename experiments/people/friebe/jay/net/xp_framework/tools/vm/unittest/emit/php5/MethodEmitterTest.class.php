<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('net.xp_framework.tools.vm.unittest.emit.php5.AbstractEmitterTest');

  /**
   * Tests PHP5 emitter
   *
   * @see      xp://net.xp_framework.tools.vm.unittest.emit.php5.MethodOverloadingEmitterTest
   * @purpose  Unit Test
   */
  class MethodEmitterTest extends AbstractEmitterTest {

    /**
     * Tests the simplest case
     *
     * @access  public
     */
    #[@test]
    function methodWithoutArguments() {
      $this->assertSourcecodeEquals(
        preg_replace('/\n\s*/', '', 'class Test extends xp·lang·Object{
          public function sayHello(){
            echo \'Hello\'; 
          }
        };'),
        $this->emit('class Test {
          public void sayHello() {
            echo "Hello";
          }
        }')
      );
    }

    /**
     * Tests a method with one string argument 
     *
     * @access  public
     */
    #[@test]
    function methodWithOneStringArgument() {
      $this->assertSourcecodeEquals(
        preg_replace('/\n\s*/', '', 'class Test extends xp·lang·Object{
          public function sayHello($name){
            echo \'Hello\', $name; 
          }
        };'),
        $this->emit('class Test {
          public void sayHello(string $name) {
            echo "Hello", $name;
          }
        }')
      );
    }

    /**
     * Tests a method with one string[] argument 
     *
     * @access  public
     */
    #[@test]
    function methodWithOneStringArrayArgument() {
      $this->assertSourcecodeEquals(
        preg_replace('/\n\s*/', '', 'class Test extends xp·lang·Object{
          public function sayHello($names){
            foreach ($names as $name) {
              echo \'Hello\', $name, \' \'; 
            }; 
          }
        };'),
        $this->emit('class Test {
          public void sayHello(string[] $names) {
            foreach ($names as $name) {
              echo "Hello", $name, " ";
            }
          }
        }')
      );
    }

    /**
     * Tests a method call
     *
     * @access  public
     */
    #[@test]
    function methodCall() {
      $this->assertSourcecodeEquals(
        preg_replace('/\n\s*/', '', 'class Test extends xp·lang·Object{
          public static function sayHello($names){
            foreach ($names as $name) {
              echo \'Hello\', $name, \' \'; 
            }; 
          }
 
          public static function main(){
            xp::create(new Test())->sayHello(array(0 => \'Timm\', 1 => \'Alex\', )); 
          }
       };'),
        $this->emit('class Test {
          public static void sayHello(string[] $names) {
            foreach ($names as $name) {
              echo "Hello", $name, " ";
            }
          }

          public static void main() {
            new Test()->sayHello(array("Timm", "Alex"));
          }
        }')
      );
    }

    /**
     * Tests a static method call
     *
     * @access  public
     */
    #[@test]
    function staticMethodCall() {
      $this->assertSourcecodeEquals(
        preg_replace('/\n\s*/', '', 'class Test extends xp·lang·Object{
          public static function sayHello($names){
            foreach ($names as $name) {
              echo \'Hello\', $name, \' \'; 
            }; 
          }
 
          public static function main(){
            Test::sayHello(array(0 => \'Timm\', 1 => \'Alex\', )); 
          }
       };'),
        $this->emit('class Test {
          public static void sayHello(string[] $names) {
            foreach ($names as $name) {
              echo "Hello", $name, " ";
            }
          }

          public static void main() {
            Test::sayHello(array("Timm", "Alex"));
          }
        }')
      );
    }
  }
?>
