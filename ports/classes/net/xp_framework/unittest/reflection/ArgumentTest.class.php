<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'net.xp_framework.unittest.reflection.TestClass'
  );

  /**
   * Test the XP reflection API
   *
   * @see      xp://lang.reflect.Argument
   * @purpose  Testcase
   */
  class ArgumentTest extends TestCase {
    public
      $class  = NULL;
  
    /**
     * Setup method
     *
     */
    public function setUp() {
      $this->class= XPClass::forName('net.xp_framework.unittest.reflection.TestClass');
    }

    /**
     * Tests the constructor's argument
     *
     */
    #[@test]
    public function constructorArgument() {
      with ($argument= $this->class->getConstructor()->getArgument(0)); {
        $this->assertEquals('mixed', $argument->getType());
        $this->assertEquals(NULL, $argument->getType(TRUE));
        $this->assertEquals('in', $argument->getName());
        $this->assertTrue($argument->isOptional());
        $this->assertEquals('NULL', $argument->getDefault());
        $this->assertEquals(NULL, $argument->getDefaultValue());
      }
    }

    /**
     * Tests the setDate() method's argument
     *
     */
    #[@test]
    public function dateSetterArgument() {
      with ($argument= $this->class->getMethod('setDate')->getArgument(0)); {
        $this->assertEquals('util.Date', $argument->getType());
        $this->assertEquals(NULL, $argument->getType(TRUE));
        $this->assertEquals('date', $argument->getName());
        $this->assertFalse($argument->isOptional());
        $this->assertFalse($argument->getDefault());
      }
    }

    /**
     * Tests Argument::getDefaultValue() throws an exception if
     * an argument does not have a default value
     *
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function getDefaultValue() {
      $this->class->getMethod('setDate')->getArgument(0)->getDefaultValue();
    }

    /**
     * Tests type hinted parameter's type is returned via getType(TRUE)
     *
     */
    #[@test]
    public function typeHintedClassType() {
      $this->assertEquals(
        'util.collections.HashTable',
        $this->class->getMethod('fromHashTable')->getArgument(0)->getType(TRUE)
      );
    }

    /**
     * Tests type hinted parameter's type is returned via getType(TRUE)
     *
     */
    #[@test]
    public function typeHintedArrayType() {
      $this->assertEquals(
        'array',
        $this->class->getMethod('fromMap')->getArgument(0)->getType(TRUE)
      );
    }
  }
?>
