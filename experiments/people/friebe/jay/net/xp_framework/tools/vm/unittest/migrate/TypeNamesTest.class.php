<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'unittest.TestCase',
    'net.xp_framework.tools.vm.util.MigrationNameMapping'
  );

  /**
   * Tests type names
   *
   * @purpose  Unit Test
   */
  class TypeNamesTest extends TestCase {
    protected
      $names= NULL;

    /**
     * Setup method
     *
     */
    public function setUp() {
      $this->names= new MigrationNameMapping();
      $this->names->addMapping('Date', 'util.Date');
      $this->names->setNamespaceSeparator('.');
    }

    /**
     * Primitive type: int / integer
     *
     */
    #[@test]
    public function intType() {
      $this->assertEquals('int', $this->names->forType('int'));
      $this->assertEquals('int', $this->names->forType('integer'));
    }

    /**
     * Primitive type: float / double
     *
     */
    #[@test]
    public function floatType() {
      $this->assertEquals('float', $this->names->forType('float'));
      $this->assertEquals('float', $this->names->forType('double'));
    }
 
    /**
     * Primitive type: bool / boolean
     *
     */
    #[@test]
    public function boolType() {
      $this->assertEquals('bool', $this->names->forType('bool'));
      $this->assertEquals('bool', $this->names->forType('boolean'));
    }

    /**
     * Date type
     *
     */
    #[@test]
    public function dateType() {
      $this->assertEquals('util.Date', $this->names->forType('Date'));
      $this->assertEquals('util.Date', $this->names->forType('util.Date'));
    }

    /**
     * Typed arrays
     *
     */
    #[@test]
    public function untypedArrays() {
      $this->assertEquals('array', $this->names->forType('array'));
    }

    /**
     * Generic arrays
     *
     */
    #[@test]
    public function genericArrays() {
      $this->assertEquals('array', $this->names->forType('array<int, string>'));
    }

    /**
     * Generic class
     *
     */
    #[@test]
    public function genericClass() {
      $this->assertEquals('lang.XPClass', $this->names->forType('lang.XPClass<util.Date>'));
    }

    /**
     * Typed arrays
     *
     */
    #[@test]
    public function primitiveTypedArrays() {
      $this->assertEquals('string[]', $this->names->forType('string[]'));
    }

    /**
     * Typed arrays
     *
     */
    #[@test]
    public function typedArrays() {
      $this->assertEquals('util.Date[]', $this->names->forType('Date[]'));
    }

    /**
     * Vararg type
     *
     */
    #[@test]
    public function varargType() {
      $this->assertEquals('mixed...', $this->names->forType('mixed*', TRUE));
    }
  }
?>
