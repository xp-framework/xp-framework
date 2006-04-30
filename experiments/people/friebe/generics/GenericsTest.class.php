<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$
 */

  uses(
    'util.profiling.unittest.TestCase',
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
     * Tests create()
     *
     * @access  public
     */
    #[@test]
    function createFunction() {
      $hash= &create('GenericMap<int, Object>');

      $this->assertGenericTypes(array(
        'K' => 'int',
        'V' => 'Object'
      ), $hash);
    }

    /**
     * Tests create() with arguments
     *
     * @access  public
     */
    #[@test]
    function createFunctionWithArguments() {
      $hash= &create('GenericMap<int, Object>', array(1 => 'one'));

      $this->assertGenericTypes(array(
        'K' => 'int',
        'V' => 'Object'
      ), $hash);
      $this->assertEquals('one', $hash->get(1));
    }
  }
?>
