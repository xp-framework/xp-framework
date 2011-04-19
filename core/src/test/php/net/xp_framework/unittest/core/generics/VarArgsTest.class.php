<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_framework.unittest.core.generics';

  uses(
    'unittest.TestCase',
    'net.xp_framework.unittest.core.generics.ListOf'
  );

  /**
   * TestCase for generic construction behaviour at runtime.
   *
   * @see   xp://net.xp_framework.unittest.core.generics.ListOf
   */
  class net·xp_framework·unittest·core·generics·VarArgsTest extends TestCase {
  
    /**
     * Test constructor with arguments
     *
     */
    #[@test]
    public function withArguments() {
      $this->assertEquals(
        array('Hello', 'World'),
        create('new net.xp_framework.unittest.core.generics.ListOf<string>', 'Hello', 'World')->elements()
      );
    }

    /**
     * Test constructor with arguments
     *
     */
    #[@test]
    public function withoutArguments() {
      $this->assertEquals(
        array(),
        create('new net.xp_framework.unittest.core.generics.ListOf<string>')->elements()
      );
    }

    /**
     * Test constructor with arguments
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function withIncorrectArguments() {
      create('new net.xp_framework.unittest.core.generics.ListOf<string>', 'Hello', 1);
    }

    /**
     * Test method with arguments
     *
     */
    #[@test]
    public function withAllOf() {
      $this->assertEquals(
        array('Hello', 'World'),
        create('new net.xp_framework.unittest.core.generics.ListOf<string>')->withAll('Hello', 'World')->elements()
      );
    }
  }
?>
