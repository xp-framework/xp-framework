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
  class AnnotationEmitterTest extends AbstractEmitterTest {

    /**
     * Tests a class annotation
     *
     * @access  public
     */
    #[@test]
    function classWithWebserviceAnnotation() {
      $this->assertSourcecodeEquals(
        preg_replace('/\n\s*/', '', 'class Test extends xp·lang·Object{
          public function sayHello(){
            echo \'Hello\'; 
          }
        }
        function __Testmeta() { 
          return array( 
            \'<main>\' => array( \'webservice\' => NULL, ), 
          );
        };'),
        $this->emit('[@webservice] class Test {
          public void sayHello() {
            echo "Hello";
          }
        }')
      );
    }
    /**
     * Tests a method annotation
     *
     * @access  public
     */
    #[@test]
    function methodWithTestAnnotation() {
      $this->assertSourcecodeEquals(
        preg_replace('/\n\s*/', '', 'class Test extends xp·lang·Object{
          public function sayHello(){
            echo \'Hello\'; 
          }
        }
        function __Testmeta() { 
          return array( 
            \'sayHello\' => array( \'test\' => NULL, ), 
          );
        };'),
        $this->emit('class Test {
          [@test] public void sayHello() {
            echo "Hello";
          }
        }')
      );
    }

    /**
     * Tests a method annotation
     *
     * @access  public
     */
    #[@test]
    function methodWithTestAndIgnoreAnnotations() {
      $this->assertSourcecodeEquals(
        preg_replace('/\n\s*/', '', 'class Test extends xp·lang·Object{
          public function sayHello(){
            echo \'Hello\'; 
          }
        }
        function __Testmeta() { 
          return array( 
            \'sayHello\' => array( \'test\' => NULL, \'ignore\' => NULL, ), 
          );
        };'),
        $this->emit('class Test {
          [@test, @ignore] public void sayHello() {
            echo "Hello";
          }
        }')
      );
    }
  }
?>
