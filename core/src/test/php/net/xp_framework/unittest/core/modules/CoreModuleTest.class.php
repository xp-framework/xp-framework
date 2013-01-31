<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase', 'lang.Module');

  /**
   * TestCase
   *
   */
  class CoreModuleTest extends TestCase {
    protected $fixture= NULL;

  
    /**
     * Retrieve "core" module
     *
     */
    public function setUp() {
      $this->fixture= Module::forName('core');
    }

    /**
     * Test getVersion()
     *
     */
    #[@test]
    public function modules_version() {
      $this->assertEquals(xp::version(), $this->fixture->getVersion());
    }

    /**
     * Test getName()
     *
     */
    #[@test]
    public function modules_name() {
      $this->assertEquals('core', $this->fixture->getName());
    }

    /**
     * Test getClassLoader()
     *
     */
    #[@test]
    public function modules_loader() {
      $this->assertInstanceOf('lang.IClassLoader', $this->fixture->getClassLoader());
    }

    /**
     * Test toString()
     *
     */
    #[@test]
    public function string_representation() {
      $this->assertEquals(
        'Module<core:'.xp::version().', lang.ClassLoader>',
        $this->fixture->toString()
      );
    }
  }
?>
