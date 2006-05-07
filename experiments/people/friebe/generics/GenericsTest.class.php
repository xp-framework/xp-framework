<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$
 */

  uses(
    'util.profiling.unittest.TestCase',
    'text.String',
    'generic+xp://GenericMap'
  );

  /**
   * Tests generics
   *
   * @purpose  Unit test
   */
  class GenericsTest extends TestCase {

    /**
     * Assertion helper
     *
     * @access  protected
     * @param   array<string, string> types
     * @param   &lang.Object object
     * @throws  util.profiling.AssertionFailedError
     */
    function assertGenericTypes($types, &$object) {
      return $this->assertEquals($types, $object->__types);
    }

    /**
     * Tests create() calls Object constructor
     *
     * @access  public
     */
    #[@test]
    function objectConstructorCalled() {
      $hash= &create('GenericMap<int, String>');
      $this->assertNotEmpty($hash->__id);
    }

    /**
     * Tests generics cannot be constructed directly (by means of new).
     *
     * @access  public
     */
    #[@test, @expect('InstantiationException')]
    function cannotBeConstructedDirectly() {
      new GenericMap();
    }

    /**
     * Tests create()
     *
     * @access  public
     */
    #[@test]
    function createFunction() {
      $hash= &create('GenericMap<int, String>');

      $this->assertGenericTypes(array(
        'K' => 'int',
        'V' => 'String'
      ), $hash);
    }

    /**
     * Tests create() with arguments
     *
     * @access  public
     */
    #[@test]
    function createFunctionWithArguments() {
      $hash= &create('GenericMap<int, String>', array(1 => new String('one')));

      $this->assertGenericTypes(array(
        'K' => 'int',
        'V' => 'String'
      ), $hash);
      $this->assertEquals(new String('one'), $hash->get(1));
    }

    /**
     * Tests create() when passed not enough components
     *
     * @access  public
     */
    #[@test, @expect('InstantiationException')]
    function notEnoughComponents() {
      create('GenericMap<int>');
    }

    /**
     * Tests create() when passed too many components
     *
     * @access  public
     */
    #[@test, @expect('InstantiationException')]
    function tooManyComponents() {
      create('GenericMap<int, int, int>');
    }

    /**
     * Tests type hinting
     *
     * @access  public
     */
    #[@test]
    function correctTypes() {
      $hash= &create('GenericMap<int, String>');
      $hash->put(1, new String('one'));
      $this->assertEquals(new String('one'), $hash->get(1));
    }

    /**
     * Tests type hinting
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function wrongTypes() {
      $hash= &create('GenericMap<int, String>');
      $hash->put(1, new Object());    // value should be a String
    }

    /**
     * Tests type hinting
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function wrongType() {
      $hash= &create('GenericMap<int, String>');
      $hash->get('string');           // key should be an int
    }
  }
?>
