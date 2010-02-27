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
   * TestCase for member access
   *
   * @see   xp://net.xp_framework.unittest.core.generics.ListOf
   */
  class net·xp_framework·unittest·core·generics·MemberTest extends TestCase {
    protected $fixture= NULL;
  
    /**
     * Creates fixture
     *
     */
    public function setUp() {
      $this->fixture= create('new net.xp_framework.unittest.core.generics.ListOf<string>', 'Hello', 'World');
    }
  
    /**
     * Test read access
     *
     */
    #[@test]
    public function readAccess() {
      $this->assertEquals(array('Hello', 'World'), $this->fixture->elements);
    }

    /**
     * Test read access
     *
     */
    #[@test, @ignore('Behaviour not defined')]
    public function readNonExistant() {
      $this->fixture->nonexistant;
    }

    /**
     * Test write access
     *
     */
    #[@test]
    public function writeAccess() {
      $this->fixture->elements= array('Hallo', 'Welt');
      $this->assertEquals(array('Hallo', 'Welt'), $this->fixture->elements);
    }

    /**
     * Test read access
     *
     */
    #[@test, @ignore('Behaviour not defined')]
    public function writeNonExistant() {
      $this->fixture->nonexistant= TRUE;
    }
  }
?>
