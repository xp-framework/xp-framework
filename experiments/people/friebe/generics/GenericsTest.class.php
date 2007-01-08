<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
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
     * @param   array<string, string> types
     * @param   lang.Object object
     * @throws  util.profiling.AssertionFailedError
     */
    protected function assertGenericTypes($types, $object) {
      return $this->assertEquals($types, $object->__types);
    }

    /**
     * Tests create() sets an __id member
     *
     */
    #[@test]
    public function idMemberIsset() {
      $hash= create('GenericMap<int, text.String>');
      $this->assertNotEmpty($hash->__id);
    }

    /**
     * Tests generics cannot be constructed directly (by means of new).
     *
     */
    #[@test, @expect('InstantiationException')]
    public function cannotBeConstructedDirectly() {
      new GenericMap();
    }

    /**
     * Tests create()
     *
     */
    #[@test]
    public function createFunction() {
      $hash= create('GenericMap<int, text.String>');

      $this->assertGenericTypes(array(
        'K' => 'int',
        'V' => 'text.String'
      ), $hash);
    }

    /**
     * Tests create() with arguments
     *
     */
    #[@test]
    public function createFunctionWithArguments() {
      $hash= create('GenericMap<int, text.String>', array(1 => new String('one')));

      $this->assertGenericTypes(array(
        'K' => 'int',
        'V' => 'text.String'
      ), $hash);
      $this->assertEquals(new String('one'), $hash->get(1));
    }

    /**
     * Tests create() when passed not enough components
     *
     */
    #[@test, @expect('InstantiationException')]
    public function notEnoughComponents() {
      create('GenericMap<int>');
    }

    /**
     * Tests create() when passed too many components
     *
     */
    #[@test, @expect('InstantiationException')]
    public function tooManyComponents() {
      create('GenericMap<int, int, int>');
    }

    /**
     * Tests type hinting
     *
     */
    #[@test]
    public function correctTypes() {
      $hash= create('GenericMap<int, text.String>');
      $hash->put(1, new String('one'));
      $this->assertEquals(new String('one'), $hash->get(1));
    }

    /**
     * Tests type hinting
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function wrongTypes() {
      $hash= create('GenericMap<int, text.String>');
      $hash->put(1, new Object());    // value should be a String
    }

    /**
     * Tests type hinting
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function wrongType() {
      $hash= create('GenericMap<int, text.String>');
      $hash->get('string');           // key should be an int
    }
  }
?>
