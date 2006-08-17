<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'util.profiling.unittest.TestCase',
    'net.xp_framework.tools.vm.util.NameMapping'
  );

  /**
   * Tests type names
   *
   * @purpose  Unit Test
   */
  class TypeNamesTest extends TestCase {
    var
      $names= NULL;

    /**
     * Setup method
     *
     * @access  public
     */
    function setUp() {
      $this->names= &new NameMapping();
      $this->names->addMapping('date', 'util.Date');
    }

    /**
     * Primitive type: int / integer
     *
     * @access  public
     */
    #[@test]
    function intType() {
      $this->assertEquals('int', $this->names->forType('int'));
      $this->assertEquals('int', $this->names->forType('integer'));
    }

    /**
     * Primitive type: float / double
     *
     * @access  public
     */
    #[@test]
    function floatType() {
      $this->assertEquals('float', $this->names->forType('float'));
      $this->assertEquals('float', $this->names->forType('double'));
    }
 
    /**
     * Primitive type: bool / boolean
     *
     * @access  public
     */
    #[@test]
    function boolType() {
      $this->assertEquals('bool', $this->names->forType('bool'));
      $this->assertEquals('bool', $this->names->forType('boolean'));
    }

    /**
     * Date type
     *
     * @access  public
     */
    #[@test]
    function dateType() {
      $this->assertEquals('xp~util~Date', $this->names->forType('Date'));
      $this->assertEquals('xp~util~Date', $this->names->forType('util.Date'));
    }

    /**
     * Typed arrays
     *
     * @access  public
     */
    #[@test]
    function untypedArrays() {
      $this->assertEquals('array', $this->names->forType('array'));
    }

    /**
     * Generic arrays
     *
     * @access  public
     */
    #[@test]
    function genericArrays() {
      $this->assertEquals('array', $this->names->forType('array<int, string>'));
    }

    /**
     * Generic class
     *
     * @access  public
     */
    #[@test]
    function genericClass() {
      $this->assertEquals('xp~lang~XPClass', $this->names->forType('lang.XPClass<util.Date>'));
    }

    /**
     * Typed arrays
     *
     * @access  public
     */
    #[@test]
    function primitiveTypedArrays() {
      $this->assertEquals('string[]', $this->names->forType('string[]'));
    }

    /**
     * Typed arrays
     *
     * @access  public
     */
    #[@test]
    function typedArrays() {
      $this->assertEquals('xp~util~Date[]', $this->names->forType('Date[]'));
    }

    /**
     * Vararg type
     *
     * @access  public
     */
    #[@test]
    function varargType() {
      $this->assertEquals('mixed...', $this->names->forType('mixed*', TRUE));
    }
  }
?>
