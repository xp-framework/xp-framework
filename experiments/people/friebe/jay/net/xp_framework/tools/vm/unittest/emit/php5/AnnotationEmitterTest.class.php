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
     */
    #[@test]
    public function classWithWebserviceAnnotation() {
      $this->assertSourcecodeEquals(
        '#[@webservice]
        class main·Test extends lang·Object{
          /**
           * @return  void
           */
          public function sayHello(){
            echo \'Hello\'; 
          }
        };',
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
     */
    #[@test]
    public function methodWithTestAnnotation() {
      $this->assertSourcecodeEquals(
        'class main·Test extends lang·Object{ 
          /**
           * @return  void
           */
          #[@test]
          public function sayHello(){
            echo \'Hello\'; 
          }
        };',
        $this->emit('class Test {
          [@test] public void sayHello() {
            echo "Hello";
          }
        }')
      );
    }

    /**
     * Tests a method annotation with key/value 
     *
     */
    #[@test]
    public function methodWithKeyValueAnnotation() {
      $this->assertSourcecodeEquals(
        'class main·Test extends lang·Object{ 
          /**
           * @return  void
           */
          #[@security(roles= array(0 => \'admin\', 1 => \'user\', ))]
          public function sayHello(){
            echo \'Hello\'; 
          }
        };',
        $this->emit('class Test {
          [@security(roles= array("admin", "user"))] public void sayHello() {
            echo "Hello";
          }
        }')
      );
    }

    /**
     * Tests a method annotation
     *
     */
    #[@test]
    public function methodWithTestAndIgnoreAnnotations() {
      $this->assertSourcecodeEquals(
        'class main·Test extends lang·Object{ 
          /**
           * @return  void
           */
          #[@test, @ignore]
          public function sayHello(){
            echo \'Hello\'; 
          }
        };',
        $this->emit('class Test {
          [@test, @ignore] public void sayHello() {
            echo "Hello";
          }
        }')
      );
    }
  }
?>
